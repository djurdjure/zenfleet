<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Maintenance\MaintenanceLog;
use App\Models\Maintenance\MaintenancePlan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
// CORRECTION : Ajout des bons namespaces pour les relations
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'registration_plate', 'vin', 'brand', 'model', 'color', 'vehicle_type_id',
        'fuel_type_id', 'transmission_type_id', 'status_id', 'manufacturing_year',
        'acquisition_date', 'purchase_price', 'current_value', 'initial_mileage',
        'current_mileage', 'engine_displacement_cc', 'power_hp', 'seats', 'status_reason', 'notes', 'organization_id',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
    ];

    // CORRECTION : Ajout du bon type de retour (BelongsTo)
    public function vehicleType(): BelongsTo { return $this->belongsTo(VehicleType::class); }
    public function fuelType(): BelongsTo { return $this->belongsTo(FuelType::class); }
    public function transmissionType(): BelongsTo { return $this->belongsTo(TransmissionType::class); }
    public function vehicleStatus(): BelongsTo { return $this->belongsTo(VehicleStatus::class, 'status_id'); }

    // CORRECTION : Ajout du bon type de retour (HasMany)
    public function assignments(): HasMany { return $this->hasMany(Assignment::class); }
    public function maintenancePlans(): HasMany { return $this->hasMany(MaintenancePlan::class); }
    public function maintenanceLogs(): HasMany { return $this->hasMany(MaintenanceLog::class); }
    
    /**
     * Vérifie si le véhicule a une affectation actuellement en cours.
     */
    public function isCurrentlyAssigned(): bool
    {
       return $this->assignments()->whereNull('end_datetime')->exists();
    }

    /**
     * La relation qui retourne les utilisateurs autorisés à utiliser ce véhicule.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_vehicle');
    }
}