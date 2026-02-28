<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class EloquentUserRepository implements UserRepositoryInterface
{
  public function updatePassword(User $user, string $newPassword): bool
  {
    return $user->update([
      'password' => $newPassword,
      'must_change_password' => false,
    ]);
  }

  public function updateActiveSession(User $user, string $sessionId): void
  {
    $user->updateQuietly([
      'active_session_id' => $sessionId,
    ]);
  }
}
