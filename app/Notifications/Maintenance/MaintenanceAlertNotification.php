<?php

namespace App\Notifications\Maintenance;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use App\Models\MaintenanceAlert;

/**
 * Notification pour les alertes de maintenance
 * Support multi-canaux : email, SMS, in-app
 */
class MaintenanceAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected MaintenanceAlert $alert;

    /**
     * Constructeur de la notification
     */
    public function __construct(MaintenanceAlert $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Canaux de notification à utiliser
     */
    public function via($notifiable): array
    {
        $channels = ['database']; // Toujours inclure les notifications in-app

        // Ajouter email selon les préférences utilisateur et la priorité
        if ($this->shouldSendEmail($notifiable)) {
            $channels[] = 'mail';
        }

        // Ajouter SMS pour les alertes critiques si configuré
        if ($this->shouldSendSms($notifiable)) {
            $channels[] = 'sms'; // Nécessite un driver SMS configuré
        }

        return $channels;
    }

    /**
     * Notification par email
     */
    public function toMail($notifiable): MailMessage
    {
        $vehicle = $this->alert->vehicle;
        $schedule = $this->alert->schedule;
        $maintenanceType = $schedule?->maintenanceType;

        $subject = $this->getEmailSubject();
        $greeting = $this->getGreeting($notifiable);

        $message = (new MailMessage)
            ->priority($this->getEmailPriority())
            ->subject($subject)
            ->greeting($greeting)
            ->line($this->alert->getFormattedMessage())
            ->line("**Véhicule :** {$vehicle->registration_plate} ({$vehicle->brand} {$vehicle->model})")
            ->line("**Type de maintenance :** {$maintenanceType?->name}")
            ->line("**Priorité :** {$this->alert->priority_name}");

        // Ajouter des détails selon le type d'alerte
        if ($this->alert->due_date) {
            $daysRemaining = \Carbon\Carbon::today()->diffInDays($this->alert->due_date, false);
            if ($daysRemaining < 0) {
                $message->line("**⚠️ En retard de :** " . abs($daysRemaining) . " jour(s)");
            } else {
                $message->line("**📅 Échéance :** " . $this->alert->due_date->format('d/m/Y') . " (dans {$daysRemaining} jour(s))");
            }
        }

        if ($this->alert->due_mileage && $vehicle->current_mileage) {
            $kmRemaining = $this->alert->due_mileage - $vehicle->current_mileage;
            if ($kmRemaining < 0) {
                $message->line("**⚠️ Kilométrage dépassé de :** " . number_format(abs($kmRemaining), 0, ',', ' ') . " km");
            } else {
                $message->line("**🛣️ Kilométrage restant :** " . number_format($kmRemaining, 0, ',', ' ') . " km");
            }
        }

        $message->action('Voir les détails', route('admin.maintenance.alerts.show', $this->alert->id));

        // Ajouter des actions rapides selon la priorité
        if ($this->alert->priority === 'critical') {
            $message->line('**🚨 Action immédiate requise !**')
                ->action('Planifier la maintenance', route('admin.maintenance.operations.create', [
                    'vehicle_id' => $vehicle->id,
                    'schedule_id' => $schedule?->id
                ]));
        }

        return $message->line('Cordialement,')
                      ->line('Système ZenFleet')
                      ->line('---')
                      ->line('Cette notification a été générée automatiquement. Veuillez ne pas répondre à cet email.');
    }

    /**
     * Notification in-app (base de données)
     */
    public function toDatabase($notifiable): array
    {
        $vehicle = $this->alert->vehicle;

        return [
            'type' => 'maintenance_alert',
            'alert_id' => $this->alert->id,
            'priority' => $this->alert->priority,
            'alert_type' => $this->alert->alert_type,
            'title' => $this->getNotificationTitle(),
            'message' => $this->alert->getFormattedMessage(),
            'vehicle' => [
                'id' => $vehicle->id,
                'registration_plate' => $vehicle->registration_plate,
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
            ],
            'due_date' => $this->alert->due_date?->toDateString(),
            'due_mileage' => $this->alert->due_mileage,
            'action_url' => route('admin.maintenance.alerts.show', $this->alert->id),
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Notification SMS (si configuré)
     */
    public function toSms($notifiable): string
    {
        $vehicle = $this->alert->vehicle;
        $message = "🚨 ZenFleet: Maintenance {$this->alert->priority_name} pour {$vehicle->registration_plate}";

        if ($this->alert->due_date) {
            $daysRemaining = \Carbon\Carbon::today()->diffInDays($this->alert->due_date, false);
            if ($daysRemaining < 0) {
                $message .= " - En retard de " . abs($daysRemaining) . " jour(s)";
            } else {
                $message .= " - Due dans {$daysRemaining} jour(s)";
            }
        }

        return $message . ". Consultez l'app pour plus de détails.";
    }

    /**
     * Déterminer si l'email doit être envoyé
     */
    protected function shouldSendEmail($notifiable): bool
    {
        // Vérifier les préférences utilisateur
        $preferences = $notifiable->notification_preferences ?? [];

        // Toujours envoyer pour les alertes critiques
        if ($this->alert->priority === 'critical') {
            return true;
        }

        // Vérifier les préférences pour les autres priorités
        return $preferences['maintenance_alerts_email'] ?? true;
    }

    /**
     * Déterminer si le SMS doit être envoyé
     */
    protected function shouldSendSms($notifiable): bool
    {
        // SMS uniquement pour les alertes critiques et si configuré
        if ($this->alert->priority !== 'critical') {
            return false;
        }

        $preferences = $notifiable->notification_preferences ?? [];
        return ($preferences['maintenance_alerts_sms'] ?? false) && !empty($notifiable->phone);
    }

    /**
     * Obtenir le sujet de l'email
     */
    protected function getEmailSubject(): string
    {
        $vehicle = $this->alert->vehicle;
        $priorityEmoji = $this->getPriorityEmoji();

        switch ($this->alert->priority) {
            case 'critical':
                return "{$priorityEmoji} URGENT - Maintenance {$vehicle->registration_plate}";
            case 'high':
                return "{$priorityEmoji} Maintenance prioritaire - {$vehicle->registration_plate}";
            default:
                return "{$priorityEmoji} Alerte maintenance - {$vehicle->registration_plate}";
        }
    }

    /**
     * Obtenir la salutation appropriée
     */
    protected function getGreeting($notifiable): string
    {
        $hour = now()->hour;

        if ($hour < 12) {
            $timeGreeting = "Bonjour";
        } elseif ($hour < 17) {
            $timeGreeting = "Bon après-midi";
        } else {
            $timeGreeting = "Bonsoir";
        }

        return "{$timeGreeting} {$notifiable->name},";
    }

    /**
     * Obtenir le titre de la notification
     */
    protected function getNotificationTitle(): string
    {
        $vehicle = $this->alert->vehicle;
        $emoji = $this->getPriorityEmoji();

        return "{$emoji} Maintenance {$this->alert->priority_name} - {$vehicle->registration_plate}";
    }

    /**
     * Obtenir l'emoji de priorité
     */
    protected function getPriorityEmoji(): string
    {
        return match ($this->alert->priority) {
            'critical' => '🚨',
            'high' => '⚠️',
            'medium' => '🔔',
            'low' => 'ℹ️',
            default => '🔔',
        };
    }

    /**
     * Obtenir la priorité email
     */
    protected function getEmailPriority(): int
    {
        return match ($this->alert->priority) {
            'critical' => 1, // Haute priorité
            'high' => 2,
            'medium' => 3,
            'low' => 4,
            default => 3,
        };
    }

    /**
     * Obtenir les tags pour le tracking
     */
    public function tags(): array
    {
        return [
            'maintenance_alert',
            "priority:{$this->alert->priority}",
            "type:{$this->alert->alert_type}",
            "vehicle:{$this->alert->vehicle_id}",
            "organization:{$this->alert->organization_id}",
        ];
    }

    /**
     * Délai avant expiration de la notification
     */
    public function retryUntil(): \DateTime
    {
        return now()->addHours(24);
    }
}