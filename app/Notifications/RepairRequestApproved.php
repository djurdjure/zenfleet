<?php

namespace App\Notifications;

use App\Models\RepairRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class RepairRequestApproved extends Notification implements ShouldQueue
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
                    ->subject('Demande de réparation approuvée - ' . $this->repairRequest->vehicle->registration_plate)
                    ->greeting('Bonjour ' . $notifiable->name . ',')
                    ->line('Votre demande de réparation pour le véhicule ' . $this->repairRequest->vehicle->registration_plate . ' a été approuvée par le superviseur.')
                    ->line('**Détails de la demande:**')
                    ->line('- Véhicule: ' . $this->repairRequest->vehicle->registration_plate)
                    ->line('- Priorité: ' . $this->repairRequest->priority_label)
                    ->line('- Description: ' . substr($this->repairRequest->description, 0, 100) . '...')
                    ->when($this->repairRequest->supervisor_comments, function ($message) {
                        return $message->line('- Commentaires: ' . $this->repairRequest->supervisor_comments);
                    })
                    ->line('La demande est maintenant en attente de validation par le gestionnaire de flotte.')
                    ->action('Voir la demande', route('admin.repair-requests.show', $this->repairRequest))
                    ->line('Merci d\'utiliser ZenFleet!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'repair_request_approved',
            'title' => 'Demande de réparation approuvée',
            'message' => 'Votre demande de réparation pour ' . $this->repairRequest->vehicle->registration_plate . ' a été approuvée.',
            'repair_request_id' => $this->repairRequest->id,
            'vehicle_plate' => $this->repairRequest->vehicle->registration_plate,
            'priority' => $this->repairRequest->priority,
            'approved_by' => $this->repairRequest->supervisor->name,
            'url' => route('admin.repair-requests.show', $this->repairRequest),
            'icon' => 'check-circle',
            'color' => 'green'
        ];
    }
}