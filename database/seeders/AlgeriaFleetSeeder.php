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

            $vehicleData = [
                'organization_id' => $organization->id,
                'brand' => $brand,
                'model' => $this->getModelForBrand($brand),
                'year' => $year,
                'license_plate' => $this->generateLicensePlate($orgWilaya),
                'vin' => strtoupper(fake()->bothify('VF1#####?#?######')), // French VIN format common in Algeria
                'color' => fake()->randomElement(['Blanc', 'Gris', 'Noir', 'Bleu', 'Rouge', 'Vert']),
                'fuel_type' => fake()->randomElement(['Essence', 'Diesel', 'GPL']),
                'transmission' => fake()->randomElement(['Manuelle', 'Automatique']),
                'seats' => fake()->randomElement([2, 5, 7, 9, 12, 20, 45]), // Various vehicle types
                'mileage' => fake()->numberBetween(5000, 300000),
                'purchase_date' => fake()->dateTimeBetween('-' . (2024 - $year) . ' years', 'now'),
                'purchase_price' => fake()->numberBetween(800000, 5000000), // DZD prices
                'status' => fake()->randomElement(['active', 'maintenance', 'inactive']),
                'insurance_company' => fake()->randomElement(['SAA', 'CAAR', 'GAM', 'Alliance', 'TRUST']),
                'insurance_policy' => fake()->numerify('POL-########'),
                'insurance_expires_at' => fake()->dateTimeBetween('now', '+2 years'),
                'technical_control_expires_at' => fake()->dateTimeBetween('now', '+1 year'),
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

            // Create driver profile
            $driverData = [
                'user_id' => $user->id,
                'organization_id' => $organization->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'date_of_birth' => $birthDate,
                'place_of_birth' => fake()->randomElement(['Alger', 'Oran', 'Constantine', 'Sétif', 'Blida', 'Annaba']),
                'address' => fake()->streetAddress() . ', ' . $organization->city,
                'phone' => '+213 ' . fake()->numerify('## ## ## ## ##'),
                'emergency_contact' => fake()->name(),
                'emergency_phone' => '+213 ' . fake()->numerify('## ## ## ## ##'),
                'license_number' => $this->generateLicenseNumber($organization->wilaya),
                'license_category' => fake()->randomElement(['B', 'C', 'D', 'C+E', 'D+E']),
                'license_issued_date' => fake()->dateTimeBetween($birthDate->format('Y-m-d') . ' +3 years', $hireDate),
                'license_expires_date' => fake()->dateTimeBetween('now', '+10 years'),
                'hire_date' => $hireDate,
                'status' => fake()->randomElement(['active', 'on_leave', 'suspended']),
                'salary' => fake()->numberBetween(30000, 80000), // DZD monthly salary
                'notes' => fake()->optional(0.2)->sentence(),
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