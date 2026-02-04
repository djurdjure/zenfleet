<?php

namespace App\Http\Requests\Admin\Driver;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDriverRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return $this->user()->can('drivers.create');
    }

    /**
     * Récupère les règles de validation qui s'appliquent à la requête.
     */
    public function rules(): array
    {
        $organizationId = $this->user()->organization_id;

        return [
            // 🔒 SECURITY: Organization ID (ajouté automatiquement via prepareForValidation)
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],

            // Étape 1
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'personal_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'personal_email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('drivers')
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
            ],

            // Étape 2
            'employee_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('drivers')
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
            ],
            'recruitment_date' => ['nullable', 'date'],
            'contract_end_date' => ['nullable', 'date', 'after_or_equal:recruitment_date'],
            'status_id' => ['required', 'exists:driver_statuses,id'],
            'user_id' => ['nullable', 'sometimes', 'exists:users,id', Rule::unique('drivers')->whereNull('deleted_at')],
            'notes' => ['nullable', 'string', 'max:5000'],

            // Étape 3
            'license_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('drivers')
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
            ],
            'license_categories' => ['nullable', 'array'],
            'license_categories.*' => ['nullable', 'string', 'in:A1,A,B,BE,C1,C1E,C,CE,D,DE,F'],
            'license_issue_date' => ['nullable', 'date'],
            'license_expiry_date' => ['nullable', 'date'],
            'license_authority' => ['nullable', 'string', 'max:255'],
            'license_verified' => ['nullable', 'boolean'],

            // Étape 4
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * 🔧 CORRECTION ENTERPRISE: Ajouter organization_id automatiquement
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'organization_id' => $this->user()->organization_id,
        ]);
    }
}
