<?php

namespace App\Filament\Guru\Pages;

use App\Models\FormSubmission;
use App\Models\User;
use App\Services\DashboardStatsService;
use App\Repositories\Contracts\KelasRepositoryInterface;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use IntlCalendar;

class Dashboard extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-home';
  protected static ?string $navigationLabel = 'Dashboard';
  protected static ?string $navigationGroup = 'Utama';
  protected static ?int $navigationSort = 1;
  protected static ?string $title = 'Dashboard Guru';
  protected static ?string $slug = '/';
  protected static string $view = 'filament.guru.pages.dashboard';

  public function getViewData(): array
  {
    $guru = Auth::user();
    $kelasRepo = app(KelasRepositoryInterface::class);
    $statsService = app(DashboardStatsService::class);

    $kelasList = $kelasRepo->getForWali($guru->id);

    // Hitung hari Ramadhan berdasarkan tanggal resmi Kemenag RI
    // 1 Ramadhan 1447H Indonesia = 19 Februari 2026
    $indonesiaRamadhanStart = Carbon::create(2026, 2, 19, 0, 0, 0, 'Asia/Jakarta');
    $indonesiaRamadhanEnd   = Carbon::create(2026, 3, 20, 23, 59, 59, 'Asia/Jakarta'); // 30 Ramadhan
    $now = now('Asia/Jakarta');

    $isRamadhan = $now->gte($indonesiaRamadhanStart) && $now->lte($indonesiaRamadhanEnd);
    $hariKe = 0;
    $hijriDay = 0;

    if ($isRamadhan) {
      $hariKe = (int) $indonesiaRamadhanStart->diffInDays($now) + 1;
      $hariKe = min($hariKe, 30);
      $hijriDay = $hariKe; // Sinkron: hari ke = tanggal Ramadhan
    }

    // Gunakan IntlCalendar hanya untuk bulan non-Ramadhan
    $hijriCal = IntlCalendar::createInstance(null, 'en_US@calendar=islamic-umalqura');
    $hijriCal->setTime($now->getTimestamp() * 1000);
    $hijriYear = $hijriCal->get(IntlCalendar::FIELD_YEAR);

    if (!$isRamadhan) {
      $hijriDay   = $hijriCal->get(IntlCalendar::FIELD_DAY_OF_MONTH);
      $hijriMonth = $hijriCal->get(IntlCalendar::FIELD_MONTH) + 1; // 0-indexed
    } else {
      $hijriMonth = 9; // Ramadhan
    }

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
    $hijriMonthName = $hijriMonthNames[$hijriMonth] ?? '';
    $hijriDateFormatted = $hijriDay . ' ' . $hijriMonthName . ' ' . $hijriYear . ' H';

    $totalSiswa = 0;
    $allSiswaIds = [];
    $kelasOverview = [];

    // ── Collect all siswa IDs upfront ──────────────────────────────
    foreach ($kelasList as $kelas) {
      $ids = $kelas->siswa->pluck('id')->toArray();
      $allSiswaIds = array_merge($allSiswaIds, $ids);
      $totalSiswa += count($ids);
    }

    // ── Batch queries (2 queries total instead of 2×N) ─────────────
    $todayDate = now('Asia/Jakarta')->toDateString();
    $todaySubmittedIds = $hariKe > 0
      ? $statsService->getTodaySubmittedUserIds($allSiswaIds, $todayDate)
      : [];
    $submissionCounts = $statsService->getSubmissionCountsPerUser($allSiswaIds);

    // ── Build per-kelas overview (no more queries) ─────────────────
    foreach ($kelasList as $kelas) {
      $siswaIds = $kelas->siswa->pluck('id')->toArray();
      $siswaCount = count($siswaIds);

      $todaySubmissions = array_intersect($todaySubmittedIds, $siswaIds);
      $sudahSubmit = count($todaySubmissions);
      $belumSubmit = $siswaCount - $sudahSubmit;

      $siswaData = $kelas->siswa->sortBy('name')->map(function ($siswa) use ($todaySubmittedIds, $submissionCounts, $hariKe) {
        return [
          'id'             => $siswa->id,
          'name'           => $siswa->name,
          'nisn'           => $siswa->nisn,
          'jk'             => $siswa->jenis_kelamin,
          'today_submitted' => in_array($siswa->id, $todaySubmittedIds),
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

    // Global submission stats (use pre-fetched data — no extra queries)
    $totalSubmissionsToday = 0;
    $totalBelumToday = 0;
    if ($hariKe > 0) {
      $totalSubmissionsToday = count(array_unique($todaySubmittedIds));
      $totalBelumToday = $totalSiswa - $totalSubmissionsToday;
    }

    // === Pending verification submissions ===
    $pendingSubmissions = FormSubmission::whereIn('user_id', $allSiswaIds)
      ->where('status', 'pending')
      ->with('user')
      ->orderBy('created_at', 'desc')
      ->get()
      ->map(function ($sub) {
        return [
          'id'         => $sub->id,
          'user_name'  => $sub->user->name ?? '-',
          'user_nisn'  => $sub->user->nisn ?? '-',
          'user_jk'    => $sub->user->jenis_kelamin ?? 'L',
          'hari_ke'    => $sub->hari_ke,
          'created_at' => $sub->created_at->format('d M Y, H:i'),
        ];
      });

    // === Siswa yang belum mengisi per hari (detail hari mana saja yang kosong) ===
    $belumMengisiDetail = [];
    if ($hariKe > 0) {
      // Batch: get all submissions grouped by user → hari_ke (1 query)
      $allSubmissions = $statsService->getSubmittedDaysPerUser($allSiswaIds);

      // Get all siswa info (1 query)
      $allSiswa = User::whereIn('id', $allSiswaIds)
        ->select('id', 'name', 'nisn', 'jenis_kelamin')
        ->orderBy('name')
        ->get();

      foreach ($allSiswa as $siswa) {
        $submittedDays = $allSubmissions[$siswa->id] ?? [];
        $missingDays = [];
        for ($d = 1; $d <= $hariKe; $d++) {
          if (!in_array($d, $submittedDays)) {
            $missingDays[] = $d;
          }
        }

        if (count($missingDays) > 0) {
          $belumMengisiDetail[] = [
            'name'         => $siswa->name,
            'nisn'         => $siswa->nisn,
            'jk'           => $siswa->jenis_kelamin ?? 'L',
            'missing_days' => $missingDays,
            'missing_count' => count($missingDays),
            'total_days'   => $hariKe,
          ];
        }
      }

      // Sort by most missing first
      usort($belumMengisiDetail, fn($a, $b) => $b['missing_count'] <=> $a['missing_count']);
    }

    // Count pending verifications
    $totalPending = FormSubmission::whereIn('user_id', $allSiswaIds)
      ->where('status', 'pending')
      ->count();

    return [
      'guru'                  => $guru,
      'hariKe'                => $hariKe,
      'isRamadhan'            => $isRamadhan,
      'hijriDate'             => $hijriDateFormatted,
      'hijriYear'             => $hijriYear,
      'totalSiswa'            => $totalSiswa,
      'totalKelas'            => $kelasList->count(),
      'totalSubmissionsToday' => $totalSubmissionsToday,
      'totalBelumToday'       => $totalBelumToday,
      'kelasOverview'         => $kelasOverview,
      'pendingSubmissions'    => $pendingSubmissions,
      'belumMengisiDetail'    => $belumMengisiDetail,
      'totalPending'          => $totalPending,
    ];
  }
}
