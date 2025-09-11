<?php
// app/Http/Requests/Admin/StoreOrganizationRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Organization::class);
    }

    public function rules(): array
    {
        return [
            // Général
            'name' => ['required', 'string', 'max:255', 'unique:organizations,name'],
            'legal_name' => ['required', 'string', 'max:255'],
            'organization_type' => ['required', 'in:enterprise,sme,startup,public,ngo,cooperative'],
            'industry' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            
            // Légal
            'siret' => ['nullable', 'string', 'size:14', 'regex:/^[0-9]{14}$/', 'unique:organizations,siret'],
            'vat_number' => ['nullable', 'string', 'max:20', 'unique:organizations,vat_number'],
            
            // Contact
            'email' => ['required', 'email', 'max:255', 'unique:organizations,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            
            // Adresse
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'size:2'],
            
            // Paramètres
            'timezone' => ['required', 'string', 'max:50'],
            'currency' => ['required', 'string', 'size:3'],
            'language' => ['required', 'string', 'max:5'],
            'max_users' => ['required', 'integer', 'min:1', 'max:1000'],
            'max_vehicles' => ['required', 'integer', 'min:1', 'max:10000'],
            'max_drivers' => ['required', 'integer', 'min:1', 'max:5000'],
            
            // Logo
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg', 'max:2048'],
            
            // Admin
            'admin_first_name' => ['required', 'string', 'max:100'],
            'admin_last_name' => ['required', 'string', 'max:100'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_phone' => ['nullable', 'string', 'max:20']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'organisation est obligatoire.',
            'name.unique' => 'Ce nom d\'organisation existe déjà.',
            'siret.regex' => 'Le SIRET doit contenir exactement 14 chiffres.',
            'admin_email.unique' => 'Cette adresse email est déjà utilisée.'
        ];
    }
}

