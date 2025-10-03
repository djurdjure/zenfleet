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
                'name' => 'Actif',
                'slug' => 'actif',
                'description' => 'Chauffeur actif et disponible pour les affectations',
                'color' => '#10B981', // Green
                'icon' => 'fa-check-circle',
                'is_active' => true,
                'can_drive' => true,
                'can_assign' => true,
                'requires_validation' => false,
                'sort_order' => 1,
                'organization_id' => null, // Global
            ],
            [
                'name' => 'En Mission',
                'slug' => 'en-mission',
                'description' => 'Chauffeur actuellement affecté à un véhicule',
                'color' => '#3B82F6', // Blue
                'icon' => 'fa-car',
                'is_active' => true,
                'can_drive' => true,
                'can_assign' => false, // Déjà assigné
                'requires_validation' => false,
                'sort_order' => 2,
                'organization_id' => null,
            ],
            [
                'name' => 'En Congé',
                'slug' => 'en-conge',
                'description' => 'Chauffeur temporairement indisponible (congés annuels, maladie)',
                'color' => '#F59E0B', // Amber
                'icon' => 'fa-calendar-times',
                'is_active' => true,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => false,
                'sort_order' => 3,
                'organization_id' => null,
            ],
            [
                'name' => 'Suspendu',
                'slug' => 'suspendu',
                'description' => 'Chauffeur suspendu temporairement (sanctions, enquêtes)',
                'color' => '#EF4444', // Red
                'icon' => 'fa-ban',
                'is_active' => true,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => true,
                'sort_order' => 4,
                'organization_id' => null,
            ],
            [
                'name' => 'Formation',
                'slug' => 'formation',
                'description' => 'Chauffeur en période de formation ou d\'intégration',
                'color' => '#8B5CF6', // Purple
                'icon' => 'fa-graduation-cap',
                'is_active' => true,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => true,
                'sort_order' => 5,
                'organization_id' => null,
            ],
            [
                'name' => 'Retraité',
                'slug' => 'retraite',
                'description' => 'Chauffeur à la retraite (archivé)',
                'color' => '#6B7280', // Gray
                'icon' => 'fa-user-clock',
                'is_active' => false,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => false,
                'sort_order' => 6,
                'organization_id' => null,
            ],
            [
                'name' => 'Démission',
                'slug' => 'demission',
                'description' => 'Chauffeur ayant démissionné (archivé)',
                'color' => '#6B7280', // Gray
                'icon' => 'fa-user-minus',
                'is_active' => false,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => false,
                'sort_order' => 7,
                'organization_id' => null,
            ],
            [
                'name' => 'Licencié',
                'slug' => 'licencie',
                'description' => 'Chauffeur licencié (archivé)',
                'color' => '#991B1B', // Dark Red
                'icon' => 'fa-user-times',
                'is_active' => false,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => false,
                'sort_order' => 8,
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
