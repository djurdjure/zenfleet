<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement("CREATE TYPE budget_period_enum AS ENUM ('monthly', 'quarterly', 'yearly')");

        Schema::create('expense_budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');

            // Scope du budget
            $table->unsignedBigInteger('vehicle_id')->nullable(); // Budget par véhicule (null = global)
            $table->string('expense_category')->nullable(); // Budget par catégorie (null = toutes)

            // Période budgétaire
            $table->enum('budget_period', ['monthly', 'quarterly', 'yearly']);
            $table->integer('budget_year');
            $table->integer('budget_month')->nullable(); // Pour budgets mensuels
            $table->integer('budget_quarter')->nullable(); // Pour budgets trimestriels

            // Montants budgétaires
            $table->decimal('budgeted_amount', 15, 2);
            $table->decimal('spent_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->storedAs('(budgeted_amount - spent_amount)');
            $table->decimal('variance_percentage', 5, 2)->storedAs('(CASE WHEN budgeted_amount > 0 THEN ((spent_amount - budgeted_amount) / budgeted_amount) * 100 ELSE 0 END)');

            // Seuils d'alerte
            $table->decimal('warning_threshold', 5, 2)->default(80.00); // % pour alerte
            $table->decimal('critical_threshold', 5, 2)->default(95.00); // % pour critique

            // Métadonnées
            $table->text('description')->nullable();
            $table->json('approval_workflow')->default('[]'); // Workflow d'approbation
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['organization_id', 'budget_period', 'budget_year']);
            $table->index(['vehicle_id', 'budget_year']);
            $table->index(['expense_category', 'budget_year']);
            $table->index(['is_active']);

            // Contraintes
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');

            // Contrainte unicité
            $table->unique([
                'organization_id', 'vehicle_id', 'expense_category',
                'budget_period', 'budget_year', 'budget_month', 'budget_quarter'
            ], 'expense_budgets_unique');
        });

        // Contraintes business
        DB::statement("
            ALTER TABLE expense_budgets
            ADD CONSTRAINT valid_budget_amounts CHECK (
                budgeted_amount > 0 AND
                spent_amount >= 0 AND
                warning_threshold > 0 AND warning_threshold <= 100 AND
                critical_threshold > warning_threshold AND critical_threshold <= 100
            )
        ");

        DB::statement("
            ALTER TABLE expense_budgets
            ADD CONSTRAINT valid_budget_period_data CHECK (
                (budget_period = 'monthly' AND budget_month BETWEEN 1 AND 12 AND budget_quarter IS NULL) OR
                (budget_period = 'quarterly' AND budget_quarter BETWEEN 1 AND 4 AND budget_month IS NULL) OR
                (budget_period = 'yearly' AND budget_month IS NULL AND budget_quarter IS NULL)
            )
        ");
    }

    public function down()
    {
        Schema::dropIfExists('expense_budgets');
        DB::statement("DROP TYPE IF EXISTS budget_period_enum");
    }
};