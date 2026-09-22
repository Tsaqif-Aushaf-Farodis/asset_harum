<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengadaan_barang', function (Blueprint $table) {
            $table->foreignId('anggaran_id')->nullable()->after('detail_permohonan_id')
                ->constrained('anggaran')->restrictOnDelete();
            $table->boolean('disusutkan')->nullable()->after('anggaran_id')
                ->comment('NULL = ikut master barang; true/false = penimpa per aset');
        });
    }

    public function down(): void
    {
        Schema::table('pengadaan_barang', function (Blueprint $table) {
            $table->dropForeign(['anggaran_id']);
            $table->dropColumn(['anggaran_id', 'disusutkan']);
        });
    }
};
