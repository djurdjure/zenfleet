
<!DOCTYPE html>
<html lang="fr" class="h-full bg-zinc-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="bdDbWyzYEX2AzpLtVeiHgZZ6OO6SseR7X892Vy7G">
        <meta name="user-data" content="{&quot;id&quot;:4,&quot;name&quot;:&quot;admin zenfleet&quot;,&quot;role&quot;:&quot;Admin&quot;}">
    
    <title>Ajouter un Nouveau Chauffeur - ZenFleet</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    

    
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    
    <link rel="stylesheet" href="/vendor/css/flatpickr.css">
    <link rel="stylesheet" href="/vendor/css/slim-select.css">
    <style>
        /* 🎨 FLATPICKR ENTERPRISE-GRADE LIGHT MODE - ZenFleet Ultra-Pro */
        .flatpickr-calendar {
            background-color: white !important;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.75rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            font-family: inherit;
        }

        /* En-tête (mois/année) - Bleu blue-600 premium */
        .flatpickr-months {
            background: rgb(37 99 235) !important;
            border-radius: 0.75rem 0.75rem 0 0;
            padding: 0.875rem 0;
        }

        .flatpickr-months .flatpickr-month,
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            background-color: transparent !important;
            color: white !important;
            font-weight: 600;
            font-size: 1rem;
        }

        /* Boutons navigation */
        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            fill: white !important;
            transition: all 0.2s;
        }

        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            fill: rgb(219 234 254) !important;
            transform: scale(1.15);
        }

        /* Jours de la semaine */
        .flatpickr-weekdays {
            background-color: rgb(249 250 251) !important;
            padding: 0.625rem 0;
            border-bottom: 1px solid rgb(229 231 235);
        }

        .flatpickr-weekday {
            color: rgb(107 114 128) !important;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Corps du calendrier */
        .flatpickr-days {
            background-color: white !important;
        }

        /* Jours du mois */
        .flatpickr-day {
            color: rgb(17 24 39) !important;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .flatpickr-day.today {
            border: 2px solid rgb(37 99 235) !important;
            font-weight: 700;
            color: rgb(37 99 235) !important;
            background-color: rgb(239 246 255) !important;
        }

        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background-color: rgb(37 99 235) !important;
            border-color: rgb(37 99 235) !important;
            color: white !important;
            font-weight: 700;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
        }

        .flatpickr-day:hover:not(.selected):not(.flatpickr-disabled):not(.today) {
            background-color: rgb(243 244 246) !important;
            border-color: rgb(229 231 235) !important;
            color: rgb(17 24 39) !important;
            transform: scale(1.05);
        }

        .flatpickr-day.flatpickr-disabled {
            color: rgb(209 213 219) !important;
            opacity: 0.4;
        }
    </style>

    <link rel="preload" as="style" href="http://localhost/build/assets/app-B3VcwZDs.css" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/app-Dx8zybLc.js" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/slimselect-CAvN1F7Q.js" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/vendor-common-B9ygI19o.js" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/ui-public-2hikc2V1.js" /><link rel="stylesheet" href="http://localhost/build/assets/app-B3VcwZDs.css" data-navigate-track="reload" /><script type="module" src="http://localhost/build/assets/app-Dx8zybLc.js" data-navigate-track="reload"></script>    <style>
    /* ========================================
   ZENFLEET SLIMSELECT ENTERPRISE THEME
   ======================================== */

    :root {
        --ss-main-height: 42px;
        --ss-primary-color: #2563eb;
        /* blue-600 */
        --ss-bg-color: #ffffff;
        --ss-font-color: #111827;
        /* gray-900 */
        --ss-font-placeholder-color: #9ca3af;
        /* gray-400 */
        --ss-border-color: #d1d5db;
        /* gray-300 */
        --ss-border-radius: 0.5rem;
        /* rounded-lg */
        --ss-spacing-l: 10px;
        --ss-spacing-m: 8px;
        --ss-spacing-s: 4px;
        --ss-animation-timing: 0.2s;
        --ss-focus-color: #3b82f6;
        /* blue-500 */
        --ss-error-color: #dc2626;
        /* red-600 */
    }

    /* Main container styling */
    .ss-main {
        background-color: #f9fafb;
        /* gray-50 */
        border-color: #d1d5db;
        /* gray-300 */
        color: #111827;
        /* gray-900 */
        border-radius: 0.5rem;
        /* rounded-lg */
        padding: 2px 0;
        /* Ajustement padding */
        min-height: 42px;
        /* Hauteur minimale */
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        /* shadow-sm */
    }

    /* Focus state */
    .ss-main:focus-within {
        border-color: var(--ss-focus-color);
        box-shadow: 0 0 0 1px var(--ss-focus-color);
        /* ring-1 */
        background-color: #ffffff;
    }

    /* Values styling */
    .ss-main .ss-values .ss-single {
        padding: 4px var(--ss-spacing-l);
        font-size: 0.875rem;
        /* text-sm = 14px */
        line-height: 1.25rem;
        /* leading-5 */
        font-weight: 400;
    }

    /* Placeholder styling */
    .ss-main .ss-values .ss-placeholder {
        font-size: 0.875rem;
        font-style: normal;
    }

    /* Dropdown content - ombre plus prononcée */
    .ss-content {
        margin-top: 4px;
        box-shadow:
            0 10px 15px -3px rgba(0, 0, 0, 0.1),
            /* shadow-lg */
            0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border-color: #e5e7eb;
        /* gray-200 */
    }

    /* Champ de recherche */
    .ss-content .ss-search {
        background-color: #f9fafb;
        /* gray-50 */
        border-bottom: 1px solid #e5e7eb;
        /* gray-200 */
        padding: var(--ss-spacing-m);
    }

    .ss-content .ss-search input {
        font-size: 0.875rem;
        padding: 10px 12px;
        border-radius: 6px;
        /* rounded-md */
    }

    .ss-content .ss-search input:focus {
        border-color: var(--ss-focus-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Options - style hover amélioré */
    .ss-content .ss-list .ss-option {
        font-size: 0.875rem;
        padding: 10px var(--ss-spacing-l);
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .ss-content .ss-list .ss-option:hover {
        background-color: #eff6ff;
        /* blue-50 */
        color: var(--ss-font-color);
        /* Garder texte lisible */
    }

    /* Option sélectionnée - fond plus subtil */
    .ss-content .ss-list .ss-option.ss-highlighted,
    .ss-content .ss-list .ss-option:not(.ss-disabled).ss-selected {
        background-color: var(--ss-primary-color);
        color: #ffffff;
    }

    /* Option sélectionnée avec checkmark */
    .ss-content .ss-list .ss-option:not(.ss-disabled).ss-selected::after {
        content: '✓';
        margin-left: auto;
        font-weight: 600;
    }

    /* État d'erreur de validation */
    .slimselect-error .ss-main {
        border-color: var(--ss-error-color) !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
        /* ring-red-600/10 */
    }

    /* Cacher le placeholder dans la liste des options */
    .ss-content .ss-list .ss-option[data-placeholder="true"] {
        display: none !important;
    }

    /* Message d'erreur */
    .ss-content .ss-list .ss-error {
        font-size: 0.875rem;
        padding: var(--ss-spacing-l);
    }

    /* Message de recherche en cours */
    .ss-content .ss-list .ss-searching {
        font-size: 0.875rem;
        color: var(--ss-primary-color);
        padding: var(--ss-spacing-l);
    }

    /* Flèche de dropdown */
    .ss-main .ss-arrow path {
        stroke-width: 14;
    }

    /* Animation d'ouverture du dropdown */
    .ss-content.ss-open-below,
    .ss-content.ss-open-above {
        animation: zenfleetSlideIn var(--ss-animation-timing) ease-out;
    }

    @keyframes zenfleetSlideIn {
        from {
            opacity: 0;
            transform: scaleY(0.95) translateY(-4px);
        }

        to {
            opacity: 1;
            transform: scaleY(1) translateY(0);
        }
    }

    /* ========================================
   RESPONSIVE MOBILE
   ======================================== */
    @media (max-width: 640px) {
        :root {
            --ss-main-height: 44px;
            /* Plus grand pour touch */
            --ss-content-height: 240px;
        }

        .ss-content .ss-list .ss-option {
            padding: 12px var(--ss-spacing-l);
            /* Touch-friendly */
            min-height: 44px;
            /* iOS minimum */
        }

        .ss-content .ss-search input {
            padding: 12px;
            font-size: 16px;
            /* Évite zoom iOS */
        }
    }

    /* ========================================
   ACCESSIBILITÉ
   ======================================== */
    @media (prefers-reduced-motion: reduce) {

        .ss-main,
        .ss-content,
        .ss-option {
            transition: none !important;
            animation: none !important;
        }
    }
</style>    <!-- Livewire Styles --><style >[wire\:loading][wire\:loading], [wire\:loading\.delay][wire\:loading\.delay], [wire\:loading\.inline-block][wire\:loading\.inline-block], [wire\:loading\.inline][wire\:loading\.inline], [wire\:loading\.block][wire\:loading\.block], [wire\:loading\.flex][wire\:loading\.flex], [wire\:loading\.table][wire\:loading\.table], [wire\:loading\.grid][wire\:loading\.grid], [wire\:loading\.inline-flex][wire\:loading\.inline-flex] {display: none;}[wire\:loading\.delay\.none][wire\:loading\.delay\.none], [wire\:loading\.delay\.shortest][wire\:loading\.delay\.shortest], [wire\:loading\.delay\.shorter][wire\:loading\.delay\.shorter], [wire\:loading\.delay\.short][wire\:loading\.delay\.short], [wire\:loading\.delay\.default][wire\:loading\.delay\.default], [wire\:loading\.delay\.long][wire\:loading\.delay\.long], [wire\:loading\.delay\.longer][wire\:loading\.delay\.longer], [wire\:loading\.delay\.longest][wire\:loading\.delay\.longest] {display: none;}[wire\:offline][wire\:offline] {display: none;}[wire\:dirty]:not(textarea):not(input):not(select) {display: none;}:root {--livewire-progress-bar-color: #2299dd;}[x-cloak] {display: none !important;}[wire\:cloak] {display: none !important;}</style>
</head>

<body class="h-full">
    <div class="min-h-full">
        
        <div class="hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
            <div class="flex grow flex-col overflow-hidden bg-[#eef2f7] border-r border-gray-200/60 shadow-sm">
                
                <div class="w-full flex-none px-4 py-4 h-16 flex items-center border-b border-gray-300/50">
                    <div class="flex items-center w-full">
                        <div class="relative mr-3">
                            <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-md">
                                <span
    class="iconify block w-5 h-5 text-white"
    data-icon="mdi:truck-fast"
    data-inline="false"></span>                            </div>
                        </div>
                        <div class="flex-1">
                            <span class="text-gray-800 text-lg font-bold tracking-tight">ZenFleet</span>
                            <div class="text-xs text-gray-600 font-medium">Fleet Management</div>
                        </div>
                    </div>
                </div>

                
                <div class="flex flex-col flex-1 overflow-hidden">
                    <ul class="grow overflow-x-hidden overflow-y-auto w-full px-2 py-4 mb-0 scrollbar-thin scrollbar-thumb-gray-400/30 scrollbar-track-transparent" role="tree">
                        
                        <li class="flex">
                                                        <a href="http://localhost/admin/dashboard"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
                                <span
    class="iconify block w-5 h-5 mr-3 text-gray-600"
    data-icon="material-symbols:dashboard-rounded"
    data-inline="false"></span>                                <span class="flex-1">Dashboard</span>
                            </a>
                        </li>

                        
                        
                        
                                                <li class="flex flex-col" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
                                <span
    class="iconify block w-5 h-5 mr-3 text-gray-600"
    data-icon="mdi:car-multiple"
    data-inline="false"></span>                                <span class="flex-1 text-left">Véhicules</span>
                                <span
    class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
    data-icon="heroicons:chevron-down"
    data-inline="false"></span>                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
                                <div class="flex w-full mt-2 pl-3">
                                    <div class="mr-1">
                                        <div class="px-1 py-2 h-full relative">
                                            <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
                                                                                        <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
                                                x-bind:style="`height: 0%; top: 0%;`"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1.5">
                                                                                <a href="http://localhost/admin/vehicles"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="mdi:format-list-bulleted"
    data-inline="false"></span>                                            Gestion Véhicules
                                        </a>
                                                                                                                        <a href="http://localhost/admin/assignments"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="mdi:clipboard-text"
    data-inline="false"></span>                                            Affectations
                                        </a>
                                                                            </div>
                                </div>
                            </div>
                        </li>
                        
                        
                                                <li class="flex flex-col" x-data="{ open: true }">
                            <button @click="open = !open"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-blue-600 text-white shadow-md">
                                <span
    class="iconify block w-5 h-5 mr-3 text-white"
    data-icon="mdi:account-group"
    data-inline="false"></span>                                <span class="flex-1 text-left">Chauffeurs</span>
                                <span
    class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
    data-icon="heroicons:chevron-down"
    data-inline="false"></span>                            </button>
                            <div x-show="open"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 max-h-0"
                                x-transition:enter-end="opacity-100 max-h-96"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 max-h-96"
                                x-transition:leave-end="opacity-0 max-h-0"
                                class="overflow-hidden">
                                <div class="flex w-full mt-2 pl-3">
                                    <div class="mr-1">
                                        <div class="px-1 py-2 h-full relative">
                                            <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
                                                                                        <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
                                                x-bind:style="`height: 0%; top: 0%;`"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                                                                <a href="http://localhost/admin/drivers"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="mdi:view-list"
    data-inline="false"></span>                                            Liste
                                        </a>
                                                                                                                        <a href="http://localhost/admin/drivers/sanctions"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="mdi:gavel"
    data-inline="false"></span>                                            Sanctions
                                        </a>
                                                                            </div>
                                </div>
                            </div>
                        </li>
                        
                        
                                                <li class="flex">
                            <a href="http://localhost/admin/depots"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
                                <span
    class="iconify block w-5 h-5 mr-3 text-gray-600"
    data-icon="mdi:office-building"
    data-inline="false"></span>                                <span class="flex-1">Dépôts</span>
                            </a>
                        </li>
                        
                        
                        
                        
                                                <li class="flex flex-col" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
                                <span
    class="iconify block w-5 h-5 mr-3 text-gray-600"
    data-icon="mdi:speedometer"
    data-inline="false"></span>                                <span class="flex-1 text-left">Kilométrage</span>
                                <span
    class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
    data-icon="heroicons:chevron-down"
    data-inline="false"></span>                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
                                <div class="flex w-full mt-2 pl-3">
                                    <div class="mr-1">
                                        <div class="px-1 py-2 h-full relative">
                                            <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
                                                                                        <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300 h-1/2"
                                                x-bind:style="`top: 0%;`"></div>
                                        </div>
                                    </div>
                                    <ul class="flex-1 space-y-1 pb-2">
                                        
                                        <li>
                                                                                        <a href="http://localhost/admin/mileage-readings"
                                                class="flex items-center h-9 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                                <span
    class="iconify block w-5 h-5 mr-2 text-gray-600"
    data-icon="mdi:history"
    data-inline="false"></span>                                                Historique
                                            </a>
                                        </li>
                                        
                                                                                <li>
                                                                                        <a href="http://localhost/admin/mileage-readings/update"
                                                class="flex items-center h-9 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                                <span
    class="iconify block w-5 h-5 mr-2 text-gray-600"
    data-icon="mdi:pencil"
    data-inline="false"></span>                                                Mettre à jour
                                            </a>
                                        </li>
                                                                            </ul>
                                </div>
                            </div>
                        </li>
                        
                        
                                                <li class="flex flex-col" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
                                <span
    class="iconify block w-5 h-5 mr-3 text-gray-600"
    data-icon="lucide:wrench"
    data-inline="false"></span>                                <span class="flex-1 text-left">Maintenance</span>
                                <span
    class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
    data-icon="lucide:chevron-down"
    data-inline="false"></span>                            </button>
                            <div x-show="open"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 max-h-0"
                                x-transition:enter-end="opacity-100 max-h-[500px]"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 max-h-[500px]"
                                x-transition:leave-end="opacity-0 max-h-0"
                                class="overflow-hidden">
                                <div class="flex w-full mt-2 pl-3">
                                    <div class="mr-1">
                                        <div class="px-1 py-2 h-full relative">
                                            <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
                                                                                        <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
                                                x-bind:style="`height: 0%; top: 0%;`"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                        
                                        <a href="http://localhost/admin/maintenance/dashboard"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="lucide:layout-dashboard"
    data-inline="false"></span>                                            Vue d'ensemble
                                        </a>

                                        
                                        <a href="http://localhost/admin/maintenance/operations"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="lucide:list"
    data-inline="false"></span>                                            Opérations
                                        </a>

                                        
                                        <a href="http://localhost/admin/maintenance/operations/kanban"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="lucide:columns-3"
    data-inline="false"></span>                                            Kanban
                                        </a>

                                        
                                        <a href="http://localhost/admin/maintenance/operations/calendar"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="lucide:calendar-days"
    data-inline="false"></span>                                            Calendrier
                                        </a>

                                        
                                        <a href="http://localhost/admin/maintenance/schedules"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="lucide:repeat"
    data-inline="false"></span>                                            Planifications
                                        </a>

                                        
                                                                                <a href="http://localhost/admin/repair-requests"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="lucide:hammer"
    data-inline="false"></span>                                            Demandes Réparation
                                        </a>
                                                                            </div>
                                </div>
                            </div>
                        </li>
                        
                        
                                                <li class="flex">
                            <a href="http://localhost/admin/alerts"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
                                <span
    class="iconify block w-5 h-5 mr-3 text-gray-600"
    data-icon="mdi:bell-ring"
    data-inline="false"></span>                                <span class="flex-1">Alertes</span>
                            </a>
                        </li>
                        
                        
                                                <li class="flex">
                            <a href="http://localhost/admin/documents"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
                                <span
    class="iconify block w-5 h-5 mr-3 text-gray-600"
    data-icon="mdi:file-document"
    data-inline="false"></span>                                <span class="flex-1">Documents</span>
                            </a>
                        </li>
                        
                        
                                                <li class="flex">
                            <a href="http://localhost/admin/suppliers"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
                                <span
    class="iconify block w-5 h-5 mr-3 text-gray-600"
    data-icon="mdi:store"
    data-inline="false"></span>                                <span class="flex-1">Fournisseurs</span>
                            </a>
                        </li>
                        
                        
                                                <li class="flex flex-col" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
                                <span
    class="iconify block w-5 h-5 mr-3 text-gray-600"
    data-icon="solar:wallet-money-bold"
    data-inline="false"></span>                                <span class="flex-1 text-left">Dépenses</span>
                                                                                                <span
    class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
    data-icon="lucide:chevron-down"
    data-inline="false"></span>                            </button>
                            <div x-show="open"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 max-h-0"
                                x-transition:enter-end="opacity-100 max-h-[400px]"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 max-h-[400px]"
                                x-transition:leave-end="opacity-0 max-h-0"
                                class="overflow-hidden">
                                <div class="flex w-full mt-2 pl-3">
                                    <div class="mr-1">
                                        <div class="px-1 py-2 h-full relative">
                                            <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
                                                                                        <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
                                                x-bind:style="`height: 0%; top: 0%;`"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                        
                                        <a href="http://localhost/admin/vehicle-expenses"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="lucide:layout-dashboard"
    data-inline="false"></span>                                            Tableau de bord
                                        </a>

                                        
                                                                                <a href="http://localhost/admin/vehicle-expenses/create"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="lucide:plus-circle"
    data-inline="false"></span>                                            Nouvelle dépense
                                        </a>
                                        
                                        
                                                                                <a href="http://localhost/admin/vehicle-expenses/dashboard"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="lucide:chart-line"
    data-inline="false"></span>                                            Analytics
                                        </a>
                                        
                                        
                                                                                <a href="http://localhost/admin/vehicle-expenses?filter=pending_approval"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="lucide:clock"
    data-inline="false"></span>                                            Approbations
                                                                                    </a>
                                        
                                        
                                        <a href="http://localhost/admin/vehicle-expenses?section=groups"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="lucide:wallet"
    data-inline="false"></span>                                            Budgets
                                        </a>

                                        
                                                                                <a href="http://localhost/admin/vehicle-expenses/export"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="lucide:download"
    data-inline="false"></span>                                            Export
                                        </a>
                                        
                                        
                                                                                <a href="http://localhost/admin/vehicle-expenses/analytics/cost-trends"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="lucide:trending-up"
    data-inline="false"></span>                                            TCO & Tendances
                                        </a>
                                                                            </div>
                                </div>
                            </div>
                        </li>
                        
                        
                                                <li class="flex">
                            <a href="http://localhost/admin/reports"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
                                <span
    class="iconify block w-5 h-5 mr-3 text-gray-600"
    data-icon="mdi:chart-bar"
    data-inline="false"></span>                                <span class="flex-1">Rapports</span>
                            </a>
                        </li>
                        
                        
                                                <li class="flex flex-col" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
                                <span
    class="iconify block w-5 h-5 mr-3 text-gray-600"
    data-icon="mdi:cog"
    data-inline="false"></span>                                <span class="flex-1 text-left">Administration</span>
                                <span
    class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
    data-icon="heroicons:chevron-down"
    data-inline="false"></span>                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
                                <div class="flex w-full mt-2 pl-3">
                                    <div class="mr-1">
                                        <div class="px-1 py-2 h-full relative">
                                            <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
                                                                                        <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
                                                x-bind:style="`height: 0%; top: 0%;`"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                                                                <a href="http://localhost/admin/users"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="mdi:account-multiple"
    data-inline="false"></span>                                            Utilisateurs
                                        </a>
                                                                                                                        <a href="http://localhost/admin/roles"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-gray-600"
    data-icon="mdi:shield-check"
    data-inline="false"></span>                                            Rôles & Permissions
                                        </a>
                                                                                                                                                                                                    </div>
                                </div>
                            </div>
                        </li>
                                            </ul>

                    
                </div>
            </div>
        </div>

        
        <div class="lg:hidden" x-data="{ open: false }">
            
            <div x-show="open"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="relative z-50 lg:hidden">
                <div class="fixed inset-0 bg-gray-900/80" @click="open = false"></div>

                <div class="fixed inset-0 flex">
                    <div x-show="open"
                        x-transition:enter="transition ease-in-out duration-300 transform"
                        x-transition:enter-start="-translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in-out duration-300 transform"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="-translate-x-full"
                        class="relative mr-16 flex w-full max-w-xs flex-1">
                        
                        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-zinc-50 px-6 pb-4">
                            
                            <div class="flex h-16 shrink-0 items-center">
                                <div class="flex items-center">
                                    <span
    class="iconify block w-6 h-6 text-zinc-900 mr-3"
    data-icon="heroicons:truck"
    data-inline="false"></span>                                    <span class="text-zinc-900 text-xl font-bold">ZenFleet</span>
                                </div>
                            </div>

                            
                            <nav class="flex flex-1 flex-col">
                                <ul role="list" class="flex flex-1 flex-col gap-y-2">
                                    <li>
                                        <ul role="list" class="-mx-2 space-y-1">
                                            
                                            <li>
                                                                                                <a href="http://localhost/admin/dashboard"
                                                    class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
                                                    <span
    class="iconify block h-5 w-5 shrink-0"
    data-icon="heroicons:home"
    data-inline="false"></span>                                                    Dashboard
                                                </a>
                                            </li>

                                            
                                            
                                            
                                                                                        <li x-data="{ open: false }">
                                                <button @click="open = !open"
                                                    class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
                                                    <span
    class="iconify block h-5 w-5 shrink-0"
    data-icon="heroicons:truck"
    data-inline="false"></span>                                                    <span class="flex-1 text-left">Véhicules</span>
                                                    <span
    class="iconify block h-4 w-4 transition-transform" :class="{ 'rotate-90': open }"
    data-icon="heroicons:chevron-right"
    data-inline="false"></span>                                                </button>
                                                <div x-show="open" x-transition class="mt-1">
                                                    <ul class="ml-6 space-y-1">
                                                        <li class="relative">
                                                            <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                            <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                            <a href="http://localhost/admin/vehicles"
                                                                class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
                                                                <span
    class="iconify block h-4 w-4 shrink-0"
    data-icon="heroicons:truck"
    data-inline="false"></span>                                                                Gestion Véhicules
                                                            </a>
                                                        </li>
                                                        <li class="relative">
                                                            <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                            <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                            <a href="http://localhost/admin/assignments"
                                                                class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
                                                                <span
    class="iconify block h-4 w-4 shrink-0"
    data-icon="heroicons:clipboard-document-list"
    data-inline="false"></span>                                                                Affectations
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            
                                            
                                                                                        <li>
                                                <a href="http://localhost/admin/drivers"
                                                    class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold bg-zinc-950 text-white">
                                                    <span
    class="iconify block h-5 w-5 shrink-0"
    data-icon="heroicons:user"
    data-inline="false"></span>                                                    Chauffeurs
                                                </a>
                                            </li>
                                            
                                            
                                                                                        <li>
                                                <a href="http://localhost/admin/depots"
                                                    class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
                                                    <span
    class="iconify block h-5 w-5 shrink-0"
    data-icon="mdi:office-building"
    data-inline="false"></span>                                                    Dépôts
                                                </a>
                                            </li>
                                            
                                            
                                                                                        <li x-data="{ open: false }">
                                                <button @click="open = !open"
                                                    class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
                                                    <span
    class="iconify block h-5 w-5 shrink-0"
    data-icon="mdi:cog"
    data-inline="false"></span>                                                    <span class="flex-1 text-left">Administration</span>
                                                    <span
    class="iconify block h-4 w-4 transition-transform" :class="{ 'rotate-90': open }"
    data-icon="heroicons:chevron-right"
    data-inline="false"></span>                                                </button>
                                                <div x-show="open" x-transition class="mt-1">
                                                    <ul class="ml-6 space-y-1">
                                                        <li class="relative">
                                                            <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                            <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                            <a href="http://localhost/admin/users"
                                                                class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
                                                                <span
    class="iconify block h-4 w-4 shrink-0"
    data-icon="mdi:account-multiple"
    data-inline="false"></span>                                                                Utilisateurs
                                                            </a>
                                                        </li>
                                                        <li class="relative">
                                                            <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                            <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                            <a href="http://localhost/admin/roles"
                                                                class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
                                                                <span
    class="iconify block h-4 w-4 shrink-0"
    data-icon="mdi:shield-check"
    data-inline="false"></span>                                                                Rôles & Permissions
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                                                                    </ul>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="sticky top-0 z-40 flex items-center gap-x-6 bg-white px-4 py-4 shadow-sm sm:px-6 lg:hidden">
                <button type="button" @click="open = true" class="-m-2.5 p-2.5 text-zinc-500 lg:hidden">
                    <span class="sr-only">Ouvrir la sidebar</span>
                    <span
    class="iconify block h-6 w-6"
    data-icon="heroicons:bars-3"
    data-inline="false"></span>                </button>
                <div class="flex-1 text-sm font-semibold leading-6 text-zinc-900">ZenFleet</div>
                <div class="h-8 w-8 bg-zinc-100 rounded-full flex items-center justify-center">
                    <span
    class="iconify block h-4 w-4 text-zinc-500"
    data-icon="heroicons:user"
    data-inline="false"></span>                </div>
            </div>
        </div>

        
        <div class="lg:pl-64">
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-zinc-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <div class="h-6 w-px bg-zinc-200 lg:hidden" aria-hidden="true"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="relative flex flex-1">
                        
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        
                        <div class="relative hidden lg:block">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span
    class="iconify block h-4 w-4 text-zinc-400"
    data-icon="heroicons:magnifying-glass"
    data-inline="false"></span>                            </div>
                            <input type="search"
                                placeholder="Rechercher..."
                                class="block w-64 rounded-md border-0 bg-white py-1.5 pl-10 pr-3 text-zinc-900 ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-zinc-600 sm:text-sm sm:leading-6">
                        </div>

                        
                        <div class="relative">
                            <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600 relative">
                                <span class="sr-only">Voir les notifications</span>
                                <span
    class="iconify block h-6 w-6"
    data-icon="mdi:bell-ring"
    data-inline="false"></span>                                <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                            </button>
                        </div>

                        
                        <div class="relative">
                            <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600 relative">
                                <span class="sr-only">Messages</span>
                                <span
    class="iconify block h-6 w-6"
    data-icon="heroicons:envelope"
    data-inline="false"></span>                                <span class="absolute -top-1 -right-1 h-4 w-4 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center">2</span>
                            </button>
                        </div>



                        
                        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-zinc-200" aria-hidden="true"></div>

                        
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="-m-1.5 flex items-center p-1.5 hover:bg-zinc-50 rounded-lg transition-colors">
                                <span class="sr-only">Ouvrir le menu utilisateur</span>
                                <div class="h-8 w-8 bg-gradient-to-br from-zinc-600 to-zinc-800 rounded-full flex items-center justify-center">
                                    <span
    class="iconify block text-white w-4 h-4"
    data-icon="heroicons:user"
    data-inline="false"></span>                                </div>
                                <span class="hidden lg:flex lg:items-center">
                                    <div class="ml-3 text-left">
                                        <div class="text-sm font-semibold leading-5 text-zinc-900">admin zenfleet</div>
                                        <div class="text-xs leading-4 text-zinc-500">Admin</div>
                                    </div>
                                    <span
    class="iconify block ml-2 h-4 w-4 text-zinc-500 transition-transform" :class="{ 'rotate-180': open }"
    data-icon="heroicons:chevron-down"
    data-inline="false"></span>                                </span>
                            </button>

                            <div x-show="open"
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 z-10 mt-2.5 w-56 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-zinc-900/5">

                                
                                <div class="px-4 py-3 border-b border-zinc-100">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-gradient-to-br from-zinc-600 to-zinc-800 rounded-full flex items-center justify-center">
                                            <span
    class="iconify block text-white w-5 h-5"
    data-icon="heroicons:user"
    data-inline="false"></span>                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-zinc-900">admin zenfleet</div>
                                            <div class="text-xs text-zinc-500">admin@zenfleet.dz</div>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="py-1">
                                    <a href="http://localhost/profile"
                                        class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                        <span
    class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"
    data-icon="heroicons:user-circle"
    data-inline="false"></span>                                        Mon Profil
                                    </a>
                                    <a href="#"
                                        class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                        <span
    class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"
    data-icon="mdi:cog"
    data-inline="false"></span>                                        Paramètres
                                    </a>
                                    <a href="#"
                                        class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                        <span
    class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"
    data-icon="heroicons:question-mark-circle"
    data-inline="false"></span>                                        Aide & Support
                                    </a>
                                    <div class="border-t border-zinc-100 my-1"></div>
                                    <form method="POST" action="http://localhost/logout">
                                        <input type="hidden" name="_token" value="bdDbWyzYEX2AzpLtVeiHgZZ6OO6SseR7X892Vy7G" autocomplete="off">                                        <button type="submit"
                                            class="group flex w-full items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                            <span
    class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"
    data-icon="heroicons:arrow-right-on-rectangle"
    data-inline="false"></span>                                            Se déconnecter
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    




<section class="bg-gray-50 min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                <span
    class="iconify block w-6 h-6 text-blue-600"
    data-icon="heroicons:user-plus"
    data-inline="false"></span>                Ajouter un Nouveau Chauffeur
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Complétez les 4 étapes pour enregistrer un chauffeur
            </p>
        </div>

        
        
        
        <div x-data="driverFormValidation()" x-init="init()">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-0 mb-6">
 
 <div class="w-full bg-white border-b border-gray-200 py-8">
    <div class="px-4 mx-auto">
        <ol class="flex items-start justify-center gap-0 w-full max-w-4xl mx-auto">
                            
                <li class="flex flex-col items-center relative flex-1">

                    
                    <div class="flex items-center w-full relative z-10">

                        
                        <div class="flex items-center justify-center w-12 h-12 rounded-full flex-shrink-0 bg-white border-2 transition-all duration-300 relative"
                            x-bind:class="{
                                'border-blue-600 shadow-lg shadow-blue-500/40': currentStep === 1,
                                'border-blue-600 shadow-md shadow-blue-500/20': currentStep &gt; 1,
                                'border-gray-300 shadow-sm': currentStep &lt; 1
                            }">

                            
                            <span class="iconify w-6 h-6 transition-colors duration-300"
                                x-bind:class="{
                                    'text-gray-400': currentStep === 1,      
                                    'text-blue-600': currentStep &gt; 1,   
                                    'text-gray-300': currentStep &lt; 1       
                                }"
                                x-bind:data-icon="'lucide:' + &quot;user&quot;"
                                data-inline="false">
                            </span>
                        </div>

                        
                                                    <div class="flex-1 h-1 mx-2 rounded-full transition-all duration-300"
                                x-bind:class="currentStep &gt; 1 ? 'bg-blue-600 shadow-sm' : 'bg-gray-300'">
                            </div>
                        
                    </div>

                    
                    <span class="mt-4 text-center text-sm font-semibold transition-all duration-200 whitespace-nowrap leading-snug"
                        x-bind:class="{
                            'text-blue-600 font-bold text-sm': currentStep === 1,
                            'text-blue-600 font-semibold': currentStep &gt; 1,
                            'text-gray-500': currentStep &lt; 1
                        }">
                        Informations Personnelles
                    </span>

                </li>
                            
                <li class="flex flex-col items-center relative flex-1">

                    
                    <div class="flex items-center w-full relative z-10">

                        
                        <div class="flex items-center justify-center w-12 h-12 rounded-full flex-shrink-0 bg-white border-2 transition-all duration-300 relative"
                            x-bind:class="{
                                'border-blue-600 shadow-lg shadow-blue-500/40': currentStep === 2,
                                'border-blue-600 shadow-md shadow-blue-500/20': currentStep &gt; 2,
                                'border-gray-300 shadow-sm': currentStep &lt; 2
                            }">

                            
                            <span class="iconify w-6 h-6 transition-colors duration-300"
                                x-bind:class="{
                                    'text-gray-400': currentStep === 2,      
                                    'text-blue-600': currentStep &gt; 2,   
                                    'text-gray-300': currentStep &lt; 2       
                                }"
                                x-bind:data-icon="'lucide:' + &quot;briefcase&quot;"
                                data-inline="false">
                            </span>
                        </div>

                        
                                                    <div class="flex-1 h-1 mx-2 rounded-full transition-all duration-300"
                                x-bind:class="currentStep &gt; 2 ? 'bg-blue-600 shadow-sm' : 'bg-gray-300'">
                            </div>
                        
                    </div>

                    
                    <span class="mt-4 text-center text-sm font-semibold transition-all duration-200 whitespace-nowrap leading-snug"
                        x-bind:class="{
                            'text-blue-600 font-bold text-sm': currentStep === 2,
                            'text-blue-600 font-semibold': currentStep &gt; 2,
                            'text-gray-500': currentStep &lt; 2
                        }">
                        Informations Professionnelles
                    </span>

                </li>
                            
                <li class="flex flex-col items-center relative flex-1">

                    
                    <div class="flex items-center w-full relative z-10">

                        
                        <div class="flex items-center justify-center w-12 h-12 rounded-full flex-shrink-0 bg-white border-2 transition-all duration-300 relative"
                            x-bind:class="{
                                'border-blue-600 shadow-lg shadow-blue-500/40': currentStep === 3,
                                'border-blue-600 shadow-md shadow-blue-500/20': currentStep &gt; 3,
                                'border-gray-300 shadow-sm': currentStep &lt; 3
                            }">

                            
                            <span class="iconify w-6 h-6 transition-colors duration-300"
                                x-bind:class="{
                                    'text-gray-400': currentStep === 3,      
                                    'text-blue-600': currentStep &gt; 3,   
                                    'text-gray-300': currentStep &lt; 3       
                                }"
                                x-bind:data-icon="'lucide:' + &quot;id-card&quot;"
                                data-inline="false">
                            </span>
                        </div>

                        
                                                    <div class="flex-1 h-1 mx-2 rounded-full transition-all duration-300"
                                x-bind:class="currentStep &gt; 3 ? 'bg-blue-600 shadow-sm' : 'bg-gray-300'">
                            </div>
                        
                    </div>

                    
                    <span class="mt-4 text-center text-sm font-semibold transition-all duration-200 whitespace-nowrap leading-snug"
                        x-bind:class="{
                            'text-blue-600 font-bold text-sm': currentStep === 3,
                            'text-blue-600 font-semibold': currentStep &gt; 3,
                            'text-gray-500': currentStep &lt; 3
                        }">
                        Permis de Conduire
                    </span>

                </li>
                            
                <li class="flex flex-col items-center relative flex-none">

                    
                    <div class="flex items-center w-full relative z-10">

                        
                        <div class="flex items-center justify-center w-12 h-12 rounded-full flex-shrink-0 bg-white border-2 transition-all duration-300 relative"
                            x-bind:class="{
                                'border-blue-600 shadow-lg shadow-blue-500/40': currentStep === 4,
                                'border-blue-600 shadow-md shadow-blue-500/20': currentStep &gt; 4,
                                'border-gray-300 shadow-sm': currentStep &lt; 4
                            }">

                            
                            <span class="iconify w-6 h-6 transition-colors duration-300"
                                x-bind:class="{
                                    'text-gray-400': currentStep === 4,      
                                    'text-blue-600': currentStep &gt; 4,   
                                    'text-gray-300': currentStep &lt; 4       
                                }"
                                x-bind:data-icon="'lucide:' + &quot;link&quot;"
                                data-inline="false">
                            </span>
                        </div>

                        
                        
                    </div>

                    
                    <span class="mt-4 text-center text-sm font-semibold transition-all duration-200 whitespace-nowrap leading-snug"
                        x-bind:class="{
                            'text-blue-600 font-bold text-sm': currentStep === 4,
                            'text-blue-600 font-semibold': currentStep &gt; 4,
                            'text-gray-500': currentStep &lt; 4
                        }">
                        Compte &amp; Urgence
                    </span>

                </li>
                    </ol>
    </div>
