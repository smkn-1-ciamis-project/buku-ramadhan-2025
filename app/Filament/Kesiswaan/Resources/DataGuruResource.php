<?php

namespace App\Filament\Kesiswaan\Resources;

use App\Filament\Kesiswaan\Resources\DataGuruResource\Pages;
use App\Models\FormSubmission;
use App\Models\Kelas;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DataGuruResource extends Resource
{
  protected static ?string $model = User::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-group';
  protected static ?string $navigationLabel = 'Data Guru';
  protected static ?string $navigationGroup = 'Data & Rekap';
  protected static ?string $modelLabel = 'Guru';
  protected static ?string $pluralModelLabel = 'Data Guru';
  protected static ?string $slug = 'data-guru';
  protected static ?int $navigationSort = 4;

  public static function shouldRegisterNavigation(): bool
  {
    return \App\Models\RoleUser::checkNav('kesiswaan_data_guru');
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->whereHas('role_user', fn(Builder $q) => $q->where('name', 'Guru'))
      ->with(['role_user', 'kelasWali']);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('Nama Guru')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('email')
          ->label('Email')
          ->searchable()
          ->toggleable(),
        Tables\Columns\TextColumn::make('kelasWali.nama')
          ->label('Kelas Wali')
          ->badge()
          ->color('info')
          ->separator(', '),
        Tables\Columns\TextColumn::make('jumlah_siswa')
          ->label('Jumlah Siswa')
          ->state(function (User $record): int {
            return User::whereIn('kelas_id', $record->kelasWali->pluck('id'))
              ->whereHas('role_user', fn($q) => $q->where('name', 'Siswa'))
              ->count();
          })
          ->alignCenter()
          ->sortable(false),
        Tables\Columns\TextColumn::make('pending_verifikasi')
          ->label('Menunggu Verifikasi')
          ->state(function (User $record): int {
            $kelasIds = $record->kelasWali->pluck('id');
            $siswaIds = User::whereIn('kelas_id', $kelasIds)->pluck('id');
            return FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'pending')->count();
          })
          ->badge()
          ->color(fn(int $state): string => $state > 0 ? 'warning' : 'success')
          ->alignCenter()
          ->sortable(false),
        Tables\Columns\TextColumn::make('total_verified')
          ->label('Total Terverifikasi')
          ->state(function (User $record): int {
            return FormSubmission::where('verified_by', $record->id)->where('status', 'verified')->count();
          })
          ->badge()
          ->color('success')
          ->alignCenter()
          ->sortable(false),
      ])
      ->defaultSort('name')
      ->filters([])
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\ViewAction::make()
            ->label('Detail')
            ->icon('heroicon-o-eye')
            ->color('info'),
        ]),
      ])
      ->bulkActions([]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListDataGuru::route('/'),
      'view'  => Pages\ViewDataGuru::route('/{record}'),
    ];
  }

  public static function canCreate(): bool
  {
    return false;
  }
}
