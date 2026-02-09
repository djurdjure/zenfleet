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
            !Schema::hasTable('maintenance_schedules') ||
            !Schema::hasTable('vehicles') ||
            !Schema::hasColumn('maintenance_schedules', 'vehicle_id')
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
            ->where('tc.table_name', 'maintenance_schedules')
            ->where('kcu.column_name', 'vehicle_id')
            ->where('ccu.table_name', 'vehicles')
            ->exists();

        if (!$constraintExists) {
            DB::statement('ALTER TABLE maintenance_schedules
                ADD CONSTRAINT fk_maintenance_schedules_vehicle
                FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql' || !Schema::hasTable('maintenance_schedules')) {
            return;
        }

        DB::statement('ALTER TABLE maintenance_schedules DROP CONSTRAINT IF EXISTS fk_maintenance_schedules_vehicle');
    }
};

