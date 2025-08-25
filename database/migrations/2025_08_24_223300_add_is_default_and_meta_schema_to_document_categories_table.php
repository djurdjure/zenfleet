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
        Schema::table('document_categories', function (Blueprint $table) {
            // As per the plan, make organization_id nullable for default categories
            $table->unsignedBigInteger('organization_id')->nullable()->change();

            $table->boolean('is_default')->default(false)->after('description');
            $table->json('meta_schema')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_categories', function (Blueprint $table) {
            $table->dropColumn('is_default');
            $table->dropColumn('meta_schema');
            // Revert organization_id to its original state.
            // Note: This assumes all categories have an organization_id after the rollback.
            // A more robust rollback might require handling categories where organization_id became null.
            $table->unsignedBigInteger('organization_id')->nullable(false)->change();
        });
    }
};
