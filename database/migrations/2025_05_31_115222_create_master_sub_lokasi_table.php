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
        Schema::create('master_sub_lokasi', function (Blueprint $table) {
            $table->id();
            //lokasi_id
            $table->foreignId('lokasi_id')->constrained('master_lokasi')->onDelete('cascade');
            //kode_sub_lokasi
            $table->string('kode_sub_lokasi')->unique();
            //nama_sub_lokasi
            $table->string('nama_sub_lokasi');
            //keterangan
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_sub_lokasi');
    }
};
