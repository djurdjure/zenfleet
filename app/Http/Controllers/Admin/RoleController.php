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
        $roles = Role::orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Affiche le formulaire pour modifier un rôle et ses permissions.
     */
    public function edit(Role $role): View
    {
        // Récupère toutes les permissions disponibles pour les afficher
        $permissions = Permission::all();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Met à jour un rôle avec les permissions sélectionnées.
     */
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




}
