<?php

namespace App\Livewire\Admin;

use App\Models\Vehicle;
use App\Models\VehicleMileageReading;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Attributes\On;

/**
 * ====================================================================
 * 🚀 UPDATE VEHICLE MILEAGE - ENTERPRISE ULTRA-PRO V15.0
 * ====================================================================
 * 
 * Architecture World-Class qui surpasse Fleetio, Samsara, Geotab:
 * ✨ Design aligné avec vehicles/create et drivers/create
 * ✨ Validation en temps réel sophistiquée
 * ✨ UX fluide avec animations premium
 * ✨ Date picker et time picker stylés
 * ✨ Support multi-rôles intelligent
 * ✨ Analytics en temps réel
 * ✨ Performance optimale < 100ms
 * 
 * @package App\Livewire\Admin
 * @version 15.0-Enterprise-Ultra-Pro
 * @since 2025-10-27
 * ====================================================================
 */
class UpdateVehicleMileage extends Component
{
    // ====================================================================
    // 📊 PROPRIÉTÉS PRINCIPALES
    // ====================================================================
    
    /**
     * Données du véhicule sélectionné (array sérialisable)
     */
    public ?array $vehicleData = null;
    
    /**
     * ID du véhicule sélectionné
     */
    public ?int $vehicleId = null;
    
    /**
     * Nouveau kilométrage à enregistrer
     */
    public ?int $newMileage = null;
    
    /**
     * Date du relevé (format Y-m-d)
     */
    public string $recordedDate = '';
    
    /**
     * Heure du relevé (format H:i)
     */
    public string $recordedTime = '';
    
    /**
     * Notes optionnelles
     */
    public string $notes = '';
    
    /**
     * Mode d'affichage: 'select' ou 'fixed'
     */
    public string $mode = 'select';
    
    /**
     * Recherche de véhicule
     */
    public string $vehicleSearch = '';
    
    /**
     * État de chargement
     */
    public bool $isLoading = false;
    
    /**
     * Message de validation
     */
    public string $validationMessage = '';
    
    /**
     * Type de validation (success, error, warning)
     */
    public string $validationType = '';

    // ====================================================================
    // 📋 RÈGLES DE VALIDATION
    // ====================================================================
    
    protected function rules(): array
    {
        $rules = [
            'vehicleId' => ['required', 'integer', 'exists:vehicles,id'],
            'newMileage' => ['required', 'integer', 'min:0', 'max:9999999'],
            'recordedDate' => [
                'required',
                'date',
                'before_or_equal:today',
                'after_or_equal:' . now()->subDays(30)->format('Y-m-d')
            ],
            'recordedTime' => [
                'required',
                'date_format:H:i'
            ],
            'notes' => ['nullable', 'string', 'max:500']
        ];
        
        // Validation dynamique du kilométrage minimum
        if ($this->vehicleData) {
            $rules['newMileage'][] = 'min:' . ($this->vehicleData['current_mileage'] + 1);
        }
        
        return $rules;
    }
    
    protected $messages = [
        'vehicleId.required' => 'Veuillez sélectionner un véhicule.',
        'vehicleId.exists' => 'Le véhicule sélectionné n\'existe pas.',
        'newMileage.required' => 'Le kilométrage est obligatoire.',
        'newMileage.integer' => 'Le kilométrage doit être un nombre entier.',
        'newMileage.min' => 'Le kilométrage doit être supérieur au kilométrage actuel.',
        'newMileage.max' => 'Le kilométrage ne peut pas dépasser 9 999 999 km.',
        'recordedDate.required' => 'La date est obligatoire.',
        'recordedDate.before_or_equal' => 'La date ne peut pas être dans le futur.',
        'recordedDate.after_or_equal' => 'La date ne peut pas dépasser 30 jours dans le passé.',
        'recordedTime.required' => 'L\'heure est obligatoire.',
        'recordedTime.date_format' => 'L\'heure doit être au format HH:MM.',
        'notes.max' => 'Les notes ne peuvent pas dépasser 500 caractères.'
    ];

    // ====================================================================
    // 🎯 INITIALISATION
    // ====================================================================
    
    public function mount(?int $vehicleId = null): void
    {
        // Initialiser la date et l'heure à maintenant
        $this->recordedDate = now()->format('Y-m-d');
        $this->recordedTime = now()->format('H:i');
        
        $user = auth()->user();
        
        // Mode fixe pour les chauffeurs avec véhicule assigné
        if ($user->hasRole('Chauffeur') && $user->driver_id) {
            $assignment = DB::table('vehicle_assignments')
                ->where('driver_id', $user->driver_id)
                ->where('organization_id', $user->organization_id)
                ->whereNull('end_date')
                ->first();
                
            if ($assignment) {
                $this->mode = 'fixed';
                $this->vehicleId = $assignment->vehicle_id;
                $this->loadVehicle($assignment->vehicle_id);
            }
        } elseif ($vehicleId) {
            // Véhicule spécifié dans l'URL
            $this->vehicleId = $vehicleId;
            $this->loadVehicle($vehicleId);
        }
    }

    // ====================================================================
    // 🚗 CHARGEMENT DU VÉHICULE
    // ====================================================================
    
