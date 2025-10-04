<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * RepairNotification Model
 *
 * @property int $id
 * @property int $repair_request_id
 * @property int $user_id
 * @property string $type
 * @property string $title
 * @property string $message
 * @property bool $is_read
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon $created_at
 *
 * @property-read RepairRequest $repairRequest
 * @property-read User $user
 */
class RepairNotification extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'repair_notifications';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Notification types
     */
    public const TYPE_NEW_REQUEST = 'new_request';
    public const TYPE_APPROVED = 'approved';
    public const TYPE_REJECTED = 'rejected';
    public const TYPE_STATUS_CHANGED = 'status_changed';
    public const TYPE_COMMENT_ADDED = 'comment_added';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'repair_request_id',
        'user_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'repair_request_id' => 'integer',
        'user_id' => 'integer',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Get the repair request this notification is for.
     */
    public function repairRequest(): BelongsTo
    {
        return $this->belongsTo(RepairRequest::class);
    }

    /**
     * Get the user this notification is for.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Unread notifications
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: Read notifications
     */
    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope: For specific user
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: By type
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Recent notifications (last 30 days)
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): bool
    {
        if ($this->is_read) {
            return true;
        }

        $this->is_read = true;
        $this->read_at = now();

        return $this->save();
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): bool
    {
        $this->is_read = false;
        $this->read_at = null;

        return $this->save();
    }

    /**
     * Get notification icon based on type
     */
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_NEW_REQUEST => 'fa-bell',
            self::TYPE_APPROVED => 'fa-check-circle',
            self::TYPE_REJECTED => 'fa-times-circle',
            self::TYPE_STATUS_CHANGED => 'fa-exchange-alt',
            self::TYPE_COMMENT_ADDED => 'fa-comment',
            default => 'fa-info-circle',
        };
    }

    /**
     * Get notification color based on type
     */
    public function getColorAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_NEW_REQUEST => 'blue',
            self::TYPE_APPROVED => 'green',
            self::TYPE_REJECTED => 'red',
            self::TYPE_STATUS_CHANGED => 'yellow',
            self::TYPE_COMMENT_ADDED => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get formatted timestamp
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Static method to create notification
     */
    public static function createNotification(
        RepairRequest $repairRequest,
        User $user,
        string $type,
        string $title,
        string $message
    ): self {
        return self::create([
            'repair_request_id' => $repairRequest->id,
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ]);
    }
}
