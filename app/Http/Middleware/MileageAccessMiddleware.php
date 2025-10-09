<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware Enterprise-Grade pour la gestion des accès au module Kilométrage
 * 
 * @package App\Http\Middleware
 * @version 1.0.0
 */
class MileageAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $permissionType = null): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Authentification requise');
        }

        // Super Admin et Admin ont toujours accès complet
        if ($user->hasRole(['Super Admin', 'Admin'])) {
            return $next($request);
        }

        // Gestionnaire Flotte avec permission complète
        if ($user->hasRole('Gestionnaire Flotte') && $user->can('view all mileage readings')) {
            return $next($request);
        }

        // Superviseur avec accès équipe
        if ($user->hasRole('Superviseur') && $user->can('view team mileage readings')) {
            return $next($request);
        }

        // Chauffeur ou autre rôle avec accès limité
        if ($user->can('view own mileage readings')) {
            return $next($request);
        }

        // Si un type de permission spécifique est requis
        if ($permissionType && $user->can($permissionType)) {
            return $next($request);
        }

        abort(403, 'Accès non autorisé au module kilométrage. Veuillez contacter votre administrateur.');
    }
}
