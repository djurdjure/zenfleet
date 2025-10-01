<?php

namespace App\Http\Requests\Admin\RepairRequest;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // L'autorisation est gérée dans le contrôleur
    }

    public function rules(): array
    {
        return [
            'comments' => 'nullable|string|max:1000',
            'action' => 'required|in:approve,reject,validate,reject_manager'
        ];
    }

    public function messages(): array
    {
        return [
            'comments.max' => 'Les commentaires ne peuvent pas dépasser 1000 caractères.',
            'action.required' => 'L\'action est requise.',
            'action.in' => 'L\'action spécifiée n\'est pas valide.'
        ];
    }

    public function attributes(): array
    {
        return [
            'comments' => 'commentaires',
            'action' => 'action'
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('comments')) {
            $this->merge([
                'comments' => trim($this->comments)
            ]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Si l'action est un rejet, les commentaires sont obligatoires
            if (in_array($this->action, ['reject', 'reject_manager']) && empty($this->comments)) {
                $validator->errors()->add('comments', 'Les commentaires sont obligatoires pour rejeter une demande.');
            }
        });
    }
}