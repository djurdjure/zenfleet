<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * RepairRequest Model - Main model for repair workflow
 *
 * @property int $id
 * @property string $uuid
 * @property int $organization_id
 * @property int $vehicle_id
 * @property int $driver_id
 * @property string $status
 * @property string $title
 * @property string $description
 * @property string $urgency
 * @property float|null $estimated_cost
 * @property int|null $current_mileage
 * @property string|null $current_location
 * @property int|null $supervisor_id
 * @property string|null $supervisor_status
 * @property string|null $supervisor_comment
 * @property \Illuminate\Support\Carbon|null $supervisor_approved_at
 * @property int|null $fleet_manager_id
 * @property string|null $fleet_manager_status
 * @property string|null $fleet_manager_comment
 * @property \Illuminate\Support\Carbon|null $fleet_manager_approved_at
 * @property string|null $rejection_reason
 * @property int|null $rejected_by
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property int|null $final_approved_by
 * @property \Illuminate\Support\Carbon|null $final_approved_at
 * @property int|null $maintenance_operation_id
 * @property array|null $photos
 * @property array|null $attachments
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Organization $organization
 * @property-read Vehicle $vehicle
 * @property-read Driver $driver
 * @property-read User|null $supervisor
 * @property-read User|null $fleetManager
 * @property-read User|null $rejectedBy
 * @property-read User|null $finalApprovedBy
 * @property-read MaintenanceOperation|null $maintenanceOperation
 * @property-read \Illuminate\Database\Eloquent\Collection<RepairRequestHistory> $history
 * @property-read \Illuminate\Database\Eloquent\Collection<RepairNotification> $notifications
 */
