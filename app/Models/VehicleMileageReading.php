<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * VehicleMileageReading Model
 *
 * Modèle pour les relevés kilométriques des véhicules.
 * Supporte les relevés manuels et automatiques avec validation stricte.
 *
 * @property int $id
 * @property int $organization_id
 * @property int $vehicle_id
 * @property string $recorded_at
 * @property int $mileage
 * @property int|null $recorded_by_id
 * @property string $recording_method
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Organization $organization
 * @property-read Vehicle $vehicle
 * @property-read User|null $recordedBy
 *
 * @version 1.0-Enterprise
 */
class VehicleMileageReading extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicle_mileage_readings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_id',
        'vehicle_id',
        'recorded_at',
        'mileage',
        'recorded_by_id',
        'recording_method',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'recorded_at' => 'datetime',
        'mileage' => 'integer',
        'recording_method' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<string>
     */
    protected $hidden = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'is_manual',
        'is_automatic',
    ];

    // =========================================================================
    // ENUMS & CONSTANTS
    // =========================================================================

    public const METHOD_MANUAL = 'manual';
    public const METHOD_AUTOMATIC = 'automatic';

    public const METHODS = [
        self::METHOD_MANUAL,
        self::METHOD_AUTOMATIC,
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Relation: Organisation propriétaire (multi-tenant)
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Relation: Véhicule concerné
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relation: Utilisateur ayant enregistré le relevé (alias: recordedBy)
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }

    /**
     * Relation: Utilisateur ayant enregistré le relevé (alias: recorder)
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }

    // =========================================================================
    // SCOPES - Query Optimization
    // =========================================================================

    /**
     * Scope: Filtrer par organisation (multi-tenant isolation)
     */
    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope: Filtrer par véhicule
     */
    public function scopeForVehicle($query, int $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    /**
     * Scope: Filtrer par méthode d'enregistrement
     */
    public function scopeByMethod($query, string $method)
    {
        return $query->where('recording_method', $method);
    }

    /**
     * Scope: Relevés manuels uniquement
     */
    public function scopeManualOnly($query)
    {
        return $query->where('recording_method', self::METHOD_MANUAL);
    }

    /**
     * Scope: Relevés automatiques uniquement
     */
    public function scopeAutomaticOnly($query)
    {
        return $query->where('recording_method', self::METHOD_AUTOMATIC);
    }

    /**
     * Scope: Filtrer par période
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('recorded_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Trier par date de relevé (plus récent en premier)
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('recorded_at', 'desc');
    }

    /**
     * Scope: Trier par date de relevé (plus ancien en premier)
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('recorded_at', 'asc');
    }

    /**
     * Scope: Ajouter la colonne 'previous_mileage' via une sous-requête corrélée.
     * 
     * ENTERPRISE GRADE: Utilise du SQL brut pour éviter les conflits d'alias
     * Eloquent lors de la corrélation entre requête principale et sous-requête.
     * 
     * La sous-requête cherche le kilométrage du relevé PRÉCÉDENT pour le même véhicule,
     * en utilisant recorded_at DESC + id DESC comme critère de tri pour gérer
     * les cas où plusieurs relevés ont lieu au même moment.
     */
    public function scopeWithPreviousMileage($query)
    {
        return $query->addSelect(
            DB::raw("(
                SELECT prev.mileage 
                FROM vehicle_mileage_readings AS prev 
                WHERE prev.vehicle_id = vehicle_mileage_readings.vehicle_id 
                AND (
                    prev.recorded_at < vehicle_mileage_readings.recorded_at 
                    OR (
                        prev.recorded_at = vehicle_mileage_readings.recorded_at 
                        AND prev.id < vehicle_mileage_readings.id
                    )
                )
                ORDER BY prev.recorded_at DESC, prev.id DESC 
                LIMIT 1
            ) AS previous_mileage")
        );
    }

    /**
     * Scope: Inclure les relations fréquemment utilisées
     */
    public function scopeWithRelations($query)
    {
        return $query->with(['vehicle', 'organization', 'recordedBy']);
    }

    // =========================================================================
    // ACCESSORS & MUTATORS
    // =========================================================================

    /**
     * Accessor: Vérifier si le relevé est manuel
     */
    public function getIsManualAttribute(): bool
    {
        return $this->recording_method === self::METHOD_MANUAL;
    }

    /**
     * Accessor: Vérifier si le relevé est automatique
     */
    public function getIsAutomaticAttribute(): bool
    {
        return $this->recording_method === self::METHOD_AUTOMATIC;
    }

    /**
     * Accessor: Formater le kilométrage avec séparateur de milliers
     */
    public function getFormattedMileageAttribute(): string
    {
        return number_format($this->mileage, 0, ',', ' ') . ' km';
    }

    // =========================================================================
    // BUSINESS LOGIC METHODS
    // =========================================================================

    /**
     * Obtenir le dernier relevé kilométrique pour un véhicule
     */
    public static function getLastReadingForVehicle(int $vehicleId): ?self
    {
        return self::where('vehicle_id', $vehicleId)
            ->latest('recorded_at')
            ->first();
    }

    /**
     * Calculer la différence de kilométrage avec le relevé précédent
     */
    public function getMileageDifference(): ?int
    {
        $previousReading = self::where('vehicle_id', $this->vehicle_id)
            ->where('recorded_at', '<', $this->recorded_at)
            ->latest('recorded_at')
            ->first();

        if (!$previousReading) {
            return null;
        }

        return $this->mileage - $previousReading->mileage;
    }

    /**
     * Vérifier si ce relevé est cohérent avec l'historique
     */
    public function isConsistent(): bool
    {
        $difference = $this->getMileageDifference();

        // Si c'est le premier relevé, c'est cohérent
        if ($difference === null) {
            return true;
        }

        // Un relevé est incohérent si le kilométrage diminue
        return $difference >= 0;
    }

    /**
     * Obtenir l'historique des relevés pour un véhicule
     */
    public static function getHistoryForVehicle(int $vehicleId, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('vehicle_id', $vehicleId)
            ->latest('recorded_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Calculer le kilométrage moyen journalier entre deux dates
     */
    public static function calculateAverageDailyMileage(int $vehicleId, $startDate, $endDate): float
    {
        $firstReading = self::where('vehicle_id', $vehicleId)
            ->where('recorded_at', '>=', $startDate)
            ->oldest('recorded_at')
            ->first();

        $lastReading = self::where('vehicle_id', $vehicleId)
            ->where('recorded_at', '<=', $endDate)
            ->latest('recorded_at')
            ->first();

        if (!$firstReading || !$lastReading || $firstReading->id === $lastReading->id) {
            return 0;
        }

        $mileageDiff = $lastReading->mileage - $firstReading->mileage;
        $daysDiff = $firstReading->recorded_at->diffInDays($lastReading->recorded_at);

        return $daysDiff > 0 ? round($mileageDiff / $daysDiff, 2) : 0;
    }

    /**
     * Créer un relevé automatique
     */
    public static function createAutomatic(
        int $organizationId,
        int $vehicleId,
        int $mileage,
        ?string $notes = null
    ): self {
        return self::create([
            'organization_id' => $organizationId,
            'vehicle_id' => $vehicleId,
            'recorded_at' => now(),
            'mileage' => $mileage,
            'recorded_by_id' => null,
            'recording_method' => self::METHOD_AUTOMATIC,
            'notes' => $notes,
        ]);
    }

    /**
     * Créer un relevé manuel
     */
    public static function createManual(
        int $organizationId,
        int $vehicleId,
        int $mileage,
        int $recordedById,
        ?\DateTime $recordedAt = null,
        ?string $notes = null
    ): self {
        return self::create([
            'organization_id' => $organizationId,
            'vehicle_id' => $vehicleId,
            'recorded_at' => $recordedAt ?? now(),
            'mileage' => $mileage,
            'recorded_by_id' => $recordedById,
            'recording_method' => self::METHOD_MANUAL,
            'notes' => $notes,
        ]);
    }

    // =========================================================================
    // EVENTS
    // =========================================================================

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Validation avant sauvegarde (sécurité supplémentaire au trigger)
        static::creating(function ($reading) {
            // Si c'est un relevé automatique, vérifier la cohérence
            if ($reading->recording_method === self::METHOD_AUTOMATIC) {
                $lastReading = self::getLastReadingForVehicle($reading->vehicle_id);

                if ($lastReading && $reading->mileage < $lastReading->mileage) {
                    throw new \InvalidArgumentException(
                        "Le kilométrage ({$reading->mileage} km) ne peut pas être inférieur au dernier relevé ({$lastReading->mileage} km)"
                    );
                }
            }
        });
    }
}
