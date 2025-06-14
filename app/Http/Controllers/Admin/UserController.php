<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Optimisation : On charge la relation 'roles' pour éviter N+1 requêtes dans la vue
        $users = User::with('roles')->orderBy('id', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        // Récupérer tous les rôles pour les afficher dans le formulaire
        $roles = Role::all();
        
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
     // app/Http/Controllers/Admin/UserController.php -> méthode update

   // app/Http/Controllers/Admin/UserController.php -> méthode update

      public function update(Request $request, User $user): RedirectResponse
	{
    	// 1. Valider que les données entrantes sont bien des IDs de rôles valides
   	 $validated = $request->validate([
        	'roles' => 'sometimes|array',
        	'roles.*' => 'exists:roles,id',
    	]);

    	// 2. Récupérer le tableau d'IDs de rôles depuis la requête validée
    	$roleIds = $validated['roles'] ?? [];

    	// 3. Trouver les objets Role correspondant à ces IDs
    	$roles = Role::whereIn('id', $roleIds)->get();

    	// 4. Synchroniser les rôles en utilisant la collection d'objets Role.
    	// C'est la méthode la plus robuste et la plus claire.
    	$user->syncRoles($roles);

    	return redirect()->route('admin.users.index')
        ->with('success', 'Les rôles de l\'utilisateur ont été mis à jour avec succès.');
	} 
    
    //////////////////////// 
    // Les autres méthodes (create, store, etc.) peuvent rester vides pour l'instant
	    /**
     * Affiche le formulaire pour créer un nouvel utilisateur.
     */
    public function create(): View
    {
        $roles = Role::all(); // Récupère tous les rôles pour les assigner à la création
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Stocke un nouvel utilisateur dans la base de données.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:50', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id',
        ]);

        // Le champ 'name' sera automatiquement rempli par l'UserObserver
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Assigner les rôles sélectionnés
        $roleIds = $request->input('roles', []);
        $roles = Role::whereIn('id', $roleIds)->get();
        $user->syncRoles($roles);

        return redirect()->route('admin.users.index')->with('success', 'Nouvel utilisateur créé avec succès.');
    }

      /**
     * Supprime un utilisateur de la base de données.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Sécurité : Empêcher un utilisateur de se supprimer lui-même
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte administrateur.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "L'utilisateur '{$userName}' a été supprimé avec succès.");
    }
}
