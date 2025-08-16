<?php

// app/Models/Supplier.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "organization_id",
        "name",
        "contact_person",
        "phone",
        "email",
        "address",
        "website",
        "notes",
        "is_active",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "is_active" => "boolean",
    ];

    /**
     * Get the organization that owns the supplier.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the expenses associated with the supplier.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get the repair requests associated with the supplier.
     */
    public function repairRequests()
    {
        return $this->hasMany(RepairRequest::class);
    }
}
