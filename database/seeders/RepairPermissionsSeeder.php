<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * RepairPermissionsSeeder - Permissions for repair request workflow
 *
 * Workflow permissions:
 * - Level 1 approval: Supervisor
 * - Level 2 approval: Fleet Manager
 * - View permissions: own, team, all
 *
 * @version 1.0-Enterprise
 */
class RepairPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Creating repair request permissions...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================================
        // DÉFINITION DES PERMISSIONS
        // ============================================================

        $permissions = [
            // View permissions
            'view own repair requests',
            'view team repair requests',
            'view all repair requests',

            // Create
            'create repair requests',

            // Update
            'update own repair requests',
            'update any repair requests',

            // Approve/Reject Level 1 (Supervisor)
            'approve repair requests level 1',
            'reject repair requests level 1',

            // Approve/Reject Level 2 (Fleet Manager)
            'approve repair requests level 2',
            'reject repair requests level 2',

            // Delete
            'delete repair requests',
            'force delete repair requests',
            'restore repair requests',

            // History & Notifications
            'view repair request history',
            'view repair request notifications',

            // Export
            'export repair requests',

            // Manage categories and depots
            'manage vehicle categories',
            'manage vehicle depots',
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
        $this->command->info('✅ Repair request permissions seeded successfully!');
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

        // Super Admin gets ALL repair permissions
        $permissions = Permission::where('name', 'like', '%repair%')->get();
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
            // View
            'view all repair requests',

            // Create
            'create repair requests',

            // Update
            'update own repair requests',
            'update any repair requests',

            // Approve/Reject both levels
            'approve repair requests level 1',
            'reject repair requests level 1',
            'approve repair requests level 2',
            'reject repair requests level 2',

            // Delete
            'delete repair requests',
            'force delete repair requests',
            'restore repair requests',

            // History & Notifications
            'view repair request history',
            'view repair request notifications',

            // Export
            'export repair requests',

            // Manage
            'manage vehicle categories',
            'manage vehicle depots',
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
            // View
            'view all repair requests',

            // Create (can create on behalf of drivers)
            'create repair requests',

            // Update
            'update own repair requests',

            // Approve/Reject level 1 & 2
            'approve repair requests level 1',
            'reject repair requests level 1',
            'approve repair requests level 2',
            'reject repair requests level 2',

            // Delete
            'delete repair requests',

            // History & Notifications
            'view repair request history',
            'view repair request notifications',

            // Export
            'export repair requests',

            // Manage
            'manage vehicle categories',
            'manage vehicle depots',
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
            // View team only
            'view team repair requests',

            // Create (can create on behalf of team)
            'create repair requests',

            // Update own
            'update own repair requests',

            // Approve/Reject level 1 only
            'approve repair requests level 1',
            'reject repair requests level 1',

            // History & Notifications
            'view repair request history',
            'view repair request notifications',
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
            // View own only
            'view own repair requests',

            // Create
            'create repair requests',

            // Update own
            'update own repair requests',

            // Delete own pending
            'delete repair requests',

            // Notifications
            'view repair request notifications',
        ];

        $role->givePermissionTo($permissions);
        $this->command->info("  ✓ Driver: " . count($permissions) . " permissions assigned");
    }
}
