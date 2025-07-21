<?php
namespace App\Http\Requests\Admin\Organization;
use Illuminate\Foundation\Http\FormRequest;
class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('create organizations'); }
    public function rules(): array {
        return [ 'name' => 'required|string|max:255|unique:organizations,name' ];
    }
}
