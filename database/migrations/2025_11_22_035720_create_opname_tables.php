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
        // Tabel Opname Session (Header)
        Schema::create('opname_session', function (Blueprint $table) {
            $table->id();
            $table->string('nama_opname');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->foreignId('lokasi_id')->nullable()->constrained('master_sub_lokasi')->onDelete('set null');
            $table->enum('status', ['draft', 'ongoing', 'completed'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Tabel Opname Detail (Per-aset)
        Schema::create('opname_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opname_session_id')->constrained('opname_session')->onDelete('cascade');
            $table->foreignId('pengadaan_id')->constrained('pengadaan_barang')->onDelete('cascade');
            $table->string('kondisi_sistem')->nullable();
            $table->string('kondisi_fisik')->nullable();
            $table->enum('status_keberadaan', ['sesuai', 'tidak_sesuai', 'hilang', 'rusak', 'baru'])->default('sesuai');
            $table->string('lokasi_fisik')->nullable();
            $table->text('catatan')->nullable();
            $table->string('foto_aset')->nullable();
            $table->string('petugas_opname')->nullable();
            $table->date('tanggal_cek')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opname_detail');
        Schema::dropIfExists('opname_session');
    }
};
