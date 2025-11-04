<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DepotAssignmentHistory Model
 *
 * Tracks all vehicle-to-depot assignment changes for complete audit trail.
 * Provides immutable history of assignments, unassignments, and transfers.
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int|null $depot_id
 * @property int $organization_id
 * @property int|null $previous_depot_id
 * @property string $action (assigned, unassigned, transferred)
 * @property int|null $assigned_by
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $assigned_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class DepotAssignmentHistory extends Model
{
    use HasFactory;

    protected $table = 'depot_assignment_history';

    protected $fillable = [
        'vehicle_id',
        'depot_id',
        'organization_id',
        'previous_depot_id',
        'action',
        'assigned_by',
        'notes',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Action types constants
     */
    public const ACTION_ASSIGNED = 'assigned';
    public const ACTION_UNASSIGNED = 'unassigned';
    public const ACTION_TRANSFERRED = 'transferred';

    /**
     * Get the vehicle that was assigned.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the depot assigned to.
     */
    public function depot(): BelongsTo
    {
        return $this->belongsTo(VehicleDepot::class, 'depot_id');
    }

    /**
     * Get the previous depot (for transfers).
     */
    public function previousDepot(): BelongsTo
    {
        return $this->belongsTo(VehicleDepot::class, 'previous_depot_id');
    }

    /**
     * Get the user who performed the action.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the organization.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope for a specific vehicle.
     */
    public function scopeForVehicle($query, int $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    /**
     * Scope for a specific depot.
     */
    public function scopeForDepot($query, int $depotId)
    {
        return $query->where('depot_id', $depotId);
    }

    /**
     * Scope for a specific organization.
     */
    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope by action type.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope ordered by date (newest first).
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('assigned_at', 'desc');
    }

    /**
     * Check if this is an assignment action.
     */
    public function isAssignment(): bool
    {
        return $this->action === self::ACTION_ASSIGNED;
    }

    /**
     * Check if this is an unassignment action.
     */
    public function isUnassignment(): bool
    {
        return $this->action === self::ACTION_UNASSIGNED;
    }

    /**
     * Check if this is a transfer action.
     */
    public function isTransfer(): bool
    {
        return $this->action === self::ACTION_TRANSFERRED;
    }

    /**
     * Get formatted action label.
     */
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            self::ACTION_ASSIGNED => 'Affecté au dépôt',
            self::ACTION_UNASSIGNED => 'Retiré du dépôt',
            self::ACTION_TRANSFERRED => 'Transféré',
            default => ucfirst($this->action),
        };
    }

    /**
     * Get action color class for UI.
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            self::ACTION_ASSIGNED => 'green',
            self::ACTION_UNASSIGNED => 'red',
            self::ACTION_TRANSFERRED => 'blue',
            default => 'gray',
        };
    }
}
