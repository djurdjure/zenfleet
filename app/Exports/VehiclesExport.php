<?php

namespace App\Exports;

use App\Models\Vehicle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\Auth;

/**
 * 📊 Enterprise Vehicles Excel Export Class
 * 
 * Classe d'export Excel professionnelle avec styles avancés
 * Utilise Maatwebsite/Excel pour génération haute performance
 * 
 * @package App\Exports
 * @version 1.0
 * @since 2025-11-03
 */
class VehiclesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnWidths, WithEvents
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
     * Collection des véhicules à exporter - Enterprise-Grade
     *
     * Priorités de filtrage:
     * 1. Si des véhicules sont spécifiquement sélectionnés (param 'vehicles'), utiliser UNIQUEMENT ces IDs
     * 2. Sinon, appliquer TOUS les filtres disponibles (comme dans VehicleController::buildAdvancedQuery)
     */
    public function collection()
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
                'assignments' => function ($q) {
                    $q->where('status', 'active')
                        ->where('start_datetime', '<=', now())
                        ->where(function ($query) {
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
            $query->where(function ($q) use ($search) {
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
            'registration_plate',
            'brand',
            'model',
            'manufacturing_year',
            'acquisition_date',
            'current_mileage',
            'created_at'
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
     * En-têtes des colonnes
     */
    public function headings(): array
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
            'Téléphone',
            'Email',
            'Dépôt',
            'Catégorie',
            'Date Acquisition',
            'Prix Acquisition',
            'Assurance Expire',
            'Contrôle Tech.',
            'Archivé'
        ];
    }

    /**
     * Mapper les données d'un véhicule
     * 
     * 🔧 FIX: Utilise directement les champs du modèle Driver (first_name, last_name, personal_phone, email)
     * au lieu de passer par la relation User qui peut être null.
     */
    public function map($vehicle): array
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
            $driver ? ($driver->email ?? $driver->personal_email ?? 'N/A') : 'N/A',
            optional($vehicle->depot)->name ?? 'N/A',
            optional($vehicle->category)->name ?? 'N/A',
            $vehicle->acquisition_date ? $vehicle->acquisition_date->format('d/m/Y') : 'N/A',
            $vehicle->acquisition_cost ?? 'N/A',
            $vehicle->insurance_expiry_date ? $vehicle->insurance_expiry_date->format('d/m/Y') : 'N/A',
            $vehicle->technical_control_expiry_date ? $vehicle->technical_control_expiry_date->format('d/m/Y') : 'N/A',
            $vehicle->is_archived ? 'Oui' : 'Non'
        ];
    }

    /**
     * Largeurs de colonnes personnalisées
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 15,  // Immatriculation
            'C' => 15,  // Marque
            'D' => 15,  // Modèle
            'E' => 10,  // Année
            'F' => 12,  // Type
            'G' => 12,  // Statut
            'H' => 12,  // Carburant
            'I' => 15,  // Transmission
            'J' => 15,  // Kilométrage
            'K' => 12,  // Couleur
            'L' => 20,  // VIN
            'M' => 25,  // Chauffeur
            'N' => 15,  // Téléphone
            'O' => 25,  // Email
            'P' => 15,  // Dépôt
            'Q' => 15,  // Catégorie
            'R' => 15,  // Date Acquisition
            'S' => 15,  // Prix Acquisition
            'T' => 15,  // Assurance
            'U' => 15,  // Contrôle Tech
            'V' => 10,  // Archivé
        ];
    }

    /**
     * Styles pour la feuille Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Style pour l'en-tête
        $sheet->getStyle('A1:V1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Bordures pour tout le tableau
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:V{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB']
                ]
            ]
        ]);

        // Alternance de couleurs pour les lignes
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:V{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB']
                    ]
                ]);
            }
        }

        return [];
    }

    /**
     * Events pour personnalisation avancée
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Figer la première ligne (en-tête)
                $event->sheet->freezePane('A2');

                // Activer les filtres automatiques
                $event->sheet->setAutoFilter('A1:V1');

                // Ajuster la hauteur de la ligne d'en-tête
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(25);

                // Ajouter un titre au-dessus du tableau
                $event->sheet->insertNewRowBefore(1, 2);
                $event->sheet->mergeCells('A1:V1');
                $event->sheet->setCellValue('A1', 'Export Véhicules - ' . Auth::user()->organization->name . ' - ' . date('d/m/Y H:i'));
                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);
            }
        ];
    }
}
