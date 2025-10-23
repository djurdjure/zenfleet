<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentRevision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * DocumentManagerService - Enterprise-Grade Document Management
 * 
 * Centralizes all document business logic including:
 * - Upload and storage
 * - Metadata management
 * - Version control (revisions)
 * - Polymorphic attachments
 * - Multi-tenant security
 * 
 * @author ZenFleet Development Team
 * @version 1.0 - Enterprise Grade
 */
class DocumentManagerService
{
    /**
     * Upload a new document and attach it to an entity.
     * 
     * @param UploadedFile $file The uploaded file
     * @param DocumentCategory $category Document category
     * @param array $metadata Additional metadata (extra_metadata)
     * @param Model|null $attachTo Entity to attach document to (optional)
     * @param array $options Additional options (issue_date, expiry_date, description, status)
     * @return Document
     * @throws \Exception
     */
    public function upload(
        UploadedFile $file,
        DocumentCategory $category,
        array $metadata = [],
        ?Model $attachTo = null,
        array $options = []
    ): Document {
        DB::beginTransaction();
        
        try {
            // Validate multi-tenant security
            $organizationId = auth()->user()->organization_id;
            
            if ($category->organization_id !== $organizationId) {
                throw new \Exception('Category does not belong to your organization.');
            }

            // Store file using Laravel Storage
            $disk = config('filesystems.default', 'public');
            $path = $file->store("documents/{$organizationId}", $disk);
            
            if (!$path) {
                throw new \Exception('Failed to store file.');
            }

            // Validate metadata against category schema
            $validatedMetadata = $this->validateMetadata($metadata, $category->meta_schema ?? []);

            // Create document
            $document = Document::create([
                'uuid' => (string) Str::uuid(),
                'organization_id' => $organizationId,
                'document_category_id' => $category->id,
                'user_id' => auth()->id(),
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size_in_bytes' => $file->getSize(),
                'issue_date' => $options['issue_date'] ?? null,
                'expiry_date' => $options['expiry_date'] ?? null,
                'description' => $options['description'] ?? null,
                'extra_metadata' => $validatedMetadata,
                'status' => $options['status'] ?? Document::STATUS_VALIDATED,
                'is_latest_version' => true,
            ]);

            // Attach to entity if provided
            if ($attachTo) {
                $this->attachToEntity($document, $attachTo);
            }

            DB::commit();
            
            return $document->fresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded file if exists
            if (isset($path) && Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
            
            throw $e;
        }
    }

    /**
     * Update document metadata (creates a revision if file is replaced).
     * 
     * @param Document $document
     * @param array $newMetadata New metadata to merge/replace
     * @param array $options Additional options (issue_date, expiry_date, description, status)
     * @param UploadedFile|null $newFile Optional new file (creates revision)
     * @return Document
     * @throws \Exception
     */
    public function updateMetadata(
        Document $document,
        array $newMetadata,
        array $options = [],
        ?UploadedFile $newFile = null
    ): Document {
        // Validate multi-tenant security
        if ($document->organization_id !== auth()->user()->organization_id) {
            throw new \Exception('Unauthorized access to document.');
        }

        DB::beginTransaction();
        
        try {
            // If a new file is provided, create a revision
            if ($newFile) {
                $this->createRevision($document, 'File updated with new version');
                
                // Delete old file
                $disk = config('filesystems.default', 'public');
                if (Storage::disk($disk)->exists($document->file_path)) {
                    Storage::disk($disk)->delete($document->file_path);
                }
                
                // Store new file
                $path = $newFile->store("documents/{$document->organization_id}", $disk);
                
                $document->file_path = $path;
                $document->original_filename = $newFile->getClientOriginalName();
                $document->mime_type = $newFile->getMimeType();
                $document->size_in_bytes = $newFile->getSize();
            }

            // Validate and merge metadata
            $validatedMetadata = $this->validateMetadata(
                $newMetadata, 
                $document->category->meta_schema ?? []
            );
            
            $document->extra_metadata = array_merge(
                $document->extra_metadata ?? [],
                $validatedMetadata
            );

            // Update optional fields
            if (isset($options['issue_date'])) {
                $document->issue_date = $options['issue_date'];
            }
            
            if (isset($options['expiry_date'])) {
                $document->expiry_date = $options['expiry_date'];
            }
            
            if (isset($options['description'])) {
                $document->description = $options['description'];
            }
            
            if (isset($options['status'])) {
                $document->status = $options['status'];
            }

            $document->save();

            DB::commit();
            
            return $document->fresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Archive a document (soft delete alternative).
     * 
     * @param Document $document
     * @return Document
     * @throws \Exception
     */
    public function archive(Document $document): Document
    {
        // Validate multi-tenant security
        if ($document->organization_id !== auth()->user()->organization_id) {
            throw new \Exception('Unauthorized access to document.');
        }

        $document->status = Document::STATUS_ARCHIVED;
        $document->save();

        return $document;
    }

    /**
     * Restore an archived document.
     * 
     * @param Document $document
     * @return Document
     * @throws \Exception
     */
    public function restore(Document $document): Document
    {
        // Validate multi-tenant security
        if ($document->organization_id !== auth()->user()->organization_id) {
            throw new \Exception('Unauthorized access to document.');
        }

        $document->status = Document::STATUS_VALIDATED;
        $document->save();

        return $document;
    }

    /**
     * Delete a document permanently (with file).
     * 
     * @param Document $document
     * @return bool
     * @throws \Exception
     */
    public function delete(Document $document): bool
    {
        // Validate multi-tenant security
        if ($document->organization_id !== auth()->user()->organization_id) {
            throw new \Exception('Unauthorized access to document.');
        }

        DB::beginTransaction();
        
        try {
            // Delete file from storage
            $disk = config('filesystems.default', 'public');
            if (Storage::disk($disk)->exists($document->file_path)) {
                Storage::disk($disk)->delete($document->file_path);
            }

            // Delete all revision files
            foreach ($document->revisions as $revision) {
                if (Storage::disk($disk)->exists($revision->file_path)) {
                    Storage::disk($disk)->delete($revision->file_path);
                }
            }

            // Delete document (cascades to revisions and documentables)
            $document->delete();

            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Attach document to an entity (polymorphic).
     * 
     * @param Document $document
     * @param Model $entity
     * @return void
     * @throws \Exception
     */
    public function attachToEntity(Document $document, Model $entity): void
    {
        // Validate multi-tenant security (entity should belong to same org)
        if (property_exists($entity, 'organization_id') && 
            $entity->organization_id !== $document->organization_id) {
            throw new \Exception('Cannot attach document to entity from different organization.');
        }

        // Check if already attached
        $exists = DB::table('documentables')
            ->where('document_id', $document->id)
            ->where('documentable_type', get_class($entity))
            ->where('documentable_id', $entity->id)
            ->exists();

        if (!$exists) {
            DB::table('documentables')->insert([
                'document_id' => $document->id,
                'documentable_type' => get_class($entity),
                'documentable_id' => $entity->id,
            ]);
        }
    }

    /**
     * Detach document from an entity.
     * 
     * @param Document $document
     * @param Model $entity
     * @return void
     */
    public function detachFromEntity(Document $document, Model $entity): void
    {
        DB::table('documentables')
            ->where('document_id', $document->id)
            ->where('documentable_type', get_class($entity))
            ->where('documentable_id', $entity->id)
            ->delete();
    }

    /**
     * Create a revision of the document.
     * 
     * @param Document $document
     * @param string|null $notes Revision notes
     * @return DocumentRevision
     */
    protected function createRevision(Document $document, ?string $notes = null): DocumentRevision
    {
        // Get next revision number
        $lastRevision = $document->revisions()->orderBy('revision_number', 'desc')->first();
        $revisionNumber = $lastRevision ? $lastRevision->revision_number + 1 : 1;

        // Create revision with current document data
        return DocumentRevision::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'file_path' => $document->file_path,
            'original_filename' => $document->original_filename,
            'mime_type' => $document->mime_type,
            'size_in_bytes' => $document->size_in_bytes,
            'extra_metadata' => $document->extra_metadata,
            'description' => $document->description,
            'issue_date' => $document->issue_date,
            'expiry_date' => $document->expiry_date,
            'revision_number' => $revisionNumber,
            'revision_notes' => $notes,
        ]);
    }

    /**
     * Validate metadata against category schema.
     * 
     * @param array $metadata
     * @param array $schema
     * @return array Validated metadata
     * @throws \Exception
     */
    protected function validateMetadata(array $metadata, array $schema): array
    {
        if (empty($schema)) {
            return $metadata;
        }

        $validated = [];

        foreach ($schema as $field) {
            $key = $field['key'] ?? null;
            $required = $field['required'] ?? false;
            $type = $field['type'] ?? 'string';

            if (!$key) {
                continue;
            }

            // Check if required field is present
            if ($required && !isset($metadata[$key])) {
                throw new \Exception("Required field '{$key}' is missing.");
            }

            // Validate type if value exists
            if (isset($metadata[$key])) {
                $value = $metadata[$key];
                
                // Basic type validation
                switch ($type) {
                    case 'date':
                        if (!strtotime($value)) {
                            throw new \Exception("Field '{$key}' must be a valid date.");
                        }
                        break;
                    case 'number':
                        if (!is_numeric($value)) {
                            throw new \Exception("Field '{$key}' must be a number.");
                        }
                        $value = (float) $value;
                        break;
                    case 'boolean':
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                        break;
                }

                $validated[$key] = $value;
            }
        }

        // Include any additional metadata not in schema
        foreach ($metadata as $key => $value) {
            if (!isset($validated[$key])) {
                $validated[$key] = $value;
            }
        }

        return $validated;
    }

    /**
     * Download a document (returns storage path or stream).
     * 
     * @param Document $document
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Exception
     */
    public function download(Document $document)
    {
        // Validate multi-tenant security
        if ($document->organization_id !== auth()->user()->organization_id) {
            throw new \Exception('Unauthorized access to document.');
        }

        $disk = config('filesystems.default', 'public');
        
        if (!Storage::disk($disk)->exists($document->file_path)) {
            throw new \Exception('File not found.');
        }

        return Storage::disk($disk)->download(
            $document->file_path,
            $document->original_filename
        );
    }
}
