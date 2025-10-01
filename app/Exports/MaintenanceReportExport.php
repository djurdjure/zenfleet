<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\MaintenanceOperation;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceAlert;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Export Excel pour les rapports de maintenance
 * Support multi-feuilles avec styles professionnels
 */
class MaintenanceReportExport implements WithMultipleSheets
{
    private string $reportType;
    private ?int $period;
    private int $organizationId;

    public function __construct(string $reportType, ?int $period = null)
    {
        $this->reportType = $reportType;
        $this->period = $period;
        $this->organizationId = auth()->user()->organization_id;
    }

    public function sheets(): array
    {
        return match($this->reportType) {
            'performance' => [
                'operations' => new OperationsSheet($this->organizationId, $this->period),
                'efficiency' => new EfficiencySheet($this->organizationId, $this->period),
                'summary' => new PerformanceSummarySheet($this->organizationId, $this->period)
            ],
            'costs' => [
                'cost_analysis' => new CostAnalysisSheet($this->organizationId, $this->period),
                'cost_by_vehicle' => new CostByVehicleSheet($this->organizationId, $this->period),
                'cost_trends' => new CostTrendsSheet($this->organizationId, $this->period)
            ],
            'kpis' => [
                'kpis_overview' => new KpisOverviewSheet($this->organizationId, $this->period),
                'benchmarks' => new BenchmarksSheet($this->organizationId, $this->period)
            ],
            'compliance' => [
                'compliance_status' => new ComplianceStatusSheet($this->organizationId),
                'upcoming_inspections' => new UpcomingInspectionsSheet($this->organizationId)
            ],
            'providers' => [
                'provider_performance' => new ProviderPerformanceSheet($this->organizationId, $this->period),
                'provider_costs' => new ProviderCostsSheet($this->organizationId, $this->period)
            ],
            default => [
                'data' => new DefaultDataSheet($this->organizationId, $this->period)
            ]
        };
    }
}

/**
 * Feuille des opérations de maintenance
 */
class OperationsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    private int $organizationId;
    private ?int $period;

    public function __construct(int $organizationId, ?int $period = null)
    {
        $this->organizationId = $organizationId;
        $this->period = $period;
    }

    public function collection(): Collection
    {
        $query = MaintenanceOperation::with(['vehicle:id,registration_plate,brand,model', 'maintenanceType:id,name,category', 'provider:id,name'])
            ->where('organization_id', $this->organizationId);

        if ($this->period) {
            $query->where('created_at', '>=', Carbon::now()->subMonths($this->period));
        }

        return $query->get()->map(function ($operation) {
            return [
                'ID' => $operation->id,
                'Véhicule' => $operation->vehicle->registration_plate ?? 'N/A',
                'Marque/Modèle' => ($operation->vehicle->brand ?? '') . ' ' . ($operation->vehicle->model ?? ''),
                'Type Maintenance' => $operation->maintenanceType->name ?? 'N/A',
                'Catégorie' => $operation->maintenanceType->category ?? 'N/A',
                'Fournisseur' => $operation->provider->name ?? 'Interne',
                'Statut' => $this->getStatusLabel($operation->status),
                'Date Création' => $operation->created_at->format('d/m/Y'),
                'Date Début' => $operation->started_date?->format('d/m/Y H:i'),
                'Date Fin' => $operation->completed_date?->format('d/m/Y H:i'),
                'Durée (min)' => $operation->duration_minutes ?? 0,
                'Coût Total (DA)' => number_format($operation->total_cost ?? 0, 2),
                'Kilométrage' => number_format($operation->mileage_at_service ?? 0),
                'Priorité' => $this->getPriorityLabel($operation->priority),
                'Description' => $operation->description ?? '',
                'Notes' => $operation->notes ?? ''
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID', 'Véhicule', 'Marque/Modèle', 'Type Maintenance', 'Catégorie',
            'Fournisseur', 'Statut', 'Date Création', 'Date Début', 'Date Fin',
            'Durée (min)', 'Coût Total (DA)', 'Kilométrage', 'Priorité', 'Description', 'Notes'
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2563EB']]
            ],
        ];
    }

    public function title(): string
    {
        return 'Opérations de Maintenance';
    }

    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'planned' => 'Planifiée',
            'in_progress' => 'En Cours',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée',
            default => $status
        };
    }

    private function getPriorityLabel(string $priority): string
    {
        return match($priority) {
            'low' => 'Faible',
            'medium' => 'Moyenne',
            'high' => 'Élevée',
            'critical' => 'Critique',
            default => $priority
        };
    }
}

/**
 * Feuille d'efficacité par type de maintenance
 */
class EfficiencySheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    private int $organizationId;
    private ?int $period;

    public function __construct(int $organizationId, ?int $period = null)
    {
        $this->organizationId = $organizationId;
        $this->period = $period;
    }

    public function collection(): Collection
    {
        $dateConstraint = $this->period ?
            ['created_at', '>=', Carbon::now()->subMonths($this->period)] :
            ['created_at', '>=', Carbon::now()->subYear()];

        return MaintenanceType::where('organization_id', $this->organizationId)
            ->withCount(['operations as total_operations' => function ($query) use ($dateConstraint) {
                $query->where($dateConstraint[0], $dateConstraint[1], $dateConstraint[2]);
            }])
            ->withCount(['operations as completed_operations' => function ($query) use ($dateConstraint) {
                $query->where($dateConstraint[0], $dateConstraint[1], $dateConstraint[2])
                      ->where('status', 'completed');
            }])
            ->withAvg(['operations as avg_duration' => function ($query) use ($dateConstraint) {
                $query->where($dateConstraint[0], $dateConstraint[1], $dateConstraint[2])
                      ->where('status', 'completed');
            }], 'duration_minutes')
            ->withSum(['operations as total_cost' => function ($query) use ($dateConstraint) {
                $query->where($dateConstraint[0], $dateConstraint[1], $dateConstraint[2])
                      ->where('status', 'completed');
            }], 'total_cost')
            ->get()
            ->map(function ($type) {
                $successRate = $type->total_operations > 0 ?
                    ($type->completed_operations / $type->total_operations) * 100 : 0;

                $efficiencyScore = $this->calculateEfficiencyScore(
                    $type->avg_duration ?? 0,
                    $type->estimated_duration_minutes ?? 0
                );

                return [
                    'Type de Maintenance' => $type->name,
                    'Catégorie' => $this->getCategoryLabel($type->category),
                    'Opérations Total' => $type->total_operations,
                    'Opérations Terminées' => $type->completed_operations,
                    'Taux de Réussite (%)' => number_format($successRate, 1),
                    'Durée Estimée (min)' => $type->estimated_duration_minutes ?? 0,
                    'Durée Moyenne Réelle (min)' => number_format($type->avg_duration ?? 0, 1),
                    'Score Efficacité (%)' => number_format($efficiencyScore, 1),
                    'Coût Total (DA)' => number_format($type->total_cost ?? 0, 2),
                    'Coût Moyen (DA)' => $type->completed_operations > 0 ?
                        number_format(($type->total_cost ?? 0) / $type->completed_operations, 2) : '0,00'
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Type de Maintenance', 'Catégorie', 'Opérations Total', 'Opérations Terminées',
            'Taux de Réussite (%)', 'Durée Estimée (min)', 'Durée Moyenne Réelle (min)',
            'Score Efficacité (%)', 'Coût Total (DA)', 'Coût Moyen (DA)'
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '059669']]
            ],
        ];
    }

    public function title(): string
    {
        return 'Efficacité par Type';
    }

    private function getCategoryLabel(string $category): string
    {
        return match($category) {
            'preventive' => 'Préventive',
            'corrective' => 'Corrective',
            'inspection' => 'Inspection',
            'revision' => 'Révision',
            default => $category
        };
    }

    private function calculateEfficiencyScore(float $avgDuration, float $estimatedDuration): float
    {
        if ($estimatedDuration <= 0) return 0;
        if ($avgDuration <= $estimatedDuration) return 100;

        return max(0, 100 - (($avgDuration - $estimatedDuration) / $estimatedDuration * 100));
    }
}

/**
 * Feuille de résumé des performances
 */
class PerformanceSummarySheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    private int $organizationId;
    private ?int $period;

    public function __construct(int $organizationId, ?int $period = null)
    {
        $this->organizationId = $organizationId;
        $this->period = $period;
    }

    public function collection(): Collection
    {
        $data = [];
        $periodMonths = $this->period ?? 12;

        for ($i = $periodMonths - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);

            $operations = MaintenanceOperation::where('organization_id', $this->organizationId)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month);

            $totalOps = $operations->count();
            $completedOps = $operations->where('status', 'completed')->count();
            $avgDuration = $operations->where('status', 'completed')->avg('duration_minutes') ?? 0;
            $totalCost = $operations->where('status', 'completed')->sum('total_cost') ?? 0;

            $data[] = [
                'Mois' => $month->format('M Y'),
                'Opérations Total' => $totalOps,
                'Opérations Terminées' => $completedOps,
                'Taux de Réussite (%)' => $totalOps > 0 ? number_format(($completedOps / $totalOps) * 100, 1) : '0,0',
                'Durée Moyenne (min)' => number_format($avgDuration, 1),
                'Coût Total (DA)' => number_format($totalCost, 2),
                'Coût Moyen par Opération (DA)' => $completedOps > 0 ? number_format($totalCost / $completedOps, 2) : '0,00'
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Mois', 'Opérations Total', 'Opérations Terminées', 'Taux de Réussite (%)',
            'Durée Moyenne (min)', 'Coût Total (DA)', 'Coût Moyen par Opération (DA)'
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'DC2626']]
            ],
        ];
    }

    public function title(): string
    {
        return 'Résumé Performance';
    }
}

