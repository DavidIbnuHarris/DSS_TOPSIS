<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\TopsisController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckRole; 

// Rute Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// AREA UMUM (Bisa diakses Admin & User)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/', [TopsisController::class, 'kuesioner']);
    Route::post('/simpan-kuesioner', [TopsisController::class, 'simpanKuesioner']);
    Route::get('/hasil', [TopsisController::class, 'hasil']);
});

// ==========================================
// AREA KHUSUS ADMIN (Hanya boleh diakses Admin)
// ==========================================
Route::middleware(['auth', CheckRole::class.':admin'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/', [MasterController::class, 'index']);
        Route::post('/alternatif', [MasterController::class, 'storeAlternatif']);
        Route::post('/kriteria', [MasterController::class, 'storeKriteria']);
        Route::get('/perhitungan', [TopsisController::class, 'hitungTopsis']);
        Route::post('/skala', [MasterController::class, 'storeSkala']);
    });
});