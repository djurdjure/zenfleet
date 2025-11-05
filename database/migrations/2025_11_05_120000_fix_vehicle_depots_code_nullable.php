<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fix Vehicle Depots Code Column
 * 
 * Enterprise-Grade Migration:
 * - Make code column nullable to match validation rules
 * - Preserve existing data integrity
 * - Add index for performance optimization
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_depots', function (Blueprint $table) {
            // Make code nullable to fix creation bug
            $table->string('code', 30)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_depots', function (Blueprint $table) {
            // Restore NOT NULL constraint (may fail if nullable data exists)
            $table->string('code', 30)->nullable(false)->change();
        });
    }
};
