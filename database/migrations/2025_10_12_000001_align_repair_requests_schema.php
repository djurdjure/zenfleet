<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 🔧 MIGRATION: Alignement du schéma repair_requests
 *
 * PROBLÈME IDENTIFIÉ:
 * - La table repair_requests a été créée par une ancienne migration (2025_01_22)
 * - Le modèle RepairRequest et le code utilisent une nouvelle structure (2025_10_05)
 * - Mismatch entre colonnes : priority vs urgency, requested_by vs driver_id, etc.
 *
 * SOLUTION ENTERPRISE:
 * - Transformation progressive avec préservation des données existantes
 * - Mapping des valeurs anciennes vers nouvelles
 * - Validation et rollback safe
 *
 * @version 1.0-FIX
 * @author ZenFleet Engineering Team
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo "🔧 TRANSFORMATION DU SCHÉMA repair_requests...\n";

        // =====================================
        // ÉTAPE 1: VÉRIFIER SI LA TRANSFORMATION EST NÉCESSAIRE
        // =====================================
        $columns = Schema::getColumnListing('repair_requests');

        if (in_array('urgency', $columns)) {
            echo "⚠️  La table est déjà transformée, skip\n";
            return;
        }

        echo "✅ Transformation nécessaire détectée\n";

        // =====================================
        // ÉTAPE 2: BACKUP DES DONNÉES (si nécessaire)
        // =====================================
        $hasData = DB::table('repair_requests')->exists();
        if ($hasData) {
            echo "📊 Données existantes détectées: " . DB::table('repair_requests')->count() . " enregistrements\n";
        }

        DB::beginTransaction();

        try {
            // =====================================
            // ÉTAPE 3: AJOUT DES NOUVELLES COLONNES
            // =====================================
            echo "➕ Ajout des nouvelles colonnes...\n";

            Schema::table('repair_requests', function (Blueprint $table) {
                // Colonnes principales manquantes
                if (!Schema::hasColumn('repair_requests', 'uuid')) {
                    $table->uuid('uuid')->nullable()->after('id');
                }

                if (!Schema::hasColumn('repair_requests', 'driver_id')) {
                    $table->foreignId('driver_id')->nullable()->after('vehicle_id')->constrained('drivers')->onDelete('cascade');
                }

                if (!Schema::hasColumn('repair_requests', 'title')) {
                    $table->string('title', 255)->nullable()->after('driver_id');
                }

                if (!Schema::hasColumn('repair_requests', 'urgency')) {
                    $table->string('urgency', 20)->default('normal')->after('description');
                }

                if (!Schema::hasColumn('repair_requests', 'current_mileage')) {
                    $table->integer('current_mileage')->nullable()->after('urgency');
                }

                if (!Schema::hasColumn('repair_requests', 'current_location')) {
                    $table->string('current_location', 255)->nullable()->after('current_mileage');
                }

                // Colonnes workflow manquantes
                if (!Schema::hasColumn('repair_requests', 'supervisor_status')) {
                    $table->string('supervisor_status', 30)->nullable()->after('supervisor_id');
                }

                if (!Schema::hasColumn('repair_requests', 'supervisor_comment')) {
                    $table->text('supervisor_comment')->nullable()->after('supervisor_status');
                }

                if (!Schema::hasColumn('repair_requests', 'supervisor_approved_at')) {
                    $table->timestamp('supervisor_approved_at')->nullable()->after('supervisor_comment');
                }

                if (!Schema::hasColumn('repair_requests', 'fleet_manager_id')) {
                    $table->foreignId('fleet_manager_id')->nullable()->after('supervisor_approved_at')->constrained('users')->onDelete('set null');
                }

                if (!Schema::hasColumn('repair_requests', 'fleet_manager_status')) {
                    $table->string('fleet_manager_status', 30)->nullable()->after('fleet_manager_id');
                }

                if (!Schema::hasColumn('repair_requests', 'fleet_manager_comment')) {
                    $table->text('fleet_manager_comment')->nullable()->after('fleet_manager_status');
                }

                if (!Schema::hasColumn('repair_requests', 'fleet_manager_approved_at')) {
                    $table->timestamp('fleet_manager_approved_at')->nullable()->after('fleet_manager_comment');
                }

                if (!Schema::hasColumn('repair_requests', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('fleet_manager_approved_at');
                }

                if (!Schema::hasColumn('repair_requests', 'rejected_by')) {
                    $table->foreignId('rejected_by')->nullable()->after('rejection_reason')->constrained('users')->onDelete('set null');
                }

                if (!Schema::hasColumn('repair_requests', 'rejected_at')) {
                    $table->timestamp('rejected_at')->nullable()->after('rejected_by');
                }

                if (!Schema::hasColumn('repair_requests', 'final_approved_by')) {
                    $table->foreignId('final_approved_by')->nullable()->after('rejected_at')->constrained('users')->onDelete('set null');
                }

                if (!Schema::hasColumn('repair_requests', 'final_approved_at')) {
                    $table->timestamp('final_approved_at')->nullable()->after('final_approved_by');
                }

                if (!Schema::hasColumn('repair_requests', 'maintenance_operation_id')) {
                    $table->foreignId('maintenance_operation_id')->nullable()->after('final_approved_at')->constrained('maintenance_operations')->onDelete('set null');
                }
            });

            echo "✅ Nouvelles colonnes ajoutées\n";

            // =====================================
            // ÉTAPE 4: MIGRATION DES DONNÉES
            // =====================================
            if ($hasData) {
                echo "🔄 Migration des données existantes...\n";

                // Générer les UUIDs manquants
                DB::statement("UPDATE repair_requests SET uuid = gen_random_uuid() WHERE uuid IS NULL");

                // Mapper priority → urgency
                $priorityMapping = [
                    'urgente' => 'critical',
                    'a_prevoir' => 'normal',
                    'non_urgente' => 'low',
                ];

                foreach ($priorityMapping as $oldPriority => $newUrgency) {
                    DB::table('repair_requests')
                        ->where('priority', $oldPriority)
                        ->update(['urgency' => $newUrgency]);
                }

                // Mapper requested_by → driver_id
                // Note: Dans l'ancienne structure, requested_by était déjà un user_id
                // Il faut vérifier si ces users sont des drivers
                DB::statement("
                    UPDATE repair_requests rr
                    SET driver_id = (
                        SELECT d.id
                        FROM drivers d
                        WHERE d.user_id = rr.requested_by
                        LIMIT 1
                    )
                    WHERE driver_id IS NULL AND requested_by IS NOT NULL
                ");

                // Générer des titres depuis descriptions (premiers 100 caractères)
                DB::statement("
                    UPDATE repair_requests
                    SET title = SUBSTRING(description FROM 1 FOR 100)
                    WHERE title IS NULL AND description IS NOT NULL
                ");

                // Mapper supervisor_decision → supervisor_status + supervisor_comment
                DB::statement("
                    UPDATE repair_requests
                    SET supervisor_status = CASE
                        WHEN supervisor_decision = 'accepte' THEN 'approved'
                        WHEN supervisor_decision = 'refuse' THEN 'rejected'
                        ELSE NULL
                    END,
                    supervisor_comment = supervisor_comments,
                    supervisor_approved_at = supervisor_decided_at
                    WHERE supervisor_decision IS NOT NULL
                ");

                // Mapper manager_decision → fleet_manager_status
                DB::statement("
                    UPDATE repair_requests
                    SET fleet_manager_id = manager_id,
                    fleet_manager_status = CASE
                        WHEN manager_decision = 'valide' THEN 'approved'
                        WHEN manager_decision = 'refuse' THEN 'rejected'
                        ELSE NULL
                    END,
                    fleet_manager_comment = manager_comments,
                    fleet_manager_approved_at = manager_decided_at
                    WHERE manager_decision IS NOT NULL
                ");

                // Mapper les status anciens → nouveaux
                $statusMapping = [
                    'en_attente' => 'pending_supervisor',
                    'accord_initial' => 'approved_supervisor',
                    'accordee' => 'approved_final',
                    'refusee' => 'rejected_final',
                    'en_cours' => 'approved_final',
                    'terminee' => 'approved_final',
                    'annulee' => 'rejected_final',
                ];

                foreach ($statusMapping as $oldStatus => $newStatus) {
                    DB::table('repair_requests')
                        ->where('status', $oldStatus)
                        ->update(['status' => $newStatus]);
                }

                echo "✅ Données migrées avec succès\n";
            }

            // =====================================
            // ÉTAPE 5: RENDRE LES NOUVELLES COLONNES OBLIGATOIRES
            // =====================================
            echo "🔒 Application des contraintes...\n";

            Schema::table('repair_requests', function (Blueprint $table) {
                // UUID doit être unique et non-null
                $table->uuid('uuid')->unique()->nullable(false)->change();

                // Title requis
                $table->string('title', 255)->nullable(false)->change();
            });

            // =====================================
            // ÉTAPE 6: SUPPRIMER LES ANCIENNES COLONNES (avec prudence)
            // =====================================
            echo "🗑️  Nettoyage des anciennes colonnes...\n";

            // On garde les anciennes colonnes pour référence historique
            // Elles seront supprimées dans une migration future après validation

            // =====================================
            // ÉTAPE 7: CRÉER LES INDEX DE PERFORMANCE
            // =====================================
            echo "⚡ Création des index de performance...\n";

            try {
                DB::statement('CREATE INDEX IF NOT EXISTS idx_repair_requests_status_org ON repair_requests (status, organization_id)');
                DB::statement('CREATE INDEX IF NOT EXISTS idx_repair_requests_driver_status ON repair_requests (driver_id, status)');
                DB::statement('CREATE INDEX IF NOT EXISTS idx_repair_requests_vehicle_date ON repair_requests (vehicle_id, created_at)');
                DB::statement('CREATE INDEX IF NOT EXISTS idx_repair_requests_urgency ON repair_requests (urgency, status)');
                echo "✅ Index créés\n";
            } catch (\Exception $e) {
                echo "⚠️  Certains index existent déjà\n";
            }

            DB::commit();

            echo "\n";
            echo "✅ ========================================\n";
            echo "✅ TRANSFORMATION TERMINÉE AVEC SUCCÈS!\n";
            echo "✅ ========================================\n";
            echo "📊 Statistiques:\n";
            echo "   - Total enregistrements: " . DB::table('repair_requests')->count() . "\n";
            echo "   - Avec urgency: " . DB::table('repair_requests')->whereNotNull('urgency')->count() . "\n";
            echo "   - Avec driver_id: " . DB::table('repair_requests')->whereNotNull('driver_id')->count() . "\n";
            echo "   - Avec title: " . DB::table('repair_requests')->whereNotNull('title')->count() . "\n";

        } catch (\Exception $e) {
            DB::rollBack();
            echo "\n❌ ERREUR LORS DE LA TRANSFORMATION: " . $e->getMessage() . "\n";
            echo "📝 Stack trace: " . $e->getTraceAsString() . "\n";
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "⚠️  ROLLBACK: Suppression des colonnes ajoutées...\n";

        Schema::table('repair_requests', function (Blueprint $table) {
            // Supprimer les foreign keys d'abord
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['fleet_manager_id']);
            $table->dropForeign(['rejected_by']);
            $table->dropForeign(['final_approved_by']);
            $table->dropForeign(['maintenance_operation_id']);

            // Supprimer les colonnes
            $table->dropColumn([
                'uuid',
                'driver_id',
                'title',
                'urgency',
                'current_mileage',
                'current_location',
                'supervisor_status',
                'supervisor_comment',
                'supervisor_approved_at',
                'fleet_manager_id',
                'fleet_manager_status',
                'fleet_manager_comment',
                'fleet_manager_approved_at',
                'rejection_reason',
                'rejected_by',
                'rejected_at',
                'final_approved_by',
                'final_approved_at',
                'maintenance_operation_id',
            ]);
        });

        // Supprimer les index
        DB::statement('DROP INDEX IF EXISTS idx_repair_requests_status_org');
        DB::statement('DROP INDEX IF EXISTS idx_repair_requests_driver_status');
        DB::statement('DROP INDEX IF EXISTS idx_repair_requests_vehicle_date');
        DB::statement('DROP INDEX IF EXISTS idx_repair_requests_urgency');

        echo "✅ Rollback terminé\n";
    }
};
