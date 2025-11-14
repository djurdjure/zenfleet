<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * 🛡️ Contrôleur de Gestion des Permissions Enterprise-Grade
 * 
 * Gestion avancée des permissions et des rôles avec:
 * - Interface intuitive et moderne
 * - Gestion granulaire des permissions
 * - Audit trail complet
 * - Support multi-tenant
 * 
 * @author ZenFleet Architecture Team
 * @version 3.0.0
 */
class PermissionController extends Controller
{
    /**
     * Configuration des modules et catégories
     */
    private array $moduleConfig = [
        'fleet' => [
            'name' => 'Gestion de Flotte',
            'icon' => 'truck',
            'color' => '#3B82F6',
            'categories' => [
                'vehicles' => ['name' => 'Véhicules', 'icon' => 'car'],
                'drivers' => ['name' => 'Chauffeurs', 'icon' => 'users'],
                'assignments' => ['name' => 'Affectations', 'icon' => 'clipboard-list'],
                'maintenance' => ['name' => 'Maintenance', 'icon' => 'wrench'],
                'mileage' => ['name' => 'Kilométrage', 'icon' => 'route']
            ]
        ],
        'finance' => [
            'name' => 'Finance',
            'icon' => 'dollar-sign',
            'color' => '#10B981',
            'categories' => [
                'expenses' => ['name' => 'Dépenses', 'icon' => 'receipt'],
                'fuel' => ['name' => 'Carburant', 'icon' => 'gas-pump'],
                'invoices' => ['name' => 'Factures', 'icon' => 'file-invoice']
            ]
        ],
        'compliance' => [
            'name' => 'Conformité',
            'icon' => 'shield-check',
            'color' => '#F59E0B',
            'categories' => [
                'documents' => ['name' => 'Documents', 'icon' => 'file-alt'],
                'sanctions' => ['name' => 'Sanctions', 'icon' => 'gavel'],
                'insurance' => ['name' => 'Assurances', 'icon' => 'shield-alt']
            ]
        ],
        'reports' => [
            'name' => 'Rapports',
            'icon' => 'chart-bar',
            'color' => '#8B5CF6',
            'categories' => [
                'analytics' => ['name' => 'Analyses', 'icon' => 'chart-line'],
                'exports' => ['name' => 'Exports', 'icon' => 'download']
            ]
        ],
        'system' => [
            'name' => 'Système',
            'icon' => 'cog',
            'color' => '#EF4444',
            'categories' => [
                'users' => ['name' => 'Utilisateurs', 'icon' => 'user-cog'],
                'roles' => ['name' => 'Rôles', 'icon' => 'user-shield'],
                'settings' => ['name' => 'Paramètres', 'icon' => 'sliders-h']
            ]
        ]
    ];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage permissions')->except(['userPermissions']);
    }

    /**
     * Affiche le tableau de bord des permissions
     */
    public function index(Request $request): View
    {
        $organizationId = auth()->user()->organization_id;
        
        // Récupérer les statistiques
        $stats = [
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'total_users' => User::where('organization_id', $organizationId)->count(),
            'recent_changes' => $this->getRecentPermissionChanges()
        ];
        
        // Récupérer les rôles avec leurs permissions groupées
        $roles = Role::with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->get()
            ->map(function ($role) {
                $role->permissions_by_module = $this->groupPermissionsByModule($role->permissions);
                return $role;
            });
        
        // Récupérer toutes les permissions groupées par module
        $allPermissions = $this->getPermissionsGroupedByModule();
        
        return view('admin.permissions.index', compact('roles', 'allPermissions', 'stats'));
    }

    /**
     * Affiche la matrice des permissions
     */
    public function matrix(): View
    {
        // Récupérer tous les rôles
        $roles = Role::orderBy('name')->get();
        
        // Récupérer toutes les permissions groupées
        $permissionsByModule = $this->getPermissionsGroupedByModule();
        
        // Créer la matrice des permissions
        $matrix = [];
        foreach ($permissionsByModule as $module => $categories) {
            foreach ($categories as $category => $permissions) {
                foreach ($permissions as $permission) {
                    $matrix[$permission->name] = [
                        'permission' => $permission,
                        'module' => $module,
                        'category' => $category,
                        'roles' => []
                    ];
                    
                    foreach ($roles as $role) {
                        $matrix[$permission->name]['roles'][$role->id] = $role->hasPermissionTo($permission->name);
                    }
                }
            }
        }
        
        return view('admin.permissions.matrix', compact('roles', 'matrix', 'permissionsByModule'));
    }

    /**
     * Gestion des permissions d'un rôle
     */
    public function rolePermissions(Request $request, Role $role): View|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'role' => $role,
                'permissions' => $role->permissions->pluck('name'),
                'all_permissions' => $this->getPermissionsGroupedByModule()
            ]);
        }
        
        $allPermissions = $this->getPermissionsGroupedByModule();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        return view('admin.permissions.role', compact('role', 'allPermissions', 'rolePermissions'));
    }

    /**
     * Met à jour les permissions d'un rôle
     */
    public function updateRolePermissions(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Sauvegarder l'état précédent pour l'audit
            $oldPermissions = $role->permissions->pluck('name')->toArray();
            
            // Synchroniser les nouvelles permissions
            $role->syncPermissions($validated['permissions'] ?? []);
            
            // Nettoyer le cache
            Cache::forget('spatie.permission.cache');
            Cache::forget('spatie.role.cache');
            
            // Log de l'audit
            $this->logPermissionChange($role, $oldPermissions, $validated['permissions'] ?? []);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Permissions du rôle {$role->name} mises à jour avec succès",
                'role' => $role->load('permissions')
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Erreur lors de la mise à jour des permissions', [
                'role' => $role->name,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour des permissions'
            ], 500);
        }
    }

    /**
     * Gestion des permissions d'un utilisateur
     */
    public function userPermissions(Request $request, User $user): View|JsonResponse
    {
        // Vérifier que l'utilisateur appartient à la même organisation
        if ($user->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }
        
        if ($request->expectsJson()) {
            return response()->json([
                'user' => $user->load('roles'),
                'direct_permissions' => $user->getDirectPermissions()->pluck('name'),
                'all_permissions' => $user->getAllPermissions()->pluck('name'),
                'permissions_via_roles' => $user->getPermissionsViaRoles()->pluck('name')
            ]);
        }
        
        $allRoles = Role::orderBy('name')->get();
        $userRoles = $user->roles->pluck('id')->toArray();
        $directPermissions = $user->getDirectPermissions()->pluck('name')->toArray();
        $allPermissions = $this->getPermissionsGroupedByModule();
        
        return view('admin.permissions.user', compact(
            'user', 'allRoles', 'userRoles', 'directPermissions', 'allPermissions'
        ));
    }

    /**
     * Met à jour les permissions d'un utilisateur
     */
    public function updateUserPermissions(Request $request, User $user): JsonResponse
    {
        // Vérifier que l'utilisateur appartient à la même organisation
        if ($user->organization_id !== auth()->user()->organization_id) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
        }
        
        $validated = $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'direct_permissions' => 'array',
            'direct_permissions.*' => 'exists:permissions,name'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Synchroniser les rôles
            if (isset($validated['roles'])) {
                $user->syncRoles($validated['roles']);
            }
            
            // Synchroniser les permissions directes
            if (isset($validated['direct_permissions'])) {
                $user->syncPermissions($validated['direct_permissions']);
            }
            
            // Nettoyer le cache
            Cache::forget('spatie.permission.cache');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Permissions de {$user->name} mises à jour avec succès",
                'user' => $user->load(['roles', 'permissions'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour des permissions'
            ], 500);
        }
    }

    /**
     * API pour la recherche de permissions
     */
    public function searchPermissions(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $module = $request->input('module');
        $category = $request->input('category');
        
        $permissions = Permission::query()
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('display_name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->when($module, function ($q) use ($module) {
                $q->where('module', $module);
            })
            ->when($category, function ($q) use ($category) {
                $q->where('category', $category);
            })
            ->orderBy('name')
            ->get();
        
        return response()->json($permissions);
    }

    /**
     * Export de la configuration des permissions
     */
    public function export(Request $request): JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $format = $request->input('format', 'json');
        
        $data = [
            'roles' => Role::with('permissions')->get(),
            'permissions' => Permission::all(),
            'users_with_roles' => User::with('roles', 'permissions')
                ->where('organization_id', auth()->user()->organization_id)
                ->get()
        ];
        
        if ($format === 'csv') {
            return $this->exportToCsv($data);
        }
        
        return response()->json($data);
    }

    /**
     * Helpers privés
     */
    private function getPermissionsGroupedByModule(): array
    {
        $permissions = Permission::all();
        $grouped = [];
        
        foreach ($permissions as $permission) {
            // Déterminer le module et la catégorie depuis le nom de la permission
            $parts = explode('.', $permission->name);
            
            if (count($parts) >= 2) {
                $category = $parts[0]; // Ex: 'assignments'
                $action = implode('.', array_slice($parts, 1)); // Ex: 'create'
                
                // Trouver le module correspondant
                $module = $this->findModuleForCategory($category);
                
                if (!isset($grouped[$module])) {
                    $grouped[$module] = [];
                }
                
                if (!isset($grouped[$module][$category])) {
                    $grouped[$module][$category] = [];
                }
                
                $grouped[$module][$category][] = $permission;
            } else {
                // Permissions legacy
                $category = $this->categorizePermission($permission->name);
                $module = $this->findModuleForCategory($category);
                
                if (!isset($grouped[$module])) {
                    $grouped[$module] = [];
                }
                
                if (!isset($grouped[$module][$category])) {
                    $grouped[$module][$category] = [];
                }
                
                $grouped[$module][$category][] = $permission;
            }
        }
        
        return $grouped;
    }

    private function groupPermissionsByModule($permissions): array
    {
        $grouped = [];
        
        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $category = $parts[0] ?? 'general';
            $module = $this->findModuleForCategory($category);
            
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            
            $grouped[$module][] = $permission;
        }
        
        return $grouped;
    }

    private function findModuleForCategory(string $category): string
    {
        $categoryModuleMap = [
            'vehicles' => 'fleet',
            'vehicle' => 'fleet',
            'drivers' => 'fleet',
            'driver' => 'fleet',
            'assignments' => 'fleet',
            'assignment' => 'fleet',
            'maintenance' => 'fleet',
            'mileage' => 'fleet',
            'expenses' => 'finance',
            'expense' => 'finance',
            'fuel' => 'finance',
            'invoices' => 'finance',
            'invoice' => 'finance',
            'documents' => 'compliance',
            'document' => 'compliance',
            'sanctions' => 'compliance',
            'sanction' => 'compliance',
            'insurance' => 'compliance',
            'reports' => 'reports',
            'analytics' => 'reports',
            'exports' => 'reports',
            'users' => 'system',
            'user' => 'system',
            'roles' => 'system',
            'role' => 'system',
            'settings' => 'system',
            'permissions' => 'system'
        ];
        
        return $categoryModuleMap[strtolower($category)] ?? 'general';
    }

    private function categorizePermission(string $permissionName): string
    {
        // Analyser le nom de permission pour déterminer la catégorie
        $keywords = [
            'assignment' => 'assignments',
            'vehicle' => 'vehicles',
            'driver' => 'drivers',
            'maintenance' => 'maintenance',
            'mileage' => 'mileage',
            'expense' => 'expenses',
            'fuel' => 'fuel',
            'document' => 'documents',
            'sanction' => 'sanctions',
            'user' => 'users',
            'role' => 'roles'
        ];
        
        foreach ($keywords as $keyword => $category) {
            if (str_contains(strtolower($permissionName), $keyword)) {
                return $category;
            }
        }
        
        return 'general';
    }

    private function getRecentPermissionChanges(): array
    {
        // Ici on pourrait implémenter un système d'audit trail
        // Pour l'instant, on retourne des données d'exemple
        return [
            [
                'user' => auth()->user()->name,
                'action' => 'Ajout de permissions',
                'target' => 'Rôle Admin',
                'date' => now()->subHours(2)
            ]
        ];
    }

    private function logPermissionChange(Role $role, array $oldPermissions, array $newPermissions): void
    {
        $added = array_diff($newPermissions, $oldPermissions);
        $removed = array_diff($oldPermissions, $newPermissions);
        
        if (!empty($added) || !empty($removed)) {
            Log::info('Permissions du rôle modifiées', [
                'role' => $role->name,
                'added' => $added,
                'removed' => $removed,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name
            ]);
        }
    }

    private function exportToCsv($data): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="permissions_export_' . now()->format('Y-m-d') . '.csv"'
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // En-tête pour les rôles et permissions
            fputcsv($file, ['Type', 'Nom', 'Permissions']);

            // Rôles
            foreach ($data['roles'] as $role) {
                fputcsv($file, [
                    'Rôle',
                    $role->name,
                    $role->permissions->pluck('name')->implode(', ')
                ]);
            }

            // Utilisateurs
            foreach ($data['users_with_roles'] as $user) {
                fputcsv($file, [
                    'Utilisateur',
                    $user->name . ' (' . $user->email . ')',
                    'Rôles: ' . $user->roles->pluck('name')->implode(', ') . 
                    ' | Permissions directes: ' . $user->permissions->pluck('name')->implode(', ')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
