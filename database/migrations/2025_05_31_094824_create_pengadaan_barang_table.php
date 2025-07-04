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
        Schema::create('pengadaan_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_inventaris')->unique();
            $table->foreignId('barang_id')->constrained('master_barang')->onDelete('cascade');
            $table->foreignId('lokasi_id')->constrained('master_lokasi')->onDelete('cascade');
            $table->string('sumber', 100);
            $table->enum('status', ['baru', 'bekas', 'hibah'])->default('baru');
            $table->foreignId('status_id')->constrained('master_status')->onDelete('cascade');
            $table->date('tanggal_pengadaan');
            $table->integer('jumlah');
            $table->foreignId('satuan_id')->constrained('master_satuan')->onDelete('cascade');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_harga', 15, 2);
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengadaan_barang');
    }
};
