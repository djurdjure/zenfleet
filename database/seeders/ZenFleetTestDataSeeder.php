<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Assignment;
use App\Models\VehicleType;
use App\Models\VehicleStatus;
use App\Models\FuelType;
use App\Models\TransmissionType;
use App\Models\DriverStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ZenFleetTestDataSeeder extends Seeder
{
    /**
     * 🚀 ZenFleet Test Data Seeder - Données complètes pour tests
     */
    public function run(): void
    {
        $this->command->info('🚀 Création des données de test ZenFleet...');

        // 1. Données de référence
        $this->createReferenceData();

        // 2. Organisation de test
        $organization = $this->createTestOrganization();

        // 3. Utilisateurs de test
        $this->createTestUsers($organization);

        // 4. Véhicules de test
        $this->createTestVehicles($organization);

        // 5. Chauffeurs de test
        $this->createTestDrivers($organization);

        // 6. Affectations de test
        $this->createTestAssignments($organization);

        $this->command->info('✅ Données de test créées avec succès !');
    }

    private function createReferenceData(): void
    {
        $this->command->info('📋 Création des données de référence...');

        // Rôles supplémentaires
        $additionalRoles = ['Supervisor', 'Gestionnaire Flotte'];
        foreach ($additionalRoles as $roleName) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName]);
        }

        // Types de véhicules
        $vehicleTypes = ['Berline', 'SUV', 'Camionnette', 'Camion', 'Autobus'];
        foreach ($vehicleTypes as $type) {
            VehicleType::firstOrCreate(['name' => $type]);
        }

        // Statuts de véhicules
        $vehicleStatuses = ['Disponible', 'Affecté', 'Maintenance', 'Hors service'];
        foreach ($vehicleStatuses as $status) {
            VehicleStatus::firstOrCreate(['name' => $status]);
        }

        // Types de carburant
        $fuelTypes = ['Essence', 'Diesel', 'GPL', 'Électrique', 'Hybride'];
        foreach ($fuelTypes as $fuel) {
            FuelType::firstOrCreate(['name' => $fuel]);
        }

        // Types de transmission
        $transmissionTypes = ['Manuelle', 'Automatique', 'Semi-automatique'];
        foreach ($transmissionTypes as $transmission) {
            TransmissionType::firstOrCreate(['name' => $transmission]);
        }

        // Statuts de chauffeurs
        $driverStatuses = ['Actif', 'En mission', 'En congé', 'Suspendu', 'Inactif'];
        foreach ($driverStatuses as $status) {
            DriverStatus::firstOrCreate(['name' => $status]);
        }
    }

    private function createTestOrganization(): Organization
    {
        return Organization::firstOrCreate(
            ['name' => 'ETRHB Alger'],
            [
                'name' => 'ETRHB Alger',
                'description' => 'Entreprise de Transport Routier Hassan Bey - Siège Alger',
                'address' => '15 Avenue Ahmed Zabana, Hussein Dey, Alger',
                'city' => 'Alger',
                'wilaya' => 'Alger',
                'zip_code' => '16040',
                'primary_phone' => '+213 23 77 85 96',
                'primary_email' => 'contact@etrhb-alger.dz',
                'website' => 'https://etrhb-alger.dz',
                'registration_number' => 'RC-16/040-2025',
                'tax_id' => 'NIF-408529637410',
                'status' => 'active',
            ]
        );
    }

    private function createTestUsers(Organization $organization): void
    {
        $this->command->info('👥 Création des utilisateurs de test...');

        $users = [
            [
                'name' => 'Hassan Benali',
                'first_name' => 'Hassan',
                'last_name' => 'Benali',
                'email' => 'h.benali@etrhb-alger.dz',
                'password' => Hash::make('driver123'),
                'role' => null, // Simple utilisateur pour chauffeur
            ],
            [
                'name' => 'Amina Kaddour',
                'first_name' => 'Amina',
                'last_name' => 'Kaddour',
                'email' => 'a.kaddour@etrhb-alger.dz',
                'password' => Hash::make('driver123'),
                'role' => null,
            ],
            [
                'name' => 'Mohamed Slimani',
                'first_name' => 'Mohamed',
                'last_name' => 'Slimani',
                'email' => 'm.slimani@etrhb-alger.dz',
                'password' => Hash::make('driver123'),
                'role' => null,
            ],
            [
                'name' => 'Supervisor Test',
                'first_name' => 'Supervisor',
                'last_name' => 'Test',
                'email' => 'supervisor@etrhb-alger.dz',
                'password' => Hash::make('supervisor123'),
                'role' => 'Supervisor',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'email' => $userData['email'],
                    'password' => $userData['password'],
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'organization_id' => $organization->id,
                ]
            );

            if ($userData['role']) {
                $user->assignRole($userData['role']);
            }
        }
    }

    private function createTestVehicles(Organization $organization): void
    {
        $this->command->info('🚗 Création des véhicules de test...');

        $vehicles = [
            [
                'registration_plate' => '16-40123-ALG',
                'vin' => 'VF7RDHMZE12345001',
                'brand' => 'Renault',
                'model' => 'Logan',
                'color' => 'Blanc',
                'vehicle_type_id' => VehicleType::where('name', 'Berline')->first()->id,
                'fuel_type_id' => FuelType::where('name', 'Diesel')->first()->id,
                'transmission_type_id' => TransmissionType::where('name', 'Manuelle')->first()->id,
                'status_id' => VehicleStatus::where('name', 'Disponible')->first()->id,
                'manufacturing_year' => 2022,
                'acquisition_date' => '2022-03-15',
                'purchase_price' => 2800000.00,
                'current_value' => 2400000.00,
                'initial_mileage' => 0,
                'current_mileage' => 45000,
                'engine_displacement_cc' => 1461,
                'power_hp' => 90,
                'seats' => 5,
            ],
            [
                'registration_plate' => '16-40124-ALG',
                'vin' => 'VF7RDHMZE12345002',
                'brand' => 'Peugeot',
                'model' => '208',
                'color' => 'Gris',
                'vehicle_type_id' => VehicleType::where('name', 'Berline')->first()->id,
                'fuel_type_id' => FuelType::where('name', 'Essence')->first()->id,
                'transmission_type_id' => TransmissionType::where('name', 'Automatique')->first()->id,
                'status_id' => VehicleStatus::where('name', 'Disponible')->first()->id,
                'manufacturing_year' => 2021,
                'acquisition_date' => '2021-06-20',
                'purchase_price' => 3200000.00,
                'current_value' => 2600000.00,
                'initial_mileage' => 0,
                'current_mileage' => 62000,
                'engine_displacement_cc' => 1199,
                'power_hp' => 75,
                'seats' => 5,
            ],
            [
                'registration_plate' => '16-40125-ALG',
                'vin' => 'VF7RDHMZE12345003',
                'brand' => 'Hyundai',
                'model' => 'H350',
                'color' => 'Blanc',
                'vehicle_type_id' => VehicleType::where('name', 'Camionnette')->first()->id,
                'fuel_type_id' => FuelType::where('name', 'Diesel')->first()->id,
                'transmission_type_id' => TransmissionType::where('name', 'Manuelle')->first()->id,
                'status_id' => VehicleStatus::where('name', 'Disponible')->first()->id,
                'manufacturing_year' => 2023,
                'acquisition_date' => '2023-01-10',
                'purchase_price' => 4500000.00,
                'current_value' => 4200000.00,
                'initial_mileage' => 0,
                'current_mileage' => 28000,
                'engine_displacement_cc' => 2497,
                'power_hp' => 150,
                'seats' => 3,
            ],
            [
                'registration_plate' => '16-40126-ALG',
                'vin' => 'VF7RDHMZE12345004',
                'brand' => 'Toyota',
                'model' => 'Hilux',
                'color' => 'Noir',
                'vehicle_type_id' => VehicleType::where('name', 'SUV')->first()->id,
                'fuel_type_id' => FuelType::where('name', 'Diesel')->first()->id,
                'transmission_type_id' => TransmissionType::where('name', 'Automatique')->first()->id,
                'status_id' => VehicleStatus::where('name', 'Maintenance')->first()->id,
                'manufacturing_year' => 2022,
                'acquisition_date' => '2022-09-05',
                'purchase_price' => 6800000.00,
                'current_value' => 6200000.00,
                'initial_mileage' => 0,
                'current_mileage' => 38000,
                'engine_displacement_cc' => 2755,
                'power_hp' => 204,
                'seats' => 5,
            ],
            [
                'registration_plate' => '16-40127-ALG',
                'vin' => 'VF7RDHMZE12345005',
                'brand' => 'Iveco',
                'model' => 'Daily',
                'color' => 'Blanc',
                'vehicle_type_id' => VehicleType::where('name', 'Camion')->first()->id,
                'fuel_type_id' => FuelType::where('name', 'Diesel')->first()->id,
                'transmission_type_id' => TransmissionType::where('name', 'Manuelle')->first()->id,
                'status_id' => VehicleStatus::where('name', 'Disponible')->first()->id,
                'manufacturing_year' => 2023,
                'acquisition_date' => '2023-04-12',
                'purchase_price' => 8900000.00,
                'current_value' => 8400000.00,
                'initial_mileage' => 0,
                'current_mileage' => 15000,
                'engine_displacement_cc' => 2998,
                'power_hp' => 180,
                'seats' => 3,
            ],
        ];

        foreach ($vehicles as $vehicleData) {
            Vehicle::firstOrCreate(
                ['registration_plate' => $vehicleData['registration_plate']],
                array_merge($vehicleData, ['organization_id' => $organization->id])
            );
        }
    }

    private function createTestDrivers(Organization $organization): void
    {
        $this->command->info('🚛 Création des chauffeurs de test...');

        $driverUsers = User::where('organization_id', $organization->id)
            ->whereDoesntHave('roles')
            ->get();

        $drivers = [
            [
                'employee_number' => 'CH001',
                'first_name' => 'Hassan',
                'last_name' => 'Benali',
                'birth_date' => '1985-03-15',
                'blood_type' => 'O+',
                'address' => '25 Rue Mohamed Belouizdad, Alger',
                'personal_phone' => '+213 555 123 456',
                'personal_email' => 'hassan.benali@gmail.com',
                'license_number' => 'LIC-ALG-85031501',
                'license_category' => 'B+C',
                'license_issue_date' => '2005-06-20',
                'license_authority' => 'Wilaya d\'Alger',
                'license_expiry_date' => '2025-06-20',
                'recruitment_date' => '2020-01-15',
                'status_id' => DriverStatus::where('name', 'Actif')->first()->id,
                'emergency_contact_name' => 'Fatima Benali',
                'emergency_contact_phone' => '+213 555 987 654',
            ],
            [
                'employee_number' => 'CH002',
                'first_name' => 'Amina',
                'last_name' => 'Kaddour',
                'birth_date' => '1990-07-22',
                'blood_type' => 'A+',
                'address' => '12 Avenue ALN, Bab Ezzouar, Alger',
                'personal_phone' => '+213 555 234 567',
                'personal_email' => 'amina.kaddour@gmail.com',
                'license_number' => 'LIC-ALG-90072201',
                'license_category' => 'B',
                'license_issue_date' => '2010-08-15',
                'license_authority' => 'Wilaya d\'Alger',
                'license_expiry_date' => '2025-08-15',
                'recruitment_date' => '2021-03-10',
                'status_id' => DriverStatus::where('name', 'En mission')->first()->id,
                'emergency_contact_name' => 'Omar Kaddour',
                'emergency_contact_phone' => '+213 555 876 543',
            ],
            [
                'employee_number' => 'CH003',
                'first_name' => 'Mohamed',
                'last_name' => 'Slimani',
                'birth_date' => '1982-11-08',
                'blood_type' => 'B+',
                'address' => '8 Cité Nasr, Kouba, Alger',
                'personal_phone' => '+213 555 345 678',
                'personal_email' => 'mohamed.slimani@gmail.com',
                'license_number' => 'LIC-ALG-82110801',
                'license_category' => 'B+C+D',
                'license_issue_date' => '2002-12-10',
                'license_authority' => 'Wilaya d\'Alger',
                'license_expiry_date' => '2025-12-10',
                'recruitment_date' => '2018-09-05',
                'status_id' => DriverStatus::where('name', 'Actif')->first()->id,
                'emergency_contact_name' => 'Khadija Slimani',
                'emergency_contact_phone' => '+213 555 765 432',
            ],
        ];

        foreach ($drivers as $index => $driverData) {
            if (isset($driverUsers[$index])) {
                $driver = Driver::firstOrCreate(
                    ['employee_number' => $driverData['employee_number']],
                    array_merge($driverData, [
                        'user_id' => $driverUsers[$index]->id,
                        'organization_id' => $organization->id,
                    ])
                );

                // Note: La liaison sera faite via la relation belongsTo dans le modèle Driver
            }
        }
    }

    private function createTestAssignments(Organization $organization): void
    {
        $this->command->info('📋 Création des affectations de test...');

        $availableVehicles = Vehicle::where('organization_id', $organization->id)->get();
        $activeDrivers = Driver::where('organization_id', $organization->id)->get();

        if ($availableVehicles->count() > 0 && $activeDrivers->count() > 0) {
            // Affectation en cours
            Assignment::firstOrCreate([
                'vehicle_id' => $availableVehicles->first()->id,
                'driver_id' => $activeDrivers->first()->id,
                'start_datetime' => Carbon::now()->subDays(2),
                'reason' => 'Mission de transport Alger - Oran',
                'notes' => 'Transport de matériel informatique - Priorité haute',
                'organization_id' => $organization->id,
            ]);

            // Affectation terminée (pour historique)
            if ($availableVehicles->count() > 1 && $activeDrivers->count() > 1) {
                Assignment::firstOrCreate([
                    'vehicle_id' => $availableVehicles->get(1)->id,
                    'driver_id' => $activeDrivers->get(1)->id,
                    'start_datetime' => Carbon::now()->subDays(7),
                    'end_datetime' => Carbon::now()->subDays(5),
                    'reason' => 'Livraison matériel',
                    'notes' => 'Mission terminée avec succès',
                    'organization_id' => $organization->id,
                ]);
            }
        }
    }
}