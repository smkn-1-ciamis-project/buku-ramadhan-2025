<?php

namespace App\Filament\Guru\Resources;

use App\Filament\Guru\Resources\RekapSiswaResource\Pages;
use App\Models\FormSubmission;
use App\Models\Kelas;
use App\Models\User;
use Carbon\Carbon;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RekapSiswaResource extends Resource
{
  protected static ?string $model = User::class;

  protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
  protected static ?string $navigationLabel = 'Rekap Siswa';
  protected static ?string $navigationGroup = 'Kelola Data';
  protected static ?string $modelLabel = 'Siswa';
  protected static ?string $pluralModelLabel = 'Rekap Siswa';
  protected static ?string $slug = 'rekap-siswa';
  protected static ?int $navigationSort = 4;

  public static function shouldRegisterNavigation(): bool
  {
    return \App\Models\RoleUser::checkNav('guru_rekap_siswa');
  }

  /**
   * Hanya tampilkan siswa dari kelas yang diwalikan oleh guru ini.
   */
  public static function getEloquentQuery(): Builder
  {
    $guru = Auth::user();
    $kelasIds = Kelas::where('wali_id', $guru->id)->pluck('id');

    return parent::getEloquentQuery()
      ->whereIn('kelas_id', $kelasIds)
      ->whereHas('role_user', fn(Builder $q) => $q->where('name', 'Siswa'))
      ->with(['kelas', 'formSubmissions']);
  }

  public static function table(Table $table): Table
  {
    $ramadhanStart = Carbon::create(2026, 2, 19);
    $today = Carbon::today();
    $hariKe = $today->gte($ramadhanStart) ? min((int) $ramadhanStart->diffInDays($today) + 1, 30) : 0;

    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('Nama Siswa')
          ->searchable()
          ->sortable()
          ->weight('bold'),
        Tables\Columns\TextColumn::make('nisn')
          ->label('NISN')
          ->searchable()
          ->sortable()
          ->color('gray')
          ->fontFamily('mono'),
        Tables\Columns\TextColumn::make('kelas.nama')
          ->label('Kelas')
          ->badge()
          ->color('info')
          ->sortable(),
        Tables\Columns\TextColumn::make('total_submit')
          ->label('Total Laporan')
          ->state(fn(User $record): int => $record->formSubmissions->count())
          ->badge()
          ->color('info')
          ->alignCenter(),
        Tables\Columns\TextColumn::make('verified_count')
          ->label('Terverifikasi')
          ->state(fn(User $record): int => $record->formSubmissions->where('status', 'verified')->count())
          ->badge()
          ->color('success')
          ->alignCenter(),
        Tables\Columns\TextColumn::make('pending_count')
          ->label('Menunggu')
          ->state(fn(User $record): int => $record->formSubmissions->where('status', 'pending')->count())
          ->badge()
          ->color(fn(int $state) => $state > 0 ? 'warning' : 'success')
          ->alignCenter(),
        Tables\Columns\TextColumn::make('rejected_count')
          ->label('Ditolak')
          ->state(fn(User $record): int => $record->formSubmissions->where('status', 'rejected')->count())
          ->badge()
          ->color(fn(int $state) => $state > 0 ? 'danger' : 'gray')
          ->alignCenter(),
        Tables\Columns\TextColumn::make('belum_submit')
          ->label('Belum Lapor')
          ->state(function (User $record) use ($hariKe): int {
            if ($hariKe < 1) return 0;
            return max($hariKe - $record->formSubmissions->count(), 0);
          })
          ->badge()
          ->color(fn(int $state) => $state > 0 ? 'danger' : 'success')
          ->alignCenter(),
        Tables\Columns\TextColumn::make('compliance_rate')
          ->label('Kepatuhan')
          ->state(function (User $record) use ($hariKe): string {
            if ($hariKe < 1) return '-';
            $rate = round(($record->formSubmissions->count() / $hariKe) * 100);
            return min($rate, 100) . '%';
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
      ->defaultSort('name')
      ->filters([
        Tables\Filters\SelectFilter::make('kelas_id')
          ->label('Kelas')
          ->options(function () {
            $guru = Auth::user();
            return Kelas::where('wali_id', $guru->id)->pluck('nama', 'id');
          }),
        Tables\Filters\SelectFilter::make('status_laporan')
          ->label('Status Laporan')
          ->options([
            'belum' => 'Belum Lapor Hari Ini',
            'sudah' => 'Sudah Lapor Hari Ini',
          ])
          ->query(function (Builder $query, array $data) use ($hariKe) {
            if (!$data['value'] || $hariKe < 1) return;
            if ($data['value'] === 'belum') {
              $query->whereDoesntHave('formSubmissions', fn(Builder $q) => $q->where('hari_ke', $hariKe));
            } else {
              $query->whereHas('formSubmissions', fn(Builder $q) => $q->where('hari_ke', $hariKe));
            }
          }),
      ])
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\Action::make('view_detail')
            ->label('Lihat Detail')
            ->icon('heroicon-o-eye')
            ->color('info')
            ->url(fn(User $record) => static::getUrl('view', ['record' => $record])),
          Tables\Actions\Action::make('export_detail')
            ->label('Export Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->url(fn(User $record) => route('guru.rekap-siswa.export-detail', $record))
            ->openUrlInNewTab(),
        ]),
      ])
      ->bulkActions([])
      ->headerActions([
        Tables\Actions\Action::make('export_excel')
          ->label('Export Excel')
          ->icon('heroicon-o-arrow-down-tray')
          ->color('success')
          ->url(fn() => route('guru.rekap-siswa.export'))
          ->openUrlInNewTab(),
      ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListRekapSiswa::route('/'),
      'view'  => Pages\ViewRekapSiswa::route('/{record}'),
    ];
  }

  public static function canCreate(): bool { return false; }
  public static function canEdit($record): bool { return false; }
  public static function canDelete($record): bool { return false; }
  public static function canView($record): bool { return true; }
  public static function canViewAny(): bool { return true; }
}
