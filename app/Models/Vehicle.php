<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasStatusBadge;
use App\Models\Maintenance\MaintenanceLog;
use App\Models\Maintenance\MaintenancePlan;
use App\Models\Scopes\UserVehicleAccessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
// CORRECTION : Ajout des bons namespaces pour les relations
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization, HasStatusBadge;

    private static array $resolvedStatusIdsCache = [];
    private static array $statusByIdCache = [];
    private static ?bool $vehicleStatusesHasOrgColumn = null;

    /**
     * 🔒 The "booted" method of the model.
     * Applique le Global Scope pour le contrôle d'accès utilisateur.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new UserVehicleAccessScope);
    }

    protected $fillable = [
        'registration_plate',
        'vin',
        'brand',
        'model',
        'color',
        'vehicle_type_id',
        'fuel_type_id',
        'transmission_type_id',
        'status_id',
        'manufacturing_year',
        'acquisition_date',
        'purchase_price',
        'current_value',
        'initial_mileage',
        'current_mileage',
        'engine_displacement_cc',
        'power_hp',
        'seats',
        'status_reason',
        'notes',
        'organization_id',
        'vehicle_name',
        'category_id',
        'depot_id',
        'is_archived',
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
        'is_archived' => 'boolean',
    ];

    // CORRECTION : Ajout du bon type de retour (BelongsTo)
    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }
    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }
    public function transmissionType(): BelongsTo
    {
        return $this->belongsTo(TransmissionType::class);
    }
    public function vehicleStatus(): BelongsTo
    {
        return $this->belongsTo(VehicleStatus::class, 'status_id');
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class);
    }
    public function depot(): BelongsTo
    {
        return $this->belongsTo(VehicleDepot::class);
    }

    // CORRECTION : Ajout du bon type de retour (HasMany)
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * 👤 Relation avec l'affectation actuelle (Active)
     * Optimisé pour éviter le N+1 problem avec limit(1) dans eager loading
     */
    public function currentAssignment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Assignment::class)->ofMany([
            'start_datetime' => 'max',
            'id' => 'max',
        ], function ($query) {
            $query->whereNull('deleted_at')
                ->where('status', 'active')
                ->where('start_datetime', '<=', now())
                ->where(function ($q) {
                    $q->whereNull('end_datetime')
                        ->orWhere('end_datetime', '>=', now());
                });
        });
    }
    public function maintenancePlans(): HasMany
    {
        return $this->hasMany(MaintenancePlan::class);
    }
    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }
    public function expenses(): HasMany
    {
        return $this->hasMany(VehicleExpense::class);
    }
    public function repairRequests(): HasMany
    {
        return $this->hasMany(RepairRequest::class);
    }
    public function mileageReadings(): HasMany
    {
        return $this->hasMany(VehicleMileageReading::class);
    }
    public function depotAssignmentHistory(): HasMany
    {
        return $this->hasMany(DepotAssignmentHistory::class);
    }

    /**
     * 📊 Relation polymorphique avec l'historique des statuts
     */
    public function statusHistory(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'statusable')->orderBy('changed_at', 'desc');
    }

    /**
     * 📊 Obtient l'historique récent des changements de statut (30 derniers jours)
     */
    public function recentStatusHistory(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'statusable')
            ->where('changed_at', '>=', now()->subDays(30))
            ->orderBy('changed_at', 'desc');
    }

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
     * Inclut les métadonnées de la table pivot pour tracer l'accès.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_vehicle')
            ->withPivot('granted_at', 'granted_by', 'access_type')
            ->withTimestamps();
    }

    // =========================================================================
    // QUERY SCOPES - ENTERPRISE GRADE
    // =========================================================================

    /**
     * 🎯 SCOPE: Véhicules actifs uniquement
     *
     * Filtre les véhicules avec status_id = 1 (Actif)
     *
     * Usage: Vehicle::active()->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        $organizationId = auth()->user()?->organization_id;
        $statusIds = self::resolveStatusIds(
            ['parking', 'actif', 'active'],
            ['Parking', 'Actif', 'Active'],
            $organizationId
        );

        if (empty($statusIds)) {
            Log::warning('[Vehicle] Aucun statut actif résolu - scopeActive() ignoré', [
                'organization_id' => $organizationId,
            ]);
            return $query;
        }

        return $query->whereIn('status_id', $statusIds);
    }

    /**
     * 🔧 SCOPE: Véhicules en maintenance
     *
     * Filtre les véhicules avec status_id = 2 (En maintenance)
     *
     * Usage: Vehicle::inMaintenance()->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInMaintenance($query)
    {
        $organizationId = auth()->user()?->organization_id;
        $statusIds = self::resolveStatusIds(
            ['en_maintenance', 'maintenance'],
            ['En maintenance', 'Maintenance'],
            $organizationId
        );

        if (empty($statusIds)) {
            Log::warning('[Vehicle] Aucun statut maintenance résolu - scopeInMaintenance() ignoré', [
                'organization_id' => $organizationId,
            ]);
            return $query;
        }

        return $query->whereIn('status_id', $statusIds);
    }

    /**
     * ⛔ SCOPE: Véhicules inactifs
     *
     * Filtre les véhicules avec status_id = 3 (Inactif)
     *
     * Usage: Vehicle::inactive()->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        $organizationId = auth()->user()?->organization_id;
        $statusIds = self::resolveStatusIds(
            ['inactif', 'inactive', 'reforme', 'hors_service', 'hors-service', 'archive', 'archived'],
            ['Inactif', 'Inactive', 'Réformé', 'Reforme', 'Hors service', 'Archivé', 'Archive'],
            $organizationId
        );

        if (empty($statusIds)) {
            Log::warning('[Vehicle] Aucun statut inactif résolu - scopeInactive() ignoré', [
                'organization_id' => $organizationId,
            ]);
            return $query;
        }

        return $query->whereIn('status_id', $statusIds);
    }

    /**
     * 🎯 SCOPE: Véhicules par statut ID
     *
     * Filtre les véhicules par un statut spécifique
     *
     * Usage: Vehicle::byStatus(1)->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $statusId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, int $statusId)
    {
        return $query->where('status_id', $statusId);
    }

    /**
     * 🏢 SCOPE: Véhicules disponibles pour affectation
     *
     * Retourne les véhicules actifs qui n'ont pas d'affectation en cours
     *
     * Usage: Vehicle::availableForAssignment()->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailableForAssignment($query)
    {
        return $query->active()
            ->whereDoesntHave('assignments', function ($q) {
                $q->where('status', 'active')
                    ->where('end_datetime', '>', now());
            });
    }

    // =========================================================================
    // SCOPES - ARCHIVAGE
    // =========================================================================

    /**
     * Scope pour récupérer uniquement les véhicules non archivés (visibles)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Scope pour récupérer uniquement les véhicules archivés
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    /**
     * Scope pour inclure ou exclure les véhicules archivés selon le paramètre
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool|null $include
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithArchived($query, $include = true)
    {
        if (!$include) {
            return $query->where('is_archived', false);
        }
        return $query;
    }

    // =========================================================================
    // HELPER METHODS - STATUS CHECKS
    // =========================================================================

    /**
     * ✅ Vérifie si le véhicule est actif
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->matchesStatus(['parking', 'actif', 'active'], ['Parking', 'Actif', 'Active']);
    }

    /**
     * 🔧 Vérifie si le véhicule est en maintenance
     *
     * @return bool
     */
    public function isInMaintenance(): bool
    {
        return $this->matchesStatus(['en_maintenance', 'maintenance'], ['En maintenance', 'Maintenance']);
    }

    /**
     * ⛔ Vérifie si le véhicule est inactif
     *
     * @return bool
     */
    public function isInactive(): bool
    {
        return $this->matchesStatus(
            ['inactif', 'inactive', 'reforme', 'hors_service', 'hors-service', 'archive', 'archived'],
            ['Inactif', 'Inactive', 'Réformé', 'Reforme', 'Hors service', 'Archivé', 'Archive']
        );
    }

    /**
     * 🎨 Retourne le nom du statut
     *
     * @return string
     */
    public function getStatusName(): string
    {
        if ($this->relationLoaded('vehicleStatus') && $this->vehicleStatus) {
            return $this->vehicleStatus->name;
        }

        $status = self::resolveStatusById($this->status_id);
        return $status?->name ?? 'Inconnu';
    }

    /**
     * 🎨 Retourne la classe CSS pour le badge de statut
     *
     * @return string
     */
    public function getStatusBadgeClass(): string
    {
        if ($this->relationLoaded('vehicleStatus') && $this->vehicleStatus) {
            return $this->vehicleStatus->badge_class;
        }

        $status = self::resolveStatusById($this->status_id);
        return $status?->badge_class ?? 'bg-gray-100 text-gray-800';
    }

    private static function resolveStatusIds(array $slugs, array $names, ?int $organizationId = null): array
    {
        $slugs = self::normalizeStatusSlugs($slugs);
        $names = array_values(array_unique(array_filter($names)));

        if (empty($slugs) && empty($names)) {
            return [];
        }

        $cacheKey = ($organizationId ?? 'global') . ':' . implode('|', $slugs) . ':' . implode('|', $names);

        if (array_key_exists($cacheKey, self::$resolvedStatusIdsCache)) {
            return self::$resolvedStatusIdsCache[$cacheKey];
        }

        $query = VehicleStatus::query()
            ->where(function ($q) use ($slugs, $names) {
                if (!empty($slugs)) {
                    $q->whereIn('slug', $slugs);
                }
                if (!empty($names)) {
                    if (!empty($slugs)) {
                        $q->orWhereIn('name', $names);
                    } else {
                        $q->whereIn('name', $names);
                    }
                }
            });

        if ($organizationId !== null && self::vehicleStatusesHaveOrgColumn()) {
            $query->where(function ($q) use ($organizationId) {
                $q->whereNull('organization_id')
                    ->orWhere('organization_id', $organizationId);
            });
        }

        $ids = $query->orderBy('id')->pluck('id')->all();
        self::$resolvedStatusIdsCache[$cacheKey] = $ids;

        return $ids;
    }

    private static function resolveStatusById(?int $statusId): ?VehicleStatus
    {
        if (!$statusId) {
            return null;
        }

        if (array_key_exists($statusId, self::$statusByIdCache)) {
            return self::$statusByIdCache[$statusId];
        }

        $status = VehicleStatus::query()->find($statusId);
        self::$statusByIdCache[$statusId] = $status;

        return $status;
    }

    private static function normalizeStatusSlugs(array $slugs): array
    {
        $normalized = [];

        foreach ($slugs as $slug) {
            if (!is_string($slug) || $slug === '') {
                continue;
            }
            $normalized[] = $slug;
            if (str_contains($slug, '_')) {
                $normalized[] = str_replace('_', '-', $slug);
            }
            if (str_contains($slug, '-')) {
                $normalized[] = str_replace('-', '_', $slug);
            }
        }

        return array_values(array_unique($normalized));
    }

    private static function vehicleStatusesHaveOrgColumn(): bool
    {
        if (self::$vehicleStatusesHasOrgColumn === null) {
            self::$vehicleStatusesHasOrgColumn = Schema::hasColumn('vehicle_statuses', 'organization_id');
        }

        return self::$vehicleStatusesHasOrgColumn;
    }

    private function matchesStatus(array $slugs, array $names): bool
    {
        $slugs = self::normalizeStatusSlugs($slugs);

        if ($this->vehicleStatus) {
            $statusSlug = $this->vehicleStatus->slug;
            if ($statusSlug && in_array($statusSlug, $slugs, true)) {
                return true;
            }

            $statusName = $this->vehicleStatus->name;
            if ($statusName && in_array($statusName, $names, true)) {
                return true;
            }
        }

        if (!$this->status_id) {
            return false;
        }

        $statusIds = self::resolveStatusIds($slugs, $names, $this->organization_id);
        return in_array($this->status_id, $statusIds, true);
    }
}
