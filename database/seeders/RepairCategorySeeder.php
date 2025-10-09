<?php

namespace Database\Seeders;

use App\Models\RepairCategory;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RepairCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Catégories par défaut enterprise-grade
        $defaultCategories = [
            [
                'name' => 'Mécanique Générale',
                'description' => 'Réparations mécaniques générales (moteur, transmission, embrayage, etc.)',
                'icon' => 'wrench',
                'color' => 'blue',
                'sort_order' => 10,
            ],
            [
                'name' => 'Freinage',
                'description' => 'Système de freinage (plaquettes, disques, liquide de frein, ABS)',
                'icon' => 'hand-paper',
                'color' => 'red',
                'sort_order' => 20,
            ],
            [
                'name' => 'Suspension',
                'description' => 'Amortisseurs, ressorts, bras de suspension, rotules',
                'icon' => 'compress-arrows-alt',
                'color' => 'purple',
                'sort_order' => 30,
            ],
            [
                'name' => 'Électricité',
                'description' => 'Système électrique, batterie, alternateur, câblage',
                'icon' => 'bolt',
                'color' => 'yellow',
                'sort_order' => 40,
            ],
            [
                'name' => 'Carrosserie',
                'description' => 'Réparation de carrosserie, peinture, pare-chocs',
                'icon' => 'car',
                'color' => 'indigo',
                'sort_order' => 50,
            ],
            [
                'name' => 'Pneumatiques',
                'description' => 'Pneus, jantes, équilibrage, géométrie',
                'icon' => 'circle',
                'color' => 'gray',
                'sort_order' => 60,
            ],
            [
                'name' => 'Climatisation',
                'description' => 'Système de climatisation et chauffage',
                'icon' => 'snowflake',
                'color' => 'blue',
                'sort_order' => 70,
            ],
            [
                'name' => 'Échappement',
                'description' => 'Pot d\'échappement, catalyseur, FAP',
                'icon' => 'cloud',
                'color' => 'gray',
                'sort_order' => 80,
            ],
            [
                'name' => 'Vitrage',
                'description' => 'Pare-brise, vitres, rétroviseurs',
                'icon' => 'window-maximize',
                'color' => 'blue',
                'sort_order' => 90,
            ],
            [
                'name' => 'Éclairage',
                'description' => 'Phares, feux, clignotants, éclairage intérieur',
                'icon' => 'lightbulb',
                'color' => 'orange',
                'sort_order' => 100,
            ],
            [
                'name' => 'Révision Périodique',
                'description' => 'Entretiens réguliers, vidanges, filtres',
                'icon' => 'calendar-check',
                'color' => 'green',
                'sort_order' => 110,
            ],
            [
                'name' => 'Contrôle Technique',
                'description' => 'Préparation et passage du contrôle technique',
                'icon' => 'clipboard-check',
                'color' => 'green',
                'sort_order' => 120,
            ],
            [
                'name' => 'Dépannage Urgent',
                'description' => 'Intervention urgente, panne immobilisante',
                'icon' => 'exclamation-triangle',
                'color' => 'red',
                'sort_order' => 130,
            ],
            [
                'name' => 'Accessoires',
                'description' => 'Installation/réparation d\'accessoires (GPS, caméra, etc.)',
                'icon' => 'puzzle-piece',
                'color' => 'pink',
                'sort_order' => 140,
            ],
            [
                'name' => 'Autres',
                'description' => 'Autres types de réparations non catégorisées',
                'icon' => 'ellipsis-h',
                'color' => 'gray',
                'sort_order' => 999,
            ],
        ];

        // Créer les catégories pour chaque organisation
        Organization::chunk(100, function ($organizations) use ($defaultCategories) {
            foreach ($organizations as $organization) {
                // Vérifier si les catégories existent déjà pour cette organisation
                $existingCount = RepairCategory::where('organization_id', $organization->id)->count();

                if ($existingCount > 0) {
                    $this->command->warn("⚠️  Skipping organization {$organization->name} - {$existingCount} categories already exist");
                    continue;
                }

                foreach ($defaultCategories as $categoryData) {
                    RepairCategory::create([
                        'organization_id' => $organization->id,
                        'name' => $categoryData['name'],
                        'description' => $categoryData['description'],
                        'slug' => Str::slug($categoryData['name']) . '-' . $organization->id,
                        'icon' => $categoryData['icon'],
                        'color' => $categoryData['color'],
                        'sort_order' => $categoryData['sort_order'],
                        'is_active' => true,
                        'metadata' => [
                            'seeded_at' => now()->toIso8601String(),
                            'version' => '1.0',
                        ],
                    ]);
                }

                $count = count($defaultCategories);
                $this->command->info("✅ Created {$count} repair categories for organization: {$organization->name}");
            }
        });

        $this->command->info("🎉 Repair categories seeder completed successfully!");
    }
}
