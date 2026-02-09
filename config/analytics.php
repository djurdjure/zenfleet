<?php

return [
    'chart' => [
        'contract_version' => '1.0',
    ],

    'cache' => [
        'ttl' => [
            // Données quasi temps réel (widgets, compteurs volatils)
            'realtime' => 300,
            // Données historiques (tendances, TCO, analyses annuelles)
            'historical' => 1800,
        ],
    ],
];

