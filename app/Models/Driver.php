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
        'user_id', 'employee_number', 'first_name', 'last_name', 'photo_path', 'birth_date',
        'blood_type', 'address', 'personal_phone', 'personal_email', 'license_number',
        'license_category', 'license_issue_date', 'license_authority', 'license_expiry_date',
        'recruitment_date', 'contract_end_date', 'status_id', 'emergency_contact_name',
        'emergency_contact_phone', 'organization_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'license_issue_date' => 'date',
        'license_expiry_date' => 'date',
        'recruitment_date' => 'date',
        'contract_end_date' => 'date',
    ];

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
}