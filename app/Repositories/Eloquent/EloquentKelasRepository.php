<?php

namespace App\Repositories\Eloquent;

use App\Models\Kelas;
use App\Repositories\Contracts\KelasRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentKelasRepository implements KelasRepositoryInterface
{
  private const CACHE_TTL = 300; // 5 minutes

  /**
   * {@inheritdoc}
   */
  public function getForWali(string $waliId): Collection
  {
    return Kelas::where('wali_id', $waliId)
      ->with(['siswa' => fn($q) => $q->select('id', 'name', 'nisn', 'jenis_kelamin', 'kelas_id')->orderBy('name')])
      ->get();
  }

  /**
   * {@inheritdoc}
   */
  public function getAllWithStats(): Collection
  {
    return Kelas::withCount('siswa')
      ->with('wali:id,name')
      ->orderBy('nama')
      ->get();
  }

  /**
   * {@inheritdoc}
   */
  public function count(): int
  {
    return Cache::remember('kelas_count', self::CACHE_TTL, function () {
      return Kelas::count();
    });
  }

  /**
   * {@inheritdoc}
   */
  public function getIdsForWali(string $waliId): array
  {
    return Kelas::where('wali_id', $waliId)->pluck('id')->toArray();
  }

  /**
   * {@inheritdoc}
   */
  public function pluckNamaById(): array
  {
    return Cache::remember('kelas_nama_by_id', self::CACHE_TTL, function () {
      return Kelas::orderBy('nama')->pluck('nama', 'id')->toArray();
    });
  }
}
