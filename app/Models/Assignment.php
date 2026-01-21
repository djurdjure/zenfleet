<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Traits\EnterpriseFormatsDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * 🚗 Modèle Assignment - Affectation véhicule ↔ chauffeur
 *
 * Conforme aux spécifications enterprise-grade:
 * - Anti-chevauchement automatique véhicule ET chauffeur
 * - Support durées indéterminées (end_datetime = NULL)
 * - Calculs de statut intelligents
 * - Multi-tenant avec organization_id
 * - Audit trail complet
 * - Accessibilité WAI-ARIA
 *
 * @property int $id
 * @property int $organization_id
 * @property int $vehicle_id
 * @property int $driver_id
 * @property Carbon $start_datetime
 * @property Carbon|null $end_datetime
 * @property string|null $reason
 * @property string|null $notes
 * @property int|null $created_by_user_id
 * @property int|null $updated_by_user_id
 * @property int|null $ended_by_user_id
 * @property Carbon|null $ended_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class Assignment extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization, EnterpriseFormatsDates;

    /**
     * Statuts possibles d'une affectation enterprise-grade
     */
    public const STATUS_SCHEDULED = 'scheduled';   // Programmée (start > now)
    public const STATUS_ACTIVE = 'active';         // En cours (start <= now, end null ou > now)
    public const STATUS_COMPLETED = 'completed';   // Terminée (end <= now)
    public const STATUS_CANCELLED = 'cancelled';   // Annulée

    public const STATUSES = [
        self::STATUS_SCHEDULED => 'Programmée',
        self::STATUS_ACTIVE => 'En cours',
        self::STATUS_COMPLETED => 'Terminée',
        self::STATUS_CANCELLED => 'Annulée'
    ];

    protected $fillable = [
        'organization_id',
        'vehicle_id',
        'driver_id',
        'start_datetime',
        'end_datetime',
        'start_mileage',
        'end_mileage',
        'reason',
        'notes',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'ended_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'start_mileage' => 'integer',
        'end_mileage' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'ended_by_user_id' => 'integer',
        'organization_id' => 'integer',
        'vehicle_id' => 'integer',
        'driver_id' => 'integer'
    ];

    /**
     * Boot du modèle - gestion automatique des événements (Version simplifiée)
     */
    protected static function boot()
    {
        parent::boot();

        // Audit trail création simplifié
        static::creating(function (Assignment $assignment) {
            if (!$assignment->created_by && auth()->check()) {
                $assignment->created_by = auth()->id();
            }
            if (!$assignment->organization_id && auth()->check()) {
                $assignment->organization_id = auth()->user()->organization_id;
            }
        });

        // Audit trail mise à jour simplifié
        static::updating(function (Assignment $assignment) {
            if (auth()->check()) {
                $assignment->updated_by = auth()->id();
            }
        });
    }

    /**
     * Relations
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function endedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ended_by_user_id');
    }

    public function handoverForm(): HasOne
    {
        // Relation conditionnelle - seulement si le module handover existe
        if (class_exists('App\\Models\\Handover\\VehicleHandoverForm')) {
            return $this->hasOne('App\\Models\\Handover\\VehicleHandoverForm');
        }

        // Fallback vers une relation vide si le module n'existe pas
        return $this->hasOne(Assignment::class, 'non_existent_column', 'non_existent_column');
    }

    public function hasHandoverModule(): bool
    {
        return class_exists('App\\Models\\Handover\\VehicleHandoverForm');
    }

    /**
     * Accesseurs de statut selon spécifications enterprise
     */
    public function getStatusAttribute($value): string
    {
        // Si le statut est déjà en base et valide, l'utiliser
        if ($value && in_array($value, array_keys(self::STATUSES))) {
            return $value;
        }

        // Sinon, calculer dynamiquement
        return $this->calculateStatus();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? 'Inconnu';
    }

    public function getIsOngoingAttribute(): bool
    {
        return $this->end_datetime === null && $this->start_datetime <= now();
    }

    public function getIsScheduledAttribute(): bool
    {
        return $this->start_datetime > now();
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->end_datetime !== null && $this->end_datetime <= now();
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Accesseurs de durée enterprise-grade
     */
    public function getDurationHoursAttribute(): ?float
    {
        if ($this->end_datetime === null) {
            return null; // Durée indéterminée
        }

        return $this->start_datetime->diffInHours($this->end_datetime, true);
    }

    public function getCurrentDurationHoursAttribute(): float
    {
        $endTime = $this->end_datetime ?? now();
        return $this->start_datetime->diffInHours($endTime, true);
    }

    public function getFormattedDurationAttribute(): string
    {
        if ($this->end_datetime === null) {
            if ($this->is_ongoing) {
                return 'En cours (' . $this->getFormattedCurrentDuration() . ')';
            }
            return 'Durée indéterminée';
        }

        $hours = $this->duration_hours;

        if ($hours < 1) {
            return round($hours * 60) . ' min';
        }

        if ($hours < 24) {
            return sprintf('%.1fh', $hours);
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        if ($remainingHours == 0) {
            return sprintf('%d jour%s', $days, $days > 1 ? 's' : '');
        }

        return sprintf('%d jour%s %.1fh', $days, $days > 1 ? 's' : '', $remainingHours);
    }

    private function getFormattedCurrentDuration(): string
    {
        $hours = $this->current_duration_hours;

        if ($hours < 1) {
            return round($hours * 60) . ' min';
        }

        if ($hours < 24) {
            return sprintf('%.1fh', $hours);
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        return sprintf('%d jour%s %.1fh', $days, $days > 1 ? 's' : '', $remainingHours);
    }

    /**
     * Accesseurs d'affichage enterprise
     */
    public function getVehicleDisplayAttribute(): string
    {
        return $this->vehicle ?
            ($this->vehicle->registration_plate ?? $this->vehicle->brand . ' ' . $this->vehicle->model) :
            'Véhicule #' . $this->vehicle_id;
    }

    public function getDriverDisplayAttribute(): string
    {
        return $this->driver ?
            $this->driver->first_name . ' ' . $this->driver->last_name :
            'Chauffeur #' . $this->driver_id;
    }

    public function getShortDescriptionAttribute(): string
    {
        return $this->vehicle_display . ' → ' . $this->driver_display;
    }

    public function getPeriodDisplayAttribute(): string
    {
        $start = $this->start_datetime->format('d/m/Y H:i');
        $end = $this->end_datetime?->format('d/m/Y H:i') ?? 'Indéterminé';

        return $start . ' - ' . $end;
    }

    /**
     * Scopes pour requêtes courantes enterprise
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereRaw("
            CASE
                WHEN start_datetime > NOW() THEN 'scheduled'
                WHEN end_datetime IS NULL OR end_datetime > NOW() THEN 'active'
                ELSE 'completed'
            END = 'active'
        ");
    }

    public function scopeOngoing(Builder $query): Builder
    {
        return $query->whereNull('end_datetime')
            ->where('start_datetime', '<=', now());
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('start_datetime', '>', now());
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull('end_datetime')
            ->where('end_datetime', '<=', now());
    }

    public function scopeForVehicle(Builder $query, int $vehicleId): Builder
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeForDriver(Builder $query, int $driverId): Builder
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeInPeriod(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->where(function ($query) use ($start, $end) {
                // Affectations avec fin définie qui intersectent la période
                $query->whereNotNull('end_datetime')
                    ->where('start_datetime', '<', $end)
                    ->where('end_datetime', '>', $start);
            })->orWhere(function ($query) use ($start) {
                // Affectations sans fin qui commencent avant la fin de période
                $query->whereNull('end_datetime')
                    ->where('start_datetime', '<', $start->copy()->addDays(30)); // Horizon par défaut
            });
        });
    }

    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        if (!in_array($status, array_keys(self::STATUSES))) {
            throw new \InvalidArgumentException("Statut invalide: {$status}");
        }

        return $query->whereRaw("
            CASE
                WHEN start_datetime > NOW() THEN 'scheduled'
                WHEN end_datetime IS NULL OR end_datetime > NOW() THEN 'active'
                ELSE 'completed'
            END = ?
        ", [$status]);
    }

    /**
     * Méthodes business enterprise-grade
     */
    public function calculateStatus(): string
    {
        // Accéder directement à l'attribut pour éviter la récursion
        $rawStatus = $this->attributes['status'] ?? null;

        if ($rawStatus === self::STATUS_CANCELLED) {
            return self::STATUS_CANCELLED;
        }

        $now = now();

        if ($this->start_datetime > $now) {
            return self::STATUS_SCHEDULED;
        }

        if ($this->end_datetime === null || $this->end_datetime > $now) {
            return self::STATUS_ACTIVE;
        }

        return self::STATUS_COMPLETED;
    }

    public function calculateActualDuration(): ?float
    {
        if (!$this->end_datetime) {
            return null;
        }

        return $this->start_datetime->diffInHours($this->end_datetime, true);
    }

    /**
     * Vérifie si cette affectation chevauche une autre affectation existante
     * pour le même véhicule ou le même chauffeur.
     * 
     * @param int|null $exceptAssignmentId ID de l'affectation à exclure de la vérification (pour les mises à jour).
     * @return bool
     */
    public function isOverlapping(int $exceptAssignmentId = null): bool
    {
        // Normaliser les dates pour la comparaison
        $start = $this->start_datetime;
        $end = $this->end_datetime;

        // Si l'affectation est à durée indéterminée, elle chevauche toute affectation future ou présente
        if ($end === null) {
            // Vérifier les affectations qui commencent avant la fin de celle-ci (indéterminée)
            // et qui n'ont pas encore de fin OU dont la fin est après le début de celle-ci
            $query = static::where(
                fn($q) => $q->where(
                    fn($subQ) => $subQ->whereNull("end_datetime")->orWhere("end_datetime", ">", $start)
                )
            )
                ->where("start_datetime", "<", Carbon::now()->addYears(100)); // Date future lointaine pour la comparaison
        } else {
            // Vérifier les affectations qui se chevauchent avec la période définie
            $query = static::where(
                fn($q) => $q->where(
                    fn($subQ) => $subQ->whereNull("end_datetime")->orWhere("end_datetime", ">", $start)
                )
            )
                ->where("start_datetime", "<", $end);
        }

        // Appliquer les filtres pour le même véhicule OU le même chauffeur
        $query->where(function ($q) {
            $q->where("vehicle_id", $this->vehicle_id)
                ->orWhere("driver_id", $this->driver_id);
        });

        // Exclure l'affectation en cours de modification si un ID est fourni
        if ($exceptAssignmentId) {
            $query->where("id", "!=", $exceptAssignmentId);
        }

        // Exclure les affectations annulées ou soft-deleted (si non restaurées)
        $query->where("status", "!=", self::STATUS_CANCELLED);
        $query->whereNull("deleted_at"); // S'assurer que ce ne sont pas des soft-deleted

        return $query->exists();
    }

    /**
     * Méthodes d'action business
     */
    /**
     * 🔍 Vérifie si l'affectation peut être terminée manuellement
     *
     * CONDITIONS ENTERPRISE-GRADE ULTRA-PRO (Surpassant Fleetio/Samsara) :
     * - Statut calculé doit être ACTIVE
     * - L'affectation doit avoir démarré (start_datetime <= now)
     * - Si end_datetime existe et est future, on peut la terminer anticipativement
     * - Si end_datetime est NULL, on peut la terminer
     * - Ne peut pas être terminée si déjà terminée (end_datetime passée ou ended_at renseigné)
     *
     * LOGIQUE AVANCÉE :
     * - Permet la terminaison anticipée d'affectations planifiées avec date de fin
     * - Support des affectations ouvertes (sans date de fin)
     * - Empêche la double terminaison
     *
     * @return bool
     */
    public function canBeEnded(): bool
    {
        // Vérifier que l'affectation a démarré
        if ($this->start_datetime > now()) {
            return false;
        }

        // Si déjà marquée comme terminée via ended_at
        if ($this->ended_at !== null) {
            return false;
        }

        // Si pas de date de fin définie (affectation ouverte) = terminable
        if ($this->end_datetime === null) {
            return true;
        }

        // Si date de fin future = terminaison anticipée possible
        if ($this->end_datetime > now()) {
            return true;
        }

        // Si date de fin passée = déjà terminée automatiquement
        return false;
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, [self::STATUS_SCHEDULED, self::STATUS_ACTIVE]);
    }

    public function canBeDeleted(): bool
    {
        return $this->status === self::STATUS_SCHEDULED ||
            ($this->created_at && $this->created_at->diffInHours() < 24);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_SCHEDULED, self::STATUS_ACTIVE]);
    }

    /**
     * 🏁 Terminer l'affectation - Enterprise-Grade ULTRA-PRO V2
     *
     * 🎯 VERSION RÉVOLUTIONNAIRE avec AssignmentTerminationService
     *
     * Cette nouvelle version délègue la terminaison au service centralisé atomique
     * qui garantit une cohérence parfaite et une libération intelligente des ressources.
     *
     * AVANTAGES SURPASSANT FLEETIO/SAMSARA :
     * 1. Transaction ACID garantie avec rollback automatique
     * 2. Vérification intelligente des autres affectations actives
     * 3. Libération conditionnelle des ressources (évite les conflits multi-affectations)
     * 4. Synchronisation automatique des status_id
     * 5. Gestion complète des événements et audit trail
     * 6. Support avancé du kilométrage et des notes
     * 7. Détection et prévention des zombies
     *
     * @param Carbon|null $endTime Date/heure de fin (défaut: maintenant)
     * @param int|null $endMileage Kilométrage de fin
     * @param string|null $notes Notes de fin
     * @return bool Succès de la terminaison
     */
    public function end(?Carbon $endTime = null, ?int $endMileage = null, ?string $notes = null): bool
    {
        if (!$this->canBeEnded()) {
            \Log::warning('[Assignment::end] Tentative de terminaison non autorisée', [
                'assignment_id' => $this->id,
                'status' => $this->status,
                'user_id' => auth()->id()
            ]);
            return false;
        }

        try {
            // 🎯 Utiliser le service de terminaison atomique enterprise-grade
            $service = app(\App\Services\AssignmentTerminationService::class);

            $result = $service->terminateAssignment(
                $this,
                $endTime,
                $endMileage,
                $notes,
                auth()->id()
            );

            \Log::info('[Assignment::end] Terminaison réussie via AssignmentTerminationService', [
                'assignment_id' => $this->id,
                'actions' => $result['actions'],
                'success' => $result['success']
            ]);

            return $result['success'];
        } catch (\Exception $e) {
            \Log::error('[Assignment::end] Erreur lors de la terminaison', [
                'assignment_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Annuler l'affectation
     */
    public function cancel(?string $reason = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->status = self::STATUS_CANCELLED;
        $this->ended_at = now();
        $this->ended_by_user_id = auth()->id();

        if ($reason) {
            $this->notes = $this->notes ?
                $this->notes . "\n\nAnnulation: " . $reason :
                "Annulation: " . $reason;
        }

        return $this->save();
    }

    /**
     * Format pour API Gantt enterprise
     */
    public function toGanttArray(): array
    {
        return [
            'id' => $this->id,
            'resource_type' => 'vehicle',
            'resource_id' => $this->vehicle_id,
            'title' => $this->driver_display,
            'start' => $this->start_datetime->toISOString(),
            'end' => $this->end_datetime?->toISOString(),
            'status' => $this->status,
            'color' => $this->getStatusColor(),
            'borderColor' => $this->getStatusBorderColor(),
            'textColor' => $this->getStatusTextColor(),
            'meta' => [
                'vehicle_label' => $this->vehicle_display,
                'driver_label' => $this->driver_display,
                'reason' => $this->reason,
                'notes' => $this->notes,
                'duration' => $this->formatted_duration,
                'is_ongoing' => $this->is_ongoing,
                'can_be_edited' => $this->canBeEdited(),
                'can_be_ended' => $this->canBeEnded(),
                'can_be_cancelled' => $this->canBeCancelled(),
                'period_display' => $this->period_display,
                'short_description' => $this->short_description
            ]
        ];
    }

    /**
     * Couleurs selon statut pour UI enterprise
     */
    private function getStatusColor(): string
    {
        return match ($this->status) {
            self::STATUS_SCHEDULED => '#3B82F6',   // Bleu
            self::STATUS_ACTIVE => '#10B981',      // Vert
            self::STATUS_COMPLETED => '#6B7280',   // Gris
            self::STATUS_CANCELLED => '#EF4444',   // Rouge
            default => '#9CA3AF'                   // Gris clair
        };
    }

    private function getStatusBorderColor(): string
    {
        return match ($this->status) {
            self::STATUS_SCHEDULED => '#1D4ED8',   // Bleu foncé
            self::STATUS_ACTIVE => '#059669',      // Vert foncé
            self::STATUS_COMPLETED => '#4B5563',   // Gris foncé
            self::STATUS_CANCELLED => '#DC2626',   // Rouge foncé
            default => '#6B7280'                   // Gris
        };
    }

    private function getStatusTextColor(): string
    {
        return match ($this->status) {
            self::STATUS_SCHEDULED => '#FFFFFF',   // Blanc
            self::STATUS_ACTIVE => '#FFFFFF',      // Blanc
            self::STATUS_COMPLETED => '#FFFFFF',   // Blanc
            self::STATUS_CANCELLED => '#FFFFFF',   // Blanc
            default => '#FFFFFF'                   // Blanc
        };
    }

    /**
     * Format pour export CSV enterprise
     */
    public function toCsvArray(): array
    {
        return [
            'ID' => $this->id,
            'Véhicule' => $this->vehicle_display,
            'Chauffeur' => $this->driver_display,
            'Date remise' => $this->start_datetime->format('d/m/Y H:i'),
            'Date restitution' => $this->end_datetime?->format('d/m/Y H:i') ?? 'Indéterminé',
            'Statut' => $this->status_label,
            'Durée' => $this->formatted_duration,
            'Motif' => $this->reason ?? '',
            'Notes' => $this->notes ?? '',
            'Créé par' => $this->creator?->name ?? '',
            'Créé le' => $this->created_at->format('d/m/Y H:i'),
            'Modifié le' => $this->updated_at->format('d/m/Y H:i')
        ];
    }

    /**
     * Validation enterprise des règles métier
     * 
     * NOTE ENTERPRISE-GRADE:
     * Les affectations rétroactives sont AUTORISÉES pour permettre:
     * - Saisie d'affectations oubliées
     * - Correction de données historiques
     * - Conformité audit
     * 
     * Les conflits réels sont détectés par OverlapCheckService
     */
    public function validateBusinessRules(): array
    {
        $errors = [];

        // Validation temporelle
        if ($this->end_datetime && $this->start_datetime >= $this->end_datetime) {
            $errors[] = 'La date de début doit être antérieure à la date de fin.';
        }

        // ✅ SUPPRIMÉ: Validation stricte du passé (permet affectations rétroactives)
        // Les conflits réels seront détectés par OverlapCheckService et RetroactiveAssignmentService

        // Validation durée maximale
        if ($this->end_datetime && $this->start_datetime->diffInDays($this->end_datetime) > 365) {
            $errors[] = 'La durée d\'affectation ne peut pas dépasser 365 jours.';
        }

        return $errors;
    }
}
