<?php

namespace App\Livewire\Admin;

use App\Models\VehicleMileageReading;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * MileageReadingsIndex - Vue globale des relevés kilométriques
 *
 * Features:
 * - Affichage de tous les relevés de l'organisation
 * - Filtrage par véhicule, méthode, auteur, dates
 * - Tri multi-colonnes
 * - Accès rapide à l'historique d'un véhicule
 * - Permission-based scoping (own/team/all)
 *
 * @version 1.0-Enterprise
 */
class MileageReadingsIndex extends Component
{
    use WithPagination;

    /**
     * 🔍 PROPRIÉTÉS DE RECHERCHE ET FILTRES
     */
    public string $search = '';
    public string $vehicleFilter = '';
    public string $methodFilter = '';
    public string $authorFilter = '';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    /**
     * 📊 PROPRIÉTÉS DE TRI ET PAGINATION
     */
    public string $sortField = 'recorded_at';
    public string $sortDirection = 'desc';
    public int $perPage = 20;

    /**
     * 🎛️ LISTENERS
     */
    protected $listeners = [
        'refresh-readings' => '$refresh',
    ];

    /**
     * 🔄 RESET PAGINATION QUAND FILTRES CHANGENT
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingVehicleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingMethodFilter(): void
    {
        $this->resetPage();
    }

    public function updatingAuthorFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    /**
     * 📊 TRI DES COLONNES
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * 🔄 RESET FILTRES
     */
    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'vehicleFilter',
            'methodFilter',
            'authorFilter',
            'dateFrom',
            'dateTo',
            'sortField',
            'sortDirection',
        ]);
        $this->resetPage();
    }

    /**
     * 📋 RÉCUPÉRATION DES RELEVÉS
     */
    public function getReadingsProperty()
    {
        $user = auth()->user();

        $query = VehicleMileageReading::with([
            'vehicle',
            'recordedBy',
        ])
            ->where('organization_id', $user->organization_id);

        // 🔐 PERMISSION-BASED SCOPING
        if ($user->can('view all mileage readings')) {
            // Tous les relevés de l'organisation
        } elseif ($user->can('view team mileage readings')) {
            // Relevés de l'équipe/dépôt
            $query->whereHas('vehicle', function ($q) use ($user) {
                if ($user->depot_id) {
                    $q->where('depot_id', $user->depot_id);
                }
            });
        } else {
            // Seulement les relevés créés par l'utilisateur
            $query->where('recorded_by_id', $user->id);
        }

        // 🔍 RECHERCHE GLOBALE
        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('mileage', 'ilike', $search)
                    ->orWhere('notes', 'ilike', $search)
                    ->orWhereHas('vehicle', function ($q) use ($search) {
                        $q->where('registration_plate', 'ilike', $search)
                            ->orWhere('brand', 'ilike', $search)
                            ->orWhere('model', 'ilike', $search);
                    })
                    ->orWhereHas('recordedBy', function ($q) use ($search) {
                        $q->where('name', 'ilike', $search);
                    });
            });
        }

        // 📊 FILTRES SPÉCIFIQUES
        if (!empty($this->vehicleFilter)) {
            $query->where('vehicle_id', $this->vehicleFilter);
        }

        if (!empty($this->methodFilter)) {
            $query->where('recording_method', $this->methodFilter);
        }

        if (!empty($this->authorFilter)) {
            $query->where('recorded_by_id', $this->authorFilter);
        }

        if (!empty($this->dateFrom)) {
            $query->whereDate('recorded_at', '>=', $this->dateFrom);
        }

        if (!empty($this->dateTo)) {
            $query->whereDate('recorded_at', '<=', $this->dateTo);
        }

        // 📊 TRI
        if ($this->sortField === 'vehicle') {
            $query->join('vehicles', 'vehicle_mileage_readings.vehicle_id', '=', 'vehicles.id')
                ->orderBy('vehicles.registration_plate', $this->sortDirection)
                ->select('vehicle_mileage_readings.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        return $query->paginate($this->perPage);
    }

    /**
     * 🚗 LISTE DES VÉHICULES (POUR FILTRE)
     */
    public function getVehiclesProperty()
    {
        $user = auth()->user();

        return Vehicle::where('organization_id', $user->organization_id)
            ->orderBy('registration_plate')
            ->get();
    }

    /**
     * 👥 LISTE DES AUTEURS (POUR FILTRE)
     */
    public function getAuthorsProperty()
    {
        $user = auth()->user();

        return User::where('organization_id', $user->organization_id)
            ->whereHas('mileageReadings')
            ->orderBy('name')
            ->get();
    }

    /**
     * 📊 STATISTIQUES GLOBALES
     */
    public function getStatsProperty(): array
    {
        $user = auth()->user();

        $query = VehicleMileageReading::where('organization_id', $user->organization_id);

        // Appliquer le même scoping que pour les readings
        if ($user->can('view all mileage readings')) {
            // Tous
        } elseif ($user->can('view team mileage readings')) {
            $query->whereHas('vehicle', function ($q) use ($user) {
                if ($user->depot_id) {
                    $q->where('depot_id', $user->depot_id);
                }
            });
        } else {
            $query->where('recorded_by_id', $user->id);
        }

        $totalReadings = $query->count();
        $manualCount = (clone $query)->where('recording_method', 'manual')->count();
        $automaticCount = (clone $query)->where('recording_method', 'automatic')->count();
        $vehiclesWithReadings = (clone $query)->distinct('vehicle_id')->count();

        return [
            'total_readings' => $totalReadings,
            'manual_count' => $manualCount,
            'automatic_count' => $automaticCount,
            'vehicles_tracked' => $vehiclesWithReadings,
        ];
    }

    /**
     * 🎨 RENDER
     */
    public function render(): View
    {
        return view('livewire.admin.mileage-readings-index', [
            'readings' => $this->readings,
            'vehicles' => $this->vehicles,
            'authors' => $this->authors,
            'stats' => $this->stats,
        ])->layout('layouts.admin.catalyst');
    }
}
