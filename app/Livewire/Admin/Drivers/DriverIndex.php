<?php

namespace App\Livewire\Admin\Drivers;

use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\User;
use App\Services\DriverService;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;

/**
 * 👨‍💼 DRIVER INDEX - ENTERPRISE LIVEWIRE COMPONENT
 * 
 * Modernisation du module Chauffeurs :
 * - Filtrage temps réel
 * - Actions de masse
 * - Support SoftDeletes (Archives)
 * - Analytics intégrés
 */
class DriverIndex extends Component
{
    use WithPagination;

    // 🔍 Filtres
    #[Url(except: '')]
    public $search = '';

    #[Url(except: '')]
    public $status_id = '';

    #[Url(except: '')]
    public $license_category = '';

    #[Url(except: '')]
    public $visibility = 'active'; // active, archived, all

    public $perPage = 25;

    // ↕️ Tri
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // 📦 Sélection & Bulk Actions
    public $selectedDrivers = [];
    public $selectAll = false;

    // 🛡️ Modal States
    public ?int $restoringDriverId = null;
    public bool $showRestoreModal = false;

    public ?int $forceDeletingDriverId = null;
    public bool $showForceDeleteModal = false;

    public ?int $archivingDriverId = null;
    public bool $showArchiveModal = false;

    // 🧠 Computed Properties
    #[\Livewire\Attributes\Computed]
    public function confirmingDriver()
    {
        $id = $this->archivingDriverId ?? $this->restoringDriverId ?? $this->forceDeletingDriverId;

        if (!$id) return null;

        return Driver::withTrashed()->find($id);
    }

    // Services
    protected DriverService $driverService;

    public function boot(DriverService $driverService)
    {
        $this->driverService = $driverService;
    }

    // 🔄 Lifecycle Hooks
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingVisibility()
    {
        $this->resetPage();
    }
    public function updatingStatusId()
    {
        $this->resetPage();
    }

