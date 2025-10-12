<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * 🎭 PERMISSION MATRIX - ENTERPRISE GRADE
 *
 * Console d'administration avancée pour la gestion des rôles et permissions
 * Architecture multi-tenant avec audit complet
 *
 * @version 1.0-ENTERPRISE
 * @author ZenFleet Security Team
 */
class PermissionMatrix extends Component
{
    use WithPagination;

    // 🎯 PROPRIÉTÉS PRINCIPALES
    public $selectedRoleId;
    public $selectedRole;
    public $organizationContext = 'current'; // 'current', 'global', 'all'

    // 🔍 FILTRES ET RECHERCHE
    public $search = '';
    public $filterByResource = '';
    public $filterByAction = '';
    public $showOnlyAssigned = false;

    // 📊 DONNÉES
    public $availableRoles = [];
    public $resources = [];
    public $actions = [];
    public $permissionsMatrix = [];
    public $rolePermissions = [];

    // 🎨 UI STATE
    public $showPreview = false;
    public $showHistory = false;
    public $pendingChanges = [];
    public $confirmationModal = false;

    // 📝 AUDIT
    public $auditLogs = [];
    public $changesSummary = [];

    /**
     * 📋 DÉFINITION DES ACTIONS PAR RESSOURCE
     */
    private function getResourceActionsMap(): array
    {
        return [
            'organizations' => ['view', 'create', 'edit', 'delete', 'restore', 'export', 'manage settings', 'view statistics'],
            'users' => ['view', 'create', 'edit', 'delete', 'restore', 'export', 'manage roles', 'reset passwords', 'impersonate'],
            'roles' => ['view', 'manage'],
            'vehicles' => ['view', 'create', 'edit', 'delete', 'restore', 'export', 'import', 'view history', 'manage maintenance', 'manage documents'],
            'drivers' => ['view', 'create', 'edit', 'delete', 'restore', 'export', 'import', 'view history', 'assign to vehicles', 'manage licenses'],
            'assignments' => ['view', 'create', 'edit', 'delete', 'end', 'extend', 'export', 'view calendar', 'view gantt'],
            'maintenance' => ['view', 'manage plans', 'create operations', 'edit operations', 'delete operations', 'approve operations', 'export reports'],
            'repair_requests' => [
                'view own', 'view team', 'view all',
                'create', 'update own', 'delete',
                'approve level 1', 'approve level 2', 'reject',
                'export'
            ],
            'mileage_readings' => [
                'view own', 'view team', 'view all',
                'create', 'edit', 'delete', 'export'
            ],
            'suppliers' => ['view', 'create', 'edit', 'delete', 'restore', 'export', 'manage contracts'],
            'expenses' => ['view', 'create', 'edit', 'delete', 'approve', 'export', 'view analytics'],
            'documents' => ['view', 'create', 'edit', 'delete', 'download', 'approve', 'export'],
            'analytics' => ['view', 'view performance metrics', 'view roi metrics', 'export'],
            'alerts' => ['view', 'create', 'edit', 'delete', 'mark as read', 'export'],
            'audit' => ['view logs', 'export logs', 'view security', 'view user', 'view organization'],
        ];
    }

    /**
     * 🚀 INITIALISATION
     */
    public function mount()
    {
        // Vérifier les permissions d'accès
        $this->authorize('manage', Role::class);

        // Charger les données initiales
        $this->loadAvailableRoles();
        $this->prepareResourcesAndActions();

        // Sélectionner le premier rôle par défaut
        if ($this->availableRoles->isNotEmpty()) {
            $this->selectedRoleId = $this->availableRoles->first()->id;
            $this->loadRolePermissions();
        }

        // Charger l'historique d'audit
        $this->loadAuditHistory();
    }

    /**
     * 📦 CHARGER LES RÔLES DISPONIBLES
     */
    public function loadAvailableRoles()
    {
        $query = Role::with('permissions')->withCount('permissions');

        if (Auth::user()->hasRole('Super Admin')) {
            // Super Admin voit tous les rôles
            $this->availableRoles = $query->get();
        } else {
            // Admin ne voit que les rôles de son organisation (pas Super Admin)
            $currentOrgId = Auth::user()->organization_id;

            $this->availableRoles = $query
                ->where(function($q) use ($currentOrgId) {
                    $q->where('organization_id', $currentOrgId)
                      ->orWhereNull('organization_id');
                })
                ->where('name', '!=', 'Super Admin')
                ->get();
        }
    }

