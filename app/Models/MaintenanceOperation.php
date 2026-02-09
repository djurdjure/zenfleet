<?php

namespace App\Models;

use App\Support\Analytics\AnalyticsCacheVersion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * Modèle MaintenanceOperation - Gestion des opérations de maintenance
 *
 * @property int $id
 * @property int $organization_id
 * @property int $vehicle_id
 * @property int $maintenance_type_id
 * @property int|null $maintenance_schedule_id
 * @property int|null $provider_id
 * @property string $status
 * @property \Carbon\Carbon|null $scheduled_date
 * @property \Carbon\Carbon|null $completed_date
 * @property int|null $mileage_at_maintenance
 * @property int|null $duration_minutes
 * @property float|null $total_cost
 * @property string|null $description
 * @property string|null $notes
 * @property int $created_by
 * @property int|null $updated_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class MaintenanceOperation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'vehicle_id',
        'maintenance_type_id',
        'maintenance_schedule_id',
        'provider_id',
        'status',
        'scheduled_date',
        'completed_date',
        'mileage_at_maintenance',
        'duration_minutes',
        'total_cost',
        'description',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'total_cost' => 'decimal:2',
        'mileage_at_maintenance' => 'integer',
        'duration_minutes' => 'integer',
    ];

    /**
     * Statuts disponibles
     */
    public const STATUS_PLANNED = 'planned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PLANNED => 'Planifiée',
        self::STATUS_IN_PROGRESS => 'En cours',
        self::STATUS_COMPLETED => 'Terminée',
        self::STATUS_CANCELLED => 'Annulée',
    ];

    /**
     * Boot du modèle pour appliquer les scopes globaux et events
     */
    protected static function booted(): void
    {
        // Scope global multi-tenant
        static::addGlobalScope('organization', function (Builder $builder) {
            if (auth()->check() && auth()->user()->organization_id) {
                $builder->where('organization_id', auth()->user()->organization_id);
            }
        });

        // Event pour mettre à jour automatiquement updated_by
        static::updating(function ($operation) {
            if (auth()->check()) {
                $operation->updated_by = auth()->id();
            }
        });

        // Event pour recalculer la prochaine maintenance après completion
        static::updated(function ($operation) {
            if ($operation->isDirty('status') && $operation->status === self::STATUS_COMPLETED) {
                $operation->handleCompletion();
            }

            AnalyticsCacheVersion::bump('maintenance', $operation->organization_id);
        });

        static::created(function ($operation) {
            AnalyticsCacheVersion::bump('maintenance', $operation->organization_id);
        });

        static::deleted(function ($operation) {
            AnalyticsCacheVersion::bump('maintenance', $operation->organization_id);
        });

        static::restored(function ($operation) {
            AnalyticsCacheVersion::bump('maintenance', $operation->organization_id);
        });
    }

    /**
     * Relation avec l'organisation (multi-tenant)
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Relation avec le véhicule
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relation avec le type de maintenance
     */
    public function maintenanceType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    /**
     * Relation avec la planification (optionnelle)
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'maintenance_schedule_id');
    }

    /**
     * Relation avec le fournisseur (optionnel)
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'provider_id');
    }

    /**
     * Relation avec les documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(MaintenanceDocument::class);
    }

    /**
     * Relation avec l'utilisateur créateur
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur modificateur
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeByStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }

    /**
     * Scope pour les opérations complétées
     */
    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope pour les opérations en cours
     */
    public function scopeInProgress(Builder $query): void
    {
        $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope pour les opérations planifiées
     */
    public function scopePlanned(Builder $query): void
    {
        $query->where('status', self::STATUS_PLANNED);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeBetweenDates(Builder $query, Carbon $startDate, Carbon $endDate): void
    {
        $query->whereBetween('scheduled_date', [$startDate, $endDate]);
    }

    /**
     * Scope pour filtrer par véhicule
     */
    public function scopeForVehicle(Builder $query, int $vehicleId): void
    {
        $query->where('vehicle_id', $vehicleId);
    }

    /**
     * Scope pour filtrer par type de maintenance
     */
    public function scopeForMaintenanceType(Builder $query, int $typeId): void
    {
        $query->where('maintenance_type_id', $typeId);
    }

    /**
     * Scope pour ordonner par date planifiée
     */
    public function scopeOrderByScheduled(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('scheduled_date', $direction);
    }

    /**
     * Scope pour les opérations récentes
     */
    public function scopeRecent(Builder $query, int $days = 30): void
    {
        $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Accessor pour le nom du statut
     */
    protected function statusName(): Attribute
    {
        return Attribute::make(
            get: fn () => self::STATUSES[$this->status] ?? $this->status
        );
    }

    /**
     * Accessor pour la durée formatée
     */
    protected function formattedDuration(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->duration_minutes) {
                    return null;
                }

                $hours = intval($this->duration_minutes / 60);
                $minutes = $this->duration_minutes % 60;

                if ($hours > 0 && $minutes > 0) {
                    return "{$hours}h {$minutes}min";
                } elseif ($hours > 0) {
                    return "{$hours}h";
                } else {
                    return "{$minutes}min";
                }
            }
        );
    }

    /**
     * Accessor pour le coût formaté
     */
    protected function formattedCost(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_cost ? number_format($this->total_cost, 2, ',', ' ') . ' DA' : null
        );
    }

    /**
     * Accessor pour vérifier si l'opération est en retard
     */
    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->status === self::STATUS_PLANNED &&
                       $this->scheduled_date &&
                       $this->scheduled_date->lt(Carbon::today());
            }
        );
    }

    /**
     * Méthode pour obtenir le badge de statut avec couleur
     */
    public function getStatusBadge(): string
    {
        $statusConfig = [
            self::STATUS_PLANNED => ['class' => 'bg-blue-100 text-blue-800'],
            self::STATUS_IN_PROGRESS => ['class' => 'bg-yellow-100 text-yellow-800'],
            self::STATUS_COMPLETED => ['class' => 'bg-green-100 text-green-800'],
            self::STATUS_CANCELLED => ['class' => 'bg-red-100 text-red-800'],
        ];

        $config = $statusConfig[$this->status] ?? $statusConfig[self::STATUS_PLANNED];
        $name = $this->status_name;

        $badge = "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$config['class']}\">{$name}</span>";

        // Ajouter un badge "En retard" si nécessaire
        if ($this->is_overdue) {
            $badge .= ' <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-1">En retard</span>';
        }

        return $badge;
    }

    /**
     * Méthode pour démarrer l'opération
     */
    public function start(): bool
    {
        if ($this->status !== self::STATUS_PLANNED) {
            return false;
        }

        return $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'scheduled_date' => $this->scheduled_date ?? Carbon::today(),
        ]);
    }

    /**
     * Méthode pour terminer l'opération
     */
    public function complete(array $data = []): bool
    {
        if (!in_array($this->status, [self::STATUS_PLANNED, self::STATUS_IN_PROGRESS])) {
            return false;
        }

        $updateData = array_merge([
            'status' => self::STATUS_COMPLETED,
            'completed_date' => Carbon::today(),
        ], $data);

        return $this->update($updateData);
    }

    /**
     * Méthode pour annuler l'opération
     */
    public function cancel(): bool
    {
        if ($this->status === self::STATUS_COMPLETED) {
            return false;
        }

        return $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * Méthode appelée après completion pour mettre à jour les planifications
     */
    protected function handleCompletion(): void
    {
        if ($this->schedule && $this->schedule->is_active) {
            // Recalculer la prochaine échéance
            $this->schedule->calculateNextDue(
                $this->completed_date ?? Carbon::today(),
                $this->mileage_at_maintenance ?? $this->vehicle->current_mileage
            );

            // Mettre à jour le kilométrage du véhicule si fourni
            if ($this->mileage_at_maintenance && $this->mileage_at_maintenance > $this->vehicle->current_mileage) {
                $this->vehicle->update(['current_mileage' => $this->mileage_at_maintenance]);
            }
        }
    }

    /**
     * Méthode pour obtenir les statistiques de l'opération
     */
    public function getStats(): array
    {
        return [
            'duration_vs_estimated' => $this->maintenanceType->estimated_duration_minutes && $this->duration_minutes
                ? ($this->duration_minutes / $this->maintenanceType->estimated_duration_minutes) * 100
                : null,
            'cost_vs_estimated' => $this->maintenanceType->estimated_cost && $this->total_cost
                ? ($this->total_cost / $this->maintenanceType->estimated_cost) * 100
                : null,
            'documents_count' => $this->documents()->count(),
            'days_from_scheduled' => $this->completed_date && $this->scheduled_date
                ? $this->scheduled_date->diffInDays($this->completed_date, false)
                : null,
        ];
    }

    /**
     * Validation rules pour le modèle
     */
    public static function validationRules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'maintenance_schedule_id' => 'nullable|exists:maintenance_schedules,id',
            'provider_id' => 'nullable|exists:suppliers,id',
            'status' => 'required|in:' . implode(',', array_keys(self::STATUSES)),
            'scheduled_date' => 'nullable|date',
            'completed_date' => 'nullable|date|after_or_equal:scheduled_date',
            'mileage_at_maintenance' => 'nullable|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:1|max:14400',
            'total_cost' => 'nullable|numeric|min:0|max:999999.99',
            'description' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    public static function validationMessages(): array
    {
        return [
            'vehicle_id.required' => 'Le véhicule est obligatoire.',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            'maintenance_type_id.required' => 'Le type de maintenance est obligatoire.',
            'maintenance_type_id.exists' => 'Le type de maintenance sélectionné n\'existe pas.',
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut sélectionné n\'est pas valide.',
            'completed_date.after_or_equal' => 'La date de completion ne peut pas être antérieure à la date planifiée.',
            'duration_minutes.min' => 'La durée doit être d\'au moins 1 minute.',
            'total_cost.numeric' => 'Le coût total doit être un nombre valide.',
            'total_cost.min' => 'Le coût total ne peut pas être négatif.',
        ];
    }
}
