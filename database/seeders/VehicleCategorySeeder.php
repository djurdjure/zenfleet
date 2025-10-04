<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\VehicleCategory;
use Illuminate\Database\Seeder;

/**
 * VehicleCategorySeeder - Seed default vehicle categories
 *
 * Creates 5 standard categories for each organization:
 * - VL (Véhicules Légers)
 * - VU (Véhicules Utilitaires)
 * - PL (Poids Lourds)
 * - MOTO (Motos)
 * - BUS (Bus)
 *
 * @version 1.0-Enterprise
 */
class VehicleCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚗 Seeding vehicle categories...');

        $categories = [
            [
                'name' => 'Véhicules Légers',
                'code' => 'VL',
                'color_code' => '#3B82F6',
                'icon' => 'car',
                'description' => 'Voitures particulières, berlines, citadines',
                'sort_order' => 1,
            ],
            [
                'name' => 'Véhicules Utilitaires',
                'code' => 'VU',
                'color_code' => '#F59E0B',
                'icon' => 'truck',
                'description' => 'Camionnettes, fourgons, véhicules de service',
                'sort_order' => 2,
            ],
            [
                'name' => 'Poids Lourds',
                'code' => 'PL',
                'color_code' => '#EF4444',
                'icon' => 'container',
                'description' => 'Camions, semi-remorques, véhicules de transport',
                'sort_order' => 3,
            ],
            [
                'name' => 'Motos',
                'code' => 'MOTO',
                'color_code' => '#10B981',
                'icon' => 'bike',
                'description' => 'Motos, scooters, cyclomoteurs',
                'sort_order' => 4,
            ],
            [
                'name' => 'Bus',
                'code' => 'BUS',
                'color_code' => '#8B5CF6',
                'icon' => 'bus',
                'description' => 'Bus, autocars, véhicules de transport en commun',
                'sort_order' => 5,
            ],
        ];

        // Get all organizations
        $organizations = Organization::all();

        if ($organizations->isEmpty()) {
            $this->command->warn('  ⚠️  No organizations found. Creating categories for default organization.');
            $organizations = collect([Organization::first() ?? Organization::factory()->create()]);
        }

        $totalCreated = 0;

        foreach ($organizations as $organization) {
            $this->command->info("  📁 Creating categories for organization: {$organization->name}");

            foreach ($categories as $categoryData) {
                $existing = VehicleCategory::where('organization_id', $organization->id)
                    ->where('code', $categoryData['code'])
                    ->first();

                if (!$existing) {
                    VehicleCategory::create(array_merge($categoryData, [
                        'organization_id' => $organization->id,
                        'is_active' => true,
                    ]));

                    $totalCreated++;
                    $this->command->info("    ✓ Created: {$categoryData['name']} ({$categoryData['code']})");
                } else {
                    $this->command->info("    - Skipped: {$categoryData['name']} (already exists)");
                }
            }
        }

        $this->command->info('');
        $this->command->info("✅ Vehicle categories seeded successfully! Total created: {$totalCreated}");
    }
}
