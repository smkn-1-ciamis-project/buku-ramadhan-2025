<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\QuranRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuranController extends Controller
{
  public function __construct(
    private readonly QuranRepositoryInterface $quranRepo,
  ) {}

  /**
   * GET /api/quran/surahs
   * Returns the list of all 114 surahs.
   */
  public function surahs(): JsonResponse
  {
    $data = $this->quranRepo->getSurahList();

    return response()->json([
      'code' => 200,
      'data' => $data,
    ]);
  }

  /**
   * GET /api/quran/surah/{number}?edition=ar.alafasy
   * Returns merged ayahs (Arabic + translation + audio) for a surah.
   */
  public function surah(Request $request, int $number): JsonResponse
  {
    if ($number < 1 || $number > 114) {
      return response()->json(['code' => 422, 'message' => 'Nomor surah tidak valid (1-114).'], 422);
    }

    $edition = $request->query('edition', 'ar.alafasy');
    $result  = $this->quranRepo->getSurahAyahs($number, $edition);

    if (!$result['surah']) {
      return response()->json(['code' => 502, 'message' => 'Gagal memuat ayat dari server Quran.'], 502);
    }

    return response()->json([
      'code'  => 200,
      'surah' => $result['surah'],
      'ayahs' => $result['ayahs'],
    ]);
  }

  /**
   * GET /api/quran/reciters
   * Returns available audio reciter editions.
   */
  public function reciters(): JsonResponse
  {
    return response()->json([
      'code' => 200,
      'data' => $this->quranRepo->getReciters(),
    ]);
  }
}
