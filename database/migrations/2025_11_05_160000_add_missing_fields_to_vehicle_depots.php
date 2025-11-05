<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add Missing Fields to Vehicle Depots Table
 * 
 * Enterprise-Grade Migration:
 * - Add email field that was missing
 * - Add description field if missing
 * - Ensure all fields are properly configured
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_depots', function (Blueprint $table) {
            // Add email field if it doesn't exist
            if (!Schema::hasColumn('vehicle_depots', 'email')) {
                $table->string('email', 255)->nullable()->after('phone');
            }
            
            // Add description field if it doesn't exist
            if (!Schema::hasColumn('vehicle_depots', 'description')) {
                $table->text('description')->nullable()->after('longitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_depots', function (Blueprint $table) {
            $table->dropColumn(['email', 'description']);
        });
    }
};