</div>

                
                <form method="POST" action="http://localhost/admin/drivers" enctype="multipart/form-data" @submit="onSubmit" class="p-6">
                    <input type="hidden" name="_token" value="bdDbWyzYEX2AzpLtVeiHgZZ6OO6SseR7X892Vy7G" autocomplete="off">                    <input type="hidden" name="current_step" x-model="currentStep">

                    
                    <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <span
    class="iconify block w-5 h-5 text-blue-600"
    data-icon="heroicons:user"
    data-inline="false"></span>                                    Informations Personnelles
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="" @blur="validateField('first_name', $event.target.value)">
  <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900">
 Prénom
  <span class="text-red-600">*</span>
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:user"
    data-inline="false"></span> </div>
 
 <input
 type="text"
 name="first_name"
 id="first_name"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: Ahmed"
 value=""
  required   
 x-bind:class="(fieldErrors && fieldErrors['first_name'] && touchedFields && touchedFields['first_name']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 @blur="validateField('first_name', $event.target.value)"
 />
 </div>

  <p class="mt-2 text-sm text-gray-600">
 Prénom du chauffeur
 </p>
 
 
 <p x-show="fieldErrors && fieldErrors['first_name'] && touchedFields && touchedFields['first_name']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span> <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="" @blur="validateField('last_name', $event.target.value)">
  <label for="last_name" class="block mb-2 text-sm font-medium text-gray-900">
 Nom
  <span class="text-red-600">*</span>
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:user"
    data-inline="false"></span> </div>
 
 <input
 type="text"
 name="last_name"
 id="last_name"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: Benali"
 value=""
  required   
 x-bind:class="(fieldErrors && fieldErrors['last_name'] && touchedFields && touchedFields['last_name']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 @blur="validateField('last_name', $event.target.value)"
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['last_name'] && touchedFields && touchedFields['last_name']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span> <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="w-full"
    x-data="{
        serverDate: '',
        displayValue: '',
        picker: null,

        init() {
            this.$nextTick(() => {
                const el = this.$refs.displayInput;
                const component = this;
                let isOpening = false; // Flag to prevent immediate close
                
                if (typeof window.Datepicker === 'undefined') {
                    console.error('❌ ZenFleet: Datepicker not loaded');
                    return;
                }
                
                // Initialize Flowbite Datepicker
                this.picker = new window.Datepicker(el, {
                    language: 'fr',
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    todayBtn: true,
                    todayBtnMode: 1,
                    clearBtn: true,
                    weekStart: 1,
                                                            maxDate: '12/01/2026',
                                        orientation: 'bottom left',
                });
                
                // ✅ ENTERPRISE-GRADE: Force hide function
                const forceHidePicker = () => {
                    if (!component.picker || isOpening) return;
                    
                    const pickerEl = component.picker.picker?.element;
                    if (pickerEl) {
                        pickerEl.style.display = 'none';
                        pickerEl.classList.remove('active', 'block');
                        pickerEl.classList.add('hidden');
                        if (component.picker.picker) {
                            component.picker.picker.active = false;
                        }
                    }
                };
                
                // ✅ ENTERPRISE-GRADE: Force show function (reset display)
                const ensurePickerVisible = () => {
                    const pickerEl = component.picker.picker?.element;
                    if (pickerEl) {
                        pickerEl.style.display = '';
                        pickerEl.classList.remove('hidden');
                    }
                };
                
                // Set initial date if value exists
                if (this.displayValue) {
                    this.picker.setDate(this.displayValue);
                    el.value = this.displayValue;
                }
                
                // ✅ Listen for show event to reset display and set flag
                el.addEventListener('show', () => {
                    isOpening = true;
                    ensurePickerVisible();
                    setTimeout(() => { isOpening = false; }, 100);
                });
                
                // Handle date change - force close on selection
                el.addEventListener('changeDate', (e) => {
                    if (e.detail.date) {
                        const d = e.detail.date;
                        const year = d.getFullYear();
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        component.serverDate = `${year}-${month}-${day}`;
                        component.displayValue = `${day}/${month}/${year}`;
                        component.$dispatch('input', component.serverDate);
                        
                        // Force hide after selection
                        setTimeout(forceHidePicker, 10);
                    } else {
                        component.serverDate = '';
                        component.displayValue = '';
                        component.$dispatch('input', '');
                    }
                });
                
                // ✅ ENTERPRISE-GRADE: Click outside handler
                document.addEventListener('mousedown', (e) => {
                    if (!component.picker || isOpening) return;
                    
                    const pickerEl = component.picker.picker?.element;
                    if (!pickerEl) return;
                    
                    // Only check active class for visibility (more reliable)
                    const isVisible = pickerEl.classList.contains('active');
                    if (!isVisible) return;
                    
                    // Check if click is outside both input and picker
                    if (!pickerEl.contains(e.target) && !el.contains(e.target)) {
                        forceHidePicker();
                    }
                });
                
                // Handle manual clear
                el.addEventListener('input', (e) => {
                    if (!el.value.trim()) {
                        component.serverDate = '';
                        component.displayValue = '';
                        component.$dispatch('input', '');
                    }
                });
            });
        }
    }"
    wire:ignore>

        <label for="datepicker-69650ac93e05a" class="block mb-2 text-sm font-medium text-gray-900 ">
        Date de naissance
            </label>
    
    <div class="relative">
        
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none z-10">
            <svg class="w-4 h-4 text-gray-500 "
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
            </svg>
        </div>

        
        <input
            x-ref="displayInput"
            type="text"
            id="datepicker-69650ac93e05a"
            class="block w-full !pl-10 p-2.5 bg-gray-50 border-2 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500     :ring-blue-500 :border-blue-500 transition-all duration-200 border-gray-300"
            placeholder="Choisir une date"
            x-model="displayValue"
                                    autocomplete="off"
            readonly>

        
        <input type="hidden" name="birth_date" x-model="serverDate">
    </div>

        <p class="mt-1 text-xs text-gray-500 ">Date de naissance du chauffeur</p>
    </div>
                                    <div class="">
  <label for="personal_phone" class="block mb-2 text-sm font-medium text-gray-900">
 Téléphone personnel
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:phone"
    data-inline="false"></span> </div>
 
 <input
 type="tel"
 name="personal_phone"
 id="personal_phone"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: 0555123456"
 value=""
   
 x-bind:class="(fieldErrors && fieldErrors['personal_phone'] && touchedFields && touchedFields['personal_phone']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['personal_phone'] && touchedFields && touchedFields['personal_phone']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span> <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="">
  <label for="personal_email" class="block mb-2 text-sm font-medium text-gray-900">
 Email personnel
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:envelope"
    data-inline="false"></span> </div>
 
 <input
 type="email"
 name="personal_email"
 id="personal_email"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: ahmed.benali@email.com"
 value=""
   
 x-bind:class="(fieldErrors && fieldErrors['personal_email'] && touchedFields && touchedFields['personal_email']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['personal_email'] && touchedFields && touchedFields['personal_email']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span> <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="">
  <label for="blood_type" class="block mb-2 text-sm font-medium text-gray-900 ">
 Groupe sanguin
  </label>
 
 <select
 name="blood_type"
 id="blood_type"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-500 dark:focus:border-primary-500"
   
 >
 
  
 
  <option value="" selected>
 Sélectionner
 </option>
  <option value="A+" >
 A+
 </option>
  <option value="A-" >
 A-
 </option>
  <option value="B+" >
 B+
 </option>
  <option value="B-" >
 B-
 </option>
  <option value="AB+" >
 AB+
 </option>
  <option value="AB-" >
 AB-
 </option>
  <option value="O+" >
 O+
 </option>
  <option value="O-" >
 O-
 </option>
   </select>

 </div>

                                    <div class="md:col-span-2">
                                        <div class="">
  <label for="address" class="block mb-2 text-sm font-medium text-gray-900">
 Adresse
  </label>
 
 <textarea
 name="address"
 id="address"
 rows="3"
 class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 !bg-gray-50"
 placeholder="Adresse complète du chauffeur..."
   
 ></textarea>

 </div>
                                    </div>

                                    
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Photo du chauffeur
                                        </label>
                                        <div class="flex items-center gap-6">
                                            
                                            <div class="flex-shrink-0">
                                                <div x-show="!photoPreview" class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center">
                                                    <span
    class="iconify block w-12 h-12 text-gray-400"
    data-icon="heroicons:user"
    data-inline="false"></span>                                                </div>
                                                <img x-show="photoPreview" :src="photoPreview" class="h-24 w-24 rounded-full object-cover ring-2 ring-blue-100" alt="Prévisualisation" x-cloak>
                                            </div>
                                            
                                            <div class="flex-1">
                                                <input
                                                    type="file"
                                                    name="photo"
                                                    id="photo"
                                                    accept="image/*"
                                                    @change="updatePhotoPreview($event)"
                                                    class="block w-full text-sm text-gray-500
 file:mr-4 file:py-2 file:px-4
 file:rounded-lg file:border-0
 file:text-sm file:font-medium
 file:bg-blue-50 file:text-blue-700
 hover:file:bg-blue-100
 cursor-pointer">
                                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF jusqu'à 5MB</p>
                                                                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        style="display: none;">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <span
    class="iconify block w-5 h-5 text-blue-600"
    data-icon="heroicons:briefcase"
    data-inline="false"></span>                                    Informations Professionnelles
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="">
  <label for="employee_number" class="block mb-2 text-sm font-medium text-gray-900">
 Matricule
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:identification"
    data-inline="false"></span> </div>
 
 <input
 type="text"
 name="employee_number"
 id="employee_number"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: EMP-2024-001"
 value=""
   
 x-bind:class="(fieldErrors && fieldErrors['employee_number'] && touchedFields && touchedFields['employee_number']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

  <p class="mt-2 text-sm text-gray-600">
 Numéro matricule unique
 </p>
 
 
 <p x-show="fieldErrors && fieldErrors['employee_number'] && touchedFields && touchedFields['employee_number']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span> <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="w-full"
    x-data="{
        serverDate: '',
        displayValue: '',
        picker: null,

        init() {
            this.$nextTick(() => {
                const el = this.$refs.displayInput;
                const component = this;
                let isOpening = false; // Flag to prevent immediate close
                
                if (typeof window.Datepicker === 'undefined') {
                    console.error('❌ ZenFleet: Datepicker not loaded');
                    return;
                }
                
                // Initialize Flowbite Datepicker
                this.picker = new window.Datepicker(el, {
                    language: 'fr',
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    todayBtn: true,
                    todayBtnMode: 1,
                    clearBtn: true,
                    weekStart: 1,
                                                            maxDate: '12/01/2026',
                                        orientation: 'bottom left',
                });
                
                // ✅ ENTERPRISE-GRADE: Force hide function
                const forceHidePicker = () => {
                    if (!component.picker || isOpening) return;
                    
                    const pickerEl = component.picker.picker?.element;
                    if (pickerEl) {
                        pickerEl.style.display = 'none';
                        pickerEl.classList.remove('active', 'block');
                        pickerEl.classList.add('hidden');
                        if (component.picker.picker) {
                            component.picker.picker.active = false;
                        }
                    }
                };
                
                // ✅ ENTERPRISE-GRADE: Force show function (reset display)
                const ensurePickerVisible = () => {
                    const pickerEl = component.picker.picker?.element;
                    if (pickerEl) {
                        pickerEl.style.display = '';
                        pickerEl.classList.remove('hidden');
                    }
                };
                
                // Set initial date if value exists
                if (this.displayValue) {
                    this.picker.setDate(this.displayValue);
                    el.value = this.displayValue;
                }
                
                // ✅ Listen for show event to reset display and set flag
                el.addEventListener('show', () => {
                    isOpening = true;
                    ensurePickerVisible();
                    setTimeout(() => { isOpening = false; }, 100);
                });
                
                // Handle date change - force close on selection
                el.addEventListener('changeDate', (e) => {
                    if (e.detail.date) {
                        const d = e.detail.date;
                        const year = d.getFullYear();
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        component.serverDate = `${year}-${month}-${day}`;
                        component.displayValue = `${day}/${month}/${year}`;
                        component.$dispatch('input', component.serverDate);
                        
                        // Force hide after selection
                        setTimeout(forceHidePicker, 10);
                    } else {
                        component.serverDate = '';
                        component.displayValue = '';
                        component.$dispatch('input', '');
                    }
                });
                
                // ✅ ENTERPRISE-GRADE: Click outside handler
                document.addEventListener('mousedown', (e) => {
                    if (!component.picker || isOpening) return;
                    
                    const pickerEl = component.picker.picker?.element;
                    if (!pickerEl) return;
                    
                    // Only check active class for visibility (more reliable)
                    const isVisible = pickerEl.classList.contains('active');
                    if (!isVisible) return;
                    
                    // Check if click is outside both input and picker
                    if (!pickerEl.contains(e.target) && !el.contains(e.target)) {
                        forceHidePicker();
                    }
                });
                
                // Handle manual clear
                el.addEventListener('input', (e) => {
                    if (!el.value.trim()) {
                        component.serverDate = '';
                        component.displayValue = '';
                        component.$dispatch('input', '');
                    }
                });
            });
        }
    }"
    wire:ignore>

        <label for="datepicker-69650ac93e7ef" class="block mb-2 text-sm font-medium text-gray-900 ">
        Date de recrutement
            </label>
    
    <div class="relative">
        
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none z-10">
            <svg class="w-4 h-4 text-gray-500 "
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
            </svg>
        </div>

        
        <input
            x-ref="displayInput"
            type="text"
            id="datepicker-69650ac93e7ef"
            class="block w-full !pl-10 p-2.5 bg-gray-50 border-2 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500     :ring-blue-500 :border-blue-500 transition-all duration-200 border-gray-300"
            placeholder="Choisir une date"
            x-model="displayValue"
                                    autocomplete="off"
            readonly>

        
        <input type="hidden" name="recruitment_date" x-model="serverDate">
    </div>

    </div>
                                    <div class="w-full"
    x-data="{
        serverDate: '',
        displayValue: '',
        picker: null,

        init() {
            this.$nextTick(() => {
                const el = this.$refs.displayInput;
                const component = this;
                let isOpening = false; // Flag to prevent immediate close
                
                if (typeof window.Datepicker === 'undefined') {
                    console.error('❌ ZenFleet: Datepicker not loaded');
                    return;
                }
                
                // Initialize Flowbite Datepicker
                this.picker = new window.Datepicker(el, {
                    language: 'fr',
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    todayBtn: true,
                    todayBtnMode: 1,
                    clearBtn: true,
                    weekStart: 1,
                                        minDate: '12/01/2026',
                                                            orientation: 'bottom left',
                });
                
                // ✅ ENTERPRISE-GRADE: Force hide function
                const forceHidePicker = () => {
                    if (!component.picker || isOpening) return;
                    
                    const pickerEl = component.picker.picker?.element;
                    if (pickerEl) {
                        pickerEl.style.display = 'none';
                        pickerEl.classList.remove('active', 'block');
                        pickerEl.classList.add('hidden');
                        if (component.picker.picker) {
                            component.picker.picker.active = false;
                        }
                    }
                };
                
                // ✅ ENTERPRISE-GRADE: Force show function (reset display)
                const ensurePickerVisible = () => {
                    const pickerEl = component.picker.picker?.element;
                    if (pickerEl) {
                        pickerEl.style.display = '';
                        pickerEl.classList.remove('hidden');
                    }
                };
                
                // Set initial date if value exists
                if (this.displayValue) {
                    this.picker.setDate(this.displayValue);
                    el.value = this.displayValue;
                }
                
                // ✅ Listen for show event to reset display and set flag
                el.addEventListener('show', () => {
                    isOpening = true;
                    ensurePickerVisible();
                    setTimeout(() => { isOpening = false; }, 100);
                });
                
                // Handle date change - force close on selection
                el.addEventListener('changeDate', (e) => {
                    if (e.detail.date) {
                        const d = e.detail.date;
                        const year = d.getFullYear();
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        component.serverDate = `${year}-${month}-${day}`;
                        component.displayValue = `${day}/${month}/${year}`;
                        component.$dispatch('input', component.serverDate);
                        
                        // Force hide after selection
                        setTimeout(forceHidePicker, 10);
                    } else {
                        component.serverDate = '';
                        component.displayValue = '';
                        component.$dispatch('input', '');
                    }
                });
                
                // ✅ ENTERPRISE-GRADE: Click outside handler
                document.addEventListener('mousedown', (e) => {
                    if (!component.picker || isOpening) return;
                    
                    const pickerEl = component.picker.picker?.element;
                    if (!pickerEl) return;
                    
                    // Only check active class for visibility (more reliable)
                    const isVisible = pickerEl.classList.contains('active');
                    if (!isVisible) return;
                    
                    // Check if click is outside both input and picker
                    if (!pickerEl.contains(e.target) && !el.contains(e.target)) {
                        forceHidePicker();
                    }
                });
                
                // Handle manual clear
                el.addEventListener('input', (e) => {
                    if (!el.value.trim()) {
                        component.serverDate = '';
                        component.displayValue = '';
                        component.$dispatch('input', '');
                    }
                });
            });
        }
    }"
    wire:ignore>

        <label for="datepicker-69650ac93e8f7" class="block mb-2 text-sm font-medium text-gray-900 ">
        Fin de contrat
            </label>
    
    <div class="relative">
        
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none z-10">
            <svg class="w-4 h-4 text-gray-500 "
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
            </svg>
        </div>

        
        <input
            x-ref="displayInput"
            type="text"
            id="datepicker-69650ac93e8f7"
            class="block w-full !pl-10 p-2.5 bg-gray-50 border-2 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500     :ring-blue-500 :border-blue-500 transition-all duration-200 border-gray-300"
            placeholder="Choisir une date"
            x-model="displayValue"
                                    autocomplete="off"
            readonly>

        
        <input type="hidden" name="contract_end_date" x-model="serverDate">
    </div>

        <p class="mt-1 text-xs text-gray-500 ">Date de fin du contrat (optionnel)</p>
    </div>
                                    <div wire:ignore
    x-data="{
        instance: null,
        initSelect() {
            if (this.instance) return;
            this.instance = new SlimSelect({
                select: this.$refs.select,
                settings: {
                    showSearch: true,
                    searchPlaceholder: 'Rechercher...',
                    searchText: 'Aucun résultat',
                    searchingText: 'Recherche...',
                    placeholderText: 'Sélectionnez un statut...',
                    allowDeselect: true,
                    hideSelected: false,
                },
                events: {
                    afterChange: (newVal) => {
                        // Dispatch event for Livewire/Alpine
                        this.$refs.select.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            });
        }
    }"
    x-init="initSelect()"
    class="" @change="validateField('status_id', $event.target.value)">

    <!--[if BLOCK]><![endif]-->    <label for="slimselect-status_id-69650ac93eb51" class="block mb-2 text-sm font-medium text-gray-900">
        Statut du Chauffeur
        <!--[if BLOCK]><![endif]-->        <span class="text-red-500">*</span>
        <!--[if ENDBLOCK]><![endif]-->
    </label>
    <!--[if ENDBLOCK]><![endif]-->

    <select
        x-ref="select"
        name="status_id"
        id="slimselect-status_id-69650ac93eb51"
        class="slimselect-field w-full"
                        @change="validateField('status_id', $event.target.value)">

        
        <!--[if BLOCK]><![endif]-->        <!--[if BLOCK]><![endif]-->        <option value="" data-placeholder="true">Sélectionnez un statut...</option>
        <!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]-->        <option
            value="7"
            >
            Disponible
        </option>
                <option
            value="8"
            >
            En mission
        </option>
                <option
            value="12"
            >
            En formation
        </option>
                <option
            value="9"
            >
            En congé
        </option>
                <option
            value="10"
            >
            Autre
        </option>
        <!--[if ENDBLOCK]><![endif]-->
        <!--[if ENDBLOCK]><![endif]-->
    </select>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
