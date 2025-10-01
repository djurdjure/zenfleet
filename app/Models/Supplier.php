<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    // Constantes pour les types de fournisseurs
    public const TYPE_MECANICIEN = 'mecanicien';
    public const TYPE_ASSUREUR = 'assureur';
    public const TYPE_STATION_SERVICE = 'station_service';
    public const TYPE_PIECES_DETACHEES = 'pieces_detachees';
    public const TYPE_PEINTURE_CARROSSERIE = 'peinture_carrosserie';
    public const TYPE_PNEUMATIQUES = 'pneumatiques';
    public const TYPE_ELECTRICITE_AUTO = 'electricite_auto';
    public const TYPE_CONTROLE_TECHNIQUE = 'controle_technique';
    public const TYPE_TRANSPORT_VEHICULES = 'transport_vehicules';
    public const TYPE_AUTRE = 'autre';

    // Constantes pour les 58 wilayas d'Algérie (complètes)
    public const WILAYAS = [
        '01' => 'Adrar', '02' => 'Chlef', '03' => 'Laghouat', '04' => 'Oum El Bouaghi',
        '05' => 'Batna', '06' => 'Béjaïa', '07' => 'Biskra', '08' => 'Béchar',
        '09' => 'Blida', '10' => 'Bouira', '11' => 'Tamanrasset', '12' => 'Tébessa',
        '13' => 'Tlemcen', '14' => 'Tiaret', '15' => 'Tizi Ouzou', '16' => 'Alger',
        '17' => 'Djelfa', '18' => 'Jijel', '19' => 'Sétif', '20' => 'Saïda',
        '21' => 'Skikda', '22' => 'Sidi Bel Abbès', '23' => 'Annaba', '24' => 'Guelma',
        '25' => 'Constantine', '26' => 'Médéa', '27' => 'Mostaganem', '28' => 'M\'Sila',
        '29' => 'Mascara', '30' => 'Ouargla', '31' => 'Oran', '32' => 'El Bayadh',
        '33' => 'Illizi', '34' => 'Bordj Bou Arréridj', '35' => 'Boumerdès',
        '36' => 'El Tarf', '37' => 'Tindouf', '38' => 'Tissemsilt', '39' => 'El Oued',
        '40' => 'Khenchela', '41' => 'Souk Ahras', '42' => 'Tipaza', '43' => 'Mila',
        '44' => 'Aïn Defla', '45' => 'Naâma', '46' => 'Aïn Témouchent', '47' => 'Ghardaïa',
        '48' => 'Relizane', '49' => 'Timimoun', '50' => 'Bordj Badji Mokhtar',
        '51' => 'Ouled Djellal', '52' => 'Béni Abbès', '53' => 'In Salah',
        '54' => 'In Guezzam', '55' => 'Touggourt', '56' => 'Djanet',
        '57' => 'El Meghaier', '58' => 'El Ménéa'
    ];

    protected $fillable = [
        'organization_id',
        'supplier_type',
        'company_name',
        'trade_register',
        'nif',
        'nis',
        'ai',
        'contact_first_name',
        'contact_last_name',
        'contact_phone',
        'contact_email',
        'address',
        'city',
        'wilaya',
        'commune',
        'postal_code',
        'phone',
        'email',
        'website',
        'specialties',
        'certifications',
        'service_areas',
        'rating',
        'response_time_hours',
        'quality_score',
        'reliability_score',
        'contract_start_date',
        'contract_end_date',
        'payment_terms',
        'preferred_payment_method',
        'credit_limit',
        'bank_name',
        'account_number',
        'rib',
        'is_active',
        'is_preferred',
        'is_certified',
        'blacklisted',
        'blacklist_reason',
        'documents',
        'notes'
    ];

    protected $casts = [
        'specialties' => 'array',
        'certifications' => 'array',
        'service_areas' => 'array',
        'documents' => 'array',
        'rating' => 'decimal:2',
        'quality_score' => 'decimal:2',
        'reliability_score' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'avg_order_value' => 'decimal:2',
        'total_amount_spent' => 'decimal:2',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'last_order_date' => 'datetime',
        'is_active' => 'boolean',
        'is_preferred' => 'boolean',
        'is_certified' => 'boolean',
        'blacklisted' => 'boolean'
    ];

    // Relations
    public function repairRequests(): HasMany
    {
        return $this->hasMany(RepairRequest::class, 'assigned_supplier_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(SupplierRating::class);
    }

    public function vehicleExpenses(): HasMany
    {
        return $this->hasMany(VehicleExpense::class);
    }

    // Relations legacy (maintien de la compatibilité)
    public function category(): BelongsTo
    {
        return $this->belongsTo(SupplierCategory::class, 'supplier_category_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany('App\Models\Expense');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance\MaintenanceLog::class);
    }

    // Scopes pour filtrage
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePreferred($query)
    {
        return $query->where('is_preferred', true);
    }

    public function scopeCertified($query)
    {
        return $query->where('is_certified', true);
    }

    public function scopeNotBlacklisted($query)
    {
        return $query->where('blacklisted', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('supplier_type', $type);
    }

    public function scopeByWilaya($query, $wilaya)
    {
        return $query->where('wilaya', $wilaya);
    }

    public function scopeByServiceArea($query, $wilaya)
    {
        return $query->where('service_areas', 'LIKE', '%"' . $wilaya . '"%');
    }

    public function scopeWithRating($query, $minRating = 5.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeSearchByName($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('company_name', 'ILIKE', '%' . $search . '%')
              ->orWhere('contact_first_name', 'ILIKE', '%' . $search . '%')
              ->orWhere('contact_last_name', 'ILIKE', '%' . $search . '%');
        });
    }

    // Méthodes de validation algérienne
    public static function validateNIF($nif): bool
    {
        return preg_match('/^[0-9]{15}$/', $nif) === 1;
    }

    public static function validateTradeRegister($rc): bool
    {
        return preg_match('/^[0-9]{2}\/[0-9]{2}-[0-9]{7}$/', $rc) === 1;
    }

    public static function validateRIB($rib): bool
    {
        return preg_match('/^[0-9]{20}$/', $rib) === 1;
    }

    // Méthodes de gestion des évaluations
    public function updateRatingsFromReviews(): void
    {
        $ratings = $this->ratings;

        if ($ratings->count() > 0) {
            $this->update([
                'rating' => $ratings->avg('overall_rating'),
                'quality_score' => $ratings->avg('quality_rating'),
                'reliability_score' => $ratings->avg('timeliness_rating')
            ]);
        }
    }

    // Méthodes utilitaires
    public function canServiceWilaya($wilaya): bool
    {
        return in_array($wilaya, $this->service_areas ?? []) || $this->wilaya === $wilaya;
    }

    public function isAvailableForRepair(): bool
    {
        return $this->is_active && !$this->blacklisted;
    }

    public function hasSpecialty($specialty): bool
    {
        return in_array($specialty, $this->specialties ?? []);
    }

    public function hasCertification($certification): bool
    {
        return in_array($certification, $this->certifications ?? []);
    }

    public function blacklist($reason = null): bool
    {
        return $this->update([
            'blacklisted' => true,
            'is_active' => false,
            'blacklist_reason' => $reason
        ]);
    }

    public function unblacklist(): bool
    {
        return $this->update([
            'blacklisted' => false,
            'is_active' => true,
            'blacklist_reason' => null
        ]);
    }

    // Accesseurs
    public function getSupplierTypeLabelAttribute(): string
    {
        return match($this->supplier_type) {
            self::TYPE_MECANICIEN => 'Mécanicien',
            self::TYPE_ASSUREUR => 'Assureur',
            self::TYPE_STATION_SERVICE => 'Station-service',
            self::TYPE_PIECES_DETACHEES => 'Pièces détachées',
            self::TYPE_PEINTURE_CARROSSERIE => 'Peinture & Carrosserie',
            self::TYPE_PNEUMATIQUES => 'Pneumatiques',
            self::TYPE_ELECTRICITE_AUTO => 'Électricité Auto',
            self::TYPE_CONTROLE_TECHNIQUE => 'Contrôle Technique',
            self::TYPE_TRANSPORT_VEHICULES => 'Transport de Véhicules',
            self::TYPE_AUTRE => 'Autre',
            default => 'Non défini'
        };
    }

    public function getWilayaLabelAttribute(): string
    {
        return self::WILAYAS[$this->wilaya] ?? $this->wilaya;
    }

    public function getFullAddressAttribute(): string
    {
        $address = $this->address;

        if ($this->commune) {
            $address .= ', ' . $this->commune;
        }

        $address .= ', ' . $this->city;
        $address .= ', ' . $this->wilaya_label;

        if ($this->postal_code) {
            $address .= ' ' . $this->postal_code;
        }

        return $address;
    }

    public function getContactNameAttribute(): string
    {
        return $this->contact_first_name . ' ' . $this->contact_last_name;
    }

    public function getRatingColorAttribute(): string
    {
        if ($this->rating >= 8) return 'green';
        if ($this->rating >= 6) return 'yellow';
        if ($this->rating >= 4) return 'orange';
        return 'red';
    }

    public function getStatusBadgeAttribute(): array
    {
        if ($this->blacklisted) {
            return ['color' => 'red', 'label' => 'Blacklisté'];
        }

        if (!$this->is_active) {
            return ['color' => 'gray', 'label' => 'Inactif'];
        }

        if ($this->is_preferred) {
            return ['color' => 'purple', 'label' => 'Privilégié'];
        }

        if ($this->is_certified) {
            return ['color' => 'blue', 'label' => 'Certifié'];
        }

        return ['color' => 'green', 'label' => 'Actif'];
    }

    // Méthodes statiques utilitaires
    public static function getSupplierTypes(): array
    {
        return [
            self::TYPE_MECANICIEN => 'Mécanicien',
            self::TYPE_ASSUREUR => 'Assureur',
            self::TYPE_STATION_SERVICE => 'Station-service',
            self::TYPE_PIECES_DETACHEES => 'Pièces détachées',
            self::TYPE_PEINTURE_CARROSSERIE => 'Peinture & Carrosserie',
            self::TYPE_PNEUMATIQUES => 'Pneumatiques',
            self::TYPE_ELECTRICITE_AUTO => 'Électricité Auto',
            self::TYPE_CONTROLE_TECHNIQUE => 'Contrôle Technique',
            self::TYPE_TRANSPORT_VEHICULES => 'Transport de Véhicules',
            self::TYPE_AUTRE => 'Autre'
        ];
    }

    public static function getPaymentMethods(): array
    {
        return [
            'virement' => 'Virement bancaire',
            'cheque' => 'Chèque',
            'especes' => 'Espèces',
            'carte' => 'Carte bancaire',
            'traite' => 'Traite'
        ];
    }
}
