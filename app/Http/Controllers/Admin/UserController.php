<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('view users');

        $query = User::with(['roles', 'organization']);

        // Si l'utilisateur n'est pas Super Admin, on filtre par son organisation
        if (!auth()->user()->hasRole('Super Admin')) {
            $query->where('organization_id', auth()->user()->organization_id);
        }

        $users = $query->orderBy('id', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('create users');
        $roles = Role::all();
        $user = auth()->user();

        if ($user->hasRole('Super Admin')) {
            $organizations = Organization::withoutGlobalScope('organization')->orderBy('name')->get();
        } else {
            $organizations = Organization::where('id', $user->organization_id)->get();
        }

        return view('admin.users.create', compact('roles', 'organizations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create users');

        $user = auth()->user();
        $organizationId = $user->hasRole('Super Admin') ? $request->input('organization_id') : $user->organization_id;

        $request->merge(['organization_id' => $organizationId]);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'organization_id' => ['required', 'exists:organizations,id'],
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id', // Valide que les IDs existent
        ]);

        $newUser = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'organization_id' => $validated['organization_id'],
        ]);

        // CORRECTION : On récupère les modèles de Rôle avant de synchroniser
        if (!empty($validated['roles'])) {
            $rolesToSync = Role::whereIn('id', $validated['roles'])->get();
            $user->syncRoles($rolesToSync);
        }

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user): View
    {
        $this->authorize('edit users');
        $roles = Role::all();
        $loggedInUser = auth()->user();

        if ($loggedInUser->hasRole('Super Admin')) {
            $organizations = Organization::withoutGlobalScope('organization')->orderBy('name')->get();
        } else {
            // Un admin ne peut voir que sa propre organisation
            $organizations = Organization::where('id', $loggedInUser->organization_id)->get();
            // On s'assure qu'un admin ne peut pas éditer un utilisateur d'une autre organisation
            if ($user->organization_id !== $loggedInUser->organization_id) {
                abort(403, 'Vous n\'êtes pas autorisé à modifier cet utilisateur.');
            }
        }

        return view('admin.users.edit', compact('user', 'roles', 'organizations'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('edit users');
        $loggedInUser = auth()->user();

        // Un admin ne peut pas changer l'organisation d'un utilisateur
        if (!$loggedInUser->hasRole('Super Admin')) {
            if ($request->input('organization_id') && $request->input('organization_id') != $loggedInUser->organization_id) {
                abort(403, 'Vous n\'êtes pas autorisé à changer l\'organisation de l\'utilisateur.');
            }
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'organization_id' => ['required', 'exists:organizations,id'],
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $userData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'organization_id' => $validated['organization_id'],
        ];
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // CORRECTION : On récupère les modèles de Rôle avant de synchroniser
        $rolesToSync = Role::whereIn('id', $validated['roles'] ?? [])->get();
        $user->syncRoles($rolesToSync);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete users');
        if (auth()->id() == $user->id) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}