    /**
     * 🗂️ PRÉPARER RESSOURCES ET ACTIONS
     */
    public function prepareResourcesAndActions()
    {
        $resourceActionsMap = $this->getResourceActionsMap();

        // Extraire les ressources uniques
        $this->resources = array_keys($resourceActionsMap);
        sort($this->resources);

        // Extraire toutes les actions uniques
        $allActions = [];
        foreach ($resourceActionsMap as $actions) {
            $allActions = array_merge($allActions, $actions);
        }
        $this->actions = array_unique($allActions);
        sort($this->actions);

        // Construire la matrice permissions
        $this->buildPermissionsMatrix($resourceActionsMap);
    }

    /**
     * 🏗️ CONSTRUIRE LA MATRICE DES PERMISSIONS
     */
    private function buildPermissionsMatrix(array $resourceActionsMap)
    {
        $allPermissions = Permission::all()->keyBy('name');

        $this->permissionsMatrix = [];

        foreach ($resourceActionsMap as $resource => $actions) {
            foreach ($actions as $action) {
                $permissionName = "{$action} {$resource}";

                if ($allPermissions->has($permissionName)) {
                    $permission = $allPermissions->get($permissionName);

                    $this->permissionsMatrix[] = [
                        'id' => $permission->id,
                        'name' => $permissionName,
                        'resource' => $resource,
                        'action' => $action,
                        'display_resource' => $this->formatResourceName($resource),
                        'display_action' => $this->formatActionName($action),
                    ];
                }
            }
        }
    }

    /**
     * 🔄 CHARGER LES PERMISSIONS DU RÔLE
     */
    public function loadRolePermissions()
    {
        if (!$this->selectedRoleId) {
            $this->rolePermissions = [];
            return;
        }

        $this->selectedRole = Role::with('permissions')->find($this->selectedRoleId);

        if ($this->selectedRole) {
            $this->rolePermissions = $this->selectedRole->permissions->pluck('id')->toArray();
        } else {
            $this->rolePermissions = [];
        }

        // Reset pending changes when switching roles
        $this->pendingChanges = [];
    }

