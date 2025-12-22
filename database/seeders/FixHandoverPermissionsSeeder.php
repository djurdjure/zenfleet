<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FixHandoverPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Définir les permissions nécessaires pour les handovers
        $permissions = [
            'create handovers',
            'view handovers',
            'edit handovers',
            'delete handovers',
            'upload signed handovers',
        ];

        // 2. Créer les permissions si elles n'existent pas
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // 3. Assigner ces permissions aux rôles clés
        $roles = ['Super Admin', 'Admin', 'Gestionnaire Flotte'];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                $role->givePermissionTo($permissions);
            }
        }
    }
}
