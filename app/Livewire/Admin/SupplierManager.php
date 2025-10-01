<?php

namespace App\Livewire\Admin;

use App\Models\Supplier;
use App\Models\SupplierRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class SupplierManager extends Component
{
    use WithFileUploads, WithPagination;

    // Propriétés pour les filtres
    public $filterType = '';
    public $filterWilaya = '';
    public $filterStatus = 'active';
    public $filterRating = '';
    public $search = '';

    // Propriétés pour les modals
    public $showCreateModal = false;
    public $showDetailsModal = false;
    public $showRatingModal = false;
    public $showBlacklistModal = false;

    // Propriété pour le fournisseur sélectionné
    public $selectedSupplier = null;

    // Propriétés du formulaire de création/modification
    public $supplier_type = '';
    public $company_name = '';
    public $trade_register = '';
    public $nif = '';
    public $nis = '';
    public $ai = '';
    public $contact_first_name = '';
    public $contact_last_name = '';
    public $contact_phone = '';
    public $contact_email = '';
    public $address = '';
    public $city = '';
    public $wilaya = '';
    public $commune = '';
    public $postal_code = '';
    public $phone = '';
    public $email = '';
    public $website = '';
    public $specialties = [];
    public $certifications = [];
    public $service_areas = [];
    public $contract_start_date = '';
    public $contract_end_date = '';
    public $payment_terms = 30;
    public $preferred_payment_method = 'virement';
    public $credit_limit = 0;
    public $bank_name = '';
    public $account_number = '';
    public $rib = '';
    public $is_preferred = false;
    public $is_certified = false;
    public $notes = '';

    // Propriétés pour les évaluations
    public $quality_rating = 5;
    public $timeliness_rating = 5;
    public $communication_rating = 5;
    public $pricing_rating = 5;
    public $overall_rating = 5;
    public $positive_feedback = '';
    public $negative_feedback = '';
    public $suggestions = '';
    public $would_recommend = true;

    // Propriétés pour blacklist
    public $blacklist_reason = '';

    // Vue sélectionnée
    public $viewType = 'grid';

    protected $rules = [
        'supplier_type' => 'required|in:mecanicien,assureur,station_service,pieces_detachees,peinture_carrosserie,pneumatiques,electricite_auto,controle_technique,transport_vehicules,autre',
        'company_name' => 'required|max:255',
        'trade_register' => 'nullable|regex:/^[0-9]{2}\/[0-9]{2}-[0-9]{7}$/',
        'nif' => 'nullable|regex:/^[0-9]{15}$/',
        'nis' => 'nullable|max:20',
        'ai' => 'nullable|max:20',
        'contact_first_name' => 'required|max:100',
        'contact_last_name' => 'required|max:100',
        'contact_phone' => 'required|max:50',
        'contact_email' => 'nullable|email|max:255',
        'address' => 'required',
        'city' => 'required|max:100',
        'wilaya' => 'required|max:50',
        'commune' => 'nullable|max:100',
        'postal_code' => 'nullable|max:10',
        'phone' => 'nullable|max:50',
        'email' => 'nullable|email|max:255',
        'website' => 'nullable|url|max:500',
        'specialties' => 'array',
        'certifications' => 'array',
        'service_areas' => 'array',
        'contract_start_date' => 'nullable|date',
        'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
        'payment_terms' => 'integer|min:0|max:365',
        'preferred_payment_method' => 'in:virement,cheque,especes,carte,traite',
        'credit_limit' => 'numeric|min:0',
        'bank_name' => 'nullable|max:255',
        'account_number' => 'nullable|max:50',
        'rib' => 'nullable|regex:/^[0-9]{20}$/',
        'is_preferred' => 'boolean',
        'is_certified' => 'boolean',
        'notes' => 'nullable'
    ];

    protected $messages = [
        'supplier_type.required' => 'Le type de fournisseur est obligatoire.',
        'company_name.required' => 'La raison sociale est obligatoire.',
        'trade_register.regex' => 'Le registre de commerce doit être au format XX/XX-XXXXXXX.',
        'nif.regex' => 'Le NIF doit contenir exactement 15 chiffres.',
        'rib.regex' => 'Le RIB doit contenir exactement 20 chiffres.',
        'contact_first_name.required' => 'Le prénom du contact est obligatoire.',
        'contact_last_name.required' => 'Le nom du contact est obligatoire.',
        'contact_phone.required' => 'Le téléphone du contact est obligatoire.',
        'address.required' => 'L\'adresse est obligatoire.',
        'city.required' => 'La ville est obligatoire.',
        'wilaya.required' => 'La wilaya est obligatoire.',
        'contract_end_date.after_or_equal' => 'La date de fin doit être postérieure à la date de début.',
        'website.url' => 'Le site web doit être une URL valide.'
    ];

    public function mount()
    {
        // Initialiser les valeurs par défaut
    }

    public function render()
    {
        $suppliers = $this->getFilteredSuppliers();
        $stats = $this->getSupplierStats();

        return view('livewire.admin.supplier-manager', [
            'suppliers' => $suppliers,
            'stats' => $stats,
            'supplierTypes' => Supplier::getSupplierTypes(),
            'wilayas' => Supplier::WILAYAS,
            'paymentMethods' => Supplier::getPaymentMethods()
        ]);
    }

    // Méthodes de filtrage
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedFilterWilaya()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterRating()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['filterType', 'filterWilaya', 'filterStatus', 'filterRating', 'search']);
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

    public function openDetailsModal($supplierId)
    {
        $this->selectedSupplier = Supplier::with(['ratings', 'repairRequests'])->find($supplierId);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedSupplier = null;
    }

    public function openRatingModal($supplierId)
    {
        $this->selectedSupplier = Supplier::find($supplierId);
        $this->resetRatingForm();
        $this->showRatingModal = true;
    }

    public function closeRatingModal()
    {
        $this->showRatingModal = false;
        $this->selectedSupplier = null;
        $this->resetRatingForm();
    }

    public function openBlacklistModal($supplierId)
    {
        $this->selectedSupplier = Supplier::find($supplierId);
        $this->blacklist_reason = '';
        $this->showBlacklistModal = true;
    }

    public function closeBlacklistModal()
    {
        $this->showBlacklistModal = false;
        $this->selectedSupplier = null;
        $this->blacklist_reason = '';
    }

    // Méthodes CRUD
    public function createSupplier()
    {
        $this->validate();

        try {
            $data = $this->getSupplierData();
            $data['organization_id'] = Auth::user()->organization_id;

            Supplier::create($data);

            $this->closeCreateModal();
            $this->dispatch('supplier-created');
            session()->flash('message', 'Fournisseur créé avec succès.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    public function editSupplier($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $this->fillForm($supplier);
        $this->showCreateModal = true;
    }

    public function updateSupplier()
    {
        $this->validate();

        try {
            $supplier = Supplier::findOrFail($this->selectedSupplier->id);
            $data = $this->getSupplierData();

            $supplier->update($data);

            $this->closeCreateModal();
            $this->dispatch('supplier-updated');
            session()->flash('message', 'Fournisseur mis à jour avec succès.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    public function deleteSupplier($supplierId)
    {
        try {
            $supplier = Supplier::findOrFail($supplierId);

            // Vérifier si le fournisseur peut être supprimé
            if ($supplier->repairRequests()->whereIn('status', ['en_cours', 'accordee'])->exists()) {
                session()->flash('error', 'Impossible de supprimer ce fournisseur car il a des réparations en cours.');
                return;
            }

            $supplier->delete();

            $this->dispatch('supplier-deleted');
            session()->flash('message', 'Fournisseur supprimé avec succès.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    // Méthodes d'évaluation
    public function rateSupplier()
    {
        $this->validate([
            'quality_rating' => 'required|numeric|between:1,10',
            'timeliness_rating' => 'required|numeric|between:1,10',
            'communication_rating' => 'required|numeric|between:1,10',
            'pricing_rating' => 'required|numeric|between:1,10',
            'overall_rating' => 'required|numeric|between:1,10',
            'positive_feedback' => 'nullable|string|max:1000',
            'negative_feedback' => 'nullable|string|max:1000',
            'suggestions' => 'nullable|string|max:1000',
            'would_recommend' => 'boolean'
        ]);

        try {
            $this->selectedSupplier->addRating([
                'organization_id' => Auth::user()->organization_id,
                'rated_by' => Auth::id(),
                'quality_rating' => $this->quality_rating,
                'timeliness_rating' => $this->timeliness_rating,
                'communication_rating' => $this->communication_rating,
                'pricing_rating' => $this->pricing_rating,
                'overall_rating' => $this->overall_rating,
                'positive_feedback' => $this->positive_feedback,
                'negative_feedback' => $this->negative_feedback,
                'suggestions' => $this->suggestions,
                'would_recommend' => $this->would_recommend
            ]);

            $this->closeRatingModal();
            $this->dispatch('supplier-rated');
            session()->flash('message', 'Évaluation ajoutée avec succès.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'évaluation: ' . $e->getMessage());
        }
    }

    // Méthodes de gestion du statut
    public function togglePreferred($supplierId)
    {
        try {
            $supplier = Supplier::findOrFail($supplierId);
            $supplier->update(['is_preferred' => !$supplier->is_preferred]);

            $this->dispatch('supplier-updated');
            $status = $supplier->fresh()->is_preferred ? 'privilégié' : 'standard';
            session()->flash('message', "Fournisseur marqué comme {$status}.");

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function toggleCertified($supplierId)
    {
        try {
            $supplier = Supplier::findOrFail($supplierId);
            $supplier->update(['is_certified' => !$supplier->is_certified]);

            $this->dispatch('supplier-updated');
            $status = $supplier->fresh()->is_certified ? 'certifié' : 'non certifié';
            session()->flash('message', "Fournisseur marqué comme {$status}.");

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function blacklistSupplier()
    {
        $this->validate([
            'blacklist_reason' => 'required|string|min:10|max:1000'
        ]);

        try {
            $this->selectedSupplier->blacklist($this->blacklist_reason);

            $this->closeBlacklistModal();
            $this->dispatch('supplier-blacklisted');
            session()->flash('message', 'Fournisseur blacklisté avec succès.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function unblacklistSupplier($supplierId)
    {
        try {
            $supplier = Supplier::findOrFail($supplierId);
            $supplier->unblacklist();

            $this->dispatch('supplier-updated');
            session()->flash('message', 'Fournisseur retiré de la blacklist.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    // Méthodes de validation DZ
    public function validateNIF()
    {
        if (empty($this->nif)) {
            $this->addError('nif', 'Veuillez saisir un NIF.');
            return;
        }

        if (Supplier::validateNIF($this->nif)) {
            $this->dispatch('nif-valid');
            session()->flash('nif-message', 'NIF valide.');
        } else {
            $this->addError('nif', 'NIF invalide. Format requis: 15 chiffres.');
        }
    }

    public function validateRC()
    {
        if (empty($this->trade_register)) {
            $this->addError('trade_register', 'Veuillez saisir un registre de commerce.');
            return;
        }

        if (Supplier::validateTradeRegister($this->trade_register)) {
            $this->dispatch('rc-valid');
            session()->flash('rc-message', 'Registre de commerce valide.');
        } else {
            $this->addError('trade_register', 'RC invalide. Format requis: XX/XX-XXXXXXX.');
        }
    }

    public function validateRIB()
    {
        if (empty($this->rib)) {
            return;
        }

        if (Supplier::validateRIB($this->rib)) {
            $this->dispatch('rib-valid');
            session()->flash('rib-message', 'RIB valide.');
        } else {
            $this->addError('rib', 'RIB invalide. Format requis: 20 chiffres.');
        }
    }

    public function switchView($type)
    {
        $this->viewType = $type;
    }

    // Méthodes utilitaires privées
    private function getFilteredSuppliers()
    {
        $query = Supplier::with(['ratings'])
                        ->forOrganization(Auth::user()->organization_id);

        // Filtres de base
        if ($this->filterType) {
            $query->byType($this->filterType);
        }

        if ($this->filterWilaya) {
            $query->byWilaya($this->filterWilaya);
        }

        if ($this->filterStatus) {
            switch ($this->filterStatus) {
                case 'active':
                    $query->active()->notBlacklisted();
                    break;
                case 'preferred':
                    $query->preferred();
                    break;
                case 'certified':
                    $query->certified();
                    break;
                case 'blacklisted':
                    $query->where('blacklisted', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        if ($this->filterRating) {
            $query->withRating((float)$this->filterRating);
        }

        if ($this->search) {
            $query->searchByName($this->search);
        }

        return $query->latest('created_at')->paginate(12);
    }

    private function getSupplierStats()
    {
        $organizationId = Auth::user()->organization_id;

        return [
            'total' => Supplier::forOrganization($organizationId)->count(),
            'active' => Supplier::forOrganization($organizationId)->active()->notBlacklisted()->count(),
            'preferred' => Supplier::forOrganization($organizationId)->preferred()->count(),
            'certified' => Supplier::forOrganization($organizationId)->certified()->count(),
            'blacklisted' => Supplier::forOrganization($organizationId)->where('blacklisted', true)->count(),
            'avg_rating' => Supplier::forOrganization($organizationId)->avg('rating') ?: 0,
            'top_wilayas' => Supplier::forOrganization($organizationId)
                                    ->selectRaw('wilaya, COUNT(*) as count')
                                    ->groupBy('wilaya')
                                    ->orderByDesc('count')
                                    ->limit(5)
                                    ->pluck('count', 'wilaya')
                                    ->toArray()
        ];
    }

    private function getSupplierData()
    {
        return [
            'supplier_type' => $this->supplier_type,
            'company_name' => $this->company_name,
            'trade_register' => $this->trade_register,
            'nif' => $this->nif,
            'nis' => $this->nis,
            'ai' => $this->ai,
            'contact_first_name' => $this->contact_first_name,
            'contact_last_name' => $this->contact_last_name,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'address' => $this->address,
            'city' => $this->city,
            'wilaya' => $this->wilaya,
            'commune' => $this->commune,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'specialties' => $this->specialties,
            'certifications' => $this->certifications,
            'service_areas' => $this->service_areas,
            'contract_start_date' => $this->contract_start_date ?: null,
            'contract_end_date' => $this->contract_end_date ?: null,
            'payment_terms' => $this->payment_terms,
            'preferred_payment_method' => $this->preferred_payment_method,
            'credit_limit' => $this->credit_limit,
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'rib' => $this->rib,
            'is_preferred' => $this->is_preferred,
            'is_certified' => $this->is_certified,
            'notes' => $this->notes
        ];
    }

    private function fillForm(Supplier $supplier)
    {
        $this->selectedSupplier = $supplier;
        $this->supplier_type = $supplier->supplier_type;
        $this->company_name = $supplier->company_name;
        $this->trade_register = $supplier->trade_register;
        $this->nif = $supplier->nif;
        $this->nis = $supplier->nis;
        $this->ai = $supplier->ai;
        $this->contact_first_name = $supplier->contact_first_name;
        $this->contact_last_name = $supplier->contact_last_name;
        $this->contact_phone = $supplier->contact_phone;
        $this->contact_email = $supplier->contact_email;
        $this->address = $supplier->address;
        $this->city = $supplier->city;
        $this->wilaya = $supplier->wilaya;
        $this->commune = $supplier->commune;
        $this->postal_code = $supplier->postal_code;
        $this->phone = $supplier->phone;
        $this->email = $supplier->email;
        $this->website = $supplier->website;
        $this->specialties = $supplier->specialties ?: [];
        $this->certifications = $supplier->certifications ?: [];
        $this->service_areas = $supplier->service_areas ?: [];
        $this->contract_start_date = $supplier->contract_start_date ? $supplier->contract_start_date->format('Y-m-d') : '';
        $this->contract_end_date = $supplier->contract_end_date ? $supplier->contract_end_date->format('Y-m-d') : '';
        $this->payment_terms = $supplier->payment_terms;
        $this->preferred_payment_method = $supplier->preferred_payment_method;
        $this->credit_limit = $supplier->credit_limit;
        $this->bank_name = $supplier->bank_name;
        $this->account_number = $supplier->account_number;
        $this->rib = $supplier->rib;
        $this->is_preferred = $supplier->is_preferred;
        $this->is_certified = $supplier->is_certified;
        $this->notes = $supplier->notes;
    }

    private function resetCreateForm()
    {
        $this->reset([
            'supplier_type', 'company_name', 'trade_register', 'nif', 'nis', 'ai',
            'contact_first_name', 'contact_last_name', 'contact_phone', 'contact_email',
            'address', 'city', 'wilaya', 'commune', 'postal_code', 'phone', 'email',
            'website', 'specialties', 'certifications', 'service_areas',
            'contract_start_date', 'contract_end_date', 'payment_terms',
            'preferred_payment_method', 'credit_limit', 'bank_name', 'account_number',
            'rib', 'is_preferred', 'is_certified', 'notes'
        ]);
        $this->payment_terms = 30;
        $this->preferred_payment_method = 'virement';
        $this->selectedSupplier = null;
    }

    private function resetRatingForm()
    {
        $this->reset([
            'quality_rating', 'timeliness_rating', 'communication_rating',
            'pricing_rating', 'overall_rating', 'positive_feedback',
            'negative_feedback', 'suggestions', 'would_recommend'
        ]);
        $this->quality_rating = 5;
        $this->timeliness_rating = 5;
        $this->communication_rating = 5;
        $this->pricing_rating = 5;
        $this->overall_rating = 5;
        $this->would_recommend = true;
    }
}