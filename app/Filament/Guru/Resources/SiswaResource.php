<?php

namespace App\Filament\Guru\Resources;

use App\Filament\Guru\Resources\SiswaResource\Pages;
use App\Models\ActivityLog;
use App\Models\Kelas;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SiswaResource extends Resource
{
  protected static ?string $model = User::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-group';
  protected static ?string $navigationLabel = 'Manajemen Siswa';
  protected static ?string $navigationGroup = 'Kelola Data';
  protected static ?string $modelLabel = 'Siswa';
  protected static ?string $pluralModelLabel = 'Siswa';
  protected static ?string $slug = 'siswa';
  protected static ?int $navigationSort = 2;

  public static function shouldRegisterNavigation(): bool
  {
    return \App\Models\RoleUser::checkNav('guru_manajemen_siswa');
  }

  /**
   * Hanya tampilkan siswa dari kelas yang diwalikan guru ini.
   */
  public static function getEloquentQuery(): Builder
  {
    $guru = Auth::user();

    // Ambil id kelas yang diwalikan guru ini
    $kelasIds = Kelas::where('wali_id', $guru->id)->pluck('id');

    return parent::getEloquentQuery()
      ->whereIn('kelas_id', $kelasIds)
      ->whereHas('role_user', fn(Builder $q) => $q->where('name', 'Siswa'));
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('Data Siswa')
          ->schema([
            Forms\Components\TextInput::make('name')
              ->label('Nama Lengkap')
              ->required()
              ->maxLength(255),
            Forms\Components\TextInput::make('nisn')
              ->label('NISN')
              ->required()
              ->regex('/^\d{10}$/')
              ->maxLength(10)
              ->unique(ignoreRecord: true)
              ->validationMessages(['unique' => 'NISN sudah digunakan.'])
              ->extraInputAttributes([
                'maxlength' => 10,
                'inputmode' => 'numeric',
                'pattern' => '[0-9]*',
                'oninput' => "this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)",
              ]),
            Forms\Components\TextInput::make('email')
              ->label('Email')
              ->email()
              ->unique(ignoreRecord: true)
              ->validationMessages(['unique' => 'Email sudah digunakan.'])
              ->helperText('Opsional. Jika dikosongkan, akan otomatis digenerate dari NISN (nisn@siswa.buku-ramadhan.id).'),
            Forms\Components\Select::make('jenis_kelamin')
              ->label('Jenis Kelamin')
              ->options([
                'L' => 'Laki-laki',
                'P' => 'Perempuan',
              ])
              ->required(),
            Forms\Components\Select::make('agama')
              ->label('Agama')
              ->options([
                'Islam'     => 'Islam',
                'Kristen'   => 'Kristen',
                'Katolik'   => 'Katolik',
                'Hindu'     => 'Hindu',
                'Buddha'    => 'Buddha',
                'Konghucu'  => 'Konghucu',
              ])
              ->required(),
            Forms\Components\TextInput::make('password')
              ->label('Password')
              ->password()
              ->dehydrated(fn($state) => filled($state))
              ->helperText('Kosongkan jika tidak ingin mengubah. Default: NISN siswa.'),
          ])
          ->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('nisn')
          ->label('NISN')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('name')
          ->label('Nama')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('jenis_kelamin')
          ->label('JK')
          ->badge()
          ->color(fn(?string $state): string => match ($state) {
            'L' => 'info',
            'P' => 'danger',
            default => 'gray',
          })
          ->formatStateUsing(fn(?string $state): string => match ($state) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
          }),
        Tables\Columns\TextColumn::make('agama')
          ->label('Agama')
          ->badge()
          ->color('success')
          ->sortable(),
      ])
      ->defaultSort('name')
      ->filters([
        Tables\Filters\SelectFilter::make('jenis_kelamin')
          ->label('Jenis Kelamin')
          ->options([
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
          ]),
        Tables\Filters\SelectFilter::make('agama')
          ->label('Agama')
          ->options([
            'Islam'    => 'Islam',
            'Kristen'  => 'Kristen',
            'Katolik'  => 'Katolik',
            'Hindu'    => 'Hindu',
            'Buddha'   => 'Buddha',
            'Konghucu' => 'Konghucu',
          ]),
      ])
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\ViewAction::make(),
          Tables\Actions\EditAction::make()
            ->after(function (User $record) {
              ActivityLog::log('edit_siswa', Auth::user(), [
                'description' => 'Mengedit data siswa ' . $record->name,
                'target_id' => $record->id,
                'target_name' => $record->name,
              ]);
            }),
          Tables\Actions\Action::make('resetPassword')
            ->label('Reset Password')
            ->icon('heroicon-o-key')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Reset Password Siswa')
            ->modalDescription(fn(User $record) => "Password siswa {$record->name} akan direset ke NISN ({$record->nisn}). Lanjutkan?")
            ->modalSubmitActionLabel('Ya, Reset')
            ->action(function (User $record) {
              $record->update([
                'password' => $record->nisn,
                'must_change_password' => true,
              ]);
              ActivityLog::log('reset_password', Auth::user(), [
                'description' => 'Reset password siswa ' . $record->name . ' ke NISN',
                'target_id' => $record->id,
                'target_name' => $record->name,
              ]);
              Notification::make()
                ->title('Password berhasil direset')
                ->body("Password {$record->name} direset ke NISN: {$record->nisn}")
                ->success()
                ->send();
            }),
          Tables\Actions\Action::make('resetSession')
            ->label('Reset Sesi Login')
            ->icon('heroicon-o-arrow-path')
            ->color('gray')
            ->requiresConfirmation()
            ->modalHeading('Reset Sesi Login Siswa')
            ->modalDescription(fn(User $record) => "Sesi login aktif siswa {$record->name} akan dihapus sehingga bisa login kembali. Lanjutkan?")
            ->modalSubmitActionLabel('Ya, Reset Sesi')
            ->disabled(fn(User $record) => empty($record->active_session_id))
            ->tooltip(fn(User $record) => empty($record->active_session_id) ? 'Tidak ada sesi aktif' : 'Reset sesi login siswa')
            ->action(function (User $record) {
              $record->updateQuietly([
                'active_session_id' => null,
                'session_login_at'  => null,
              ]);
              ActivityLog::log('reset_session', Auth::user(), [
                'description' => 'Reset sesi login siswa ' . $record->name,
                'target_id' => $record->id,
                'target_name' => $record->name,
              ]);
              Notification::make()
                ->title('Sesi login berhasil direset')
                ->body("{$record->name} sekarang bisa login kembali.")
                ->success()
                ->send();
            }),
          Tables\Actions\DeleteAction::make()
            ->before(function (User $record) {
              ActivityLog::log('delete_siswa', Auth::user(), [
                'description' => 'Menghapus siswa ' . $record->name . ' (NISN: ' . $record->nisn . ')',
                'target_id' => $record->id,
                'target_name' => $record->name,
                'target_nisn' => $record->nisn,
              ]);
            })
            ->requiresConfirmation()
            ->modalHeading('Hapus Siswa')
            ->modalDescription('Apakah Anda yakin ingin menghapus siswa ini? Data yang sudah dihapus tidak dapat dikembalikan.')
            ->modalSubmitActionLabel('Ya, Hapus'),
        ])
          ->icon('heroicon-m-ellipsis-vertical')
          ->tooltip('Aksi'),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make()
            ->before(function (\Illuminate\Database\Eloquent\Collection $records) {
              ActivityLog::log('bulk_delete_siswa', Auth::user(), [
                'description' => 'Menghapus massal ' . $records->count() . ' siswa',
                'count' => $records->count(),
                'names' => $records->pluck('name')->toArray(),
              ]);
            })
            ->requiresConfirmation()
            ->modalHeading('Hapus Siswa Terpilih')
            ->modalDescription('Apakah Anda yakin ingin menghapus semua siswa yang dipilih?')
            ->modalSubmitActionLabel('Ya, Hapus Semua'),
        ]),
      ]);
  }

  public static function getRelations(): array
  {
    return [];
  }

  public static function getPages(): array
  {
    return [
      'index'  => Pages\ListSiswa::route('/'),
      'create' => Pages\CreateSiswa::route('/create'),
      'edit'   => Pages\EditSiswa::route('/{record}/edit'),
    ];
  }
}
