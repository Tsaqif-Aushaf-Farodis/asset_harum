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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengadaan_id')->constrained('pengadaan_barang')->onDelete('cascade');
            $table->string('peminjam_nama');
            $table->string('peminjam_nip')->nullable();
            $table->string('peminjam_instansi')->nullable();
            $table->string('peminjam_telepon')->nullable();
            $table->text('keperluan');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_rencana_kembali');
            $table->date('tanggal_kembali_aktual')->nullable();
            $table->foreignId('kondisi_pinjam_id')->constrained('master_status')->onDelete('cascade');
            $table->foreignId('kondisi_kembali_id')->nullable()->constrained('master_status')->onDelete('set null');
            $table->enum('status_peminjaman', [
                'pending',
                'approved',
                'borrowed',
                'returned',
                'overdue',
                'lost'
            ])->default('pending');
            $table->string('dokumen_peminjaman')->nullable();
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('peminjaman');
    }
};
