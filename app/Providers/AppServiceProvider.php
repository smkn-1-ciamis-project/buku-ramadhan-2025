<?php

namespace App\Providers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use App\Listeners\AuthActivityLogger;

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
        // Register auth activity logger
        $logger = new AuthActivityLogger();
        Event::listen(Login::class, [$logger, 'handleLogin']);
        Event::listen(Failed::class, [$logger, 'handleFailed']);

        // Clear session tracking when user logs out.
        //
        // Jika logout dipicu oleh EnsureSingleSession (perangkat LAMA ditendang),
        // flag 'logout_kicked_by_single_session' sudah di-set → JANGAN hapus
        // active_session_id karena milik perangkat BARU.
        //
        // Jika user logout secara eksplisit (klik tombol logout), SELALU hapus
        // active_session_id agar user bisa login lagi di perangkat manapun.
        Event::listen(Logout::class, function (Logout $event) use ($logger) {
            if ($event->user) {
                // Log the logout activity
                $logger->handleLogout($event);

                // Jangan hapus jika ini adalah tendangan dari EnsureSingleSession
                if (app()->bound('logout_kicked_by_single_session')) {
                    return;
                }

                /** @var \App\Models\User $user */
                $user = $event->user;
                $user->update([
                    'active_session_id' => null,
                    'session_login_at'  => null,
                ]);
            }
        });
    }
}
