<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::getConnection()->getDriverName() !== 'pgsql' ||
            !Schema::hasTable('repair_requests') ||
            !Schema::hasTable('vehicles') ||
            !Schema::hasColumn('repair_requests', 'vehicle_id')
        ) {
            return;
        }

        $constraintExists = DB::table('information_schema.table_constraints as tc')
            ->join('information_schema.key_column_usage as kcu', function ($join) {
                $join->on('tc.constraint_name', '=', 'kcu.constraint_name')
                    ->on('tc.table_schema', '=', 'kcu.table_schema');
            })
            ->join('information_schema.constraint_column_usage as ccu', function ($join) {
                $join->on('tc.constraint_name', '=', 'ccu.constraint_name')
                    ->on('tc.table_schema', '=', 'ccu.table_schema');
            })
            ->where('tc.constraint_type', 'FOREIGN KEY')
            ->where('tc.table_schema', DB::raw('current_schema()'))
            ->where('tc.table_name', 'repair_requests')
            ->where('kcu.column_name', 'vehicle_id')
            ->where('ccu.table_name', 'vehicles')
            ->exists();

        if (!$constraintExists) {
            DB::statement('ALTER TABLE repair_requests
                ADD CONSTRAINT fk_repair_requests_vehicle
                FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql' || !Schema::hasTable('repair_requests')) {
            return;
        }

        DB::statement('ALTER TABLE repair_requests DROP CONSTRAINT IF EXISTS fk_repair_requests_vehicle');
    }
};
