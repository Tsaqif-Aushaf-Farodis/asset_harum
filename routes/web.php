<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\KategoriBarangController;
use App\Http\Controllers\MasterLokasiController;
use App\Http\Controllers\MasterSatuanController;
use App\Http\Controllers\MasterBarangController;
use App\Http\Controllers\MasterStatusController;
use App\Http\Controllers\PengadaanBarangController;
use App\Http\Controllers\MasterSubLokasiController;
use App\Http\Controllers\DummyController;
use Illuminate\Support\Facades\Route;

require('auth.php');

/*
 * Tidak bisa diakses jika sudah login.
 */
Route::middleware('guest')->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('landing');
});

/*
 * Perlu login untuk mengakses
 */
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('instansi', InstansiController::class);
    Route::resource('kategori-barang', KategoriBarangController::class);
    Route::resource('master-lokasi', MasterLokasiController::class);
    Route::resource('master-satuan', MasterSatuanController::class);
    Route::resource('master-barang', MasterBarangController::class);
    Route::resource('master-status', MasterStatusController::class);
    Route::resource('pengadaan-barang', PengadaanBarangController::class);
    Route::resource('master-sub-lokasi', MasterSubLokasiController::class);
    Route::resource('permohonan', \App\Http\Controllers\PermohonanController::class);


    Route::get('/generate-opname', [DummyController::class, 'generateOpname'])->name('dummy.generate-opname');
    Route::get('/opname', [DummyController::class, 'opname'])->name('dummy.opname');

    Route::get('/form-peminjaman', [DummyController::class, 'formPeminjaman'])->name('dummy.form-peminjaman');
    Route::get('/riwayat-peminjaman', [DummyController::class, 'riwayatPeminjaman'])->name('dummy.riwayat-peminjaman');

    Route::get('/form-pengembalian', [DummyController::class, 'formPengembalian'])->name('dummy.form-pengembalian');
    Route::get('/riwayat-pengembalian', [DummyController::class, 'riwayatPengembalian'])->name('dummy.riwayat-pengembalian');

    Route::get('/laporan-pengembalian', [DummyController::class, 'laporanPengembalian'])->name('dummy.laporan-pengembalian');
  


});
