<?php

namespace App\Livewire;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Services\OverlapCheckService;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * 📝 Composant Formulaire d'Affectation - Enterprise Grade
 *
 * Fonctionnalités selon spécifications:
 * - Validation temps réel avec détection de conflits
 * - Auto-suggestions de créneaux libres
 * - Support durées indéterminées (end_datetime = NULL)
 * - UX optimisée avec feedback visuel immédiat
 * - Accessibilité WAI-ARIA complète
 */
class AssignmentForm extends Component
{
    use AuthorizesRequests;

    // Props du composant
    public ?Assignment $assignment = null;
    public bool $isEditing = false;

    // Données du formulaire
    #[Validate('required|exists:vehicles,id')]
    public string $vehicle_id = '';

    #[Validate('required|exists:drivers,id')]
    public string $driver_id = '';

    #[Validate('required|date|after:now')]
    public string $start_datetime = '';

    #[Validate('nullable|date|after:start_datetime')]
    public string $end_datetime = '';

    #[Validate('nullable|string|max:500')]
    public string $reason = '';

    #[Validate('nullable|string|max:1000')]
    public string $notes = '';

    // État de validation
    public array $conflicts = [];
    public array $suggestions = [];
    public bool $hasConflicts = false;
    public bool $isValidating = false;
    public bool $forceCreate = false;

    // Options pour les selects
    public $vehicleOptions = [];
    public $driverOptions = [];

    protected OverlapCheckService $overlapService;

    public function boot(OverlapCheckService $overlapService)
    {
        $this->overlapService = $overlapService;
    }

    public function mount(?Assignment $assignment = null)
    {
        if ($assignment) {
            $this->assignment = $assignment;
            $this->isEditing = true;
            $this->authorize('update', $assignment);
            $this->fillFromAssignment($assignment);
        } else {
            $this->authorize('create', Assignment::class);
            $this->initializeNewAssignment();
        }

        $this->loadOptions();
    }

    public function render()
    {
        return view('livewire.assignment-form', [
            'vehicleOptions' => $this->vehicleOptions,
            'driverOptions' => $this->driverOptions,
        ]);
    }

    /**
     * Validation temps réel des conflits
     */
    public function updatedVehicleId()
    {
        $this->validateAssignment();
    }

    public function updatedDriverId()
    {
        $this->validateAssignment();
    }

    public function updatedStartDatetime()
    {
        $this->validateAssignment();
    }

    public function updatedEndDatetime()
    {
        $this->validateAssignment();
    }

    /**
     * Validation des conflits d'affectation
     */
    public function validateAssignment()
    {
        if (empty($this->vehicle_id) || empty($this->driver_id) || empty($this->start_datetime)) {
            $this->resetValidation();
            return;
        }

        $this->isValidating = true;

        try {
            $start = Carbon::parse($this->start_datetime);
            $end = $this->end_datetime ? Carbon::parse($this->end_datetime) : null;

            $result = $this->overlapService->checkOverlap(
                vehicleId: (int) $this->vehicle_id,
                driverId: (int) $this->driver_id,
                start: $start,
                end: $end,
                excludeId: $this->assignment?->id
            );

            $this->hasConflicts = $result['has_conflicts'];
            $this->conflicts = $result['conflicts'];
            $this->suggestions = $result['suggestions'];

            // Feedback visuel
            if ($this->hasConflicts) {
                $this->dispatch('conflicts-detected', [
                    'conflicts' => $this->conflicts,
                    'suggestions' => $this->suggestions
                ]);
            } else {
                $this->dispatch('conflicts-cleared');
            }

        } catch (\Exception $e) {
            $this->addError('validation', 'Erreur lors de la validation: ' . $e->getMessage());
        } finally {
            $this->isValidating = false;
        }
    }

    /**
     * Suggestions automatiques de créneaux
     */
    public function suggestNextSlot()
    {
        if (empty($this->vehicle_id) || empty($this->driver_id)) {
            $this->addError('suggestion', 'Veuillez sélectionner un véhicule et un chauffeur.');
            return;
        }

        $duration = $this->end_datetime ?
            Carbon::parse($this->start_datetime)->diffInHours(Carbon::parse($this->end_datetime)) :
            24; // Durée par défaut

        $slot = $this->overlapService->findNextAvailableSlot(
            vehicleId: (int) $this->vehicle_id,
            driverId: (int) $this->driver_id,
            durationHours: (int) $duration
        );

        if ($slot) {
            $this->start_datetime = $slot['start'];
            $this->end_datetime = $slot['end'];
            $this->validateAssignment();

            $this->dispatch('slot-suggested', [
                'message' => 'Créneau libre suggéré: ' . $slot['start_formatted'] . ' - ' . $slot['end_formatted']
            ]);
        } else {
            $this->addError('suggestion', 'Aucun créneau libre trouvé dans les 30 prochains jours.');
        }
    }

    /**
     * Appliquer une suggestion de créneau
     */
    public function applySuggestion(int $index)
    {
        if (isset($this->suggestions[$index])) {
            $suggestion = $this->suggestions[$index];
            $this->start_datetime = $suggestion['start'];
            $this->end_datetime = $suggestion['end'];
            $this->validateAssignment();

            $this->dispatch('suggestion-applied', [
                'message' => 'Créneau appliqué: ' . $suggestion['description']
            ]);
        }
    }

