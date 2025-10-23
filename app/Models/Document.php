<?php

// app/Models/Document.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

    // Document statuses
    const STATUS_DRAFT = 'draft';
    const STATUS_VALIDATED = 'validated';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'uuid',
        'organization_id',
        'document_category_id',
        'user_id',
        'file_path',
        'original_filename',
        'mime_type',
        'size_in_bytes',
        'issue_date',
        'expiry_date',
        'description',
        'extra_metadata',
        'status',
        'is_latest_version',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'extra_metadata' => 'array',
        'size_in_bytes' => 'integer',
        'is_latest_version' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        // Automatically generate a UUID when creating a new document
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the organization that owns the document.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the category of the document.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }

    /**
     * Get the user who uploaded the document.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the vehicles that are assigned this document.
     */
    public function vehicles(): MorphToMany
    {
        return $this->morphedByMany(Vehicle::class, 'documentable');
    }

    /**
     * Get all of the users (drivers, etc.) that are assigned this document.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'documentable');
    }

    /**
     * Get all of the suppliers that are assigned this document.
     */
    public function suppliers(): MorphToMany
    {
        return $this->morphedByMany(Supplier::class, 'documentable');
    }

    /**
     * Get all revisions of this document.
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(DocumentRevision::class)->orderBy('revision_number', 'desc');
    }

    /**
     * Get all documentables (polymorphic relation).
     */
    public function documentables(): HasMany
    {
        return $this->hasMany(Documentable::class);
    }
    
    /**
     * Get the human-readable file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size_in_bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        if ($bytes == 0) {
            return '0 ' . $units[0];
        }
        $i = floor(log($bytes, 1024));
        return round($bytes / (1024 ** $i), 2) . ' ' . $units[$i];
    }

    /**
     * Check if document is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if document is expiring soon (within 30 days).
     */
    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        
        return $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(now()) <= 30;
    }

    /**
     * Scope: Filter by organization (multi-tenant).
     */
    public function scopeForOrganization(Builder $query, int $organizationId): Builder
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope: Filter by category.
     */
    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('document_category_id', $categoryId);
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get only latest versions.
     */
    public function scopeLatestVersions(Builder $query): Builder
    {
        return $query->where('is_latest_version', true);
    }

    /**
     * Scope: Get expired documents.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expiry_date')
                     ->whereDate('expiry_date', '<', now());
    }

    /**
     * Scope: Get documents expiring soon (within 30 days).
     */
    public function scopeExpiringSoon(Builder $query): Builder
    {
        return $query->whereNotNull('expiry_date')
                     ->whereDate('expiry_date', '>=', now())
                     ->whereDate('expiry_date', '<=', now()->addDays(30));
    }

    /**
     * Scope: Full-text search using PostgreSQL tsvector (enterprise-grade).
     * 
     * @param Builder $query
     * @param string $term Search term
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        // Check if PostgreSQL
        $driver = $query->getConnection()->getDriverName();
        
        if ($driver === 'pgsql') {
            // Use PostgreSQL Full-Text Search with tsvector
            $term = str_replace(' ', ' & ', $term); // Convert spaces to AND operator
            return $query->whereRaw("search_vector @@ plainto_tsquery('french', ?)", [$term]);
        }
        
        // Fallback for non-PostgreSQL (MySQL, SQLite, etc.)
        return $query->where(function ($q) use ($term) {
            $q->where('original_filename', 'ILIKE', "%{$term}%")
              ->orWhere('description', 'ILIKE', "%{$term}%");
        });
    }

    /**
     * Scope: Documents attached to a specific entity (polymorphic).
     */
    public function scopeForEntity(Builder $query, Model $entity): Builder
    {
        return $query->whereHas('documentables', function ($q) use ($entity) {
            $q->where('documentable_type', get_class($entity))
              ->where('documentable_id', $entity->id);
        });
    }
}
