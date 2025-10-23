<?php

namespace App\Livewire\Admin;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Services\DocumentManagerService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * DocumentManagerIndex - Main Document Management Dashboard
 * 
 * Features:
 * - Full-text search using PostgreSQL tsvector
 * - Filtering by category and status
 * - Pagination
 * - Download, Archive, Delete actions
 * - Multi-tenant security
 * - Enterprise-grade UI
 * 
 * @author ZenFleet Development Team
 * @version 1.0 - Enterprise Grade
 */
class DocumentManagerIndex extends Component
{
    use WithPagination;

    /**
     * Filters and Search
     */
    public string $search = '';
    public ?int $categoryFilter = null;
    public ?string $statusFilter = null;
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    /**
     * Pagination
     */
    public int $perPage = 15;

    /**
     * Querystring bindings
     */
    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => null, 'as' => 'category'],
        'statusFilter' => ['except' => null, 'as' => 'status'],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    /**
     * Reset pagination when filters change
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'categoryFilter', 'statusFilter'])) {
            $this->resetPage();
        }
    }

    /**
     * Get categories for filter dropdown (scoped to organization)
     */
    public function getCategoriesProperty()
    {
        return DocumentCategory::where('organization_id', auth()->user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get filtered and paginated documents
     */
    public function getDocumentsProperty()
    {
        $query = Document::query()
            ->with(['category', 'uploader'])
            ->forOrganization(auth()->user()->organization_id)
            ->latestVersions();

        // Apply search (uses PostgreSQL Full-Text Search)
        if (!empty($this->search)) {
            $query->search($this->search);
        }

        // Apply category filter
        if ($this->categoryFilter) {
            $query->byCategory($this->categoryFilter);
        }

        // Apply status filter
        if ($this->statusFilter) {
            $query->byStatus($this->statusFilter);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    /**
     * Sort by field
     */
    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Download document
     */
    public function download(int $documentId)
    {
        try {
            $document = Document::forOrganization(auth()->user()->organization_id)
                ->findOrFail($documentId);

            $service = app(DocumentManagerService::class);
            return $service->download($document);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du téléchargement : ' . $e->getMessage());
        }
    }

    /**
     * Archive document
     */
    public function archive(int $documentId)
    {
        try {
            $document = Document::forOrganization(auth()->user()->organization_id)
                ->findOrFail($documentId);

            $service = app(DocumentManagerService::class);
            $service->archive($document);

            session()->flash('success', 'Document archivé avec succès.');
            $this->dispatch('document-archived', documentId: $documentId);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'archivage : ' . $e->getMessage());
        }
    }

    /**
     * Delete document
     */
    public function delete(int $documentId)
    {
        try {
            $document = Document::forOrganization(auth()->user()->organization_id)
                ->findOrFail($documentId);

            // Check permission (only admins can delete)
            if (!auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Gestionnaire Flotte'])) {
                session()->flash('error', 'Vous n\'avez pas la permission de supprimer ce document.');
                return;
            }

            $service = app(DocumentManagerService::class);
            $service->delete($document);

            session()->flash('success', 'Document supprimé définitivement.');
            $this->dispatch('document-deleted', documentId: $documentId);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        $this->reset(['search', 'categoryFilter', 'statusFilter']);
        $this->resetPage();
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.admin.document-manager-index', [
            'documents' => $this->documents,
            'categories' => $this->categories,
        ])->layout('layouts.admin.catalyst');
    }
}
