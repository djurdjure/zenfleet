<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * VehicleMileagePermissionsSeeder - Permissions for vehicle mileage readings
 *
 * Permissions structure:
 * - View: own, team, all (multi-level visibility)
 * - Create: manual readings (drivers, supervisors, admins)
 * - Update: corrections and modifications
 * - Delete: remove incorrect readings
 * - Manage automatic: GPS/telematic integration (admin only)
 *
 * Multi-tenant ready: Permissions are global but scoped by organization via Policy
 *
 * @version 1.0-Enterprise
 */
class VehicleMileagePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📊 Creating vehicle mileage reading permissions...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================================
        // DÉFINITION DES PERMISSIONS
        // ============================================================

        $permissions = [
            // View permissions (multi-level)
            'view own mileage readings',
            'view team mileage readings',
            'view all mileage readings',

            // Create permissions
            'create mileage readings',

            // Update permissions
            'update own mileage readings',
            'update any mileage readings',

            // Delete permissions
            'delete mileage readings',
            'force delete mileage readings',
            'restore mileage readings',

            // Automatic readings (GPS/telematic integration)
            'manage automatic mileage readings',

            // Export & Reports
            'export mileage readings',
            'view mileage statistics',

            // History
            'view mileage reading history',
        ];

        $createdCount = 0;
        $existingCount = 0;

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );

            if ($permission->wasRecentlyCreated) {
                $createdCount++;
                $this->command->info("  ✓ Created: {$permissionName}");
            } else {
                $existingCount++;
            }
        }

        $this->command->info("  📊 {$createdCount} permissions created, {$existingCount} already existed");

        // ============================================================
        // ASSIGNATION AUX RÔLES
        // ============================================================

        $this->command->info('');
        $this->command->info('👥 Assigning permissions to roles...');

        $this->assignSuperAdminPermissions();
        $this->assignAdminPermissions();
        $this->assignFleetManagerPermissions();
        $this->assignSupervisorPermissions();
        $this->assignDriverPermissions();

        $this->command->info('');
        $this->command->info('✅ Vehicle mileage permissions seeded successfully!');
    }

    /**
     * Assign permissions to Super Admin role.
     */
    protected function assignSuperAdminPermissions(): void
    {
        $role = Role::where('name', 'Super Admin')->first();

        if (!$role) {
            $this->command->warn('  ⚠️  Super Admin role not found, skipping...');
            return;
        }

        // Super Admin gets ALL mileage permissions
        $permissions = Permission::where('name', 'like', '%mileage%')->get();
        $role->syncPermissions($permissions);

        $this->command->info("  ✓ Super Admin: {$permissions->count()} permissions assigned");
    }

    /**
     * Assign permissions to Admin role.
     */
    protected function assignAdminPermissions(): void
    {
        $role = Role::where('name', 'Admin')->first();

        if (!$role) {
            $this->command->warn('  ⚠️  Admin role not found, skipping...');
            return;
        }

        $permissions = [
            // View all
            'view all mileage readings',

            // Create manual readings
            'create mileage readings',

            // Update any
            'update any mileage readings',

            // Delete & restore
            'delete mileage readings',
            'force delete mileage readings',
            'restore mileage readings',

            // Automatic readings (GPS integration)
            'manage automatic mileage readings',

            // Export & Statistics
            'export mileage readings',
            'view mileage statistics',

            // History
            'view mileage reading history',
        ];

        $role->givePermissionTo($permissions);
        $this->command->info("  ✓ Admin: " . count($permissions) . " permissions assigned");
    }

    /**
     * Assign permissions to Fleet Manager role.
     */
    protected function assignFleetManagerPermissions(): void
    {
        $role = Role::where('name', 'Gestionnaire Flotte')->first();

        if (!$role) {
            $this->command->warn('  ⚠️  Fleet Manager role not found, skipping...');
            return;
        }

        $permissions = [
            // View all in organization
            'view all mileage readings',

            // Create manual readings
            'create mileage readings',

            // Update any (for corrections)
            'update any mileage readings',

            // Delete (for corrections)
            'delete mileage readings',

            // Automatic readings (GPS integration)
            'manage automatic mileage readings',

            // Export & Statistics
            'export mileage readings',
            'view mileage statistics',

            // History
            'view mileage reading history',
        ];

        $role->givePermissionTo($permissions);
        $this->command->info("  ✓ Fleet Manager: " . count($permissions) . " permissions assigned");
    }

    /**
     * Assign permissions to Supervisor role.
     */
    protected function assignSupervisorPermissions(): void
    {
        $role = Role::where('name', 'Supervisor')->first();

        if (!$role) {
            $this->command->warn('  ⚠️  Supervisor role not found, skipping...');
            return;
        }

        $permissions = [
            // View team readings
            'view team mileage readings',

            // Create manual readings (for team vehicles)
            'create mileage readings',

            // Update own readings
            'update own mileage readings',

            // View statistics
            'view mileage statistics',

            // History
            'view mileage reading history',
        ];

        $role->givePermissionTo($permissions);
        $this->command->info("  ✓ Supervisor: " . count($permissions) . " permissions assigned");
    }

    /**
     * Assign permissions to Driver role.
     */
    protected function assignDriverPermissions(): void
    {
        $role = Role::where('name', 'Chauffeur')->first();

        if (!$role) {
            $this->command->warn('  ⚠️  Driver role not found, skipping...');
            return;
        }

        $permissions = [
            // View own readings only
            'view own mileage readings',

            // Create manual readings (primary use case)
            'create mileage readings',

            // Update own readings (within time window, e.g., 24h)
            'update own mileage readings',

            // Delete own readings (only if pending/recent)
            'delete mileage readings',
        ];

        $role->givePermissionTo($permissions);
        $this->command->info("  ✓ Driver: " . count($permissions) . " permissions assigned");
    }
}
