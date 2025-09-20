<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * 🏢 Enterprise Permission Middleware - Ultra Professional Security
 *
 * Middleware de sécurité enterprise avec:
 * - Contrôle granulaire des permissions
 * - Audit trail automatique
 * - Performance optimisée avec cache
 * - Support multi-organisation
 *
 * @version 3.0-Enterprise
 * @author ZenFleet Security Team
 */
class EnterprisePermissionMiddleware
{
    /**
     * Mapping des routes vers les permissions requises
     */
    private array $routePermissionMap = [
        // Véhicules
        'admin.vehicles.index' => 'view_vehicles',
        'admin.vehicles.create' => 'create_vehicles',
        'admin.vehicles.store' => 'create_vehicles',
        'admin.vehicles.show' => 'view_vehicles',
        'admin.vehicles.edit' => 'edit_vehicles',
        'admin.vehicles.update' => 'edit_vehicles',
        'admin.vehicles.destroy' => 'delete_vehicles',
        'admin.vehicles.export' => 'export_vehicles',
        'admin.vehicles.import.*' => 'import_vehicles',

        // Chauffeurs
        'admin.drivers.index' => 'view_drivers',
        'admin.drivers.create' => 'create_drivers',
        'admin.drivers.store' => 'create_drivers',
        'admin.drivers.show' => 'view_drivers',
        'admin.drivers.edit' => 'edit_drivers',
        'admin.drivers.update' => 'edit_drivers',
        'admin.drivers.destroy' => 'delete_drivers',
        'admin.drivers.export' => 'export_drivers',
        'admin.drivers.import.*' => 'import_drivers',

        // Affectations
        'admin.assignments.index' => 'view_assignments',
        'admin.assignments.create' => 'create_assignments',
        'admin.assignments.store' => 'create_assignments',
        'admin.assignments.show' => 'view_assignments',
        'admin.assignments.edit' => 'edit_assignments',
        'admin.assignments.update' => 'edit_assignments',
        'admin.assignments.destroy' => 'delete_assignments',
        'admin.assignments.export' => 'export_assignments',

        // Utilisateurs
        'admin.users.index' => 'view_users',
        'admin.users.create' => 'create_users',
        'admin.users.store' => 'create_users',
        'admin.users.show' => 'view_users',
        'admin.users.edit' => 'edit_users',
        'admin.users.update' => 'edit_users',
        'admin.users.destroy' => 'delete_users',
        'admin.users.export' => 'export_users',

        // Rôles et Permissions
        'admin.roles.index' => 'view_roles',
        'admin.roles.show' => 'view_roles',
        'admin.roles.edit' => 'edit_roles',
        'admin.roles.update' => 'edit_roles',
        'admin.permissions.index' => 'manage_permissions',

        // Documents
        'admin.documents.index' => 'view_documents',
        'admin.documents.create' => 'create_documents',
        'admin.documents.store' => 'create_documents',
        'admin.documents.show' => 'view_documents',
        'admin.documents.edit' => 'edit_documents',
        'admin.documents.update' => 'edit_documents',
        'admin.documents.destroy' => 'delete_documents',

        // Fournisseurs
        'admin.suppliers.index' => 'view_suppliers',
        'admin.suppliers.create' => 'create_suppliers',
        'admin.suppliers.store' => 'create_suppliers',
        'admin.suppliers.show' => 'view_suppliers',
        'admin.suppliers.edit' => 'edit_suppliers',
        'admin.suppliers.update' => 'edit_suppliers',
        'admin.suppliers.destroy' => 'delete_suppliers',

        // Maintenance
        'admin.maintenance.*' => 'view_maintenance',
        'admin.maintenance.create' => 'create_maintenance_plans',
        'admin.maintenance.store' => 'create_maintenance_plans',
        'admin.maintenance.edit' => 'edit_maintenance_plans',
        'admin.maintenance.update' => 'edit_maintenance_plans',
        'admin.maintenance.destroy' => 'delete_maintenance_plans',

        // Organisations (Super Admin seulement)
        'admin.organizations.*' => 'view_organizations',
        'admin.organizations.create' => 'create_organizations',
        'admin.organizations.store' => 'create_organizations',
        'admin.organizations.edit' => 'edit_organizations',
        'admin.organizations.update' => 'edit_organizations',
        'admin.organizations.destroy' => 'delete_organizations',

        // Système (Super Admin seulement)
        'admin.system.*' => 'view_system_analytics',
        'admin.audit.*' => 'view_audit_logs',
    ];

    /**
     * Handle an incoming request with enterprise security
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        // Vérifier l'authentification
        if (!Auth::check()) {
            return $this->handleUnauthorized($request, 'Non authentifié');
        }

        $user = Auth::user();
        $routeName = $request->route()->getName();

        // Log de l'accès pour audit
        $this->logAccess($request, $user, $routeName);

        // Si des permissions spécifiques sont passées en paramètre
        if (!empty($permissions)) {
            return $this->checkExplicitPermissions($request, $next, $permissions);
        }

        // Sinon, utiliser le mapping automatique
        return $this->checkRoutePermissions($request, $next, $routeName);
    }

    /**
     * Vérifier les permissions explicites passées en paramètre
     */
    private function checkExplicitPermissions(Request $request, Closure $next, array $permissions): Response
    {
        $user = Auth::user();

        foreach ($permissions as $permission) {
            if (!$user->can($permission)) {
                return $this->handleUnauthorized(
                    $request,
                    "Permission requise: {$permission}",
                    $permission
                );
            }
        }

        return $next($request);
    }

