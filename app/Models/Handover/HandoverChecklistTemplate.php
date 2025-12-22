<?php

namespace App\Models\Handover;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Organization;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HandoverChecklistTemplate extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'name',
        'vehicle_type_id',
        'template_json',
        'is_default',
    ];

    protected $casts = [
        'template_json' => 'array',
        'is_default' => 'boolean',
    ];

    /**
     * Relation: A template belongs to an organization.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Relation: A template belongs to a vehicle type (nullable).
     */
    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }
}
