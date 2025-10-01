<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Driver;

class OrganizationTestDataSeeder extends Seeder
{
    /**
     * Create test data for organizations page
     */
    public function run(): void
    {
        echo "🏢 Création des données de test pour la page organisations...\n";

        // 1. S'assurer qu'au moins une organisation principale existe
        $mainOrg = Organization::firstOrCreate(
            ['name' => 'ZenFleet Demo'],
            [
                'legal_name' => 'ZenFleet Technologies Algérie',
                'organization_type' => 'enterprise',
                'industry' => 'Transport & Logistique',
                'description' => 'Organisation de démonstration pour ZenFleet',
                'status' => 'active',
                'phone_number' => '+213-21-123-456',
                'primary_email' => 'contact@zenfleet.dz',
                'address' => '123 Rue de la Liberté',
                'city' => 'Alger',
                'commune' => 'Sidi M\'Hamed',
                'zip_code' => '16000',
                'wilaya' => '16',
                'nif' => '000016001234567',
                'ai' => '16123456789012',
                'nis' => '000016123456789',
                'trade_register' => '16-123456-B-23',
                'manager_first_name' => 'Ahmed',
                'manager_last_name' => 'Benali',
                'manager_phone_number' => '+213-555-123-456',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. Créer quelques organisations supplémentaires pour les tests
        $additionalOrgs = [
            [
                'name' => 'Entreprise Transport Est',
                'legal_name' => 'SARL Transport Constantine',
                'organization_type' => 'sme',
                'industry' => 'Transport',
                'status' => 'active',
                'city' => 'Constantine',
                'wilaya' => '25',
                'nif' => '000025001111111',
            ],
            [
                'name' => 'Logistique Sud',
                'legal_name' => 'EURL Logistique Ouargla',
                'organization_type' => 'sme',
                'industry' => 'Logistique',
                'status' => 'active',
                'city' => 'Ouargla',
                'wilaya' => '30',
                'nif' => '000030002222222',
            ],
            [
                'name' => 'FleetCorp Oran',
                'legal_name' => 'SPA FleetCorp Ouest',
                'organization_type' => 'enterprise',
                'industry' => 'Transport & Logistique',
                'status' => 'inactive',
                'city' => 'Oran',
                'wilaya' => '31',
                'nif' => '000031003333333',
            ],
        ];

        foreach ($additionalOrgs as $orgData) {
            Organization::firstOrCreate(
                ['name' => $orgData['name']],
                array_merge($orgData, [
                    'phone_number' => '+213-555-' . rand(100000, 999999),
                    'primary_email' => strtolower(str_replace(' ', '.', $orgData['name'])) . '@example.dz',
                    'address' => 'Adresse ' . $orgData['city'],
                    'commune' => 'Centre Ville',
                    'zip_code' => substr($orgData['wilaya'], -2) . '000',
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now(),
                ])
            );
        }

        // 3. S'assurer qu'il y a des utilisateurs actifs pour les comptages
        $organizations = Organization::all();
        foreach ($organizations as $org) {
            // Créer quelques utilisateurs de test pour chaque organisation
            $userCount = rand(3, 8);
            for ($i = 1; $i <= $userCount; $i++) {
                User::firstOrCreate(
                    ['email' => "user{$i}.{$org->id}@{$org->name}.test"],
                    [
                        'name' => "Utilisateur Test {$i}",
                        'first_name' => "User{$i}",
                        'last_name' => "Test",
                        'organization_id' => $org->id,
                        'status' => rand(0, 10) > 2 ? 'active' : 'inactive', // 80% actifs
                        'role' => collect(['admin', 'user', 'supervisor'])->random(),
                        'phone' => '+213-555-' . rand(100000, 999999),
                        'password' => \Hash::make('password'),
                        'email_verified_at' => now(),
                        'created_at' => now()->subDays(rand(1, 60)),
                        'updated_at' => now(),
                    ]
                );
            }

            // Créer quelques véhicules de test
            $vehicleCount = rand(2, 6);
            for ($i = 1; $i <= $vehicleCount; $i++) {
                Vehicle::firstOrCreate(
                    ['registration_plate' => "DZ-{$org->wilaya}-TEST-" . str_pad($org->id . $i, 3, '0', STR_PAD_LEFT)],
                    [
                        'organization_id' => $org->id,
                        'brand' => collect(['Peugeot', 'Renault', 'Ford', 'Hyundai'])->random(),
                        'model' => collect(['308', 'Megane', 'Transit', 'H1'])->random(),
                        'year' => rand(2018, 2024),
                        'current_mileage' => rand(5000, 150000),
                        'fuel_capacity' => rand(40, 80),
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now(),
                    ]
                );
            }

            // Créer quelques chauffeurs de test
            $driverCount = rand(2, 5);
            for ($i = 1; $i <= $driverCount; $i++) {
                Driver::firstOrCreate(
                    ['driver_license_number' => "DL-{$org->wilaya}-" . str_pad($org->id . $i, 6, '0', STR_PAD_LEFT)],
                    [
                        'organization_id' => $org->id,
                        'first_name' => "Chauffeur{$i}",
                        'last_name' => "Test",
                        'personal_phone' => '+213-555-' . rand(100000, 999999),
                        'email' => "chauffeur{$i}.org{$org->id}@test.dz",
                        'driver_license_number' => "DL-{$org->wilaya}-" . str_pad($org->id . $i, 6, '0', STR_PAD_LEFT),
                        'driver_license_expiry' => now()->addYears(rand(1, 5)),
                        'status' => 'active',
                        'created_at' => now()->subDays(rand(1, 45)),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        echo "✅ Données de test créées avec succès:\n";
        echo "   - " . Organization::count() . " organisations\n";
        echo "   - " . User::count() . " utilisateurs\n";
        echo "   - " . Vehicle::count() . " véhicules\n";
        echo "   - " . Driver::count() . " chauffeurs\n";

        // 4. Statistiques détaillées
        foreach (Organization::withCount(['activeUsers', 'vehicles', 'driversModel'])->get() as $org) {
            echo "   📊 {$org->name}: {$org->active_users_count} users, {$org->vehicles_count} vehicles, {$org->drivers_model_count} drivers\n";
        }
    }
}