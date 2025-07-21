<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\Maintenance\DashboardController as MaintenanceDashboardController;
use App\Http\Controllers\Admin\Maintenance\MaintenancePlanController;
use App\Http\Controllers\Admin\Maintenance\MaintenanceLogController;
use App\Http\Controllers\Admin\Handover\VehicleHandoverController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Routes Publiques et Authentification ---
Route::get('/', function () { return view('welcome'); });

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- GROUPE DE ROUTES POUR L'ADMINISTRATION ---
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // --- Routes accessibles au Super Admin ET aux Admins d'organisation ---
    Route::middleware(['role:Super Admin|Admin'])->group(function() {

        // --- MAINTENANCE ---
        Route::get('/maintenance', [MaintenanceDashboardController::class, 'index'])->name('maintenance.dashboard');
        Route::resource('maintenance/plans', MaintenancePlanController::class)->names('maintenance.plans');
        Route::resource('maintenance/logs', MaintenanceLogController::class)->only(['create', 'store'])->names('maintenance.logs');

        // --- FICHES DE REMISE ---
        Route::get('/assignments/{assignment}/handovers/create', [VehicleHandoverController::class, 'create'])->name('handovers.vehicles.create');
        Route::post('/handovers/{handover}/upload-signed', [VehicleHandoverController::class, 'uploadSigned'])->name('handovers.vehicles.uploadSigned');
        Route::resource('handovers', VehicleHandoverController::class)->names('handovers.vehicles')->except(['create', 'index']);

        // --- GESTION DES RESSOURCES PRINCIPALES ---
        Route::resource('users', UserController::class);
        Route::resource('drivers', DriverController::class);
        Route::resource('vehicles', VehicleController::class);
        Route::resource('assignments', AssignmentController::class);

        // --- ACTIONS SPÉCIFIQUES (ARCHIVAGE, IMPORT, ETC.) ---
        Route::patch('/drivers/{driver}/restore', [DriverController::class, 'restore'])->name('drivers.restore')->withTrashed();
        Route::delete('/drivers/{driver}/force-delete', [DriverController::class, 'forceDelete'])->name('drivers.force-delete')->withTrashed();

        Route::get('/vehicles/import', [VehicleController::class, 'showImportForm'])->name('vehicles.import.show');
        Route::post('/vehicles/import', [VehicleController::class, 'handleImport'])->name('vehicles.import.handle');
        Route::get('/vehicles/import-template', [VehicleController::class, 'downloadTemplate'])->name('vehicles.import.template');
        Route::get('/vehicles/import/results', [VehicleController::class, 'showImportResults'])->name('vehicles.import.results');
        Route::patch('/vehicles/{vehicle}/restore', [VehicleController::class, 'restore'])->name('vehicles.restore')->withTrashed();
        Route::delete('/vehicles/{vehicle}/force-delete', [VehicleController::class, 'forceDelete'])->name('vehicles.force-delete')->withTrashed();
    });

    // --- Routes accessibles UNIQUEMENT au Super Admin ---
    Route::middleware(['role:Super Admin'])->group(function() {
        Route::resource('organizations', OrganizationController::class);
        Route::resource('roles', RoleController::class)->only(['index', 'edit', 'update']);
    });
});

require __DIR__.'/auth.php';