</div>


                                    <div class="md:col-span-2">
                                        <div class="">
  <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">
 Notes professionnelles
  </label>
 
 <textarea
 name="notes"
 id="notes"
 rows="4"
 class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 !bg-gray-50"
 placeholder="Informations complémentaires sur le chauffeur..."
   
 ></textarea>

  <p class="mt-2 text-sm text-gray-600">
 Compétences, formations, remarques, etc.
 </p>
 </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        style="display: none;">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <span
    class="iconify block w-5 h-5 text-blue-600"
    data-icon="heroicons:identification"
    data-inline="false"></span>                                    Permis de Conduire
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="">
  <label for="license_number" class="block mb-2 text-sm font-medium text-gray-900">
 Numéro de permis
  <span class="text-red-600">*</span>
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:identification"
    data-inline="false"></span> </div>
 
 <input
 type="text"
 name="license_number"
 id="license_number"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: 123456789"
 value=""
  required   
 x-bind:class="(fieldErrors && fieldErrors['license_number'] && touchedFields && touchedFields['license_number']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

  <p class="mt-2 text-sm text-gray-600">
 Numéro du permis de conduire
 </p>
 
 
 <p x-show="fieldErrors && fieldErrors['license_number'] && touchedFields && touchedFields['license_number']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span> <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    
                                    
                                    <div>
                                        <div x-data="{ 
    open: false, 
    selected: [], 
    options: JSON.parse('[\u0022A1\u0022,\u0022A\u0022,\u0022B\u0022,\u0022BE\u0022,\u0022C1\u0022,\u0022C1E\u0022,\u0022C\u0022,\u0022CE\u0022,\u0022D\u0022,\u0022DE\u0022,\u0022F\u0022]'),
    toggle(value) {
        const index = this.selected.indexOf(value);
        if (index === -1) {
            this.selected.push(value);
        } else {
            this.selected.splice(index, 1);
        }
        // Dispatch change event pour la validation Alpine.js externe
        this.$el.dispatchEvent(new CustomEvent('change', { detail: { selected: this.selected } }));
    },
    isSelected(value) {
        return this.selected.includes(value);
    },
    get selectedLabels() {
        if (this.selected.length === 0) {
            return 'Sélectionnez les catégories de permis...';
        }
        // Afficher uniquement les abréviations (valeurs) pour optimiser l'affichage
        return this.selected.join(', ');
    }
}"
    @click.outside="open = false"
    class="relative" @change="validateField('license_categories', $event.detail.selected)">

        <label for="multi-select-license_categories-69650ac93ee36" class="block mb-2 text-sm font-medium text-gray-900">
        Catégories de permis
                <span class="text-red-500">*</span>
            </label>
    
    <!-- Bouton d'affichage -->
    <button type="button"
        @click="open = !open"
        :aria-expanded="open"
        aria-haspopup="true"
        class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg shadow-sm px-4 py-2.5 text-left cursor-default focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
        :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': '' }">
        <span x-text="selectedLabels" class="block truncate" :class="{ 'text-gray-500': selected.length === 0 }"></span>
        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </span>
    </button>

    <!-- Liste des options -->
    <div x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-10 mt-1 w-full rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none max-h-60 overflow-auto"
        style="display: none;">

        <ul class="py-1 text-base ring-1 ring-gray-200 rounded-lg">
                        <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 transition duration-100 ease-in-out"
                @click.stop="toggle('A1')">

                <div class="flex items-center">
                    <!-- Case à cocher invisible pour la soumission -->
                    <input type="checkbox"
                        name="license_categories[]"
                        value="A1"
                        :checked="isSelected('A1')"
                        class="hidden"
                        :id="'multi-select-license_categories-69650ac93ee36-A1'">

                    <!-- Affichage de la case à cocher custom -->
                    <div class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center mr-3 transition duration-150 ease-in-out"
                        :class="{ 'bg-blue-600 border-blue-600': isSelected('A1'), 'bg-white': !isSelected('A1') }">
                        <svg x-show="isSelected('A1')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <span class="font-normal block truncate">
                        A1 - Motocyclettes légères
                    </span>
                </div>
            </li>
                        <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 transition duration-100 ease-in-out"
                @click.stop="toggle('A')">

                <div class="flex items-center">
                    <!-- Case à cocher invisible pour la soumission -->
                    <input type="checkbox"
                        name="license_categories[]"
                        value="A"
                        :checked="isSelected('A')"
                        class="hidden"
                        :id="'multi-select-license_categories-69650ac93ee36-A'">

                    <!-- Affichage de la case à cocher custom -->
                    <div class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center mr-3 transition duration-150 ease-in-out"
                        :class="{ 'bg-blue-600 border-blue-600': isSelected('A'), 'bg-white': !isSelected('A') }">
                        <svg x-show="isSelected('A')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <span class="font-normal block truncate">
                        A - Motocyclettes
                    </span>
                </div>
            </li>
                        <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 transition duration-100 ease-in-out"
                @click.stop="toggle('B')">

                <div class="flex items-center">
                    <!-- Case à cocher invisible pour la soumission -->
                    <input type="checkbox"
                        name="license_categories[]"
                        value="B"
                        :checked="isSelected('B')"
                        class="hidden"
                        :id="'multi-select-license_categories-69650ac93ee36-B'">

                    <!-- Affichage de la case à cocher custom -->
                    <div class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center mr-3 transition duration-150 ease-in-out"
                        :class="{ 'bg-blue-600 border-blue-600': isSelected('B'), 'bg-white': !isSelected('B') }">
                        <svg x-show="isSelected('B')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <span class="font-normal block truncate">
                        B - Véhicules légers
                    </span>
                </div>
            </li>
                        <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 transition duration-100 ease-in-out"
                @click.stop="toggle('BE')">

                <div class="flex items-center">
                    <!-- Case à cocher invisible pour la soumission -->
                    <input type="checkbox"
                        name="license_categories[]"
                        value="BE"
                        :checked="isSelected('BE')"
                        class="hidden"
                        :id="'multi-select-license_categories-69650ac93ee36-BE'">

                    <!-- Affichage de la case à cocher custom -->
                    <div class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center mr-3 transition duration-150 ease-in-out"
                        :class="{ 'bg-blue-600 border-blue-600': isSelected('BE'), 'bg-white': !isSelected('BE') }">
                        <svg x-show="isSelected('BE')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <span class="font-normal block truncate">
                        B(E) - Véhicules légers avec remorque
                    </span>
                </div>
            </li>
                        <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 transition duration-100 ease-in-out"
                @click.stop="toggle('C1')">

                <div class="flex items-center">
                    <!-- Case à cocher invisible pour la soumission -->
                    <input type="checkbox"
                        name="license_categories[]"
                        value="C1"
                        :checked="isSelected('C1')"
                        class="hidden"
                        :id="'multi-select-license_categories-69650ac93ee36-C1'">

                    <!-- Affichage de la case à cocher custom -->
                    <div class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center mr-3 transition duration-150 ease-in-out"
                        :class="{ 'bg-blue-600 border-blue-600': isSelected('C1'), 'bg-white': !isSelected('C1') }">
                        <svg x-show="isSelected('C1')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <span class="font-normal block truncate">
                        C1 - Poids lourds légers
                    </span>
                </div>
            </li>
                        <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 transition duration-100 ease-in-out"
                @click.stop="toggle('C1E')">

                <div class="flex items-center">
                    <!-- Case à cocher invisible pour la soumission -->
                    <input type="checkbox"
                        name="license_categories[]"
                        value="C1E"
                        :checked="isSelected('C1E')"
                        class="hidden"
                        :id="'multi-select-license_categories-69650ac93ee36-C1E'">

                    <!-- Affichage de la case à cocher custom -->
                    <div class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center mr-3 transition duration-150 ease-in-out"
                        :class="{ 'bg-blue-600 border-blue-600': isSelected('C1E'), 'bg-white': !isSelected('C1E') }">
                        <svg x-show="isSelected('C1E')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <span class="font-normal block truncate">
                        C1(E) - Poids lourds légers avec remorque
                    </span>
                </div>
            </li>
                        <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 transition duration-100 ease-in-out"
                @click.stop="toggle('C')">

                <div class="flex items-center">
                    <!-- Case à cocher invisible pour la soumission -->
                    <input type="checkbox"
                        name="license_categories[]"
                        value="C"
                        :checked="isSelected('C')"
                        class="hidden"
                        :id="'multi-select-license_categories-69650ac93ee36-C'">

                    <!-- Affichage de la case à cocher custom -->
                    <div class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center mr-3 transition duration-150 ease-in-out"
                        :class="{ 'bg-blue-600 border-blue-600': isSelected('C'), 'bg-white': !isSelected('C') }">
                        <svg x-show="isSelected('C')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <span class="font-normal block truncate">
                        C - Poids lourds
                    </span>
                </div>
            </li>
                        <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 transition duration-100 ease-in-out"
                @click.stop="toggle('CE')">

                <div class="flex items-center">
                    <!-- Case à cocher invisible pour la soumission -->
                    <input type="checkbox"
                        name="license_categories[]"
                        value="CE"
                        :checked="isSelected('CE')"
                        class="hidden"
                        :id="'multi-select-license_categories-69650ac93ee36-CE'">

                    <!-- Affichage de la case à cocher custom -->
                    <div class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center mr-3 transition duration-150 ease-in-out"
                        :class="{ 'bg-blue-600 border-blue-600': isSelected('CE'), 'bg-white': !isSelected('CE') }">
                        <svg x-show="isSelected('CE')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <span class="font-normal block truncate">
                        C(E) - Poids lourds avec remorque
                    </span>
                </div>
            </li>
                        <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 transition duration-100 ease-in-out"
                @click.stop="toggle('D')">

                <div class="flex items-center">
                    <!-- Case à cocher invisible pour la soumission -->
                    <input type="checkbox"
                        name="license_categories[]"
                        value="D"
                        :checked="isSelected('D')"
                        class="hidden"
                        :id="'multi-select-license_categories-69650ac93ee36-D'">

                    <!-- Affichage de la case à cocher custom -->
                    <div class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center mr-3 transition duration-150 ease-in-out"
                        :class="{ 'bg-blue-600 border-blue-600': isSelected('D'), 'bg-white': !isSelected('D') }">
                        <svg x-show="isSelected('D')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <span class="font-normal block truncate">
                        D - Transport de personnes
                    </span>
                </div>
            </li>
                        <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 transition duration-100 ease-in-out"
                @click.stop="toggle('DE')">

                <div class="flex items-center">
                    <!-- Case à cocher invisible pour la soumission -->
                    <input type="checkbox"
                        name="license_categories[]"
                        value="DE"
                        :checked="isSelected('DE')"
                        class="hidden"
                        :id="'multi-select-license_categories-69650ac93ee36-DE'">

                    <!-- Affichage de la case à cocher custom -->
                    <div class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center mr-3 transition duration-150 ease-in-out"
                        :class="{ 'bg-blue-600 border-blue-600': isSelected('DE'), 'bg-white': !isSelected('DE') }">
                        <svg x-show="isSelected('DE')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <span class="font-normal block truncate">
                        D(E) - Transport de personnes avec remorque
                    </span>
                </div>
            </li>
                        <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 transition duration-100 ease-in-out"
                @click.stop="toggle('F')">

                <div class="flex items-center">
                    <!-- Case à cocher invisible pour la soumission -->
                    <input type="checkbox"
                        name="license_categories[]"
                        value="F"
                        :checked="isSelected('F')"
                        class="hidden"
                        :id="'multi-select-license_categories-69650ac93ee36-F'">

                    <!-- Affichage de la case à cocher custom -->
                    <div class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center mr-3 transition duration-150 ease-in-out"
                        :class="{ 'bg-blue-600 border-blue-600': isSelected('F'), 'bg-white': !isSelected('F') }">
                        <svg x-show="isSelected('F')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <span class="font-normal block truncate">
                        F - Véhicules agricoles
                    </span>
                </div>
            </li>
                    </ul>
    </div>

    <!-- Le champ caché qui envoyait une chaîne de caractères a été supprimé. 
    La soumission est maintenant gérée par les checkboxes cachées avec name="license_categories[]", 
    ce qui assure que le serveur reçoit un tableau comme requis par la validation. -->

        <p class="mt-2 text-sm text-gray-500">
        Sélectionnez toutes les catégories de permis détenues par le chauffeur
    </p>
    
    
    <p x-show="fieldErrors && fieldErrors['license_categories'] && touchedFields && touchedFields['license_categories']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>        <span x-text="fieldErrors['license_categories']"></span>
    </p>
