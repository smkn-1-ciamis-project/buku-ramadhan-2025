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
