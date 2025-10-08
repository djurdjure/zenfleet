<?php

namespace App\Livewire\Admin;

use App\Models\Vehicle;
use App\Models\VehicleMileageReading;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

/**
 * UpdateVehicleMileage - Mise à jour du kilométrage véhicule
 *
 * Features:
 * - Interface simplifiée pour mise à jour rapide
 * - Contrôles d'accès par rôle (chauffeur/superviseur/admin)
 * - Validation avancée (kilométrage croissant uniquement)
 * - Historique automatique des modifications
 * - Multi-tenant scoping strict
 *
 * Permissions:
 * - Chauffeur: uniquement son véhicule assigné
 * - Superviseur/Chef de parc: véhicules de son dépôt
 * - Admin/Gestionnaire: tous les véhicules de l'organisation
 *
 * @version 1.0-Enterprise
 */
class UpdateVehicleMileage extends Component
{
    /**
     * 🚗 PROPRIÉTÉS DU VÉHICULE
     */
    public ?int $vehicleId = null;
    public ?Vehicle $selectedVehicle = null;

    /**
     * 📝 PROPRIÉTÉS DU FORMULAIRE
     */
    public int $newMileage = 0;
    public string $recordedAt = '';
    public string $notes = '';

    /**
     * 🎯 MODE D'AFFICHAGE
     * - 'select': Permet de sélectionner un véhicule (admin/superviseur)
     * - 'fixed': Véhicule pré-sélectionné (chauffeur ou URL avec ID)
     */
    public string $mode = 'select';

    /**
     * 🔍 RECHERCHE DE VÉHICULE
     */
    public string $vehicleSearch = '';

