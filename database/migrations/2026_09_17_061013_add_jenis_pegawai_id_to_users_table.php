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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('jenis_pegawai_id')->nullable()->after('role_id');
            $table->foreign('jenis_pegawai_id')->references('uuid')->on('jenis_pegawais')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['jenis_pegawai_id']);
            $table->dropColumn('jenis_pegawai_id');
        });
    }
};
