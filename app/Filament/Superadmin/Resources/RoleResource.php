<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\RoleResource\Pages;
use App\Filament\Superadmin\Resources\RoleResource\RelationManagers\UsersRelationManager;
use App\Models\RoleUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoleResource extends Resource
{
  protected static ?string $model = RoleUser::class;

  protected static ?string $navigationIcon = 'heroicon-o-shield-check';
  protected static ?string $navigationLabel = 'Manajemen Role';
  protected static ?string $navigationGroup = 'Pengaturan';
  protected static ?string $modelLabel = 'Role';
  protected static ?string $pluralModelLabel = 'Role';
  protected static ?string $slug = 'role';
  protected static ?int $navigationSort = 7;

  public static function shouldRegisterNavigation(): bool
  {
    return RoleUser::checkNav('sa_role');
  }

  // Menu keys per panel
  protected const SUPERADMIN_MENUS = [
    'sa_guru' => 'Manajemen Guru',
    'sa_kesiswaan' => 'Manajemen Kesiswaan',
    'sa_siswa' => 'Manajemen Siswa',
    'sa_kelas' => 'Manajemen Kelas',
    'sa_log_formulir' => 'Log Formulir',
    'sa_role' => 'Manajemen Role',
    'sa_setting_formulir' => 'Setting Formulir',
    'sa_log_aktivitas' => 'Log Aktivitas',
  ];

  protected const GURU_MENUS = [
    'guru_manajemen_siswa' => 'Manajemen Siswa',
    'guru_verifikasi' => 'Verifikasi Formulir',
  ];

  protected const KESISWAAN_MENUS = [
    'kesiswaan_validasi' => 'Validasi Formulir',
    'kesiswaan_rekap_kelas' => 'Rekap Per Kelas',
    'kesiswaan_data_guru' => 'Data Guru',
    'kesiswaan_data_siswa' => 'Data Siswa',
    'kesiswaan_setting_formulir' => 'Setting Formulir',
  ];

  /**
   * Get the correct menu set for a given role name.
   */
  protected static function getMenusForRole(?string $roleName): array
  {
    return match ($roleName) {
      'Super Admin' => self::SUPERADMIN_MENUS,
      'Guru' => self::GURU_MENUS,
      'Kesiswaan' => self::KESISWAAN_MENUS,
      default => [],
    };
  }

  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\Section::make('Data Role')->schema([
        Forms\Components\TextInput::make('name')
          ->label('Nama Role')
          ->required()
          ->unique(ignoreRecord: true)
          ->maxLength(255)
          ->live(),
        Forms\Components\Toggle::make('need_approval')
          ->label('Butuh Persetujuan')
          ->helperText('Jika diaktifkan, pengguna dengan role ini perlu disetujui admin sebelum bisa login'),
      ]),

      // ── Superadmin Menu Visibility ──────────────────────────────────────
      Forms\Components\Section::make('Visibilitas Menu Sidebar')
        ->description('Atur menu sidebar yang tampil untuk role ini. Semua aktif secara default.')
        ->schema(
          collect(self::SUPERADMIN_MENUS)->map(
            fn($label, $key) =>
            Forms\Components\Toggle::make("menu_visibility.{$key}")
              ->label($label)
              ->default(true)
          )->values()->toArray()
        )
        ->columns(2)
        ->visible(fn(Get $get) => $get('name') === 'Super Admin'),

      // ── Guru Menu Visibility ────────────────────────────────────────────
      Forms\Components\Section::make('Visibilitas Menu Sidebar')
        ->description('Atur menu sidebar yang tampil untuk role ini. Semua aktif secara default.')
        ->schema(
          collect(self::GURU_MENUS)->map(
            fn($label, $key) =>
            Forms\Components\Toggle::make("menu_visibility.{$key}")
              ->label($label)
              ->default(true)
          )->values()->toArray()
        )
        ->columns(2)
        ->visible(fn(Get $get) => $get('name') === 'Guru'),

      // ── Kesiswaan Menu Visibility ───────────────────────────────────────
      Forms\Components\Section::make('Visibilitas Menu Sidebar')
        ->description('Atur menu sidebar yang tampil untuk role ini. Semua aktif secara default.')
        ->schema(
          collect(self::KESISWAAN_MENUS)->map(
            fn($label, $key) =>
            Forms\Components\Toggle::make("menu_visibility.{$key}")
              ->label($label)
              ->default(true)
          )->values()->toArray()
        )
        ->columns(2)
        ->visible(fn(Get $get) => $get('name') === 'Kesiswaan'),
    ]);
  }

  public static function infolist(Infolist $infolist): Infolist
  {
    return $infolist->schema([
      Infolists\Components\Section::make('Detail Role')->schema([
        Infolists\Components\TextEntry::make('name')
          ->label('Nama Role')
          ->badge()
          ->color('info'),
        Infolists\Components\IconEntry::make('need_approval')
          ->label('Butuh Persetujuan')
          ->boolean(),
        Infolists\Components\TextEntry::make('users_count')
          ->label('Total Pengguna')
          ->getStateUsing(fn($record) => $record->users()->count() . ' pengguna')
          ->badge()
          ->color('success'),
        Infolists\Components\TextEntry::make('created_at')
          ->label('Dibuat')
          ->dateTime('d M Y, H:i'),
      ])->columns(4),

      Infolists\Components\Section::make('Visibilitas Menu Sidebar')
        ->schema(function () use ($infolist) {
          $record = $infolist->getRecord();
          $menus = self::getMenusForRole($record?->name);
          return collect($menus)->map(
            fn($label, $key) =>
            Infolists\Components\IconEntry::make("mv_{$key}")
              ->label($label)
              ->boolean()
              ->trueIcon('heroicon-o-eye')
              ->falseIcon('heroicon-o-eye-slash')
              ->trueColor('success')
              ->falseColor('danger')
              ->getStateUsing(fn($record) => $record->isMenuVisible($key))
          )->values()->toArray();
        })
        ->columns(3)
        ->visible(fn($record) => in_array($record?->name, ['Super Admin', 'Guru', 'Kesiswaan'])),
    ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('Nama Role')
          ->searchable()
          ->sortable()
          ->icon('heroicon-o-shield-check'),
        Tables\Columns\IconColumn::make('need_approval')
          ->label('Butuh Persetujuan')
          ->boolean()
          ->trueIcon('heroicon-o-check-circle')
          ->falseIcon('heroicon-o-x-circle')
          ->trueColor('warning')
          ->falseColor('gray'),
        Tables\Columns\TextColumn::make('users_count')
          ->label('Jumlah Pengguna')
          ->counts('users')
          ->badge()
          ->color('info')
          ->sortable(),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Dibuat')
          ->since()
          ->tooltip(fn($record) => $record->created_at?->translatedFormat('d M Y, H:i'))
          ->sortable(),
      ])
      ->defaultSort('name', 'asc')
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\ViewAction::make(),
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

  public static function getRelationManagers(): array
  {
    return [
      UsersRelationManager::class,
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListRole::route('/'),
      'create' => Pages\CreateRole::route('/create'),
      'view' => Pages\ViewRole::route('/{record}'),
      'edit' => Pages\EditRole::route('/{record}/edit'),
    ];
  }
}
