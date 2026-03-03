<?php

namespace App\Repositories\Api;

use App\Repositories\Contracts\QuranRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiQuranRepository implements QuranRepositoryInterface
{
  /** Cache TTL: surah list & reciters = 24 hours (static data) */
  private const CACHE_TTL_STATIC = 86400;

  /** Cache TTL: surah ayahs = 12 hours */
  private const CACHE_TTL_AYAHS = 43200;

  private const CACHE_PREFIX = 'quran_';

  /** Pre-defined reciters with display names (Indonesian-friendly) */
  private const RECITERS = [
    ['id' => 'ar.alafasy',              'name' => 'Mishary Rashid Alafasy',       'style' => 'Murattal'],
    ['id' => 'ar.abdulbasitmurattal',    'name' => 'Abdul Basit Abdul Samad',      'style' => 'Murattal'],
    ['id' => 'ar.abdurrahmaansudais',    'name' => 'Abdurrahman As-Sudais',        'style' => 'Murattal'],
    ['id' => 'ar.hudhaify',              'name' => 'Ali Al-Hudhaify',              'style' => 'Murattal'],
    ['id' => 'ar.minshawi',              'name' => 'Mohamed Siddiq Al-Minshawi',   'style' => 'Murattal'],
    ['id' => 'ar.husary',                'name' => 'Mahmoud Khalil Al-Husary',     'style' => 'Murattal'],
    ['id' => 'ar.muhammadayyoub',        'name' => 'Muhammad Ayyoub',              'style' => 'Murattal'],
    ['id' => 'ar.maaboremhah',           'name' => 'Maher Al-Muaiqly',            'style' => 'Murattal'],
    ['id' => 'ar.ibrahimakhbar',         'name' => 'Ibrahim Al-Akhdar',            'style' => 'Murattal'],
  ];

  /** Only allow these edition IDs to prevent injection */
  private array $allowedEditions;

  private string $apiBase;

  public function __construct()
  {
    $rawUrl = \App\Models\AppSetting::getValue('quran_api_url', 'https://api.alquran.cloud/v1/ayah/');
    $this->apiBase = rtrim(preg_replace('/ayah\/?$/', '', $rawUrl), '/');
    $this->allowedEditions = array_column(self::RECITERS, 'id');
  }

  /**
   * {@inheritdoc}
   */
  public function getSurahList(): array
  {
    return Cache::remember(self::CACHE_PREFIX . 'surah_list', self::CACHE_TTL_STATIC, function () {
      try {
        $response = Http::timeout(15)->get($this->apiBase . '/surah');

        if ($response->successful()) {
          $json = $response->json();
          if (($json['code'] ?? 0) === 200 && isset($json['data'])) {
            return $json['data'];
          }
        }

        Log::warning('Quran API: Failed to fetch surah list', [
          'status' => $response->status(),
        ]);
      } catch (\Throwable $e) {
        Log::error('Quran API: Surah list error', ['error' => $e->getMessage()]);
      }

      return [];
    });
  }

  /**
   * {@inheritdoc}
   */
  public function getSurahAyahs(int $surahNumber, string $edition = 'ar.alafasy'): array
  {
    if ($surahNumber < 1 || $surahNumber > 114) {
      return ['surah' => null, 'ayahs' => []];
    }

    // Validate edition to prevent injection
    if (!in_array($edition, $this->allowedEditions, true)) {
      $edition = 'ar.alafasy';
    }

    $cacheKey = self::CACHE_PREFIX . "surah_{$surahNumber}_{$edition}";

    return Cache::remember($cacheKey, self::CACHE_TTL_AYAHS, function () use ($surahNumber, $edition) {
      try {
        // Fetch Arabic+audio edition and Indonesian translation in parallel
        $responses = Http::pool(fn($pool) => [
          $pool->as('arabic')->timeout(20)->get("{$this->apiBase}/surah/{$surahNumber}/{$edition}"),
          $pool->as('translation')->timeout(20)->get("{$this->apiBase}/surah/{$surahNumber}/id.indonesian"),
        ]);

        $arJson = $responses['arabic']->json();
        $idJson = $responses['translation']->json();

        if (($arJson['code'] ?? 0) === 200 && ($idJson['code'] ?? 0) === 200) {
          $arData = $arJson['data'];
          $arAyahs = $arData['ayahs'] ?? [];
          $idAyahs = $idJson['data']['ayahs'] ?? [];

          $merged = [];
          for ($i = 0, $len = count($arAyahs); $i < $len; $i++) {
            $merged[] = [
              'numberInSurah' => $arAyahs[$i]['numberInSurah'],
              'juz'           => $arAyahs[$i]['juz'],
              'arabic'        => $arAyahs[$i]['text'],
              'translation'   => $idAyahs[$i]['text'] ?? '',
              'audio'         => $arAyahs[$i]['audio'] ?? '',
            ];
          }

          $surah = [
            'number'                 => $arData['number'],
            'name'                   => $arData['name'],
            'englishName'            => $arData['englishName'],
            'englishNameTranslation' => $arData['englishNameTranslation'] ?? '',
            'numberOfAyahs'          => $arData['numberOfAyahs'],
            'revelationType'         => $arData['revelationType'],
          ];

          return ['surah' => $surah, 'ayahs' => $merged];
        }

        Log::warning('Quran API: Failed to fetch ayahs', [
          'surah'   => $surahNumber,
          'edition' => $edition,
          'arCode'  => $arJson['code'] ?? null,
          'idCode'  => $idJson['code'] ?? null,
        ]);
      } catch (\Throwable $e) {
        Log::error('Quran API: Ayahs error', [
          'surah'   => $surahNumber,
          'edition' => $edition,
          'error'   => $e->getMessage(),
        ]);
      }

      return ['surah' => null, 'ayahs' => []];
    });
  }

  /**
   * {@inheritdoc}
   */
  public function getReciters(): array
  {
    return self::RECITERS;
  }
}
