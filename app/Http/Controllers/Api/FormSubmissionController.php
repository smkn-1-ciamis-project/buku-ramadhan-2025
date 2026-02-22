<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormSetting;
use App\Models\FormSubmission;
use App\Models\PrayerCheckin;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormSubmissionController extends Controller
{
  /**
   * Simpan / update formulir harian.
   */
  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'hari_ke' => 'required|integer|min:1|max:30',
      'data'    => 'required|array',
    ]);

    $user = Auth::user();

    // Check if form is active for user's religion
    $agama = $user->agama ?? 'Islam';
    $setting = FormSetting::where('agama', $agama)->first();

    if ($setting && !$setting->is_active) {
      return response()->json([
        'success' => false,
        'message' => 'Formulir untuk agama ' . $agama . ' sedang dinonaktifkan oleh kesiswaan.',
      ], 403);
    }

    $submission = FormSubmission::updateOrCreate(
      [
        'user_id' => $user->id,
        'hari_ke' => $request->hari_ke,
      ],
      [
        'data' => $request->data,
        'status' => 'pending',
        'verified_by' => null,
        'verified_at' => null,
        'catatan_guru' => null,
      ]
    );

    // ── Sync sholat data to prayer_checkins table ──
    $this->syncPrayerCheckins($user, $request->hari_ke, $request->data);

    return response()->json([
      'success' => true,
      'message' => 'Formulir hari ke-' . $request->hari_ke . ' berhasil disimpan.',
      'submission' => $submission,
    ]);
  }

  /**
   * Sync sholat/tarawih/sunat data from formulir to prayer_checkins table.
   */
  private function syncPrayerCheckins($user, int $hariKe, array $data): void
  {
    // 1 Ramadhan 1447H = 19 Feb 2026
    $ramadhanStart = Carbon::create(2026, 2, 19);
    $tanggal = $ramadhanStart->copy()->addDays($hariKe - 1)->toDateString();

    // Sholat fardu (subuh, dzuhur, ashar, maghrib, isya)
    if (isset($data['sholat']) && is_array($data['sholat'])) {
      foreach ($data['sholat'] as $key => $status) {
        if (in_array($key, PrayerCheckin::SHALAT_WAJIB) && in_array($status, PrayerCheckin::STATUS_WAJIB)) {
          PrayerCheckin::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => $tanggal, 'shalat' => $key],
            ['tipe' => 'wajib', 'status' => $status, 'waktu_checkin' => now()]
          );
        }
      }
    }

    // Tarawih
    if (isset($data['tarawih']) && in_array($data['tarawih'], PrayerCheckin::STATUS_WAJIB)) {
      PrayerCheckin::updateOrCreate(
        ['user_id' => $user->id, 'tanggal' => $tanggal, 'shalat' => 'tarawih'],
        ['tipe' => 'wajib', 'status' => $data['tarawih'], 'waktu_checkin' => now()]
      );
    }

    // Sholat sunnah (rowatib, tahajud, dhuha)
    if (isset($data['sunat']) && is_array($data['sunat'])) {
      foreach ($data['sunat'] as $key => $status) {
        if (in_array($key, PrayerCheckin::SHALAT_SUNNAH) && in_array($status, PrayerCheckin::STATUS_SUNNAH)) {
          PrayerCheckin::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => $tanggal, 'shalat' => $key],
            ['tipe' => 'sunnah', 'status' => $status, 'waktu_checkin' => now()]
          );
        }
      }
    }
  }

  /**
   * Ambil semua submission milik user yang login.
   */
  public function index(): JsonResponse
  {
    $user = Auth::user();

    $submissions = FormSubmission::where('user_id', $user->id)
      ->orderBy('hari_ke')
      ->get();

    return response()->json([
      'success' => true,
      'submissions' => $submissions,
      'submitted_days' => $submissions->pluck('hari_ke')->toArray(),
    ]);
  }

  /**
   * Ambil submission untuk hari tertentu.
   */
  public function show(int $hariKe): JsonResponse
  {
    $user = Auth::user();

    $submission = FormSubmission::where('user_id', $user->id)
      ->where('hari_ke', $hariKe)
      ->first();

    return response()->json([
      'success'    => true,
      'submission' => $submission,
    ]);
  }
}
