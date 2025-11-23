<?php

namespace App\Services;

use App\Models\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

/**
 * 📄 SERVICE D'EXPORT PDF DES CHAUFFEURS - ENTERPRISE-GRADE
 *
 * Service spécialisé pour l'export PDF des chauffeurs via microservice centralisé:
 * - 🎨 Export liste de chauffeurs (avec filtres)
 * - 🚀 Utilisation du microservice PDF Node.js
 * - 📊 HTML enrichi enterprise-grade
 * - 🔒 Isolation d'organisation
 *
 * @version 1.0 - Enterprise PDF Export
 * @since 2025-11-21
 */
class DriverPdfExportService
{
    protected $filters;
    protected $organization_id;
    protected $pdfService;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->organization_id = Auth::user()->organization_id;
        $this->pdfService = new PdfGenerationService();
    }

    /**
     * 📊 Export de la liste des chauffeurs en PDF
     */
    public function exportList()
    {
        try {
            // 1️⃣ Récupérer les chauffeurs filtrés (limité à 100 pour éviter timeout)
            $drivers = $this->getDrivers();

            // 2️⃣ Générer le HTML pour le template
            $html = $this->generateListHtml($drivers);

            // 3️⃣ Appeler le microservice PDF
            $pdfContent = $this->pdfService->generateFromHtml($html);

            // 4️⃣ Générer le nom du fichier
            $fileName = 'drivers_list_' . date('Y-m-d') . '.pdf';

            // 5️⃣ Retourner la réponse HTTP avec le PDF
            return Response::make($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Content-Length' => strlen($pdfContent),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'DENY',
                'X-PDF-Service' => 'Enterprise Microservice v2.0'
            ]);

        } catch (\Exception $e) {
            Log::error('Driver PDF export failed', [
                'error' => $e->getMessage(),
                'filters' => $this->filters,
                'organization_id' => $this->organization_id
            ]);
            throw $e;
        }
    }

    /**
     * 🔍 Récupération des chauffeurs avec filtres
     */
    protected function getDrivers()
    {
        $query = Driver::query()
            ->with([
                'driverStatus',
                'user',
                'activeAssignment.vehicle'
            ])
            ->where('organization_id', $this->organization_id)
            ->limit(100); // ⚠️ Limite pour éviter timeout microservice

        // 🔥 FILTRE 1: Visibilité (archivé/actif/tous)
        $visibility = $this->filters['visibility'] ?? 'active';
        if ($visibility === 'archived') {
            $query->onlyTrashed();
        } elseif ($visibility === 'all') {
            $query->withTrashed();
        }

        // 🔥 FILTRE 2: Recherche textuelle
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(employee_number) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(personal_email) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(personal_phone) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        // 🔥 FILTRE 3: Statut
        if (!empty($this->filters['status_id'])) {
            $query->where('driver_status_id', $this->filters['status_id']);
        }

        // 🔥 FILTRE 4: Catégorie de permis
        if (!empty($this->filters['license_category'])) {
            $query->where('license_category', $this->filters['license_category']);
        }

        // 🔥 FILTRE 5: Date d'embauche
        if (!empty($this->filters['hired_after'])) {
            $query->whereDate('recruitment_date', '>=', $this->filters['hired_after']);
        }

        // 🔥 TRI
        $sortBy = $this->filters['sort_by'] ?? 'created_at';
        $sortDirection = $this->filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query->get();
    }

    /**
     * 🎨 Génération du HTML pour la liste des chauffeurs
     */
    protected function generateListHtml($drivers)
    {
        $organization = Auth::user()->organization;
        $generatedAt = now()->format('d/m/Y à H:i');
        $totalDrivers = $drivers->count();

        $html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Chauffeurs</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.4;
        }

        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .header h1 {
            font-size: 22px;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .header p {
            font-size: 11px;
            opacity: 0.95;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            padding: 12px 15px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .meta-info div {
            font-size: 10px;
        }

        .meta-info strong {
            display: block;
            color: #6b7280;
            font-size: 9px;
            text-transform: uppercase;
            margin-bottom: 2px;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }

        thead {
            background: #3b82f6;
            color: white;
        }

        thead th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tbody tr:hover {
            background-color: #eff6ff;
        }

        tbody td {
            padding: 10px 8px;
            font-size: 10px;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
        }

        .badge-green {
            background: #dcfce7;
            color: #166534;
        }

        .badge-orange {
            background: #fed7aa;
            color: #9a3412;
        }

        .badge-purple {
            background: #e9d5ff;
            color: #6b21a8;
        }

        .badge-amber {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-red {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-gray {
            background: #f3f4f6;
            color: #374151;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            font-size: 9px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Liste des Chauffeurs</h1>
        <p>{$organization->name}</p>
    </div>

    <div class="meta-info">
        <div>
            <strong>Généré le</strong>
            <span>{$generatedAt}</span>
        </div>
        <div>
            <strong>Total chauffeurs</strong>
            <span>{$totalDrivers}</span>
        </div>
        <div>
            <strong>Utilisateur</strong>
            <span>{Auth::user()->name}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Statut</th>
                <th>Permis</th>
                <th>Véhicule</th>
            </tr>
        </thead>
        <tbody>
HTML;

        foreach ($drivers as $driver) {
            $statusClass = $this->getStatusBadgeClass($driver->driverStatus ? $driver->driverStatus->name : 'N/A');
            $matricule = $driver->employee_number ?? 'N/A';
            $fullName = $driver->first_name . ' ' . $driver->last_name;
            $email = $driver->personal_email ?? 'N/A';
            $phone = $driver->personal_phone ?? 'N/A';
            $status = $driver->driverStatus ? $driver->driverStatus->name : 'N/A';
            $license = $driver->license_number ?? 'N/A';
            $vehicle = $driver->activeAssignment && $driver->activeAssignment->vehicle
                ? $driver->activeAssignment->vehicle->registration_plate
                : 'Aucun';

            $html .= <<<HTML
            <tr>
                <td>{$matricule}</td>
                <td><strong>{$fullName}</strong></td>
                <td>{$email}</td>
                <td>{$phone}</td>
                <td><span class="badge {$statusClass}">{$status}</span></td>
                <td>{$license}</td>
                <td>{$vehicle}</td>
            </tr>
HTML;
        }

        $html .= <<<HTML
        </tbody>
    </table>

    <div class="footer">
        <p>Document généré automatiquement par ZenFleet - Système de Gestion de Flotte Enterprise-Grade</p>
        <p>© {date('Y')} {$organization->name} - Tous droits réservés</p>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * 🎨 Obtenir la classe CSS du badge selon le statut
     */
    protected function getStatusBadgeClass($status)
    {
        return match($status) {
            'Disponible' => 'badge-green',
            'En mission' => 'badge-orange',
            'En repos' => 'badge-amber',
            'En congé' => 'badge-purple',
            'Maladie' => 'badge-red',
            'Indisponible' => 'badge-gray',
            default => 'badge-gray',
        };
    }
}
