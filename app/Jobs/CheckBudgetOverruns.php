<?php

namespace App\Jobs;

use App\Models\ExpenseBudget;
use App\Models\User;
use App\Notifications\BudgetOverrunAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

class CheckBudgetOverruns implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        Log::info('Début de la vérification des dépassements de budget');

        // Récupérer tous les budgets actifs
        $budgets = ExpenseBudget::active()->get();

        foreach ($budgets as $budget) {
            try {
                // Recalculer les montants dépensés
                $budget->recalculateSpentAmount();

                $utilizationPercentage = $budget->getUtilizationPercentage();

                // Déterminer le type d'alerte
                $alertType = null;

                if ($utilizationPercentage >= 100) {
                    $alertType = 'overrun';
                } elseif ($utilizationPercentage >= $budget->critical_threshold) {
                    $alertType = 'critical';
                } elseif ($utilizationPercentage >= $budget->warning_threshold) {
                    $alertType = 'warning';
                }

                if ($alertType) {
                    // Vérifier si une alerte similaire n'a pas déjà été envoyée récemment
                    $recentAlert = false;

                    if (Schema::hasTable('notifications')) {
                        $recentAlert = \DB::table('notifications')
                            ->where('type', BudgetOverrunAlert::class)
                            ->where('data->budget_id', $budget->id)
                            ->where('data->alert_type', $alertType)
                            ->where('created_at', '>=', now()->subHours(24))
                            ->exists();
                    }

                    if (!$recentAlert) {
                        app(PermissionRegistrar::class)->setPermissionsTeamId($budget->organization_id);

                        // Envoyer l'alerte aux gestionnaires de l'organisation
                        $managers = User::where('organization_id', $budget->organization_id)
                                      ->whereHas('roles', function ($query) {
                                          $query->whereIn('name', [
                                              'Super Admin', 'Admin', 'Gestionnaire Flotte'
                                          ]);
                                      })
                                      ->get();

                        foreach ($managers as $manager) {
                            $manager->notify(new BudgetOverrunAlert($budget, $alertType));
                        }

                        Log::info("Alerte budget envoyée", [
                            'budget_id' => $budget->id,
                            'organization_id' => $budget->organization_id,
                            'alert_type' => $alertType,
                            'utilization' => $utilizationPercentage,
                            'managers_notified' => $managers->count()
                        ]);
                    } else {
                        Log::debug("Alerte budget récente ignorée", [
                            'budget_id' => $budget->id,
                            'alert_type' => $alertType
                        ]);
                    }
                }

            } catch (\Exception $e) {
                Log::error("Erreur lors de la vérification du budget", [
                    'budget_id' => $budget->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Fin de la vérification des dépassements de budget');
    }
}
