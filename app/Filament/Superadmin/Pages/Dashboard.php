<?php

namespace App\Filament\Superadmin\Pages;

use App\Models\FormSubmission;
use App\Models\Kelas;
use App\Models\RoleUser;
use App\Models\User;
use Carbon\Carbon;
use Filament\Pages\Page;

class Dashboard extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-home';
  protected static ?string $navigationLabel = 'Dashboard';
  protected static ?string $navigationGroup = 'Utama';
  protected static ?int $navigationSort = 1;
  protected static ?string $title = 'Dashboard Superadmin';
  protected static ?string $slug = '/';
  protected static string $view = 'filament.superadmin.pages.dashboard';

  public function getViewData(): array
  {
    $now = now('Asia/Jakarta');

    // Statistik umum
    $totalGuru = User::whereHas('role_user', fn($q) => $q->where('name', 'Guru'))->count();
    $totalSiswa = User::whereHas('role_user', fn($q) => $q->where('name', 'Siswa'))->count();
    $totalKelas = Kelas::count();
    $totalRole = RoleUser::count();

    // Statistik formulir
    $totalFormulir = FormSubmission::count();
    $totalPending = FormSubmission::where('status', 'pending')->count();
    $totalVerified = FormSubmission::where('status', 'verified')->count();
    $totalRejected = FormSubmission::where('status', 'rejected')->count();

    // Hitung hari Ramadhan
    $indonesiaRamadhanStart = Carbon::create(2026, 2, 19, 0, 0, 0, 'Asia/Jakarta');
    $indonesiaRamadhanEnd = Carbon::create(2026, 3, 20, 23, 59, 59, 'Asia/Jakarta');
    $isRamadhan = $now->gte($indonesiaRamadhanStart) && $now->lte($indonesiaRamadhanEnd);
    $hariKe = 0;
    if ($isRamadhan) {
      $hariKe = min((int) $indonesiaRamadhanStart->diffInDays($now) + 1, 30);
    }

    // Formulir hari ini
    $formulirHariIni = FormSubmission::whereDate('created_at', $now->toDateString())->count();
    $siswaSubmitHariIni = FormSubmission::whereDate('created_at', $now->toDateString())
      ->distinct('user_id')
      ->count('user_id');

    // Tingkat kepatuhan hari ini
    $complianceRate = $totalSiswa > 0 ? round(($siswaSubmitHariIni / $totalSiswa) * 100) : 0;

    // Verifikasi rate
    $verifyRate = $totalFormulir > 0 ? round(($totalVerified / $totalFormulir) * 100) : 0;

    // Aktivitas terbaru (10 formulir terakhir)
    $recentSubmissions = FormSubmission::with(['user.kelas', 'verifier'])
      ->orderBy('created_at', 'desc')
      ->limit(10)
      ->get()
      ->map(fn($sub) => [
        'id' => $sub->id,
        'user_name' => $sub->user->name ?? '-',
        'user_kelas' => $sub->user->kelas->nama ?? '-',
        'hari_ke' => $sub->hari_ke,
        'status' => $sub->status,
        'created_at' => $sub->created_at->diffForHumans(),
        'created_at_full' => $sub->created_at->translatedFormat('d M Y, H:i'),
      ]);

    // Kelas overview with submission stats
    $kelasOverview = Kelas::withCount('siswa')
      ->with('wali')
      ->orderBy('nama')
      ->get()
      ->map(function ($k) use ($now, $hariKe) {
        $todaySubmissions = $hariKe > 0
          ? FormSubmission::whereDate('created_at', $now->toDateString())
          ->whereHas('user', fn($q) => $q->where('kelas_id', $k->id))
          ->distinct('user_id')
          ->count('user_id')
          : 0;

        return [
          'nama' => $k->nama,
          'wali' => $k->wali->name ?? '-',
          'siswa_count' => $k->siswa_count,
          'today_submissions' => $todaySubmissions,
          'rate' => $k->siswa_count > 0 ? round(($todaySubmissions / $k->siswa_count) * 100) : 0,
        ];
      });

    return [
      'totalGuru' => $totalGuru,
      'totalSiswa' => $totalSiswa,
      'totalKelas' => $totalKelas,
      'totalRole' => $totalRole,
      'totalFormulir' => $totalFormulir,
      'totalPending' => $totalPending,
      'totalVerified' => $totalVerified,
      'totalRejected' => $totalRejected,
      'hariKe' => $hariKe,
      'isRamadhan' => $isRamadhan,
      'formulirHariIni' => $formulirHariIni,
      'siswaSubmitHariIni' => $siswaSubmitHariIni,
      'complianceRate' => $complianceRate,
      'verifyRate' => $verifyRate,
      'recentSubmissions' => $recentSubmissions,
      'kelasOverview' => $kelasOverview,
    ];
  }
}
