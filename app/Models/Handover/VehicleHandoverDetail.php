<?php
namespace App\Models\Handover;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleHandoverDetail extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['handover_form_id', 'category', 'item', 'status'];
    public function handoverForm(): BelongsTo { return $this->belongsTo(VehicleHandoverForm::class, 'handover_form_id'); }
}
