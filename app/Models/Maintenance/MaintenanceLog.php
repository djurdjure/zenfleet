<?php

namespace App\Models\Maintenance;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// CORRECTION : Ajout du bon namespace pour la relation
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceLog extends Model
{
    use HasFactory; // Le SoftDeletes est déjà dans la migration mais on peut l'ajouter ici pour la cohérence
    use BelongsToOrganization, SoftDeletes;

    protected $fillable = [
        'vehicle_id', 'maintenance_plan_id', 'maintenance_type_id', 'maintenance_status_id',
        'performed_on_date', 'performed_at_mileage', 'cost', 'details', 'performed_by', 'organization_id',
    ];

    protected $casts = [
        'performed_on_date' => 'date',
        'cost' => 'decimal:2',
    ];

    // CORRECTION : Ajout des bons types de retour
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function maintenancePlan(): BelongsTo
    {
        return $this->belongsTo(MaintenancePlan::class);
    }

    public function maintenanceType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    public function maintenanceStatus(): BelongsTo
    {
        return $this->belongsTo(MaintenanceStatus::class);
    }
}