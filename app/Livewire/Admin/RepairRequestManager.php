<?php

namespace App\Livewire\Admin;

use App\Models\RepairRequest;
use App\Models\Vehicle;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class RepairRequestManager extends Component
{
    use WithFileUploads, WithPagination;

    // Propriétés publiques pour les filtres
    public $filterStatus = '';
    public $filterPriority = '';
    public $filterVehicle = '';
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';

    // Propriétés pour les modals
    public $showCreateModal = false;
    public $showApprovalModal = false;
    public $showDetailsModal = false;
    public $showAssignSupplierModal = false;
    public $showCompleteWorkModal = false;

    // Propriété pour la demande sélectionnée
    public $selectedRequest = null;

    // Propriétés du formulaire de création
    public $vehicle_id = '';
    public $priority = 'non_urgente';
    public $description = '';
    public $location_description = '';
    public $estimated_cost = '';
    public $photos = [];
    public $attachments = [];

    // Propriétés pour les actions de workflow
    public $approvalComments = '';
    public $selectedSupplierId = '';
    public $actualCost = '';
    public $completionNotes = '';
    public $finalRating = '';
    public $workPhotos = [];

    // Propriétés pour les données de référence
    public $vehicles = [];
    public $suppliers = [];

    // Vue (liste ou kanban)
    public $viewType = 'kanban';

    protected $rules = [
        'vehicle_id' => 'required|exists:vehicles,id',
        'priority' => 'required|in:urgente,a_prevoir,non_urgente',
        'description' => 'required|string|min:10|max:2000',
        'location_description' => 'nullable|string|max:500',
        'estimated_cost' => 'nullable|numeric|min:0|max:999999.99',
        'photos.*' => 'nullable|image|max:5120',
        'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt|max:10240'
    ];

    protected $messages = [
        'vehicle_id.required' => 'Vous devez sélectionner un véhicule.',
        'priority.required' => 'Vous devez sélectionner une priorité.',
        'description.required' => 'La description est obligatoire.',
        'description.min' => 'La description doit contenir au moins 10 caractères.',
        'description.max' => 'La description ne peut pas dépasser 2000 caractères.',
        'estimated_cost.numeric' => 'Le coût estimé doit être un nombre.',
        'photos.*.image' => 'Les photos doivent être des images.',
        'photos.*.max' => 'Chaque photo ne peut pas dépasser 5 MB.',
    ];

    public function mount()
    {
        $this->loadReferenceData();
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $repairRequests = $this->getFilteredRequests();
        $stats = $this->getRepairStats();

        if ($this->viewType === 'kanban') {
            $kanbanData = $this->getKanbanData();
            return view('livewire.admin.repair-request-manager-kanban', [
                'repairRequests' => $repairRequests,
                'kanbanData' => $kanbanData,
                'stats' => $stats
            ]);
        }

        return view('livewire.admin.repair-request-manager', [
            'repairRequests' => $repairRequests,
            'stats' => $stats
        ]);
    }

    // Méthodes de filtrage
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterPriority()
    {
        $this->resetPage();
    }

    public function updatedFilterVehicle()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['filterStatus', 'filterPriority', 'filterVehicle', 'search']);
        $this->resetPage();
    }

    // Méthodes de gestion des modals
    public function openCreateModal()
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
        $this->resetErrorBag();
    }

    public function openApprovalModal($requestId)
    {
        $this->selectedRequest = RepairRequest::with(['vehicle', 'requester'])->find($requestId);
        $this->approvalComments = '';
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->selectedRequest = null;
        $this->approvalComments = '';
    }

    public function openDetailsModal($requestId)
    {
        $this->selectedRequest = RepairRequest::with([
            'vehicle',
            'requester',
            'supervisor',
            'manager',
            'assignedSupplier'
        ])->find($requestId);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedRequest = null;
    }

    public function openAssignSupplierModal($requestId)
    {
        $this->selectedRequest = RepairRequest::find($requestId);
        $this->selectedSupplierId = '';
        $this->showAssignSupplierModal = true;
    }

    public function closeAssignSupplierModal()
    {
        $this->showAssignSupplierModal = false;
        $this->selectedRequest = null;
        $this->selectedSupplierId = '';
    }

    public function openCompleteWorkModal($requestId)
    {
        $this->selectedRequest = RepairRequest::find($requestId);
        $this->reset(['actualCost', 'completionNotes', 'finalRating', 'workPhotos']);
        $this->showCompleteWorkModal = true;
    }

    public function closeCompleteWorkModal()
    {
        $this->showCompleteWorkModal = false;
        $this->selectedRequest = null;
        $this->reset(['actualCost', 'completionNotes', 'finalRating', 'workPhotos']);
    }

    // Méthodes CRUD
    public function createRequest()
    {
        $this->validate();

        try {
            $data = [
                'organization_id' => Auth::user()->organization_id,
                'vehicle_id' => $this->vehicle_id,
                'requested_by' => Auth::id(),
                'priority' => $this->priority,
                'description' => $this->description,
                'location_description' => $this->location_description,
                'estimated_cost' => $this->estimated_cost ?: null,
                'requested_at' => now()
            ];

            // Gérer l'upload des photos
            if (!empty($this->photos)) {
                $photoPaths = [];
                foreach ($this->photos as $photo) {
                    if ($photo->isValid()) {
                        $path = $photo->store('repair-requests/photos', 'public');
                        $photoPaths[] = $path;
                    }
                }
                $data['photos'] = $photoPaths;
            }

            // Gérer l'upload des attachments
            if (!empty($this->attachments)) {
                $attachmentData = [];
                foreach ($this->attachments as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('repair-requests/attachments', 'public');
                        $attachmentData[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'size' => $file->getSize(),
                            'type' => $file->getClientMimeType()
                        ];
                    }
                }
                $data['attachments'] = $attachmentData;
            }

            RepairRequest::create($data);

            $this->closeCreateModal();
            $this->dispatch('repair-request-created');
            session()->flash('message', 'Demande de réparation créée avec succès.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    // Méthodes de workflow
    public function approveRequest()
    {
        try {
            $user = Auth::user();

            if (!$this->selectedRequest->canBeApprovedBy($user)) {
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

    public function rejectRequest()
    {
        if (empty(trim($this->approvalComments))) {
            $this->addError('approvalComments', 'Un commentaire est requis pour rejeter une demande.');
            return;
        }

        try {
            $user = Auth::user();

            if (!$this->selectedRequest->canBeApprovedBy($user)) {
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

    public function validateRequest()
    {
        try {
            $user = Auth::user();

            if (!$this->selectedRequest->canBeValidatedBy($user)) {
                session()->flash('error', 'Vous n\'êtes pas autorisé à valider cette demande.');
                return;
            }

            $this->selectedRequest->validateByManager($user, $this->approvalComments);

            $this->closeApprovalModal();
            $this->dispatch('repair-request-updated');
            session()->flash('message', 'Demande validée avec succès.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function rejectByManager()
    {
        if (empty(trim($this->approvalComments))) {
            $this->addError('approvalComments', 'Un commentaire est requis pour rejeter une demande.');
            return;
        }

        try {
            $user = Auth::user();

            if (!$this->selectedRequest->canBeValidatedBy($user)) {
                session()->flash('error', 'Vous n\'êtes pas autorisé à rejeter cette demande.');
                return;
            }

            $this->selectedRequest->rejectByManager($user, $this->approvalComments);

            $this->closeApprovalModal();
            $this->dispatch('repair-request-updated');
            session()->flash('message', 'Demande rejetée par le manager.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function assignSupplier()
    {
        $this->validate([
            'selectedSupplierId' => 'required|exists:suppliers,id'
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

    public function startWork($requestId)
    {
        try {
            $request = RepairRequest::find($requestId);
            $request->startWork();

            $this->dispatch('repair-request-updated');
            session()->flash('message', 'Travaux démarrés.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function completeWork()
    {
        $this->validate([
            'actualCost' => 'required|numeric|min:0',
            'completionNotes' => 'nullable|string|max:1000',
            'finalRating' => 'nullable|numeric|between:1,10'
        ]);

        try {
            $workPhotosPaths = null;
            if (!empty($this->workPhotos)) {
                $workPhotosPaths = [];
                foreach ($this->workPhotos as $photo) {
                    if ($photo->isValid()) {
                        $path = $photo->store('repair-requests/work-photos', 'public');
                        $workPhotosPaths[] = $path;
                    }
                }
            }

            $this->selectedRequest->completeWork(
                $this->actualCost,
                $this->completionNotes,
                $workPhotosPaths,
                $this->finalRating
            );

            $this->closeCompleteWorkModal();
            $this->dispatch('repair-request-updated');
            session()->flash('message', 'Travaux complétés avec succès.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function cancelRequest($requestId)
    {
        try {
            $request = RepairRequest::find($requestId);
            $request->cancel();

            $this->dispatch('repair-request-updated');
            session()->flash('message', 'Demande annulée.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function switchView($type)
    {
        $this->viewType = $type;
    }

    // Méthodes utilitaires privées
    private function getFilteredRequests()
    {
        $query = RepairRequest::with(['vehicle', 'requester', 'supervisor', 'manager', 'assignedSupplier'])
                              ->forOrganization(Auth::user()->organization_id);

        // Filtres de base
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterPriority) {
            $query->where('priority', $this->filterPriority);
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

        // Filtres par date
        if ($this->dateFrom) {
            $query->whereDate('requested_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('requested_at', '<=', $this->dateTo);
        }

        // Filtrage par rôle utilisateur
        $user = Auth::user();
        if ($user->hasRole('driver')) {
            $query->where('requested_by', $user->id);
        }

        return $query->latest('requested_at')->paginate(15);
    }

    private function getKanbanData()
    {
        $organizationId = Auth::user()->organization_id;

        return [
            'pending' => RepairRequest::pending()
                                    ->forOrganization($organizationId)
                                    ->with(['vehicle', 'requester'])
                                    ->latest('requested_at')
                                    ->get(),
            'initial_approval' => RepairRequest::where('status', RepairRequest::STATUS_INITIAL_APPROVAL)
                                             ->forOrganization($organizationId)
                                             ->with(['vehicle', 'requester'])
                                             ->latest('supervisor_decided_at')
                                             ->get(),
            'approved' => RepairRequest::where('status', RepairRequest::STATUS_APPROVED)
                                     ->forOrganization($organizationId)
                                     ->with(['vehicle', 'requester', 'assignedSupplier'])
                                     ->latest('manager_decided_at')
                                     ->get(),
            'in_progress' => RepairRequest::inProgress()
                                        ->forOrganization($organizationId)
                                        ->with(['vehicle', 'requester', 'assignedSupplier'])
                                        ->latest('work_started_at')
                                        ->get(),
            'completed' => RepairRequest::completed()
                                      ->forOrganization($organizationId)
                                      ->with(['vehicle', 'requester', 'assignedSupplier'])
                                      ->latest('work_completed_at')
                                      ->limit(10)
                                      ->get()
        ];
    }

    private function getRepairStats()
    {
        $organizationId = Auth::user()->organization_id;

        return [
            'total' => RepairRequest::forOrganization($organizationId)->count(),
            'pending' => RepairRequest::pending()->forOrganization($organizationId)->count(),
            'urgent' => RepairRequest::urgent()->forOrganization($organizationId)->count(),
            'in_progress' => RepairRequest::inProgress()->forOrganization($organizationId)->count(),
            'completed_this_month' => RepairRequest::completed()
                                               ->forOrganization($organizationId)
                                               ->whereMonth('work_completed_at', now()->month)
                                               ->count(),
            'avg_cost' => RepairRequest::completed()
                                     ->forOrganization($organizationId)
                                     ->whereNotNull('actual_cost')
                                     ->avg('actual_cost') ?? 0,
            'total_cost_this_year' => RepairRequest::completed()
                                               ->forOrganization($organizationId)
                                               ->whereYear('work_completed_at', now()->year)
                                               ->sum('actual_cost') ?? 0
        ];
    }

    private function loadReferenceData()
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

    private function resetCreateForm()
    {
        $this->reset([
            'vehicle_id',
            'priority',
            'description',
            'location_description',
            'estimated_cost',
            'photos',
            'attachments'
        ]);
        $this->priority = 'non_urgente';
    }
}