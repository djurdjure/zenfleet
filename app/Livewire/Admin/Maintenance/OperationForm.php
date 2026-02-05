<?php

namespace App\Livewire\Admin\Maintenance;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use App\Models\MaintenanceOperation;
use App\Models\MaintenanceSchedule;
use App\Models\Supplier;
use App\Models\MaintenanceType;
use App\Models\MaintenanceDocument;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Composant Livewire pour le formulaire d'opération de maintenance
 * Interface dynamique avec upload de documents et calcul automatique des coûts
 */
class OperationForm extends Component
{
    use WithFileUploads;
    use AuthorizesRequests;

    // Propriétés principales
    public ?int $operationId = null;
    public bool $editMode = false;

    // Propriétés du formulaire principal
    #[Validate('required|exists:vehicles,id')]
    public int $vehicle_id = 0;

    #[Validate('required|exists:maintenance_types,id')]
    public int $maintenance_type_id = 0;

    #[Validate('nullable|exists:maintenance_schedules,id')]
    public ?int $maintenance_schedule_id = null;

    #[Validate('nullable|exists:suppliers,id')]
    public ?int $provider_id = null;

    #[Validate('required|in:planned,in_progress,completed,cancelled')]
    public string $status = 'planned';

    #[Validate('nullable|date')]
    public string $scheduled_date = '';

    #[Validate('nullable|date|after_or_equal:scheduled_date')]
    public string $completed_date = '';

    #[Validate('nullable|integer|min:0')]
    public string $mileage_at_maintenance = '';

    #[Validate('nullable|integer|min:1|max:14400')]
    public string $duration_minutes = '';

