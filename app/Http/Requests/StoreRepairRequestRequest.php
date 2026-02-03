<?php

namespace App\Http\Requests;

use App\Models\RepairRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StoreRepairRequestRequest - Validation for creating repair requests
 *
 * Validates:
 * - Required fields: driver_id, vehicle_id, title, description
 * - Optional: urgency, current_mileage, current_location, estimated_cost
 * - File uploads: photos[], attachments[]
 * - Multi-tenant isolation via driver.organization_id
 *
 * @version 1.0-Enterprise
 */
class StoreRepairRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('repair-requests.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 🔑 RELATIONS REQUISES
            'driver_id' => [
                'required',
                'integer',
                Rule::exists('drivers', 'id')
                    ->where('organization_id', $this->user()->organization_id)
                    ->whereNull('deleted_at'),
            ],
            'vehicle_id' => [
                'required',
                'integer',
                Rule::exists('vehicles', 'id')
                    ->where('organization_id', $this->user()->organization_id)
                    ->whereNull('deleted_at'),
            ],

            // 📝 INFORMATIONS DE BASE
            'title' => [
                'required',
                'string',
                'max:255',
                'min:5',
            ],
            'description' => [
                'required',
                'string',
                'min:20',
                'max:5000',
            ],

            // 🚨 URGENCE
            'urgency' => [
                'nullable',
                'string',
                Rule::in([
                    RepairRequest::URGENCY_LOW,
                    RepairRequest::URGENCY_NORMAL,
                    RepairRequest::URGENCY_HIGH,
                    RepairRequest::URGENCY_CRITICAL,
                ]),
            ],

            // 📊 INFORMATIONS VÉHICULE
            'current_mileage' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999999',
            ],
            'current_location' => [
                'nullable',
                'string',
                'max:255',
            ],

            // 💰 ESTIMATION FINANCIÈRE
            'estimated_cost' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            // 📸 FICHIERS - PHOTOS
            'photos' => [
                'nullable',
                'array',
                'max:10', // Maximum 10 photos
            ],
            'photos.*' => [
                'file',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:5120', // 5MB max per photo
            ],

            // 📎 FICHIERS - ATTACHMENTS
            'attachments' => [
                'nullable',
                'array',
                'max:5', // Maximum 5 attachments
            ],
            'attachments.*' => [
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,txt',
                'max:10240', // 10MB max per attachment
            ],

            // 📌 NOTES ADDITIONNELLES
            'notes' => [
                'nullable',
                'string',
                'max:2000',
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
            // Driver
            'driver_id.required' => 'Le chauffeur est obligatoire.',
            'driver_id.exists' => 'Le chauffeur sélectionné n\'existe pas dans votre organisation.',

            // Vehicle
            'vehicle_id.required' => 'Le véhicule est obligatoire.',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas dans votre organisation.',

            // Title
            'title.required' => 'Le titre est obligatoire.',
            'title.min' => 'Le titre doit contenir au moins :min caractères.',
            'title.max' => 'Le titre ne peut pas dépasser :max caractères.',

            // Description
            'description.required' => 'La description est obligatoire.',
            'description.min' => 'La description doit contenir au moins :min caractères pour être claire.',
            'description.max' => 'La description ne peut pas dépasser :max caractères.',

            // Urgency
            'urgency.in' => 'Le niveau d\'urgence sélectionné est invalide.',

            // Mileage
            'current_mileage.integer' => 'Le kilométrage doit être un nombre entier.',
            'current_mileage.min' => 'Le kilométrage ne peut pas être négatif.',

            // Cost
            'estimated_cost.numeric' => 'Le coût estimé doit être un nombre.',
            'estimated_cost.min' => 'Le coût estimé ne peut pas être négatif.',

            // Photos
            'photos.max' => 'Vous ne pouvez pas télécharger plus de :max photos.',
            'photos.*.image' => 'Le fichier doit être une image.',
            'photos.*.mimes' => 'Les photos doivent être au format JPEG, JPG, PNG ou WEBP.',
            'photos.*.max' => 'Chaque photo ne peut pas dépasser 5MB.',

            // Attachments
            'attachments.max' => 'Vous ne pouvez pas télécharger plus de :max pièces jointes.',
            'attachments.*.mimes' => 'Les pièces jointes doivent être au format PDF, DOC, DOCX, XLS, XLSX ou TXT.',
            'attachments.*.max' => 'Chaque pièce jointe ne peut pas dépasser 10MB.',
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
            'driver_id' => 'chauffeur',
            'vehicle_id' => 'véhicule',
            'title' => 'titre',
            'description' => 'description',
            'urgency' => 'urgence',
            'current_mileage' => 'kilométrage actuel',
            'current_location' => 'localisation actuelle',
            'estimated_cost' => 'coût estimé',
            'photos' => 'photos',
            'attachments' => 'pièces jointes',
            'notes' => 'notes',
        ];
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Set default urgency if not provided
        if (!$this->has('urgency')) {
            $this->merge([
                'urgency' => RepairRequest::URGENCY_NORMAL,
            ]);
        }
    }
}
