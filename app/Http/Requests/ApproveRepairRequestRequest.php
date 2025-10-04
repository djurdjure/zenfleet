<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ApproveRepairRequestRequest - Validation for approving repair requests
 *
 * Used for both:
 * - Supervisor approval (level 1)
 * - Fleet Manager approval (level 2)
 *
 * Validates:
 * - Optional comment field
 *
 * @version 1.0-Enterprise
 */
class ApproveRepairRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by the Policy in the controller
        // This just checks that user is authenticated
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 💬 COMMENTAIRE OPTIONNEL
            'comment' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'comment.string' => 'Le commentaire doit être une chaîne de caractères.',
            'comment.max' => 'Le commentaire ne peut pas dépasser :max caractères.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'comment' => 'commentaire',
        ];
    }
}