</div>                                    </div>

                                    <div class="w-full"
    x-data="{
        serverDate: '',
        displayValue: '',
        picker: null,

        init() {
            this.$nextTick(() => {
                const el = this.$refs.displayInput;
                const component = this;
                let isOpening = false; // Flag to prevent immediate close
                
                if (typeof window.Datepicker === 'undefined') {
                    console.error('❌ ZenFleet: Datepicker not loaded');
                    return;
                }
                
                // Initialize Flowbite Datepicker
                this.picker = new window.Datepicker(el, {
                    language: 'fr',
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    todayBtn: true,
                    todayBtnMode: 1,
                    clearBtn: true,
                    weekStart: 1,
                                                            maxDate: '12/01/2026',
                                        orientation: 'bottom left',
                });
                
                // ✅ ENTERPRISE-GRADE: Force hide function
                const forceHidePicker = () => {
                    if (!component.picker || isOpening) return;
                    
                    const pickerEl = component.picker.picker?.element;
                    if (pickerEl) {
                        pickerEl.style.display = 'none';
                        pickerEl.classList.remove('active', 'block');
                        pickerEl.classList.add('hidden');
                        if (component.picker.picker) {
                            component.picker.picker.active = false;
                        }
                    }
                };
                
                // ✅ ENTERPRISE-GRADE: Force show function (reset display)
                const ensurePickerVisible = () => {
                    const pickerEl = component.picker.picker?.element;
                    if (pickerEl) {
                        pickerEl.style.display = '';
                        pickerEl.classList.remove('hidden');
                    }
                };
                
                // Set initial date if value exists
                if (this.displayValue) {
                    this.picker.setDate(this.displayValue);
                    el.value = this.displayValue;
                }
                
                // ✅ Listen for show event to reset display and set flag
                el.addEventListener('show', () => {
                    isOpening = true;
                    ensurePickerVisible();
                    setTimeout(() => { isOpening = false; }, 100);
                });
                
                // Handle date change - force close on selection
                el.addEventListener('changeDate', (e) => {
                    if (e.detail.date) {
                        const d = e.detail.date;
                        const year = d.getFullYear();
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        component.serverDate = `${year}-${month}-${day}`;
                        component.displayValue = `${day}/${month}/${year}`;
                        component.$dispatch('input', component.serverDate);
                        
                        // Force hide after selection
                        setTimeout(forceHidePicker, 10);
                    } else {
                        component.serverDate = '';
                        component.displayValue = '';
                        component.$dispatch('input', '');
                    }
                });
                
                // ✅ ENTERPRISE-GRADE: Click outside handler
                document.addEventListener('mousedown', (e) => {
                    if (!component.picker || isOpening) return;
                    
                    const pickerEl = component.picker.picker?.element;
                    if (!pickerEl) return;
                    
                    // Only check active class for visibility (more reliable)
                    const isVisible = pickerEl.classList.contains('active');
                    if (!isVisible) return;
                    
                    // Check if click is outside both input and picker
                    if (!pickerEl.contains(e.target) && !el.contains(e.target)) {
                        forceHidePicker();
                    }
                });
                
                // Handle manual clear
                el.addEventListener('input', (e) => {
                    if (!el.value.trim()) {
                        component.serverDate = '';
                        component.displayValue = '';
                        component.$dispatch('input', '');
                    }
                });
            });
        }
    }"
    wire:ignore>

        <label for="datepicker-69650ac93efa5" class="block mb-2 text-sm font-medium text-gray-900 ">
        Date de délivrance
                <span class="text-red-500 ml-0.5">*</span>
            </label>
    
    <div class="relative">
        
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none z-10">
            <svg class="w-4 h-4 text-gray-500 "
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
            </svg>
        </div>

        
        <input
            x-ref="displayInput"
            type="text"
            id="datepicker-69650ac93efa5"
            class="block w-full !pl-10 p-2.5 bg-gray-50 border-2 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500     :ring-blue-500 :border-blue-500 transition-all duration-200 border-gray-300"
            placeholder="Choisir une date"
            x-model="displayValue"
                         required             autocomplete="off"
            readonly>

        
        <input type="hidden" name="license_issue_date" x-model="serverDate">
    </div>

    </div>
                                    <div class="w-full"
    x-data="{
        serverDate: '',
        displayValue: '',
        picker: null,

        init() {
            this.$nextTick(() => {
                const el = this.$refs.displayInput;
                const component = this;
                let isOpening = false; // Flag to prevent immediate close
                
                if (typeof window.Datepicker === 'undefined') {
                    console.error('❌ ZenFleet: Datepicker not loaded');
                    return;
                }
                
                // Initialize Flowbite Datepicker
                this.picker = new window.Datepicker(el, {
                    language: 'fr',
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    todayBtn: true,
                    todayBtnMode: 1,
                    clearBtn: true,
                    weekStart: 1,
                                        minDate: '12/01/2026',
                                                            orientation: 'bottom left',
                });
                
                // ✅ ENTERPRISE-GRADE: Force hide function
                const forceHidePicker = () => {
                    if (!component.picker || isOpening) return;
                    
                    const pickerEl = component.picker.picker?.element;
                    if (pickerEl) {
                        pickerEl.style.display = 'none';
                        pickerEl.classList.remove('active', 'block');
                        pickerEl.classList.add('hidden');
                        if (component.picker.picker) {
                            component.picker.picker.active = false;
                        }
                    }
                };
                
                // ✅ ENTERPRISE-GRADE: Force show function (reset display)
                const ensurePickerVisible = () => {
                    const pickerEl = component.picker.picker?.element;
                    if (pickerEl) {
                        pickerEl.style.display = '';
                        pickerEl.classList.remove('hidden');
                    }
                };
                
                // Set initial date if value exists
                if (this.displayValue) {
                    this.picker.setDate(this.displayValue);
                    el.value = this.displayValue;
                }
                
                // ✅ Listen for show event to reset display and set flag
                el.addEventListener('show', () => {
                    isOpening = true;
                    ensurePickerVisible();
                    setTimeout(() => { isOpening = false; }, 100);
                });
                
                // Handle date change - force close on selection
                el.addEventListener('changeDate', (e) => {
                    if (e.detail.date) {
                        const d = e.detail.date;
                        const year = d.getFullYear();
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        component.serverDate = `${year}-${month}-${day}`;
                        component.displayValue = `${day}/${month}/${year}`;
                        component.$dispatch('input', component.serverDate);
                        
                        // Force hide after selection
                        setTimeout(forceHidePicker, 10);
                    } else {
                        component.serverDate = '';
                        component.displayValue = '';
                        component.$dispatch('input', '');
                    }
                });
                
                // ✅ ENTERPRISE-GRADE: Click outside handler
                document.addEventListener('mousedown', (e) => {
                    if (!component.picker || isOpening) return;
                    
                    const pickerEl = component.picker.picker?.element;
                    if (!pickerEl) return;
                    
                    // Only check active class for visibility (more reliable)
                    const isVisible = pickerEl.classList.contains('active');
                    if (!isVisible) return;
                    
                    // Check if click is outside both input and picker
                    if (!pickerEl.contains(e.target) && !el.contains(e.target)) {
                        forceHidePicker();
                    }
                });
                
                // Handle manual clear
                el.addEventListener('input', (e) => {
                    if (!el.value.trim()) {
                        component.serverDate = '';
                        component.displayValue = '';
                        component.$dispatch('input', '');
                    }
                });
            });
        }
    }"
    wire:ignore>

        <label for="datepicker-69650ac93f01c" class="block mb-2 text-sm font-medium text-gray-900 ">
        Date d&#039;expiration
                <span class="text-red-500 ml-0.5">*</span>
            </label>
    
    <div class="relative">
        
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none z-10">
            <svg class="w-4 h-4 text-gray-500 "
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
            </svg>
        </div>

        
        <input
            x-ref="displayInput"
            type="text"
            id="datepicker-69650ac93f01c"
            class="block w-full !pl-10 p-2.5 bg-gray-50 border-2 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500     :ring-blue-500 :border-blue-500 transition-all duration-200 border-gray-300"
            placeholder="Choisir une date"
            x-model="displayValue"
                         required             autocomplete="off"
            readonly>

        
        <input type="hidden" name="license_expiry_date" x-model="serverDate">
    </div>

        <p class="mt-1 text-xs text-gray-500 ">Date d&#039;expiration du permis</p>
    </div>
                                    <div class="">
  <label for="license_authority" class="block mb-2 text-sm font-medium text-gray-900">
 Autorité de délivrance
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:building-office-2"
    data-inline="false"></span> </div>
 
 <input
 type="text"
 name="license_authority"
 id="license_authority"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: Wilaya d&#039;Alger"
 value=""
   
 x-bind:class="(fieldErrors && fieldErrors['license_authority'] && touchedFields && touchedFields['license_authority']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['license_authority'] && touchedFields && touchedFields['license_authority']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span> <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="flex items-center h-full pt-6">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                name="license_verified"
                                                value="1"
                                                
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700 font-medium">
                                                <span
    class="iconify block w-4 h-4 inline text-blue-600"
    data-icon="heroicons:check-badge"
    data-inline="false"></span>                                                Permis vérifié
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        style="display: none;">
                        <div class="space-y-6">
                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <span
    class="iconify block w-5 h-5 text-blue-600"
    data-icon="heroicons:user-circle"
    data-inline="false"></span>                                    Compte Utilisateur (Optionnel)
                                </h3>

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                    <div class="flex">
                                        <span
    class="iconify block w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0"
    data-icon="heroicons:information-circle"
    data-inline="false"></span>                                        <div>
                                            <p class="text-sm font-medium text-blue-900">Création de compte optionnelle</p>
                                            <p class="text-xs text-blue-700 mt-1">
                                                Si vous associez un compte utilisateur, le chauffeur pourra se connecter à l'application.
                                                Vous pouvez aussi le faire plus tard.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                                        <div wire:ignore
    x-data="{
        instance: null,
        initSelect() {
            if (this.instance) return;
            this.instance = new SlimSelect({
                select: this.$refs.select,
                settings: {
                    showSearch: true,
                    searchPlaceholder: 'Rechercher...',
                    searchText: 'Aucun résultat',
                    searchingText: 'Recherche...',
                    placeholderText: 'Rechercher un utilisateur...',
                    allowDeselect: true,
                    hideSelected: false,
                },
                events: {
                    afterChange: (newVal) => {
                        // Dispatch event for Livewire/Alpine
                        this.$refs.select.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            });
        }
    }"
    x-init="initSelect()"
    class="">

    <!--[if BLOCK]><![endif]-->    <label for="slimselect-user_id-69650ac93f227" class="block mb-2 text-sm font-medium text-gray-900">
        Compte utilisateur
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    </label>
    <!--[if ENDBLOCK]><![endif]-->

    <select
        x-ref="select"
        name="user_id"
        id="slimselect-user_id-69650ac93f227"
        class="slimselect-field w-full"
                        >

        
        <!--[if BLOCK]><![endif]-->        <!--[if BLOCK]><![endif]-->        <option value="" data-placeholder="true">Rechercher un utilisateur...</option>
        <!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]-->        <option
            value="2"
            >
             (amine.belabes@trans-algerlogistics.local)
        </option>
                <option
            value="1"
            >
             (mohamed.meziani@trans-algerlogistics.local)
        </option>
                <option
            value="23"
            >
            Ali Boumalou (ali@zenfleet.dz)
        </option>
                <option
            value="5"
            >
            Gestionnaire Flotte (gestionnaire@zenfleet.dz)
        </option>
                <option
            value="6"
            >
            SUPER VISEUR (superviseur@zenfleet.dz)
        </option>
                <option
            value="3"
            >
            Super Administrateur (superadmin@zenfleet.dz)
        </option>
                <option
            value="4"
            >
            admin zenfleet (admin@zenfleet.dz)
        </option>
                <option
            value="7"
            >
            hamid Baroudi (comptable@zenfleet.dz)
        </option>
        <!--[if ENDBLOCK]><![endif]-->
        <!--[if ENDBLOCK]><![endif]-->
    </select>

    <!--[if BLOCK]><![endif]-->    <p class="mt-2 text-sm text-gray-500">
        Sélectionnez un compte existant ou laissez vide (optionnel)
    </p>
    <!--[if ENDBLOCK]><![endif]-->
