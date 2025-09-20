<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Algeria Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains Algeria-specific configuration for the ZenFleet
    | application, including locale settings, formatting, and validation rules.
    |
    */

    'locale' => [
        'default' => 'ar',
        'fallback' => 'fr',
        'supported' => ['ar', 'fr'],
    ],

    'timezone' => 'Africa/Algiers',

    'currency' => [
        'code' => 'DZD',
        'symbol' => 'د.ج',
        'name' => 'Dinar Algérien',
        'format' => [
            'decimal_separator' => ',',
            'thousands_separator' => ' ',
            'decimals' => 2,
            'pattern' => '%value %symbol',
        ],
    ],

    'formats' => [
        'date' => 'd/m/Y',
        'datetime' => 'd/m/Y H:i',
        'time' => 'H:i',
        'phone' => '+213',
    ],

    'validation' => [
        'phone' => '/^(\+213|0)[0-9]{8,9}$/',
        'nin' => '/^[0-9]{18}$/',
        'nif' => '/^[0-9]+$/',
        'ai' => '/^[0-9]+$/',
        'nis' => '/^[0-9]+$/',
        'postal_code' => '/^[0-9]{5}$/',
        'wilaya_code' => '/^[0-4][0-9]$/',
    ],

    'wilayas' => [
        '01' => ['name' => 'Adrar', 'name_ar' => 'أدرار'],
        '02' => ['name' => 'Chlef', 'name_ar' => 'الشلف'],
        '03' => ['name' => 'Laghouat', 'name_ar' => 'الأغواط'],
        '04' => ['name' => 'Oum El Bouaghi', 'name_ar' => 'أم البواقي'],
        '05' => ['name' => 'Batna', 'name_ar' => 'باتنة'],
        '06' => ['name' => 'Béjaïa', 'name_ar' => 'بجاية'],
        '07' => ['name' => 'Biskra', 'name_ar' => 'بسكرة'],
        '08' => ['name' => 'Béchar', 'name_ar' => 'بشار'],
        '09' => ['name' => 'Blida', 'name_ar' => 'البليدة'],
        '10' => ['name' => 'Bouira', 'name_ar' => 'البويرة'],
        '11' => ['name' => 'Tamanrasset', 'name_ar' => 'تمنراست'],
        '12' => ['name' => 'Tébessa', 'name_ar' => 'تبسة'],
        '13' => ['name' => 'Tlemcen', 'name_ar' => 'تلمسان'],
        '14' => ['name' => 'Tiaret', 'name_ar' => 'تيارت'],
        '15' => ['name' => 'Tizi Ouzou', 'name_ar' => 'تيزي وزو'],
        '16' => ['name' => 'Alger', 'name_ar' => 'الجزائر'],
        '17' => ['name' => 'Djelfa', 'name_ar' => 'الجلفة'],
        '18' => ['name' => 'Jijel', 'name_ar' => 'جيجل'],
        '19' => ['name' => 'Sétif', 'name_ar' => 'سطيف'],
        '20' => ['name' => 'Saïda', 'name_ar' => 'سعيدة'],
        '21' => ['name' => 'Skikda', 'name_ar' => 'سكيكدة'],
        '22' => ['name' => 'Sidi Bel Abbès', 'name_ar' => 'سيدي بلعباس'],
        '23' => ['name' => 'Annaba', 'name_ar' => 'عنابة'],
        '24' => ['name' => 'Guelma', 'name_ar' => 'قالمة'],
        '25' => ['name' => 'Constantine', 'name_ar' => 'قسنطينة'],
        '26' => ['name' => 'Médéa', 'name_ar' => 'المدية'],
        '27' => ['name' => 'Mostaganem', 'name_ar' => 'مستغانم'],
        '28' => ['name' => 'M\'Sila', 'name_ar' => 'المسيلة'],
        '29' => ['name' => 'Mascara', 'name_ar' => 'معسكر'],
        '30' => ['name' => 'Ouargla', 'name_ar' => 'ورقلة'],
        '31' => ['name' => 'Oran', 'name_ar' => 'وهران'],
        '32' => ['name' => 'El Bayadh', 'name_ar' => 'البيض'],
        '33' => ['name' => 'Illizi', 'name_ar' => 'إليزي'],
        '34' => ['name' => 'Bordj Bou Arréridj', 'name_ar' => 'برج بوعريريج'],
        '35' => ['name' => 'Boumerdès', 'name_ar' => 'بومرداس'],
        '36' => ['name' => 'El Tarf', 'name_ar' => 'الطارف'],
        '37' => ['name' => 'Tindouf', 'name_ar' => 'تندوف'],
        '38' => ['name' => 'Tissemsilt', 'name_ar' => 'تيسمسيلت'],
        '39' => ['name' => 'El Oued', 'name_ar' => 'الوادي'],
        '40' => ['name' => 'Khenchela', 'name_ar' => 'خنشلة'],
        '41' => ['name' => 'Souk Ahras', 'name_ar' => 'سوق أهراس'],
        '42' => ['name' => 'Tipaza', 'name_ar' => 'تيبازة'],
        '43' => ['name' => 'Mila', 'name_ar' => 'ميلة'],
        '44' => ['name' => 'Aïn Defla', 'name_ar' => 'عين الدفلى'],
        '45' => ['name' => 'Naâma', 'name_ar' => 'النعامة'],
        '46' => ['name' => 'Aïn Témouchent', 'name_ar' => 'عين تموشنت'],
        '47' => ['name' => 'Ghardaïa', 'name_ar' => 'غرداية'],
        '48' => ['name' => 'Relizane', 'name_ar' => 'غليزان'],
    ],

    'business' => [
        'working_days' => [1, 2, 3, 4, 5], // Monday to Friday
        'working_hours' => [
            'start' => '08:00',
            'end' => '17:00',
        ],
        'weekend_days' => [5, 6], // Friday and Saturday (Islamic weekend)
    ],

    'legal_forms' => [
        'SARL' => 'Société à Responsabilité Limitée',
        'SPA' => 'Société par Actions',
        'SNC' => 'Société en Nom Collectif',
        'EURL' => 'Entreprise Unipersonnelle à Responsabilité Limitée',
        'EI' => 'Entreprise Individuelle',
        'SCS' => 'Société en Commandite Simple',
    ],

    'insurance_companies' => [
        'SAA' => 'Société Algérienne d\'Assurance',
        'CAAR' => 'Compagnie Algérienne d\'Assurance et de Réassurance',
        'GAM' => 'Groupe Assurance Mutuelle',
        'Alliance' => 'Alliance Assurances',
        'TRUST' => 'Trust Algeria',
        'CAAT' => 'Compagnie Algérienne d\'Assurance Transport',
    ],
];