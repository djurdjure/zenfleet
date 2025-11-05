<?php

namespace App\Livewire\Vehicles;

use App\Models\Vehicle;
use App\Models\VehicleDepot;
use App\Services\DepotAssignmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

/**
 * BulkDepotAssignment Livewire Component
 *
 * Enterprise-grade bulk vehicle-to-depot assignment interface with:
 * - Multiple vehicle selection
 * - Real-time capacity preview
 * - Validation before assignment
 * - Detailed success/failure reporting
 * - Atomic transaction handling
 *
 * @package App\Livewire\Vehicles
 */
class BulkDepotAssignment extends Component
{
    // Component state
    public $showModal = false;
    public $selectedVehicleIds = [];
    public $targetDepotId = null;
    public $assignmentNotes = '';

    // UI state
    public $isProcessing = false;
    public $capacityPreview = null;
    public $validationErrors = [];
    public $assignmentResult = null;

    // Data
    public $availableDepots = [];
    public $selectedVehiclesData = [];

    protected $listeners = [
        'openBulkAssignmentModal' => 'open',
        'closeBulkAssignmentModal' => 'close',
    ];

    protected function rules()
    {
        return [
            'targetDepotId' => 'required|exists:vehicle_depots,id',
            'selectedVehicleIds' => 'required|array|min:1',
            'selectedVehicleIds.*' => 'exists:vehicles,id',
            'assignmentNotes' => 'nullable|string|max:1000',
        ];
    }

    protected $messages = [
        'targetDepotId.required' => 'Veuillez sélectionner un dépôt de destination.',
        'targetDepotId.exists' => 'Le dépôt sélectionné n\'existe pas.',
        'selectedVehicleIds.required' => 'Aucun véhicule sélectionné.',
        'selectedVehicleIds.min' => 'Sélectionnez au moins un véhicule.',
        'assignmentNotes.max' => 'Les notes ne peuvent pas dépasser 1000 caractères.',
    ];

    public function mount()
    {
        $this->loadAvailableDepots();
    }

    /**
     * Open modal with selected vehicles
     *
     * @param array $vehicleIds
     */
    public function open(array $vehicleIds = [])
    {
        $this->reset(['targetDepotId', 'assignmentNotes', 'validationErrors', 'assignmentResult']);
        $this->selectedVehicleIds = $vehicleIds;

        // Load data for selected vehicles
        $this->loadSelectedVehiclesData();

        // Reload available depots
        $this->loadAvailableDepots();

        $this->showModal = true;

        Log::info('Bulk assignment modal opened', [
            'vehicle_count' => count($vehicleIds),
            'user_id' => Auth::id()
        ]);
    }

    /**
     * Close modal
     */
    public function close()
    {
        $this->showModal = false;
        $this->reset();
    }

    /**
     * Load available depots for selection
     */
    protected function loadAvailableDepots()
    {
        $this->availableDepots = VehicleDepot::where('organization_id', Auth::user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($depot) {
                return [
                    'id' => $depot->id,
                    'name' => $depot->name,
                    'code' => $depot->code,
                    'city' => $depot->city,
                    'capacity' => $depot->capacity,
                    'current_count' => $depot->current_count,
                    'available_capacity' => $depot->availableCapacity,
                    'occupancy_percentage' => $depot->occupancyPercentage,
                    'has_space' => $depot->hasAvailableSpace(),
                ];
            })
            ->toArray();
    }

