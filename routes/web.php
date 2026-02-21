<?php

use App\Http\Controllers\Api\FormSubmissionController;
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

// API Change Password (session-auth)
Route::middleware('auth')->post('/api/change-password', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:8|confirmed',
    ]);

    $user = Auth::user();

    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json(['success' => false, 'message' => 'Password lama tidak sesuai.'], 422);
    }

    $user->update([
        'password' => Hash::make($request->new_password),
        'must_change_password' => false,
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
