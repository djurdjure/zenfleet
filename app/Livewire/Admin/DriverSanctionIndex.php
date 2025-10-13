<?php

namespace App\Livewire\Admin;

use App\Models\Driver;
use App\Models\DriverSanction;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

/**
 * Composant Livewire pour la gestion des sanctions de chauffeurs
 *
 * Fonctionnalités:
 * - Affichage de l'historique des sanctions avec pagination
 * - Filtres multi-critères (type, date, chauffeur, archivé)
 * - Création/modification/suppression de sanctions
 * - Archivage/désarchivage
 * - Upload de pièces jointes
 * - Respect des autorisations via Policy
 *
 * @author ZenFleet Enterprise Team
 * @version 1.0.0
 * @package App\Livewire\Admin
 */
class DriverSanctionIndex extends Component
{
    use WithPagination, WithFileUploads, AuthorizesRequests;

    /**
     * Propriétés pour les filtres
     */
    public $showFilters = false;
    public $filterSanctionType = '';
    public $filterDriverId = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterArchived = 'active'; // active|archived|all
    public $search = '';

    /**
     * Propriétés pour le formulaire modal
     */
    public $showModal = false;
    public $editMode = false;
    public $sanctionId = null;

    // Champs du formulaire
    public $driver_id;
    public $sanction_type;
    public $reason;
    public $sanction_date;
    public $attachment;
    public $existingAttachmentPath = null;

    /**
     * Propriétés pour la confirmation de suppression
     */
    public $showDeleteModal = false;
    public $sanctionToDelete = null;

    /**
     * Règles de validation
     *
     * @var array
     */
    protected $rules = [
        'driver_id' => 'required|exists:drivers,id',
        'sanction_type' => 'required|in:avertissement_verbal,avertissement_ecrit,mise_a_pied,mise_en_demeure',
        'reason' => 'required|string|min:10|max:5000',
        'sanction_date' => 'required|date|before_or_equal:today',
        'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
    ];

    /**
     * Messages de validation personnalisés
     *
     * @var array
     */
    protected $messages = [
        'driver_id.required' => 'Le chauffeur est obligatoire.',
        'driver_id.exists' => 'Le chauffeur sélectionné n\'existe pas.',
        'sanction_type.required' => 'Le type de sanction est obligatoire.',
        'sanction_type.in' => 'Le type de sanction sélectionné est invalide.',
        'reason.required' => 'La raison est obligatoire.',
        'reason.min' => 'La raison doit contenir au moins 10 caractères.',
        'reason.max' => 'La raison ne peut pas dépasser 5000 caractères.',
        'sanction_date.required' => 'La date de sanction est obligatoire.',
        'sanction_date.date' => 'La date de sanction doit être une date valide.',
        'sanction_date.before_or_equal' => 'La date de sanction ne peut pas être dans le futur.',
        'attachment.file' => 'La pièce jointe doit être un fichier.',
        'attachment.mimes' => 'La pièce jointe doit être un fichier PDF, JPG, JPEG ou PNG.',
        'attachment.max' => 'La pièce jointe ne peut pas dépasser 5 MB.',
    ];

    /**
     * Réinitialiser la pagination lors d'un changement de filtre
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterSanctionType()
    {
        $this->resetPage();
    }

    public function updatingFilterDriverId()
    {
        $this->resetPage();
    }

    public function updatingFilterDateFrom()
    {
        $this->resetPage();
    }

    public function updatingFilterDateTo()
    {
        $this->resetPage();
    }

    public function updatingFilterArchived()
    {
        $this->resetPage();
    }

    /**
     * Réinitialiser tous les filtres
     */
    public function resetFilters()
    {
        $this->reset([
            'filterSanctionType',
            'filterDriverId',
            'filterDateFrom',
            'filterDateTo',
            'filterArchived',
            'search'
        ]);
        $this->resetPage();
    }

    /**
     * Ouvrir le modal de création
     */
    public function create()
    {
        $this->authorize('create', DriverSanction::class);

        $this->reset([
            'editMode',
            'sanctionId',
            'driver_id',
            'sanction_type',
            'reason',
            'sanction_date',
            'attachment',
            'existingAttachmentPath'
        ]);

        $this->sanction_date = now()->format('Y-m-d');
        $this->showModal = true;
    }

    /**
     * Ouvrir le modal d'édition
     *
     * @param int $id
     */
    public function edit($id)
    {
        $sanction = DriverSanction::findOrFail($id);
        $this->authorize('update', $sanction);

        $this->editMode = true;
        $this->sanctionId = $sanction->id;
        $this->driver_id = $sanction->driver_id;
        $this->sanction_type = $sanction->sanction_type;
        $this->reason = $sanction->reason;
        $this->sanction_date = $sanction->sanction_date->format('Y-m-d');
        $this->existingAttachmentPath = $sanction->attachment_path;
        $this->attachment = null;

        $this->showModal = true;
    }

