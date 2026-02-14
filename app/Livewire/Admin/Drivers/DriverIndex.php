<?php

namespace App\Livewire\Admin\Drivers;

use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\Assignment;
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
use Illuminate\Support\Str;

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
    public string $forceDeleteConfirm = '';

    public ?int $archivingDriverId = null;
    public bool $showArchiveModal = false;

    // 🧾 Bulk Confirmation Modals
    public bool $showBulkArchiveModal = false;
    public bool $showBulkRestoreModal = false;
    public bool $showBulkForceDeleteModal = false;
    public bool $showBulkStatusModal = false;
    public ?int $bulkStatusId = null;
    public string $bulkForceDeleteConfirm = '';

    // 🧠 Computed Properties
    #[\Livewire\Attributes\Computed]
    public function confirmingDriver()
    {
        $id = $this->archivingDriverId ?? $this->restoringDriverId ?? $this->forceDeletingDriverId;

        if (!$id) return null;

        return Driver::withTrashed()->find($id);
    }

    #[\Livewire\Attributes\Computed]
    public function confirmingDriverNonDriverRoles()
    {
        $driver = $this->confirmingDriver;
        if (!$driver || !$driver->user) {
            return collect();
        }

        return $driver->user->getRoleNames()
            ->filter(fn ($role) => $role !== 'Chauffeur')
            ->values();
    }

    #[\Livewire\Attributes\Computed]
    public function selectedDriversPreview()
    {
        if (empty($this->selectedDrivers)) {
            return collect();
        }

        return Driver::withTrashed()
            ->whereIn('id', $this->selectedDrivers)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->take(3)
            ->get();
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
        if (!$this->ensurePermission('drivers.delete', 'Permission refusée pour archiver des chauffeurs.')) {
            return;
        }

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

    public function confirmBulkStatusChange(): void
    {
        if (!$this->ensurePermission(['drivers.status.update', 'drivers.update'], 'Permission refusée pour changer le statut des chauffeurs.')) {
            return;
        }

        if (empty($this->selectedDrivers)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Aucun chauffeur sélectionné']);
            return;
        }

        $this->bulkStatusId = null;
        $this->showBulkStatusModal = true;
    }

    public function bulkChangeStatus(): void
    {
        if (!$this->ensurePermission(['drivers.status.update', 'drivers.update'], 'Permission refusée pour changer le statut des chauffeurs.')) {
            return;
        }

        $this->validate([
            'bulkStatusId' => ['required', Rule::exists(DriverStatus::class, 'id')],
            'selectedDrivers' => 'required|array|min:1',
        ]);

        $orgId = Auth::user()->organization_id;
        $isSuperAdmin = Auth::user()->hasRole('Super Admin');

        $query = Driver::whereIn('id', $this->selectedDrivers);
        if (!$isSuperAdmin && $orgId) {
            $query->where('organization_id', $orgId);
        }

        $count = $query->update(['status_id' => $this->bulkStatusId]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $count . ' chauffeur(s) mis à jour'
        ]);

        $this->resetBulkState();
    }

    public function cancelBulkStatusChange(): void
    {
        $this->showBulkStatusModal = false;
        $this->bulkStatusId = null;
    }

    public function confirmBulkArchive(): void
    {
        if (!$this->ensurePermission('drivers.delete', 'Permission refusée pour archiver des chauffeurs.')) {
            return;
        }

        if (empty($this->selectedDrivers)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Aucun chauffeur sélectionné']);
            return;
        }

        $this->showBulkArchiveModal = true;
    }

    public function bulkRestore()
    {
        if (!$this->ensurePermission('drivers.restore', 'Permission refusée pour restaurer des chauffeurs.')) {
            return;
        }

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

    public function confirmBulkRestore(): void
    {
        if (!$this->ensurePermission('drivers.restore', 'Permission refusée pour restaurer des chauffeurs.')) {
            return;
        }

        if (empty($this->selectedDrivers)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Aucun chauffeur sélectionné']);
            return;
        }

        $this->showBulkRestoreModal = true;
    }

    public function bulkForceDelete()
    {
        if (!$this->ensurePermission('drivers.force-delete', 'Permission refusée pour supprimer définitivement des chauffeurs.')) {
            return;
        }

        if (empty($this->selectedDrivers)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Aucun chauffeur sélectionné']);
            return;
        }

        if (Str::upper(trim($this->bulkForceDeleteConfirm)) !== 'SUPPRIMER') {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Veuillez saisir SUPPRIMER pour confirmer la suppression.']);
            return;
        }

        $count = 0;
        $warnings = [];
        foreach ($this->selectedDrivers as $id) {
            $result = $this->driverService->forceDeleteDriver($id);
            if (!empty($result['deleted'])) {
                $count++;
                if (!empty($result['user_skip_reason'])) {
                    $warnings[] = $result['user_skip_reason'];
                }
            }
        }

        $this->dispatch('toast', ['type' => 'success', 'message' => "$count chauffeur(s) supprimé(s) définitivement"]);
        if (!empty($warnings)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Certains comptes utilisateurs n\'ont pas été supprimés car ils possèdent d\'autres rôles.',
            ]);
        }
        $this->resetBulkState();
    }

    public function confirmBulkForceDelete(): void
    {
        if (empty($this->selectedDrivers)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Aucun chauffeur sélectionné']);
            return;
        }

        $this->bulkForceDeleteConfirm = '';
        $this->showBulkForceDeleteModal = true;
    }

    protected function resetBulkState()
    {
        $this->selectedDrivers = [];
        $this->selectAll = false;
        $this->showArchiveModal = false;
        $this->showRestoreModal = false;
        $this->showForceDeleteModal = false;
        $this->showBulkArchiveModal = false;
        $this->showBulkRestoreModal = false;
        $this->showBulkForceDeleteModal = false;
        $this->showBulkStatusModal = false;
        $this->bulkStatusId = null;
        $this->bulkForceDeleteConfirm = '';
    }

    // --- INDIVIDUAL ACTIONS ---

    // Archive
    public function confirmArchive(int $id)
    {
        if (!$this->ensurePermission('drivers.delete', 'Permission refusée pour archiver un chauffeur.')) {
            return;
        }

        $this->archivingDriverId = $id;
        $this->showArchiveModal = true;
    }

    public function cancelArchive()
    {
        $this->archivingDriverId = null;
        $this->showArchiveModal = false;
    }

    public function cancelBulkArchive(): void
    {
        $this->showBulkArchiveModal = false;
    }

    public function archiveDriver()
    {
        if (!$this->ensurePermission('drivers.delete', 'Permission refusée pour archiver un chauffeur.')) {
            return;
        }

        if (!$this->archivingDriverId) return;

        // Utiliser withTrashed() pour éviter les erreurs si déjà supprimé (race condition)
        $driver = Driver::withTrashed()->find($this->archivingDriverId);

        if (!$driver) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Chauffeur introuvable.']);
            $this->cancelArchive();
            return;
        }

        try {
            // Vérification pré-archivage : uniquement les affectations réellement actives
            // (les affectations annulées/passées ne doivent pas bloquer l'archivage).
            $referenceTime = now();
            $activeAssignment = $driver->assignments()
                ->where(function ($query) use ($referenceTime) {
                    $query->where(function ($activeQuery) use ($referenceTime) {
                        $activeQuery->where('status', Assignment::STATUS_ACTIVE)
                            ->where('start_datetime', '<=', $referenceTime)
                            ->where(function ($dateQuery) use ($referenceTime) {
                                $dateQuery->whereNull('end_datetime')
                                    ->orWhere('end_datetime', '>', $referenceTime);
                            });
                    })
                        ->orWhere(function ($legacyQuery) use ($referenceTime) {
                            $legacyQuery->whereNull('status')
                                ->where('start_datetime', '<=', $referenceTime)
                                ->where(function ($dateQuery) use ($referenceTime) {
                                    $dateQuery->whereNull('end_datetime')
                                        ->orWhere('end_datetime', '>', $referenceTime);
                                });
                        });
                })
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhere('status', '!=', Assignment::STATUS_CANCELLED);
                })
                ->first();

            if ($activeAssignment) {
                $start = optional($activeAssignment->start_datetime)->format('d/m/Y H:i') ?? 'N/A';
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Impossible d'archiver : affectation #{$activeAssignment->id} en cours (début : {$start})."
                ]);
                $this->cancelArchive();
                return;
            }

            if ($this->driverService->archiveDriver($driver)) {
                $this->dispatch('toast', ['type' => 'success', 'message' => 'Chauffeur archivé avec succès']);
            } else {
                // Fallback si le service bloque pour une autre raison.
                $this->dispatch('toast', ['type' => 'error', 'message' => 'Archivage refusé: vérifier les dépendances actives du chauffeur.']);
            }
        } catch (\Throwable $e) {
            \Log::error('Erreur archivage chauffeur', [
                'driver_id' => $driver->id,
                'message' => $e->getMessage(),
            ]);
            $this->dispatch('toast', ['type' => 'error', 'message' => "Erreur lors de l'archivage du chauffeur."]);
        }
        $this->cancelArchive();
    }

    // Restore
    public function confirmRestore(int $id)
    {
        if (!$this->ensurePermission('drivers.restore', 'Permission refusée pour restaurer un chauffeur.')) {
            return;
        }

        $this->restoringDriverId = $id;
        $this->showRestoreModal = true;
    }

    public function cancelRestore()
    {
        $this->restoringDriverId = null;
        $this->showRestoreModal = false;
    }

    public function cancelBulkRestore(): void
    {
        $this->showBulkRestoreModal = false;
    }

    public function restoreDriver()
    {
        if (!$this->ensurePermission('drivers.restore', 'Permission refusée pour restaurer un chauffeur.')) {
            return;
        }

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
        if (!$this->ensurePermission('drivers.force-delete', 'Permission refusée pour supprimer définitivement un chauffeur.')) {
            return;
        }

        $this->forceDeletingDriverId = $id;
        $this->forceDeleteConfirm = '';
        $this->showForceDeleteModal = true;
    }

    public function cancelForceDelete()
    {
        $this->forceDeletingDriverId = null;
        $this->showForceDeleteModal = false;
        $this->forceDeleteConfirm = '';
    }

    public function cancelBulkForceDelete(): void
    {
        $this->bulkForceDeleteConfirm = '';
        $this->showBulkForceDeleteModal = false;
    }

    public function forceDeleteDriver()
    {
        if (!$this->ensurePermission('drivers.force-delete', 'Permission refusée pour supprimer définitivement un chauffeur.')) {
            return;
        }

        if (!$this->forceDeletingDriverId) return;

        if (strtoupper(trim($this->forceDeleteConfirm)) !== 'SUPPRIMER') {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Veuillez saisir SUPPRIMER pour confirmer la suppression.']);
            return;
        }

        $result = $this->driverService->forceDeleteDriver($this->forceDeletingDriverId);
        if (!empty($result['deleted'])) {
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Chauffeur supprimé définitivement']);
            if (!empty($result['user_skip_reason'])) {
                $this->dispatch('toast', ['type' => 'warning', 'message' => $result['user_skip_reason']]);
            }
        } else {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Erreur lors de la suppression']);
        }
        $this->cancelForceDelete();
    }

    // --- EXPORT PDF (MICROSERVICE) ---
    public function exportPdf(int $id)
    {
        if (!$this->ensurePermission('drivers.export', 'Permission refusée pour exporter les chauffeurs.')) {
            return;
        }

        $driver = Driver::with(['driverStatus', 'user', 'organization', 'assignments.vehicle', 'supervisor', 'sanctions'])
            ->withTrashed()
            ->find($id);

        if (!$driver) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Chauffeur introuvable']);
            return;
        }

        // 1. Gestion de la photo en Base64 (vital pour le microservice PDF si domaine local inaccessible)
        $photoBase64 = null;
        if ($driver->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($driver->photo)) {
            try {
                $path = \Illuminate\Support\Facades\Storage::disk('public')->path($driver->photo);
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $photoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Impossible de charger la photo du chauffeur: ' . $e->getMessage());
            }
        }

        // 2. Générer le HTML
        $html = view('pdf.driver-profile', [
            'driver' => $driver,
            // Passer la photo encodée explicitement
            'photoBase64' => $photoBase64
        ])->render();

        // 3. Appeler le microservice PDF
        try {
            $response = Http::timeout(30)->post('http://pdf-service:3000/generate-pdf', [
                'html' => $html,
                'options' => [
                    'format' => 'A4',
                    'printBackground' => true,
                    'margin' => [
                        'top' => '0mm',    // Marges gérées par le CSS @page
                        'right' => '0mm',
                        'bottom' => '0mm',
                        'left' => '0mm'
                    ]
                ]
            ]);

            if ($response->successful()) {
                $filename = 'Fiche_Chauffeur_' . ($driver->employee_number ?? $driver->id) . '.pdf';

                // 💾 SAUVEGARDE TEMPORAIRE POUR STABILITÉ DU TÉLÉCHARGEMENT
                // Évite les problèmes de streaming Livewire qui peuvent retourner des UUIDs
                $tempPath = 'temp/' . \Illuminate\Support\Str::uuid() . '.pdf';
                \Illuminate\Support\Facades\Storage::put($tempPath, $response->body());

                return response()->download(
                    \Illuminate\Support\Facades\Storage::path($tempPath),
                    $filename,
                    ['Content-Type' => 'application/pdf']
                )->deleteFileAfterSend();
            } else {
                \Illuminate\Support\Facades\Log::error('PDF Service Error', ['body' => $response->body()]);
                $this->dispatch('toast', ['type' => 'error', 'message' => 'Erreur du service PDF: ' . $response->status()]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PDF Service Exception', ['error' => $e->getMessage()]);
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Service PDF indisponible']);
        }
    }

    protected function ensurePermission(array|string $ability, string $message): bool
    {
        $user = Auth::user();

        if (!$user) {
            $this->dispatch('toast', ['type' => 'error', 'message' => $message]);
            return false;
        }

        foreach ((array) $ability as $perm) {
            if ($user->can($perm)) {
                return true;
            }
        }

        $this->dispatch('toast', ['type' => 'error', 'message' => $message]);
        return false;
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
