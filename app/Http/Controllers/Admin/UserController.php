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
        $users = User::with(['roles', 'organization'])->orderBy('id', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('create users');
        $roles = Role::all();
        $organizations = Organization::withoutGlobalScope('organization')->orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'organizations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create users');
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'organization_id' => ['required', 'exists:organizations,id'],
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id', // Valide que les IDs existent
        ]);

        $user = User::create([
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
        $organizations = Organization::withoutGlobalScope('organization')->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles', 'organizations'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('edit users');
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