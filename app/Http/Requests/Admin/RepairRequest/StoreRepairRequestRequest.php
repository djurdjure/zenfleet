<?php

namespace App\Http\Requests\Admin\RepairRequest;

use App\Models\RepairRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreRepairRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', RepairRequest::class);
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => [
                'required',
                'exists:vehicles,id',
                function ($attribute, $value, $fail) {
                    // Vérifier que le véhicule appartient à l'organisation de l'utilisateur
                    $vehicle = \App\Models\Vehicle::find($value);
                    if (!$vehicle || $vehicle->organization_id !== $this->user()->organization_id) {
                        $fail('Le véhicule sélectionné n\'est pas valide.');
                    }
                }
            ],
            'priority' => [
                'required',
                'in:' . implode(',', [
                    RepairRequest::PRIORITY_URGENT,
                    RepairRequest::PRIORITY_SCHEDULED,
                    RepairRequest::PRIORITY_NON_URGENT
                ])
            ],
            'description' => 'required|string|min:10|max:2000',
            'location_description' => 'nullable|string|max:500',
            'estimated_cost' => 'nullable|numeric|min:0|max:999999.99',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max par image
            'attachments' => 'nullable|array|max:3',
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,txt|max:10240' // 10MB max par fichier
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'Vous devez sélectionner un véhicule.',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            'priority.required' => 'Vous devez sélectionner une priorité.',
            'priority.in' => 'La priorité sélectionnée n\'est pas valide.',
            'description.required' => 'La description de la réparation est obligatoire.',
            'description.min' => 'La description doit contenir au moins 10 caractères.',
            'description.max' => 'La description ne peut pas dépasser 2000 caractères.',
            'location_description.max' => 'La localisation ne peut pas dépasser 500 caractères.',
            'estimated_cost.numeric' => 'Le coût estimé doit être un nombre.',
            'estimated_cost.min' => 'Le coût estimé ne peut pas être négatif.',
            'estimated_cost.max' => 'Le coût estimé ne peut pas dépasser 999,999.99 DA.',
            'photos.max' => 'Vous ne pouvez pas télécharger plus de 5 photos.',
            'photos.*.image' => 'Tous les fichiers photos doivent être des images.',
            'photos.*.mimes' => 'Les photos doivent être au format JPEG, PNG, JPG, GIF ou WebP.',
            'photos.*.max' => 'Chaque photo ne peut pas dépasser 5 MB.',
            'attachments.max' => 'Vous ne pouvez pas télécharger plus de 3 pièces jointes.',
            'attachments.*.file' => 'Les pièces jointes doivent être des fichiers valides.',
            'attachments.*.mimes' => 'Les pièces jointes doivent être au format PDF, DOC, DOCX, XLS, XLSX ou TXT.',
            'attachments.*.max' => 'Chaque pièce jointe ne peut pas dépasser 10 MB.'
        ];
    }

    public function attributes(): array
    {
        return [
            'vehicle_id' => 'véhicule',
            'priority' => 'priorité',
            'description' => 'description',
            'location_description' => 'localisation',
            'estimated_cost' => 'coût estimé',
            'photos' => 'photos',
            'attachments' => 'pièces jointes'
        ];
    }

    protected function prepareForValidation(): void
    {
        // Nettoyer les données avant validation
        if ($this->has('estimated_cost') && $this->estimated_cost === '') {
            $this->merge(['estimated_cost' => null]);
        }

        if ($this->has('location_description')) {
            $this->merge([
                'location_description' => trim($this->location_description)
            ]);
        }

        if ($this->has('description')) {
            $this->merge([
                'description' => trim($this->description)
            ]);
        }
    }
}