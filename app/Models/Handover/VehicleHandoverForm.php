<?php
namespace App\Models\Handover;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class VehicleHandoverForm extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;
    protected $fillable = [
        'assignment_id', 'issue_date', 'current_mileage', 'general_observations', 
        'additional_observations', 'signed_form_path', 'organization_id',
    ];




    protected $casts = ['issue_date' => 'date'];
    public function assignment(): BelongsTo { return $this->belongsTo(Assignment::class); }
    public function details(): HasMany { return $this->hasMany(VehicleHandoverDetail::class, 'handover_form_id'); }

    /**
     * Mark this handover form as the latest version and mark others as obsolete.
     */
    public function markAsLatestVersion()
    {
        // Mark all other handover forms for this assignment as not latest
        static::where('assignment_id', $this->assignment_id)
            ->where('id', '!=', $this->id)
            ->update(['is_latest_version' => false]);
        
        // Mark this one as latest
        $this->update(['is_latest_version' => true]);
    }
}
