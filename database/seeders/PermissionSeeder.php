<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Enums\Permission as PermissionEnum;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Définition de toutes les permissions ---
        $permissions = PermissionEnum::all();

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
            PermissionEnum::VIEW_VEHICLES->value,
            PermissionEnum::CREATE_VEHICLES->value,
            PermissionEnum::EDIT_VEHICLES->value,
            PermissionEnum::DELETE_VEHICLES->value,
            PermissionEnum::RESTORE_VEHICLES->value,
            PermissionEnum::VIEW_DRIVERS->value,
            PermissionEnum::CREATE_DRIVERS->value,
            PermissionEnum::EDIT_DRIVERS->value,
            PermissionEnum::DELETE_DRIVERS->value,
            PermissionEnum::RESTORE_DRIVERS->value,
            PermissionEnum::VIEW_ASSIGNMENTS->value,
            PermissionEnum::CREATE_ASSIGNMENTS->value,
            PermissionEnum::EDIT_ASSIGNMENTS->value,
            PermissionEnum::END_ASSIGNMENTS->value,
            PermissionEnum::VIEW_MAINTENANCE->value,
            PermissionEnum::MANAGE_MAINTENANCE_PLANS->value,
            PermissionEnum::LOG_MAINTENANCE->value,
            PermissionEnum::CREATE_HANDOVERS->value,
            PermissionEnum::VIEW_HANDOVERS->value,
            PermissionEnum::EDIT_HANDOVERS->value,
            PermissionEnum::VIEW_DOCUMENTS->value,
            PermissionEnum::CREATE_DOCUMENTS->value,
            PermissionEnum::EDIT_DOCUMENTS->value,
            PermissionEnum::DELETE_DOCUMENTS->value,
            PermissionEnum::MANAGE_DOCUMENT_CATEGORIES->value,
        ]);
        $this->command->info('Permissions granted to "Gestionnaire Flotte" role.');

        // Le rôle Chauffeur peut voir les véhicules et ses affectations.
        $driverRole->syncPermissions([
            PermissionEnum::VIEW_VEHICLES->value,
            PermissionEnum::VIEW_ASSIGNMENTS->value,
        ]);
        $this->command->info('Permissions for "Chauffeur" role have been set.');
    }
}
