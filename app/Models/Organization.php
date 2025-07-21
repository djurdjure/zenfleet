<?php
namespace App\Models;

use App\Models\Handover\VehicleHandoverForm;
use App\Models\Maintenance\MaintenanceLog;
use App\Models\Maintenance\MaintenancePlan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory;
    protected $fillable = ['uuid', 'name', 'address', 'contact_email', 'status'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function users(): HasMany { return $this->hasMany(User::class); }
    public function vehicles(): HasMany { return $this->hasMany(Vehicle::class); }
    public function drivers(): HasMany { return $this->hasMany(Driver::class); }
    public function assignments(): HasMany { return $this->hasMany(Assignment::class); }
    public function maintenancePlans(): HasMany { return $this->hasMany(MaintenancePlan::class); }
    public function maintenanceLogs(): HasMany { return $this->hasMany(MaintenanceLog::class); }
    public function handoverForms(): HasMany { return $this->hasMany(VehicleHandoverForm::class); }
}
