<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ⚡ ZENFLEET LOGGING - ENTERPRISE OPTIMIZED
    |--------------------------------------------------------------------------
    | Configuration professionnelle avec gestion d'erreurs robuste
    | Version: 2.0 - Optimisée pour les performances et la sécurité
    |--------------------------------------------------------------------------
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | 🚨 DEPRECATIONS LOG CHANNEL
    |--------------------------------------------------------------------------
    */
    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | 🛡️ ENTERPRISE LOG CHANNELS - PRODUCTION READY
    |--------------------------------------------------------------------------
    */
    'channels' => [

        /*
        |----------------------------------------------------------------------
        | 📚 CHANNELS PRINCIPAUX - CONFIGURATION ROBUSTE
        |----------------------------------------------------------------------
        */
        'stack' => [
            'driver' => 'stack',
            'channels' => array_filter([
                'daily',
                env('LOG_SLACK_WEBHOOK_URL') ? 'slack' : null,
                env('LOG_TEAMS_WEBHOOK_URL') ? 'teams' : null,
                // Ajout conditionnel des channels externes seulement si configurés
                env('LOG_SENTRY_DSN') ? 'sentry' : null,
            ]),
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
            'permission' => 0664,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => env('LOG_RETENTION_DAYS', 30),
            'replace_placeholders' => true,
            'permission' => 0664,
            // Optimisation : ajout de la rotation par taille
            'max_files' => env('LOG_MAX_FILES', 30),
        ],

        /*
        |----------------------------------------------------------------------
        | 🛡️ CHANNELS SÉCURITÉ ET AUDIT - RENFORCÉS
        |----------------------------------------------------------------------
        */
        'audit' => [
            'driver' => 'daily',
            'path' => storage_path('logs/audit/audit.log'),
            'level' => 'info',
            'days' => env('AUDIT_RETENTION_DAYS', 365),
            'permission' => 0600,
            'replace_placeholders' => true,
            // Sécurité renforcée : formatage sécurisé
            'formatter' => \Monolog\Formatter\JsonFormatter::class,
        ],

        'security' => [
            'driver' => 'daily',
            'path' => storage_path('logs/security/security.log'),
            'level' => 'warning',
            'days' => env('SECURITY_RETENTION_DAYS', 180),
            'permission' => 0600,
            'replace_placeholders' => true,
            'formatter' => \Monolog\Formatter\JsonFormatter::class,
        ],

        'authentication' => [
            'driver' => 'daily',
            'path' => storage_path('logs/auth/authentication.log'),
            'level' => 'info',
            'days' => env('AUTH_RETENTION_DAYS', 90),
            'permission' => 0640,
            'replace_placeholders' => true,
        ],

        'authorization' => [
            'driver' => 'daily',
            'path' => storage_path('logs/auth/authorization.log'),
            'level' => 'warning',
            'days' => env('AUTHZ_RETENTION_DAYS', 90),
            'permission' => 0640,
            'replace_placeholders' => true,
        ],

        /*
        |----------------------------------------------------------------------
        | 📈 CHANNELS PERFORMANCE ET MONITORING - OPTIMISÉS
        |----------------------------------------------------------------------
        */
        'performance' => [
            'driver' => 'daily',
            'path' => storage_path('logs/performance/performance.log'),
            'level' => 'info',
            'days' => env('PERFORMANCE_RETENTION_DAYS', 30),
            'replace_placeholders' => true,
            'permission' => 0644,
            // Optimisation : buffer pour les performances
            'processors' => [
                \Monolog\Processor\MemoryUsageProcessor::class,
                \Monolog\Processor\ProcessIdProcessor::class,
            ],
        ],

        'database' => [
            'driver' => 'daily',
            'path' => storage_path('logs/database/database.log'),
            'level' => env('DB_LOG_LEVEL', 'warning'),
            'days' => env('DB_RETENTION_DAYS', 14),
            'replace_placeholders' => true,
            'permission' => 0644,
        ],

        'api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api/api.log'),
            'level' => 'info',
            'days' => env('API_RETENTION_DAYS', 30),
            'replace_placeholders' => true,
            'permission' => 0644,
        ],

        /*
        |----------------------------------------------------------------------
        | 🚨 CHANNELS ERREURS - HIÉRARCHISÉS
        |----------------------------------------------------------------------
        */
        'errors' => [
            'driver' => 'daily',
            'path' => storage_path('logs/errors/errors.log'),
            'level' => 'error',
            'days' => env('ERROR_RETENTION_DAYS', 60),
            'permission' => 0640,
            'replace_placeholders' => true,
        ],

        // 🔥 Alias pour compatibilité avec les contrôleurs
        'error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/errors/errors.log'),
            'level' => 'error',
            'days' => env('ERROR_RETENTION_DAYS', 60),
            'permission' => 0640,
            'replace_placeholders' => true,
        ],

        'critical' => [
            'driver' => 'stack',
            'channels' => array_filter([
                'critical_file',
                env('LOG_SLACK_WEBHOOK_URL') ? 'slack' : null,
                env('ADMIN_EMAIL') ? 'mail' : null,
            ]),
            'ignore_exceptions' => false,
        ],

        'critical_file' => [
            'driver' => 'daily',
            'path' => storage_path('logs/critical/critical.log'),
            'level' => 'critical',
            'days' => env('CRITICAL_RETENTION_DAYS', 180),
            'permission' => 0600,
            'replace_placeholders' => true,
        ],

        /*
        |----------------------------------------------------------------------
        | 📱 CHANNELS NOTIFICATIONS EXTERNES - SÉCURISÉS
        |----------------------------------------------------------------------
        */
        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('APP_NAME', 'ZenFleet') . ' Logger',
            'emoji' => ':warning:',
            'level' => env('SLACK_LOG_LEVEL', 'error'),
            'replace_placeholders' => true,
            // Optimisation : limitation du taux d'envoi
            'context' => [
                'validate_url' => true,
                'fallback_channel' => 'daily',
                'rate_limit' => env('SLACK_RATE_LIMIT', 10), // messages par minute
            ],
        ],

        'teams' => [
            'driver' => 'custom',
            'via' => App\Logging\CustomTeamsLogger::class,
            'url' => env('LOG_TEAMS_WEBHOOK_URL'),
            'level' => env('TEAMS_LOG_LEVEL', 'critical'),
            'validate_url' => true,
            'rate_limit' => env('TEAMS_RATE_LIMIT', 5),
        ],

        /*
        |----------------------------------------------------------------------
        | 📧 CHANNELS EMAIL - ALERTES CRITIQUES UNIQUEMENT
        |----------------------------------------------------------------------
        */
        'mail' => [
            'driver' => 'custom',
            'via' => App\Logging\CustomMailLogger::class,
            'to' => array_filter(explode(',', env('ADMIN_EMAIL', ''))),
            'subject' => env('APP_NAME') . ' - Alerte Critique Système',
            'level' => 'critical',
            // Limitation pour éviter le spam
            'throttle' => [
                'max_emails' => 5,
                'time_window' => 3600, // 1 heure
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | 🖥️ CHANNELS SYSTÈME - OPTIMISÉS
        |----------------------------------------------------------------------
        */
        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', \Monolog\Handler\SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
            'processors' => [
                \Monolog\Processor\PsrLogMessageProcessor::class,
                \Monolog\Processor\IntrospectionProcessor::class,
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => \Monolog\Handler\StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
            'processors' => [\Monolog\Processor\PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => LOG_USER,
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => \Monolog\Handler\NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/emergency.log'),
        ],

        /*
        |----------------------------------------------------------------------
        | 🏢 CHANNELS MÉTIER ZENFLEET - BUSINESS OPTIMISÉS
        |----------------------------------------------------------------------
        */
        'vehicle_tracking' => [
            'driver' => 'daily',
            'path' => storage_path('logs/business/vehicle_tracking.log'),
            'level' => 'info',
            'days' => env('TRACKING_RETENTION_DAYS', 90),
            'replace_placeholders' => true,
            'permission' => 0644,
            // Optimisation pour les gros volumes
            'max_files' => 90,
        ],

        'maintenance' => [
            'driver' => 'daily',
            'path' => storage_path('logs/business/maintenance.log'),
            'level' => 'info',
            'days' => env('MAINTENANCE_RETENTION_DAYS', 180),
            'replace_placeholders' => true,
            'permission' => 0644,
        ],

        'assignments' => [
            'driver' => 'daily',
            'path' => storage_path('logs/business/assignments.log'),
            'level' => 'info',
            'days' => env('ASSIGNMENT_RETENTION_DAYS', 60),
            'replace_placeholders' => true,
            'permission' => 0644,
        ],

        'organizations' => [
            'driver' => 'daily',
            'path' => storage_path('logs/business/organizations.log'),
            'level' => 'info',
            'days' => env('ORGANIZATION_RETENTION_DAYS', 180),
            'replace_placeholders' => true,
            'permission' => 0644,
        ],

        // Nouveau : Channel pour les webhooks entrants
        'webhooks' => [
            'driver' => 'daily',
            'path' => storage_path('logs/webhooks/webhooks.log'),
            'level' => 'info',
            'days' => env('WEBHOOK_RETENTION_DAYS', 30),
            'replace_placeholders' => true,
            'permission' => 0644,
        ],

        // Nouveau : Channel pour les imports/exports
        'imports' => [
            'driver' => 'daily',
            'path' => storage_path('logs/imports/imports.log'),
            'level' => 'info',
            'days' => env('IMPORT_RETENTION_DAYS', 90),
            'replace_placeholders' => true,
            'permission' => 0644,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 🎛️ CONFIGURATION ENTERPRISE AVANCÉE - OPTIMISÉE
    |--------------------------------------------------------------------------
    */
    'log_rotation' => [
        'enabled' => env('LOG_ROTATION_ENABLED', true),
        'max_size' => env('LOG_MAX_SIZE', '100MB'),
        'compress' => env('LOG_COMPRESS', true),
        'compress_algorithm' => env('LOG_COMPRESS_ALGORITHM', 'gzip'),
    ],

    'monitoring' => [
        'enabled' => env('LOG_MONITORING_ENABLED', false),
        'alert_threshold' => env('LOG_ALERT_THRESHOLD', 100),
        'error_threshold' => env('LOG_ERROR_THRESHOLD', 10), // erreurs par minute
        'channels' => array_filter([
            env('LOG_SLACK_WEBHOOK_URL') ? 'slack' : null,
            env('ADMIN_EMAIL') ? 'mail' : null,
        ]),
        'health_check' => [
            'enabled' => true,
            'interval' => 300, // 5 minutes
            'disk_usage_threshold' => 85, // %
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 🔧 CONFIGURATION AVANCÉE
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'async_logging' => env('LOG_ASYNC_ENABLED', false),
        'buffer_size' => env('LOG_BUFFER_SIZE', 100),
        'flush_interval' => env('LOG_FLUSH_INTERVAL', 5), // secondes
    ],

    'security' => [
        'anonymize_sensitive_data' => env('LOG_ANONYMIZE_DATA', true),
        'encrypt_logs' => env('LOG_ENCRYPT_ENABLED', false),
        'sensitive_fields' => [
            'password', 'token', 'secret', 'key', 'authorization',
            'x-api-key', 'cookie', 'session'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 🔧 CONFIGURATION DE DÉVELOPPEMENT
    |--------------------------------------------------------------------------
    */
    'development' => [
        'query_log' => env('LOG_QUERIES', false),
        'dump_server' => env('LOG_DUMP_SERVER', false),
        'telescope' => env('TELESCOPE_ENABLED', false),
        'debug_bar' => env('DEBUG_BAR_ENABLED', false),
    ],
];
