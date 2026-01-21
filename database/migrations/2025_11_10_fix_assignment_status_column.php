<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Correction de la structure de la table assignments
     * Assure que la colonne status existe et supprime cancelled_at si présente
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('assignments', function (Blueprint $table) {
            // Ajouter la colonne status si elle n'existe pas
            if (!Schema::hasColumn('assignments', 'status')) {
                $table->string('status', 20)->default('scheduled')->after('notes');
                
                // Ajouter un index pour les performances
                $table->index(['organization_id', 'status']);
                
                echo "✅ Colonne 'status' ajoutée à la table assignments\n";
            } else {
                echo "ℹ️ Colonne 'status' existe déjà\n";
            }
            
            // Supprimer cancelled_at si elle existe (non utilisée)
            if (Schema::hasColumn('assignments', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
                echo "✅ Colonne 'cancelled_at' supprimée (non utilisée)\n";
            }
        });
        
        // Mettre à jour les statuts existants basés sur les dates
        $nowExpression = $driver === 'sqlite' ? 'CURRENT_TIMESTAMP' : 'NOW()';
        DB::statement("
            UPDATE assignments 
            SET status = CASE
                WHEN end_datetime IS NOT NULL AND end_datetime <= {$nowExpression} THEN 'completed'
                WHEN start_datetime <= {$nowExpression} AND (end_datetime IS NULL OR end_datetime > {$nowExpression}) THEN 'active'
                WHEN start_datetime > {$nowExpression} THEN 'scheduled'
                ELSE status
            END
            WHERE status IS NULL OR status = ''
        ");
        
        echo "✅ Statuts mis à jour pour les affectations existantes\n";
        
        // Créer un index optimisé si nécessaire
        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_assignments_status_dates 
            ON assignments (organization_id, status, start_datetime, end_datetime) 
            WHERE deleted_at IS NULL
        ");
        
        echo "✅ Index optimisés créés\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Ne pas supprimer la colonne status car elle est nécessaire
            // Mais on peut recréer cancelled_at si vraiment nécessaire pour rollback
            
            if (!Schema::hasColumn('assignments', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('notes');
            }
            
            // Supprimer les index créés
            if (Schema::hasIndex('assignments', 'idx_assignments_status_dates')) {
                DB::statement("DROP INDEX IF EXISTS idx_assignments_status_dates");
            }
        });
    }
};
