<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mutasi_aset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengadaan_id')->constrained('pengadaan_barang')->onDelete('cascade');
            $table->enum('jenis_mutasi', [
                'pindah_lokasi',
                'ubah_pengguna',
                'non_aktif',
                'barang_keluar',
                'penghapusan',
                'peminjaman',
                'pengembalian'
            ]);
            $table->foreignId('lokasi_asal_id')->nullable()->constrained('master_sub_lokasi')->onDelete('set null');
            $table->foreignId('lokasi_tujuan_id')->nullable()->constrained('master_sub_lokasi')->onDelete('set null');
            $table->string('pengguna_asal')->nullable();
            $table->string('pengguna_tujuan')->nullable();
            $table->date('tanggal_mutasi');
            $table->text('alasan')->nullable();
            $table->string('dokumen_pendukung')->nullable();
            $table->enum('status_mutasi', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_aset');
    }
};
