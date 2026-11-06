<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Pengelola\PengelolaTpsController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\PengelolaTpsMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/masuk', [LoginController::class, 'create'])->name('login');
    Route::post('/masuk', [LoginController::class, 'store'])->middleware('throttle:6,1');

    Route::get('/daftar', [RegisterController::class, 'create'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'store'])->middleware('throttle:6,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user?->isPengelolaTps()) {
            return redirect()->route('pengelola.dashboard');
        }

        return view('dashboard');
    })->name('dashboard');

    Route::post('/keluar', [LoginController::class, 'destroy'])->name('logout');
});

// Panel Admin (role: admin)
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', AdminMiddleware::class])
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        // Akun & Verifikasi
        Route::get('/akun', [AdminController::class, 'akun'])->name('akun');
        Route::patch('/akun/{user}/status', [AdminController::class, 'ubahStatusAkun'])->name('akun.status');

        // TPS & Kapasitas
        Route::get('/tps', [AdminController::class, 'tps'])->name('tps');
        Route::post('/tps', [AdminController::class, 'storeTps'])->name('tps.store');
        Route::patch('/tps/{tps}/verifikasi', [AdminController::class, 'verifikasiTps'])->name('tps.verifikasi');

        // Master Jenis Sampah & Poin
        Route::get('/jenis-sampah', [AdminController::class, 'jenisSampah'])->name('jenis-sampah');
        Route::post('/jenis-sampah', [AdminController::class, 'storeJenisSampah'])->name('jenis-sampah.store');
        Route::patch('/jenis-sampah/{jenisSampah}', [AdminController::class, 'updateJenisSampah'])->name('jenis-sampah.update');

        // Konten Edukasi & Pengumuman
        Route::get('/konten', [AdminController::class, 'konten'])->name('konten');
        Route::post('/konten', [AdminController::class, 'storeKonten'])->name('konten.store');
        Route::patch('/konten/{konten:konten_id}/status', [AdminController::class, 'statusKonten'])->name('konten.status');
    });

// Panel Pengelola TPS (role: pengelola_tps)
Route::prefix('pengelola')
    ->name('pengelola.')
    ->middleware(['auth', PengelolaTpsMiddleware::class])
    ->group(function () {
        Route::get('/', [PengelolaTpsController::class, 'index'])->name('dashboard');

        // Penimbangan & Konfirmasi Setoran Masuk
        Route::patch('/setoran/{setoran}/tiba', [PengelolaTpsController::class, 'konfirmasiTiba'])->name('setoran.tiba');
        Route::post('/setoran/{setoran}/timbang', [PengelolaTpsController::class, 'timbangSetoran'])->name('setoran.timbang');
        Route::patch('/setoran/{setoran}/tolak', [PengelolaTpsController::class, 'tolakSetoran'])->name('setoran.tolak');

        // Kapasitas per Jenis Sampah
        Route::get('/kapasitas', [PengelolaTpsController::class, 'kapasitas'])->name('kapasitas');
        Route::patch('/kapasitas/{jenisSampah}', [PengelolaTpsController::class, 'updateKapasitasJenis'])->name('kapasitas.update');
        Route::post('/kapasitas/{jenisSampah}/kosongkan', [PengelolaTpsController::class, 'kosongkanPetak'])->name('kapasitas.kosongkan');

        // Riwayat Setoran & Ulasan
        Route::get('/riwayat', [PengelolaTpsController::class, 'riwayat'])->name('riwayat');

        // Profil & Operasional TPS
        Route::get('/profil-tps', [PengelolaTpsController::class, 'profilTps'])->name('profil-tps');
        Route::patch('/profil-tps', [PengelolaTpsController::class, 'updateProfilTps'])->name('profil-tps.update');
        Route::post('/profil-tps/baru', [PengelolaTpsController::class, 'storeTpsBaru'])->name('profil-tps.store');

        // Notifikasi
        Route::post('/notifikasi/baca', [PengelolaTpsController::class, 'bacaNotifikasi'])->name('notifikasi.baca');
    });
