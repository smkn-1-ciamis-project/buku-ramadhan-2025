<?php

namespace App\Repositories\Eloquent;

use App\Models\PrayerCheckin;
use App\Models\User;
use App\Repositories\Contracts\PrayerCheckinRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class EloquentPrayerCheckinRepository implements PrayerCheckinRepositoryInterface
{
  public function todayForUser(User $user): Collection
  {
    $today = now()->toDateString();
    $cacheKey = "checkins_today_{$user->id}_{$today}";

    return Cache::remember($cacheKey, 120, function () use ($user) {
      return PrayerCheckin::todayForUser($user->id);
    });
  }

  public function forDate(User $user, string $date): Collection
  {
    $cacheKey = "checkins_date_{$user->id}_{$date}";

    return Cache::remember($cacheKey, 300, function () use ($user, $date) {
      return PrayerCheckin::forDate($user->id, $date);
    });
  }

  public function getFilledDates(User $user, string $startDate, string $endDate): array
  {
    return PrayerCheckin::where('user_id', $user->id)
      ->whereBetween('tanggal', [$startDate, $endDate])
      ->selectRaw('tanggal, COUNT(*) as total')
      ->groupBy('tanggal')
      ->having('total', '>=', 9)
      ->pluck('total', 'tanggal')
      ->keys()
      ->map(fn($d) => Carbon::parse($d)->toDateString())
      ->toArray();
  }

  public function updateOrCreate(User $user, string $tanggal, string $shalat, array $attributes): PrayerCheckin
  {
    $checkin = PrayerCheckin::updateOrCreate(
      [
        'user_id' => $user->id,
        'tanggal' => $tanggal,
        'shalat' => $shalat,
      ],
      $attributes
    );

    // Bust caches
    $today = now()->toDateString();
    Cache::forget("checkins_today_{$user->id}_{$today}");
    Cache::forget("checkins_today_{$user->id}_{$tanggal}");
    Cache::forget("checkins_date_{$user->id}_{$tanggal}");

    return $checkin;
  }

  public function batchUpsert(User $user, string $tanggal, array $records): void
  {
    if (empty($records)) {
      return;
    }

    $now = now();
    $rows = array_map(fn(array $r) => [
      'id' => Str::orderedUuid()->toString(),
      'user_id' => $user->id,
      'tanggal' => $tanggal,
      'shalat' => $r['shalat'],
      'tipe' => $r['tipe'],
      'status' => $r['status'],
      'waktu_checkin' => $r['waktu_checkin'] ?? $now,
      'created_at' => $now,
      'updated_at' => $now,
    ], $records);

    PrayerCheckin::upsert(
      $rows,
      ['user_id', 'tanggal', 'shalat'],
      ['tipe', 'status', 'waktu_checkin', 'updated_at']
    );

    // Bust caches
    $today = $now->toDateString();
    Cache::forget("checkins_today_{$user->id}_{$today}");
    Cache::forget("checkins_today_{$user->id}_{$tanggal}");
    Cache::forget("checkins_date_{$user->id}_{$tanggal}");
  }
}
