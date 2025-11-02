<?php

/**
 * ====================================================================
 * 🚀 EXPENSE CATEGORIES CONFIGURATION - ENTERPRISE ULTRA-PRO V1.0
 * ====================================================================
 * 
 * Configuration centralisée des catégories de dépenses
 * Synchronisée avec la contrainte PostgreSQL
 * 
 * @package Config
 * @version 1.0.0-Enterprise
 * @since 2025-10-29
 * ====================================================================
 */

return [
    /**
     * Catégories de dépenses autorisées dans la base de données
     * Ces valeurs DOIVENT correspondre exactement à la contrainte PostgreSQL
     * vehicle_expenses_expense_category_check
     */
    'categories' => [
        'maintenance_preventive' => [
            'label' => 'Maintenance préventive',
            'icon' => 'lucide:wrench',
            'color' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
            'types' => [
                'vidange' => 'Vidange moteur',
                'filtre_air' => 'Filtre à air',
                'filtre_huile' => 'Filtre à huile',
                'filtre_gasoil' => 'Filtre gasoil',
                'filtre_habitacle' => 'Filtre habitacle',
                'courroie_distribution' => 'Courroie de distribution',
                'courroie_accessoire' => 'Courroie accessoire',
                'bougies' => 'Bougies d\'allumage',
                'liquide_frein' => 'Liquide de frein',
                'liquide_refroidissement' => 'Liquide de refroidissement',
                'plaquettes_frein' => 'Plaquettes de frein',
                'disques_frein' => 'Disques de frein',
                'pneus' => 'Pneumatiques',
                'batterie' => 'Batterie',
                'balais_essuie_glace' => 'Balais essuie-glace',
                'revision_generale' => 'Révision générale',
                'autre' => 'Autre maintenance'
            ]
        ],
        'reparation' => [
            'label' => 'Réparation',
            'icon' => 'lucide:tool',
            'color' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800'],
            'types' => [
                'moteur' => 'Réparation moteur',
                'boite_vitesse' => 'Boîte de vitesse',
                'embrayage' => 'Embrayage',
                'suspension' => 'Suspension',
                'direction' => 'Direction',
                'freinage' => 'Système de freinage',
                'echappement' => 'Échappement',
                'climatisation' => 'Climatisation',
                'electronique' => 'Électronique',
                'carrosserie' => 'Carrosserie',
                'vitrage' => 'Vitrage',
                'eclairage' => 'Éclairage',
                'pneumatique' => 'Réparation pneu',
                'autre' => 'Autre réparation'
            ]
        ],
        'pieces_detachees' => [
            'label' => 'Pièces détachées',
            'icon' => 'lucide:package',
            'color' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
            'types' => [
                'pieces_moteur' => 'Pièces moteur',
                'pieces_freinage' => 'Pièces freinage',
                'pieces_suspension' => 'Pièces suspension',
                'pieces_transmission' => 'Pièces transmission',
                'pieces_echappement' => 'Pièces échappement',
                'pieces_carrosserie' => 'Pièces carrosserie',
                'pieces_eclairage' => 'Pièces éclairage',
                'accessoires' => 'Accessoires',
                'consommables' => 'Consommables',
                'autre' => 'Autres pièces'
            ]
        ],
        'carburant' => [
            'label' => 'Carburant',
            'icon' => 'lucide:fuel',
            'color' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
            'types' => [
                'essence' => 'Essence',
                'diesel' => 'Diesel',
                'gpl' => 'GPL',
                'electrique' => 'Recharge électrique',
                'hybride' => 'Hybride',
                'adblue' => 'AdBlue',
                'autre' => 'Autre carburant'
            ]
        ],
        'assurance' => [
            'label' => 'Assurance',
            'icon' => 'lucide:shield',
            'color' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800'],
            'types' => [
                'responsabilite_civile' => 'Responsabilité civile',
                'tous_risques' => 'Tous risques',
                'vol_incendie' => 'Vol et incendie',
                'bris_glace' => 'Bris de glace',
                'assistance' => 'Assistance',
                'protection_juridique' => 'Protection juridique',
                'garantie_conducteur' => 'Garantie conducteur',
                'franchise' => 'Franchise',
                'autre' => 'Autre assurance'
            ]
        ],
        'controle_technique' => [
            'label' => 'Contrôle technique',
            'icon' => 'lucide:clipboard-check',
            'color' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-800'],
            'types' => [
                'controle_initial' => 'Contrôle initial',
                'contre_visite' => 'Contre-visite',
                'controle_pollution' => 'Contrôle pollution',
                'controle_complementaire' => 'Contrôle complémentaire',
                'autre' => 'Autre contrôle'
            ]
        ],
        'vignette' => [
            'label' => 'Vignette / Taxes',
            'icon' => 'lucide:receipt',
            'color' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
            'types' => [
                'vignette_annuelle' => 'Vignette annuelle',
                'taxe_circulation' => 'Taxe de circulation',
                'taxe_co2' => 'Taxe CO2',
                'ecotaxe' => 'Écotaxe',
                'patente' => 'Patente',
                'autre' => 'Autre taxe'
            ]
        ],
        'amendes' => [
            'label' => 'Amendes',
            'icon' => 'lucide:alert-triangle',
            'color' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
            'types' => [
                'exces_vitesse' => 'Excès de vitesse',
                'stationnement' => 'Stationnement',
                'feu_rouge' => 'Feu rouge',
                'telephone' => 'Téléphone au volant',
                'ceinture' => 'Ceinture de sécurité',
                'alcool' => 'Alcoolémie',
                'defaut_documents' => 'Défaut de documents',
                'defaut_equipement' => 'Défaut d\'équipement',
                'autre' => 'Autre amende'
            ]
        ],
        'peage' => [
            'label' => 'Péage',
            'icon' => 'lucide:route',
            'color' => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-800'],
            'types' => [
                'autoroute' => 'Autoroute',
                'pont' => 'Pont',
                'tunnel' => 'Tunnel',
                'badge_telepayage' => 'Badge télépéage',
                'abonnement' => 'Abonnement péage',
                'autre' => 'Autre péage'
            ]
        ],
        'parking' => [
            'label' => 'Parking / Stationnement',
            'icon' => 'lucide:car',
            'color' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-800'],
            'types' => [
                'parking_horaire' => 'Parking horaire',
                'parking_journalier' => 'Parking journalier',
                'parking_mensuel' => 'Parking mensuel',
                'parking_annuel' => 'Parking annuel',
                'horodateur' => 'Horodateur',
                'garage' => 'Garage',
                'autre' => 'Autre parking'
            ]
        ],
        'lavage' => [
            'label' => 'Lavage / Nettoyage',
            'icon' => 'lucide:droplet',
            'color' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-800'],
            'types' => [
                'lavage_exterieur' => 'Lavage extérieur',
                'lavage_interieur' => 'Nettoyage intérieur',
                'lavage_complet' => 'Lavage complet',
                'lavage_moteur' => 'Lavage moteur',
                'detailing' => 'Detailing',
                'produits_entretien' => 'Produits d\'entretien',
                'autre' => 'Autre nettoyage'
            ]
        ],
        'transport' => [
            'label' => 'Transport / Remorquage',
            'icon' => 'lucide:truck',
            'color' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800'],
            'types' => [
                'remorquage' => 'Remorquage',
                'depannage' => 'Dépannage',
                'transport_vehicule' => 'Transport véhicule',
                'location_remorque' => 'Location remorque',
                'ferry' => 'Ferry',
                'train_auto' => 'Train auto',
                'autre' => 'Autre transport'
            ]
        ],
        'formation_chauffeur' => [
            'label' => 'Formation chauffeur',
            'icon' => 'lucide:graduation-cap',
            'color' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800'],
            'types' => [
                'permis_conduire' => 'Permis de conduire',
                'formation_securite' => 'Formation sécurité',
                'eco_conduite' => 'Éco-conduite',
                'conduite_defensive' => 'Conduite défensive',
                'formation_reglementaire' => 'Formation réglementaire',
                'stage_recuperation' => 'Stage récupération points',
                'autre' => 'Autre formation'
            ]
        ],
        'autre' => [
            'label' => 'Autre',
            'icon' => 'lucide:more-horizontal',
            'color' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
            'types' => [
                'location' => 'Location véhicule',
                'leasing' => 'Leasing',
                'carte_grise' => 'Carte grise',
                'expertise' => 'Expertise',
                'frais_administratifs' => 'Frais administratifs',
                'equipement' => 'Équipement',
                'divers' => 'Divers'
            ]
        ]
    ],

    /**
     * Mapping des anciennes valeurs vers les nouvelles
     * Pour assurer la compatibilité
     */
    'legacy_mapping' => [
        'maintenance' => 'maintenance_preventive',
        'fuel' => 'carburant',
        'repair' => 'reparation',
        'insurance' => 'assurance',
        'tolls' => 'peage',
        'fines' => 'amendes',
        'other' => 'autre',
        'taxe' => 'vignette',
        'amende' => 'amendes'
    ],

    /**
     * Catégories nécessitant une approbation automatique
     */
    'requires_approval' => [
        'reparation' => 5000, // Si > 5000€
        'pieces_detachees' => 3000, // Si > 3000€
        'assurance' => 0, // Toujours
        'amendes' => 0, // Toujours
        'autre' => 1000 // Si > 1000€
    ],

    /**
     * Catégories avec TVA par défaut (ALGÉRIE)
     * Taux applicables: 0% (exonéré), 9% (réduit), 19% (normal)
     */
    'default_tva_rates' => [
        'carburant' => 19,
        'maintenance_preventive' => 19,
        'reparation' => 19,
        'pieces_detachees' => 19,
        'parking' => 19,
        'peage' => 19,
        'lavage' => 19,
        'transport' => 19,
        'assurance' => 0, // Pas de TVA
        'vignette' => 0, // Pas de TVA
        'amendes' => 0, // Pas de TVA
        'controle_technique' => 19,
        'formation_chauffeur' => 9, // Taux réduit pour formation
        'autre' => 19
    ]
];
