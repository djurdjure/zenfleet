<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AlgeriaFleetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates realistic Algeria-centric vehicles and drivers for each organization
     */
    public function run(): void
    {
        $organizations = Organization::where('status', 'active')->get();

        if ($organizations->isEmpty()) {
            $this->command->warn('No active organizations found. Run AlgeriaOrganizationSeeder first.');
            return;
        }

        foreach ($organizations as $organization) {
            $this->createOrganizationFleet($organization);
        }

        $this->command->info('✅ Algeria fleet data seeded successfully');
    }

    /**
     * Create vehicles and drivers for a specific organization
     */
    private function createOrganizationFleet(Organization $organization): void
    {
        // Determine fleet size based on organization type
        $fleetSize = match ($organization->organization_type) {
            'enterprise' => fake()->numberBetween(15, 30),
            'sme' => fake()->numberBetween(5, 15),
            'startup' => fake()->numberBetween(2, 8),
            'public' => fake()->numberBetween(10, 25),
            default => fake()->numberBetween(3, 10)
        };

        // Create vehicles with Algeria-specific data
        $vehicles = $this->createVehicles($organization, $fleetSize);

        // Create drivers for the organization
        $drivers = $this->createDrivers($organization, $fleetSize);

        $this->command->info("✅ Created {$vehicles->count()} vehicles and {$drivers->count()} drivers for {$organization->name}");
    }

    /**
     * Create vehicles with realistic Algeria data
     */
    private function createVehicles(Organization $organization, int $count)
    {
        $vehicles = collect();

        // Common vehicle brands in Algeria
        $brands = ['Renault', 'Peugeot', 'Hyundai', 'Toyota', 'Volkswagen', 'Ford', 'Isuzu', 'Mercedes', 'Iveco'];

        // Algeria license plate patterns by wilaya
        $platePatterns = [
            '16' => ['161234-16', '162345-16', '163456-16'], // Alger
            '31' => ['311234-31', '312345-31', '313456-31'], // Oran
            '19' => ['191234-19', '192345-19', '193456-19'], // Sétif
            '25' => ['251234-25', '252345-25', '253456-25'], // Constantine
        ];

        $orgWilaya = $organization->wilaya ?? '16';
        $patterns = $platePatterns[$orgWilaya] ?? $platePatterns['16'];

        for ($i = 0; $i < $count; $i++) {
            $brand = fake()->randomElement($brands);
            $year = fake()->numberBetween(2010, 2024);

            $mileage = fake()->numberBetween(5000, 300000);

            // Obtenir ou créer les IDs nécessaires
            $fuelTypeId = \DB::table('fuel_types')->value('id') ?? 1;
            $transmissionTypeId = \DB::table('transmission_types')->value('id') ?? 1;
            $vehicleTypeId = \DB::table('vehicle_types')->value('id') ?? 1;
            $statusId = \DB::table('vehicle_statuses')->value('id') ?? 1;

            $vehicleData = [
                'organization_id' => $organization->id,
                'brand' => $brand,
                'model' => $this->getModelForBrand($brand),
                'manufacturing_year' => $year, // Corrigé: year → manufacturing_year
                'registration_plate' => $this->generateLicensePlate($orgWilaya), // Corrigé: license_plate → registration_plate
                'vin' => strtoupper(fake()->bothify('VF1#####?#?######')), // French VIN format common in Algeria
                'color' => fake()->randomElement(['Blanc', 'Gris', 'Noir', 'Bleu', 'Rouge', 'Vert']),
                'fuel_type_id' => $fuelTypeId, // Corrigé: fuel_type → fuel_type_id
                'transmission_type_id' => $transmissionTypeId, // Corrigé: transmission → transmission_type_id
                'vehicle_type_id' => $vehicleTypeId, // Ajouté
                'status_id' => $statusId, // Corrigé: status → status_id
                'seats' => fake()->randomElement([2, 5, 7, 9, 12, 20, 45]), // Various vehicle types
                'initial_mileage' => $mileage, // Ajouté
                'current_mileage' => $mileage, // Corrigé: mileage → current_mileage
                'acquisition_date' => fake()->dateTimeBetween('-' . (2024 - $year) . ' years', 'now'), // Corrigé: purchase_date → acquisition_date
                'purchase_price' => fake()->numberBetween(800000, 5000000), // DZD prices
                'notes' => fake()->optional(0.3)->sentence(),
                'created_at' => now()->subDays(fake()->numberBetween(1, 365)),
                'updated_at' => now()->subDays(fake()->numberBetween(1, 30)),
            ];

            $vehicle = Vehicle::create($vehicleData);
            $vehicles->push($vehicle);
        }

        return $vehicles;
    }

    /**
     * Create drivers for the organization
     */
    private function createDrivers(Organization $organization, int $count)
    {
        $drivers = collect();

        // Typical Algerian first names
        $algerianFirstNames = [
            'Ahmed', 'Mohamed', 'Ali', 'Omar', 'Karim', 'Yacine', 'Amine', 'Sofiane', 'Bilal', 'Mehdi',
            'Fatima', 'Aicha', 'Khadija', 'Amina', 'Soraya', 'Nadia', 'Samira', 'Leila', 'Asma', 'Meriem'
        ];

        // Typical Algerian family names
        $algerianLastNames = [
            'Benali', 'Belabes', 'Benaissa', 'Meziani', 'Boumediene', 'Hamidi', 'Khelifa', 'Zeroual',
            'Belkacem', 'Benabdellah', 'Bensalem', 'Cherif', 'Djelloul', 'Fergani', 'Guerrouche'
        ];

        for ($i = 0; $i < $count; $i++) {
            $firstName = fake()->randomElement($algerianFirstNames);
            $lastName = fake()->randomElement($algerianLastNames);
            $birthDate = fake()->dateTimeBetween('-65 years', '-21 years');
            $hireDate = fake()->dateTimeBetween('-5 years', 'now');

            // Create driver user account
            $user = User::create([
                'name' => "{$firstName} {$lastName}",
                'email' => strtolower($firstName . '.' . $lastName . '@' . str_replace(' ', '', $organization->name) . '.local'),
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'organization_id' => $organization->id,
                'created_at' => $hireDate,
                'updated_at' => now(),
            ]);

            // Obtenir un status_id valide pour les chauffeurs
            $driverStatusId = \DB::table('driver_statuses')->where('is_active', true)->value('id') ?? 1;

            // Create driver profile
            $licenseIssueDate = fake()->dateTimeBetween($birthDate->format('Y-m-d') . ' +3 years', $hireDate);

            $driverData = [
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'birth_date' => $birthDate, // Corrigé: date_of_birth → birth_date
                'address' => fake()->streetAddress() . ', ' . ($organization->city ?? 'Alger'),
                'personal_phone' => '+213 ' . fake()->numerify('## ## ## ## ##'), // Corrigé: phone → personal_phone
                'emergency_contact_name' => fake()->name(), // Corrigé: emergency_contact → emergency_contact_name
                'emergency_contact_phone' => '+213 ' . fake()->numerify('## ## ## ## ##'), // Corrigé: emergency_phone → emergency_contact_phone
                'license_number' => $this->generateLicenseNumber($organization->wilaya ?? '16'),
                'license_category' => fake()->randomElement(['B', 'C', 'D', 'C+E', 'D+E']),
                'license_issue_date' => $licenseIssueDate, // Corrigé: license_issued_date → license_issue_date
                'license_expiry_date' => fake()->dateTimeBetween('now', '+10 years'), // Ajouté
                'license_authority' => 'Wilaya de ' . ($organization->city ?? 'Alger'), // Ajouté
                'recruitment_date' => $hireDate, // Corrigé: hire_date → recruitment_date
                'status_id' => $driverStatusId, // Corrigé: status → status_id
                'created_at' => $hireDate,
                'updated_at' => now(),
            ];

            $driver = Driver::create($driverData);
            $drivers->push($driver);
        }

        return $drivers;
    }

    /**
     * Get realistic model names for each brand
     */
    private function getModelForBrand(string $brand): string
    {
        $models = [
            'Renault' => ['Clio', 'Symbol', 'Logan', 'Sandero', 'Duster', 'Trafic', 'Master'],
            'Peugeot' => ['206', '207', '208', '301', '308', 'Partner', 'Boxer'],
            'Hyundai' => ['i10', 'i20', 'Accent', 'Elantra', 'Tucson', 'H100'],
            'Toyota' => ['Yaris', 'Corolla', 'Auris', 'RAV4', 'Hilux', 'Hiace'],
            'Volkswagen' => ['Polo', 'Golf', 'Jetta', 'Caddy', 'Crafter'],
            'Ford' => ['Fiesta', 'Focus', 'Escort', 'Transit', 'Ranger'],
            'Isuzu' => ['D-Max', 'NPR', 'NQR', 'FTR'],
            'Mercedes' => ['Sprinter', 'Vito', 'A-Class', 'C-Class'],
            'Iveco' => ['Daily', 'Eurocargo', 'Stralis']
        ];

        return fake()->randomElement($models[$brand] ?? ['Generic Model']);
    }

    /**
     * Generate realistic Algeria license plate
     */
    private function generateLicensePlate(string $wilaya): string
    {
        $series = fake()->numberBetween(100000, 999999);
        return "{$series}-{$wilaya}";
    }

    /**
     * Generate realistic Algeria driving license number
     */
    private function generateLicenseNumber(string $wilaya): string
    {
        $year = fake()->numberBetween(00, 24);
        $series = fake()->numberBetween(100000, 999999);
        return "{$wilaya}{$year}{$series}";
    }
}