<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;

class AuthActivityLogger
{
  /**
   * Handle Login event.
   * Prevents duplicate log within 30 seconds for the same user.
   */
  public function handleLogin(Login $event): void
  {
    if ($event->user instanceof User) {
      // Check if a login was already logged for this user within the last 30 seconds
      $recent = ActivityLog::where('user_id', $event->user->id)
        ->where('activity', 'login')
        ->where('created_at', '>=', now()->subSeconds(30))
        ->exists();

      if (!$recent) {
        ActivityLog::log('login', $event->user);
      }
    }
  }

  /**
   * Handle Logout event.
   */
  public function handleLogout(Logout $event): void
  {
    if ($event->user instanceof User) {
      ActivityLog::log('logout', $event->user);
    }
  }

  /**
   * Handle Failed login attempt.
   */
  public function handleFailed(Failed $event): void
  {
    $user = null;
    if ($event->user instanceof User) {
      $user = $event->user;
    }

    ActivityLog::log('login_failed', $user, [
      'credentials' => $event->credentials['email'] ?? $event->credentials['nisn'] ?? 'unknown',
    ]);
  }
}
