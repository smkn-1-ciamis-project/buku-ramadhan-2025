<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ImpersonateController extends Controller
{
    /**
     * Superadmin masuk sebagai user lain (SSO / impersonate).
     * Fork session agar tab superadmin tetap aktif.
     */
    public function loginAs(Request $request, User $user)
    {
        $admin = Auth::user();
        $adminRole = strtolower(trim($admin->role_user?->name ?? ''));

        // Hanya superadmin yang boleh impersonate
        if (! in_array($adminRole, ['super admin', 'superadmin'])) {
            abort(403);
        }

        // Jangan impersonate diri sendiri
        if ($admin->id === $user->id) {
            return back();
        }

        $adminId   = $admin->id;
        $adminName = $admin->name;

        // Log aktivitas
        ActivityLog::log('impersonate', $admin, [
            'description'    => "Masuk sebagai {$user->name} ({$user->role_user?->name})",
            'target_user_id' => $user->id,
            'target_user'    => $user->name,
        ]);

        // Fork session: simpan session superadmin lalu buat session baru
        // agar tab superadmin tidak logout
        session()->save();
        session()->setId(Str::random(40));
        session()->start();

        // Login sebagai user target di session baru
        Auth::login($user);
        session()->put('password_hash_' . Auth::getDefaultDriver(), $user->getAuthPassword());
        session()->put('impersonator_id', $adminId);
        session()->put('impersonator_name', $adminName);

        // Redirect ke panel yang sesuai
        $role = strtolower(trim($user->role_user?->name ?? ''));

        return match (true) {
            $role === 'siswa' => redirect('/siswa'),
            $role === 'guru' => redirect('/portal-guru-smkn1'),
            in_array($role, ['kesiswaan', 'kepala sekolah']) => redirect('/portal-kesiswaan-smkn1'),
            in_array($role, ['super admin', 'superadmin']) => redirect('/portal-admin-smkn1'),
            default => redirect('/'),
        };
    }

    /**
     * Kembali ke akun superadmin asli.
     */
    public function leaveImpersonation()
    {
        $impersonatorId = session()->get('impersonator_id');

        if (! $impersonatorId) {
            return redirect('/portal-admin-smkn1');
        }

        $admin = User::find($impersonatorId);

        if (! $admin) {
            session()->forget(['impersonator_id', 'impersonator_name']);
            return redirect('/portal-admin-smkn1/login');
        }

        // Log
        $currentUser = Auth::user();
        ActivityLog::log('leave_impersonate', $admin, [
            'description'    => "Kembali dari akun {$currentUser->name}",
            'target_user_id' => $currentUser->id,
            'target_user'    => $currentUser->name,
        ]);

        // Hapus session impersonation saat ini
        session()->invalidate();

        // Buat session baru dan login sebagai superadmin
        session()->regenerate();
        Auth::login($admin);
        session()->put('password_hash_' . Auth::getDefaultDriver(), $admin->getAuthPassword());

        return redirect('/portal-admin-smkn1');
    }
}
