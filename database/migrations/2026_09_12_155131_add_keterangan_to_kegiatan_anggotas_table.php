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
        Schema::table('kegiatan_anggotas', function (Blueprint $table) {
            $table->foreignUuid('jenis_kegiatan_id')->nullable()->constrained('jenis_kegiatans')->nullOnDelete();
            $table->text('keterangan_kegiatan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan_anggotas', function (Blueprint $table) {
            $table->dropForeign(['jenis_kegiatan_id']);
            $table->dropColumn(['jenis_kegiatan_id', 'keterangan_kegiatan']);
        });
    }
};
