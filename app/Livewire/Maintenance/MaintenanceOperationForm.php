<?php

namespace App\Livewire\Maintenance;

use App\Models\MaintenanceOperation;
use App\Models\Vehicle;
use App\Models\Maintenance\MaintenanceType;
use App\Models\MaintenanceProvider;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;

/**
 * 🔧 Composant Formulaire d'Opération de Maintenance
 *
 * Fonctionnalités:
 * - Validation temps réel
 * - Gestion des coûts et statuts
 * - Intégration Enterprise-Grade
 *
 * @author ZenFleet Architecture Team
 */
class MaintenanceOperationForm extends Component
{
    use AuthorizesRequests;

    public ?MaintenanceOperation $operation = null;
    public bool $isEdit = false;

    // Champs du formulaire
    #[Validate('required|exists:vehicles,id')]
    public ?int $vehicle_id = null;

    #[Validate('required|exists:maintenance_types,id')]
    public ?int $maintenance_type_id = null;

    #[Validate('required|exists:maintenance_providers,id')]
    public ?int $provider_id = null;

    #[Validate('required|date')]
    public string $scheduled_date = '';

    #[Validate('nullable|date_format:H:i')]
    public string $start_time = '09:00';

    #[Validate('nullable|date_format:H:i|after:start_time')]
    public string $end_time = '10:00';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('nullable|numeric|min:0')]
    public ?float $total_cost = null;

    #[Validate('required|in:planned,in_progress,completed,cancelled')]
    public string $status = 'planned';

    // Options et état
    public bool $isValidating = false;

    // Données pour les selects
    public $vehicles = [];
    public $maintenanceTypes = [];
    public $providers = [];

    public function mount(?MaintenanceOperation $operation = null)
    {
        $this->operation = $operation;
        $this->isEdit = $operation !== null;

        if ($this->isEdit) {
            $this->authorize('update', $operation);
            $this->loadOperationData();
        } else {
            $this->authorize('create', MaintenanceOperation::class);
            $this->setDefaultValues();
        }

        $this->loadSelectData();
    }

    /**
     * Charge les données de l'opération en édition
     */
    private function loadOperationData()
    {
        $this->vehicle_id = $this->operation->vehicle_id;
        $this->maintenance_type_id = $this->operation->maintenance_type_id;
        $this->provider_id = $this->operation->provider_id;
        $this->scheduled_date = $this->operation->scheduled_date->format('Y-m-d');
        // Gestion des heures si disponibles (supposons qu'elles soient stockées ou gérées via scheduled_date)
        // Pour cet exemple, on simplifie ou on extrait si la DB le permet.
        // Si scheduled_date est un datetime :
        $this->start_time = $this->operation->scheduled_date->format('H:i');
        
        $this->description = $this->operation->description ?? '';
        $this->total_cost = $this->operation->total_cost;
        $this->status = $this->operation->status;
    }

    /**
     * Valeurs par défaut pour création
     */
    private function setDefaultValues()
    {
        $this->scheduled_date = now()->addDay()->format('Y-m-d');
        $this->status = 'planned';
    }

    /**
     * Charge les données pour les selects
     */
    private function loadSelectData()
    {
        $organizationId = auth()->user()->organization_id;

        // Véhicules
        $this->vehicles = Vehicle::where('organization_id', $organizationId)
            ->whereNull('deleted_at')
            ->select('id', 'registration_plate', 'brand', 'model', 'current_mileage')
            ->orderBy('registration_plate')
            ->get()
            ->map(function($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'label' => $vehicle->registration_plate,
                    'details' => $vehicle->brand . ' ' . $vehicle->model,
                    'mileage' => $vehicle->current_mileage
                ];
            });

        // Types de maintenance
        $this->maintenanceTypes = MaintenanceType::orderBy('category')
            ->orderBy('name')
            ->select('id', 'name', 'category', 'estimated_cost')
            ->get()
            ->map(function($type) {
                return [
                    'id' => $type->id,
                    'label' => $type->name,
                    'category' => $type->category,
                    'cost' => $type->estimated_cost
                ];
            });

        // Fournisseurs
        $this->providers = MaintenanceProvider::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->select('id', 'name', 'company_name', 'city')
            ->orderBy('name')
            ->get()
            ->map(function($provider) {
                return [
                    'id' => $provider->id,
                    'label' => $provider->name,
                    'details' => $provider->company_name . ($provider->city ? ' (' . $provider->city . ')' : '')
                ];
            });
    }

    /**
     * Watchers
     */
    public function updatedMaintenanceTypeId()
    {
        // Pré-remplir le coût estimé si disponible et si le coût n'est pas déjà saisi
        if ($this->maintenance_type_id && !$this->total_cost) {
            $type = collect($this->maintenanceTypes)->firstWhere('id', $this->maintenance_type_id);
            if ($type && $type['cost']) {
                $this->total_cost = $type['cost'];
            }
        }
    }

    /**
     * Sauvegarde
     */
    public function save()
    {
        $this->validate();

        try {
            // Construction du datetime complet
            $scheduledDateTime = Carbon::parse($this->scheduled_date . ' ' . $this->start_time);

            $data = [
                'vehicle_id' => $this->vehicle_id,
                'maintenance_type_id' => $this->maintenance_type_id,
                'provider_id' => $this->provider_id,
                'scheduled_date' => $scheduledDateTime,
                'description' => $this->description,
                'total_cost' => $this->total_cost,
                'status' => $this->status,
            ];

            if ($this->isEdit) {
                $this->operation->update($data);
                $message = 'Opération de maintenance modifiée avec succès';
            } else {
                $data['organization_id'] = auth()->user()->organization_id;
                $data['created_by'] = auth()->id();
                $this->operation = MaintenanceOperation::create($data);
                $message = 'Opération de maintenance créée avec succès';
            }

            $this->dispatch('operation-saved', $message);
            
            // Redirection après court délai (géré par le frontend ou ici)
            return redirect()->route('admin.maintenance.operations.index')->with('success', $message);

        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'save' => ['Erreur lors de la sauvegarde: ' . $e->getMessage()]
            ]);
        }
    }

    public function cancel()
    {
        return redirect()->route('admin.maintenance.operations.index');
    }

    public function render()
    {
        return view('livewire.maintenance.maintenance-operation-form');
    }
}
