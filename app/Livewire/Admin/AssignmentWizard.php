<?php

namespace App\Livewire\Admin;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\VehicleStatus;
use App\Models\DriverStatus;
use App\Services\OverlapCheckService;
use App\Services\StatusTransitionService;
use App\Enums\VehicleStatusEnum;
use App\Enums\DriverStatusEnum;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 🚀 ASSIGNMENT WIZARD - Ultra-Professional Enterprise-Grade
 *
 * Composant d'affectation révolutionnaire en page unique surpassant Fleetio et Samsara.
 *
 * Features Enterprise:
 * - Page unique sans étapes multiples (UX optimale)
 * - Filtrage intelligent véhicules PARKING uniquement
 * - Filtrage chauffeurs DISPONIBLES uniquement
 * - Validation temps réel avec détection conflits
 * - Suggestions automatiques de créneaux
 * - Timeline visuelle avec Gantt preview
 * - Changement automatique statuts (PARKING → AFFECTÉ, DISPONIBLE → EN_MISSION)
 * - Recherche fuzzy temps réel
 * - Cards visuelles avec photos et badges
 * - Analytics instantanées
 * - Mobile-first responsive
 *
 * @version 2.0-Enterprise-Revolution
 */
class AssignmentWizard extends Component
{
    // =========================================================================
    // PROPRIÉTÉS DU FORMULAIRE
    // =========================================================================

    public ?int $selectedVehicleId = null;
    public ?int $selectedDriverId = null;
    public ?string $startDatetime = null;
    public ?string $endDatetime = null;
    public string $reason = '';
    public string $notes = '';
    public bool $isIndefinite = false;

    // =========================================================================
    // RECHERCHE & FILTRES
    // =========================================================================

    public string $vehicleSearch = '';
    public string $driverSearch = '';
    public string $vehicleTypeFilter = '';
    public string $depotFilter = '';

    // =========================================================================
    // ÉTAT DU WIZARD
    // =========================================================================

    public array $conflicts = [];
    public bool $hasConflicts = false;
    public bool $isValidating = false;
    public array $suggestions = [];
    public bool $showSuggestions = false;
    public string $successMessage = '';
    public string $errorMessage = '';

    // =========================================================================
    // SERVICES
    // =========================================================================

    protected OverlapCheckService $overlapService;
    protected StatusTransitionService $statusService;

    /**
     * Injection de dépendances
     */
    public function boot(
        OverlapCheckService $overlapService,
        StatusTransitionService $statusService
    ) {
        $this->overlapService = $overlapService;
        $this->statusService = $statusService;
    }

    /**
     * Initialisation du composant
     */
    public function mount()
    {
        // Initialiser dates par défaut
        $this->startDatetime = now()->addHour()->startOfHour()->format('Y-m-d\TH:i');
        $this->endDatetime = now()->addDays(1)->startOfHour()->format('Y-m-d\TH:i');
    }

    /**
     * Render du composant avec optimisations Enterprise
     */
    public function render()
    {
        return view('livewire.admin.assignment-wizard', [
            'availableVehicles' => $this->availableVehicles,
            'availableDrivers' => $this->availableDrivers,
            'selectedVehicle' => $this->selectedVehicle,
            'selectedDriver' => $this->selectedDriver,
            'analytics' => $this->getAnalytics(),
        ]);
    }

    // =========================================================================
    // COMPUTED PROPERTIES - Optimisation performances
    // =========================================================================

    /**
     * Récupère les véhicules DISPONIBLES (statut PARKING uniquement)
     */
    #[Computed]
    public function availableVehicles()
    {
        $parkingStatus = VehicleStatus::where('slug', 'parking')->first();

        if (!$parkingStatus) {
            Log::warning('Statut PARKING non trouvé - créer via migrations');
            return collect([]);
        }

        $query = Vehicle::with(['vehicleType', 'vehicleStatus', 'depot'])
            ->where('organization_id', auth()->user()->organization_id)
            ->where('status_id', $parkingStatus->id)
            ->where('is_archived', false);

        // Recherche fuzzy
        if ($this->vehicleSearch) {
            $query->where(function ($q) {
                $q->where('registration_plate', 'ILIKE', "%{$this->vehicleSearch}%")
                  ->orWhere('vehicle_name', 'ILIKE', "%{$this->vehicleSearch}%")
                  ->orWhere('brand', 'ILIKE', "%{$this->vehicleSearch}%")
                  ->orWhere('model', 'ILIKE', "%{$this->vehicleSearch}%");
            });
        }

        // Filtre par type
        if ($this->vehicleTypeFilter) {
            $query->where('vehicle_type_id', $this->vehicleTypeFilter);
        }

        // Filtre par dépôt
        if ($this->depotFilter) {
            $query->where('depot_id', $this->depotFilter);
        }

        return $query->orderBy('registration_plate')->get();
    }

