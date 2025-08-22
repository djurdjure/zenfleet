<?php

namespace App\Http\Requests\Admin\Assignment;

use Illuminate\Foundation\Http\FormRequest;

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
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['nullable', 'date', 'after_or_equal:start_datetime'],
            'start_mileage' => ['nullable', 'integer', 'min:0'], // Rendu nullable car non présent dans le nouveau formulaire
            'reason' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Combiner date et heure de début
        if ($this->start_date && $this->start_time) {
            $this->merge([
                'start_datetime' => $this->start_date . ' ' . $this->start_time,
            ]);
        }

        // Combiner date et heure de fin si elles existent
        if ($this->end_date && $this->end_time) {
            $this->merge([
                'end_datetime' => $this->end_date . ' ' . $this->end_time,
            ]);
        }
    }
}
