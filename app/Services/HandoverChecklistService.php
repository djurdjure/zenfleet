<?php

namespace App\Services;

use App\Models\Handover\HandoverChecklistTemplate;
use App\Models\Vehicle;

class HandoverChecklistService
{
    /**
     * Fallback template used when no checklist template exists in DB.
     *
     * This keeps handover creation operational in dev/staging and prevents
     * UX dead-ends when templates were not seeded yet.
     */
    private const FALLBACK_TEMPLATE = [
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
    ];

    /**
     * Get the most appropriate template for a vehicle.
     * 
     * Priority:
     * 1. Template matching the vehicle's type
     * 2. Default template (is_default=true) for the organization
     * 
     * @param Vehicle $vehicle
     * @return HandoverChecklistTemplate|null
     */
    public function getTemplateForVehicle(Vehicle $vehicle): ?HandoverChecklistTemplate
    {
        // First, try to get a template specific to the vehicle type
        $template = HandoverChecklistTemplate::where('organization_id', $vehicle->organization_id)
            ->where('vehicle_type_id', $vehicle->vehicle_type_id)
            ->first();

        if ($template) {
            return $template;
        }

        // Fallback to the default template for the organization
        return HandoverChecklistTemplate::where('organization_id', $vehicle->organization_id)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Get the template structure as an array for the view.
     * 
     * Returns a structured array with category names as keys and items/types as values.
     * Also includes status types for each category.
     * 
     * @param Vehicle $vehicle
     * @return array
     */
    public function getTemplateStructure(Vehicle $vehicle): array
    {
        $template = $this->getTemplateForVehicle($vehicle);
        $templateJson = $template?->template_json;

        if (!is_array($templateJson) || empty($templateJson)) {
            $templateJson = self::FALLBACK_TEMPLATE;
        }

        return $this->buildStructureFromTemplateJson($templateJson);
    }

    /**
     * Get valid statuses for a category type.
     * 
     * @param string $categoryType Either 'binary' or 'condition'
     * @return array
     */
    public function getValidStatusesForCategory(string $categoryType): array
    {
        return match ($categoryType) {
            'binary' => ['Oui', 'Non', 'N/A'],
            'condition' => ['Bon', 'Moyen', 'Mauvais', 'N/A'],
            default => ['Bon', 'Moyen', 'Mauvais', 'N/A'],
        };
    }

    /**
     * Get all valid statuses (union of binary and condition).
     * Used for validation when we don't know the specific category type.
     * 
     * @return array
     */
    public function getAllValidStatuses(): array
    {
        return ['Bon', 'Moyen', 'Mauvais', 'N/A', 'Oui', 'Non'];
    }

    /**
     * Validate checklist data against a template.
     * 
     * @param Vehicle $vehicle
     * @param array $checklistData
     * @return array Returns ['valid' => bool, 'errors' => array]
     */
    public function validateChecklistData(Vehicle $vehicle, array $checklistData): array
    {
        $template = $this->getTemplateForVehicle($vehicle);
        $errors = [];
        $templateJson = $template?->template_json;

        if (!is_array($templateJson) || empty($templateJson)) {
            $templateJson = self::FALLBACK_TEMPLATE;
        }

        foreach ($checklistData as $category => $items) {
            // Check if category exists in template
            if (!isset($templateJson[$category])) {
                $errors[] = "Unknown category: {$category}";
                continue;
            }

            $categoryConfig = $templateJson[$category];
            $validStatuses = $this->getValidStatusesForCategory($categoryConfig['type'] ?? 'condition');

            foreach ($items as $itemKey => $status) {
                // Check if status is valid for this category type
                if (!in_array($status, $validStatuses)) {
                    $errors[] = "Invalid status '{$status}' for {$category} -> {$itemKey}";
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Build view-ready structure from template json.
     */
    private function buildStructureFromTemplateJson(array $templateJson): array
    {
        $structure = [];

        foreach ($templateJson as $category => $config) {
            $categoryType = $config['type'] ?? 'condition';
            $structure[$category] = [
                'type' => $categoryType,
                'items' => $config['items'] ?? [],
                'statuses' => $this->getValidStatusesForCategory($categoryType),
            ];
        }

        return $structure;
    }
}