    /**
     * Sauvegarder la sanction (création ou modification)
     */
    public function save()
    {
        $this->validate();

        try {
            if ($this->editMode) {
                // Modification
                $sanction = DriverSanction::findOrFail($this->sanctionId);
                $this->authorize('update', $sanction);

                $sanction->update([
                    'driver_id' => $this->driver_id,
                    'sanction_type' => $this->sanction_type,
                    'reason' => $this->reason,
                    'sanction_date' => $this->sanction_date,
                ]);

                // Gérer le nouveau fichier si uploadé
                if ($this->attachment) {
                    // Supprimer l'ancien fichier
                    if ($sanction->attachment_path) {
                        Storage::delete($sanction->attachment_path);
                    }

                    // Enregistrer le nouveau fichier
                    $path = $this->attachment->store('sanctions', 'public');
                    $sanction->update(['attachment_path' => $path]);
                }

                session()->flash('success', 'Sanction modifiée avec succès.');
            } else {
                // Création
                $this->authorize('create', DriverSanction::class);

                $data = [
                    'organization_id' => auth()->user()->organization_id,
                    'driver_id' => $this->driver_id,
                    'supervisor_id' => auth()->id(),
                    'sanction_type' => $this->sanction_type,
                    'reason' => $this->reason,
                    'sanction_date' => $this->sanction_date,
                ];

                // Gérer le fichier uploadé
                if ($this->attachment) {
                    $data['attachment_path'] = $this->attachment->store('sanctions', 'public');
                }

                DriverSanction::create($data);

                session()->flash('success', 'Sanction créée avec succès.');
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    /**
     * Fermer le modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->reset([
            'editMode',
            'sanctionId',
            'driver_id',
            'sanction_type',
            'reason',
            'sanction_date',
            'attachment',
            'existingAttachmentPath'
        ]);
        $this->resetValidation();
    }

    /**
     * Confirmer la suppression
     *
     * @param int $id
     */
    public function confirmDelete($id)
    {
        $sanction = DriverSanction::findOrFail($id);
        $this->authorize('delete', $sanction);

        $this->sanctionToDelete = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Supprimer la sanction
     */
    public function delete()
    {
        try {
            $sanction = DriverSanction::findOrFail($this->sanctionToDelete);
            $this->authorize('delete', $sanction);

            // Supprimer le fichier attaché
            if ($sanction->attachment_path) {
                Storage::delete($sanction->attachment_path);
            }

            $sanction->delete();

            session()->flash('success', 'Sanction supprimée avec succès.');
            $this->showDeleteModal = false;
            $this->sanctionToDelete = null;
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Annuler la suppression
     */
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->sanctionToDelete = null;
    }

    /**
     * Archiver une sanction
     *
     * @param int $id
     */
    public function archive($id)
    {
        try {
            $sanction = DriverSanction::findOrFail($id);
            $this->authorize('archive', $sanction);

            $sanction->archive();

            session()->flash('success', 'Sanction archivée avec succès.');
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'archivage: ' . $e->getMessage());
        }
    }

    /**
     * Désarchiver une sanction
     *
     * @param int $id
     */
    public function unarchive($id)
    {
        try {
            $sanction = DriverSanction::findOrFail($id);
            $this->authorize('unarchive', $sanction);

            $sanction->unarchive();

            session()->flash('success', 'Sanction désarchivée avec succès.');
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du désarchivage: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer la pièce jointe existante
     */
    public function removeExistingAttachment()
    {
        if ($this->editMode && $this->sanctionId) {
            $sanction = DriverSanction::findOrFail($this->sanctionId);
            $this->authorize('update', $sanction);

            if ($sanction->deleteAttachment()) {
                $this->existingAttachmentPath = null;
                session()->flash('success', 'Pièce jointe supprimée avec succès.');
            }
        }
    }

    /**
     * Obtenir les sanctions avec filtres appliqués
     */
    public function getSanctionsProperty()
    {
        $query = DriverSanction::query()
            ->with(['driver', 'supervisor', 'organization'])
            ->orderBy('sanction_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Filtre par type de sanction
        if ($this->filterSanctionType) {
            $query->where('sanction_type', $this->filterSanctionType);
        }

        // Filtre par chauffeur
        if ($this->filterDriverId) {
            $query->where('driver_id', $this->filterDriverId);
        }

        // Filtre par date de début
        if ($this->filterDateFrom) {
            $query->where('sanction_date', '>=', $this->filterDateFrom);
        }

        // Filtre par date de fin
        if ($this->filterDateTo) {
            $query->where('sanction_date', '<=', $this->filterDateTo);
        }

        // Filtre par statut archivé
        if ($this->filterArchived === 'active') {
            $query->active();
        } elseif ($this->filterArchived === 'archived') {
            $query->archived();
        }

        // Recherche globale
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reason', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('driver', function ($dq) {
                        $dq->where('first_name', 'ilike', '%' . $this->search . '%')
                            ->orWhere('last_name', 'ilike', '%' . $this->search . '%');
                    });
            });
        }

        return $query->paginate(15);
    }

    /**
     * Obtenir les chauffeurs pour le select
     */
    public function getDriversProperty()
    {
        return Driver::query()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);
    }

    /**
     * Obtenir les types de sanctions pour les filtres
     */
    public function getSanctionTypesProperty()
    {
        return DriverSanction::SANCTION_TYPES;
    }

    /**
     * Render du composant
     */
    public function render()
    {
        // Vérifier l'autorisation de voir la liste
        $this->authorize('viewAny', DriverSanction::class);

        return view('livewire.admin.driver-sanction-index', [
            'sanctions' => $this->sanctions,
            'drivers' => $this->drivers,
            'sanctionTypes' => $this->sanctionTypes,
        ])->layout('layouts.admin.catalyst-enterprise', ['title' => 'Sanctions Chauffeurs']);
    }
}
