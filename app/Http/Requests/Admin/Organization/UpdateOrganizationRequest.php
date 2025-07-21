<?php
namespace App\Http\Requests\Admin\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateOrganizationRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('edit organizations'); }
    public function rules(): array {
        $organizationId = $this->route('organization')->id;
        return [ 'name' => ['required','string','max:255', Rule::unique('organizations')->ignore($organizationId)] ];
    }
}
