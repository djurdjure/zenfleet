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
        // Delete status where slug is empty or name matches 'Available' (case insensitive)
        DB::table('vehicle_statuses')
            ->where('slug', '')
            ->orWhereRaw('LOWER(name) = ?', ['available'])
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a destructive operation, no simple revert.
        // We could re-insert 'Available' but it would have a new ID.
    }
};
