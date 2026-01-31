<?php

namespace App\Livewire;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\VehicleMileageReading;
use App\Services\OverlapCheckService;
use App\Services\RetroactiveAssignmentService;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    // 🆕 SÉPARATION DATE ET HEURE (ENTERPRISE V3)
    // 📅 FORMAT FRANÇAIS DD/MM/YYYY - date_format:d/m/Y
    #[Validate('required|date_format:d/m/Y')]
    public string $start_date = '';

    #[Validate('required|string')]
    public string $start_time = '08:00';

    #[Validate('nullable|date_format:d/m/Y')]
    public string $end_date = '';

    #[Validate('nullable|string')]
    public string $end_time = '18:00';

    // Propriétés combinées (pour compatibilité)
    public string $start_datetime = '';
    public string $end_datetime = '';

    #[Validate('nullable|string|max:500')]
    public string $reason = '';

    #[Validate('nullable|string|max:1000')]
    public string $notes = '';

    // 🆕 KILOMÉTRAGE AVEC MISE À JOUR DYNAMIQUE
    #[Validate('nullable|integer|min:0')]
    public ?int $start_mileage = null;

    public ?int $current_vehicle_mileage = null;
    public bool $updateVehicleMileage = true; // Par défaut, met à jour le véhicule
    public bool $mileageModified = false;

    // État de validation
    public array $conflicts = [];
    public array $suggestions = [];
    public bool $hasConflicts = false;
    public bool $isValidating = false;
    public bool $forceCreate = false;

    // 🆕 GESTION DES AFFECTATIONS RÉTROACTIVES (ENTERPRISE V4)
    public bool $isRetroactive = false;
    public array $retroactiveValidation = [];
    public array $historicalWarnings = [];
    public ?int $confidenceScore = null;
    public bool $allowPastDates = true; // Autoriser les dates passées

    // Options pour les selects
    public $vehicleOptions = [];
    public $driverOptions = [];

    protected OverlapCheckService $overlapService;
    protected RetroactiveAssignmentService $retroactiveService;

    public function boot(OverlapCheckService $overlapService, RetroactiveAssignmentService $retroactiveService)
    {
        $this->overlapService = $overlapService;
        $this->retroactiveService = $retroactiveService;
    }

    public function mount($assignmentId = null)
    {
        \Log::info('[ROOT AssignmentForm] mount() appelé - app/Livewire/AssignmentForm.php', [
            'assignmentId' => $assignmentId,
            'assignmentId_type' => gettype($assignmentId)
        ]);

        if ($assignmentId) {
            \Log::info('[ROOT AssignmentForm] Mode ÉDITION détecté', ['assignmentId' => $assignmentId]);
            $this->assignment = Assignment::findOrFail($assignmentId);
            $this->isEditing = true;
            $this->authorize('update', $this->assignment);
            $this->fillFromAssignment($this->assignment);
        } else {
            \Log::info('[ROOT AssignmentForm] Mode CRÉATION détecté - Tentative authorize create');
            $this->authorize('create', Assignment::class);
            \Log::info('[ROOT AssignmentForm] ✅ AUTHORIZE PASSED!');
            $this->initializeNewAssignment();
        }

        \Log::info('[ROOT AssignmentForm] Avant loadOptions()');
        $this->loadOptions();
        
        // Formater les dates pour l'affichage dans le formulaire
        $this->formatDatesForDisplay();
        
        \Log::info('[ROOT AssignmentForm] Après loadOptions() - mount() terminé avec succès');
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
        // Charger le kilométrage actuel du véhicule sélectionné
        if ($this->vehicle_id) {
            $vehicle = Vehicle::find($this->vehicle_id);
            if ($vehicle) {
                $this->current_vehicle_mileage = $vehicle->current_mileage;
                // Pré-remplir le kilométrage de départ si vide et pas encore modifié
                if ($this->start_mileage === null && $vehicle->current_mileage) {
                    $this->start_mileage = $vehicle->current_mileage;
                    $this->mileageModified = false;
                }
            }
        } else {
            $this->current_vehicle_mileage = null;
        }

        $this->combineDateTime();
        $this->validateAssignment();
    }

    /**
     * Mise à jour directe lors de la sélection d'un véhicule (pour affichage immédiat)
     */
    public function updatedVehicleIdLive()
    {
        // Charger le kilométrage actuel du véhicule sélectionné immédiatement
        if ($this->vehicle_id) {
            $vehicle = Vehicle::find($this->vehicle_id);
            if ($vehicle) {
                $this->current_vehicle_mileage = $vehicle->current_mileage;
                // Pré-remplir le kilométrage de départ si vide
                if ($this->start_mileage === null && $vehicle->current_mileage) {
                    $this->start_mileage = $vehicle->current_mileage;
                    $this->mileageModified = false;
                }
            }
        } else {
            $this->current_vehicle_mileage = null;
        }
    }

    public function updatedDriverId()
    {
        $this->combineDateTime();
        $this->validateAssignment();
    }

    // 🆕 WATCHERS POUR DATE/HEURE SÉPARÉES
    public function updatedStartDate()
    {
        if ($this->start_date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->start_date)) {
            $this->start_date = $this->formatDateForDisplay($this->start_date);
        }

        // 🔥 ENTERPRISE FIX: NE PAS convertir ici pour garder le format français dans l'UI
        // La conversion vers ISO se fera temporairement dans combineDateTime()
        // Cela évite que Livewire envoie une valeur ISO au navigateur que Flatpickr ne peut pas parser
        
        $this->combineDateTime();
        
        // 🔍 DÉTECTION AFFECTATION RÉTROACTIVE
        $this->checkIfRetroactive();
        
        $this->validateAssignment();
    }

    public function updatedStartTime()
    {
        $this->combineDateTime();
        $this->validateAssignment();
    }

    public function updatedEndDate()
    {
        if ($this->end_date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->end_date)) {
            $this->end_date = $this->formatDateForDisplay($this->end_date);
        }

        // 🔥 ENTERPRISE FIX: NE PAS convertir ici, garder format français
        // La conversion se fera dans combineDateTime()

        $this->combineDateTime();
        $this->validateAssignment();

        // 🔥 CORRECTION : Réinitialiser le SlimSelect end_time quand end_date change
        // Cela permet d'initialiser le SlimSelect quand le champ end_time apparaît
        $this->dispatch('reinit-end-time');
    }

    public function updatedEndTime()
    {
        $this->combineDateTime();
        $this->validateAssignment();
    }

    public function updatedStartMileage()
    {
        $this->mileageModified = true;
    }

    /**
     * 🔥 ENTERPRISE GRADE: Charge le kilométrage du véhicule sans validation
     * Méthode optimisée appelée par JavaScript lors de la sélection du véhicule
     *
     * @return void
     */
    public function loadVehicleMileage()
    {
        if (!$this->vehicle_id) {
            $this->current_vehicle_mileage = null;
            $this->start_mileage = null;
            return;
        }

        $vehicle = Vehicle::select('id', 'current_mileage')
            ->find($this->vehicle_id);

        if (!$vehicle) {
            \Log::warning('[AssignmentForm] Véhicule non trouvé', ['vehicle_id' => $this->vehicle_id]);
            $this->current_vehicle_mileage = null;
            $this->start_mileage = null;
            return;
        }

        // Mettre à jour le kilométrage actuel du véhicule
        $this->current_vehicle_mileage = $vehicle->current_mileage ?? 0;

        // Pré-remplir le kilométrage de départ si vide et pas encore modifié
        if ($this->start_mileage === null || !$this->mileageModified) {
            $this->start_mileage = $vehicle->current_mileage ?? 0;
            $this->mileageModified = false;
        }

        \Log::info('[AssignmentForm] Kilométrage chargé', [
            'vehicle_id' => $this->vehicle_id,
            'current_mileage' => $this->current_vehicle_mileage,
            'start_mileage' => $this->start_mileage,
        ]);
    }

    /**
     * 🆕 ENTERPRISE V4: Combine date et heure avec conversion ISO temporaire
     * Cette méthode convertit les dates du format français vers ISO pour créer des datetime valides,
     * SANS modifier les propriétés start_date et end_date (qui restent en français pour l'UI)
     */
    private function combineDateTime(): void
    {
        // Combiner date et heure de début
        if ($this->start_date && $this->start_time) {
            // Convertir temporairement vers ISO si nécessaire
            $startDateISO = $this->convertToISO($this->start_date);
            $this->start_datetime = $startDateISO . ' ' . $this->start_time;
        }

        // Combiner date et heure de fin (si présentes)
        if ($this->end_date && $this->end_time) {
            // Convertir temporairement vers ISO si nécessaire
            $endDateISO = $this->convertToISO($this->end_date);
            $this->end_datetime = $endDateISO . ' ' . $this->end_time;
        } elseif (!$this->end_date) {
            $this->end_datetime = '';
        }
    }

    /**
     * 🔥 ENTERPRISE GRADE: Convertit une date vers ISO SANS modifier la propriété source
     * Retourne une version ISO de la date pour utilisation interne
     * 
     * @param string $date Date au format français ou ISO
     * @return string Date au format ISO
     */
    private function convertToISO(string $date): string
    {
        if (empty($date)) {
            return '';
        }

        // Si déjà au format ISO, retourner tel quel
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // Convertir du format français vers ISO
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $date, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];
            
            // Validation de la date
            if (checkdate((int)$month, (int)$day, (int)$year)) {
                return "$year-$month-$day";
            }
        }

        // Si échec, retourner la valeur originale
        return $date;
    }

    /**
     * 🔥 ENTERPRISE GRADE: Convertit une date du format français (d/m/Y) vers ISO (Y-m-d)
     * Gère intelligemment les différents formats possibles
     * 
     * @param string $property Nom de la propriété à convertir
     * @return void
     */
    private function convertDateFromFrenchFormat(string $property): void
    {
        if (empty($this->$property)) {
            return;
        }

        try {
            $dateValue = $this->$property;
            
            // Si déjà au format ISO (Y-m-d), ne rien faire
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
                return;
            }
            
            // Convertir du format français (d/m/Y ou d-m-Y) vers ISO
            if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $dateValue, $matches)) {
                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $year = $matches[3];
                
                // Validation basique de la date
                if (checkdate((int)$month, (int)$day, (int)$year)) {
                    $this->$property = "$year-$month-$day";
                } else {
                    // Date invalide, réinitialiser
                    $this->addError($property, "La date saisie n'est pas valide.");
                    $this->$property = '';
                }
            }
        } catch (\Exception $e) {
            \Log::error('[AssignmentForm] Erreur conversion date', [
                'property' => $property,
                'value' => $this->$property ?? null,
                'error' => $e->getMessage()
            ]);
            $this->$property = '';
        }
    }

    /**
     * 🔥 ENTERPRISE GRADE: Convertit une date du format ISO (Y-m-d) vers français (d/m/Y)
     * Pour l'affichage dans les champs de formulaire
     * 
     * @param string $date Date au format ISO
     * @return string Date au format français
     */
    private function formatDateForDisplay(string $date): string
    {
        if (empty($date)) {
            return '';
        }

        try {
            // Si déjà au format français, retourner tel quel
            if (preg_match('/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}$/', $date)) {
                return str_replace('-', '/', $date);
            }
            
            // Convertir du format ISO vers français
            if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $date, $matches)) {
                return $matches[3] . '/' . $matches[2] . '/' . $matches[1];
            }
            
            // Essayer avec Carbon comme fallback
            return Carbon::parse($date)->format('d/m/Y');
        } catch (\Exception $e) {
            \Log::error('[AssignmentForm] Erreur formatage date pour affichage', [
                'date' => $date,
                'error' => $e->getMessage()
            ]);
            return $date;
        }
    }

    /**
     * 🔥 ENTERPRISE GRADE V2: Formate les dates ISO vers français pour l'affichage
     * Convertit UNIQUEMENT les dates au format ISO, laisse les dates françaises intactes
     * Utilisé après fillFromAssignment() pour convertir les dates venant de la BDD
     * 
     * @return void
     */
    private function formatDatesForDisplay(): void
    {
        // Formater la date de début SI elle est au format ISO
        // Les dates déjà en français ne sont pas touchées
        if ($this->start_date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->start_date)) {
            $this->start_date = $this->formatDateForDisplay($this->start_date);
        }
        
        // Formater la date de fin SI elle est au format ISO
        if ($this->end_date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->end_date)) {
            $this->end_date = $this->formatDateForDisplay($this->end_date);
        }
    }

    /**
     * 🕐 ENTERPRISE GRADE: Vérifie si l'affectation est rétroactive
     * Détecte les dates passées et active les validations historiques
     * 
     * @return void
     */
    private function checkIfRetroactive(): void
    {
        if (empty($this->start_date)) {
            $this->isRetroactive = false;
            return;
        }

        try {
            // Convertir la date au format Carbon pour comparaison
            $startDate = $this->start_datetime ? Carbon::parse($this->start_datetime) : null;
            
            if ($startDate && $startDate->isPast()) {
                $this->isRetroactive = true;
                
                // Calculer le nombre de jours dans le passé
                $daysInPast = $startDate->diffInDays(now());
                
                // Afficher un message informatif
                if ($daysInPast > 0) {
                    $this->dispatch('retroactive-detected', [
                        'message' => "⚠️ Affectation rétroactive détectée ({$daysInPast} jour(s) dans le passé)",
                        'days' => $daysInPast
                    ]);
                }
            } else {
                $this->isRetroactive = false;
            }
        } catch (\Exception $e) {
            \Log::error('[AssignmentForm] Erreur détection rétroactive', [
                'error' => $e->getMessage(),
                'start_date' => $this->start_date
            ]);
            $this->isRetroactive = false;
        }
    }

    /**
     * Validation des conflits d'affectation
     */
    public function validateAssignment()
    {
        if (empty($this->vehicle_id) || empty($this->driver_id) || empty($this->start_datetime)) {
            $this->resetConflictsValidation();
            return;
        }

        $this->isValidating = true;

        try {
            $start = Carbon::parse($this->start_datetime);
            $end = $this->end_datetime ? Carbon::parse($this->end_datetime) : null;

            // 🕐 VALIDATION RÉTROACTIVE si date passée
            if ($this->isRetroactive) {
                $retroValidation = $this->retroactiveService->validateRetroactiveAssignment(
                    vehicleId: (int) $this->vehicle_id,
                    driverId: (int) $this->driver_id,
                    startDate: $start,
                    endDate: $end,
                    organizationId: auth()->user()->organization_id
                );

                $this->retroactiveValidation = $retroValidation;
                $this->historicalWarnings = $retroValidation['warnings'] ?? [];
                $this->confidenceScore = $retroValidation['confidence_score']['score'] ?? null;

                // Ajouter les erreurs historiques aux erreurs standard
                if (!$retroValidation['is_valid']) {
                    foreach ($retroValidation['errors'] as $error) {
                        $this->addError('historical_validation', $error['message']);
                    }
                }

                // Dispatch event pour afficher les warnings historiques
                if (count($this->historicalWarnings) > 0) {
                    $this->dispatch('historical-warnings', [
                        'warnings' => $this->historicalWarnings,
                        'confidence_score' => $this->confidenceScore,
                        'recommendations' => $retroValidation['recommendations'] ?? []
                    ]);
                }
            }

            // Validation standard des conflits
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
            } else if (!$this->isRetroactive) {
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
        // 🔥 ENTERPRISE FIX V2: NE PAS convertir les dates ici
        // Les dates restent en français dans start_date et end_date
        // combineDateTime() fait la conversion temporaire pour créer start_datetime et end_datetime en ISO
        
        // Combiner date et heure avant validation
        $this->combineDateTime();

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
            DB::beginTransaction();

            $data = [
                'organization_id' => auth()->user()->organization_id,
                'vehicle_id' => (int) $this->vehicle_id,
                'driver_id' => (int) $this->driver_id,
                'start_datetime' => Carbon::parse($this->start_datetime),
                'end_datetime' => $this->end_datetime ? Carbon::parse($this->end_datetime) : null,
                'start_mileage' => $this->start_mileage,
                'reason' => $this->reason ?: null,
                'notes' => $this->notes ?: null,
            ];

            // 🔍 DIAGNOSTIC : Logger les données avant création/mise à jour
            \Log::info('[AssignmentForm] 📝 Data prepared for Assignment', [
                'start_datetime_string' => $this->start_datetime,
                'end_datetime_string' => $this->end_datetime,
                'start_datetime_carbon' => $data['start_datetime']->toIso8601String(),
                'end_datetime_carbon' => $data['end_datetime'] ? $data['end_datetime']->toIso8601String() : null,
                'start_timestamp' => $data['start_datetime']->timestamp,
                'end_timestamp' => $data['end_datetime'] ? $data['end_datetime']->timestamp : null,
                'comparison' => $data['end_datetime'] ? ($data['start_datetime'] < $data['end_datetime'] ? 'start < end ✓' : 'start >= end ✗') : 'no end',
            ]);

            if ($this->isEditing) {
                $this->assignment->update($data);
                $message = 'Affectation modifiée avec succès.';
                $event = 'assignment-updated';
            } else {
                $this->assignment = Assignment::create($data);
                $message = 'Affectation créée avec succès.';
                $event = 'assignment-created';
            }

            // 🆕 ENTERPRISE V3: Mise à jour du kilométrage du véhicule avec historique
            if ($this->updateVehicleMileage && $this->start_mileage && $this->mileageModified) {
                $this->updateVehicleMileageWithHistory();
            }

            DB::commit();

            $this->dispatch($event, [
                'assignment' => $this->assignment,
                'message' => $message
            ]);

            // Réinitialiser si création
            if (!$this->isEditing) {
                $this->reset([
                    'vehicle_id', 'driver_id', 'start_date', 'start_time',
                    'end_date', 'end_time', 'start_datetime', 'end_datetime',
                    'start_mileage', 'reason', 'notes', 'forceCreate',
                    'mileageModified', 'updateVehicleMileage'
                ]);
                $this->resetConflictsValidation();
                parent::resetValidation();
                $this->current_vehicle_mileage = null;
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('save', 'Erreur lors de la sauvegarde: ' . $e->getMessage());
        }
    }

    /**
     * 🆕 ENTERPRISE V3: Met à jour le kilométrage du véhicule et crée l'historique
     */
    private function updateVehicleMileageWithHistory(): void
    {
        $vehicle = Vehicle::find($this->vehicle_id);
        if (!$vehicle) return;

        $user = auth()->user();
        $oldMileage = $vehicle->current_mileage;

        // Vérification que le nouveau kilométrage est supérieur
        if ($this->start_mileage <= $oldMileage) {
            throw new \Exception("Le kilométrage doit être supérieur au kilométrage actuel ({$oldMileage} km)");
        }

        // Mettre à jour le véhicule
        $vehicle->current_mileage = $this->start_mileage;
        $vehicle->save();

        // Créer l'entrée dans l'historique kilométrique (VehicleMileageReading)
        VehicleMileageReading::create([
            'organization_id' => $user->organization_id,
            'vehicle_id' => $vehicle->id,
            'recorded_at' => now(),
            'mileage' => $this->start_mileage,
            'recorded_by_id' => $user->id,
            'recording_method' => VehicleMileageReading::METHOD_MANUAL,
            'notes' => sprintf(
                'Mise à jour lors de l\'affectation #%d - Ancien: %s km, Nouveau: %s km',
                $this->assignment->id,
                number_format($oldMileage),
                number_format($this->start_mileage)
            ),
        ]);

        \Log::info('[AssignmentForm] Kilométrage mis à jour', [
            'vehicle_id' => $vehicle->id,
            'old_mileage' => $oldMileage,
            'new_mileage' => $this->start_mileage,
            'assignment_id' => $this->assignment->id,
            'updated_by' => $user->id,
        ]);
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

        // Séparation date et heure - Format ISO pour le stockage interne
        if ($assignment->start_datetime) {
            $this->start_date = $assignment->start_datetime->format('Y-m-d');
            $this->start_time = $assignment->start_datetime->format('H:i');
            $this->start_datetime = $assignment->start_datetime->format('Y-m-d H:i');
        }

        if ($assignment->end_datetime) {
            $this->end_date = $assignment->end_datetime->format('Y-m-d');
            $this->end_time = $assignment->end_datetime->format('H:i');
            $this->end_datetime = $assignment->end_datetime->format('Y-m-d H:i');
        } else {
            $this->end_date = '';
            $this->end_time = '18:00';
            $this->end_datetime = '';
        }
        
        // Les dates seront formatées en français dans mount() via formatDatesForDisplay()

        $this->start_mileage = $assignment->start_mileage;
        $this->reason = $assignment->reason ?? '';
        $this->notes = $assignment->notes ?? '';

        // Charger le kilométrage actuel du véhicule
        if ($assignment->vehicle) {
            $this->current_vehicle_mileage = $assignment->vehicle->current_mileage;
        }

        $this->mileageModified = false;
    }

    private function initializeNewAssignment()
    {
        // 🔥 ENTERPRISE FIX V2: Date de début = aujourd'hui au format FRANÇAIS
        // On garde le format français dans la propriété pour compatibilité Flatpickr
        // La conversion vers ISO se fera automatiquement dans combineDateTime()
        $this->start_date = now()->format('d/m/Y');
        $this->start_time = '08:00';

        // Fin vide par défaut (durée indéterminée)
        $this->end_date = '';
        $this->end_time = '18:00';

        $this->reason = '';
        $this->notes = '';

        // 🔥 PAS DE CONVERSION ICI: start_date reste en français
        // combineDateTime() fera la conversion temporaire pour créer start_datetime en ISO
        $this->combineDateTime();

        $this->mileageModified = false;
    }

    private function loadOptions()
    {
        $organizationId = auth()->user()->organization_id;
        $hasVehicleAvailability = Schema::hasColumn('vehicles', 'is_available')
            && Schema::hasColumn('vehicles', 'assignment_status');
        $hasVehicleCurrentDriver = Schema::hasColumn('vehicles', 'current_driver_id');

        $hasDriverAvailability = Schema::hasColumn('drivers', 'is_available')
            && Schema::hasColumn('drivers', 'assignment_status');
        $hasDriverCurrentVehicle = Schema::hasColumn('drivers', 'current_vehicle_id');

        $vehicleBaseQuery = Vehicle::where('organization_id', $organizationId)
            ->when(Schema::hasColumn('vehicles', 'is_archived'), function ($query) {
                $query->where('is_archived', false);
            })
            ->select('id', 'registration_plate', 'brand', 'model', 'current_mileage')
            ->orderBy('registration_plate');

        $vehicleAvailabilityQuery = (clone $vehicleBaseQuery);

        if ($hasVehicleAvailability) {
            $vehicleAvailabilityQuery->where('is_available', true)
                ->where('assignment_status', 'available');

            if ($hasVehicleCurrentDriver) {
                $vehicleAvailabilityQuery->whereNull('current_driver_id');
            }
        }

        $vehicleOptions = $hasVehicleAvailability ? $vehicleAvailabilityQuery->get() : collect();

        if ($vehicleOptions->isEmpty()) {
            $vehicleOptions = (clone $vehicleBaseQuery)
                ->when($hasVehicleCurrentDriver, function ($query) {
                    $query->whereNull('current_driver_id');
                })
                ->where(function ($query) {
                    $query->whereHas('vehicleStatus', function ($statusQuery) {
                        $statusQuery->where('is_active', true)
                            ->where(function ($statusScope) {
                                $statusScope->where('can_be_assigned', true)
                                    ->orWhereIn('slug', ['parking', 'available'])
                                    ->orWhereIn('name', ['Parking', 'Disponible', 'Available']);
                            });
                    })->orWhereDoesntHave('vehicleStatus');
                })
                ->get();
        }

        $this->vehicleOptions = $vehicleOptions;

        $driverBaseQuery = Driver::where('organization_id', $organizationId)
            ->select('id', 'first_name', 'last_name', 'license_number')
            ->orderBy('last_name')
            ->orderBy('first_name');

        $driverAvailabilityQuery = (clone $driverBaseQuery);

        if ($hasDriverAvailability) {
            $driverAvailabilityQuery->where('is_available', true)
                ->where('assignment_status', 'available');

            if ($hasDriverCurrentVehicle) {
                $driverAvailabilityQuery->whereNull('current_vehicle_id');
            }
        }

        $driverOptions = $hasDriverAvailability ? $driverAvailabilityQuery->get() : collect();

        if ($driverOptions->isEmpty()) {
            $driverOptions = (clone $driverBaseQuery)
                ->when($hasDriverCurrentVehicle, function ($query) {
                    $query->whereNull('current_vehicle_id');
                })
                ->where(function ($query) {
                    $query->whereHas('driverStatus', function ($statusQuery) {
                        $statusQuery->where('is_active', true)
                            ->where(function ($statusScope) {
                                $statusScope->where(function ($q) {
                                    $q->where('can_assign', true)->where('can_drive', true);
                                })
                                    ->orWhereIn('slug', ['disponible', 'active', 'available'])
                                    ->orWhereIn('name', ['Disponible', 'Actif', 'Active', 'Available']);
                            });
                    })->orWhereDoesntHave('driverStatus');
                })
                ->get();
        }

        $this->driverOptions = $driverOptions;
    }

    /**
     * Réinitialise l'état de validation des conflits et suggestions
     * Note: Ne pas confondre avec resetValidation() native de Livewire
     */
    protected function resetConflictsValidation()
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

    /**
     * 🆕 ENTERPRISE V3: Génère les options de temps (30 min d'intervalle)
     */
    #[Computed]
    public function timeOptions(): array
    {
        $times = [];
        for ($hour = 0; $hour < 24; $hour++) {
            foreach (['00', '30'] as $minute) {
                $time = sprintf('%02d:%s', $hour, $minute);
                $times[] = [
                    'value' => $time,
                    'label' => $time
                ];
            }
        }
        return $times;
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