    /**
     * 📋 RÈGLES DE VALIDATION
     */
    protected function rules(): array
    {
        $rules = [
            'recordedAt' => [
                'required',
                'date',
                'before_or_equal:now',
                'after_or_equal:' . now()->subDays(7)->toDateString(),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];

        // Validation du kilométrage uniquement si un véhicule est sélectionné
        if ($this->selectedVehicle) {
            $rules['newMileage'] = [
                'required',
                'integer',
                'min:' . $this->selectedVehicle->current_mileage,
                'max:9999999',
            ];
        } else {
            $rules['vehicleId'] = ['required', 'integer', 'exists:vehicles,id'];
        }

        return $rules;
    }

    /**
     * 🔧 MESSAGES DE VALIDATION PERSONNALISÉS
     */
    protected $messages = [
        'vehicleId.required' => 'Veuillez sélectionner un véhicule.',
        'vehicleId.exists' => 'Le véhicule sélectionné n\'existe pas.',
        'newMileage.required' => 'Le kilométrage est obligatoire.',
        'newMileage.integer' => 'Le kilométrage doit être un nombre entier.',
        'newMileage.min' => 'Le kilométrage ne peut pas être inférieur au kilométrage actuel.',
        'newMileage.max' => 'Le kilométrage ne peut pas dépasser 9 999 999 km.',
        'recordedAt.required' => 'La date est obligatoire.',
        'recordedAt.date' => 'La date doit être une date valide.',
        'recordedAt.before_or_equal' => 'La date ne peut pas être dans le futur.',
        'recordedAt.after_or_equal' => 'La date ne peut pas dépasser 7 jours dans le passé.',
        'notes.max' => 'Les notes ne peuvent pas dépasser 500 caractères.',
    ];

    /**
     * 🏗️ INITIALISATION DU COMPOSANT
     */
    public function mount(?int $vehicleId = null): void
    {
        $user = auth()->user();

        // Initialiser la date à maintenant
        $this->recordedAt = now()->format('Y-m-d\TH:i');

        // Si vehicleId fourni (depuis URL ou route)
        if ($vehicleId) {
            $this->vehicleId = $vehicleId;
            $this->loadVehicle($vehicleId);
            $this->mode = 'fixed';
            return;
        }

        // Détecter le mode selon le rôle
        if ($user->hasRole('Chauffeur')) {
            // Chauffeur: charger automatiquement son véhicule assigné
            $assignedVehicle = $this->getDriverAssignedVehicle();
            if ($assignedVehicle) {
                $this->vehicleId = $assignedVehicle->id;
                $this->loadVehicle($assignedVehicle->id);
                $this->mode = 'fixed';
            } else {
                session()->flash('warning', 'Aucun véhicule ne vous est actuellement assigné.');
            }
        } else {
            // Admin/Superviseur: mode sélection
            $this->mode = 'select';
        }
    }

    /**
     * 🚗 CHARGER UN VÉHICULE
     */
    private function loadVehicle(int $vehicleId): void
    {
        $user = auth()->user();

        $query = Vehicle::where('organization_id', $user->organization_id)
            ->where('id', $vehicleId);

        // Appliquer le scoping selon les permissions
        if ($user->hasRole('Chauffeur')) {
            // Vérifier que c'est bien son véhicule
            $query->whereHas('currentAssignments', function ($q) use ($user) {
                $q->where('driver_id', $user->driver_id);
            });
        } elseif ($user->hasAnyRole(['Supervisor', 'Chef de Parc'])) {
            // Limiter aux véhicules de son dépôt
            if ($user->depot_id) {
                $query->where('depot_id', $user->depot_id);
            }
        }
        // Admin/Gestionnaire Flotte: pas de restriction supplémentaire

        $this->selectedVehicle = $query->first();

        if ($this->selectedVehicle) {
            $this->newMileage = $this->selectedVehicle->current_mileage;
        } else {
            session()->flash('error', 'Vous n\'avez pas accès à ce véhicule.');
            $this->selectedVehicle = null;
            $this->vehicleId = null;
        }
    }

    /**
     * 🚗 RÉCUPÉRER LE VÉHICULE ASSIGNÉ AU CHAUFFEUR
     */
    private function getDriverAssignedVehicle(): ?Vehicle
    {
        $user = auth()->user();

        if (!$user->driver_id) {
            return null;
        }

        return Vehicle::where('organization_id', $user->organization_id)
            ->whereHas('currentAssignments', function ($q) use ($user) {
                $q->where('driver_id', $user->driver_id);
            })
            ->first();
    }

    /**
     * 🔄 QUAND LE VÉHICULE CHANGE (MODE SÉLECTION)
     */
    public function updatedVehicleId($value): void
    {
        if ($value) {
            $this->loadVehicle($value);
            $this->resetValidation();
        } else {
            $this->selectedVehicle = null;
            $this->newMileage = 0;
        }
    }

    /**
     * 🚗 LISTE DES VÉHICULES ACCESSIBLES (POUR MODE SÉLECTION)
     */
    public function getAvailableVehiclesProperty()
    {
        $user = auth()->user();

        $query = Vehicle::where('organization_id', $user->organization_id)
            ->select('id', 'registration_plate', 'brand', 'model', 'current_mileage', 'depot_id');

        // Appliquer le scoping selon les permissions
        if ($user->hasAnyRole(['Supervisor', 'Chef de Parc'])) {
            if ($user->depot_id) {
                $query->where('depot_id', $user->depot_id);
            }
        }

        // Filtrer par recherche si présente
        if (!empty($this->vehicleSearch)) {
            $search = '%' . $this->vehicleSearch . '%';
            $query->where(function ($q) use ($search) {
                $q->where('registration_plate', 'ilike', $search)
                    ->orWhere('brand', 'ilike', $search)
                    ->orWhere('model', 'ilike', $search);
            });
        }

        return $query->orderBy('registration_plate')->get();
    }

    /**
     * 💾 ENREGISTRER LA MISE À JOUR DU KILOMÉTRAGE
     */
    public function save(): void
    {
        // Charger le véhicule si pas encore fait
        if (!$this->selectedVehicle && $this->vehicleId) {
            $this->loadVehicle($this->vehicleId);
        }

        // Vérifier qu'un véhicule est sélectionné
        if (!$this->selectedVehicle) {
            session()->flash('error', 'Veuillez sélectionner un véhicule.');
            return;
        }

        // Validation personnalisée supplémentaire
        if ($this->newMileage < $this->selectedVehicle->current_mileage) {
            $this->addError('newMileage',
                "Le kilométrage ({$this->newMileage} km) ne peut pas être inférieur au kilométrage actuel (" .
                number_format($this->selectedVehicle->current_mileage) . " km)."
            );
            return;
        }

        if ($this->newMileage == $this->selectedVehicle->current_mileage) {
            $this->addError('newMileage', 'Le kilométrage doit être différent du kilométrage actuel.');
            return;
        }

        // Valider les données
        $validated = $this->validate();

        DB::beginTransaction();
        try {
            // Créer le relevé kilométrique
            $reading = VehicleMileageReading::create([
                'vehicle_id' => $this->selectedVehicle->id,
                'mileage' => $this->newMileage,
                'recorded_at' => $this->recordedAt,
                'recording_method' => 'manual',
                'notes' => $this->notes,
                'recorded_by_id' => auth()->id(),
                'organization_id' => auth()->user()->organization_id,
            ]);

            // L'Observer VehicleMileageReadingObserver met à jour automatiquement vehicle.current_mileage

            DB::commit();

            // Message de succès
            $oldMileage = $this->selectedVehicle->current_mileage;
            $difference = $this->newMileage - $oldMileage;

            session()->flash('success',
                "Kilométrage mis à jour avec succès : " . number_format($oldMileage) . " km → " .
                number_format($this->newMileage) . " km (+{$difference} km)"
            );

            // CORRECTIF ENTERPRISE-GRADE: Sauvegarder le vehicleId AVANT resetForm()
            // pour éviter "Attempt to read property 'id' on null" lors du dispatch
            $savedVehicleId = $this->selectedVehicle->id;

            // Réinitialiser le formulaire
            $this->resetForm();

            // Émettre un événement pour rafraîchir d'autres composants
            // Utilise le vehicleId sauvegardé au lieu de $this->selectedVehicle->id
            $this->dispatch('mileage-updated', vehicleId: $savedVehicleId);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * 🔄 RESET DU FORMULAIRE
     */
    private function resetForm(): void
    {
        if ($this->mode === 'select') {
            $this->reset(['vehicleId', 'selectedVehicle', 'newMileage', 'notes']);
        } else {
            // En mode fixe, recharger le véhicule
            if ($this->selectedVehicle) {
                $this->selectedVehicle->refresh();
                $this->newMileage = $this->selectedVehicle->current_mileage;
            }
            $this->notes = '';
        }

        $this->recordedAt = now()->format('Y-m-d\TH:i');
        $this->resetValidation();
    }

    /**
     * 🔄 RAFRAÎCHIR LE COMPOSANT
     */
    public function refresh(): void
    {
        if ($this->selectedVehicle) {
            $this->selectedVehicle->refresh();
            $this->newMileage = $this->selectedVehicle->current_mileage;
        }
    }

    /**
     * 🎨 RENDER
     */
    public function render(): View
    {
        return view('livewire.admin.update-vehicle-mileage', [
            'availableVehicles' => $this->mode === 'select' ? $this->availableVehicles : null,
        ])->layout('layouts.admin.catalyst');
    }
}
