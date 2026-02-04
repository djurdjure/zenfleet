<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; 

class User extends Authenticatable
{
    // CORRECTION : On retire "BelongsToOrganization" et on nettoie les doublons
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes {
        hasDirectPermission as protected spatieHasDirectPermission;
        hasPermissionTo as protected spatieHasPermissionTo;
    }

    /**
     * 🔐 OVERRIDE: Relation roles() pour gérer le multi-tenant avec organization_id
     * 
     * Le système Spatie standard ne prend pas en compte organization_id.
     * Cette surcharge ajoute le filtre nécessaire pour le multi-tenant.
     * 
     * IMPORTANT: Le filtre organization_id doit accepter NULL OU la valeur de l'utilisateur
     * pour gérer les permissions globales.
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        $relation = $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            'role_id'
        );
        
        // Filtrer par organization_id (NULL ou valeur utilisateur)
        if ($this->organization_id) {
            $relation->where(function($query) {
                $query->where(config('permission.table_names.model_has_roles') . '.organization_id', $this->organization_id)
                      ->orWhereNull(config('permission.table_names.model_has_roles') . '.organization_id');
            });
        }
        
        return $relation;
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'organization_id',
        'use_custom_permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'use_custom_permissions' => 'boolean',
    ];

    /**
     * Un utilisateur appartient toujours à une organisation.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Un utilisateur peut avoir un profil de chauffeur.
     */
    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }
    
    /**
     * La relation qui retourne les véhicules auxquels cet utilisateur a accès.
     * Inclut les métadonnées de la table pivot pour tracer l'accès.
     */
    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'user_vehicle')
            ->withPivot('granted_at', 'granted_by', 'access_type')
            ->withTimestamps();
    }

    /**
     * 📊 RELATION: Relevés Kilométriques Enregistrés par l'Utilisateur
     *
     * Un utilisateur peut enregistrer plusieurs relevés kilométriques.
     * Cette relation lie les relevés créés manuellement par l'utilisateur
     * (recorded_by_id) aux relevés kilométriques.
     *
     * @return HasMany
     * @version 1.0-Enterprise
     */
    public function mileageReadings(): HasMany
    {
        return $this->hasMany(VehicleMileageReading::class, 'recorded_by_id');
    }

    /**
     * Enforce role-based permissions unless explicit custom permissions are enabled.
     */
    public function hasDirectPermission($permission): bool
    {
        if (!$this->use_custom_permissions && $this->roles()->exists()) {
            return false;
        }

        return $this->spatieHasDirectPermission($permission);
    }

    /**
     * Override permission checks to ignore direct permissions by default.
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        if (!$this->use_custom_permissions && $this->roles()->exists()) {
            $permission = $this->filterPermission($permission, $guardName);
            return $this->hasPermissionViaRole($permission);
        }

        return $this->spatieHasPermissionTo($permission, $guardName);
    }
}
