<?php

namespace App\Notifications;

use App\Models\VehicleExpense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpenseApprovalRequired extends Notification implements ShouldQueue
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
        return (new MailMessage)
                    ->subject('Approbation de dépense requise - ' . $this->expense->vehicle->registration_plate)
                    ->greeting('Bonjour ' . $notifiable->name . ',')
                    ->line('Une dépense nécessite votre approbation.')
                    ->line('**Détails de la dépense:**')
                    ->line('- Véhicule: ' . $this->expense->vehicle->registration_plate)
                    ->line('- Catégorie: ' . $this->expense->category_label)
                    ->line('- Type: ' . $this->expense->expense_type)
                    ->line('- Montant: ' . number_format($this->expense->total_ttc, 2) . ' DA')
                    ->line('- Date: ' . $this->expense->expense_date->format('d/m/Y'))
                    ->line('- Enregistrée par: ' . $this->expense->recordedBy->name)
                    ->when($this->expense->supplier, function ($message) {
                        return $message->line('- Fournisseur: ' . $this->expense->supplier->company_name);
                    })
                    ->line('- Description: ' . substr($this->expense->description, 0, 150) . '...')
                    ->action('Approuver/Rejeter', route('admin.vehicle-expenses.show', $this->expense))
                    ->line('Merci de traiter cette demande dans les plus brefs délais.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'expense_approval_required',
            'title' => 'Approbation de dépense requise',
            'message' => 'Dépense de ' . number_format($this->expense->total_ttc, 2) . ' DA pour ' . $this->expense->vehicle->registration_plate . ' en attente d\'approbation.',
            'expense_id' => $this->expense->id,
            'vehicle_plate' => $this->expense->vehicle->registration_plate,
            'category' => $this->expense->expense_category,
            'amount' => $this->expense->total_ttc,
            'expense_date' => $this->expense->expense_date->toDateString(),
            'recorded_by' => $this->expense->recordedBy->name,
            'supplier_name' => $this->expense->supplier?->company_name,
            'url' => route('admin.vehicle-expenses.show', $this->expense),
            'icon' => 'currency-dollar',
            'color' => 'blue',
            'priority' => 'medium'
        ];
    }
}