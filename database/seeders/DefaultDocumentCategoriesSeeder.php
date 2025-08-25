<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentCategory;

class DefaultDocumentCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultCategories = [
            [
                'name' => 'Assurance',
                'description' => 'Document d\'assurance du véhicule.',
                'is_default' => true,
                'organization_id' => null,
                'meta_schema' => json_encode([
                    'fields' => [
                        ['name' => 'fournisseur_id', 'label' => 'Fournisseur', 'type' => 'entity_select', 'entity' => 'supplier', 'required' => true, 'visible' => true, 'editable' => true],
                        ['name' => 'date_debut', 'label' => 'Date de Début', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                        ['name' => 'date_fin', 'label' => 'Date de Fin', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                    ]
                ]),
            ],
            [
                'name' => 'Assurance Marchandise',
                'description' => 'Assurance spécifique pour la marchandise transportée.',
                'is_default' => true,
                'organization_id' => null,
                'meta_schema' => json_encode([
                    'fields' => [
                        ['name' => 'fournisseur_id', 'label' => 'Fournisseur', 'type' => 'entity_select', 'entity' => 'supplier', 'required' => true, 'visible' => true, 'editable' => true],
                        ['name' => 'date_debut', 'label' => 'Date de Début', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                        ['name' => 'date_fin', 'label' => 'Date de Fin', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                    ]
                ]),
            ],
            [
                'name' => 'Permis de Circuler',
                'description' => 'Document autorisant la circulation du véhicule.',
                'is_default' => true,
                'organization_id' => null,
                'meta_schema' => json_encode([
                    'fields' => [
                        ['name' => 'fournisseur_id', 'label' => 'Fournisseur', 'type' => 'entity_select', 'entity' => 'supplier', 'required' => true, 'visible' => true, 'editable' => true],
                        ['name' => 'date_debut', 'label' => 'Date de Début', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                        ['name' => 'date_fin', 'label' => 'Date de Fin', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                    ]
                ]),
            ],
            [
                'name' => 'Vignette',
                'description' => 'Vignette fiscale ou environnementale.',
                'is_default' => true,
                'organization_id' => null,
                'meta_schema' => json_encode([
                    'fields' => [
                        ['name' => 'date_debut', 'label' => 'Date de Début', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                        ['name' => 'date_fin', 'label' => 'Date de Fin', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                    ]
                ]),
            ],
            [
                'name' => 'Contrôle Technique',
                'description' => 'Rapport de contrôle technique du véhicule.',
                'is_default' => true,
                'organization_id' => null,
                'meta_schema' => json_encode([
                    'fields' => [
                        ['name' => 'fournisseur_id', 'label' => 'Fournisseur', 'type' => 'entity_select', 'entity' => 'supplier', 'required' => true, 'visible' => true, 'editable' => true],
                        ['name' => 'date_debut', 'label' => 'Date de Début', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                        ['name' => 'date_fin', 'label' => 'Date de Fin', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                    ]
                ]),
            ],
            [
                'name' => 'Constat d\'Accident',
                'description' => 'Constat amiable ou rapport d\'accident.',
                'is_default' => true,
                'organization_id' => null,
                'meta_schema' => json_encode([
                    'fields' => [
                        ['name' => 'date_accident', 'label' => 'Date de l\'Accident', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                    ]
                ]),
            ],
            [
                'name' => 'Fiche Remise Véhicule',
                'description' => 'Document de remise du véhicule à un chauffeur.',
                'is_default' => true,
                'organization_id' => null,
                'meta_schema' => json_encode([
                    'fields' => [
                        ['name' => 'date_remise', 'label' => 'Date de Remise', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                        ['name' => 'date_reprise_prevue', 'label' => 'Date de Reprise Prévue', 'type' => 'date', 'required' => false, 'visible' => true, 'editable' => true],
                    ]
                ]),
            ],
            [
                'name' => 'Fiche Reprise Véhicule',
                'description' => 'Document de reprise du véhicule d\'un chauffeur.',
                'is_default' => true,
                'organization_id' => null,
                'meta_schema' => json_encode([
                    'fields' => [
                        ['name' => 'date_reprise', 'label' => 'Date de Reprise', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                    ]
                ]),
            ],
            [
                'name' => 'Permis de Conduire',
                'description' => 'Permis de conduire du chauffeur.',
                'is_default' => true,
                'organization_id' => null,
                'meta_schema' => json_encode([
                    'fields' => [
                        ['name' => 'categories_permis', 'label' => 'Catégories de Permis', 'type' => 'multiselect', 'options' => ['A', 'B', 'C', 'D', 'E'], 'required' => true, 'visible' => true, 'editable' => true],
                        ['name' => 'restrictions', 'label' => 'Restrictions', 'type' => 'textarea', 'required' => false, 'visible' => true, 'editable' => true],
                    ]
                ]),
            ],
            [
                'name' => 'Avertissement',
                'description' => 'Avertissement disciplinaire ou observation.',
                'is_default' => true,
                'organization_id' => null,
                'meta_schema' => json_encode([
                    'fields' => [
                        ['name' => 'date_avertissement', 'label' => 'Date d\'Avertissement', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                        ['name' => 'observation', 'label' => 'Observation', 'type' => 'textarea', 'required' => true, 'visible' => true, 'editable' => true],
                    ]
                ]),
            ],
            [
                'name' => 'Facture',
                'description' => 'Facture de service ou d\'achat.',
                'is_default' => true,
                'organization_id' => null,
                'meta_schema' => json_encode([
                    'fields' => [
                        ['name' => 'montant_ht', 'label' => 'Montant HT', 'type' => 'number', 'required' => true, 'visible' => true, 'editable' => true],
                        ['name' => 'numero_facture', 'label' => 'Numéro de Facture', 'type' => 'string', 'required' => true, 'visible' => true, 'editable' => true],
                        ['name' => 'date_emission', 'label' => 'Date d\'Émission', 'type' => 'date', 'required' => true, 'visible' => true, 'editable' => true],
                    ]
                ]),
            ],
        ];

        foreach ($defaultCategories as $categoryData) {
            // Use updateOrCreate to avoid duplicates
            DocumentCategory::updateOrCreate(
                ['name' => $categoryData['name'], 'is_default' => true],
                $categoryData
            );
        }
    }
}
