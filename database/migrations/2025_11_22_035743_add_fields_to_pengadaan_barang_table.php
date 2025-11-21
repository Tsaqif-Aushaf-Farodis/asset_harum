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
        Schema::table('pengadaan_barang', function (Blueprint $table) {
            $table->foreignId('permohonan_id')->nullable()->after('id')->constrained('permohonan')->onDelete('set null');
            $table->foreignId('detail_permohonan_id')->nullable()->after('permohonan_id')->constrained('detail_permohonan')->onDelete('set null');
            $table->boolean('is_active')->default(true)->after('keterangan');
            $table->boolean('is_borrowed')->default(false)->after('is_active');
            $table->string('current_user')->nullable()->after('is_borrowed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengadaan_barang', function (Blueprint $table) {
            $table->dropForeign(['permohonan_id']);
            $table->dropForeign(['detail_permohonan_id']);
            $table->dropColumn(['permohonan_id', 'detail_permohonan_id', 'is_active', 'is_borrowed', 'current_user']);
        });
    }
};
