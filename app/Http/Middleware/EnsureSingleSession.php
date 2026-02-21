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
     *  2. Session expiry check  — superadmin 24 jam, role lain 12 jam.
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

            // ── 2. Session expiry: superadmin 24 jam, lainnya 12 jam ───────────
            $maxHours = $this->isSuperadmin($user) ? 24 : 12;

            if (
                $user->session_login_at &&
                $user->session_login_at->addHours($maxHours)->isPast()
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
                    ->with('session_expired', "Sesi Anda telah berakhir setelah {$maxHours} jam. Silakan login kembali.");
            }
        }

        return $next($request);
    }

    /** Cek apakah user adalah superadmin. */
    private function isSuperadmin($user): bool
    {
        $roleName = strtolower($user->role_user?->name ?? '');
        return str_contains($roleName, 'super admin') || str_contains($roleName, 'superadmin');
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