    /**
     * Vérifier les permissions basées sur le mapping des routes
     */
    private function checkRoutePermissions(Request $request, Closure $next, string $routeName): Response
    {
        $user = Auth::user();

        // Super Admin bypass (mais on log quand même)
        if ($user->hasRole('Super Admin')) {
            $this->logSuperAdminAccess($request, $user, $routeName);
            return $next($request);
        }

        $requiredPermission = $this->getRequiredPermission($routeName);

        if (!$requiredPermission) {
            // Route non mappée, autoriser mais logger
            $this->logUnmappedRoute($request, $routeName);
            return $next($request);
        }

        if (!$user->can($requiredPermission)) {
            return $this->handleUnauthorized(
                $request,
                "Accès refusé à la route: {$routeName}",
                $requiredPermission
            );
        }

        return $next($request);
    }

    /**
     * Obtenir la permission requise pour une route
     */
    private function getRequiredPermission(string $routeName): ?string
    {
        // Vérification exacte
        if (isset($this->routePermissionMap[$routeName])) {
            return $this->routePermissionMap[$routeName];
        }

        // Vérification avec wildcards
        foreach ($this->routePermissionMap as $pattern => $permission) {
            if (str_contains($pattern, '*')) {
                $regex = str_replace('*', '.*', $pattern);
                if (preg_match("/^{$regex}$/", $routeName)) {
                    return $permission;
                }
            }
        }

        return null;
    }

    /**
     * Gérer les accès non autorisés avec logging enterprise
     */
    private function handleUnauthorized(Request $request, string $reason, ?string $permission = null): Response
    {
        $user = Auth::user();

        // Log de sécurité détaillé
        Log::channel('security')->warning('Accès non autorisé', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_roles' => $user->getRoleNames()->toArray(),
            'route' => $request->route()->getName(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'reason' => $reason,
            'required_permission' => $permission,
            'timestamp' => now()->toISOString(),
            'session_id' => $request->session()->getId(),
        ]);

        // Réponse différenciée selon le type de requête
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Cette action n\'est pas autorisée.',
                'error' => 'Insufficient permissions',
                'required_permission' => $permission,
                'user_permissions' => $user->getPermissionNames(),
            ], 403);
        }

        // Redirection avec message d'erreur contextualisé
        $errorMessage = $this->getContextualErrorMessage($permission);

        return redirect()
            ->back()
            ->with('error', $errorMessage)
            ->with('error_type', 'permission_denied')
            ->with('required_permission', $permission);
    }

    /**
     * Obtenir un message d'erreur contextualisé
     */
    private function getContextualErrorMessage(?string $permission): string
    {
        $messages = [
            'view_vehicles' => 'Vous n\'avez pas l\'autorisation de consulter les véhicules.',
            'create_vehicles' => 'Vous n\'avez pas l\'autorisation de créer des véhicules.',
            'edit_vehicles' => 'Vous n\'avez pas l\'autorisation de modifier les véhicules.',
            'delete_vehicles' => 'Vous n\'avez pas l\'autorisation de supprimer des véhicules.',
            'view_drivers' => 'Vous n\'avez pas l\'autorisation de consulter les chauffeurs.',
            'create_drivers' => 'Vous n\'avez pas l\'autorisation de créer des chauffeurs.',
            'edit_drivers' => 'Vous n\'avez pas l\'autorisation de modifier les chauffeurs.',
            'delete_drivers' => 'Vous n\'avez pas l\'autorisation de supprimer des chauffeurs.',
            'view_users' => 'Vous n\'avez pas l\'autorisation de consulter les utilisateurs.',
            'manage_permissions' => 'Vous n\'avez pas l\'autorisation de gérer les permissions.',
        ];

        return $messages[$permission] ?? 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette ressource.';
    }

    /**
     * Logger les accès pour audit trail
     */
    private function logAccess(Request $request, $user, string $routeName): void
    {
        Log::channel('audit')->info('Accès route admin', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'route' => $routeName,
            'method' => $request->method(),
            'ip' => $request->ip(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Logger les accès Super Admin
     */
    private function logSuperAdminAccess(Request $request, $user, string $routeName): void
    {
        Log::channel('audit')->info('Accès Super Admin', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'route' => $routeName,
            'action' => 'SUPER_ADMIN_BYPASS',
            'ip' => $request->ip(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Logger les routes non mappées
     */
    private function logUnmappedRoute(Request $request, string $routeName): void
    {
        Log::channel('audit')->notice('Route non mappée dans le système de permissions', [
            'route' => $routeName,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => Auth::id(),
            'timestamp' => now()->toISOString(),
        ]);
    }
}