    /**
     * Récupère les chauffeurs DISPONIBLES uniquement
     */
    #[Computed]
    public function availableDrivers()
    {
        $disponibleStatus = DriverStatus::where('slug', 'disponible')->first();

        if (!$disponibleStatus) {
            Log::warning('Statut DISPONIBLE non trouvé - créer via migrations');
            return collect([]);
        }

        $query = Driver::with(['driverStatus'])
            ->where('organization_id', auth()->user()->organization_id)
            ->where('status_id', $disponibleStatus->id)
            ->whereNull('deleted_at');

        // Recherche fuzzy
        if ($this->driverSearch) {
            $query->where(function ($q) {
                $q->where('first_name', 'ILIKE', "%{$this->driverSearch}%")
                  ->orWhere('last_name', 'ILIKE', "%{$this->driverSearch}%")
                  ->orWhere('license_number', 'ILIKE', "%{$this->driverSearch}%")
                  ->orWhere('employee_number', 'ILIKE', "%{$this->driverSearch}%");
            });
        }

        return $query->orderBy('last_name')->orderBy('first_name')->get();
    }

    /**
     * Véhicule sélectionné
     */
    #[Computed]
    public function selectedVehicle()
    {
        if (!$this->selectedVehicleId) {
            return null;
        }

        return Vehicle::with(['vehicleType', 'vehicleStatus', 'depot'])
            ->find($this->selectedVehicleId);
    }

    /**
     * Chauffeur sélectionné
     */
    #[Computed]
    public function selectedDriver()
    {
        if (!$this->selectedDriverId) {
            return null;
        }

        return Driver::with(['driverStatus'])->find($this->selectedDriverId);
    }

    // =========================================================================
    // ACTIONS
    // =========================================================================

    /**
     * Sélectionner un véhicule
     */
    public function selectVehicle(int $vehicleId)
    {
        $this->selectedVehicleId = $vehicleId;
        $this->validateInRealTime();

        $this->dispatch('vehicle-selected', ['vehicleId' => $vehicleId]);
    }

    /**
     * Sélectionner un chauffeur
     */
    public function selectDriver(int $driverId)
    {
        $this->selectedDriverId = $driverId;
        $this->validateInRealTime();

        $this->dispatch('driver-selected', ['driverId' => $driverId]);
    }

    /**
     * Validation en temps réel
     */
    public function validateInRealTime()
    {
        if (!$this->selectedVehicleId || !$this->selectedDriverId || !$this->startDatetime) {
            $this->conflicts = [];
            $this->hasConflicts = false;
            return;
        }

        $this->isValidating = true;

        try {
            $endDate = $this->isIndefinite ? null : ($this->endDatetime ?? null);

            $conflicts = $this->overlapService->checkConflicts(
                vehicleId: $this->selectedVehicleId,
                driverId: $this->selectedDriverId,
                startDatetime: $this->startDatetime,
                endDatetime: $endDate
            );

            $this->conflicts = $conflicts;
            $this->hasConflicts = !empty($conflicts);

        } catch (\Exception $e) {
            Log::error('Error validating assignment', [
                'error' => $e->getMessage(),
                'vehicle_id' => $this->selectedVehicleId,
                'driver_id' => $this->selectedDriverId,
            ]);
        } finally {
            $this->isValidating = false;
        }
    }

    /**
     * Toggle durée indéterminée
     */
    public function toggleIndefinite()
    {
        $this->isIndefinite = !$this->isIndefinite;

        if ($this->isIndefinite) {
            $this->endDatetime = null;
        } else {
            $this->endDatetime = Carbon::parse($this->startDatetime)->addDays(1)->format('Y-m-d\TH:i');
        }

        $this->validateInRealTime();
    }

