<?php

namespace App\Filament\Kesiswaan\Resources;

use App\Filament\Kesiswaan\Resources\DataGuruResource\Pages;
use App\Models\FormSubmission;
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
      ->with([
        'role_user',
        'kelasWali' => fn($q) => $q
          ->withCount(['siswa'])
          ->withCount([
            'formSubmissions as kelas_pending_count' => fn(Builder $q2) => $q2->where('status', 'pending'),
          ]),
      ])
      ->addSelect([
        'total_verified_sub' => FormSubmission::query()
          ->selectRaw('count(*)')
          ->where('status', 'verified')
          ->whereColumn('form_submissions.verified_by', 'users.id'),
      ]);
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
          ->state(fn(User $record): int => $record->kelasWali->sum('siswa_count'))
          ->alignCenter()
          ->sortable(false),
        Tables\Columns\TextColumn::make('pending_verifikasi')
          ->label('Menunggu Verifikasi')
          ->state(fn(User $record): int => $record->kelasWali->sum('kelas_pending_count'))
          ->badge()
          ->color(fn(int $state): string => $state > 0 ? 'warning' : 'gray')
          ->alignCenter()
          ->sortable(false),
        Tables\Columns\TextColumn::make('total_verified')
          ->label('Total Terverifikasi')
          ->state(fn(User $record): int => (int) $record->total_verified_sub)
          ->badge()
          ->color(fn(int $state): string => $state > 0 ? 'success' : 'gray')
          ->alignCenter()
          ->sortable(false),
      ])
      ->defaultSort('name')
      ->filters([])
      ->actions([])
      ->bulkActions([])
      ->recordUrl(fn(User $record) => static::getUrl('view', ['record' => $record]));
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
