<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationLevel extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     * Laravel handles timestamps by default if the columns exist,
     * so no 'public $timestamps = true;' is explicitly needed.
     * Only set 'public $timestamps = false;' if you DON'T have/want them.
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'level_number',
        'name',
        'description',
    ];

    /**
     * Define the relationship with users.
     * Un niveau de validation peut être associé à plusieurs utilisateurs.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_validation_levels');
    }
}
