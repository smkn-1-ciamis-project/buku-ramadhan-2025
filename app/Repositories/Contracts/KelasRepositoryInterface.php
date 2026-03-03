<?php

namespace App\Repositories\Contracts;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\Collection;

interface KelasRepositoryInterface
{
  /**
   * Get all kelas for a wali guru, with siswa eager-loaded.
   */
  public function getForWali(string $waliId): Collection;

  /**
   * Get all kelas with wali and siswa count, ordered by nama.
   */
  public function getAllWithStats(): Collection;

  /**
   * Get total kelas count.
   */
  public function count(): int;

  /**
   * Get kelas IDs for a specific wali.
   *
   * @return array<string>
   */
  public function getIdsForWali(string $waliId): array;

  /**
   * Get kelas names for filter dropdown.
   *
   * @return array<string, string> [id => nama]
   */
  public function pluckNamaById(): array;
}
