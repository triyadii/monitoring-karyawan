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
        Schema::create('manajemen_ppds', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('namaClient');
            $table->string('nomorTelepon');
            $table->text('alamat');
            $table->string('nomorKontrak')->nullable();
            $table->string('tenor')->nullable();
            $table->string('angsuran')->nullable();
            $table->string('merk')->nullable();
            $table->string('type')->nullable();
            $table->string('jenisKendaraan')->nullable();
            $table->string('pinjaman')->nullable();
            $table->date('jatuhTempo')->nullable();
            $table->string('ktp')->nullable();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manajemen_ppds');
    }
};
