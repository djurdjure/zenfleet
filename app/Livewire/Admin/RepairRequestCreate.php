<?php

namespace App\Livewire\Admin;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\RepairCategory;
use App\Models\RepairRequest;
use App\Models\Vehicle;
use App\Services\RepairRequestService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Composant Livewire pour création de demandes de réparation
 *
 * Fonctionnalités:
 * - Sélection véhicule → Auto-charge le chauffeur assigné + kilométrage
 * - Sélection chauffeur → Auto-charge le véhicule assigné + kilométrage
 * - Upload de pièces jointes
 * - Conforme Livewire 3 et Alpine.js 3
 */
class RepairRequestCreate extends Component
{
    use WithFileUploads;
    use AuthorizesRequests;

    // Champs du formulaire
    public $vehicle_id;
    public $driver_id;
    public $title;
    public $description;
    public $category_id;
    public $urgency = 'normal';
    public $current_mileage;
    public $estimated_cost;
    public $attachments = [];

    // Données pour les sélecteurs
    public $vehicles = [];
    public $drivers = [];
    public $categories = [];

    // Contexte de sécurité chauffeur pur
    public bool $isDriverScoped = false;
    public ?int $lockedDriverId = null;
    public ?int $lockedVehicleId = null;

    // État
    public $loading = false;

    protected $rules = [
        'vehicle_id' => 'required|exists:vehicles,id',
        'driver_id' => 'required|exists:drivers,id',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'category_id' => 'nullable|exists:repair_categories,id',
        'urgency' => 'required|in:low,normal,high,critical',
        'current_mileage' => 'nullable|integer|min:0',
        'estimated_cost' => 'nullable|numeric|min:0',
        'attachments.*' => 'nullable|file|max:10240', // 10MB max
    ];

    protected $messages = [
        'vehicle_id.required' => 'Veuillez sélectionner un véhicule.',
        'driver_id.required' => 'Veuillez sélectionner un chauffeur.',
        'title.required' => 'Le titre est obligatoire.',
        'description.required' => 'La description est obligatoire.',
        'urgency.required' => 'Le niveau d\'urgence est obligatoire.',
    ];

    public function mount()
    {
        $this->authorize('create', RepairRequest::class);
        $user = auth()->user();
        $organizationId = $user->organization_id;

        $this->isDriverScoped = $user->isDriverOnly();

        if ($this->isDriverScoped) {
            $this->initializeDriverScopedForm($organizationId, (int) $user->id);
        } else {
            $this->vehicles = Vehicle::where('organization_id', $organizationId)
                ->active()
                ->whereNull('deleted_at')
                ->orderBy('registration_plate')
                ->get(['id', 'registration_plate', 'brand', 'model', 'current_mileage'])
                ->values()
                ->toArray();

            $this->drivers = Driver::with('user')
                ->where('organization_id', $organizationId)
                ->whereNull('deleted_at')
                ->get()
                ->map(function ($driver) {
                    return [
                        'id' => $driver->id,
                        'name' => $driver->user->name ?? 'N/A',
                        'license_number' => $driver->license_number ?? '',
                    ];
                })
                ->values()
                ->toArray();
        }

        // Charger les catégories
        $this->categories = RepairCategory::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->values()
            ->toArray();
    }

    private function initializeDriverScopedForm(int $organizationId, int $userId): void
    {
        $driver = Driver::with('user')
            ->where('organization_id', $organizationId)
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->first();

        if (! $driver) {
            $this->drivers = [];
            $this->vehicles = [];
            return;
        }

        $this->lockedDriverId = (int) $driver->id;
        $this->driver_id = $this->lockedDriverId;
        $this->drivers = [[
            'id' => $driver->id,
            'name' => $driver->user->name ?? 'N/A',
            'license_number' => $driver->license_number ?? '',
        ]];

        $referenceTime = now();
        $assignment = Assignment::query()
            ->where('organization_id', $organizationId)
            ->where('driver_id', $driver->id)
            ->where('status', '!=', Assignment::STATUS_CANCELLED)
            ->where('start_datetime', '<=', $referenceTime)
            ->where(function ($query) use ($referenceTime) {
                $query->whereNull('end_datetime')
                    ->orWhere('end_datetime', '>=', $referenceTime);
            })
            ->with(['vehicle:id,organization_id,registration_plate,brand,model,current_mileage'])
            ->orderByDesc('start_datetime')
            ->first();

        if (! $assignment || ! $assignment->vehicle) {
            $this->vehicles = [];
            return;
        }

        $vehicle = $assignment->vehicle;
        $this->lockedVehicleId = (int) $vehicle->id;
        $this->vehicle_id = $this->lockedVehicleId;
        $this->current_mileage = $vehicle->current_mileage;
        $this->vehicles = [[
            'id' => $vehicle->id,
            'registration_plate' => $vehicle->registration_plate,
            'brand' => $vehicle->brand,
            'model' => $vehicle->model,
            'current_mileage' => $vehicle->current_mileage,
        ]];
    }

