<?php

namespace App\Livewire\Assignments;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Services\AssignmentOverlapService;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;

/**
 * 📝 Composant Formulaire d'Affectation
 *
 * Fonctionnalités:
 * - Validation temps réel anti-chevauchement
 * - Support durées indéterminées
 * - Suggestions de créneaux libres
 * - Pré-remplissage intelligent
 *
 * @author ZenFleet Architecture Team
 */
class AssignmentForm extends Component
{
    use AuthorizesRequests;

    public ?Assignment $assignment = null;
    public bool $isEdit = false;

    // Champs du formulaire
    #[Validate('required|exists:vehicles,id')]
    public ?int $vehicle_id = null;

    #[Validate('required|exists:drivers,id')]
    public ?int $driver_id = null;

    #[Validate('required|date|after_or_equal:now')]
    public string $start_datetime = '';

    #[Validate('nullable|date|after:start_datetime')]
    public string $end_datetime = '';

    #[Validate('nullable|string|max:500')]
    public string $reason = '';

    #[Validate('nullable|string|max:1000')]
    public string $notes = '';

    #[Validate('nullable|integer|min:0')]
    public ?int $start_mileage = null;

    #[Validate('nullable|integer|min:0|gte:start_mileage')]
    public ?int $end_mileage = null;

    #[Validate('nullable|numeric|min:0|max:8760')]
    public ?float $estimated_duration_hours = null;

    // Options et état
    public bool $indefinite_duration = false;
    public bool $showConflicts = false;
    public bool $isValidating = false;

    // Données pour les selects
    public $vehicles = [];
    public $drivers = [];

    // Validation et suggestions
    public array $conflicts = [];
    public array $suggestedSlots = [];
    public array $validationMessages = [];

    // Services
    private AssignmentOverlapService $overlapService;

    public function boot(AssignmentOverlapService $overlapService)
    {
        $this->overlapService = $overlapService;
    }

    public function mount(?Assignment $assignment = null)
    {
        $this->assignment = $assignment;
        $this->isEdit = $assignment !== null;

        if ($this->isEdit) {
            $this->authorize('update', $assignment);
            $this->loadAssignmentData();
        } else {
            $this->authorize('create', Assignment::class);
            $this->setDefaultValues();
        }

        $this->loadSelectData();
    }

    /**
     * Charge les données de l'affectation en édition
     */
    private function loadAssignmentData()
    {
        $this->vehicle_id = $this->assignment->vehicle_id;
        $this->driver_id = $this->assignment->driver_id;
        $this->start_datetime = $this->assignment->start_datetime->format('Y-m-d\TH:i');
        $this->end_datetime = $this->assignment->end_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->reason = $this->assignment->reason ?? '';
        $this->notes = $this->assignment->notes ?? '';
        $this->start_mileage = $this->assignment->start_mileage;
        $this->end_mileage = $this->assignment->end_mileage;
        $this->estimated_duration_hours = $this->assignment->estimated_duration_hours;
        $this->indefinite_duration = $this->assignment->end_datetime === null;
    }

    /**
     * Valeurs par défaut pour création
     */
    private function setDefaultValues()
    {
        $this->start_datetime = now()->addHour()->format('Y-m-d\TH:i');
        $this->indefinite_duration = false;
    }

    /**
     * Charge les données pour les selects
     */
    private function loadSelectData()
    {
        $organizationId = auth()->user()->organization_id;

        $this->vehicles = Vehicle::where('organization_id', $organizationId)
            ->where('deleted_at', null)
            ->with(['status' => function($q) { $q->select('id', 'status'); }])
            ->select('id', 'registration_plate', 'brand', 'model', 'status_id', 'current_mileage')
            ->orderBy('registration_plate')
            ->get()
            ->map(function($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'label' => $vehicle->registration_plate ?? ($vehicle->brand . ' ' . $vehicle->model),
                    'details' => $vehicle->brand . ' ' . $vehicle->model,
                    'mileage' => $vehicle->current_mileage,
                    'available' => true // TODO: vérifier disponibilité
                ];
            });

