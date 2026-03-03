<?php

namespace App\Filament\Kesiswaan\Pages;

use App\Models\FormSubmission;
use App\Models\Kelas;
use App\Models\User;
use App\Services\DashboardStatsService;
use Carbon\Carbon;
use Filament\Pages\Page;

class Dashboard extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-home';
  protected static ?string $navigationLabel = 'Dashboard';
  protected static ?string $navigationGroup = 'Utama';
  protected static ?int $navigationSort = 1;
  protected static ?string $title = 'Dashboard Kesiswaan';
  protected static ?string $slug = '/';
  protected static string $view = 'filament.kesiswaan.pages.dashboard';

  public function getViewData(): array
  {
    $now = now('Asia/Jakarta');
    $statsService = app(DashboardStatsService::class);

    // Ramadhan calculation
    $ramadhanStart = Carbon::create(2026, 2, 19, 0, 0, 0, 'Asia/Jakarta');
    $ramadhanEnd   = Carbon::create(2026, 3, 20, 23, 59, 59, 'Asia/Jakarta');
    $isRamadhan    = $now->gte($ramadhanStart) && $now->lte($ramadhanEnd);
    $hariKe        = $isRamadhan ? min((int) $ramadhanStart->diffInDays($now) + 1, 30) : 0;

    // Hijri date
    $hijriCal = \IntlCalendar::createInstance(null, 'en_US@calendar=islamic-umalqura');
    $hijriCal->setTime($now->getTimestamp() * 1000);
    $hijriYear = $hijriCal->get(\IntlCalendar::FIELD_YEAR);
    $hijriMonthNames = [
      1 => 'Muharram',
      2 => 'Safar',
      3 => 'Rabiul Awal',
      4 => 'Rabiul Akhir',
      5 => 'Jumadil Awal',
      6 => 'Jumadil Akhir',
      7 => 'Rajab',
      8 => "Sya'ban",
      9 => 'Ramadhan',
      10 => 'Syawal',
      11 => "Dzulqa'dah",
      12 => 'Dzulhijjah',
    ];
    if ($isRamadhan) {
      $hijriDay = $hariKe;
      $hijriMonth = 9;
    } else {
      $hijriDay   = $hijriCal->get(\IntlCalendar::FIELD_DAY_OF_MONTH);
      $hijriMonth = $hijriCal->get(\IntlCalendar::FIELD_MONTH) + 1;
    }
    $hijriDate = $hijriDay . ' ' . ($hijriMonthNames[$hijriMonth] ?? '') . ' ' . $hijriYear . ' H';

    // Global stats
    $totalSiswa    = User::whereHas('role_user', fn($q) => $q->where('name', 'Siswa'))->count();
    $totalGuru     = User::whereHas('role_user', fn($q) => $q->where('name', 'Guru'))->count();
    $totalKelas    = Kelas::count();
    $totalFormulir = FormSubmission::count();
    $totalPending  = FormSubmission::where('status', 'pending')->count();
    $totalVerified = FormSubmission::where('status', 'verified')->count();
    $totalRejected = FormSubmission::where('status', 'rejected')->count();

    // Kesiswaan validation stats (only from guru-verified)
    $menungguValidasi = FormSubmission::where('status', 'verified')->where('kesiswaan_status', 'pending')->count();
    $sudahDivalidasi  = FormSubmission::where('status', 'verified')->where('kesiswaan_status', 'validated')->count();
    $ditolakKesiswaan = FormSubmission::where('status', 'verified')->where('kesiswaan_status', 'rejected')->count();
    $validasiRate     = $totalVerified > 0 ? round(($sudahDivalidasi / $totalVerified) * 100) : 0;

    // Today's stats
    $siswaSubmitToday = $hariKe > 0
      ? FormSubmission::whereDate('created_at', $now->toDateString())->distinct('user_id')->count('user_id')
      : 0;
    $belumSubmitToday = $totalSiswa - $siswaSubmitToday;
    $complianceRate   = $totalSiswa > 0 ? round(($siswaSubmitToday / $totalSiswa) * 100) : 0;
    $verifyRate       = $totalFormulir > 0 ? round(($totalVerified / $totalFormulir) * 100) : 0;

    // Per-kelas overview — batch queries (2 queries instead of 5×N)
    $kelasAll = Kelas::withCount('siswa')->with('wali')->orderBy('nama')->get();
    $perKelasStats = $statsService->getPerKelasStats($kelasAll, $now->toDateString(), $hariKe);

    $kelasOverview = $kelasAll->map(function ($k) use ($perKelasStats) {
      $s = $perKelasStats[$k->id] ?? ['today_sub' => 0, 'total_sub' => 0, 'verified' => 0, 'pending' => 0, 'rejected' => 0];
      $todayRate = $k->siswa_count > 0 ? round(($s['today_sub'] / $k->siswa_count) * 100) : 0;
      return [
        'nama'         => $k->nama,
        'wali'         => $k->wali->name ?? '-',
        'siswa_count'  => $k->siswa_count,
        'today_sub'    => $s['today_sub'],
        'today_rate'   => $todayRate,
        'total_sub'    => $s['total_sub'],
        'verified'     => $s['verified'],
        'pending'      => $s['pending'],
        'rejected'     => $s['rejected'],
      ];
    });

    // Recent activity (latest 10 verified/rejected by guru)
    $recentVerified = FormSubmission::with(['user.kelas', 'verifier'])
      ->whereIn('status', ['verified', 'rejected'])
      ->whereNotNull('verified_by')
      ->orderBy('verified_at', 'desc')
      ->limit(10)
      ->get()
      ->map(fn($s) => [
        'user_name'  => $s->user->name ?? '-',
        'user_kelas' => $s->user->kelas->nama ?? '-',
        'hari_ke'    => $s->hari_ke,
        'status'     => $s->status,
        'verifier'   => $s->verifier->name ?? '-',
        'verified_at' => $s->verified_at?->diffForHumans(),
        'verified_at_full' => $s->verified_at?->translatedFormat('d M Y, H:i'),
      ]);

    // Guru yang belum verifikasi — batch query (1 query instead of N)
    $guruKelas = Kelas::with('wali')->whereNotNull('wali_id')->get();
    $pendingPerKelas = $statsService->getPendingCountPerKelas($guruKelas->pluck('id')->toArray());

    $guruPending = $guruKelas->map(function ($k) use ($pendingPerKelas) {
      return [
        'guru'    => $k->wali->name ?? '-',
        'kelas'   => $k->nama,
        'pending' => $pendingPerKelas[$k->id] ?? 0,
      ];
    })->filter(fn($g) => $g['pending'] > 0)->sortByDesc('pending')->values()->take(10);

    return compact(
      'hariKe',
      'isRamadhan',
      'hijriDate',
      'hijriYear',
      'totalSiswa',
      'totalGuru',
      'totalKelas',
      'totalFormulir',
      'totalPending',
      'totalVerified',
      'totalRejected',
      'menungguValidasi',
      'sudahDivalidasi',
      'ditolakKesiswaan',
      'validasiRate',
      'siswaSubmitToday',
      'belumSubmitToday',
      'complianceRate',
      'verifyRate',
      'kelasOverview',
      'recentVerified',
      'guruPending'
    );
  }
}
