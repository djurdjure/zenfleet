<?php

namespace App\Http\Requests;

use App\Enums\VehicleStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * 🚗 CHANGE VEHICLE STATUS REQUEST - Enterprise-Grade Validation
 *
 * Form Request pour valider les changements de statut de véhicules.
 *
 * Validation:
 * - Statut valide (Enum)
 * - Raison requise pour certains statuts
 * - Métadonnées optionnelles (JSON)
 * - Permissions utilisateur
 *
 * @version 2.0-Enterprise
 */
class ChangeVehicleStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Vérifier que l'utilisateur a la permission de changer le statut des véhicules
        return $this->user()->can('update-vehicle-status');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(VehicleStatusEnum::class)],
            'reason' => ['nullable', 'string', 'max:1000'],
            'metadata' => ['nullable', 'array'],
            'metadata.*' => ['nullable'],
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
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $status = VehicleStatusEnum::tryFrom($this->input('status'));

            if (!$status) {
                return;
            }

            // Règle métier : Raison obligatoire pour certains statuts
            if ($this->requiresReason($status) && empty($this->input('reason'))) {
                $validator->errors()->add(
                    'reason',
                    "Une raison est obligatoire pour passer en statut '{$status->label()}'."
                );
            }

            // Règle métier : Vérifier les métadonnées requises pour certains statuts
            if ($status === VehicleStatusEnum::REFORME && empty($this->input('metadata.reform_reason'))) {
                $validator->errors()->add(
                    'metadata.reform_reason',
                    "La raison de réforme est obligatoire."
                );
            }
        });
    }

    /**
     * Détermine si une raison est requise pour ce statut
     */
    protected function requiresReason(VehicleStatusEnum $status): bool
    {
        return in_array($status, [
            VehicleStatusEnum::EN_PANNE,
            VehicleStatusEnum::EN_MAINTENANCE,
            VehicleStatusEnum::REFORME,
        ]);
    }

    /**
     * Get the validated status as an Enum
     */
    public function getStatusEnum(): VehicleStatusEnum
    {
        return VehicleStatusEnum::from($this->validated('status'));
    }
}
