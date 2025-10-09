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
        Schema::table('repair_requests', function (Blueprint $table) {
            // Ajouter la colonne category_id après description
            $table->foreignId('category_id')
                ->nullable()
                ->after('description')
                ->constrained('repair_categories')
                ->nullOnDelete();

            // Ajouter un index pour les performances
            $table->index(['category_id', 'organization_id'], 'idx_repair_requests_category_org');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropIndex('idx_repair_requests_category_org');
            $table->dropColumn('category_id');
        });
    }
};
