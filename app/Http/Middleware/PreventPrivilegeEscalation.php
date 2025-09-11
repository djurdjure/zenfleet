<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PreventPrivilegeEscalation
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Vérifier les tentatives d'assignation de rôle Super Admin
        if ($request->has('roles') || $request->has('role')) {
            $roles = $request->input('roles', [$request->input('role')]);
            
            foreach ($roles as $roleIdOrName) {
                // Détecter tentative d'assignation Super Admin par non-Super Admin
                if ((is_string($roleIdOrName) && $roleIdOrName === 'Super Admin') ||
                    (is_numeric($roleIdOrName) && $this->isRoleIdSuperAdmin($roleIdOrName))) {
                    
                    if (!$user->hasRole('Super Admin')) {
                        Log::warning('Tentative d\'escalation de privilèges bloquée', [
                            'user' => $user->email,
                            'attempted_role' => $roleIdOrName,
                            'ip' => $request->ip(),
                            'route' => $request->route()->getName()
                        ]);
                        
                        abort(403, 'Tentative d\'escalation de privilèges détectée et bloquée.');
                    }
                }
            }
        }
        
        return $next($request);
    }
    
    private function isRoleIdSuperAdmin($roleId): bool
    {
        try {
            $role = \Spatie\Permission\Models\Role::find($roleId);
            return $role && $role->name === 'Super Admin';
        } catch (\Exception $e) {
            return false;
        }
    }
}

