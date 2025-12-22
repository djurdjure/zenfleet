<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HandoverTemplatePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the new permission
        $permission = Permission::firstOrCreate([
            'name' => 'manage handover templates',
            'guard_name' => 'web',
        ]);

        $this->command->info("Permission 'manage handover templates' created.");

        // Assign to Admin and Fleet Manager roles if they exist
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole && !$adminRole->hasPermissionTo($permission)) {
            $adminRole->givePermissionTo($permission);
            $this->command->info("   - Assigned to 'Admin' role");
        }

        $fleetManagerRole = Role::where('name', 'Fleet Manager')->first();
        if ($fleetManagerRole && !$fleetManagerRole->hasPermissionTo($permission)) {
            $fleetManagerRole->givePermissionTo($permission);
            $this->command->info("   - Assigned to 'Fleet Manager' role");
        }

        // Also assign to Super Admin if it exists
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole && !$superAdminRole->hasPermissionTo($permission)) {
            $superAdminRole->givePermissionTo($permission);
            $this->command->info("   - Assigned to 'Super Admin' role");
        }

        $this->command->info('Handover template permissions setup completed.');
    }
}
