<?php

namespace App\Livewire\Admin;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Services\DocumentManagerService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;
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
    use WithFileUploads; // Added for file upload
    use AuthorizesRequests;

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
    public int $perPage = 25;

    /**
     * Upload Modal State & Properties
     */
    public bool $showUploadModal = false;
    public $newFile; // Renamed from $file to avoid conflicts
    public ?int $uploadCategoryId = null;
    public ?string $uploadDescription = null;
    public ?string $uploadIssueDate = null;
    public ?string $uploadExpiryDate = null;
    public string $uploadStatus = 'validated';
    public array $uploadMetadata = [];

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
     * Listeners
     */
    protected $listeners = [
        'refresh-documents' => '$refresh',
    ];

    public function mount(): void
    {
        $this->authorize('viewAny', Document::class);
    }

    /**
     * Reset pagination when filters change
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'categoryFilter', 'statusFilter'])) {
            $this->resetPage();
        }

        // Reset metadata when category changes in upload modal
        if ($propertyName === 'uploadCategoryId') {
            $this->uploadMetadata = [];
            $this->resetValidation();
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
     * Get selected category for upload (helper)
     */
    public function getSelectedUploadCategoryProperty()
    {
        return $this->uploadCategoryId
            ? DocumentCategory::find($this->uploadCategoryId)
            : null;
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
     * Open Upload Modal
     */
    public function openUploadModal()
    {
        $this->authorize('create', Document::class);
        $this->resetUploadForm();
        $this->showUploadModal = true;
    }

    /**
     * Close Upload Modal
     */
    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->resetUploadForm();
        $this->resetValidation();
    }

    /**
     * Reset Upload Form
     */
    protected function resetUploadForm()
    {
        $this->reset([
            'newFile',
            'uploadCategoryId',
            'uploadDescription',
            'uploadIssueDate',
            'uploadExpiryDate',
            'uploadStatus',
            'uploadMetadata',
        ]);
        $this->uploadStatus = 'validated';
    }

    /**
     * Upload Document Logic
     */
    public function upload()
    {
        $this->authorize('create', Document::class);
        // 1. Static Validation
        $rules = [
            'newFile' => ['required', 'file', 'max:10240'], // 10MB max
            'uploadCategoryId' => ['required', 'exists:document_categories,id'],
            'uploadDescription' => ['nullable', 'string', 'max:500'],
            'uploadIssueDate' => ['nullable', 'date'],
            'uploadExpiryDate' => ['nullable', 'date', 'after:uploadIssueDate'],
            'uploadStatus' => ['required', 'in:draft,validated'],
        ];

        // 2. Dynamic Metadata Validation
        if ($this->uploadCategoryId) {
            $category = DocumentCategory::find($this->uploadCategoryId);
            if ($category && $category->meta_schema) {
                foreach ($category->meta_schema as $field) {
                    $key = $field['key'] ?? null;
                    $required = $field['required'] ?? false;
                    $type = $field['type'] ?? 'string';

                    if (!$key) continue;

                    $fieldRules = [];
                    $fieldRules[] = $required ? 'required' : 'nullable';

                    switch ($type) {
                        case 'date':
                            $fieldRules[] = 'date';
                            break;
                        case 'number':
                            $fieldRules[] = 'numeric';
                            break;
                        case 'boolean':
                            $fieldRules[] = 'boolean';
                            break;
                        default:
                            $fieldRules[] = 'string';
                            $fieldRules[] = 'max:255';
                            break;
                    }
                    $rules["uploadMetadata.{$key}"] = $fieldRules;
                }
            }
        }

        $this->validate($rules, [
            'newFile.required' => 'Veuillez sélectionner un fichier.',
            'newFile.max' => 'Le fichier ne doit pas dépasser 10 MB.',
            'uploadCategoryId.required' => 'Veuillez sélectionner une catégorie.',
        ]);

        try {
            $category = DocumentCategory::findOrFail($this->uploadCategoryId);
            $service = app(DocumentManagerService::class);

            $options = [
                'description' => $this->uploadDescription,
                'issue_date' => $this->uploadIssueDate,
                'expiry_date' => $this->uploadExpiryDate,
                'status' => $this->uploadStatus,
            ];

            // Perform Upload
            $document = $service->upload(
                $this->newFile,
                $category,
                $this->uploadMetadata,
                null, // No attachment logic in this context yet
                $options
            );

            session()->flash('success', "Document '{$document->original_filename}' ajouté avec succès !");
            $this->closeUploadModal();
            $this->resetPage(); // Go to first page to see new doc

        } catch (\Exception $e) {
            \Log::error('Upload Error: ' . $e->getMessage());
            session()->flash('error', "Erreur lors de l'upload : " . $e->getMessage());
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

            if (!auth()->user()?->can('documents.download')) {
                abort(403, 'Permission refusée pour télécharger des documents.');
            }
            $this->authorize('view', $document);

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

            $this->authorize('update', $document);

            $service = app(DocumentManagerService::class);
            $service->archive($document);

            session()->flash('success', 'Document archivé avec succès.');
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

            $this->authorize('delete', $document);

            $service = app(DocumentManagerService::class);
            $service->delete($document);

            session()->flash('success', 'Document supprimé définitivement.');
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
        // Calculate Stats
        $stats = [
            'total' => Document::forOrganization(auth()->user()->organization_id)->count(),
            'validated' => Document::forOrganization(auth()->user()->organization_id)->where('status', 'validated')->count(),
            'draft' => Document::forOrganization(auth()->user()->organization_id)->where('status', 'draft')->count(),
            'archived' => Document::forOrganization(auth()->user()->organization_id)->where('status', 'archived')->count(),
        ];

        return view('livewire.admin.document-manager-index', [
            'documents' => $this->documents,
            'categories' => $this->categories,
            'stats' => $stats,
        ])->layout('layouts.admin.catalyst');
    }
}
