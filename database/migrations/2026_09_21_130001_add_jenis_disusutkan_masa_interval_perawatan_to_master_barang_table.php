<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_barang', function (Blueprint $table) {
            $table->enum('jenis_barang', ['peralatan', 'perlengkapan'])
                ->default('peralatan')
                ->after('kategori_barang_id');
            $table->boolean('disusutkan')->default(false)->after('jenis_barang')
                ->comment('Hanya Peralatan: apakah barang ini disusutkan');
            $table->unsignedSmallInteger('masa_pemakaian_bulan')->nullable()->after('disusutkan');
            $table->unsignedTinyInteger('interval_penyusutan_tahun')->default(1)->after('masa_pemakaian_bulan')
                ->comment('Nilai turun tiap N tahun');
            $table->boolean('butuh_perawatan')->default(false)->after('interval_penyusutan_tahun');
        });
    }

    public function down(): void
    {
        Schema::table('master_barang', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_barang',
                'disusutkan',
                'masa_pemakaian_bulan',
                'interval_penyusutan_tahun',
                'butuh_perawatan',
            ]);
        });
    }
};
