<?php

namespace App\Filament\Guru\Resources\RekapSiswaResource\Pages;

use App\Filament\Guru\Resources\RekapSiswaResource;
use App\Models\FormSubmission;
use Carbon\Carbon;
use Filament\Resources\Pages\ViewRecord;

class ViewRekapSiswa extends ViewRecord
{
  protected static string $resource = RekapSiswaResource::class;

  protected static string $view = 'filament.guru.pages.view-rekap-siswa';

  protected function getViewData(): array
  {
    $siswa = $this->record;
    $siswa->load(['kelas', 'formSubmissions']);

    $ramadhanStart = Carbon::create(2026, 2, 19);
    $today = Carbon::today();
    $hariKe = $today->gte($ramadhanStart) ? min((int) $ramadhanStart->diffInDays($today) + 1, 30) : 0;

    $submissions = $siswa->formSubmissions;
    $totalSubmissions = $submissions->count();
    $verified = $submissions->where('status', 'verified')->count();
    $pending  = $submissions->where('status', 'pending')->count();
    $rejected = $submissions->where('status', 'rejected')->count();
    $belumLapor = max($hariKe - $totalSubmissions, 0);

    $complianceRate = $hariKe > 0 ? round(($totalSubmissions / $hariKe) * 100) : 0;
    $complianceRate = min($complianceRate, 100);
    $verifyRate = $totalSubmissions > 0 ? round(($verified / $totalSubmissions) * 100) : 0;

    // Build daily grid (hari 1-30)
    $submissionMap = $submissions->keyBy('hari_ke');
    $dailyGrid = [];
    $maxHari = max($hariKe, 1);
    for ($i = 1; $i <= $maxHari; $i++) {
      $sub = $submissionMap->get($i);
      $dailyGrid[] = [
        'hari' => $i,
        'tanggal' => $ramadhanStart->copy()->addDays($i - 1)->translatedFormat('d M'),
        'status' => $sub ? $sub->status : 'belum',
        'submitted_at' => $sub ? $sub->created_at->translatedFormat('H:i') : null,
        'verified_at' => $sub && $sub->verified_at ? $sub->verified_at->translatedFormat('H:i') : null,
        'catatan' => $sub ? ($sub->catatan_guru ?? null) : null,
      ];
    }

    return [
      'siswa' => $siswa,
      'hariKe' => $hariKe,
      'totalSubmissions' => $totalSubmissions,
      'verified' => $verified,
      'pending' => $pending,
      'rejected' => $rejected,
      'belumLapor' => $belumLapor,
      'complianceRate' => $complianceRate,
      'verifyRate' => $verifyRate,
      'dailyGrid' => $dailyGrid,
    ];
  }
}
