<?php

namespace App\Filament\Guru\Pages;

use App\Models\FormSubmission;
use App\Models\Kelas;
use App\Models\User;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-home';
  protected static ?string $navigationLabel = 'Dashboard';
  protected static ?string $title = 'Dashboard Guru';
  protected static ?string $slug = '/';
  protected static string $view = 'filament.guru.pages.dashboard';

  public function getViewData(): array
  {
    $guru = Auth::user();
    $kelasList = Kelas::where('wali_id', $guru->id)->with('siswa')->get();

    // Hitung hari Ramadhan
    $ramadhanStart = Carbon::create(2026, 2, 18);
    $today = now();
    $hariKe = $today->gte($ramadhanStart) ? $ramadhanStart->diffInDays($today) + 1 : 0;
    $hariKe = min($hariKe, 30);

    $totalSiswa = 0;
    $allSiswaIds = [];
    $kelasOverview = [];

    foreach ($kelasList as $kelas) {
      $siswaIds = $kelas->siswa->pluck('id')->toArray();
      $allSiswaIds = array_merge($allSiswaIds, $siswaIds);
      $siswaCount = count($siswaIds);
      $totalSiswa += $siswaCount;

      // Siapa yang sudah submit hari ini
      $todaySubmissions = [];
      if ($hariKe > 0) {
        $todaySubmissions = FormSubmission::whereIn('user_id', $siswaIds)
          ->where('hari_ke', $hariKe)
          ->pluck('user_id')
          ->toArray();
      }

      $sudahSubmit = count($todaySubmissions);
      $belumSubmit = $siswaCount - $sudahSubmit;

      // Total submission per siswa (untuk progress bar)
      $submissionCounts = FormSubmission::whereIn('user_id', $siswaIds)
        ->select('user_id', DB::raw('COUNT(*) as total'))
        ->groupBy('user_id')
        ->pluck('total', 'user_id')
        ->toArray();

      // Siswa list with their submission status
      $siswaData = $kelas->siswa->sortBy('name')->map(function ($siswa) use ($todaySubmissions, $submissionCounts, $hariKe) {
        return [
          'id'             => $siswa->id,
          'name'           => $siswa->name,
          'nisn'           => $siswa->nisn,
          'jk'             => $siswa->jenis_kelamin,
          'today_submitted' => in_array($siswa->id, $todaySubmissions),
          'total_submitted' => $submissionCounts[$siswa->id] ?? 0,
          'progress'       => $hariKe > 0 ? round((($submissionCounts[$siswa->id] ?? 0) / $hariKe) * 100) : 0,
        ];
      })->values();

      $kelasOverview[] = [
        'kelas'         => $kelas,
        'siswa_count'   => $siswaCount,
        'sudah_submit'  => $sudahSubmit,
        'belum_submit'  => $belumSubmit,
        'siswa_data'    => $siswaData,
      ];
    }

    // Global submission stats
    $totalSubmissionsToday = 0;
    $totalBelumToday = 0;
    if ($hariKe > 0) {
      $totalSubmissionsToday = FormSubmission::whereIn('user_id', $allSiswaIds)
        ->where('hari_ke', $hariKe)
        ->count();
      $totalBelumToday = $totalSiswa - $totalSubmissionsToday;
    }

    return [
      'guru'                  => $guru,
      'hariKe'                => $hariKe,
      'totalSiswa'            => $totalSiswa,
      'totalKelas'            => $kelasList->count(),
      'totalSubmissionsToday' => $totalSubmissionsToday,
      'totalBelumToday'       => $totalBelumToday,
      'kelasOverview'         => $kelasOverview,
    ];
  }
}
