<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// CORRECTION : Ajout des bons namespaces pour les relations
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        // Champs de base
        'user_id', 'organization_id', 'first_name', 'last_name', 'email',

        // Informations personnelles
        'employee_number', 'birth_date', 'blood_type',
        'personal_phone', 'personal_email', 'address', 'city', 'postal_code',

        // Permis de conduire
        'license_number', 'license_category',
        'driver_license_expiry_date', 'license_issue_date', 'license_authority',

        // Emploi et statut
        'recruitment_date', 'contract_end_date', 'status_id',

        // Contact d'urgence
        'emergency_contact_name', 'emergency_contact_phone',

        // Photo et documents
        'photo', 'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'recruitment_date' => 'date',
        'contract_end_date' => 'date',
        'driver_license_expiry_date' => 'date',
        'license_issue_date' => 'date',
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