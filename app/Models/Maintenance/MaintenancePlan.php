<?php

namespace App\Models\Maintenance;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// CORRECTION : Ajout du bon namespace pour la relation
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenancePlan extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'vehicle_id', 'maintenance_type_id', 'recurrence_value', 'recurrence_unit_id',
        'next_due_date', 'next_due_mileage', 'notes', 'organization_id',
    ];

    protected $casts = [
        'next_due_date' => 'date',
    ];

    // CORRECTION : Ajout des bons types de retour
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function maintenanceType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    public function recurrenceUnit(): BelongsTo
    {
        return $this->belongsTo(RecurrenceUnit::class);
    }
}