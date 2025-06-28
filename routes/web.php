<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController; // Import MahasiswaController

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

// Rute Halaman Depan
Route::get('/', function () {
    return redirect()->route('login');
});

// Rute Autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute yang Dilindungi oleh Middleware 'auth'
Route::middleware(['auth'])->group(function () {
    // Grup rute untuk Dosen
    Route::middleware(['peran:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dashboard');
        Route::get('/jadwal/{jadwal}', [DosenController::class, 'showJadwalDetail'])->name('jadwal.detail');
        Route::post('/jadwal/{jadwal}/scan', [DosenController::class, 'scanAbsensi'])->name('jadwal.scan');
        Route::post('/jadwal/{jadwal}/open', [DosenController::class, 'openClass'])->name('jadwal.open'); // New route
        Route::post('/jadwal/{jadwal}/close', [DosenController::class, 'closeClass'])->name('jadwal.close'); // New route
    });

    // Grup rute untuk Mahasiswa
    Route::middleware(['peran:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
        // Rute untuk menampilkan QR Code berdasarkan jadwal
        Route::get('/jadwal/{jadwal}/qrcode', [MahasiswaController::class, 'showQrCode'])->name('jadwal.qrcode');
    });
});