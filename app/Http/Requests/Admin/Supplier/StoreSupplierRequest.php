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
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')],
            'address' => ['nullable', 'string'],
            'supplier_category_id' => ['nullable', 'exists:supplier_categories,id'],
        ];
    }
}
