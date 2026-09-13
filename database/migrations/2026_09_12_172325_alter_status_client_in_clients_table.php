<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new temporary column
        Schema::table('clients', function (Blueprint $table) {
            $table->uuid('status_client_uuid')->nullable()->after('status_client');
        });

        // Map existing integer statuses to UUIDs
        $defaultStatus = DB::table('master_status_clients')->where('nama_status', 'Berminat')->first();
        if ($defaultStatus) {
            DB::table('clients')->update(['status_client_uuid' => $defaultStatus->id]);
        }

        Schema::table('clients', function (Blueprint $table) {
            // Drop old column
            $table->dropColumn('status_client');
        });

        Schema::table('clients', function (Blueprint $table) {
            // Rename temporary to original
            $table->renameColumn('status_client_uuid', 'status_client');
        });

        Schema::table('clients', function (Blueprint $table) {
            // Add foreign key
            $table->foreign('status_client')->references('id')->on('master_status_clients')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['status_client']);
            $table->dropColumn('status_client');
            $table->integer('status_client')->default(1);
        });
    }
};
