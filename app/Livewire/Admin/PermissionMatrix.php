<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Organization;
use App\Support\PermissionAliases;
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
    public $organizationContext = 'organization'; // 'organization', 'global', 'all'
    public $availableOrganizations = [];
    public $selectedOrganizationId;

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
    public $compactMode = false;
    public $pendingChanges = [];
    public $confirmationModal = false;
    public $showApplyAllModal = false;
    public $applyAllTargetCount = 0;

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
            'users' => ['view', 'create', 'edit', 'delete', 'restore', 'export', 'roles.manage', 'reset passwords', 'impersonate'],
            'roles' => ['view', 'manage'],
            'vehicles' => ['view', 'create', 'edit', 'delete', 'restore', 'export', 'import', 'view history', 'manage maintenance', 'manage documents'],
            'drivers' => ['view', 'create', 'edit', 'delete', 'restore', 'export', 'import', 'view history', 'assign to vehicles', 'manage licenses'],
            'assignments' => ['view', 'create', 'edit', 'delete', 'end', 'extend', 'export', 'view calendar', 'assignments.view-gantt'],
            'depots' => ['view', 'create', 'edit', 'delete', 'restore', 'export'],
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
            'expenses' => ['view', 'create', 'edit', 'delete', 'approve', 'export', 'analytics.view'],
            'documents' => ['view', 'create', 'edit', 'delete', 'download', 'approve', 'export'],
            'analytics' => ['view', 'view performance metrics', 'view roi metrics', 'export'],
            'alerts' => ['view', 'create', 'edit', 'delete', 'mark as read', 'export'],
            'audit' => ['view logs', 'export logs', 'view security', 'view user', 'view organization'],
        ];
    }

    /**
     * 🚀 INITIALISATION
     */
    public function mount(?int $roleId = null)
    {
        // Vérifier les permissions d'accès
        $this->authorize('manage', Role::class);

        $user = Auth::user();
        $requestedRole = $roleId ? Role::find($roleId) : null;

        if ($user->hasRole('Super Admin')) {
            $this->availableOrganizations = Organization::orderBy('name')
                ->get(['id', 'name', 'legal_name']);
            if ($requestedRole) {
                if ($requestedRole->organization_id === null) {
                    $this->organizationContext = 'global';
                    $this->selectedOrganizationId = $user->organization_id
                        ?? $this->availableOrganizations->first()?->id;
                } else {
                    $this->organizationContext = 'organization';
                    $this->selectedOrganizationId = $requestedRole->organization_id;
                }
            } else {
                $this->selectedOrganizationId = $user->organization_id
                    ?? $this->availableOrganizations->first()?->id;
            }
        } else {
            $this->selectedOrganizationId = $user->organization_id;
        }

        // Charger les données initiales
        $this->loadAvailableRoles();
        $this->prepareResourcesAndActions();

        // Sélectionner le premier rôle par défaut
        if ($requestedRole && $this->availableRoles->contains('id', $requestedRole->id)) {
            $this->selectedRoleId = $requestedRole->id;
        } elseif ($this->availableRoles->isNotEmpty()) {
            $this->selectedRoleId = $this->availableRoles->first()->id;
        }

        if ($this->selectedRoleId) {
            $this->loadRolePermissions();
        }

        // Charger l'historique d'audit
        $this->loadAuditHistory();
    }

    /**
     * 🔁 Recharger les rôles quand le contexte change.
     */
    public function updatedOrganizationContext($value = null): void
    {
        $normalized = $this->normalizeSelectValue($value ?? $this->organizationContext);
        $this->organizationContext = $normalized ?: 'organization';
        $this->loadAvailableRoles();

        if ($this->availableRoles->isNotEmpty()) {
            if (!$this->selectedRoleId || !$this->availableRoles->contains('id', $this->selectedRoleId)) {
                $this->selectedRoleId = $this->availableRoles->first()->id;
                $this->loadRolePermissions();
            }
        } else {
            $this->selectedRoleId = null;
            $this->selectedRole = null;
            $this->rolePermissions = [];
        }
    }

    public function updatedSelectedOrganizationId($value = null): void
    {
        $this->selectedOrganizationId = $this->normalizeSelectId($value ?? $this->selectedOrganizationId);
        $this->loadAvailableRoles();

        if ($this->availableRoles->isNotEmpty()) {
            $this->selectedRoleId = $this->availableRoles->first()->id;
            $this->loadRolePermissions();
        } else {
            $this->selectedRoleId = null;
            $this->selectedRole = null;
            $this->rolePermissions = [];
        }
    }

    /**
     * 📦 CHARGER LES RÔLES DISPONIBLES
     */
    public function loadAvailableRoles()
    {
        $query = Role::with('permissions')->withCount('permissions');
        $currentOrgId = $this->selectedOrganizationId ?? Auth::user()->organization_id;

        if (Auth::user()->hasRole('Super Admin')) {
            if ($this->organizationContext === 'global') {
                $query->whereNull('organization_id');
            } elseif ($this->organizationContext === 'all') {
                // no filter
            } else {
                if ($currentOrgId) {
                    $query->where('organization_id', $currentOrgId);
                }
            }

            $roles = $query->get();

            if ($this->organizationContext !== 'all') {
                $roles = $this->collapseRoleVariants($roles, $currentOrgId);
            }

            $this->availableRoles = $roles->sortBy('name')->values();
        } else {
            // Admin : ne voit que les rôles de son organisation (avec fallback global si absent)
            $roles = $query
                ->where('organization_id', $currentOrgId)
                ->where('name', '!=', 'Super Admin')
                ->get();

            $this->availableRoles = $this->collapseRoleVariants($roles, $currentOrgId)
                ->sortBy('name')
                ->values();
        }
    }

    /**
     * ✅ Déduplique par nom en privilégiant le rôle de l'organisation courante.
     */
    private function collapseRoleVariants($roles, ?int $organizationId)
    {
        return $roles->groupBy('name')->map(function ($group) use ($organizationId) {
            if ($organizationId !== null) {
                $orgRole = $group->firstWhere('organization_id', $organizationId);
                if ($orgRole) {
                    return $orgRole;
                }
            }

            return $group->firstWhere('organization_id', null) ?? $group->first();
        });
    }

    /**
     * 🗂️ PRÉPARER RESSOURCES ET ACTIONS
     */
    public function prepareResourcesAndActions()
    {
        $this->buildPermissionsMatrixFromPermissions();
    }

    /**
     * 🏗️ CONSTRUIRE LA MATRICE DES PERMISSIONS
     */
    private function buildPermissionsMatrixFromPermissions(): void
    {
        $this->permissionsMatrix = [];
        $permissions = Permission::query()->orderBy('name')->get();
        $permissions = $this->filterLegacyPermissions($permissions);

        foreach ($permissions as $permission) {
            $meta = $this->describePermission($permission->name);

            $this->permissionsMatrix[] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'resource' => $meta['resource'],
                'action' => $meta['action'],
                'display_resource' => $meta['display_resource'],
                'display_action' => $meta['display_action'],
                'display_name' => $meta['display_name'],
            ];
        }

        $this->resources = collect($this->permissionsMatrix)
            ->pluck('resource')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        $this->actions = collect($this->permissionsMatrix)
            ->pluck('action')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function filterLegacyPermissions($permissions)
    {
        $allNames = $permissions->pluck('name');

        return $permissions->filter(function ($permission) use ($allNames) {
            if (!PermissionAliases::isLegacy($permission->name)) {
                return true;
            }

            $canonical = PermissionAliases::canonicalFor($permission->name);

            return !$canonical || !$allNames->contains($canonical);
        })->values();
    }

    private function describePermission(string $permissionName): array
    {
        $canonical = PermissionAliases::canonicalFor($permissionName) ?? $permissionName;
        $canonical = trim($canonical);

        $overrides = $this->permissionPresentationOverrides();
        if (isset($overrides[$canonical])) {
            $resource = $this->normalizeResourceKey($overrides[$canonical]['resource'] ?? '');
            $action = $this->normalizeActionKey($overrides[$canonical]['action'] ?? '');
            $displayName = $overrides[$canonical]['label'] ?? null;

            $displayResource = $this->formatResourceName($resource);
            $displayAction = $this->formatActionName($action);

            return [
                'resource' => $resource,
                'action' => $action,
                'display_resource' => $displayResource,
                'display_action' => $displayAction,
                'display_name' => $displayName ?: $this->formatPermissionLabel($action, $resource),
            ];
        }

        if (str_contains($canonical, '.')) {
            $segments = explode('.', $canonical);
            $resourceRaw = array_shift($segments);
            $actionRaw = implode(' ', $segments);
        } else {
            [$actionRaw, $resourceRaw] = $this->splitActionResource($canonical);
        }

        $resource = $this->normalizeResourceKey($resourceRaw);
        $action = $this->normalizeActionKey($actionRaw);
        if ($action === '') {
            $action = 'manage';
        }

        $displayResource = $this->formatResourceName($resource);
        $displayAction = $this->formatActionName($action);
        $displayName = $this->formatPermissionLabel($action, $resource);

        return [
            'resource' => $resource,
            'action' => $action,
            'display_resource' => $displayResource,
            'display_action' => $displayAction,
            'display_name' => $displayName,
        ];
    }

    private function splitActionResource(string $permission): array
    {
        $value = strtolower(trim($permission));
        if ($value === '') {
            return ['', ''];
        }

        $multiWordActions = [
            'manage organization settings',
            'manage organization subscription',
            'view organization statistics',
            'view statistics',
            'view history',
            'view calendar',
            'view logs',
            'export logs',
            'reset passwords',
            'mark as read',
            'approve level 2',
            'approve level 1',
            'view all',
            'view team',
            'view own',
            'update own',
            'assign to vehicles',
            'manage settings',
            'manage subscription',
            'manage organizations',
            'manage organization',
        ];

        foreach ($multiWordActions as $action) {
            if (str_starts_with($value, $action . ' ')) {
                $resource = trim(substr($value, strlen($action)));
                return [$action, $resource];
            }
            if ($value === $action) {
                return [$action, ''];
            }
        }

        $parts = preg_split('/\s+/', $value, 2);
        $action = $parts[0] ?? $value;
        $resource = $parts[1] ?? '';

        return [$action, $resource];
    }

    private function normalizeResourceKey(string $resource): string
    {
        $resource = trim(strtolower($resource));
        if ($resource === '') {
            return 'misc';
        }

        $resource = str_replace('-', '_', $resource);

        $resourceMap = [
            'organization' => 'organizations',
            'organizations' => 'organizations',
            'organization settings' => 'organizations',
            'organization subscription' => 'organizations',
            'organization statistics' => 'organizations',
            'organizations statistics' => 'organizations',
            'user' => 'users',
            'users' => 'users',
            'role' => 'roles',
            'roles' => 'roles',
            'vehicle' => 'vehicles',
            'vehicles' => 'vehicles',
            'driver' => 'drivers',
            'drivers' => 'drivers',
            'assignment' => 'assignments',
            'assignments' => 'assignments',
            'depot' => 'depots',
            'depots' => 'depots',
            'maintenance operation' => 'maintenance',
            'maintenance operations' => 'maintenance',
            'maintenance' => 'maintenance',
            'repair request' => 'repair_requests',
            'repair requests' => 'repair_requests',
            'mileage reading' => 'mileage_readings',
            'mileage readings' => 'mileage_readings',
            'supplier' => 'suppliers',
            'suppliers' => 'suppliers',
            'expense' => 'expenses',
            'expenses' => 'expenses',
            'document' => 'documents',
            'documents' => 'documents',
            'alert' => 'alerts',
            'alerts' => 'alerts',
            'audit' => 'audit',
            'system' => 'organizations',
        ];

        if (isset($resourceMap[$resource])) {
            return $resourceMap[$resource];
        }

        if (str_contains($resource, 'organization')) {
            return 'organizations';
        }

        return str_replace(' ', '_', $resource);
    }

    private function normalizeActionKey(string $action): string
    {
        $action = trim(strtolower($action));
        $action = str_replace(['_', '-'], ' ', $action);
        $action = preg_replace('/\s+/', ' ', $action);

        return $action;
    }

    private function permissionPresentationOverrides(): array
    {
        return [
            'manage organization settings' => [
                'resource' => 'organizations',
                'action' => 'manage settings',
                'label' => 'Gérer paramètres organisation',
            ],
            'manage organization subscription' => [
                'resource' => 'organizations',
                'action' => 'manage subscription',
                'label' => 'Gérer abonnement organisation',
            ],
            'view organization statistics' => [
                'resource' => 'organizations',
                'action' => 'view statistics',
                'label' => 'Voir statistiques organisation',
            ],
            'manage organizations' => [
                'resource' => 'organizations',
                'action' => 'manage',
                'label' => 'Gérer organisations',
            ],
            'system.manage_organizations' => [
                'resource' => 'organizations',
                'action' => 'manage',
                'label' => 'Administrer organisations (système)',
            ],
        ];
    }

    /**
     * 🔄 CHARGER LES PERMISSIONS DU RÔLE
     */
    public function loadRolePermissions()
    {
        $roleId = $this->normalizeSelectId($this->selectedRoleId);
        $this->selectedRoleId = $roleId;

        if (!$roleId) {
            $this->selectedRole = null;
            $this->rolePermissions = [];
            return;
        }

        $this->selectedRole = Role::with('permissions')->find($roleId);

        if ($this->selectedRole instanceof \Illuminate\Support\Collection) {
            $this->selectedRole = $this->selectedRole->first();
        }

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

            // Synchroniser les permissions (normalisation canonical)
            $permissions = Permission::whereIn('id', $this->rolePermissions)->get();
            $normalized = PermissionAliases::normalize($permissions->pluck('name')->all());
            $normalizedPermissions = $this->resolvePermissionsForRole($this->selectedRole, $normalized);
            $this->selectedRole->syncPermissions($normalizedPermissions);

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

    public function confirmApplyToAllOrganizations(): void
    {
        if (!Auth::user()->hasRole('Super Admin')) {
            return;
        }

        if (!$this->selectedRole) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Veuillez sélectionner un rôle'
            ]);
            return;
        }

        if ($this->selectedRole->name === 'Super Admin') {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'La propagation du rôle Super Admin est interdite.'
            ]);
            return;
        }

        $this->applyAllTargetCount = Organization::count();
        $this->showApplyAllModal = true;
    }

    public function applyPermissionsToAllOrganizations(): void
    {
        if (!Auth::user()->hasRole('Super Admin') || !$this->selectedRole) {
            return;
        }

        if ($this->selectedRole->name === 'Super Admin') {
            return;
        }

        $roleName = $this->selectedRole->name;
        $guard = $this->selectedRole->guard_name;

        $permissions = Permission::whereIn('id', $this->rolePermissions)->get();
        $normalized = PermissionAliases::normalize($permissions->pluck('name')->all());

        DB::transaction(function () use ($roleName, $guard, $normalized) {
            $orgIds = Organization::pluck('id');

            foreach ($orgIds as $orgId) {
                $role = Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => $guard,
                    'organization_id' => $orgId,
                ]);

                $permissionsForRole = $this->resolvePermissionsForRole($role, $normalized);
                $role->syncPermissions($permissionsForRole);
            }
        });

        $this->showApplyAllModal = false;
        $this->dispatch('notification', [
            'type' => 'success',
            'message' => 'Permissions appliquées à toutes les organisations.'
        ]);
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

    public function toggleCompactMode(): void
    {
        $this->compactMode = !$this->compactMode;
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
            'misc' => 'Divers',
        ];

        return $translations[$resource] ?? ucfirst(str_replace('_', ' ', $resource));
    }

    /**
     * 🏷️ NOM SINGULIER D'UNE RESSOURCE
     */
    private function formatResourceLabel(string $resource): string
    {
        $labels = [
            'organizations' => 'organisation',
            'users' => 'utilisateur',
            'roles' => 'rôle',
            'vehicles' => 'véhicule',
            'drivers' => 'chauffeur',
            'assignments' => 'affectation',
            'depots' => 'dépôt',
            'maintenance' => 'maintenance',
            'repair_requests' => 'demande de réparation',
            'mileage_readings' => 'relevé kilométrique',
            'suppliers' => 'fournisseur',
            'expenses' => 'dépense',
            'documents' => 'document',
            'analytics' => 'analytics',
            'alerts' => 'alerte',
            'audit' => 'audit',
        ];

        return $labels[$resource] ?? str_replace('_', ' ', rtrim($resource, 's'));
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
            'update' => 'Modifier',
            'update own' => 'Modifier (Propres)',
            'delete' => 'Supprimer',
            'restore' => 'Restaurer',
            'export' => 'Exporter',
            'import' => 'Importer',
            'manage' => 'Gérer',
            'manage subscription' => 'Gérer abonnement',
            'manage organizations' => 'Gérer organisations',
            'approve' => 'Approuver',
            'approve level 1' => 'Approuver (N1)',
            'approve level 2' => 'Approuver (N2)',
            'reject' => 'Rejeter',
            'assign to vehicles' => 'Assigner aux véhicules',
            'roles.manage' => 'Gérer rôles',
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
            'assignments.view-gantt' => 'Voir Gantt',
            'view statistics' => 'Voir statistiques',
            'analytics.view' => 'Voir analytics',
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
            'manage organization settings' => 'Gérer paramètres organisation',
            'organizations.create' => 'Créer organisation',
            'organizations.edit' => 'Modifier organisation',
            'organizations.delete' => 'Supprimer organisation',
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
     * 🏷️ LIBELLÉ COMPLET D'UNE PERMISSION
     */
    private function formatPermissionLabel(string $action, string $resource): string
    {
        if ($resource === 'organizations') {
            return match ($action) {
                'create' => 'Créer organisation',
                'edit' => 'Modifier organisation',
                'delete' => 'Supprimer organisation',
                'manage settings' => 'Gérer paramètres organisation',
                default => $this->formatActionName($action) . ' organisation',
            };
        }

        return $this->formatActionName($action) . ' ' . $this->formatResourceLabel($resource);
    }

    /**
     * 🔄 WHEN SELECTED ROLE CHANGES
     */
    public function updatedSelectedRoleId($value = null)
    {
        $this->selectedRoleId = $this->normalizeSelectId($value ?? $this->selectedRoleId);
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
        $filterByResource = $this->normalizeSelectValue($this->filterByResource);
        $filterByAction = $this->normalizeSelectValue($this->filterByAction);

        if ($filterByResource !== $this->filterByResource) {
            $this->filterByResource = $filterByResource ?? '';
        }
        if ($filterByAction !== $this->filterByAction) {
            $this->filterByAction = $filterByAction ?? '';
        }

        // Filtre par recherche
        if ($this->search) {
            $filteredMatrix = $filteredMatrix->filter(function ($perm) {
                return str_contains(strtolower($perm['name']), strtolower($this->search)) ||
                       str_contains(strtolower($perm['display_resource']), strtolower($this->search)) ||
                       str_contains(strtolower($perm['display_action']), strtolower($this->search)) ||
                       str_contains(strtolower($perm['display_name']), strtolower($this->search));
            });
        }

        // Filtre par ressource
        if ($filterByResource) {
            $filteredMatrix = $filteredMatrix->where('resource', $filterByResource);
        }

        // Filtre par action
        if ($filterByAction) {
            $filteredMatrix = $filteredMatrix->where('action', $filterByAction);
        }

        // Filtre "uniquement assignées"
        if ($this->showOnlyAssigned) {
            $filteredMatrix = $filteredMatrix->filter(function ($perm) {
                return in_array($perm['id'], $this->rolePermissions);
            });
        }

        // Regrouper par ressource
        $groupedPermissions = $filteredMatrix->groupBy('resource');
        $quickInsights = $this->buildQuickInsights($filteredMatrix);

        return view('livewire.admin.permission-matrix', [
            'groupedPermissions' => $groupedPermissions,
            'hasPendingChanges' => count($this->pendingChanges) > 0,
            'quickInsights' => $quickInsights,
        ]);
    }

    private function buildQuickInsights($filteredMatrix): array
    {
        $totalPermissions = count($this->permissionsMatrix);
        $assignedCount = count($this->rolePermissions);
        $coverage = $totalPermissions > 0
            ? (int) round(($assignedCount / $totalPermissions) * 100)
            : 0;

        $filteredIds = $filteredMatrix->pluck('id')->all();
        $filteredAssigned = count(array_intersect($filteredIds, $this->rolePermissions));

        $riskyKeywords = [
            'delete',
            'force-delete',
            'force delete',
            'impersonate',
            'bypass',
            'approve level 2',
            'manage organizations',
            'manage settings',
            'system.manage',
        ];

        $assignedPermissions = collect($this->permissionsMatrix)
            ->whereIn('id', $this->rolePermissions);

        $riskyCount = $assignedPermissions->filter(function ($permission) use ($riskyKeywords) {
            $haystack = strtolower(($permission['name'] ?? '') . ' ' . ($permission['action'] ?? ''));

            foreach ($riskyKeywords as $keyword) {
                if (str_contains($haystack, $keyword)) {
                    return true;
                }
            }

            return false;
        })->count();

        $canonicalNames = collect($this->permissionsMatrix)
            ->map(fn($permission) => PermissionAliases::canonicalFor($permission['name']) ?? $permission['name']);
        $anomalyCount = $canonicalNames->count() - $canonicalNames->unique()->count();

        return [
            'assigned_count' => $assignedCount,
            'total_permissions' => $totalPermissions,
            'coverage' => $coverage,
            'filtered_count' => $filteredMatrix->count(),
            'filtered_assigned' => $filteredAssigned,
            'risky_count' => $riskyCount,
            'anomaly_count' => max(0, $anomalyCount),
        ];
    }

    private function normalizeSelectValue($value): ?string
    {
        if (is_array($value)) {
            if ($value === []) {
                return null;
            }

            $value = array_key_exists('value', $value)
                ? $value['value']
                : ($value[0] ?? null);
        }

        if (is_object($value)) {
            if (isset($value->value)) {
                $value = $value->value;
            } elseif (method_exists($value, '__toString')) {
                $value = (string) $value;
            } else {
                return null;
            }
        }

        if (is_string($value)) {
            $value = trim($value);
            return $value === '' ? null : $value;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        return null;
    }

    private function normalizeSelectId($value): ?int
    {
        $normalized = $this->normalizeSelectValue($value);
        if ($normalized === null || !is_numeric($normalized)) {
            return null;
        }

        return (int) $normalized;
    }
}
