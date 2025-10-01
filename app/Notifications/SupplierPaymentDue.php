<?php

namespace App\Notifications;

use App\Models\VehicleExpense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupplierPaymentDue extends Notification implements ShouldQueue
{
    use Queueable;

    public $expense;

    public function __construct(VehicleExpense $expense)
    {
        $this->expense = $expense;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $daysPastDue = now()->diffInDays($this->expense->payment_due_date, false);
        $status = $daysPastDue < 0 ? 'en retard' : 'à échéance';

        return (new MailMessage)
                    ->subject('Paiement ' . $status . ' - ' . $this->expense->supplier->company_name)
                    ->greeting('Bonjour ' . $notifiable->name . ',')
                    ->line('Un paiement fournisseur nécessite votre attention.')
                    ->line('**Détails du paiement:**')
                    ->line('- Fournisseur: ' . $this->expense->supplier->company_name)
                    ->line('- Véhicule: ' . $this->expense->vehicle->registration_plate)
                    ->line('- Montant: ' . number_format($this->expense->total_ttc, 2) . ' DA')
                    ->line('- Date d\'échéance: ' . $this->expense->payment_due_date->format('d/m/Y'))
                    ->when($daysPastDue < 0, function ($message) use ($daysPastDue) {
                        return $message->line('⚠️ **RETARD:** ' . abs($daysPastDue) . ' jour(s) de retard');
                    })
                    ->when($daysPastDue >= 0 && $daysPastDue <= 3, function ($message) use ($daysPastDue) {
                        return $message->line('📅 **ÉCHÉANCE:** ' . $daysPastDue . ' jour(s) restant(s)');
                    })
                    ->line('- Référence facture: ' . $this->expense->invoice_reference)
                    ->action('Traiter le paiement', route('admin.vehicle-expenses.show', $this->expense))
                    ->line('Merci de traiter ce paiement rapidement pour maintenir de bonnes relations fournisseurs.');
    }

    public function toDatabase($notifiable)
    {
        $daysPastDue = now()->diffInDays($this->expense->payment_due_date, false);
        $isOverdue = $daysPastDue < 0;

        return [
            'type' => 'supplier_payment_due',
            'title' => $isOverdue ? 'Paiement en retard' : 'Paiement à échéance',
            'message' => 'Paiement ' . ($isOverdue ? 'en retard' : 'à échéance') . ' pour ' . $this->expense->supplier->company_name . ' - ' . number_format($this->expense->total_ttc, 2) . ' DA',
            'expense_id' => $this->expense->id,
            'supplier_name' => $this->expense->supplier->company_name,
            'vehicle_plate' => $this->expense->vehicle->registration_plate,
            'amount' => $this->expense->total_ttc,
            'due_date' => $this->expense->payment_due_date->toDateString(),
            'days_past_due' => abs($daysPastDue),
            'is_overdue' => $isOverdue,
            'invoice_reference' => $this->expense->invoice_reference,
            'url' => route('admin.vehicle-expenses.show', $this->expense),
            'icon' => $isOverdue ? 'exclamation-circle' : 'clock',
            'color' => $isOverdue ? 'red' : 'orange',
            'priority' => $isOverdue ? 'high' : 'medium'
        ];
    }
}