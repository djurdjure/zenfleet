<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Driver;
use Spatie\Permission\Models\Role;

class DashboardTestDataSeeder extends Seeder
{
    /**
     * Create comprehensive test data for dashboard
     */
    public function run(): void
    {
        echo "🏢 Création des données de test pour le dashboard...\n";

        // 1. S'assurer qu'il y a des rôles
        $this->createRoles();

        // 2. Créer des organisations avec des statistiques variées
        $this->createOrganizationsWithStats();

        // 3. S'assurer qu'il y a des utilisateurs Super Admin
        $this->createSuperAdminUser();

        // 4. Créer des données réalistes
        $this->createRealisticData();

        echo "✅ Dashboard test data created successfully!\n";
        $this->displaySummary();
    }

    private function createRoles()
    {
        echo "   📋 Création des rôles de test...\n";

        $roles = [
            'Super Admin' => 'Super Administrateur système',
            'Admin' => 'Administrateur organisation',
            'Gestionnaire Flotte' => 'Gestionnaire de flotte',
            'Supervisor' => 'Superviseur opérationnel',
            'Chauffeur' => 'Chauffeur/Driver'
        ];

        foreach ($roles as $roleName => $description) {
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['description' => $description]
            );
        }

        echo "      ✅ " . count($roles) . " rôles créés/vérifiés\n";
    }

    private function createOrganizationsWithStats()
    {
        echo "   🏢 Création d'organisations avec statistiques variées...\n";

        $organizations = [
            [
                'name' => 'ZenFleet Enterprise',
                'status' => 'active',
                'city' => 'Alger',
                'wilaya' => '16',
                'users_to_create' => 15,
                'vehicles_to_create' => 25,
                'drivers_to_create' => 20,
            ],
            [
                'name' => 'Transport Express Algérie',
                'status' => 'active',
                'city' => 'Constantine',
                'wilaya' => '25',
                'users_to_create' => 8,
                'vehicles_to_create' => 12,
                'drivers_to_create' => 10,
            ],
            [
                'name' => 'Logistique Sud SARL',
                'status' => 'pending',
                'city' => 'Ouargla',
                'wilaya' => '30',
                'users_to_create' => 5,
                'vehicles_to_create' => 8,
                'drivers_to_create' => 6,
            ],
            [
                'name' => 'FleetCorp Ouest',
                'status' => 'inactive',
                'city' => 'Oran',
                'wilaya' => '31',
                'users_to_create' => 3,
                'vehicles_to_create' => 5,
                'drivers_to_create' => 4,
            ],
        ];

        foreach ($organizations as $orgData) {
            $org = Organization::firstOrCreate(
                ['name' => $orgData['name']],
                [
                    'legal_name' => $orgData['name'] . ' SARL',
                    'organization_type' => 'enterprise',
                    'status' => $orgData['status'],
                    'city' => $orgData['city'],
                    'wilaya' => $orgData['wilaya'],
                    'address' => 'Adresse ' . $orgData['city'],
                    'phone_number' => '+213-555-' . rand(100000, 999999),
                    'primary_email' => strtolower(str_replace(' ', '.', $orgData['name'])) . '@example.dz',
                    'nif' => '000' . $orgData['wilaya'] . rand(100000, 999999),
                    'created_at' => now()->subDays(rand(1, 90)),
                ]
            );

            // Créer des utilisateurs pour cette organisation
            $this->createUsersForOrganization($org, $orgData['users_to_create']);

            // Créer des véhicules pour cette organisation
            $this->createVehiclesForOrganization($org, $orgData['vehicles_to_create']);

            // Créer des chauffeurs pour cette organisation
            $this->createDriversForOrganization($org, $orgData['drivers_to_create']);

            echo "      ✅ {$org->name}: {$orgData['users_to_create']} users, {$orgData['vehicles_to_create']} vehicles, {$orgData['drivers_to_create']} drivers\n";
        }
    }

    private function createUsersForOrganization($org, $count)
    {
        $statuses = ['active', 'active', 'active', 'inactive']; // 75% actifs
        $roles = ['Admin', 'Gestionnaire Flotte', 'Supervisor', 'Chauffeur'];

        for ($i = 1; $i <= $count; $i++) {
            $user = User::firstOrCreate(
                ['email' => "user{$i}.{$org->id}@{$org->name}.test"],
                [
                    'name' => "User {$i} {$org->name}",
                    'first_name' => "User{$i}",
                    'last_name' => "Org{$org->id}",
                    'organization_id' => $org->id,
                    'status' => $statuses[array_rand($statuses)],
                    'role' => $roles[array_rand($roles)],
                    'phone' => '+213-555-' . rand(100000, 999999),
                    'password' => \Hash::make('password'),
                    'email_verified_at' => now(),
                    'created_at' => now()->subDays(rand(1, 60)),
                ]
            );

            // Assigner un rôle aléatoire
            if (!$user->hasAnyRole($roles)) {
                $user->assignRole($roles[array_rand($roles)]);
            }
        }
    }

    private function createVehiclesForOrganization($org, $count)
    {
        $brands = ['Peugeot', 'Renault', 'Ford', 'Hyundai', 'Iveco'];
        $models = ['308', 'Megane', 'Transit', 'H1', 'Daily'];

        for ($i = 1; $i <= $count; $i++) {
            Vehicle::firstOrCreate(
                ['registration_plate' => "DZ-{$org->wilaya}-" . str_pad($org->id . $i, 4, '0', STR_PAD_LEFT)],
                [
                    'organization_id' => $org->id,
                    'brand' => $brands[array_rand($brands)],
                    'model' => $models[array_rand($models)],
                    'year' => rand(2018, 2024),
                    'current_mileage' => rand(5000, 150000),
                    'fuel_capacity' => rand(40, 80),
                    'created_at' => now()->subDays(rand(1, 45)),
                ]
            );
        }
    }

    private function createDriversForOrganization($org, $count)
    {
        $firstNames = ['Ahmed', 'Mohamed', 'Ali', 'Youcef', 'Karim', 'Fatima', 'Aicha', 'Samira'];
        $lastNames = ['Benali', 'Benaissa', 'Meradji', 'Bencherif', 'Khelifi', 'Boudiaf'];

        for ($i = 1; $i <= $count; $i++) {
            Driver::firstOrCreate(
                ['driver_license_number' => "DL-{$org->wilaya}-" . str_pad($org->id . $i, 8, '0', STR_PAD_LEFT)],
                [
                    'organization_id' => $org->id,
                    'first_name' => $firstNames[array_rand($firstNames)],
                    'last_name' => $lastNames[array_rand($lastNames)],
                    'personal_phone' => '+213-555-' . rand(100000, 999999),
                    'email' => "driver{$i}.org{$org->id}@test.dz",
                    'driver_license_number' => "DL-{$org->wilaya}-" . str_pad($org->id . $i, 8, '0', STR_PAD_LEFT),
                    'driver_license_expiry' => now()->addYears(rand(1, 5)),
                    'status' => rand(0, 10) > 2 ? 'active' : 'inactive', // 80% actifs
                    'created_at' => now()->subDays(rand(1, 60)),
                ]
            );
        }
    }

    private function createSuperAdminUser()
    {
        echo "   👑 Création d'un utilisateur Super Admin...\n";

        // S'assurer qu'il y a au moins une organisation pour le Super Admin
        $mainOrg = Organization::first();
        if (!$mainOrg) {
            $mainOrg = Organization::create([
                'name' => 'ZenFleet System',
                'legal_name' => 'ZenFleet Technologies SARL',
                'organization_type' => 'enterprise',
                'status' => 'active',
                'city' => 'Alger',
                'wilaya' => '16',
                'address' => 'Rue de la Technologie, Alger',
                'phone_number' => '+213-21-555-0001',
                'primary_email' => 'system@zenfleet.dz',
                'nif' => '000016999999999',
            ]);
        }

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@zenfleet.dz'],
            [
                'name' => 'Super Administrateur',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'organization_id' => $mainOrg->id,
                'status' => 'active',
                'role' => 'super_admin',
                'phone' => '+213-21-555-0001',
                'password' => \Hash::make('password'),
                'email_verified_at' => now(),
                'is_super_admin' => true,
            ]
        );

        if (!$superAdmin->hasRole('Super Admin')) {
            $superAdmin->assignRole('Super Admin');
        }

        echo "      ✅ Super Admin créé: superadmin@zenfleet.dz / password\n";
    }

    private function createRealisticData()
    {
        echo "   📊 Ajout de données réalistes supplémentaires...\n";

        // Créer quelques organisations avec des statuts différents
        $statusDistribution = [
            'active' => 0.7,    // 70% actives
            'pending' => 0.2,   // 20% en attente
            'inactive' => 0.1,  // 10% inactives
        ];

        $currentOrgCount = Organization::count();
        $targetOrgCount = max(10, $currentOrgCount + 3); // Au moins 10 organisations

        for ($i = $currentOrgCount + 1; $i <= $targetOrgCount; $i++) {
            $status = $this->getRandomStatus($statusDistribution);

            Organization::create([
                'name' => "Organisation Test {$i}",
                'legal_name' => "Organisation Test {$i} SARL",
                'organization_type' => collect(['sme', 'enterprise', 'startup'])->random(),
                'status' => $status,
                'city' => collect(['Alger', 'Constantine', 'Oran', 'Annaba', 'Blida'])->random(),
                'wilaya' => str_pad(rand(1, 48), 2, '0', STR_PAD_LEFT),
                'address' => "Adresse test {$i}",
                'phone_number' => '+213-555-' . rand(100000, 999999),
                'primary_email' => "org{$i}@test.dz",
                'nif' => '000' . str_pad(rand(1, 48), 2, '0', STR_PAD_LEFT) . rand(100000, 999999),
                'created_at' => now()->subDays(rand(1, 120)),
            ]);
        }

        echo "      ✅ Données réalistes ajoutées\n";
    }

    private function getRandomStatus($distribution)
    {
        $rand = mt_rand() / mt_getrandmax();
        $cumulative = 0;

        foreach ($distribution as $status => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $status;
            }
        }

        return 'active'; // fallback
    }

    private function displaySummary()
    {
        echo "\n📊 Résumé des données créées:\n";
        echo "   - Organisations: " . Organization::count() . "\n";
        echo "     * Actives: " . Organization::where('status', 'active')->count() . "\n";
        echo "     * En attente: " . Organization::where('status', 'pending')->count() . "\n";
        echo "     * Inactives: " . Organization::where('status', 'inactive')->count() . "\n";
        echo "   - Utilisateurs: " . User::count() . "\n";
        echo "     * Actifs: " . User::where('status', 'active')->count() . "\n";
        echo "   - Véhicules: " . Vehicle::count() . "\n";
        echo "   - Chauffeurs: " . Driver::count() . "\n";
        echo "   - Rôles: " . Role::count() . "\n";

        echo "\n🔐 Comptes de test:\n";
        echo "   - Super Admin: superadmin@zenfleet.dz / password\n";
        echo "   - Admin Org: admin@zenfleet.dz / password\n";

        echo "\n🎯 Le dashboard Super Admin est maintenant prêt pour les tests!\n";
    }
}