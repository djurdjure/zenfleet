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
        Schema::create('expense_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            
            // Budget management
            $table->decimal('budget_allocated', 15, 2)->default(0);
            $table->decimal('budget_used', 15, 2)
                ->default(0)
                ->comment('Calculé automatiquement depuis vehicle_expenses');
            $table->decimal('budget_remaining', 15, 2)
                ->storedAs('budget_allocated - budget_used')
                ->comment('Budget restant calculé');
            
            // Période fiscale
            $table->integer('fiscal_year')->default(date('Y'));
            $table->integer('fiscal_quarter')->nullable();
            $table->integer('fiscal_month')->nullable();
            
            // Configuration
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('alert_on_threshold')->default(true);
            $table->decimal('alert_threshold_percentage', 5, 2)->default(80.00);
            $table->boolean('block_on_exceeded')->default(false);
            
            // Métadonnées flexibles
            $table->json('metadata')->default('{}');
            $table->json('tags')->default('[]');
            $table->json('responsible_users')->default('[]'); // IDs des responsables
            
            // Traçabilité
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour performance
            $table->unique(['organization_id', 'name', 'fiscal_year']);
            $table->index(['organization_id', 'is_active']);
            $table->index(['organization_id', 'fiscal_year']);
            $table->index(['budget_remaining']);
            $table->index('created_by');
            
            // Contraintes
            $table->foreign('organization_id')
                ->references('id')->on('organizations')
                ->onDelete('cascade');
            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('restrict');
            $table->foreign('updated_by')
                ->references('id')->on('users')
                ->onDelete('set null');
        });
        
        // Contraintes métier PostgreSQL
        DB::statement("
            ALTER TABLE expense_groups
            ADD CONSTRAINT valid_budget_amounts CHECK (
                budget_allocated >= 0 AND
                budget_used >= 0
            )
        ");
        
        DB::statement("
            ALTER TABLE expense_groups
            ADD CONSTRAINT valid_alert_threshold CHECK (
                alert_threshold_percentage BETWEEN 0 AND 100
            )
        ");
        
        DB::statement("
            ALTER TABLE expense_groups
            ADD CONSTRAINT valid_fiscal_period CHECK (
                fiscal_year BETWEEN 2020 AND 2100 AND
                (fiscal_quarter IS NULL OR fiscal_quarter BETWEEN 1 AND 4) AND
                (fiscal_month IS NULL OR fiscal_month BETWEEN 1 AND 12)
            )
        ");
        
        // Trigger pour mise à jour automatique du budget_used
        DB::statement("
            CREATE OR REPLACE FUNCTION update_expense_group_budget()
            RETURNS TRIGGER AS $$
            BEGIN
                IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
                    UPDATE expense_groups
                    SET budget_used = (
                        SELECT COALESCE(SUM(total_ttc), 0)
                        FROM vehicle_expenses
                        WHERE expense_group_id = COALESCE(NEW.expense_group_id, OLD.expense_group_id)
                        AND deleted_at IS NULL
                    )
                    WHERE id = COALESCE(NEW.expense_group_id, OLD.expense_group_id);
                END IF;
                
                IF TG_OP = 'DELETE' THEN
                    UPDATE expense_groups
                    SET budget_used = (
                        SELECT COALESCE(SUM(total_ttc), 0)
                        FROM vehicle_expenses
                        WHERE expense_group_id = OLD.expense_group_id
                        AND deleted_at IS NULL
                    )
                    WHERE id = OLD.expense_group_id;
                END IF;
                
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer le trigger et la fonction
        DB::statement("DROP TRIGGER IF EXISTS update_group_budget_on_expense ON vehicle_expenses");
        DB::statement("DROP FUNCTION IF EXISTS update_expense_group_budget()");
        
        Schema::dropIfExists('expense_groups');
    }
};
