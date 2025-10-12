<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $fillable = [
        // Informations générales
        'uuid',
        'name',
        'legal_name',
        'organization_type',
        'industry',
        'description',
        'website',
        'phone_number',
        'email',
        'logo_path',
        'status',

        // Informations légales Algeria
        'trade_register',
        'nif',
        'ai',
        'nis',
        'address',
        'city',
        'commune',
        'zip_code',
        'wilaya',
        'scan_nif_path',
        'scan_ai_path',
        'scan_nis_path',

        // Représentant légal
        'manager_first_name',
        'manager_last_name',
        'manager_nin',
        'manager_address',
        'manager_dob',
        'manager_pob',
        'manager_phone_number',
        'manager_id_scan_path',
    ];

    protected $casts = [
        'manager_dob' => 'date',
        'uuid' => 'string',
    ];

    protected $dates = [
        'manager_dob',
        'created_at',
        'updated_at',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    // ✅ RELATIONS SÉCURISÉES - Avec vérification d'existence des rôles

    public function users(): HasMany
    {
        return $this->hasMany(User::class)->where('status', 'active');
    }

    public function allUsers(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function activeUsers(): HasMany
    {
        return $this->hasMany(User::class)->where('status', 'active');
    }

    public function admins(): HasMany
    {
        // ✅ CORRECTION: Vérifier que le rôle existe avant de l'utiliser
        try {
            return $this->users()->role('Admin');
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            \Log::warning("Role 'Admin' does not exist for organization {$this->name}");

            return $this->users()->whereHas('roles', function ($q) {
                $q->where('name', 'Admin');
            });
        }
    }

    public function fleetManagers(): HasMany
    {
        // ✅ CORRECTION: Utiliser le bon nom de rôle
        try {
            return $this->users()->role('Gestionnaire Flotte');
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            \Log::warning("Role 'Gestionnaire Flotte' does not exist for organization {$this->name}");

            return $this->users()->whereHas('roles', function ($q) {
                $q->whereIn('name', ['Gestionnaire Flotte', 'fleet_manager']);
            });
        }
    }

    public function supervisors(): HasMany
    {
        try {
            return $this->users()->role('supervisor');
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            \Log::warning("Role 'supervisor' does not exist for organization {$this->name}");

            return $this->users()->whereHas('roles', function ($q) {
                $q->where('name', 'supervisor');
            });
        }
    }

    public function drivers(): HasMany
    {
        // ✅ CORRECTION PRINCIPALE: Gestion sécurisée du rôle driver
        try {
            return $this->users()->role('Chauffeur');
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            \Log::warning("Role 'Chauffeur' does not exist for organization {$this->name}");

            // Fallback avec requête directe
            return $this->users()->whereHas('roles', function ($q) {
                $q->whereIn('name', ['Chauffeur', 'driver']);
            });
        }
    }

    // Relations avec autres modèles
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function driversModel(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    /**
     * 🚗 VÉHICULES ACTIFS
     *
     * Retourne uniquement les véhicules actifs (utilise le scope active())
     *
     * @return HasMany
     */
    public function activeVehicles(): HasMany
    {
        // REFACTORED: utilisation du Query Scope active() du modèle Vehicle
        return $this->vehicles()->active();
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function maintenanceRecords(): HasManyThrough
    {
        return $this->hasManyThrough(MaintenanceRecord::class, Vehicle::class);
    }

    public function trips(): HasManyThrough
    {
        return $this->hasManyThrough(Trip::class, Vehicle::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(PermissionAuditLog::class);
    }

    // Algeria-specific relationships
    public function wilayaInfo(): BelongsTo
    {
        return $this->belongsTo(AlgeriaWilaya::class, 'wilaya', 'code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWithActiveSubscription($query)
    {
        return $query->where('subscription_expires_at', '>', now())
            ->orWhereNull('subscription_expires_at');
    }

    // Méthodes utilitaires
    public function isSubscriptionActive(): bool
    {
        if ($this->subscription_expires_at === null) {
            return true;
        }

        return $this->subscription_expires_at->isFuture();
    }

    public function canAddUsers(): bool
    {
        $limits = [
            'basic' => 10,
            'professional' => 50,
            'enterprise' => null, // Illimité
        ];

        $limit = $limits[$this->subscription_plan] ?? 10;

        if ($limit === null) {
            return true;
        }

        return $this->users()->count() < $limit;
    }

    public function canAddVehicles(): bool
    {
        $limits = [
            'basic' => 25,
            'professional' => 100,
            'enterprise' => null, // Illimité
        ];

        $limit = $limits[$this->subscription_plan] ?? 25;

        if ($limit === null) {
            return true;
        }

        return $this->vehicles()->count() < $limit;
    }

    public function getSettingsAttribute($value): array
    {
        $default = [
            'locale' => 'ar',
            'date_format' => 'd/m/Y',
            'phone_format' => '+213',
            'notifications' => [
                'maintenance_alerts' => true,
                'fuel_alerts' => true,
                'driver_alerts' => true,
                'email_reports' => true,
            ],
        ];

        return array_merge($default, json_decode($value, true) ?? []);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($organization) {
            if (empty($organization->uuid)) {
                $organization->uuid = (string) Str::uuid();
            }
        });

        static::deleting(function ($organization) {
            // Soft delete tous les utilisateurs liés
            $organization->allUsers()->delete();
            // Soft delete tous les véhicules liés
            $organization->vehicles()->delete();
        });
    }

    /**
     * Get available roles for this organization
     */
    public function getAvailableRoles(): array
    {
        return \Spatie\Permission\Models\Role::where('guard_name', 'web')
            ->pluck('name')
            ->toArray();
    }

    /**
     * Get user count by role
     */
    public function getUserCountByRole(string $roleName): int
    {
        return $this->users()->whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        })->count();
    }

    /**
     * Get organization statistics
     */
    public function getStatistics(): array
    {
        return [
            'users' => [
                'total' => $this->current_users,
                'active' => $this->activeUsers()->count(),
                'by_role' => [
                    'admins' => $this->admins()->count(),
                    'fleet_managers' => $this->fleetManagers()->count(),
                    'supervisors' => $this->supervisors()->count(),
                    'drivers' => $this->drivers()->count(),
                ],
            ],
            'fleet' => [
                'vehicles' => $this->current_vehicles,
                'drivers' => $this->current_drivers,
            ],
            'limits' => [
                'users' => "{$this->current_users}/{$this->max_users}",
                'vehicles' => "{$this->current_vehicles}/{$this->max_vehicles}",
                'drivers' => "{$this->current_drivers}/{$this->max_drivers}",
                'storage' => "{$this->current_storage_mb}/{$this->max_storage_mb} MB",
            ],
            'subscription' => [
                'plan' => $this->subscription_plan,
                'active' => $this->isSubscriptionActive(),
                'expires_at' => $this->subscription_expires_at,
            ],
        ];
    }

    /**
     * Convert the model to an array optimized for API responses
     */
    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'display_name' => $this->display_name,
            'organization_type' => $this->organization_type,
            'industry' => $this->industry,
            'status' => $this->status,
            'wilaya' => $this->wilaya,
            'city' => $this->city,
            'logo_url' => $this->logo_path ? asset('storage/'.$this->logo_path) : null,
            'subscription_plan' => $this->subscription_plan,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
