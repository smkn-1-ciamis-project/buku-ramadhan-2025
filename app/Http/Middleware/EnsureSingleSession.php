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
     *
     * Urutan pemeriksaan per request:
     *  1. Single-session check  — jika session ID tidak cocok, perangkat LAMA dikeluarkan.
     *  2. 12-hour expiry check  — jika sudah 12 jam sejak login, paksa login ulang.
     *
     * Catatan penting:
     *  Ketika perangkat LAMA ditendang (case 1), Auth::logout() tetap dipanggil agar
     *  guard Laravel bersih. Listener di AppServiceProvider sudah aman karena hanya
     *  menghapus DB jika session()->getId() == active_session_id (tidak berlaku di sini).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // ── 1. Single-session: tendang perangkat LAMA ─────────────────────
            if (
                $user->active_session_id &&
                $user->active_session_id !== session()->getId()
            ) {
                // Flag agar Logout event listener TIDAK menghapus active_session_id
                // (karena session yang aktif milik perangkat BARU, bukan yang ini)
                app()->instance('logout_kicked_by_single_session', true);

                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                return redirect()->to($this->loginUrl($request))
                    ->with('session_expired', 'Sesi Anda telah berakhir karena akun ini login dari perangkat lain.');
            }

            // ── 2. 12-hour expiry: paksa login ulang setelah 12 jam ────────────
            if (
                $user->session_login_at &&
                $user->session_login_at->addHours(12)->isPast()
            ) {
                // Hapus tracking terlebih dahulu, BARU logout
                // (supaya Logout event listener tidak mencoba menghapus lagi)
                $user->updateQuietly([
                    'active_session_id' => null,
                    'session_login_at'  => null,
                ]);

                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                return redirect()->to($this->loginUrl($request))
                    ->with('session_expired', 'Sesi Anda telah berakhir setelah 12 jam. Silakan login kembali.');
            }
        }

        return $next($request);
    }

    /** Resolve login URL safely regardless of panel context. */
    private function loginUrl(Request $request): string
    {
        try {
            $panel = filament()->getCurrentPanel();
            if ($panel) {
                return $panel->getLoginUrl();
            }
        } catch (\Throwable) {
            // Fall through to path-based detection
        }

        // Detect panel from request path prefix
        $path = ltrim($request->getPathInfo(), '/');
        $prefix = explode('/', $path)[0] ?? 'siswa';

        return '/' . $prefix . '/login';
    }
}
