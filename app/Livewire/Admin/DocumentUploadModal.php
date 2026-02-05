<?php

namespace App\Livewire\Admin;

use App\Models\DocumentCategory;
use App\Services\DocumentManagerService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Document;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * DocumentUploadModal - Modal for uploading documents
 * 
 * Features:
 * - File upload with validation
 * - Dynamic metadata fields based on category schema
 * - Polymorphic attachment to entities
 * - Multi-tenant security
 * - Real-time validation
 * 
 * @author ZenFleet Development Team
 * @version 1.0 - Enterprise Grade
 */
class DocumentUploadModal extends Component
{
    use WithFileUploads;
    use AuthorizesRequests;

    /**
     * Modal state
     */
    public bool $isOpen = false;

    /**
     * Upload form properties
     */
    public $file;
    public ?int $categoryId = null;
    public ?string $description = null;
    public ?string $issueDate = null;
    public ?string $expiryDate = null;
    public string $status = 'validated';
    
    /**
     * Dynamic metadata (from category schema)
     */
    public array $metadata = [];

    /**
     * Polymorphic attachment (optional)
     */
    public ?string $attachToType = null;
    public ?int $attachToId = null;

    /**
     * Listeners
     */
    protected $listeners = [
        'open-upload-modal' => 'openModal',
    ];

    /**
     * Validation rules
     */
    protected function rules(): array
    {
        $rules = [
            'file' => ['required', 'file', 'max:10240'], // 10MB max
            'categoryId' => ['required', 'exists:document_categories,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'issueDate' => ['nullable', 'date'],
            'expiryDate' => ['nullable', 'date', 'after:issue_date'],
            'status' => ['required', 'in:draft,validated'],
        ];

        // Add dynamic metadata rules based on category schema
        if ($this->categoryId) {
            $category = DocumentCategory::find($this->categoryId);
            
            if ($category && $category->meta_schema) {
                foreach ($category->meta_schema as $field) {
                    $key = $field['key'] ?? null;
                    $required = $field['required'] ?? false;
                    $type = $field['type'] ?? 'string';

                    if (!$key) continue;

                    $fieldRules = [];
                    
                    if ($required) {
                        $fieldRules[] = 'required';
                    } else {
                        $fieldRules[] = 'nullable';
                    }

                    // Type-specific validation
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

                    $rules["metadata.{$key}"] = $fieldRules;
                }
            }
        }

        return $rules;
    }

    /**
     * Custom validation messages
     */
    protected $messages = [
        'file.required' => 'Veuillez sélectionner un fichier.',
        'file.max' => 'Le fichier ne doit pas dépasser 10 MB.',
        'categoryId.required' => 'Veuillez sélectionner une catégorie.',
        'categoryId.exists' => 'La catégorie sélectionnée est invalide.',
        'expiryDate.after' => 'La date d\'expiration doit être après la date d\'émission.',
    ];

    /**
     * Open modal (can be called from other components with attachment params)
     */
    public function openModal(?string $attachToType = null, ?int $attachToId = null)
    {
        $this->authorize('create', Document::class);
        $this->isOpen = true;
        $this->attachToType = $attachToType;
        $this->attachToId = $attachToId;
    }

    /**
     * Close modal and reset form
     */
    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset([
            'file', 
            'categoryId', 
            'description', 
            'issueDate', 
            'expiryDate', 
            'status',
            'metadata',
            'attachToType',
            'attachToId',
        ]);
        $this->resetValidation();
    }

    /**
     * Get categories for dropdown (scoped to organization)
     */
    public function getCategoriesProperty()
    {
        return DocumentCategory::where('organization_id', auth()->user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get selected category (for dynamic form)
     */
    public function getSelectedCategoryProperty(): ?DocumentCategory
    {
        return $this->categoryId 
            ? DocumentCategory::find($this->categoryId) 
            : null;
    }

    /**
     * When category changes, reset metadata
     */
    public function updatedCategoryId($value)
    {
        $this->metadata = [];
        $this->resetValidation();
    }

    /**
     * Upload document
     */
    public function upload()
    {
        $this->authorize('create', Document::class);
        // Validate form
        $this->validate();

        try {
            $category = DocumentCategory::where('id', $this->categoryId)
                ->where('organization_id', auth()->user()->organization_id)
                ->firstOrFail();

            $service = app(DocumentManagerService::class);

            // Prepare options
            $options = [
                'description' => $this->description,
                'issue_date' => $this->issueDate,
                'expiry_date' => $this->expiryDate,
                'status' => $this->status,
            ];

            // Resolve attachment entity (if provided)
            $attachTo = null;
            if ($this->attachToType && $this->attachToId) {
                $attachTo = $this->resolveEntity($this->attachToType, $this->attachToId);
            }

            // Upload document
            $document = $service->upload(
                $this->file,
                $category,
                $this->metadata,
                $attachTo,
                $options
            );

            // Success feedback
            session()->flash('success', "Document '{$document->original_filename}' uploadé avec succès !");
            
            // Dispatch events
            $this->dispatch('document-uploaded', documentId: $document->id);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Document uploadé avec succès !',
            ]);

            // Close modal
            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'upload : ' . $e->getMessage());
        }
    }

    /**
     * Resolve entity from type and ID (for polymorphic attachment)
     */
    protected function resolveEntity(string $type, int $id): ?Model
    {
        // Map types to models
        $modelMap = [
            'vehicle' => \App\Models\Vehicle::class,
            'driver' => \App\Models\Driver::class,
            'user' => \App\Models\User::class,
            'supplier' => \App\Models\Supplier::class,
        ];

        $modelClass = $modelMap[$type] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            return null;
        }

        // Get entity with multi-tenant check
        $query = $modelClass::where('id', $id);
        
        if (property_exists($modelClass, 'organization_id')) {
            $query->where('organization_id', auth()->user()->organization_id);
        }

        return $query->first();
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.admin.document-upload-modal', [
            'categories' => $this->categories,
            'selectedCategory' => $this->selectedCategory,
        ]);
    }
}
