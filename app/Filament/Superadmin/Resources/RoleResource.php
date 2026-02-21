<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\RoleResource\Pages;
use App\Models\RoleUser;
use Filament\Forms;
use Filament\Forms\Form;
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

  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\Section::make('Data Role')->schema([
        Forms\Components\TextInput::make('name')
          ->label('Nama Role')
          ->required()
          ->unique(ignoreRecord: true)
          ->maxLength(255),
        Forms\Components\Toggle::make('need_approval')
          ->label('Butuh Persetujuan')
          ->helperText('Jika diaktifkan, pengguna dengan role ini perlu disetujui admin sebelum bisa login'),
      ]),
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
      'index' => Pages\ListRole::route('/'),
      'create' => Pages\CreateRole::route('/create'),
      'edit' => Pages\EditRole::route('/{record}/edit'),
    ];
  }
}
