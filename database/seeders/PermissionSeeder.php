<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Définition de toutes les permissions ---
        $permissions = [
            'view organizations', 'create organizations', 'edit organizations', 'delete organizations',
            'manage roles', 'view users', 'create users', 'edit users', 'delete users',
            'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles', 'restore vehicles', 'force delete vehicles',
            'view drivers', 'create drivers', 'edit drivers', 'delete drivers', 'restore drivers', 'force delete drivers',
            'view assignments', 'create assignments', 'edit assignments', 'end assignments',
            'view maintenance', 'manage maintenance plans', 'log maintenance',
            'create handovers', 'view handovers', 'edit handovers', 'delete handovers', 'upload signed handovers',
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
        $this->command->info('All permissions created or verified successfully.');

        // --- Création des Rôles ---
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Gestionnaire Flotte']);
        $driverRole = Role::firstOrCreate(['name' => 'Chauffeur']);
        $this->command->info('Roles created or verified successfully.');

        // --- Attribution des Permissions ---
        $superAdminRole->givePermissionTo(Permission::all());
        $this->command->info('All permissions granted to Super Admin role.');

        $adminPermissions = Permission::where('name', 'not like', '%organizations%')->get();
        $adminRole->syncPermissions($adminPermissions);
        $this->command->info('Permissions granted to Admin role.');

        $managerRole->syncPermissions([
            'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles', 'restore vehicles',
            'view drivers', 'create drivers', 'edit drivers', 'delete drivers', 'restore drivers',
            'view assignments', 'create assignments', 'edit assignments', 'end assignments',
            'view maintenance', 'manage maintenance plans', 'log maintenance',
            'create handovers', 'view handovers', 'edit handovers',
        ]);
        $this->command->info('Permissions granted to "Gestionnaire Flotte" role.');

        // Le rôle Chauffeur peut voir les véhicules et ses affectations.
        $driverRole->syncPermissions([
            'view vehicles',
            'view assignments',
        ]);
        $this->command->info('Permissions for "Chauffeur" role have been set.');
    }
}