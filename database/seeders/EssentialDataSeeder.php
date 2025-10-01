<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EssentialDataSeeder extends Seeder
{
    /**
     * Run the database seeds - Enterprise-grade Essential Data
     */
    public function run(): void
    {
        echo "🚀 Seeding Essential Data for ZenFleet Enterprise...\n";

        // 1. Vehicle Types
        $vehicleTypes = [
            ['name' => 'Berline', 'description' => 'Véhicule de tourisme 4 portes'],
            ['name' => 'Break', 'description' => 'Véhicule familial avec coffre étendu'],
            ['name' => 'SUV', 'description' => 'Sport Utility Vehicle'],
            ['name' => 'Utilitaire', 'description' => 'Véhicule de transport de marchandises'],
            ['name' => 'Camionnette', 'description' => 'Petit véhicule commercial'],
            ['name' => 'Camion', 'description' => 'Véhicule lourd de transport'],
            ['name' => 'Bus', 'description' => 'Transport de personnes'],
            ['name' => 'Moto', 'description' => 'Deux roues motorisé']
        ];

        foreach ($vehicleTypes as $type) {
            \DB::table('vehicle_types')->updateOrInsert(
                ['name' => $type['name']],
                array_merge($type, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
        echo "✅ " . count($vehicleTypes) . " types de véhicules créés\n";

        // 2. Vehicle Statuses
        $vehicleStatuses = [
            ['name' => 'Disponible', 'color_code' => '#10b981'],
            ['name' => 'En service', 'color_code' => '#3b82f6'],
            ['name' => 'Maintenance', 'color_code' => '#f59e0b'],
            ['name' => 'Réparation', 'color_code' => '#ef4444'],
            ['name' => 'Accident', 'color_code' => '#dc2626'],
            ['name' => 'Hors service', 'color_code' => '#6b7280'],
            ['name' => 'Vendu', 'color_code' => '#8b5cf6']
        ];

        foreach ($vehicleStatuses as $status) {
            \DB::table('vehicle_statuses')->updateOrInsert(
                ['name' => $status['name']],
                array_merge($status, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
        echo "✅ " . count($vehicleStatuses) . " statuts de véhicules créés\n";

        // 3. Fuel Types
        $fuelTypes = [
            ['name' => 'Essence', 'description' => 'Carburant essence sans plomb'],
            ['name' => 'Diesel', 'description' => 'Carburant diesel/gasoil'],
            ['name' => 'GPL', 'description' => 'Gaz de Pétrole Liquéfié'],
            ['name' => 'GNC', 'description' => 'Gaz Naturel Comprimé'],
            ['name' => 'Électrique', 'description' => 'Véhicule 100% électrique'],
            ['name' => 'Hybride', 'description' => 'Moteur hybride essence-électrique'],
            ['name' => 'Hybride Diesel', 'description' => 'Moteur hybride diesel-électrique']
        ];

        foreach ($fuelTypes as $fuel) {
            \DB::table('fuel_types')->updateOrInsert(
                ['name' => $fuel['name']],
                array_merge($fuel, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
        echo "✅ " . count($fuelTypes) . " types de carburant créés\n";

        // 4. Transmission Types
        $transmissionTypes = [
            ['name' => 'Manuelle', 'description' => 'Boîte de vitesses manuelle'],
            ['name' => 'Automatique', 'description' => 'Boîte de vitesses automatique'],
            ['name' => 'Semi-automatique', 'description' => 'Boîte semi-automatique/robotisée'],
            ['name' => 'CVT', 'description' => 'Transmission à variation continue']
        ];

        foreach ($transmissionTypes as $transmission) {
            \DB::table('transmission_types')->updateOrInsert(
                ['name' => $transmission['name']],
                array_merge($transmission, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
        echo "✅ " . count($transmissionTypes) . " types de transmission créés\n";

        // 5. Données de test pour développement
        if (app()->environment(['local', 'staging', 'development'])) {
            $this->seedTestData();
        }

        echo "🎉 Essential Data Seeding completed - Enterprise ready!\n";
    }

    /**
     * Seed test data for development
     */
    private function seedTestData(): void
    {
        echo "\n🧪 Seeding Test Data for Development...\n";

        // Vérifier qu'il y a au moins une organisation
        $orgId = \DB::table('organizations')->first()->id ?? 1;

        // Véhicules de test
        $testVehicles = [
            [
                'organization_id' => $orgId,
                'registration_plate' => 'AB-123-CD',
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'color' => 'Blanc',
                'vehicle_type_id' => 1, // Berline
                'fuel_type_id' => 1, // Essence
                'transmission_type_id' => 1, // Manuelle
                'status_id' => 1, // Disponible
                'current_mileage' => 50000,
                'manufacturing_year' => 2020
            ],
            [
                'organization_id' => $orgId,
                'registration_plate' => 'IJ-789-KL',
                'brand' => 'Renault',
                'model' => 'Clio',
                'color' => 'Rouge',
                'vehicle_type_id' => 1, // Berline
                'fuel_type_id' => 2, // Diesel
                'transmission_type_id' => 1, // Manuelle
                'status_id' => 1, // Disponible
                'current_mileage' => 75000,
                'manufacturing_year' => 2019
            ],
            [
                'organization_id' => $orgId,
                'registration_plate' => 'EF-456-GH',
                'brand' => 'Peugeot',
                'model' => '308 HDI',
                'color' => 'Bleu',
                'vehicle_type_id' => 1, // Berline
                'fuel_type_id' => 2, // Diesel
                'transmission_type_id' => 1, // Manuelle
                'status_id' => 2, // En service
                'current_mileage' => 30000,
                'manufacturing_year' => 2021
            ]
        ];

        foreach ($testVehicles as $vehicle) {
            \DB::table('vehicles')->updateOrInsert(
                ['registration_plate' => $vehicle['registration_plate']],
                array_merge($vehicle, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
        echo "✅ " . count($testVehicles) . " véhicules de test créés\n";

        // Chauffeurs de test
        $testDrivers = [
            [
                'organization_id' => $orgId,
                'first_name' => 'Ahmed',
                'last_name' => 'Benali',
                'personal_phone' => '0555123456',
                'driver_license_number' => 'DL001234567',
                'driver_license_expiry_date' => now()->addYears(2)->format('Y-m-d')
            ],
            [
                'organization_id' => $orgId,
                'first_name' => 'Youcef',
                'last_name' => 'Slimani',
                'personal_phone' => '0666789012',
                'driver_license_number' => 'DL987654321',
                'driver_license_expiry_date' => now()->addYears(3)->format('Y-m-d')
            ]
        ];

        foreach ($testDrivers as $driver) {
            \DB::table('drivers')->updateOrInsert(
                ['driver_license_number' => $driver['driver_license_number']],
                array_merge($driver, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
        echo "✅ " . count($testDrivers) . " chauffeurs de test créés\n";

        echo "🧪 Test Data Seeding completed!\n";
    }
}
