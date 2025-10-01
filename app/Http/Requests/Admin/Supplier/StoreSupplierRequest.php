<?php

namespace App\Http\Requests\Admin\Supplier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        // Seuls les utilisateurs avec la permission peuvent créer des fournisseurs.
        // Nous créerons cette permission plus tard.
        return $this->user()->can('create suppliers');
    }

    /**
     * Obtient les règles de validation qui s'appliquent à la requête.
     */
    public function rules(): array
    {
        return [
            // Informations générales
            'company_name' => ['required', 'string', 'max:255'],
            'supplier_type' => ['required', 'string', 'max:255'],
            'trade_register' => ['nullable', 'string', 'max:255'],
            'nif' => ['nullable', 'string', 'size:15', 'regex:/^[0-9]{15}$/'],

            // Contact principal
            'contact_first_name' => ['required', 'string', 'max:255'],
            'contact_last_name' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'regex:/^[\+\d\s\(\)-]{8,20}$/'],
            'contact_email' => ['nullable', 'email', 'max:255'],

            // Localisation
            'address' => ['required', 'string'],
            'wilaya' => ['required', 'string', 'max:2'],
            'city' => ['required', 'string', 'max:255'],
            'commune' => ['nullable', 'string', 'max:255'],

            // Paramètres
            'rating' => ['nullable', 'numeric', 'between:0,10'],
            'is_active' => ['nullable', 'boolean'],
            'is_preferred' => ['nullable', 'boolean'],
            'is_certified' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}