<?php

namespace App\Models\Maintenance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurrenceUnit extends Model
{
    use HasFactory;

    /**
     * Indique si le modèle doit être horodaté.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];
}
