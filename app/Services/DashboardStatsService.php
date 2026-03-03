<?php

namespace App\Services;

use App\Models\FormSubmission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Centralized service for dashboard statistics.
 *
 * All queries are batched to prevent N+1 issues — one query
 * per stat type regardless of how many kelas/siswa exist.
 */
class DashboardStatsService
{
  /**
   * Get per-kelas submission stats using batch queries.
   *
   * Returns array keyed by kelas_id with stats:
   *  today_sub, total_sub, verified, pending, rejected
   *
   * @param  \Illuminate\Support\Collection  $kelasCollection  Kelas models (must have siswa loaded or withCount)
   * @param  string  $todayDate  e.g. '2026-02-19'
   * @param  int     $hariKe     Current Ramadhan day (0 if not Ramadhan)
   * @return array<string, array>
   */
  public function getPerKelasStats(Collection $kelasCollection, string $todayDate, int $hariKe): array
  {
    // Collect all kelas IDs
    $kelasIds = $kelasCollection->pluck('id')->toArray();

    if (empty($kelasIds)) {
      return [];
    }

    // Single query: aggregate stats per kelas using join
    $stats = FormSubmission::query()
      ->join('users', 'form_submissions.user_id', '=', 'users.id')
      ->whereIn('users.kelas_id', $kelasIds)
      ->select(
        'users.kelas_id',
        DB::raw('COUNT(*) as total_sub'),
        DB::raw("SUM(CASE WHEN form_submissions.status = 'verified' THEN 1 ELSE 0 END) as verified"),
        DB::raw("SUM(CASE WHEN form_submissions.status = 'pending' THEN 1 ELSE 0 END) as pending"),
        DB::raw("SUM(CASE WHEN form_submissions.status = 'rejected' THEN 1 ELSE 0 END) as rejected"),
      )
      ->groupBy('users.kelas_id')
      ->get()
      ->keyBy('kelas_id');

    // Today submissions per kelas (distinct user_id count)
    $todayStats = collect();
    if ($hariKe > 0) {
      $todayStats = FormSubmission::query()
        ->join('users', 'form_submissions.user_id', '=', 'users.id')
        ->whereIn('users.kelas_id', $kelasIds)
        ->whereDate('form_submissions.created_at', $todayDate)
        ->select(
          'users.kelas_id',
          DB::raw('COUNT(DISTINCT form_submissions.user_id) as today_sub')
        )
        ->groupBy('users.kelas_id')
        ->get()
        ->keyBy('kelas_id');
    }

    // Build result array
    $result = [];
    foreach ($kelasIds as $kelasId) {
      $s = $stats->get($kelasId);
      $t = $todayStats->get($kelasId);
      $result[$kelasId] = [
        'today_sub' => $t->today_sub ?? 0,
        'total_sub' => $s->total_sub ?? 0,
        'verified'  => $s->verified ?? 0,
        'pending'   => $s->pending ?? 0,
        'rejected'  => $s->rejected ?? 0,
      ];
    }

    return $result;
  }

  /**
   * Get per-siswa submission stats using batch queries.
   *
   * Returns array keyed by user_id with stats:
   *  total, verified, pending, rejected, kesiswaan_validated, kesiswaan_pending, kesiswaan_rejected
   *
   * @param  array  $siswaIds
   * @return array<string, array>
   */
  public function getPerSiswaStats(array $siswaIds): array
  {
    if (empty($siswaIds)) {
      return [];
    }

    $stats = FormSubmission::query()
      ->whereIn('user_id', $siswaIds)
      ->select(
        'user_id',
        DB::raw('COUNT(*) as total'),
        DB::raw("SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) as verified"),
        DB::raw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending"),
        DB::raw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected"),
        DB::raw("SUM(CASE WHEN status = 'verified' AND kesiswaan_status = 'validated' THEN 1 ELSE 0 END) as kesiswaan_validated"),
        DB::raw("SUM(CASE WHEN status = 'verified' AND kesiswaan_status = 'pending' THEN 1 ELSE 0 END) as kesiswaan_pending"),
        DB::raw("SUM(CASE WHEN status = 'verified' AND kesiswaan_status = 'rejected' THEN 1 ELSE 0 END) as kesiswaan_rejected"),
      )
      ->groupBy('user_id')
      ->get()
      ->keyBy('user_id');

    $result = [];
    foreach ($siswaIds as $id) {
      $s = $stats->get($id);
      $result[$id] = [
        'total'                => $s->total ?? 0,
        'verified'             => $s->verified ?? 0,
        'pending'              => $s->pending ?? 0,
        'rejected'             => $s->rejected ?? 0,
        'kesiswaan_validated'  => $s->kesiswaan_validated ?? 0,
        'kesiswaan_pending'    => $s->kesiswaan_pending ?? 0,
        'kesiswaan_rejected'   => $s->kesiswaan_rejected ?? 0,
      ];
    }

    return $result;
  }

  /**
   * Get which siswa submitted today, grouped by user_id.
   *
   * @param  array   $siswaIds
   * @param  string  $todayDate
   * @return array<string> user IDs that submitted today
   */
  public function getTodaySubmittedUserIds(array $siswaIds, string $todayDate): array
  {
    if (empty($siswaIds)) {
      return [];
    }

    return FormSubmission::whereIn('user_id', $siswaIds)
      ->whereDate('created_at', $todayDate)
      ->distinct()
      ->pluck('user_id')
      ->toArray();
  }

  /**
   * Get total submission counts per user for progress calculation.
   *
   * @param  array  $siswaIds
   * @return array<string, int>  [user_id => count]
   */
  public function getSubmissionCountsPerUser(array $siswaIds): array
  {
    if (empty($siswaIds)) {
      return [];
    }

    return FormSubmission::whereIn('user_id', $siswaIds)
      ->select('user_id', DB::raw('COUNT(*) as total'))
      ->groupBy('user_id')
      ->pluck('total', 'user_id')
      ->toArray();
  }

  /**
   * Get pending submission counts per kelas (for guru pending list).
   *
   * @param  array  $kelasIds
   * @return array<string, int>  [kelas_id => pending_count]
   */
  public function getPendingCountPerKelas(array $kelasIds): array
  {
    if (empty($kelasIds)) {
      return [];
    }

    return FormSubmission::query()
      ->join('users', 'form_submissions.user_id', '=', 'users.id')
      ->whereIn('users.kelas_id', $kelasIds)
      ->where('form_submissions.status', 'pending')
      ->select('users.kelas_id', DB::raw('COUNT(*) as pending_count'))
      ->groupBy('users.kelas_id')
      ->pluck('pending_count', 'kelas_id')
      ->toArray();
  }

  /**
   * Get all submissions for given user IDs grouped by user_id and hari_ke.
   *
   * @param  array  $siswaIds
   * @return \Illuminate\Support\Collection  grouped by user_id → plucked hari_ke
   */
  public function getSubmittedDaysPerUser(array $siswaIds): Collection
  {
    if (empty($siswaIds)) {
      return collect();
    }

    return FormSubmission::whereIn('user_id', $siswaIds)
      ->select('user_id', 'hari_ke')
      ->get()
      ->groupBy('user_id')
      ->map(fn($subs) => $subs->pluck('hari_ke')->toArray());
  }
}
