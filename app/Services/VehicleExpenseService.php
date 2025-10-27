<?php

namespace App\Services;

use App\Models\VehicleExpense;
use App\Models\ExpenseGroup;
use App\Models\ExpenseAuditLog;
use App\Models\Vehicle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

/**
 * VehicleExpenseService - Service Layer pour la gestion des dépenses
 * 
 * @package App\Services
 * @version 1.0.0-Enterprise
 * @since 2025-10-27
 */
class VehicleExpenseService
{
    /**
     * Créer une nouvelle dépense
     * 
     * @param array $data
     * @return VehicleExpense
     */
    public function create(array $data): VehicleExpense
    {
        // Vérifier le budget si un groupe est spécifié
        if (isset($data['expense_group_id'])) {
            $group = ExpenseGroup::find($data['expense_group_id']);
            if ($group && !$group->canAddExpense($data['amount_ht'] * (1 + $data['tva_rate'] / 100))) {
                throw new \Exception('Budget dépassé pour ce groupe de dépenses.');
            }
        }

        // Créer la dépense
        $expense = VehicleExpense::create($data);

        // Mettre à jour le kilométrage du véhicule si nécessaire
        if ($expense->odometer_reading && $expense->vehicle) {
            $vehicle = $expense->vehicle;
            if ($expense->odometer_reading > $vehicle->current_mileage) {
                $vehicle->current_mileage = $expense->odometer_reading;
                $vehicle->save();
            }
        }

        return $expense;
    }

    /**
     * Mettre à jour une dépense
     * 
     * @param VehicleExpense $expense
     * @param array $data
     * @return VehicleExpense
     */
    public function update(VehicleExpense $expense, array $data): VehicleExpense
    {
        // Vérifier le budget si le groupe ou le montant change
        if (isset($data['expense_group_id']) || isset($data['amount_ht']) || isset($data['tva_rate'])) {
            $groupId = $data['expense_group_id'] ?? $expense->expense_group_id;
            $amountHt = $data['amount_ht'] ?? $expense->amount_ht;
            $tvaRate = $data['tva_rate'] ?? $expense->tva_rate;
            $newTotal = $amountHt * (1 + $tvaRate / 100);
            $oldTotal = $expense->total_ttc;
            
            if ($groupId) {
                $group = ExpenseGroup::find($groupId);
                if ($group && !$group->canAddExpense($newTotal - $oldTotal)) {
                    throw new \Exception('Budget dépassé pour ce groupe de dépenses.');
                }
            }
        }

        $expense->update($data);

        return $expense->fresh();
    }

    /**
     * Obtenir les alertes budgétaires
     * 
     * @param int $organizationId
     * @return Collection
     */
    public function getBudgetAlerts(int $organizationId): Collection
    {
        return ExpenseGroup::where('organization_id', $organizationId)
            ->active()
            ->currentYear()
            ->where(function ($query) {
                $query->nearThreshold()
                      ->orWhere->overBudget();
            })
            ->get()
            ->map(function ($group) {
                return [
                    'group' => $group->name,
                    'budget_usage' => $group->budget_usage_percentage,
                    'budget_remaining' => $group->budget_remaining,
                    'is_over_budget' => $group->is_over_budget,
                    'is_near_threshold' => $group->is_near_threshold,
                    'alert_type' => $group->is_over_budget ? 'danger' : 'warning',
                    'message' => $group->is_over_budget 
                        ? "Budget dépassé de " . number_format(abs($group->budget_remaining), 2) . " DZD"
                        : "Budget utilisé à {$group->budget_usage_percentage}%"
                ];
            });
    }

