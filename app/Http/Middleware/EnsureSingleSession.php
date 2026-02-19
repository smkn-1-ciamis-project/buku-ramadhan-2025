<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleSession
{
    /**
     * Hanya izinkan satu sesi aktif per akun.
     * Jika session ID tidak cocok, user akan di-logout (perangkat lama ditendang).
     *
     * Juga cek auto-logout setelah 12 jam jika "Ingat Saya" dicentang.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // 1. Single session check: kick old device if session mismatch
            if ($user->active_session_id && $user->active_session_id !== session()->getId()) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                return redirect()->to(
                    filament()->getCurrentPanel()?->getLoginUrl() ?? '/siswa/login'
                )->with('session_expired', 'Sesi Anda telah berakhir karena akun ini login dari perangkat lain.');
            }

            // 2. Auto-logout after 12 hours from login time
            if ($user->session_login_at) {
                $maxHours = 12;
                $loginAt = $user->session_login_at;

                if (now()->diffInHours($loginAt, true) >= $maxHours) {
                    // Clear session tracking on user
                    $user->update([
                        'active_session_id' => null,
                        'session_login_at' => null,
                    ]);

                    Auth::logout();
                    session()->invalidate();
                    session()->regenerateToken();

                    return redirect()->to(
                        filament()->getCurrentPanel()?->getLoginUrl() ?? '/siswa/login'
                    )->with('session_expired', 'Sesi Anda telah berakhir setelah 12 jam. Silakan login kembali.');
                }
            }
        }

        return $next($request);
    }
}
