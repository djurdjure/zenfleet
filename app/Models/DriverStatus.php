<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToOrganization;

/**
 * 🚛 MODÈLE DRIVER STATUS - Version Enterprise-Grade
 *
 * Gestion complète des statuts de chauffeurs avec fonctionnalités avancées :
 * - Multi-tenant avec organisation
 * - Permissions et règles métier
 * - Interface utilisateur intégrée
 * - Validation et contrôles
 *
 * @version 2.0-Enterprise
 */
class DriverStatus extends Model
{
    use HasFactory, BelongsToOrganization;

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
        'can_drive',
        'can_assign',
        'requires_validation',
        'organization_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'can_drive' => 'boolean',
        'can_assign' => 'boolean',
        'requires_validation' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Générer le slug automatiquement
        static::creating(function ($driverStatus) {
            if (empty($driverStatus->slug)) {
                $driverStatus->slug = \Str::slug($driverStatus->name);
            }
        });

        static::updating(function ($driverStatus) {
            if ($driverStatus->isDirty('name') && empty($driverStatus->slug)) {
                $driverStatus->slug = \Str::slug($driverStatus->name);
            }
        });
    }

    // ============================================================
    // RELATIONS ENTERPRISE
    // ============================================================

    /**
     * Get all drivers with this status
     */
    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class, 'status_id');
    }

    /**
     * Get the organization that owns this status (if multi-tenant)
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    // ============================================================
    // SCOPES ENTERPRISE
    // ============================================================

    /**
     * Scope to only active statuses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to statuses that allow driving
     */
    public function scopeCanDrive($query)
    {
        return $query->where('can_drive', true);
    }

    /**
     * Scope to statuses that allow assignment
     */
    public function scopeCanAssign($query)
    {
        return $query->where('can_assign', true);
    }

    /**
     * Scope to global statuses (not organization-specific)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('organization_id');
    }

    /**
     * Scope to organization-specific statuses
     */
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where(function ($q) use ($organizationId) {
            $q->whereNull('organization_id')
              ->orWhere('organization_id', $organizationId);
        });
    }

    /**
     * Scope ordered by sort order then name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ============================================================
    // MÉTHODES ENTERPRISE
    // ============================================================

    /**
     * Check if drivers with this status can be assigned to vehicles
     */
    public function canBeAssigned(): bool
    {
        return $this->is_active && $this->can_assign && $this->can_drive;
    }

    /**
     * Check if this status requires validation before use
     */
    public function needsValidation(): bool
    {
        return $this->requires_validation;
    }

    /**
     * Get the count of drivers with this status
     */
    public function getDriversCount(): int
    {
        return $this->drivers()->count();
    }

    /**
     * Get the count of active drivers with this status
     */
    public function getActiveDriversCount(): int
    {
        return $this->drivers()->where('status', 'active')->count();
    }

    /**
     * Check if this status is safe to delete
     */
    public function canBeDeleted(): bool
    {
        return $this->getDriversCount() === 0;
    }

    /**
     * Get the HTML badge for this status
     */
    public function getBadgeHtml(): string
    {
        $classes = [
            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
            'transition-colors duration-200'
        ];

        $bgColor = $this->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';

        return sprintf(
            '<span class="%s %s" style="background-color: %s15; color: %s;">
                <i class="fas %s mr-1"></i>%s
            </span>',
            implode(' ', $classes),
            $bgColor,
            $this->color,
            $this->color,
            $this->icon,
            $this->name
        );
    }

    /**
     * Get status options for select dropdowns
     */
    public static function getSelectOptions($organizationId = null): array
    {
        $query = static::active()->ordered();

        if ($organizationId) {
            $query->forOrganization($organizationId);
        } else {
            $query->global();
        }

        return $query->get()->mapWithKeys(function ($status) {
            return [$status->id => $status->name];
        })->toArray();
    }

    /**
     * Get the default status for new drivers
     */
    public static function getDefault($organizationId = null): ?self
    {
        return static::where('slug', 'active')
            ->forOrganization($organizationId)
            ->first();
    }

    /**
     * Create organization-specific status
     */
    public static function createForOrganization(array $attributes, $organizationId): self
    {
        return static::create(array_merge($attributes, [
            'organization_id' => $organizationId
        ]));
    }

    // ============================================================
    // ATTRIBUTS CALCULÉS
    // ============================================================

    /**
     * Get the status indicator class
     */
    public function getStatusIndicatorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    /**
     * Get the display name with icon
     */
    public function getDisplayNameAttribute(): string
    {
        return sprintf(
            '<i class="fas %s mr-2" style="color: %s;"></i>%s',
            $this->icon,
            $this->color,
            $this->name
        );
    }

    /**
     * Convert to array for API responses
     */
    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'can_drive' => $this->can_drive,
            'can_assign' => $this->can_assign,
            'requires_validation' => $this->requires_validation,
            'drivers_count' => $this->getDriversCount(),
            'active_drivers_count' => $this->getActiveDriversCount(),
            'can_be_assigned' => $this->canBeAssigned(),
        ];
    }
}
