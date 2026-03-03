<?php

namespace App\Repositories\Contracts;

use App\Models\ActivityLog;
use App\Models\User;

interface ActivityLogRepositoryInterface
{
  /**
   * Create an activity log entry.
   */
  public function log(string $activity, ?User $user = null, array $extra = []): ActivityLog;

  /**
   * Get recent activity logs with user eager-loaded.
   *
   * @return \Illuminate\Database\Eloquent\Collection<ActivityLog>
   */
  public function getRecent(int $limit = 20): \Illuminate\Database\Eloquent\Collection;

  /**
   * Get activity logs for a specific user.
   *
   * @return \Illuminate\Database\Eloquent\Collection<ActivityLog>
   */
  public function getForUser(string $userId, int $limit = 50): \Illuminate\Database\Eloquent\Collection;

  /**
   * Truncate all activity logs.
   */
  public function truncate(): void;
}
