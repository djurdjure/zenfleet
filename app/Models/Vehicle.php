<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Maintenance\MaintenanceLog;
use App\Models\Maintenance\MaintenancePlan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
// CORRECTION : Ajout des bons namespaces pour les relations
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'registration_plate', 'vin', 'brand', 'model', 'color', 'vehicle_type_id',
        'fuel_type_id', 'transmission_type_id', 'status_id', 'manufacturing_year',
        'acquisition_date', 'purchase_price', 'current_value', 'initial_mileage',
        'current_mileage', 'engine_displacement_cc', 'power_hp', 'seats', 'status_reason', 'notes', 'organization_id',
        'vehicle_name', 'category_id', 'depot_id',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'current_mileage' => 'integer',
        'initial_mileage' => 'integer',
        'manufacturing_year' => 'integer',
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
        'engine_displacement_cc' => 'integer',
        'power_hp' => 'integer',
        'seats' => 'integer',
    ];

    // CORRECTION : Ajout du bon type de retour (BelongsTo)
    public function vehicleType(): BelongsTo { return $this->belongsTo(VehicleType::class); }
    public function fuelType(): BelongsTo { return $this->belongsTo(FuelType::class); }
    public function transmissionType(): BelongsTo { return $this->belongsTo(TransmissionType::class); }
    public function vehicleStatus(): BelongsTo { return $this->belongsTo(VehicleStatus::class, 'status_id'); }
    public function category(): BelongsTo { return $this->belongsTo(VehicleCategory::class); }
    public function depot(): BelongsTo { return $this->belongsTo(VehicleDepot::class); }

    // CORRECTION : Ajout du bon type de retour (HasMany)
    public function assignments(): HasMany { return $this->hasMany(Assignment::class); }
    public function maintenancePlans(): HasMany { return $this->hasMany(MaintenancePlan::class); }
    public function maintenanceLogs(): HasMany { return $this->hasMany(MaintenanceLog::class); }
    public function repairRequests(): HasMany { return $this->hasMany(RepairRequest::class); }
    public function mileageReadings(): HasMany { return $this->hasMany(VehicleMileageReading::class); }

    // =========================================================================
    // MILEAGE MANAGEMENT METHODS
    // =========================================================================

    /**
     * Obtient le dernier relevé kilométrique enregistré.
     *
     * @return VehicleMileageReading|null
     */
    public function getLatestMileageReading(): ?VehicleMileageReading
    {
        return $this->mileageReadings()
            ->latest('recorded_at')
            ->first();
    }

    /**
     * Obtient le kilométrage total parcouru depuis l'acquisition.
     *
     * @return int
     */
    public function getTotalMileageDriven(): int
    {
        if (!$this->current_mileage || !$this->initial_mileage) {
            return 0;
        }

        return max(0, $this->current_mileage - $this->initial_mileage);
    }

    /**
     * Accesseur: Kilométrage formaté avec séparateur de milliers.
     *
     * @return string
     */
    public function getFormattedCurrentMileageAttribute(): string
    {
        return number_format($this->current_mileage ?? 0, 0, ',', ' ') . ' km';
    }

    /**
     * Accesseur: Kilométrage initial formaté avec séparateur de milliers.
     *
     * @return string
     */
    public function getFormattedInitialMileageAttribute(): string
    {
        return number_format($this->initial_mileage ?? 0, 0, ',', ' ') . ' km';
    }

    /**
     * Accesseur: Kilométrage total parcouru formaté.
     *
     * @return string
     */
    public function getFormattedTotalMileageAttribute(): string
    {
        return number_format($this->getTotalMileageDriven(), 0, ',', ' ') . ' km';
    }

    /**
     * Vérifie si le véhicule nécessite un relevé kilométrique.
     * Recommandé si aucun relevé depuis plus de 30 jours.
     *
     * @return bool
     */
    public function needsMileageReading(): bool
    {
        $latestReading = $this->getLatestMileageReading();

        if (!$latestReading) {
            return true;
        }

        return $latestReading->recorded_at->diffInDays(now()) > 30;
    }

    /**
     * Calcule le kilométrage moyen journalier sur une période.
     *
     * @param \Carbon\Carbon|null $startDate
     * @param \Carbon\Carbon|null $endDate
     * @return float
     */
    public function getAverageDailyMileage($startDate = null, $endDate = null): float
    {
        return VehicleMileageReading::calculateAverageDailyMileage(
            $this->id,
            $startDate ?? now()->subDays(30),
            $endDate ?? now()
        );
    }

    /**
     * Met à jour manuellement le current_mileage.
     *
     * ⚠️ ATTENTION: Cette méthode est publique mais devrait être utilisée avec précaution.
     * L'Observer VehicleMileageReadingObserver gère automatiquement les mises à jour
     * lors de la création/modification de relevés kilométriques.
     *
     * Utilisez cette méthode UNIQUEMENT pour:
     * - Corrections administratives exceptionnelles
     * - Migrations de données
     * - Opérations de maintenance système
     *
     * @param int $newMileage
     * @param bool $skipValidation Ne pas valider si nouveau kilométrage > actuel
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function updateMileage(int $newMileage, bool $skipValidation = false): bool
    {
        if ($newMileage < 0) {
            throw new \InvalidArgumentException("Le kilométrage ne peut pas être négatif");
        }

        if (!$skipValidation && $newMileage < $this->current_mileage) {
            throw new \InvalidArgumentException(
                "Le nouveau kilométrage ({$newMileage} km) ne peut pas être inférieur au kilométrage actuel ({$this->current_mileage} km)"
            );
        }

        $this->current_mileage = $newMileage;
        return $this->save();
    }

    /**
     * Synchronise le current_mileage avec le dernier relevé enregistré.
     *
     * Utilisé par VehicleMileageReadingObserver pour maintenir la cohérence.
     * Cette méthode est appelée automatiquement, ne pas appeler manuellement.
     *
     * @internal Utilisé uniquement par VehicleMileageReadingObserver
     * @param int $mileage
     * @return void
     */
    public function syncCurrentMileageFromReading(int $mileage): void
    {
        // Mise à jour sans déclencher les événements ni les timestamps
        $this->timestamps = false;
        $this->current_mileage = $mileage;
        $this->save();
        $this->timestamps = true;
    }

    /**
     * 🔧 Relation avec les opérations de maintenance - ENTERPRISE GRADE
     */
    public function maintenanceOperations(): HasMany
    {
        return $this->hasMany(MaintenanceOperation::class);
    }

    /**
     * 🔧 Relation avec les opérations de maintenance actives
     */
    public function activeMaintenanceOperations(): HasMany
    {
        return $this->hasMany(MaintenanceOperation::class)
                    ->whereIn('status', [
                        MaintenanceOperation::STATUS_PLANNED,
                        MaintenanceOperation::STATUS_IN_PROGRESS
                    ]);
    }

    /**
     * 🔧 Relation avec les opérations de maintenance récentes (30 derniers jours)
     */
    public function recentMaintenanceOperations(): HasMany
    {
        return $this->hasMany(MaintenanceOperation::class)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->orderBy('created_at', 'desc');
    }
    
    /**
     * Vérifie si le véhicule a une affectation actuellement en cours.
     */
    public function isCurrentlyAssigned(): bool
    {
       return $this->assignments()->whereNull('end_datetime')->exists();
    }

    /**
     * 🔧 Vérifie si le véhicule est actuellement en maintenance - ENTERPRISE GRADE
     */
    public function isUnderMaintenance(): bool
    {
        return $this->activeMaintenanceOperations()->exists();
    }

    /**
     * 🔧 Obtient la prochaine maintenance planifiée - ENTERPRISE GRADE
     */
    public function getNextMaintenance()
    {
        return $this->maintenanceOperations()
                    ->where('status', MaintenanceOperation::STATUS_PLANNED)
                    ->whereDate('scheduled_date', '>=', now()->toDateString())
                    ->orderBy('scheduled_date')
                    ->first();
    }

    /**
     * 🔧 Obtient le coût total de maintenance pour une période - ENTERPRISE GRADE
     */
    public function getMaintenanceCost($startDate = null, $endDate = null): float
    {
        $query = $this->maintenanceOperations()
                      ->where('status', MaintenanceOperation::STATUS_COMPLETED);

        if ($startDate) {
            $query->whereDate('completed_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('completed_date', '<=', $endDate);
        }

        return $query->sum('total_cost') ?? 0.0;
    }

    /**
     * 🔧 Obtient les statistiques de maintenance enterprise - ENTERPRISE GRADE
     */
    public function getMaintenanceStats(): array
    {
        $totalOperations = $this->maintenanceOperations()->count();
        $completedOperations = $this->maintenanceOperations()
            ->where('status', MaintenanceOperation::STATUS_COMPLETED)
            ->count();

        $averageCost = $this->maintenanceOperations()
            ->where('status', MaintenanceOperation::STATUS_COMPLETED)
            ->avg('total_cost') ?? 0;

        $lastMaintenance = $this->maintenanceOperations()
            ->where('status', MaintenanceOperation::STATUS_COMPLETED)
            ->orderBy('completed_date', 'desc')
            ->first();

        return [
            'total_operations' => $totalOperations,
            'completed_operations' => $completedOperations,
            'completion_rate' => $totalOperations > 0 ? ($completedOperations / $totalOperations) * 100 : 0,
            'average_cost' => round($averageCost, 2),
            'total_cost_ytd' => $this->getMaintenanceCost(now()->startOfYear()),
            'last_maintenance_date' => $lastMaintenance?->completed_date,
            'days_since_last_maintenance' => $lastMaintenance
                ? now()->diffInDays($lastMaintenance->completed_date)
                : null,
            'is_under_maintenance' => $this->isUnderMaintenance(),
            'next_maintenance' => $this->getNextMaintenance()?->scheduled_date,
        ];
    }

    /**
     * La relation qui retourne les utilisateurs autorisés à utiliser ce véhicule.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_vehicle');
    }
}