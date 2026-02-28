<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\RekapKelasResource\Pages;
use App\Models\Kelas;
use App\Models\RoleUser;
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
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $modelLabel = 'Kelas';
    protected static ?string $pluralModelLabel = 'Rekap Per Kelas';
    protected static ?string $slug = 'rekap-kelas';
    protected static ?int $navigationSort = 7;

    public static function shouldRegisterNavigation(): bool
    {
        return RoleUser::checkNav('sa_rekap_kelas');
    }

    public static function getEloquentQuery(): Builder
    {
        $ramadhanStart = Carbon::create(2026, 2, 19);
        $today = Carbon::today();
        $hariKe = $today->gte($ramadhanStart) ? (int) $ramadhanStart->diffInDays($today) + 1 : 0;
        if ($hariKe > 30) $hariKe = 30;

        $query = parent::getEloquentQuery()
            ->with(['wali'])
            ->withCount(['siswa'])
            ->withCount([
                'formSubmissions as verified_count_val' => fn(Builder $q) => $q->where('status', 'verified'),
                'formSubmissions as pending_count_val' => fn(Builder $q) => $q->where('status', 'pending'),
                'formSubmissions as rejected_count_val' => fn(Builder $q) => $q->where('status', 'rejected'),
                'formSubmissions as total_submissions_count' => fn(Builder $q) => $q,
            ]);

        if ($hariKe >= 1) {
            $query->withCount([
                'formSubmissions as submit_hari_ini_count' => fn(Builder $q) => $q->where('hari_ke', $hariKe),
            ]);
        }

        return $query;
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
                    ->state(fn(Kelas $record): int => $record->siswa_count)
                    ->alignCenter()
                    ->sortable(false),
                Tables\Columns\TextColumn::make('submit_hari_ini')
                    ->label('Submit Hari Ini')
                    ->state(function (Kelas $record) use ($hariKe): string {
                        if ($hariKe < 1) return '-';
                        $submitted = $record->submit_hari_ini_count ?? 0;
                        return "{$submitted}/{$record->siswa_count}";
                    })
                    ->alignCenter()
                    ->color(fn(string $state) => $state === '-' ? 'gray' : null),
                Tables\Columns\TextColumn::make('verified_count')
                    ->label('Terverifikasi')
                    ->state(fn(Kelas $record): int => $record->verified_count_val)
                    ->badge()
                    ->color('success')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('pending_count')
                    ->label('Menunggu')
                    ->state(fn(Kelas $record): int => $record->pending_count_val)
                    ->badge()
                    ->color(fn(int $state) => $state > 0 ? 'warning' : 'success')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('rejected_count')
                    ->label('Ditolak')
                    ->state(fn(Kelas $record): int => $record->rejected_count_val)
                    ->badge()
                    ->color(fn(int $state) => $state > 0 ? 'danger' : 'gray')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('compliance_rate')
                    ->label('Kepatuhan')
                    ->state(function (Kelas $record) use ($hariKe): string {
                        $totalSiswa = $record->siswa_count;
                        if ($totalSiswa === 0 || $hariKe < 1) return '-';
                        $expectedTotal = $totalSiswa * $hariKe;
                        $actualSubmitted = $record->total_submissions_count;
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
            ->actions([])
            ->bulkActions([])
            ->recordUrl(fn(Kelas $record) => static::getUrl('view', ['record' => $record]));
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
