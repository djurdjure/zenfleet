<?php

namespace App\Livewire\Admin\Drivers;

use App\Models\Driver;
use App\Models\DriverSanction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * ====================================================================
 * ⚖️ DRIVER SANCTIONS COMPONENT - WORLD-CLASS ENTERPRISE GRADE
 * ====================================================================
 * 
 * Composant Livewire pour la gestion des sanctions des chauffeurs
 * - Liste avec filtres avancés
 * - Création/Modification/Suppression
 * - Archivage/Désarchivage
 * - Upload pièces jointes
 * - Statistiques en temps réel
 * 
 * @version 1.0-World-Class
 * @since 2025-01-19
 * ====================================================================
 */
class DriverSanctions extends Component
{
    use WithPagination, WithFileUploads;

    // ===============================================
    // PROPRIÉTÉS PUBLIQUES
    // ===============================================

    // Recherche et filtres
    public string $search = '';
    public ?int $driverFilter = null;
    public string $sanctionTypeFilter = '';
    public string $severityFilter = '';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public bool $showArchived = false;
    public bool $showFilters = false;

    // Tri et pagination
    public string $sortField = 'sanction_date';
    public string $sortDirection = 'desc';
    public int $perPage = 15;

    // Modal
    public bool $showModal = false;
    public bool $editMode = false;
    public ?int $sanctionId = null;

    // Formulaire
    public ?int $driver_id = null;
    public string $sanction_type = '';
    public string $severity = 'medium'; // low, medium, high, critical
    public string $reason = '';
    public string $sanction_date = '';
    public ?string $duration_days = null;
    public $attachment = null;
    public ?string $existingAttachment = null;
    public string $status = 'active'; // active, appealed, cancelled, archived
    public ?string $notes = null;

    // ===============================================
    // PROPRIÉTÉS MODALES CONFIRMATION
    // ===============================================

    public bool $showArchiveModal = false;
    public ?int $sanctionToArchive = null;

    public bool $showRestoreModal = false;
    public ?int $sanctionToRestore = null;

    public bool $showForceDeleteModal = false;
    public ?int $sanctionToForceDelete = null;

    // ===============================================
    // RÈGLES DE VALIDATION
    // ===============================================

    protected $rules = [
        'driver_id' => 'required|exists:drivers,id',
        'sanction_type' => 'required|in:avertissement_verbal,avertissement_ecrit,mise_a_pied,suspension_permis,amende,blame,licenciement',
        'severity' => 'required|in:low,medium,high,critical',
        'reason' => 'required|string|min:10|max:2000',
        'sanction_date' => 'required|date|before_or_equal:today',
        'duration_days' => 'nullable|integer|min:1|max:365',
        'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        'status' => 'required|in:active,appealed,cancelled,archived',
        'notes' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'driver_id.required' => 'Le chauffeur est obligatoire',
        'sanction_type.required' => 'Le type de sanction est obligatoire',
        'severity.required' => 'Le niveau de gravité est obligatoire',
        'reason.required' => 'Le motif est obligatoire',
        'reason.min' => 'Le motif doit contenir au moins 10 caractères',
        'sanction_date.required' => 'La date est obligatoire',
        'sanction_date.before_or_equal' => 'La date ne peut pas être dans le futur',
        'attachment.max' => 'Le fichier ne doit pas dépasser 10 MB',
    ];

    // ===============================================
    // QUERY STRING
    // ===============================================

    protected $queryString = [
        'search' => ['except' => ''],
        'sanctionTypeFilter' => ['except' => ''],
        'sortField' => ['except' => 'sanction_date'],
        'sortDirection' => ['except' => 'desc'],
    ];

    // ===============================================
    // MÉTHODES DU CYCLE DE VIE
    // ===============================================