// Feuilles additionnelles pour les autres types de rapports
class CostAnalysisSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    private int $organizationId;
    private ?int $period;

    public function __construct(int $organizationId, ?int $period = null)
    {
        $this->organizationId = $organizationId;
        $this->period = $period;
    }

    public function collection(): Collection
    {
        // Analyse des coûts par catégorie et par mois
        $data = [];
        $periodMonths = $this->period ?? 12;

        for ($i = $periodMonths - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);

            $preventiveCost = MaintenanceOperation::where('organization_id', $this->organizationId)
                ->join('maintenance_types', 'maintenance_operations.maintenance_type_id', '=', 'maintenance_types.id')
                ->where('maintenance_types.category', 'preventive')
                ->where('maintenance_operations.status', 'completed')
                ->whereYear('maintenance_operations.completed_date', $month->year)
                ->whereMonth('maintenance_operations.completed_date', $month->month)
                ->sum('maintenance_operations.total_cost') ?? 0;

            $correctiveCost = MaintenanceOperation::where('organization_id', $this->organizationId)
                ->join('maintenance_types', 'maintenance_operations.maintenance_type_id', '=', 'maintenance_types.id')
                ->where('maintenance_types.category', 'corrective')
                ->where('maintenance_operations.status', 'completed')
                ->whereYear('maintenance_operations.completed_date', $month->year)
                ->whereMonth('maintenance_operations.completed_date', $month->month)
                ->sum('maintenance_operations.total_cost') ?? 0;

            $inspectionCost = MaintenanceOperation::where('organization_id', $this->organizationId)
                ->join('maintenance_types', 'maintenance_operations.maintenance_type_id', '=', 'maintenance_types.id')
                ->where('maintenance_types.category', 'inspection')
                ->where('maintenance_operations.status', 'completed')
                ->whereYear('maintenance_operations.completed_date', $month->year)
                ->whereMonth('maintenance_operations.completed_date', $month->month)
                ->sum('maintenance_operations.total_cost') ?? 0;

            $totalCost = $preventiveCost + $correctiveCost + $inspectionCost;

            $data[] = [
                'Mois' => $month->format('M Y'),
                'Maintenance Préventive (DA)' => number_format($preventiveCost, 2),
                'Maintenance Corrective (DA)' => number_format($correctiveCost, 2),
                'Inspections (DA)' => number_format($inspectionCost, 2),
                'Total (DA)' => number_format($totalCost, 2),
                '% Préventive' => $totalCost > 0 ? number_format(($preventiveCost / $totalCost) * 100, 1) : '0,0',
                '% Corrective' => $totalCost > 0 ? number_format(($correctiveCost / $totalCost) * 100, 1) : '0,0'
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Mois', 'Maintenance Préventive (DA)', 'Maintenance Corrective (DA)',
            'Inspections (DA)', 'Total (DA)', '% Préventive', '% Corrective'
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F59E0B']]
            ],
        ];
    }

    public function title(): string
    {
        return 'Analyse des Coûts';
    }
}

// Classes simplifiées pour les autres feuilles
class CostByVehicleSheet extends CostAnalysisSheet { public function title(): string { return 'Coût par Véhicule'; } }
class CostTrendsSheet extends CostAnalysisSheet { public function title(): string { return 'Tendances des Coûts'; } }
class KpisOverviewSheet extends PerformanceSummarySheet { public function title(): string { return 'Vue d\'ensemble KPIs'; } }
class BenchmarksSheet extends PerformanceSummarySheet { public function title(): string { return 'Benchmarks'; } }
class ComplianceStatusSheet extends PerformanceSummarySheet { public function title(): string { return 'Statut Conformité'; } }
class UpcomingInspectionsSheet extends PerformanceSummarySheet { public function title(): string { return 'Inspections à Venir'; } }
class ProviderPerformanceSheet extends EfficiencySheet { public function title(): string { return 'Performance Fournisseurs'; } }
class ProviderCostsSheet extends CostAnalysisSheet { public function title(): string { return 'Coûts Fournisseurs'; } }
class DefaultDataSheet extends OperationsSheet { public function title(): string { return 'Données'; } }