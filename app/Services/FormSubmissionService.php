<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\PrayerCheckin;
use App\Models\User;
use App\Repositories\Contracts\FormSettingRepositoryInterface;
use App\Repositories\Contracts\FormSubmissionRepositoryInterface;
use App\Repositories\Contracts\PrayerCheckinRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class FormSubmissionService
{
  public function __construct(
    private FormSubmissionRepositoryInterface $formSubmissionRepo,
    private FormSettingRepositoryInterface $formSettingRepo,
    private PrayerCheckinRepositoryInterface $prayerCheckinRepo,
  ) {}

  /**
   * Get all submissions for the given user.
   */
  public function getAllForUser(User $user): array
  {
    return $this->formSubmissionRepo->getAllForUser($user);
  }

  /**
   * Get a single submission for a specific day.
   */
  public function getForDay(User $user, int $hariKe): ?\App\Models\FormSubmission
  {
    return $this->formSubmissionRepo->getForUserAndDay($user, $hariKe);
  }

  /**
   * Store or update a form submission.
   *
   * @return array{success: bool, message: string, submission?: \App\Models\FormSubmission}
   */
  public function storeSubmission(User $user, int $hariKe, array $formData, bool $isDraft = false): array
  {
    // Check if form is active for user's religion
    $agama = $user->agama ?? 'Islam';
    $isMuslim = User::isMuslimAgama($agama);
    $setting = $this->formSettingRepo->getForAgama($agama);

    if ($setting && !$setting->is_active) {
      return [
        'success' => false,
        'message' => 'Formulir untuk agama ' . $agama . ' sedang dinonaktifkan oleh kesiswaan.',
        'status' => 403,
      ];
    }

    // Block re-submission if already validated by kesiswaan
    $existing = $this->formSubmissionRepo->findByUserAndDay($user, $hariKe);

    if ($existing && $existing->kesiswaan_status === 'validated') {
      return [
        'success' => false,
        'message' => 'Formulir hari ke-' . $hariKe . ' sudah divalidasi oleh kesiswaan dan tidak dapat diubah.',
        'status' => 403,
      ];
    }

    // Draft: hanya simpan data, jangan reset verifikasi, jangan ubah status jika sudah pending/verified
    if ($isDraft) {
      $updateData = ['data' => $formData];
      // Hanya set status draft jika belum pernah disubmit (belum ada record atau masih draft)
      if (!$existing || $existing->status === 'draft') {
        $updateData['status'] = 'draft';
      }
      $submission = $this->formSubmissionRepo->updateOrCreate($user, $hariKe, $updateData);

      // Sync sholat data to prayer_checkins (Muslim only)
      if ($isMuslim) {
        $this->syncPrayerCheckins($user, $hariKe, $formData);
      }

      return [
        'success' => true,
        'message' => 'Draft formulir hari ke-' . $hariKe . ' berhasil disimpan.',
        'submission' => $submission,
      ];
    }

    // Full submit: reset verifikasi
    $submission = $this->formSubmissionRepo->updateOrCreate($user, $hariKe, [
      'data' => $formData,
      'status' => 'pending',
      'verified_by' => null,
      'verified_at' => null,
      'catatan_guru' => null,
      'kesiswaan_status' => 'pending',
      'validated_by' => null,
      'validated_at' => null,
      'catatan_kesiswaan' => null,
    ]);

    // Sync sholat data to prayer_checkins (Muslim only)
    if ($isMuslim) {
      $this->syncPrayerCheckins($user, $hariKe, $formData);
    }

    // Bust additional caches
    Cache::forget("checkins_today_{$user->id}_" . now()->toDateString());
    Cache::forget("checkins_date_{$user->id}_" . now()->toDateString());

    // Log activity
    ActivityLog::log('submit_form', $user, [
      'description' => 'Mengirim formulir hari ke-' . $hariKe,
      'hari_ke' => $hariKe,
      'submission_id' => $submission->id,
      'is_update' => $existing ? true : false,
    ]);

    return [
      'success' => true,
      'message' => 'Formulir hari ke-' . $hariKe . ' berhasil disimpan.',
      'submission' => $submission,
    ];
  }

  /**
   * Sync sholat/tarawih/sunat data from formulir to prayer_checkins table.
   * Uses batch upsert (single query) instead of looping updateOrCreate.
   */
  private function syncPrayerCheckins(User $user, int $hariKe, array $data): void
  {
    // 1 Ramadhan 1447H = 19 Feb 2026
    $ramadhanStart = Carbon::create(2026, 2, 19);
    $tanggal = $ramadhanStart->copy()->addDays($hariKe - 1)->toDateString();
    $now = now();

    $records = [];

    // Sholat fardu
    if (isset($data['sholat']) && is_array($data['sholat'])) {
      foreach ($data['sholat'] as $key => $status) {
        if (in_array($key, PrayerCheckin::SHALAT_WAJIB) && in_array($status, PrayerCheckin::STATUS_WAJIB)) {
          $records[] = [
            'shalat' => $key,
            'tipe' => 'wajib',
            'status' => $status,
            'waktu_checkin' => $now,
          ];
        }
      }
    }

    // Tarawih
    if (isset($data['tarawih']) && in_array($data['tarawih'], PrayerCheckin::STATUS_WAJIB)) {
      $records[] = [
        'shalat' => 'tarawih',
        'tipe' => 'wajib',
        'status' => $data['tarawih'],
        'waktu_checkin' => $now,
      ];
    }

    // Sholat sunnah
    if (isset($data['sunat']) && is_array($data['sunat'])) {
      foreach ($data['sunat'] as $key => $status) {
        if (in_array($key, PrayerCheckin::SHALAT_SUNNAH) && in_array($status, PrayerCheckin::STATUS_SUNNAH)) {
          $records[] = [
            'shalat' => $key,
            'tipe' => 'sunnah',
            'status' => $status,
            'waktu_checkin' => $now,
          ];
        }
      }
    }

    // Single batch upsert instead of N separate updateOrCreate calls
    $this->prayerCheckinRepo->batchUpsert($user, $tanggal, $records);
  }
}
