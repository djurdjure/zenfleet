<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vehicles') || Schema::hasColumn('vehicles', 'is_archived')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            $table->boolean('is_archived')
                ->default(false)
                ->after('notes')
                ->comment('Indique si le vehicule est archive');

            $table->index('is_archived', 'idx_vehicles_archived');
            $table->index(['organization_id', 'is_archived'], 'idx_vehicles_org_archived');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('vehicles') || !Schema::hasColumn('vehicles', 'is_archived')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex('idx_vehicles_org_archived');
            $table->dropIndex('idx_vehicles_archived');
            $table->dropColumn('is_archived');
        });
    }
};
