<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 🚀 ENTERPRISE MIGRATION - Ajout champs pour détection de conflits et audit
     */
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Champs multi-tenant enterprise
            $table->foreignId('organization_id')->after('id')->constrained('organizations')->onDelete('cascade');

            // Statut pour workflow enterprise
            $table->string('status', 20)->after('notes')->default('active')->index();

            // Audit trail complet
            $table->foreignId('created_by')->after('status')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('ended_by_user_id')->after('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('ended_at')->after('ended_by_user_id')->nullable();

            // 🔥 INDEXES CRITIQUES POUR PERFORMANCE ENTERPRISE
            // Index pour détection de conflits véhicule (requêtes < 50ms)
            $table->index(['vehicle_id', 'start_datetime', 'end_datetime'], 'idx_vehicle_period');

            // Index pour détection de conflits chauffeur (requêtes < 50ms)
            $table->index(['driver_id', 'start_datetime', 'end_datetime'], 'idx_driver_period');

            // Index composite pour requêtes multi-tenant
            $table->index(['organization_id', 'status', 'start_datetime'], 'idx_org_status_start');

            // Index pour recherche par période (calendrier, gantt)
            $table->index(['start_datetime', 'end_datetime'], 'idx_period_range');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Suppression des indexes
            $table->dropIndex('idx_vehicle_period');
            $table->dropIndex('idx_driver_period');
            $table->dropIndex('idx_org_status_start');
            $table->dropIndex('idx_period_range');

            // Suppression des colonnes
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['ended_by_user_id']);

            $table->dropColumn([
                'organization_id',
                'status',
                'created_by',
                'updated_by',
                'ended_by_user_id',
                'ended_at'
            ]);
        });
    }
};
