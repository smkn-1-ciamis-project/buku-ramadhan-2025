<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class CheckDatabaseConnection
{
  private static ?bool $redisAvailable = null;

  /**
   * Cek koneksi database. Jika database tidak tersedia (dihapus/mati),
   * tampilkan halaman maintenance.
   */
  public function handle(Request $request, Closure $next): Response
  {
    $this->applyRedisFallbackIfNeeded();

    // Skip jika sudah di halaman maintenance
    if ($request->is('maintenance')) {
      return $next($request);
    }

    try {
      DB::connection()->getPdo();
    } catch (\Throwable $e) {
      return response()->view('errors.maintenance', [
        'message' => 'Database tidak tersedia. Silakan hubungi administrator.',
      ], 503);
    }

    return $next($request);
  }

  /**
   * Jika Redis tidak aktif, paksa cache/session pakai file agar aplikasi tetap jalan.
   */
  private function applyRedisFallbackIfNeeded(): void
  {
    if (self::$redisAvailable === null) {
      try {
        $pong = Redis::connection('default')->ping();
        self::$redisAvailable = ($pong === true) || (strtoupper((string) $pong) === 'PONG');
      } catch (\Throwable $e) {
        self::$redisAvailable = false;
      }
    }

    if (self::$redisAvailable) {
      return;
    }

    if (config('cache.default') === 'redis') {
      Config::set('cache.default', 'file');
    }

    if (config('session.driver') === 'redis') {
      Config::set('session.driver', 'file');
      Config::set('session.connection', null);
      Config::set('session.store', null);
    }
  }
}
