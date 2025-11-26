<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetTenantSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $userId = Auth::id();
            // Récupération de l'org ID via le user (suppose que user->organization_id est chargé)
            $orgId = Auth::user()->organization_id; 

            // Injection dans la session PostgreSQL pour RLS
            // IMPORTANT: PostgreSQL ne supporte pas les paramètres liés dans SET
            // On cast en int pour la sécurité (les IDs sont toujours des entiers)
            DB::statement("SET app.current_user_id = " . (int)$userId);
            
            if ($orgId) {
                DB::statement("SET app.current_organization_id = " . (int)$orgId);
            }
        }

        return $next($request);
    }
}
