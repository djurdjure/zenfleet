<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expense_budgets')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        Schema::table('expense_budgets', function (Blueprint $table) {
            if (!Schema::hasColumn('expense_budgets', 'scope_type')) {
                $table->string('scope_type', 32)->nullable();
            }

            if (!Schema::hasColumn('expense_budgets', 'scope_description')) {
                $table->text('scope_description')->nullable();
            }
        });

        if (!Schema::hasColumn('expense_budgets', 'status')) {
            if ($driver === 'pgsql') {
                DB::statement("
                    ALTER TABLE expense_budgets
                    ADD COLUMN status VARCHAR(20)
                    GENERATED ALWAYS AS (
                        CASE WHEN COALESCE(is_active, false) THEN 'active' ELSE 'inactive' END
                    ) STORED
                ");
            } else {
                Schema::table('expense_budgets', function (Blueprint $table) {
                    $table->string('status', 20)->default('active');
                });

                DB::table('expense_budgets')->update([
                    'status' => DB::raw("CASE WHEN COALESCE(is_active, 0) = 1 THEN 'active' ELSE 'inactive' END"),
                ]);
            }
        }

        DB::table('expense_budgets')
            ->select('id', 'vehicle_id', 'expense_category')
            ->orderBy('id')
            ->chunkById(500, function ($budgets) {
                foreach ($budgets as $budget) {
                    $scopeType = 'global';
                    $scopeDescription = 'Tous véhicules - Toutes catégories';

                    if (!is_null($budget->vehicle_id)) {
                        $scopeType = 'vehicle';
                        $scopeDescription = "Véhicule #{$budget->vehicle_id}";
                    } elseif (!is_null($budget->expense_category)) {
                        $scopeType = 'category';
                        $scopeDescription = "Catégorie: {$budget->expense_category}";
                    }

                    DB::table('expense_budgets')
                        ->where('id', $budget->id)
                        ->update([
                            'scope_type' => $scopeType,
                            'scope_description' => $scopeDescription,
                        ]);
                }
            });

        if ($driver === 'pgsql') {
            DB::statement('CREATE INDEX IF NOT EXISTS idx_expense_budgets_scope_type ON expense_budgets(scope_type)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_expense_budgets_status ON expense_budgets(status)');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('expense_budgets')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS idx_expense_budgets_scope_type');
            DB::statement('DROP INDEX IF EXISTS idx_expense_budgets_status');
        }

        if (Schema::hasColumn('expense_budgets', 'status')) {
            Schema::table('expense_budgets', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        Schema::table('expense_budgets', function (Blueprint $table) {
            if (Schema::hasColumn('expense_budgets', 'scope_description')) {
                $table->dropColumn('scope_description');
            }

            if (Schema::hasColumn('expense_budgets', 'scope_type')) {
                $table->dropColumn('scope_type');
            }
        });
    }
};
