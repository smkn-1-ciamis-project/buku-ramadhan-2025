<?php

namespace App\Filament\Kesiswaan\Resources;

use App\Filament\Kesiswaan\Resources\RekapKelasResource\Pages;
use App\Models\FormSubmission;
use App\Models\Kelas;
use App\Models\User;
use Carbon\Carbon;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RekapKelasResource extends Resource
{
  protected static ?string $model = Kelas::class;

  protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
  protected static ?string $navigationLabel = 'Rekap Per Kelas';
  protected static ?string $navigationGroup = 'Data & Rekap';
  protected static ?string $modelLabel = 'Kelas';
  protected static ?string $pluralModelLabel = 'Rekap Per Kelas';
  protected static ?string $slug = 'rekap-kelas';
  protected static ?int $navigationSort = 5;

  public static function shouldRegisterNavigation(): bool
  {
    return \App\Models\RoleUser::checkNav('kesiswaan_rekap_kelas');
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
          ->label('Jumlah Siswa')
          ->state(fn(Kelas $record): int => $record->siswa->count())
          ->alignCenter()
          ->sortable(false),
        Tables\Columns\TextColumn::make('submit_hari_ini')
          ->label('Submit Hari Ini')
          ->state(function (Kelas $record) use ($hariKe): string {
            if ($hariKe < 1) return '-';
            $siswaIds = $record->siswa->pluck('id');
            $submitted = FormSubmission::whereIn('user_id', $siswaIds)
              ->where('hari_ke', $hariKe)->count();
            $total = $siswaIds->count();
            return "{$submitted}/{$total}";
          })
          ->alignCenter()
          ->color(fn(string $state) => $state === '-' ? 'gray' : null),
        Tables\Columns\TextColumn::make('verified_count')
          ->label('Terverifikasi')
          ->state(function (Kelas $record): int {
            $siswaIds = $record->siswa->pluck('id');
            return FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'verified')->count();
          })
          ->badge()
          ->color('success')
          ->alignCenter(),
        Tables\Columns\TextColumn::make('pending_count')
          ->label('Menunggu')
          ->state(function (Kelas $record): int {
            $siswaIds = $record->siswa->pluck('id');
            return FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'pending')->count();
          })
          ->badge()
          ->color(fn(int $state) => $state > 0 ? 'warning' : 'success')
          ->alignCenter(),
        Tables\Columns\TextColumn::make('rejected_count')
          ->label('Ditolak')
          ->state(function (Kelas $record): int {
            $siswaIds = $record->siswa->pluck('id');
            return FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'rejected')->count();
          })
          ->badge()
          ->color(fn(int $state) => $state > 0 ? 'danger' : 'gray')
          ->alignCenter(),
        Tables\Columns\TextColumn::make('compliance_rate')
          ->label('Kepatuhan')
          ->state(function (Kelas $record) use ($hariKe): string {
            $totalSiswa = $record->siswa->count();
            if ($totalSiswa === 0 || $hariKe < 1) return '-';
            $expectedTotal = $totalSiswa * $hariKe;
            $siswaIds = $record->siswa->pluck('id');
            $actualSubmitted = FormSubmission::whereIn('user_id', $siswaIds)->count();
            return round(($actualSubmitted / $expectedTotal) * 100) . '%';
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
      'index' => Pages\ListRekapKelas::route('/'),
      'view'  => Pages\ViewRekapKelas::route('/{record}'),
    ];
  }

  public static function canCreate(): bool
  {
    return false;
  }
}
