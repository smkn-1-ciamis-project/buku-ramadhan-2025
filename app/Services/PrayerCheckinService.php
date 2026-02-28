<?php

namespace App\Services;

use App\Models\FormSubmission;
use App\Models\PrayerCheckin;
use App\Models\User;
use App\Repositories\Contracts\FormSubmissionRepositoryInterface;
use App\Repositories\Contracts\PrayerCheckinRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PrayerCheckinService
{
  public function __construct(
    private PrayerCheckinRepositoryInterface $prayerCheckinRepo,
    private FormSubmissionRepositoryInterface $formSubmissionRepo,
  ) {}

  /**
   * Get the first unfilled Ramadhan day for the user.
   *
   * @return array{success: bool, tanggal: string, hari_ke: int, all_filled: bool}
   */
  public function getFirstUnfilled(User $user): array
  {
    $ramadhanStart = Carbon::create(2026, 2, 19);
    $today = Carbon::today();

    $maxDay = min(30, $ramadhanStart->diffInDays($today) + 1);

    if ($maxDay < 1) {
      return [
        'success' => true,
        'tanggal' => $today->toDateString(),
        'hari_ke' => 0,
        'all_filled' => false,
      ];
    }

    $filledDates = $this->prayerCheckinRepo->getFilledDates(
      $user,
      $ramadhanStart->toDateString(),
      $today->toDateString()
    );

    for ($day = 1; $day <= $maxDay; $day++) {
      $tanggal = $ramadhanStart->copy()->addDays($day - 1)->toDateString();
      if (!in_array($tanggal, $filledDates)) {
        return [
          'success' => true,
          'tanggal' => $tanggal,
          'hari_ke' => $day,
          'all_filled' => false,
        ];
      }
    }

    return [
      'success' => true,
      'tanggal' => $today->toDateString(),
      'hari_ke' => $maxDay,
      'all_filled' => true,
    ];
  }

  /**
   * Get today's checkins for user, mapped by shalat name.
   *
   * @return array<string, array{status: string, tipe: string, waktu_checkin: ?string}>
   */
  public function getTodayCheckins(User $user): array
  {
    $checkins = $this->prayerCheckinRepo->todayForUser($user);

    $mapped = [];
    foreach ($checkins as $c) {
      $mapped[$c->shalat] = [
        'status' => $c->status,
        'tipe' => $c->tipe,
        'waktu_checkin' => $c->waktu_checkin?->format('H:i'),
      ];
    }

    return $mapped;
  }

  /**
   * Get checkins for user on a specific date, mapped by shalat name.
   *
   * @return array<string, array{status: string, tipe: string, waktu_checkin: ?string}>
   */
  public function getForDate(User $user, string $date): array
  {
    $checkins = $this->prayerCheckinRepo->forDate($user, $date);

    $mapped = [];
    foreach ($checkins as $c) {
      $mapped[$c->shalat] = [
        'status' => $c->status,
        'tipe' => $c->tipe,
        'waktu_checkin' => $c->waktu_checkin?->format('H:i'),
      ];
    }

    return $mapped;
  }

  /**
   * Store a prayer check-in.
   *
   * @return array{success: bool, message: string, checkin?: array, status?: int}
   */
  public function storeCheckin(User $user, string $shalat, string $status, ?string $tanggal = null): array
  {
    $ramadhanStart = Carbon::create(2026, 2, 19);
    $tanggal = $tanggal ? Carbon::parse($tanggal)->toDateString() : now()->toDateString();

    // Validate Ramadhan date range
    $ramadhanEnd = $ramadhanStart->copy()->addDays(29)->toDateString();
    if ($tanggal < $ramadhanStart->toDateString() || $tanggal > $ramadhanEnd) {
      return [
        'success' => false,
        'message' => 'Tanggal harus dalam rentang bulan Ramadhan.',
        'status' => 422,
      ];
    }

    // Determine type
    $tipe = in_array($shalat, PrayerCheckin::SHALAT_WAJIB) ? 'wajib' : 'sunnah';

    // Validate status matches type
    if ($tipe === 'wajib' && !in_array($status, PrayerCheckin::STATUS_WAJIB)) {
      return [
        'success' => false,
        'message' => 'Status untuk shalat wajib harus: jamaah, munfarid, atau tidak.',
        'status' => 422,
      ];
    }

    if ($tipe === 'sunnah' && !in_array($status, PrayerCheckin::STATUS_SUNNAH)) {
      return [
        'success' => false,
        'message' => 'Status untuk shalat sunnah harus: ya atau tidak.',
        'status' => 422,
      ];
    }

    $checkin = $this->prayerCheckinRepo->updateOrCreate($user, $tanggal, $shalat, [
      'tipe' => $tipe,
      'status' => $status,
      'waktu_checkin' => now(),
    ]);

    // Sync to form_submissions if exists
    $this->syncToFormSubmission($user, $shalat, $status, $tanggal);

    // Bust submission cache
    Cache::forget("submissions_{$user->id}");

    return [
      'success' => true,
      'message' => 'Check-in ' . ucfirst($shalat) . ' berhasil disimpan.',
      'checkin' => [
        'shalat' => $checkin->shalat,
        'status' => $checkin->status,
        'tipe' => $checkin->tipe,
        'waktu_checkin' => $checkin->waktu_checkin->format('H:i'),
      ],
    ];
  }

  /**
   * Sync check-in data to form_submissions.
   */
  private function syncToFormSubmission(User $user, string $shalat, string $status, string $tanggal): void
  {
    $ramadhanStart = Carbon::create(2026, 2, 19);
    $target = Carbon::parse($tanggal);
    $hariKe = $ramadhanStart->diffInDays($target) + 1;

    if ($hariKe < 1 || $hariKe > 30) return;

    $submission = $this->formSubmissionRepo->findByUserAndDay($user, $hariKe);
    if (!$submission) return;

    $data = $submission->data;
    if (!is_array($data)) $data = [];

    $wajibFardu = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];

    if (in_array($shalat, $wajibFardu)) {
      if (!isset($data['sholat'])) $data['sholat'] = [];
      $data['sholat'][$shalat] = $status;
    } elseif ($shalat === 'tarawih') {
      $data['tarawih'] = $status;
    } elseif (in_array($shalat, PrayerCheckin::SHALAT_SUNNAH)) {
      if (!isset($data['sunat'])) $data['sunat'] = [];
      $data['sunat'][$shalat] = $status;
    }

    $this->formSubmissionRepo->updateData($submission, $data);
  }
}
