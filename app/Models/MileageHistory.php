<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle d'historique de kilométrage
 * 
 * @package App\Models
 * @version 1.0.0
 */
class MileageHistory extends Model
{
    use HasFactory;

    protected $table = 'mileage_histories';

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'assignment_id',
        'mileage_value',
        'recorded_at',
        'type',
        'notes',
        'created_by',
        'organization_id'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'mileage_value' => 'integer'
    ];

    /**
     * Relations
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
