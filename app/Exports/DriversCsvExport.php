<?php

namespace App\Exports;

use App\Models\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use League\Csv\Writer;

/**
 * 📊 EXPORT CSV DES CHAUFFEURS - ENTERPRISE-GRADE
 *
 * Export CSV haute performance avec:
 * - 🚀 Performance optimisée avec League\CSV
 * - 🌐 UTF-8 BOM pour compatibilité Excel
 * - 🔍 Support des filtres avancés
 * - 📋 Compatible multi-organisation
 *
 * @version 1.0 - Enterprise Export System
 * @since 2025-11-21
 */
class DriversCsvExport
{
    protected $filters;
    protected $organization_id;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->organization_id = Auth::user()->organization_id;
    }

    /**
     * 🔥 Récupération des chauffeurs avec filtres
     */
    protected function getDrivers()
    {
        $query = Driver::query()
            ->with([
                'driverStatus',
                'user',
                'activeAssignment.vehicle'
            ])
            ->where('organization_id', $this->organization_id);

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
     * 📥 Téléchargement du fichier CSV
     */
    public function download()
    {
        // 1️⃣ Créer le writer CSV
        $csv = Writer::createFromString();

        // 2️⃣ Ajouter le BOM UTF-8 pour Excel
        $csv->setOutputBOM(Writer::BOM_UTF8);

        // 3️⃣ Insérer les en-têtes
        $csv->insertOne([
            'ID',
            'Matricule',
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
            'Date de naissance',
            'Statut',
            'N° Permis',
            'Catégorie',
            'Expiration Permis',
            'Date d\'embauche',
            'Véhicule actuel',
            'Immat. véhicule',
            'Compte utilisateur',
            'Archivé'
        ]);

        // 4️⃣ Récupérer les chauffeurs
        $drivers = $this->getDrivers();

        // 5️⃣ Insérer les données
        foreach ($drivers as $driver) {
            $csv->insertOne([
                $driver->id,
                $driver->employee_number ?? 'N/A',
                $driver->last_name ?? '',
                $driver->first_name ?? '',
                $driver->personal_email ?? 'N/A',
                $driver->personal_phone ?? 'N/A',
                $driver->birth_date ? $driver->birth_date->format('d/m/Y') : 'N/A',
                $driver->driverStatus ? $driver->driverStatus->name : 'N/A',
                $driver->license_number ?? 'N/A',
                $driver->license_category ?? 'N/A',
                $driver->license_expiry_date ? $driver->license_expiry_date->format('d/m/Y') : 'N/A',
                $driver->recruitment_date ? $driver->recruitment_date->format('d/m/Y') : 'N/A',
                $driver->activeAssignment && $driver->activeAssignment->vehicle
                    ? $driver->activeAssignment->vehicle->brand . ' ' . $driver->activeAssignment->vehicle->model
                    : 'Aucun',
                $driver->activeAssignment && $driver->activeAssignment->vehicle
                    ? $driver->activeAssignment->vehicle->registration_plate
                    : 'N/A',
                $driver->user ? $driver->user->email : 'Pas de compte',
                $driver->deleted_at ? 'Oui' : 'Non'
            ]);
        }

        // 6️⃣ Générer le nom du fichier
        $fileName = 'drivers_export_' . date('Y-m-d_H-i-s') . '.csv';

        // 7️⃣ Retourner la réponse HTTP
        return Response::make($csv->toString(), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