</div>

                                </div>
                            </div>

                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <span
    class="iconify block w-5 h-5 text-red-600"
    data-icon="heroicons:phone"
    data-inline="false"></span>                                    Contact d'Urgence
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="">
  <label for="emergency_contact_name" class="block mb-2 text-sm font-medium text-gray-900">
 Nom du contact
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:user"
    data-inline="false"></span> </div>
 
 <input
 type="text"
 name="emergency_contact_name"
 id="emergency_contact_name"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: Fatima Benali"
 value=""
   
 x-bind:class="(fieldErrors && fieldErrors['emergency_contact_name'] && touchedFields && touchedFields['emergency_contact_name']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['emergency_contact_name'] && touchedFields && touchedFields['emergency_contact_name']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span> <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="">
  <label for="emergency_contact_phone" class="block mb-2 text-sm font-medium text-gray-900">
 Téléphone du contact
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:phone"
    data-inline="false"></span> </div>
 
 <input
 type="tel"
 name="emergency_contact_phone"
 id="emergency_contact_phone"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: 0555987654"
 value=""
   
 x-bind:class="(fieldErrors && fieldErrors['emergency_contact_phone'] && touchedFields && touchedFields['emergency_contact_phone']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['emergency_contact_phone'] && touchedFields && touchedFields['emergency_contact_phone']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span> <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="">
  <label for="emergency_contact_relationship" class="block mb-2 text-sm font-medium text-gray-900">
 Lien de parenté
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:users"
    data-inline="false"></span> </div>
 
 <input
 type="text"
 name="emergency_contact_relationship"
 id="emergency_contact_relationship"
 class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
 placeholder="Ex: Épouse, Frère, Mère"
 value=""
   
 x-bind:class="(fieldErrors && fieldErrors['emergency_contact_relationship'] && touchedFields && touchedFields['emergency_contact_relationship']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
 
 />
 </div>

 
 
 <p x-show="fieldErrors && fieldErrors['emergency_contact_relationship'] && touchedFields && touchedFields['emergency_contact_relationship']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start font-medium"
 style="display: none;">
 <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span> <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-between">
                        <div>
                            <button
                                type="button"
                                @click="prevStep()"
                                x-show="currentStep > 1"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700">
                                <span
    class="iconify block w-4 h-4"
    data-icon="heroicons:arrow-left"
    data-inline="false"></span>                                Précédent
                            </button>
                        </div>

                        <div class="flex items-center gap-4">
                            <a href="http://localhost/admin/drivers"
                                class="text-gray-600 hover:text-gray-900 font-medium text-sm transition-colors">
                                Annuler
                            </a>

                            <button
                                type="button"
                                @click="nextStep()"
                                x-show="currentStep < 4"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md text-sm">
                                Suivant
                                <span
    class="iconify block w-4 h-4"
    data-icon="heroicons:arrow-right"
    data-inline="false"></span>                            </button>

                            <button
                                type="submit"
                                x-show="currentStep === 4"
                                x-cloak
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md text-sm">
                                <span
    class="iconify block w-5 h-5"
    data-icon="heroicons:check"
    data-inline="false"></span>                                Créer le Chauffeur
                            </button>
                        </div>
                    </div>
                </form>
