<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'name',
        'contact_name',
        'phone',
        'email',
        'address',
        'supplier_category_id',
        'organization_id',
    ];

    /**
     * Relation : Un fournisseur appartient à une catégorie.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SupplierCategory::class, 'supplier_category_id');
    }

    /**
     * Relation : Un fournisseur peut être lié à plusieurs dépenses.
     * (Anticipation pour le futur module de gestion des dépenses)
     */
    public function expenses(): HasMany
    {
        // Note : Le modèle Expense sera à créer dans le futur.
        // return $this->hasMany(Expense::class);
        return $this->hasMany('App\Models\Expense'); // Placeholder
    }

    /**
     * Relation : Un fournisseur peut être lié à plusieurs interventions de maintenance.
     * (Anticipation pour le futur module de maintenance)
     */
    public function maintenances(): HasMany
    {
        // Note : Le modèle MaintenanceLog existe déjà.
        return $this->hasMany(Maintenance\MaintenanceLog::class);
    }

    // La relation organization() est maintenant gérée par le trait BelongsToOrganization.
}
