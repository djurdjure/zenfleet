<?php
namespace App\Models\Handover;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Assignment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleHandoverForm extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['assignment_id', 'issue_date', 'current_mileage', 'general_observations', 'additional_observations'];
    protected $casts = ['issue_date' => 'date'];
    public function assignment(): BelongsTo { return $this->belongsTo(Assignment::class); }
    public function details(): HasMany { return $this->hasMany(VehicleHandoverDetail::class, 'handover_form_id'); }
}
