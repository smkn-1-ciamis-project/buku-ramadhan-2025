<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\GuruResource\Pages;
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

class GuruResource extends Resource
{
  protected static ?string $model = User::class;

  protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
  protected static ?string $navigationLabel = 'Manajemen Guru';
  protected static ?string $navigationGroup = 'Pengguna';
  protected static ?string $modelLabel = 'Guru';
  protected static ?string $pluralModelLabel = 'Guru';
  protected static ?string $slug = 'guru';
  protected static ?int $navigationSort = 2;

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->whereHas('role_user', fn(Builder $q) => $q->where('name', 'Guru'))
      ->with(['role_user', 'kelasWali']);
  }

  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\Section::make('Informasi Guru')->schema([
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
          ->maxLength(20),
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
          ]),
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
          ->color(fn(string $state): string => $state === 'L' ? 'info' : 'danger')
          ->formatStateUsing(fn(string $state): string => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
        Tables\Columns\TextColumn::make('no_hp')
          ->label('No. HP')
          ->placeholder('-'),
        Tables\Columns\TextColumn::make('kelasWali.nama')
          ->label('Wali Kelas')
          ->badge()
          ->color('success')
          ->placeholder('Belum ditentukan'),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Terdaftar')
          ->since()
          ->tooltip(fn($record) => $record->created_at->translatedFormat('d M Y, H:i'))
          ->sortable(),
      ])
      ->defaultSort('name', 'asc')
      ->filters([
        Tables\Filters\SelectFilter::make('jenis_kelamin')
          ->label('Jenis Kelamin')
          ->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
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

  public static function mutateFormDataBeforeCreate(array $data): array
  {
    $data['role_user_id'] = RoleUser::where('name', 'Guru')->first()?->id;
    $data['email_verified_at'] = now();
    return $data;
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListGuru::route('/'),
      'create' => Pages\CreateGuru::route('/create'),
      'edit' => Pages\EditGuru::route('/{record}/edit'),
    ];
  }
}
