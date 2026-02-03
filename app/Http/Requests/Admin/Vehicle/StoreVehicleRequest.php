<?php

namespace App\Http\Requests\Admin\Vehicle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user && $user->can('vehicles.create');
    }

    /**
     * Récupère les règles de validation qui s'appliquent à la requête.
     * 
     * ⚠️ RÈGLES ENTERPRISE-GRADE - Validation stricte pour intégrité des données
     */
    public function rules(): array
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $organizationId = $user->organization_id;

        return [
            // ========================================
            // PHASE 1: IDENTIFICATION (Champs Required)
            // ========================================
            // ========================================
            // PHASE 1: IDENTIFICATION
            // ========================================
            'registration_plate' => [
                'required',
                'string',
                'max:50',
                Rule::unique('vehicles')
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
            ],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'], // Devenu facultatif
            'vin' => [
                'nullable',
                'string',
                'size:17',
                Rule::unique('vehicles')
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
            ],
            'vehicle_name' => [
                'nullable',
                'string',
                'max:150',
                Rule::unique('vehicles')
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
            ],
            'color' => ['nullable', 'string', 'max:50'],

            // ========================================
            // PHASE 2: CARACTÉRISTIQUES
            // ========================================
            'vehicle_type_id' => ['nullable', 'exists:vehicle_types,id'], // Devenu facultatif
            'fuel_type_id' => ['required', 'exists:fuel_types,id'],
            'transmission_type_id' => ['nullable', 'exists:transmission_types,id'], // Devenu facultatif
            'manufacturing_year' => ['nullable', 'integer', 'digits:4', 'min:1950', 'max:' . (date('Y') + 1)],
            'seats' => ['nullable', 'integer', 'min:1', 'max:99'],
            'power_hp' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'engine_displacement_cc' => ['nullable', 'integer', 'min:0', 'max:99999'],

            // ========================================
            // PHASE 3: ACQUISITION & STATUT
            // ========================================
            'status_id' => ['nullable', 'exists:vehicle_statuses,id'], // Devenu facultatif (géré par défaut)
            'acquisition_date' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'], // Devenu facultatif
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'current_value' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'initial_mileage' => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'current_mileage' => ['nullable', 'integer', 'min:0', 'max:9999999', 'gte:initial_mileage'],
            'notes' => ['nullable', 'string', 'max:5000'],

            // ========================================
            // CHAMPS OPTIONNELS
            // ========================================
            'category_id' => [
                'nullable',
                Rule::exists('vehicle_categories', 'id')
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
            ],
            'depot_id' => [
                'nullable',
                Rule::exists('vehicle_depots', 'id')
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
            ],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Prépare les données avant validation.
     */
    protected function prepareForValidation(): void
    {
        // Si aucun statut n'est fourni, on assigne "Parking" par défaut
        if ($this->missing('status_id') || is_null($this->input('status_id'))) {
            $parkingStatus = \App\Models\VehicleStatus::where('name', 'Parking')->first();

            if ($parkingStatus) {
                $this->merge([
                    'status_id' => $parkingStatus->id,
                ]);
            }
        }
    }

    /**
     * Messages d'erreur personnalisés enterprise-grade
     */
    public function messages(): array
    {
        return [
            // Phase 1 - Identification
            'registration_plate.required' => 'L\'immatriculation est obligatoire',
            'registration_plate.unique' => 'Cette immatriculation existe déjà dans votre organisation',
            'brand.required' => 'La marque du véhicule est obligatoire',
            'model.required' => 'Le modèle du véhicule est obligatoire',
            'vin.size' => 'Le VIN doit contenir exactement 17 caractères',
            'vin.unique' => 'Ce numéro VIN existe déjà dans votre organisation',

            // Phase 2 - Caractéristiques
            'vehicle_type_id.required' => 'Le type de véhicule est obligatoire',
            'fuel_type_id.required' => 'Le type de carburant est obligatoire',
            'transmission_type_id.required' => 'Le type de transmission est obligatoire',
            'manufacturing_year.digits' => 'L\'année doit comporter 4 chiffres',
            'manufacturing_year.min' => 'L\'année doit être postérieure à 1950',
            'manufacturing_year.max' => 'L\'année ne peut pas être dans le futur',
            'seats.min' => 'Le nombre de places doit être au moins 1',
            'seats.max' => 'Le nombre de places ne peut pas dépasser 99',

            // Phase 3 - Acquisition
            'status_id.required' => 'Le statut du véhicule est obligatoire',
            'acquisition_date.required' => 'La date d\'acquisition est obligatoire',
            'acquisition_date.date_format' => 'La date d\'acquisition doit être au format valide',
            'acquisition_date.before_or_equal' => 'La date d\'acquisition ne peut pas être dans le futur',
            'current_mileage.gte' => 'Le kilométrage actuel doit être supérieur ou égal au kilométrage initial',
        ];
    }

    /**
     * Attributs personnalisés pour messages d'erreur
     */
    public function attributes(): array
    {
        return [
            'registration_plate' => 'immatriculation',
            'vin' => 'numéro VIN',
            'brand' => 'marque',
            'model' => 'modèle',
            'vehicle_type_id' => 'type de véhicule',
            'fuel_type_id' => 'type de carburant',
            'transmission_type_id' => 'type de transmission',
            'status_id' => 'statut',
            'acquisition_date' => 'date d\'acquisition',
            'manufacturing_year' => 'année de fabrication',
            'seats' => 'nombre de places',
            'power_hp' => 'puissance',
            'engine_displacement_cc' => 'cylindrée',
        ];
    }
}
