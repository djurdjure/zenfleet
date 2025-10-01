<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\VehicleType;
use App\Models\VehicleStatus;
use App\Models\Organization;

class AssignmentTestDataSeeder extends Seeder
{
    /**
     * Crée des données de test pour les affectations
     */
    public function run(): void
    {
        echo "🚀 Création des données de test pour les affectations...\n";

        $orgId = Organization::first()->id;

        // 1. Créer quelques types de véhicules si nécessaire
        $vehicleTypes = [
            ['name' => 'Berline', 'slug' => 'berline', 'organization_id' => $orgId],
            ['name' => 'Utilitaire', 'slug' => 'utilitaire', 'organization_id' => $orgId],
            ['name' => 'Camionnette', 'slug' => 'camionnette', 'organization_id' => $orgId],
        ];

        foreach ($vehicleTypes as $typeData) {
            VehicleType::firstOrCreate(
                ['slug' => $typeData['slug'], 'organization_id' => $orgId],
                $typeData
            );
        }

        // 2. Créer des statuts de véhicules si nécessaire
        $vehicleStatuses = [
            ['name' => 'Disponible', 'slug' => 'disponible', 'organization_id' => $orgId],
            ['name' => 'En maintenance', 'slug' => 'maintenance', 'organization_id' => $orgId],
            ['name' => 'Hors service', 'slug' => 'hors-service', 'organization_id' => $orgId],
        ];

        foreach ($vehicleStatuses as $statusData) {
            VehicleStatus::firstOrCreate(
                ['slug' => $statusData['slug'], 'organization_id' => $orgId],
                $statusData
            );
        }

        // 3. Créer des véhicules de test
        $availableStatusId = VehicleStatus::where('name', 'ILIKE', '%disponible%')->first()->id ?? null;
        $berlineTypeId = VehicleType::where('name', 'ILIKE', '%berline%')->first()->id ?? null;
        $utilitaireTypeId = VehicleType::where('name', 'ILIKE', '%utilitaire%')->first()->id ?? null;

        $testVehicles = [
            [
                'organization_id' => $orgId,
                'vehicle_type_id' => $berlineTypeId,
                'vehicle_status_id' => $availableStatusId,
                'registration_plate' => 'DZ-16-TEST-001',
                'brand' => 'Peugeot',
                'model' => '308',
                'year' => 2022,
                'current_mileage' => 15000,
                'fuel_capacity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'organization_id' => $orgId,
                'vehicle_type_id' => $berlineTypeId,
                'vehicle_status_id' => $availableStatusId,
                'registration_plate' => 'DZ-16-TEST-002',
                'brand' => 'Renault',
                'model' => 'Megane',
                'year' => 2021,
                'current_mileage' => 22000,
                'fuel_capacity' => 45,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'organization_id' => $orgId,
                'vehicle_type_id' => $utilitaireTypeId,
                'vehicle_status_id' => $availableStatusId,
                'registration_plate' => 'DZ-16-TEST-003',
                'brand' => 'Ford',
                'model' => 'Transit',
                'year' => 2023,
                'current_mileage' => 8000,
                'fuel_capacity' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($testVehicles as $vehicleData) {
            Vehicle::firstOrCreate(
                ['registration_plate' => $vehicleData['registration_plate']],
                $vehicleData
            );
        }

        // 4. Créer des chauffeurs de test
        $testDrivers = [
            [
                'organization_id' => $orgId,
                'first_name' => 'Ahmed',
                'last_name' => 'Bencherif',
                'personal_phone' => '0555123456',
                'email' => 'ahmed.bencherif@test.dz',
                'driver_license_number' => 'DL-16-001-2024',
                'driver_license_expiry' => now()->addYears(5),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'organization_id' => $orgId,
                'first_name' => 'Youcef',
                'last_name' => 'Meradji',
                'personal_phone' => '0555789123',
                'email' => 'youcef.meradji@test.dz',
                'driver_license_number' => 'DL-16-002-2024',
                'driver_license_expiry' => now()->addYears(3),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'organization_id' => $orgId,
                'first_name' => 'Fatima',
                'last_name' => 'Benaissa',
                'personal_phone' => '0555456789',
                'email' => 'fatima.benaissa@test.dz',
                'driver_license_number' => 'DL-16-003-2024',
                'driver_license_expiry' => now()->addYears(4),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($testDrivers as $driverData) {
            Driver::firstOrCreate(
                ['driver_license_number' => $driverData['driver_license_number']],
                $driverData
            );
        }

        echo "✅ Données de test créées avec succès:\n";
        echo "   - " . count($testVehicles) . " véhicules de test\n";
        echo "   - " . count($testDrivers) . " chauffeurs de test\n";
        echo "   - Types et statuts véhicules configurés\n";

        $availableVehicles = Vehicle::where('organization_id', $orgId)->count();
        $availableDrivers = Driver::where('organization_id', $orgId)->count();

        echo "📊 Total ressources disponibles:\n";
        echo "   - {$availableVehicles} véhicules\n";
        echo "   - {$availableDrivers} chauffeurs\n";
    }
}