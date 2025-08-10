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
        return $this->user()->can('create vehicles');
    }

    /**
     * Récupère les règles de validation qui s'appliquent à la requête.
     */
    public function rules(): array
    {
        return [
            'registration_plate' => ['required', 'string', 'max:50', Rule::unique('vehicles')->whereNull('deleted_at')],
            'vin' => ['nullable', 'string', 'size:17', Rule::unique('vehicles')->whereNull('deleted_at')],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
            'fuel_type_id' => ['required', 'exists:fuel_types,id'],
            'transmission_type_id' => ['required', 'exists:transmission_types,id'],
            'status_id' => ['required', 'exists:vehicle_statuses,id'],
            'manufacturing_year' => ['nullable', 'integer', 'digits:4', 'min:1950', 'max:'.(date('Y') + 1)],
            'acquisition_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'current_value' => ['nullable', 'numeric', 'min:0'],
            'initial_mileage' => ['nullable', 'integer', 'min:0'],
            'current_mileage' => ['nullable', 'integer', 'min:0', 'gte:initial_mileage'], // This rule is for creation, should be initial_mileage
            'engine_displacement_cc' => ['nullable', 'integer', 'min:0'],
            'power_hp' => ['nullable', 'integer', 'min:0'],
            'seats' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }
}

