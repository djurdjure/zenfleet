<?php

namespace App\Notifications;

use App\Models\ExpenseBudget;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BudgetOverrunAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public $budget;
    public $alertType; // 'warning', 'critical', 'overrun'

    public function __construct(ExpenseBudget $budget, string $alertType = 'warning')
    {
        $this->budget = $budget;
        $this->alertType = $alertType;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $subject = match($this->alertType) {
            'warning' => 'Alerte budget - Seuil d\'avertissement atteint',
            'critical' => 'Alerte budget critique - Action requise',
            'overrun' => 'Dépassement de budget - Intervention nécessaire',
            default => 'Alerte budget'
        };

        $message = match($this->alertType) {
            'warning' => 'Le budget a atteint le seuil d\'avertissement (' . $this->budget->warning_threshold . '%).',
            'critical' => 'Le budget a atteint le seuil critique (' . $this->budget->critical_threshold . '%).',
            'overrun' => 'Le budget a été dépassé !',
            default => 'Une situation nécessite votre attention.'
        };

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting('Bonjour ' . $notifiable->name . ',')
                    ->line($message)
                    ->line('**Détails du budget:**')
                    ->line('- Période: ' . $this->budget->period_label)
                    ->line('- Scope: ' . $this->budget->scope_description)
                    ->line('- Budget alloué: ' . number_format($this->budget->budgeted_amount, 2) . ' DA')
                    ->line('- Montant dépensé: ' . number_format($this->budget->spent_amount, 2) . ' DA')
                    ->line('- Utilisation: ' . number_format($this->budget->utilization_percentage, 1) . '%')
                    ->line('- Reste disponible: ' . number_format($this->budget->remaining_amount, 2) . ' DA')
                    ->when($this->alertType === 'overrun', function ($mail) {
                        return $mail->line('⚠️ **ACTION REQUISE:** Veuillez réviser les dépenses ou ajuster le budget.');
                    })
                    ->action('Voir le budget', route('admin.expense-budgets.show', $this->budget))
                    ->line('Surveillez régulièrement vos budgets pour une gestion optimale de votre flotte.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'budget_alert',
            'alert_type' => $this->alertType,
            'title' => match($this->alertType) {
                'warning' => 'Alerte budget - Seuil atteint',
                'critical' => 'Budget critique',
                'overrun' => 'Budget dépassé',
                default => 'Alerte budget'
            },
            'message' => 'Budget ' . $this->budget->scope_description . ' : ' . number_format($this->budget->utilization_percentage, 1) . '% utilisé',
            'budget_id' => $this->budget->id,
            'period' => $this->budget->period_label,
            'utilization_percentage' => $this->budget->utilization_percentage,
            'budgeted_amount' => $this->budget->budgeted_amount,
            'spent_amount' => $this->budget->spent_amount,
            'remaining_amount' => $this->budget->remaining_amount,
            'url' => route('admin.expense-budgets.show', $this->budget),
            'icon' => match($this->alertType) {
                'warning' => 'exclamation-triangle',
                'critical' => 'exclamation-circle',
                'overrun' => 'x-circle',
                default => 'bell'
            },
            'color' => match($this->alertType) {
                'warning' => 'yellow',
                'critical' => 'orange',
                'overrun' => 'red',
                default => 'blue'
            },
            'priority' => match($this->alertType) {
                'warning' => 'medium',
                'critical' => 'high',
                'overrun' => 'urgent',
                default => 'low'
            }
        ];
    }
}