    /**
     * 🎯 VALIDATION ENTERPRISE-GRADE DES DATES
     * 
     * Méthode critique pour validation temps réel des dates d'affectation.
     * Surpasse les standards de Fleetio et Samsara avec:
     * - Validation multi-niveaux
     * - Détection de chevauchements
     * - Suggestions automatiques
     * - Feedback temps réel
     * 
     * @return void
     */
    public function validateDates()
    {
        $this->errorMessage = '';
        $this->successMessage = '';
        
        try {
            // 1. Validation de base - Dates requises
            if (!$this->startDatetime) {
                $this->errorMessage = '⚠️ La date de début est requise.';
                return;
            }
            
            // 2. Parse des dates avec Carbon pour validation
            $startDate = Carbon::parse($this->startDatetime);
            $now = Carbon::now();
            
            // 3. Validation: Date de début ne peut pas être dans le futur trop lointain (1 an max)
            $maxFutureDate = $now->copy()->addYear();
            if ($startDate->greaterThan($maxFutureDate)) {
                $this->errorMessage = '⚠️ La date de début ne peut pas être supérieure à 1 an dans le futur.';
                return;
            }
            
            // 4. Validation: Avertissement pour dates passées (autoriser mais avertir)
            if ($startDate->lessThan($now)) {
                // Permettre les dates passées jusqu'à 3 mois pour régularisation
                $minPastDate = $now->copy()->subMonths(3);
                if ($startDate->lessThan($minPastDate)) {
                    $this->errorMessage = '❌ La date de début ne peut pas être antérieure à 3 mois.';
                    return;
                }
                
                // Avertissement pour date passée
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => '⚠️ Attention: Vous créez une affectation avec une date passée (régularisation).'
                ]);
            }
            
            // 5. Validation de la date de fin si non indéterminée
            if (!$this->isIndefinite && $this->endDatetime) {
                $endDate = Carbon::parse($this->endDatetime);
                
                // La date de fin doit être après la date de début
                if ($endDate->lessThanOrEqualTo($startDate)) {
                    $this->errorMessage = '❌ La date de fin doit être après la date de début.';
                    return;
                }
                
                // Durée minimale: 1 heure
                $duration = $startDate->diffInHours($endDate);
                if ($duration < 1) {
                    $this->errorMessage = '⚠️ La durée minimale d\'une affectation est de 1 heure.';
                    return;
                }
                
                // Durée maximale: 1 an
                if ($duration > 8760) { // 365 * 24
                    $this->errorMessage = '⚠️ La durée maximale d\'une affectation est de 1 an. Utilisez "Durée indéterminée" pour les affectations longues.';
                    return;
                }
                
                // Calcul et affichage de la durée
                $durationFormatted = $this->formatDuration($startDate, $endDate);
                $this->successMessage = "✅ Durée: {$durationFormatted}";
            }
            
            // 6. Vérification des conflits si véhicule et chauffeur sélectionnés
            if ($this->selectedVehicleId || $this->selectedDriverId) {
                $this->checkForConflicts();
                
                if ($this->hasConflicts) {
                    $conflictCount = count($this->conflicts);
                    $this->errorMessage = "⚠️ {$conflictCount} conflit(s) détecté(s). Ajustez les dates ou utilisez la suggestion automatique.";
                    
                    // Suggestion automatique de créneau libre
                    $this->generateSmartSuggestion();
                }
            }
            
            // 7. Validation métier avancée
            $this->validateBusinessRules($startDate, $endDate ?? null);
            
            // 8. Si tout est valide, déclencher la validation temps réel
            if (!$this->errorMessage) {
                $this->validateInRealTime();
                
                if (!$this->hasConflicts) {
                    $this->successMessage = '✅ Dates valides - Aucun conflit détecté';
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur validation dates', [
                'error' => $e->getMessage(),
                'start' => $this->startDatetime,
                'end' => $this->endDatetime
            ]);
            
