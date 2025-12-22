<?php

namespace App\Models\Handover;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Assignment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Facades\Storage;

class VehicleHandoverForm extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, BelongsToOrganization, InteractsWithMedia;

    protected $fillable = [
        'assignment_id',
        'issue_date',
        'current_mileage',
        'general_observations',
        'additional_observations',
        'signed_form_path',
        'organization_id',
    ];

    protected $casts = ['issue_date' => 'date'];

    /**
     * Relation: Une fiche de remise appartient à une affectation.
     * CORRECTION: Ajout du bon type de retour (BelongsTo)
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Relation: Une fiche de remise a plusieurs lignes de détail.
     * CORRECTION: Ajout du bon type de retour (HasMany)
     */
    public function details(): HasMany
    {
        return $this->hasMany(VehicleHandoverDetail::class, 'handover_form_id');
    }

    /**
     * Mark this handover form as the latest version and mark others as obsolete.
     */
    public function markAsLatestVersion()
    {
        // Mark all other handover forms for this assignment as not latest
        static::where('assignment_id', $this->assignment_id)
            ->where('id', '!=', $this->id)
            ->update(['is_latest_version' => false]);

        // Mark this one as latest
        $this->update(['is_latest_version' => true]);
    }

    /**
     * Register media collections for this model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('signed_form')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg']);
    }

    /**
     * Get the URL of the signed form.
     * Provides backward compatibility with old signed_form_path.
     *
     * @return string|null
     */
    public function getSignedFormUrl(): ?string
    {
        // First, try to get from media library
        $media = $this->getFirstMedia('signed_form');

        if ($media) {
            return $media->getUrl();
        }

        // Fallback to old signed_form_path if no media exists
        if ($this->signed_form_path) {
            return Storage::disk('public')->url($this->signed_form_path);
        }

        return null;
    }
}
