<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Models\PrayerCheckin;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrayerCheckinController extends Controller
{
    /**
     * Ambil semua check-in hari ini untuk user yang login.
     */
    public function today(): JsonResponse
    {
        $user = Auth::user();
        $checkins = PrayerCheckin::todayForUser($user->id);

        // Map ke associative array: shalat => {status, waktu_checkin}
        $result = [];
        foreach ($checkins as $c) {
            $result[$c->shalat] = [
                'status' => $c->status,
                'tipe' => $c->tipe,
                'waktu_checkin' => $c->waktu_checkin?->format('H:i'),
            ];
        }

        return response()->json([
            'success' => true,
            'tanggal' => now()->toDateString(),
            'checkins' => $result,
        ]);
    }

    /**
     * Ambil check-in untuk tanggal tertentu.
     */
    public function forDate(string $date): JsonResponse
    {
        $user = Auth::user();
        $checkins = PrayerCheckin::forDate($user->id, $date);

        $result = [];
        foreach ($checkins as $c) {
            $result[$c->shalat] = [
                'status' => $c->status,
                'tipe' => $c->tipe,
                'waktu_checkin' => $c->waktu_checkin?->format('H:i'),
            ];
        }

        return response()->json([
            'success' => true,
            'tanggal' => $date,
            'checkins' => $result,
        ]);
    }

    /**
     * Simpan / update check-in shalat.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'shalat' => 'required|string|in:subuh,dzuhur,ashar,maghrib,isya,tarawih,rowatib,tahajud,dhuha',
            'status' => 'required|string|in:jamaah,munfarid,ya,tidak',
        ]);

        $user = Auth::user();
        $shalat = $request->shalat;
        $status = $request->status;

        // Tentukan tipe berdasarkan nama shalat
        $tipe = in_array($shalat, PrayerCheckin::SHALAT_WAJIB) ? 'wajib' : 'sunnah';

        // Validasi status sesuai tipe
        if ($tipe === 'wajib' && !in_array($status, PrayerCheckin::STATUS_WAJIB)) {
            return response()->json([
                'success' => false,
                'message' => 'Status untuk shalat wajib harus: jamaah, munfarid, atau tidak.',
            ], 422);
        }

        if ($tipe === 'sunnah' && !in_array($status, PrayerCheckin::STATUS_SUNNAH)) {
            return response()->json([
                'success' => false,
                'message' => 'Status untuk shalat sunnah harus: ya atau tidak.',
            ], 422);
        }

        $checkin = PrayerCheckin::updateOrCreate(
            [
                'user_id' => $user->id,
                'tanggal' => now()->toDateString(),
                'shalat' => $shalat,
            ],
            [
                'tipe' => $tipe,
                'status' => $status,
                'waktu_checkin' => now(),
            ]
        );

        // Sync ke form_submissions jika ada
        $this->syncToFormSubmission($user, $shalat, $status);

        return response()->json([
            'success' => true,
            'message' => 'Check-in ' . ucfirst($shalat) . ' berhasil disimpan.',
            'checkin' => [
                'shalat' => $checkin->shalat,
                'status' => $checkin->status,
                'tipe' => $checkin->tipe,
                'waktu_checkin' => $checkin->waktu_checkin->format('H:i'),
            ],
        ]);
    }

    /**
     * Sync check-in data ke form_submissions (jika sudah ada submission untuk hari ini).
     */
    private function syncToFormSubmission($user, string $shalat, string $status): void
    {
        // Hitung hari ke- Ramadhan dari tanggal hari ini
        $ramadhanStart = Carbon::create(2026, 2, 19);
        $today = Carbon::today();
        $hariKe = $ramadhanStart->diffInDays($today) + 1;

        if ($hariKe < 1 || $hariKe > 30) return;

        $submission = FormSubmission::where('user_id', $user->id)
            ->where('hari_ke', $hariKe)
            ->first();

        if (!$submission) return;

        $data = $submission->data;
        if (!is_array($data)) $data = [];

        // Map check-in ke field formulir
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

        $submission->update(['data' => $data]);
    }
}
