<?php

namespace App\Filament\Kesiswaan\Resources\RekapKelasResource\Pages;

use App\Filament\Kesiswaan\Resources\RekapKelasResource;
use App\Models\FormSubmission;
use Carbon\Carbon;
use Filament\Resources\Pages\ViewRecord;

class ViewRekapKelas extends ViewRecord
{
  protected static string $resource = RekapKelasResource::class;

  protected static string $view = 'filament.kesiswaan.pages.view-rekap-kelas';

  protected function getViewData(): array
  {
    $kelas = $this->record;
    $kelas->load(['wali', 'siswa']);

    $siswaIds = $kelas->siswa->pluck('id');
    $totalSiswa = $siswaIds->count();

    $ramadhanStart = Carbon::create(2026, 2, 19);
    $today = Carbon::today();
    $hariKe = $today->gte($ramadhanStart) ? min((int) $ramadhanStart->diffInDays($today) + 1, 30) : 0;

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

    // Per-siswa progress
    $siswaProgress = $kelas->siswa->map(function ($siswa) use ($hariKe) {
      $submissions = FormSubmission::where('user_id', $siswa->id)->get();
      $total = $submissions->count();
      $verifiedCount = $submissions->where('status', 'verified')->count();
      $pendingCount  = $submissions->where('status', 'pending')->count();
      $rejectedCount = $submissions->where('status', 'rejected')->count();

      // Kesiswaan validation stats (only from guru-verified submissions)
      $verifiedSubs = $submissions->where('status', 'verified');
      $kesiswaanValidated = $verifiedSubs->where('kesiswaan_status', 'validated')->count();
      $kesiswaanPending   = $verifiedSubs->where('kesiswaan_status', 'pending')->count();
      $kesiswaanRejected  = $verifiedSubs->where('kesiswaan_status', 'rejected')->count();

      $rate = $hariKe > 0 ? round(($total / $hariKe) * 100) : 0;
      return [
        'name' => $siswa->name,
        'nisn' => $siswa->nisn ?? '-',
        'total' => $total,
        'verified' => $verifiedCount,
        'pending' => $pendingCount,
        'rejected' => $rejectedCount,
        'kesiswaan_validated' => $kesiswaanValidated,
        'kesiswaan_pending' => $kesiswaanPending,
        'kesiswaan_rejected' => $kesiswaanRejected,
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
