<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\KategoriBarangController;
use App\Http\Controllers\MasterLokasiController;
use App\Http\Controllers\MasterSatuanController;
use App\Http\Controllers\MasterBarangController;
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
  


});
