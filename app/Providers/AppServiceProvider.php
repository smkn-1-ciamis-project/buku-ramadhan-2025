<?php

namespace App\Providers;

use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use App\Repositories\Contracts\FormSettingRepositoryInterface;
use App\Repositories\Contracts\FormSubmissionRepositoryInterface;
use App\Repositories\Contracts\KelasRepositoryInterface;
use App\Repositories\Contracts\PrayerCheckinRepositoryInterface;
use App\Repositories\Contracts\QuranRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Api\ApiQuranRepository;
use App\Repositories\Eloquent\EloquentActivityLogRepository;
use App\Repositories\Eloquent\EloquentFormSettingRepository;
use App\Repositories\Eloquent\EloquentFormSubmissionRepository;
use App\Repositories\Eloquent\EloquentKelasRepository;
use App\Repositories\Eloquent\EloquentPrayerCheckinRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
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
        // ── Repository Pattern Bindings ─────────────────────────────────
        $this->app->bind(FormSubmissionRepositoryInterface::class, EloquentFormSubmissionRepository::class);
        $this->app->bind(PrayerCheckinRepositoryInterface::class, EloquentPrayerCheckinRepository::class);
        $this->app->bind(FormSettingRepositoryInterface::class, EloquentFormSettingRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(QuranRepositoryInterface::class, ApiQuranRepository::class);
        $this->app->bind(ActivityLogRepositoryInterface::class, EloquentActivityLogRepository::class);
        $this->app->bind(KelasRepositoryInterface::class, EloquentKelasRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Increase memory limit for Filament panels with heavy tables
        ini_set('memory_limit', '256M');

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