        $this->drivers = Driver::where('organization_id', $organizationId)
            ->where('deleted_at', null)
            ->with(['status' => function($q) { $q->select('id', 'status'); }])
            ->select('id', 'first_name', 'last_name', 'status_id', 'phone_number', 'license_expiry_date')
            ->orderBy('last_name')
            ->get()
            ->map(function($driver) {
                return [
                    'id' => $driver->id,
                    'label' => $driver->first_name . ' ' . $driver->last_name,
                    'phone' => $driver->phone_number,
                    'license_expiry' => $driver->license_expiry_date?->format('d/m/Y'),
                    'available' => true // TODO: vérifier disponibilité
                ];
            });
    }

    /**
     * Watchers pour validation temps réel
     */

    public function updatedVehicleId()
    {
        $this->updateMileageFromVehicle();
        $this->validateOverlap();
    }

    public function updatedDriverId()
    {
        $this->validateOverlap();
    }

    public function updatedStartDatetime()
    {
        $this->updateEstimatedDuration();
        $this->validateOverlap();
    }

    public function updatedEndDatetime()
    {
        $this->updateEstimatedDuration();
        $this->validateOverlap();
    }

    public function updatedIndefiniteDuration()
    {
        if ($this->indefinite_duration) {
            $this->end_datetime = '';
            $this->estimated_duration_hours = null;
        } else {
            $this->updateEstimatedDuration();
        }
        $this->validateOverlap();
    }

    /**
     * Met à jour le kilométrage depuis le véhicule
     */
    private function updateMileageFromVehicle()
    {
        if ($this->vehicle_id) {
            $vehicle = collect($this->vehicles)->firstWhere('id', $this->vehicle_id);
            if ($vehicle && !$this->start_mileage) {
                $this->start_mileage = $vehicle['mileage'];
            }
        }
    }

    /**
     * Calcule la durée estimée
     */
    private function updateEstimatedDuration()
    {
        if ($this->start_datetime && $this->end_datetime && !$this->indefinite_duration) {
            try {
                $start = Carbon::parse($this->start_datetime);
                $end = Carbon::parse($this->end_datetime);
                $this->estimated_duration_hours = $start->diffInHours($end, true);
            } catch (\Exception $e) {
                $this->estimated_duration_hours = null;
            }
        }
    }

    /**
     * Validation des chevauchements en temps réel
     */
    public function validateOverlap()
    {
        if (!$this->vehicle_id || !$this->driver_id || !$this->start_datetime) {
            $this->resetValidation();
            return;
        }

        $this->isValidating = true;

        try {
            $startDateTime = Carbon::parse($this->start_datetime);
            $endDateTime = $this->indefinite_duration || !$this->end_datetime ?
                null : Carbon::parse($this->end_datetime);

            $validation = $this->overlapService->validateAssignment(
                auth()->user()->organization_id,
                $this->vehicle_id,
                $this->driver_id,
                $startDateTime,
                $endDateTime,
                $this->assignment?->id
            );

            $this->validationMessages = array_merge(
                $validation['validationErrors'],
                $validation['overlapErrors']
            );

            $this->conflicts = $validation['conflicts'];
            $this->suggestedSlots = $validation['suggestedSlots'];
            $this->showConflicts = !$validation['isValid'];

        } catch (\Exception $e) {
            $this->validationMessages = ['Erreur de validation: ' . $e->getMessage()];
            $this->showConflicts = true;
        }

        $this->isValidating = false;
    }

    /**
     * Réinitialise la validation
     */
    private function resetValidation()
    {
        $this->validationMessages = [];
        $this->conflicts = [];
        $this->suggestedSlots = [];
        $this->showConflicts = false;
    }

    /**
     * Applique un créneau suggéré
     */
    public function applySuggestedSlot(array $slot)
    {
        $this->start_datetime = $slot['start'];
        $this->end_datetime = $slot['end'] ?? '';
        $this->indefinite_duration = $slot['end'] === null;

        $this->updateEstimatedDuration();
        $this->validateOverlap();

        $this->dispatch('slot-applied', 'Créneau appliqué: ' . $slot['start_formatted']);
    }

    /**
     * Recherche le prochain créneau libre
     */
    public function findNextAvailableSlot()
    {
        if (!$this->vehicle_id || !$this->driver_id) {
            $this->dispatch('validation-error', 'Veuillez sélectionner un véhicule et un chauffeur');
            return;
        }

        try {
            $fromDate = $this->start_datetime ? Carbon::parse($this->start_datetime) : now();
            $durationHours = $this->estimated_duration_hours ?? 24;

            $nextSlot = $this->overlapService->findNextAvailableSlot(
                auth()->user()->organization_id,
                $this->vehicle_id,
                $this->driver_id,
                $fromDate,
                (int) $durationHours
            );

            if ($nextSlot) {
                $this->start_datetime = $nextSlot['start'];
                $this->end_datetime = $nextSlot['end'];
                $this->indefinite_duration = false;
                $this->updateEstimatedDuration();
                $this->validateOverlap();

                $this->dispatch('slot-found', 'Prochain créneau libre trouvé');
            } else {
                $this->dispatch('no-slot-found', 'Aucun créneau libre trouvé dans les 30 prochains jours');
            }

        } catch (\Exception $e) {
            $this->dispatch('validation-error', 'Erreur lors de la recherche: ' . $e->getMessage());
        }
    }

    /**
     * Sauvegarde de l'affectation
     */
    public function save()
    {
        // Validation finale
        $this->validate();

        if ($this->showConflicts && !empty($this->validationMessages)) {
            throw ValidationException::withMessages([
                'conflicts' => $this->validationMessages
            ]);
        }

        try {
            $data = [
                'vehicle_id' => $this->vehicle_id,
                'driver_id' => $this->driver_id,
                'start_datetime' => Carbon::parse($this->start_datetime),
                'end_datetime' => $this->indefinite_duration || !$this->end_datetime ?
                    null : Carbon::parse($this->end_datetime),
                'reason' => $this->reason,
                'notes' => $this->notes,
                'start_mileage' => $this->start_mileage,
                'end_mileage' => $this->end_mileage,
                'estimated_duration_hours' => $this->estimated_duration_hours,
            ];

            if ($this->isEdit) {
                $this->assignment->update($data);
                $message = 'Affectation modifiée avec succès';
            } else {
                $data['organization_id'] = auth()->user()->organization_id;
                $this->assignment = Assignment::create($data);
                $message = 'Affectation créée avec succès';
            }

            $this->dispatch('assignment-saved', $this->assignment->id, $message);

        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'save' => ['Erreur lors de la sauvegarde: ' . $e->getMessage()]
            ]);
        }
    }

    /**
     * Annulation
     */
    public function cancel()
    {
        $this->dispatch('assignment-form-cancelled');
    }

    /**
     * Validation personnalisée
     */
    public function rules()
    {
        $rules = [
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'start_datetime' => 'required|date',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'start_mileage' => 'nullable|integer|min:0',
            'estimated_duration_hours' => 'nullable|numeric|min:0|max:8760'
        ];

        if (!$this->indefinite_duration) {
            $rules['end_datetime'] = 'required|date|after:start_datetime';
            $rules['end_mileage'] = 'nullable|integer|min:0|gte:start_mileage';
        }

        return $rules;
    }

    /**
     * Messages de validation
     */
    public function messages()
    {
        return [
            'vehicle_id.required' => 'Veuillez sélectionner un véhicule',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas',
            'driver_id.required' => 'Veuillez sélectionner un chauffeur',
            'driver_id.exists' => 'Le chauffeur sélectionné n\'existe pas',
            'start_datetime.required' => 'La date de remise est obligatoire',
            'start_datetime.date' => 'Format de date invalide',
            'end_datetime.required' => 'La date de restitution est obligatoire pour une durée définie',
            'end_datetime.date' => 'Format de date invalide',
            'end_datetime.after' => 'La date de restitution doit être postérieure à la date de remise',
            'end_mileage.gte' => 'Le kilométrage de fin doit être supérieur ou égal au kilométrage de début',
            'estimated_duration_hours.max' => 'La durée ne peut excéder 8760 heures (1 an)',
        ];
    }

    /**
     * Render du composant
     */
    public function render()
    {
        return view('livewire.assignments.assignment-form');
    }
}