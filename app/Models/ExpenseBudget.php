<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseBudget extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    public const PERIOD_MONTHLY = 'monthly';
    public const PERIOD_QUARTERLY = 'quarterly';
    public const PERIOD_YEARLY = 'yearly';

    protected $fillable = [
        'organization_id',
        'vehicle_id',
        'expense_category',
        'budget_period',
        'budget_year',
        'budget_month',
        'budget_quarter',
        'budgeted_amount',
        'spent_amount',
        'warning_threshold',
        'critical_threshold',
        'description',
        'approval_workflow',
        'is_active'
    ];

    protected $casts = [
        'budgeted_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'variance_percentage' => 'decimal:2',
        'warning_threshold' => 'decimal:2',
        'critical_threshold' => 'decimal:2',
        'approval_workflow' => 'array',
        'is_active' => 'boolean'
    ];

    protected $appends = [
        'remaining_amount',
        'variance_percentage',
        'status',
        'utilization_percentage'
    ];

    // Relations
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(VehicleExpense::class, function ($query) {
            $query->where('expense_category', $this->expense_category);

            if ($this->vehicle_id) {
                $query->where('vehicle_id', $this->vehicle_id);
            }

            // Filtrer par période
            if ($this->budget_period === self::PERIOD_MONTHLY) {
                $query->whereYear('expense_date', $this->budget_year)
                      ->whereMonth('expense_date', $this->budget_month);
            } elseif ($this->budget_period === self::PERIOD_QUARTERLY) {
                $startMonth = ($this->budget_quarter - 1) * 3 + 1;
                $endMonth = $this->budget_quarter * 3;
                $query->whereYear('expense_date', $this->budget_year)
                      ->whereBetween(DB::raw('MONTH(expense_date)'), [$startMonth, $endMonth]);
            } else {
                $query->whereYear('expense_date', $this->budget_year);
            }
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForPeriod($query, $period, $year, $month = null, $quarter = null)
    {
        return $query->where('budget_period', $period)
                    ->where('budget_year', $year)
                    ->when($month, function ($q, $month) {
                        return $q->where('budget_month', $month);
                    })
                    ->when($quarter, function ($q, $quarter) {
                        return $q->where('budget_quarter', $quarter);
                    });
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('expense_category', $category);
    }

    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeOverWarningThreshold($query)
    {
        return $query->whereRaw('(spent_amount / budgeted_amount * 100) >= warning_threshold');
    }

    public function scopeOverCriticalThreshold($query)
    {
        return $query->whereRaw('(spent_amount / budgeted_amount * 100) >= critical_threshold');
    }

    public function scopeOverBudget($query)
    {
        return $query->whereRaw('spent_amount > budgeted_amount');
    }

    // Méthodes de calcul
    public function recalculateSpentAmount(): void
    {
        $totalSpent = $this->expenses()->approved()->sum('total_ttc');

        $this->update([
            'spent_amount' => $totalSpent
        ]);
    }

    public function getRemainingAmount(): float
    {
        return $this->budgeted_amount - $this->spent_amount;
    }

    public function getVariancePercentage(): float
    {
        if ($this->budgeted_amount == 0) {
            return 0;
        }

        return (($this->spent_amount - $this->budgeted_amount) / $this->budgeted_amount) * 100;
    }

    public function getUtilizationPercentage(): float
    {
        if ($this->budgeted_amount == 0) {
            return 0;
        }

        return ($this->spent_amount / $this->budgeted_amount) * 100;
    }

    public function isOverWarningThreshold(): bool
    {
        return $this->getUtilizationPercentage() >= $this->warning_threshold;
    }

    public function isOverCriticalThreshold(): bool
    {
        return $this->getUtilizationPercentage() >= $this->critical_threshold;
    }

    public function isOverBudget(): bool
    {
        return $this->spent_amount > $this->budgeted_amount;
    }

    // Accesseurs
    public function getRemainingAmountAttribute(): float
    {
        return $this->getRemainingAmount();
    }

    public function getVariancePercentageAttribute(): float
    {
        return $this->getVariancePercentage();
    }

    public function getUtilizationPercentageAttribute(): float
    {
        return $this->getUtilizationPercentage();
    }

    public function getStatusAttribute(): array
    {
        if ($this->isOverBudget()) {
            return ['color' => 'red', 'label' => 'Dépassé', 'icon' => 'exclamation-triangle'];
        }

        if ($this->isOverCriticalThreshold()) {
            return ['color' => 'orange', 'label' => 'Critique', 'icon' => 'exclamation'];
        }

        if ($this->isOverWarningThreshold()) {
            return ['color' => 'yellow', 'label' => 'Attention', 'icon' => 'warning'];
        }

        return ['color' => 'green', 'label' => 'OK', 'icon' => 'check'];
    }

    public function getPeriodLabelAttribute(): string
    {
        return match($this->budget_period) {
            self::PERIOD_MONTHLY => "Mois {$this->budget_month}/{$this->budget_year}",
            self::PERIOD_QUARTERLY => "T{$this->budget_quarter} {$this->budget_year}",
            self::PERIOD_YEARLY => "Année {$this->budget_year}",
            default => 'Période non définie'
        };
    }

    public function getScopeDescriptionAttribute(): string
    {
        $scope = [];

        if ($this->vehicle_id) {
            $scope[] = "Véhicule: {$this->vehicle->registration_plate}";
        } else {
            $scope[] = "Tous véhicules";
        }

        if ($this->expense_category) {
            $categories = VehicleExpense::getExpenseCategories();
            $scope[] = "Catégorie: {$categories[$this->expense_category]}";
        } else {
            $scope[] = "Toutes catégories";
        }

        return implode(' - ', $scope);
    }

    // Méthodes statiques
    public static function createMonthlyBudget($organizationId, $year, $month, $category = null, $vehicleId = null, $amount = 0): self
    {
        return static::create([
            'organization_id' => $organizationId,
            'vehicle_id' => $vehicleId,
            'expense_category' => $category,
            'budget_period' => self::PERIOD_MONTHLY,
            'budget_year' => $year,
            'budget_month' => $month,
            'budgeted_amount' => $amount
        ]);
    }

    public static function createQuarterlyBudget($organizationId, $year, $quarter, $category = null, $vehicleId = null, $amount = 0): self
    {
        return static::create([
            'organization_id' => $organizationId,
            'vehicle_id' => $vehicleId,
            'expense_category' => $category,
            'budget_period' => self::PERIOD_QUARTERLY,
            'budget_year' => $year,
            'budget_quarter' => $quarter,
            'budgeted_amount' => $amount
        ]);
    }

    public static function createYearlyBudget($organizationId, $year, $category = null, $vehicleId = null, $amount = 0): self
    {
        return static::create([
            'organization_id' => $organizationId,
            'vehicle_id' => $vehicleId,
            'expense_category' => $category,
            'budget_period' => self::PERIOD_YEARLY,
            'budget_year' => $year,
            'budgeted_amount' => $amount
        ]);
    }

    public static function getBudgetPeriods(): array
    {
        return [
            self::PERIOD_MONTHLY => 'Mensuel',
            self::PERIOD_QUARTERLY => 'Trimestriel',
            self::PERIOD_YEARLY => 'Annuel'
        ];
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::created(function ($budget) {
            $budget->recalculateSpentAmount();
        });
    }
}