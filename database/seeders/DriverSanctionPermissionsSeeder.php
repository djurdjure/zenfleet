<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * DriverSanctionPermissionsSeeder - Permissions for driver sanctions management
 *
 * Permissions structure:
 * - View: sanctions history with multi-level access
 * - Create: issue sanctions (supervisors, admins)
 * - Update: modify sanctions (time-limited for supervisors, unlimited for admins)
 * - Delete: remove sanctions (admins only)
 * - Archive: close sanctions after retention period
 *
 * Multi-tenant ready: Permissions are global but scoped by organization via Policy
 *
 * @author ZenFleet Enterprise Team
 * @version 1.0-Enterprise
 */
class DriverSanctionPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('⚖️  Creating driver sanction permissions...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================================
        // DÉFINITION DES PERMISSIONS
        // ============================================================

        $permissions = [
            // View permissions (multi-level)
            'view own driver sanctions',
            'view team driver sanctions',
            'view all driver sanctions',

            // Create permissions
            'create driver sanctions',

            // Update permissions
            'update own driver sanctions',
            'update any driver sanctions',

            // Delete permissions
            'delete driver sanctions',
            'force delete driver sanctions',
            'restore driver sanctions',

            // Archive management
            'archive driver sanctions',
            'unarchive driver sanctions',

            // Export & Reports
            'export driver sanctions',
            'view driver sanction statistics',

            // History & Audit
            'view driver sanction history',
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

        $this->command->info("  ⚖️  {$createdCount} permissions created, {$existingCount} already existed");

        // ============================================================
        // ASSIGNATION AUX RÔLES
        // ============================================================

        $this->command->info('');
        $this->command->info('👥 Assigning permissions to roles...');

        $this->assignSuperAdminPermissions();
        $this->assignAdminPermissions();
        $this->assignFleetManagerPermissions();
        $this->assignSupervisorPermissions();

        $this->command->info('');
        $this->command->info('✅ Driver sanction permissions seeded successfully!');
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

        // Super Admin gets ALL sanction permissions
        $permissions = Permission::where('name', 'like', '%driver sanction%')->get();
        $role->syncPermissions($permissions->merge($role->permissions));

        $this->command->info("  ✓ Super Admin: {$permissions->count()} sanction permissions assigned");
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
            // View all in organization
            'view all driver sanctions',

            // Create sanctions
            'create driver sanctions',

            // Update any (no time limit)
            'update any driver sanctions',

            // Delete & restore
            'delete driver sanctions',
            'force delete driver sanctions',
            'restore driver sanctions',

            // Archive management
            'archive driver sanctions',
            'unarchive driver sanctions',

            // Export & Statistics
            'export driver sanctions',
            'view driver sanction statistics',

            // History
            'view driver sanction history',
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
            'view all driver sanctions',

            // Create sanctions
            'create driver sanctions',

            // Update any
            'update any driver sanctions',

            // Delete
            'delete driver sanctions',

            // Archive management
            'archive driver sanctions',
            'unarchive driver sanctions',

            // Export & Statistics
            'export driver sanctions',
            'view driver sanction statistics',

            // History
            'view driver sanction history',
        ];

        $role->givePermissionTo($permissions);
        $this->command->info("  ✓ Fleet Manager: " . count($permissions) . " permissions assigned");
    }

    /**
     * Assign permissions to Supervisor role.
     */
    protected function assignSupervisorPermissions(): void
    {
        $role = Role::where('name', 'Superviseur')->first();

        if (!$role) {
            $this->command->warn('  ⚠️  Supervisor Transport role not found, skipping...');
            return;
        }

        $permissions = [
            // View own created sanctions and team sanctions
            'view own driver sanctions',
            'view team driver sanctions',

            // Create sanctions (primary function)
            'create driver sanctions',

            // Update own sanctions (time-limited via Policy)
            'update own driver sanctions',

            // Archive own sanctions (after 30 days)
            'archive driver sanctions',

            // View statistics
            'view driver sanction statistics',

            // History
            'view driver sanction history',
        ];

        $role->givePermissionTo($permissions);
        $this->command->info("  ✓ Supervisor Transport: " . count($permissions) . " permissions assigned");
    }
}
