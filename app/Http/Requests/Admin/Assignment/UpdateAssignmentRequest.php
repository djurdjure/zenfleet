<?php

namespace App\Http\Requests\Admin\Assignment;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

/**
 * 📝 UpdateAssignmentRequest - Validation Enterprise-Grade
 *
 * Validation pour la modification d'une affectation existante
 * Support format date français (DD/MM/YYYY)
 * Compatible avec l'architecture multitenant
 *
 * @author ZenFleet Architecture Team
 */
class UpdateAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // L'utilisateur doit avoir la permission de modifier des affectations.
        return $this->user()->can('edit assignments');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['required', 'exists:drivers,id'],

            // 📅 VALIDATION FORMAT EUROPÉEN/FRANÇAIS (DD/MM/YYYY)
            // Enterprise-Grade: Support format date localisé
            // Pour l'update, on autorise les dates passées (correction d'erreur)
            'start_date' => [
                'required',
                'date_format:d/m/Y', // Format français: 19/11/2025
            ],
            'start_time' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'start_mileage' => ['nullable', 'integer', 'min:0'],

            // Type d'affectation
            'assignment_type' => ['required', 'in:open,scheduled'],

            // 📅 Champs conditionnels pour affectation programmée (format français)
            'end_date' => [
                'nullable',
                'date_format:d/m/Y', // Format français: 20/11/2025
                'after:start_date',
                'required_if:assignment_type,scheduled'
            ],
            'end_time' => ['nullable', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', 'required_if:assignment_type,scheduled'],
            'estimated_end_mileage' => ['nullable', 'integer', 'min:0'],

            // Informations complémentaires
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Messages date début
            'start_date.required' => 'La date de début est obligatoire.',
            'start_date.date_format' => 'Le format de la date de début doit être JJ/MM/AAAA (ex: 19/11/2025).',

            // Messages heure
            'start_time.required' => 'L\'heure de début est obligatoire.',
            'start_time.regex' => 'Le format de l\'heure doit être HH:MM (ex: 14:30).',

            // Messages type affectation
            'assignment_type.required' => 'Le type d\'affectation est obligatoire.',
            'assignment_type.in' => 'Le type d\'affectation doit être "ouverte" ou "programmée".',

            // Messages date fin
            'end_date.required_if' => 'La date de fin est obligatoire pour une affectation programmée.',
            'end_date.date_format' => 'Le format de la date de fin doit être JJ/MM/AAAA (ex: 20/11/2025).',
            'end_date.after' => 'La date de fin doit être postérieure à la date de début.',

            // Messages heure fin
            'end_time.required_if' => 'L\'heure de fin est obligatoire pour une affectation programmée.',
            'end_time.regex' => 'Le format de l\'heure de fin doit être HH:MM (ex: 16:30).',

            // Messages kilométrage
            'estimated_end_mileage.integer' => 'Le kilométrage doit être un nombre entier.',
            'estimated_end_mileage.min' => 'Le kilométrage ne peut pas être négatif.',
        ];
    }

    /**
     * 🔄 Préparation Enterprise-Grade des données avant validation
     *
     * Pas de préparation avant validation - validation directe du format français
     */
    protected function prepareForValidation(): void
    {
        // Pas de préparation avant validation - la validation date_format:d/m/Y
        // acceptera le format français directement
    }

    /**
     * 🔄 Traitement APRÈS validation réussie
     *
     * Conversion format français validé → format ISO pour la base de données
     * Cette méthode est automatiquement appelée après la validation
     *
     * @return array
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        // ✅ CONVERSION DATE DÉBUT : DD/MM/YYYY → YYYY-MM-DD
        // Format validé (français) vers format ISO (base de données)
        if (isset($data['start_date']) && isset($data['start_time'])) {
            try {
                // Parser date française et convertir en ISO
                $startDate = Carbon::createFromFormat('d/m/Y', $data['start_date'])->format('Y-m-d');
                $data['start_date'] = $startDate;

                // Créer datetime complet pour le contrôleur
                $data['start_datetime'] = $startDate . ' ' . $data['start_time'];
            } catch (\Exception $e) {
                // Fallback sécurisé (ne devrait jamais arriver après validation)
                \Log::error('Erreur conversion start_date lors update', [
                    'start_date' => $data['start_date'] ?? null,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // ✅ CONVERSION DATE FIN : DD/MM/YYYY → YYYY-MM-DD (si présente)
        if (!empty($data['end_date']) && !empty($data['end_time'])) {
            try {
                // Parser date française et convertir en ISO
                $endDate = Carbon::createFromFormat('d/m/Y', $data['end_date'])->format('Y-m-d');
                $data['end_date'] = $endDate;

                // Créer datetime complet pour le contrôleur
                $data['end_datetime'] = $endDate . ' ' . $data['end_time'];
            } catch (\Exception $e) {
                // Fallback sécurisé
                \Log::error('Erreur conversion end_date lors update', [
                    'end_date' => $data['end_date'] ?? null,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $data;
    }
}
