<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EnterpriseUsersSeeder extends Seeder
{
    /**
     * Run the database seeds - Enterprise Users & Permissions System
     */
    public function run(): void
    {
        echo "🚀 Seeding Enterprise Users & Permissions System...\n";

        // 1. Créer les permissions du système
        $this->createPermissions();

        // 2. Créer les rôles avec permissions
        $this->createRoles();

        // 3. Créer les utilisateurs de test
        $this->createTestUsers();

        echo "🎉 Enterprise Users & Permissions System ready!\n";
    }

    /**
     * Créer toutes les permissions du système ZenFleet
     */
    private function createPermissions(): void
    {
        echo "\n📋 Creating Enterprise Permissions...\n";

        $permissions = [
            // Permissions Organisations
            'view organizations',
            'create organizations',
            'edit organizations',
            'delete organizations',

            // Permissions Utilisateurs
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage user roles',

            // Permissions Véhicules
            'view vehicles',
            'create vehicles',
            'edit vehicles',
            'delete vehicles',
            'import vehicles',

            // Permissions Chauffeurs
            'view drivers',
            'create drivers',
            'edit drivers',
            'delete drivers',
            'import drivers',

            // Permissions Affectations
            'view assignments',
            'create assignments',
            'edit assignments',
            'delete assignments',
            'end assignments',
            'view assignment statistics',

            // Permissions Système
            'view dashboard',
            'view reports',
            'manage settings',
            'view audit logs',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        echo "   ✅ " . count($permissions) . " permissions créées\n";
    }

    /**
     * Créer les rôles avec leurs permissions
     */
    private function createRoles(): void
    {
        echo "\n👥 Creating Enterprise Roles...\n";

        // Rôle Super Admin (Accès total)
        $superAdmin = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web'
        ]);
        $superAdmin->givePermissionTo(\Spatie\Permission\Models\Permission::all());
        echo "   ✅ Super Admin (toutes permissions)\n";

        // Rôle Admin Organisation (Gestion complète de son organisation)
        $admin = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web'
        ]);
        $admin->givePermissionTo([
            'view dashboard',
            'view users', 'create users', 'edit users',
            'view vehicles', 'create vehicles', 'edit vehicles', 'import vehicles',
            'view drivers', 'create drivers', 'edit drivers', 'import drivers',
            'view assignments', 'create assignments', 'edit assignments', 'end assignments', 'view assignment statistics',
            'view reports'
        ]);
        echo "   ✅ Admin (gestion organisation)\n";

        // Rôle Superviseur (Gestion opérationnelle)
        $supervisor = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'Superviseur',
            'guard_name' => 'web'
        ]);
        $supervisor->givePermissionTo([
            'view dashboard',
            'view vehicles', 'edit vehicles',
            'view drivers', 'edit drivers',
            'view assignments', 'create assignments', 'edit assignments', 'end assignments',
            'view reports'
        ]);
        echo "   ✅ Superviseur (opérations)\n";

        // Rôle Gestionnaire Flotte
        $fleetManager = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'Gestionnaire Flotte',
            'guard_name' => 'web'
        ]);
        $fleetManager->givePermissionTo([
            'view dashboard',
            'view vehicles', 'create vehicles', 'edit vehicles',
            'view assignments', 'create assignments', 'edit assignments',
            'view reports'
        ]);
        echo "   ✅ Gestionnaire Flotte (véhicules & affectations)\n";

        // Rôle Chauffeur (Accès limité)
        $driver = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'Chauffeur',
            'guard_name' => 'web'
        ]);
        $driver->givePermissionTo([
            'view dashboard',
            'view assignments'
        ]);
        echo "   ✅ Chauffeur (consultation)\n";
    }

    /**
     * Créer les utilisateurs de test avec différents rôles
     */
    private function createTestUsers(): void
    {
        echo "\n🧪 Creating Enterprise Test Users...\n";

        $orgId = \App\Models\Organization::first()->id;

        // 1. Super Admin
        $superAdmin = \App\Models\User::firstOrCreate([
            'email' => 'superadmin@zenfleet.dz'
        ], [
            'name' => 'Super Administrateur',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@zenfleet.dz',
            'phone' => '021100001',
            'password' => \Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'active',
            'organization_id' => $orgId,
            'email_verified_at' => now()
        ]);
        $superAdmin->assignRole('Super Admin');
        echo "   ✅ Super Admin: superadmin@zenfleet.dz / password\n";

        // 2. Admin Organisation
        $admin = \App\Models\User::firstOrCreate([
            'email' => 'admin@zenfleet.dz'
        ], [
            'name' => 'Administrateur ZenFleet',
            'first_name' => 'Admin',
            'last_name' => 'ZenFleet',
            'email' => 'admin@zenfleet.dz',
            'phone' => '021123456',
            'password' => \Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'organization_id' => $orgId,
            'email_verified_at' => now()
        ]);
        $admin->assignRole('Admin');
        echo "   ✅ Admin: admin@zenfleet.dz / password\n";

        // 3. Superviseur
        $supervisor = \App\Models\User::firstOrCreate([
            'email' => 'superviseur@zenfleet.dz'
        ], [
            'name' => 'Superviseur Opérations',
            'first_name' => 'Ahmed',
            'last_name' => 'Supervisor',
            'email' => 'superviseur@zenfleet.dz',
            'phone' => '021200001',
            'password' => \Hash::make('password'),
            'role' => 'supervisor',
            'status' => 'active',
            'organization_id' => $orgId,
            'email_verified_at' => now()
        ]);
        $supervisor->assignRole('Superviseur');
        echo "   ✅ Superviseur: superviseur@zenfleet.dz / password\n";

        // 4. Gestionnaire Flotte
        $fleetManager = \App\Models\User::firstOrCreate([
            'email' => 'flotte@zenfleet.dz'
        ], [
            'name' => 'Gestionnaire Flotte',
            'first_name' => 'Youcef',
            'last_name' => 'Fleet',
            'email' => 'flotte@zenfleet.dz',
            'phone' => '021300001',
            'password' => \Hash::make('password'),
            'role' => 'fleet_manager',
            'status' => 'active',
            'organization_id' => $orgId,
            'email_verified_at' => now()
        ]);
        $fleetManager->assignRole('Gestionnaire Flotte');
        echo "   ✅ Gestionnaire Flotte: flotte@zenfleet.dz / password\n";

        // 5. Chauffeur Test
        $driverUser = \App\Models\User::firstOrCreate([
            'email' => 'chauffeur@zenfleet.dz'
        ], [
            'name' => 'Chauffeur Test',
            'first_name' => 'Mohamed',
            'last_name' => 'Driver',
            'email' => 'chauffeur@zenfleet.dz',
            'phone' => '0555400001',
            'password' => \Hash::make('password'),
            'role' => 'driver',
            'status' => 'active',
            'organization_id' => $orgId,
            'email_verified_at' => now()
        ]);
        $driverUser->assignRole('Chauffeur');
        echo "   ✅ Chauffeur: chauffeur@zenfleet.dz / password\n";

        // 6. Associer le chauffeur test avec un profil Driver
        $existingDriver = \App\Models\Driver::first();
        if ($existingDriver) {
            $existingDriver->update(['user_id' => $driverUser->id, 'email' => $driverUser->email]);
            echo "   ✅ Profil chauffeur lié à l'utilisateur\n";
        }

        echo "\n📊 Récapitulatif des comptes de test:\n";
        echo "   🔐 superadmin@zenfleet.dz / password (Accès total)\n";
        echo "   🔐 admin@zenfleet.dz / password (Admin organisation)\n";
        echo "   🔐 superviseur@zenfleet.dz / password (Superviseur opérations)\n";
        echo "   🔐 flotte@zenfleet.dz / password (Gestionnaire flotte)\n";
        echo "   🔐 chauffeur@zenfleet.dz / password (Chauffeur)\n";
    }
}
