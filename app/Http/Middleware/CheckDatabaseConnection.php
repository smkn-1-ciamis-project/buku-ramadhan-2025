<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckDatabaseConnection
{
  /**
   * Cek koneksi database. Jika database tidak tersedia (dihapus/mati),
   * tampilkan halaman maintenance.
   */
  public function handle(Request $request, Closure $next): Response
  {
    // Skip jika sudah di halaman maintenance
    if ($request->is('maintenance')) {
      return $next($request);
    }

    try {
      DB::connection()->getPdo();
    } catch (\Exception $e) {
      return response()->view('errors.maintenance', [
        'message' => 'Database tidak tersedia. Silakan hubungi administrator.',
      ], 503);
    }

    return $next($request);
  }
}
