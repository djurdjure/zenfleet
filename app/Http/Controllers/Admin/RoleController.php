<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Affiche la liste des rôles.
     */
    public function index(): View
    {
        $user = auth()->user();
        $organizationId = $user?->organization_id;
        $isSuperAdmin = $user?->hasRole('Super Admin');

        $rolesQuery = Role::query()
            ->when(!$isSuperAdmin, function ($query) use ($organizationId) {
                $query->where(function ($subQuery) use ($organizationId) {
                    $subQuery->whereNull('organization_id')
                        ->orWhere('organization_id', $organizationId);
                });
            })
            ->orderBy('name');

        $roles = $rolesQuery->get();

        if ($organizationId) {
            $roles = $roles->sortByDesc(function ($role) use ($organizationId) {
                return (int) ($role->organization_id === $organizationId);
            });
        }

        $roles = $roles->unique('name')->values();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Affiche le formulaire pour modifier un rôle et ses permissions.
     */
    public function edit(Role $role): View
    {
        // Récupère toutes les permissions disponibles
        $allPermissions = Permission::orderBy('name')->get();

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
        $role->syncPermissions($permissions);

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
}
