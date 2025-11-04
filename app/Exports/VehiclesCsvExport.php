<?php

namespace App\Exports;

use App\Models\Vehicle;
use League\Csv\Writer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

/**
 * 📄 Enterprise Vehicles CSV Export Class
 * 
 * Classe d'export CSV optimisée pour le module véhicules
 * Utilise League\CSV pour performance maximale
 * 
 * @package App\Exports
 * @version 1.0
 * @since 2025-11-03
 */
class VehiclesCsvExport
{
    protected $filters;
    protected $organization_id;

    /**
     * Constructeur avec filtres optionnels
     */
    public function __construct($filters = [])
    {
        $this->filters = $filters;
        $this->organization_id = Auth::user()->organization_id;
    }

    /**
     * Générer et télécharger le fichier CSV
     */
    public function download()
    {
        // Créer le writer CSV
        $csv = Writer::createFromString('');
        
        // UTF-8 BOM pour compatibilité Excel
        $csv->setOutputBOM(Writer::BOM_UTF8);
        
        // Ajouter les en-têtes
        $csv->insertOne($this->getHeaders());
        
        // Récupérer les véhicules
        $vehicles = $this->getVehicles();
        
        // Ajouter les données
        foreach ($vehicles as $vehicle) {
            $csv->insertOne($this->mapVehicleData($vehicle));
        }
        
        // Préparer la réponse
        $fileName = 'vehicles_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        return Response::make($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Récupérer les véhicules avec filtres
     */
    protected function getVehicles()
    {
        $query = Vehicle::query()
            ->where('organization_id', $this->organization_id)
            ->with([
                'vehicleType',
                'vehicleStatus',
                'fuelType',
                'transmissionType',
                'depot',
                'category',
                'assignments' => function($q) {
                    $q->where('is_active', true)
                      ->with('driver.user');
                }
            ]);

        // Appliquer les filtres
        if (isset($this->filters['archived'])) {
            if ($this->filters['archived'] === 'true') {
                $query->where('is_archived', true);
            } elseif ($this->filters['archived'] === 'false') {
                $query->where('is_archived', false);
            }
        } else {
            $query->where('is_archived', false);
        }

        if (isset($this->filters['status_id'])) {
            $query->where('status_id', $this->filters['status_id']);
        }

        if (isset($this->filters['vehicle_type_id'])) {
            $query->where('vehicle_type_id', $this->filters['vehicle_type_id']);
        }

        if (isset($this->filters['fuel_type_id'])) {
            $query->where('fuel_type_id', $this->filters['fuel_type_id']);
        }

        if (isset($this->filters['depot_id'])) {
            $query->where('depot_id', $this->filters['depot_id']);
        }

        if (isset($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('registration_plate', 'ilike', "%{$search}%")
                  ->orWhere('brand', 'ilike', "%{$search}%")
                  ->orWhere('model', 'ilike', "%{$search}%")
                  ->orWhere('vin', 'ilike', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * En-têtes CSV
     */
    protected function getHeaders(): array
    {
        return [
            'ID',
            'Immatriculation',
            'Marque',
            'Modèle',
            'Année',
            'Type',
            'Statut',
            'Carburant',
            'Transmission',
            'Kilométrage',
            'Couleur',
            'VIN',
            'Chauffeur',
            'Téléphone Chauffeur',
            'Dépôt',
            'Catégorie',
            'Date Acquisition',
            'Prix Acquisition',
            'Assurance Expire',
            'Contrôle Tech. Expire',
            'Prochaine Maintenance',
            'Archivé'
        ];
    }

    /**
     * Mapper les données d'un véhicule
     */
    protected function mapVehicleData($vehicle): array
    {
        $activeAssignment = $vehicle->assignments->first();
        $driver = $activeAssignment ? $activeAssignment->driver : null;
        $user = $driver ? $driver->user : null;

        return [
            $vehicle->id,
            $vehicle->registration_plate,
            $vehicle->brand,
            $vehicle->model,
            $vehicle->manufacturing_year,
            optional($vehicle->vehicleType)->name ?? 'N/A',
            optional($vehicle->vehicleStatus)->name ?? 'N/A',
            optional($vehicle->fuelType)->name ?? 'N/A',
            optional($vehicle->transmissionType)->name ?? 'N/A',
            $vehicle->current_mileage,
            $vehicle->color,
            $vehicle->vin,
            $user ? $user->name . ' ' . ($user->last_name ?? '') : 'Non affecté',
            $driver ? ($driver->phone ?? $user->phone ?? 'N/A') : 'N/A',
            optional($vehicle->depot)->name ?? 'N/A',
            optional($vehicle->category)->name ?? 'N/A',
            $vehicle->acquisition_date ? $vehicle->acquisition_date->format('d/m/Y') : 'N/A',
            $vehicle->acquisition_cost ?? 'N/A',
            $vehicle->insurance_expiry_date ? $vehicle->insurance_expiry_date->format('d/m/Y') : 'N/A',
            $vehicle->technical_control_expiry_date ? $vehicle->technical_control_expiry_date->format('d/m/Y') : 'N/A',
            $vehicle->next_maintenance_date ? $vehicle->next_maintenance_date->format('d/m/Y') : 'N/A',
            $vehicle->is_archived ? 'Oui' : 'Non'
        ];
    }
}
