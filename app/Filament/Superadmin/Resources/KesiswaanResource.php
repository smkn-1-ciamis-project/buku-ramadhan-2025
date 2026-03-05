<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\KesiswaanResource\Pages;
use App\Models\ActivityLog;
use App\Models\RoleUser;
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

class KesiswaanResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Manajemen Kesiswaan';
    protected static ?string $navigationGroup = 'Pengguna';
    protected static ?string $modelLabel = 'Kesiswaan';
    protected static ?string $pluralModelLabel = 'Kesiswaan';
    protected static ?string $slug = 'kesiswaan';
    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return RoleUser::checkNav('sa_kesiswaan');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('role_user', fn(Builder $q) => $q->where('name', 'Kesiswaan'))
            ->with('role_user');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Petugas Kesiswaan')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_hp')
                    ->label('No. HP')
                    ->tel()
                    ->maxLength(20)
                    ->dehydrateStateUsing(function (?string $state): ?string {
                        if (empty($state)) return null;
                        $digits = preg_replace('/[^0-9]/', '', $state);
                        if (empty($digits)) return null;
                        if (str_starts_with($digits, '8') && strlen($digits) >= 9 && strlen($digits) <= 13) {
                            $digits = '0' . $digits;
                        }
                        return $digits;
                    }),
                Forms\Components\Select::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                    ->required(),
            ])->columns(2),

            Forms\Components\Section::make('Keamanan')->schema([
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->maxLength(255)
                    ->helperText(fn(string $operation) => $operation === 'edit' ? 'Kosongkan jika tidak ingin mengubah password' : ''),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('JK')
                    ->badge()
                    ->color(fn(?string $state): string => $state === 'L' ? 'info' : 'danger')
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                        default => '-'
                    }),
                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No. HP')
                    ->formatStateUsing(function (?string $state): ?string {
                        if (empty($state)) return null;
                        if (is_numeric($state) && str_starts_with($state, '8')) {
                            return '0' . $state;
                        }
                        return $state;
                    })
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->since()
                    ->tooltip(fn($record) => $record->created_at->translatedFormat('d M Y, H:i'))
                    ->sortable(),
            ])
            ->defaultSort('name', 'asc')
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('loginAs')
                        ->label('Login Sebagai')
                        ->icon('heroicon-o-arrow-right-on-rectangle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Login Sebagai Kesiswaan')
                        ->modalDescription(fn(User $record) => "Anda akan masuk ke akun kesiswaan {$record->name}. Lanjutkan?")
                        ->modalSubmitActionLabel('Ya, Masuk')
                        ->url(fn(User $record) => route('impersonate', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\EditAction::make()
                        ->after(function (User $record) {
                            ActivityLog::log('edit_kesiswaan', Auth::user(), [
                                'description' => 'Mengedit data kesiswaan ' . $record->name,
                                'target_user_id' => $record->id,
                                'target_user' => $record->name,
                            ]);
                        }),
                    Tables\Actions\Action::make('resetPassword')
                        ->label('Reset Password')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Reset Password Kesiswaan')
                        ->modalDescription(fn(User $record) => "Password {$record->name} akan direset ke email ({$record->email}). Lanjutkan?")
                        ->modalSubmitActionLabel('Ya, Reset')
                        ->action(function (User $record) {
                            $record->update([
                                'password' => Hash::make($record->email),
                                'must_change_password' => true,
                            ]);
                            ActivityLog::log('reset_password', Auth::user(), [
                                'description' => 'Mereset password kesiswaan ' . $record->name,
                                'target_user_id' => $record->id,
                                'target_user' => $record->name,
                            ]);
                            Notification::make()
                                ->title('Password berhasil direset')
                                ->body("Password {$record->name} direset ke email: {$record->email}")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('resetSession')
                        ->label('Reset Sesi Login')
                        ->icon('heroicon-o-arrow-path')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Reset Sesi Login Kesiswaan')
                        ->modalDescription(fn(User $record) => "Sesi login aktif {$record->name} akan dihapus sehingga bisa login kembali. Lanjutkan?")
                        ->modalSubmitActionLabel('Ya, Reset Sesi')
                        ->disabled(fn(User $record) => empty($record->active_session_id))
                        ->tooltip(fn(User $record) => empty($record->active_session_id) ? 'Tidak ada sesi aktif' : 'Reset sesi login')
                        ->action(function (User $record) {
                            $record->updateQuietly([
                                'active_session_id' => null,
                                'session_login_at'  => null,
                            ]);
                            ActivityLog::log('reset_session', Auth::user(), [
                                'description' => 'Mereset sesi login kesiswaan ' . $record->name,
                                'target_user_id' => $record->id,
                                'target_user' => $record->name,
                            ]);
                            Notification::make()
                                ->title('Sesi login berhasil direset')
                                ->body("{$record->name} sekarang bisa login kembali.")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->before(function (User $record) {
                            ActivityLog::log('delete_kesiswaan', Auth::user(), [
                                'description' => 'Menghapus kesiswaan ' . $record->name,
                                'target_user_id' => $record->id,
                                'target_user' => $record->name,
                            ]);
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (\Illuminate\Database\Eloquent\Collection $records) {
                            ActivityLog::log('bulk_delete_kesiswaan', Auth::user(), [
                                'description' => 'Menghapus ' . $records->count() . ' kesiswaan sekaligus',
                                'count' => $records->count(),
                                'names' => $records->pluck('name')->toArray(),
                            ]);
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKesiswaan::route('/'),
            'create' => Pages\CreateKesiswaan::route('/create'),
            'edit' => Pages\EditKesiswaan::route('/{record}/edit'),
        ];
    }
}
