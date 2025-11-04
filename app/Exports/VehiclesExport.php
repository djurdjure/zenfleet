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
     * Collection des véhicules à exporter
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
                'assignments' => function($q) {
                    $q->where('status', 'active')
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
     */
    public function map($vehicle): array
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
            $user ? $user->email : 'N/A',
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
            AfterSheet::class => function(AfterSheet $event) {
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
