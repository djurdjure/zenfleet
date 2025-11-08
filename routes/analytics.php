<?php

use App\Http\Controllers\Admin\StatusAnalyticsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 📊 ANALYTICS ROUTES - Enterprise Dashboard
|--------------------------------------------------------------------------
|
| Routes pour les dashboards analytics et rapports de performance.
| Nécessite authentification et permissions appropriées.
|
*/

Route::middleware(['auth', 'web'])->prefix('admin/analytics')->name('admin.analytics.')->group(function () {

    // Dashboard principal statuts
    Route::get('/statuts', [StatusAnalyticsController::class, 'index'])
        ->name('status-dashboard')
        ->middleware('can:view-status-history');

    // Export CSV (à implémenter)
    Route::get('/statuts/export-csv', [StatusAnalyticsController::class, 'exportCsv'])
        ->name('status-export-csv')
        ->middleware('can:view-all-status-history');

    // Export PDF (à implémenter)
    Route::get('/statuts/export-pdf', [StatusAnalyticsController::class, 'exportPdf'])
        ->name('status-export-pdf')
        ->middleware('can:view-all-status-history');

    // API endpoints pour AJAX/Livewire (à implémenter)
    Route::get('/api/daily-stats', [StatusAnalyticsController::class, 'getDailyStatsApi'])
        ->name('api.daily-stats');
});
