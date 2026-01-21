<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Disable transactions for this migration (required for CREATE INDEX CONCURRENTLY)
     */
    public $withinTransaction = false;

    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        $hasVehicles = Schema::hasTable('vehicles');
        $hasDrivers = Schema::hasTable('drivers');

        if (!$hasVehicles && !$hasDrivers) {
            return;
        }

        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm;');

        if ($hasVehicles) {
            if (Schema::hasColumn('vehicles', 'registration_plate')) {
                DB::statement("
                    CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_vehicles_registration_plate_trgm
                    ON vehicles USING gin (registration_plate gin_trgm_ops)
                ");
            }

            if (Schema::hasColumn('vehicles', 'brand')) {
                DB::statement("
                    CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_vehicles_brand_trgm
                    ON vehicles USING gin (brand gin_trgm_ops)
                ");
            }

            if (Schema::hasColumn('vehicles', 'model')) {
                DB::statement("
                    CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_vehicles_model_trgm
                    ON vehicles USING gin (model gin_trgm_ops)
                ");
            }
        }

        if ($hasDrivers) {
            $driverWhere = Schema::hasColumn('drivers', 'deleted_at') ? ' WHERE deleted_at IS NULL' : '';

            if (
                Schema::hasColumn('drivers', 'organization_id')
                && Schema::hasColumn('drivers', 'last_name')
                && Schema::hasColumn('drivers', 'first_name')
            ) {
                DB::statement("
                    CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_drivers_org_name
                    ON drivers (organization_id, last_name, first_name)$driverWhere
                ");
            }

            if (Schema::hasColumn('drivers', 'license_number')) {
                DB::statement("
                    CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_drivers_license_number_trgm
                    ON drivers USING gin (license_number gin_trgm_ops)
                ");
            }
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_vehicles_registration_plate_trgm');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_vehicles_brand_trgm');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_vehicles_model_trgm');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_drivers_org_name');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_drivers_license_number_trgm');
    }
};
