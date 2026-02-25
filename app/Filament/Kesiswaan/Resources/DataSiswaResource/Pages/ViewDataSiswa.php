<?php

namespace App\Filament\Kesiswaan\Resources\DataSiswaResource\Pages;

use App\Filament\Kesiswaan\Resources\DataSiswaResource;
use App\Models\FormSubmission;
use App\Models\PrayerCheckin;
use Carbon\Carbon;
use Filament\Resources\Pages\ViewRecord;

class ViewDataSiswa extends ViewRecord
{
  protected static string $resource = DataSiswaResource::class;

  protected static string $view = 'filament.kesiswaan.pages.view-data-siswa';

  protected function getViewData(): array
  {
    $user = $this->record;
    $user->load(['kelas.wali', 'role_user']);

    // Ramadhan calculation
    $ramadhanStart = Carbon::create(2026, 2, 19, 0, 0, 0, 'Asia/Jakarta');
    $ramadhanEnd   = Carbon::create(2026, 3, 20, 23, 59, 59, 'Asia/Jakarta');
    $now = now('Asia/Jakarta');
    $isRamadhan = $now->gte($ramadhanStart) && $now->lte($ramadhanEnd);
    $hariKe = $isRamadhan ? min((int) $ramadhanStart->diffInDays($now) + 1, 30) : 0;

    // Submission stats
    $submissions = FormSubmission::where('user_id', $user->id)
      ->orderBy('hari_ke')
      ->get();
    $totalSubmit = $submissions->count();
    $verified    = $submissions->where('status', 'verified')->count();
    $pending     = $submissions->where('status', 'pending')->count();
    $rejected    = $submissions->where('status', 'rejected')->count();
    $submittedDays = $submissions->pluck('hari_ke')->toArray();

    $missingDays = [];
    for ($d = 1; $d <= $hariKe; $d++) {
      if (!in_array($d, $submittedDays)) {
        $missingDays[] = $d;
      }
    }

    $progress = $hariKe > 0 ? round(($totalSubmit / $hariKe) * 100) : 0;
    $verifyRate = $totalSubmit > 0 ? round(($verified / $totalSubmit) * 100) : 0;

    // Per-day detail for the table
    $dayDetails = [];
    for ($d = 1; $d <= $hariKe; $d++) {
      $sub = $submissions->firstWhere('hari_ke', $d);
      $dayDetails[] = [
        'hari' => $d,
        'tanggal' => $ramadhanStart->copy()->addDays($d - 1)->translatedFormat('d M Y'),
        'status' => $sub ? $sub->status : 'belum',
        'verified_at' => $sub && $sub->verified_at ? $sub->verified_at->format('d/m/Y H:i') : null,
        'catatan_guru' => $sub->catatan_guru ?? null,
        'created_at' => $sub ? $sub->created_at->format('d/m/Y H:i') : null,
      ];
    }

    // Prayer checkin stats
    $totalPrayerDays = PrayerCheckin::where('user_id', $user->id)
      ->distinct('tanggal')
      ->count('tanggal');
    $totalJamaah = PrayerCheckin::where('user_id', $user->id)
      ->where('status', 'jamaah')
      ->count();
    $totalMunfarid = PrayerCheckin::where('user_id', $user->id)
      ->where('status', 'munfarid')
      ->count();

    // Streak calculation (consecutive days submitted)
    $streak = 0;
    for ($d = $hariKe; $d >= 1; $d--) {
      if (in_array($d, $submittedDays)) {
        $streak++;
      } else {
        break;
      }
    }

    // Last submission info
    $lastSubmission = $submissions->last();

    // Build calendar cells (7-column Mon–Sun grid, same as validasi page)
    $leadingEmpties = $ramadhanStart->dayOfWeekIso - 1; // Thursday = 4, so 3 leading empties
    $calendarCells  = [];

    for ($i = 0; $i < $leadingEmpties; $i++) {
      $calendarCells[] = null;
    }
    for ($d = 1; $d <= 30; $d++) {
      $date = $ramadhanStart->copy()->addDays($d - 1);
      $sub  = $submissions->firstWhere('hari_ke', $d);
      $calendarCells[] = [
        'ramadanDay'       => $d,
        'masehiDay'        => $date->day,
        'masehiMonthShort' => $date->locale('id')->isoFormat('MMM'),
        'isPast'           => $d <= $hariKe,
        'isToday'          => $d === $hariKe && $now->isSameDay($date),
        'status'           => $sub ? $sub->status : null,
      ];
    }
    // Trailing empties to complete last row
    $trailing = (7 - (count($calendarCells) % 7)) % 7;
    for ($i = 0; $i < $trailing; $i++) {
      $calendarCells[] = null;
    }

    return [
      'user' => $user,
      'hariKe' => $hariKe,
      'totalSubmit' => $totalSubmit,
      'verified' => $verified,
      'pending' => $pending,
      'rejected' => $rejected,
      'missingDays' => $missingDays,
      'progress' => min($progress, 100),
      'verifyRate' => $verifyRate,
      'dayDetails' => $dayDetails,
      'calendarCells' => $calendarCells,
      'totalPrayerDays' => $totalPrayerDays,
      'totalJamaah' => $totalJamaah,
      'totalMunfarid' => $totalMunfarid,
      'streak' => $streak,
      'lastSubmission' => $lastSubmission,
    ];
  }
}
