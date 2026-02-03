<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DriversExport;
use App\Exports\DriversCsvExport;
use App\Services\DriverPdfExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

/**
 * 📊 TRAIT D'EXTENSIONS DRIVER CONTROLLER - EXPORTS ENTERPRISE
 *
 * Ce trait contient toutes les méthodes d'export pour les chauffeurs:
 * - 📄 Export PDF via microservice centralisé
 * - 📊 Export Excel avec styles enterprise
 * - 📋 Export CSV haute performance
 *
 * @version 1.0 - Enterprise Export Extensions
 * @since 2025-11-21
 */
trait DriverControllerExtensions
{
    /**
     * 📊 Export CSV des chauffeurs
     */
    public function exportCsv(Request $request)
    {
        $this->logUserAction('driver.export.csv', $request);

        try {
            // Vérifier les permissions
            if (!Auth::user()->can('drivers.view')) {
                abort(403, 'Non autorisé à exporter les chauffeurs');
            }

            $filters = $request->all();
            $csvExport = new DriversCsvExport($filters);

            return $csvExport->download();

        } catch (\Exception $e) {
            $this->logError('driver.export.csv.error', $e, $request, ['request_data' => $request->all()]);
            return back()->with('error', 'Erreur lors de l\'export CSV: ' . $e->getMessage());
        }
    }

    /**
     * 📊 Export Excel des chauffeurs
     */
    public function exportExcel(Request $request)
    {
        $this->logUserAction('driver.export.excel', $request);

        try {
            // Vérifier les permissions
            if (!Auth::user()->can('drivers.view')) {
                abort(403, 'Non autorisé à exporter les chauffeurs');
            }

            $filters = $request->all();
            $fileName = 'drivers_export_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new DriversExport($filters), $fileName);

        } catch (\Exception $e) {
            $this->logError('driver.export.excel.error', $e, $request, ['request_data' => $request->all()]);
            return back()->with('error', 'Erreur lors de l\'export Excel: ' . $e->getMessage());
        }
    }

    /**
     * 📄 Export PDF des chauffeurs (liste)
     */
    public function exportPdf(Request $request)
    {
        $this->logUserAction('driver.export.pdf', $request);

        try {
            // Vérifier les permissions
            if (!Auth::user()->can('drivers.view')) {
                abort(403, 'Non autorisé à exporter les chauffeurs');
            }

            $filters = $request->all();
            $pdfService = new DriverPdfExportService($filters);

            return $pdfService->exportList();

        } catch (\Exception $e) {
            $this->logError('driver.export.pdf.error', $e, $request, ['request_data' => $request->all()]);
            return back()->with('error', 'Erreur lors de l\'export PDF: ' . $e->getMessage());
        }
    }
}
