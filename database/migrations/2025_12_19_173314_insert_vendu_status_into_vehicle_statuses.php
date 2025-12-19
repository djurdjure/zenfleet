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
        // Insert 'Vendu' status into the reference table
        DB::table('vehicle_statuses')->insertOrIgnore([
            'name' => 'Vendu',
            'slug' => 'vendu', // Critical: must match Enum
            'description' => 'Véhicule vendu et retiré de la flotte',
            'color' => '#9ca3af', // Gray-400
            'icon' => 'lucide:badge-dollar-sign',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'Vendu' status
        DB::table('vehicle_statuses')->where('slug', 'vendu')->delete();
    }
};
