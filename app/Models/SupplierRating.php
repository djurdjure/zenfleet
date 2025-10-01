<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierRating extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'supplier_id',
        'repair_request_id',
        'rated_by',
        'quality_rating',
        'timeliness_rating',
        'communication_rating',
        'pricing_rating',
        'overall_rating',
        'positive_feedback',
        'negative_feedback',
        'suggestions',
        'would_recommend',
        'service_categories_rated'
    ];

    protected $casts = [
        'quality_rating' => 'decimal:2',
        'timeliness_rating' => 'decimal:2',
        'communication_rating' => 'decimal:2',
        'pricing_rating' => 'decimal:2',
        'overall_rating' => 'decimal:2',
        'would_recommend' => 'boolean',
        'service_categories_rated' => 'array'
    ];

    // Relations
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function repairRequest(): BelongsTo
    {
        return $this->belongsTo(RepairRequest::class);
    }

    public function ratedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_by');
    }

    // Méthodes utilitaires
    public function getAverageRating(): float
    {
        return ($this->quality_rating + $this->timeliness_rating +
                $this->communication_rating + $this->pricing_rating) / 4;
    }

    public function hasPositiveFeedback(): bool
    {
        return !empty($this->positive_feedback);
    }

    public function hasNegativeFeedback(): bool
    {
        return !empty($this->negative_feedback);
    }

    public function hasSuggestions(): bool
    {
        return !empty($this->suggestions);
    }
}