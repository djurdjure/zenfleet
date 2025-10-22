<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

/**
 * Modèle DriverSanction
 *
 * Représente une sanction disciplinaire appliquée à un chauffeur.
 * Gère l'isolation multi-tenant via le trait BelongsToOrganization.
 *
 * @property int $id
 * @property int $organization_id
 * @property int $driver_id
 * @property int $supervisor_id
 * @property string $sanction_type
 * @property string $reason
 * @property \Carbon\Carbon $sanction_date
 * @property string|null $attachment_path
 * @property \Carbon\Carbon|null $archived_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read \App\Models\Organization $organization
 * @property-read \App\Models\Driver $driver
 * @property-read \App\Models\User $supervisor
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DriverSanctionHistory[] $history
 *
 * @method static Builder active()
 * @method static Builder archived()
 * @method static Builder forDriver(int $driverId)
 * @method static Builder bySupervisor(int $supervisorId)
 * @method static Builder ofType(string $type)
 *
 * @author ZenFleet Enterprise Team
 * @version 1.0.0
 * @package App\Models
 */
class DriverSanction extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'driver_sanctions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_id',
        'driver_id',
        'supervisor_id',
        'sanction_type',
        'severity',
        'reason',
        'sanction_date',
        'duration_days',
        'attachment_path',
        'status',
        'notes',
        'archived_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sanction_date' => 'date',
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Types de sanctions disponibles
     *
     * @var array<string, array>
     */
    public const SANCTION_TYPES = [
        'avertissement_verbal' => [
            'label' => 'Avertissement Verbal',
            'icon' => 'fa-comment-dots',
            'color' => 'yellow',
            'severity' => 1,
        ],
        'avertissement_ecrit' => [
            'label' => 'Avertissement Écrit',
            'icon' => 'fa-file-alt',
            'color' => 'orange',
            'severity' => 2,
        ],
        'mise_a_pied' => [
            'label' => 'Mise à Pied',
            'icon' => 'fa-user-slash',
            'color' => 'red',
            'severity' => 3,
        ],
        'mise_en_demeure' => [
            'label' => 'Mise en Demeure',
            'icon' => 'fa-gavel',
            'color' => 'red',
            'severity' => 4,
        ],
    ];

    /**
     * Boot method pour gérer les événements du modèle
     */
    protected static function boot()
    {
        parent::boot();

        // Enregistrer automatiquement l'action dans l'historique lors de la création
        static::created(function ($sanction) {
            $sanction->recordHistory('created', [
                'sanction_type' => $sanction->sanction_type,
                'reason' => $sanction->reason,
                'sanction_date' => $sanction->sanction_date->format('Y-m-d'),
            ]);
        });

        // Enregistrer automatiquement l'action dans l'historique lors de la mise à jour
        static::updated(function ($sanction) {
            $changes = $sanction->getChanges();
            if (count($changes) > 1) { // > 1 car updated_at change toujours
                $sanction->recordHistory('updated', [
                    'changes' => $changes,
                    'original' => $sanction->getOriginal(),
                ]);
            }
        });

        // Enregistrer automatiquement l'action dans l'historique lors de la suppression
        static::deleted(function ($sanction) {
            $sanction->recordHistory('deleted', [
                'deleted_at' => now()->toDateTimeString(),
            ]);
        });
    }

    /**
     * Relation: Organisation propriétaire
     *
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Relation: Chauffeur sanctionné
     *
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Relation: Superviseur ayant émis la sanction
     *
     * @return BelongsTo
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Relation: Historique des actions sur cette sanction
     *
     * @return HasMany
     */
    public function history(): HasMany
    {
        return $this->hasMany(DriverSanctionHistory::class, 'sanction_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Sanctions actives (non archivées)
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope: Sanctions archivées
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope: Sanctions pour un chauffeur spécifique
     *
     * @param Builder $query
     * @param int $driverId
     * @return Builder
     */
    public function scopeForDriver(Builder $query, int $driverId): Builder
    {
        return $query->where('driver_id', $driverId);
    }

    /**
     * Scope: Sanctions émises par un superviseur spécifique
     *
     * @param Builder $query
     * @param int $supervisorId
     * @return Builder
     */
    public function scopeBySupervisor(Builder $query, int $supervisorId): Builder
    {
        return $query->where('supervisor_id', $supervisorId);
    }

    /**
     * Scope: Sanctions d'un type spécifique
     *
     * @param Builder $query
     * @param string $type
     * @return Builder
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('sanction_type', $type);
    }

    /**
     * Obtenir le label du type de sanction
     *
     * @return string
     */
    public function getSanctionTypeLabel(): string
    {
        return self::SANCTION_TYPES[$this->sanction_type]['label'] ?? $this->sanction_type;
    }

    /**
     * Obtenir l'icône du type de sanction
     *
     * @return string
     */
    public function getSanctionTypeIcon(): string
    {
        return self::SANCTION_TYPES[$this->sanction_type]['icon'] ?? 'fa-exclamation-triangle';
    }

    /**
     * Obtenir la couleur du type de sanction
     *
     * @return string
     */
    public function getSanctionTypeColor(): string
    {
        return self::SANCTION_TYPES[$this->sanction_type]['color'] ?? 'gray';
    }

    /**
     * Obtenir la sévérité du type de sanction
     *
     * @return int
     */
    public function getSanctionTypeSeverity(): int
    {
        return self::SANCTION_TYPES[$this->sanction_type]['severity'] ?? 0;
    }

    /**
     * Vérifier si la sanction est archivée
     *
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    /**
     * Archiver la sanction
     *
     * @return bool
     */
    public function archive(): bool
    {
        $this->archived_at = now();
        $result = $this->save();

        if ($result) {
            $this->recordHistory('archived', [
                'archived_at' => $this->archived_at->toDateTimeString(),
            ]);
        }

        return $result;
    }

    /**
     * Désarchiver la sanction
     *
     * @return bool
     */
    public function unarchive(): bool
    {
        $this->archived_at = null;
        $result = $this->save();

        if ($result) {
            $this->recordHistory('unarchived', [
                'unarchived_at' => now()->toDateTimeString(),
            ]);
        }

        return $result;
    }

    /**
     * Obtenir l'URL de la pièce jointe
     *
     * @return string|null
     */
    public function getAttachmentUrl(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        return Storage::url($this->attachment_path);
    }

    /**
     * Supprimer la pièce jointe
     *
     * @return bool
     */
    public function deleteAttachment(): bool
    {
        if ($this->attachment_path && Storage::exists($this->attachment_path)) {
            Storage::delete($this->attachment_path);
            $this->attachment_path = null;
            return $this->save();
        }

        return false;
    }

    /**
     * Enregistrer une action dans l'historique
     *
     * @param string $action
     * @param array $details
     * @return DriverSanctionHistory
     */
    public function recordHistory(string $action, array $details): DriverSanctionHistory
    {
        return $this->history()->create([
            'user_id' => auth()->id() ?? 1, // Fallback to system user
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Obtenir le nombre de jours depuis la sanction
     *
     * @return int
     */
    public function getDaysSinceSanction(): int
    {
        return $this->sanction_date->diffInDays(now());
    }

    /**
     * Vérifier si la sanction est récente (moins de 30 jours)
     *
     * @return bool
     */
    public function isRecent(): bool
    {
        return $this->getDaysSinceSanction() <= 30;
    }
}
