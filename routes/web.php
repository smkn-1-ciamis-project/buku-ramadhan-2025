<?php

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
    if (!auth()->check()) {
        return redirect('/siswa/login');
    }
    return view('siswa.dashboard');
})->name('siswa.dashboard');
