<?php

namespace App\Models\Maintenance;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenancePlan extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'maintenance_type_id',
        'recurrence_value',
        'recurrence_unit_id',
        'next_due_date',
        'next_due_mileage',
        'notes',
        'organization_id',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'next_due_date' => 'date',
    ];

    /**
     * Relation : Un plan de maintenance appartient à un véhicule.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relation : Un plan de maintenance est d'un certain type.
     */
    public function maintenanceType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    /**
     * Relation : Un plan de maintenance a une unité de récurrence.
     */
    public function recurrenceUnit(): BelongsTo
    {
        return $this->belongsTo(RecurrenceUnit::class);
    }
}
