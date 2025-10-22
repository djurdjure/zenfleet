<?php

namespace App\Exports;

use App\Models\Driver;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * 📊 Export Excel des Chauffeurs Archivés - ULTRA PRO
 * 
 * Fonctionnalités :
 * - Export avec en-têtes stylés
 * - Mapping des données avec formatage
 * - Largeurs de colonnes optimisées
 * - Style enterprise-grade
 */
class ArchivedDriversExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Collection des chauffeurs archivés avec filtres
     */
    public function collection()
    {
        $query = Driver::onlyTrashed()
            ->with(['driverStatus', 'user', 'organization']);

        // Filtrage par organisation pour non-Super Admin
        if (!auth()->user()->hasRole('Super Admin')) {
            $query->where('organization_id', auth()->user()->organization_id);
        }

        // Filtre par date d'archivage (début)
        if (!empty($this->filters['archived_from'])) {
            $query->whereDate('deleted_at', '>=', $this->filters['archived_from']);
        }

        // Filtre par date d'archivage (fin)
        if (!empty($this->filters['archived_to'])) {
            $query->whereDate('deleted_at', '<=', $this->filters['archived_to']);
        }

        // Filtre par statut
        if (!empty($this->filters['status_id'])) {
            $query->where('status_id', $this->filters['status_id']);
        }

        // Filtre par recherche (nom, prénom, matricule)
        if (!empty($this->filters['search'])) {
            $search = strtolower($this->filters['search']);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(employee_number) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->orderBy('deleted_at', 'desc')->get();
    }

    /**
     * En-têtes de colonnes
     */
    public function headings(): array
    {
        return [
            'Matricule',
            'Prénom',
            'Nom',
            'Email',
            'Téléphone',
            'Statut',
            'Catégories Permis',
            'Date Recrutement',
            'Date Archivage',
            'Archivé Par',
            'Organisation',
            'Raison Archivage',
        ];
    }

    /**
     * Mapping des données pour chaque ligne
     */
    public function map($driver): array
    {
        return [
            $driver->employee_number ?? 'N/A',
            $driver->first_name ?? '',
            $driver->last_name ?? '',
            $driver->personal_email ?? $driver->email ?? '',
            $driver->personal_phone ?? '',
            $driver->driverStatus?->name ?? 'N/A',
            $driver->license_categories ? implode(', ', $driver->license_categories) : ($driver->license_category ?? 'N/A'),
            $driver->recruitment_date ? $driver->recruitment_date->format('d/m/Y') : '',
            $driver->deleted_at ? $driver->deleted_at->format('d/m/Y H:i') : '',
            auth()->user()->name, // Qui a exporté
            $driver->organization?->name ?? 'N/A',
            'Archivage manuel', // Raison par défaut
        ];
    }

    /**
     * Styles du fichier Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Style de l'en-tête (ligne 1)
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F97316'], // Orange - Amber-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Hauteur de l'en-tête
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Alignement des données
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:L' . $lastRow)->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Bordures pour toutes les cellules avec données
        $sheet->getStyle('A1:L' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        return [];
    }

    /**
     * Largeurs des colonnes
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Matricule
            'B' => 20,  // Prénom
            'C' => 20,  // Nom
            'D' => 30,  // Email
            'E' => 15,  // Téléphone
            'F' => 15,  // Statut
            'G' => 25,  // Catégories Permis
            'H' => 15,  // Date Recrutement
            'I' => 20,  // Date Archivage
            'J' => 20,  // Archivé Par
            'K' => 25,  // Organisation
            'L' => 25,  // Raison
        ];
    }

    /**
     * Titre de l'onglet Excel
     */
    public function title(): string
    {
        return 'Chauffeurs Archivés';
    }
}
