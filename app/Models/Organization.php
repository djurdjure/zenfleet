<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Supplier;
use App\Models\MaintenanceRecord;
use App\Models\Trip;
use App\Models\PermissionAuditLog;

class Organization extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'name',
        'slug', 
        'legal_name',
        'organization_type',
        'industry',
        'description',
        'siret',
        'vat_number',
        'legal_form',
        'registration_number',
        'registration_date',
        'email',
        'phone',
        'website',
        'address',
        'address_line_2',
        'city',
        'postal_code',
        'state_province',
        'country',
        'timezone',
        'currency',
        'language',
        'date_format',
        'time_format',
        'logo_path',
        'status',
        'subscription_plan',
        'subscription_expires_at',
        'max_vehicles',
        'max_drivers', 
        'max_users',
        'working_days',
        'settings',
        'created_by',
        'updated_by',
        'admin_user_id',
        'total_users',
        'active_users',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'subscription_expires_at' => 'datetime',
        'is_active' => 'boolean',
        'settings' => 'array',
        'working_days' => 'array',
    ];

    protected $dates = [
        'subscription_expires_at',
        'deleted_at'
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
        return $this->hasMany(User::class)->where('is_active', true);
    }

    public function allUsers(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function admins(): HasMany
    {
        // ✅ CORRECTION: Vérifier que le rôle existe avant de l'utiliser
        try {
            return $this->users()->role('Admin');
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            \Log::warning("Role 'Admin' does not exist for organization {$this->name}");
            return $this->users()->whereHas('roles', function($q) { $q->where('name', 'Admin'); });
        }
    }

    public function fleetManagers(): HasMany
    {
        // ✅ CORRECTION: Utiliser le bon nom de rôle
        try {
            return $this->users()->role('Gestionnaire Flotte');
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            \Log::warning("Role 'Gestionnaire Flotte' does not exist for organization {$this->name}");
            return $this->users()->whereHas('roles', function($q) { 
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
            return $this->users()->whereHas('roles', function($q) { $q->where('name', 'supervisor'); });
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
            return $this->users()->whereHas('roles', function($q) { 
                $q->whereIn('name', ['Chauffeur', 'driver']); 
            });
        }
    }

    // Relations avec autres modèles
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function activeVehicles(): HasMany
    {
        return $this->vehicles()->where('status', 'active');
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
            'enterprise' => null // Illimité
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
            'enterprise' => null // Illimité
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
            'timezone' => 'UTC',
            'currency' => 'EUR',
            'language' => 'fr',
            'date_format' => 'd/m/Y',
            'notifications' => [
                'maintenance_alerts' => true,
                'fuel_alerts' => true,
                'driver_alerts' => true,
                'email_reports' => true
            ]
        ];

        return array_merge($default, json_decode($value, true) ?? []);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($organization) {
            // Soft delete tous les utilisateurs liés
            $organization->allUsers()->delete();
            // Soft delete tous les véhicules liés
            $organization->vehicles()->delete();
        });
    }

    // ✅ NOUVELLE MÉTHODE: Vérifier les rôles disponibles
    public function getAvailableRoles(): array
    {
        return \Spatie\Permission\Models\Role::where('guard_name', 'web')
            ->pluck('name')
            ->toArray();
    }

    // ✅ NOUVELLE MÉTHODE: Statistiques sécurisées par rôle
    public function getUserCountByRole(string $roleName): int
    {
        try {
            return $this->users()->role($roleName)->count();
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            return 0;
        }
    }
}
