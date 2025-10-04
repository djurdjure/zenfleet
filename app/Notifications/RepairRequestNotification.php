<?php

namespace App\Notifications;

use App\Models\RepairRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * RepairRequestNotification - Notification système pour demandes de réparation
 *
 * Types de notifications:
 * - new_request: Nouvelle demande créée
 * - approved_level_1: Approuvée par superviseur
 * - pending_approval_l2: En attente gestionnaire
 * - approved_final: Approuvée définitivement
 * - rejected: Rejetée
 *
 * Canaux: database, mail
 *
 * @version 1.0-Enterprise
 */
class RepairRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * 🔔 TYPES DE NOTIFICATIONS
     */
    public const TYPE_NEW_REQUEST = 'new_request';
    public const TYPE_APPROVED_LEVEL_1 = 'approved_level_1';
    public const TYPE_PENDING_APPROVAL_L2 = 'pending_approval_l2';
    public const TYPE_APPROVED_FINAL = 'approved_final';
    public const TYPE_REJECTED = 'rejected';

    /**
     * Constructor.
     */
    public function __construct(
        public RepairRequest $repairRequest,
        public string $type
    ) {
    }

    /**
     * 📡 CANAUX DE NOTIFICATION
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * 💾 NOTIFICATION DATABASE
     */
    public function toDatabase(object $notifiable): array
    {
        $config = $this->getNotificationConfig();

        return [
            'repair_request_id' => $this->repairRequest->id,
            'repair_request_uuid' => $this->repairRequest->uuid,
            'type' => $this->type,
            'title' => $config['title'],
            'body' => $config['body'],
            'icon' => $config['icon'],
            'color' => $config['color'],
            'url' => route('admin.repair-requests.show', $this->repairRequest),
            'vehicle' => [
                'name' => $this->repairRequest->vehicle->vehicle_name ?? $this->repairRequest->vehicle->license_plate,
                'license_plate' => $this->repairRequest->vehicle->license_plate,
            ],
            'driver' => [
                'name' => $this->repairRequest->driver->user->name ?? 'N/A',
            ],
            'urgency' => $this->repairRequest->urgency,
            'status' => $this->repairRequest->status,
        ];
    }

    /**
     * 📧 NOTIFICATION EMAIL
     */
    public function toMail(object $notifiable): MailMessage
    {
        $config = $this->getNotificationConfig();

        $mail = (new MailMessage)
            ->subject($config['email_subject'])
            ->greeting($config['greeting'])
            ->line($config['body']);

        // 📊 INFORMATIONS SUPPLÉMENTAIRES
        $mail->line('**Détails de la demande:**')
            ->line('• Véhicule: ' . ($this->repairRequest->vehicle->vehicle_name ?? $this->repairRequest->vehicle->license_plate))
            ->line('• Chauffeur: ' . ($this->repairRequest->driver->user->name ?? 'N/A'))
            ->line('• Urgence: ' . $this->getUrgencyLabel($this->repairRequest->urgency))
            ->line('• Demande: ' . $this->repairRequest->title);

        // 🔴 INFORMATIONS REJET
        if ($this->type === self::TYPE_REJECTED && $this->repairRequest->rejection_reason) {
            $mail->line('')
                ->line('**Raison du rejet:**')
                ->line($this->repairRequest->rejection_reason);
        }

        // 🔗 BOUTON ACTION
        $mail->action($config['action_text'], route('admin.repair-requests.show', $this->repairRequest));

        // 📝 FOOTER
        if ($this->type === self::TYPE_APPROVED_FINAL) {
            $mail->line('Une opération de maintenance a été créée automatiquement pour cette demande.');
        }

        return $mail;
    }

    /**
     * 🎨 CONFIGURATION DES NOTIFICATIONS PAR TYPE
     */
    protected function getNotificationConfig(): array
    {
        return match ($this->type) {
            self::TYPE_NEW_REQUEST => [
                'title' => 'Nouvelle demande de réparation',
                'body' => 'Une nouvelle demande de réparation a été créée et nécessite votre attention.',
                'icon' => 'bell',
                'color' => 'blue',
                'email_subject' => 'Nouvelle demande de réparation - ' . $this->repairRequest->title,
                'greeting' => 'Bonjour !',
                'action_text' => 'Consulter la demande',
            ],

            self::TYPE_APPROVED_LEVEL_1 => [
                'title' => 'Demande approuvée par le superviseur',
                'body' => 'Votre demande de réparation a été approuvée par le superviseur et est maintenant en attente de validation finale.',
                'icon' => 'check-circle',
                'color' => 'green',
                'email_subject' => 'Demande approuvée - ' . $this->repairRequest->title,
                'greeting' => 'Bonne nouvelle !',
                'action_text' => 'Voir le statut',
            ],

            self::TYPE_PENDING_APPROVAL_L2 => [
                'title' => 'Demande en attente de validation finale',
                'body' => 'Une demande de réparation a été approuvée par le superviseur et nécessite votre validation finale.',
                'icon' => 'clock',
                'color' => 'orange',
                'email_subject' => 'Validation requise - ' . $this->repairRequest->title,
                'greeting' => 'Bonjour !',
                'action_text' => 'Valider la demande',
            ],

            self::TYPE_APPROVED_FINAL => [
                'title' => 'Demande approuvée définitivement',
                'body' => 'Votre demande de réparation a été approuvée définitivement. Une opération de maintenance a été créée.',
                'icon' => 'check-circle',
                'color' => 'green',
                'email_subject' => 'Demande approuvée - ' . $this->repairRequest->title,
                'greeting' => 'Excellente nouvelle !',
                'action_text' => 'Voir les détails',
            ],

            self::TYPE_REJECTED => [
                'title' => 'Demande rejetée',
                'body' => 'Votre demande de réparation a été rejetée. Consultez les détails pour plus d\'informations.',
                'icon' => 'x-circle',
                'color' => 'red',
                'email_subject' => 'Demande rejetée - ' . $this->repairRequest->title,
                'greeting' => 'Bonjour,',
                'action_text' => 'Consulter les détails',
            ],

            default => [
                'title' => 'Mise à jour demande de réparation',
                'body' => 'Le statut de votre demande de réparation a été mis à jour.',
                'icon' => 'bell',
                'color' => 'gray',
                'email_subject' => 'Mise à jour - ' . $this->repairRequest->title,
                'greeting' => 'Bonjour !',
                'action_text' => 'Voir la demande',
            ],
        };
    }

    /**
     * 🚨 LABELS D'URGENCE
     */
    protected function getUrgencyLabel(string $urgency): string
    {
        return match ($urgency) {
            RepairRequest::URGENCY_LOW => 'Faible',
            RepairRequest::URGENCY_NORMAL => 'Normal',
            RepairRequest::URGENCY_HIGH => 'Élevé',
            RepairRequest::URGENCY_CRITICAL => 'Critique',
            default => 'Non défini',
        };
    }

    /**
     * 📊 DONNÉES POUR ARRAY/BROADCAST
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
