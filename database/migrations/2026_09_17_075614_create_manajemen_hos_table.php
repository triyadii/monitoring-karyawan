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
        Schema::create('manajemen_hos', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('namaClient');
            $table->string('nomorTelepon');
            $table->text('alamat');
            $table->foreignUuid('status_id')->constrained('master_status_clients')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manajemen_hos');
    }
};
