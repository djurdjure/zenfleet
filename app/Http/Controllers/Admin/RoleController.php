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
        return view('admin.roles.permissions', [
            'roleId' => $role->id,
        ]);
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