    #[Validate('nullable|numeric|min:0|max:999999.99')]
    public string $total_cost = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('nullable|string|max:2000')]
    public string $notes = '';

    // Propriétés pour les documents
    public array $uploadedFiles = [];
    public array $existingDocuments = [];
    public array $documentsToDelete = [];

    // Propriétés pour les calculs dynamiques
    public bool $autoCalculateCost = false;
    public array $costBreakdown = [];

    // Propriétés UI
    public string $currentStep = 'basic'; // basic, details, documents, review
    public bool $showAdvanced = false;

    /**
     * Initialisation du composant
     */
    public function mount(?int $operationId = null): void
    {
        $this->operationId = $operationId;
        $this->editMode = $operationId !== null;

        if ($this->editMode) {
            $this->loadOperation();
        } else {
            $this->authorize('create', MaintenanceOperation::class);
            $this->scheduled_date = Carbon::today()->format('Y-m-d');
        }
    }

    /**
     * Computed property pour les véhicules disponibles
     */
    #[Computed]
    public function vehicles()
    {
        return Vehicle::active()
            ->with(['organization'])
            ->orderBy('registration_plate')
            ->get(['id', 'registration_plate', 'brand', 'model', 'current_mileage']);
    }

    /**
     * Computed property pour les types de maintenance
     */
    #[Computed]
    public function maintenanceTypes()
    {
        return MaintenanceType::active()
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    /**
     * Computed property pour les planifications disponibles pour le véhicule
     */
    #[Computed]
    public function availableSchedules()
    {
        if (!$this->vehicle_id) {
            return collect();
        }

        return MaintenanceSchedule::active()
            ->where('vehicle_id', $this->vehicle_id)
            ->with(['maintenanceType'])
            ->orderBy('next_due_date')
            ->get();
    }

    /**
     * Computed property pour les fournisseurs disponibles
     */
    #[Computed]
    public function providers()
    {
        return Supplier::active()
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'rating', 'city']);
    }

    /**
     * Computed property pour le véhicule sélectionné
     */
    #[Computed]
    public function selectedVehicle()
    {
        return $this->vehicle_id ? Vehicle::find($this->vehicle_id) : null;
    }

    /**
     * Computed property pour le type de maintenance sélectionné
     */
    #[Computed]
    public function selectedMaintenanceType()
    {
        return $this->maintenance_type_id ? MaintenanceType::find($this->maintenance_type_id) : null;
    }

    /**
     * Computed property pour vérifier si le formulaire est valide
     */
    #[Computed]
    public function isFormValid()
    {
        return $this->vehicle_id &&
               $this->maintenance_type_id &&
               $this->status &&
               ($this->scheduled_date || $this->status !== 'planned');
    }

    /**
     * Méthode pour charger une opération existante
     */
    private function loadOperation(): void
    {
        $operation = MaintenanceOperation::with(['documents'])->findOrFail($this->operationId);
        $this->authorize('update', $operation);

        $this->vehicle_id = $operation->vehicle_id;
        $this->maintenance_type_id = $operation->maintenance_type_id;
        $this->maintenance_schedule_id = $operation->maintenance_schedule_id;
        $this->provider_id = $operation->provider_id;
        $this->status = $operation->status;
        $this->scheduled_date = $operation->scheduled_date?->format('Y-m-d') ?? '';
        $this->completed_date = $operation->completed_date?->format('Y-m-d') ?? '';
        $this->mileage_at_maintenance = (string) ($operation->mileage_at_maintenance ?? '');
        $this->duration_minutes = (string) ($operation->duration_minutes ?? '');
        $this->total_cost = (string) ($operation->total_cost ?? '');
        $this->description = $operation->description ?? '';
        $this->notes = $operation->notes ?? '';

        $this->existingDocuments = $operation->documents->toArray();
    }

    /**
     * Méthode appelée quand le véhicule change
     */
    public function updatedVehicleId(): void
    {
        $this->maintenance_schedule_id = null;
        $this->resetValidation(['vehicle_id', 'maintenance_schedule_id']);

        // Suggérer le kilométrage actuel
        if ($this->selectedVehicle && !$this->mileage_at_maintenance) {
            $this->mileage_at_maintenance = (string) ($this->selectedVehicle->current_mileage ?? '');
        }
    }

    /**
     * Méthode appelée quand le type de maintenance change
     */
    public function updatedMaintenanceTypeId(): void
    {
        $this->resetValidation('maintenance_type_id');

        if ($this->selectedMaintenanceType) {
            // Charger les valeurs par défaut
            if (!$this->duration_minutes && $this->selectedMaintenanceType->estimated_duration_minutes) {
                $this->duration_minutes = (string) $this->selectedMaintenanceType->estimated_duration_minutes;
            }

            if (!$this->total_cost && $this->selectedMaintenanceType->estimated_cost) {
                $this->total_cost = (string) $this->selectedMaintenanceType->estimated_cost;
            }

            if (!$this->description) {
                $this->description = "Maintenance {$this->selectedMaintenanceType->name}";
            }
        }
    }

    /**
     * Méthode appelée quand le statut change
     */
    public function updatedStatus(): void
    {
        $this->resetValidation('status');

        // Auto-remplir les dates selon le statut
        switch ($this->status) {
            case 'in_progress':
                if (!$this->scheduled_date) {
                    $this->scheduled_date = Carbon::today()->format('Y-m-d');
                }
                break;

            case 'completed':
                if (!$this->completed_date) {
                    $this->completed_date = Carbon::today()->format('Y-m-d');
                }
                if (!$this->scheduled_date) {
                    $this->scheduled_date = $this->completed_date;
                }
                break;
        }
    }

    /**
     * Méthode pour gérer l'upload de fichiers
     */
    public function uploadFiles(): void
    {
        $this->validate([
            'uploadedFiles.*' => 'file|max:10240|mimes:jpeg,png,pdf,doc,docx,xls,xlsx,txt,csv',
        ]);

        foreach ($this->uploadedFiles as $index => $file) {
            if ($file->isValid()) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $mimeType = $file->getMimeType();
                $size = $file->getSize();

                // Générer un nom unique
                $uniqueName = Str::uuid() . '.' . $extension;
                $path = "maintenance/documents/{$uniqueName}";

                // Sauvegarder le fichier
                $file->storeAs('maintenance/documents', $uniqueName, 'public');

                // Ajouter aux documents existants pour affichage
                $this->existingDocuments[] = [
                    'id' => 'temp_' . Str::uuid(),
                    'name' => $filename,
                    'original_name' => $filename,
                    'file_path' => $path,
                    'file_type' => MaintenanceDocument::determineFileType($mimeType),
                    'mime_type' => $mimeType,
                    'file_size' => $size,
                    'document_type' => 'other',
                    'is_temporary' => true,
                ];
            }
        }

        $this->uploadedFiles = [];
        session()->flash('success', 'Fichiers uploadés avec succès.');
    }

    /**
     * Méthode pour supprimer un document
     */
    public function removeDocument(string $documentId): void
    {
        if (str_starts_with($documentId, 'temp_')) {
            // Supprimer un fichier temporaire
            $this->existingDocuments = array_filter(
                $this->existingDocuments,
                fn($doc) => $doc['id'] !== $documentId
            );

            // Supprimer le fichier physique
            $document = collect($this->existingDocuments)->firstWhere('id', $documentId);
            if ($document && isset($document['file_path'])) {
                Storage::disk('public')->delete($document['file_path']);
            }
        } else {
            // Marquer pour suppression
            $this->documentsToDelete[] = $documentId;
            $this->existingDocuments = array_filter(
                $this->existingDocuments,
                fn($doc) => $doc['id'] != $documentId
            );
        }
    }

    /**
     * Méthode pour mettre à jour le type d'un document
     */
    public function updateDocumentType(string $documentId, string $type): void
    {
        foreach ($this->existingDocuments as &$document) {
            if ($document['id'] == $documentId) {
                $document['document_type'] = $type;
                break;
            }
        }
    }

    /**
     * Méthode pour calculer automatiquement le coût
     */
    public function calculateCost(): void
    {
        $baseCost = $this->selectedMaintenanceType?->estimated_cost ?? 0;
        $providerMultiplier = 1.0;

        if ($this->provider_id) {
            $provider = MaintenanceProvider::find($this->provider_id);
            // Ajuster selon la note du fournisseur (exemple de logique)
            if ($provider && $provider->rating) {
                $providerMultiplier = 0.8 + ($provider->rating / 5) * 0.4; // 0.8 - 1.2
            }
        }

        $calculatedCost = $baseCost * $providerMultiplier;

        $this->costBreakdown = [
            'base_cost' => $baseCost,
            'provider_multiplier' => $providerMultiplier,
            'calculated_cost' => $calculatedCost,
        ];

        $this->total_cost = number_format($calculatedCost, 2, '.', '');
    }

    /**
     * Méthode pour passer à l'étape suivante
     */
    public function nextStep(): void
    {
        $steps = ['basic', 'details', 'documents', 'review'];
        $currentIndex = array_search($this->currentStep, $steps);

        if ($currentIndex < count($steps) - 1) {
            $this->currentStep = $steps[$currentIndex + 1];
        }
    }

    /**
     * Méthode pour revenir à l'étape précédente
     */
    public function previousStep(): void
    {
        $steps = ['basic', 'details', 'documents', 'review'];
        $currentIndex = array_search($this->currentStep, $steps);

        if ($currentIndex > 0) {
            $this->currentStep = $steps[$currentIndex - 1];
        }
    }

    /**
     * Méthode pour aller directement à une étape
     */
    public function goToStep(string $step): void
    {
        $this->currentStep = $step;
    }

    /**
     * Méthode pour sauvegarder l'opération
     */
    public function save(): void
    {
        $this->validate();

        $data = [
            'vehicle_id' => $this->vehicle_id,
            'maintenance_type_id' => $this->maintenance_type_id,
            'maintenance_schedule_id' => $this->maintenance_schedule_id ?: null,
            'provider_id' => $this->provider_id ?: null,
            'status' => $this->status,
            'scheduled_date' => $this->scheduled_date ?: null,
            'completed_date' => $this->completed_date ?: null,
            'mileage_at_maintenance' => $this->mileage_at_maintenance ? (int) $this->mileage_at_maintenance : null,
            'duration_minutes' => $this->duration_minutes ? (int) $this->duration_minutes : null,
            'total_cost' => $this->total_cost ? (float) $this->total_cost : null,
            'description' => $this->description ?: null,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editMode) {
            $operation = MaintenanceOperation::findOrFail($this->operationId);
            $this->authorize('update', $operation);
            $operation->update($data);
        } else {
            $this->authorize('create', MaintenanceOperation::class);
            $data['organization_id'] = auth()->user()->organization_id;
            $data['created_by'] = auth()->id();
            $operation = MaintenanceOperation::create($data);
            $this->operationId = $operation->id;
        }

        // Gérer les documents
        $this->handleDocuments($operation);

        $message = $this->editMode ? 'Opération mise à jour avec succès.' : 'Opération créée avec succès.';
        session()->flash('success', $message);

        // Rediriger vers la liste
        return redirect()->route('admin.maintenance.operations.index');
    }

    /**
     * Méthode pour gérer les documents
     */
    private function handleDocuments(MaintenanceOperation $operation): void
    {
        // Supprimer les documents marqués pour suppression
        if (!empty($this->documentsToDelete)) {
            $documentsToDelete = MaintenanceDocument::whereIn('id', $this->documentsToDelete)->get();
            foreach ($documentsToDelete as $document) {
                $document->delete(); // Le model s'occupe de supprimer le fichier
            }
        }

        // Sauvegarder les nouveaux documents
        foreach ($this->existingDocuments as $docData) {
            if (isset($docData['is_temporary']) && $docData['is_temporary']) {
                MaintenanceDocument::create([
                    'organization_id' => auth()->user()->organization_id,
                    'maintenance_operation_id' => $operation->id,
                    'name' => $docData['name'],
                    'original_name' => $docData['original_name'],
                    'file_path' => $docData['file_path'],
                    'file_type' => $docData['file_type'],
                    'mime_type' => $docData['mime_type'],
                    'file_size' => $docData['file_size'],
                    'document_type' => $docData['document_type'],
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.admin.maintenance.operation-form', [
            'vehicles' => $this->vehicles,
            'maintenanceTypes' => $this->maintenanceTypes,
            'availableSchedules' => $this->availableSchedules,
            'providers' => $this->providers,
            'selectedVehicle' => $this->selectedVehicle,
            'selectedMaintenanceType' => $this->selectedMaintenanceType,
            'isFormValid' => $this->isFormValid,
        ]);
    }
}
