<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Support\PermissionAliases;
use App\Models\Organization;

class RoleController extends Controller
{
    /**
     * Affiche la liste des rôles.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $isSuperAdmin = $user?->hasRole('Super Admin');

        $context = $request->string('context')->toString() ?: 'organization';
        $selectedOrgId = $request->integer('organization_id') ?: $user?->organization_id;
        $includeGlobal = $request->boolean('include_global', false);

        if (!$isSuperAdmin) {
            $context = 'organization';
            $selectedOrgId = $user?->organization_id;
            $includeGlobal = false;
        }

        $rolesQuery = Role::query();

        if ($context === 'global') {
            $rolesQuery->whereNull('organization_id');
        } elseif ($context === 'all') {
            // no filter
        } else {
            if ($selectedOrgId) {
                $rolesQuery->where('organization_id', $selectedOrgId);
            }

            if ($includeGlobal) {
                $rolesQuery->orWhereNull('organization_id');
            }
        }

        $rolesQuery->orderBy('name');

        $roles = $rolesQuery->get();
        $organizations = $isSuperAdmin
            ? Organization::orderBy('name')->get(['id', 'name', 'legal_name'])
            : collect();

        return view('admin.roles.index', compact(
            'roles',
            'organizations',
            'context',
            'selectedOrgId',
            'includeGlobal',
            'isSuperAdmin'
        ));
    }

    /**
     * Affiche le formulaire pour modifier un rôle et ses permissions.
     */
    public function edit(Role $role): View
    {
        // Récupère toutes les permissions disponibles
        $allPermissions = Permission::orderBy('name')->get();
        $allPermissions = $this->filterLegacyPermissions($allPermissions);

        // Grouper les permissions par Ressource (et non par action)
        $permissionsByCategory = $allPermissions->groupBy(function ($permission) {
            $name = $permission->name;
            if (str_contains($name, 'organization')) return 'organizations';
            if (str_contains($name, 'user')) return 'users';
            if (str_contains($name, 'role')) return 'roles';
            if (str_contains($name, 'vehicle')) return 'vehicles';
            if (str_contains($name, 'driver') && !str_contains($name, 'sanction')) return 'drivers';
            if (str_contains($name, 'assignment')) return 'assignments';
            if (str_contains($name, 'maintenance')) return 'maintenance';
            if (str_contains($name, 'repair')) return 'repairs';
            if (str_contains($name, 'mileage')) return 'mileage';
            if (str_contains($name, 'supplier')) return 'suppliers';
            if (str_contains($name, 'expense')) return 'expenses';
            if (str_contains($name, 'document')) return 'documents';
            if (str_contains($name, 'alert')) return 'alerts';
            if (str_contains($name, 'audit')) return 'audit';
            if (str_contains($name, 'sanction')) return 'sanctions';
            if (str_contains($name, 'depot')) return 'depots';
            if (str_contains($name, 'report') || str_contains($name, 'analytics')) return 'reports';
            
            return 'autres';
        });

        // Ordre des catégories pour affichage logique
        $categoryOrder = [
            'organizations', 'users', 'roles', 
            'vehicles', 'drivers', 'assignments', 'depots',
            'maintenance', 'repairs', 'mileage', 
            'suppliers', 'expenses', 'documents', 
            'alerts', 'sanctions', 'reports', 'audit', 'autres'
        ];
        
        $orderedCategories = collect($categoryOrder)->mapWithKeys(function ($category) use ($permissionsByCategory) {
            return [$category => $permissionsByCategory->get($category, collect())];
        })->filter(fn($perms) => $perms->isNotEmpty());

        return view('admin.roles.edit', compact('role', 'allPermissions', 'orderedCategories'));
    }

    /**
     * Met à jour un rôle avec les permissions sélectionnées.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        // 1. Valider que les données entrantes sont bien des IDs de permissions valides
        $validated = $request->validate([
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id', // Valide que chaque ID existe dans la table 'permissions'
        ]);

        // 2. Récupérer le tableau d'IDs de permissions depuis la requête validée
        $permissionIds = $validated['permissions'] ?? [];

        // 3. Trouver les objets Permission correspondant à ces IDs
        $permissions = Permission::whereIn('id', $permissionIds)->get();

        // 4. Synchroniser les permissions en utilisant la collection d'objets Permission.
        // C'est la méthode la plus robuste qui élimine toute ambiguïté.
        $normalizedNames = PermissionAliases::normalize($permissions->pluck('name')->all());
        $normalizedPermissions = $this->resolvePermissionsForRole($role, $normalizedNames);

        $role->syncPermissions($normalizedPermissions);

        return redirect()->route('admin.roles.index')
            ->with('success', "Les permissions pour le rôle '{$role->name}' ont été mises à jour.");
    }

    /**
     * 🎭 MATRICE DES PERMISSIONS - ENTERPRISE GRADE
     *
     * Affiche la console avancée de gestion des permissions
     */
    public function permissions(): View
    {
        return view('admin.roles.permissions');
    }

    private function filterLegacyPermissions($permissions)
    {
        $allNames = $permissions->pluck('name');

        return $permissions->filter(function ($permission) use ($allNames) {
            if (!PermissionAliases::isLegacy($permission->name)) {
                return true;
            }

            $canonical = PermissionAliases::canonicalFor($permission->name);

            return !$canonical || !$allNames->contains($canonical);
        })->values();
    }

    private function resolvePermissionsForRole(Role $role, array $permissionNames)
    {
        $guard = $role->guard_name;

        $resolved = collect();

        foreach ($permissionNames as $name) {
            $permission = Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => $guard,
            ]);
            $resolved->push($permission);
        }

        return $resolved;
    }
}
