<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('vehicle_expenses', function (Blueprint $table) {
            // Groupement analytique
            $table->unsignedBigInteger('expense_group_id')
                ->nullable()
                ->after('repair_request_id')
                ->comment('Lien vers le groupe de dépenses pour analyse par lot');
            
            // Donneur d'ordre
            $table->unsignedBigInteger('requester_id')
                ->nullable()
                ->after('recorded_by')
                ->comment('Utilisateur qui a initié la demande de dépense');
            
            // Niveau de priorité
            $table->string('priority_level', 20)
                ->default('normal')
                ->after('needs_approval')
                ->comment('Priorité: low, normal, high, urgent');
            
            // Centre de coût
            $table->string('cost_center', 100)
                ->nullable()
                ->after('expense_wilaya')
                ->comment('Centre de coût pour comptabilité analytique');
            
            // Workflow d'approbation à 2 niveaux
            $table->boolean('level1_approved')->default(false)->after('approved');
            $table->unsignedBigInteger('level1_approved_by')->nullable()->after('level1_approved');
            $table->timestamp('level1_approved_at')->nullable()->after('level1_approved_by');
            $table->text('level1_comments')->nullable()->after('level1_approved_at');
            
            $table->boolean('level2_approved')->default(false)->after('level1_comments');
            $table->unsignedBigInteger('level2_approved_by')->nullable()->after('level2_approved');
            $table->timestamp('level2_approved_at')->nullable()->after('level2_approved_by');
            $table->text('level2_comments')->nullable()->after('level2_approved_at');
            
            // Statut global du workflow
            $table->string('approval_status', 50)
                ->default('draft')
                ->after('level2_comments')
                ->comment('draft, pending_level1, pending_level2, approved, rejected');
            
            // Rejet
            $table->boolean('is_rejected')->default(false)->after('approval_status');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('is_rejected');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
            
            // Urgence et deadline
            $table->boolean('is_urgent')->default(false)->after('priority_level');
            $table->date('approval_deadline')->nullable()->after('is_urgent');
            
            // Référence externe (pour intégration)
            $table->string('external_reference', 255)
                ->nullable()
                ->after('payment_reference')
                ->comment('Référence système externe (ERP, comptabilité, etc.)');
            
            // Index pour performance
            $table->index('expense_group_id');
            $table->index('requester_id');
            $table->index('priority_level');
            $table->index('cost_center');
            $table->index('approval_status');
            $table->index(['organization_id', 'expense_group_id']);
            $table->index(['organization_id', 'approval_status']);
            $table->index(['is_urgent', 'approval_deadline']);
            
            // Foreign keys
            $table->foreign('expense_group_id')
                ->references('id')->on('expense_groups')
                ->onDelete('set null');
            $table->foreign('requester_id')
                ->references('id')->on('users')
                ->onDelete('set null');
            $table->foreign('level1_approved_by')
                ->references('id')->on('users')
                ->onDelete('set null');
            $table->foreign('level2_approved_by')
                ->references('id')->on('users')
                ->onDelete('set null');
            $table->foreign('rejected_by')
                ->references('id')->on('users')
                ->onDelete('set null');
        });

        if ($driver !== 'pgsql') {
            return;
        }
        
        // Créer les triggers séparés pour mise à jour du budget_used dans expense_groups
        DB::statement("
            CREATE TRIGGER update_group_budget_on_expense_insert
            AFTER INSERT ON vehicle_expenses
            FOR EACH ROW
            WHEN (NEW.expense_group_id IS NOT NULL)
            EXECUTE FUNCTION update_expense_group_budget();
        ");
        
        DB::statement("
            CREATE TRIGGER update_group_budget_on_expense_update
            AFTER UPDATE ON vehicle_expenses
            FOR EACH ROW
            WHEN (OLD.expense_group_id IS DISTINCT FROM NEW.expense_group_id 
                  OR OLD.total_ttc IS DISTINCT FROM NEW.total_ttc
                  OR OLD.deleted_at IS DISTINCT FROM NEW.deleted_at)
            EXECUTE FUNCTION update_expense_group_budget();
        ");
        
        DB::statement("
            CREATE TRIGGER update_group_budget_on_expense_delete
            AFTER DELETE ON vehicle_expenses
            FOR EACH ROW
            WHEN (OLD.expense_group_id IS NOT NULL)
            EXECUTE FUNCTION update_expense_group_budget();
        ");
        
        // Contraintes métier
        DB::statement("
            ALTER TABLE vehicle_expenses
            ADD CONSTRAINT valid_priority_level CHECK (
                priority_level IN ('low', 'normal', 'high', 'urgent')
            )
        ");
        
        DB::statement("
            ALTER TABLE vehicle_expenses
            ADD CONSTRAINT valid_approval_status_v2 CHECK (
                approval_status IN ('draft', 'pending_level1', 'pending_level2', 'approved', 'rejected')
            )
        ");
        
        DB::statement("
            ALTER TABLE vehicle_expenses
            ADD CONSTRAINT valid_two_level_approval CHECK (
                (NOT level2_approved OR level1_approved) AND
                (NOT is_rejected OR (NOT level1_approved AND NOT level2_approved))
            )
        ");
        
        // Fonction pour calculer automatiquement approval_status
        DB::statement("
            CREATE OR REPLACE FUNCTION update_approval_status()
            RETURNS TRIGGER AS $$
            BEGIN
                IF NEW.is_rejected THEN
                    NEW.approval_status = 'rejected';
                ELSIF NEW.level2_approved THEN
                    NEW.approval_status = 'approved';
                    NEW.approved = true;
                ELSIF NEW.level1_approved THEN
                    NEW.approval_status = 'pending_level2';
                ELSIF NEW.needs_approval THEN
                    NEW.approval_status = 'pending_level1';
                ELSE
                    NEW.approval_status = 'draft';
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");
        
        DB::statement("
            CREATE TRIGGER auto_update_approval_status
            BEFORE INSERT OR UPDATE ON vehicle_expenses
            FOR EACH ROW
            EXECUTE FUNCTION update_approval_status();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            // Supprimer les triggers
            DB::statement("DROP TRIGGER IF EXISTS update_group_budget_on_expense_insert ON vehicle_expenses");
            DB::statement("DROP TRIGGER IF EXISTS update_group_budget_on_expense_update ON vehicle_expenses");
            DB::statement("DROP TRIGGER IF EXISTS update_group_budget_on_expense_delete ON vehicle_expenses");
            DB::statement("DROP TRIGGER IF EXISTS auto_update_approval_status ON vehicle_expenses");
            DB::statement("DROP FUNCTION IF EXISTS update_approval_status()");
        }
        
        Schema::table('vehicle_expenses', function (Blueprint $table) {
            // Supprimer les foreign keys
            $table->dropForeign(['expense_group_id']);
            $table->dropForeign(['requester_id']);
            $table->dropForeign(['level1_approved_by']);
            $table->dropForeign(['level2_approved_by']);
            $table->dropForeign(['rejected_by']);
            
            // Supprimer les colonnes
            $table->dropColumn([
                'expense_group_id',
                'requester_id',
                'priority_level',
                'cost_center',
                'level1_approved',
                'level1_approved_by',
                'level1_approved_at',
                'level1_comments',
                'level2_approved',
                'level2_approved_by',
                'level2_approved_at',
                'level2_comments',
                'approval_status',
                'is_rejected',
                'rejected_by',
                'rejected_at',
                'rejection_reason',
                'is_urgent',
                'approval_deadline',
                'external_reference'
            ]);
        });
    }
};
