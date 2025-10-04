<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * RepairRequestHistory Model
 *
 * @property int $id
 * @property int $repair_request_id
 * @property int|null $user_id
 * @property string $action
 * @property string|null $from_status
 * @property string $to_status
 * @property string|null $comment
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon $created_at
 *
 * @property-read RepairRequest $repairRequest
 * @property-read User|null $user
 */
class RepairRequestHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'repair_request_history';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'repair_request_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'comment',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'repair_request_id' => 'integer',
        'user_id' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the repair request this history entry belongs to.
     */
    public function repairRequest(): BelongsTo
    {
        return $this->belongsTo(RepairRequest::class);
    }

    /**
     * Get the user who performed this action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human-readable action label.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Créée',
            'supervisor_approved' => 'Approuvé par superviseur',
            'supervisor_rejected' => 'Rejeté par superviseur',
            'fleet_manager_approved' => 'Approuvé par gestionnaire',
            'fleet_manager_rejected' => 'Rejeté par gestionnaire',
            'updated' => 'Mise à jour',
            'deleted' => 'Supprimée',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    /**
     * Get formatted timestamp.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
