<?php

namespace App\Http\Requests\Admin\Supplier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return $this->user()->can('suppliers.update');
    }

    /**
     * Obtient les règles de validation qui s'appliquent à la requête.
     */
    public function rules(): array
    {
        $supplierId = $this->route('supplier')->id;

        return [
            // Informations générales
            'company_name' => ['required', 'string', 'max:255'],
            'supplier_type' => ['required', 'string', 'max:255'],
            'trade_register' => ['nullable', 'string', 'regex:/^[0-9]{2}\/[0-9]{2}-[0-9]{2}[A-Z][0-9]{7}$/'],
            'nif' => ['nullable', 'string', 'size:15', 'regex:/^[0-9]{15}$/'],
            'nis' => ['nullable', 'string', 'max:255'],
            'ai' => ['nullable', 'string', 'max:255'],

            // Contact principal
            'contact_first_name' => ['required', 'string', 'max:255'],
            'contact_last_name' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'regex:/^[\+\d\s\(\)-]{8,20}$/'],
            'contact_email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'contact_email')->ignore($supplierId)],
            'phone' => ['nullable', 'string', 'regex:/^[\+\d\s\(\)-]{8,20}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],

            // Localisation
            'address' => ['required', 'string'],
            'wilaya' => ['required', 'string', 'max:2'],
            'city' => ['required', 'string', 'max:255'],
            'commune' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],

            // Paramètres
            'rating' => ['nullable', 'numeric', 'between:0,5'],
            'quality_score' => ['nullable', 'numeric', 'between:0,100'],
            'reliability_score' => ['nullable', 'numeric', 'between:0,100'],
            'is_active' => ['nullable', 'boolean'],
            'is_preferred' => ['nullable', 'boolean'],
            'is_certified' => ['nullable', 'boolean'],
            'blacklisted' => ['nullable', 'boolean'],
            'blacklist_reason' => ['nullable', 'string', 'required_if:blacklisted,1'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    public function messages(): array
    {
        return [
            'trade_register.regex' => 'Le registre du commerce doit respecter le format algérien: XX/XX-XXAXXXXXXX ou XX/XX-XXBXXXXXXX (ex: 16/00-23A1234567)',
            'nif.regex' => 'Le NIF doit contenir exactement 15 chiffres',
            'nif.size' => 'Le NIF doit contenir exactement 15 chiffres',
            'contact_phone.regex' => 'Le format du numéro de téléphone est invalide',
            'phone.regex' => 'Le format du numéro de téléphone est invalide',
            'rating.between' => 'Le rating doit être entre 0 et 5',
            'quality_score.between' => 'Le score qualité doit être entre 0 et 100',
            'reliability_score.between' => 'Le score fiabilité doit être entre 0 et 100',
            'blacklist_reason.required_if' => 'Veuillez indiquer la raison de la mise en liste noire',
        ];
    }

    /**
     * Préparer les données avant validation
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer le registre de commerce (supprimer espaces)
        if ($this->has('trade_register')) {
            $this->merge([
                'trade_register' => $this->trade_register ? trim($this->trade_register) : null,
            ]);
        }

        // Nettoyer le NIF (supprimer espaces)
        if ($this->has('nif')) {
            $this->merge([
                'nif' => $this->nif ? preg_replace('/\s+/', '', $this->nif) : null,
            ]);
        }

        // Convertir les checkboxes en booléens
        $this->merge([
            'is_active' => $this->has('is_active') ? (bool) $this->is_active : false,
            'is_preferred' => $this->has('is_preferred') ? (bool) $this->is_preferred : false,
            'is_certified' => $this->has('is_certified') ? (bool) $this->is_certified : false,
            'blacklisted' => $this->has('blacklisted') ? (bool) $this->blacklisted : false,
        ]);
    }
}