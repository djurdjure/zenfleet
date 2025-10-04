<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\VehicleDepot;
use Illuminate\Database\Seeder;

/**
 * VehicleDepotSeeder - Seed default vehicle depots
 *
 * Creates 3 depots for each organization:
 * - DEPOT-ALGER-01 (Algiers, cap 50)
 * - DEPOT-ORAN-01 (Oran, cap 30)
 * - DEPOT-CONSTANTINE-01 (Constantine, cap 25)
 *
 * @version 1.0-Enterprise
 */
class VehicleDepotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏢 Seeding vehicle depots...');

        $depots = [
            [
                'name' => 'Dépôt Central Alger',
                'code' => 'DEPOT-ALGER-01',
                'address' => 'Zone Industrielle, Rouiba',
                'city' => 'Alger',
                'wilaya' => 16,
                'postal_code' => 16012,
                'phone' => '0213 23 45 67 89',
                'manager_name' => 'Mohamed Benali',
                'manager_phone' => '0661 23 45 67',
                'capacity' => 50,
                'current_count' => 0,
                'latitude' => 36.7538,
                'longitude' => 3.0588,
            ],
            [
                'name' => 'Dépôt Oran',
                'code' => 'DEPOT-ORAN-01',
                'address' => 'Zone Industrielle Es Senia',
                'city' => 'Oran',
                'wilaya' => 31,
                'postal_code' => 31000,
                'phone' => '0213 41 23 45 67',
                'manager_name' => 'Ahmed Bouazza',
                'manager_phone' => '0662 34 56 78',
                'capacity' => 30,
                'current_count' => 0,
                'latitude' => 35.6969,
                'longitude' => -0.6331,
            ],
            [
                'name' => 'Dépôt Constantine',
                'code' => 'DEPOT-CONSTANTINE-01',
                'address' => 'Route de Ain Smara',
                'city' => 'Constantine',
                'wilaya' => 25,
                'postal_code' => 25000,
                'phone' => '0213 31 12 34 56',
                'manager_name' => 'Karim Meziani',
                'manager_phone' => '0663 45 67 89',
                'capacity' => 25,
                'current_count' => 0,
                'latitude' => 36.3650,
                'longitude' => 6.6147,
            ],
        ];

        // Get all organizations
        $organizations = Organization::all();

        if ($organizations->isEmpty()) {
            $this->command->warn('  ⚠️  No organizations found. Creating depots for default organization.');
            $organizations = collect([Organization::first() ?? Organization::factory()->create()]);
        }

        $totalCreated = 0;

        foreach ($organizations as $organization) {
            $this->command->info("  📁 Creating depots for organization: {$organization->name}");

            foreach ($depots as $depotData) {
                $existing = VehicleDepot::where('organization_id', $organization->id)
                    ->where('code', $depotData['code'])
                    ->first();

                if (!$existing) {
                    VehicleDepot::create(array_merge($depotData, [
                        'organization_id' => $organization->id,
                        'is_active' => true,
                    ]));

                    $totalCreated++;
                    $this->command->info("    ✓ Created: {$depotData['name']} (capacity: {$depotData['capacity']})");
                } else {
                    $this->command->info("    - Skipped: {$depotData['name']} (already exists)");
                }
            }
        }

        $this->command->info('');
        $this->command->info("✅ Vehicle depots seeded successfully! Total created: {$totalCreated}");
    }
}