    /**
     * Forcer la création malgré les conflits
     */
    public function toggleForceCreate()
    {
        $this->forceCreate = !$this->forceCreate;

        if ($this->forceCreate) {
            $this->dispatch('force-mode-enabled', [
                'message' => '⚠️ Mode force activé - Les conflits seront ignorés'
            ]);
        } else {
            $this->dispatch('force-mode-disabled');
        }
    }

    /**
     * Sauvegarde de l'affectation
     */
    public function save()
    {
        // Validation Laravel standard
        $this->validate();

        // Validation métier si pas en mode force
        if (!$this->forceCreate) {
            $validation = $this->overlapService->validateAssignment(
                vehicleId: (int) $this->vehicle_id,
                driverId: (int) $this->driver_id,
                start: Carbon::parse($this->start_datetime),
                end: $this->end_datetime ? Carbon::parse($this->end_datetime) : null,
                excludeId: $this->assignment?->id
            );

            if (!$validation['is_valid']) {
                foreach ($validation['errors'] as $error) {
                    $this->addError('business_validation', $error);
                }
                return;
            }
        }

        try {
            $data = [
                'organization_id' => auth()->user()->organization_id,
                'vehicle_id' => (int) $this->vehicle_id,
                'driver_id' => (int) $this->driver_id,
                'start_datetime' => Carbon::parse($this->start_datetime),
                'end_datetime' => $this->end_datetime ? Carbon::parse($this->end_datetime) : null,
                'reason' => $this->reason ?: null,
                'notes' => $this->notes ?: null,
            ];

            if ($this->isEditing) {
                $this->assignment->update($data);
                $message = 'Affectation modifiée avec succès.';
                $event = 'assignment-updated';
            } else {
                $this->assignment = Assignment::create($data);
                $message = 'Affectation créée avec succès.';
                $event = 'assignment-created';
            }

            $this->dispatch($event, [
                'assignment' => $this->assignment,
                'message' => $message
            ]);

            // Réinitialiser si création
            if (!$this->isEditing) {
                $this->reset([
                    'vehicle_id', 'driver_id', 'start_datetime',
                    'end_datetime', 'reason', 'notes', 'forceCreate'
                ]);
                $this->resetValidation();
            }

        } catch (\Exception $e) {
            $this->addError('save', 'Erreur lors de la sauvegarde: ' . $e->getMessage());
        }
    }

    /**
     * Duplication d'affectation existante
     */
    #[On('open-assignment-form')]
    public function handleOpenForm($data = [])
    {
        if (isset($data['prefill']) && $data['prefill']) {
            $this->vehicle_id = $data['vehicle_id'] ?? '';
            $this->driver_id = $data['driver_id'] ?? '';
            $this->start_datetime = $data['start_datetime'] ?? '';
            $this->reason = $data['reason'] ?? '';

            $this->validateAssignment();
        }
    }

    /**
     * Méthodes utilitaires
     */
    private function fillFromAssignment(Assignment $assignment)
    {
        $this->vehicle_id = (string) $assignment->vehicle_id;
        $this->driver_id = (string) $assignment->driver_id;
        $this->start_datetime = $assignment->start_datetime->format('Y-m-d\TH:i');
        $this->end_datetime = $assignment->end_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->reason = $assignment->reason ?? '';
        $this->notes = $assignment->notes ?? '';
    }

    private function initializeNewAssignment()
    {
        $this->start_datetime = now()->addHour()->format('Y-m-d\TH:i');
        $this->end_datetime = '';
        $this->reason = '';
        $this->notes = '';
    }

    private function loadOptions()
    {
        $organizationId = auth()->user()->organization_id;

        $this->vehicleOptions = Vehicle::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->select('id', 'registration_plate', 'brand', 'model')
            ->orderBy('registration_plate')
            ->get();

        $this->driverOptions = Driver::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->select('id', 'first_name', 'last_name', 'license_number')
            ->orderBy('last_name')
            ->get();
    }

    private function resetValidation()
    {
        $this->conflicts = [];
        $this->suggestions = [];
        $this->hasConflicts = false;
        $this->isValidating = false;
    }

    /**
     * Getters pour la vue
     */
    public function getSelectedVehicleProperty()
    {
        return $this->vehicleOptions->firstWhere('id', $this->vehicle_id);
    }

    public function getSelectedDriverProperty()
    {
        return $this->driverOptions->firstWhere('id', $this->driver_id);
    }

    public function getDurationHoursProperty(): ?float
    {
        if (!$this->start_datetime || !$this->end_datetime) {
            return null;
        }

        try {
            $start = Carbon::parse($this->start_datetime);
            $end = Carbon::parse($this->end_datetime);
            return $start->diffInHours($end, true);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFormattedDurationProperty(): string
    {
        $hours = $this->duration_hours;

        if ($hours === null) {
            return 'Durée indéterminée';
        }

        if ($hours < 1) {
            return round($hours * 60) . ' min';
        }

        if ($hours < 24) {
            return round($hours, 1) . 'h';
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        return $days . 'j' . ($remainingHours > 0 ? ' ' . round($remainingHours, 1) . 'h' : '');
    }
}