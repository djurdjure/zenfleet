<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Modèle MaintenanceType - Gestion des types de maintenance enterprise-grade
 *
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $description
 * @property string $category
 * @property bool $is_recurring
 * @property int|null $default_interval_km
 * @property int|null $default_interval_days
 * @property int|null $estimated_duration_minutes
 * @property float|null $estimated_cost
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class MaintenanceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'category',
        'is_recurring',
        'default_interval_km',
        'default_interval_days',
        'estimated_duration_minutes',
        'estimated_cost',
        'is_active',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'estimated_cost' => 'decimal:2',
        'default_interval_km' => 'integer',
        'default_interval_days' => 'integer',
        'estimated_duration_minutes' => 'integer',
    ];

    /**
     * Categories constants pour validation et filtering
     */
    public const CATEGORY_PREVENTIVE = 'preventive';
    public const CATEGORY_CORRECTIVE = 'corrective';
    public const CATEGORY_INSPECTION = 'inspection';
    public const CATEGORY_REVISION = 'revision';

    public const CATEGORIES = [
        self::CATEGORY_PREVENTIVE => 'Préventive',
        self::CATEGORY_CORRECTIVE => 'Corrective',
        self::CATEGORY_INSPECTION => 'Inspection',
        self::CATEGORY_REVISION => 'Révision',
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
     * Relation avec les planifications de maintenance
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    /**
     * Relation avec les opérations de maintenance
     */
    public function operations(): HasMany
    {
        return $this->hasMany(MaintenanceOperation::class);
    }

    /**
     * Scope pour filtrer les types actifs
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par catégorie
     */
    public function scopeByCategory(Builder $query, string $category): void
    {
        $query->where('category', $category);
    }

    /**
     * Scope pour les types récurrents
     */
    public function scopeRecurring(Builder $query): void
    {
        $query->where('is_recurring', true);
    }

    /**
     * Scope pour recherche par nom
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'ilike', "%{$search}%")
              ->orWhere('description', 'ilike', "%{$search}%");
        });
    }

    /**
     * Accessor pour le nom de la catégorie formaté
     */
    protected function categoryName(): Attribute
    {
        return Attribute::make(
            get: fn () => self::CATEGORIES[$this->category] ?? $this->category
        );
    }

    /**
     * Accessor pour la durée formatée
     */
    protected function formattedDuration(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->estimated_duration_minutes) {
                    return null;
                }

                $hours = intval($this->estimated_duration_minutes / 60);
                $minutes = $this->estimated_duration_minutes % 60;

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
            get: fn () => $this->estimated_cost ? number_format($this->estimated_cost, 2, ',', ' ') . ' DA' : null
        );
    }

    /**
     * Accessor pour l'intervalle formaté
     */
    protected function formattedInterval(): Attribute
    {
        return Attribute::make(
            get: function () {
                $intervals = [];

                if ($this->default_interval_km) {
                    $intervals[] = number_format($this->default_interval_km, 0, ',', ' ') . ' km';
                }

                if ($this->default_interval_days) {
                    $intervals[] = $this->default_interval_days . ' jours';
                }

                return implode(' ou ', $intervals);
            }
        );
    }

    /**
     * Méthode pour obtenir la couleur hexadécimale selon la catégorie
     * 
     * @return string Couleur hexadécimale
     */
    public function getCategoryColor(): string
    {
        $colors = [
            self::CATEGORY_PREVENTIVE => '#10B981',  // Green
            self::CATEGORY_CORRECTIVE => '#EF4444',  // Red
            self::CATEGORY_INSPECTION => '#3B82F6',  // Blue
            self::CATEGORY_REVISION => '#8B5CF6',    // Purple
        ];

        return $colors[$this->category] ?? '#6B7280'; // Gray par défaut
    }

    /**
     * Méthode pour obtenir le badge de catégorie avec couleur
     */
    public function getCategoryBadge(): string
    {
        $colors = [
            self::CATEGORY_PREVENTIVE => 'bg-green-100 text-green-800',
            self::CATEGORY_CORRECTIVE => 'bg-red-100 text-red-800',
            self::CATEGORY_INSPECTION => 'bg-blue-100 text-blue-800',
            self::CATEGORY_REVISION => 'bg-purple-100 text-purple-800',
        ];

        $color = $colors[$this->category] ?? 'bg-gray-100 text-gray-800';
        $name = $this->category_name;

        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$color}\">{$name}</span>";
    }

    /**
     * Méthode pour calculer le prochain échéance basé sur les intervalles
     */
    public function calculateNextDue(\DateTime $lastMaintenance, int $currentMileage): array
    {
        $nextDue = [
            'date' => null,
            'mileage' => null,
        ];

        if ($this->default_interval_days) {
            $nextDue['date'] = (clone $lastMaintenance)->modify("+{$this->default_interval_days} days");
        }

        if ($this->default_interval_km) {
            $nextDue['mileage'] = $currentMileage + $this->default_interval_km;
        }

        return $nextDue;
    }

    /**
     * Validation rules pour le modèle
     */
    public static function validationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:' . implode(',', array_keys(self::CATEGORIES)),
            'is_recurring' => 'boolean',
            'default_interval_km' => 'nullable|integer|min:1|max:1000000',
            'default_interval_days' => 'nullable|integer|min:1|max:3650',
            'estimated_duration_minutes' => 'nullable|integer|min:1|max:14400',
            'estimated_cost' => 'nullable|numeric|min:0|max:999999.99',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    public static function validationMessages(): array
    {
        return [
            'name.required' => 'Le nom du type de maintenance est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'category.required' => 'La catégorie est obligatoire.',
            'category.in' => 'La catégorie sélectionnée n\'est pas valide.',
            'default_interval_km.integer' => 'L\'intervalle en kilomètres doit être un nombre entier.',
            'default_interval_km.min' => 'L\'intervalle en kilomètres doit être d\'au moins 1 km.',
            'default_interval_days.integer' => 'L\'intervalle en jours doit être un nombre entier.',
            'default_interval_days.min' => 'L\'intervalle en jours doit être d\'au moins 1 jour.',
            'estimated_duration_minutes.integer' => 'La durée estimée doit être un nombre entier de minutes.',
            'estimated_cost.numeric' => 'Le coût estimé doit être un nombre valide.',
        ];
    }
}