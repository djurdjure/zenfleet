<?php

namespace App\Http\Requests;

use App\Enums\DriverStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * 👤 CHANGE DRIVER STATUS REQUEST - Enterprise-Grade Validation
 *
 * Form Request pour valider les changements de statut de chauffeurs.
 *
 * Validation:
 * - Statut valide (Enum)
 * - Raison requise pour certains statuts
 * - Métadonnées optionnelles (JSON)
 * - Permissions utilisateur
 *
 * @version 2.0-Enterprise
 */
class ChangeDriverStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Vérifier que l'utilisateur a la permission de changer le statut des chauffeurs
        return $this->user()->can('drivers.status.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(DriverStatusEnum::class)],
            'reason' => ['nullable', 'string', 'max:1000'],
            'metadata' => ['nullable', 'array'],
            'metadata.*' => ['nullable'],
            'metadata.leave_type' => ['nullable', 'string', 'in:annual,sick,maternity,paternity,unpaid,exceptional'],
            'metadata.leave_start_date' => ['nullable', 'date'],
            'metadata.leave_end_date' => ['nullable', 'date', 'after_or_equal:metadata.leave_start_date'],
            'metadata.other_reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Le statut est obligatoire.',
            'status.enum' => 'Le statut sélectionné est invalide.',
            'reason.max' => 'La raison ne peut pas dépasser 1000 caractères.',
            'metadata.array' => 'Les métadonnées doivent être un tableau.',
            'metadata.leave_type.in' => 'Le type de congé sélectionné est invalide.',
            'metadata.leave_end_date.after_or_equal' => 'La date de fin de congé doit être postérieure ou égale à la date de début.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'status' => 'statut',
            'reason' => 'raison',
            'metadata' => 'métadonnées',
            'metadata.leave_type' => 'type de congé',
            'metadata.leave_start_date' => 'date de début de congé',
            'metadata.leave_end_date' => 'date de fin de congé',
            'metadata.other_reason' => 'raison spécifique',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $status = DriverStatusEnum::tryFrom($this->input('status'));

            if (!$status) {
                return;
            }

            // Règle métier : Raison obligatoire pour statut AUTRE
            if ($status === DriverStatusEnum::AUTRE && empty($this->input('reason'))) {
                $validator->errors()->add(
                    'reason',
                    "Une raison est obligatoire pour le statut 'Autre'."
                );
            }

            // Règle métier : Métadonnées de congé obligatoires pour EN_CONGE
            if ($status === DriverStatusEnum::EN_CONGE) {
                if (empty($this->input('metadata.leave_type'))) {
                    $validator->errors()->add(
                        'metadata.leave_type',
                        "Le type de congé est obligatoire."
                    );
                }

                if (empty($this->input('metadata.leave_start_date'))) {
                    $validator->errors()->add(
                        'metadata.leave_start_date',
                        "La date de début de congé est obligatoire."
                    );
                }

                if (empty($this->input('metadata.leave_end_date'))) {
                    $validator->errors()->add(
                        'metadata.leave_end_date',
                        "La date de fin de congé est obligatoire."
                    );
                }
            }
        });
    }

    /**
     * Get the validated status as an Enum
     */
    public function getStatusEnum(): DriverStatusEnum
    {
        return DriverStatusEnum::from($this->validated('status'));
    }
}
