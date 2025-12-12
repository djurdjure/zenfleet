<?php

namespace App\Http\Requests\Admin\Driver;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit drivers');
    }

    public function rules(): array
    {
        $driverId = $this->route('driver')->id;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'employee_number' => ['nullable', 'string', 'max:100', Rule::unique('drivers')->ignore($driverId)->whereNull('deleted_at')],
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
            'license_number' => ['nullable', 'string', 'max:100'],
            'license_categories' => ['nullable', 'array'],
            'license_categories.*' => ['nullable', 'string', 'in:A1,A,B,BE,C1,C1E,C,CE,D,DE,F'],
            'license_issue_date' => ['nullable', 'date'],
            'license_expiry_date' => ['nullable', 'date'],
            'license_authority' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
            'personal_email' => ['nullable', 'string', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'license_verified' => ['nullable', 'boolean'],


        ];
    }
}
