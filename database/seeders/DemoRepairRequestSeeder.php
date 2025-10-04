<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Organization;
use App\Models\RepairRequest;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\VehicleDepot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * DemoRepairRequestSeeder - Seed demo repair requests
 *
 * Creates:
 * - 3 demo users (supervisor, fleet manager, driver)
 * - 5 repair requests with different statuses
 *
 * @version 1.0-Enterprise
 */
class DemoRepairRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Seeding demo repair requests...');

        // Get or create organization
        $organization = Organization::first();
        if (!$organization) {
            $this->command->warn('  ⚠️  No organization found. Creating demo organization.');
            $organization = Organization::factory()->create([
                'name' => 'Demo Organization',
            ]);
        }

        // Ensure roles exist
        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);
        $fleetRole = Role::firstOrCreate(['name' => 'Gestionnaire Flotte', 'guard_name' => 'web']);
        $driverRole = Role::firstOrCreate(['name' => 'Chauffeur', 'guard_name' => 'web']);

        // Create demo users
        $this->command->info('  👥 Creating demo users...');

        $supervisorUser = User::firstOrCreate(
            ['email' => 'supervisor@zenfleet.dz'],
            [
                'name' => 'Supervisor Demo',
                'email' => 'supervisor@zenfleet.dz',
                'password' => Hash::make('password'),
                'organization_id' => $organization->id,
                'email_verified_at' => now(),
            ]
        );
        $supervisorUser->syncRoles([$supervisorRole]);
        $this->command->info('    ✓ Supervisor: supervisor@zenfleet.dz');

        $fleetUser = User::firstOrCreate(
            ['email' => 'fleet@zenfleet.dz'],
            [
                'name' => 'Fleet Manager Demo',
                'email' => 'fleet@zenfleet.dz',
                'password' => Hash::make('password'),
                'organization_id' => $organization->id,
                'email_verified_at' => now(),
            ]
        );
        $fleetUser->syncRoles([$fleetRole]);
        $this->command->info('    ✓ Fleet Manager: fleet@zenfleet.dz');

        $driverUser = User::firstOrCreate(
            ['email' => 'driver@zenfleet.dz'],
            [
                'name' => 'Driver Demo',
                'email' => 'driver@zenfleet.dz',
                'password' => Hash::make('password'),
                'organization_id' => $organization->id,
                'email_verified_at' => now(),
            ]
        );
        $driverUser->syncRoles([$driverRole]);
        $this->command->info('    ✓ Driver: driver@zenfleet.dz');

        // Create driver entity
        $driver = Driver::firstOrCreate(
            [
                'user_id' => $driverUser->id,
                'organization_id' => $organization->id,
            ],
            [
                'license_number' => 'DZ123456789',
                'license_category' => 'B',
                'driver_license_expiry_date' => now()->addYears(5),
                'personal_phone' => '0661234567',
                'address' => 'Alger, Algérie',
                'supervisor_id' => $supervisorUser->id,
                'first_name' => 'Driver',
                'last_name' => 'Demo',
            ]
        );

        // Get or create vehicle
        $vehicle = Vehicle::where('organization_id', $organization->id)->first();
        if (!$vehicle) {
            $category = VehicleCategory::where('organization_id', $organization->id)->first();
            $depot = VehicleDepot::where('organization_id', $organization->id)->first();

            $vehicle = Vehicle::create([
                'organization_id' => $organization->id,
                'license_plate' => '16-123-45',
                'vehicle_name' => 'Véhicule Demo',
                'brand' => 'Renault',
                'model' => 'Kangoo',
                'year' => 2022,
                'fuel_type' => 'diesel',
                'category_id' => $category?->id,
                'depot_id' => $depot?->id,
            ]);
        }

        // Create repair requests with different statuses
        $this->command->info('  🔧 Creating demo repair requests...');

        $repairRequests = [
            [
                'title' => 'Problème de freinage urgent',
                'description' => 'Les freins arrière font un bruit anormal et la pédale est molle. Cela nécessite une inspection immédiate pour la sécurité.',
                'urgency' => 'critical',
                'status' => RepairRequest::STATUS_PENDING_SUPERVISOR,
                'current_mileage' => 45000,
                'current_location' => 'Alger Centre',
                'estimated_cost' => 15000,
            ],
            [
                'title' => 'Vidange programmée',
                'description' => 'Vidange d\'huile moteur à effectuer selon le calendrier d\'entretien. Le véhicule a atteint le kilométrage requis.',
                'urgency' => 'normal',
                'status' => RepairRequest::STATUS_PENDING_FLEET_MANAGER,
                'current_mileage' => 50000,
                'current_location' => 'Dépôt Alger',
                'estimated_cost' => 5000,
                'supervisor_id' => $supervisorUser->id,
                'supervisor_status' => 'approved',
                'supervisor_approved_at' => now()->subDays(1),
                'supervisor_comment' => 'Entretien nécessaire, approuvé pour maintenance',
            ],
            [
                'title' => 'Remplacement pneus avant',
                'description' => 'Les deux pneus avant sont usés et doivent être remplacés avant la prochaine inspection technique.',
                'urgency' => 'high',
                'status' => RepairRequest::STATUS_APPROVED_FINAL,
                'current_mileage' => 48000,
                'current_location' => 'Oran',
                'estimated_cost' => 25000,
                'supervisor_id' => $supervisorUser->id,
                'supervisor_status' => 'approved',
                'supervisor_approved_at' => now()->subDays(3),
                'supervisor_comment' => 'Approuvé, sécurité prioritaire',
                'fleet_manager_id' => $fleetUser->id,
                'fleet_manager_status' => 'approved',
                'fleet_manager_approved_at' => now()->subDays(2),
                'fleet_manager_comment' => 'Budget approuvé, planifier intervention',
                'final_approved_by' => $fleetUser->id,
                'final_approved_at' => now()->subDays(2),
            ],
            [
                'title' => 'Rétroviseur cassé',
                'description' => 'Le rétroviseur droit est cassé suite à un accrochage mineur.',
                'urgency' => 'low',
                'status' => RepairRequest::STATUS_REJECTED_SUPERVISOR,
                'current_mileage' => 42000,
                'current_location' => 'Constantine',
                'estimated_cost' => 3000,
                'supervisor_id' => $supervisorUser->id,
                'supervisor_status' => 'rejected',
                'supervisor_comment' => 'Réparation cosmétique non prioritaire. Reporter à la prochaine maintenance.',
                'rejection_reason' => 'Réparation cosmétique non prioritaire. Le rétroviseur reste fonctionnel malgré la fissure.',
                'rejected_by' => $supervisorUser->id,
                'rejected_at' => now()->subDay(),
            ],
            [
                'title' => 'Climatisation en panne',
                'description' => 'Le système de climatisation ne fonctionne plus depuis une semaine.',
                'urgency' => 'normal',
                'status' => RepairRequest::STATUS_REJECTED_FINAL,
                'current_mileage' => 46000,
                'current_location' => 'Blida',
                'estimated_cost' => 35000,
                'supervisor_id' => $supervisorUser->id,
                'supervisor_status' => 'approved',
                'supervisor_approved_at' => now()->subDays(4),
                'supervisor_comment' => 'Confort important pour le chauffeur',
                'fleet_manager_id' => $fleetUser->id,
                'fleet_manager_status' => 'rejected',
                'fleet_manager_comment' => 'Budget insuffisant ce trimestre',
                'rejection_reason' => 'Budget trimestriel dépassé. Réparation reportée au prochain trimestre. Utilisation du véhicule reste possible.',
                'rejected_by' => $fleetUser->id,
                'rejected_at' => now()->subDays(3),
            ],
        ];

        $created = 0;
        foreach ($repairRequests as $requestData) {
            $existing = RepairRequest::where('organization_id', $organization->id)
                ->where('title', $requestData['title'])
                ->first();

            if (!$existing) {
                RepairRequest::create(array_merge($requestData, [
                    'uuid' => Str::uuid()->toString(),
                    'organization_id' => $organization->id,
                    'driver_id' => $driver->id,
                    'vehicle_id' => $vehicle->id,
                ]));

                $created++;
                $this->command->info("    ✓ Created: {$requestData['title']} ({$requestData['status']})");
            } else {
                $this->command->info("    - Skipped: {$requestData['title']} (already exists)");
            }
        }

        $this->command->info('');
        $this->command->info("✅ Demo repair requests seeded successfully! Total created: {$created}");
        $this->command->info('');
        $this->command->info('📧 Demo credentials:');
        $this->command->info('  • supervisor@zenfleet.dz / password');
        $this->command->info('  • fleet@zenfleet.dz / password');
        $this->command->info('  • driver@zenfleet.dz / password');
    }
}
