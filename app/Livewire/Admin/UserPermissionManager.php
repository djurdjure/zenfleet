<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserPermissionManager extends Component
{
    public $userId;
    public $user;
    public $selectedRole;
    public $customPermissions = [];
    public $availableRoles;
    public $allPermissions;
    public $permissionsByCategory = [];
    public $useCustomPermissions = false;

    protected $rules = [
        'selectedRole' => 'required|exists:roles,id',
        'customPermissions' => 'array',
        'customPermissions.*' => 'exists:permissions,id',
    ];

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->user = User::with(['roles', 'permissions'])->findOrFail($userId);

        // Vérifier les permissions
        if (!Auth::user()->can('edit users') && !Auth::user()->hasRole('Super Admin')) {
            abort(403, 'Accès non autorisé');
        }

        // Isolation multi-tenant : Admin ne peut modifier que les utilisateurs de son org
        if (!Auth::user()->hasRole('Super Admin') && $this->user->organization_id !== Auth::user()->organization_id) {
            abort(403, 'Vous ne pouvez modifier que les utilisateurs de votre organisation');
        }

        // Charger les rôles disponibles
        $this->loadAvailableRoles();

        // Charger toutes les permissions
        $this->loadPermissions();

        // Initialiser le rôle sélectionné
        $this->selectedRole = $this->user->roles->first()->id ?? null;

        // Vérifier si l'utilisateur a des permissions personnalisées
        $this->checkCustomPermissions();
    }

    public function loadAvailableRoles()
    {
        if (Auth::user()->hasRole('Super Admin')) {
            // Super Admin voit tous les rôles
            $this->availableRoles = Role::all();
        } else {
            // Admin ne peut assigner que certains rôles (pas Super Admin)
            $this->availableRoles = Role::where('name', '!=', 'Super Admin')->get();
        }
    }

    public function loadPermissions()
    {
        $this->allPermissions = Permission::all();

        // Organiser les permissions par catégorie
        $this->permissionsByCategory = [
            'Véhicules' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'vehicle'))->values(),
            'Chauffeurs' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'driver'))->values(),
            'Affectations' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'assignment'))->values(),
            'Fournisseurs' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'supplier'))->values(),
            'Utilisateurs' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'user'))->values(),
            'Organisations' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'organization'))->values(),
            'Dépôts' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'depot'))->values(),
            'Maintenance' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'maintenance'))->values(),
            'Réparations' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'repair'))->values(),
            'Alertes' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'alert'))->values(),
            'Documents' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'document'))->values(),
            'Dépenses' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'expense'))->values(),
            'Kilométrage' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'mileage'))->values(),
            'Sanctions' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'sanction'))->values(),
            'Rapports' => $this->allPermissions->filter(fn($p) => str_contains($p->name, 'report') || str_contains($p->name, 'dashboard') || str_contains($p->name, 'analytics'))->values(),
            'Système' => $this->allPermissions->filter(fn($p) =>
                str_contains($p->name, 'setting') ||
                str_contains($p->name, 'audit') ||
                str_contains($p->name, 'role')
            )->values(),
        ];

        // Filtrer les catégories vides
        $this->permissionsByCategory = collect($this->permissionsByCategory)
            ->filter(fn($perms) => $perms->count() > 0);
    }

    public function checkCustomPermissions()
    {
        $role = Role::find($this->selectedRole);
        if (!$role) return;

        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $userPermissions = $this->user->permissions->pluck('id')->toArray();

        // Si l'utilisateur a des permissions différentes du rôle, activer custom
        $this->useCustomPermissions = !empty(array_diff($userPermissions, $rolePermissions)) ||
                                      !empty(array_diff($rolePermissions, $userPermissions));

        if ($this->useCustomPermissions) {
            $this->customPermissions = $userPermissions;
        } else {
            $this->customPermissions = $rolePermissions;
        }
    }

    public function updatedSelectedRole($roleId)
    {
        $role = Role::find($roleId);
        if ($role) {
            // Charger les permissions du rôle
            $this->customPermissions = $role->permissions->pluck('id')->toArray();
        }
    }

    public function toggleCustomPermissions()
    {
        $this->useCustomPermissions = !$this->useCustomPermissions;

        if (!$this->useCustomPermissions && $this->selectedRole) {
            // Revenir aux permissions du rôle
            $role = Role::find($this->selectedRole);
            if ($role) {
                $this->customPermissions = $role->permissions->pluck('id')->toArray();
            }
        }
    }

    public function selectAllInCategory($category)
    {
        $categoryPermissions = $this->permissionsByCategory[$category] ?? collect();
        $categoryIds = $categoryPermissions->pluck('id')->toArray();

        // Ajouter toutes les permissions de la catégorie
        $this->customPermissions = array_unique(array_merge($this->customPermissions, $categoryIds));
    }

    public function deselectAllInCategory($category)
    {
        $categoryPermissions = $this->permissionsByCategory[$category] ?? collect();
        $categoryIds = $categoryPermissions->pluck('id')->toArray();

        // Retirer toutes les permissions de la catégorie
        $this->customPermissions = array_diff($this->customPermissions, $categoryIds);
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Mettre à jour le rôle
            $role = Role::findOrFail($this->selectedRole);

            // Vérifier les permissions d'escalation
            if (!Auth::user()->hasRole('Super Admin') && $role->name === 'Super Admin') {
                $this->addError('selectedRole', 'Vous ne pouvez pas assigner le rôle Super Admin');
                DB::rollBack();
                return;
            }

            // Empêcher l'auto-promotion
            if ($this->user->id === Auth::id() && $role->name === 'Super Admin') {
                $this->addError('selectedRole', 'Vous ne pouvez pas vous auto-promouvoir Super Admin');
                DB::rollBack();
                return;
            }

            // Synchroniser le rôle
            $this->user->syncRoles([$role->name]);

            // Synchroniser les permissions si mode personnalisé
            if ($this->useCustomPermissions) {
                $permissions = Permission::whereIn('id', $this->customPermissions)->get();
                $this->user->syncPermissions($permissions);
            } else {
                // Supprimer les permissions directes, utiliser uniquement celles du rôle
                $this->user->permissions()->detach();
            }

            // Log de l'action
            Log::info('User permissions updated', [
                'user_id' => $this->user->id,
                'role' => $role->name,
                'custom_permissions' => $this->useCustomPermissions,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            // Message de succès
            session()->flash('success', 'Permissions mises à jour avec succès');

            // Vider le cache des permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // Rediriger vers la liste des utilisateurs
            return redirect()->route('admin.users.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user permissions', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);

            $this->addError('general', 'Une erreur est survenue lors de la mise à jour des permissions');
        }
    }

    public function render()
    {
        return view('livewire.admin.user-permission-manager');
    }
}
