<?php

namespace App\Livewire\Admin\Mileage;

use App\Models\Vehicle;
use App\Models\VehicleMileageReading;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;

/**
 * ====================================================================
 * 🚀 MILEAGE UPDATE COMPONENT V2 - ENTERPRISE SINGLE PAGE
 * ====================================================================
 * 
 * Module de mise à jour du kilométrage - Architecture monopage optimale
 * 
 * Fonctionnalités:
 * ✨ Recherche de véhicule avec Tom Select
 * ✨ Validation en temps réel du kilométrage
 * ✨ Date/Time picker stylés Flowbite
 * ✨ Historique récent des relevés
 * ✨ Statistiques véhicule
 * ✨ UX fluide et responsive
 * 
 * @package App\Livewire\Admin\Mileage
 * @version 2.0-Enterprise
 * @since 2025-11-02
 * ====================================================================
 */
class MileageUpdateComponent extends Component
{
    // ====================================================================
    // CASTS LIVEWIRE - ENTERPRISE GRADE TYPE SAFETY
    // ====================================================================
    
    /**
     * ✅ CORRECTION CRITIQUE: Cast pour éviter TypeError avec Tom Select
     * Livewire reçoit parfois des strings au lieu d'int depuis le frontend
     */
    protected array $casts = [
        'vehicle_id' => 'integer',
    ];
    
    // ====================================================================
    // PROPRIÉTÉS PUBLIQUES
    // ====================================================================
    
    /**
     * ID du véhicule sélectionné
     */
    public ?int $vehicle_id = null;
    
    /**
     * Date de la lecture (format Y-m-d)
     */
    public string $date = '';
    
    /**
     * Heure de la lecture (format H:i)
     */
    public string $time = '';
    
    /**
     * Nouveau kilométrage
     */
    public ?int $mileage = null;
    
    /**
     * Notes optionnelles
     */
    public ?string $notes = null;
    
    /**
     * Données du véhicule sélectionné (cached)
     */
    public ?array $vehicleData = null;
    
    /**
     * Message de validation en temps réel
     */
    public string $validationMessage = '';
    
    /**
     * Type de validation: 'success', 'warning', 'error'
     */
    public string $validationType = '';

    // ====================================================================
    // RÈGLES DE VALIDATION
    // ====================================================================
    
    /**
     * Hook Livewire: Normaliser les données AVANT validation
     * 
     * ✅ ENTERPRISE-GRADE: Conversion automatique des formats
     * - Date: d/m/Y → Y-m-d (21/10/2025 → 2025-10-21)
     * - Heure: Accepte H:i, HH:i, H:i:s, etc.
     */
    protected function prepareForValidation($attributes)
    {
        // ✅ NORMALISATION DATE: d/m/Y → Y-m-d
        if (isset($attributes['date']) && $attributes['date']) {
            $attributes['date'] = $this->normalizeDateFormat($attributes['date']);
        }
        
        // ✅ NORMALISATION HEURE: Assurer le format H:i
        if (isset($attributes['time']) && $attributes['time']) {
            $attributes['time'] = $this->normalizeTimeFormat($attributes['time']);
        }
        
        return $attributes;
    }
    
    /**
     * Normaliser le format de date
     * Accepte: d/m/Y, Y-m-d, d-m-Y, etc.
     * Retourne: Y-m-d
     */
    private function normalizeDateFormat(string $date): string
    {
        try {
            // Nettoyer la chaîne
            $date = trim($date);
            
            // Tentative 1: Format d/m/Y (21/10/2025)
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $date, $matches)) {
                return sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
            }
            
