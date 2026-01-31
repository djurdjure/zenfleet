
<!DOCTYPE html>
<html lang="fr" class="h-full bg-zinc-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="bEwJ66fcBeySZV1izFVsCmjw8GzH806w9hgV23ZN">
        <meta name="user-data" content="{&quot;id&quot;:2,&quot;name&quot;:&quot;el hadi chemli&quot;,&quot;role&quot;:&quot;Admin&quot;}">
    
    <title>Ajouter un Nouveau Chauffeur - ZenFleet</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    

    
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    


    <!-- 🚀 Performance: Load CSS in Parallel (No JS blocking) -->
    <script data-navigate-once="true">window.livewireScriptConfig = {"csrf":"bEwJ66fcBeySZV1izFVsCmjw8GzH806w9hgV23ZN","uri":"\/livewire\/update","progressBar":"","nonce":""};</script>
    <script type="module" src="http://localhost:5173/@vite/client" data-navigate-track="reload"></script><link rel="stylesheet" href="http://localhost:5173/resources/css/app.css" data-navigate-track="reload" /><link rel="stylesheet" href="http://localhost:5173/resources/css/admin/app.css" data-navigate-track="reload" /><script type="module" src="http://localhost:5173/resources/js/admin/app.js" data-navigate-track="reload"></script>        <!-- Livewire Styles --><style >[wire\:loading][wire\:loading], [wire\:loading\.delay][wire\:loading\.delay], [wire\:loading\.inline-block][wire\:loading\.inline-block], [wire\:loading\.inline][wire\:loading\.inline], [wire\:loading\.block][wire\:loading\.block], [wire\:loading\.flex][wire\:loading\.flex], [wire\:loading\.table][wire\:loading\.table], [wire\:loading\.grid][wire\:loading\.grid], [wire\:loading\.inline-flex][wire\:loading\.inline-flex] {display: none;}[wire\:loading\.delay\.none][wire\:loading\.delay\.none], [wire\:loading\.delay\.shortest][wire\:loading\.delay\.shortest], [wire\:loading\.delay\.shorter][wire\:loading\.delay\.shorter], [wire\:loading\.delay\.short][wire\:loading\.delay\.short], [wire\:loading\.delay\.default][wire\:loading\.delay\.default], [wire\:loading\.delay\.long][wire\:loading\.delay\.long], [wire\:loading\.delay\.longer][wire\:loading\.delay\.longer], [wire\:loading\.delay\.longest][wire\:loading\.delay\.longest] {display: none;}[wire\:offline][wire\:offline] {display: none;}[wire\:dirty]:not(textarea):not(input):not(select) {display: none;}:root {--livewire-progress-bar-color: #2299dd;}[x-cloak] {display: none !important;}[wire\:cloak] {display: none !important;}</style>
</head>

<body class="h-full">
    <div class="min-h-full">
        
        <div class="max-lg:hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
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

                        
                                                <li class="flex">
                            <a href="http://localhost/admin/organizations"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
                                <span
    class="iconify block w-5 h-5 mr-3 text-gray-600"
    data-icon="mdi:office-building"
    data-inline="false"></span>                                <span class="flex-1">Organisations</span>
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
                <div class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm" @click="open = false"></div>

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
                                                    class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                                                    <span
    class="iconify block h-5 w-5 shrink-0"
    data-icon="heroicons:home"
    data-inline="false"></span>                                                    Dashboard
                                                </a>
                                            </li>

                                            
                                            
                                            
                                                                                        <li x-data="{ open: false }">
                                                <button @click="open = !open"
                                                    class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-gray-900 hover:bg-gray-100">
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
                                                                class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50">
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

                        
                        <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600">
                            <span class="sr-only">Basculer le mode sombre</span>
                            <span
    class="iconify block h-6 w-6"
    data-icon="heroicons:moon"
    data-inline="false"></span>                        </button>

                        
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
                                        <div class="text-sm font-semibold leading-5 text-zinc-900">el hadi chemli</div>
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
                                            <div class="text-sm font-medium text-zinc-900">el hadi chemli</div>
                                            <div class="text-xs text-zinc-500">echemli@difex.dz</div>
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
                                        <input type="hidden" name="_token" value="bEwJ66fcBeySZV1izFVsCmjw8GzH806w9hgV23ZN" autocomplete="off">                                        <button type="submit"
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
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12 lg:mx-0">

        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                <span
    class="iconify block w-6 h-6 text-blue-600"
    data-icon="heroicons:user-plus"
    data-inline="false"></span>                Ajouter un Nouveau Chauffeur
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Complétez les sections ci-dessous pour enregistrer un chauffeur
            </p>
        </div>

        
        
        
        <div x-data="driverFormValidationCreate()" x-init="init()">

            
            <form method="POST" action="http://localhost/admin/drivers" enctype="multipart/form-data" @submit="onSubmit" class="space-y-8">
                <input type="hidden" name="_token" value="bEwJ66fcBeySZV1izFVsCmjw8GzH806w9hgV23ZN" autocomplete="off">
                
                <div class="relative">
            <div class="absolute left-5 top-6 bottom-6 w-px bg-slate-200/80"></div>
    
    <div class="relative pl-12">
                    <div class="absolute left-1.5 top-6">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white text-blue-600 shadow-sm ring-2 ring-blue-100">
                    <span
    class="iconify block w-5 h-5"
    data-icon="heroicons:user"
    data-inline="false"></span>                </span>
            </div>
        
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-4 px-6 py-4 bg-slate-50/70 border-b border-slate-200">
                <div>
                                        <h3 class="text-sm font-semibold text-slate-900">Informations Personnelles</h3>
                                            <p class="text-xs text-slate-500 mt-0.5">Identité, coordonnées et informations de base du chauffeur</p>
                                    </div>
                
            </div>

            <div class="p-6 ">
                <div class="col-span-full w-full min-w-0 grid grid-cols-1 md:grid-cols-2 gap-6"
        style="position: relative;"
    >
        
    <div class="hidden md:block absolute left-1/2 top-4 bottom-4 w-px bg-gradient-to-b from-transparent via-slate-200 to-transparent" aria-hidden="true"></div>
    
    <div class="" @blur="validateField('first_name', $event.target.value)">
        <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900">
        Prénom
                <span class="text-red-600">*</span>
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:user"
    data-inline="false"></span>        </div>
        
        <input
            type="text"
            name="first_name"
            id="first_name"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: Ahmed"
            value=""
             required                         
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['first_name'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['first_name']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            @blur="validateField('first_name', $event.target.value)" />
    </div>

        <p class="mt-2 text-sm text-gray-600">
        Prénom du chauffeur
    </p>
    
    
    <p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['first_name'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['first_name']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>        <span>Ce champ est obligatoire et doit être correctement rempli</span>
    </p>
</div>
                        <div class="" @blur="validateField('last_name', $event.target.value)">
        <label for="last_name" class="block mb-2 text-sm font-medium text-gray-900">
        Nom
                <span class="text-red-600">*</span>
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:user"
    data-inline="false"></span>        </div>
        
        <input
            type="text"
            name="last_name"
            id="last_name"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: Benali"
            value=""
             required                         
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['last_name'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['last_name']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            @blur="validateField('last_name', $event.target.value)" />
    </div>

    
    
    <p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['last_name'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['last_name']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>        <span>Ce champ est obligatoire et doit être correctement rempli</span>
    </p>
</div>
</div>

                    <div class="col-span-full w-full min-w-0 grid grid-cols-1 md:grid-cols-2 gap-6 mt-6"
        style="position: relative;"
    >
        
    <div class="hidden md:block absolute left-1/2 top-4 bottom-4 w-px bg-gradient-to-b from-transparent via-slate-200 to-transparent" aria-hidden="true"></div>
    
    <div
    class="w-full"
    x-data="zenfleetDatepicker"
    data-value=""
    data-display-value=""
         data-max-date="31/01/2026"     wire:ignore>
        <label for="datepicker-697d451178543" class="block mb-2 text-sm font-medium text-gray-900">
        Date de naissance
            </label>
    
    <div class="relative">
        
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none z-10">
            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
            </svg>
        </div>

        
        <input
            x-ref="displayInput"
            type="text"
            id="datepicker-697d451178543"
            class="block w-full !pl-10 p-2.5 bg-gray-50 border-2 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 border-gray-300"
            placeholder="Choisir une date"
            x-model="displayValue"
                                    autocomplete="off"
            readonly>

        
        <input
            type="hidden"
            name="birth_date"
            x-model="serverDate"
            value="">
    </div>

        <p class="mt-1 text-xs text-gray-500">Date de naissance du chauffeur</p>
    </div>
                        <div class="">
        <label for="personal_phone" class="block mb-2 text-sm font-medium text-gray-900">
        Téléphone personnel
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:phone"
    data-inline="false"></span>        </div>
        
        <input
            type="tel"
            name="personal_phone"
            id="personal_phone"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: 0555123456"
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['personal_phone'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['personal_phone']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
             />
    </div>

    
    
    <p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['personal_phone'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['personal_phone']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>        <span>Ce champ est obligatoire et doit être correctement rempli</span>
    </p>
</div>
                        <div class="">
        <label for="personal_email" class="block mb-2 text-sm font-medium text-gray-900">
        Email personnel
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:envelope"
    data-inline="false"></span>        </div>
        
        <input
            type="email"
            name="personal_email"
            id="personal_email"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: ahmed.benali@email.com"
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['personal_email'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['personal_email']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
             />
    </div>

    
    
    <p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['personal_email'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['personal_email']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>        <span>Ce champ est obligatoire et doit être correctement rempli</span>
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
</div>

                    <div class="col-span-6 mt-6">
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

                    
                    <div class="col-span-6 mt-6">
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                            Photo du chauffeur
                        </label>
                        <div class="flex items-center gap-6">
                            
                            <div class="flex-shrink-0">
                                <div x-show="!photoPreview" class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center">
                                    <span
    class="iconify block w-12 h-12 text-gray-400"
    data-icon="heroicons:user"
    data-inline="false"></span>                                </div>
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
        </section>
    </div>
</div>

                
                <div class="relative">
            <div class="absolute left-5 top-6 bottom-6 w-px bg-slate-200/80"></div>
    
    <div class="relative pl-12">
                    <div class="absolute left-1.5 top-6">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white text-blue-600 shadow-sm ring-2 ring-blue-100">
                    <span
    class="iconify block w-5 h-5"
    data-icon="heroicons:briefcase"
    data-inline="false"></span>                </span>
            </div>
        
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-4 px-6 py-4 bg-slate-50/70 border-b border-slate-200">
                <div>
                                        <h3 class="text-sm font-semibold text-slate-900">Informations Professionnelles</h3>
                                            <p class="text-xs text-slate-500 mt-0.5">Matricule, statut et informations de recrutement</p>
                                    </div>
                
            </div>

            <div class="p-6 ">
                <div class="col-span-full w-full min-w-0 grid grid-cols-1 md:grid-cols-2 gap-6"
        style="position: relative;"
    >
        
    <div class="hidden md:block absolute left-1/2 top-4 bottom-4 w-px bg-gradient-to-b from-transparent via-slate-200 to-transparent" aria-hidden="true"></div>
    
    <div class="">
        <label for="employee_number" class="block mb-2 text-sm font-medium text-gray-900">
        Matricule
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:identification"
    data-inline="false"></span>        </div>
        
        <input
            type="text"
            name="employee_number"
            id="employee_number"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: EMP-2024-001"
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['employee_number'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['employee_number']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
             />
    </div>

        <p class="mt-2 text-sm text-gray-600">
        Numéro matricule unique
    </p>
    
    
    <p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['employee_number'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['employee_number']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>        <span>Ce champ est obligatoire et doit être correctement rempli</span>
    </p>
</div>
                        <div
    class="w-full"
    x-data="zenfleetDatepicker"
    data-value=""
    data-display-value=""
         data-max-date="31/01/2026"     wire:ignore>
        <label for="datepicker-697d451178953" class="block mb-2 text-sm font-medium text-gray-900">
        Date de recrutement
            </label>
    
    <div class="relative">
        
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none z-10">
            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
            </svg>
        </div>

        
        <input
            x-ref="displayInput"
            type="text"
            id="datepicker-697d451178953"
            class="block w-full !pl-10 p-2.5 bg-gray-50 border-2 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 border-gray-300"
            placeholder="Choisir une date"
            x-model="displayValue"
                                    autocomplete="off"
            readonly>

        
        <input
            type="hidden"
            name="recruitment_date"
            x-model="serverDate"
            value="">
    </div>

    </div>
                        <div
    class="w-full"
    x-data="zenfleetDatepicker"
    data-value=""
    data-display-value=""
     data-min-date="31/01/2026"         wire:ignore>
        <label for="datepicker-697d451178994" class="block mb-2 text-sm font-medium text-gray-900">
        Fin de contrat
            </label>
    
    <div class="relative">
        
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none z-10">
            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
            </svg>
        </div>

        
        <input
            x-ref="displayInput"
            type="text"
            id="datepicker-697d451178994"
            class="block w-full !pl-10 p-2.5 bg-gray-50 border-2 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 border-gray-300"
            placeholder="Choisir une date"
            x-model="displayValue"
                                    autocomplete="off"
            readonly>

        
        <input
            type="hidden"
            name="contract_end_date"
            x-model="serverDate"
            value="">
    </div>

        <p class="mt-1 text-xs text-gray-500">Date de fin du contrat (optionnel)</p>
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

        <label for="slimselect-status_id-697d4511789df" class="block mb-2 text-sm font-medium text-gray-900">
        Statut du Chauffeur
                <span class="text-red-500">*</span>
            </label>
    
    <select
        x-ref="select"
        name="status_id"
        id="slimselect-status_id-697d4511789df"
        class="slimselect-field w-full"
                        @change="validateField('status_id', $event.target.value)">

        
                        <option value="" data-placeholder="true">Sélectionnez un statut...</option>
        
                <option
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
            value="11"
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
                    </select>

    </div>
</div>

                    <div class="col-span-6 mt-6">
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
        </section>
    </div>
</div>

                
                <div class="relative">
            <div class="absolute left-5 top-6 bottom-6 w-px bg-slate-200/80"></div>
    
    <div class="relative pl-12">
                    <div class="absolute left-1.5 top-6">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white text-blue-600 shadow-sm ring-2 ring-blue-100">
                    <span
    class="iconify block w-5 h-5"
    data-icon="heroicons:identification"
    data-inline="false"></span>                </span>
            </div>
        
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-4 px-6 py-4 bg-slate-50/70 border-b border-slate-200">
                <div>
                                        <h3 class="text-sm font-semibold text-slate-900">Permis de Conduire</h3>
                                            <p class="text-xs text-slate-500 mt-0.5">Numéro, catégories et dates de validité</p>
                                    </div>
                
            </div>

            <div class="p-6 ">
                <div class="col-span-full w-full min-w-0 grid grid-cols-1 md:grid-cols-2 gap-6"
        style="position: relative;"
    >
        
    <div class="hidden md:block absolute left-1/2 top-4 bottom-4 w-px bg-gradient-to-b from-transparent via-slate-200 to-transparent" aria-hidden="true"></div>
    
    <div class="">
        <label for="license_number" class="block mb-2 text-sm font-medium text-gray-900">
        Numéro de permis
                <span class="text-red-600">*</span>
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:identification"
    data-inline="false"></span>        </div>
        
        <input
            type="text"
            name="license_number"
            id="license_number"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: 123456789"
            value=""
             required                         
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['license_number'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['license_number']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
             />
    </div>

        <p class="mt-2 text-sm text-gray-600">
        Numéro du permis de conduire
    </p>
    
    
    <p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['license_number'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['license_number']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>        <span>Ce champ est obligatoire et doit être correctement rempli</span>
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

        <label for="multi-select-license_categories-697d451178b31" class="block mb-2 text-sm font-medium text-gray-900">
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
                        :id="'multi-select-license_categories-697d451178b31-A1'">

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
                        :id="'multi-select-license_categories-697d451178b31-A'">

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
                        :id="'multi-select-license_categories-697d451178b31-B'">

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
                        :id="'multi-select-license_categories-697d451178b31-BE'">

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
                        :id="'multi-select-license_categories-697d451178b31-C1'">

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
                        :id="'multi-select-license_categories-697d451178b31-C1E'">

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
                        :id="'multi-select-license_categories-697d451178b31-C'">

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
                        :id="'multi-select-license_categories-697d451178b31-CE'">

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
                        :id="'multi-select-license_categories-697d451178b31-D'">

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
                        :id="'multi-select-license_categories-697d451178b31-DE'">

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
                        :id="'multi-select-license_categories-697d451178b31-F'">

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
</div>                        </div>

                        <div
    class="w-full"
    x-data="zenfleetDatepicker"
    data-value=""
    data-display-value=""
         data-max-date="31/01/2026"     wire:ignore>
        <label for="datepicker-697d451178bfa" class="block mb-2 text-sm font-medium text-gray-900">
        Date de délivrance
                <span class="text-red-500 ml-0.5">*</span>
            </label>
    
    <div class="relative">
        
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none z-10">
            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
            </svg>
        </div>

        
        <input
            x-ref="displayInput"
            type="text"
            id="datepicker-697d451178bfa"
            class="block w-full !pl-10 p-2.5 bg-gray-50 border-2 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 border-gray-300"
            placeholder="Choisir une date"
            x-model="displayValue"
                         required             autocomplete="off"
            readonly>

        
        <input
            type="hidden"
            name="license_issue_date"
            x-model="serverDate"
            value="">
    </div>

    </div>
                        <div
    class="w-full"
    x-data="zenfleetDatepicker"
    data-value=""
    data-display-value=""
     data-min-date="31/01/2026"         wire:ignore>
        <label for="datepicker-697d451178c3f" class="block mb-2 text-sm font-medium text-gray-900">
        Date d&#039;expiration
                <span class="text-red-500 ml-0.5">*</span>
            </label>
    
    <div class="relative">
        
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none z-10">
            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
            </svg>
        </div>

        
        <input
            x-ref="displayInput"
            type="text"
            id="datepicker-697d451178c3f"
            class="block w-full !pl-10 p-2.5 bg-gray-50 border-2 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 border-gray-300"
            placeholder="Choisir une date"
            x-model="displayValue"
                         required             autocomplete="off"
            readonly>

        
        <input
            type="hidden"
            name="license_expiry_date"
            x-model="serverDate"
            value="">
    </div>

        <p class="mt-1 text-xs text-gray-500">Date d&#039;expiration du permis</p>
    </div>
                        <div class="">
        <label for="license_authority" class="block mb-2 text-sm font-medium text-gray-900">
        Autorité de délivrance
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:building-office-2"
    data-inline="false"></span>        </div>
        
        <input
            type="text"
            name="license_authority"
            id="license_authority"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: Wilaya d&#039;Alger"
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['license_authority'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['license_authority']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
             />
    </div>

    
    
    <p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['license_authority'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['license_authority']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>        <span>Ce champ est obligatoire et doit être correctement rempli</span>
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
    data-inline="false"></span>                                    Permis vérifié
                                </span>
                            </label>
                        </div>
</div>
            </div>
        </section>
    </div>
</div>

                
                <div class="relative">
            <div class="absolute left-5 top-6 bottom-6 w-px bg-slate-200/80"></div>
    
    <div class="relative pl-12">
                    <div class="absolute left-1.5 top-6">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white text-blue-600 shadow-sm ring-2 ring-blue-100">
                    <span
    class="iconify block w-5 h-5"
    data-icon="heroicons:link"
    data-inline="false"></span>                </span>
            </div>
        
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-4 px-6 py-4 bg-slate-50/70 border-b border-slate-200">
                <div>
                                        <h3 class="text-sm font-semibold text-slate-900">Compte &amp; Contact d&#039;Urgence</h3>
                                            <p class="text-xs text-slate-500 mt-0.5">Accès applicatif optionnel et personne à contacter</p>
                                    </div>
                
            </div>

            <div class="p-6 ">
                <div class="space-y-6">
                        
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900 mb-3 flex items-center gap-2">
                                <span
    class="iconify block w-4 h-4 text-blue-600"
    data-icon="heroicons:user-circle"
    data-inline="false"></span>                                Compte Utilisateur (Optionnel)
                            </h4>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <div class="flex">
                                    <span
    class="iconify block w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0"
    data-icon="heroicons:information-circle"
    data-inline="false"></span>                                    <div>
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

        <label for="slimselect-user_id-697d451178dc2" class="block mb-2 text-sm font-medium text-gray-900">
        Compte utilisateur
            </label>
    
    <select
        x-ref="select"
        name="user_id"
        id="slimselect-user_id-697d451178dc2"
        class="slimselect-field w-full"
                        >

        
                        <option value="" data-placeholder="true">Rechercher un utilisateur...</option>
        
                <option
            value="2"
            >
            el hadi chemli (echemli@difex.dz)
        </option>
                    </select>

        <p class="mt-2 text-sm text-gray-500">
        Sélectionnez un compte existant ou laissez vide (optionnel)
    </p>
    </div>                            </div>
                        </div>

                        
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900 mb-3 flex items-center gap-2">
                                <span
    class="iconify block w-4 h-4 text-red-600"
    data-icon="heroicons:phone"
    data-inline="false"></span>                                Contact d'Urgence
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="">
        <label for="emergency_contact_name" class="block mb-2 text-sm font-medium text-gray-900">
        Nom du contact
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:user"
    data-inline="false"></span>        </div>
        
        <input
            type="text"
            name="emergency_contact_name"
            id="emergency_contact_name"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: Fatima Benali"
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['emergency_contact_name'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['emergency_contact_name']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
             />
    </div>

    
    
    <p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['emergency_contact_name'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['emergency_contact_name']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>        <span>Ce champ est obligatoire et doit être correctement rempli</span>
    </p>
</div>
                                <div class="">
        <label for="emergency_contact_phone" class="block mb-2 text-sm font-medium text-gray-900">
        Téléphone du contact
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:phone"
    data-inline="false"></span>        </div>
        
        <input
            type="tel"
            name="emergency_contact_phone"
            id="emergency_contact_phone"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: 0555987654"
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['emergency_contact_phone'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['emergency_contact_phone']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
             />
    </div>

    
    
    <p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['emergency_contact_phone'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['emergency_contact_phone']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>        <span>Ce champ est obligatoire et doit être correctement rempli</span>
    </p>
</div>
                                <div class="">
        <label for="emergency_contact_relationship" class="block mb-2 text-sm font-medium text-gray-900">
        Lien de parenté
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:users"
    data-inline="false"></span>        </div>
        
        <input
            type="text"
            name="emergency_contact_relationship"
            id="emergency_contact_relationship"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: Épouse, Frère, Mère"
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['emergency_contact_relationship'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['emergency_contact_relationship']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
             />
    </div>

    
    
    <p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['emergency_contact_relationship'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['emergency_contact_relationship']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>        <span>Ce champ est obligatoire et doit être correctement rempli</span>
    </p>
</div>                            </div>
                        </div>
                    </div>
            </div>
        </section>
    </div>
</div>

                
                <div class="rounded-2xl border border-slate-200 bg-white p-5 flex items-center justify-between">
                    <a href="http://localhost/admin/drivers"
                        class="text-gray-600 hover:text-gray-900 font-medium text-sm transition-colors">
                        Annuler
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md text-sm">
                        <span
    class="iconify block w-5 h-5"
    data-icon="heroicons:check"
    data-inline="false"></span>                        Créer le Chauffeur
                    </button>
                </div>
            </form>

        </div>
    </div>
</section>

                </div>
            </main>
        </div>
    </div>

    

    


    

    


    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ====================================================================
            // TOM SELECT - Initialisation Globale
            // ====================================================================
            if (window.TomSelect) {
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
            }

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
            if (window.TomSelect) {
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
            }

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
    window.zenfleetDriverErrors = [];
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
</body>

</html>
