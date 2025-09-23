<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\SupplierCategoryController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\DocumentCategoryController;
// use App\Http\Controllers\Admin\MaintenanceDashboardController;
// use App\Http\Controllers\Admin\MaintenancePlanController; // Temporairement désactivé
use App\Http\Controllers\Admin\MaintenanceLogController;
// use App\Http\Controllers\Admin\VehicleHandoverController; // Temporairement désactivé
use App\Http\Controllers\Admin\PlanningController;

/*
|--------------------------------------------------------------------------
| 🚀 ZENFLEET ROUTES - ARCHITECTURE ULTRA-PROFESSIONNELLE CORRIGÉE
| Version 4.1 - Expert System Architecture - Routes Conflict Fixed
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| 🏠 ROUTE RACINE - Redirection Intelligente Multi-Rôles
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        // ✅ ARCHITECTURE EXPERTE: Redirection selon hiérarchie des rôles
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Gestionnaire Flotte'])) {
            return redirect()->route('admin.dashboard');
        }
        
        // Superviseurs et Chauffeurs vers dashboard standard
        return redirect()->route('dashboard');
    }
    
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| 🔒 DASHBOARD UTILISATEUR STANDARD - SUPERVISEURS ET CHAUFFEURS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // ✅ CORRECTION CRITIQUE: Dashboard accessible à tous les rôles authentifiés
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| 🏢 ZONE ADMINISTRATION - RBAC ULTRA-SÉCURISÉ
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 📊 DASHBOARD ADMINISTRATIF PRINCIPAL
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('role:Super Admin|Admin|Gestionnaire Flotte|Supervisor');

    /*
    |--------------------------------------------------------------------------
    | 👑 NIVEAU SUPER ADMIN - GESTION SYSTÈME GLOBALE
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:Super Admin')->group(function () {
        
        // 🏢 ✅ ORGANISATIONS - INTERFACE ULTRA-PROFESSIONNELLE INTÉGRÉE
        Route::prefix('organizations')->name('organizations.')->group(function () {
            // Route principale avec données
            Route::get('/', [OrganizationController::class, 'index'])->name('index');
            
            // Routes CRUD traditionnelles pour compatibilité
            Route::get('/create', [OrganizationController::class, 'create'])->name('create');
            Route::post('/', [OrganizationController::class, 'store'])->name('store');
            Route::get('/{organization}', [OrganizationController::class, 'show'])->name('show');
            Route::get('/{organization}/edit', [OrganizationController::class, 'edit'])->name('edit');
            Route::put('/{organization}', [OrganizationController::class, 'update'])->name('update');
            Route::delete('/{organization}', [OrganizationController::class, 'destroy'])->name('destroy');
            
            // Actions avancées
            Route::get('/export', [OrganizationController::class, 'export'])->name('export');
            Route::patch('/{organization}/restore', [OrganizationController::class, 'restore'])
                ->name('restore')->withTrashed();
            Route::get('/{organization}/audit', [OrganizationController::class, 'auditTrail'])
                ->name('audit');
            
            // ✅ NOUVELLES ROUTES POUR FONCTIONNALITÉS AVANCÉES
            Route::post('/{organization}/toggle-status', [OrganizationController::class, 'toggleStatus'])
                ->name('toggle-status');
            Route::get('/statistics/summary', [OrganizationController::class, 'getStatisticsSummary'])
                ->name('statistics.summary');
            Route::post('/bulk-actions', [OrganizationController::class, 'bulkActions'])
                ->name('bulk-actions');
        });

        // 📊 Monitoring et Analytics Système
        Route::prefix('system')->name('system.')->group(function () {
            Route::get('metrics', [DashboardController::class, 'systemMetrics'])->name('metrics');
            Route::get('logs', [DashboardController::class, 'systemLogs'])->name('logs');
            Route::get('health', [DashboardController::class, 'systemHealth'])->name('health');
            Route::get('performance', [DashboardController::class, 'systemPerformance'])->name('performance');
            Route::get('analytics', [DashboardController::class, 'systemAnalytics'])->name('analytics');
        });

        // 🔐 Audit et Sécurité Avancés
        Route::prefix('audit')->name('audit.')->group(function () {
            Route::get('/', [DashboardController::class, 'auditLogs'])->name('index');
            Route::get('security', [DashboardController::class, 'securityAudit'])->name('security');
            Route::get('users', [DashboardController::class, 'userAudit'])->name('users');
            Route::get('organizations', [DashboardController::class, 'organizationAudit'])->name('organizations');
            Route::get('export', [DashboardController::class, 'exportAudit'])->name('export');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | 👥 GESTION UTILISATEURS - SYSTÈME DE PERMISSIONS ENTERPRISE
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'verified', 'enterprise.permission'])->group(function () {

        // Utilisateurs avec actions avancées
        Route::resource('users', UserController::class);
        Route::prefix('users')->name('users.')->group(function () {
            Route::post('{user}/assign-role', [UserController::class, 'assignRole'])->name('assign-role');
            Route::post('{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
            Route::get('{user}/impersonate', [UserController::class, 'impersonate'])->name('impersonate');
            Route::get('export', [UserController::class, 'export'])->name('export');
        });

        // Rôles et Permissions
        Route::resource('roles', RoleController::class)->only(['index', 'show', 'edit', 'update']);
        Route::get('permissions', [RoleController::class, 'permissions'])->name('permissions.index');
    });

    /*
    |--------------------------------------------------------------------------
    | 🚗 GESTION OPÉRATIONNELLE - FLOTTE COMPLÈTE ENTERPRISE
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'verified', 'enterprise.permission'])->group(function () {
        
        // 🚙 Véhicules avec Import/Export Avancé - Configuration Enterprise
        Route::prefix('vehicles')->name('vehicles.')->group(function () {
            // CORRECTION MAJEURE: Routes spécifiques AVANT les routes avec paramètres
            // Import/Export Enterprise
            Route::get('import', [VehicleController::class, 'showImportForm'])->name('import.show');
            Route::post('import', [VehicleController::class, 'handleImport'])->name('import.handle');
            Route::post('import/validate', [VehicleController::class, 'preValidateImportFile'])->name('import.validate');
            Route::get('import-template', [VehicleController::class, 'downloadTemplate'])->name('import.template');
            Route::get('import/results', [VehicleController::class, 'showImportResults'])->name('import.results');
            Route::get('export', [VehicleController::class, 'export'])->name('export');

            // Gestion des archives
            Route::get('archived', [VehicleController::class, 'archived'])->name('archived');

            // Route de création
            Route::get('create', [VehicleController::class, 'create'])->name('create');

            // Routes CRUD principales
            Route::get('/', [VehicleController::class, 'index'])->name('index');
            Route::post('/', [VehicleController::class, 'store'])->name('store');

            // Routes avec paramètres {vehicle} - TOUJOURS EN DERNIER
            Route::get('{vehicle}', [VehicleController::class, 'show'])->name('show');
            Route::get('{vehicle}/edit', [VehicleController::class, 'edit'])->name('edit');
            Route::put('{vehicle}', [VehicleController::class, 'update'])->name('update');
            Route::patch('{vehicle}', [VehicleController::class, 'update'])->name('update');
            Route::delete('{vehicle}', [VehicleController::class, 'destroy'])->name('destroy');

            // Actions spécifiques avec paramètres
            Route::patch('{vehicle}/restore', [VehicleController::class, 'restore'])->name('restore')->withTrashed();
            Route::delete('{vehicle}/force-delete', [VehicleController::class, 'forceDelete'])->name('force-delete')->withTrashed();
            Route::get('{vehicle}/history', [VehicleController::class, 'history'])->name('history');
            Route::get('{vehicle}/maintenance', [VehicleController::class, 'maintenance'])->name('maintenance');
            Route::get('{vehicle}/documents', [VehicleController::class, 'documents'])->name('documents');
        });

        // 👨‍💼 Chauffeurs avec Import/Export
        Route::prefix('drivers')->name('drivers.')->group(function () {
            // CORRECTION MAJEURE: Routes spécifiques AVANT les routes avec paramètres
            Route::get('statistics', [DriverController::class, 'statistics'])->name('statistics');
            Route::get('import', [DriverController::class, 'showImportForm'])->name('import.show');
            Route::post('import', [DriverController::class, 'handleImport'])->name('import.handle');
            Route::get('import-template', [DriverController::class, 'downloadTemplate'])->name('import.template');
            Route::get('import/results', [DriverController::class, 'showImportResults'])->name('import.results');
            Route::get('export', [DriverController::class, 'export'])->name('export');
            Route::get('archived', [DriverController::class, 'archived'])->name('archived');
            Route::get('create', [DriverController::class, 'create'])->name('create');

            // Routes CRUD principales
            Route::get('/', [DriverController::class, 'index'])->name('index');
            Route::post('/', [DriverController::class, 'store'])->name('store');

            // Routes avec paramètres {driver} - TOUJOURS EN DERNIER
            Route::get('{driver}', [DriverController::class, 'show'])->name('show');
            Route::get('{driver}/edit', [DriverController::class, 'edit'])->name('edit');
            Route::put('{driver}', [DriverController::class, 'update'])->name('update');
            Route::delete('{driver}', [DriverController::class, 'destroy'])->name('destroy');
            Route::patch('{driver}/restore', [DriverController::class, 'restore'])->name('restore')->withTrashed();
            Route::delete('{driver}/force-delete', [DriverController::class, 'forceDelete'])->name('force-delete')->withTrashed();

            // Routes futures (à implémenter)
            // Route::get('{driver}/history', [DriverController::class, 'history'])->name('history');
            // Route::get('{driver}/performance', [DriverController::class, 'performance'])->name('performance');
        });

        // 🔄 Affectations Avancées
        Route::resource('assignments', AssignmentController::class);
        Route::prefix('assignments')->name('assignments.')->group(function () {
            Route::patch('{assignment}/end', [AssignmentController::class, 'end'])->name('end');
            Route::get('{assignment}/details', [AssignmentController::class, 'details'])->name('details');
            Route::post('{assignment}/extend', [AssignmentController::class, 'extend'])->name('extend');
            Route::get('calendar', [AssignmentController::class, 'calendar'])->name('calendar');
            Route::get('export', [AssignmentController::class, 'export'])->name('export');
        });

        // 🏪 Fournisseurs et Catégories
        Route::resource('suppliers', SupplierController::class);
        Route::get('suppliers/export', [SupplierController::class, 'export'])->name('suppliers.export');
        Route::resource('supplier-categories', SupplierCategoryController::class);

        // 📄 Documents et Catégories
        Route::resource('documents', DocumentController::class);
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('search', [DocumentController::class, 'search'])->name('search');
            Route::post('{document}/download', [DocumentController::class, 'download'])->name('download');
            Route::get('expired', [DocumentController::class, 'expired'])->name('expired');
            Route::get('expiring-soon', [DocumentController::class, 'expiringSoon'])->name('expiring-soon');
        });
        Route::resource('document-categories', DocumentCategoryController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | 🔧 MAINTENANCE - SUPERVISEURS INCLUS
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:Super Admin|Admin|Gestionnaire Flotte|Supervisor')->group(function () {
        
        // Dashboard Maintenance - ACTIVATION ENTERPRISE
        Route::prefix('maintenance')->name('maintenance.')->group(function () {
            Route::get('/', [DashboardController::class, 'maintenanceDashboard'])->name('dashboard');
            Route::get('calendar', [DashboardController::class, 'maintenanceCalendar'])->name('calendar');
            Route::get('alerts', [DashboardController::class, 'maintenanceAlerts'])->name('alerts');
            Route::get('analytics', [DashboardController::class, 'maintenanceAnalytics'])->name('analytics');
        });

        // Plans et Logs de Maintenance - Temporairement simplifiés
        // Route::resource('maintenance/plans', MaintenancePlanController::class)->names('maintenance.plans');
        // Route::post('maintenance/plans/{plan}/duplicate', [MaintenancePlanController::class, 'duplicate'])
        //     ->name('maintenance.plans.duplicate');

        // Logs de maintenance fonctionnels
        Route::prefix('maintenance/logs')->name('maintenance.logs.')->group(function () {
            Route::get('/', [DashboardController::class, 'maintenanceLogs'])->name('index');
            Route::get('export', [DashboardController::class, 'exportMaintenanceLogs'])->name('export');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | 📋 FICHES DE REMISE - HANDOVERS ✅ SECTION CORRIGÉE
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:Super Admin|Admin|Gestionnaire Flotte|Supervisor')->group(function () {
        
        // Handovers - Temporairement désactivés en attendant le contrôleur
        // Route::prefix('handovers')->name('handovers.vehicles.')->group(function () {
        //     Route::get('/', [VehicleHandoverController::class, 'index'])->name('index');
        //     Route::get('create', [VehicleHandoverController::class, 'create'])->name('create');
        //     Route::post('/', [VehicleHandoverController::class, 'store'])->name('store');
        //     Route::get('{handover}', [VehicleHandoverController::class, 'show'])->name('show');
        //     Route::get('{handover}/edit', [VehicleHandoverController::class, 'edit'])->name('edit');
        //     Route::put('{handover}', [VehicleHandoverController::class, 'update'])->name('update');
        //     Route::delete('{handover}', [VehicleHandoverController::class, 'destroy'])->name('destroy');
        // });

        // Placeholder pour handovers
        Route::get('handovers', [\App\Http\Controllers\Admin\PlaceholderController::class, 'index'])
            ->name('handovers.vehicles.index');
    });

    /*
    |--------------------------------------------------------------------------
    | 📅 PLANNING ET OPTIMISATION
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:Super Admin|Admin|Gestionnaire Flotte|Supervisor')->group(function () {
        
        Route::prefix('planning')->name('planning.')->group(function () {
            Route::get('/', [PlanningController::class, 'index'])->name('index');
            Route::get('gantt', [PlanningController::class, 'gantt'])->name('gantt');
            Route::get('calendar', [PlanningController::class, 'calendar'])->name('calendar');
            Route::post('optimize', [PlanningController::class, 'optimize'])->name('optimize');
            Route::get('export', [PlanningController::class, 'export'])->name('export');
            Route::get('conflicts', [PlanningController::class, 'conflicts'])->name('conflicts');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | 📊 RAPPORTS ET ANALYTICS AVANCÉS
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:Super Admin|Admin|Gestionnaire Flotte')->group(function () {
        
        // Rapports Détaillés
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [DashboardController::class, 'reports'])->name('index');
            Route::get('vehicles', [DashboardController::class, 'vehicleReports'])->name('vehicles');
            Route::get('drivers', [DashboardController::class, 'driverReports'])->name('drivers');
            Route::get('maintenance', [DashboardController::class, 'maintenanceReports'])->name('maintenance');
            Route::get('costs', [DashboardController::class, 'costReports'])->name('costs');
            Route::get('utilization', [DashboardController::class, 'utilizationReports'])->name('utilization');
            Route::post('generate', [DashboardController::class, 'generateReport'])->name('generate');
            Route::get('export/{type}', [DashboardController::class, 'exportReport'])->name('export');
        });

        // Analytics Business Intelligence
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [DashboardController::class, 'analytics'])->name('index');
            Route::get('performance', [DashboardController::class, 'performanceAnalytics'])->name('performance');
            Route::get('usage', [DashboardController::class, 'usageAnalytics'])->name('usage');
            Route::get('predictive', [DashboardController::class, 'predictiveAnalytics'])->name('predictive');
            Route::get('roi', [DashboardController::class, 'roiAnalytics'])->name('roi');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | ⚙️ PARAMÈTRES ET CONFIGURATION
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:Super Admin|Admin')->group(function () {
        
        // Paramètres Système
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [DashboardController::class, 'settings'])->name('index');
            Route::post('/', [DashboardController::class, 'updateSettings'])->name('update');
            Route::get('security', [DashboardController::class, 'securitySettings'])->name('security');
            Route::get('notifications', [DashboardController::class, 'notificationSettings'])->name('notifications');
            Route::get('integrations', [DashboardController::class, 'integrationSettings'])->name('integrations');
            Route::get('backup', [DashboardController::class, 'backupSettings'])->name('backup');
        });

        // Gestion API
        Route::prefix('api-keys')->name('api.keys.')->group(function () {
            Route::get('/', [DashboardController::class, 'apiKeys'])->name('index');
            Route::post('/', [DashboardController::class, 'createApiKey'])->name('create');
            Route::delete('{key}', [DashboardController::class, 'deleteApiKey'])->name('delete');
            Route::patch('{key}/regenerate', [DashboardController::class, 'regenerateApiKey'])->name('regenerate');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | 🛡️ SÉCURITÉ ET SESSIONS
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:Super Admin|Admin')->group(function () {
        
        Route::prefix('security')->name('security.')->group(function () {
            Route::get('sessions', [DashboardController::class, 'activeSessions'])->name('sessions');
            Route::get('login-attempts', [DashboardController::class, 'loginAttempts'])->name('login-attempts');
            Route::post('revoke-session/{session}', [DashboardController::class, 'revokeSession'])->name('revoke-session');
            Route::get('two-factor', [DashboardController::class, 'twoFactorSettings'])->name('two-factor');
        });
    });
});

/*
|--------------------------------------------------------------------------
| 🔐 ROUTES D'AUTHENTIFICATION SÉCURISÉES
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| 🚧 ROUTES PLACEHOLDER POUR MODULES EN DÉVELOPPEMENT
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'enterprise.permission'])->prefix('admin')->name('admin.')->group(function () {
    // Modules temporaires en développement - ATTENTION: Vérifier que ces routes ne créent pas de conflits
    // CORRECTION: Suppression des routes qui écrasent les vraies routes des modules fonctionnels
    // Route::get('assignments', [\App\Http\Controllers\Admin\PlaceholderController::class, 'index'])->name('assignments.index');
    // Route::get('drivers', [\App\Http\Controllers\Admin\PlaceholderController::class, 'index'])->name('drivers.index');

    // Modules temporaires - Redirection vers Dashboard avec info
    Route::get('maintenance-temp', [\App\Http\Controllers\Admin\PlaceholderController::class, 'index'])->name('maintenance-temp.index');
});

/*
|--------------------------------------------------------------------------
| 🛠️ ROUTES DE DÉVELOPPEMENT (Uniquement en DEV)
|--------------------------------------------------------------------------
*/
if (app()->environment('local', 'development')) {
    Route::prefix('dev')->name('dev.')->group(function () {
        Route::get('test-dashboard', function () {
            return view('admin.test');
        })->name('test-dashboard');
        
        Route::get('phpinfo', function () {
            return phpinfo();
        })->name('phpinfo');
    });
}