    public function mount(): void
    {
        $this->sanction_date = now()->format('Y-m-d');
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['search', 'driverFilter', 'sanctionTypeFilter', 'severityFilter', 'dateFrom', 'dateTo', 'showArchived'])) {
            $this->resetPage();
        }
    }

    // ===============================================
    // MÉTHODES DE TRI ET FILTRAGE
    // ===============================================

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleFilters(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'driverFilter',
            'sanctionTypeFilter',
            'severityFilter',
            'dateFrom',
            'dateTo',
            'showArchived',
            'sortField',
            'sortDirection',
        ]);
        $this->resetPage();
    }

    public function applyFilters(): void
    {
        $this->resetPage();
    }

    // ===============================================
    // MÉTHODES CRUD
    // ===============================================

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
        $this->dispatch('show-modal');
    }

    public function openEditModal(int $id): void
    {
        $sanction = DriverSanction::findOrFail($id);

        $this->sanctionId = $sanction->id;
        $this->driver_id = $sanction->driver_id;
        $this->sanction_type = $sanction->sanction_type;
        $this->severity = $sanction->severity ?? 'medium';
        $this->reason = $sanction->reason;
        $this->sanction_date = $sanction->sanction_date->format('Y-m-d');
        $this->duration_days = $sanction->duration_days;
        $this->existingAttachment = $sanction->attachment_path;
        $this->status = $sanction->status ?? 'active';
        $this->notes = $sanction->notes;

        $this->editMode = true;
        $this->showModal = true;
        $this->dispatch('show-modal');
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'organization_id' => auth()->user()->organization_id,
                'driver_id' => $this->driver_id,
                'sanction_type' => $this->sanction_type,
                'severity' => $this->severity ?? 'medium',
                'reason' => $this->reason,
                'sanction_date' => $this->sanction_date,
                'duration_days' => $this->duration_days,
                'status' => $this->status ?? 'active',
                'notes' => $this->notes,
                'supervisor_id' => auth()->id(),
            ];

            // Upload attachment
            if ($this->attachment) {
                $path = $this->attachment->store('sanctions', 'public');
                $data['attachment_path'] = $path;
            }

            if ($this->editMode) {
                $sanction = DriverSanction::findOrFail($this->sanctionId);

                // Delete old attachment if new one uploaded
                if ($this->attachment && $sanction->attachment_path) {
                    Storage::disk('public')->delete($sanction->attachment_path);
                }

                $sanction->update($data);

                $this->dispatch('notification', [
                    'type' => 'success',
                    'message' => 'Sanction modifiée avec succès'
                ]);
            } else {
                DriverSanction::create($data);

                $this->dispatch('notification', [
                    'type' => 'success',
                    'message' => 'Sanction ajoutée avec succès'
                ]);
            }

            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Erreur : ' . $e->getMessage()
            ]);
        }
    }

    public function deleteSanction(int $id): void
    {
        try {
            $sanction = DriverSanction::findOrFail($id);

            // Delete attachment if exists
            if ($sanction->attachment_path) {
                Storage::disk('public')->delete($sanction->attachment_path);
            }

            $sanction->delete();

            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Sanction supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Erreur lors de la suppression'
            ]);
        }
    }

    // ===============================================
    // MÉTHODES DE CONFIRMATION
    // ===============================================

    public function confirmArchive(int $id): void
    {
        $this->sanctionToArchive = $id;
        $this->showArchiveModal = true;
    }

    public function cancelArchive(): void
    {
        $this->showArchiveModal = false;
        $this->sanctionToArchive = null;
    }

    public function executeArchive(): void
    {
        if ($this->sanctionToArchive) {
            $sanction = DriverSanction::find($this->sanctionToArchive);
            if ($sanction) {
                $sanction->update(['status' => 'archived']);
                $this->dispatch('notification', [
                    'type' => 'success',
                    'message' => 'Sanction archivée avec succès'
                ]);
            }
        }
        $this->showArchiveModal = false;
        $this->sanctionToArchive = null;
        $this->resetPage();
    }

    public function confirmRestore(int $id): void
    {
        $this->sanctionToRestore = $id;
        $this->showRestoreModal = true;
    }

    public function cancelRestore(): void
    {
        $this->showRestoreModal = false;
        $this->sanctionToRestore = null;
    }

    public function executeRestore(): void
    {
        if ($this->sanctionToRestore) {
            $sanction = DriverSanction::find($this->sanctionToRestore);
            if ($sanction) {
                $sanction->update(['status' => 'active']);
                $this->dispatch('notification', [
                    'type' => 'success',
                    'message' => 'Sanction restaurée avec succès'
                ]);
            }
        }
        $this->showRestoreModal = false;
        $this->sanctionToRestore = null;
        $this->resetPage();
    }

    public function confirmForceDelete(int $id): void
    {
        $this->sanctionToForceDelete = $id;
        $this->showForceDeleteModal = true;
    }

    public function cancelForceDelete(): void
    {
        $this->showForceDeleteModal = false;
        $this->sanctionToForceDelete = null;
    }

    public function forceDelete(): void
    {
        if ($this->sanctionToForceDelete) {
            try {
                $sanction = DriverSanction::withTrashed()->find($this->sanctionToForceDelete);

                if ($sanction) {
                    // Delete attachment if exists
                    if ($sanction->attachment_path) {
                        Storage::disk('public')->delete($sanction->attachment_path);
                    }

                    $sanction->forceDelete();

                    $this->dispatch('notification', [
                        'type' => 'success',
                        'message' => 'Sanction supprimée définitivement'
                    ]);
                }
            } catch (\Exception $e) {
                $this->dispatch('notification', [
                    'type' => 'error',
                    'message' => 'Erreur lors de la suppression définitive'
                ]);
            }
        }
        $this->showForceDeleteModal = false;
        $this->sanctionToForceDelete = null;
        $this->resetPage();
    }

    public function removeAttachment(): void
    {
        $this->attachment = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset([
            'sanctionId',
            'driver_id',
            'sanction_type',
            'severity',
            'reason',
            'duration_days',
            'attachment',
            'existingAttachment',
            'status',
            'notes',
        ]);

        $this->sanction_date = now()->format('Y-m-d');
        $this->resetErrorBag();
    }

    // ===============================================
    // MÉTHODES DE REQUÊTE
    // ===============================================

    protected function getSanctionsQuery()
    {
        return DriverSanction::query()
            ->with(['driver', 'supervisor'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('reason', 'like', "%{$this->search}%")
                        ->orWhereHas('driver', function ($dq) {
                            $dq->where('first_name', 'like', "%{$this->search}%")
                                ->orWhere('last_name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->driverFilter, function ($query) {
                $query->where('driver_id', $this->driverFilter);
            })
            ->when($this->sanctionTypeFilter, function ($query) {
                $query->where('sanction_type', $this->sanctionTypeFilter);
            })
            ->when($this->severityFilter, function ($query) {
                $query->where('severity', $this->severityFilter);
            })
            ->when($this->dateFrom, function ($query) {
                try {
                    $date = $this->dateFrom;
                    // Support format d/m/Y
                    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
                        $date = \Carbon\Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                    }
                    $query->whereDate('sanction_date', '>=', $date);
                } catch (\Exception $e) {
                    // Ignore invalid dates
                }
            })
            ->when($this->dateTo, function ($query) {
                try {
                    $date = $this->dateTo;
                    // Support format d/m/Y
                    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
                        $date = \Carbon\Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                    }
                    $query->whereDate('sanction_date', '<=', $date);
                } catch (\Exception $e) {
                    // Ignore invalid dates
                }
            })
            ->when(!$this->showArchived, function ($query) {
                $query->where('status', '!=', 'archived');
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    protected function getStatistics(): array
    {
        $query = DriverSanction::query();

        if (!$this->showArchived) {
            $query->where('status', '!=', 'archived');
        }

        $total = $query->count();
        $active = $query->where('status', 'active')->count();
        $byType = $query->select('sanction_type', \DB::raw('count(*) as count'))
            ->groupBy('sanction_type')
            ->pluck('count', 'sanction_type')
            ->toArray();

        $bySeverity = $query->select('severity', \DB::raw('count(*) as count'))
            ->groupBy('severity')
            ->pluck('count', 'severity')
            ->toArray();

        return [
            'total' => $total,
            'active' => $active,
            'by_type' => $byType,
            'by_severity' => $bySeverity,
            'this_month' => DriverSanction::whereMonth('sanction_date', now()->month)
                ->whereYear('sanction_date', now()->year)
                ->count(),
        ];
    }

    // ===============================================
    // RENDU DU COMPOSANT
    // ===============================================

    public function render()
    {
        $sanctions = $this->getSanctionsQuery()->paginate($this->perPage);
        $drivers = Driver::orderBy('first_name')->get();
        $statistics = $this->getStatistics();

        return view('livewire.admin.drivers.driver-sanctions', [
            'sanctions' => $sanctions,
            'drivers' => $drivers,
            'statistics' => $statistics,
        ]);
    }
}
