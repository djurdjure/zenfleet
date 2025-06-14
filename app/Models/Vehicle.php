<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class Vehicle extends Model
{
        //use HasFactory;
        use HasFactory, SoftDeletes; // <--- AJOUT 2 : Utiliser le trait

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'acquisition_date' => 'date',
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'registration_plate',
        'vin',
        'brand',
        'model',
        'color',
        'vehicle_type_id',
        'fuel_type_id',
        'transmission_type_id',
        'status_id',
        'manufacturing_year',
        'acquisition_year',
        'acquisition_date',
        'purchase_price',
        'current_value',
        'initial_mileage',
        'current_mileage',
        'engine_displacement_cc',
        'power_hp',
        'seats',
        'status_reason',
        'notes',
    ];


    /**
     * Relation: Un véhicule appartient à un type de véhicule.
     */
    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    /**
     * Relation: Un véhicule appartient à un type de carburant.
     */
    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }

    /**
     * Relation: Un véhicule appartient à un type de transmission.
     */
    public function transmissionType(): BelongsTo
    {
        return $this->belongsTo(TransmissionType::class);
    }

    /**
     * Relation: Un véhicule a un statut.
     */
    public function vehicleStatus(): BelongsTo
    {
        return $this->belongsTo(VehicleStatus::class, 'status_id');
    }

     /**
     * Relation : Un véhicule peut avoir plusieurs affectations.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

     /**
     * Vérifie si le véhicule a une affectation actuellement en cours.
     */
    
    public function isCurrentlyAssigned(): bool
   {   
       return $this->assignments()->whereNull('end_datetime')->exists();
   }



    /**
     * Relation: Un véhicule peut avoir plusieurs plans de maintenance.
     */
    public function maintenancePlans(): HasMany
    {
        return $this->hasMany(\App\Models\Maintenance\MaintenancePlan::class);
    }

    /**
     * Relation: Un véhicule peut avoir plusieurs historiques de maintenance.
     */
    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(\App\Models\Maintenance\MaintenanceLog::class);
    }



}
