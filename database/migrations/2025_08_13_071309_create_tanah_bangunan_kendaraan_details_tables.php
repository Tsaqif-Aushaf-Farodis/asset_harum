<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Detail Tanah
        Schema::create('tanah_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengadaan_id')->constrained('pengadaan_barang')->onDelete('cascade');
            $table->decimal('luas', 10, 2);
            $table->string('status_tanah')->nullable(); // Hak Milik, Sewa, dll
            $table->string('sertifikat_nomor')->nullable();
            $table->date('sertifikat_tanggal')->nullable();
            $table->string('penggunaan')->nullable();
            $table->text('lokasi')->nullable();
            $table->timestamps();
        });

        // Tabel Detail Bangunan
        Schema::create('bangunan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengadaan_id')->constrained('pengadaan_barang')->onDelete('cascade');
            $table->text('alamat')->nullable();
            $table->decimal('luas', 10, 2)->nullable();
            $table->integer('jumlah_lantai')->nullable();
            $table->string('bahan_bangunan')->nullable();
            $table->string('nomor_imb')->nullable();
            $table->date('tanggal_imb')->nullable();
            $table->enum('kondisi', ['baik', 'rusak ringan', 'rusak berat'])->default('baik');
            $table->timestamps();
        });

        // Tabel Detail Kendaraan
        Schema::create('kendaraan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengadaan_id')->constrained('pengadaan_barang')->onDelete('cascade');
            $table->string('merk')->nullable();
            $table->string('tipe')->nullable();
            $table->year('tahun_perakitan')->nullable();
            $table->string('no_polisi')->nullable();
            $table->string('no_rangka')->nullable();
            $table->string('no_mesin')->nullable();
            $table->integer('kapasitas_cc')->nullable();
            $table->string('warna')->nullable();
            $table->enum('kondisi', ['baik', 'rusak'])->default('baik');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraan_details');
        Schema::dropIfExists('bangunan_details');
        Schema::dropIfExists('tanah_details');
    }
};
