<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

/**
 * Modèle MaintenanceDocument - Gestion des documents de maintenance
 *
 * @property int $id
 * @property int $organization_id
 * @property int $maintenance_operation_id
 * @property string $name
 * @property string $original_name
 * @property string $file_path
 * @property string $file_type
 * @property string $mime_type
 * @property int $file_size
 * @property string $document_type
 * @property string|null $description
 * @property array|null $metadata
 * @property int $uploaded_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class MaintenanceDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'maintenance_operation_id',
        'name',
        'original_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'document_type',
        'description',
        'metadata',
        'uploaded_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer',
    ];

    /**
     * Types de fichiers disponibles
     */
    public const FILE_TYPE_IMAGE = 'image';
    public const FILE_TYPE_PDF = 'pdf';
    public const FILE_TYPE_DOCUMENT = 'document';

    public const FILE_TYPES = [
        self::FILE_TYPE_IMAGE => 'Image',
        self::FILE_TYPE_PDF => 'PDF',
        self::FILE_TYPE_DOCUMENT => 'Document',
    ];

    /**
     * Types de documents disponibles
     */
    public const DOC_TYPE_INVOICE = 'invoice';
    public const DOC_TYPE_REPORT = 'report';
    public const DOC_TYPE_PHOTO_BEFORE = 'photo_before';
    public const DOC_TYPE_PHOTO_AFTER = 'photo_after';
    public const DOC_TYPE_WARRANTY = 'warranty';
    public const DOC_TYPE_OTHER = 'other';

    public const DOCUMENT_TYPES = [
        self::DOC_TYPE_INVOICE => 'Facture',
        self::DOC_TYPE_REPORT => 'Rapport',
        self::DOC_TYPE_PHOTO_BEFORE => 'Photo avant',
        self::DOC_TYPE_PHOTO_AFTER => 'Photo après',
        self::DOC_TYPE_WARRANTY => 'Garantie',
        self::DOC_TYPE_OTHER => 'Autre',
    ];

    /**
     * Boot du modèle pour appliquer les scopes globaux
     */
    protected static function booted(): void
    {
        // Scope global multi-tenant
        static::addGlobalScope('organization', function (Builder $builder) {
            if (auth()->check() && auth()->user()->organization_id) {
                $builder->where('organization_id', auth()->user()->organization_id);
            }
        });

        // Event pour nettoyer les fichiers lors de la suppression
        static::deleting(function ($document) {
            $document->deleteFile();
        });
    }

    /**
     * Relation avec l'organisation (multi-tenant)
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Relation avec l'opération de maintenance
     */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(MaintenanceOperation::class, 'maintenance_operation_id');
    }

    /**
     * Relation avec l'utilisateur qui a uploadé le document
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scope pour filtrer par type de fichier
     */
    public function scopeByFileType(Builder $query, string $fileType): void
    {
        $query->where('file_type', $fileType);
    }

    /**
     * Scope pour filtrer par type de document
     */
    public function scopeByDocumentType(Builder $query, string $documentType): void
    {
        $query->where('document_type', $documentType);
    }

    /**
     * Scope pour filtrer les images
     */
    public function scopeImages(Builder $query): void
    {
        $query->where('file_type', self::FILE_TYPE_IMAGE);
    }

    /**
     * Scope pour filtrer les PDFs
     */
    public function scopePdfs(Builder $query): void
    {
        $query->where('file_type', self::FILE_TYPE_PDF);
    }

    /**
     * Scope pour filtrer les documents
     */
    public function scopeDocuments(Builder $query): void
    {
        $query->where('file_type', self::FILE_TYPE_DOCUMENT);
    }

    /**
     * Scope pour ordonner par type puis par nom
     */
    public function scopeOrderByType(Builder $query): void
    {
        $query->orderBy('document_type')->orderBy('name');
    }

    /**
     * Accessor pour le nom du type de fichier
     */
    protected function fileTypeName(): Attribute
    {
        return Attribute::make(
            get: fn () => self::FILE_TYPES[$this->file_type] ?? $this->file_type
        );
    }

    /**
     * Accessor pour le nom du type de document
     */
    protected function documentTypeName(): Attribute
    {
        return Attribute::make(
            get: fn () => self::DOCUMENT_TYPES[$this->document_type] ?? $this->document_type
        );
    }

    /**
     * Accessor pour la taille formatée
     */
    protected function formattedSize(): Attribute
    {
        return Attribute::make(
            get: function () {
                $bytes = $this->file_size;
                $units = ['B', 'KB', 'MB', 'GB'];
                $factor = floor(log($bytes, 1024));
                return sprintf('%.2f %s', $bytes / pow(1024, $factor), $units[$factor]);
            }
        );
    }

    /**
     * Accessor pour l'URL du fichier
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::url($this->file_path)
        );
    }

    /**
     * Accessor pour vérifier si le fichier est une image
     */
    protected function isImage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->file_type === self::FILE_TYPE_IMAGE
        );
    }

    /**
     * Accessor pour vérifier si le fichier est un PDF
     */
    protected function isPdf(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->file_type === self::FILE_TYPE_PDF
        );
    }

    /**
     * Accessor pour vérifier si le fichier existe
     */
    protected function exists(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::exists($this->file_path)
        );
    }

    /**
     * Méthode pour obtenir le badge du type de document
     */
    public function getDocumentTypeBadge(): string
    {
        $typeConfig = [
            self::DOC_TYPE_INVOICE => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-file-invoice'],
            self::DOC_TYPE_REPORT => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-file-alt'],
            self::DOC_TYPE_PHOTO_BEFORE => ['class' => 'bg-purple-100 text-purple-800', 'icon' => 'fas fa-camera'],
            self::DOC_TYPE_PHOTO_AFTER => ['class' => 'bg-purple-100 text-purple-800', 'icon' => 'fas fa-camera'],
            self::DOC_TYPE_WARRANTY => ['class' => 'bg-orange-100 text-orange-800', 'icon' => 'fas fa-shield-alt'],
            self::DOC_TYPE_OTHER => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fas fa-file'],
        ];

        $config = $typeConfig[$this->document_type] ?? $typeConfig[self::DOC_TYPE_OTHER];
        $name = $this->document_type_name;

        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$config['class']}\">
                    <i class=\"{$config['icon']} mr-1\"></i>{$name}
                </span>";
    }

    /**
     * Méthode pour obtenir l'icône du type de fichier
     */
    public function getFileIcon(): string
    {
        $icons = [
            self::FILE_TYPE_IMAGE => 'fas fa-image',
            self::FILE_TYPE_PDF => 'fas fa-file-pdf',
            self::FILE_TYPE_DOCUMENT => 'fas fa-file-alt',
        ];

        return $icons[$this->file_type] ?? 'fas fa-file';
    }

    /**
     * Méthode pour supprimer le fichier physique
     */
    public function deleteFile(): bool
    {
        if (Storage::exists($this->file_path)) {
            return Storage::delete($this->file_path);
        }

        return true;
    }

    /**
     * Méthode pour générer une URL de téléchargement sécurisée
     */
    public function getDownloadUrl(): string
    {
        return route('admin.maintenance.documents.download', $this->id);
    }

    /**
     * Méthode pour obtenir les métadonnées formatées
     */
    public function getFormattedMetadata(): array
    {
        $metadata = $this->metadata ?? [];

        if ($this->is_image && isset($metadata['width'], $metadata['height'])) {
            $metadata['dimensions'] = "{$metadata['width']} x {$metadata['height']} px";
        }

        return $metadata;
    }

    /**
     * Méthode pour vérifier les permissions de téléchargement
     */
    public function canBeDownloadedBy(User $user): bool
    {
        // Vérifier l'organisation
        if ($user->organization_id !== $this->organization_id) {
            return false;
        }

        // Vérifier les permissions spécifiques
        return $user->can('maintenance.operations.view');
    }

    /**
     * Méthode statique pour déterminer le type de fichier basé sur le MIME type
     */
    public static function determineFileType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return self::FILE_TYPE_IMAGE;
        }

        if ($mimeType === 'application/pdf') {
            return self::FILE_TYPE_PDF;
        }

        return self::FILE_TYPE_DOCUMENT;
    }

    /**
     * Méthode statique pour obtenir les types MIME autorisés
     */
    public static function getAllowedMimeTypes(): array
    {
        return [
            // Images
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            // Documents
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
        ];
    }

    /**
     * Validation rules pour le modèle
     */
    public static function validationRules(): array
    {
        return [
            'maintenance_operation_id' => 'required|exists:maintenance_operations,id',
            'name' => 'required|string|max:255',
            'original_name' => 'required|string|max:255',
            'file_path' => 'required|string|max:500',
            'file_type' => 'required|in:' . implode(',', array_keys(self::FILE_TYPES)),
            'mime_type' => 'required|string|max:100|in:' . implode(',', self::getAllowedMimeTypes()),
            'file_size' => 'required|integer|min:1|max:10485760', // Max 10MB
            'document_type' => 'required|in:' . implode(',', array_keys(self::DOCUMENT_TYPES)),
            'description' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    public static function validationMessages(): array
    {
        return [
            'maintenance_operation_id.required' => 'L\'opération de maintenance est obligatoire.',
            'maintenance_operation_id.exists' => 'L\'opération de maintenance sélectionnée n\'existe pas.',
            'name.required' => 'Le nom du document est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'file_type.required' => 'Le type de fichier est obligatoire.',
            'file_type.in' => 'Le type de fichier n\'est pas autorisé.',
            'mime_type.required' => 'Le type MIME est obligatoire.',
            'mime_type.in' => 'Le type de fichier n\'est pas autorisé.',
            'file_size.required' => 'La taille du fichier est obligatoire.',
            'file_size.max' => 'Le fichier ne peut pas dépasser 10 MB.',
            'document_type.required' => 'Le type de document est obligatoire.',
            'document_type.in' => 'Le type de document sélectionné n\'est pas valide.',
        ];
    }
}
