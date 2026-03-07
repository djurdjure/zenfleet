<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Maintenance\MaintenanceOperationController;
use App\Http\Controllers\Admin\Maintenance\MaintenanceScheduleController;
use App\Http\Controllers\Admin\MaintenanceAlertController;
use App\Http\Controllers\Admin\MaintenanceReportController;
use App\Livewire\Admin\Maintenance\MaintenanceTable;

/*
|--------------------------------------------------------------------------
| 🔧 MODULE MAINTENANCE ROUTES - ENTERPRISE-GRADE
|--------------------------------------------------------------------------
|
| Routes pour le module maintenance refactoré
| Architecture: Controller Pattern + Service Layer
| 
| @version 1.0 Enterprise
| @author ZenFleet Architecture Team
|
*/

Route::prefix('admin/maintenance')->name('admin.maintenance.')->middleware(['auth', 'verified'])->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | 📊 DASHBOARD MAINTENANCE
    |--------------------------------------------------------------------------
    */
    // TODO: Créer MaintenanceDashboardController pour vue d'ensemble dédiée
    // Pour l'instant, redirige vers la vue opérations qui contient déjà les métriques
    Route::get('/dashboard', function () {
        return redirect()->route('admin.maintenance.operations.index');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | 🔧 OPÉRATIONS DE MAINTENANCE (CRUD Complet)
    |--------------------------------------------------------------------------
    */
    Route::prefix('operations')->name('operations.')->group(function () {
        /*
        |----------------------------------------------------------------------
        | ROUTES STATIQUES - Doivent être définies EN PREMIER
        |----------------------------------------------------------------------
        */
        // Vue liste principale (Livewire, filtres sans refresh complet)
        Route::get('/', MaintenanceTable::class)->name('index');
        
        // Vues alternatives (statiques)
        Route::get('/kanban', [MaintenanceOperationController::class, 'kanban'])->name('kanban');
        Route::get('/calendar', [MaintenanceOperationController::class, 'calendar'])->name('calendar');
        Route::get('/timeline', [MaintenanceOperationController::class, 'timeline'])->name('timeline');
        
        // Actions de création
        Route::get('/create', [MaintenanceOperationController::class, 'create'])->name('create');
        Route::post('/', [MaintenanceOperationController::class, 'store'])->name('store');
        
        // Export (statiques)
        Route::get('/export/csv', [MaintenanceOperationController::class, 'export'])->name('export');
        Route::get('/export/pdf', [MaintenanceOperationController::class, 'exportPdf'])->name('export.pdf');
        
        /*
        |----------------------------------------------------------------------
        | ROUTES DYNAMIQUES - Avec contraintes pour accepter UNIQUEMENT des IDs
        |----------------------------------------------------------------------
        | IMPORTANT: where('operation', '[0-9]+') garantit que seuls les
        | nombres sont acceptés, évitant les conflits avec routes statiques
        |----------------------------------------------------------------------
        */
        Route::get('/{operation}', [MaintenanceOperationController::class, 'show'])
            ->name('show')
            ->where('operation', '[0-9]+');
            
        Route::get('/{operation}/edit', [MaintenanceOperationController::class, 'edit'])
            ->name('edit')
            ->where('operation', '[0-9]+');
            
        Route::put('/{operation}', [MaintenanceOperationController::class, 'update'])
            ->name('update')
            ->where('operation', '[0-9]+');
            
        Route::delete('/{operation}', [MaintenanceOperationController::class, 'destroy'])
            ->name('destroy')
            ->where('operation', '[0-9]+');
        
        /*
        |----------------------------------------------------------------------
        | ACTIONS SPÉCIALES - Avec contraintes
        |----------------------------------------------------------------------
        */
        Route::patch('/{operation}/start', [MaintenanceOperationController::class, 'start'])
            ->name('start')
            ->where('operation', '[0-9]+');
            
        Route::patch('/{operation}/complete', [MaintenanceOperationController::class, 'complete'])
            ->name('complete')
            ->where('operation', '[0-9]+');
            
        Route::patch('/{operation}/cancel', [MaintenanceOperationController::class, 'cancel'])
            ->name('cancel')
            ->where('operation', '[0-9]+');
    });

    /*
    |--------------------------------------------------------------------------
    | 📅 PLANIFICATIONS (MAINTENANCE PRÉVENTIVE)
    |--------------------------------------------------------------------------
    */
    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::get('/', [MaintenanceScheduleController::class, 'index'])->name('index');
        Route::get('/create', [MaintenanceScheduleController::class, 'create'])->name('create');
        Route::post('/', [MaintenanceScheduleController::class, 'store'])->name('store');
        Route::get('/{schedule}', [MaintenanceScheduleController::class, 'show'])->name('show');
        Route::get('/{schedule}/edit', [MaintenanceScheduleController::class, 'edit'])->name('edit');
        Route::put('/{schedule}', [MaintenanceScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}', [MaintenanceScheduleController::class, 'destroy'])->name('destroy');
        
        // Actions
        Route::patch('/{schedule}/toggle', [MaintenanceScheduleController::class, 'toggleActive'])->name('toggle');
        Route::post('/create-operations', [MaintenanceScheduleController::class, 'createOperations'])->name('create-operations');
    });

    /*
    |--------------------------------------------------------------------------
    | 🔔 ALERTES MAINTENANCE
    |--------------------------------------------------------------------------
    */
    Route::prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/', [MaintenanceAlertController::class, 'index'])->name('index');
        Route::patch('/{alert}/read', [MaintenanceAlertController::class, 'markAsRead'])->name('read');
        Route::patch('/{alert}/deactivate', [MaintenanceAlertController::class, 'deactivate'])->name('deactivate');
        Route::delete('/{alert}', [MaintenanceAlertController::class, 'destroy'])->name('destroy');
        Route::post('/scan', [MaintenanceAlertController::class, 'scanAlerts'])->name('scan');
    });

    /*
    |--------------------------------------------------------------------------
    | 📈 RAPPORTS & ANALYTICS
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [MaintenanceReportController::class, 'index'])->name('index');
        Route::get('/costs', [MaintenanceReportController::class, 'costs'])->name('costs');
        Route::get('/performance', [MaintenanceReportController::class, 'performance'])->name('performance');
        Route::get('/vehicles', [MaintenanceReportController::class, 'vehicles'])->name('vehicles');
        Route::get('/providers', [MaintenanceReportController::class, 'providers'])->name('providers');
        Route::get('/forecast', [MaintenanceReportController::class, 'forecast'])->name('forecast');
    });

    /*
    |--------------------------------------------------------------------------
    | ⚙️ TYPES DE MAINTENANCE (Configuration)
    |--------------------------------------------------------------------------
    */
    Route::prefix('types')->name('types.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MaintenanceTypeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\MaintenanceTypeController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\MaintenanceTypeController::class, 'store'])->name('store');
        Route::get('/{type}/edit', [\App\Http\Controllers\Admin\MaintenanceTypeController::class, 'edit'])->name('edit');
        Route::put('/{type}', [\App\Http\Controllers\Admin\MaintenanceTypeController::class, 'update'])->name('update');
        Route::delete('/{type}', [\App\Http\Controllers\Admin\MaintenanceTypeController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | 🏢 FOURNISSEURS MAINTENANCE (Configuration)
    |--------------------------------------------------------------------------
    */
    Route::prefix('providers')->name('providers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'store'])->name('store');
        Route::get('/{provider}/edit', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'edit'])->name('edit');
        Route::put('/{provider}', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'update'])->name('update');
        Route::delete('/{provider}', [\App\Http\Controllers\Admin\MaintenanceProviderController::class, 'destroy'])->name('destroy');
    });
});
