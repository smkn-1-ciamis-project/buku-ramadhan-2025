<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Models\PrayerCheckin;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PrayerCheckinController extends Controller
{
    /**
     * Cari hari Ramadhan pertama yang belum di-checkin lengkap (< 9 shalat).
     * Kalau semua sudah diisi, kembalikan hari ini.
     */
    public function firstUnfilled(): JsonResponse
    {
        $user = Auth::user();
        $ramadhanStart = Carbon::create(2026, 2, 19);
        $today = Carbon::today();

        // Batas hari Ramadhan (1–30), jangan lewati hari ini
        $maxDay = min(30, $ramadhanStart->diffInDays($today) + 1);

        if ($maxDay < 1) {
            // Belum masuk Ramadhan
            return response()->json([
                'success' => true,
                'tanggal' => $today->toDateString(),
                'hari_ke' => 0,
                'all_filled' => false,
            ]);
        }

        // Ambil semua tanggal yang sudah terisi penuh (9 shalat)
        $filledDates = PrayerCheckin::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $ramadhanStart->toDateString(),
                $today->toDateString(),
            ])
            ->selectRaw('tanggal, COUNT(*) as total')
            ->groupBy('tanggal')
            ->having('total', '>=', 9)
            ->pluck('total', 'tanggal')
            ->keys()
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        // Cari hari pertama yang belum complete
        for ($day = 1; $day <= $maxDay; $day++) {
            $tanggal = $ramadhanStart->copy()->addDays($day - 1)->toDateString();
            if (!in_array($tanggal, $filledDates)) {
                return response()->json([
                    'success' => true,
                    'tanggal' => $tanggal,
                    'hari_ke' => $day,
                    'all_filled' => false,
                ]);
            }
        }

        // Semua sudah diisi, kembalikan hari ini
        return response()->json([
            'success' => true,
            'tanggal' => $today->toDateString(),
            'hari_ke' => $maxDay,
            'all_filled' => true,
        ]);
    }

    /**
     * Ambil semua check-in hari ini untuk user yang login.
     */
    public function today(): JsonResponse
    {
        $user = Auth::user();
        $today = now()->toDateString();
        $cacheKey = "checkins_today_{$user->id}_{$today}";

        $result = Cache::remember($cacheKey, 120, function () use ($user) {
            $checkins = PrayerCheckin::todayForUser($user->id);
            $mapped = [];
            foreach ($checkins as $c) {
                $mapped[$c->shalat] = [
                    'status' => $c->status,
                    'tipe' => $c->tipe,
                    'waktu_checkin' => $c->waktu_checkin?->format('H:i'),
                ];
            }
            return $mapped;
        });

        return response()->json([
            'success' => true,
            'tanggal' => $today,
            'checkins' => $result,
        ]);
    }

    /**
     * Ambil check-in untuk tanggal tertentu.
     */
    public function forDate(string $date): JsonResponse
    {
        $user = Auth::user();
        $cacheKey = "checkins_date_{$user->id}_{$date}";

        $result = Cache::remember($cacheKey, 300, function () use ($user, $date) {
            $checkins = PrayerCheckin::forDate($user->id, $date);
            $mapped = [];
            foreach ($checkins as $c) {
                $mapped[$c->shalat] = [
                    'status' => $c->status,
                    'tipe' => $c->tipe,
                    'waktu_checkin' => $c->waktu_checkin?->format('H:i'),
                ];
            }
            return $mapped;
        });

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
            'tanggal' => 'nullable|date|before_or_equal:today',
        ]);

        $user = Auth::user();
        $shalat = $request->shalat;
        $status = $request->status;

        // Gunakan tanggal yang dikirim, atau hari ini
        $ramadhanStart = Carbon::create(2026, 2, 19);
        $tanggal = $request->tanggal ? Carbon::parse($request->tanggal)->toDateString() : now()->toDateString();

        // Validasi: tanggal harus dalam rentang Ramadhan (19 Feb – 20 Mar 2026)
        $ramadhanEnd = $ramadhanStart->copy()->addDays(29)->toDateString();
        if ($tanggal < $ramadhanStart->toDateString() || $tanggal > $ramadhanEnd) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal harus dalam rentang bulan Ramadhan.',
            ], 422);
        }

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
                'tanggal' => $tanggal,
                'shalat' => $shalat,
            ],
            [
                'tipe' => $tipe,
                'status' => $status,
                'waktu_checkin' => now(),
            ]
        );

        // Sync ke form_submissions jika ada
        $this->syncToFormSubmission($user, $shalat, $status, $tanggal);

        // Bust caches
        $today = now()->toDateString();
        Cache::forget("checkins_today_{$user->id}_{$today}");
        Cache::forget("checkins_today_{$user->id}_{$tanggal}");
        Cache::forget("checkins_date_{$user->id}_{$tanggal}");
        Cache::forget("submissions_{$user->id}");

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
    private function syncToFormSubmission($user, string $shalat, string $status, ?string $tanggal = null): void
    {
        // Hitung hari ke- Ramadhan dari tanggal yang diberikan
        $ramadhanStart = Carbon::create(2026, 2, 19);
        $target = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
        $hariKe = $ramadhanStart->diffInDays($target) + 1;

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
