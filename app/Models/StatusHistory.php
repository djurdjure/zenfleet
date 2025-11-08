<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * 📊 STATUS HISTORY MODEL - Enterprise-Grade Audit Trail
 *
 * Modèle pour la traçabilité complète des changements de statuts.
 *
 * Fonctionnalités:
 * - Polymorphic relations (vehicles, drivers, extensible)
 * - Event Sourcing léger pour reconstruction d'état
 * - Analytics et reporting avancé
 * - Conformité réglementaire (RGPD, audit trails)
 *
 * @property int $id
 * @property string $statusable_type
 * @property int $statusable_id
 * @property string|null $from_status
 * @property string $to_status
 * @property string|null $reason
 * @property array|null $metadata
 * @property int|null $changed_by_user_id
 * @property string $change_type
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property int|null $organization_id
 * @property Carbon $changed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @version 2.0-Enterprise
 */
class StatusHistory extends Model
{
    use HasFactory, BelongsToOrganization;

    /**
     * The table associated with the model.
     */
    protected $table = 'status_history';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'statusable_type',
        'statusable_id',
        'from_status',
        'to_status',
        'reason',
        'metadata',
        'changed_by_user_id',
        'change_type',
        'ip_address',
        'user_agent',
        'organization_id',
        'changed_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'metadata' => 'array',
        'changed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'ip_address',
        'user_agent',
    ];

    // =========================================================================
    // RELATIONS - POLYMORPHIC & FOREIGN KEYS
    // =========================================================================

    /**
     * Get the parent statusable model (Vehicle, Driver, etc.)
     */
    public function statusable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who made the change
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }

    /**
     * Get the organization that owns this history entry
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    // =========================================================================
    // SCOPES - FILTERING & QUERIES
    // =========================================================================

    /**
     * Scope to filter by entity type
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type Class name or short name (e.g., 'Vehicle', 'Driver')
     */
    public function scopeForType($query, string $type)
    {
        // Support both full class name and short name
        if (!str_contains($type, '\\')) {
            $type = "App\\Models\\{$type}";
        }

        return $query->where('statusable_type', $type);
    }

    /**
     * Scope to filter by entity ID
     */
    public function scopeForEntity($query, int $id)
    {
        return $query->where('statusable_id', $id);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('changed_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by status transition
     */
    public function scopeFromStatus($query, string $status)
    {
        return $query->where('from_status', $status);
    }

    /**
     * Scope to filter by target status
     */
    public function scopeToStatus($query, string $status)
    {
        return $query->where('to_status', $status);
    }

    /**
     * Scope to filter by change type
     */
    public function scopeChangeType($query, string $type)
    {
        return $query->where('change_type', $type);
    }

    /**
     * Scope to filter manual changes only
     */
    public function scopeManual($query)
    {
        return $query->where('change_type', 'manual');
    }

    /**
     * Scope to filter automatic changes only
     */
    public function scopeAutomatic($query)
    {
        return $query->where('change_type', 'automatic');
    }

    /**
     * Scope ordered by most recent first
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('changed_at', 'desc');
    }

    /**
     * Scope ordered by oldest first
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('changed_at', 'asc');
    }

    // =========================================================================
    // HELPER METHODS - BUSINESS LOGIC
    // =========================================================================

    /**
     * Check if this was a manual change by a user
     */
    public function isManualChange(): bool
    {
        return $this->change_type === 'manual' && $this->changed_by_user_id !== null;
    }

    /**
     * Check if this was an automatic system change
     */
    public function isAutomaticChange(): bool
    {
        return $this->change_type === 'automatic';
    }

    /**
     * Check if this is an initial status (no previous status)
     */
    public function isInitialStatus(): bool
    {
        return $this->from_status === null;
    }

    /**
     * Get the duration in the previous status (if applicable)
     *
     * @return int|null Duration in seconds, or null if initial status
     */
    public function getPreviousStatusDuration(): ?int
    {
        if ($this->isInitialStatus()) {
            return null;
        }

        // Find the previous status change for the same entity
        $previousChange = static::forType($this->statusable_type)
            ->forEntity($this->statusable_id)
            ->where('changed_at', '<', $this->changed_at)
            ->orderBy('changed_at', 'desc')
            ->first();

        if (!$previousChange) {
            return null;
        }

        return $this->changed_at->diffInSeconds($previousChange->changed_at);
    }

    /**
     * Get a human-readable description of this change
     */
    public function getDescription(): string
    {
        $entity = class_basename($this->statusable_type);
        $from = $this->from_status ?? 'Création';
        $to = $this->to_status;

        return "{$entity} : {$from} → {$to}";
    }

    /**
     * Get the change badge HTML
     */
    public function getBadgeHtml(): string
    {
        $typeColors = [
            'manual' => 'bg-blue-100 text-blue-800',
            'automatic' => 'bg-green-100 text-green-800',
            'system' => 'bg-gray-100 text-gray-800',
        ];

        $color = $typeColors[$this->change_type] ?? 'bg-gray-100 text-gray-800';

        return sprintf(
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium %s">%s</span>',
            $color,
            ucfirst($this->change_type)
        );
    }

    // =========================================================================
    // STATIC FACTORY METHODS - CREATE HISTORY ENTRIES
    // =========================================================================

    /**
     * Record a status change
     *
     * @param Model $entity Entity whose status changed (Vehicle, Driver, etc.)
     * @param string|null $fromStatus Previous status (null if initial)
     * @param string $toStatus New status
     * @param array $options Additional options (reason, metadata, change_type, etc.)
     * @return static
     */
    public static function recordChange(
        Model $entity,
        ?string $fromStatus,
        string $toStatus,
        array $options = []
    ): self {
        return static::create([
            'statusable_type' => get_class($entity),
            'statusable_id' => $entity->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'reason' => $options['reason'] ?? null,
            'metadata' => $options['metadata'] ?? null,
            'changed_by_user_id' => $options['user_id'] ?? auth()->id(),
            'change_type' => $options['change_type'] ?? 'manual',
            'ip_address' => $options['ip_address'] ?? request()->ip(),
            'user_agent' => $options['user_agent'] ?? request()->userAgent(),
            'organization_id' => $entity->organization_id ?? null,
            'changed_at' => $options['changed_at'] ?? now(),
        ]);
    }

    // =========================================================================
    // ANALYTICS & REPORTING METHODS
    // =========================================================================

    /**
     * Get average duration in a specific status
     *
     * @param string $entityType
     * @param string $status
     * @param int|null $organizationId
     * @return float|null Average duration in days
     */
    public static function getAverageDurationInStatus(
        string $entityType,
        string $status,
        ?int $organizationId = null
    ): ?float {
        // This is a simplified version - real implementation would be more complex
        // involving window functions or subqueries

        $query = static::forType($entityType)
            ->toStatus($status);

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        $changes = $query->get();

        if ($changes->isEmpty()) {
            return null;
        }

        $durations = $changes->map(fn($change) => $change->getPreviousStatusDuration())->filter();

        if ($durations->isEmpty()) {
            return null;
        }

        // Convert to days
        return $durations->average() / 86400;
    }

    /**
     * Get status transition statistics
     *
     * @param string $entityType
     * @param int|null $organizationId
     * @return array
     */
    public static function getTransitionStats(string $entityType, ?int $organizationId = null): array
    {
        $query = static::forType($entityType);

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        $transitions = $query->select('from_status', 'to_status', \DB::raw('count(*) as count'))
            ->whereNotNull('from_status')
            ->groupBy('from_status', 'to_status')
            ->get();

        return $transitions->map(function ($transition) {
            return [
                'from' => $transition->from_status,
                'to' => $transition->to_status,
                'count' => $transition->count,
            ];
        })->toArray();
    }
}
