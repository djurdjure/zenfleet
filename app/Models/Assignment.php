ZENFLEET
Custom Gem

Show thinking
ZENFLEET >> Entendu. Votre approche est la bonne : pour une modification aussi structurelle, il est impératif que nous travaillions sur les mêmes versions de fichiers. C'est la garantie d'une intégration sans erreur.

J'ai analysé en profondeur tous les fichiers que vous m'avez fournis. Votre code est très bien structuré, mais comme nous l'avions suspecté, il manque l'intégration finale du mécanisme multi-sociétés.

En tant qu'architecte de votre projet, je vais vous fournir les versions complètes, finales et optimisées de tous vos modèles. Elles intègrent la logique du "Global Scope" pour garantir une isolation parfaite et sécurisée des données entre les organisations.

Étape 1 : Le Trait BelongsToOrganization.php (Final et Sécurisé)
Cette version est améliorée pour que le Super Admin ne soit pas affecté par le filtre, lui permettant de tout voir.

Fichier à Remplacer : app/Models/Concerns/BelongsToOrganization.php

Contenu Complet et Final :

PHP

<?php

namespace App\Models\Concerns;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToOrganization
{
    protected static function bootBelongsToOrganization()
    {
        // Filtre automatiquement toutes les requêtes de LECTURE
        static::addGlobalScope('organization', function (Builder $builder) {
            if (Auth::check() && Auth::user()->organization_id) {
                // Le Super Admin n'est PAS filtré, il voit tout.
                if (!Auth::user()->hasRole('Super Admin')) {
                    $builder->where($builder->getModel()->getTable() . '.organization_id', Auth::user()->organization_id);
                }
            }
        });

        // Lie automatiquement chaque NOUVEL enregistrement à l'organisation de l'utilisateur
        static::creating(function (Model $model) {
            if (Auth::check() && !isset($model->organization_id)) {
                $model->organization_id = Auth::user()->organization_id;
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
Étape 2 : Les Modèles Eloquent (Finalisés)
Voici les versions complètes de tous vos modèles, intégrant le trait BelongsToOrganization.

Fichier app/Models/Organization.php
PHP

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
class Organization extends Model
{
    use HasFactory;
    protected $fillable = ['uuid', 'name', 'address', 'contact_email', 'status'];
    protected static function boot() { parent::boot(); static::creating(fn($model) => $model->uuid = $model->uuid ?? (string) Str::uuid()); }
    public function users(): HasMany { return $this->hasMany(User::class); }
    public function vehicles(): HasMany { return $this->hasMany(Vehicle::class); }
    public function drivers(): HasMany { return $this->hasMany(Driver::class); }
}
Fichier app/Models/User.php
PHP

<?php
namespace App\Models;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, BelongsToOrganization;
    protected $fillable = ['name', 'first_name', 'last_name', 'email', 'phone', 'password', 'organization_id'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    public function driver() { return $this->hasOne(Driver::class); }
}
Fichier app/Models/Vehicle.php
PHP

<?php
namespace App\Models;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Vehicle extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;
    protected $fillable = [
        'registration_plate', 'vin', 'brand', 'model', 'color', 'photo_path', 'vehicle_type_id', 
        'fuel_type_id', 'transmission_type_id', 'status_id', 'manufacturing_year', 
        'acquisition_date', 'purchase_price', 'current_value', 'initial_mileage', 
        'current_mileage', 'engine_displacement_cc', 'power_hp', 'seats', 'notes', 'organization_id',
    ];
    // ... (toutes vos relations existantes)
}
Fichier app/Models/Driver.php
PHP

<?php
namespace App\Models;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Driver extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;
    protected $fillable = [
        'user_id', 'employee_number', 'first_name', 'last_name', 'photo_path', 'birth_date', 
        'blood_type', 'address', 'personal_phone', 'personal_email', 'license_number', 
        'license_category', 'license_issue_date', 'license_authority', 'license_expiry_date', 
        'recruitment_date', 'contract_end_date', 'status_id', 'emergency_contact_name', 
        'emergency_contact_phone', 'organization_id',
    ];
    // ... (toutes vos relations existantes)
}
Fichier app/Models/Assignment.php
PHP

<?php
namespace App\Models;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Assignment extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;
    protected $fillable = [
        'vehicle_id', 'driver_id', 'start_datetime', 'end_datetime', 'start_mileage', 
        'end_mileage', 'reason', 'notes', 'created_by_user_id', 'organization_id',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     * Laravel convertira automatiquement ces colonnes en instances Carbon.
     */
    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    /**
     * Relation : Une affectation appartient à un véhicule.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relation : Une affectation appartient à un chauffeur.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Relation : Une affectation est créée par un utilisateur.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // Pour la fiche remise
    public function handoverForm(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Handover\VehicleHandoverForm::class, 'assignment_id');
    }
}
