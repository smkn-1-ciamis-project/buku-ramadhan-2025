<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure rate limiters for API endpoints.
     */
    protected function configureRateLimiting(): void
    {
        // GET endpoints: 30 requests per minute per user
        RateLimiter::for('api-read', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Terlalu banyak permintaan. Silakan tunggu sebentar.',
                        'retry_after' => 60,
                    ], 429);
                });
        });

        // POST endpoints (form submit, check-in): 10 requests per minute per user
        RateLimiter::for('api-write', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Terlalu banyak permintaan. Silakan tunggu sebentar.',
                        'retry_after' => 60,
                    ], 429);
                });
        });

        // Change password: 5 requests per minute (brute-force protection)
        RateLimiter::for('api-password', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Terlalu banyak percobaan. Silakan tunggu 1 menit.',
                        'retry_after' => 60,
                    ], 429);
                });
        });
    }
}
