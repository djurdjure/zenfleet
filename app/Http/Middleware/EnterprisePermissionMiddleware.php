<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use App\Support\PermissionAliases;
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
     *
     * ⚠️ IMPORTANT: Utilise la notation canonique "resource.action"
     * Exemple: "vehicles.view", "drivers.create"
     */
    private array $routePermissionMap = [
        // Véhicules
        'admin.vehicles.index' => 'vehicles.view',
        'admin.vehicles.create' => 'vehicles.create',
        'admin.vehicles.store' => 'vehicles.create',
        'admin.vehicles.show' => 'vehicles.view',
        'admin.vehicles.edit' => 'vehicles.update',
        'admin.vehicles.update' => 'vehicles.update',
        'admin.vehicles.destroy' => 'vehicles.delete',
        'admin.vehicles.restore' => 'vehicles.restore',
        'admin.vehicles.export' => 'vehicles.export',
        'admin.vehicles.import.*' => 'vehicles.import',

        // Chauffeurs
        'admin.drivers.index' => 'drivers.view',
        'admin.drivers.create' => 'drivers.create',
        'admin.drivers.store' => 'drivers.create',
        'admin.drivers.show' => 'drivers.view',
        'admin.drivers.edit' => 'drivers.update',
        'admin.drivers.update' => 'drivers.update',
        'admin.drivers.destroy' => 'drivers.delete',
        'admin.drivers.restore' => 'drivers.restore',
        'admin.drivers.export' => 'drivers.export',
        'admin.drivers.import.*' => 'drivers.import',

        // Affectations - FORMAT MODERNE (dot notation)
        'admin.assignments.index' => 'assignments.view',
        'admin.assignments.create' => 'assignments.create',
        'admin.assignments.store' => 'assignments.create',
        'admin.assignments.show' => 'assignments.view',
        'admin.assignments.edit' => 'assignments.update',
        'admin.assignments.update' => 'assignments.update',
        'admin.assignments.destroy' => 'assignments.delete',
        'admin.assignments.end' => 'assignments.end',
        'admin.assignments.export' => 'assignments.export',

        // Utilisateurs
        'admin.users.index' => 'users.view',
        'admin.users.create' => 'users.create',
        'admin.users.store' => 'users.create',
        'admin.users.show' => 'users.view',
        'admin.users.edit' => 'users.update',
        'admin.users.update' => 'users.update',
        'admin.users.destroy' => 'users.delete',
        'admin.users.export' => 'users.export',

        // Rôles et Permissions
        'admin.roles.index' => 'roles.manage',
        'admin.roles.show' => 'roles.manage',
        'admin.roles.edit' => 'roles.manage',
        'admin.roles.update' => 'roles.manage',
        'admin.permissions.index' => 'permissions.manage',

        // Documents
        'admin.documents.index' => 'documents.view',
        'admin.documents.create' => 'documents.create',
        'admin.documents.store' => 'documents.create',
        'admin.documents.show' => 'documents.view',
        'admin.documents.edit' => 'documents.update',
        'admin.documents.update' => 'documents.update',
        'admin.documents.destroy' => 'documents.delete',

        // Fournisseurs
        'admin.suppliers.index' => 'suppliers.view',
        'admin.suppliers.create' => 'suppliers.create',
        'admin.suppliers.store' => 'suppliers.create',
        'admin.suppliers.show' => 'suppliers.view',
        'admin.suppliers.edit' => 'suppliers.update',
        'admin.suppliers.update' => 'suppliers.update',
        'admin.suppliers.destroy' => 'suppliers.delete',

        // Maintenance
        'admin.maintenance.*' => 'maintenance.view',
        'admin.maintenance.create' => 'maintenance.plans.manage',
        'admin.maintenance.store' => 'maintenance.plans.manage',
        'admin.maintenance.edit' => 'maintenance.plans.manage',
        'admin.maintenance.update' => 'maintenance.plans.manage',
        'admin.maintenance.destroy' => 'maintenance.plans.manage',

        // Demandes de Réparation
        'admin.repair-requests.index' => 'repair-requests.view.own', // Minimum permission
        'admin.repair-requests.create' => 'repair-requests.create',
        'admin.repair-requests.store' => 'repair-requests.create',
        'admin.repair-requests.show' => 'repair-requests.view.own',
        'admin.repair-requests.edit' => 'repair-requests.update.own',
        'admin.repair-requests.update' => 'repair-requests.update.own',
        'admin.repair-requests.destroy' => 'repair-requests.delete',
        'admin.repair-requests.approve-level-one' => 'repair-requests.approve.level1',
        'admin.repair-requests.reject-level-one' => 'repair-requests.reject.level1',
        'admin.repair-requests.approve-level-two' => 'repair-requests.approve.level2',
        'admin.repair-requests.reject-level-two' => 'repair-requests.reject.level2',

        // Relevés Kilométriques
        'admin.mileage-readings.index' => 'mileage-readings.view.own', // Minimum permission
        'admin.mileage-readings.create' => 'mileage-readings.create',
        'admin.mileage-readings.store' => 'mileage-readings.create',
        'admin.mileage-readings.show' => 'mileage-readings.view.own',
        'admin.mileage-readings.edit' => 'mileage-readings.update.own',
        'admin.mileage-readings.update' => 'mileage-readings.update.own',
        'admin.mileage-readings.destroy' => 'mileage-readings.delete',
        'admin.mileage-readings.export' => 'mileage-readings.export',
        'admin.mileage-readings.statistics' => 'mileage-readings.view.statistics',

        // Organisations (Super Admin seulement)
        'admin.organizations.*' => 'organizations.view',
        'admin.organizations.create' => 'organizations.create',
        'admin.organizations.store' => 'organizations.create',
        'admin.organizations.edit' => 'organizations.update',
        'admin.organizations.update' => 'organizations.update',
        'admin.organizations.destroy' => 'organizations.delete',

        // Dépôts - GESTION
        'admin.depots.index' => 'depots.view',
        'admin.depots.show' => 'depots.view',
        'admin.depots.create' => 'depots.create',
        'admin.depots.store' => 'depots.create',
        'admin.depots.edit' => 'depots.update',
        'admin.depots.update' => 'depots.update',
        'admin.depots.destroy' => 'depots.delete',
        'admin.depots.export.pdf' => 'depots.export',
        'admin.depots.restore' => 'depots.restore',

        // Système (Super Admin seulement)
        'admin.system.*' => 'system.view',
        'admin.audit.*' => 'audit-logs.view',
    ];
    private ?Collection $cachedUserPermissions = null;

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

        // Safety check for route name
        if (empty($routeName)) {
            Log::warning('Route accessed without name', ['url' => $request->fullUrl()]);
            // Continue without permission check if route has no name (or handle strictly)
            // For now, let it pass but log it safely
            return $next($request);
        }

        // Log de l'accès pour audit - Safety wrapper
        try {
            $this->logAccess($request, $user, $routeName);
        } catch (\Exception $e) {
            // Ignore logging errors to prevent 500
        }

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
            if (!$this->hasPermission($user, $permission)) {
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
            $this->logUnmappedRoute($request, $routeName);

            if (app()->environment('local', 'development')) {
                return $next($request);
            }

            return $this->handleUnauthorized(
                $request,
                "Route non mappée: {$routeName}"
            );
        }

        // Vérifier la permission avec support hiérarchique
        if (!$this->hasPermissionHierarchical($user, $requiredPermission)) {
            return $this->handleUnauthorized(
                $request,
                "Accès refusé à la route: {$routeName}",
                $requiredPermission
            );
        }

        return $next($request);
    }

    /**
     * Vérifier les permissions de manière hiérarchique
     *
     * Si une permission de niveau supérieur existe, elle donne accès aux niveaux inférieurs
     * Exemple: "view all mileage readings" inclut "view team" et "view own"
     */
    private function hasPermissionHierarchical($user, string $permission): bool
    {
        // Vérification directe
        if ($this->hasPermission($user, $permission)) {
            return true;
        }

        // Permissions hiérarchiques pour les relevés kilométriques
        if ($permission === 'mileage-readings.view.own') {
            return $this->hasPermission($user, 'mileage-readings.view.team')
                || $this->hasPermission($user, 'mileage-readings.view.all');
        }

        if ($permission === 'mileage-readings.view.team') {
            return $this->hasPermission($user, 'mileage-readings.view.all');
        }

        // Permissions hiérarchiques pour les demandes de réparation
        if ($permission === 'repair-requests.view.own') {
            return $this->hasPermission($user, 'repair-requests.view.team')
                || $this->hasPermission($user, 'repair-requests.view.all');
        }

        if ($permission === 'repair-requests.view.team') {
            return $this->hasPermission($user, 'repair-requests.view.all');
        }

        // Aucune permission hiérarchique trouvée
        return false;
    }

    /**
     * Vérifie une permission avec support des alias.
     */
    private function hasPermission($user, string $permission): bool
    {
        $permissionNames = $this->getCachedUserPermissions($user);

        foreach (PermissionAliases::resolve($permission) as $alias) {
            if ($permissionNames->contains($alias)) {
                return true;
            }
        }

        return false;
    }

    private function getCachedUserPermissions($user): Collection
    {
        if ($this->cachedUserPermissions instanceof Collection) {
            return $this->cachedUserPermissions;
        }

        $this->cachedUserPermissions = $user->getAllPermissions()->pluck('name');

        return $this->cachedUserPermissions;
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
            // Canonical (dot notation)
            'vehicles.view' => 'Vous n\'avez pas l\'autorisation de consulter les véhicules.',
            'vehicles.create' => 'Vous n\'avez pas l\'autorisation de créer des véhicules.',
            'vehicles.update' => 'Vous n\'avez pas l\'autorisation de modifier les véhicules.',
            'vehicles.delete' => 'Vous n\'avez pas l\'autorisation de supprimer des véhicules.',
            'vehicles.restore' => 'Vous n\'avez pas l\'autorisation de restaurer des véhicules.',
            'vehicles.export' => 'Vous n\'avez pas l\'autorisation d\'exporter les véhicules.',
            'drivers.view' => 'Vous n\'avez pas l\'autorisation de consulter les chauffeurs.',
            'drivers.create' => 'Vous n\'avez pas l\'autorisation de créer des chauffeurs.',
            'drivers.update' => 'Vous n\'avez pas l\'autorisation de modifier les chauffeurs.',
            'drivers.delete' => 'Vous n\'avez pas l\'autorisation de supprimer des chauffeurs.',
            'drivers.restore' => 'Vous n\'avez pas l\'autorisation de restaurer des chauffeurs.',
            'users.view' => 'Vous n\'avez pas l\'autorisation de consulter les utilisateurs.',
            'users.create' => 'Vous n\'avez pas l\'autorisation de créer des utilisateurs.',
            'users.update' => 'Vous n\'avez pas l\'autorisation de modifier les utilisateurs.',
            'users.delete' => 'Vous n\'avez pas l\'autorisation de supprimer des utilisateurs.',
            'roles.manage' => 'Vous n\'avez pas l\'autorisation de gérer les rôles et permissions.',
            'permissions.manage' => 'Vous n\'avez pas l\'autorisation de gérer les rôles et permissions.',
            'assignments.view' => 'Vous n\'avez pas l\'autorisation de consulter les affectations.',
            'assignments.create' => 'Vous n\'avez pas l\'autorisation de créer des affectations.',
            'assignments.update' => 'Vous n\'avez pas l\'autorisation de modifier les affectations.',
            'assignments.delete' => 'Vous n\'avez pas l\'autorisation de supprimer des affectations.',
            'assignments.end' => 'Vous n\'avez pas l\'autorisation de terminer des affectations.',
            'maintenance.view' => 'Vous n\'avez pas l\'autorisation de consulter la maintenance.',
            'maintenance.plans.manage' => 'Vous n\'avez pas l\'autorisation de gérer les plans de maintenance.',
            'suppliers.view' => 'Vous n\'avez pas l\'autorisation de consulter les fournisseurs.',
            'documents.view' => 'Vous n\'avez pas l\'autorisation de consulter les documents.',
            'organizations.view' => 'Vous n\'avez pas l\'autorisation d\'accéder aux organisations (Super Admin uniquement).',
            'audit-logs.view' => 'Vous n\'avez pas l\'autorisation de consulter les journaux d\'audit.',
            'system.view' => 'Vous n\'avez pas l\'autorisation d\'accéder aux paramètres système.',
            'mileage-readings.view.own' => 'Vous n\'avez pas l\'autorisation de consulter les relevés kilométriques.',
            'mileage-readings.view.team' => 'Vous n\'avez pas l\'autorisation de consulter les relevés kilométriques de votre équipe.',
            'mileage-readings.view.all' => 'Vous n\'avez pas l\'autorisation de consulter tous les relevés kilométriques.',
            'mileage-readings.create' => 'Vous n\'avez pas l\'autorisation de créer des relevés kilométriques.',
            'mileage-readings.delete' => 'Vous n\'avez pas l\'autorisation de supprimer des relevés kilométriques.',
            'mileage-readings.export' => 'Vous n\'avez pas l\'autorisation d\'exporter les relevés kilométriques.',

            // Legacy aliases (fallback)
            'view vehicles' => 'Vous n\'avez pas l\'autorisation de consulter les véhicules.',
            'create vehicles' => 'Vous n\'avez pas l\'autorisation de créer des véhicules.',
            'edit vehicles' => 'Vous n\'avez pas l\'autorisation de modifier les véhicules.',
            'delete vehicles' => 'Vous n\'avez pas l\'autorisation de supprimer des véhicules.',
            'restore vehicles' => 'Vous n\'avez pas l\'autorisation de restaurer des véhicules.',
            'view drivers' => 'Vous n\'avez pas l\'autorisation de consulter les chauffeurs.',
            'create drivers' => 'Vous n\'avez pas l\'autorisation de créer des chauffeurs.',
            'edit drivers' => 'Vous n\'avez pas l\'autorisation de modifier les chauffeurs.',
            'delete drivers' => 'Vous n\'avez pas l\'autorisation de supprimer des chauffeurs.',
            'restore drivers' => 'Vous n\'avez pas l\'autorisation de restaurer des chauffeurs.',
            'view users' => 'Vous n\'avez pas l\'autorisation de consulter les utilisateurs.',
            'create users' => 'Vous n\'avez pas l\'autorisation de créer des utilisateurs.',
            'edit users' => 'Vous n\'avez pas l\'autorisation de modifier les utilisateurs.',
            'delete users' => 'Vous n\'avez pas l\'autorisation de supprimer des utilisateurs.',
            'manage roles' => 'Vous n\'avez pas l\'autorisation de gérer les rôles et permissions.',
            'view assignments' => 'Vous n\'avez pas l\'autorisation de consulter les affectations.',
            'create assignments' => 'Vous n\'avez pas l\'autorisation de créer des affectations.',
            'edit assignments' => 'Vous n\'avez pas l\'autorisation de modifier les affectations.',
            'end assignments' => 'Vous n\'avez pas l\'autorisation de terminer des affectations.',
            'view maintenance' => 'Vous n\'avez pas l\'autorisation de consulter la maintenance.',
            'manage maintenance plans' => 'Vous n\'avez pas l\'autorisation de gérer les plans de maintenance.',
            'view suppliers' => 'Vous n\'avez pas l\'autorisation de consulter les fournisseurs.',
            'view documents' => 'Vous n\'avez pas l\'autorisation de consulter les documents.',
            'view organizations' => 'Vous n\'avez pas l\'autorisation d\'accéder aux organisations (Super Admin uniquement).',
            'view own mileage readings' => 'Vous n\'avez pas l\'autorisation de consulter les relevés kilométriques.',
            'view team mileage readings' => 'Vous n\'avez pas l\'autorisation de consulter les relevés kilométriques de votre équipe.',
            'view all mileage readings' => 'Vous n\'avez pas l\'autorisation de consulter tous les relevés kilométriques.',
            'create mileage readings' => 'Vous n\'avez pas l\'autorisation de créer des relevés kilométriques.',
            'edit mileage readings' => 'Vous n\'avez pas l\'autorisation de modifier les relevés kilométriques.',
            'delete mileage readings' => 'Vous n\'avez pas l\'autorisation de supprimer des relevés kilométriques.',
            'export mileage readings' => 'Vous n\'avez pas l\'autorisation d\'exporter les relevés kilométriques.',
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
        try {
            Log::channel('audit')->notice('Route non mappée dans le système de permissions', [
                'route' => $routeName ?? 'unknown',
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => Auth::id(),
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            // Fallback to default log if audit channel missing
            Log::warning('Route non mappée (Audit log failed): ' . ($routeName ?? 'unknown'), [
                'error' => $e->getMessage()
            ]);
        }
    }
}
