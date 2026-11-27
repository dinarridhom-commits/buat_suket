<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SuratUndanganController;
use App\Http\Controllers\SuketAbsenController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TrashController;

// Autentikasi (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Lupa & Atur Ulang Kata Sandi (OTP Email)
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token?}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Logout (Auth only)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Area Internal Dilindungi Autentikasi (Protected by Auth)
Route::middleware('auth')->group(function () {

    // Dashboard Utama
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Master Pegawai
    Route::prefix('pegawai')->name('pegawai.')->group(function () {
        Route::get('/', [PegawaiController::class, 'index'])->name('index');
        Route::get('/search', [PegawaiController::class, 'search'])->name('search');
        Route::get('/ekspor-csv', [PegawaiController::class, 'exportCsv'])->name('export.csv');
        Route::get('/ekspor-excel', [PegawaiController::class, 'exportCsv'])->name('export.excel');
        Route::post('/impor', [PegawaiController::class, 'importCsv'])->name('import');
        Route::post('/simpan', [PegawaiController::class, 'store'])->name('store');
        Route::put('/{id}/update', [PegawaiController::class, 'update'])->name('update');
        Route::delete('/hapus-massal', [PegawaiController::class, 'destroyBulk'])->name('destroy.bulk');
        Route::delete('/{id}/hapus', [PegawaiController::class, 'destroy'])->name('destroy');
    });

    // Modul Persuratan (Surat Undangan & Suket Lupa Absen)
    Route::prefix('surat')->name('surat.')->group(function () {
        // Surat Undangan
        Route::get('/undangan', [SuratUndanganController::class, 'index'])->name('undangan.index');
        Route::get('/undangan/ekspor-excel', [SuratUndanganController::class, 'exportExcel'])->name('undangan.export.excel');
        Route::get('/undangan/buat', [SuratUndanganController::class, 'create'])->name('undangan.create');
        Route::post('/undangan/simpan', [SuratUndanganController::class, 'store'])->name('undangan.store');
        Route::delete('/undangan/hapus-massal', [SuratUndanganController::class, 'destroyBulk'])->name('undangan.destroy.bulk');
        Route::get('/undangan/{id}', [SuratUndanganController::class, 'show'])->name('undangan.show');
        Route::get('/undangan/{id}/edit', [SuratUndanganController::class, 'edit'])->name('undangan.edit');
        Route::put('/undangan/{id}', [SuratUndanganController::class, 'update'])->name('undangan.update');
        Route::patch('/undangan/{id}/status', [SuratUndanganController::class, 'updateStatus'])->name('undangan.status');
        Route::delete('/undangan/{id}/hapus', [SuratUndanganController::class, 'destroy'])->name('undangan.destroy');

        // Suket Lupa Absen (Masuk / Pulang)
        Route::get('/suket', [SuketAbsenController::class, 'index'])->name('suket.index');
        Route::get('/suket/ekspor-excel', [SuketAbsenController::class, 'exportExcel'])->name('suket.export.excel');
        Route::get('/suket/buat', [SuketAbsenController::class, 'create'])->name('suket.create');
        Route::post('/suket/simpan', [SuketAbsenController::class, 'store'])->name('suket.store');
        Route::delete('/suket/hapus-massal', [SuketAbsenController::class, 'destroyBulk'])->name('suket.destroy.bulk');
        Route::get('/suket/{id}', [SuketAbsenController::class, 'show'])->name('suket.show');
        Route::get('/suket/{id}/edit', [SuketAbsenController::class, 'edit'])->name('suket.edit');
        Route::put('/suket/{id}', [SuketAbsenController::class, 'update'])->name('suket.update');
        Route::delete('/suket/{id}/hapus', [SuketAbsenController::class, 'destroy'])->name('suket.destroy');
    });

    // Pengaturan Kop & Instansi
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Riwayat Penghapusan (Soft Delete — Pulihkan / Hapus Permanen)
    Route::prefix('riwayat-penghapusan')->name('trash.')->group(function () {
        Route::get('/', [TrashController::class, 'index'])->name('index');
        Route::post('/{type}/aksi-massal', [TrashController::class, 'bulk'])->name('bulk');
        Route::delete('/{type}/kosongkan', [TrashController::class, 'empty'])->name('empty');
        Route::post('/{type}/{id}/pulihkan', [TrashController::class, 'restore'])->name('restore');
        Route::delete('/{type}/{id}/permanen', [TrashController::class, 'forceDelete'])->name('force-delete');
    });

    // Profil & Ganti Kata Sandi
    Route::get('/profil', [AuthController::class, 'showProfile'])->name('profil.index');
    Route::put('/profil', [AuthController::class, 'updateProfile'])->name('profil.update');
    Route::put('/profil/sandi', [AuthController::class, 'changePassword'])->name('profil.password');

    // Panduan Penggunaan
    Route::get('/panduan', fn () => view('panduan.index'))->name('panduan.index');

});
