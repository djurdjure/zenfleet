<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;

class LoggingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Validation des channels externes au démarrage
        $this->validateExternalLogChannels();
    }

    public function register(): void
    {
        // Configuration avancée des loggers
        $this->configureCustomLoggers();
    }

    /**
     * Valider les channels externes (Slack, Teams, etc.)
     */
    private function validateExternalLogChannels(): void
    {
        $externalChannels = [
            'slack' => env('LOG_SLACK_WEBHOOK_URL'),
            'teams' => env('LOG_TEAMS_WEBHOOK_URL'),
        ];

        foreach ($externalChannels as $channel => $url) {
            if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
                Log::channel('daily')->warning("Invalid URL for {$channel} logging channel: {$url}");
            }
        }
    }

    /**
     * Configurer les loggers personnalisés
     */
    private function configureCustomLoggers(): void
    {
        // Configuration pour le channel organizations
        $this->app['log']->extend('organizations', function ($app, $config) {
            $logger = new Logger('organizations');
            // Configuration spécifique...
            return $logger;
        });
    }
}

