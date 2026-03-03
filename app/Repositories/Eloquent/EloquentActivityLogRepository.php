<?php

namespace App\Repositories\Eloquent;

use App\Models\ActivityLog;
use App\Models\User;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;

class EloquentActivityLogRepository implements ActivityLogRepositoryInterface
{
  /**
   * {@inheritdoc}
   */
  public function log(string $activity, ?User $user = null, array $extra = []): ActivityLog
  {
    return ActivityLog::log($activity, $user, $extra);
  }

  /**
   * {@inheritdoc}
   */
  public function getRecent(int $limit = 20): \Illuminate\Database\Eloquent\Collection
  {
    return ActivityLog::with('user')
      ->orderBy('created_at', 'desc')
      ->limit($limit)
      ->get();
  }

  /**
   * {@inheritdoc}
   */
  public function getForUser(string $userId, int $limit = 50): \Illuminate\Database\Eloquent\Collection
  {
    return ActivityLog::where('user_id', $userId)
      ->orderBy('created_at', 'desc')
      ->limit($limit)
      ->get();
  }

  /**
   * {@inheritdoc}
   */
  public function truncate(): void
  {
    ActivityLog::truncate();
  }
}
