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
        // Clear session tracking when user logs out
        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                /** @var \App\Models\User $user */
                $user = $event->user;
                $user->update([
                    'active_session_id' => null,
                    'session_login_at' => null,
                ]);
            }
        });
    }
}
