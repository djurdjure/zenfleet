<?php

namespace App\Http\Requests\Admin\SupplierCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // We'll rely on the controller's authorization for now
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:supplier_categories,name',
        ];
    }
}