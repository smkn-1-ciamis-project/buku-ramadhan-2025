<?php

use App\Http\Controllers\Api\FormSubmissionController;
use App\Http\Controllers\Api\PrayerCheckinController;
use App\Http\Controllers\PageController;
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
Route::get('/', [PageController::class, 'index'])->name('index');

// Halaman Tim Pengembang (publik)
Route::get('/tim-pengembang', [PageController::class, 'timPengembang'])->name('tim-pengembang');

// Siswa Dashboard — standalone (no Filament template)
Route::get('/siswa', [PageController::class, 'siswaDashboard'])->name('siswa.dashboard');

// API Formulir Harian (session-auth + throttle)
Route::middleware(['auth', 'throttle:api-read'])->prefix('api/formulir')->group(function () {
    Route::get('/', [FormSubmissionController::class, 'index']);
    Route::get('/{hariKe}', [FormSubmissionController::class, 'show']);
});
Route::middleware(['auth', 'throttle:api-write'])->post('/api/formulir', [FormSubmissionController::class, 'store']);

// API Prayer Check-in (session-auth + throttle)
Route::middleware(['auth', 'throttle:api-read'])->prefix('api/prayer-checkins')->group(function () {
    Route::get('/today', [PrayerCheckinController::class, 'today']);
    Route::get('/first-unfilled', [PrayerCheckinController::class, 'firstUnfilled']);
    Route::get('/date/{date}', [PrayerCheckinController::class, 'forDate']);
});
Route::middleware(['auth', 'throttle:api-write'])->post('/api/prayer-checkins', [PrayerCheckinController::class, 'store']);

// API Change Password (session-auth + strict throttle)
Route::middleware(['auth', 'throttle:api-password'])->post('/api/change-password', [PageController::class, 'changePassword']);

// API Form Settings — serve dynamic form config per agama (throttle)
Route::middleware(['auth', 'throttle:api-read'])->get('/api/form-settings/{agama}', [PageController::class, 'formSettings']);

// API App Settings — serve dynamic app settings (API URLs, Ramadhan schedule, etc.) to frontend JS
Route::middleware(['auth', 'throttle:api-read'])->get('/api/app-settings', [PageController::class, 'appSettings']);

// Export Rekap Siswa (Guru) — prefix berbeda dari panel agar tidak konflik dengan Filament routing
Route::middleware(['auth', 'throttle:export'])->prefix('guru-exports')->group(function () {
    Route::get('/rekap-siswa', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        $roleName = strtolower(trim($user->role_user?->name ?? ''));
        abort_unless($roleName === 'guru', 403);
        return \App\Services\RekapExportService::exportRekapSiswa();
    })->name('guru.rekap-siswa.export');

    Route::get('/rekap-siswa/{siswa}', function (\App\Models\User $siswa) {
        $guru = \Illuminate\Support\Facades\Auth::user();
        $roleName = strtolower(trim($guru->role_user?->name ?? ''));
        abort_unless($roleName === 'guru', 403);
        $kelasIds = \App\Models\Kelas::where('wali_id', $guru->id)->pluck('id')->toArray();
        abort_unless(in_array($siswa->kelas_id, $kelasIds), 403);
        return \App\Services\RekapExportService::exportDetailSiswa($siswa);
    })->name('guru.rekap-siswa.export-detail');
});

// Export Validasi (Kesiswaan) — per kelas (satu/banyak) atau semua kelas
Route::middleware(['auth', 'throttle:export'])->prefix('kesiswaan-exports')->group(function () {
    Route::get('/validasi/{kelas?}', function (?string $kelas = null) {
        $user = \Illuminate\Support\Facades\Auth::user();
        $roleName = strtolower(trim($user->role_user?->name ?? ''));
        abort_unless(
            in_array($roleName, ['kesiswaan', 'superadmin', 'super admin', 'kepala sekolah']),
            403
        );
        // Support comma-separated kelas IDs for multi-select export
        $kelasIds = $kelas ? array_filter(explode(',', $kelas)) : null;
        return \App\Services\KesiswaanExportService::exportValidasi($kelasIds);
    })->name('kesiswaan.validasi.export');
});
