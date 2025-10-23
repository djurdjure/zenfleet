<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Documentable - Polymorphic Pivot Model
 * 
 * This model represents the many-to-many polymorphic relationship
 * between documents and any entity in the system (vehicles, drivers, etc.).
 * 
 * @property int $document_id
 * @property int $documentable_id
 * @property string $documentable_type
 */
class Documentable extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The table associated with the model.
     */
    protected $table = 'documentables';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * Get the document.
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the owning documentable model (Vehicle, Driver, etc.).
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
