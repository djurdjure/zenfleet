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
     * Récupérer les véhicules avec filtres - Enterprise-Grade
     *
     * Priorités de filtrage:
     * 1. Si des véhicules sont spécifiquement sélectionnés (param 'vehicles'), utiliser UNIQUEMENT ces IDs
     * 2. Sinon, appliquer TOUS les filtres disponibles (comme dans VehicleController::buildAdvancedQuery)
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
                    $q->where('status', 'active')
                      ->where('start_datetime', '<=', now())
                      ->where(function($query) {
                          $query->whereNull('end_datetime')
                                ->orWhere('end_datetime', '>=', now());
                      })
                      ->with('driver.user')
                      ->limit(1);
                }
            ]);

        // 🎯 PRIORITÉ 1: Si des véhicules spécifiques sont sélectionnés
        if (isset($this->filters['vehicles']) && !empty($this->filters['vehicles'])) {
            $vehicleIds = $this->parseVehicleIds($this->filters['vehicles']);

            if (!empty($vehicleIds)) {
                return $query->whereIn('id', $vehicleIds)->get();
            }
        }

        // 🔍 PRIORITÉ 2: Appliquer tous les filtres (mirror de VehicleController)

        // Filtre archivage
        if (isset($this->filters['archived'])) {
            if ($this->filters['archived'] === 'true') {
                $query->where('is_archived', true);
            } elseif ($this->filters['archived'] === 'all') {
                // Afficher tous les véhicules
            } else {
                $query->where('is_archived', false);
            }
        } else {
            $query->where('is_archived', false);
        }

        // Filtre recherche
        if (isset($this->filters['search']) && !empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('registration_plate', 'ilike', "%{$search}%")
                  ->orWhere('vin', 'ilike', "%{$search}%")
                  ->orWhere('brand', 'ilike', "%{$search}%")
                  ->orWhere('model', 'ilike', "%{$search}%");
            });
        }

        // Filtre statut
        if (isset($this->filters['status_id']) && !empty($this->filters['status_id'])) {
            $query->where('status_id', $this->filters['status_id']);
        }

        // Filtre type de véhicule
        if (isset($this->filters['vehicle_type_id']) && !empty($this->filters['vehicle_type_id'])) {
            $query->where('vehicle_type_id', $this->filters['vehicle_type_id']);
        }

        // Filtre type de carburant
        if (isset($this->filters['fuel_type_id']) && !empty($this->filters['fuel_type_id'])) {
            $query->where('fuel_type_id', $this->filters['fuel_type_id']);
        }

        // Filtre dépôt
        if (isset($this->filters['depot_id']) && !empty($this->filters['depot_id'])) {
            $query->where('depot_id', $this->filters['depot_id']);
        }

        // Filtres par date d'acquisition
        if (isset($this->filters['acquisition_from']) && !empty($this->filters['acquisition_from'])) {
            $query->where('acquisition_date', '>=', $this->filters['acquisition_from']);
        }

        if (isset($this->filters['acquisition_to']) && !empty($this->filters['acquisition_to'])) {
            $query->where('acquisition_date', '<=', $this->filters['acquisition_to']);
        }

        // Tri intelligent
        $sortBy = $this->filters['sort_by'] ?? 'created_at';
        $sortDirection = $this->filters['sort_direction'] ?? 'desc';

        $allowedSorts = [
            'registration_plate', 'brand', 'model', 'manufacturing_year',
            'acquisition_date', 'current_mileage', 'created_at'
        ];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->get();
    }

    /**
     * Parser les IDs de véhicules depuis différents formats
     * Gère: "1,2,3" | "[1,2,3]" | [1,2,3]
     */
    private function parseVehicleIds($vehicles)
    {
        // Si c'est déjà un tableau
        if (is_array($vehicles)) {
            return array_filter(array_map('intval', $vehicles));
        }

        // Si c'est une chaîne JSON
        if (is_string($vehicles) && (str_starts_with($vehicles, '[') || str_starts_with($vehicles, '{'))) {
            $decoded = json_decode($vehicles, true);
            if (is_array($decoded)) {
                return array_filter(array_map('intval', $decoded));
            }
        }

        // Si c'est une chaîne séparée par des virgules
        if (is_string($vehicles)) {
            $ids = explode(',', $vehicles);
            return array_filter(array_map('intval', $ids));
        }

        return [];
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
     * 
     * 🔧 FIX: Utilise directement les champs du modèle Driver (first_name, last_name, personal_phone)
     * au lieu de passer par la relation User qui peut être null.
     */
    protected function mapVehicleData($vehicle): array
    {
        $activeAssignment = $vehicle->assignments->first();
        $driver = $activeAssignment ? $activeAssignment->driver : null;

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
            $driver ? $driver->first_name . ' ' . $driver->last_name : 'Non affecté',
            $driver ? ($driver->personal_phone ?? 'N/A') : 'N/A',
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
