<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PrayerCheckinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrayerCheckinController extends Controller
{
    public function __construct(
        private PrayerCheckinService $prayerCheckinService,
    ) {}

    /**
     * Check if user is Muslim. Non-Muslim users should not access prayer check-in.
     */
    private function isMuslim($user): bool
    {
        return \App\Models\User::isMuslimAgama($user->agama);
    }

    /**
     * Cari hari Ramadhan pertama yang belum di-checkin lengkap.
     */
    public function firstUnfilled(): JsonResponse
    {
        $user = Auth::user();

        if (!$this->isMuslim($user)) {
            return response()->json(['success' => false, 'message' => 'Fitur check-in shalat hanya untuk siswa Muslim.'], 403);
        }

        $result = $this->prayerCheckinService->getFirstUnfilled($user);

        return response()->json($result);
    }

    /**
     * Ambil semua check-in hari ini untuk user yang login.
     */
    public function today(): JsonResponse
    {
        $user = Auth::user();

        if (!$this->isMuslim($user)) {
            return response()->json(['success' => false, 'message' => 'Fitur check-in shalat hanya untuk siswa Muslim.'], 403);
        }

        $result = $this->prayerCheckinService->getTodayCheckins($user);

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

        if (!$this->isMuslim($user)) {
            return response()->json(['success' => false, 'message' => 'Fitur check-in shalat hanya untuk siswa Muslim.'], 403);
        }

        $result = $this->prayerCheckinService->getForDate($user, $date);

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

        if (!$this->isMuslim($user)) {
            return response()->json(['success' => false, 'message' => 'Fitur check-in shalat hanya untuk siswa Muslim.'], 403);
        }

        $result = $this->prayerCheckinService->storeCheckin(
            $user,
            $request->shalat,
            $request->status,
            $request->tanggal
        );

        $statusCode = $result['status'] ?? ($result['success'] ? 200 : 500);

        return response()->json(
            collect($result)->except('status')->toArray(),
            $statusCode
        );
    }
}