    /**
     * Appelé automatiquement quand vehicle_id change (Livewire 3)
     * Charge le chauffeur assigné et le kilométrage
     */
    public function updatedVehicleId($value)
    {
        if ($this->isDriverScoped) {
            $this->vehicle_id = $this->lockedVehicleId;
            return;
        }

        if (empty($value)) {
            $this->driver_id = null;
            $this->current_mileage = null;
            return;
        }

        $this->loading = true;

        // Récupérer le véhicule
        $vehicle = Vehicle::find($value);

        if ($vehicle) {
            // Charger le kilométrage actuel
            $this->current_mileage = $vehicle->current_mileage;

            // Trouver l'assignment actif pour ce véhicule
            $activeAssignment = Assignment::where('vehicle_id', $value)
                ->where(function ($query) {
                    $query->whereNull('end_datetime')
                        ->orWhere('end_datetime', '>', now());
                })
                ->where('start_datetime', '<=', now())
                ->where('status', '!=', Assignment::STATUS_CANCELLED)
                ->orderBy('start_datetime', 'desc')
                ->first();

            if ($activeAssignment) {
                $this->driver_id = $activeAssignment->driver_id;
            }
        }

        $this->loading = false;
    }

    /**
     * Appelé automatiquement quand driver_id change (Livewire 3)
     * Charge le véhicule assigné et le kilométrage
     */
    public function updatedDriverId($value)
    {
        if ($this->isDriverScoped) {
            $this->driver_id = $this->lockedDriverId;
            return;
        }

        if (empty($value)) {
            // Ne pas effacer vehicle_id si c'est juste un changement manuel
            return;
        }

        // Si vehicle_id est déjà défini, ne pas le changer
        // (évite les boucles infinies)
        if (!empty($this->vehicle_id)) {
            return;
        }

        $this->loading = true;

        // Trouver l'assignment actif pour ce chauffeur
        $activeAssignment = Assignment::where('driver_id', $value)
            ->where(function ($query) {
                $query->whereNull('end_datetime')
                    ->orWhere('end_datetime', '>', now());
            })
            ->where('start_datetime', '<=', now())
            ->where('status', '!=', Assignment::STATUS_CANCELLED)
            ->orderBy('start_datetime', 'desc')
            ->first();

        if ($activeAssignment) {
            $this->vehicle_id = $activeAssignment->vehicle_id;

            // Charger aussi le kilométrage
            $vehicle = Vehicle::find($activeAssignment->vehicle_id);
            if ($vehicle) {
                $this->current_mileage = $vehicle->current_mileage;
            }
        }

        $this->loading = false;
    }

    /**
     * Soumettre le formulaire
     */
    public function submit()
    {
        $this->authorize('create', RepairRequest::class);

        if ($this->isDriverScoped) {
            $this->driver_id = $this->lockedDriverId;
            $this->vehicle_id = $this->lockedVehicleId;
        }

        if ($this->isDriverScoped && (! $this->driver_id || ! $this->vehicle_id)) {
            $this->addError(
                'form',
                'Aucun vehicule actif ne vous est affecte. Contactez votre superviseur pour finaliser l\'affectation.'
            );
            return;
        }

        $this->validate();

        try {
            $repairRequest = app(RepairRequestService::class)->createRequest([
                'vehicle_id' => $this->vehicle_id,
                'driver_id' => $this->driver_id,
                'title' => $this->title,
                'description' => $this->description,
                'category_id' => $this->category_id,
                'urgency' => $this->urgency,
                'current_mileage' => $this->current_mileage,
                'location_description' => null,
                'estimated_cost' => $this->estimated_cost,
                'organization_id' => auth()->user()->organization_id,
                'requested_by' => auth()->id(),
                'attachments' => $this->attachments,
            ]);

            session()->flash('success', 'Demande de réparation créée avec succès.');

            return redirect()->route('admin.repair-requests.show', $repairRequest);
        } catch (\Exception $e) {
            report($e);
            $this->addError('form', 'Échec de création: ' . $e->getMessage());
            session()->flash('error', 'Échec de création: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.repair-request-create')
            ->layout('layouts.admin.catalyst');
    }
}
