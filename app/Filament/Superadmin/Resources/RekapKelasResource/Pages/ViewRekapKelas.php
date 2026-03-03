<?php

namespace App\Filament\Superadmin\Resources\RekapKelasResource\Pages;

use App\Filament\Superadmin\Resources\RekapKelasResource;
use App\Models\FormSubmission;
use App\Services\DashboardStatsService;
use Carbon\Carbon;
use Filament\Resources\Pages\ViewRecord;

class ViewRekapKelas extends ViewRecord
{
    protected static string $resource = RekapKelasResource::class;

    protected static string $view = 'filament.superadmin.pages.view-rekap-kelas';

    protected function getViewData(): array
    {
        $kelas = $this->record;
        $kelas->load(['wali', 'siswa']);
        $statsService = app(DashboardStatsService::class);

        $siswaIds = $kelas->siswa->pluck('id')->toArray();
        $totalSiswa = count($siswaIds);

        $ramadhanStart = Carbon::create(2026, 2, 19);
        $today = Carbon::today();
        $hariKe = $today->gte($ramadhanStart) ? min((int) $ramadhanStart->diffInDays($today) + 1, 30) : 0;

        // Batch aggregate stats for the whole kelas (1 query)
        $totalSubmissions = FormSubmission::whereIn('user_id', $siswaIds)->count();
        $verified = FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'verified')->count();
        $pending  = FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'pending')->count();
        $rejected = FormSubmission::whereIn('user_id', $siswaIds)->where('status', 'rejected')->count();

        $todaySubmit = $hariKe > 0
            ? FormSubmission::whereIn('user_id', $siswaIds)->where('hari_ke', $hariKe)->count()
            : 0;

        $expectedTotal = $totalSiswa * max($hariKe, 1);
        $complianceRate = $expectedTotal > 0 ? round(($totalSubmissions / $expectedTotal) * 100) : 0;
        $verifyRate = $totalSubmissions > 0 ? round(($verified / $totalSubmissions) * 100) : 0;

        // Per-siswa progress — batch (1 query instead of 4×N)
        $perSiswaStats = $statsService->getPerSiswaStats($siswaIds);

        $siswaProgress = $kelas->siswa->map(function ($siswa) use ($hariKe, $perSiswaStats) {
            $s = $perSiswaStats[$siswa->id] ?? ['total' => 0, 'verified' => 0, 'pending' => 0, 'rejected' => 0];
            $rate = $hariKe > 0 ? round(($s['total'] / $hariKe) * 100) : 0;
            return [
                'name' => $siswa->name,
                'nisn' => $siswa->nisn ?? '-',
                'total' => $s['total'],
                'verified' => $s['verified'],
                'pending' => $s['pending'],
                'rejected' => $s['rejected'],
                'rate' => min($rate, 100),
            ];
        })->sortBy('name')->values();

        return [
            'kelas' => $kelas,
            'totalSiswa' => $totalSiswa,
            'hariKe' => $hariKe,
            'totalSubmissions' => $totalSubmissions,
            'verified' => $verified,
            'pending' => $pending,
            'rejected' => $rejected,
            'todaySubmit' => $todaySubmit,
            'complianceRate' => $complianceRate,
            'verifyRate' => $verifyRate,
            'siswaProgress' => $siswaProgress,
        ];
    }
}
