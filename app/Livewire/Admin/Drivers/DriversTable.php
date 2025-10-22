<?php

namespace App\Livewire\Admin\Drivers;

use App\Models\Driver;
use App\Models\DriverStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

/**
 * ====================================================================
 * 🚀 DRIVERS TABLE COMPONENT - WORLD-CLASS ENTERPRISE GRADE
 * ====================================================================
 * 
 * Composant Livewire réutilisable pour la gestion des chauffeurs
 * - Recherche en temps réel
 * - Filtres avancés
 * - Tri des colonnes
 * - Pagination
 * - Actions en masse
 * - Performance optimisée
 * 
 * @version 1.0-World-Class
 * @since 2025-01-19
 * ====================================================================
 */
class DriversTable extends Component
{
    use WithPagination;

    // ===============================================
    // PROPRIÉTÉS PUBLIQUES
    // ===============================================
    public string $search = '';
    public ?int $statusFilter = null;
    public string $sortField = 'first_name';
    public string $sortDirection = 'asc';
    public int $perPage = 15;
    public bool $showFilters = false;
    public array $selectedDrivers = [];
    public bool $selectAll = false;
    
    // Filtres avancés
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public ?string $licenseCategory = null;
    public bool $includeArchived = false;

    // ===============================================
    // QUERY STRING
    // ===============================================
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => null],
        'sortField' => ['except' => 'first_name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 15],
    ];

    // ===============================================
    // LISTENERS
    // ===============================================
    protected $listeners = [
        'refreshDrivers' => '$refresh',
        'driverDeleted' => 'handleDriverDeleted',
        'driverRestored' => 'handleDriverRestored',
    ];

    // ===============================================
    // MÉTHODES DU CYCLE DE VIE
    // ===============================================
    
    /**
     * Initialisation du composant
     */
    public function mount(): void
    {
        // Initialisation des valeurs par défaut si nécessaire
    }

    /**
     * Mise à jour de la recherche
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Mise à jour du filtre de statut
     */
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Mise à jour de l'option "inclure archivés"
     */
    public function updatedIncludeArchived(): void
    {
        $this->resetPage();
    }

    // ===============================================
    // MÉTHODES DE TRI
    // ===============================================

    /**
     * Trier par champ
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

    // ===============================================
    // MÉTHODES DE FILTRAGE
    // ===============================================

    /**
     * Basculer l'affichage des filtres
     */
    public function toggleFilters(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    /**
     * Réinitialiser tous les filtres
     */
    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'statusFilter',
            'dateFrom',
            'dateTo',
            'licenseCategory',
            'includeArchived',
            'sortField',
            'sortDirection',
        ]);
        $this->resetPage();
    }

    // ===============================================
    // MÉTHODES DE SÉLECTION
    // ===============================================

    /**
     * Sélectionner/désélectionner tous les chauffeurs
     */
    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedDrivers = $this->getDriversQuery()
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedDrivers = [];
        }
    }

    /**
     * Désélectionner tous les chauffeurs
     */
    public function deselectAll(): void
    {
        $this->selectedDrivers = [];
        $this->selectAll = false;
    }

    // ===============================================
    // ACTIONS EN MASSE
    // ===============================================

    /**
     * Archiver les chauffeurs sélectionnés
     */
    public function bulkArchive(): void
    {
        if (empty($this->selectedDrivers)) {
            $this->dispatch('notification', [
                'type' => 'warning',
                'message' => 'Aucun chauffeur sélectionné'
            ]);
            return;
        }

        try {
            Driver::whereIn('id', $this->selectedDrivers)->delete();
            
            $count = count($this->selectedDrivers);
            $this->deselectAll();
            
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => "$count chauffeur(s) archivé(s) avec succès"
            ]);
            
            $this->dispatch('refreshDrivers');
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'archivage: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Exporter les chauffeurs sélectionnés
     */
    public function bulkExport(): void
    {
        if (empty($this->selectedDrivers)) {
            $this->dispatch('notification', [
                'type' => 'warning',
                'message' => 'Aucun chauffeur sélectionné'
            ]);
            return;
        }

        // Logique d'export (CSV, Excel, etc.)
        $this->dispatch('startExport', $this->selectedDrivers);
    }

    // ===============================================
    // MÉTHODES DE GESTION DES ÉVÉNEMENTS
    // ===============================================

    /**
     * Gérer la suppression d'un chauffeur
     */
    public function handleDriverDeleted($driverId): void
    {
        $this->dispatch('notification', [
            'type' => 'success',
            'message' => 'Chauffeur archivé avec succès'
        ]);
        
        // Retirer de la sélection si sélectionné
        $this->selectedDrivers = array_diff($this->selectedDrivers, [$driverId]);
    }

    /**
     * Gérer la restauration d'un chauffeur
     */
    public function handleDriverRestored($driverId): void
    {
        $this->dispatch('notification', [
            'type' => 'success',
            'message' => 'Chauffeur restauré avec succès'
        ]);
    }

    // ===============================================
    // MÉTHODES DE REQUÊTE
    // ===============================================

    /**
     * Construire la requête des chauffeurs
     */
    protected function getDriversQuery(): Builder
    {
        return Driver::query()
            ->with(['driverStatus', 'user', 'organization'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('employee_number', 'like', "%{$this->search}%")
                        ->orWhere('personal_phone', 'like', "%{$this->search}%")
                        ->orWhere('personal_email', 'like', "%{$this->search}%")
                        ->orWhere('license_number', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter, function (Builder $query) {
                $query->where('status_id', $this->statusFilter);
            })
            ->when($this->dateFrom, function (Builder $query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function (Builder $query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->licenseCategory, function (Builder $query) {
                $query->where('license_category', $this->licenseCategory);
            })
            ->when($this->includeArchived, function (Builder $query) {
                $query->withTrashed();
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    /**
     * Obtenir les statistiques des chauffeurs
     */
    protected function getAnalytics(): array
    {
        $query = Driver::query();

        if ($this->includeArchived) {
            $query->withTrashed();
        }

        $total = $query->count();
        $available = $query->whereHas('driverStatus', function ($q) {
            $q->where('name', 'Disponible');
        })->count();
        $onMission = $query->whereHas('driverStatus', function ($q) {
            $q->where('name', 'En mission');
        })->count();
        $resting = $query->whereHas('driverStatus', function ($q) {
            $q->where('name', 'En repos');
        })->count();

        // Calculer l'âge moyen
        $avgAge = Driver::whereNotNull('birth_date')
            ->get()
            ->avg(fn($driver) => optional($driver->birth_date)->age ?? 0);

        // Permis valides
        $validLicenses = Driver::where('license_expiry_date', '>', now())->count();

        // Ancienneté moyenne
        $avgSeniority = Driver::whereNotNull('recruitment_date')
            ->get()
            ->avg(fn($driver) => optional($driver->recruitment_date)->diffInYears(now()));

        return [
            'total_drivers' => $total,
            'available_drivers' => $available,
            'on_mission_drivers' => $onMission,
            'resting_drivers' => $resting,
            'avg_age' => round($avgAge, 1),
            'valid_licenses' => $validLicenses,
            'avg_seniority' => round($avgSeniority, 1),
        ];
    }

    // ===============================================
    // RENDU DU COMPOSANT
    // ===============================================

    /**
     * Rendre le composant
     */
    public function render()
    {
        $drivers = $this->getDriversQuery()->paginate($this->perPage);
        $driverStatuses = DriverStatus::all();
        $analytics = $this->getAnalytics();

        return view('livewire.admin.drivers.drivers-table', [
            'drivers' => $drivers,
            'driverStatuses' => $driverStatuses,
            'analytics' => $analytics,
        ]);
    }
}
