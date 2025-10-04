<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\User;
use App\Models\Organization;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Supplier;
use App\Models\Assignment;
use App\Models\RepairRequest;
use App\Policies\DocumentPolicy;
use App\Policies\DocumentCategoryPolicy;
use App\Policies\UserPolicy;
use App\Policies\RolePolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\VehiclePolicy;
use App\Policies\DriverPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\AssignmentPolicy;
use App\Policies\RepairRequestPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * 🛡️ MAPPING DES POLICIES DE SÉCURITÉ ENTERPRISE
     * Chaque modèle sensible a sa propre policy pour un contrôle granulaire
     */
    protected $policies = [
        // Policies système
        Document::class => DocumentPolicy::class,
        DocumentCategory::class => DocumentCategoryPolicy::class,
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Organization::class => OrganizationPolicy::class,

        // 🛡️ POLICIES GESTION DE FLOTTE (Enterprise-Grade)
        Vehicle::class => VehiclePolicy::class,
        Driver::class => DriverPolicy::class,
        Supplier::class => SupplierPolicy::class,
        Assignment::class => AssignmentPolicy::class,

        // 🔧 POLICIES SYSTÈME DE RÉPARATION
        RepairRequest::class => RepairRequestPolicy::class,
    ];

    /**
     * 🔐 ENREGISTREMENT DES SERVICES D'AUTORISATION
     */
    public function boot(): void
    {
        $this->registerPolicies();
        
        // 🛡️ GATE GLOBAL SUPER ADMIN
        // Le Super Admin bypasse toutes les vérifications (sauf celles explicitement bloquées)
        Gate::before(function (User $user, string $ability) {
            // Super Admin a accès à tout SAUF l'auto-promotion
            if ($user->hasRole('Super Admin')) {
                // Bloquer explicitement les actions dangereuses même pour Super Admin
                $blockedAbilities = [
                    'promote-self-to-super-admin',
                    'delete-last-super-admin',
                ];
                
                if (in_array($ability, $blockedAbilities)) {
                    return false;
                }
                
                return true;
            }
            
            return null; // Continuer avec les policies normales
        });

        // 🛡️ GATES PERSONNALISÉS DE SÉCURITÉ
        
        // Gate pour empêcher l'escalation de privilèges
        Gate::define('assign-role-to-user', function (User $user, Role $role, User $targetUser = null) {
            // Seul Super Admin peut assigner le rôle Super Admin
            if ($role->name === 'Super Admin') {
                return $user->hasRole('Super Admin');
            }
            
            // Empêcher l'auto-promotion
            if ($targetUser && $user->id === $targetUser->id && $role->name === 'Super Admin') {
                return false;
            }
            
            // Admin peut assigner tous les autres rôles
            return $user->hasRole(['Super Admin', 'Admin']);
        });
        
        // Gate pour la gestion des organisations
        Gate::define('manage-organizations', function (User $user) {
            return $user->hasRole('Super Admin');
        });
        
        // Gate pour la création d'admins
        Gate::define('create-admin-user', function (User $user) {
            return $user->hasRole('Super Admin');
        });
        
        // Gate pour l'accès multi-organisation
        Gate::define('access-all-organizations', function (User $user) {
            return $user->hasRole('Super Admin');
        });
        
        // 🔍 GATE POUR L'AUDIT ET SÉCURITÉ
        Gate::define('view-security-logs', function (User $user) {
            return $user->hasRole('Super Admin');
        });
        
        Gate::define('view-user-activity', function (User $user, User $targetUser = null) {
            if ($user->hasRole('Super Admin')) {
                return true;
            }
            
            if ($user->hasRole('Admin')) {
                // Admin peut voir les utilisateurs de son organisation
                return $targetUser ? $user->organization_id === $targetUser->organization_id : true;
            }
            
            return false;
        });
    }
}
