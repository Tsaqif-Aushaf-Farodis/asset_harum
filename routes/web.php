<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\KategoriBarangController;
use App\Http\Controllers\MasterLokasiController;
use App\Http\Controllers\MasterSatuanController;
use App\Http\Controllers\MasterBarangController;
use App\Http\Controllers\MasterStatusController;
use App\Http\Controllers\PengadaanBarangController;
use App\Http\Controllers\MasterSubLokasiController;
use App\Http\Controllers\TanahController;
use App\Http\Controllers\BangunanController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\MutasiAsetController;
use App\Http\Controllers\OpnameController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\LaporanController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('instansi', InstansiController::class);
    Route::resource('kategori-barang', KategoriBarangController::class);
    Route::resource('master-lokasi', MasterLokasiController::class);
    Route::resource('master-satuan', MasterSatuanController::class);
    Route::resource('master-barang', MasterBarangController::class);
    Route::resource('master-status', MasterStatusController::class);
    Route::resource('pengadaan-barang', PengadaanBarangController::class);
    Route::resource('master-sub-lokasi', MasterSubLokasiController::class);
    
    Route::resource('permohonan', \App\Http\Controllers\PermohonanController::class);

    // Asset routes
    Route::resource('tanah', TanahController::class);
    Route::resource('bangunan', BangunanController::class);
    Route::resource('kendaraan', KendaraanController::class);
    
    // QR Code routes
    Route::get('/tanah/{tanah}/qr-code', [TanahController::class, 'generateQrCode'])->name('tanah.qr-code');
    Route::get('/bangunan/{bangunan}/qr-code', [BangunanController::class, 'generateQrCode'])->name('bangunan.qr-code');
    Route::get('/kendaraan/{kendaraan}/qr-code', [KendaraanController::class, 'generateQrCode'])->name('kendaraan.qr-code');

    // Mutasi Aset routes
    Route::resource('mutasi-aset', MutasiAsetController::class);
    Route::post('/mutasi-aset/{mutasiAset}/approve', [MutasiAsetController::class, 'approve'])->name('mutasi-aset.approve');
    Route::post('/mutasi-aset/{mutasiAset}/reject', [MutasiAsetController::class, 'reject'])->name('mutasi-aset.reject');

    // Opname routes
    Route::resource('opname', OpnameController::class);
    Route::post('/opname/{opname}/start', [OpnameController::class, 'start'])->name('opname.start');
    Route::get('/opname/{opname}/input-hasil', [OpnameController::class, 'inputHasil'])->name('opname.input-hasil');
    Route::post('/opname/{opname}/save-hasil', [OpnameController::class, 'saveHasil'])->name('opname.save-hasil');
    Route::post('/opname/{opname}/complete', [OpnameController::class, 'complete'])->name('opname.complete');
    Route::get('/opname/{opname}/export-kertas-kerja', [OpnameController::class, 'exportKertasKerja'])->name('opname.export-kertas-kerja');
    Route::get('/opname/{opname}/export-laporan', [OpnameController::class, 'exportLaporan'])->name('opname.export-laporan');

    // Peminjaman routes
    Route::resource('peminjaman', PeminjamanController::class);
    Route::post('/peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve'])->name('peminjaman.approve');
    Route::post('/peminjaman/{peminjaman}/reject', [PeminjamanController::class, 'reject'])->name('peminjaman.reject');
    Route::get('/peminjaman/{peminjaman}/pengembalian', [PeminjamanController::class, 'pengembalian'])->name('peminjaman.pengembalian');
    Route::post('/peminjaman/{peminjaman}/pengembalian', [PeminjamanController::class, 'storePengembalian'])->name('peminjaman.store-pengembalian');
    Route::get('/peminjaman-riwayat', [PeminjamanController::class, 'riwayat'])->name('peminjaman.riwayat');

    // Laporan routes
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/inventaris', [LaporanController::class, 'inventaris'])->name('laporan.inventaris');
    Route::get('/laporan/mutasi', [LaporanController::class, 'mutasi'])->name('laporan.mutasi');
    Route::get('/laporan/opname', [LaporanController::class, 'opname'])->name('laporan.opname');
    Route::get('/laporan/peminjaman', [LaporanController::class, 'peminjaman'])->name('laporan.peminjaman');
    Route::get('/laporan/nilai-aset', [LaporanController::class, 'nilaiAset'])->name('laporan.nilai-aset');
    Route::get('/laporan/inventaris/export', [LaporanController::class, 'exportInventaris'])->name('laporan.inventaris.export');
    Route::get('/laporan/mutasi/export', [LaporanController::class, 'exportMutasi'])->name('laporan.mutasi.export');
    Route::get('/laporan/peminjaman/export', [LaporanController::class, 'exportPeminjaman'])->name('laporan.peminjaman.export');
    Route::get('/laporan/nilai-aset/export', [LaporanController::class, 'exportNilaiAset'])->name('laporan.nilai-aset.export');

    // Permohonan approval routes
    Route::post('/permohonan/{permohonan}/approve', [\App\Http\Controllers\PermohonanController::class, 'approve'])->name('permohonan.approve');
    Route::post('/permohonan/{permohonan}/reject', [\App\Http\Controllers\PermohonanController::class, 'reject'])->name('permohonan.reject');

    


});