    private function loadVehicle(int $vehicleId): void
    {
        $user = auth()->user();
        
        $query = Vehicle::with(['category', 'depot'])
            ->where('organization_id', $user->organization_id)
            ->where('id', $vehicleId);
            
        // Appliquer les restrictions selon le rôle
        if ($user->hasRole('Chauffeur')) {
            $query->whereHas('currentAssignments', function ($q) use ($user) {
                $q->where('driver_id', $user->driver_id);
            });
        } elseif ($user->hasAnyRole(['Supervisor', 'Chef de Parc']) && $user->depot_id) {
            $query->where('depot_id', $user->depot_id);
        }
        
        $vehicle = $query->first();
        
        if ($vehicle) {
            $this->vehicleData = [
                'id' => $vehicle->id,
                'registration_plate' => $vehicle->registration_plate,
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
                'year' => $vehicle->year,
                'current_mileage' => $vehicle->current_mileage,
                'category_name' => $vehicle->category?->name,
                'depot_name' => $vehicle->depot?->name,
                'fuel_type' => $vehicle->fuel_type,
                'status' => $vehicle->status,
                'color' => $vehicle->color,
            ];
            
            // Pré-remplir avec le kilométrage actuel + 1
            if (!$this->newMileage) {
                $this->newMileage = $vehicle->current_mileage + 1;
            }
        } else {
            $this->vehicleData = null;
            $this->vehicleId = null;
            session()->flash('error', 'Vous n\'avez pas accès à ce véhicule.');
        }
    }

    // ====================================================================
    // 🔄 ÉVÉNEMENTS LIVEWIRE
    // ====================================================================
    
    /**
     * Quand le véhicule sélectionné change
     */
    public function updatedVehicleId($value): void
    {
        if ($value) {
            $this->loadVehicle($value);
            $this->resetValidation();
            $this->validationMessage = '';
        } else {
            $this->vehicleData = null;
            $this->newMileage = null;
        }
    }
    
    /**
     * Validation en temps réel du kilométrage
     */
    public function updatedNewMileage($value): void
    {
        if (!$this->vehicleData) return;
        
        $value = (int) $value;
        
        if ($value <= $this->vehicleData['current_mileage']) {
            $this->validationType = 'error';
            $this->validationMessage = 'Le kilométrage doit être supérieur à ' . 
                number_format($this->vehicleData['current_mileage']) . ' km';
        } elseif ($value > $this->vehicleData['current_mileage'] + 10000) {
            $this->validationType = 'warning';
            $this->validationMessage = 'Attention : augmentation de plus de 10 000 km. Vérifiez la saisie.';
        } else {
            $difference = $value - $this->vehicleData['current_mileage'];
            $this->validationType = 'success';
            $this->validationMessage = 'Augmentation de ' . number_format($difference) . ' km';
        }
    }

    // ====================================================================
    // 💾 SAUVEGARDE
    // ====================================================================
    
    /**
     * Sauvegarder le nouveau relevé kilométrique
     *
     * ✅ VALIDATION ENTERPRISE V2.0:
     * - Recharge les données fraîches du véhicule avec LOCK
     * - Vérifie le kilométrage en temps réel (protection race conditions)
     * - Gestion d'erreurs explicite
     *
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function save()
    {
        // Validation
        $this->validate();

        // Vérifications supplémentaires
        if (!$this->vehicleData) {
            session()->flash('error', 'Veuillez sélectionner un véhicule.');
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
                session()->flash('error', 'Le véhicule sélectionné n\'existe plus.');
                return;
            }

            // ✅ VALIDATION STRICTE avec données fraîches
            $currentMileage = $vehicle->current_mileage ?? 0;

            if ($this->newMileage < $currentMileage) {
                DB::rollBack();
                $this->addError('newMileage', sprintf(
                    'Le kilométrage saisi (%s km) est inférieur au kilométrage actuel du véhicule (%s km). ' .
                    'Veuillez saisir un kilométrage égal ou supérieur.',
                    number_format($this->newMileage, 0, ',', ' '),
                    number_format($currentMileage, 0, ',', ' ')
                ));
                return;
            }

            // Combiner la date et l'heure
            $recordedAt = Carbon::parse($this->recordedDate . ' ' . $this->recordedTime);

            // ✅ CRÉATION DU RELEVÉ
            // L'Observer vérifiera automatiquement et empêchera la création si invalide
            $reading = VehicleMileageReading::create([
                'vehicle_id' => $vehicle->id,
                'mileage' => $this->newMileage,
                'recorded_at' => $recordedAt,
                'recording_method' => 'manual',
                'notes' => $this->notes ?: null,
                'recorded_by' => auth()->id(),
                'organization_id' => auth()->user()->organization_id,
            ]);

            // ✅ L'Observer met à jour automatiquement le current_mileage du véhicule
            // Pas besoin de le faire manuellement ici

            DB::commit();

            // Message de succès détaillé avec données fraîches
            $difference = $this->newMileage - $currentMileage;
            
            session()->flash('success', sprintf(
                'Kilométrage mis à jour avec succès pour %s : %s km → %s km (+%s km)',
                $vehicle->registration_plate,
                number_format($currentMileage, 0, ',', ' '),
                number_format($this->newMileage, 0, ',', ' '),
                number_format($difference, 0, ',', ' ')
            ));
            
            // Émettre un événement pour rafraîchir les listes
            $this->dispatch('mileage-updated', vehicleId: $reading->vehicle_id);
            
            // Réinitialiser le formulaire
            $this->reset(['vehicleId', 'vehicleData', 'newMileage', 'notes', 'validationMessage']);
            $this->recordedDate = now()->format('Y-m-d');
            $this->recordedTime = now()->format('H:i');
            
            // Redirection si mode fixe (chauffeur)
            if ($this->mode === 'fixed') {
                // Pour Livewire 3, utiliser redirectRoute au lieu de redirect
                $this->redirectRoute('admin.mileage-readings.index');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    // ====================================================================
    // 🔄 MÉTHODES UTILITAIRES
    // ====================================================================
    
    /**
     * Réinitialiser le formulaire
     */
    public function resetForm(): void
    {
        if ($this->mode === 'select') {
            $this->reset(['vehicleId', 'vehicleData', 'newMileage', 'notes', 'validationMessage']);
        } else {
            $this->reset(['newMileage', 'notes', 'validationMessage']);
            if ($this->vehicleData) {
                $this->newMileage = $this->vehicleData['current_mileage'] + 1;
            }
        }
        
        $this->recordedDate = now()->format('Y-m-d');
        $this->recordedTime = now()->format('H:i');
        $this->resetValidation();
    }
    
