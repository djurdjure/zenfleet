<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleStatus extends Model
{
    use HasFactory;
    // Indique à Laravel que ce modèle n'utilise pas les colonnes created_at et updated_at.
    public $timestamps = false;

    // Définit le champ 'name' comme étant assignable en masse.
    protected $fillable = ['name'];

}
