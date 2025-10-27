<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * ExpenseAuditLog Model - Traçabilité complète des modifications de dépenses
 * 
 * @property int $id
 * @property int $organization_id
 * @property int $vehicle_expense_id
 * @property int $user_id
 * @property string $action
 * @property string $action_category
 * @property string $description
 * @property array|null $old_values
 * @property array|null $new_values
 * @property array $changed_fields
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $session_id
 * @property string|null $request_id
 * @property string|null $previous_status
 * @property string|null $new_status
 * @property float|null $previous_amount
 * @property float|null $new_amount
 * @property bool $is_sensitive
 * @property bool $requires_review
 * @property bool $reviewed
 * @property int|null $reviewed_by
 * @property \Carbon\Carbon|null $reviewed_at
 * @property string|null $review_notes
 * @property bool $is_anomaly
 * @property string|null $anomaly_details
 * @property string|null $risk_level
 * @property array $metadata
 * @property array $tags
 * @property \Carbon\Carbon $created_at
 * 
 * @property-read Organization $organization
 * @property-read VehicleExpense $expense
 * @property-read User $user
 * @property-read User|null $reviewer
 */
class ExpenseAuditLog extends Model
{
    use HasFactory, BelongsToOrganization;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'expense_audit_logs';

    /**
     * Indicates if the model should be timestamped.
     * Logs are immutable, so no updated_at
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_id',
        'vehicle_expense_id',
        'user_id',
        'action',
        'action_category',
        'description',
        'old_values',
        'new_values',
        'changed_fields',
        'ip_address',
        'user_agent',
        'session_id',
        'request_id',
        'previous_status',
        'new_status',
        'previous_amount',
        'new_amount',
        'is_sensitive',
        'requires_review',
        'reviewed',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'is_anomaly',
        'anomaly_details',
        'risk_level',
        'metadata',
        'tags'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
        'previous_amount' => 'decimal:2',
        'new_amount' => 'decimal:2',
        'is_sensitive' => 'boolean',
        'requires_review' => 'boolean',
        'reviewed' => 'boolean',
        'reviewed_at' => 'datetime',
        'is_anomaly' => 'boolean',
        'metadata' => 'array',
        'tags' => 'array',
        'created_at' => 'datetime'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'reviewed_at'];

    /**
     * Actions d'audit possibles
     */
    const ACTION_CREATED = 'created';
    const ACTION_UPDATED = 'updated';
    const ACTION_DELETED = 'deleted';
    const ACTION_APPROVED = 'approved';
    const ACTION_REJECTED = 'rejected';
    const ACTION_PAID = 'paid';
    const ACTION_AUDITED = 'audited';
    const ACTION_LEVEL1_APPROVED = 'level1_approved';
    const ACTION_LEVEL2_APPROVED = 'level2_approved';
    const ACTION_RESTORED = 'restored';
    const ACTION_ARCHIVED = 'archived';
    const ACTION_IMPORTED = 'imported';
    const ACTION_EXPORTED = 'exported';

    /**
     * Catégories d'actions
     */
    const CATEGORY_WORKFLOW = 'workflow';
    const CATEGORY_FINANCIAL = 'financial';
    const CATEGORY_ADMINISTRATIVE = 'administrative';
    const CATEGORY_SECURITY = 'security';
    const CATEGORY_SYSTEM = 'system';

    /**
     * Niveaux de risque
     */
    const RISK_LOW = 'low';
    const RISK_MEDIUM = 'medium';
    const RISK_HIGH = 'high';
    const RISK_CRITICAL = 'critical';

    // ====================================================================
    // BOOT METHOD
    // ====================================================================

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            // Ajouter automatiquement created_at si non défini
            if (!$log->created_at) {
                $log->created_at = now();
            }

            // Ajouter automatiquement les informations de requête
            if (!$log->ip_address) {
                $log->ip_address = request()->ip();
            }

            if (!$log->user_agent) {
                $log->user_agent = request()->userAgent();
            }

            if (!$log->session_id) {
                $log->session_id = session()->getId();
            }

