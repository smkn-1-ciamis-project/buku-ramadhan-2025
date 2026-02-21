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
   */
  public function handleLogin(Login $event): void
  {
    if ($event->user instanceof User) {
      ActivityLog::log('login', $event->user);
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
