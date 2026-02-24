<?php

namespace App\Filament\Kesiswaan\Resources;

use App\Filament\Kesiswaan\Resources\ValidasiKelasResource\Pages;
use App\Models\FormSubmission;
use App\Models\Kelas;
use Carbon\Carbon;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ValidasiKelasResource extends Resource
{
  protected static ?string $model = Kelas::class;

  protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
  protected static ?string $navigationLabel = 'Validasi Formulir';
  protected static ?string $navigationGroup = 'Validasi';
  protected static ?string $modelLabel = 'Kelas';
  protected static ?string $pluralModelLabel = 'Validasi Per Kelas';
  protected static ?string $slug = 'validasi';
  protected static ?int $navigationSort = 2;

  public static function shouldRegisterNavigation(): bool
  {
    return \App\Models\RoleUser::checkNav('kesiswaan_validasi');
  }

  public static function getNavigationBadge(): ?string
  {
    $count = FormSubmission::where('status', 'verified')
      ->where('kesiswaan_status', 'pending')
      ->count();
    return $count > 0 ? (string) $count : null;
  }

  public static function getNavigationBadgeColor(): ?string
  {
    return 'warning';
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()->with(['wali', 'siswa']);
  }

  public static function table(Table $table): Table
  {
    $ramadhanStart = Carbon::create(2026, 2, 19);
    $today = Carbon::today();
    $hariKe = $today->gte($ramadhanStart) ? (int) $ramadhanStart->diffInDays($today) + 1 : 0;
    if ($hariKe > 30) $hariKe = 30;

    return $table
      ->columns([
        Tables\Columns\TextColumn::make('nama')
          ->label('Kelas')
          ->searchable()
          ->sortable()
          ->badge()
          ->color('info')
          ->weight('bold'),
        Tables\Columns\TextColumn::make('wali.name')
          ->label('Wali Kelas')
          ->searchable()
          ->sortable()
          ->placeholder('-'),
        Tables\Columns\TextColumn::make('total_siswa')
          ->label('Siswa')
          ->state(fn(Kelas $record): int => $record->siswa->count())
          ->alignCenter(),
        Tables\Columns\TextColumn::make('menunggu_validasi')
          ->label('Menunggu Validasi')
          ->state(function (Kelas $record): int {
            $siswaIds = $record->siswa->pluck('id');
            return FormSubmission::whereIn('user_id', $siswaIds)
              ->where('status', 'verified')
              ->where('kesiswaan_status', 'pending')
              ->count();
          })
          ->badge()
          ->color(fn(int $state) => $state > 0 ? 'warning' : 'gray')
          ->alignCenter(),
        Tables\Columns\TextColumn::make('sudah_divalidasi')
          ->label('Divalidasi')
          ->state(function (Kelas $record): int {
            $siswaIds = $record->siswa->pluck('id');
            return FormSubmission::whereIn('user_id', $siswaIds)
              ->where('status', 'verified')
              ->where('kesiswaan_status', 'validated')
              ->count();
          })
          ->badge()
          ->color('success')
          ->alignCenter(),
        Tables\Columns\TextColumn::make('ditolak_kesiswaan')
          ->label('Ditolak')
          ->state(function (Kelas $record): int {
            $siswaIds = $record->siswa->pluck('id');
            return FormSubmission::whereIn('user_id', $siswaIds)
              ->where('status', 'verified')
              ->where('kesiswaan_status', 'rejected')
              ->count();
          })
          ->badge()
          ->color(fn(int $state) => $state > 0 ? 'danger' : 'gray')
          ->alignCenter(),
        Tables\Columns\TextColumn::make('progress_validasi')
          ->label('Progress')
          ->state(function (Kelas $record): string {
            $siswaIds = $record->siswa->pluck('id');
            $total = FormSubmission::whereIn('user_id', $siswaIds)
              ->where('status', 'verified')
              ->count();
            if ($total === 0) return '-';
            $validated = FormSubmission::whereIn('user_id', $siswaIds)
              ->where('status', 'verified')
              ->where('kesiswaan_status', 'validated')
              ->count();
            return round(($validated / $total) * 100) . '%';
          })
          ->alignCenter()
          ->color(function (string $state) {
            if ($state === '-') return 'gray';
            $val = (int) str_replace('%', '', $state);
            return match (true) {
              $val >= 80 => 'success',
              $val >= 50 => 'warning',
              default    => 'danger',
            };
          }),
      ])
      ->defaultSort('nama')
      ->filters([])
      ->actions([])
      ->bulkActions([])
      ->recordUrl(fn(Kelas $record) => static::getUrl('validasi-kelas', ['record' => $record]));
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListValidasiKelas::route('/'),
      'validasi-kelas' => Pages\ValidasiPerKelas::route('/{record}/validasi'),
    ];
  }

  public static function canCreate(): bool
  {
    return false;
  }
}