    /**
     * 🎯 TOGGLE PERMISSION
     */
    public function togglePermission(int $permissionId)
    {
        if (!$this->selectedRole) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Veuillez sélectionner un rôle'
            ]);
            return;
        }

        // Check if already in pending changes
        if (in_array($permissionId, $this->rolePermissions)) {
            // Remove from role permissions
            $this->rolePermissions = array_diff($this->rolePermissions, [$permissionId]);
            $this->pendingChanges[] = ['action' => 'remove', 'permission_id' => $permissionId];
        } else {
            // Add to role permissions
            $this->rolePermissions[] = $permissionId;
            $this->pendingChanges[] = ['action' => 'add', 'permission_id' => $permissionId];
        }

        $this->dispatch('notification', [
            'type' => 'info',
            'message' => 'Modification en attente - Cliquez sur Enregistrer pour appliquer'
        ]);
    }

    /**
     * ✅ SÉLECTIONNER TOUTES LES PERMISSIONS D'UNE RESSOURCE
     */
    public function selectAllForResource(string $resource)
    {
        if (!$this->selectedRole) return;

        $resourcePermissions = collect($this->permissionsMatrix)
            ->where('resource', $resource)
            ->pluck('id')
            ->toArray();

        foreach ($resourcePermissions as $permId) {
            if (!in_array($permId, $this->rolePermissions)) {
                $this->rolePermissions[] = $permId;
                $this->pendingChanges[] = ['action' => 'add', 'permission_id' => $permId];
            }
        }

        $this->dispatch('notification', [
            'type' => 'success',
            'message' => 'Toutes les permissions de ' . $this->formatResourceName($resource) . ' sélectionnées'
        ]);
    }

    /**
     * ❌ DÉSÉLECTIONNER TOUTES LES PERMISSIONS D'UNE RESSOURCE
     */
    public function deselectAllForResource(string $resource)
    {
        if (!$this->selectedRole) return;

        $resourcePermissions = collect($this->permissionsMatrix)
            ->where('resource', $resource)
            ->pluck('id')
            ->toArray();

        foreach ($resourcePermissions as $permId) {
            if (in_array($permId, $this->rolePermissions)) {
                $this->rolePermissions = array_diff($this->rolePermissions, [$permId]);
                $this->pendingChanges[] = ['action' => 'remove', 'permission_id' => $permId];
            }
        }

        $this->dispatch('notification', [
            'type' => 'warning',
            'message' => 'Toutes les permissions de ' . $this->formatResourceName($resource) . ' désélectionnées'
        ]);
    }

    /**
     * ✅ SÉLECTIONNER TOUTES LES PERMISSIONS D'UNE ACTION
     */
    public function selectAllForAction(string $action)
    {
        if (!$this->selectedRole) return;

        $actionPermissions = collect($this->permissionsMatrix)
            ->where('action', $action)
            ->pluck('id')
            ->toArray();

        foreach ($actionPermissions as $permId) {
            if (!in_array($permId, $this->rolePermissions)) {
                $this->rolePermissions[] = $permId;
                $this->pendingChanges[] = ['action' => 'add', 'permission_id' => $permId];
            }
        }

        $this->dispatch('notification', [
            'type' => 'success',
            'message' => 'Toutes les permissions "' . $this->formatActionName($action) . '" sélectionnées'
        ]);
    }

    /**
     * ❌ DÉSÉLECTIONNER TOUTES LES PERMISSIONS D'UNE ACTION
     */
    public function deselectAllForAction(string $action)
    {
        if (!$this->selectedRole) return;

        $actionPermissions = collect($this->permissionsMatrix)
            ->where('action', $action)
            ->pluck('id')
            ->toArray();

        foreach ($actionPermissions as $permId) {
            if (in_array($permId, $this->rolePermissions)) {
                $this->rolePermissions = array_diff($this->rolePermissions, [$permId]);
                $this->pendingChanges[] = ['action' => 'remove', 'permission_id' => $permId];
            }
        }

        $this->dispatch('notification', [
            'type' => 'warning',
            'message' => 'Toutes les permissions "' . $this->formatActionName($action) . '" désélectionnées'
        ]);
    }

    /**
     * 💾 ENREGISTRER LES MODIFICATIONS
     */
    public function save()
    {
        if (!$this->selectedRole) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Aucun rôle sélectionné'
            ]);
            return;
        }

        // Vérifier les permissions
        $this->authorize('manage', Role::class);

        // Empêcher la modification du rôle Super Admin par un non-Super Admin
        if ($this->selectedRole->name === 'Super Admin' && !Auth::user()->hasRole('Super Admin')) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Vous ne pouvez pas modifier le rôle Super Admin'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            // Synchroniser les permissions
            $permissions = Permission::whereIn('id', $this->rolePermissions)->get();
            $this->selectedRole->syncPermissions($permissions);

            // Audit log
            $this->logPermissionChange();

            // Vider le cache des permissions
            $this->clearPermissionsCache();

            DB::commit();

            // Reset pending changes
            $this->pendingChanges = [];

            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Permissions mises à jour avec succès pour le rôle ' . $this->selectedRole->name
            ]);

            // Recharger les données
            $this->loadRolePermissions();
            $this->loadAuditHistory();

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating role permissions', [
                'role_id' => $this->selectedRole->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 🔄 ANNULER LES MODIFICATIONS
     */
    public function cancel()
    {
        $this->loadRolePermissions();
        $this->pendingChanges = [];

        $this->dispatch('notification', [
            'type' => 'info',
            'message' => 'Modifications annulées'
        ]);
    }

    /**
     * 📜 LOGGER LES CHANGEMENTS
     */
    private function logPermissionChange()
    {
        Log::info('Role permissions updated', [
            'role_id' => $this->selectedRole->id,
            'role_name' => $this->selectedRole->name,
            'permissions_count' => count($this->rolePermissions),
            'updated_by' => Auth::id(),
            'updated_by_name' => Auth::user()->name,
            'organization_id' => Auth::user()->organization_id,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * 🗑️ VIDER LE CACHE DES PERMISSIONS
     */
    private function clearPermissionsCache()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Cache::forget('spatie.permission.cache');

        Log::info('Permission cache cleared', [
            'cleared_by' => Auth::id(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * 📊 CHARGER L'HISTORIQUE D'AUDIT
     */
    private function loadAuditHistory()
    {
        // Cette méthode sera utilisée pour charger les logs d'audit depuis la base
        // Pour l'instant, on peut lire les logs Laravel récents
        $this->auditLogs = [];
    }

    /**
     * 🎨 FORMATER LE NOM D'UNE RESSOURCE
     */
    private function formatResourceName(string $resource): string
    {
        $translations = [
            'organizations' => 'Organisations',
            'users' => 'Utilisateurs',
            'roles' => 'Rôles',
            'vehicles' => 'Véhicules',
            'drivers' => 'Chauffeurs',
            'assignments' => 'Affectations',
            'maintenance' => 'Maintenance',
            'repair_requests' => 'Demandes Réparation',
            'mileage_readings' => 'Relevés Kilométriques',
            'suppliers' => 'Fournisseurs',
            'expenses' => 'Dépenses',
            'documents' => 'Documents',
            'analytics' => 'Analytics',
            'alerts' => 'Alertes',
            'audit' => 'Audit',
        ];

        return $translations[$resource] ?? ucfirst(str_replace('_', ' ', $resource));
    }

    /**
     * 🎨 FORMATER LE NOM D'UNE ACTION
     */
    private function formatActionName(string $action): string
    {
        $translations = [
            'view' => 'Voir',
            'view own' => 'Voir (Propres)',
            'view team' => 'Voir (Équipe)',
            'view all' => 'Voir (Tous)',
            'create' => 'Créer',
            'edit' => 'Modifier',
            'update own' => 'Modifier (Propres)',
            'delete' => 'Supprimer',
            'restore' => 'Restaurer',
            'export' => 'Exporter',
            'import' => 'Importer',
            'manage' => 'Gérer',
            'approve' => 'Approuver',
            'approve level 1' => 'Approuver (N1)',
            'approve level 2' => 'Approuver (N2)',
            'reject' => 'Rejeter',
            'assign to vehicles' => 'Assigner aux véhicules',
            'manage roles' => 'Gérer rôles',
            'manage licenses' => 'Gérer permis',
            'manage contracts' => 'Gérer contrats',
            'manage plans' => 'Gérer plans',
            'manage operations' => 'Gérer opérations',
            'create operations' => 'Créer opérations',
            'edit operations' => 'Modifier opérations',
            'delete operations' => 'Supprimer opérations',
            'approve operations' => 'Approuver opérations',
            'view history' => 'Voir historique',
            'view calendar' => 'Voir calendrier',
            'view gantt' => 'Voir Gantt',
            'view statistics' => 'Voir statistiques',
            'view analytics' => 'Voir analytics',
            'view performance metrics' => 'Voir métriques performance',
            'view roi metrics' => 'Voir métriques ROI',
            'view logs' => 'Voir logs',
            'export logs' => 'Exporter logs',
            'view security' => 'Voir sécurité',
            'view user' => 'Voir utilisateur',
            'view organization' => 'Voir organisation',
            'mark as read' => 'Marquer lu',
            'reset passwords' => 'Réinitialiser MDP',
            'impersonate' => 'Impersonate',
            'manage settings' => 'Gérer paramètres',
            'manage maintenance' => 'Gérer maintenance',
            'manage documents' => 'Gérer documents',
            'download' => 'Télécharger',
            'end' => 'Terminer',
            'extend' => 'Prolonger',
            'export reports' => 'Exporter rapports',
        ];

        return $translations[$action] ?? ucfirst($action);
    }

    /**
     * 🔄 WHEN SELECTED ROLE CHANGES
     */
    public function updatedSelectedRoleId()
    {
        $this->loadRolePermissions();
    }

    /**
     * 🔄 WHEN SEARCH CHANGES
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * 🎨 RENDER
     */
    public function render()
    {
        // Appliquer les filtres
        $filteredMatrix = collect($this->permissionsMatrix);

        // Filtre par recherche
        if ($this->search) {
            $filteredMatrix = $filteredMatrix->filter(function ($perm) {
                return str_contains(strtolower($perm['name']), strtolower($this->search)) ||
                       str_contains(strtolower($perm['display_resource']), strtolower($this->search)) ||
                       str_contains(strtolower($perm['display_action']), strtolower($this->search));
            });
        }

        // Filtre par ressource
        if ($this->filterByResource) {
            $filteredMatrix = $filteredMatrix->where('resource', $this->filterByResource);
        }

        // Filtre par action
        if ($this->filterByAction) {
            $filteredMatrix = $filteredMatrix->where('action', $this->filterByAction);
        }

        // Filtre "uniquement assignées"
        if ($this->showOnlyAssigned) {
            $filteredMatrix = $filteredMatrix->filter(function ($perm) {
                return in_array($perm['id'], $this->rolePermissions);
            });
        }

        // Regrouper par ressource
        $groupedPermissions = $filteredMatrix->groupBy('resource');

        return view('livewire.admin.permission-matrix', [
            'groupedPermissions' => $groupedPermissions,
            'hasPendingChanges' => count($this->pendingChanges) > 0,
        ]);
    }
}