            // Tentative 2: Format d-m-Y (21-10-2025)
            if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $date, $matches)) {
                return sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
            }
            
            // Tentative 3: Format Y-m-d (2025-10-21) - Déjà bon
            if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $date)) {
                return $date;
            }
            
            // Tentative 4: Parser avec Carbon (fallback)
            return Carbon::parse($date)->format('Y-m-d');
            
        } catch (\Exception $e) {
            \Log::warning('MileageUpdate: Date format invalid', [
                'date' => $date,
                'error' => $e->getMessage()
            ]);
            return $date; // Retourner tel quel, la validation échouera
        }
    }
    
    /**
     * Normaliser le format d'heure
     * Avec le nouveau time-picker Alpine.js, le format HH:MM est garanti
     * Cette méthode est conservée par sécurité et rétrocompatibilité
     * 
     * @param string $time Format attendu: HH:MM
     * @return string Format normalisé HH:MM
     */
    private function normalizeTimeFormat(string $time): string
    {
        try {
            // Nettoyer la chaîne
            $time = trim($time);
            
            // Le nouveau time-picker Alpine.js garantit le format HH:MM
            // Vérification simple avec regex
            if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $time)) {
                return $time; // Déjà au bon format
            }
            
            // Fallback pour compatibilité: Pattern H:i ou HH:i
            if (preg_match('/^(\d{1,2}):(\d{1,2})/', $time, $matches)) {
                $hours = (int) $matches[1];
                $minutes = (int) $matches[2];
                
                // Validation et formatage
                if ($hours >= 0 && $hours <= 23 && $minutes >= 0 && $minutes <= 59) {
                    return sprintf('%02d:%02d', $hours, $minutes);
                }
            }
            
            // Dernier recours: Parser avec Carbon
            return Carbon::parse($time)->format('H:i');
            
        } catch (\Exception $e) {
            \Log::warning('MileageUpdate: Time format invalid', [
                'time' => $time,
                'error' => $e->getMessage()
            ]);
            return $time; // Retourner tel quel pour que la validation Livewire capture l'erreur
        }
    }
    
    protected function rules(): array
    {
        $rules = [
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after_or_equal:' . now()->subDays(30)->format('Y-m-d')
            ],
            'time' => ['required', 'date_format:H:i'],
            'mileage' => ['required', 'integer', 'min:0', 'max:9999999'],
            'notes' => ['nullable', 'string', 'max:500']
        ];
        
        // Validation dynamique: kilométrage > dernier relevé
        if ($this->vehicleData && isset($this->vehicleData['current_mileage'])) {
            $rules['mileage'][] = 'gt:' . $this->vehicleData['current_mileage'];
        }
        
        return $rules;
    }
    
    protected $messages = [
        'vehicle_id.required' => 'Veuillez sélectionner un véhicule.',
        'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
        'date.required' => 'La date est obligatoire.',
        'date.before_or_equal' => 'La date ne peut pas être dans le futur.',
        'date.after_or_equal' => 'La date ne peut pas dépasser 30 jours dans le passé.',
        'time.required' => 'L\'heure est obligatoire.',
        'time.date_format' => 'L\'heure doit être au format HH:MM.',
        'mileage.required' => 'Le kilométrage est obligatoire.',
        'mileage.integer' => 'Le kilométrage doit être un nombre entier.',
        'mileage.min' => 'Le kilométrage ne peut pas être négatif.',
        'mileage.max' => 'Le kilométrage ne peut pas dépasser 9 999 999 km.',
        'mileage.gt' => 'Le kilométrage doit être supérieur au dernier relevé.',
        'notes.max' => 'Les notes ne peuvent pas dépasser 500 caractères.'
    ];

    // ====================================================================
    // INITIALISATION
    // ====================================================================
    
    /**
     * Montage du composant
     */
    public function mount(?int $vehicleId = null): void
    {
        // Initialiser date et heure à maintenant
        $this->date = now()->format('Y-m-d');
        $this->time = now()->format('H:i');
        
        // Si un véhicule est passé en paramètre
        if ($vehicleId) {
            $this->vehicle_id = $vehicleId;
            $this->loadVehicleData();
        }
    }

    // ====================================================================
    // ÉVÉNEMENTS LIVEWIRE
    // ====================================================================
    
    /**
     * Quand le véhicule sélectionné change
     */
    public function updatedVehicleId($value): void
    {
        if ($value) {
            $this->loadVehicleData();
            $this->resetValidation('mileage');
            $this->validationMessage = '';
            $this->validationType = '';
        } else {
            $this->vehicleData = null;
            $this->mileage = null;
        }
    }
    
    /**
     * Validation en temps réel du kilométrage
     */
    public function updatedMileage($value): void
    {
        if (!$this->vehicleData || !isset($this->vehicleData['current_mileage'])) {
            return;
        }
        
        $currentMileage = $this->vehicleData['current_mileage'];
        $value = (int) $value;
        
        if ($value <= 0) {
            $this->validationType = 'error';
            $this->validationMessage = 'Le kilométrage doit être positif.';
        } elseif ($value <= $currentMileage) {
            $this->validationType = 'error';
            $this->validationMessage = 'Le kilométrage doit être supérieur à ' . 
                number_format($currentMileage, 0, ',', ' ') . ' km';
        } elseif ($value > $currentMileage + 10000) {
            $this->validationType = 'warning';
            $difference = $value - $currentMileage;
            $this->validationMessage = '⚠️ Augmentation importante : +' . 
                number_format($difference, 0, ',', ' ') . ' km. Vérifiez la saisie.';
        } else {
            $this->validationType = 'success';
            $difference = $value - $currentMileage;
            $this->validationMessage = '✓ Augmentation de ' . 
                number_format($difference, 0, ',', ' ') . ' km';
        }
    }

    // ====================================================================
    // MÉTHODES PRINCIPALES
    // ====================================================================
    
    /**
     * Charger les données du véhicule
     */
    private function loadVehicleData(): void
    {
        $vehicle = Vehicle::with(['category', 'depot', 'vehicleType', 'fuelType'])
            ->where('organization_id', auth()->user()->organization_id)
            ->where('id', $this->vehicle_id)
            ->first();
        
        if ($vehicle) {
            $this->vehicleData = [
                'id' => $vehicle->id,
                'registration_plate' => $vehicle->registration_plate,
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
                'manufacturing_year' => $vehicle->manufacturing_year,
                'current_mileage' => $vehicle->current_mileage ?? 0,
                'category_name' => $vehicle->category?->name,
                'depot_name' => $vehicle->depot?->name,
                'vehicle_type' => $vehicle->vehicleType?->name,
                'fuel_type' => $vehicle->fuelType?->name,
                'color' => $vehicle->color,
            ];
            
            // Suggérer un kilométrage initial
            if (!$this->mileage) {
                $this->mileage = ($vehicle->current_mileage ?? 0) + 1;
            }
        } else {
            $this->vehicleData = null;
            session()->flash('error', 'Véhicule introuvable ou accès refusé.');
        }
    }
    
    /**
     * Sauvegarder le relevé kilométrique
     *
     * ✅ VALIDATION ENTERPRISE V2.0:
     * - Recharge les données fraîches du véhicule avec LOCK
     * - Vérifie le kilométrage en temps réel (protection race conditions)
     * - Gestion d'erreurs explicite
     */
    public function save()
    {
        // Validation des règles de base
        $this->validate();

        // Vérifications de sécurité supplémentaires
        if (!$this->vehicleData) {
            $this->addError('vehicle_id', 'Veuillez sélectionner un véhicule valide.');
            return;
        }

        try {
            DB::beginTransaction();

            // ✅ VALIDATION ENTERPRISE V2.0: Recharger le véhicule avec LOCK
            // pour obtenir les données les plus récentes et éviter les race conditions
            $vehicle = Vehicle::where('id', $this->vehicleData['id'])
                ->lockForUpdate()
                ->first();

            if (!$vehicle) {
                DB::rollBack();
                $this->addError('vehicle_id', 'Le véhicule sélectionné n\'existe plus.');
                return;
            }

            // ✅ VALIDATION STRICTE avec données fraîches
            $currentMileage = $vehicle->current_mileage ?? 0;

            if ($this->mileage < $currentMileage) {
                DB::rollBack();
                $this->addError('mileage', sprintf(
                    'Le kilométrage saisi (%s km) est inférieur au kilométrage actuel du véhicule (%s km). ' .
                    'Veuillez saisir un kilométrage égal ou supérieur.',
                    number_format($this->mileage, 0, ',', ' '),
                    number_format($currentMileage, 0, ',', ' ')
                ));
                return;
            }
            
            // ✅ CORRECTION ENTERPRISE V3: Parsing robuste multi-formats
            // Gestion de tous les cas possibles de date/heure
            
            // 1. Normaliser la date au format Y-m-d
            $normalizedDate = $this->normalizeDateFormat($this->date);
            
            // 2. Normaliser l'heure au format H:i
            $normalizedTime = $this->normalizeTimeFormat($this->time);
            
            // 3. Combiner et créer l'objet Carbon
            try {
                // Méthode 1: createFromFormat strict
                $recordedAt = Carbon::createFromFormat('Y-m-d H:i', $normalizedDate . ' ' . $normalizedTime);
            } catch (\Exception $e) {
                // Méthode 2: parse flexible comme fallback
                try {
                    $recordedAt = Carbon::parse($normalizedDate . ' ' . $normalizedTime);
                } catch (\Exception $e2) {
                    // Méthode 3: construire manuellement
                    $dateParts = explode('-', $normalizedDate);
                    $timeParts = explode(':', $normalizedTime);
                    
                    if (count($dateParts) === 3 && count($timeParts) === 2) {
                        $recordedAt = Carbon::create(
                            (int)$dateParts[0], // year
                            (int)$dateParts[1], // month
                            (int)$dateParts[2], // day
                            (int)$timeParts[0], // hour
                            (int)$timeParts[1], // minute
                            0 // second
                        );
                    } else {
                        throw new \Exception(
                            "Impossible de parser la date/heure. " .
                            "Date normalisée: {$normalizedDate}, Heure normalisée: {$normalizedTime}"
                        );
                    }
                }
            }
            
            // Vérification de sécurité Enterprise-Grade
            if (!$recordedAt || !$recordedAt instanceof Carbon) {
                throw new \Exception(
                    "Erreur critique de création de date/heure. " .
                    "Format attendu: Y-m-d H:i. Reçu: {$this->date} {$this->time}"
                );
            }
            
            // Vérifier que la date n'est pas dans le futur
            if ($recordedAt->isFuture()) {
                throw new \Exception("La date/heure du relevé ne peut pas être dans le futur.");
            }
            
            // ✅ CRÉATION DU RELEVÉ
            // L'Observer vérifiera automatiquement et empêchera la création si invalide
            $reading = VehicleMileageReading::createManual(
                organizationId: auth()->user()->organization_id,
                vehicleId: $vehicle->id,
                mileage: $this->mileage,
                recordedById: auth()->id(),
                recordedAt: $recordedAt,
                notes: $this->notes
            );

            // ✅ L'Observer met à jour automatiquement le current_mileage du véhicule
            // Pas besoin de le faire manuellement ici

            DB::commit();

            // Message de succès avec données fraîches
            $difference = $this->mileage - $currentMileage;

            session()->flash('success', sprintf(
                'Kilométrage enregistré avec succès pour %s : %s km → %s km (+%s km)',
                $vehicle->registration_plate,
                number_format($currentMileage, 0, ',', ' '),
                number_format($this->mileage, 0, ',', ' '),
                number_format($difference, 0, ',', ' ')
            ));
            
            // Émettre événement pour refresh éventuel
            $this->dispatch('mileage-updated', vehicleId: $reading->vehicle_id);
            
            // Réinitialiser le formulaire
            $this->resetForm();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
            
            \Log::error('Erreur enregistrement kilométrage', [
                'vehicle_id' => $this->vehicle_id,
                'mileage' => $this->mileage,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Réinitialiser le formulaire
     */
    public function resetForm(): void
    {
        $this->reset(['vehicle_id', 'mileage', 'notes', 'vehicleData', 'validationMessage', 'validationType']);
        $this->date = now()->format('Y-m-d');
        $this->time = now()->format('H:i');
        $this->resetValidation();
    }

    // ====================================================================
    // PROPRIÉTÉS CALCULÉES
    // ====================================================================
    
    /**
     * Liste des véhicules disponibles pour la sélection
     *
     * ✅ CORRECTION ENTERPRISE-GRADE V6.0 FINALE:
     * - Affiche TOUS les véhicules de l'organisation (sauf archivés)
     * - Pas de restriction par statut (le scope active() filtrait sur status_id=1 qui n'existe pas)
     * - Pas de restriction par affectation ou dépôt
     * - Retourne directement les objets Vehicle (pas des arrays)
     * - Charge les relations nécessaires avec eager loading
     * - Gestion robuste des erreurs avec logs
     */
    public function getAvailableVehiclesProperty()
    {
        try {
            $user = auth()->user();

            if (!$user || !$user->organization_id) {
                \Log::warning('MileageUpdate: User not authenticated or no organization_id');
                return collect([]);
            }

            // ✅ CORRECTION V6.0: TOUS les véhicules non archivés de l'organisation
            // Sans restriction par statut, affectation ou dépôt
            $vehicles = Vehicle::where('organization_id', $user->organization_id)
                ->where('is_archived', false)  // Uniquement non archivés
                // ✅ SUPPRESSION du scope active() qui filtrait sur status_id=1 (inexistant)
                ->with(['category', 'depot', 'vehicleType', 'fuelType', 'vehicleStatus'])
                ->orderBy('registration_plate')
                ->get();

            // Log pour debug (seulement en local/dev)
            if (app()->environment(['local', 'development'])) {
                \Log::info('MileageUpdate V6.0: ALL vehicles loaded', [
                    'count' => $vehicles->count(),
                    'organization_id' => $user->organization_id,
                    'user_id' => $user->id,
                    'user_role' => $user->roles->pluck('name')->first(),
                    'sample_statuses' => $vehicles->take(5)->pluck('vehicleStatus.name', 'registration_plate')->toArray(),
                ]);
            }

            // ✅ Retourner directement les objets Vehicle
            return $vehicles;

        } catch (\Exception $e) {
            \Log::error('MileageUpdate: Error loading vehicles', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // En production, retourner collection vide au lieu de crasher
            return collect([]);
        }
    }
    
    /**
     * Historique récent des relevés (5 derniers)
     */
    public function getRecentReadingsProperty()
    {
        if (!$this->vehicleData) {
            return collect([]);
        }
        
        return VehicleMileageReading::where('vehicle_id', $this->vehicleData['id'])
            ->where('organization_id', auth()->user()->organization_id)
            ->with('recordedBy:id,name')
            ->orderBy('recorded_at', 'desc')
            ->limit(5)
            ->get();
    }
    
    /**
     * Statistiques du véhicule
     */
    public function getVehicleStatsProperty()
    {
        if (!$this->vehicleData) {
            return null;
        }
        
        $readings = VehicleMileageReading::where('vehicle_id', $this->vehicleData['id'])
            ->where('organization_id', auth()->user()->organization_id)
            ->orderBy('recorded_at', 'desc')
            ->limit(30)
            ->get();
        
        if ($readings->count() < 2) {
            return null;
        }
        
        $firstReading = $readings->last();
        $lastReading = $readings->first();
        $daysDiff = $firstReading->recorded_at->diffInDays($lastReading->recorded_at) ?: 1;
        $kmDiff = $lastReading->mileage - $firstReading->mileage;
        
        // Kilométrage du mois en cours
        $startOfMonth = now()->startOfMonth();
        $monthReadings = $readings->filter(function ($reading) use ($startOfMonth) {
            return $reading->recorded_at->gte($startOfMonth);
        });
        
        $kmThisMonth = 0;
        if ($monthReadings->count() >= 2) {
            $kmThisMonth = $monthReadings->first()->mileage - $monthReadings->last()->mileage;
        }
        
        return [
            'daily_average' => $daysDiff > 0 ? round($kmDiff / $daysDiff) : 0,
            'monthly_average' => $daysDiff > 0 ? round(($kmDiff / $daysDiff) * 30) : 0,
            'km_this_month' => max(0, $kmThisMonth),
            'total_readings' => $readings->count(),
            'last_reading_date' => $lastReading->recorded_at->format('d/m/Y à H:i'),
        ];
    }

    // ====================================================================
    // RENDU
    // ====================================================================
    
    /**
     * Rendu du composant
     */
    public function render(): View
    {
        return view('livewire.admin.mileage.mileage-update-component', [
            'availableVehicles' => $this->availableVehicles,
            'recentReadings' => $this->recentReadings,
            'vehicleStats' => $this->vehicleStats,
        ])->layout('layouts.admin.catalyst');
    }
}
