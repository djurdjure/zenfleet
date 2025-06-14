<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     * Correspond aux colonnes de la table 'assignments'.
     */
    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'start_datetime',
        'end_datetime',
        'start_mileage',
        'end_mileage',
        'reason',
        'notes',
        'created_by_user_id',
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
}
