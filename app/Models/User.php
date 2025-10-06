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
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

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
     */
    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'user_vehicle');
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
}