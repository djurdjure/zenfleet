<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;
use App\Services\PermissionService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InitialRbacSeeder extends Seeder
{
    private PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function run(): void
    {
        // 1. Initialiser les permissions système
        $this->permissionService->initializeSystemPermissions();

        // 2. Créer l'organisation principale
        $mainOrganization = Organization::firstOrCreate([
            'slug' => 'zenfleet-main'
        ], [
            'name' => 'ZenFleet Principal',
            'email' => 'admin@zenfleet.app',
            'phone' => '+33 1 23 45 67 89',
            'address' => '123 Rue de la Technologie, Paris, France',
            'subscription_plan' => 'enterprise',
            'is_active' => true,
            'settings' => [
                'timezone' => 'Europe/Paris',
                'currency' => 'EUR',
                'language' => 'fr',
                'date_format' => 'd/m/Y'
            ]
        ]);

        // 3. Créer le Super Admin
        $superAdmin = User::firstOrCreate([
            'email' => 'superadmin@zenfleet.app'
        ], [
            'name' => 'Super Administrateur',
            'email_verified_at' => now(),
            'password' => Hash::make('SuperAdmin2025!'),
            'organization_id' => null, // Super admin n'appartient à aucune organisation spécifique
            'is_super_admin' => true,
            'is_active' => true,
            'remember_token' => Str::random(10)
        ]);

        $superAdmin->assignRole('super_admin');

        // 4. Créer un Admin pour l'organisation principale
        $orgAdmin = User::firstOrCreate([
            'email' => 'admin@zenfleet.app'
        ], [
            'name' => 'Administrateur Organisation',
            'email_verified_at' => now(),
            'password' => Hash::make('AdminZen2025!'),
            'organization_id' => $mainOrganization->id,
            'is_super_admin' => false,
            'is_active' => true,
            'remember_token' => Str::random(10)
        ]);

        $orgAdmin->assignRole('admin');

        // 5. Créer un Gestionnaire de Flotte
        $fleetManager = User::firstOrCreate([
            'email' => 'fleet@zenfleet.app'
        ], [
            'name' => 'Gestionnaire de Flotte',
            'email_verified_at' => now(),
            'password' => Hash::make('FleetManager2025!'),
            'organization_id' => $mainOrganization->id,
            'is_super_admin' => false,
            'is_active' => true,
            'remember_token' => Str::random(10)
        ]);

        $fleetManager->assignRole('fleet_manager');

        // 6. Créer un Superviseur
        $supervisor = User::firstOrCreate([
            'email' => 'supervisor@zenfleet.app'
        ], [
            'name' => 'Superviseur Équipe',
            'email_verified_at' => now(),
            'password' => Hash::make('Supervisor2025!'),
            'organization_id' => $mainOrganization->id,
            'is_super_admin' => false,
            'is_active' => true,
            'remember_token' => Str::random(10)
        ]);

        $supervisor->assignRole('supervisor');

        // 7. Créer des Chauffeurs de test
        for ($i = 1; $i <= 3; $i++) {
            $driver = User::firstOrCreate([
                'email' => "chauffeur{$i}@zenfleet.app"
            ], [
                'name' => "Chauffeur Test {$i}",
                'email_verified_at' => now(),
                'password' => Hash::make('Driver2025!'),
                'organization_id' => $mainOrganization->id,
                'is_super_admin' => false,
                'is_active' => true,
                'remember_token' => Str::random(10)
            ]);

            $driver->assignRole('driver');
        }

        $this->command->info('✅ Système RBAC initialisé avec succès');
        $this->command->info('🔐 Comptes créés :');
        $this->command->info('   Super Admin: superadmin@zenfleet.app / SuperAdmin2025!');
        $this->command->info('   Admin Org: admin@zenfleet.app / AdminZen2025!');
        $this->command->info('   Gestionnaire: fleet@zenfleet.app / FleetManager2025!');
        $this->command->info('   Superviseur: supervisor@zenfleet.app / Supervisor2025!');
        $this->command->info('   Chauffeurs: chauffeur1-3@zenfleet.app / Driver2025!');
    }
}