class RepairRequest extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'repair_requests';

    /**
     * Status constants
     */
    public const STATUS_PENDING_SUPERVISOR = 'pending_supervisor';
    public const STATUS_APPROVED_SUPERVISOR = 'approved_supervisor';
    public const STATUS_REJECTED_SUPERVISOR = 'rejected_supervisor';
    public const STATUS_PENDING_FLEET_MANAGER = 'pending_fleet_manager';
    public const STATUS_APPROVED_FINAL = 'approved_final';
    public const STATUS_REJECTED_FINAL = 'rejected_final';

    /**
     * Urgency levels
     */
    public const URGENCY_LOW = 'low';
    public const URGENCY_NORMAL = 'normal';
    public const URGENCY_HIGH = 'high';
    public const URGENCY_CRITICAL = 'critical';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'organization_id',
        'vehicle_id',
        'driver_id',
        'requested_by',
        'category_id',
        'status',
        'title',
        'description',
        'urgency',
        'estimated_cost',
        'current_mileage',
        'current_location',
        'supervisor_id',
        'supervisor_status',
        'supervisor_comment',
        'supervisor_approved_at',
        'fleet_manager_id',
        'fleet_manager_status',
        'fleet_manager_comment',
        'fleet_manager_approved_at',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
        'final_approved_by',
        'final_approved_at',
        'maintenance_operation_id',
        'photos',
        'attachments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'organization_id' => 'integer',
        'vehicle_id' => 'integer',
        'driver_id' => 'integer',
        'category_id' => 'integer',
        'supervisor_id' => 'integer',
        'fleet_manager_id' => 'integer',
        'rejected_by' => 'integer',
        'final_approved_by' => 'integer',
        'maintenance_operation_id' => 'integer',
        'estimated_cost' => 'decimal:2',
        'current_mileage' => 'integer',
        'photos' => 'array',
        'attachments' => 'array',
        'supervisor_approved_at' => 'datetime',
        'fleet_manager_approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'final_approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (RepairRequest $request) {
            if (empty($request->uuid)) {
                $request->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the organization that owns the repair request.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the vehicle for this repair request.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the driver who created this repair request.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the category of this repair request.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(RepairCategory::class, 'category_id');
    }

    /**
     * Get the supervisor who reviewed this request.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get the fleet manager who reviewed this request.
     */
    public function fleetManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fleet_manager_id');
    }

    /**
     * Get the user who rejected this request.
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the user who gave final approval.
     */
    public function finalApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'final_approved_by');
    }

    /**
     * Get the associated maintenance operation.
     */
    public function maintenanceOperation(): BelongsTo
    {
        return $this->belongsTo(MaintenanceOperation::class);
    }

    /**
     * Get all history entries for this request.
     */
    public function history(): HasMany
    {
        return $this->hasMany(RepairRequestHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get all notifications for this request.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(RepairNotification::class);
    }

    /**
     * Get the user who created this request (driver's user account).
     * Alias for better readability in views.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get the assigned supplier for this repair.
     * Note: Currently there's no supplier assignment in the schema.
     * This will return null until supplier assignment feature is implemented.
     */
    public function assignedSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Scope: Pending supervisor approval
     */
    public function scopePendingSupervisor(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING_SUPERVISOR);
    }

    /**
     * Scope: Pending fleet manager approval
     */
    public function scopePendingFleetManager(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING_FLEET_MANAGER);
    }

    /**
     * Scope: Approved requests
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED_FINAL);
    }

    /**
     * Scope: Rejected requests
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_REJECTED_SUPERVISOR,
            self::STATUS_REJECTED_FINAL,
        ]);
    }

    /**
     * Scope: By urgency level
     */
    public function scopeByUrgency(Builder $query, string $urgency): Builder
    {
        return $query->where('urgency', $urgency);
    }

    /**
     * Scope: Critical urgency
     */
    public function scopeCritical(Builder $query): Builder
    {
        return $query->where('urgency', self::URGENCY_CRITICAL);
    }

    /**
     * Scope: For specific organization
     */
    public function scopeForOrganization(Builder $query, int $organizationId): Builder
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope: For specific vehicle
     */
    public function scopeForVehicle(Builder $query, int $vehicleId): Builder
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    /**
     * Scope: For specific driver
     */
    public function scopeForDriver(Builder $query, int $driverId): Builder
    {
        return $query->where('driver_id', $driverId);
    }

    /**
     * Scope: For specific supervisor
     */
    public function scopeForSupervisor(Builder $query, int $supervisorId): Builder
    {
        return $query->where('supervisor_id', $supervisorId);
    }

    /**
     * Scope: All pending requests (supervisor + fleet manager)
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING_SUPERVISOR,
            self::STATUS_PENDING_FLEET_MANAGER,
        ]);
    }

    /**
     * Scope: Urgent requests (high + critical)
     */
    public function scopeUrgent(Builder $query): Builder
    {
        return $query->whereIn('urgency', [
            self::URGENCY_HIGH,
            self::URGENCY_CRITICAL,
        ]);
    }

    /**
     * Scope: In progress requests (approved and linked to maintenance)
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED_FINAL)
                     ->whereNotNull('maintenance_operation_id');
    }

    /**
     * Scope: Completed requests (approved with completed maintenance)
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED_FINAL)
                     ->whereHas('maintenanceOperation', function ($q) {
                         $q->where('status', 'completed');
                     });
    }

    /**
     * Check if request is pending
     */
    public function isPending(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING_SUPERVISOR,
            self::STATUS_PENDING_FLEET_MANAGER,
        ]);
    }

    /**
     * Check if request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED_FINAL;
    }

    /**
     * Check if request is rejected
     */
    public function isRejected(): bool
    {
        return in_array($this->status, [
            self::STATUS_REJECTED_SUPERVISOR,
            self::STATUS_REJECTED_FINAL,
        ]);
    }

    /**
     * Approve by supervisor
     */
    public function approveBySupervisor(User $supervisor, ?string $comment = null): bool
    {
        $this->supervisor_id = $supervisor->id;
        $this->supervisor_status = 'approved';
        $this->supervisor_comment = $comment;
        $this->supervisor_approved_at = now();
        $this->status = self::STATUS_PENDING_FLEET_MANAGER;

        if ($this->save()) {
            $this->logHistory('supervisor_approved', self::STATUS_PENDING_SUPERVISOR, self::STATUS_PENDING_FLEET_MANAGER, $supervisor, $comment);
            return true;
        }

        return false;
    }

    /**
     * Reject by supervisor
     */
    public function rejectBySupervisor(User $supervisor, string $reason): bool
    {
        $this->supervisor_id = $supervisor->id;
        $this->supervisor_status = 'rejected';
        $this->supervisor_comment = $reason;
        $this->status = self::STATUS_REJECTED_SUPERVISOR;
        $this->rejection_reason = $reason;
        $this->rejected_by = $supervisor->id;
        $this->rejected_at = now();

        if ($this->save()) {
            $this->logHistory('supervisor_rejected', self::STATUS_PENDING_SUPERVISOR, self::STATUS_REJECTED_SUPERVISOR, $supervisor, $reason);
            return true;
        }

        return false;
    }

    /**
     * Approve by fleet manager (final approval)
     */
    public function approveByFleetManager(User $fleetManager, ?string $comment = null): bool
    {
        $this->fleet_manager_id = $fleetManager->id;
        $this->fleet_manager_status = 'approved';
        $this->fleet_manager_comment = $comment;
        $this->fleet_manager_approved_at = now();
        $this->status = self::STATUS_APPROVED_FINAL;
        $this->final_approved_by = $fleetManager->id;
        $this->final_approved_at = now();

        if ($this->save()) {
            $this->logHistory('fleet_manager_approved', self::STATUS_PENDING_FLEET_MANAGER, self::STATUS_APPROVED_FINAL, $fleetManager, $comment);
            return true;
        }

        return false;
    }

    /**
     * Reject by fleet manager
     */
    public function rejectByFleetManager(User $fleetManager, string $reason): bool
    {
        $this->fleet_manager_id = $fleetManager->id;
        $this->fleet_manager_status = 'rejected';
        $this->fleet_manager_comment = $reason;
        $this->status = self::STATUS_REJECTED_FINAL;
        $this->rejection_reason = $reason;
        $this->rejected_by = $fleetManager->id;
        $this->rejected_at = now();

        if ($this->save()) {
            $this->logHistory('fleet_manager_rejected', self::STATUS_PENDING_FLEET_MANAGER, self::STATUS_REJECTED_FINAL, $fleetManager, $reason);
            return true;
        }

        return false;
    }

    /**
     * Log action to history
     */
    protected function logHistory(string $action, ?string $fromStatus, string $toStatus, ?User $user = null, ?string $comment = null): void
    {
        $this->history()->create([
            'user_id' => $user?->id,
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'comment' => $comment,
        ]);
    }

    /**
     * Get urgency badge color
     */
    public function getUrgencyColorAttribute(): string
    {
        return match ($this->urgency) {
            self::URGENCY_CRITICAL => 'red',
            self::URGENCY_HIGH => 'orange',
            self::URGENCY_NORMAL => 'blue',
            self::URGENCY_LOW => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED_FINAL => 'green',
            self::STATUS_REJECTED_SUPERVISOR, self::STATUS_REJECTED_FINAL => 'red',
            self::STATUS_PENDING_SUPERVISOR, self::STATUS_PENDING_FLEET_MANAGER => 'yellow',
            self::STATUS_APPROVED_SUPERVISOR => 'blue',
            default => 'gray',
        };
    }

    /**
     * Get human-readable status
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_SUPERVISOR => 'En attente superviseur',
            self::STATUS_APPROVED_SUPERVISOR => 'Approuvé par superviseur',
            self::STATUS_REJECTED_SUPERVISOR => 'Rejeté par superviseur',
            self::STATUS_PENDING_FLEET_MANAGER => 'En attente gestionnaire',
            self::STATUS_APPROVED_FINAL => 'Approuvé',
            self::STATUS_REJECTED_FINAL => 'Rejeté',
            default => 'Inconnu',
        };
    }

    /**
     * Get human-readable urgency
     */
    public function getUrgencyLabelAttribute(): string
    {
        return match ($this->urgency) {
            self::URGENCY_CRITICAL => 'Critique',
            self::URGENCY_HIGH => 'Haute',
            self::URGENCY_NORMAL => 'Normale',
            self::URGENCY_LOW => 'Basse',
            default => 'Normale',
        };
    }
}
