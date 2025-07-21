<?php

namespace App\Models\Maintenance;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceLog extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'maintenance_plan_id',
        'maintenance_type_id',
        'maintenance_status_id',
        'performed_on_date',
        'performed_at_mileage',
        'cost',
        'details',
        'performed_by',
        'organization_id',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'performed_on_date' => 'date',
        'cost' => 'decimal:2',
    ];

    /**
     * Relation : Un log de maintenance appartient à un véhicule.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relation : Un log peut être lié à un plan de maintenance (pour la maintenance préventive).
     */
    public function maintenancePlan(): BelongsTo
    {
        return $this->belongsTo(MaintenancePlan::class);
    }

    /**
     * Relation : Un log est d'un certain type de maintenance.
     */
    public function maintenanceType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    /**
     * Relation : Un log a un statut.
     */
    public function maintenanceStatus(): BelongsTo
    {
        return $this->belongsTo(MaintenanceStatus::class);
    }
}
