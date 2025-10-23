<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DocumentRevision Model - Enterprise-Grade Version History
 * 
 * Stores historical versions of documents for audit and compliance.
 * Each time a document is updated, a revision is automatically created.
 * 
 * @property int $id
 * @property int $document_id
 * @property int $user_id
 * @property string $file_path
 * @property string $original_filename
 * @property string $mime_type
 * @property int $size_in_bytes
 * @property array|null $extra_metadata
 * @property string|null $description
 * @property \Carbon\Carbon|null $issue_date
 * @property \Carbon\Carbon|null $expiry_date
 * @property int $revision_number
 * @property string|null $revision_notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class DocumentRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'file_path',
        'original_filename',
        'mime_type',
        'size_in_bytes',
        'extra_metadata',
        'description',
        'issue_date',
        'expiry_date',
        'revision_number',
        'revision_notes',
    ];

    protected $casts = [
        'size_in_bytes' => 'integer',
        'extra_metadata' => 'array',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'revision_number' => 'integer',
    ];

    /**
     * Get the document that owns this revision.
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the user who created this revision.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
     * Scope: Get revisions ordered by revision number (newest first)
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('revision_number', 'desc');
    }

    /**
     * Scope: Get revisions for a specific document
     */
    public function scopeForDocument($query, int $documentId)
    {
        return $query->where('document_id', $documentId);
    }
}
