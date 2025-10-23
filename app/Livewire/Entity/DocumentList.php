<?php

namespace App\Livewire\Entity;

use App\Models\Document;
use App\Services\DocumentManagerService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

/**
 * DocumentList - Display documents attached to an entity
 * 
 * Usage:
 * @livewire('entity.document-list', ['entity' => $vehicle])
 * 
 * Features:
 * - Display documents for any entity (polymorphic)
 * - Download action
 * - Detach action
 * - Add new document (opens upload modal with pre-filled attachment)
 * - Multi-tenant security
 * 
 * @author ZenFleet Development Team
 * @version 1.0 - Enterprise Grade
 */
class DocumentList extends Component
{
    /**
     * The entity to display documents for
     */
    public Model $entity;

    /**
     * Show actions (download, detach)
     */
    public bool $showActions = true;

    /**
     * Show add button
     */
    public bool $showAddButton = true;

    /**
     * Listeners
     */
    protected $listeners = [
        'document-uploaded' => '$refresh',
        'document-detached' => '$refresh',
    ];

    /**
     * Mount component
     */
    public function mount(Model $entity, bool $showActions = true, bool $showAddButton = true)
    {
        $this->entity = $entity;
        $this->showActions = $showActions;
        $this->showAddButton = $showAddButton;

        // Validate multi-tenant security
        if (property_exists($entity, 'organization_id')) {
            if ($entity->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized access to entity.');
            }
        }
    }

    /**
     * Get documents for this entity
     */
    public function getDocumentsProperty()
    {
        return Document::query()
            ->forOrganization(auth()->user()->organization_id)
            ->forEntity($this->entity)
            ->with(['category', 'uploader'])
            ->latest()
            ->get();
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
     * Detach document from this entity
     */
    public function detach(int $documentId)
    {
        try {
            $document = Document::forOrganization(auth()->user()->organization_id)
                ->findOrFail($documentId);

            $service = app(DocumentManagerService::class);
            $service->detachFromEntity($document, $this->entity);

            session()->flash('success', 'Document détaché avec succès.');
            $this->dispatch('document-detached', documentId: $documentId);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du détachement : ' . $e->getMessage());
        }
    }

    /**
     * Open upload modal with pre-filled attachment
     */
    public function openUploadModal()
    {
        $entityType = $this->getEntityTypeName($this->entity);
        
        $this->dispatch('open-upload-modal', 
            attachToType: $entityType,
            attachToId: $this->entity->id
        );
    }

    /**
     * Get entity type name for polymorphic relation
     */
    protected function getEntityTypeName(Model $entity): string
    {
        $classMap = [
            \App\Models\Vehicle::class => 'vehicle',
            \App\Models\Driver::class => 'driver',
            \App\Models\User::class => 'user',
            \App\Models\Supplier::class => 'supplier',
        ];

        $class = get_class($entity);
        
        return $classMap[$class] ?? strtolower(class_basename($entity));
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.entity.document-list', [
            'documents' => $this->documents,
        ]);
    }
}
