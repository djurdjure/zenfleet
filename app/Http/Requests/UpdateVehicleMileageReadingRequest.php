<?php

namespace App\Http\Requests;

use App\Models\VehicleMileageReading;
use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateVehicleMileageReadingRequest - Validation for updating mileage readings
 *
 * Validates:
 * - Optional: mileage, recorded_at, notes
 * - Business rules:
 *   - Automatic readings cannot be updated (only admins)
 *   - Drivers can only update within 24h
 *   - New mileage must be valid
 *
 * @version 1.0-Enterprise
 */
class UpdateVehicleMileageReadingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $reading = $this->route('mileage_reading');

        // Check if user can update this reading via Policy
        return $this->user()->can('update', $reading);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $reading = $this->route('mileage_reading');

        return [
            // 📊 KILOMÉTRAGE (Optional for update)
            'mileage' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
                'max:9999999',
                function ($attribute, $value, $fail) use ($reading) {
                    $vehicle = $reading->vehicle;

                    // Si on modifie le kilométrage, valider la cohérence
                    if ($value != $reading->mileage) {
                        // Vérifier qu'il n'y a pas de relevé plus récent avec un kilométrage supérieur
                        $newerHigherReading = VehicleMileageReading::where('vehicle_id', $vehicle->id)
                            ->where('recorded_at', '>', $reading->recorded_at)
                            ->where('mileage', '>', $value)
                            ->exists();

                        if ($newerHigherReading) {
                            $fail("Le kilométrage ({$value} km) ne peut pas être modifié car un relevé ultérieur avec un kilométrage supérieur existe déjà.");
                        }

                        // Vérifier qu'il n'y a pas de relevé antérieur avec un kilométrage inférieur
                        $olderLowerReading = VehicleMileageReading::where('vehicle_id', $vehicle->id)
                            ->where('recorded_at', '<', $reading->recorded_at)
                            ->where('mileage', '>', $value)
                            ->exists();

                        if ($olderLowerReading) {
                            $fail("Le kilométrage ({$value} km) ne peut pas être modifié car il serait inférieur à un relevé antérieur.");
                        }
                    }
                },
            ],

            // 📅 DATE D'ENREGISTREMENT (Optional for update)
            'recorded_at' => [
                'sometimes',
                'required',
                'date',
                'before_or_equal:now',
                'after_or_equal:' . now()->subYears(1)->toDateString(),
            ],

            // 🔧 MÉTHODE D'ENREGISTREMENT (Optional for update)
            'recording_method' => [
                'sometimes',
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
            'mileage' => 'kilométrage',
            'recorded_at' => 'date d\'enregistrement',
            'recording_method' => 'méthode d\'enregistrement',
            'notes' => 'notes',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $reading = $this->route('mileage_reading');

        // SÉCURITÉ: Prevent updating automatic readings (unless admin)
        if ($reading->is_automatic && !$this->user()->can('mileage-readings.manage.automatic')) {
            abort(403, 'Les relevés automatiques ne peuvent pas être modifiés manuellement.');
        }
    }
}