            if (!$log->request_id) {
                $log->request_id = request()->header('X-Request-ID') ?? uniqid('req_');
            }

            // Déterminer automatiquement la catégorie si non définie
            if (!$log->action_category) {
                $log->action_category = self::determineCategory($log->action);
            }

            // Déterminer automatiquement si sensible
            if ($log->is_sensitive === null) {
                $log->is_sensitive = self::isSensitiveAction($log->action, $log->changed_fields);
            }
        });
    }

    // ====================================================================
    // RELATIONS
    // ====================================================================

    /**
     * Organisation propriétaire
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Dépense concernée
     */
    public function expense(): BelongsTo
    {
        return $this->belongsTo(VehicleExpense::class, 'vehicle_expense_id');
    }

    /**
     * Utilisateur qui a effectué l'action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Utilisateur qui a revu le log
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ====================================================================
    // SCOPES
    // ====================================================================

    /**
     * Logs par action
     */
    public function scopeByAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Logs par catégorie
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('action_category', $category);
    }

    /**
     * Logs par utilisateur
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Logs par dépense
     */
    public function scopeByExpense(Builder $query, int $expenseId): Builder
    {
        return $query->where('vehicle_expense_id', $expenseId);
    }

    /**
     * Logs nécessitant revue
     */
    public function scopeRequiringReview(Builder $query): Builder
    {
        return $query->where('requires_review', true)
                     ->where('reviewed', false);
    }

    /**
     * Logs avec anomalies
     */
    public function scopeWithAnomalies(Builder $query): Builder
    {
        return $query->where('is_anomaly', true);
    }

    /**
     * Logs par niveau de risque
     */
    public function scopeByRiskLevel(Builder $query, string $level): Builder
    {
        return $query->where('risk_level', $level);
    }

    /**
     * Logs sensibles
     */
    public function scopeSensitive(Builder $query): Builder
    {
        return $query->where('is_sensitive', true);
    }

    /**
     * Logs dans une période
     */
    public function scopeInPeriod(Builder $query, $start, $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Logs récents
     */
    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ====================================================================
    // MÉTHODES MÉTIER
    // ====================================================================

    /**
     * Créer un log d'audit
     */
    public static function log(
        VehicleExpense $expense,
        string $action,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        $changedFields = [];
        
        if ($oldValues && $newValues) {
            $changedFields = array_keys(array_diff_assoc($newValues, $oldValues));
        }

        return self::create([
            'organization_id' => $expense->organization_id,
            'vehicle_expense_id' => $expense->id,
            'user_id' => auth()->id() ?? $expense->recorded_by,
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changed_fields' => $changedFields,
            'previous_status' => $oldValues['approval_status'] ?? null,
            'new_status' => $newValues['approval_status'] ?? null,
            'previous_amount' => $oldValues['total_ttc'] ?? null,
            'new_amount' => $newValues['total_ttc'] ?? null
        ]);
    }

    /**
     * Marquer comme revu
     */
    public function markAsReviewed(string $notes = null): void
    {
        $this->reviewed = true;
        $this->reviewed_by = auth()->id();
        $this->reviewed_at = now();
        $this->review_notes = $notes;
        $this->save();
    }

    /**
     * Déterminer la catégorie d'une action
     */
    protected static function determineCategory(string $action): string
    {
        $categories = [
            self::CATEGORY_WORKFLOW => [
                self::ACTION_APPROVED,
                self::ACTION_REJECTED,
                self::ACTION_LEVEL1_APPROVED,
                self::ACTION_LEVEL2_APPROVED
            ],
            self::CATEGORY_FINANCIAL => [
                self::ACTION_PAID,
                self::ACTION_AUDITED
            ],
            self::CATEGORY_ADMINISTRATIVE => [
                self::ACTION_CREATED,
                self::ACTION_UPDATED,
                self::ACTION_DELETED,
                self::ACTION_RESTORED,
                self::ACTION_ARCHIVED
            ],
            self::CATEGORY_SYSTEM => [
                self::ACTION_IMPORTED,
                self::ACTION_EXPORTED
            ]
        ];

        foreach ($categories as $category => $actions) {
            if (in_array($action, $actions)) {
                return $category;
            }
        }

        return self::CATEGORY_ADMINISTRATIVE;
    }

    /**
     * Déterminer si une action est sensible
     */
    protected static function isSensitiveAction(string $action, array $changedFields = []): bool
    {
        // Actions toujours sensibles
        $sensitiveActions = [
            self::ACTION_DELETED,
            self::ACTION_PAID,
            self::ACTION_APPROVED,
            self::ACTION_LEVEL2_APPROVED
        ];

        if (in_array($action, $sensitiveActions)) {
            return true;
        }

        // Champs sensibles
        $sensitiveFields = [
            'total_ttc',
            'amount_ht',
            'payment_reference',
            'approved_by',
            'approval_status'
        ];

        return !empty(array_intersect($changedFields, $sensitiveFields));
    }

    /**
     * Détecter les anomalies
     */
    public function detectAnomalies(): void
    {
        $anomalies = [];
        $riskLevel = self::RISK_LOW;

        // Montant très élevé
        if ($this->new_amount && $this->new_amount > 1000000) {
            $anomalies[] = 'Montant très élevé';
            $riskLevel = self::RISK_HIGH;
        }

        // Approbation trop rapide
        if ($this->action === self::ACTION_APPROVED) {
            $expense = $this->expense;
            if ($expense && $expense->created_at->diffInMinutes($this->created_at) < 5) {
                $anomalies[] = 'Approbation très rapide';
                $riskLevel = self::RISK_MEDIUM;
            }
        }

        // Modification après approbation
        if ($this->action === self::ACTION_UPDATED && $this->previous_status === 'approved') {
            $anomalies[] = 'Modification après approbation';
            $riskLevel = self::RISK_HIGH;
        }

        // Multiple rejets
        $rejectCount = self::where('vehicle_expense_id', $this->vehicle_expense_id)
            ->where('action', self::ACTION_REJECTED)
            ->count();
        
        if ($rejectCount > 2) {
            $anomalies[] = 'Multiples rejets';
            $riskLevel = self::RISK_MEDIUM;
        }

        if (!empty($anomalies)) {
            $this->is_anomaly = true;
            $this->anomaly_details = implode('; ', $anomalies);
            $this->risk_level = $riskLevel;
            $this->requires_review = true;
            $this->save();
        }
    }

    /**
     * Obtenir un résumé de l'audit
     */
    public function getSummary(): string
    {
        $summary = sprintf(
            "[%s] %s: %s",
            $this->created_at->format('Y-m-d H:i'),
            $this->user->name ?? 'Système',
            $this->description
        );

        if ($this->new_amount && $this->previous_amount) {
            $diff = $this->new_amount - $this->previous_amount;
            $summary .= sprintf(" (Montant: %+.2f DZD)", $diff);
        }

        if ($this->is_anomaly) {
            $summary .= " ⚠️ ANOMALIE: " . $this->anomaly_details;
        }

        return $summary;
    }

    /**
     * Exporter pour rapport
     */
    public function toReportArray(): array
    {
        return [
            'date' => $this->created_at->format('Y-m-d H:i:s'),
            'user' => $this->user->name ?? 'Système',
            'action' => $this->action,
            'category' => $this->action_category,
            'description' => $this->description,
            'expense_id' => $this->vehicle_expense_id,
            'previous_status' => $this->previous_status,
            'new_status' => $this->new_status,
            'previous_amount' => $this->previous_amount,
            'new_amount' => $this->new_amount,
            'changed_fields' => implode(', ', $this->changed_fields ?? []),
            'ip_address' => $this->ip_address,
            'is_anomaly' => $this->is_anomaly ? 'Oui' : 'Non',
            'risk_level' => $this->risk_level,
            'reviewed' => $this->reviewed ? 'Oui' : 'Non',
            'reviewer' => $this->reviewer->name ?? '-'
        ];
    }
}
