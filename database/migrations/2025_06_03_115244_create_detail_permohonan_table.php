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
        Schema::create('detail_permohonan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonan')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('master_barang')->onDelete('restrict');
            $table->integer('volume')->default(1);
            $table->string('satuan', 50);
            $table->bigInteger('harga')->default(0);
            $table->bigInteger('jumlah')->default(0);
            $table->string('kode_ma', 50)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_permohonan');
    }
};
