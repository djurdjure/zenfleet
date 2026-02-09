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
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use App\Services\OrganizationRoleProvisioner;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('users.view');

        $query = User::with(['roles', 'organization'])->withCount('vehicles');

        // Si l'utilisateur n'est pas Super Admin, on filtre par son organisation
        if (!auth()->user()->hasRole('Super Admin')) {
            $query->where('organization_id', auth()->user()->organization_id);
        }

        $perPage = (int) $request->input('per_page', 15);
        $users = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('users.create');

        $user = auth()->user();
        if ($user->hasRole('Super Admin')) {
            $organizations = Organization::withoutGlobalScope('organization')->orderBy('name')->get();
        } else {
            $organizations = Organization::where('id', $user->organization_id)->get();
        }

        $selectedOrganizationId = (int) (request()->query('organization_id')
            ?: old('organization_id')
            ?: ($user->hasRole('Super Admin') ? ($organizations->first()->id ?? 0) : $user->organization_id));

        if ($selectedOrganizationId > 0) {
            $this->ensureRolesForTargetOrganization($selectedOrganizationId);
        }

        // 🛡️ SÉCURITÉ: Filtrer les rôles selon l'organisation cible
        $roles = $this->getAssignableRoles($selectedOrganizationId > 0 ? $selectedOrganizationId : null);

        return view('admin.users.create', compact('roles', 'organizations', 'selectedOrganizationId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('users.create');

        $user = auth()->user();
        $organizationId = $user->hasRole('Super Admin') ? $request->input('organization_id') : $user->organization_id;
        $request->merge(['organization_id' => $organizationId]);

        if ($organizationId) {
            $this->ensureRolesForTargetOrganization((int) $organizationId);
        }

        // 🛡️ SÉCURITÉ: Validation des rôles avec contrôle d'escalation
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'organization_id' => ['required', 'exists:organizations,id'],
            'roles' => ['sometimes', 'array', function ($attribute, $value, $fail) use ($organizationId) {
                if (!$this->canAssignRoles($value, null, (int) $organizationId)) {
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
        $this->authorize('users.update');

        // 🛡️ SÉCURITÉ: Filtrer les rôles selon les permissions
        $roles = $this->getAssignableRoles((int) $user->organization_id);
        
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
        $this->authorize('users.update');

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
            'roles' => ['sometimes', 'array', function ($attribute, $value, $fail) use ($user, $request) {
                if (!$this->canAssignRoles($value, $user, (int) $request->input('organization_id'))) {
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
        $this->authorize('users.delete');

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
        Log::warning('Utilisateur archivé', [
            'deleter' => auth()->user()->email,
            'deleted_user' => $user->email,
            'deleted_user_roles' => $user->getRoleNames()->toArray(),
            'ip' => request()->ip()
        ]);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur archivé avec succès.');
    }

    public function forceDelete(User $user): RedirectResponse
    {
        $this->authorize('users.delete');

        if (auth()->id() == $user->id) {
            return back()->with('error', 'Vous ne pouvez pas supprimer définitivement votre propre compte.');
        }

        // 🛡️ SÉCURITÉ: Empêcher la suppression du dernier Super Admin
        if ($user->hasRole('Super Admin')) {
            $superAdminCount = User::role('Super Admin')->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', 'Impossible de supprimer le dernier Super Admin.');
            }

            // Seul un Super Admin peut supprimer un autre Super Admin
            if (!auth()->user()->hasRole('Super Admin')) {
                abort(403, 'Seul un Super Admin peut supprimer définitivement un autre Super Admin.');
            }
        }

        DB::transaction(function () use ($user) {
            // Dissocier un éventuel chauffeur lié
            \App\Models\Driver::withTrashed()
                ->where('user_id', $user->id)
                ->update(['user_id' => null]);

            // Révoquer accès véhicules
            $user->vehicles()->detach();

            // Supprimer tokens API si existants
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            // Nettoyer les pivots Spatie (roles/permissions)
            DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->where('model_id', $user->id)
                ->delete();

            if (Schema::hasTable('model_has_permissions')) {
                DB::table('model_has_permissions')
                    ->where('model_type', User::class)
                    ->where('model_id', $user->id)
                    ->delete();
            }

            $user->forceDelete();
        });

        Log::warning('Utilisateur supprimé définitivement', [
            'deleter' => auth()->user()->email,
            'deleted_user' => $user->email,
            'deleted_user_roles' => $user->getRoleNames()->toArray(),
            'ip' => request()->ip()
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé définitivement avec succès.');
    }

    /**
     * 🛡️ SÉCURITÉ: Obtenir les rôles que l'utilisateur connecté peut assigner
     */
    private function getAssignableRoles(?int $organizationId = null)
    {
        $user = auth()->user();

        if ($user->hasRole('Super Admin')) {
            // Scope strict: l'organisation cible + rôle global Super Admin (si présent)
            $query = Role::query();

            if ($organizationId) {
                $query->where(function ($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId)
                        ->orWhere(function ($globalQ) {
                            $globalQ->whereNull('organization_id')
                                ->where('name', 'Super Admin');
                        });
                });
            } else {
                $query->whereNull('organization_id');
            }

            return $query->orderBy('name')->get()->unique('name')->values();
        }

        // Admin/Gestionnaire: uniquement les rôles de son organisation, sans Super Admin
        return Role::where('organization_id', $user->organization_id)
            ->where('name', '!=', 'Super Admin')
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->values();
    }

    /**
     * 🛡️ SÉCURITÉ: Vérifier si l'utilisateur peut assigner les rôles demandés
     */
    private function canAssignRoles(array $roleIds, User $targetUser = null, ?int $targetOrganizationId = null): bool
    {
        $user = auth()->user();

        if ($targetOrganizationId && !$user->hasRole('Super Admin') && (int) $targetOrganizationId !== (int) $user->organization_id) {
            return false;
        }

        // Récupérer les rôles par leurs IDs
        $roles = Role::whereIn('id', $roleIds)->get();
        if ($roles->count() !== count($roleIds)) {
            return false;
        }

        foreach ($roles as $role) {
            // Règle 1: Seul Super Admin peut assigner le rôle Super Admin
            if ($role->name === 'Super Admin' && !$user->hasRole('Super Admin')) {
                return false;
            }

            // Règle 2: Empêcher l'auto-promotion (si on modifie un utilisateur existant)
            if ($targetUser && $user->id === $targetUser->id && $role->name === 'Super Admin') {
                return false;
            }

            // Règle 3: Interdire les rôles d'une autre organisation pour les rôles non-globaux
            if ($role->name !== 'Super Admin' && $targetOrganizationId && $role->organization_id !== null) {
                if ((int) $role->organization_id !== (int) $targetOrganizationId) {
                    return false;
                }
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
        if (!empty($roleIds) && !$this->canAssignRoles($roleIds, $user, (int) $user->organization_id)) {
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

        // Récupérer les rôles à assigner puis les normaliser par nom
        // pour éviter les erreurs de scope (ID d'une autre organisation)
        $selectedRoles = Role::whereIn('id', $roleIds)->get()->unique('name');
        $rolesToAssign = collect();

        foreach ($selectedRoles as $selectedRole) {
            $resolvedRole = $this->resolveRoleForOrganization($selectedRole->name, (int) $user->organization_id);
            if ($resolvedRole) {
                $rolesToAssign->push($resolvedRole);
            }
        }

        // ASSIGNATION MANUELLE avec organization_id correct
        // On ne peut pas utiliser syncRoles() car il ne gère pas bien organization_id
        foreach ($rolesToAssign as $role) {
            // organization_id est requis dans model_has_roles.
            // Même pour un rôle global (ex: Super Admin), on garde le contexte org utilisateur.
            $organizationId = $user->organization_id;

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
     * Résout le rôle applicable à l'organisation cible.
     */
    private function resolveRoleForOrganization(string $roleName, int $organizationId): ?Role
    {
        if ($roleName === 'Super Admin') {
            return Role::where('name', 'Super Admin')
                ->whereNull('organization_id')
                ->first()
                ?? Role::where('name', 'Super Admin')->first();
        }

        return Role::where('name', $roleName)
            ->where('organization_id', $organizationId)
            ->first()
            ?? Role::where('name', $roleName)->whereNull('organization_id')->first();
    }

    /**
     * S'assure que les rôles de base existent pour l'organisation cible.
     */
    private function ensureRolesForTargetOrganization(int $organizationId): void
    {
        $organization = Organization::withoutGlobalScope('organization')->find($organizationId);
        if (!$organization) {
            return;
        }

        app(OrganizationRoleProvisioner::class)->ensureRolesForOrganization($organization);
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