</div>

        </div>
    </div>
</section>

                </div>
            </main>
        </div>
    </div>

    

    
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    

    
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ====================================================================
            // TOM SELECT - Initialisation Globale
            // ====================================================================
            document.querySelectorAll('.tomselect').forEach(function(el) {
                if (el.tomselect) return; // Déjà initialisé

                new TomSelect(el, {
                    plugins: ['clear_button', 'remove_button'],
                    maxOptions: 100,
                    placeholder: el.getAttribute('data-placeholder') || 'Rechercher...',
                    allowEmptyOption: true,
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    render: {
                        no_results: function(data, escape) {
                            return '<div class="no-results p-2 text-sm text-gray-500">Aucun résultat trouvé</div>';
                        }
                    }
                });
            });

            // ====================================================================
            // FLATPICKR DATEPICKER - Initialisation Globale
            // ====================================================================
            document.querySelectorAll('.datepicker').forEach(function(el) {
                if (el._flatpickr) return; // Déjà initialisé

                const minDate = el.getAttribute('data-min-date');
                const maxDate = el.getAttribute('data-max-date');
                const dateFormat = el.getAttribute('data-date-format') || 'd/m/Y';

                flatpickr(el, {
                    locale: 'fr',
                    dateFormat: dateFormat,
                    minDate: minDate,
                    maxDate: maxDate,
                    allowInput: true,
                    disableMobile: true,
                    nextArrow: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>',
                    prevArrow: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>',
                });
            });

            // ====================================================================
            // FLATPICKR TIMEPICKER - Initialisation Globale avec Masque
            // ====================================================================

            // Fonction de masque de saisie pour le format HH:MM
            function applyTimeMask(input) {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, ''); // Garder seulement les chiffres

                    if (value.length >= 2) {
                        // Limiter les heures à 23
                        let hours = parseInt(value.substring(0, 2));
                        if (hours > 23) hours = 23;

                        let formattedValue = String(hours).padStart(2, '0');

                        if (value.length >= 3) {
                            // Limiter les minutes à 59
                            let minutes = parseInt(value.substring(2, 4));
                            if (minutes > 59) minutes = 59;
                            formattedValue += ':' + String(minutes).padStart(2, '0');
                        } else if (value.length === 2) {
                            formattedValue += ':';
                        }

                        e.target.value = formattedValue;
                    }
                });

                // Empêcher la suppression du ':'
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace') {
                        const cursorPos = e.target.selectionStart;
                        if (cursorPos === 3 && e.target.value.charAt(2) === ':') {
                            e.preventDefault();
                            e.target.value = e.target.value.substring(0, 2);
                        }
                    }
                });
            }

            document.querySelectorAll('.timepicker').forEach(function(el) {
                if (el._flatpickr) return; // Déjà initialisé

                const enableSeconds = el.getAttribute('data-enable-seconds') === 'true';

                // Appliquer le masque de saisie
                applyTimeMask(el);

                flatpickr(el, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: enableSeconds ? "H:i:S" : "H:i",
                    time_24hr: true,
                    allowInput: true,
                    disableMobile: true,
                    defaultHour: 0,
                    defaultMinute: 0,
                });
            });
        });

        // ====================================================================
        // LIVEWIRE - Réinitialisation après mises à jour
        // ====================================================================
        document.addEventListener('livewire:navigated', function() {
            // Réinitialiser Tom Select
            document.querySelectorAll('.tomselect').forEach(function(el) {
                if (!el.tomselect) {
                    new TomSelect(el, {
                        plugins: ['clear_button', 'remove_button'],
                        maxOptions: 100,
                        placeholder: el.getAttribute('data-placeholder') || 'Rechercher...',
                        allowEmptyOption: true,
                        create: false,
                    });
                }
            });

            // Réinitialiser Flatpickr
            document.querySelectorAll('.datepicker, .timepicker').forEach(function(el) {
                if (!el._flatpickr) {
                    flatpickr(el, {
                        locale: 'fr',
                        allowInput: true,
                        disableMobile: true,
                    });
                }
            });
        });
    </script>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('driverFormValidation', () => ({
                        currentStep: 1,
            photoPreview: null,
            fieldErrors: {
                // Phase 1: Informations Personnelles
                first_name: '',
                last_name: '',
                birth_date: '',
                personal_phone: '',
                personal_email: '',
                blood_type: '',
                address: '',
                // Phase 2: Informations Professionnelles
                employee_number: '',
                recruitment_date: '',
                status_id: '',
                // Phase 3: Permis de Conduire
                license_number: '',
                license_categories: '',
                license_issue_date: '',
                license_expiry_date: ''
            },
            touchedFields: {
                first_name: false,
                last_name: false,
                birth_date: false,
                personal_phone: false,
                personal_email: false,
                status_id: false,
                employee_number: false,
                license_number: false,
                license_categories: false
            },
            formValid: false,

            init() {
                                            },

            updatePhotoPreview(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.photoPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            validateField(fieldName, value) {
                this.touchedFields[fieldName] = true;

                switch (fieldName) {
                    case 'first_name':
                        if (!value || value.trim() === '') {
                            this.fieldErrors.first_name = 'Le prénom est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.first_name);
                        } else if (value.trim().length < 2) {
                            this.fieldErrors.first_name = 'Le prénom doit contenir au moins 2 caractères';
                            this.showFieldError(fieldName, this.fieldErrors.first_name);
                        } else if (!/^[a-zA-ZÀ-ÿ\s'-]+$/.test(value.trim())) {
                            this.fieldErrors.first_name = 'Le prénom ne doit contenir que des lettres';
                            this.showFieldError(fieldName, this.fieldErrors.first_name);
                        } else {
                            this.fieldErrors.first_name = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'last_name':
                        if (!value || value.trim() === '') {
                            this.fieldErrors.last_name = 'Le nom est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.last_name);
                        } else if (value.trim().length < 2) {
                            this.fieldErrors.last_name = 'Le nom doit contenir au moins 2 caractères';
                            this.showFieldError(fieldName, this.fieldErrors.last_name);
                        } else if (!/^[a-zA-ZÀ-ÿ\s'-]+$/.test(value.trim())) {
                            this.fieldErrors.last_name = 'Le nom ne doit contenir que des lettres';
                            this.showFieldError(fieldName, this.fieldErrors.last_name);
                        } else {
                            this.fieldErrors.last_name = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'birth_date':
                        if (!value) {
                            this.fieldErrors.birth_date = 'La date de naissance est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.birth_date);
                        } else {
                            const birthDate = new Date(value);
                            const today = new Date();
                            const age = today.getFullYear() - birthDate.getFullYear();
                            if (age < 18) {
                                this.fieldErrors.birth_date = 'Le chauffeur doit être majeur (18 ans minimum)';
                                this.showFieldError(fieldName, this.fieldErrors.birth_date);
                            } else if (age > 70) {
                                this.fieldErrors.birth_date = 'L\'âge maximum est de 70 ans';
                                this.showFieldError(fieldName, this.fieldErrors.birth_date);
                            } else {
                                this.fieldErrors.birth_date = '';
                                this.removeFieldError(fieldName);
                            }
                        }
                        break;

                    case 'personal_phone':
                        if (!value || value.trim() === '') {
                            this.fieldErrors.personal_phone = 'Le téléphone est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.personal_phone);
                        } else if (!/^(0[567])[0-9]{8}$/.test(value.replace(/\s/g, ''))) {
                            this.fieldErrors.personal_phone = 'Format invalide (ex: 0555123456)';
                            this.showFieldError(fieldName, this.fieldErrors.personal_phone);
                        } else {
                            this.fieldErrors.personal_phone = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'personal_email':
                        if (value && value.trim() !== '') {
                            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            if (!emailRegex.test(value)) {
                                this.fieldErrors.personal_email = 'Format email invalide';
                                this.showFieldError(fieldName, this.fieldErrors.personal_email);
                            } else {
                                this.fieldErrors.personal_email = '';
                                this.removeFieldError(fieldName);
                            }
                        } else {
                            this.fieldErrors.personal_email = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'employee_number':
                        if (!value || value.trim() === '') {
                            this.fieldErrors.employee_number = 'Le matricule est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.employee_number);
                        } else if (value.trim().length < 3) {
                            this.fieldErrors.employee_number = 'Le matricule doit contenir au moins 3 caractères';
                            this.showFieldError(fieldName, this.fieldErrors.employee_number);
                        } else {
                            this.fieldErrors.employee_number = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'recruitment_date':
                        if (!value) {
                            this.fieldErrors.recruitment_date = 'La date de recrutement est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.recruitment_date);
                        } else {
                            const recruitDate = new Date(value);
                            const today = new Date();
                            if (recruitDate > today) {
                                this.fieldErrors.recruitment_date = 'La date ne peut pas être dans le futur';
                                this.showFieldError(fieldName, this.fieldErrors.recruitment_date);
                            } else {
                                this.fieldErrors.recruitment_date = '';
                                this.removeFieldError(fieldName);
                            }
                        }
                        break;

                    case 'status_id':
                        if (!value || value === '' || value === '0') {
                            this.fieldErrors.status_id = 'Le statut du chauffeur est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.status_id);
                        } else {
                            this.fieldErrors.status_id = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'license_number':
                        if (!value || value.trim() === '') {
                            this.fieldErrors.license_number = 'Le numéro de permis est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.license_number);
                        } else if (value.trim().length < 5) {
                            this.fieldErrors.license_number = 'Le numéro de permis doit contenir au moins 5 caractères';
                            this.showFieldError(fieldName, this.fieldErrors.license_number);
                        } else {
                            this.fieldErrors.license_number = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'license_categories':
                        // La valeur passée est le tableau 'selected' de l'événement custom
                        if (!value || value.length === 0) {
                            this.fieldErrors.license_categories = 'Au moins une catégorie de permis est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.license_categories);
                        } else {
                            this.fieldErrors.license_categories = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'license_issue_date':
                        if (!value) {
                            this.fieldErrors.license_issue_date = 'La date de délivrance est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.license_issue_date);
                        } else {
                            this.fieldErrors.license_issue_date = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'license_expiry_date':
                        if (!value) {
                            this.fieldErrors.license_expiry_date = 'La date d\'expiration est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.license_expiry_date);
                        } else {
                            const expiryDate = new Date(value);
                            const today = new Date();
                            if (expiryDate < today) {
                                this.fieldErrors.license_expiry_date = 'Le permis est expiré';
                                this.showFieldError(fieldName, this.fieldErrors.license_expiry_date);
                            } else {
                                this.fieldErrors.license_expiry_date = '';
                                this.removeFieldError(fieldName);
                            }
                        }
                        break;
                }

                this.updateFormValidity();
            },

            showFieldError(fieldName, message) {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                    field.classList.remove('border-gray-300', 'focus:border-blue-500', 'focus:ring-blue-500');

                    // Ajouter ou mettre à jour le message d'erreur
                    let errorDiv = field.parentElement.querySelector('.field-error');
                    if (!errorDiv) {
                        errorDiv = document.createElement('p');
                        errorDiv.className = 'field-error mt-1.5 text-sm text-red-600 flex items-center gap-1';
                        errorDiv.innerHTML = `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><span>${message}</span>`;
                        field.parentElement.appendChild(errorDiv);
                    } else {
                        errorDiv.querySelector('span').textContent = message;
                    }
                }
            },

            removeFieldError(fieldName) {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                    field.classList.add('border-gray-300', 'focus:border-blue-500', 'focus:ring-blue-500');

                    const errorDiv = field.parentElement.querySelector('.field-error');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                }
            },

            updateFormValidity() {
                let hasErrors = false;
                for (let key in this.fieldErrors) {
                    if (this.fieldErrors[key] !== '') {
                        hasErrors = true;
                        break;
                    }
                }
                this.formValid = !hasErrors;
            },

            hasError(fieldName) {
                return this.touchedFields[fieldName] && this.fieldErrors[fieldName] !== '';
            },

            validateStep(step) {
                let isValid = true;
                let fieldsToValidate = [];

                switch (step) {
                    case 1: // Informations Personnelles
                        fieldsToValidate = [
                            'first_name',
                            'last_name',
                            'birth_date',
                            'personal_phone'
                        ];
                        // Email est optionnel
                        const emailField = document.querySelector('[name="personal_email"]');
                        if (emailField && emailField.value) {
                            fieldsToValidate.push('personal_email');
                        }
                        break;

                    case 2: // Informations Professionnelles
                        fieldsToValidate = [
                            'employee_number',
                            'recruitment_date',
                            'status_id'
                        ];
                        break;

                    case 3: // Permis de Conduire
                        fieldsToValidate = [
                            'license_number',
                            'license_categories',
                            'license_issue_date',
                            'license_expiry_date'
                        ];
                        break;

                    case 4: // Compte & Urgence
                        // Tous les champs sont optionnels dans cette étape
                        break;
                }

                // Valider chaque champ de l'étape
                fieldsToValidate.forEach(fieldName => {
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (field) {
                        this.validateField(fieldName, field.value);
                        if (this.fieldErrors[fieldName]) {
                            isValid = false;
                        }
                    }
                });

                // Si des erreurs existent, afficher une alerte
                if (!isValid) {
                    this.showStepErrors(step, fieldsToValidate);
                }

                return isValid;
            },

            showStepErrors(step, fields) {
                let errorMessages = [];
                fields.forEach(fieldName => {
                    if (this.fieldErrors[fieldName]) {
                        const label = document.querySelector(`[name="${fieldName}"]`)?.parentElement?.querySelector('label')?.textContent || fieldName;
                        errorMessages.push(`• ${label}: ${this.fieldErrors[fieldName]}`);
                    }
                });

                if (errorMessages.length > 0) {
                    // Créer ou mettre à jour le message d'erreur global
                    let alertDiv = document.querySelector('.step-validation-alert');
                    if (!alertDiv) {
                        alertDiv = document.createElement('div');
                        alertDiv.className = 'step-validation-alert fixed top-4 right-4 z-50 max-w-md bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg';
                        alertDiv.innerHTML = `
 <div class="flex items-start gap-3">
 <svg class="w-5 h-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
 </svg>
 <div class="flex-1">
 <h3 class="text-sm font-semibold text-red-800">Veuillez corriger les erreurs suivantes :</h3>
 <div class="mt-2 text-sm text-red-700 error-list"></div>
 </div>
 <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
 <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
 </svg>
 </button>
 </div>
 `;
                        document.body.appendChild(alertDiv);
                    }
                    alertDiv.querySelector('.error-list').innerHTML = errorMessages.join('<br>');

                    // Auto-fermer après 5 secondes
                    setTimeout(() => {
                        if (alertDiv) {
                            alertDiv.remove();
                        }
                    }, 5000);
                }
            },

            nextStep() {
                if (this.validateStep(this.currentStep)) {
                    if (this.currentStep < 4) {
                        this.currentStep++;
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            },

            onSubmit(event) {
                if (!this.validateStep(4)) {
                    event.preventDefault();
                    return false;
                }

                // Conversion automatique des dates avant soumission
                this.convertDatesBeforeSubmit(event);
            },

            /**
             * 🔄 Conversion Enterprise-Grade des dates avant soumission
             * Convertit automatiquement tous les champs de date du format d/m/Y vers Y-m-d
             */
            convertDatesBeforeSubmit(event) {
                const form = event.target;

                // Liste des champs de date à convertir
                const dateFields = [
                    'birth_date',
                    'recruitment_date',
                    'contract_end_date',
                    'license_issue_date',
                    'license_expiry_date'
                ];

                dateFields.forEach(fieldName => {
                    const input = form.querySelector(`[name="${fieldName}"]`);
                    if (input && input.value) {
                        const convertedDate = this.convertDateFormat(input.value);
                        if (convertedDate) {
                            input.value = convertedDate;
                        }
                    }
                });
            },

            /**
             * 📅 Convertit une date du format dd/mm/yyyy vers yyyy-mm-dd
             * Gère plusieurs formats d'entrée de manière robuste
             */
            convertDateFormat(dateString) {
                if (!dateString) return null;

                // Si déjà au format yyyy-mm-dd, retourner tel quel
                if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
                    return dateString;
                }

                // Conversion depuis dd/mm/yyyy ou d/m/yyyy
                const match = dateString.match(/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/);
                if (match) {
                    const day = match[1].padStart(2, '0');
                    const month = match[2].padStart(2, '0');
                    const year = match[3];

                    // Validation basique de la date
                    const date = new Date(`${year}-${month}-${day}`);
                    if (date && !isNaN(date.getTime())) {
                        return `${year}-${month}-${day}`;
                    }
                }

                // Si format non reconnu, retourner null et logger une erreur
                console.error('Format de date non reconnu:', dateString);
                return null;
            },

            handleValidationErrors(errors) {
                console.log('Server Errors:', errors);
                // Map server errors to fieldErrors
                Object.keys(errors).forEach(field => {
                    this.fieldErrors[field] = errors[field][0];
                    this.touchedFields[field] = true;
                });

                const fieldToStepMap = {
                    'first_name': 1,
                    'last_name': 1,
                    'birth_date': 1,
                    'personal_phone': 1,
                    'address': 1,
                    'blood_type': 1,
                    'personal_email': 1,
                    'photo': 1,
                    'employee_number': 2,
                    'recruitment_date': 2,
                    'contract_end_date': 2,
                    'status_id': 2,
                    'notes': 2,
                    'license_number': 3,
                    'license_categories': 3,
                    'license_issue_date': 3,
                    'license_expiry_date': 3,
                    'license_authority': 3,
                    'license_verified': 3,
                    'user_id': 4,
                    'emergency_contact_name': 4,
                    'emergency_contact_phone': 4,
                    'emergency_contact_relationship': 4
                };

                // Determine the first step with an error
                const errorFields = Object.keys(errors);
                let firstErrorStep = null;

                for (const field of errorFields) {
                    if (fieldToStepMap[field]) {
                        if (firstErrorStep === null || fieldToStepMap[field] < firstErrorStep) {
                            firstErrorStep = fieldToStepMap[field];
                        }
                    }
                }

                if (firstErrorStep) {
                    this.currentStep = firstErrorStep;
                }
            }
        }));
    });
</script>
    

    
    <div x-data="toastManager()"
        @toast.window="showToast($event.detail)"
        class="fixed top-4 right-4 z-50 space-y-2"
        style="pointer-events: none;">
        <template x-for="(toast, index) in toasts" :key="toast.id">
            <div x-show="toast.show"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-x-full"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-full"
                class="max-w-md w-full shadow-lg rounded-lg pointer-events-auto overflow-hidden"
                :class="{
                  'bg-green-50 border border-green-200': toast.type === 'success',
                  'bg-red-50 border border-red-200': toast.type === 'error',
                  'bg-blue-50 border border-blue-200': toast.type === 'info',
                  'bg-yellow-50 border border-yellow-200': toast.type === 'warning'
              }">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <template x-if="toast.type === 'success'">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                            <template x-if="toast.type === 'error'">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                            <template x-if="toast.type === 'info'">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                            <template x-if="toast.type === 'warning'">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </template>
                        </div>
                        <div class="ml-3 flex-1">
                            <template x-if="toast.title">
                                <p class="text-sm font-semibold mb-1"
                                    :class="{
                                    'text-green-900': toast.type === 'success',
                                    'text-red-900': toast.type === 'error',
                                    'text-blue-900': toast.type === 'info',
                                    'text-yellow-900': toast.type === 'warning'
                                }"
                                    x-text="toast.title"></p>
                            </template>
                            <p class="text-sm"
                                :class="{
                                'text-green-800': toast.type === 'success',
                                'text-red-800': toast.type === 'error',
                                'text-blue-800': toast.type === 'info',
                                'text-yellow-800': toast.type === 'warning'
                            }"
                                x-text="toast.message || 'Notification'"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="removeToast(toast.id)"
                                class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
                                :class="{
                                     'text-green-500 hover:text-green-600 focus:ring-green-500': toast.type === 'success',
                                     'text-red-500 hover:text-red-600 focus:ring-red-500': toast.type === 'error',
                                     'text-blue-500 hover:text-blue-600 focus:ring-blue-500': toast.type === 'info',
                                     'text-yellow-500 hover:text-yellow-600 focus:ring-yellow-500': toast.type === 'warning'
                                 }">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </template>
    </div>

    <script>
        function toastManager() {
            return {
                toasts: [],
                counter: 0,

                showToast(detail) {
                    const id = ++this.counter;
                    const toast = {
                        id: id,
                        type: detail.type || 'info',
                        title: detail.title || '',
                        message: detail.message || 'Notification',
                        show: true
                    };

                    this.toasts.push(toast);

                    // Auto-remove after 5 seconds
                    setTimeout(() => {
                        this.removeToast(id);
                    }, 5000);
                },

                removeToast(id) {
                    const index = this.toasts.findIndex(t => t.id === id);
                    if (index !== -1) {
                        this.toasts[index].show = false;
                        setTimeout(() => {
                            this.toasts.splice(index, 1);
                        }, 300);
                    }
                }
            }
        }
    </script>
    <script data-navigate-once="true">window.livewireScriptConfig = {"csrf":"bdDbWyzYEX2AzpLtVeiHgZZ6OO6SseR7X892Vy7G","uri":"\/livewire\/update","progressBar":"","nonce":""};</script>
</body>

</html>