<?php

namespace App\Exports;

use App\Models\VehicleMileageReading;
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
use Carbon\Carbon;

/**
 * 📊 Enterprise Mileage Readings Excel Export Class
 * 
 * Classe d'export Excel professionnelle avec styles avancés
 * Utilise Maatwebsite/Excel pour génération haute performance
 * 
 * @package App\Exports
 * @version 1.0
 * @since 2025-12-28
 */
class MileageReadingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnWidths, WithEvents
{
    protected $filters;
    protected $organization_id;

    /**
     * Constructeur avec filtres optionnels
     */
    public function __construct($filters = [])
    {
        $this->filters = $filters;
        $this->organization_id = optional(Auth::user())->organization_id;
    }

    /**
     * Collection des relevés à exporter - Enterprise-Grade
     */
    public function collection()
    {
        $query = VehicleMileageReading::forOrganization($this->organization_id)
            ->select('vehicle_mileage_readings.*')
            ->with(['vehicle', 'recordedBy'])
            ->withPreviousMileage(); // Essential for 'Diff' column calculation

        // Filtre: Véhicule
        if (!empty($this->filters['vehicle_id'])) {
            $query->where('vehicle_id', $this->filters['vehicle_id']);
        }

        // Filtre: Méthode d'enregistrement
        if (!empty($this->filters['method'])) {
            $query->where('recording_method', $this->filters['method']);
        }

        // Filtre: Période (date début)
        if (!empty($this->filters['date_from'])) {
            $query->where('recorded_at', '>=', Carbon::parse($this->filters['date_from'])->startOfDay());
        }

        // Filtre: Période (date fin)
        if (!empty($this->filters['date_to'])) {
            $query->where('recorded_at', '<=', Carbon::parse($this->filters['date_to'])->endOfDay());
        }

        // Filtre: Utilisateur enregistreur
        if (!empty($this->filters['recorded_by'])) {
            $query->where('recorded_by_id', $this->filters['recorded_by']);
        }

        // Filtre: Recherche textuelle
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('vehicle', function ($vehicleQuery) use ($search) {
                    $vehicleQuery->where('registration_plate', 'ILIKE', "%{$search}%")
                        ->orWhere('brand', 'ILIKE', "%{$search}%")
                        ->orWhere('model', 'ILIKE', "%{$search}%");
                })
                    ->orWhere('mileage', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'ILIKE', "%{$search}%");
            });
        }

        // Filtre: Kilométrage minimum
        if (!empty($this->filters['mileage_min'])) {
            $query->where('mileage', '>=', $this->filters['mileage_min']);
        }

        // Filtre: Kilométrage maximum
        if (!empty($this->filters['mileage_max'])) {
            $query->where('mileage', '<=', $this->filters['mileage_max']);
        }

        // Tri
        $sortField = $this->filters['sort_by'] ?? 'recorded_at';
        $sortDirection = $this->filters['sort_direction'] ?? 'desc';

        if ($sortField === 'vehicle') {
            $query->join('vehicles', 'vehicle_mileage_readings.vehicle_id', '=', 'vehicles.id')
                ->select('vehicle_mileage_readings.*')
                ->orderBy('vehicles.registration_plate', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
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
            'Véhicule',
            'Marque / Modèle',
            'Date Relevé',
            'Heure',
            'Kilométrage',
            'Diff. (km)',
            'Méthode',
            'Enregistré par',
            'Notes',
            'Date Création (Système)'
        ];
    }

    /**
     * Mapper les données d'un relevé
     */
    public function map($reading): array
    {
        // Calcul différence (approximatif si pas chargé, mais idéalement on devrait utiliser le scopeWithPreviousMileage)
        // Pour l'export Excel, on recalculera la différence si possible ou on laissera vide si complexe sans scope
        $diff = null;
        // NOTE: Pour avoir la diff précise, il faudrait charger avec le scope. 
        // Mais VehicleMileageReading::forOrganization() est un scope. On peut ajouter scopeWithPreviousMileage() dans la query.

        return [
            $reading->id,
            $reading->vehicle ? $reading->vehicle->registration_plate : 'Véhicule Inconnu',
            $reading->vehicle ? ($reading->vehicle->brand . ' ' . $reading->vehicle->model) : 'N/A',
            $reading->recorded_at ? $reading->recorded_at->format('d/m/Y') : 'N/A',
            $reading->recorded_at ? $reading->recorded_at->format('H:i') : 'N/A',
            number_format($reading->mileage, 0, ',', ' '),
            // Différence (nécessiterait d'inclure le scopeWithPreviousMileage dans la collection, on va le faire)
            $reading->previous_mileage ? number_format($reading->mileage - $reading->previous_mileage, 0, ',', ' ') : 'Initial',
            $reading->recording_method === 'manual' ? 'Manuel' : 'Automatique',
            $reading->recordedBy?->name ?? 'Système',
            $reading->notes,
            $reading->created_at ? $reading->created_at->format('d/m/Y H:i') : 'N/A'
        ];
    }

    /**
     * Largeurs de colonnes personnalisées
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 15,  // Véhicule
            'C' => 25,  // Marque/Modèle
            'D' => 12,  // Date
            'E' => 8,   // Heure
            'F' => 15,  // Kilométrage
            'G' => 12,  // Différence
            'H' => 12,  // Méthode
            'I' => 20,  // Enregistré par
            'J' => 30,  // Notes
            'K' => 20,  // Date Création
        ];
    }

    /**
     * Styles pour la feuille Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Style pour l'en-tête
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6'] // Bleu ZenFleet
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Bordures pour tout le tableau
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:K{$lastRow}")->applyFromArray([
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
                $sheet->getStyle("A{$i}:K{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB']
                    ]
                ]);
            }
        }

        // Alignement droite pour kilométrage et diff
        $sheet->getStyle("F2:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }

    /**
     * Events pour personnalisation avancée
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Figer la première ligne
                $event->sheet->freezePane('A2');
                $event->sheet->setAutoFilter('A1:K1');

                // Ajouter un titre
                $event->sheet->insertNewRowBefore(1, 2);
                $event->sheet->mergeCells('A1:K1');

                $orgName = optional(Auth::user()->organization)->name ?? 'ZenFleet';
                $title = 'Export Relevés Kilométriques - ' . $orgName . ' - ' . date('d/m/Y H:i');

                $event->sheet->setCellValue('A1', $title);
                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(30);
            }
        ];
    }
}