    /**
     * Load data for selected vehicles
     */
    protected function loadSelectedVehiclesData()
    {
        $this->selectedVehiclesData = Vehicle::whereIn('id', $this->selectedVehicleIds)
            ->where('organization_id', Auth::user()->organization_id)
            ->with(['make', 'model', 'depot'])
            ->get()
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'registration_plate' => $vehicle->registration_plate,
                    'make_name' => $vehicle->make?->name ?? 'N/A',
                    'model_name' => $vehicle->model?->name ?? 'N/A',
                    'current_depot_name' => $vehicle->depot?->name ?? 'Non affecté',
                    'current_depot_id' => $vehicle->depot_id,
                    'status' => $vehicle->status,
                ];
            })
            ->toArray();
    }

    /**
     * Update capacity preview when depot is selected
     */
    public function updatedTargetDepotId($value)
    {
        if (!$value) {
            $this->capacityPreview = null;
            return;
        }

        $depot = VehicleDepot::find($value);
        if (!$depot) {
            $this->capacityPreview = null;
            return;
        }

        // Count vehicles that will actually be assigned (not already in this depot)
        $vehiclesToAssign = collect($this->selectedVehiclesData)
            ->where('current_depot_id', '!=', $depot->id)
            ->count();

        $this->capacityPreview = [
            'depot_name' => $depot->name,
            'current_count' => $depot->current_count,
            'capacity' => $depot->capacity,
            'available_before' => $depot->availableCapacity,
            'vehicles_to_assign' => $vehiclesToAssign,
            'available_after' => $depot->availableCapacity - $vehiclesToAssign,
            'sufficient_capacity' => $depot->availableCapacity >= $vehiclesToAssign,
            'occupancy_before' => $depot->occupancyPercentage,
            'occupancy_after' => (($depot->current_count + $vehiclesToAssign) / $depot->capacity) * 100,
        ];

        Log::info('Capacity preview updated', [
            'depot_id' => $depot->id,
            'vehicles_to_assign' => $vehiclesToAssign,
            'sufficient_capacity' => $this->capacityPreview['sufficient_capacity']
        ]);
    }

    /**
     * Perform bulk assignment
     */
    public function assignVehicles()
    {
        $this->validate();

        $this->isProcessing = true;
        $this->validationErrors = [];
        $this->assignmentResult = null;

        try {
            $depot = VehicleDepot::findOrFail($this->targetDepotId);
            $user = Auth::user();

            // Use DepotAssignmentService for bulk assignment
            $service = app(DepotAssignmentService::class);

            $result = $service->bulkAssignVehiclesToDepot(
                $this->selectedVehicleIds,
                $depot,
                $user,
                $this->assignmentNotes
            );

            $this->assignmentResult = $result;

            if ($result['success'] && $result['assigned'] > 0) {
                session()->flash('success', $result['message']);

                Log::info('Bulk assignment completed successfully', [
                    'depot_id' => $depot->id,
                    'assigned' => $result['assigned'],
                    'skipped' => $result['skipped'],
                    'failed' => $result['failed'],
                    'user_id' => $user->id
                ]);

                // Dispatch event to refresh vehicle list
                $this->dispatch('vehicles-bulk-assigned', [
                    'depot_id' => $depot->id,
                    'count' => $result['assigned']
                ]);

                // Close modal after short delay to show result
                $this->dispatch('close-modal-delayed', ['modalName' => 'bulk-depot-assignment']);
            } else {
                session()->flash('warning', 'Aucun véhicule n\'a été affecté. ' . ($result['message'] ?? ''));
            }

        } catch (\InvalidArgumentException $e) {
            $this->validationErrors[] = $e->getMessage();
            session()->flash('error', $e->getMessage());

            Log::warning('Bulk assignment validation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

        } catch (\RuntimeException $e) {
            $this->validationErrors[] = $e->getMessage();
            session()->flash('error', 'Erreur lors de l\'affectation: ' . $e->getMessage());

            Log::error('Bulk assignment runtime error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

        } catch (\Exception $e) {
            $this->validationErrors[] = 'Une erreur inattendue s\'est produite.';
            session()->flash('error', 'Une erreur inattendue s\'est produite. Veuillez réessayer.');

            Log::error('Bulk assignment unexpected error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        return view('livewire.vehicles.bulk-depot-assignment');
    }
}
