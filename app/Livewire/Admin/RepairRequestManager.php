<?php

namespace App\Livewire\Admin;

use App\Models\RepairRequest;
use App\Models\Vehicle;
use App\Models\Supplier;
use App\Models\Driver;
use App\Models\User;
use App\Services\RepairRequestService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class RepairRequestManager extends Component
{
    use AuthorizesRequests;
    use WithFileUploads, WithPagination;

    // ──────────────────────────────────────────────────────────
    // Filtres
    // ──────────────────────────────────────────────────────────

    public string $filterStatus = '';
    public string $filterUrgency = '';
    public string $filterVehicle = '';
    public string $search = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    // ──────────────────────────────────────────────────────────
    // Modals
    // ──────────────────────────────────────────────────────────

    public bool $showCreateModal = false;
    public bool $showApprovalModal = false;
    public bool $showDetailsModal = false;
    public bool $showAssignSupplierModal = false;
    public bool $showCompleteWorkModal = false;

    // ──────────────────────────────────────────────────────────
    // Demande sélectionnée
    // ──────────────────────────────────────────────────────────

    public $selectedRequest = null;

    // ──────────────────────────────────────────────────────────
    // Formulaire de création
    // ──────────────────────────────────────────────────────────

    public string $vehicle_id = '';
    public string $urgency = 'normal';
    public string $title = '';
    public string $description = '';
    public string $location_description = '';
    public string $estimated_cost = '';
    public array $photos = [];
    public array $attachments = [];

    // ──────────────────────────────────────────────────────────
    // Actions workflow
    // ──────────────────────────────────────────────────────────

    public string $approvalComments = '';
    public string $selectedSupplierId = '';
    public string $actualCost = '';
    public string $completionNotes = '';
    public string $finalRating = '';
    public array $workPhotos = [];

    // ──────────────────────────────────────────────────────────
    // Données de référence
    // ──────────────────────────────────────────────────────────

    public $vehicles = [];
    public $suppliers = [];

    // ──────────────────────────────────────────────────────────
    // Vue (liste ou kanban)
    // ──────────────────────────────────────────────────────────

    public string $viewType = 'kanban';

    // ──────────────────────────────────────────────────────────
    // Validation
    // ──────────────────────────────────────────────────────────

    protected $rules = [
        'vehicle_id'          => 'required|exists:vehicles,id',
        'urgency'             => 'required|in:low,normal,high,critical',
        'title'               => 'nullable|string|max:255',
        'description'         => 'required|string|min:10|max:2000',
        'location_description' => 'nullable|string|max:500',
        'estimated_cost'      => 'nullable|numeric|min:0|max:999999.99',
        'photos.*'            => 'nullable|image|max:5120',
        'attachments.*'       => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt|max:10240',
    ];

    protected $messages = [
        'vehicle_id.required'    => 'Vous devez sélectionner un véhicule.',
        'urgency.required'       => 'Vous devez sélectionner un niveau d\'urgence.',
        'urgency.in'             => 'Le niveau d\'urgence sélectionné n\'est pas valide.',
        'title.max'              => 'Le titre ne peut pas dépasser 255 caractères.',
        'description.required'   => 'La description est obligatoire.',
        'description.min'        => 'La description doit contenir au moins 10 caractères.',
        'description.max'        => 'La description ne peut pas dépasser 2000 caractères.',
        'estimated_cost.numeric' => 'Le coût estimé doit être un nombre.',
        'photos.*.image'         => 'Les photos doivent être des images.',
        'photos.*.max'           => 'Chaque photo ne peut pas dépasser 5 MB.',
    ];

    // ──────────────────────────────────────────────────────────
    // Lifecycle
    // ──────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->loadReferenceData();
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        if (empty($this->vehicles)) {
            $this->loadReferenceData();
        }

        $repairRequests = $this->getFilteredRequests();
        $stats = $this->getRepairStats();
        $kanbanData = $this->getKanbanData();

        return view('livewire.admin.repair-request-manager-kanban', [
            'repairRequests' => $repairRequests,
            'kanbanData'     => $kanbanData,
            'stats'          => $stats,
            'vehicles'       => $this->vehicles,
            'suppliers'      => $this->suppliers,
        ])->layout('layouts.admin.catalyst');
    }

    // ──────────────────────────────────────────────────────────
    // Filtrage réactif
    // ──────────────────────────────────────────────────────────

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterUrgency(): void
    {
        $this->resetPage();
    }

    public function updatedFilterVehicle(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['filterStatus', 'filterUrgency', 'filterVehicle', 'search']);
        $this->resetPage();
    }

    // ──────────────────────────────────────────────────────────
    // Gestion des modals
    // ──────────────────────────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
        $this->resetErrorBag();
    }

    public function openApprovalModal(int $requestId): void
    {
        $this->selectedRequest = RepairRequest::with(['vehicle', 'driver', 'requester'])->find($requestId);
        $this->approvalComments = '';
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal(): void
    {
        $this->showApprovalModal = false;
        $this->selectedRequest = null;
        $this->approvalComments = '';
    }

    public function openDetailsModal(int $requestId): void
    {
        $this->selectedRequest = RepairRequest::with([
            'vehicle',
            'driver',
            'requester',
            'supervisor',
            'fleetManager',
            'assignedSupplier',
        ])->find($requestId);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->selectedRequest = null;
    }

    public function openAssignSupplierModal(int $requestId): void
    {
        $this->selectedRequest = RepairRequest::find($requestId);
        $this->selectedSupplierId = '';
        $this->showAssignSupplierModal = true;
    }

    public function closeAssignSupplierModal(): void
    {
        $this->showAssignSupplierModal = false;
        $this->selectedRequest = null;
        $this->selectedSupplierId = '';
    }

    public function openCompleteWorkModal(int $requestId): void
    {
        $this->selectedRequest = RepairRequest::find($requestId);
        $this->reset(['actualCost', 'completionNotes', 'finalRating', 'workPhotos']);
        $this->showCompleteWorkModal = true;
    }

    public function closeCompleteWorkModal(): void
    {
        $this->showCompleteWorkModal = false;
        $this->selectedRequest = null;
        $this->reset(['actualCost', 'completionNotes', 'finalRating', 'workPhotos']);
    }

    // ──────────────────────────────────────────────────────────
    // Création
    // ──────────────────────────────────────────────────────────

    public function createRequest(): void
    {
        $this->authorize('create', RepairRequest::class);
        $this->validate();

        try {
            $user = Auth::user();
            $driverId = $this->resolveDriverIdForUser($user);
            if (! $driverId) {
                session()->flash('error', 'Aucun profil chauffeur lié à cet utilisateur.');
                return;
            }

            $repairRequest = app(RepairRequestService::class)->createRequest([
                'organization_id' => $user->organization_id,
                'vehicle_id'      => $this->vehicle_id,
                'driver_id'       => $driverId,
                'requested_by'    => $user->id,
                'title'           => $this->normalizedTitle(),
                'urgency'         => $this->urgency,
                'description'     => $this->description,
                'current_location' => $this->location_description,
                'location_description' => $this->location_description,
                'estimated_cost'  => $this->estimated_cost ?: null,
                'photos'          => $this->photos,
                'attachments'     => $this->attachments,
            ]);

            $this->closeCreateModal();
            $this->dispatch('repair-request-created');
            session()->flash('message', "Demande de réparation #{$repairRequest->id} créée avec succès.");
        } catch (\Exception $e) {
            report($e);
            $this->addError('createRequest', 'Échec de création: ' . $e->getMessage());
            session()->flash('error', 'Échec de création: ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────────────────
    // Workflow — Approbation L1 (Superviseur)
    // ──────────────────────────────────────────────────────────

    public function approveRequest(): void
    {
        try {
            $user = Auth::user();

            if (! $this->selectedRequest->canBeApprovedBy($user)) {
                session()->flash('error', 'Vous n\'êtes pas autorisé à approuver cette demande.');
                return;
            }

            $this->selectedRequest->approveBySupervisor($user, $this->approvalComments);

            $this->closeApprovalModal();
            $this->dispatch('repair-request-updated');
            session()->flash('message', 'Demande approuvée avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function rejectRequest(): void
    {
        if (empty(trim($this->approvalComments))) {
            $this->addError('approvalComments', 'Un commentaire est requis pour rejeter une demande.');
            return;
        }

        try {
            $user = Auth::user();

            if (! $this->selectedRequest->canBeApprovedBy($user)) {
                session()->flash('error', 'Vous n\'êtes pas autorisé à rejeter cette demande.');
                return;
            }

            $this->selectedRequest->rejectBySupervisor($user, $this->approvalComments);

            $this->closeApprovalModal();
            $this->dispatch('repair-request-updated');
            session()->flash('message', 'Demande rejetée.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────────────────
    // Workflow — Validation L2 (Gestionnaire de Flotte)
    // ──────────────────────────────────────────────────────────

    public function validateRequest(): void
    {
        try {
            $user = Auth::user();

            if (! $this->selectedRequest->canBeValidatedBy($user)) {
                session()->flash('error', 'Vous n\'êtes pas autorisé à valider cette demande.');
                return;
            }

            $this->selectedRequest->approveByFleetManager($user, $this->approvalComments);

            $this->closeApprovalModal();
            $this->dispatch('repair-request-updated');
            session()->flash('message', 'Demande validée avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function rejectByManager(): void
    {
        if (empty(trim($this->approvalComments))) {
            $this->addError('approvalComments', 'Un commentaire est requis pour rejeter une demande.');
            return;
        }

        try {
            $user = Auth::user();

            if (! $this->selectedRequest->canBeValidatedBy($user)) {
                session()->flash('error', 'Vous n\'êtes pas autorisé à rejeter cette demande.');
                return;
            }

            $this->selectedRequest->rejectByFleetManager($user, $this->approvalComments);

            $this->closeApprovalModal();
            $this->dispatch('repair-request-updated');
            session()->flash('message', 'Demande rejetée par le gestionnaire.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────────────────
    // Post-workflow actions
    // ──────────────────────────────────────────────────────────

    public function assignSupplier(): void
    {
        $this->validate([
            'selectedSupplierId' => 'required|exists:suppliers,id',
        ]);

        try {
            $this->selectedRequest->assignToSupplier($this->selectedSupplierId);

            $this->closeAssignSupplierModal();
            $this->dispatch('repair-request-updated');
            session()->flash('message', 'Fournisseur assigné avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function startWork(int $requestId): void
    {
        try {
            $request = RepairRequest::findOrFail($requestId);
            $request->startWork();

            $this->dispatch('repair-request-updated');
            session()->flash('message', 'Travaux démarrés.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function completeWork(): void
    {
        $this->validate([
            'actualCost'      => 'required|numeric|min:0',
            'completionNotes' => 'nullable|string|max:1000',
            'finalRating'     => 'nullable|numeric|between:1,10',
        ]);

        try {
            $workPhotosPaths = null;
            if (! empty($this->workPhotos)) {
                $workPhotosPaths = [];
                foreach ($this->workPhotos as $photo) {
                    if ($photo->isValid()) {
                        $workPhotosPaths[] = $photo->store('repair-requests/work-photos', 'public');
                    }
                }
            }

            $this->selectedRequest->completeWork(
                $this->actualCost,
                $this->completionNotes,
                $workPhotosPaths,
                $this->finalRating ?: null,
            );

            $this->closeCompleteWorkModal();
            $this->dispatch('repair-request-updated');
            session()->flash('message', 'Travaux complétés avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function cancelRequest(int $requestId): void
    {
        try {
            $request = RepairRequest::findOrFail($requestId);
            $request->cancel();

            $this->dispatch('repair-request-updated');
            session()->flash('message', 'Demande annulée.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function switchView(string $type): void
    {
        $this->viewType = $type;
    }

    // ──────────────────────────────────────────────────────────
    // Requêtes privées
    // ──────────────────────────────────────────────────────────

    private function getFilteredRequests()
    {
        $query = RepairRequest::with(['vehicle', 'driver', 'requester', 'supervisor', 'fleetManager', 'rejectedBy'])
            ->forOrganization(Auth::user()->organization_id);

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterUrgency) {
            $query->where('urgency', $this->filterUrgency);
        }

        if ($this->filterVehicle) {
            $query->where('vehicle_id', $this->filterVehicle);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('vehicle', function ($vq) {
                        $vq->where('registration_plate', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Isolation stricte : un chauffeur ne voit que ses propres demandes
        $user = Auth::user();
        if ($this->isDriverUser($user)) {
            $driverId = $this->resolveDriverIdForUser($user);
            if (! $driverId) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where('driver_id', $driverId);
            }
        }

        return $query->latest('created_at')->paginate(15);
    }

    private function getKanbanData(): array
    {
        $organizationId = Auth::user()->organization_id;
        $user = Auth::user();

        $baseQuery = RepairRequest::query()->forOrganization($organizationId);

        // Isolation stricte chauffeur
        if ($this->isDriverUser($user)) {
            $driverId = $this->resolveDriverIdForUser($user);
            if (! $driverId) {
                $baseQuery->whereRaw('1 = 0');
            } else {
                $baseQuery->where('driver_id', $driverId);
            }
        }

        return [
            'pending_supervisor' => (clone $baseQuery)
                ->where('status', RepairRequest::STATUS_PENDING_SUPERVISOR)
                ->with(['vehicle', 'driver', 'requester'])
                ->latest('created_at')
                ->get(),

            'approved_supervisor' => (clone $baseQuery)
                ->where('status', RepairRequest::STATUS_APPROVED_SUPERVISOR)
                ->with(['vehicle', 'driver', 'requester', 'supervisor'])
                ->latest('supervisor_approved_at')
                ->get(),

            'pending_fleet_manager' => (clone $baseQuery)
                ->where('status', RepairRequest::STATUS_PENDING_FLEET_MANAGER)
                ->with(['vehicle', 'driver', 'requester', 'supervisor'])
                ->latest('supervisor_approved_at')
                ->get(),

            'approved_final' => (clone $baseQuery)
                ->where('status', RepairRequest::STATUS_APPROVED_FINAL)
                ->with(['vehicle', 'driver', 'requester', 'supervisor', 'fleetManager'])
                ->latest('final_approved_at')
                ->limit(20)
                ->get(),

            'rejected' => (clone $baseQuery)
                ->rejected()
                ->with(['vehicle', 'driver', 'requester', 'rejectedBy'])
                ->latest('rejected_at')
                ->limit(10)
                ->get(),
        ];
    }

    private function getRepairStats(): array
    {
        $organizationId = Auth::user()->organization_id;
        $user = Auth::user();
        $baseQuery = RepairRequest::query()->forOrganization($organizationId);

        // Isolation stricte chauffeur
        if ($this->isDriverUser($user)) {
            $driverId = $this->resolveDriverIdForUser($user);
            if (! $driverId) {
                $baseQuery->whereRaw('1 = 0');
            } else {
                $baseQuery->where('driver_id', $driverId);
            }
        }

        return [
            'total'   => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->pending()->count(),
            'urgent'  => (clone $baseQuery)->urgent()->count(),
            'approved' => (clone $baseQuery)->approved()->count(),
            'rejected' => (clone $baseQuery)->rejected()->count(),

            'approved_this_month' => (clone $baseQuery)
                ->approved()
                ->whereMonth('final_approved_at', now()->month)
                ->count(),

            'avg_estimated_cost' => (clone $baseQuery)
                ->whereNotNull('estimated_cost')
                ->avg('estimated_cost') ?? 0,

            'total_estimated_cost' => (clone $baseQuery)
                ->approved()
                ->whereYear('final_approved_at', now()->year)
                ->sum('estimated_cost') ?? 0,
        ];
    }

    private function loadReferenceData(): void
    {
        $organizationId = Auth::user()->organization_id;

        $this->vehicles = Vehicle::where('organization_id', $organizationId)
            ->orderBy('registration_plate')
            ->get();

        $this->suppliers = Supplier::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->orderBy('company_name')
            ->get();
    }

    private function resetCreateForm(): void
    {
        $this->reset([
            'vehicle_id',
            'urgency',
            'title',
            'description',
            'location_description',
            'estimated_cost',
            'photos',
            'attachments',
        ]);
        $this->urgency = 'normal';
    }

    private function isDriverUser(?User $user): bool
    {
        return $user?->isDriverOnly() ?? false;
    }

    private function resolveDriverIdForUser(?User $user): ?int
    {
        if (! $user) {
            return null;
        }

        return Driver::query()
            ->where('organization_id', $user->organization_id)
            ->where('user_id', $user->id)
            ->value('id');
    }

    private function normalizedTitle(): string
    {
        $title = trim($this->title);
        if ($title !== '') {
            return $title;
        }

        return \Illuminate\Support\Str::limit(
            trim((string) \Illuminate\Support\Str::of($this->description)->squish()),
            120,
            ''
        );
    }
}