    /**
     * 🔄 Réinitialiser les filtres
     */
    public function resetFilters()
    {
        $this->search = '';
        $this->status_id = '';
        $this->license_category = '';
        $this->visibility = 'active';
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // --- BULK ACTIONS ---

    public function toggleSelection($id)
    {
        if (in_array($id, $this->selectedDrivers)) {
            $this->selectedDrivers = array_diff($this->selectedDrivers, [$id]);
        } else {
            $this->selectedDrivers[] = $id;
        }
        $this->selectAll = false;
    }

    public function toggleAll()
    {
        $this->selectAll = !$this->selectAll;
        if ($this->selectAll) {
            $this->selectedDrivers = $this->getDriversQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedDrivers = [];
        }
    }

    public function bulkArchive()
    {
        if (empty($this->selectedDrivers)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Aucun chauffeur sélectionné']);
            return;
        }

        $count = 0;
        $errors = 0;

        $drivers = Driver::whereIn('id', $this->selectedDrivers)->get();

        foreach ($drivers as $driver) {
            if ($this->driverService->archiveDriver($driver)) {
                $count++;
            } else {
                $errors++;
            }
        }

        if ($errors > 0) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => "$count archivé(s), $errors impossible(s) (affectations actives)"]);
        } else {
            $this->dispatch('toast', ['type' => 'success', 'message' => "$count chauffeur(s) archivé(s)"]);
        }

        $this->resetBulkState();
    }

    public function bulkRestore()
    {
        if (empty($this->selectedDrivers)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Aucun chauffeur sélectionné']);
            return;
        }

        $count = 0;
        foreach ($this->selectedDrivers as $id) {
            if ($this->driverService->restoreDriver($id)) {
                $count++;
            }
        }

        $this->dispatch('toast', ['type' => 'success', 'message' => "$count chauffeur(s) restauré(s)"]);
        $this->resetBulkState();
    }

    public function bulkForceDelete()
    {
        if (empty($this->selectedDrivers)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Aucun chauffeur sélectionné']);
            return;
        }

        $count = 0;
        foreach ($this->selectedDrivers as $id) {
            if ($this->driverService->forceDeleteDriver($id)) {
                $count++;
            }
        }

        $this->dispatch('toast', ['type' => 'success', 'message' => "$count chauffeur(s) supprimé(s) définitivement"]);
        $this->resetBulkState();
    }

    protected function resetBulkState()
    {
        $this->selectedDrivers = [];
        $this->selectAll = false;
        $this->showArchiveModal = false;
        $this->showRestoreModal = false;
        $this->showForceDeleteModal = false;
    }

    // --- INDIVIDUAL ACTIONS ---

    // Archive
    public function confirmArchive(int $id)
    {
        $this->archivingDriverId = $id;
        $this->showArchiveModal = true;
    }

    public function cancelArchive()
    {
        $this->archivingDriverId = null;
        $this->showArchiveModal = false;
    }

    public function archiveDriver()
    {
        if (!$this->archivingDriverId) return;

        // Utiliser withTrashed() pour éviter les erreurs si déjà supprimé (race condition)
        $driver = Driver::withTrashed()->find($this->archivingDriverId);

        if (!$driver) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Chauffeur introuvable.']);
            $this->cancelArchive();
            return;
        }

        // 🔍 Vérification pré-archivage pour UX détaillée
        $activeAssignment = $driver->assignments()
            ->where(function ($query) {
                $query->whereNull('end_datetime')
                    ->orWhere('end_datetime', '>', now());
            })
            ->first();

        if ($activeAssignment) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => "Impossible d'archiver : Affectation #{$activeAssignment->id} en cours (Début : " . $activeAssignment->start_datetime->format('d/m/Y H:i') . ")"
            ]);
            $this->cancelArchive();
            return;
        }

        if ($this->driverService->archiveDriver($driver)) {
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Chauffeur archivé avec succès']);
        } else {
            // Fallback si le service bloque pour une autre raison
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Erreur lors de l\'archivage.']);
        }
        $this->cancelArchive();
    }

    // Restore
    public function confirmRestore(int $id)
    {
        $this->restoringDriverId = $id;
        $this->showRestoreModal = true;
    }

    public function cancelRestore()
    {
        $this->restoringDriverId = null;
        $this->showRestoreModal = false;
    }

    public function restoreDriver()
    {
        if (!$this->restoringDriverId) return;

        if ($this->driverService->restoreDriver($this->restoringDriverId)) {
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Chauffeur restauré avec succès']);
        } else {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Erreur lors de la restauration']);
        }
        $this->cancelRestore();
    }

    // Force Delete
    public function confirmForceDelete(int $id)
    {
        $this->forceDeletingDriverId = $id;
        $this->showForceDeleteModal = true;
    }

    public function cancelForceDelete()
    {
        $this->forceDeletingDriverId = null;
        $this->showForceDeleteModal = false;
    }

    public function forceDeleteDriver()
    {
        if (!$this->forceDeletingDriverId) return;

        if ($this->driverService->forceDeleteDriver($this->forceDeletingDriverId)) {
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Chauffeur supprimé définitivement']);
        } else {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Erreur lors de la suppression']);
        }
        $this->cancelForceDelete();
    }

    // --- EXPORT PDF (MICROSERVICE) ---
    public function exportPdf(int $id)
    {
        $driver = Driver::with(['driverStatus', 'user', 'organization', 'assignments.vehicle', 'supervisor'])
            ->withTrashed()
            ->find($id);

        if (!$driver) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Chauffeur introuvable']);
            return;
        }

        // 1. Générer le HTML
        $html = view('pdf.driver-profile', ['driver' => $driver])->render();

        // 2. Appeler le microservice PDF
        try {
            $response = Http::timeout(30)->post('http://pdf-service:3000/generate-pdf', [
                'html' => $html,
                'options' => [
                    'format' => 'A4',
                    'printBackground' => true,
                    'margin' => [
                        'top' => '10mm',
                        'right' => '10mm',
                        'bottom' => '10mm',
                        'left' => '10mm'
                    ]
                ]
            ]);

            if ($response->successful()) {
                $filename = 'Fiche_Chauffeur_' . $driver->employee_number . '.pdf';

                return response()->streamDownload(function () use ($response) {
                    echo $response->body();
                }, $filename);
            } else {
                \Illuminate\Support\Facades\Log::error('PDF Service Error', ['body' => $response->body()]);
                $this->dispatch('toast', ['type' => 'error', 'message' => 'Erreur du service PDF: ' . $response->status()]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PDF Service Exception', ['error' => $e->getMessage()]);
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Service PDF indisponible']);
        }
    }

    // --- DATA FETCHING ---

    public function getDriversQuery(): Builder
    {
        $query = Driver::query()
            ->with(['driverStatus', 'user', 'activeAssignment.vehicle']);

        // Multi-tenant scope is global in Driver model

        // Search
        $query->when($this->search, function ($q) {
            $q->where(function ($sub) {
                $sub->where('first_name', 'ilike', "%{$this->search}%")
                    ->orWhere('last_name', 'ilike', "%{$this->search}%")
                    ->orWhere('employee_number', 'ilike', "%{$this->search}%")
                    ->orWhere('license_number', 'ilike', "%{$this->search}%")
                    ->orWhere('personal_email', 'ilike', "%{$this->search}%");
            });
        });

        // Filters
        $query->when($this->status_id, fn($q) => $q->where('status_id', $this->status_id));
        $query->when($this->license_category, fn($q) => $q->whereJsonContains('license_categories', $this->license_category));

        // Visibility (Soft Deletes)
        if ($this->visibility === 'archived') {
            $query->onlyTrashed();
        } elseif ($this->visibility === 'all') {
            $query->withTrashed();
        }

        // Sorting
        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $drivers = $this->getDriversQuery()->paginate($this->perPage);

        $driverStatuses = Cache::remember('driver_statuses', 3600, fn() => DriverStatus::orderBy('name')->get());

        // Analytics (Simplified)
        $baseQuery = Driver::query();
        if (Auth::user()->organization_id && !Auth::user()->hasRole('Super Admin')) {
            $baseQuery->where('organization_id', Auth::user()->organization_id);
        }

        $analytics = [
            'total_drivers' => (clone $baseQuery)->count(),
            'available_drivers' => (clone $baseQuery)->whereHas('driverStatus', fn($q) => $q->where('name', 'Disponible'))->count(),
            'active_drivers' => (clone $baseQuery)->whereHas('driverStatus', fn($q) => $q->where('name', 'En mission'))->count(),
            'resting_drivers' => (clone $baseQuery)->whereHas('driverStatus', fn($q) => $q->where('name', 'En repos'))->count(),
        ];

        return view('livewire.admin.drivers.driver-index', [
            'drivers' => $drivers,
            'driverStatuses' => $driverStatuses,
            'analytics' => $analytics
        ])->extends('layouts.admin.catalyst')->section('content');
    }
}
