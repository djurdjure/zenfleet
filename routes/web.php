<?php

use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Maintenance\DashboardController as MaintenanceDashboardController;
use App\Http\Controllers\Admin\Maintenance\MaintenancePlanController;
use App\Http\Controllers\Admin\Maintenance\MaintenanceLogController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Fichier de routage principal de l'application web.
|
*/

// --- Routes Publiques et Dashboard ---
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// --- Route pour le Profil de l'Utilisateur Connecté ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- GROUPE DE ROUTES POUR L'ADMINISTRATION ---
// Toutes les routes ici nécessitent que l'utilisateur soit authentifié et ait le rôle 'Admin' ou les permissions adéquates.
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // -- Gestion des Utilisateurs --
    Route::resource('users', UserController::class);

    // -- Gestion des Rôles --
    Route::resource('roles', RoleController::class)->only(['index', 'edit', 'update']);

    // -- Gestion des Chauffeurs --
    Route::patch('/drivers/{driver}/restore', [DriverController::class, 'restore'])->name('drivers.restore')->withTrashed();
    Route::delete('/drivers/{driver}/force-delete', [DriverController::class, 'forceDelete'])->name('drivers.force-delete')->withTrashed();
    Route::resource('drivers', DriverController::class);

    // -- Gestion des Véhicules --
    // Déclaration des routes spécifiques AVANT la route de ressource pour éviter les conflits.
    Route::get('/vehicles/import', [VehicleController::class, 'showImportForm'])->name('vehicles.import.show');
    Route::post('/vehicles/import', [VehicleController::class, 'handleImport'])->name('vehicles.import.handle');
    Route::get('/vehicles/import-template', [VehicleController::class, 'downloadTemplate'])->name('vehicles.import.template');
    Route::get('/vehicles/import/results', [VehicleController::class, 'showImportResults'])->name('vehicles.import.results');
    Route::patch('/vehicles/{vehicle}/restore', [VehicleController::class, 'restore'])->name('vehicles.restore')->withTrashed();
    Route::delete('/vehicles/{vehicle}/force-delete', [VehicleController::class, 'forceDelete'])->name('vehicles.force-delete')->withTrashed();
    Route::resource('vehicles', VehicleController::class);
    Route::post('/vehicles/{vehicle}/maintenance-plans', [MaintenancePlanController::class, 'store'])->name('vehicles.maintenance_plans.store');



    // -- Gestion des ASSIGNMENTS --
    Route::patch('/assignments/{assignment}/end', [AssignmentController::class, 'end'])->name('assignments.end');
    Route::resource('assignments', AssignmentController::class);

    // --- GESTION DE LA MAINTENANCE ---
    Route::get('/maintenance', [MaintenanceDashboardController::class, 'index'])->name('maintenance.dashboard');
    Route::resource('maintenance/plans', MaintenancePlanController::class)->names('maintenance.plans');
    //Route::resource('maintenance/plans', MaintenancePlanController::class);
    Route::resource('maintenance/logs', MaintenanceLogController::class); // <-- AJOUT
    Route::resource('maintenance/logs', MaintenanceLogController::class)->names('maintenance.logs');

});

// Inclusion des routes d'authentification de Breeze
require __DIR__.'/auth.php';