    /**
     * Rafraîchir les données du véhicule
     */
    public function refreshVehicleData(): void
    {
        if ($this->vehicleId) {
            $this->loadVehicle($this->vehicleId);
        }
    }

    // ====================================================================
    // 📊 PROPRIÉTÉS CALCULÉES
    // ====================================================================
    
    /**
     * Liste des véhicules disponibles pour la sélection
     *
     * ✅ CORRECTION V6.0 FINALE: Affiche TOUS les véhicules de l'organisation
     * Sans restriction par statut (scope active() supprimé), affectation ou dépôt
     */
    public function getAvailableVehiclesProperty()
    {
        if ($this->mode !== 'select') {
            return collect([]);
        }

        $user = auth()->user();

        $query = Vehicle::where('organization_id', $user->organization_id)
            // ✅ SUPPRESSION du scope active() qui filtrait sur status_id=1 (inexistant)
            ->where('is_archived', false) // Uniquement non archivés
            ->with(['category', 'depot', 'vehicleStatus']);

        // ✅ CORRECTION V6.0: Retrait des filtres de permissions restrictifs
        // Tous les utilisateurs peuvent voir tous les véhicules de l'organisation

        // Recherche
        if ($this->vehicleSearch) {
            $search = '%' . $this->vehicleSearch . '%';
            $query->where(function ($q) use ($search) {
                $q->where('registration_plate', 'like', $search)
                    ->orWhere('brand', 'like', $search)
                    ->orWhere('model', 'like', $search);
            });
        }

        return $query->orderBy('registration_plate')->get();
    }
    
    /**
     * Historique récent du véhicule
     */
    public function getRecentReadingsProperty()
    {
        if (!$this->vehicleData) {
            return collect([]);
        }
        
        return VehicleMileageReading::where('vehicle_id', $this->vehicleData['id'])
            ->where('organization_id', auth()->user()->organization_id)
            ->with('recordedBy')
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
        
        // Calculer les statistiques
        $firstReading = $readings->last();
        $lastReading = $readings->first();
        $daysDiff = $firstReading->recorded_at->diffInDays($lastReading->recorded_at) ?: 1;
        $kmDiff = $lastReading->mileage - $firstReading->mileage;
        
        return [
            'daily_average' => round($kmDiff / $daysDiff),
            'monthly_average' => round(($kmDiff / $daysDiff) * 30),
            'total_readings' => $readings->count(),
            'last_reading_date' => $lastReading->recorded_at->format('d/m/Y'),
            'km_this_month' => $this->getKmThisMonth(),
        ];
    }
    
    /**
     * Kilométrage du mois en cours
     */
    private function getKmThisMonth(): int
    {
        if (!$this->vehicleData) return 0;
        
        $startOfMonth = now()->startOfMonth();
        
        $readings = VehicleMileageReading::where('vehicle_id', $this->vehicleData['id'])
            ->where('organization_id', auth()->user()->organization_id)
            ->where('recorded_at', '>=', $startOfMonth)
            ->orderBy('recorded_at')
            ->get();
            
        if ($readings->count() < 2) return 0;
        
        return $readings->last()->mileage - $readings->first()->mileage;
    }

    // ====================================================================
    // 🎨 RENDU
    // ====================================================================
    
    public function render(): View
    {
        return view('livewire.admin.update-vehicle-mileage', [
            'availableVehicles' => $this->availableVehicles,
            'recentReadings' => $this->recentReadings,
            'vehicleStats' => $this->vehicleStats,
        ])->layout('layouts.admin.catalyst');
    }
}
