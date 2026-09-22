<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemakaian_perlengkapan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengadaan_id')->constrained('pengadaan_barang')->restrictOnDelete();
            $table->unsignedInteger('jumlah');
            $table->date('tanggal_pemakaian');
            $table->foreignId('lokasi_id')->constrained('master_sub_lokasi')->restrictOnDelete();
            $table->string('pemakai', 150);
            $table->text('keperluan')->nullable();
            $table->decimal('harga_satuan', 15, 2)->default(0)
                ->comment('Snapshot harga satuan batch saat dipakai');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['pengadaan_id', 'deleted_at']);
            $table->index('tanggal_pemakaian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemakaian_perlengkapan');
    }
};
