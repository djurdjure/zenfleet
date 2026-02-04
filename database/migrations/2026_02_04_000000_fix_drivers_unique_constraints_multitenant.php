<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('drivers') || !Schema::hasColumn('drivers', 'organization_id')) {
            return;
        }

        // Drop global unique constraints if they exist
        DB::statement('ALTER TABLE drivers DROP CONSTRAINT IF EXISTS drivers_employee_number_unique');
        DB::statement('ALTER TABLE drivers DROP CONSTRAINT IF EXISTS drivers_license_number_unique');
        DB::statement('ALTER TABLE drivers DROP CONSTRAINT IF EXISTS drivers_personal_email_unique');

        // Create org-scoped unique indexes (ignore soft-deleted rows)
        DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS drivers_employee_number_org_unique
            ON drivers (organization_id, employee_number)
            WHERE deleted_at IS NULL AND employee_number IS NOT NULL");

        DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS drivers_license_number_org_unique
            ON drivers (organization_id, license_number)
            WHERE deleted_at IS NULL AND license_number IS NOT NULL");

        DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS drivers_personal_email_org_unique
            ON drivers (organization_id, personal_email)
            WHERE deleted_at IS NULL AND personal_email IS NOT NULL");
    }

    public function down(): void
    {
        if (!Schema::hasTable('drivers')) {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS drivers_employee_number_org_unique');
        DB::statement('DROP INDEX IF EXISTS drivers_license_number_org_unique');
        DB::statement('DROP INDEX IF EXISTS drivers_personal_email_org_unique');
    }
};
