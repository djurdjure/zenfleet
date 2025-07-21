<?php

namespace App\Models\Concerns;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToOrganization
{
    protected static function bootBelongsToOrganization()
    {
        // Filtre automatiquement toutes les requêtes de LECTURE
        static::addGlobalScope('organization', function (Builder $builder) {
            if (Auth::check() && Auth::user()->organization_id) {
                // Le Super Admin n'est PAS filtré, il voit tout.
                if (!Auth::user()->hasRole('Super Admin')) {
                    $builder->where($builder->getModel()->getTable() . '.organization_id', Auth::user()->organization_id);
                }
            }
        });

        // Lie automatiquement chaque NOUVEL enregistrement à l'organisation de l'utilisateur
        static::creating(function (Model $model) {
            if (Auth::check() && !isset($model->organization_id)) {
                $model->organization_id = Auth::user()->organization_id;
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}