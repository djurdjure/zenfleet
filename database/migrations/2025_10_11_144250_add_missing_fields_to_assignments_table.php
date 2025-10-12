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
        // Skip si la table assignments n'existe pas encore
        if (!Schema::hasTable('assignments')) {
            echo "⚠️  Table assignments n'existe pas encore, skip adding fields\n";
            return;
        }

        Schema::table('assignments', function (Blueprint $table) {
            // Ajouter les colonnes uniquement si elles n'existent pas déjà
            if (!Schema::hasColumn('assignments', 'organization_id')) {
                $table->foreignId('organization_id')->after('id')->constrained('organizations')->onDelete('cascade');
                echo "✅ organization_id ajoutée\n";
            }

            if (!Schema::hasColumn('assignments', 'status')) {
                $table->string('status', 20)->default('active')->index();
                echo "✅ status ajoutée\n";
            }

            if (!Schema::hasColumn('assignments', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                echo "✅ created_by ajoutée\n";
            }

            if (!Schema::hasColumn('assignments', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                echo "✅ updated_by ajoutée\n";
            }

            if (!Schema::hasColumn('assignments', 'ended_by_user_id')) {
                $table->foreignId('ended_by_user_id')->nullable()->constrained('users')->onDelete('set null');
                echo "✅ ended_by_user_id ajoutée\n";
            }

            if (!Schema::hasColumn('assignments', 'ended_at')) {
                $table->timestamp('ended_at')->nullable();
                echo "✅ ended_at ajoutée\n";
            }
        });

        // Ajouter les index de manière sûre
        try {
            DB::statement('CREATE INDEX IF NOT EXISTS idx_vehicle_period ON assignments (vehicle_id, start_datetime, end_datetime)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_driver_period ON assignments (driver_id, start_datetime, end_datetime)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_org_status_start ON assignments (organization_id, status, start_datetime)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_period_range ON assignments (start_datetime, end_datetime)');
            echo "✅ Index de performance créés\n";
        } catch (\Exception $e) {
            echo "⚠️  Certains index existent déjà\n";
        }
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
