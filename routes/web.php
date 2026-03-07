<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\DriverSanctionController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\SupplierCategoryController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\DocumentCategoryController;
use App\Http\Controllers\Admin\VehicleDepotController;
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

    /*
    |--------------------------------------------------------------------------
    | 🚗 ESPACE CHAUFFEUR - Routes Simplifiées
    |--------------------------------------------------------------------------
    | Les chauffeurs ont accès à leurs propres demandes de réparation
    | via le même component admin mais avec scope automatique
    */
        Route::prefix('driver')->name('driver.')->group(function () {
        // Dashboard chauffeur (redirige vers /dashboard)
        Route::redirect('dashboard', '/dashboard')->name('dashboard');

        // Demandes de réparation - ENTERPRISE: Contrôleur avec layout approprié
        Route::get('/repair-requests', [\App\Http\Controllers\Driver\RepairRequestController::class, 'index'])
            ->name('repair-requests.index');

            // Mise à jour du kilométrage - Chauffeur peut mettre à jour son véhicule
            Route::get('/mileage/update', [\App\Http\Controllers\Admin\MileageReadingController::class, 'update'])
                ->name('mileage.update')
                ->middleware('mileage.access');
        });
});

/*
|--------------------------------------------------------------------------
| 🏢 ZONE ADMINISTRATION - RBAC ULTRA-SÉCURISÉ
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'audit.log'])
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
            ->middleware('role:Super Admin|Admin|Gestionnaire Flotte|Supervisor|Superviseur');

        /*
    |--------------------------------------------------------------------------
    | 🎨 DESIGN SYSTEM - COMPOSANTS DEMO
    |--------------------------------------------------------------------------
    */
        Route::get('/components-demo', function () {
            return view('admin.components-demo');
        })->name('components.demo')->middleware('role:Super Admin|Admin');

        /*
    |--------------------------------------------------------------------------
    | 👑 NIVEAU SUPER ADMIN - GESTION SYSTÈME GLOBALE
    |--------------------------------------------------------------------------
    */
        Route::middleware('role:Super Admin')->group(function () {

            // 🏢 ✅ ORGANISATIONS - INTERFACE ULTRA-PROFESSIONNELLE INTÉGRÉE
            Route::prefix('organizations')->name('organizations.')->group(function () {
                // Route principale (Livewire standard)
                Route::get('/', \App\Livewire\Admin\OrganizationTable::class)->name('index');

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
                Route::get('{user}/permissions', fn($user) => view('admin.users.permissions', ['userId' => $user]))->name('permissions');
                Route::post('{user}/assign-role', [UserController::class, 'assignRole'])->name('assign-role');
                Route::post('{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
                Route::post('{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
                Route::get('{user}/impersonate', [UserController::class, 'impersonate'])->name('impersonate');
                Route::get('export', [UserController::class, 'export'])->name('export');
                Route::delete('{user}/force-delete', [UserController::class, 'forceDelete'])->name('force-delete');

                // 🔒 GESTION DES ACCÈS VÉHICULES
                Route::get('{user}/vehicles', \App\Livewire\Admin\Users\VehicleAccessManager::class)
                    ->name('vehicles.manage');
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

            // 📊 Relevés Kilométriques - Module Complet Enterprise-Grade (Livewire 3 Pattern)
            Route::prefix('mileage-readings')->name('mileage-readings.')->middleware('mileage.access')->group(function () {
                // Vue globale des relevés - Accès géré par middleware enterprise-grade
                Route::get('/', [\App\Http\Controllers\Admin\MileageReadingController::class, 'index'])
                    ->name('index');

                // Export CSV avec filtres avancés - Enterprise
                Route::get('/export', [\App\Http\Controllers\Admin\MileageReadingController::class, 'export'])
                    ->name('export')
                    ->middleware('can:mileage-readings.export');

                // NOUVEAUX EXPORTS ENTERPRISE-GRADE
                Route::get('/export/csv', [\App\Http\Controllers\Admin\MileageReadingController::class, 'exportCsv'])
                    ->name('export.csv')
                    ->middleware('can:mileage-readings.export');

                Route::get('/export/pdf', [\App\Http\Controllers\Admin\MileageReadingController::class, 'exportPdf'])
                    ->name('export.pdf')
                    ->middleware('can:mileage-readings.export');

                // Mise à jour du kilométrage (tous les rôles selon permissions)
                Route::get('/update/{vehicle?}', [\App\Http\Controllers\Admin\MileageReadingController::class, 'update'])
                    ->name('update');
            });

            // 🚙 Véhicules avec Import/Export Avancé - Configuration Enterprise
            Route::prefix('vehicles')->name('vehicles.')->group(function () {
                // CORRECTION MAJEURE: Routes spécifiques AVANT les routes avec paramètres
                // Import/Export Enterprise
                Route::get('import', [VehicleController::class, 'showImportForm'])->name('import.show');
                Route::post('import', [VehicleController::class, 'handleImport'])->name('import.handle');
                Route::post('import/validate', [VehicleController::class, 'preValidateImportFile'])->name('import.validate');
                Route::get('import-template', [VehicleController::class, 'downloadTemplate'])->name('import.template');
                Route::get('import/results', [VehicleController::class, 'showImportResults'])->name('import.results');
                Route::get('import/report', [VehicleController::class, 'downloadImportReport'])->name('import.report');
                Route::get('export', [VehicleController::class, 'export'])->name('export');

                // START: Tâche 1 - Routes d'Export Multiformats
                Route::get('export/csv', [VehicleController::class, 'exportCsv'])->name('export.csv');
                Route::get('export/excel', [VehicleController::class, 'exportExcel'])->name('export.excel');
                Route::get('export/pdf', [VehicleController::class, 'exportPdf'])->name('export.pdf');
                // END: Tâche 1

                // Actions en masse (Batch Operations) - Enterprise-Grade
                Route::post('batch-archive', [VehicleController::class, 'batchArchive'])->name('batch.archive');
                Route::post('batch-status', [VehicleController::class, 'batchStatus'])->name('batch.status');

                // Gestion des archives
                Route::get('archived', [VehicleController::class, 'archived'])->name('archived');

                // Route de création
                Route::get('create', [VehicleController::class, 'create'])->name('create');

                // Routes CRUD principales
                Route::get('/', \App\Livewire\Admin\Vehicles\VehicleIndex::class)->name('index');
                Route::post('/', [VehicleController::class, 'store'])->name('store');

                // Routes avec paramètres {vehicle} - TOUJOURS EN DERNIER
                Route::get('{vehicle}', [VehicleController::class, 'show'])->name('show');
                Route::get('{vehicle}/edit', [VehicleController::class, 'edit'])->name('edit');
                Route::match(['put', 'patch'], '{vehicle}', [VehicleController::class, 'update'])->name('update');
                Route::delete('{vehicle}', [VehicleController::class, 'destroy'])->name('destroy');

                // Actions spécifiques avec paramètres
                Route::put('{vehicle}/archive', [VehicleController::class, 'archive'])->name('archive');
                Route::put('{vehicle}/unarchive', [VehicleController::class, 'unarchive'])->name('unarchive');
                Route::patch('{vehicle}/restore', [VehicleController::class, 'restore'])->name('restore.soft')->withTrashed();
                Route::delete('{vehicle}/force-delete', [VehicleController::class, 'forceDelete'])->name('force-delete')->withTrashed();
                Route::get('{vehicle}/history', [VehicleController::class, 'history'])->name('history');
                Route::get('{vehicle}/maintenance', [VehicleController::class, 'maintenance'])->name('maintenance');
                Route::get('{vehicle}/documents', [VehicleController::class, 'documents'])->name('documents');

                // START: Tâche 2 - Routes pour Export PDF individuel et Duplication
                Route::get('{vehicle}/export/pdf', [VehicleController::class, 'exportSinglePdf'])->name('export.single.pdf');
                Route::post('{vehicle}/duplicate', [VehicleController::class, 'duplicate'])->name('duplicate');
                // END: Tâche 2

                // Historique kilométrique - Livewire Component via Controller
                Route::get('{vehicle}/mileage-history', [\App\Http\Controllers\Admin\MileageReadingController::class, 'history'])->name('mileage-history');
            });

            /*
        |--------------------------------------------------------------------------
        | 🏢 DÉPÔTS - GESTION ENTERPRISE-GRADE
        |--------------------------------------------------------------------------
        | Système complet de gestion des dépôts avec:
        | - CRUD complet avec Livewire
        | - Affectation véhicules avec capacité
        | - Historique des affectations
        | - Statistiques et reporting
        */
            Route::prefix('depots')->name('depots.')->group(function () {
                // Page principale de gestion (Livewire Component)
                Route::get('/', function () {
                    return view('admin.depots.index');
                })->name('index');

                // Fiche détaillée dépôt (format document professionnel)
                Route::get('/{id}', [VehicleDepotController::class, 'show'])->name('show');

                // Export PDF
                Route::get('/{id}/pdf', [VehicleDepotController::class, 'exportPdf'])->name('export.pdf');

                // Suppression (soft delete)
                Route::delete('/{id}', [VehicleDepotController::class, 'destroy'])->name('destroy');

                // Restauration
                Route::post('/{id}/restore', [VehicleDepotController::class, 'restore'])->name('restore');
            });

            // 👨‍💼 Chauffeurs avec Import/Export
            Route::prefix('drivers')->name('drivers.')->group(function () {
                // CORRECTION MAJEURE: Routes spécifiques AVANT les routes avec paramètres
                Route::get('statistics', [DriverController::class, 'statistics'])->name('statistics');

                // ✨ NOUVEAU: Import avec Livewire (World-Class Enterprise)
                Route::get('import', function () {
                    return view('admin.drivers.import-livewire');
                })->name('import.show');

                // ✨ NOUVEAU: Sanctions avec Livewire (World-Class Enterprise)
                Route::get('sanctions', function () {
                    return view('admin.drivers.sanctions-livewire');
                })->name('sanctions.index');

                // ✨ NOUVEAU: Détail d'une sanction (Enterprise-Grade)
                Route::get('sanctions/{sanction}', [DriverSanctionController::class, 'show'])
                    ->name('sanctions.show');

                Route::get('export', [DriverController::class, 'export'])->name('export');

                // 🔥 EXPORT MULTIFORMATS ENTERPRISE-GRADE (PDF, CSV, Excel)
                Route::get('export/csv', [DriverController::class, 'exportCsv'])->name('export.csv');
                Route::get('export/excel', [DriverController::class, 'exportExcel'])->name('export.excel');
                Route::get('export/pdf', [DriverController::class, 'exportPdf'])->name('export.pdf');

                Route::get('archived', [DriverController::class, 'archived'])->name('archived');
                Route::get('archived/export', [DriverController::class, 'exportArchived'])->name('archived.export');
                Route::post('archived/bulk-restore', [DriverController::class, 'bulkRestore'])->name('archived.bulk-restore');
                Route::get('create', [DriverController::class, 'create'])->name('create');

                // Routes CRUD principales
                Route::get('/', \App\Livewire\Admin\Drivers\DriverIndex::class)->name('index');
                Route::post('/', [DriverController::class, 'store'])->name('store');

                // Routes avec paramètres {driver} - TOUJOURS EN DERNIER
                Route::get('{driver}/export-profile-pdf', [DriverController::class, 'exportProfilePdf'])->name('export.profile.pdf');
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

            // 🔄 Affectations Enterprise-Grade
            // IMPORTANT: Routes spécifiques AVANT Route::resource pour éviter conflits
            Route::prefix('assignments')->name('assignments.')->group(function () {
                // 🚀 WIZARD: Page unique ultra-pro (AVANT resource pour priorité routing)
                // 🚀 WIZARD EST MAINTENANT LE SYSTÈME PAR DÉFAUT (remplace l'ancien create)
                // 🔒 SÉCURITÉ ENTERPRISE: Utilise le contrôleur pour respecter le pattern MVC
                Route::get('create', [AssignmentController::class, 'create'])->name('create');

                // Routes utilitaires avancées Enterprise
                Route::get('calendar', [AssignmentController::class, 'calendar'])->name('calendar');
                Route::get('gantt', [AssignmentController::class, 'gantt'])->name('gantt');
                Route::get('export', [AssignmentController::class, 'export'])->name('export');
                Route::get('stats', [AssignmentController::class, 'stats'])->name('stats');

                // 🏥 HEALTH CHECK ENTERPRISE-GRADE - Monitoring et Auto-Healing
                // IMPORTANT: Ces routes DOIVENT être AVANT {assignment} pour éviter conflits de routing
                // Dashboard UI
                Route::get('health-dashboard', function () {
                    return view('admin.assignments.health-dashboard');
                })->name('health-dashboard');

                // API Endpoints
                Route::get('health', [\App\Http\Controllers\Admin\AssignmentHealthCheckController::class, 'health'])
                    ->name('health');
                Route::get('zombies', [\App\Http\Controllers\Admin\AssignmentHealthCheckController::class, 'zombies'])
                    ->name('zombies');
                Route::get('metrics', [\App\Http\Controllers\Admin\AssignmentHealthCheckController::class, 'metrics'])
                    ->name('metrics');
                Route::post('heal', [\App\Http\Controllers\Admin\AssignmentHealthCheckController::class, 'heal'])
                    ->name('heal');

                // Routes CRUD (index, store, show, edit, update, destroy)
                Route::get('/', \App\Livewire\Admin\Assignments\AssignmentIndex::class)->name('index');
                Route::post('/', [AssignmentController::class, 'store'])->name('store');
                Route::get('{assignment}', [AssignmentController::class, 'show'])->name('show');
                Route::get('{assignment}/edit', [AssignmentController::class, 'edit'])->name('edit');
                Route::put('{assignment}', [AssignmentController::class, 'update'])->name('update');
                Route::delete('{assignment}', [AssignmentController::class, 'destroy'])->name('destroy');
            });

            // Routes avec paramètres (APRÈS resource)
            Route::prefix('assignments')->name('assignments.')->group(function () {
                Route::patch('{assignment}/end', [AssignmentController::class, 'end'])->name('end');
                Route::get('{assignment}/details', [AssignmentController::class, 'details'])->name('details');
                Route::post('{assignment}/extend', [AssignmentController::class, 'extend'])->name('extend');

                // 📄 Export PDF Enterprise-Grade - Micro-service PDF
                Route::get('{assignment}/export/pdf', [AssignmentController::class, 'exportPdf'])->name('export.pdf');
            });

            // 🚗 API pour les ressources disponibles (via AssignmentController)
            Route::get('vehicles/available', [AssignmentController::class, 'availableVehicles'])->name('vehicles.available');
            Route::get('drivers/available', [AssignmentController::class, 'availableDrivers'])->name('drivers.available');

            // 🏪 Fournisseurs et Catégories - ENTERPRISE GRADE V2.0
            Route::get('suppliers/export', [SupplierController::class, 'export'])->name('suppliers.export');
            Route::get('suppliers', \App\Livewire\Admin\Suppliers\SupplierIndex::class)->name('suppliers.index');
            Route::resource('suppliers', SupplierController::class)->except('index');
            Route::resource('supplier-categories', SupplierCategoryController::class);

            /*
        |--------------------------------------------------------------------------
        | 🔧 MODULES ENTERPRISE - RÉPARATIONS, FOURNISSEURS, DÉPENSES
        |--------------------------------------------------------------------------
        | Modules développés avec architecture enterprise-grade
        | - Workflow validation 2 niveaux pour réparations
        | - Conformité DZ totale pour fournisseurs
        | - Traçabilité maximale pour dépenses
        */

            // 🔧 MODULE DEMANDES DE RÉPARATION ENTERPRISE-GRADE
            Route::prefix('repair-requests')->name('repair-requests.')->group(function () {
                // Vue principale avec composant Livewire Ultra-Professionnel
                Route::get('/', function () {
                    return view('admin.repair-requests.index');
                })->name('index');

                // CRUD Operations
                Route::get('/create', [\App\Http\Controllers\Admin\RepairRequestController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\RepairRequestController::class, 'store'])->name('store');
                Route::get('/export', [\App\Http\Controllers\Admin\RepairRequestController::class, 'export'])->name('export');
                Route::get('/{repairRequest}', [\App\Http\Controllers\Admin\RepairRequestController::class, 'show'])->name('show');
                Route::delete('/{repairRequest}', [\App\Http\Controllers\Admin\RepairRequestController::class, 'destroy'])->name('destroy');

                // Workflow d'approbation à 2 niveaux
                Route::post('/{repairRequest}/approve-supervisor', [\App\Http\Controllers\Admin\RepairRequestController::class, 'approveSupervisor'])->name('approve-supervisor');
                Route::post('/{repairRequest}/reject-supervisor', [\App\Http\Controllers\Admin\RepairRequestController::class, 'rejectSupervisor'])->name('reject-supervisor');
                Route::post('/{repairRequest}/approve-fleet-manager', [\App\Http\Controllers\Admin\RepairRequestController::class, 'approveFleetManager'])->name('approve-fleet-manager');
                Route::post('/{repairRequest}/reject-fleet-manager', [\App\Http\Controllers\Admin\RepairRequestController::class, 'rejectFleetManager'])->name('reject-fleet-manager');
            });

            // 🏢 MODULE FOURNISSEURS ENTERPRISE - Conformité DZ Totale
            // TODO: Implémenter SupplierEnterpriseController
            /*
        Route::prefix('suppliers-enterprise')->name('suppliers-enterprise.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SupplierEnterpriseController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\SupplierEnterpriseController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\SupplierEnterpriseController::class, 'store'])->name('store');
            Route::get('/{supplier}', [\App\Http\Controllers\Admin\SupplierEnterpriseController::class, 'show'])->name('show');
            Route::get('/{supplier}/edit', [\App\Http\Controllers\Admin\SupplierEnterpriseController::class, 'edit'])->name('edit');
            Route::put('/{supplier}', [\App\Http\Controllers\Admin\SupplierEnterpriseController::class, 'update'])->name('update');
            Route::delete('/{supplier}', [\App\Http\Controllers\Admin\SupplierEnterpriseController::class, 'destroy'])->name('destroy');

            // Actions spécialisées
            Route::post('/{supplier}/blacklist', [\App\Http\Controllers\Admin\SupplierEnterpriseController::class, 'blacklist'])->name('blacklist');
            Route::post('/{supplier}/unblacklist', [\App\Http\Controllers\Admin\SupplierEnterpriseController::class, 'unblacklist'])->name('unblacklist');
            Route::post('/{supplier}/toggle-preferred', [\App\Http\Controllers\Admin\SupplierEnterpriseController::class, 'togglePreferred'])->name('toggle-preferred');
            Route::post('/{supplier}/rate', [\App\Http\Controllers\Admin\SupplierEnterpriseController::class, 'rate'])->name('rate');

            // Export et validation DZ
            Route::get('/export', [\App\Http\Controllers\Admin\SupplierEnterpriseController::class, 'export'])->name('export');
            Route::post('/validate-nif', [\App\Http\Controllers\Admin\SupplierEnterpriseController::class, 'validateNIF'])->name('validate-nif');
            Route::post('/validate-rc', [\App\Http\Controllers\Admin\SupplierEnterpriseController::class, 'validateRC'])->name('validate-rc');

            // Gestion des évaluations
            Route::prefix('{supplier}/ratings')->name('ratings.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\SupplierRatingController::class, 'index'])->name('index');
                Route::post('/', [\App\Http\Controllers\Admin\SupplierRatingController::class, 'store'])->name('store');
                Route::delete('/{rating}', [\App\Http\Controllers\Admin\SupplierRatingController::class, 'destroy'])->name('destroy');
            });
        });
        */

            // 💰 MODULE DÉPENSES - Traçabilité Maximale
            Route::prefix('vehicle-expenses')->name('vehicle-expenses.')->group(function () {
                // Dashboard principal avec analytics
                Route::get('/', \App\Livewire\Admin\VehicleExpenses\ExpenseManager::class)->name('index');
                Route::get('/dashboard', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'dashboard'])->name('dashboard');

                // CRUD operations
                Route::get('/create', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'store'])->name('store');
                Route::get('/{expense}', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'show'])->name('show');
                Route::get('/{expense}/edit', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'edit'])->name('edit');
                Route::put('/{expense}', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'update'])->name('update');
                Route::delete('/{expense}', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'destroy'])->name('destroy');

                // Workflow d'approbation
                Route::post('/{expense}/request-approval', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'requestApproval'])->name('request-approval');
                Route::post('/{expense}/approve', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'approve'])->name('approve');
                Route::post('/{expense}/reject-approval', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'rejectApproval'])->name('reject-approval');

                // Gestion des paiements
                Route::post('/{expense}/mark-paid', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'markAsPaid'])->name('mark-paid');
                Route::post('/{expense}/audit', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'audit'])->name('audit');

                // Import/Export et récurrence
                Route::get('/import', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'showImportForm'])->name('import.show');
                Route::post('/import', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'handleImport'])->name('import.handle');
                Route::get('/export', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'export'])->name('export');
                Route::post('/create-recurring', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'createRecurring'])->name('create-recurring');

                // Analytics et rapports
                Route::get('/analytics/fuel-efficiency', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'fuelEfficiencyReport'])->name('analytics.fuel-efficiency');
                Route::get('/analytics/cost-trends', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'costTrendsReport'])->name('analytics.cost-trends');
                Route::get('/analytics/budget-utilization', [\App\Http\Controllers\Admin\VehicleExpenseController::class, 'budgetUtilizationReport'])->name('analytics.budget-utilization');
            });

            // 📊 GESTION DES BUDGETS DE DÉPENSES
            // TODO: ExpenseBudgetController needs to be created
            /*
        Route::prefix('expense-budgets')->name('expense-budgets.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ExpenseBudgetController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\ExpenseBudgetController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\ExpenseBudgetController::class, 'store'])->name('store');
            Route::get('/{budget}', [\App\Http\Controllers\Admin\ExpenseBudgetController::class, 'show'])->name('show');
            Route::get('/{budget}/edit', [\App\Http\Controllers\Admin\ExpenseBudgetController::class, 'edit'])->name('edit');
            Route::put('/{budget}', [\App\Http\Controllers\Admin\ExpenseBudgetController::class, 'update'])->name('update');
            Route::delete('/{budget}', [\App\Http\Controllers\Admin\ExpenseBudgetController::class, 'destroy'])->name('destroy');

            // Actions spéciales
            Route::post('/{budget}/recalculate', [\App\Http\Controllers\Admin\ExpenseBudgetController::class, 'recalculate'])->name('recalculate');
            Route::get('/alerts/overruns', [\App\Http\Controllers\Admin\ExpenseBudgetController::class, 'budgetOverruns'])->name('alerts.overruns');
        });
        */

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
    | 🔧 MODULE MAINTENANCE ENTERPRISE-GRADE - ROUTES INTÉGRÉES
    |--------------------------------------------------------------------------
    | Architecture corrigée : Routes maintenance directement intégrées
    | pour éviter les conflits de préfixe et assurer le bon routage
    */

        // 🚨 SYSTÈME D'ALERTES ENTERPRISE
        Route::prefix('alerts')->name('alerts.')->middleware('can:alerts.view')->group(function () {
            Route::view('/', 'admin.alerts.index-livewire')->name('index');
            Route::get('/api', [\App\Http\Controllers\Admin\AlertController::class, 'getAlertsApi'])->name('api');
            Route::post('/mark-as-read', [\App\Http\Controllers\Admin\AlertController::class, 'markAsRead'])->name('mark-as-read');
            Route::get('/export', [\App\Http\Controllers\Admin\AlertController::class, 'export'])->name('export');
        });

        // 💰 MODULE DÉPENSES ENTERPRISE-GRADE
        Route::prefix('expenses')->name('expenses.')->group(function () {
            // Dashboard et liste
            Route::get('/', [\App\Http\Controllers\Admin\ExpenseController::class, 'index'])->name('index');
            Route::get('/analytics', [\App\Http\Controllers\Admin\ExpenseController::class, 'analytics'])->name('analytics');

            // CRUD operations
            Route::get('/create', [\App\Http\Controllers\Admin\ExpenseController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\ExpenseController::class, 'store'])->name('store');
            Route::get('/{expense}', [\App\Http\Controllers\Admin\ExpenseController::class, 'show'])->name('show');
            Route::get('/{expense}/edit', [\App\Http\Controllers\Admin\ExpenseController::class, 'edit'])->name('edit');
            Route::put('/{expense}', [\App\Http\Controllers\Admin\ExpenseController::class, 'update'])->name('update');
            Route::delete('/{expense}', [\App\Http\Controllers\Admin\ExpenseController::class, 'destroy'])->name('destroy');

            // Actions spécialisées
            Route::post('/{expense}/approve', [\App\Http\Controllers\Admin\ExpenseController::class, 'approve'])->name('approve');
            Route::post('/{expense}/reject', [\App\Http\Controllers\Admin\ExpenseController::class, 'reject'])->name('reject');
            Route::get('/{expense}/receipt', [\App\Http\Controllers\Admin\ExpenseController::class, 'downloadReceipt'])->name('receipt');

            // Export et rapports
            Route::get('/export/excel', [\App\Http\Controllers\Admin\ExpenseController::class, 'export'])->name('export');
            Route::get('/reports/summary', [\App\Http\Controllers\Admin\ExpenseController::class, 'reportSummary'])->name('reports.summary');
        });

        // 🔧 Dashboard Maintenance Principal
        Route::prefix('maintenance')->name('maintenance.')->group(function () {
            // Dashboard principal et overview
            Route::get('/', [\App\Http\Controllers\Admin\MaintenanceController::class, 'dashboard'])->name('dashboard');
            Route::get('/overview', [\App\Http\Controllers\Admin\MaintenanceController::class, 'overview'])->name('overview');

            // 📊 Sous-menu Surveillance
            Route::prefix('surveillance')->name('surveillance.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\Maintenance\SurveillanceController::class, 'index'])->name('index');
            });

            // 📋 Gestion des Types de Maintenance
            Route::prefix('types')->name('types.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\MaintenanceTypeController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\MaintenanceTypeController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\MaintenanceTypeController::class, 'store'])->name('store');
                Route::get('/{maintenanceType}', [\App\Http\Controllers\Admin\MaintenanceTypeController::class, 'show'])->name('show');
                Route::get('/{maintenanceType}/edit', [\App\Http\Controllers\Admin\MaintenanceTypeController::class, 'edit'])->name('edit');
                Route::put('/{maintenanceType}', [\App\Http\Controllers\Admin\MaintenanceTypeController::class, 'update'])->name('update');
                Route::delete('/{maintenanceType}', [\App\Http\Controllers\Admin\MaintenanceTypeController::class, 'destroy'])->name('destroy');
            });

            // 🏢 Gestion des Fournisseurs de Maintenance
            Route::prefix('providers')->name('providers.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'store'])->name('store');
                Route::get('/{provider}', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'show'])->name('show');
                Route::get('/{provider}/edit', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'edit'])->name('edit');
                Route::put('/{provider}', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'update'])->name('update');
                Route::delete('/{provider}', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'destroy'])->name('destroy');
            });

            // 📅 Gestion des Planifications de Maintenance
            Route::prefix('schedules')->name('schedules.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\MaintenanceScheduleController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\MaintenanceScheduleController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\MaintenanceScheduleController::class, 'store'])->name('store');
                Route::get('/{schedule}', [\App\Http\Controllers\Admin\MaintenanceScheduleController::class, 'show'])->name('show');
                Route::get('/{schedule}/edit', [\App\Http\Controllers\Admin\MaintenanceScheduleController::class, 'edit'])->name('edit');
                Route::put('/{schedule}', [\App\Http\Controllers\Admin\MaintenanceScheduleController::class, 'update'])->name('update');
                Route::delete('/{schedule}', [\App\Http\Controllers\Admin\MaintenanceScheduleController::class, 'destroy'])->name('destroy');
            });

            // 🔧 Gestion des Opérations de Maintenance
            Route::prefix('operations')->name('operations.')->group(function () {
                Route::get('/', \App\Livewire\Admin\Maintenance\MaintenanceTable::class)->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\MaintenanceOperationController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\MaintenanceOperationController::class, 'store'])->name('store');
                Route::get('/{operation}', [\App\Http\Controllers\Admin\MaintenanceOperationController::class, 'show'])->name('show');
                Route::get('/{operation}/edit', [\App\Http\Controllers\Admin\MaintenanceOperationController::class, 'edit'])->name('edit');
                Route::put('/{operation}', [\App\Http\Controllers\Admin\MaintenanceOperationController::class, 'update'])->name('update');
                Route::delete('/{operation}', [\App\Http\Controllers\Admin\MaintenanceOperationController::class, 'destroy'])->name('destroy');
            });

            // 🚨 Gestion des Alertes de Maintenance
            Route::prefix('alerts')->name('alerts.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\MaintenanceAlertController::class, 'index'])->name('index');
                Route::get('/dashboard', [\App\Http\Controllers\Admin\MaintenanceAlertController::class, 'dashboard'])->name('dashboard');
                Route::get('/{alert}', [\App\Http\Controllers\Admin\MaintenanceAlertController::class, 'show'])->name('show');
                Route::delete('/{alert}', [\App\Http\Controllers\Admin\MaintenanceAlertController::class, 'destroy'])->name('destroy');
                Route::post('/{alert}/acknowledge', [\App\Http\Controllers\Admin\MaintenanceAlertController::class, 'acknowledge'])->name('acknowledge');
            });

            // 📊 Rapports et Analytiques de Maintenance
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\MaintenanceReportController::class, 'index'])->name('index');
                Route::get('/performance', [\App\Http\Controllers\Admin\MaintenanceReportController::class, 'performance'])->name('performance');
                Route::get('/costs', [\App\Http\Controllers\Admin\MaintenanceReportController::class, 'costs'])->name('costs');
                Route::get('/kpis', [\App\Http\Controllers\Admin\MaintenanceReportController::class, 'kpis'])->name('kpis');
                Route::get('/compliance', [\App\Http\Controllers\Admin\MaintenanceReportController::class, 'compliance'])->name('compliance');
                Route::get('/providers-analysis', [\App\Http\Controllers\Admin\MaintenanceReportController::class, 'providersAnalysis'])->name('providers-analysis');
                Route::get('/custom', [\App\Http\Controllers\Admin\MaintenanceReportController::class, 'custom'])->name('custom');
                Route::post('/custom/generate', [\App\Http\Controllers\Admin\MaintenanceReportController::class, 'generateCustom'])->name('custom.generate');
            });

            // 🔄 Automatisation et Jobs
            Route::prefix('automation')->name('automation.')->group(function () {
                Route::post('/check-schedules', [\App\Http\Controllers\Admin\MaintenanceController::class, 'triggerScheduleCheck'])->name('check-schedules');
                Route::post('/generate-alerts', [\App\Http\Controllers\Admin\MaintenanceController::class, 'generateAlerts'])->name('generate-alerts');
            });

            // 🏢 Gestion des Fournisseurs de Maintenance
            Route::prefix('providers')->name('providers.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'store'])->name('store');
                Route::get('/{provider}', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'show'])->name('show');
                Route::get('/{provider}/edit', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'edit'])->name('edit');
                Route::put('/{provider}', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'update'])->name('update');
                Route::delete('/{provider}', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'destroy'])->name('destroy');
            });
        });

        /*
    |--------------------------------------------------------------------------
    | 🔧 LEGACY MAINTENANCE SYSTEM - DÉSACTIVÉ POUR ÉVITER CONFLITS
    |--------------------------------------------------------------------------
    | ⚠️ SYSTÈME LEGACY DÉSACTIVÉ - Remplacé par le module Enterprise
    | Le nouveau système est défini dans /routes/maintenance.php
    |
    | PROBLÈME RÉSOLU: Conflit de nommage des routes 'maintenance.dashboard'
    | - Ancien: DashboardController::maintenanceDashboard (avec $urgentPlans)
    | - Nouveau: MaintenanceController::dashboard (variables correctes)
    |--------------------------------------------------------------------------
    */
        Route::middleware('role:Super Admin|Admin|Gestionnaire Flotte|Supervisor|Superviseur')->group(function () {

            // ❌ LEGACY Dashboard Maintenance - DÉSACTIVÉ pour éviter conflit de routes
            /*
        Route::prefix('maintenance')->name('maintenance.')->group(function () {
            Route::get('/', [DashboardController::class, 'maintenanceDashboard'])->name('dashboard');
            Route::get('calendar', [DashboardController::class, 'maintenanceCalendar'])->name('calendar');
            Route::get('alerts', [DashboardController::class, 'maintenanceAlerts'])->name('alerts');
            Route::get('analytics', [DashboardController::class, 'maintenanceAnalytics'])->name('analytics');
        });
        */

            // ❌ LEGACY Plans et Logs - DÉSACTIVÉ
            // Route::resource('maintenance/plans', MaintenancePlanController::class)->names('maintenance.plans');
            // Route::post('maintenance/plans/{plan}/duplicate', [MaintenancePlanController::class, 'duplicate'])
            //     ->name('maintenance.plans.duplicate');

            // ❌ LEGACY Logs de maintenance - DÉSACTIVÉ
            /*
        Route::prefix('maintenance/logs')->name('maintenance.logs.')->group(function () {
            Route::get('/', [DashboardController::class, 'maintenanceLogs'])->name('index');
            Route::get('export', [DashboardController::class, 'exportMaintenanceLogs'])->name('export');
        });
        */
        });

        /*
    |--------------------------------------------------------------------------
    | 📋 FICHES DE REMISE - HANDOVERS ✅ SECTION CORRIGÉE
    |--------------------------------------------------------------------------
    */
        Route::middleware('role:Super Admin|Admin|Gestionnaire Flotte|Supervisor|Superviseur')->group(function () {

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
        Route::middleware('role:Super Admin|Admin|Gestionnaire Flotte|Supervisor|Superviseur')->group(function () {

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
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| 🧪 ROUTE DE TEST RLS (Row Level Security)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/rls_test.php';

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

/*
|--------------------------------------------------------------------------
| 🔧 MODULE MAINTENANCE - Enterprise Grade
|--------------------------------------------------------------------------
*/
require __DIR__ . '/maintenance.php';


/*
|--------------------------------------------------------------------------
| 📊 MODULE ANALYTICS - Enterprise Dashboard
|--------------------------------------------------------------------------
*/
require __DIR__ . '/analytics.php';

/*
|--------------------------------------------------------------------------
| 📋 FICHES DE REMISE - Vehicle Handovers
|--------------------------------------------------------------------------
*/
require __DIR__ . '/handovers.php';
