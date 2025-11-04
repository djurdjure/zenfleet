<?php

namespace App\Livewire\Assignments;

use App\Models\Vehicle;
use App\Models\VehicleDepot;
use App\Services\DepotAssignmentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * AssignDepotModal Livewire Component
 *
 * Modal for assigning/transferring vehicles to depots with:
 * - Real-time capacity display
 * - Distance calculation (if geolocation available)
 * - Validation feedback
 * - History creation
 *
 * @package App\Livewire\Assignments
 */
class AssignDepotModal extends Component
{
    public $show = false;
    public $vehicleId;
    public $selectedDepotId = '';
    public $notes = '';
    public $action = 'assign'; // assign, transfer, unassign

    // Computed properties
    public $vehicle;
    public $availableDepots;

    protected $listeners = [
        'openAssignDepotModal' => 'open',
        'closeAssignDepotModal' => 'close',
    ];

    protected function rules()
    {
        $rules = [
            'notes' => 'nullable|string|max:1000',
        ];

        if ($this->action !== 'unassign') {
            $rules['selectedDepotId'] = 'required|exists:vehicle_depots,id';
        }

        return $rules;
    }

    protected $messages = [
        'selectedDepotId.required' => 'Veuillez sélectionner un dépôt',
        'selectedDepotId.exists' => 'Le dépôt sélectionné n\'existe pas',
    ];

    public function open($vehicleId)
    {
        $this->vehicleId = $vehicleId;
        $this->loadVehicle();
        $this->loadAvailableDepots();
        $this->determineAction();
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
        $this->reset(['vehicleId', 'selectedDepotId', 'notes', 'vehicle', 'availableDepots']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.assignments.assign-depot-modal');
    }

    protected function loadVehicle()
    {
        $this->vehicle = Vehicle::where('id', $this->vehicleId)
            ->where('organization_id', Auth::user()->organization_id)
            ->with(['depot', 'make', 'model'])
            ->firstOrFail();
    }

    protected function loadAvailableDepots()
    {
        $this->availableDepots = VehicleDepot::forOrganization(Auth::user()->organization_id)
            ->active()
            ->orderBy('name')
            ->get()
            ->map(function ($depot) {
                $depot->distance = $this->calculateDistance($depot);
                $depot->can_assign = $depot->hasAvailableSpace() || $depot->id === $this->vehicle?->depot_id;
                return $depot;
            });
    }

    protected function determineAction()
    {
        if ($this->vehicle->depot_id) {
            $this->action = 'transfer';
        } else {
            $this->action = 'assign';
        }
    }

    protected function calculateDistance($depot)
    {
        // If vehicle or depot doesn't have coordinates, return null
        if (!$this->vehicle->latitude || !$this->vehicle->longitude ||
            !$depot->latitude || !$depot->longitude) {
            return null;
        }

        // Haversine formula for distance calculation
        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->vehicle->latitude);
        $lonFrom = deg2rad($this->vehicle->longitude);
        $latTo = deg2rad($depot->latitude);
        $lonTo = deg2rad($depot->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return round($distance, 1);
    }

    public function assign()
    {
        $this->validate();

        $service = new DepotAssignmentService();
        $depot = VehicleDepot::findOrFail($this->selectedDepotId);

        // Validate organization match
        if ($depot->organization_id !== Auth::user()->organization_id) {
            session()->flash('error', 'Dépôt non autorisé');
            return;
        }

        try {
            $service->assignVehicleToDepot(
                $this->vehicle,
                $depot,
                Auth::user(),
                $this->notes
            );

            $actionText = $this->action === 'transfer' ? 'transféré' : 'affecté';
            session()->flash('success', "Véhicule {$actionText} au dépôt {$depot->name} avec succès");

            $this->dispatch('depot-assigned');
            $this->dispatch('refreshVehicleData');
            $this->close();

        } catch (\InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        } catch (\RuntimeException $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function unassign()
    {
        $this->validate(['notes' => 'nullable|string|max:1000']);

        if (!$this->vehicle->depot_id) {
            session()->flash('error', 'Le véhicule n\'est pas affecté à un dépôt');
            return;
        }

        $service = new DepotAssignmentService();

        try {
            $service->unassignVehicleFromDepot(
                $this->vehicle,
                Auth::user(),
                $this->notes
            );

            session()->flash('success', 'Véhicule retiré du dépôt avec succès');

            $this->dispatch('depot-unassigned');
            $this->dispatch('refreshVehicleData');
            $this->close();

        } catch (\InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        } catch (\RuntimeException $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function setAction($action)
    {
        $this->action = $action;

        if ($action === 'unassign') {
            $this->selectedDepotId = '';
        }
    }

    public function getDepotStatusBadge($depot)
    {
        if (!$depot->capacity) {
            return ['text' => 'Capacité non définie', 'color' => 'gray'];
        }

        if ($depot->isFull()) {
            return ['text' => 'Complet', 'color' => 'red'];
        }

        $percentage = $depot->occupancyPercentage;
        if ($percentage >= 80) {
            return ['text' => 'Presque complet', 'color' => 'orange'];
        }

        return ['text' => 'Disponible', 'color' => 'green'];
    }
}
