<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add comment to signed_form_path column to mark it as deprecated
        // Note: The column is kept for backward compatibility
        DB::statement("COMMENT ON COLUMN vehicle_handover_forms.signed_form_path IS 'Deprecated: Use Spatie Media Library instead. Kept for backward compatibility.'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the comment
        DB::statement("COMMENT ON COLUMN vehicle_handover_forms.signed_form_path IS NULL");
    }
};
