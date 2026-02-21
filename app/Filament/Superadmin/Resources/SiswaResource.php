<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\SiswaResource\Pages;
use App\Models\Kelas;
use App\Models\RoleUser;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class SiswaResource extends Resource
{
  protected static ?string $model = User::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-group';
  protected static ?string $navigationLabel = 'Manajemen Siswa';
  protected static ?string $navigationGroup = 'Pengguna';
  protected static ?string $modelLabel = 'Siswa';
  protected static ?string $pluralModelLabel = 'Siswa';
  protected static ?string $slug = 'siswa';
  protected static ?int $navigationSort = 3;

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->whereHas('role_user', fn(Builder $q) => $q->where('name', 'Siswa'))
      ->with(['role_user', 'kelas']);
  }

  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\Section::make('Informasi Siswa')->schema([
        Forms\Components\TextInput::make('name')
          ->label('Nama Lengkap')
          ->required()
          ->maxLength(255),
        Forms\Components\TextInput::make('nisn')
          ->label('NISN')
          ->required()
          ->unique(ignoreRecord: true)
          ->maxLength(10),
        Forms\Components\TextInput::make('email')
          ->label('Email')
          ->email()
          ->unique(ignoreRecord: true)
          ->maxLength(255),
        Forms\Components\Select::make('jenis_kelamin')
          ->label('Jenis Kelamin')
          ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
          ->required(),
        Forms\Components\Select::make('agama')
          ->label('Agama')
          ->options([
            'Islam' => 'Islam',
            'Kristen' => 'Kristen',
            'Katolik' => 'Katolik',
            'Hindu' => 'Hindu',
            'Buddha' => 'Buddha',
            'Konghucu' => 'Konghucu',
          ])
          ->required(),
        Forms\Components\Select::make('kelas_id')
          ->label('Kelas')
          ->relationship('kelas', 'nama')
          ->searchable()
          ->preload(),
        Forms\Components\TextInput::make('no_hp')
          ->label('No. HP')
          ->tel()
          ->maxLength(20),
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
        Tables\Columns\TextColumn::make('nisn')
          ->label('NISN')
          ->searchable()
          ->copyable(),
        Tables\Columns\TextColumn::make('kelas.nama')
          ->label('Kelas')
          ->badge()
          ->color('info')
          ->placeholder('Belum ada kelas')
          ->sortable(),
        Tables\Columns\TextColumn::make('jenis_kelamin')
          ->label('JK')
          ->badge()
          ->color(fn(string $state): string => $state === 'L' ? 'info' : 'danger')
          ->formatStateUsing(fn(string $state): string => $state === 'L' ? 'L' : 'P'),
        Tables\Columns\TextColumn::make('agama')
          ->label('Agama')
          ->sortable(),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Terdaftar')
          ->since()
          ->tooltip(fn($record) => $record->created_at->translatedFormat('d M Y, H:i'))
          ->sortable(),
      ])
      ->defaultSort('name', 'asc')
      ->filters([
        Tables\Filters\SelectFilter::make('kelas_id')
          ->label('Kelas')
          ->relationship('kelas', 'nama')
          ->searchable()
          ->preload(),
        Tables\Filters\SelectFilter::make('jenis_kelamin')
          ->label('Jenis Kelamin')
          ->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
        Tables\Filters\SelectFilter::make('agama')
          ->label('Agama')
          ->options([
            'Islam' => 'Islam',
            'Kristen' => 'Kristen',
            'Katolik' => 'Katolik',
            'Hindu' => 'Hindu',
            'Buddha' => 'Buddha',
            'Konghucu' => 'Konghucu',
          ]),
      ])
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\EditAction::make(),
          Tables\Actions\DeleteAction::make(),
        ]),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListSiswa::route('/'),
      'create' => Pages\CreateSiswa::route('/create'),
      'edit' => Pages\EditSiswa::route('/{record}/edit'),
    ];
  }
}
