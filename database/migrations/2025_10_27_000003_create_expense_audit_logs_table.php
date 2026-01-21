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
        Schema::create('expense_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('vehicle_expense_id');
            $table->unsignedBigInteger('user_id');
            
            // Type d'action
            $table->string('action', 50); // created, updated, approved, rejected, deleted, etc.
            $table->string('action_category', 50); // workflow, financial, administrative
            
            // Détails de l'action
            $table->text('description');
            $table->json('old_values')->nullable(); // Valeurs avant modification
            $table->json('new_values')->nullable(); // Nouvelles valeurs
            $table->json('changed_fields')->default('[]'); // Liste des champs modifiés
            
            // Contexte
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('session_id', 255)->nullable();
            $table->string('request_id', 255)->nullable(); // Pour tracer les requêtes
            
            // Workflow tracking
            $table->string('previous_status', 50)->nullable();
            $table->string('new_status', 50)->nullable();
            $table->decimal('previous_amount', 15, 2)->nullable();
            $table->decimal('new_amount', 15, 2)->nullable();
            
            // Compliance et sécurité
            $table->boolean('is_sensitive')->default(false); // Action sensible
            $table->boolean('requires_review')->default(false); // Nécessite revue
            $table->boolean('reviewed')->default(false);
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            
            // Alertes et anomalies
            $table->boolean('is_anomaly')->default(false);
            $table->text('anomaly_details')->nullable();
            $table->string('risk_level', 20)->nullable(); // low, medium, high, critical
            
            // Métadonnées
            $table->json('metadata')->default('{}');
            $table->json('tags')->default('[]');
            
            // Timestamps
            $table->timestamp('created_at')->useCurrent();
            // Pas de updated_at car les logs sont immuables
            
            // Index pour performance
            $table->index(['organization_id', 'vehicle_expense_id']);
            $table->index(['organization_id', 'user_id']);
            $table->index(['organization_id', 'action']);
            $table->index(['organization_id', 'created_at']);
            $table->index(['vehicle_expense_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index('ip_address');
            $table->index('session_id');
            $table->index(['requires_review', 'reviewed']);
            $table->index(['is_anomaly', 'risk_level']);
            
            // Foreign keys
            $table->foreign('organization_id')
                ->references('id')->on('organizations')
                ->onDelete('cascade');
            $table->foreign('vehicle_expense_id')
                ->references('id')->on('vehicle_expenses')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('reviewed_by')
                ->references('id')->on('users')
                ->onDelete('set null');
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'pgsql') {
            return;
        }
        
        // Vue pour résumé des audits par utilisateur
        DB::statement("
            CREATE VIEW expense_audit_summary AS
            SELECT 
                eal.organization_id,
                eal.user_id,
                u.name as user_name,
                eal.action,
                COUNT(*) as action_count,
                DATE(eal.created_at) as action_date
            FROM expense_audit_logs eal
            JOIN users u ON u.id = eal.user_id
            GROUP BY 
                eal.organization_id, 
                eal.user_id, 
                u.name,
                eal.action, 
                DATE(eal.created_at)
        ");
        
        // Fonction pour créer automatiquement des logs d'audit
        DB::statement("
            CREATE OR REPLACE FUNCTION log_expense_changes()
            RETURNS TRIGGER AS $$
            DECLARE
                v_action TEXT;
                v_old_values JSONB;
                v_new_values JSONB;
                v_changed_fields JSONB;
                v_user_id BIGINT;
            BEGIN
                -- Déterminer l'action
                IF TG_OP = 'INSERT' THEN
                    v_action := 'created';
                    v_old_values := NULL;
                    v_new_values := to_jsonb(NEW);
                ELSIF TG_OP = 'UPDATE' THEN
                    v_action := 'updated';
                    v_old_values := to_jsonb(OLD);
                    v_new_values := to_jsonb(NEW);
                    
                    -- Détection d'actions spécifiques
                    IF OLD.approval_status != NEW.approval_status THEN
                        IF NEW.approval_status = 'approved' THEN
                            v_action := 'approved';
                        ELSIF NEW.approval_status = 'rejected' THEN
                            v_action := 'rejected';
                        END IF;
                    END IF;
                    
                    IF OLD.payment_status != NEW.payment_status AND NEW.payment_status = 'paid' THEN
                        v_action := 'paid';
                    END IF;
                    
                ELSIF TG_OP = 'DELETE' THEN
                    v_action := 'deleted';
                    v_old_values := to_jsonb(OLD);
                    v_new_values := NULL;
                END IF;
                
                -- Obtenir l'ID utilisateur (à améliorer avec contexte session)
                v_user_id := COALESCE(NEW.updated_by, NEW.recorded_by, OLD.updated_by, OLD.recorded_by);
                
                -- Calculer les champs modifiés
                IF TG_OP = 'UPDATE' THEN
                    SELECT jsonb_agg(key)
                    INTO v_changed_fields
                    FROM jsonb_each(v_old_values)
                    WHERE v_old_values->key IS DISTINCT FROM v_new_values->key;
                ELSE
                    v_changed_fields := '[]'::jsonb;
                END IF;
                
                -- Insérer le log
                INSERT INTO expense_audit_logs (
                    organization_id,
                    vehicle_expense_id,
                    user_id,
                    action,
                    action_category,
                    description,
                    old_values,
                    new_values,
                    changed_fields,
                    previous_status,
                    new_status,
                    previous_amount,
                    new_amount,
                    created_at
                ) VALUES (
                    COALESCE(NEW.organization_id, OLD.organization_id),
                    COALESCE(NEW.id, OLD.id),
                    v_user_id,
                    v_action,
                    CASE 
                        WHEN v_action IN ('approved', 'rejected') THEN 'workflow'
                        WHEN v_action IN ('paid') THEN 'financial'
                        ELSE 'administrative'
                    END,
                    'Expense ' || v_action || ' for vehicle #' || COALESCE(NEW.vehicle_id, OLD.vehicle_id),
                    v_old_values,
                    v_new_values,
                    v_changed_fields,
                    CASE WHEN TG_OP = 'UPDATE' THEN OLD.approval_status ELSE NULL END,
                    CASE WHEN TG_OP != 'DELETE' THEN NEW.approval_status ELSE NULL END,
                    CASE WHEN TG_OP = 'UPDATE' THEN OLD.total_ttc ELSE NULL END,
                    CASE WHEN TG_OP != 'DELETE' THEN NEW.total_ttc ELSE NULL END,
                    NOW()
                );
                
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");
        
        // Trigger pour audit automatique
        DB::statement("
            CREATE TRIGGER audit_expense_changes
            AFTER INSERT OR UPDATE OR DELETE ON vehicle_expenses
            FOR EACH ROW
            EXECUTE FUNCTION log_expense_changes();
        ");
        
        // Fonction pour détecter les anomalies
        DB::statement("
            CREATE OR REPLACE FUNCTION detect_expense_anomalies()
            RETURNS TRIGGER AS $$
            BEGIN
                -- Détection d'anomalie: montant très élevé
                IF NEW.total_ttc > 1000000 THEN
                    UPDATE expense_audit_logs
                    SET is_anomaly = true,
                        anomaly_details = 'Montant très élevé: ' || NEW.total_ttc,
                        risk_level = 'high',
                        requires_review = true
                    WHERE vehicle_expense_id = NEW.id
                    AND created_at = (
                        SELECT MAX(created_at) 
                        FROM expense_audit_logs 
                        WHERE vehicle_expense_id = NEW.id
                    );
                END IF;
                
                -- Détection: approbation trop rapide
                IF NEW.approval_status = 'approved' 
                   AND NEW.created_at > NOW() - INTERVAL '5 minutes' THEN
                    UPDATE expense_audit_logs
                    SET is_anomaly = true,
                        anomaly_details = 'Approbation très rapide (moins de 5 minutes)',
                        risk_level = 'medium',
                        requires_review = true
                    WHERE id = (
                        SELECT id
                        FROM expense_audit_logs
                        WHERE vehicle_expense_id = NEW.id
                        AND action = 'approved'
                        ORDER BY created_at DESC
                        LIMIT 1
                    );
                END IF;
                
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");
        
        DB::statement("
            CREATE TRIGGER detect_anomalies_on_expense
            AFTER INSERT OR UPDATE ON vehicle_expenses
            FOR EACH ROW
            EXECUTE FUNCTION detect_expense_anomalies();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            // Supprimer les triggers et fonctions
            DB::statement("DROP TRIGGER IF EXISTS audit_expense_changes ON vehicle_expenses");
            DB::statement("DROP TRIGGER IF EXISTS detect_anomalies_on_expense ON vehicle_expenses");
            DB::statement("DROP FUNCTION IF EXISTS log_expense_changes()");
            DB::statement("DROP FUNCTION IF EXISTS detect_expense_anomalies()");

            // Supprimer la vue
            DB::statement("DROP VIEW IF EXISTS expense_audit_summary");
        }
        
        // Supprimer la table
        Schema::dropIfExists('expense_audit_logs');
    }
};
