<?php

namespace App\Http\Requests\Admin\Vehicle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit vehicles');
    }

    public function messages(): array
    {
        return [
            'registration_plate.required' => 'L\'immatriculation est obligatoire.',
            'registration_plate.unique' => 'Cette immatriculation existe déjà dans votre organisation.',
            'vin.size' => 'Le numéro VIN doit contenir exactement 17 caractères.',
            'vin.unique' => 'Ce numéro VIN existe déjà dans votre organisation.',
            'vehicle_name.unique' => 'Ce nom de véhicule existe déjà dans votre organisation.',
            'manufacturing_year.integer' => 'L\'année de fabrication doit être un nombre.',
            'manufacturing_year.digits' => 'L\'année de fabrication doit contenir 4 chiffres.',
            'manufacturing_year.min' => 'L\'année de fabrication doit être au minimum 1950.',
            'manufacturing_year.max' => 'L\'année de fabrication ne peut pas dépasser l\'année prochaine.',
            'acquisition_date.date_format' => 'La date d\'acquisition doit être au format valide.',
            'purchase_price.numeric' => 'Le prix d\'achat doit être un nombre.',
            'purchase_price.min' => 'Le prix d\'achat doit être positif.',
            'current_value.numeric' => 'La valeur actuelle doit être un nombre.',
            'current_value.min' => 'La valeur actuelle doit être positive.',
            'initial_mileage.integer' => 'Le kilométrage initial doit être un nombre entier.',
            'initial_mileage.min' => 'Le kilométrage initial doit être positif.',
            'current_mileage.integer' => 'Le kilométrage actuel doit être un nombre entier.',
            'current_mileage.min' => 'Le kilométrage actuel doit être positif.',
            'current_mileage.gte' => 'Le kilométrage actuel ne peut pas être inférieur au kilométrage initial.',
            'engine_displacement_cc.integer' => 'La cylindrée doit être un nombre entier.',
            'engine_displacement_cc.min' => 'La cylindrée doit être positive.',
            'power_hp.integer' => 'La puissance doit être un nombre entier.',
            'power_hp.min' => 'La puissance doit être positive.',
            'seats.integer' => 'Le nombre de places doit être un nombre entier.',
            'seats.min' => 'Le nombre de places doit être au minimum 1.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'L\'image doit être au format jpeg, png, jpg ou gif.',
            'photo.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ];
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle')->id;
        $organizationId = auth()->user()->organization_id;

        return [
            'registration_plate' => [
                'required',
                'string',
                'max:50',
                Rule::unique('vehicles')
                    ->ignore($vehicleId)
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
            ],
            'vin' => [
                'nullable',
                'string',
                'size:17',
                Rule::unique('vehicles')
                    ->ignore($vehicleId)
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
            ],
            'vehicle_name' => [
                'nullable',
                'string',
                'max:150',
                Rule::unique('vehicles')
                    ->ignore($vehicleId)
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
            ],
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
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'vehicle_type_id' => ['nullable', 'exists:vehicle_types,id'],
            'fuel_type_id' => ['required', 'exists:fuel_types,id'],
            'transmission_type_id' => ['nullable', 'exists:transmission_types,id'],
            'status_id' => ['nullable', 'exists:vehicle_statuses,id'],
            'manufacturing_year' => ['nullable', 'integer', 'digits:4', 'min:1950', 'max:' . (date('Y') + 1)],
            'acquisition_date' => ['nullable', 'date_format:Y-m-d'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'current_value' => ['nullable', 'numeric', 'min:0'],
            'initial_mileage' => ['nullable', 'integer', 'min:0'],
            'current_mileage' => ['nullable', 'integer', 'min:0', 'gte:' . ($this->route('vehicle')->initial_mileage ?? 0)],
            'engine_displacement_cc' => ['nullable', 'integer', 'min:0'],
            'power_hp' => ['nullable', 'integer', 'min:0'],
            'seats' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }
}
