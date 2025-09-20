<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrganizationAlgeriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Organization::class);
    }

    public function rules(): array
    {
        return [
            // Informations générales
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:organizations,name'
            ],
            'legal_name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'organization_type' => [
                'required',
                Rule::in(['enterprise', 'sme', 'startup', 'public', 'ngo', 'cooperative'])
            ],
            'industry' => [
                'nullable',
                'string',
                'max:100'
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'website' => [
                'nullable',
                'url',
                'max:255'
            ],
            'phone_number' => [
                'required',
                'string',
                'max:20',
                'regex:/^(\+213|0)[0-9]{8,9}$/' // Algeria phone format
            ],
            'primary_email' => [
                'required',
                'email',
                'max:255',
                'unique:organizations,primary_email'
            ],
            'status' => [
                'nullable',
                Rule::in(['active', 'inactive', 'suspended'])
            ],

            // Informations légales Algeria
            'trade_register' => [
                'required',
                'string',
                'max:255',
                'unique:organizations,trade_register'
            ],
            'nif' => [
                'required',
                'string',
                'max:20',
                'unique:organizations,nif',
                'regex:/^[0-9]+$/' // NIF is numeric in Algeria
            ],
            'ai' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9]+$/' // AI is numeric in Algeria
            ],
            'nis' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9]+$/' // NIS is numeric in Algeria
            ],

            // Adresse Algeria
            'address' => [
                'required',
                'string',
                'max:255'
            ],
            'city' => [
                'required',
                'string',
                'max:100'
            ],
            'commune' => [
                'nullable',
                'string',
                'max:100'
            ],
            'zip_code' => [
                'nullable',
                'string',
                'max:5',
                'regex:/^[0-9]{5}$/' // Algeria postal codes are 5 digits
            ],
            'wilaya' => [
                'required',
                'string',
                'size:2',
                'exists:algeria_wilayas,code'
            ],

            // Représentant légal
            'manager_first_name' => [
                'required',
                'string',
                'max:100'
            ],
            'manager_last_name' => [
                'required',
                'string',
                'max:100'
            ],
            'manager_nin' => [
                'required',
                'string',
                'size:18',
                'regex:/^[0-9]{18}$/', // Algeria NIN is 18 digits
                'unique:organizations,manager_nin'
            ],
            'manager_address' => [
                'nullable',
                'string',
                'max:255'
            ],
            'manager_dob' => [
                'nullable',
                'date',
                'before:today',
                'after:1930-01-01'
            ],
            'manager_pob' => [
                'nullable',
                'string',
                'max:100'
            ],
            'manager_phone_number' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(\+213|0)[0-9]{8,9}$/' // Algeria phone format
            ],

            // Documents (file uploads)
            'scan_nif' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048' // 2MB max
            ],
            'scan_ai' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048'
            ],
            'scan_nis' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048'
            ],
            'manager_id_scan' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048'
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,svg',
                'max:1024' // 1MB max for logo
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'organisation est obligatoire.',
            'name.unique' => 'Ce nom d\'organisation existe déjà.',
            'primary_email.unique' => 'Cette adresse email est déjà utilisée.',
            'phone_number.regex' => 'Le numéro de téléphone doit être au format Algeria (+213 ou 0).',
            'nif.required' => 'Le NIF est obligatoire.',
            'nif.unique' => 'Ce NIF existe déjà.',
            'nif.regex' => 'Le NIF doit contenir uniquement des chiffres.',
            'ai.regex' => 'L\'AI doit contenir uniquement des chiffres.',
            'nis.regex' => 'Le NIS doit contenir uniquement des chiffres.',
            'wilaya.required' => 'La wilaya est obligatoire.',
            'wilaya.exists' => 'Cette wilaya n\'existe pas.',
            'zip_code.regex' => 'Le code postal doit contenir exactement 5 chiffres.',
            'manager_nin.required' => 'Le NIN du gérant est obligatoire.',
            'manager_nin.size' => 'Le NIN doit contenir exactement 18 chiffres.',
            'manager_nin.regex' => 'Le NIN doit contenir uniquement des chiffres.',
            'manager_nin.unique' => 'Ce NIN de gérant existe déjà.',
            'manager_dob.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'manager_phone_number.regex' => 'Le téléphone du gérant doit être au format Algeria.',
            'trade_register.unique' => 'Ce registre de commerce existe déjà.',
        ];
    }

    public function prepareForValidation(): void
    {
        // Normalize phone numbers
        if ($this->has('phone_number')) {
            $phone = $this->input('phone_number');
            $phone = preg_replace('/[^0-9+]/', '', $phone);
            if (str_starts_with($phone, '0')) {
                $phone = '+213' . substr($phone, 1);
            }
            $this->merge(['phone_number' => $phone]);
        }

        if ($this->has('manager_phone_number')) {
            $phone = $this->input('manager_phone_number');
            $phone = preg_replace('/[^0-9+]/', '', $phone);
            if (str_starts_with($phone, '0')) {
                $phone = '+213' . substr($phone, 1);
            }
            $this->merge(['manager_phone_number' => $phone]);
        }

        // Normalize NIN/NIF/AI/NIS (remove spaces, dashes)
        foreach (['manager_nin', 'nif', 'ai', 'nis'] as $field) {
            if ($this->has($field)) {
                $value = preg_replace('/[^0-9]/', '', $this->input($field));
                $this->merge([$field => $value]);
            }
        }
    }
}