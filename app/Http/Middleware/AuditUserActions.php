<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditUserActions
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Auditer uniquement les actions sensibles
        if ($this->shouldAudit($request)) {
            $this->logUserAction($request, $response);
        }
        
        return $response;
    }
    
    /**
     * Déterminer si l'action doit être auditée
     */
    private function shouldAudit(Request $request): bool
    {
        // Auditer les actions POST, PUT, PATCH, DELETE
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return false;
        }
        
        // Auditer uniquement certaines routes sensibles
        $auditRoutes = [
            'admin.users.',
            'admin.roles.',
            'admin.organizations.',
            'admin.vehicles.',
            'admin.drivers.'
        ];
        
        $routeName = $request->route()->getName();
        
        foreach ($auditRoutes as $pattern) {
            if (strpos($routeName, $pattern) === 0) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Logger l'action utilisateur
     */
    private function logUserAction(Request $request, $response): void
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }
        
        $logData = [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->getRoleNames()->first(),
            'action' => $request->method(),
            'route' => $request->route()->getName(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString(),
            'status_code' => $response->getStatusCode()
        ];
        
        // Ajouter les données sensibles si pertinentes
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $logData['request_data'] = $this->sanitizeRequestData($request->all());
        }
        
        Log::channel('audit')->info('User action', $logData);
    }
    
    /**
     * Nettoyer les données sensibles du log
     */
    private function sanitizeRequestData(array $data): array
    {
        $sensitive = ['password', 'password_confirmation', 'current_password'];
        
        foreach ($sensitive as $key) {
            if (isset($data[$key])) {
                $data[$key] = '[REDACTED]';
            }
        }
        
        return $data;
    }
}

