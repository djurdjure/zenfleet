<?php

namespace Database\Seeders;

use App\Models\DriverStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * 🚛 DRIVER STATUS SEEDER - Enterprise-Grade
 *
 * Crée les statuts de chauffeurs avec configuration complète :
 * - Couleurs et icônes
 * - Permissions (can_drive, can_assign)
 * - Multi-organisation (global + spécifique)
 * - Tri et activation
 *
 * @version 2.0-Enterprise
 */
class DriverStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🌍 STATUTS GLOBAUX - Disponibles pour toutes les organisations
        $globalStatuses = [
            [
                'name' => 'Disponible',
                'slug' => 'disponible',
                'description' => 'Chauffeur disponible pour nouvelle affectation',
                'color' => '#10b981', // Green
                'icon' => 'fa-check-circle',
                'is_active' => true,
                'can_drive' => true,
                'can_assign' => true,
                'requires_validation' => false,
                'sort_order' => 1,
                'organization_id' => null,
            ],
            [
                'name' => 'En mission',
                'slug' => 'en_mission',
                'description' => 'Chauffeur actuellement affecté à un véhicule',
                'color' => '#3b82f6', // Blue
                'icon' => 'fa-car',
                'is_active' => true,
                'can_drive' => true,
                'can_assign' => false,
                'requires_validation' => false,
                'sort_order' => 2,
                'organization_id' => null,
            ],
            [
                'name' => 'En formation',
                'slug' => 'en_formation',
                'description' => 'Chauffeur en période de formation',
                'color' => '#8b5cf6', // Purple
                'icon' => 'fa-graduation-cap',
                'is_active' => true,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => true,
                'sort_order' => 3,
                'organization_id' => null,
            ],
            [
                'name' => 'En congé',
                'slug' => 'en_conge',
                'description' => 'Chauffeur en congé',
                'color' => '#f59e0b', // Amber
                'icon' => 'fa-umbrella-beach',
                'is_active' => true,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => true,
                'sort_order' => 4,
                'organization_id' => null,
            ],
            [
                'name' => 'Autre',
                'slug' => 'autre',
                'description' => 'Autre statut ou inactif',
                'color' => '#6b7280', // Gray
                'icon' => 'fa-question-circle',
                'is_active' => true,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => true,
                'sort_order' => 5,
                'organization_id' => null,
            ],
        ];

        // Créer ou mettre à jour les statuts globaux
        foreach ($globalStatuses as $statusData) {
            DriverStatus::updateOrCreate(
                ['slug' => $statusData['slug'], 'organization_id' => null],
                $statusData
            );
        }

        // Afficher le message (compatible avec ou sans command)
        $message = '✅ ' . count($globalStatuses) . ' statuts de chauffeurs globaux créés/mis à jour';

        if ($this->command) {
            $this->command->info($message);
        } else {
            echo "   {$message}\n";
        }
    }
}
