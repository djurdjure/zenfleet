<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Handover\VehicleHandoverForm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// CORRECTION : Ajout des bons namespaces pour les relations
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assignment extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'vehicle_id', 'driver_id', 'start_datetime', 'end_datetime', 'start_mileage',
        'end_mileage', 'reason', 'notes', 'created_by_user_id', 'organization_id',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    // CORRECTION : Ajout des bons types de retour
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function handoverForm(): HasOne
    {
        return $this->hasOne(VehicleHandoverForm::class);
    }
}