            $this->errorMessage = '❌ Erreur lors de la validation des dates. Vérifiez le format.';
        }
    }

    /**
     * Vérification des conflits d'affectation
     */
    protected function checkForConflicts()
    {
        if (!$this->selectedVehicleId && !$this->selectedDriverId) {
            return;
        }

        try {
            $endDate = $this->isIndefinite ? null : $this->endDatetime;
            
            // Utiliser le service de détection de conflits
            if (isset($this->overlapService)) {
                $conflicts = $this->overlapService->checkConflicts(
                    vehicleId: $this->selectedVehicleId,
                    driverId: $this->selectedDriverId,
                    startDatetime: $this->startDatetime,
                    endDatetime: $endDate
                );
                
                $this->conflicts = $conflicts;
                $this->hasConflicts = !empty($conflicts);
            }
        } catch (\Exception $e) {
            Log::error('Erreur vérification conflits', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Génération intelligente de suggestions de créneaux
     */
    protected function generateSmartSuggestion()
    {
        try {
            if (!isset($this->overlapService)) {
                return;
            }
            
            $duration = 24; // Durée par défaut en heures
            if (!$this->isIndefinite && $this->endDatetime && $this->startDatetime) {
                $duration = Carbon::parse($this->startDatetime)->diffInHours(Carbon::parse($this->endDatetime));
            }
            
            $suggestion = $this->overlapService->findNextAvailableSlot(
                vehicleId: $this->selectedVehicleId,
                driverId: $this->selectedDriverId,
                durationHours: (int) $duration
            );
            
            if ($suggestion) {
                $this->showSuggestions = true;
                $this->suggestions = [$suggestion];
                
                $this->dispatch('toast', [
                    'type' => 'info',
                    'message' => '💡 Suggestion: Créneau libre disponible à partir du ' . $suggestion['start_formatted']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erreur génération suggestion', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Validation des règles métier avancées
     */
    protected function validateBusinessRules(Carbon $startDate, ?Carbon $endDate)
    {
        // Règle 1: Pas d'affectation le dimanche (configurable)
        if ($startDate->isSunday() && config('zenfleet.assignments.restrict_sunday', false)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => '⚠️ Attention: Affectation un dimanche détectée.'
            ]);
        }
        
        // Règle 2: Vérifier les heures de travail (6h-22h par défaut)
        $startHour = $startDate->hour;
        if ($startHour < 6 || $startHour >= 22) {
            $this->dispatch('toast', [
                'type' => 'info',
                'message' => '🌙 Affectation en dehors des heures normales de travail.'
            ]);
        }
        
        // Règle 3: Durée maximale continue de conduite (9h selon réglementation)
        if ($endDate) {
            $durationHours = $startDate->diffInHours($endDate);
            if ($durationHours > 9) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => '⚠️ Durée supérieure à 9h - Pensez aux pauses réglementaires.'
                ]);
            }
        }
    }

    /**
     * Formatage intelligent de la durée
     */
    protected function formatDuration(Carbon $start, Carbon $end): string
    {
        $diff = $start->diff($end);
        
        $parts = [];
        
        if ($diff->y > 0) {
            $parts[] = $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
        }
        if ($diff->m > 0) {
            $parts[] = $diff->m . ' mois';
        }
        if ($diff->d > 0) {
            $parts[] = $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
        }
        if ($diff->h > 0) {
            $parts[] = $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
        }
        if ($diff->i > 0 && count($parts) < 2) {
            $parts[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        }
        
        return implode(' ', array_slice($parts, 0, 2));
    }

    /**
     * Suggérer un créneau libre
     */
    public function suggestSlot()
    {
        if (!$this->selectedVehicleId || !$this->selectedDriverId) {
            $this->errorMessage = 'Veuillez sélectionner un véhicule et un chauffeur';
            return;
        }

        $duration = $this->endDatetime
            ? Carbon::parse($this->startDatetime)->diffInHours(Carbon::parse($this->endDatetime))
            : 24;

        $slot = $this->overlapService->findNextAvailableSlot(
            vehicleId: $this->selectedVehicleId,
            driverId: $this->selectedDriverId,
            durationHours: (int) $duration
        );

        if ($slot) {
            $this->startDatetime = Carbon::parse($slot['start'])->format('Y-m-d\TH:i');
            $this->endDatetime = $slot['end'] ? Carbon::parse($slot['end'])->format('Y-m-d\TH:i') : null;

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Créneau libre suggéré: ' . $slot['start_formatted']
            ]);

            $this->validateInRealTime();
        } else {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Aucun créneau libre trouvé dans les 30 prochains jours'
            ]);
        }
    }

    /**
     * Valider l'affectation avant création
     */
    public function validateAssignment()
    {
        $this->isValidating = true;
        $this->hasConflicts = false;
        $this->conflicts = [];
        $this->errorMessage = '';
        
        // Validation des champs requis
        if (!$this->selectedVehicleId || !$this->selectedDriverId) {
            $this->errorMessage = 'Veuillez sélectionner un véhicule et un chauffeur.';
            $this->isValidating = false;
            return;
        }
        
        if (!$this->startDatetime) {
            $this->errorMessage = 'La date de début est requise.';
            $this->isValidating = false;
            return;
        }
        
        if (!$this->reason) {
            $this->errorMessage = 'La raison de l\'affectation est requise.';
            $this->isValidating = false;
            return;
        }
        
        // Vérification des conflits
        $this->checkForConflicts();
        
        if ($this->hasConflicts) {
            $this->errorMessage = 'Des conflits ont été détectés. Veuillez ajuster les dates ou utiliser la suggestion automatique.';
        } else {
            $this->successMessage = '✅ Validation réussie. L\'affectation peut être créée.';
            $this->dispatch('assignment-validated');
        }
        
        $this->isValidating = false;
    }

    /**
     * Créer l'affectation (ACTION PRINCIPALE)
     */
    public function createAssignment()
    {
        // Validation des champs obligatoires avec règles Enterprise-Grade
        $minDate = now()->subMonths(3)->format('Y-m-d H:i:s'); // Permettre jusqu'à 3 mois dans le passé
        $maxDate = now()->addYear()->format('Y-m-d H:i:s'); // Maximum 1 an dans le futur
        
        $this->validate([
            'selectedVehicleId' => 'required|exists:vehicles,id',
            'selectedDriverId' => 'required|exists:drivers,id',
            'startDatetime' => "required|date|after:{$minDate}|before:{$maxDate}",
            'endDatetime' => 'nullable|date|after:startDatetime',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ], [
            'startDatetime.after' => 'La date de début ne peut pas être antérieure à 3 mois.',
            'startDatetime.before' => 'La date de début ne peut pas être supérieure à 1 an dans le futur.',
            'endDatetime.after' => 'La date de fin doit être après la date de début.',
        ]);

        // Vérifier conflits
        if ($this->hasConflicts && empty($this->conflicts)) {
            $this->validateInRealTime();
        }

        if ($this->hasConflicts) {
            $this->errorMessage = 'Impossible de créer l\'affectation : des conflits existent';
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => $this->errorMessage
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            // 1. Créer l'affectation
            $assignment = Assignment::create([
                'vehicle_id' => $this->selectedVehicleId,
                'driver_id' => $this->selectedDriverId,
                'start_datetime' => $this->startDatetime,
                'end_datetime' => $this->isIndefinite ? null : $this->endDatetime,
                'reason' => $this->reason,
                'notes' => $this->notes,
                'status' => 'active',
                'organization_id' => auth()->user()->organization_id,
                'created_by_user_id' => auth()->id(),
            ]);

            // 2. Changer statut véhicule: PARKING → AFFECTÉ
            $vehicle = Vehicle::find($this->selectedVehicleId);
            $this->statusService->changeVehicleStatus(
                $vehicle,
                VehicleStatusEnum::AFFECTE,
                [
                    'reason' => "Affectation #{$assignment->id} au chauffeur {$this->selectedDriver->full_name}",
                    'metadata' => ['assignment_id' => $assignment->id],
                ]
            );

            // 3. Changer statut chauffeur: DISPONIBLE → EN_MISSION
            $driver = Driver::find($this->selectedDriverId);
            $this->statusService->changeDriverStatus(
                $driver,
                DriverStatusEnum::EN_MISSION,
                [
                    'reason' => "Affectation #{$assignment->id} du véhicule {$vehicle->registration_plate}",
                    'metadata' => ['assignment_id' => $assignment->id],
                ]
            );

            DB::commit();

            // Reset formulaire
            $this->reset([
                'selectedVehicleId',
                'selectedDriverId',
                'reason',
                'notes',
                'conflicts',
                'hasConflicts',
                'errorMessage'
            ]);

            $this->startDatetime = now()->addHour()->startOfHour()->format('Y-m-d\TH:i');
            $this->endDatetime = now()->addDays(1)->startOfHour()->format('Y-m-d\TH:i');

            $this->successMessage = "Affectation créée avec succès ! Véhicule et chauffeur passés en service.";

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $this->successMessage
            ]);

            $this->dispatch('assignment-created', ['assignmentId' => $assignment->id]);

            // Redirection optionnelle
            // return redirect()->route('admin.assignments.index');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create assignment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'vehicle_id' => $this->selectedVehicleId,
                'driver_id' => $this->selectedDriverId,
            ]);

            $this->errorMessage = 'Erreur lors de la création: ' . $e->getMessage();

            $this->dispatch('toast', [
                'type' => 'error',
                'message' => $this->errorMessage
            ]);
        }
    }

    /**
     * Analytics pour dashboard
     */
    protected function getAnalytics(): array
    {
        $organizationId = auth()->user()->organization_id;

        return [
            'total_vehicles_parking' => $this->availableVehicles->count(),
            'total_drivers_available' => $this->availableDrivers->count(),
            'active_assignments' => Assignment::where('organization_id', $organizationId)
                ->where('status', 'active')
                ->whereNull('end_datetime')
                ->orWhere('end_datetime', '>', now())
                ->count(),
        ];
    }
}
