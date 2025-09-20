<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get the first organization
        $organization = Organization::first();
        if (!$organization) {
            $this->command->error('No organization found. Please create an organization first.');
            return;
        }

        // Create permissions if they don't exist
        $permissions = [
            'view_admin_panel',
            'manage_organizations',
            'manage_users',
            'manage_vehicles',
            'manage_drivers',
            'view_reports',
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles if they don't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

        // Assign all permissions to Super Admin role
        $superAdminRole->syncPermissions($permissions);

        // Create Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@zenfleet.dz'],
            [
                'name' => 'Super Administrateur',
                'email' => 'superadmin@zenfleet.dz',
                'password' => Hash::make('ZenFleet2025!'),
                'email_verified_at' => now(),
                'is_active' => true,
                'organization_id' => $organization->id,
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => '+213 21 000 000',
                'job_title' => 'Administrateur Système',
                'hire_date' => now(),
                'is_super_admin' => true,
                'failed_login_attempts' => 0,
                'last_activity_at' => now(),
                'user_status' => 'active',
                'timezone' => 'Africa/Algiers',
                'language' => 'fr',
            ]
        );

        // Assign Super Admin role
        $superAdmin->assignRole($superAdminRole);

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: superadmin@zenfleet.dz');
        $this->command->info('Password: ZenFleet2025!');
    }
}
