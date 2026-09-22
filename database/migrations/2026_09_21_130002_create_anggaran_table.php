<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggaran', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->foreignId('lokasi_id')->constrained('master_lokasi')->restrictOnDelete();
            $table->decimal('pagu', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tahun', 'lokasi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggaran');
    }
};
