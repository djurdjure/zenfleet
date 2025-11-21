<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasStatusBadge;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// CORRECTION : Ajout des bons namespaces pour les relations
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization, HasStatusBadge;

    protected $fillable = [
        // Champs de base
        'user_id', 'organization_id', 'first_name', 'last_name', 'email',

        // Informations personnelles
        'employee_number', 'birth_date', 'blood_type',
        'personal_phone', 'personal_email', 'address', 'city', 'postal_code',

        // Permis de conduire
        'license_number', 'license_category', 'license_categories',
        'license_expiry_date', // ✅ CORRECTION: Nom de colonne réel dans la DB
        'license_issue_date', 'license_authority',

        // Emploi et statut
        'recruitment_date', 'contract_end_date', 'status_id',

        // Contact d'urgence
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',

        // Photo et documents
        'photo', 'notes',

        // Superviseur
        'supervisor_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'recruitment_date' => 'date',
        'contract_end_date' => 'date',
        'license_expiry_date' => 'date', // ✅ CORRECTION: Nom de colonne réel dans la DB
        'license_issue_date' => 'date',
        'license_categories' => 'array', // Cast JSON pour les catégories multiples
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * 🔄 Accessors pour gérer la compatibilité entre champs dupliqués
     */

    // Gestion de la date de naissance
    public function getBirthDateAttribute()
    {
        $dateValue = $this->attributes['birth_date'] ?? null;

        if (!$dateValue) {
            return null;
        }

        return $this->asDate($dateValue);
    }

    // CORRECTION : Ajout des bons types de retour
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driverStatus(): BelongsTo
    {
        return $this->belongsTo(DriverStatus::class, 'status_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function repairRequests(): HasMany
    {
        return $this->hasMany(RepairRequest::class);
    }

    public function sanctions(): HasMany
    {
        return $this->hasMany(DriverSanction::class);
    }

    /**
     * ⚡ Relation pour récupérer l'affectation active en cours (sans date de fin)
     * Utilisé pour afficher le véhicule actuel dans la liste des chauffeurs
     */
    public function activeAssignment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Assignment::class)
            ->whereNull('end_datetime')
            ->orWhere(function ($query) {
                $query->where('end_datetime', '>=', now());
            })
            ->with('vehicle') // Eager load le véhicule
            ->latest('start_datetime');
    }

    /**
     * ⚡ Relation pour récupérer les sanctions actives
     * Utilisé pour déterminer le statut du chauffeur
     */
    public function activeSanctions(): HasMany
    {
        return $this->hasMany(DriverSanction::class)
            ->where('status', 'active')
            ->whereNull('archived_at');
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

    /**
     * Vérifie si le chauffeur a une affectation actuellement en cours.
     */
    public function isCurrentlyAssigned(): bool
    {
       return $this->assignments()->whereNull('end_datetime')->exists();
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}