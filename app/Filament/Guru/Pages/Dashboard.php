<?php

namespace App\Filament\Guru\Pages;

use App\Models\FormSubmission;
use App\Models\Kelas;
use App\Models\User;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use IntlCalendar;

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

    // Hitung hari Ramadhan dari kalender Hijriah riil (Um Al-Qura)
    $hijriCal = IntlCalendar::createInstance(null, 'en_US@calendar=islamic-umalqura');
    $hijriCal->setTime(now()->getTimestamp() * 1000);
    $hijriDay   = $hijriCal->get(IntlCalendar::FIELD_DAY_OF_MONTH);
    $hijriMonth = $hijriCal->get(IntlCalendar::FIELD_MONTH) + 1; // 0-indexed
    $hijriYear  = $hijriCal->get(IntlCalendar::FIELD_YEAR);

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

    $isRamadhan = ($hijriMonth === 9);

    // Hitung hari ke puasa berdasarkan tanggal resmi Kemenag RI
    // 1 Ramadhan 1447H Indonesia = 19 Februari 2026
    $indonesiaRamadhanStart = Carbon::create(2026, 2, 19, 0, 0, 0, 'Asia/Jakarta');
    $hariKe = 0;
    if ($isRamadhan && now('Asia/Jakarta')->gte($indonesiaRamadhanStart)) {
      $hariKe = (int) $indonesiaRamadhanStart->diffInDays(now('Asia/Jakarta')) + 1;
      $hariKe = min($hariKe, 30);
    }

    $totalSiswa = 0;
    $allSiswaIds = [];
    $kelasOverview = [];

    foreach ($kelasList as $kelas) {
      $siswaIds = $kelas->siswa->pluck('id')->toArray();
      $allSiswaIds = array_merge($allSiswaIds, $siswaIds);
      $siswaCount = count($siswaIds);
      $totalSiswa += $siswaCount;

      // Siapa yang sudah submit hari ini (berdasarkan tanggal created_at)
      $todaySubmissions = [];
      if ($hariKe > 0) {
        $todaySubmissions = FormSubmission::whereIn('user_id', $siswaIds)
          ->whereDate('created_at', now('Asia/Jakarta')->toDateString())
          ->pluck('user_id')
          ->unique()
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

    // Global submission stats (berdasarkan tanggal hari ini)
    $totalSubmissionsToday = 0;
    $totalBelumToday = 0;
    if ($hariKe > 0) {
      $submittedUserIds = FormSubmission::whereIn('user_id', $allSiswaIds)
        ->whereDate('created_at', now('Asia/Jakarta')->toDateString())
        ->pluck('user_id')
        ->unique()
        ->count();
      $totalSubmissionsToday = $submittedUserIds;
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
      // Get all submissions grouped by user
      $allSubmissions = FormSubmission::whereIn('user_id', $allSiswaIds)
        ->select('user_id', 'hari_ke')
        ->get()
        ->groupBy('user_id')
        ->map(fn($subs) => $subs->pluck('hari_ke')->toArray());

      // Get all siswa info
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
