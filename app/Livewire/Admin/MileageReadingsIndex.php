<?php

namespace App\Livewire\Admin;

use App\Models\VehicleMileageReading;
use App\Models\Vehicle;
use App\Models\User;
use App\Services\MileageReadingService;
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
     * 💼 SERVICE LAYER
     */
    protected MileageReadingService $service;

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
     *
     * Récupère les véhicules de l'organisation pour le filtre dropdown.
     *
     * Optimisations Enterprise-Grade:
     * - Select minimal (id, registration_plate, brand, model)
     * - Filtre multi-tenant strict (organization_id)
     * - Tri alphabétique par plaque
     * - Exclude soft deleted vehicles
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVehiclesProperty()
    {
        $user = auth()->user();

        return Vehicle::where('organization_id', $user->organization_id)
            ->select('id', 'registration_plate', 'brand', 'model')
            ->orderBy('registration_plate')
            ->get();
    }

    /**
     * 👥 LISTE DES AUTEURS (POUR FILTRE)
     *
     * Récupère uniquement les utilisateurs qui ont créé au moins un relevé
     * kilométrique dans l'organisation courante.
     *
     * Optimisations:
     * - Cache query pour éviter requêtes répétées
     * - Filtre multi-tenant (organization_id)
     * - Select minimal (id, name) pour performance
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAuthorsProperty()
    {
        $user = auth()->user();

        return User::where('organization_id', $user->organization_id)
            ->whereHas('mileageReadings', function ($query) use ($user) {
                // Filtre supplémentaire: relevés de la même organisation
                $query->where('organization_id', $user->organization_id);
            })
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    /**
     * 📊 ANALYTICS ENTERPRISE - VIA SERVICE LAYER
     * 
     * Utilise le MileageReadingService pour obtenir 20+ KPIs avancés
     * avec caching intelligent (5 minutes).
     */
    public function getAnalyticsProperty(): array
    {
        if (!isset($this->service)) {
            $this->service = app(MileageReadingService::class);
        }

        return $this->service->getAnalytics(auth()->user()->organization_id);
    }

    /**
     * 📊 STATISTIQUES GLOBALES (Compatibilité legacy)
     */
    public function getStatsProperty(): array
    {
        $analytics = $this->analytics;

        return [
            'total_readings' => $analytics['total_readings'] ?? 0,
            'manual_count' => $analytics['manual_count'] ?? 0,
            'automatic_count' => $analytics['automatic_count'] ?? 0,
            'vehicles_tracked' => $analytics['vehicles_tracked'] ?? 0,
            'last_reading_date' => $analytics['last_reading_date'] ?? null,
            'total_mileage_covered' => $analytics['total_mileage_covered'] ?? 0,
            'avg_daily_mileage' => $analytics['avg_daily_mileage'] ?? 0,
            'readings_last_7_days' => $analytics['readings_last_7_days'] ?? 0,
            'readings_last_30_days' => $analytics['readings_last_30_days'] ?? 0,
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
            'analytics' => $this->analytics, // Analytics complètes 20+ KPIs
        ]);
    }
}
