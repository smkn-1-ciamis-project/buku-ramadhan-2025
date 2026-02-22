<?php

use App\Http\Controllers\Api\FormSubmissionController;
use App\Http\Controllers\Api\PrayerCheckinController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to Siswa login
Route::get('/', function () {
    return redirect('/siswa/login');
})->name('index');

// Halaman Tim Pengembang (publik)
Route::get('/tim-pengembang', function () {
    return view('tim-pengembang');
})->name('tim-pengembang');

// Siswa Dashboard — standalone (no Filament template)
Route::get('/siswa', function () {
    if (!Auth::check()) {
        return redirect('/siswa/login');
    }
    return view('siswa.dashboard');
})->name('siswa.dashboard');

// API Formulir Harian (session-auth)
Route::middleware('auth')->prefix('api/formulir')->group(function () {
    Route::get('/', [FormSubmissionController::class, 'index']);
    Route::post('/', [FormSubmissionController::class, 'store']);
    Route::get('/{hariKe}', [FormSubmissionController::class, 'show']);
});

// API Prayer Check-in (session-auth)
Route::middleware('auth')->prefix('api/prayer-checkins')->group(function () {
    Route::get('/today', [PrayerCheckinController::class, 'today']);
    Route::get('/date/{date}', [PrayerCheckinController::class, 'forDate']);
    Route::post('/', [PrayerCheckinController::class, 'store']);
});

// API Change Password (session-auth)
Route::middleware('auth')->post('/api/change-password', function (\Illuminate\Http\Request $request) {
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
        'password' => Hash::make($request->new_password),
        'must_change_password' => false,
    ]);

    // Re-login agar session hash password diperbarui
    // sehingga AuthenticateSession tidak logout user
    Auth::login($user);

    // Update active_session_id agar EnsureSingleSession
    // tidak menendang user karena session ID berubah
    $user->updateQuietly([
        'active_session_id' => session()->getId(),
    ]);

    return response()->json(['success' => true, 'message' => 'Password berhasil diubah.']);
});

// API Form Settings — serve dynamic form config per agama
Route::middleware('auth')->get('/api/form-settings/{agama}', function (string $agama) {
    $setting = \App\Models\FormSetting::where('agama', $agama)->first();

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
});
