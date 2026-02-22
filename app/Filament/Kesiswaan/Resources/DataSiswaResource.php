<?php

namespace App\Filament\Kesiswaan\Resources;

use App\Filament\Kesiswaan\Resources\DataSiswaResource\Pages;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DataSiswaResource extends Resource
{
  protected static ?string $model = User::class;

  protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
  protected static ?string $navigationLabel = 'Data Siswa';
  protected static ?string $navigationGroup = 'Data & Rekap';
  protected static ?string $modelLabel = 'Siswa';
  protected static ?string $pluralModelLabel = 'Data Siswa';
  protected static ?string $slug = 'data-siswa';
  protected static ?int $navigationSort = 3;

  public static function shouldRegisterNavigation(): bool
  {
    return \App\Models\RoleUser::checkNav('kesiswaan_data_siswa');
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->whereHas('role_user', fn(Builder $q) => $q->where('name', 'Siswa'))
      ->with(['kelas', 'role_user']);
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
          ->sortable(),
        Tables\Columns\TextColumn::make('kelas.nama')
          ->label('Kelas')
          ->sortable()
          ->badge()
          ->color('info'),
        Tables\Columns\TextColumn::make('jenis_kelamin')
          ->label('JK')
          ->formatStateUsing(fn(?string $state) => $state === 'P' ? 'Perempuan' : 'Laki-laki')
          ->toggleable(),
        Tables\Columns\TextColumn::make('agama')
          ->label('Agama')
          ->sortable()
          ->toggleable(),
        Tables\Columns\TextColumn::make('email')
          ->label('Email')
          ->searchable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('no_hp')
          ->label('No. HP')
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('form_submissions_count')
          ->label('Total Formulir')
          ->counts('formSubmissions')
          ->sortable()
          ->badge()
          ->color('success')
          ->alignCenter(),
      ])
      ->defaultSort('name')
      ->filters([
        Tables\Filters\SelectFilter::make('kelas_id')
          ->label('Kelas')
          ->relationship('kelas', 'nama')
          ->searchable()
          ->preload(),
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
        Tables\Filters\SelectFilter::make('jenis_kelamin')
          ->label('Jenis Kelamin')
          ->options([
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
          ]),
      ])
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\ViewAction::make()
            ->label('Lihat')
            ->icon('heroicon-o-eye')
            ->color('info'),
        ]),
      ])
      ->bulkActions([]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListDataSiswa::route('/'),
      'view'  => Pages\ViewDataSiswa::route('/{record}'),
    ];
  }

  public static function canCreate(): bool
  {
    return false;
  }
}
