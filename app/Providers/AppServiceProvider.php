<?php

namespace App\Providers;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Clear session tracking when user logs out.
        //
        // IMPORTANT: Auth::logout() is also called inside EnsureSingleSession when
        // an OLD device is kicked (session mismatch). At that point session()->getId()
        // equals the OLD device's session — NOT the DB's active_session_id (which
        // already holds the NEW device's session). We MUST only wipe the DB when the
        // device doing the logout is the one that actually owns the active session,
        // otherwise we break the new device's 12-hour timer and single-session guard.
        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                /** @var \App\Models\User $user */
                $user = $event->user;

                // session()->getId() at this point is still the session of the device
                // being logged out (before session()->invalidate() is called).
                if (
                    $user->active_session_id !== null &&
                    $user->active_session_id === session()->getId()
                ) {
                    $user->update([
                        'active_session_id' => null,
                        'session_login_at'  => null,
                    ]);
                }
            }
        });
    }
}
