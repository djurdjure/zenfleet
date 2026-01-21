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

    // Tableau des types pour les formulaires et filtres
    public const TYPES = [
        self::TYPE_MECANICIEN => 'Mécanicien',
        self::TYPE_ASSUREUR => 'Assureur',
        self::TYPE_STATION_SERVICE => 'Station Service',
        self::TYPE_PIECES_DETACHEES => 'Pièces Détachées',
        self::TYPE_PEINTURE_CARROSSERIE => 'Peinture & Carrosserie',
        self::TYPE_PNEUMATIQUES => 'Pneumatiques',
        self::TYPE_ELECTRICITE_AUTO => 'Électricité Auto',
        self::TYPE_CONTROLE_TECHNIQUE => 'Contrôle Technique',
        self::TYPE_TRANSPORT_VEHICULES => 'Transport Véhicules',
        self::TYPE_AUTRE => 'Autre',
    ];

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
        'tax_rate',
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
        'tax_rate' => 'decimal:2',
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
        if ($nif === null) {
            return false;
        }

        $nif = (string) $nif;

        return preg_match('/^[0-9]{15}$/', $nif) === 1;
    }

    public static function validateNIS($nis): bool
    {
        if ($nis === null) {
            return false;
        }

        $nis = (string) $nis;

        return preg_match('/^[0-9]{15}$/', $nis) === 1;
    }

    public static function validateTradeRegister($rc): bool
    {
        if ($rc === null) {
            return false;
        }

        $rc = (string) $rc;

        if (preg_match('/^[0-9]{2}\/[0-9]{2}-[0-9]{7}$/', $rc) !== 1) {
            return false;
        }

        $wilayaCode = substr($rc, 0, 2);

        return array_key_exists($wilayaCode, self::WILAYAS);
    }

    public static function validateRIB($rib): bool
    {
        if ($rib === null) {
            return false;
        }

        $rib = (string) $rib;

        return preg_match('/^[0-9]{20}$/', $rib) === 1;
    }

    public static function isValidWilaya($wilaya): bool
    {
        if ($wilaya === null) {
            return false;
        }

        $wilaya = trim((string) $wilaya);

        if ($wilaya === '') {
            return false;
        }

        if (array_key_exists($wilaya, self::WILAYAS)) {
            return true;
        }

        $normalized = self::normalizeWilayaName($wilaya);

        foreach (self::WILAYAS as $name) {
            if (self::normalizeWilayaName($name) === $normalized) {
                return true;
            }
        }

        return false;
    }

    public static function getWilayaCode($wilaya): ?string
    {
        if ($wilaya === null) {
            return null;
        }

        $wilaya = trim((string) $wilaya);

        if ($wilaya === '') {
            return null;
        }

        if (array_key_exists($wilaya, self::WILAYAS)) {
            return $wilaya;
        }

        $normalized = self::normalizeWilayaName($wilaya);

        foreach (self::WILAYAS as $code => $name) {
            if (self::normalizeWilayaName($name) === $normalized) {
                return $code;
            }
        }

        return null;
    }

    public static function extractWilayaCodeFromTradeRegister($register): ?string
    {
        if (!self::validateTradeRegister($register)) {
            return null;
        }

        $register = (string) $register;

        return substr($register, 0, 2);
    }

    public static function validateAlgerianPhone($phone): bool
    {
        if ($phone === null) {
            return false;
        }

        $phone = (string) $phone;

        if ($phone === '') {
            return false;
        }

        $landlinePattern = '/^\+213-(2[1-9]|3[1-8]|4[1-9])-\d{6}$/';
        $mobilePattern = '/^\+213-(5|6|7)\d{2}-\d{6}$/';

        return preg_match($landlinePattern, $phone) === 1
            || preg_match($mobilePattern, $phone) === 1;
    }

    public static function isValidTvaRate($rate): bool
    {
        if (!is_numeric($rate)) {
            return false;
        }

        $rate = (float) $rate;

        return in_array($rate, [0.0, 9.0, 19.0], true);
    }

    public static function validateCompleteData(array $data): bool
    {
        $required = [
            'nif' => [self::class, 'validateNIF'],
            'nis' => [self::class, 'validateNIS'],
            'trade_register' => [self::class, 'validateTradeRegister'],
            'rib' => [self::class, 'validateRIB'],
            'wilaya' => [self::class, 'isValidWilaya'],
            'phone' => [self::class, 'validateAlgerianPhone'],
            'tax_rate' => [self::class, 'isValidTvaRate'],
        ];

        foreach ($required as $key => $validator) {
            if (!array_key_exists($key, $data)) {
                return false;
            }

            if (!call_user_func($validator, $data[$key])) {
                return false;
            }
        }

        return true;
    }

    protected static function normalizeWilayaName(string $name): string
    {
        $normalized = trim($name);
        $normalized = mb_strtolower($normalized);
        $normalized = str_replace(['’', '\'', '-', ' '], '', $normalized);

        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $normalized);
        if ($ascii !== false && $ascii !== '') {
            $normalized = $ascii;
        }

        return $normalized;
    }

    public function calculateComplianceScore(): int
    {
        $checks = [
            self::validateNIF($this->nif ?? null),
            self::validateNIS($this->nis ?? null),
            self::validateTradeRegister($this->trade_register ?? null),
            self::validateRIB($this->rib ?? null),
            self::isValidWilaya($this->wilaya ?? null),
            self::validateAlgerianPhone($this->phone ?? null),
            !empty($this->certifications),
            self::isValidTvaRate($this->tax_rate ?? null),
        ];

        $total = count($checks);
        $passed = count(array_filter($checks));

        if ($total === 0) {
            return 0;
        }

        return (int) round(($passed / $total) * 100);
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
