<?php

namespace App\Http\Requests\Admin\Driver;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('drivers.update');
    }

    public function rules(): array
    {
        $driverId = $this->route('driver')->id;
        $organizationId = $this->user()->organization_id;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'employee_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('drivers')
                    ->ignore($driverId)
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
            ],
            'user_id' => ['nullable', 'sometimes', 'exists:users,id', Rule::unique('drivers')->ignore($driverId)->whereNull('deleted_at')],
            'status_id' => ['required', 'exists:driver_statuses,id'],
            // ... (toutes les autres règles identiques à StoreDriverRequest)
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'birth_date' => ['nullable', 'date'],
            'personal_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'recruitment_date' => ['nullable', 'date'],
            'contract_end_date' => ['nullable', 'date', 'after_or_equal:recruitment_date'],
            'license_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('drivers')
                    ->ignore($driverId)
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
            ],
            'license_categories' => ['nullable', 'array'],
            'license_categories.*' => ['nullable', 'string', 'in:A1,A,B,BE,C1,C1E,C,CE,D,DE,F'],
            'license_issue_date' => ['nullable', 'date'],
            'license_expiry_date' => ['nullable', 'date'],
            'license_authority' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
            'personal_email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('drivers')
                    ->ignore($driverId)
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
            ],
            'notes' => ['nullable', 'string', 'max:5000'],
            'license_verified' => ['nullable', 'boolean'],


        ];
    }

    protected function prepareForValidation(): void
    {
        $payload = [];

        $issueDate = $this->normalizeDateInput($this->input('license_issue_date'));
        $expiryDate = $this->normalizeDateInput($this->input('license_expiry_date'));

        if ($issueDate) {
            $payload['license_issue_date'] = $issueDate->format('Y-m-d');

            // Business rule: expiry = issue date + 10 years - 1 day unless user explicitly set another value.
            if (!$expiryDate) {
                $payload['license_expiry_date'] = $issueDate->copy()->addYears(10)->subDay()->format('Y-m-d');
            }
        }

        if ($expiryDate) {
            $payload['license_expiry_date'] = $expiryDate->format('Y-m-d');
        }

        if (!empty($payload)) {
            $this->merge($payload);
        }
    }

    private function normalizeDateInput(mixed $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
            }

            if (is_string($value) && preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
            }

            return Carbon::parse((string) $value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
