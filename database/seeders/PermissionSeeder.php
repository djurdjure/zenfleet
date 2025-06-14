<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Vider le cache des permissions avant de commencer
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Définition de toutes les permissions ---

        // Permissions pour la gestion des utilisateurs
        Permission::firstOrCreate(['name' => 'view users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete users', 'guard_name' => 'web']);

        // Permissions pour la gestion des rôles
        Permission::firstOrCreate(['name' => 'manage roles', 'guard_name' => 'web']);

        // Permissions pour la gestion des véhicules
        Permission::firstOrCreate(['name' => 'view vehicles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create vehicles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit vehicles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete vehicles', 'guard_name' => 'web']); // Archiver
        Permission::firstOrCreate(['name' => 'restore vehicles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'force delete vehicles', 'guard_name' => 'web']);

        // Permissions pour la gestion des chauffeurs
        Permission::firstOrCreate(['name' => 'view drivers', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create drivers', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit drivers', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete drivers', 'guard_name' => 'web']); // Archiver
        Permission::firstOrCreate(['name' => 'restore drivers', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'force delete drivers', 'guard_name' => 'web']);

        // Permissions pour la gestion des affectations
        Permission::firstOrCreate(['name' => 'view assignments', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create assignments', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit assignments', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'end assignments', 'guard_name' => 'web']); // Pour terminer une affectation

        // --- AJOUT : PERMISSIONS POUR LA MAINTENANCE ---
        Permission::firstOrCreate(['name' => 'view maintenance', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage maintenance plans', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'log maintenance', 'guard_name' => 'web']);
        // ...
        Permission::firstOrCreate(['name' => 'manage maintenance plans', 'guard_name' => 'web']);



        $this->command->info('All permissions created or verified successfully.');

        // --- Définition des Rôles ---
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'Gestionnaire Flotte', 'guard_name' => 'web']);
        $driverRole = Role::firstOrCreate(['name' => 'Chauffeur', 'guard_name' => 'web']);

        $this->command->info('Roles created or verified successfully.');

        // --- Attribution des Permissions aux Rôles ---

        // L'Admin a toutes les permissions
        $adminRole->givePermissionTo(Permission::all());
        $this->command->info('All permissions granted to Admin role.');

        // Le Gestionnaire de Flotte a des droits étendus sur les véhicules et chauffeurs
        $managerRole->syncPermissions([
            'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles', 'restore vehicles',
            'view drivers', 'create drivers', 'edit drivers', 'delete drivers', 'restore drivers',
            'view assignments', 'create assignments', 'edit assignments', 'end assignments',
        ]);
        $this->command->info('Permissions granted to "Gestionnaire Flotte" role.');

        // Le Chauffeur a des droits très limités
        $driverRole->syncPermissions([
            // Pour l'instant, un chauffeur n'a pas de permission par défaut.
            // On pourrait ajouter 'view own assignments' plus tard.
        ]);
        $this->command->info('Permissions granted to "Chauffeur" role.');

        // --- Assignation du rôle Admin à l'utilisateur par défaut ---
        $adminUser = User::where('email', 'admin@zenfleet.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('Admin');
            $this->command->info('Admin role assigned to default admin user.');
        }
    }
}
