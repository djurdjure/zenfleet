<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle DriverSanctionHistory
 *
 * Enregistre toutes les actions effectuées sur les sanctions
 * pour une traçabilité complète (audit trail).
 *
 * @property int $id
 * @property int $sanction_id
 * @property int $user_id
 * @property string $action
 * @property array $details
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read \App\Models\DriverSanction $sanction
 * @property-read \App\Models\User $user
 *
 * @author ZenFleet Enterprise Team
 * @version 1.0.0
 * @package App\Models
 */
class DriverSanctionHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'driver_sanction_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'sanction_id',
        'user_id',
        'action',
        'details',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Actions possibles dans l'historique
     *
     * @var array<string, string>
     */
    public const ACTIONS = [
        'created' => 'Création',
        'updated' => 'Modification',
        'archived' => 'Archivage',
        'unarchived' => 'Désarchivage',
        'deleted' => 'Suppression',
    ];

    /**
     * Relation: Sanction concernée
     *
     * @return BelongsTo
     */
    public function sanction(): BelongsTo
    {
        return $this->belongsTo(DriverSanction::class, 'sanction_id');
    }

    /**
     * Relation: Utilisateur ayant effectué l'action
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir le label de l'action
     *
     * @return string
     */
    public function getActionLabel(): string
    {
        return self::ACTIONS[$this->action] ?? $this->action;
    }

    /**
     * Obtenir l'icône de l'action
     *
     * @return string
     */
    public function getActionIcon(): string
    {
        return match ($this->action) {
            'created' => 'fa-plus-circle',
            'updated' => 'fa-edit',
            'archived' => 'fa-archive',
            'unarchived' => 'fa-box-open',
            'deleted' => 'fa-trash',
            default => 'fa-circle',
        };
    }

    /**
     * Obtenir la couleur de l'action
     *
     * @return string
     */
    public function getActionColor(): string
    {
        return match ($this->action) {
            'created' => 'green',
            'updated' => 'blue',
            'archived' => 'gray',
            'unarchived' => 'indigo',
            'deleted' => 'red',
            default => 'gray',
        };
    }

    /**
     * Formater les détails pour l'affichage
     *
     * @return string
     */
    public function getFormattedDetails(): string
    {
        if (empty($this->details)) {
            return 'Aucun détail';
        }

        $formatted = [];
        foreach ($this->details as $key => $value) {
            if (is_array($value)) {
                $formatted[] = $key . ': ' . json_encode($value, JSON_PRETTY_PRINT);
            } else {
                $formatted[] = $key . ': ' . $value;
            }
        }

        return implode("\n", $formatted);
    }
}
