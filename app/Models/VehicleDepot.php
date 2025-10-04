<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * VehicleDepot Model
 *
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string $code
 * @property string|null $address
 * @property string|null $city
 * @property string|null $wilaya
 * @property string|null $postal_code
 * @property string|null $phone
 * @property string|null $manager_name
 * @property string|null $manager_phone
 * @property int|null $capacity
 * @property int $current_count
 * @property float|null $latitude
 * @property float|null $longitude
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Organization $organization
 * @property-read \Illuminate\Database\Eloquent\Collection<Vehicle> $vehicles
 */
class VehicleDepot extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicle_depots';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'address',
        'city',
        'wilaya',
        'postal_code',
        'phone',
        'manager_name',
        'manager_phone',
        'capacity',
        'current_count',
        'latitude',
        'longitude',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'organization_id' => 'integer',
        'capacity' => 'integer',
        'current_count' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the organization that owns the depot.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get all vehicles in this depot.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'depot_id');
    }

    /**
     * Scope a query to only include active depots.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include depots for a specific organization.
     */
    public function scopeForOrganization(Builder $query, int $organizationId): Builder
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope a query to only include depots with available capacity.
     */
    public function scopeWithCapacity(Builder $query): Builder
    {
        return $query->whereColumn('current_count', '<', 'capacity')
                     ->whereNotNull('capacity');
    }

    /**
     * Get the available capacity.
     */
    public function getAvailableCapacityAttribute(): ?int
    {
        if ($this->capacity === null) {
            return null;
        }

        return max(0, $this->capacity - $this->current_count);
    }

    /**
     * Get the occupancy percentage.
     */
    public function getOccupancyPercentageAttribute(): ?float
    {
        if ($this->capacity === null || $this->capacity === 0) {
            return null;
        }

        return round(($this->current_count / $this->capacity) * 100, 2);
    }

    /**
     * Check if depot has available space.
     */
    public function hasAvailableSpace(): bool
    {
        if ($this->capacity === null) {
            return true; // Unlimited capacity
        }

        return $this->current_count < $this->capacity;
    }

    /**
     * Check if depot is at full capacity.
     */
    public function isFull(): bool
    {
        if ($this->capacity === null) {
            return false;
        }

        return $this->current_count >= $this->capacity;
    }

    /**
     * Increment the current vehicle count.
     */
    public function incrementCount(int $amount = 1): bool
    {
        $this->current_count += $amount;
        return $this->save();
    }

    /**
     * Decrement the current vehicle count.
     */
    public function decrementCount(int $amount = 1): bool
    {
        $this->current_count = max(0, $this->current_count - $amount);
        return $this->save();
    }

    /**
     * Activate the depot.
     */
    public function activate(): bool
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * Deactivate the depot.
     */
    public function deactivate(): bool
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * Get full address as a single string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->wilaya,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }
}
