<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Roles officiels geles en Phase 0
    |--------------------------------------------------------------------------
    |
    | Ce catalogue ne remplace pas encore le futur RoleCatalog canonique.
    | Il sert de garde-fou pour eviter l'augmentation de la dette RBAC
    | pendant la phase de gel.
    |
    */
    'official_roles' => [
        'Super Admin',
        'Admin',
        'Gestionnaire Flotte',
        'Superviseur',
        'Chauffeur',
        'Comptable',
        'Mecanicien',
    ],

    /*
    |--------------------------------------------------------------------------
    | Seeders RBAC historiques interdits dans les entrypoints actifs
    |--------------------------------------------------------------------------
    |
    | Ces seeders existent encore dans le repository pour compatibilite
    | historique, mais ils ne doivent plus etre relies aux flux normaux
    | de bootstrap, CI ou deploiement standard.
    |
    */
    'deprecated_seeders' => [
        'RolesAndPermissionsSeeder',
        'MasterPermissionsSeeder',
        'PermissionSeeder',
        'EnterpriseRbacSeeder',
        'SecurityEnhancedRbacSeeder',
        'EnterprisePermissionsSeeder',
        'InitialRbacSeeder',
        'SuperAdminSeeder',
        'EnterpriseUsersSeeder',
    ],

    /*
    |--------------------------------------------------------------------------
    | Entry points proteges par le gel RBAC
    |--------------------------------------------------------------------------
    */
    'protected_entrypoints' => [
        '.github/workflows/ci.yml',
        'database/seeders/DatabaseSeeder.php',
    ],

    /*
    |--------------------------------------------------------------------------
    | References de policies attendues
    |--------------------------------------------------------------------------
    |
    | Les absences sont remontees en warning pour l'instant afin de ne pas
    | casser le gel Phase 0 sur une dette preexistante.
    |
    */
    'policy_file_expectations' => [
        'App\\Policies\\UserPolicy' => 'app/Policies/UserPolicy.php',
        'App\\Policies\\OrganizationPolicy' => 'app/Policies/OrganizationPolicy.php',
    ],
];
