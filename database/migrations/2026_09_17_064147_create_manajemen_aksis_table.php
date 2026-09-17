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
        Schema::create('manajemen_aksis', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('idAksi')->nullable();
            $table->string('namaAksi');
            $table->text('kegiatan')->nullable();
            $table->json('foto')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manajemen_aksis');
    }
};
