<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 🚗 VEHICLE STATUS MODEL - Enterprise-Grade Type Definition
 *
 * Modèle représentant les différents statuts possibles pour un véhicule.
 * Aligné avec la table vehicle_statuses et le VehicleStatusEnum.
 * 
 * NOTE: Ce modèle N'UTILISE PAS le trait BelongsToOrganization car les statuts
 * sont des enregistrements globaux partagés par toutes les organisations.
 *
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $description
 * @property string $color
 * @property string $icon
 * @property bool $is_active
 * @property int $sort_order
 * @property bool $can_be_assigned
 * @property bool $is_operational
 * @property bool $requires_maintenance
 * @property int|null $organization_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @version 2.0-Enterprise
 */
class VehicleStatus extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'vehicle_statuses';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'is_active',
        'sort_order',
        'can_be_assigned',
        'is_operational',
        'requires_maintenance',
        'organization_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'can_be_assigned' => 'boolean',
        'is_operational' => 'boolean',
        'requires_maintenance' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'color' => '#6b7280',
        'icon' => 'lucide:circle',
        'is_active' => true,
        'sort_order' => 0,
        'can_be_assigned' => false,
        'is_operational' => true,
        'requires_maintenance' => false,
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get the vehicles with this status.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'status_id');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope to filter only active statuses.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter statuses that can be assigned.
     */
    public function scopeAssignable($query)
    {
        return $query->where('can_be_assigned', true);
    }

    /**
     * Scope to filter operational statuses.
     */
    public function scopeOperational($query)
    {
        return $query->where('is_operational', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Get the badge color class based on the hex color.
     */
    public function getBadgeClassAttribute(): string
    {
        // Map hex colors to Tailwind classes (fallback)
        $colorMap = [
            '#3b82f6' => 'bg-blue-100 text-blue-800',    // Blue - Parking
            '#10b981' => 'bg-emerald-100 text-emerald-800', // Green - Affecté
            '#ef4444' => 'bg-rose-100 text-rose-800',    // Red - En panne
            '#f59e0b' => 'bg-amber-100 text-amber-800',  // Amber - En maintenance
            '#6b7280' => 'bg-gray-100 text-gray-800',    // Gray - Réformé
        ];

        return $colorMap[$this->color] ?? 'bg-gray-100 text-gray-800';
    }
}
