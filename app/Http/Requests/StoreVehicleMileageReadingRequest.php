<?php

namespace App\Http\Requests;

use App\Models\VehicleMileageReading;
use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StoreVehicleMileageReadingRequest - Validation for creating mileage readings
 *
 * Validates:
 * - Required: vehicle_id, mileage, recorded_at
 * - Optional: recording_method, notes
 * - Multi-tenant isolation via vehicle.organization_id
 * - Business rule: mileage >= vehicle.current_mileage
 *
 * @version 1.0-Enterprise
 */
class StoreVehicleMileageReadingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create mileage readings');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 🔑 VÉHICULE (Required)
            'vehicle_id' => [
                'required',
                'integer',
                Rule::exists('vehicles', 'id')
                    ->where('organization_id', $this->user()->organization_id)
                    ->whereNull('deleted_at'),
            ],

            // 📊 KILOMÉTRAGE (Required)
            'mileage' => [
                'required',
                'integer',
                'min:0',
                'max:9999999',
                function ($attribute, $value, $fail) {
                    if (!$this->vehicle_id) {
                        return;
                    }

                    $vehicle = Vehicle::find($this->vehicle_id);

                    if (!$vehicle) {
                        return;
                    }

                    // RÈGLE MÉTIER: Nouveau kilométrage doit être >= current_mileage du véhicule
                    if ($value < $vehicle->current_mileage) {
                        $fail("Le kilométrage ({$value} km) ne peut pas être inférieur au kilométrage actuel du véhicule ({$vehicle->current_mileage} km).");
                    }
                },
            ],

            // 📅 DATE D'ENREGISTREMENT (Required)
            'recorded_at' => [
                'required',
                'date',
                'before_or_equal:now',
                'after_or_equal:' . now()->subYears(1)->toDateString(), // Max 1 an dans le passé
            ],

            // 🔧 MÉTHODE D'ENREGISTREMENT (Required)
            'recording_method' => [
                'required',
                'string',
                Rule::in([
                    VehicleMileageReading::METHOD_MANUAL,
                    VehicleMileageReading::METHOD_AUTOMATIC,
                ]),
            ],

            // 📝 NOTES (Optional)
            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Vehicle
            'vehicle_id.required' => 'Le véhicule est obligatoire.',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas dans votre organisation.',

            // Mileage
            'mileage.required' => 'Le kilométrage est obligatoire.',
            'mileage.integer' => 'Le kilométrage doit être un nombre entier.',
            'mileage.min' => 'Le kilométrage ne peut pas être négatif.',
            'mileage.max' => 'Le kilométrage ne peut pas dépasser 9 999 999 km.',

            // Recorded At
            'recorded_at.required' => 'La date d\'enregistrement est obligatoire.',
            'recorded_at.date' => 'La date d\'enregistrement doit être une date valide.',
            'recorded_at.before_or_equal' => 'La date d\'enregistrement ne peut pas être dans le futur.',
            'recorded_at.after_or_equal' => 'La date d\'enregistrement ne peut pas dépasser 1 an dans le passé.',

            // Recording Method
            'recording_method.required' => 'La méthode d\'enregistrement est obligatoire.',
            'recording_method.in' => 'La méthode d\'enregistrement doit être "manual" ou "automatic".',

            // Notes
            'notes.max' => 'Les notes ne peuvent pas dépasser :max caractères.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'vehicle_id' => 'véhicule',
            'mileage' => 'kilométrage',
            'recorded_at' => 'date d\'enregistrement',
            'recording_method' => 'méthode d\'enregistrement',
            'notes' => 'notes',
        ];
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Set recorded_by_id to authenticated user for manual readings
        if ($this->recording_method === VehicleMileageReading::METHOD_MANUAL) {
            $this->merge([
                'recorded_by_id' => $this->user()->id,
            ]);
        }

        // Set organization_id from user
        $this->merge([
            'organization_id' => $this->user()->organization_id,
        ]);
    }
}
