<?php

namespace App\Repositories\Contracts;

use App\Models\PrayerCheckin;
use App\Models\User;
use Illuminate\Support\Collection;

interface PrayerCheckinRepositoryInterface
{
  /**
   * Get all check-ins for a user on today's date.
   */
  public function todayForUser(User $user): Collection;

  /**
   * Get all check-ins for a user on a specific date.
   */
  public function forDate(User $user, string $date): Collection;

  /**
   * Get fully-filled dates (>= 9 shalat) for user within a date range.
   *
   * @return string[] Array of date strings
   */
  public function getFilledDates(User $user, string $startDate, string $endDate): array;

  /**
   * Create or update a prayer check-in.
   */
  public function updateOrCreate(User $user, string $tanggal, string $shalat, array $attributes): PrayerCheckin;

  /**
   * Batch upsert multiple prayer check-ins in a single query.
   *
   * @param  array<array{user_id: string, tanggal: string, shalat: string, tipe: string, status: string, waktu_checkin: string}>  $records
   */
  public function batchUpsert(User $user, string $tanggal, array $records): void;
}
