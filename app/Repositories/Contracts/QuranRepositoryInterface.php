<?php

namespace App\Repositories\Contracts;

interface QuranRepositoryInterface
{
  /**
   * Get list of all 114 surahs.
   *
   * @return array
   */
  public function getSurahList(): array;

  /**
   * Get ayahs of a surah with Arabic text + audio from a specific reciter edition,
   * merged with Indonesian translation.
   *
   * @param int    $surahNumber  1-114
   * @param string $edition      e.g. 'ar.alafasy'
   * @return array ['surah' => [...], 'ayahs' => [...]]
   */
  public function getSurahAyahs(int $surahNumber, string $edition = 'ar.alafasy'): array;

  /**
   * Get available audio reciter editions.
   *
   * @return array [['id' => 'ar.alafasy', 'name' => 'Mishary Alafasy', ...], ...]
   */
  public function getReciters(): array;
}
