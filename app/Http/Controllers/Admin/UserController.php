<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('view users');

        $query = User::with(['roles', 'organization'])->withCount('vehicles');

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

        // 🛡️ SÉCURITÉ: Filtrer les rôles selon les permissions
        $roles = $this->getAssignableRoles();
        
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

        // 🛡️ SÉCURITÉ: Validation des rôles avec contrôle d'escalation
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'organization_id' => ['required', 'exists:organizations,id'],
            'roles' => ['sometimes', 'array', function ($attribute, $value, $fail) {
                if (!$this->canAssignRoles($value)) {
                    $fail('Vous n\'êtes pas autorisé à assigner un ou plusieurs de ces rôles.');
                }
            }],
            'roles.*' => 'exists:roles,id',
        ]);

        $newUser = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'organization_id' => $validated['organization_id'],
        ]);

        // 🛡️ SÉCURITÉ: Assignation sécurisée des rôles
        if (!empty($validated['roles'])) {
            $this->secureRoleAssignment($newUser, $validated['roles']);
        }

        // 📝 AUDIT: Logger la création d'utilisateur
        Log::info('Utilisateur créé', [
            'creator' => auth()->user()->email,
            'new_user' => $newUser->email,
            'roles' => $validated['roles'] ?? [],
            'ip' => $request->ip()
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user): View
    {
        $this->authorize('edit users');

        // 🛡️ SÉCURITÉ: Filtrer les rôles selon les permissions
        $roles = $this->getAssignableRoles();
        
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

        // 🛡️ SÉCURITÉ: Prévention de l'auto-promotion
        if ($this->isAttemptingPrivilegeEscalation($user, $request->input('roles', []))) {
            abort(403, 'Tentative d\'escalation de privilèges détectée et bloquée.');
        }

        // Un admin ne peut pas changer l'organisation d'un utilisateur
        if (!$loggedInUser->hasRole('Super Admin')) {
            if ($request->input('organization_id') && $request->input('organization_id') != $loggedInUser->organization_id) {
                abort(403, 'Vous n\'êtes pas autorisé à changer l\'organisation de l\'utilisateur.');
            }
        }

        // 🛡️ SÉCURITÉ: Validation avec contrôle des rôles
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'organization_id' => ['required', 'exists:organizations,id'],
            'roles' => ['sometimes', 'array', function ($attribute, $value, $fail) use ($user) {
                if (!$this->canAssignRoles($value, $user)) {
                    $fail('Vous n\'êtes pas autorisé à assigner un ou plusieurs de ces rôles.');
                }
            }],
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

        // 🛡️ SÉCURITÉ: Mise à jour sécurisée des rôles
        $this->secureRoleAssignment($user, $validated['roles'] ?? []);

        // 📝 AUDIT: Logger la modification
        Log::info('Utilisateur modifié', [
            'modifier' => auth()->user()->email,
            'target_user' => $user->email,
            'new_roles' => $validated['roles'] ?? [],
            'ip' => $request->ip()
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete users');

        if (auth()->id() == $user->id) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // 🛡️ SÉCURITÉ: Empêcher la suppression du dernier Super Admin
        if ($user->hasRole('Super Admin')) {
            $superAdminCount = User::role('Super Admin')->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', 'Impossible de supprimer le dernier Super Admin.');
            }
            
            // Seul un Super Admin peut supprimer un autre Super Admin
            if (!auth()->user()->hasRole('Super Admin')) {
                abort(403, 'Seul un Super Admin peut supprimer un autre Super Admin.');
            }
        }

        // 📝 AUDIT: Logger la suppression
        Log::warning('Utilisateur supprimé', [
            'deleter' => auth()->user()->email,
            'deleted_user' => $user->email,
            'deleted_user_roles' => $user->getRoleNames()->toArray(),
            'ip' => request()->ip()
        ]);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * 🛡️ SÉCURITÉ: Obtenir les rôles que l'utilisateur connecté peut assigner
     */
    private function getAssignableRoles()
    {
        $user = auth()->user();
        
        if ($user->hasRole('Super Admin')) {
            // Super Admin peut assigner tous les rôles
            return Role::all();
        } else {
            // Admin ne peut pas assigner le rôle Super Admin
            return Role::where('name', '!=', 'Super Admin')->get();
        }
    }

    /**
     * 🛡️ SÉCURITÉ: Vérifier si l'utilisateur peut assigner les rôles demandés
     */
    private function canAssignRoles(array $roleIds, User $targetUser = null): bool
    {
        $user = auth()->user();
        
        // Récupérer les rôles par leurs IDs
        $roles = Role::whereIn('id', $roleIds)->get();
        
        foreach ($roles as $role) {
            // Règle 1: Seul Super Admin peut assigner le rôle Super Admin
            if ($role->name === 'Super Admin' && !$user->hasRole('Super Admin')) {
                return false;
            }
            
            // Règle 2: Empêcher l'auto-promotion (si on modifie un utilisateur existant)
            if ($targetUser && $user->id === $targetUser->id && $role->name === 'Super Admin') {
                return false;
            }
        }
        
        return true;
    }

    /**
     * 🛡️ SÉCURITÉ: Assignation sécurisée des rôles avec vérifications
     *
     * ENTERPRISE-GRADE: Gestion multi-tenant avec nettoyage des anciennes assignations
     */
    private function secureRoleAssignment(User $user, array $roleIds): void
    {
        // Double vérification avant assignation
        if (!empty($roleIds) && !$this->canAssignRoles($roleIds, $user)) {
            throw new AuthorizationException('Permission insuffisante pour assigner ces rôles');
        }

        // CORRECTIF MULTI-TENANT:
        // Supprimer TOUTES les anciennes assignations de rôles pour cet utilisateur
        // pour éviter les conflits de clé primaire (role_id, model_id, model_type)
        // quand organization_id change
        DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->delete();

        // Si aucun rôle à assigner, on s'arrête ici (utilisateur sans rôle)
        if (empty($roleIds)) {
            return;
        }

        // Récupérer les rôles à assigner
        $rolesToSync = Role::whereIn('id', $roleIds)->get();

        // ASSIGNATION MANUELLE avec organization_id correct
        // On ne peut pas utiliser syncRoles() car il ne gère pas bien organization_id
        foreach ($rolesToSync as $role) {
            // Déterminer l'organization_id pour cette assignation
            // Super Admin est global (NULL), les autres sont scoped
            $organizationId = ($role->name === 'Super Admin') ? null : $user->organization_id;

            DB::table('model_has_roles')->insert([
                'role_id' => $role->id,
                'model_type' => get_class($user),
                'model_id' => $user->id,
                'organization_id' => $organizationId,
            ]);
        }

        // Invalider le cache des permissions pour cet utilisateur
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Recharger les relations
        $user->load('roles');
    }

    /**
     * 🛡️ SÉCURITÉ: Détecter les tentatives d'escalation de privilèges
     */
    private function isAttemptingPrivilegeEscalation(User $targetUser, array $roleIds): bool
    {
        $currentUser = auth()->user();
        
        // Si c'est un Super Admin, pas de restriction
        if ($currentUser->hasRole('Super Admin')) {
            return false;
        }
        
        // Vérifier si on essaie d'assigner Super Admin
        $roles = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
        if (in_array('Super Admin', $roles)) {
            return true;
        }
        
        // Vérifier l'auto-promotion
        if ($currentUser->id === $targetUser->id) {
            $currentHighestLevel = $this->getUserHighestRoleLevel($currentUser);
            foreach ($roles as $roleName) {
                $roleLevel = $this->getRoleLevel($roleName);
                if ($roleLevel < $currentHighestLevel) { // Niveau plus élevé
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * 🛡️ SÉCURITÉ: Obtenir le niveau hiérarchique d'un rôle
     */
    private function getRoleLevel(string $roleName): int
    {
        $hierarchy = [
            'Super Admin' => 1,
            'Admin' => 2,
            'Gestionnaire Flotte' => 3,
            'supervisor' => 4,
            'Chauffeur' => 5
        ];
        
        return $hierarchy[$roleName] ?? 999;
    }

    /**
     * 🛡️ SÉCURITÉ: Obtenir le niveau le plus élevé d'un utilisateur
     */
    private function getUserHighestRoleLevel(User $user): int
    {
        $userRoles = $user->roles;
        $minLevel = 999;
        
        foreach ($userRoles as $role) {
            $level = $this->getRoleLevel($role->name);
            if ($level < $minLevel) {
                $minLevel = $level;
            }
        }
        
        return $minLevel;
    }
}
