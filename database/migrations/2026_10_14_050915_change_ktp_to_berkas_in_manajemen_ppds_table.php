<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Replaces single 'ktp' column with 'berkas' JSON column (max 3 files).
     */
    public function up(): void
    {
        Schema::table('manajemen_ppds', function (Blueprint $table) {
            $table->json('berkas')->nullable()->after('ktp');
        });

        // Migrate existing ktp data to berkas array, then drop ktp
        DB::table('manajemen_ppds')->whereNotNull('ktp')->get()->each(function ($row) {
            DB::table('manajemen_ppds')->where('uuid', $row->uuid)->update([
                'berkas' => json_encode([$row->ktp]),
            ]);
        });

        Schema::table('manajemen_ppds', function (Blueprint $table) {
            $table->dropColumn('ktp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manajemen_ppds', function (Blueprint $table) {
            $table->string('ktp')->nullable()->after('berkas');
        });

        // Migrate back: take first item from berkas array
        DB::table('manajemen_ppds')->whereNotNull('berkas')->get()->each(function ($row) {
            $berkas = json_decode($row->berkas, true);
            DB::table('manajemen_ppds')->where('uuid', $row->uuid)->update([
                'ktp' => $berkas[0] ?? null,
            ]);
        });

        Schema::table('manajemen_ppds', function (Blueprint $table) {
            $table->dropColumn('berkas');
        });
    }
};
