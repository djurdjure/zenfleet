<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * Modèle MaintenanceAlert - Gestion des alertes de maintenance
 *
 * @property int $id
 * @property int $organization_id
 * @property int $vehicle_id
 * @property int $maintenance_schedule_id
 * @property string $alert_type
 * @property string $priority
 * @property string $message
 * @property \Carbon\Carbon|null $due_date
 * @property int|null $due_mileage
 * @property bool $is_acknowledged
 * @property int|null $acknowledged_by
 * @property \Carbon\Carbon|null $acknowledged_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class MaintenanceAlert extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'vehicle_id',
        'maintenance_schedule_id',
        'alert_type',
        'priority',
        'message',
        'due_date',
        'due_mileage',
        'is_acknowledged',
        'acknowledged_by',
        'acknowledged_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_acknowledged' => 'boolean',
        'acknowledged_at' => 'datetime',
        'due_mileage' => 'integer',
    ];

    /**
     * Types d'alertes disponibles
     */
    public const TYPE_KM_BASED = 'km_based';
    public const TYPE_TIME_BASED = 'time_based';
    public const TYPE_OVERDUE = 'overdue';

    public const TYPES = [
        self::TYPE_KM_BASED => 'Basée sur le kilométrage',
        self::TYPE_TIME_BASED => 'Basée sur le temps',
        self::TYPE_OVERDUE => 'En retard',
    ];

    /**
     * Priorités disponibles
     */
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_CRITICAL = 'critical';

    public const PRIORITIES = [
        self::PRIORITY_LOW => 'Faible',
        self::PRIORITY_MEDIUM => 'Moyenne',
        self::PRIORITY_HIGH => 'Haute',
        self::PRIORITY_CRITICAL => 'Critique',
    ];

    /**
     * Boot du modèle pour appliquer les scopes globaux
     */
    protected static function booted(): void
    {
        // Scope global multi-tenant
        static::addGlobalScope('organization', function (Builder $builder) {
            if (auth()->check() && auth()->user()->organization_id) {
                $builder->where('organization_id', auth()->user()->organization_id);
            }
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
     * Relation avec la planification de maintenance
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'maintenance_schedule_id');
    }

    /**
     * Relation avec l'utilisateur qui a acquitté l'alerte
     */
    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Scope pour filtrer les alertes non acquittées
     */
    public function scopeUnacknowledged(Builder $query): void
    {
        $query->where('is_acknowledged', false);
    }

    /**
     * Scope pour filtrer les alertes acquittées
     */
    public function scopeAcknowledged(Builder $query): void
    {
        $query->where('is_acknowledged', true);
    }

    /**
     * Scope pour filtrer par priorité
     */
    public function scopeByPriority(Builder $query, string $priority): void
    {
        $query->where('priority', $priority);
    }

    /**
     * Scope pour filtrer par type d'alerte
     */
    public function scopeByType(Builder $query, string $type): void
    {
        $query->where('alert_type', $type);
    }

    /**
     * Scope pour filtrer les alertes critiques
     */
    public function scopeCritical(Builder $query): void
    {
        $query->where('priority', self::PRIORITY_CRITICAL);
    }

    /**
     * Scope pour filtrer les alertes hautes priorités et critiques
     */
    public function scopeHighPriority(Builder $query): void
    {
        $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_CRITICAL]);
    }

    /**
     * Scope pour ordonner par priorité (critique en premier)
     */
    public function scopeOrderByPriority(Builder $query): void
    {
        $query->orderByRaw('
            CASE priority
                WHEN \'critical\' THEN 1
                WHEN \'high\' THEN 2
                WHEN \'medium\' THEN 3
                WHEN \'low\' THEN 4
                ELSE 5
            END,
            created_at DESC
        ');
    }

    /**
     * Scope pour filtrer par véhicule
     */
    public function scopeForVehicle(Builder $query, int $vehicleId): void
    {
        $query->where('vehicle_id', $vehicleId);
    }

    /**
     * Scope pour filtrer les alertes récentes
     */
    public function scopeRecent(Builder $query, int $days = 7): void
    {
        $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Accessor pour le nom de la priorité
     */
    protected function priorityName(): Attribute
    {
        return Attribute::make(
            get: fn () => self::PRIORITIES[$this->priority] ?? $this->priority
        );
    }

    /**
     * Accessor pour le nom du type
     */
    protected function typeName(): Attribute
    {
        return Attribute::make(
            get: fn () => self::TYPES[$this->alert_type] ?? $this->alert_type
        );
    }

    /**
     * Accessor pour l'âge de l'alerte en heures
     */
    protected function ageInHours(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at->diffInHours(now())
        );
    }

    /**
     * Accessor pour vérifier si l'alerte est récente (moins de 24h)
     */
    protected function isRecent(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->age_in_hours < 24
        );
    }

    /**
     * Méthode pour obtenir le badge de priorité avec couleur
     */
    public function getPriorityBadge(): string
    {
        $priorityConfig = [
            self::PRIORITY_LOW => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fas fa-info-circle'],
            self::PRIORITY_MEDIUM => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-exclamation-circle'],
            self::PRIORITY_HIGH => ['class' => 'bg-orange-100 text-orange-800', 'icon' => 'fas fa-exclamation-triangle'],
            self::PRIORITY_CRITICAL => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-exclamation-triangle'],
        ];

        $config = $priorityConfig[$this->priority] ?? $priorityConfig[self::PRIORITY_MEDIUM];
        $name = $this->priority_name;

        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$config['class']}\">
                    <i class=\"{$config['icon']} mr-1\"></i>{$name}
                </span>";
    }

    /**
     * Méthode pour obtenir le badge du type d'alerte
     */
    public function getTypeBadge(): string
    {
        $typeConfig = [
            self::TYPE_KM_BASED => ['class' => 'bg-purple-100 text-purple-800', 'icon' => 'fas fa-tachometer-alt'],
            self::TYPE_TIME_BASED => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-clock'],
            self::TYPE_OVERDUE => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-exclamation'],
        ];

        $config = $typeConfig[$this->alert_type] ?? $typeConfig[self::TYPE_TIME_BASED];
        $name = $this->type_name;

        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$config['class']}\">
                    <i class=\"{$config['icon']} mr-1\"></i>{$name}
                </span>";
    }

    /**
     * Méthode pour acquitter l'alerte
     */
    public function acknowledge(int $userId = null): bool
    {
        if ($this->is_acknowledged) {
            return false;
        }

        return $this->update([
            'is_acknowledged' => true,
            'acknowledged_by' => $userId ?? auth()->id(),
            'acknowledged_at' => now(),
        ]);
    }

    /**
     * Méthode pour réactiver l'alerte (désacquitter)
     */
    public function unacknowledge(): bool
    {
        if (!$this->is_acknowledged) {
            return false;
        }

        return $this->update([
            'is_acknowledged' => false,
            'acknowledged_by' => null,
            'acknowledged_at' => null,
        ]);
    }

    /**
     * Méthode pour déterminer si l'alerte nécessite une escalade
     */
    public function needsEscalation(): bool
    {
        if ($this->is_acknowledged) {
            return false;
        }

        $escalationRules = [
            self::PRIORITY_CRITICAL => 2, // 2 heures
            self::PRIORITY_HIGH => 8,     // 8 heures
            self::PRIORITY_MEDIUM => 24,  // 24 heures
            self::PRIORITY_LOW => 72,     // 72 heures
        ];

        $maxHours = $escalationRules[$this->priority] ?? 24;

        return $this->age_in_hours >= $maxHours;
    }

    /**
     * Méthode pour formater le message avec les détails de l'échéance
     */
    public function getFormattedMessage(): string
    {
        $message = $this->message;

        if ($this->due_date || $this->due_mileage) {
            $dueInfo = [];

            if ($this->due_date) {
                $daysUntilDue = Carbon::today()->diffInDays($this->due_date, false);
                if ($daysUntilDue < 0) {
                    $dueInfo[] = "En retard de " . abs($daysUntilDue) . " jour(s)";
                } elseif ($daysUntilDue === 0) {
                    $dueInfo[] = "Due aujourd'hui";
                } else {
                    $dueInfo[] = "Due dans {$daysUntilDue} jour(s)";
                }
            }

            if ($this->due_mileage && $this->vehicle) {
                $kmRemaining = $this->due_mileage - ($this->vehicle->current_mileage ?? 0);
                if ($kmRemaining < 0) {
                    $dueInfo[] = "Dépassé de " . number_format(abs($kmRemaining), 0, ',', ' ') . " km";
                } else {
                    $dueInfo[] = number_format($kmRemaining, 0, ',', ' ') . " km restants";
                }
            }

            if (!empty($dueInfo)) {
                $message .= " (" . implode(', ', $dueInfo) . ")";
            }
        }

        return $message;
    }

    /**
     * Méthode statique pour créer des alertes en lot
     */
    public static function createBulkAlerts(array $schedules): int
    {
        $created = 0;

        foreach ($schedules as $schedule) {
            if ($schedule->createAlertIfNeeded()) {
                $created++;
            }
        }

        return $created;
    }

    /**
     * Validation rules pour le modèle
     */
    public static function validationRules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_schedule_id' => 'required|exists:maintenance_schedules,id',
            'alert_type' => 'required|in:' . implode(',', array_keys(self::TYPES)),
            'priority' => 'required|in:' . implode(',', array_keys(self::PRIORITIES)),
            'message' => 'required|string|max:1000',
            'due_date' => 'nullable|date',
            'due_mileage' => 'nullable|integer|min:0',
            'is_acknowledged' => 'boolean',
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
            'maintenance_schedule_id.required' => 'La planification de maintenance est obligatoire.',
            'maintenance_schedule_id.exists' => 'La planification de maintenance sélectionnée n\'existe pas.',
            'alert_type.required' => 'Le type d\'alerte est obligatoire.',
            'alert_type.in' => 'Le type d\'alerte sélectionné n\'est pas valide.',
            'priority.required' => 'La priorité est obligatoire.',
            'priority.in' => 'La priorité sélectionnée n\'est pas valide.',
            'message.required' => 'Le message d\'alerte est obligatoire.',
            'message.max' => 'Le message ne peut pas dépasser 1000 caractères.',
            'due_mileage.min' => 'Le kilométrage d\'échéance ne peut pas être négatif.',
        ];
    }
}