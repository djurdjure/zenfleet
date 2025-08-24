<?php

// app/Models/Document.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'extra_metadata' => 'array',
        'size_in_bytes' => 'integer',
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
}
