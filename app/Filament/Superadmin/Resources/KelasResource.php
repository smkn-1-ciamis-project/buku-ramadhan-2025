<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\KelasResource\Pages;
use App\Filament\Superadmin\Resources\KelasResource\RelationManagers\SiswaRelationManager;
use App\Models\Kelas;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KelasResource extends Resource
{
  protected static ?string $model = Kelas::class;

  protected static ?string $navigationIcon = 'heroicon-o-building-library';
  protected static ?string $navigationLabel = 'Manajemen Kelas';
  protected static ?string $navigationGroup = 'Akademik';
  protected static ?string $modelLabel = 'Kelas';
  protected static ?string $pluralModelLabel = 'Kelas';
  protected static ?string $slug = 'kelas';
  protected static ?int $navigationSort = 5;

  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\Section::make('Data Kelas')->schema([
        Forms\Components\TextInput::make('nama')
          ->label('Nama Kelas')
          ->required()
          ->maxLength(255)
          ->placeholder('Contoh: 10 AKL 1 KLOTER 1'),
        Forms\Components\Select::make('wali_id')
          ->label('Wali Kelas')
          ->options(function () {
            return User::whereHas('role_user', fn(Builder $q) => $q->where('name', 'Guru'))
              ->pluck('name', 'id');
          })
          ->searchable()
          ->preload()
          ->placeholder('Pilih guru sebagai wali kelas'),
      ])->columns(2),
    ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('nama')
          ->label('Nama Kelas')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('wali.name')
          ->label('Wali Kelas')
          ->searchable()
          ->placeholder('Belum ditentukan')
          ->icon('heroicon-o-academic-cap'),
        Tables\Columns\TextColumn::make('siswa_count')
          ->label('Jumlah Siswa')
          ->counts('siswa')
          ->badge()
          ->color('info')
          ->sortable(),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Dibuat')
          ->since()
          ->tooltip(fn($record) => $record->created_at->translatedFormat('d M Y, H:i'))
          ->sortable(),
      ])
      ->defaultSort('nama', 'asc')
      ->filters([])
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

  public static function getRelations(): array
  {
    return [
      SiswaRelationManager::class,
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListKelas::route('/'),
      'create' => Pages\CreateKelas::route('/create'),
      'edit' => Pages\EditKelas::route('/{record}/edit'),
    ];
  }
}
