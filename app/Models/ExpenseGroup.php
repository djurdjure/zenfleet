<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * ExpenseGroup Model - Groupement de dépenses pour analyse et budget
 * 
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $description
 * @property float $budget_allocated
 * @property float $budget_used
 * @property float $budget_remaining
 * @property int $fiscal_year
 * @property int|null $fiscal_quarter
 * @property int|null $fiscal_month
 * @property bool $is_active
 * @property bool $alert_on_threshold
 * @property float $alert_threshold_percentage
 * @property bool $block_on_exceeded
 * @property array $metadata
 * @property array $tags
 * @property array $responsible_users
 * @property int $created_by
 * @property int|null $updated_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * 
 * @property-read Organization $organization
 * @property-read User $creator
 * @property-read User|null $updater
 * @property-read \Illuminate\Database\Eloquent\Collection|VehicleExpense[] $expenses
 */
class ExpenseGroup extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'expense_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'budget_allocated',
        'budget_used',
        'fiscal_year',
        'fiscal_quarter',
        'fiscal_month',
        'is_active',
        'alert_on_threshold',
        'alert_threshold_percentage',
        'block_on_exceeded',
        'metadata',
        'tags',
        'responsible_users',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'budget_allocated' => 'decimal:2',
        'budget_used' => 'decimal:2',
        'budget_remaining' => 'decimal:2',
        'fiscal_year' => 'integer',
        'fiscal_quarter' => 'integer',
        'fiscal_month' => 'integer',
        'is_active' => 'boolean',
        'alert_on_threshold' => 'boolean',
        'alert_threshold_percentage' => 'decimal:2',
        'block_on_exceeded' => 'boolean',
        'metadata' => 'array',
        'tags' => 'array',
        'responsible_users' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array
     */
    protected $appends = [
        'budget_usage_percentage',
        'is_over_budget',
        'is_near_threshold',
        'fiscal_period_label'
    ];

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
     * Créateur du groupe
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Dernier utilisateur à avoir modifié
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Dépenses liées à ce groupe
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(VehicleExpense::class, 'expense_group_id');
    }

    /**
     * Utilisateurs responsables (relation via IDs dans JSON)
     */
    public function responsibleUsers()
    {
        return User::whereIn('id', $this->responsible_users ?? [])->get();
    }

    // ====================================================================
    // ACCESSORS
    // ====================================================================

    /**
     * Pourcentage d'utilisation du budget
     */
    public function getBudgetUsagePercentageAttribute(): float
    {
        if ($this->budget_allocated <= 0) {
            return 0;
        }
        
        return round(($this->budget_used / $this->budget_allocated) * 100, 2);
    }

    /**
     * Indique si le budget est dépassé
     */
    public function getIsOverBudgetAttribute(): bool
    {
        return $this->budget_used > $this->budget_allocated;
    }

    /**
     * Indique si on approche du seuil d'alerte
     */
    public function getIsNearThresholdAttribute(): bool
    {
        if (!$this->alert_on_threshold) {
            return false;
        }
        
        return $this->budget_usage_percentage >= $this->alert_threshold_percentage;
    }

    /**
     * Label de la période fiscale
     */
    public function getFiscalPeriodLabelAttribute(): string
    {
        $label = "Année {$this->fiscal_year}";
        
        if ($this->fiscal_quarter) {
            $label = "T{$this->fiscal_quarter} {$this->fiscal_year}";
        }
        
        if ($this->fiscal_month) {
            $months = [
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
                4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
                7 => 'Juillet', 8 => 'Août', 9 => 'Septembre',
                10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
            ];
            $label = ($months[$this->fiscal_month] ?? '') . " {$this->fiscal_year}";
        }
        
        return $label;
    }

    /**
     * Couleur du badge selon l'état du budget
     */
    public function getBudgetStatusColorAttribute(): string
    {
        if ($this->is_over_budget) {
            return 'red';
        }
        
        if ($this->is_near_threshold) {
            return 'yellow';
        }
        
        if ($this->budget_usage_percentage > 50) {
            return 'blue';
        }
        
        return 'green';
    }

    /**
     * Icône selon l'état du budget
     */
    public function getBudgetStatusIconAttribute(): string
    {
        if ($this->is_over_budget) {
            return 'heroicons:exclamation-circle';
        }
        
        if ($this->is_near_threshold) {
            return 'heroicons:exclamation-triangle';
        }
        
        return 'heroicons:check-circle';
    }

    // ====================================================================
    // SCOPES
    // ====================================================================

    /**
     * Groupes actifs uniquement
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Groupes de l'année fiscale courante
     */
    public function scopeCurrentYear(Builder $query): Builder
    {
        return $query->where('fiscal_year', date('Y'));
    }

    /**
     * Groupes d'une année spécifique
     */
    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->where('fiscal_year', $year);
    }

    /**
     * Groupes d'un trimestre spécifique
     */
    public function scopeForQuarter(Builder $query, int $year, int $quarter): Builder
    {
        return $query->where('fiscal_year', $year)
                     ->where('fiscal_quarter', $quarter);
    }

    /**
     * Groupes d'un mois spécifique
     */
    public function scopeForMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->where('fiscal_year', $year)
                     ->where('fiscal_month', $month);
    }

    /**
     * Groupes dépassant leur budget
     */
    public function scopeOverBudget(Builder $query): Builder
    {
        return $query->whereColumn('budget_used', '>', 'budget_allocated');
    }

    /**
     * Groupes approchant du seuil d'alerte
     */
    public function scopeNearThreshold(Builder $query): Builder
    {
        return $query->where('alert_on_threshold', true)
                     ->whereRaw('(budget_used / NULLIF(budget_allocated, 0)) * 100 >= alert_threshold_percentage');
    }

    /**
     * Recherche par nom ou description
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }
        
        $search = '%' . trim($search) . '%';
        
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'ILIKE', $search)
              ->orWhere('description', 'ILIKE', $search);
        });
    }

    /**
     * Filtrer par responsable
     */
    public function scopeByResponsible(Builder $query, int $userId): Builder
    {
        return $query->whereJsonContains('responsible_users', $userId);
    }

    /**
     * Trier par utilisation du budget
     */
    public function scopeOrderByBudgetUsage(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderByRaw("(budget_used / NULLIF(budget_allocated, 0)) $direction");
    }

    // ====================================================================
    // MÉTHODES MÉTIER
    // ====================================================================

    /**
     * Ajouter une dépense au groupe
     */
    public function addExpense(VehicleExpense $expense): void
    {
        $expense->expense_group_id = $this->id;
        $expense->save();
        
        $this->refreshBudgetUsed();
    }

    /**
     * Rafraîchir le budget utilisé
     */
    public function refreshBudgetUsed(): void
    {
        $this->budget_used = $this->expenses()
            ->whereNull('deleted_at')
            ->sum('total_ttc');
        
        $this->save();
    }

    /**
     * Vérifier si on peut ajouter une dépense
     */
    public function canAddExpense(float $amount): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        if ($this->block_on_exceeded && ($this->budget_used + $amount) > $this->budget_allocated) {
            return false;
        }
        
        return true;
    }

    /**
     * Obtenir les statistiques du groupe
     */
    public function getStatistics(): array
    {
        $expenses = $this->expenses()->whereNull('deleted_at');
        
        return [
            'total_expenses' => $expenses->count(),
            'budget_allocated' => $this->budget_allocated,
            'budget_used' => $this->budget_used,
            'budget_remaining' => $this->budget_remaining,
            'budget_usage_percentage' => $this->budget_usage_percentage,
            'average_expense' => $expenses->avg('total_ttc') ?? 0,
            'max_expense' => $expenses->max('total_ttc') ?? 0,
            'min_expense' => $expenses->min('total_ttc') ?? 0,
            'approved_count' => $expenses->where('approved', true)->count(),
            'pending_count' => $expenses->where('needs_approval', true)->where('approved', false)->count(),
            'categories' => $expenses->groupBy('expense_category')
                ->selectRaw('expense_category, COUNT(*) as count, SUM(total_ttc) as total')
                ->get()
        ];
    }

    /**
     * Allouer un budget supplémentaire
     */
    public function allocateAdditionalBudget(float $amount, string $reason = null): void
    {
        $this->budget_allocated += $amount;
        
        if ($reason) {
            $metadata = $this->metadata ?? [];
            $metadata['budget_adjustments'] = $metadata['budget_adjustments'] ?? [];
            $metadata['budget_adjustments'][] = [
                'amount' => $amount,
                'reason' => $reason,
                'date' => now()->toIso8601String(),
                'by' => auth()->id()
            ];
            $this->metadata = $metadata;
        }
        
        $this->save();
    }

    /**
     * Vérifier et envoyer les alertes si nécessaire
     */
    public function checkAndSendAlerts(): void
    {
        if (!$this->alert_on_threshold) {
            return;
        }
        
        if ($this->is_near_threshold) {
            // Envoyer notification aux responsables
            foreach ($this->responsibleUsers() as $user) {
                // TODO: Implémenter notification
                // $user->notify(new BudgetThresholdReached($this));
            }
        }
        
        if ($this->is_over_budget) {
            // Envoyer alerte critique
            foreach ($this->responsibleUsers() as $user) {
                // TODO: Implémenter notification
                // $user->notify(new BudgetExceeded($this));
            }
        }
    }

    /**
     * Dupliquer le groupe pour une nouvelle période
     */
    public function duplicateForPeriod(int $year, ?int $quarter = null, ?int $month = null): self
    {
        $newGroup = $this->replicate();
        $newGroup->fiscal_year = $year;
        $newGroup->fiscal_quarter = $quarter;
        $newGroup->fiscal_month = $month;
        $newGroup->budget_used = 0;
        $newGroup->name = $this->name . " - " . $year;
        $newGroup->created_by = auth()->id();
        $newGroup->save();
        
        return $newGroup;
    }

    /**
     * Archiver le groupe (soft delete avec actions supplémentaires)
     */
    public function archive(): void
    {
        $this->is_active = false;
        $this->save();
        $this->delete();
    }

    // ====================================================================
    // MÉTHODES DE PRÉSENTATION
    // ====================================================================

    /**
     * Formater pour l'affichage
     */
    public function toDisplayArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'fiscal_period' => $this->fiscal_period_label,
            'budget_allocated' => number_format($this->budget_allocated, 2) . ' DZD',
            'budget_used' => number_format($this->budget_used, 2) . ' DZD',
            'budget_remaining' => number_format($this->budget_remaining, 2) . ' DZD',
            'budget_usage' => $this->budget_usage_percentage . '%',
            'status' => [
                'color' => $this->budget_status_color,
                'icon' => $this->budget_status_icon,
                'is_over' => $this->is_over_budget,
                'is_near_threshold' => $this->is_near_threshold
            ],
            'expenses_count' => $this->expenses()->count(),
            'is_active' => $this->is_active
        ];
    }
}
