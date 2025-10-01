<?php

namespace App\Notifications;

use App\Models\RepairRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RepairRequestValidated extends Notification implements ShouldQueue
{
    use Queueable;

    public $repairRequest;

    public function __construct(RepairRequest $repairRequest)
    {
        $this->repairRequest = $repairRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Demande de réparation validée - Travaux autorisés')
                    ->greeting('Excellente nouvelle ' . $notifiable->name . ' !')
                    ->line('Votre demande de réparation pour le véhicule ' . $this->repairRequest->vehicle->registration_plate . ' a été entièrement validée.')
                    ->line('**Détails de la demande:**')
                    ->line('- Véhicule: ' . $this->repairRequest->vehicle->registration_plate)
                    ->line('- Priorité: ' . $this->repairRequest->priority_label)
                    ->line('- Description: ' . substr($this->repairRequest->description, 0, 100) . '...')
                    ->when($this->repairRequest->estimated_cost, function ($message) {
                        return $message->line('- Coût estimé: ' . number_format($this->repairRequest->estimated_cost, 2) . ' DA');
                    })
                    ->when($this->repairRequest->manager_comments, function ($message) {
                        return $message->line('- Commentaires: ' . $this->repairRequest->manager_comments);
                    })
                    ->line('Les travaux vont maintenant pouvoir commencer. Un fournisseur sera assigné prochainement.')
                    ->action('Voir la demande', route('admin.repair-requests.show', $this->repairRequest))
                    ->line('Merci d\'utiliser ZenFleet pour la gestion de votre flotte!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'repair_request_validated',
            'title' => 'Demande de réparation validée',
            'message' => 'Votre demande de réparation pour ' . $this->repairRequest->vehicle->registration_plate . ' a été validée. Les travaux peuvent commencer.',
            'repair_request_id' => $this->repairRequest->id,
            'vehicle_plate' => $this->repairRequest->vehicle->registration_plate,
            'priority' => $this->repairRequest->priority,
            'validated_by' => $this->repairRequest->manager->name,
            'estimated_cost' => $this->repairRequest->estimated_cost,
            'url' => route('admin.repair-requests.show', $this->repairRequest),
            'icon' => 'check-badge',
            'color' => 'blue'
        ];
    }
}