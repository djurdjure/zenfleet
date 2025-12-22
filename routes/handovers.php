<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Handover\VehicleHandoverController;

/*
|--------------------------------------------------------------------------
| 📋 FICHES DE REMISE - VEHICLE HANDOVER ROUTES
|--------------------------------------------------------------------------
|
| Routes pour la gestion des fiches de remise de véhicules
| - Création de fiches pour les affectations
| - Upload de fiches signées
| - Génération PDF
|
*/

Route::middleware(['auth', 'verified', 'role:Super Admin|Admin|Gestionnaire Flotte|Supervisor'])
    ->prefix('admin/handovers')
    ->name('admin.handovers.vehicles.')
    ->group(function () {

        // Route index - Liste des fiches
        Route::get('/', [VehicleHandoverController::class, 'index'])->name('index');

        // Route create - Créer une fiche pour une affectation
        Route::get('assignment/{assignment}/create', [VehicleHandoverController::class, 'create'])->name('create');

        // Routes CRUD
        Route::post('/', [VehicleHandoverController::class, 'store'])->name('store');
        Route::get('{handover}', [VehicleHandoverController::class, 'show'])->name('show');
        Route::get('{handover}/edit', [VehicleHandoverController::class, 'edit'])->name('edit');
        Route::put('{handover}', [VehicleHandoverController::class, 'update'])->name('update');
        Route::delete('{handover}', [VehicleHandoverController::class, 'destroy'])->name('destroy');

        // Routes spéciales
        Route::post('{handover}/upload-signed', [VehicleHandoverController::class, 'uploadSigned'])->name('upload-signed');
        Route::get('{handover}/download-pdf', [VehicleHandoverController::class, 'downloadPdf'])->name('download-pdf');
    });
