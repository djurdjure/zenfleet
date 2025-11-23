<?php

namespace App\Exports;

use App\Models\Driver;
use Illuminate\Support\Facades\Auth;
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

/**
 * 📊 EXPORT EXCEL DES CHAUFFEURS - ENTERPRISE-GRADE
 *
 * Implémente un export Excel ultra-professionnel avec:
 * - 🎨 Styles enterprise (header bleu, bordures, alternance de couleurs)
 * - 📋 En-têtes figés et filtres automatiques
 * - 🔍 Support des filtres avancés
 * - 📏 Largeurs de colonnes optimisées
 * - 🌐 Compatible multi-organisation
 *
 * @version 1.0 - Enterprise Export System
 * @since 2025-11-21
 */
class DriversExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnWidths, WithEvents
{
    protected $filters;
    protected $organization_id;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->organization_id = Auth::user()->organization_id;
    }

    /**
     * 🔥 Récupération des chauffeurs avec filtres intelligents
     */
    public function collection()
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
        // Sinon 'active' par défaut - seulement les non-supprimés

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
     * 📋 En-têtes des colonnes
     */
    public function headings(): array
    {
        return [
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
        ];
    }

    /**
     * 🗂️ Mapping des données pour chaque ligne
     */
    public function map($driver): array
    {
        return [
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
        ];
    }

    /**
     * 🎨 Styles enterprise pour le fichier Excel
     */
    public function styles(Worksheet $sheet)
    {
        // 1️⃣ Style de l'en-tête (ligne 1)
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6'], // Bleu enterprise
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // 2️⃣ Hauteur de ligne pour l'en-tête
        $sheet->getRowDimension(1)->setRowHeight(25);

        // 3️⃣ Bordures sur toutes les cellules avec données
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:P' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // 4️⃣ Alternance de couleurs pour les lignes (1 ligne sur 2)
        for ($i = 2; $i <= $highestRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':P' . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB'], // Gris très clair
                    ],
                ]);
            }
        }

        return [];
    }

    /**
     * 📏 Largeurs de colonnes optimisées
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 15,  // Matricule
            'C' => 15,  // Nom
            'D' => 15,  // Prénom
            'E' => 25,  // Email
            'F' => 15,  // Téléphone
            'G' => 15,  // Date de naissance
            'H' => 15,  // Statut
            'I' => 18,  // N° Permis
            'J' => 12,  // Catégorie
            'K' => 18,  // Expiration Permis
            'L' => 18,  // Date d'embauche
            'M' => 20,  // Véhicule actuel
            'N' => 15,  // Immat. véhicule
            'O' => 25,  // Compte utilisateur
            'P' => 10,  // Archivé
        ];
    }

    /**
     * 🎯 Événements post-génération (figer en-têtes, filtres automatiques)
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // 1️⃣ Figer la première ligne (en-têtes)
                $event->sheet->freezePane('A2');

                // 2️⃣ Ajouter des filtres automatiques
                $event->sheet->setAutoFilter('A1:P1');
            },
        ];
    }
}
