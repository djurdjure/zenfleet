<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

/**
 * Modèle MaintenanceSchedule - Gestion de la planification des maintenances
 *
 * @property int $id
 * @property int $organization_id
 * @property int $vehicle_id
 * @property int $maintenance_type_id
 * @property \Carbon\Carbon|null $next_due_date
 * @property int|null $next_due_mileage
 * @property int|null $interval_km
 * @property int|null $interval_days
 * @property int $alert_km_before
 * @property int $alert_days_before
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'vehicle_id',
        'maintenance_type_id',
        'next_due_date',
        'next_due_mileage',
        'interval_km',
        'interval_days',
        'alert_km_before',
        'alert_days_before',
        'is_active',
    ];

    protected $casts = [
        'next_due_date' => 'date',
        'is_active' => 'boolean',
        'next_due_mileage' => 'integer',
        'interval_km' => 'integer',
        'interval_days' => 'integer',
        'alert_km_before' => 'integer',
        'alert_days_before' => 'integer',
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
     * Relation avec le type de maintenance
     */
    public function maintenanceType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    /**
     * Relation avec les opérations de maintenance
     */
    public function operations(): HasMany
    {
        return $this->hasMany(MaintenanceOperation::class);
    }

    /**
     * Relation avec les alertes
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(MaintenanceAlert::class);
    }

    /**
     * Scope pour filtrer les planifications actives
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope pour les maintenances dues
     */
    public function scopeDue(Builder $query): void
    {
        $query->where(function ($q) {
            $q->where('next_due_date', '<=', now())
              ->orWhereRaw('next_due_mileage <= (SELECT current_mileage FROM vehicles WHERE vehicles.id = maintenance_schedules.vehicle_id)');
        });
    }

    /**
     * Scope pour les maintenances en retard
     */
    public function scopeOverdue(Builder $query): void
    {
        $query->where(function ($q) {
            $q->where('next_due_date', '<', now())
              ->orWhereRaw('next_due_mileage < (SELECT current_mileage FROM vehicles WHERE vehicles.id = maintenance_schedules.vehicle_id)');
        });
    }

    /**
     * Scope pour les alertes à déclencher
     */
    public function scopeNeedingAlert(Builder $query): void
    {
        $query->where(function ($q) {
            // Alerte basée sur la date
            $q->where('next_due_date', '<=', now()->addDays(DB::raw('alert_days_before')))
              // Alerte basée sur le kilométrage
              ->orWhereRaw('next_due_mileage <= (SELECT current_mileage FROM vehicles WHERE vehicles.id = maintenance_schedules.vehicle_id) + alert_km_before');
        });
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
     * Scope pour ordonner par urgence
     */
    public function scopeOrderByUrgency(Builder $query): void
    {
        $query->orderByRaw('
            CASE
                WHEN next_due_date < CURRENT_DATE THEN 1
                WHEN next_due_mileage < (SELECT current_mileage FROM vehicles WHERE vehicles.id = maintenance_schedules.vehicle_id) THEN 1
                WHEN next_due_date <= CURRENT_DATE + INTERVAL \'7 days\' THEN 2
                WHEN next_due_mileage <= (SELECT current_mileage FROM vehicles WHERE vehicles.id = maintenance_schedules.vehicle_id) + 1000 THEN 2
                ELSE 3
            END,
            next_due_date ASC
        ');
    }

    /**
     * Accessor pour le statut de la maintenance
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->is_active) {
                    return 'inactive';
                }

                $today = Carbon::today();
                $currentMileage = $this->vehicle?->current_mileage ?? 0;

                // Vérifier si en retard
                if (($this->next_due_date && $this->next_due_date->lt($today)) ||
                    ($this->next_due_mileage && $this->next_due_mileage < $currentMileage)) {
                    return 'overdue';
                }

                // Vérifier si due bientôt
                $alertDate = $today->addDays($this->alert_days_before);
                $alertMileage = $currentMileage + $this->alert_km_before;

                if (($this->next_due_date && $this->next_due_date->lte($alertDate)) ||
                    ($this->next_due_mileage && $this->next_due_mileage <= $alertMileage)) {
                    return 'due_soon';
                }

                return 'scheduled';
            }
        );
    }

    /**
     * Accessor pour le nombre de jours restants
     */
    protected function daysRemaining(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->next_due_date) {
                    return null;
                }

                return Carbon::today()->diffInDays($this->next_due_date, false);
            }
        );
    }

    /**
     * Accessor pour le kilométrage restant
     */
    protected function kilometersRemaining(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->next_due_mileage || !$this->vehicle) {
                    return null;
                }

                return $this->next_due_mileage - ($this->vehicle->current_mileage ?? 0);
            }
        );
    }

    /**
     * Méthode pour obtenir le badge de statut avec couleur
     */
    public function getStatusBadge(): string
    {
        $statusConfig = [
            'overdue' => ['text' => 'En retard', 'class' => 'bg-red-100 text-red-800'],
            'due_soon' => ['text' => 'Bientôt due', 'class' => 'bg-orange-100 text-orange-800'],
            'scheduled' => ['text' => 'Planifiée', 'class' => 'bg-green-100 text-green-800'],
            'inactive' => ['text' => 'Inactive', 'class' => 'bg-gray-100 text-gray-800'],
        ];

        $config = $statusConfig[$this->status] ?? $statusConfig['scheduled'];

        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$config['class']}\">{$config['text']}</span>";
    }

    /**
     * Méthode pour calculer la prochaine échéance après maintenance
     */
    public function calculateNextDue(Carbon $maintenanceDate = null, int $maintenanceMileage = null): void
    {
        $baseDate = $maintenanceDate ?? Carbon::today();
        $baseMileage = $maintenanceMileage ?? ($this->vehicle?->current_mileage ?? 0);

        $nextDueDate = null;
        $nextDueMileage = null;

        if ($this->interval_days) {
            $nextDueDate = $baseDate->copy()->addDays($this->interval_days);
        }

        if ($this->interval_km) {
            $nextDueMileage = $baseMileage + $this->interval_km;
        }

        $this->update([
            'next_due_date' => $nextDueDate,
            'next_due_mileage' => $nextDueMileage,
        ]);
    }

    /**
     * Méthode pour créer une alerte si nécessaire
     */
    public function createAlertIfNeeded(): ?MaintenanceAlert
    {
        if ($this->status === 'scheduled' || $this->status === 'inactive') {
            return null;
        }

        // Vérifier si une alerte existe déjà et n'est pas acquittée
        $existingAlert = $this->alerts()
            ->where('is_acknowledged', false)
            ->latest()
            ->first();

        if ($existingAlert) {
            return $existingAlert;
        }

        $alertType = 'time_based';
        $priority = 'medium';
        $message = '';

        if ($this->status === 'overdue') {
            $alertType = 'overdue';
            $priority = 'high';
            $message = "Maintenance en retard pour {$this->vehicle->registration_plate} - {$this->maintenanceType->name}";
        } elseif ($this->status === 'due_soon') {
            if ($this->kilometers_remaining !== null && $this->kilometers_remaining <= $this->alert_km_before) {
                $alertType = 'km_based';
                $message = "Maintenance due dans {$this->kilometers_remaining} km pour {$this->vehicle->registration_plate} - {$this->maintenanceType->name}";
            } else {
                $message = "Maintenance due dans {$this->days_remaining} jour(s) pour {$this->vehicle->registration_plate} - {$this->maintenanceType->name}";
            }
        }

        return MaintenanceAlert::create([
            'organization_id' => $this->organization_id,
            'vehicle_id' => $this->vehicle_id,
            'maintenance_schedule_id' => $this->id,
            'alert_type' => $alertType,
            'priority' => $priority,
            'message' => $message,
            'due_date' => $this->next_due_date,
            'due_mileage' => $this->next_due_mileage,
        ]);
    }

    /**
     * Validation rules pour le modèle
     */
    public static function validationRules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'next_due_date' => 'nullable|date|after:today',
            'next_due_mileage' => 'nullable|integer|min:0',
            'interval_km' => 'nullable|integer|min:1|max:1000000',
            'interval_days' => 'nullable|integer|min:1|max:3650',
            'alert_km_before' => 'integer|min:0|max:50000',
            'alert_days_before' => 'integer|min:0|max:365',
            'is_active' => 'boolean',
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
            'next_due_date.after' => 'La prochaine échéance doit être dans le futur.',
            'interval_km.min' => 'L\'intervalle en kilomètres doit être d\'au moins 1 km.',
            'interval_days.min' => 'L\'intervalle en jours doit être d\'au moins 1 jour.',
        ];
    }
}