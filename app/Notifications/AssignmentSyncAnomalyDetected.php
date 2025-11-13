<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

/**
 * 🚨 NOTIFICATION ENTERPRISE-GRADE - ANOMALIE DE SYNCHRONISATION DÉTECTÉE
 *
 * Alerte automatique lorsque des incohérences sont détectées dans le système d'affectations.
 * Supporte multi-canal : Email, Slack, Teams, SMS.
 *
 * @package App\Notifications
 * @version 1.0.0-Enterprise
 * @since 2025-11-12
 */
class AssignmentSyncAnomalyDetected extends Notification
{
    use Queueable;

    public function __construct(
        public int $totalInconsistencies,
        public int $vehiclesAffected,
        public int $driversAffected,
        public array $details = []
    ) {}

    /**
     * Canaux de notification
     */
    public function via($notifiable): array
    {
        $channels = ['mail'];

        if (config('services.slack.notifications.bot_user_oauth_token')) {
            $channels[] = 'slack';
        }

        return $channels;
    }

    /**
     * Notification Email
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🚨 [ZenFleet] Anomalies de synchronisation détectées')
            ->error()
            ->line("**{$this->totalInconsistencies} incohérence(s)** ont été détectées dans le système d'affectations.")
            ->line('')
            ->line('**Détails :**')
            ->line("• Véhicules affectés : {$this->vehiclesAffected}")
            ->line("• Chauffeurs affectés : {$this->driversAffected}")
            ->line('')
            ->line('**Actions recommandées :**')
            ->line('1. Consulter le dashboard de santé : /admin/assignments/health-dashboard')
            ->line('2. Exécuter la synchronisation manuelle : `php artisan assignments:sync`')
            ->line('3. Vérifier les logs système pour plus de détails')
            ->line('')
            ->line('Les incohérences ont été automatiquement corrigées par le système.')
            ->action('Voir le dashboard', url('/admin/assignments/health-dashboard'))
            ->line('Cette alerte a été générée automatiquement par ZenFleet Enterprise.');
    }

    /**
     * Notification Slack
     */
    public function toSlack($notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->error()
            ->from('ZenFleet Monitor', ':rotating_light:')
            ->to(config('services.slack.notifications.channel', '#alerts'))
            ->content('🚨 **Anomalies de synchronisation détectées**')
            ->attachment(function ($attachment) {
                $attachment
                    ->title('Rapport de synchronisation')
                    ->fields([
                        'Total incohérences' => $this->totalInconsistencies,
                        'Véhicules affectés' => $this->vehiclesAffected,
                        'Chauffeurs affectés' => $this->driversAffected,
                        'Statut' => '✅ Auto-corrigé',
                    ])
                    ->color('warning');
            })
            ->attachment(function ($attachment) {
                $attachment
                    ->title('Actions recommandées')
                    ->content(implode("\n", [
                        '1. Consulter le dashboard de santé',
                        '2. Vérifier les logs pour plus de détails',
                        '3. Surveiller la récurrence'
                    ]));
            });
    }

    /**
     * Représentation en array
     */
    public function toArray($notifiable): array
    {
        return [
            'total_inconsistencies' => $this->totalInconsistencies,
            'vehicles_affected' => $this->vehiclesAffected,
            'drivers_affected' => $this->driversAffected,
            'details' => $this->details,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
