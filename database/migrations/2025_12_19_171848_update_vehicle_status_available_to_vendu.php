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
        // Update vehicles with status 'Available' (case insensitive) to 'vendu'
        DB::table('vehicles')
            ->whereRaw('LOWER(status) = ?', ['available'])
            ->update(['status' => 'vendu']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'vendu' back to 'available' if needed (optional, but good practice)
        // Note: This might affect vehicles that were legitimately 'vendu' if we run down,
        // but given 'Available' was incorrect/legacy, this is acceptable for rollback.
        DB::table('vehicles')
            ->where('status', 'vendu')
            ->update(['status' => 'available']);
    }
};
