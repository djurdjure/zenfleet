<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\CurlHandler;
use Illuminate\Support\Facades\Log;

class CustomTeamsLogger
{
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('teams');
        
        // Valider l'URL avant de créer le handler
        if (empty($config['url']) || !filter_var($config['url'], FILTER_VALIDATE_URL)) {
            Log::channel('daily')->warning('Teams webhook URL is invalid or empty, falling back to daily log');
            return Log::channel('daily')->getLogger();
        }
        
        try {
            $handler = new CurlHandler($config['url']);
            $handler->setLevel($config['level'] ?? 'error');
            $logger->pushHandler($handler);
        } catch (\Exception $e) {
            Log::channel('daily')->error('Failed to create Teams logger: ' . $e->getMessage());
            return Log::channel('daily')->getLogger();
        }
        
        return $logger;
    }
}

