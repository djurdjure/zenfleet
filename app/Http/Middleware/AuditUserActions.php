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
        $start = microtime(true);
        $response = $next($request);
        
        // Auditer uniquement les actions sensibles
        if ($this->shouldAudit($request)) {
            $this->logUserAction($request, $response, $start);
        }
        
        return $response;
    }
    
    /**
     * Déterminer si l'action doit être auditée
     */
    private function shouldAudit(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return false;
        }

        // Auditer les actions POST, PUT, PATCH, DELETE
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $auditRoutes = [
                'admin.users.',
                'admin.roles.',
                'admin.organizations.',
                'admin.vehicles.',
                'admin.drivers.',
                'admin.assignments.',
                'admin.maintenance.',
                'admin.suppliers.',
                'admin.documents.',
                'admin.depots.',
                'admin.repair-requests.',
                'admin.mileage-readings.',
            ];

            foreach ($auditRoutes as $pattern) {
                if (strpos($routeName, $pattern) === 0) {
                    return true;
                }
            }

            return false;
        }

        // Auditer certains GET sensibles (exports, audits, PDF)
        if ($request->method() === 'GET') {
            $sensitiveGetPatterns = [
                '.export',
                '.download',
                '.pdf',
                '.audit',
                '.statistics',
            ];

            foreach ($sensitiveGetPatterns as $pattern) {
                if (str_contains($routeName, $pattern)) {
                    return true;
                }
            }
        }

        return false;
    }
    
    /**
     * Logger l'action utilisateur
     */
    private function logUserAction(Request $request, $response, float $start): void
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }
        
        $durationMs = (int) round((microtime(true) - $start) * 1000);
        $route = $request->route();
        $routeName = $route?->getName();

        $logData = [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->getRoleNames()->first(),
            'organization_id' => $user->organization_id,
            'action' => $request->method(),
            'route' => $routeName,
            'route_params' => $this->sanitizeRouteParams($route?->parameters() ?? []),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $durationMs,
            'request_id' => $request->headers->get('X-Request-Id') ?? null,
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
        $sensitive = [
            'password',
            'password_confirmation',
            'current_password',
            'token',
            '_token',
            'secret',
            'api_key',
        ];
        
        foreach ($sensitive as $key) {
            if (isset($data[$key])) {
                $data[$key] = '[REDACTED]';
            }
        }

        foreach ($data as $key => $value) {
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $data[$key] = [
                    'original_name' => $value->getClientOriginalName(),
                    'mime_type' => $value->getClientMimeType(),
                    'size' => $value->getSize(),
                ];
            }
        }
        
        return $data;
    }

    private function sanitizeRouteParams(array $params): array
    {
        foreach ($params as $key => $value) {
            if (is_object($value) && method_exists($value, 'getKey')) {
                $params[$key] = $value->getKey();
            } elseif (is_object($value)) {
                $params[$key] = (string) $value;
            }
        }

        return $params;
    }
}
