<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
  /**
   * Update user's password.
   */
  public function updatePassword(User $user, string $newPassword): bool;

  /**
   * Update user's active session ID.
   */
  public function updateActiveSession(User $user, string $sessionId): void;
}
