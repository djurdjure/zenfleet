<?php

namespace Database\Seeders;

use App\Models\Handover\HandoverChecklistTemplate;
use App\Models\Organization;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class HandoverChecklistTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all organizations
        $organizations = Organization::all();

        // Get vehicle types
        $carType = VehicleType::where('name', 'Voiture')->first();
        $motoType = VehicleType::where('name', 'Moto')->first();

        foreach ($organizations as $organization) {
            // Car template (specific to vehicle type)
            if ($carType) {
                HandoverChecklistTemplate::updateOrCreate(
                    [
                        'organization_id' => $organization->id,
                        'vehicle_type_id' => $carType->id,
                    ],
                    [
                        'name' => 'Checklist Voiture Standard',
                        'is_default' => false,
                        'template_json' => [
                            'Papiers du véhicule' => [
                                'type' => 'binary',
                                'items' => [
                                    'Carte Grise',
                                    'Assurance',
                                    'Vignette',
                                    'Contrôle technique',
                                    'Permis de circuler',
                                    'Carte Carburant',
                                ],
                            ],
                            'Accessoires Intérieur' => [
                                'type' => 'binary',
                                'items' => [
                                    'Triangle',
                                    'Cric',
                                    'Manivelle/Clé',
                                    'Gilet',
                                    'Tapis',
                                    'Extincteur',
                                    'Trousse de secours',
                                    'Rétroviseur intérieur',
                                    'Pare-soleil',
                                    'Autoradio',
                                    'Propreté',
                                ],
                            ],
                            'Pneumatiques' => [
                                'type' => 'condition',
                                'items' => [
                                    'Roue AV Gauche',
                                    'Roue AV Droite',
                                    'Roue AR Gauche',
                                    'Roue AR Droite',
                                    'Roue de Secours',
                                    'Enjoliveurs',
                                ],
                            ],
                            'État Extérieur' => [
                                'type' => 'condition',
                                'items' => [
                                    'Vitres',
                                    'Pare-brise',
                                    'Rétroviseur Gauche',
                                    'Rétroviseur Droit',
                                    'Verrouillage',
                                    'Poignées',
                                    'Feux avant',
                                    'Feux arrières',
                                    'Essuie-glaces',
                                    'Carrosserie Générale',
                                ],
                            ],
                        ],
                    ]
                );
            }

            // Moto template (specific to vehicle type)
            if ($motoType) {
                HandoverChecklistTemplate::updateOrCreate(
                    [
                        'organization_id' => $organization->id,
                        'vehicle_type_id' => $motoType->id,
                    ],
                    [
                        'name' => 'Checklist Moto Standard',
                        'is_default' => false,
                        'template_json' => [
                            'Papiers & Accessoires' => [
                                'type' => 'binary',
                                'items' => [
                                    'Carte Grise',
                                    'Assurance',
                                    'Carte Carburant',
                                    'Clé',
                                    'Casque',
                                    'Top-case',
                                ],
                            ],
                            'État Général' => [
                                'type' => 'condition',
                                'items' => [
                                    'Pneu Avant',
                                    'Pneu Arrière',
                                    'Saute-vent',
                                    'Rétroviseur Gauche',
                                    'Rétroviseur Droit',
                                    'Verrouillage',
                                    'Feux avant',
                                    'Feux arrières',
                                    'Carrosserie Générale',
                                    'Propreté',
                                ],
                            ],
                        ],
                    ]
                );
            }

            // Default template (no vehicle type specified)
            // This will be used as fallback if no specific template matches
            HandoverChecklistTemplate::updateOrCreate(
                [
                    'organization_id' => $organization->id,
                    'vehicle_type_id' => null,
                ],
                [
                    'name' => 'Checklist Générique par Défaut',
                    'is_default' => true,
                    'template_json' => [
                        'Papiers du véhicule' => [
                            'type' => 'binary',
                            'items' => [
                                'Carte Grise',
                                'Assurance',
                                'Vignette',
                                'Contrôle technique',
                                'Permis de circuler',
                                'Carte Carburant',
                            ],
                        ],
                        'Accessoires Intérieur' => [
                            'type' => 'binary',
                            'items' => [
                                'Triangle',
                                'Cric',
                                'Manivelle/Clé',
                                'Gilet',
                                'Tapis',
                                'Extincteur',
                                'Trousse de secours',
                                'Rétroviseur intérieur',
                                'Pare-soleil',
                                'Autoradio',
                                'Propreté',
                            ],
                        ],
                        'Pneumatiques' => [
                            'type' => 'condition',
                            'items' => [
                                'Roue AV Gauche',
                                'Roue AV Droite',
                                'Roue AR Gauche',
                                'Roue AR Droite',
                                'Roue de Secours',
                                'Enjoliveurs',
                            ],
                        ],
                        'État Extérieur' => [
                            'type' => 'condition',
                            'items' => [
                                'Vitres',
                                'Pare-brise',
                                'Rétroviseur Gauche',
                                'Rétroviseur Droit',
                                'Verrouillage',
                                'Poignées',
                                'Feux avant',
                                'Feux arrières',
                                'Essuie-glaces',
                                'Carrosserie Générale',
                            ],
                        ],
                    ],
                ]
            );
        }

        $this->command->info('Default handover checklist templates created for all organizations.');
    }
}
