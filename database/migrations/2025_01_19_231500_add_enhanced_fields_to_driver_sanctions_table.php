<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * ====================================================================
 * 🔧 MIGRATION ENHANCED FIELDS - POSTGRESQL COMPATIBLE
 * ====================================================================
 * 
 * Migration ultra-professionnelle compatible PostgreSQL et MySQL
 * 
 * Ajoute les champs pour la refactorisation ultra-professionnelle:
 * - severity: niveau de gravité (low, medium, high, critical)
 * - status: statut de la sanction (active, appealed, cancelled, archived)
 * - duration_days: durée de la sanction en jours
 * - notes: notes additionnelles
 * - Extension types sanctions (8 types au lieu de 4)
 *
 * @version 2.0-PostgreSQL-Compatible
 * @since 2025-01-19
 * ====================================================================
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (!Schema::hasTable('driver_sanctions')) {
            return;
        }
        
        // ===============================================
        // ÉTENDRE LES TYPES DE SANCTIONS EXISTANTS
        // ===============================================
        
        if ($driver === 'pgsql') {
            // PostgreSQL: Méthode compatible
            // Vérifier si la colonne utilise un type ENUM custom ou CHECK constraint
            $columnType = DB::select("
                SELECT data_type, udt_name 
                FROM information_schema.columns 
                WHERE table_name = 'driver_sanctions' 
                AND column_name = 'sanction_type'
            ")[0] ?? null;
            
            if ($columnType && $columnType->data_type === 'USER-DEFINED') {
                // Type ENUM personnalisé PostgreSQL - Ajouter les nouvelles valeurs
                DB::statement("ALTER TYPE {$columnType->udt_name} ADD VALUE IF NOT EXISTS 'suspension_permis'");
                DB::statement("ALTER TYPE {$columnType->udt_name} ADD VALUE IF NOT EXISTS 'amende'");
                DB::statement("ALTER TYPE {$columnType->udt_name} ADD VALUE IF NOT EXISTS 'blame'");
                DB::statement("ALTER TYPE {$columnType->udt_name} ADD VALUE IF NOT EXISTS 'licenciement'");
            } else {
                // Utilise probablement une CHECK constraint - Modifier la contrainte
                // Drop et recréer la contrainte
                DB::statement("
                    ALTER TABLE driver_sanctions 
                    DROP CONSTRAINT IF EXISTS driver_sanctions_sanction_type_check
                ");
                
                DB::statement("
                    ALTER TABLE driver_sanctions 
                    ADD CONSTRAINT driver_sanctions_sanction_type_check 
                    CHECK (sanction_type IN (
                        'avertissement_verbal',
                        'avertissement_ecrit',
                        'mise_a_pied',
                        'mise_en_demeure',
                        'suspension_permis',
                        'amende',
                        'blame',
                        'licenciement'
                    ))
                ");
            }
        } elseif ($driver === 'mysql') {
            // MySQL: Méthode MODIFY COLUMN
            DB::statement("
                ALTER TABLE driver_sanctions 
                MODIFY COLUMN sanction_type ENUM(
                    'avertissement_verbal',
                    'avertissement_ecrit',
                    'mise_a_pied',
                    'mise_en_demeure',
                    'suspension_permis',
                    'amende',
                    'blame',
                    'licenciement'
                ) NOT NULL
            ");
        } else {
            // SQLite et autres moteurs: pas de support ENUM/MODIFY, on skip l'extension.
        }
        
        // ===============================================
        // AJOUTER LES NOUVEAUX CHAMPS
        // ===============================================
        
        Schema::table('driver_sanctions', function (Blueprint $table) {
            // Niveau de gravité
            $table->string('severity', 20)
                ->default('medium')
                ->after('sanction_type')
                ->comment('Niveau de gravité: low, medium, high, critical');
            
            // Durée de la sanction
            $table->integer('duration_days')
                ->nullable()
                ->after('sanction_date')
                ->comment('Durée de la sanction en jours (pour mise à pied, suspension, etc.)');
            
            // Statut de la sanction
            $table->string('status', 20)
                ->default('active')
                ->after('attachment_path')
                ->comment('Statut: active, appealed, cancelled, archived');
            
            // Notes additionnelles
            $table->text('notes')
                ->nullable()
                ->after('status')
                ->comment('Notes additionnelles sur la sanction');
            
            // Index pour améliorer les performances de recherche
            $table->index('severity', 'idx_sanctions_severity');
            $table->index('status', 'idx_sanctions_status');
            $table->index(['organization_id', 'status'], 'idx_sanctions_org_status');
        });
        
        // Ajouter des CHECK constraints pour PostgreSQL et MySQL 8+
        if ($driver === 'pgsql') {
            DB::statement("
                ALTER TABLE driver_sanctions 
                ADD CONSTRAINT driver_sanctions_severity_check 
                CHECK (severity IN ('low', 'medium', 'high', 'critical'))
            ");
            
            DB::statement("
                ALTER TABLE driver_sanctions 
                ADD CONSTRAINT driver_sanctions_status_check 
                CHECK (status IN ('active', 'appealed', 'cancelled', 'archived'))
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if (!Schema::hasTable('driver_sanctions')) {
            return;
        }
        
        // Supprimer les CHECK constraints si PostgreSQL
        if ($driver === 'pgsql') {
            DB::statement("
                ALTER TABLE driver_sanctions 
                DROP CONSTRAINT IF EXISTS driver_sanctions_severity_check
            ");
            
            DB::statement("
                ALTER TABLE driver_sanctions 
                DROP CONSTRAINT IF EXISTS driver_sanctions_status_check
            ");
        }
        
        Schema::table('driver_sanctions', function (Blueprint $table) {
            $table->dropIndex('idx_sanctions_severity');
            $table->dropIndex('idx_sanctions_status');
            $table->dropIndex('idx_sanctions_org_status');
            
            $table->dropColumn(['severity', 'status', 'duration_days', 'notes']);
        });
        
        // Note: La restauration du type ENUM sanction_type à ses valeurs originales
        // n'est pas implémentée car cela pourrait causer des pertes de données.
        // Si nécessaire, effectuer manuellement.
    }
};
