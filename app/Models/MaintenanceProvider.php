<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Modèle MaintenanceProvider - Gestion des fournisseurs de maintenance
 *
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $company_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $city
 * @property string|null $postal_code
 * @property array|null $specialties
 * @property float|null $rating
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class MaintenanceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'specialties',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'specialties' => 'array',
        'rating' => 'decimal:1',
        'is_active' => 'boolean',
    ];

    /**
     * Spécialités disponibles
     */
    public const SPECIALTIES = [
        'brake' => 'Freinage',
        'engine' => 'Moteur',
        'transmission' => 'Transmission',
        'electrical' => 'Électricité',
        'bodywork' => 'Carrosserie',
        'tire' => 'Pneumatiques',
        'ac' => 'Climatisation',
        'exhaust' => 'Échappement',
        'suspension' => 'Suspension',
        'fuel_system' => 'Système carburant',
        'cooling' => 'Refroidissement',
        'lighting' => 'Éclairage',
        'general' => 'Maintenance générale',
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
     * Relation avec les opérations de maintenance
     */
    public function operations(): HasMany
    {
        return $this->hasMany(MaintenanceOperation::class, 'provider_id');
    }

    /**
     * Scope pour filtrer les fournisseurs actifs
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope pour recherche par nom, entreprise ou ville
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'ilike', "%{$search}%")
              ->orWhere('company_name', 'ilike', "%{$search}%")
              ->orWhere('city', 'ilike', "%{$search}%")
              ->orWhere('email', 'ilike', "%{$search}%");
        });
    }

    /**
     * Scope pour filtrer par ville
     */
    public function scopeByCity(Builder $query, string $city): void
    {
        $query->where('city', 'ilike', "%{$city}%");
    }

    /**
     * Scope pour filtrer par spécialité
     */
    public function scopeBySpecialty(Builder $query, string $specialty): void
    {
        $query->whereJsonContains('specialties', $specialty);
    }

    /**
     * Scope pour filtrer par note minimale
     */
    public function scopeMinRating(Builder $query, float $minRating): void
    {
        $query->where('rating', '>=', $minRating);
    }

    /**
     * Scope pour ordonner par note décroissante
     */
    public function scopeOrderByRating(Builder $query): void
    {
        $query->orderByDesc('rating');
    }

    /**
     * Accessor pour l'adresse complète formatée
     */
    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: function () {
                $parts = array_filter([
                    $this->address,
                    $this->postal_code . ' ' . $this->city,
                ]);

                return implode(', ', $parts);
            }
        );
    }

    /**
     * Accessor pour les spécialités formatées
     */
    protected function formattedSpecialties(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->specialties) {
                    return [];
                }

                return collect($this->specialties)
                    ->map(fn($key) => self::SPECIALTIES[$key] ?? $key)
                    ->values()
                    ->toArray();
            }
        );
    }

    /**
     * Accessor pour la note formatée avec étoiles
     */
    protected function formattedRating(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->rating) {
                    return 'Non noté';
                }

                $stars = str_repeat('★', floor($this->rating));
                $halfStar = ($this->rating - floor($this->rating)) >= 0.5 ? '☆' : '';
                $emptyStars = str_repeat('☆', 5 - ceil($this->rating));

                return $stars . $halfStar . $emptyStars . " ({$this->rating}/5)";
            }
        );
    }

    /**
     * Méthode pour obtenir le badge de statut
     */
    public function getStatusBadge(): string
    {
        if ($this->is_active) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Actif</span>';
        }

        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactif</span>';
    }

    /**
     * Méthode pour obtenir les badges de spécialités
     */
    public function getSpecialtyBadges(): string
    {
        if (!$this->specialties) {
            return '';
        }

        $badges = collect($this->specialties)
            ->map(function($specialty) {
                $name = self::SPECIALTIES[$specialty] ?? $specialty;
                return '<span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 mr-1 mb-1">' . e($name) . '</span>';
            })
            ->implode('');

        return $badges;
    }

    /**
     * Méthode pour calculer la note moyenne basée sur les opérations
     */
    public function updateRatingFromOperations(): void
    {
        $averageRating = $this->operations()
            ->whereNotNull('rating')
            ->avg('rating');

        if ($averageRating) {
            $this->update(['rating' => round($averageRating, 1)]);
        }
    }

    /**
     * Méthode pour obtenir les statistiques du fournisseur
     */
    public function getStats(): array
    {
        $operations = $this->operations();

        return [
            'total_operations' => $operations->count(),
            'completed_operations' => $operations->where('status', 'completed')->count(),
            'average_cost' => $operations->whereNotNull('total_cost')->avg('total_cost'),
            'average_duration' => $operations->whereNotNull('duration_minutes')->avg('duration_minutes'),
            'last_operation' => $operations->latest()->first()?->created_at,
        ];
    }

    /**
     * Validation rules pour le modèle
     */
    public static function validationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'specialties' => 'nullable|array',
            'specialties.*' => 'string|in:' . implode(',', array_keys(self::SPECIALTIES)),
            'rating' => 'nullable|numeric|min:0|max:5',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    public static function validationMessages(): array
    {
        return [
            'name.required' => 'Le nom du fournisseur est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.email' => 'L\'adresse email doit être valide.',
            'rating.numeric' => 'La note doit être un nombre.',
            'rating.min' => 'La note doit être comprise entre 0 et 5.',
            'rating.max' => 'La note doit être comprise entre 0 et 5.',
            'specialties.array' => 'Les spécialités doivent être une liste.',
            'specialties.*.in' => 'Une ou plusieurs spécialités ne sont pas valides.',
        ];
    }
}