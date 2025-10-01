<?php

namespace App\Http\Requests\Admin\Assignment;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // L'utilisateur doit avoir la permission de créer des affectations.
        return $this->user()->can('create assignments');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['required', 'exists:drivers,id'],

            // Nouveaux champs séparés pour date/heure
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'start_mileage' => ['required', 'integer', 'min:0'],

            // Type d'affectation
            'assignment_type' => ['required', 'in:open,scheduled'],

            // Champs conditionnels pour affectation programmée
            'end_date' => ['nullable', 'date', 'after:start_date', 'required_if:assignment_type,scheduled'],
            'end_time' => ['nullable', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', 'required_if:assignment_type,scheduled'],
            'estimated_end_mileage' => ['nullable', 'integer', 'min:0', 'gt:start_mileage'],

            // Informations complémentaires
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'start_date.required' => 'La date de début est obligatoire.',
            'start_date.after_or_equal' => 'La date de début ne peut pas être antérieure à aujourd\'hui.',
            'start_time.required' => 'L\'heure de début est obligatoire.',
            'start_time.regex' => 'Le format de l\'heure doit être HH:MM.',
            'assignment_type.required' => 'Le type d\'affectation est obligatoire.',
            'assignment_type.in' => 'Le type d\'affectation doit être "ouverte" ou "programmée".',
            'end_date.required_if' => 'La date de fin est obligatoire pour une affectation programmée.',
            'end_date.after' => 'La date de fin doit être postérieure à la date de début.',
            'end_time.required_if' => 'L\'heure de fin est obligatoire pour une affectation programmée.',
            'end_time.regex' => 'Le format de l\'heure de fin doit être HH:MM.',
            'estimated_end_mileage.gt' => 'Le kilométrage de fin doit être supérieur au kilométrage de début.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Combiner date et heure pour créer start_datetime pour compatibilité
        if ($this->has('start_date') && $this->has('start_time')) {
            $this->merge([
                'start_datetime' => $this->start_date . ' ' . $this->start_time,
            ]);
        }

        // Combiner date et heure de fin si présentes
        if ($this->has('end_date') && $this->has('end_time') && $this->end_date && $this->end_time) {
            $this->merge([
                'end_datetime' => $this->end_date . ' ' . $this->end_time,
            ]);
        }
    }
}
