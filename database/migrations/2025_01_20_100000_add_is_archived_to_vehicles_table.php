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
        if (!Schema::hasTable('vehicles') || Schema::hasColumn('vehicles', 'is_archived')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            $table->boolean('is_archived')
                ->default(false)
                ->after('notes')
                ->comment('Indique si le véhicule est archivé');

            // Index pour améliorer les performances des requêtes filtrées
            $table->index('is_archived', 'idx_vehicles_archived');

            // Index composé pour les requêtes multi-tenant
            $table->index(['organization_id', 'is_archived'], 'idx_vehicles_org_archived');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('vehicles') || !Schema::hasColumn('vehicles', 'is_archived')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex('idx_vehicles_org_archived');
            $table->dropIndex('idx_vehicles_archived');
            $table->dropColumn('is_archived');
        });
    }
};
