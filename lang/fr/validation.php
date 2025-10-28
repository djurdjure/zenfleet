<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Messages de validation en Français
    |--------------------------------------------------------------------------
    |
    | Les lignes suivantes contiennent les messages d'erreur par défaut utilisés
    | par la classe de validation. Certaines de ces règles ont plusieurs versions,
    | comme les règles de taille.
    |
    */

    'accepted' => 'Le champ :attribute doit être accepté.',
    'accepted_if' => 'Le champ :attribute doit être accepté lorsque :other est :value.',
    'active_url' => 'Le champ :attribute n\'est pas une URL valide.',
    'after' => 'Le champ :attribute doit être une date postérieure au :date.',
    'after_or_equal' => 'Le champ :attribute doit être une date postérieure ou égale au :date.',
    'alpha' => 'Le champ :attribute ne peut contenir que des lettres.',
    'alpha_dash' => 'Le champ :attribute ne peut contenir que des lettres, des chiffres, des tirets et des underscores.',
    'alpha_num' => 'Le champ :attribute ne peut contenir que des lettres et des chiffres.',
    'array' => 'Le champ :attribute doit être un tableau.',
    'before' => 'Le champ :attribute doit être une date antérieure au :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date antérieure ou égale au :date.',
    'between' => [
        'array' => 'Le champ :attribute doit avoir entre :min et :max éléments.',
        'file' => 'Le fichier :attribute doit peser entre :min et :max kilo-octets.',
        'numeric' => 'Le champ :attribute doit être compris entre :min et :max.',
        'string' => 'Le champ :attribute doit contenir entre :min et :max caractères.',
    ],
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => 'Le champ :attribute n\'est pas une date valide.',
    'date_equals' => 'Le champ :attribute doit être une date égale à :date.',
    'date_format' => 'Le champ :attribute ne correspond pas au format :format.',
    'declined' => 'Le champ :attribute doit être décliné.',
    'declined_if' => 'Le champ :attribute doit être décliné lorsque :other est :value.',
    'different' => 'Les champs :attribute et :other doivent être différents.',
    'digits' => 'Le champ :attribute doit contenir :digits chiffres.',
    'digits_between' => 'Le champ :attribute doit contenir entre :min et :max chiffres.',
    'dimensions' => 'Les dimensions de l\'image :attribute ne sont pas valides.',
    'distinct' => 'Le champ :attribute a une valeur en double.',
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'ends_with' => 'Le champ :attribute doit se terminer par une des valeurs suivantes : :values.',
    'enum' => 'Le champ :attribute sélectionné est invalide.',
    'exists' => 'Le :attribute sélectionné est invalide.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'filled' => 'Le champ :attribute doit avoir une valeur.',
    'gt' => [
        'array' => 'Le champ :attribute doit avoir plus de :value éléments.',
        'file' => 'Le fichier :attribute doit peser plus de :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être supérieur à :value.',
        'string' => 'Le champ :attribute doit contenir plus de :value caractères.',
    ],
    'gte' => [
        'array' => 'Le champ :attribute doit avoir au moins :value éléments.',
        'file' => 'Le fichier :attribute doit peser au moins :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être supérieur ou égal à :value.',
        'string' => 'Le champ :attribute doit contenir au moins :value caractères.',
    ],
    'image' => 'Le champ :attribute doit être une image.',
    'in' => 'Le champ :attribute sélectionné est invalide.',
    'in_array' => 'Le champ :attribute n\'existe pas dans :other.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'ip' => 'Le champ :attribute doit être une adresse IP valide.',
    'ipv4' => 'Le champ :attribute doit être une adresse IPv4 valide.',
    'ipv6' => 'Le champ :attribute doit être une adresse IPv6 valide.',
    'json' => 'Le champ :attribute doit être une chaîne JSON valide.',
    'lt' => [
        'array' => 'Le champ :attribute doit avoir moins de :value éléments.',
        'file' => 'Le fichier :attribute doit peser moins de :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être inférieur à :value.',
        'string' => 'Le champ :attribute doit contenir moins de :value caractères.',
    ],
    'lte' => [
        'array' => 'Le champ :attribute ne doit pas avoir plus de :value éléments.',
        'file' => 'Le fichier :attribute ne doit pas peser plus de :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être inférieur ou égal à :value.',
        'string' => 'Le champ :attribute ne doit pas contenir plus de :value caractères.',
    ],
    'mac_address' => 'Le champ :attribute doit être une adresse MAC valide.',
    'max' => [
        'array' => 'Le champ :attribute ne doit pas avoir plus de :max éléments.',
        'file' => 'Le fichier :attribute ne doit pas peser plus de :max kilo-octets.',
        'numeric' => 'Le champ :attribute ne doit pas être supérieur à :max.',
        'string' => 'Le champ :attribute ne doit pas contenir plus de :max caractères.',
    ],
    'mimes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'mimetypes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'min' => [
        'array' => 'Le champ :attribute doit avoir au moins :min éléments.',
        'file' => 'Le fichier :attribute doit peser au moins :min kilo-octets.',
        'numeric' => 'Le champ :attribute doit être supérieur ou égal à :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'multiple_of' => 'Le champ :attribute doit être un multiple de :value.',
    'not_in' => 'Le champ :attribute sélectionné est invalide.',
    'not_regex' => 'Le format du champ :attribute est invalide.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'password' => [
        'letters' => 'Le champ :attribute doit contenir au moins une lettre.',
        'mixed' => 'Le champ :attribute doit contenir au moins une majuscule et une minuscule.',
        'numbers' => 'Le champ :attribute doit contenir au moins un chiffre.',
        'symbols' => 'Le champ :attribute doit contenir au moins un symbole.',
        'uncompromised' => 'Le :attribute donné est apparu dans une fuite de données. Veuillez choisir un :attribute différent.',
    ],
    'present' => 'Le champ :attribute doit être présent.',
    'prohibited' => 'Le champ :attribute est interdit.',
    'prohibited_if' => 'Le champ :attribute est interdit lorsque :other est :value.',
    'prohibited_unless' => 'Le champ :attribute est interdit sauf si :other est dans :values.',
    'prohibits' => 'Le champ :attribute interdit la présence de :other.',
    'regex' => 'Le format du champ :attribute est invalide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'required_array_keys' => 'Le champ :attribute doit contenir des entrées pour : :values.',
    'required_if' => 'Le champ :attribute est obligatoire lorsque :other est :value.',
    'required_unless' => 'Le champ :attribute est obligatoire sauf si :other est dans :values.',
    'required_with' => 'Le champ :attribute est obligatoire lorsque :values est présent.',
    'required_with_all' => 'Le champ :attribute est obligatoire lorsque :values sont présents.',
    'required_without' => 'Le champ :attribute est obligatoire lorsque :values n\'est pas présent.',
    'required_without_all' => 'Le champ :attribute est obligatoire lorsqu\'aucun de :values n\'est présent.',
    'same' => 'Les champs :attribute et :other doivent correspondre.',
    'size' => [
        'array' => 'Le champ :attribute doit contenir :size éléments.',
        'file' => 'Le fichier :attribute doit peser :size kilo-octets.',
        'numeric' => 'Le champ :attribute doit être égal à :size.',
        'string' => 'Le champ :attribute doit contenir :size caractères.',
    ],
    'starts_with' => 'Le champ :attribute doit commencer par une des valeurs suivantes : :values.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone' => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique' => 'Le champ :attribute a déjà été pris.',
    'uploaded' => 'Le téléchargement du champ :attribute a échoué.',
    'url' => 'Le champ :attribute doit être une URL valide.',
    'uuid' => 'Le champ :attribute doit être un UUID valide.',

    /*
    |--------------------------------------------------------------------------
    | Messages de validation personnalisés
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'supplier_id' => [
            'exists' => 'Le fournisseur sélectionné n\'est pas valide ou n\'existe pas.',
        ],
        'vehicle_id' => [
            'required' => 'Vous devez sélectionner un véhicule.',
            'exists' => 'Le véhicule sélectionné n\'existe pas.',
        ],
        'expense_category' => [
            'required' => 'Vous devez sélectionner une catégorie de dépense.',
        ],
        'expense_type' => [
            'required' => 'Vous devez décrire la nature de la dépense.',
        ],
        'amount_ht' => [
            'required' => 'Le montant HT est obligatoire.',
            'numeric' => 'Le montant HT doit être un nombre.',
            'min' => 'Le montant HT doit être supérieur à 0.',
            'max' => 'Le montant HT est trop élevé.',
        ],
        'expense_date' => [
            'required' => 'La date de la dépense est obligatoire.',
            'date' => 'La date de la dépense n\'est pas valide.',
            'before_or_equal' => 'La date de la dépense ne peut pas être dans le futur.',
        ],
        'description' => [
            'required' => 'Une description de la dépense est obligatoire.',
            'min' => 'La description doit contenir au moins :min caractères.',
            'max' => 'La description ne doit pas dépasser :max caractères.',
        ],
        'invoice_number' => [
            'max' => 'Le numéro de facture ne doit pas dépasser :max caractères.',
        ],
        'invoice_date' => [
            'date' => 'La date de facture n\'est pas valide.',
            'before_or_equal' => 'La date de facture ne peut pas être dans le futur.',
        ],
        'odometer_reading' => [
            'required' => 'Le kilométrage est obligatoire pour une dépense de carburant.',
            'integer' => 'Le kilométrage doit être un nombre entier.',
            'min' => 'Le kilométrage ne peut pas être négatif.',
        ],
        'fuel_quantity' => [
            'required' => 'La quantité de carburant est obligatoire.',
            'numeric' => 'La quantité de carburant doit être un nombre.',
            'min' => 'La quantité de carburant doit être supérieure à 0.',
        ],
        'fuel_price_per_liter' => [
            'required' => 'Le prix par litre est obligatoire pour une dépense de carburant.',
            'numeric' => 'Le prix par litre doit être un nombre.',
            'min' => 'Le prix par litre doit être supérieur à 0.',
        ],
        'fuel_type' => [
            'required' => 'Le type de carburant est obligatoire.',
        ],
        'tva_rate' => [
            'numeric' => 'Le taux de TVA doit être un nombre.',
            'min' => 'Le taux de TVA ne peut pas être négatif.',
            'max' => 'Le taux de TVA ne peut pas dépasser 100%.',
        ],
        'attachments.*' => [
            'file' => 'Le document doit être un fichier valide.',
            'mimes' => 'Le document doit être de type : JPG, PNG, PDF, DOC ou DOCX.',
            'max' => 'Le document ne doit pas dépasser 5 MB.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Attributs de validation personnalisés
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'supplier_id' => 'fournisseur',
        'vehicle_id' => 'véhicule',
        'expense_category' => 'catégorie de dépense',
        'expense_type' => 'type de dépense',
        'expense_subtype' => 'sous-type de dépense',
        'amount_ht' => 'montant HT',
        'tva_rate' => 'taux TVA',
        'tva_amount' => 'montant TVA',
        'total_ttc' => 'total TTC',
        'expense_date' => 'date de la dépense',
        'description' => 'description',
        'invoice_number' => 'numéro de facture',
        'invoice_date' => 'date de facture',
        'receipt_number' => 'numéro de reçu',
        'payment_method' => 'méthode de paiement',
        'payment_status' => 'statut de paiement',
        'odometer_reading' => 'kilométrage',
        'fuel_quantity' => 'quantité de carburant',
        'fuel_price_per_liter' => 'prix par litre',
        'fuel_type' => 'type de carburant',
        'internal_notes' => 'notes internes',
        'approval_deadline' => 'date limite d\'approbation',
        'priority_level' => 'niveau de priorité',
        'cost_center' => 'centre de coût',
        'attachments' => 'pièces jointes',
        'driver_id' => 'chauffeur',
        'expense_group_id' => 'groupe de dépenses',
    ],
];
