<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * RejectRepairRequestRequest - Validation for rejecting repair requests
 *
 * Used for both:
 * - Supervisor rejection (level 1)
 * - Fleet Manager rejection (level 2)
 *
 * Validates:
 * - MANDATORY reason field (business requirement)
 *
 * @version 1.0-Enterprise
 */
class RejectRepairRequestRequest extends FormRequest
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
            // 🚫 RAISON DE REJET OBLIGATOIRE
            'reason' => [
                'required',
                'string',
                'min:10',
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
            'reason.required' => 'La raison du rejet est obligatoire.',
            'reason.string' => 'La raison doit être une chaîne de caractères.',
            'reason.min' => 'La raison doit contenir au moins :min caractères pour être claire.',
            'reason.max' => 'La raison ne peut pas dépasser :max caractères.',
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
            'reason' => 'raison du rejet',
        ];
    }
}
