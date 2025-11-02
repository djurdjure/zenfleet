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
     */
    public function save()
    {
        // Validation
        $this->validate();
        
        // Vérifications de sécurité supplémentaires
        if (!$this->vehicleData) {
            $this->addError('vehicle_id', 'Veuillez sélectionner un véhicule valide.');
            return;
        }
        
        if ($this->mileage <= $this->vehicleData['current_mileage']) {
            $this->addError('mileage', 'Le kilométrage doit être supérieur au dernier relevé.');
            return;
        }
        
        try {
            DB::beginTransaction();
            
            // Combiner date et heure
            $recordedAt = Carbon::parse($this->date . ' ' . $this->time);
            
            // Créer le relevé
            $reading = VehicleMileageReading::createManual(
                organizationId: auth()->user()->organization_id,
                vehicleId: $this->vehicleData['id'],
                mileage: $this->mileage,
                recordedById: auth()->id(),
                recordedAt: $recordedAt,
                notes: $this->notes
            );
            
            // Mettre à jour le kilométrage du véhicule
            Vehicle::where('id', $this->vehicleData['id'])
                ->update(['current_mileage' => $this->mileage]);
            
            DB::commit();
            
            // Message de succès
            $oldMileage = $this->vehicleData['current_mileage'];
            $difference = $this->mileage - $oldMileage;
            
            session()->flash('success', sprintf(
                'Kilométrage enregistré avec succès pour %s : %s km → %s km (+%s km)',
                $this->vehicleData['registration_plate'],
                number_format($oldMileage, 0, ',', ' '),
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
     */
    public function getAvailableVehiclesProperty()
    {
        return Vehicle::where('organization_id', auth()->user()->organization_id)
            ->whereNotNull('current_mileage')
            ->where('is_archived', false)
            ->whereHas('vehicleStatus', function ($query) {
                $query->whereIn('name', ['Disponible', 'En service', 'En maintenance']);
            })
            ->with(['category', 'vehicleType'])
            ->orderBy('registration_plate')
            ->get()
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'label' => sprintf(
                        '%s - %s %s (%s)',
                        $vehicle->registration_plate,
                        $vehicle->brand,
                        $vehicle->model,
                        $vehicle->category?->name ?? 'N/A'
                    ),
                    'registration_plate' => $vehicle->registration_plate,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                ];
            });
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
