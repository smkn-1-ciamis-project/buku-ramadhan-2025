<?php

namespace App\Http\Controllers;

use App\Models\FormSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PageController extends Controller
{
  public function index(): RedirectResponse
  {
    return redirect('/siswa/login');
  }

  public function timPengembang(): View
  {
    return view('tim-pengembang');
  }

  public function siswaDashboard(): RedirectResponse|View
  {
    if (!Auth::check()) {
      return redirect('/siswa/login');
    }
    return view('siswa.dashboard');
  }

  public function changePassword(Request $request): JsonResponse
  {
    $request->validate([
      'current_password' => 'required|string',
      'new_password' => 'required|string|min:8|confirmed',
    ]);

    /** @var \App\Models\User $user */
    $user = Auth::user();

    if (!Hash::check($request->current_password, $user->password)) {
      return response()->json(['success' => false, 'message' => 'Password lama tidak sesuai.'], 422);
    }

    $user->update([
      'password' => $request->new_password,
      'must_change_password' => false,
    ]);

    // Re-login agar session hash password diperbarui
    Auth::login($user);

    $user->updateQuietly([
      'active_session_id' => session()->getId(),
    ]);

    return response()->json(['success' => true, 'message' => 'Password berhasil diubah.']);
  }

  public function formSettings(string $agama): JsonResponse
  {
    $setting = FormSetting::getForAgama($agama);

    if (!$setting) {
      return response()->json(['message' => 'Setting formulir belum dikonfigurasi untuk agama ini.'], 404);
    }

    if (!$setting->is_active) {
      return response()->json([
        'inactive' => true,
        'message' => 'Formulir untuk agama ' . $agama . ' sedang dinonaktifkan oleh kesiswaan.',
      ], 403);
    }

    return response()->json([
      'agama' => $setting->agama,
      'sections' => collect($setting->sections)->filter(fn($s) => $s['enabled'] ?? true)->values()->toArray(),
    ]);
  }
}
