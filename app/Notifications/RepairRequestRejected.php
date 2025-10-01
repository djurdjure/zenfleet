<?php

namespace App\Notifications;

use App\Models\RepairRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RepairRequestRejected extends Notification implements ShouldQueue
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
        $rejectedBy = $this->repairRequest->supervisor ?: $this->repairRequest->manager;
        $rejectionLevel = $this->repairRequest->supervisor ? 'superviseur' : 'gestionnaire de flotte';
        $comments = $this->repairRequest->supervisor_comments ?: $this->repairRequest->manager_comments;

        return (new MailMessage)
                    ->subject('Demande de réparation refusée - ' . $this->repairRequest->vehicle->registration_plate)
                    ->greeting('Bonjour ' . $notifiable->name . ',')
                    ->line('Malheureusement, votre demande de réparation pour le véhicule ' . $this->repairRequest->vehicle->registration_plate . ' a été refusée par le ' . $rejectionLevel . '.')
                    ->line('**Détails de la demande:**')
                    ->line('- Véhicule: ' . $this->repairRequest->vehicle->registration_plate)
                    ->line('- Priorité: ' . $this->repairRequest->priority_label)
                    ->line('- Description: ' . substr($this->repairRequest->description, 0, 100) . '...')
                    ->when($comments, function ($message) use ($comments) {
                        return $message->line('**Raison du refus:** ' . $comments);
                    })
                    ->line('Si vous pensez qu\'il s\'agit d\'une erreur, vous pouvez créer une nouvelle demande avec plus de détails.')
                    ->action('Créer une nouvelle demande', route('admin.repair-requests.create'))
                    ->line('Merci de votre compréhension.');
    }

    public function toDatabase($notifiable)
    {
        $rejectedBy = $this->repairRequest->supervisor ?: $this->repairRequest->manager;
        $comments = $this->repairRequest->supervisor_comments ?: $this->repairRequest->manager_comments;

        return [
            'type' => 'repair_request_rejected',
            'title' => 'Demande de réparation refusée',
            'message' => 'Votre demande de réparation pour ' . $this->repairRequest->vehicle->registration_plate . ' a été refusée.',
            'repair_request_id' => $this->repairRequest->id,
            'vehicle_plate' => $this->repairRequest->vehicle->registration_plate,
            'priority' => $this->repairRequest->priority,
            'rejected_by' => $rejectedBy->name,
            'rejection_reason' => $comments,
            'url' => route('admin.repair-requests.show', $this->repairRequest),
            'icon' => 'x-circle',
            'color' => 'red'
        ];
    }
}