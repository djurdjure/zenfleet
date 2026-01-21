<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\VehicleExpense;
use App\Notifications\SupplierPaymentDue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

class CheckPaymentsDue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        Log::info('Début de la vérification des paiements dus');

        // Récupérer les dépenses approuvées avec paiement dû dans les 7 prochains jours ou en retard
        $expenses = VehicleExpense::with(['supplier', 'vehicle', 'organization'])
            ->where('approval_status', VehicleExpense::APPROVAL_APPROVED)
            ->where('payment_status', '!=', VehicleExpense::PAYMENT_PAID)
            ->whereNotNull('payment_due_date')
            ->where('payment_due_date', '<=', now()->addDays(7))
            ->get();

        $notificationsAvailable = Schema::hasTable('notifications');

        foreach ($expenses as $expense) {
            try {
                $daysPastDue = now()->diffInDays($expense->payment_due_date, false);
                $isOverdue = $daysPastDue < 0;

                // Déterminer la fréquence de notification
                $shouldNotify = false;

                if ($isOverdue) {
                    // Pour les paiements en retard : notification quotidienne
                    $lastNotification = false;

                    if ($notificationsAvailable) {
                        $lastNotification = \DB::table('notifications')
                            ->where('type', SupplierPaymentDue::class)
                            ->where('data->expense_id', $expense->id)
                            ->where('created_at', '>=', now()->subDay())
                            ->exists();
                    }

                    $shouldNotify = !$lastNotification;
                } else {
                    // Pour les paiements à échéance : notification selon les seuils
                    if ($daysPastDue <= 1) {
                        // J-1 et J : notification quotidienne
                        $lastNotification = false;

                        if ($notificationsAvailable) {
                            $lastNotification = \DB::table('notifications')
                                ->where('type', SupplierPaymentDue::class)
                                ->where('data->expense_id', $expense->id)
                                ->where('created_at', '>=', now()->subDay())
                                ->exists();
                        }

                        $shouldNotify = !$lastNotification;
                    } elseif ($daysPastDue <= 3) {
                        // J-3 à J-2 : notification unique
                        $existingNotification = false;

                        if ($notificationsAvailable) {
                            $existingNotification = \DB::table('notifications')
                                ->where('type', SupplierPaymentDue::class)
                                ->where('data->expense_id', $expense->id)
                                ->exists();
                        }

                        $shouldNotify = !$existingNotification;
                    } elseif ($daysPastDue == 7) {
                        // J-7 : notification unique
                        $weekNotification = false;

                        if ($notificationsAvailable) {
                            $weekNotification = \DB::table('notifications')
                                ->where('type', SupplierPaymentDue::class)
                                ->where('data->expense_id', $expense->id)
                                ->where('created_at', '>=', now()->subDays(2))
                                ->exists();
                        }

                        $shouldNotify = !$weekNotification;
                    }
                }

                if ($shouldNotify) {
                    app(PermissionRegistrar::class)->setPermissionsTeamId($expense->organization_id);

                    // Envoyer la notification aux gestionnaires financiers et administrateurs
                    $managers = User::where('organization_id', $expense->organization_id)
                                  ->whereHas('roles', function ($query) {
                                      $query->whereIn('name', [
                                          'Super Admin', 'Admin', 'Gestionnaire Flotte',
                                          'Responsable Financier'
                                      ]);
                                  })
                                  ->get();

                    foreach ($managers as $manager) {
                        $manager->notify(new SupplierPaymentDue($expense));
                    }

                    Log::info("Notification paiement envoyée", [
                        'expense_id' => $expense->id,
                        'supplier' => $expense->supplier->company_name,
                        'vehicle' => $expense->vehicle->registration_plate,
                        'amount' => $expense->total_ttc,
                        'due_date' => $expense->payment_due_date->toDateString(),
                        'days_past_due' => abs($daysPastDue),
                        'is_overdue' => $isOverdue,
                        'managers_notified' => $managers->count()
                    ]);
                }

            } catch (\Exception $e) {
                Log::error("Erreur lors de la vérification du paiement", [
                    'expense_id' => $expense->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Fin de la vérification des paiements dus', [
            'expenses_checked' => $expenses->count()
        ]);
    }
}
