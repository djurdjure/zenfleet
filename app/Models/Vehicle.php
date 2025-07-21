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

    protected $casts = [
        'acquisition_date' => 'date',
    ];

    public function vehicleType(): BelongsTo { return $this->belongsTo(VehicleType::class); }
    public function fuelType(): BelongsTo { return $this->belongsTo(FuelType::class); }
    public function transmissionType(): BelongsTo { return $this->belongsTo(TransmissionType::class); }
    public function vehicleStatus(): BelongsTo { return $this->belongsTo(VehicleStatus::class, 'status_id'); }
    public function assignments(): HasMany { return $this->hasMany(Assignment::class); }
    public function maintenancePlans(): HasMany { return $this->hasMany(\App\Models\Maintenance\MaintenancePlan::class); }
    public function maintenanceLogs(): HasMany { return $this->hasMany(\App\Models\Maintenance\MaintenanceLog::class); }
}