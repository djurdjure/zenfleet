<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class VehicleExpense extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    // Constantes pour les catégories de dépenses
    public const CATEGORY_MAINTENANCE_PREVENTIVE = 'maintenance_preventive';
    public const CATEGORY_REPARATION = 'reparation';
    public const CATEGORY_PIECES_DETACHEES = 'pieces_detachees';
    public const CATEGORY_CARBURANT = 'carburant';
    public const CATEGORY_ASSURANCE = 'assurance';
    public const CATEGORY_CONTROLE_TECHNIQUE = 'controle_technique';
    public const CATEGORY_VIGNETTE = 'vignette';
    public const CATEGORY_AMENDES = 'amendes';
    public const CATEGORY_PEAGE = 'peage';
    public const CATEGORY_PARKING = 'parking';
    public const CATEGORY_LAVAGE = 'lavage';
    public const CATEGORY_TRANSPORT = 'transport';
    public const CATEGORY_FORMATION_CHAUFFEUR = 'formation_chauffeur';
    public const CATEGORY_AUTRE = 'autre';

    public const EXPENSE_CATEGORIES = [
        self::CATEGORY_MAINTENANCE_PREVENTIVE => 'Maintenance Préventive',
        self::CATEGORY_REPARATION => 'Réparation',
        self::CATEGORY_PIECES_DETACHEES => 'Pièces Détachées',
        self::CATEGORY_CARBURANT => 'Carburant',
        self::CATEGORY_ASSURANCE => 'Assurance',
        self::CATEGORY_CONTROLE_TECHNIQUE => 'Contrôle Technique',
        self::CATEGORY_VIGNETTE => 'Vignette',
        self::CATEGORY_AMENDES => 'Amendes',
        self::CATEGORY_PEAGE => 'Péage',
        self::CATEGORY_PARKING => 'Parking',
        self::CATEGORY_LAVAGE => 'Lavage',
        self::CATEGORY_TRANSPORT => 'Transport',
        self::CATEGORY_FORMATION_CHAUFFEUR => 'Formation Chauffeur',
        self::CATEGORY_AUTRE => 'Autre',
    ];

    // Statuts de paiement
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_REJECTED = 'rejected';

    // Méthodes de paiement DZ
    public const PAYMENT_VIREMENT = 'virement';
    public const PAYMENT_CHEQUE = 'cheque';
    public const PAYMENT_ESPECES = 'especes';
    public const PAYMENT_CARTE = 'carte';

    // Statuts d'approbation
    public const APPROVAL_DRAFT = 'draft';
    public const APPROVAL_PENDING_LEVEL1 = 'pending_level1';
    public const APPROVAL_PENDING_LEVEL2 = 'pending_level2';
    public const APPROVAL_APPROVED = 'approved';
    public const APPROVAL_REJECTED = 'rejected';

    // Types de carburant
    public const FUEL_ESSENCE = 'essence';
    public const FUEL_GASOIL = 'gasoil';
    public const FUEL_GPL = 'gpl';

    protected $fillable = [
        'organization_id',
        'vehicle_id',
        'supplier_id',
        'driver_id',
        'repair_request_id',
        'expense_group_id', // Nouveau
        'requester_id', // Nouveau
        'expense_category',
        'expense_type',
        'expense_subtype',
        'amount_ht',
        'tva_rate',
        'tva_amount',
        'total_ttc',
        'invoice_number',
        'invoice_date',
        'receipt_number',
        'fiscal_receipt',
        'odometer_reading',
        'fuel_quantity',
        'fuel_price_per_liter',
        'fuel_type',
        'expense_location',
        'expense_city',
        'expense_wilaya',
        'cost_center', // Nouveau
        'needs_approval',
        'priority_level', // Nouveau
        'is_urgent', // Nouveau
        'approval_deadline', // Nouveau
        'approval_comments',
        'approved',
        'approved_by',
        'approved_at',
        'level1_approved', // Nouveau
        'level1_approved_by', // Nouveau
        'level1_approved_at', // Nouveau
        'level1_comments', // Nouveau
        'level2_approved', // Nouveau
        'level2_approved_by', // Nouveau
        'level2_approved_at', // Nouveau
        'level2_comments', // Nouveau
        'approval_status', // Nouveau
        'is_rejected', // Nouveau
        'rejected_by', // Nouveau
        'rejected_at', // Nouveau
        'rejection_reason', // Nouveau
        'payment_status',
        'payment_method',
        'payment_date',
        'payment_due_date',
        'payment_reference',
        'external_reference', // Nouveau
        'recorded_by',
        'expense_date',
        'description',
        'internal_notes',
        'tags',
        'custom_fields',
        'attachments',
        'is_recurring',
        'recurrence_pattern',
        'next_due_date',
        'parent_expense_id',
        'requires_audit',
        'budget_allocated',
        'variance_percentage'
    ];

    protected $casts = [
        'amount_ht' => 'decimal:2',
        'tva_rate' => 'decimal:2',
        'tva_amount' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'fuel_quantity' => 'decimal:3',
        'fuel_price_per_liter' => 'decimal:3',
        'expense_date' => 'date',
        'approval_deadline' => 'date',
        'invoice_date' => 'date',
        'payment_date' => 'date',
        'payment_due_date' => 'date',
        'next_due_date' => 'date',
        'approved_at' => 'datetime',
        'audited_at' => 'datetime',
        'level1_approved_at' => 'datetime',
        'level2_approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'fiscal_receipt' => 'boolean',
        'needs_approval' => 'boolean',
        'approved' => 'boolean',
        'level1_approved' => 'boolean',
        'level2_approved' => 'boolean',
        'is_rejected' => 'boolean',
        'is_urgent' => 'boolean',
        'is_recurring' => 'boolean',
        'requires_audit' => 'boolean',
        'audited' => 'boolean',
        'tags' => 'array',
        'custom_fields' => 'array',
        'attachments' => 'array',
        'budget_allocated' => 'decimal:2',
        'variance_percentage' => 'decimal:2'
    ];

    protected $appends = [
        'tva_amount',
        'total_ttc',
        'category_label',
        'status_badge',
        'fuel_cost_per_100km'
    ];

    // Relations
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function repairRequest(): BelongsTo
    {
        return $this->belongsTo(RepairRequest::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function auditedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'audited_by');
    }

    /**
     * Groupe de dépenses
     */
    public function expenseGroup(): BelongsTo
    {
        return $this->belongsTo(ExpenseGroup::class, 'expense_group_id');
    }

    /**
     * Demandeur (qui a initié la dépense)
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Approbateur niveau 1
     */
    public function level1Approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'level1_approved_by');
    }

    /**
     * Approbateur niveau 2
     */
    public function level2Approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'level2_approved_by');
    }

    /**
     * Utilisateur qui a rejeté
     */
    public function rejectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function parentExpense(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_expense_id');
    }

    public function childExpenses(): HasMany
    {
        return $this->hasMany(self::class, 'parent_expense_id');
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(ExpenseBudget::class, function ($query) {
            $query->where('expense_category', $this->expense_category)
                  ->where('vehicle_id', $this->vehicle_id)
                  ->whereYear('budget_year', $this->expense_date->year);
        });
    }

    // Scopes
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('expense_category', $category);
    }

    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('needs_approval', true)
                    ->where('approved', false);
    }

    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', '!=', self::PAYMENT_PAID);
    }

    public function scopeFuelExpenses($query)
    {
        return $query->where('expense_category', self::CATEGORY_CARBURANT);
    }

    public function scopeMaintenanceExpenses($query)
    {
        return $query->whereIn('expense_category', [
            self::CATEGORY_MAINTENANCE_PREVENTIVE,
            self::CATEGORY_REPARATION,
            self::CATEGORY_PIECES_DETACHEES
        ]);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeRequiringAudit($query)
    {
        return $query->where('requires_audit', true)
                    ->where('audited', false);
    }

    public function scopeByWilaya($query, $wilaya)
    {
        return $query->where('expense_wilaya', $wilaya);
    }

    public function scopeOverBudget($query)
    {
        return $query->whereRaw('variance_percentage > 0');
    }

    // Méthodes de workflow
    public function requestApproval(): bool
    {
        return $this->update([
            'needs_approval' => true
        ]);
    }

    public function approve(User $approver, ?string $comments = null): bool
    {
        $this->update([
            'approved' => true,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_comments' => $comments
        ]);

        return true;
    }

    public function reject(User $approver, string $reason): bool
    {
        return $this->update([
            'approved' => false,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_comments' => $reason
        ]);
    }

    public function markAsPaid(?string $paymentMethod = null, ?string $reference = null): bool
    {
        return $this->update([
            'payment_status' => self::PAYMENT_PAID,
            'payment_date' => now(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $reference
        ]);
    }

    public function audit(User $auditor): bool
    {
        return $this->update([
            'audited' => true,
            'audited_by' => $auditor->id,
            'audited_at' => now()
        ]);
    }

    // Méthodes de récurrence
    public function createRecurringExpense(): ?VehicleExpense
    {
        if (!$this->is_recurring || !$this->next_due_date || $this->next_due_date->isFuture()) {
            return null;
        }

        $nextExpense = $this->replicate([
            'id',
            'expense_date',
            'invoice_number',
            'invoice_date',
            'receipt_number',
            'approved',
            'approved_by',
            'approved_at',
            'payment_status',
            'payment_date',
            'payment_reference',
            'created_at',
            'updated_at'
        ]);

        $nextExpense->parent_expense_id = $this->id;
        $nextExpense->expense_date = $this->next_due_date;
        $nextExpense->recorded_by = Auth::id();

        // Calculer la prochaine date d'échéance
        $nextDueDate = match($this->recurrence_pattern) {
            'monthly' => $this->next_due_date->addMonth(),
            'quarterly' => $this->next_due_date->addQuarter(),
            'yearly' => $this->next_due_date->addYear(),
            default => null
        };

        $nextExpense->next_due_date = $nextDueDate;
        $nextExpense->save();

        // Mettre à jour la dépense actuelle
        $this->update(['next_due_date' => $nextDueDate]);

        return $nextExpense;
    }

    // Méthodes de calcul et analyse
    public function calculateFuelEfficiency(): ?array
    {
        if ($this->expense_category !== self::CATEGORY_CARBURANT || !$this->fuel_quantity) {
            return null;
        }

        // Trouver la dernière dépense de carburant pour calculer la distance
        $lastFuelExpense = static::where('vehicle_id', $this->vehicle_id)
                                 ->where('expense_category', self::CATEGORY_CARBURANT)
                                 ->where('odometer_reading', '<', $this->odometer_reading)
                                 ->orderBy('odometer_reading', 'desc')
                                 ->first();

        if (!$lastFuelExpense || !$this->odometer_reading) {
            return null;
        }

        $distance = $this->odometer_reading - $lastFuelExpense->odometer_reading;

        if ($distance <= 0) {
            return null;
        }

        return [
            'distance_km' => $distance,
            'fuel_consumed_liters' => $this->fuel_quantity,
            'consumption_per_100km' => ($this->fuel_quantity / $distance) * 100,
            'cost_per_km' => $this->total_ttc / $distance,
            'cost_per_100km' => ($this->total_ttc / $distance) * 100
        ];
    }

    public function updateBudgetImpact(): void
    {
        $budget = ExpenseBudget::where('organization_id', $this->organization_id)
                              ->where('expense_category', $this->expense_category)
                              ->where(function ($query) {
                                  $query->whereNull('vehicle_id')
                                        ->orWhere('vehicle_id', $this->vehicle_id);
                              })
                              ->where('budget_year', $this->expense_date->year)
                              ->first();

        if ($budget) {
            $budget->recalculateSpentAmount();
        }
    }

    // Accesseurs
    public function getTvaAmountAttribute(): float
    {
        return round(($this->amount_ht * $this->tva_rate) / 100, 2);
    }

    public function getTotalTtcAttribute(): float
    {
        return $this->amount_ht + $this->tva_amount;
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->expense_category) {
            self::CATEGORY_MAINTENANCE_PREVENTIVE => 'Maintenance Préventive',
            self::CATEGORY_REPARATION => 'Réparation',
            self::CATEGORY_PIECES_DETACHEES => 'Pièces Détachées',
            self::CATEGORY_CARBURANT => 'Carburant',
            self::CATEGORY_ASSURANCE => 'Assurance',
            self::CATEGORY_CONTROLE_TECHNIQUE => 'Contrôle Technique',
            self::CATEGORY_VIGNETTE => 'Vignette',
            self::CATEGORY_AMENDES => 'Amendes',
            self::CATEGORY_PEAGE => 'Péage',
            self::CATEGORY_PARKING => 'Parking',
            self::CATEGORY_LAVAGE => 'Lavage',
            self::CATEGORY_TRANSPORT => 'Transport',
            self::CATEGORY_FORMATION_CHAUFFEUR => 'Formation Chauffeur',
            self::CATEGORY_AUTRE => 'Autre',
            default => 'Non défini'
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        if ($this->needs_approval && !$this->approved) {
            return ['color' => 'yellow', 'label' => 'En attente d\'approbation'];
        }

        if ($this->approved && $this->payment_status === self::PAYMENT_PAID) {
            return ['color' => 'green', 'label' => 'Payée'];
        }

        if ($this->approved && $this->payment_status === self::PAYMENT_PENDING) {
            return ['color' => 'blue', 'label' => 'À payer'];
        }

        if (!$this->approved) {
            return ['color' => 'red', 'label' => 'Rejetée'];
        }

        return ['color' => 'gray', 'label' => 'En cours'];
    }

    public function getFuelCostPer100kmAttribute(): ?float
    {
        $efficiency = $this->calculateFuelEfficiency();
        return $efficiency['cost_per_100km'] ?? null;
    }

    // Méthodes statiques utilitaires
    public static function getExpenseCategories(): array
    {
        return [
            self::CATEGORY_MAINTENANCE_PREVENTIVE => 'Maintenance Préventive',
            self::CATEGORY_REPARATION => 'Réparation',
            self::CATEGORY_PIECES_DETACHEES => 'Pièces Détachées',
            self::CATEGORY_CARBURANT => 'Carburant',
            self::CATEGORY_ASSURANCE => 'Assurance',
            self::CATEGORY_CONTROLE_TECHNIQUE => 'Contrôle Technique',
            self::CATEGORY_VIGNETTE => 'Vignette',
            self::CATEGORY_AMENDES => 'Amendes',
            self::CATEGORY_PEAGE => 'Péage',
            self::CATEGORY_PARKING => 'Parking',
            self::CATEGORY_LAVAGE => 'Lavage',
            self::CATEGORY_TRANSPORT => 'Transport',
            self::CATEGORY_FORMATION_CHAUFFEUR => 'Formation Chauffeur',
            self::CATEGORY_AUTRE => 'Autre'
        ];
    }

    public static function getPaymentMethods(): array
    {
        return [
            self::PAYMENT_VIREMENT => 'Virement bancaire',
            self::PAYMENT_CHEQUE => 'Chèque',
            self::PAYMENT_ESPECES => 'Espèces',
            self::PAYMENT_CARTE => 'Carte bancaire'
        ];
    }

    public static function getFuelTypes(): array
    {
        return [
            self::FUEL_ESSENCE => 'Essence',
            self::FUEL_GASOIL => 'Gasoil',
            self::FUEL_GPL => 'GPL'
        ];
    }

    public static function getRecurrencePatterns(): array
    {
        return [
            'monthly' => 'Mensuel',
            'quarterly' => 'Trimestriel',
            'yearly' => 'Annuel'
        ];
    }

    // Boot method pour les événements
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($expense) {
            if (!$expense->recorded_by) {
                $expense->recorded_by = Auth::id();
            }
        });

        static::created(function ($expense) {
            $expense->updateBudgetImpact();
        });

        static::updated(function ($expense) {
            if ($expense->wasChanged(['amount_ht', 'tva_rate', 'expense_category'])) {
                $expense->updateBudgetImpact();
            }
        });

        static::deleted(function ($expense) {
            $expense->updateBudgetImpact();
        });
    }
}
