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
     *
     * ⚠️ IMPORTANT: Utilise le format Spatie avec ESPACES (ex: 'view vehicles')
     * Les permissions Spatie sont définies avec des espaces, pas des underscores
     */
    private array $routePermissionMap = [
        // Véhicules
        'admin.vehicles.index' => 'view vehicles',
        'admin.vehicles.create' => 'create vehicles',
        'admin.vehicles.store' => 'create vehicles',
        'admin.vehicles.show' => 'view vehicles',
        'admin.vehicles.edit' => 'edit vehicles',
        'admin.vehicles.update' => 'edit vehicles',
        'admin.vehicles.destroy' => 'delete vehicles',
        'admin.vehicles.restore' => 'restore vehicles',
        'admin.vehicles.export' => 'view vehicles', // Basé sur view
        'admin.vehicles.import.*' => 'create vehicles', // Basé sur create

        // Chauffeurs
        'admin.drivers.index' => 'view drivers',
        'admin.drivers.create' => 'create drivers',
        'admin.drivers.store' => 'create drivers',
        'admin.drivers.show' => 'view drivers',
        'admin.drivers.edit' => 'edit drivers',
        'admin.drivers.update' => 'edit drivers',
        'admin.drivers.destroy' => 'delete drivers',
        'admin.drivers.restore' => 'restore drivers',
        'admin.drivers.export' => 'view drivers', // Basé sur view
        'admin.drivers.import.*' => 'create drivers', // Basé sur create

        // Affectations
        'admin.assignments.index' => 'view assignments',
        'admin.assignments.create' => 'create assignments',
        'admin.assignments.store' => 'create assignments',
        'admin.assignments.show' => 'view assignments',
        'admin.assignments.edit' => 'edit assignments',
        'admin.assignments.update' => 'edit assignments',
        'admin.assignments.destroy' => 'view assignments', // Pas de delete assignment
        'admin.assignments.end' => 'end assignments',
        'admin.assignments.export' => 'view assignments',

        // Utilisateurs
        'admin.users.index' => 'view users',
        'admin.users.create' => 'create users',
        'admin.users.store' => 'create users',
        'admin.users.show' => 'view users',
        'admin.users.edit' => 'edit users',
        'admin.users.update' => 'edit users',
        'admin.users.destroy' => 'delete users',
        'admin.users.export' => 'view users',

        // Rôles et Permissions
        'admin.roles.index' => 'manage roles',
        'admin.roles.show' => 'manage roles',
        'admin.roles.edit' => 'manage roles',
        'admin.roles.update' => 'manage roles',
        'admin.permissions.index' => 'manage roles',

        // Documents
        'admin.documents.index' => 'view documents',
        'admin.documents.create' => 'create documents',
        'admin.documents.store' => 'create documents',
        'admin.documents.show' => 'view documents',
        'admin.documents.edit' => 'edit documents',
        'admin.documents.update' => 'edit documents',
        'admin.documents.destroy' => 'delete documents',

        // Fournisseurs
        'admin.suppliers.index' => 'view suppliers',
        'admin.suppliers.create' => 'create suppliers',
        'admin.suppliers.store' => 'create suppliers',
        'admin.suppliers.show' => 'view suppliers',
        'admin.suppliers.edit' => 'edit suppliers',
        'admin.suppliers.update' => 'edit suppliers',
        'admin.suppliers.destroy' => 'delete suppliers',

        // Maintenance
        'admin.maintenance.*' => 'view maintenance',
        'admin.maintenance.create' => 'manage maintenance plans',
        'admin.maintenance.store' => 'manage maintenance plans',
        'admin.maintenance.edit' => 'manage maintenance plans',
        'admin.maintenance.update' => 'manage maintenance plans',
        'admin.maintenance.destroy' => 'manage maintenance plans',

        // Demandes de Réparation
        'admin.repair-requests.index' => 'view own repair requests', // Minimum permission
        'admin.repair-requests.create' => 'create repair requests',
        'admin.repair-requests.store' => 'create repair requests',
        'admin.repair-requests.show' => 'view own repair requests',
        'admin.repair-requests.edit' => 'update own repair requests',
        'admin.repair-requests.update' => 'update own repair requests',
        'admin.repair-requests.destroy' => 'delete repair requests',
        'admin.repair-requests.approve-level-one' => 'approve repair requests level 1',
        'admin.repair-requests.reject-level-one' => 'approve repair requests level 1',
        'admin.repair-requests.approve-level-two' => 'approve repair requests level 2',
        'admin.repair-requests.reject-level-two' => 'approve repair requests level 2',

        // Relevés Kilométriques
        'admin.mileage-readings.index' => 'view own mileage readings', // Minimum permission
        'admin.mileage-readings.create' => 'create mileage readings',
        'admin.mileage-readings.store' => 'create mileage readings',
        'admin.mileage-readings.show' => 'view own mileage readings',
        'admin.mileage-readings.edit' => 'edit mileage readings',
        'admin.mileage-readings.update' => 'edit mileage readings', // Corrigé: edit au lieu de update
        'admin.mileage-readings.destroy' => 'delete mileage readings',
        'admin.mileage-readings.export' => 'export mileage readings',
        'admin.mileage-readings.statistics' => 'view all mileage readings', // Corrigé: view all au lieu de view statistics

        // Organisations (Super Admin seulement)
        'admin.organizations.*' => 'view organizations',
        'admin.organizations.create' => 'create organizations',
        'admin.organizations.store' => 'create organizations',
        'admin.organizations.edit' => 'edit organizations',
        'admin.organizations.update' => 'edit organizations',
        'admin.organizations.destroy' => 'delete organizations',

        // Système (Super Admin seulement)
        'admin.system.*' => 'view organizations', // Super Admin only
        'admin.audit.*' => 'view organizations', // Super Admin only
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
        if ($user->can($permission)) {
            return true;
        }

        // Permissions hiérarchiques pour les relevés kilométriques
        if ($permission === 'view own mileage readings') {
            return $user->can('view team mileage readings') || $user->can('view all mileage readings');
        }

        if ($permission === 'view team mileage readings') {
            return $user->can('view all mileage readings');
        }

        // Permissions hiérarchiques pour les demandes de réparation
        if ($permission === 'view own repair requests') {
            return $user->can('view team repair requests') || $user->can('view all repair requests');
        }

        if ($permission === 'view team repair requests') {
            return $user->can('view all repair requests');
        }

        // Aucune permission hiérarchique trouvée
        return false;
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
        Log::channel('audit')->notice('Route non mappée dans le système de permissions', [
            'route' => $routeName,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => Auth::id(),
            'timestamp' => now()->toISOString(),
        ]);
    }
}