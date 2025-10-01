<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaintenanceType;
use App\Models\Organization;

class MaintenanceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Types de maintenance prédéfinis
        $maintenanceTypes = [
            [
                'name' => 'Renouvellement assurance',
                'category' => 'inspection',
                'description' => 'Renouvellement de l\'assurance du véhicule',
                'default_interval_km' => null,
                'default_interval_days' => 365,
                'estimated_duration_minutes' => 30,
                'is_recurring' => true,
            ],
            [
                'name' => 'Assurance marchandises',
                'category' => 'inspection',
                'description' => 'Assurance pour le transport de marchandises',
                'default_interval_km' => null,
                'default_interval_days' => 365,
                'estimated_duration_minutes' => 30,
                'is_recurring' => true,
            ],
            [
                'name' => 'Vignette/impôts',
                'category' => 'inspection',
                'description' => 'Paiement de la vignette et des impôts annuels',
                'default_interval_km' => null,
                'default_interval_days' => 365,
                'estimated_duration_minutes' => 60,
                'is_recurring' => true,
            ],
            [
                'name' => 'Contrôle technique périodique',
                'category' => 'inspection',
                'description' => 'Contrôle technique obligatoire',
                'default_interval_km' => null,
                'default_interval_days' => 730,
                'estimated_duration_minutes' => 120,
                'is_recurring' => true,
            ],
            [
                'name' => 'Vidange huile moteur',
                'category' => 'preventive',
                'description' => 'Changement de l\'huile moteur et du filtre',
                'default_interval_km' => 10000,
                'default_interval_days' => 365,
                'estimated_duration_minutes' => 60,
                'is_recurring' => true,
            ],
            [
                'name' => 'Remplacement filtres',
                'category' => 'preventive',
                'description' => 'Remplacement des filtres à air, carburant et habitacle',
                'default_interval_km' => 15000,
                'default_interval_days' => 365,
                'estimated_duration_minutes' => 45,
                'is_recurring' => true,
            ],
            [
                'name' => 'Contrôle/courroie de distribution ou chaîne',
                'category' => 'preventive',
                'description' => 'Vérification et remplacement si nécessaire de la courroie/chaîne de distribution',
                'default_interval_km' => 60000,
                'default_interval_days' => 1825,
                'estimated_duration_minutes' => 240,
                'is_recurring' => true,
            ],
            [
                'name' => 'Rotation/permutation des pneus',
                'category' => 'preventive',
                'description' => 'Permutation des pneus pour usure uniforme',
                'default_interval_km' => 10000,
                'default_interval_days' => 180,
                'estimated_duration_minutes' => 30,
                'is_recurring' => true,
            ],
            [
                'name' => 'Test/remplacement batterie',
                'category' => 'preventive',
                'description' => 'Test de la batterie et remplacement si nécessaire',
                'default_interval_km' => null,
                'default_interval_days' => 730,
                'estimated_duration_minutes' => 30,
                'is_recurring' => true,
            ],
            [
                'name' => 'Contrôle éclairage et signalisation',
                'category' => 'preventive',
                'description' => 'Vérification de tous les feux et signalisations',
                'default_interval_km' => 15000,
                'default_interval_days' => 180,
                'estimated_duration_minutes' => 30,
                'is_recurring' => true,
            ],
            [
                'name' => 'Remplacement balais d\'essuie-glace',
                'category' => 'preventive',
                'description' => 'Remplacement des balais d\'essuie-glace',
                'default_interval_km' => null,
                'default_interval_days' => 365,
                'estimated_duration_minutes' => 15,
                'is_recurring' => true,
            ],
            [
                'name' => 'Contrôle mécanique',
                'category' => 'preventive',
                'description' => 'Contrôle général mécanique du véhicule',
                'default_interval_km' => 20000,
                'default_interval_days' => 365,
                'estimated_duration_minutes' => 120,
                'is_recurring' => true,
            ],
            [
                'name' => 'Contrôle électricité',
                'category' => 'preventive',
                'description' => 'Contrôle du système électrique',
                'default_interval_km' => 20000,
                'default_interval_days' => 365,
                'estimated_duration_minutes' => 90,
                'is_recurring' => true,
            ],
            [
                'name' => 'Contrôle des Freins',
                'category' => 'preventive',
                'description' => 'Vérification du système de freinage complet',
                'default_interval_km' => 20000,
                'default_interval_days' => 365,
                'estimated_duration_minutes' => 90,
                'is_recurring' => true,
            ],
            [
                'name' => 'Autres',
                'category' => 'corrective',
                'description' => 'Autres types de maintenance non listés',
                'default_interval_km' => null,
                'default_interval_days' => null,
                'estimated_duration_minutes' => 60,
                'is_recurring' => false,
            ],
        ];

        // Pour chaque organisation, créer les types de maintenance
        $organizations = Organization::all();

        foreach ($organizations as $organization) {
            foreach ($maintenanceTypes as $type) {
                MaintenanceType::updateOrCreate(
                    [
                        'organization_id' => $organization->id,
                        'name' => $type['name'],
                    ],
                    [
                        'category' => $type['category'],
                        'description' => $type['description'],
                        'default_interval_km' => $type['default_interval_km'],
                        'default_interval_days' => $type['default_interval_days'],
                        'estimated_duration_minutes' => $type['estimated_duration_minutes'],
                        'is_recurring' => $type['is_recurring'],
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('Types de maintenance prédéfinis créés avec succès pour toutes les organisations.');
    }
}