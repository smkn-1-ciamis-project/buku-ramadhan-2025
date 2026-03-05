<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    /**
     * Superadmin masuk sebagai user lain (SSO / impersonate).
     * Simpan ID superadmin asli di session agar bisa kembali.
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

        // Simpan ID superadmin asli di session
        session()->put('impersonator_id', $admin->id);
        session()->put('impersonator_name', $admin->name);

        // Log aktivitas
        ActivityLog::log('impersonate', $admin, [
            'description' => "Masuk sebagai {$user->name} ({$user->role_user?->name})",
            'target_user_id' => $user->id,
            'target_user' => $user->name,
        ]);

        // Login sebagai user target
        Auth::login($user);

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
            'description' => "Kembali dari akun {$currentUser->name}",
            'target_user_id' => $currentUser->id,
            'target_user' => $currentUser->name,
        ]);

        // Hapus session impersonation
        session()->forget(['impersonator_id', 'impersonator_name']);

        // Login kembali sebagai superadmin
        Auth::login($admin);

        return redirect('/portal-admin-smkn1');
    }
}
