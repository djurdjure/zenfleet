<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * 🔒 GLOBAL SCOPE: User Vehicle Access Control
 * 
 * Filtre automatiquement les véhicules en fonction des droits d'accès de l'utilisateur:
 * - Super Admin: Tous les véhicules, toutes les organisations
 * - Admin: Tous les véhicules de son organisation
 * - Chauffeur: Véhicules assignés (assignments actives)
 * - Autres: Véhicules accordés manuellement (`user_vehicle`)
 */
class UserVehicleAccessScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();
        
        // Si pas d'utilisateur connecté, bloquer tout accès
        if (!$user) {
            $builder->whereRaw('1 = 0');
            return;
        }
        
        // Super Admin: Accès total, pas de filtre
        if ($user->hasRole('Super Admin')) {
            return;
        }
        
        // Admin: Tous les véhicules de son organisation
        if ($user->hasRole('Admin')) {
            $builder->where('organization_id', $user->organization_id);
            return;
        }
        
        // Pour les autres utilisateurs (chauffeurs et utilisateurs normaux)
        $builder->where(function($query) use ($user) {
            // 1. Véhicules accessibles via la table pivot (accès manuel)
            $query->whereHas('users', function($q) use ($user) {
                $q->where('users.id', $user->id);
            });
            
            // 2. Si l'utilisateur est un chauffeur, ajouter les véhicules assignés
            if ($user->driver) {
                $query->orWhereHas('assignments', function($q) use ($user) {
                    $q->where('driver_id', $user->driver->id)
                      ->where('status', 'active')
                      ->whereNull('end_datetime')
                      ->orWhere(function($sq) {
                          $sq->where('end_datetime', '>=', now());
                      });
                });
            }
        });
        
        // Assurer que l'utilisateur voit uniquement les véhicules de son organisation
        $builder->where('organization_id', $user->organization_id);
    }
}