    /**
     * Obtenir les dépenses similaires pour comparaison
     * 
     * @param VehicleExpense $expense
     * @param int $limit
     * @return Collection
     */
    public function getSimilarExpenses(VehicleExpense $expense, int $limit = 5): Collection
    {
        return VehicleExpense::where('organization_id', $expense->organization_id)
            ->where('id', '!=', $expense->id)
            ->where('expense_category', $expense->expense_category)
            ->where('vehicle_id', $expense->vehicle_id)
            ->whereBetween('expense_date', [
                Carbon::parse($expense->expense_date)->subMonths(3),
                Carbon::parse($expense->expense_date)->addMonths(3)
            ])
            ->orderBy('expense_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Export des dépenses
     * 
     * @param int $organizationId
     * @param string $format
     * @param array $filters
     * @return mixed
     */
    public function export(int $organizationId, string $format, array $filters = [])
    {
        // Construire la requête
        $query = VehicleExpense::where('organization_id', $organizationId)
            ->with(['vehicle', 'supplier', 'driver', 'expenseGroup']);

        // Appliquer les filtres
        if (!empty($filters['date_from'])) {
            $query->where('expense_date', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->where('expense_date', '<=', $filters['date_to']);
        }
        
        if (!empty($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }
        
        if (!empty($filters['expense_group_id'])) {
            $query->where('expense_group_id', $filters['expense_group_id']);
        }
        
        if (!empty($filters['approval_status'])) {
            $query->where('approval_status', $filters['approval_status']);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        // Export selon le format
        switch ($format) {
            case 'csv':
                return $this->exportToCsv($expenses);
            case 'excel':
                return $this->exportToExcel($expenses);
            case 'pdf':
                return $this->exportToPdf($expenses);
            default:
                throw new \Exception('Format d\'export non supporté.');
        }
    }

    /**
     * Import des dépenses depuis fichier
     * 
     * @param UploadedFile $file
     * @param int $organizationId
     * @param int|null $expenseGroupId
     * @return array
     */
    public function import(UploadedFile $file, int $organizationId, ?int $expenseGroupId = null): array
    {
        $imported = 0;
        $errors = 0;
        $errorDetails = [];

        try {
            $data = Excel::toArray([], $file)[0]; // Première feuille
            
            foreach ($data as $index => $row) {
                // Passer l'en-tête
                if ($index === 0) continue;
                
                try {
                    // Mapper les colonnes
                    $expenseData = $this->mapImportRow($row, $organizationId, $expenseGroupId);
                    
                    // Créer la dépense
                    $this->create($expenseData);
                    $imported++;
                    
                } catch (\Exception $e) {
                    $errors++;
                    $errorDetails[] = [
                        'row' => $index + 1,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return [
                'success' => true,
                'imported' => $imported,
                'errors' => $errors,
                'error_details' => $errorDetails
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'imported' => 0,
                'errors' => 0
            ];
        }
    }

    /**
     * Calculer les statistiques mensuelles
     * 
     * @param int $organizationId
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getMonthlyStats(int $organizationId, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $expenses = VehicleExpense::where('organization_id', $organizationId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->get();

        return [
            'total_expenses' => $expenses->count(),
            'total_amount' => $expenses->sum('total_ttc'),
            'average_expense' => $expenses->avg('total_ttc'),
            'by_category' => $expenses->groupBy('expense_category')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_ttc'),
                    'percentage' => 0 // Calculé côté client
                ];
            }),
            'by_vehicle' => $expenses->groupBy('vehicle_id')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_ttc')
                ];
            }),
            'by_status' => [
                'pending' => $expenses->where('approval_status', 'pending_level1')->count(),
                'approved' => $expenses->where('approval_status', 'approved')->count(),
                'rejected' => $expenses->where('approval_status', 'rejected')->count(),
                'paid' => $expenses->where('payment_status', 'paid')->count()
            ],
            'top_expenses' => $expenses->sortByDesc('total_ttc')->take(5)->values()
        ];
    }

    /**
     * Détecter les dépenses anormales
     * 
     * @param int $organizationId
     * @return Collection
     */
    public function detectAnomalies(int $organizationId): Collection
    {
        $anomalies = collect();

        // Dépenses très élevées (> 3x la moyenne)
        $avgExpense = VehicleExpense::where('organization_id', $organizationId)
            ->whereMonth('expense_date', now()->month)
            ->avg('total_ttc');
        
        $highExpenses = VehicleExpense::where('organization_id', $organizationId)
            ->whereMonth('expense_date', now()->month)
            ->where('total_ttc', '>', $avgExpense * 3)
            ->get();
        
        foreach ($highExpenses as $expense) {
            $anomalies->push([
                'type' => 'high_amount',
                'expense' => $expense,
                'message' => 'Montant anormalement élevé',
                'severity' => 'warning'
            ]);
        }

        // Dépenses en doublon potentiel
        $duplicates = VehicleExpense::where('organization_id', $organizationId)
            ->select('vehicle_id', 'supplier_id', 'total_ttc', 'expense_date')
            ->groupBy('vehicle_id', 'supplier_id', 'total_ttc', 'expense_date')
            ->havingRaw('COUNT(*) > 1')
            ->get();
        
        foreach ($duplicates as $duplicate) {
            $expenses = VehicleExpense::where('organization_id', $organizationId)
                ->where('vehicle_id', $duplicate->vehicle_id)
                ->where('supplier_id', $duplicate->supplier_id)
                ->where('total_ttc', $duplicate->total_ttc)
                ->where('expense_date', $duplicate->expense_date)
                ->get();
            
            foreach ($expenses as $expense) {
                $anomalies->push([
                    'type' => 'duplicate',
                    'expense' => $expense,
                    'message' => 'Dépense potentiellement dupliquée',
                    'severity' => 'info'
                ]);
            }
        }

        // Consommation carburant anormale
        $fuelExpenses = VehicleExpense::where('organization_id', $organizationId)
            ->where('expense_category', 'carburant')
            ->whereMonth('expense_date', now()->month)
            ->with('vehicle')
            ->get();
        
        foreach ($fuelExpenses as $expense) {
            if ($expense->fuel_quantity && $expense->odometer_reading) {
                // Calculer la consommation
                $lastReading = VehicleExpense::where('vehicle_id', $expense->vehicle_id)
                    ->where('expense_category', 'carburant')
                    ->where('odometer_reading', '<', $expense->odometer_reading)
                    ->orderBy('odometer_reading', 'desc')
                    ->first();
                
                if ($lastReading) {
                    $distance = $expense->odometer_reading - $lastReading->odometer_reading;
                    if ($distance > 0) {
                        $consumption = ($expense->fuel_quantity / $distance) * 100; // L/100km
                        
                        // Alerte si consommation > 15L/100km
                        if ($consumption > 15) {
                            $anomalies->push([
                                'type' => 'high_consumption',
                                'expense' => $expense,
                                'message' => sprintf('Consommation élevée: %.1f L/100km', $consumption),
                                'severity' => 'warning'
                            ]);
                        }
                    }
                }
            }
        }

        return $anomalies;
    }

    // ====================================================================
    // MÉTHODES PRIVÉES
    // ====================================================================

    /**
     * Export CSV
     */
    private function exportToCsv($expenses)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="expenses_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($expenses) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($file, [
                'Date', 'Véhicule', 'Catégorie', 'Type', 'Description',
                'Montant HT', 'TVA', 'Total TTC', 'Fournisseur', 'Statut',
                'N° Facture', 'Méthode Paiement', 'Groupe'
            ]);
            
            // Données
            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->expense_date->format('d/m/Y'),
                    $expense->vehicle->registration_plate ?? '-',
                    $expense->expense_category,
                    $expense->expense_type,
                    $expense->description,
                    $expense->amount_ht,
                    $expense->tva_amount,
                    $expense->total_ttc,
                    $expense->supplier->name ?? '-',
                    $expense->approval_status,
                    $expense->invoice_number ?? '-',
                    $expense->payment_method ?? '-',
                    $expense->expenseGroup->name ?? '-'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Excel
     */
    private function exportToExcel($expenses)
    {
        // TODO: Implémenter l'export Excel avec Maatwebsite/Excel
        throw new \Exception('Export Excel à implémenter');
    }

    /**
     * Export PDF
     */
    private function exportToPdf($expenses)
    {
        $pdf = Pdf::loadView('exports.expenses-pdf', [
            'expenses' => $expenses,
            'organization' => auth()->user()->organization,
            'generated_at' => now()
        ]);

        return $pdf->download('expenses_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Mapper une ligne d'import
     */
    private function mapImportRow(array $row, int $organizationId, ?int $expenseGroupId): array
    {
        // Trouver le véhicule par immatriculation
        $vehicle = Vehicle::where('organization_id', $organizationId)
            ->where('registration_plate', trim($row[0] ?? ''))
            ->first();
        
        if (!$vehicle) {
            throw new \Exception("Véhicule non trouvé: " . ($row[0] ?? 'vide'));
        }

        return [
            'organization_id' => $organizationId,
            'vehicle_id' => $vehicle->id,
            'expense_group_id' => $expenseGroupId,
            'expense_category' => $row[1] ?? 'autre',
            'expense_type' => $row[2] ?? 'Non spécifié',
            'amount_ht' => floatval($row[3] ?? 0),
            'tva_rate' => floatval($row[4] ?? 19),
            'expense_date' => Carbon::parse($row[5] ?? now())->format('Y-m-d'),
            'description' => $row[6] ?? 'Import automatique',
            'invoice_number' => $row[7] ?? null,
            'odometer_reading' => intval($row[8] ?? 0) ?: null,
            'recorded_by' => auth()->id(),
            'requester_id' => auth()->id()
        ];
    }
}
