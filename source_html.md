
<!DOCTYPE html>
<html lang="fr" class="h-full bg-zinc-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="Pvm3yjCAZeHGNl7uL7BBnE8scngjAaS6utgmQ4R2">
        <meta name="user-data" content="{&quot;id&quot;:2,&quot;name&quot;:&quot;el hadi chemli&quot;,&quot;role&quot;:&quot;Admin&quot;}">
    
    <title>Ajouter un Nouveau Véhicule - ZenFleet</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    

    
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    


    <!-- 🚀 Performance: Load CSS in Parallel (No JS blocking) -->
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
                        
                        
                                                <li class="flex flex-col" x-data="{ open: true }">
                            <button @click="open = !open"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-blue-600 text-white shadow-md">
                                <span
    class="iconify block w-5 h-5 mr-3 text-white"
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
                        
                        
                                                <li class="flex flex-col" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
                                <span
    class="iconify block w-5 h-5 mr-3 text-gray-600"
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

                                            
                                            
                                            
                                                                                        <li x-data="{ open: true }">
                                                <button @click="open = !open"
                                                    class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold bg-blue-600 text-white">
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
                                                                class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium bg-blue-50 text-blue-700">
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
                                                    class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
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
                                        <input type="hidden" name="_token" value="Pvm3yjCAZeHGNl7uL7BBnE8scngjAaS6utgmQ4R2" autocomplete="off">                                        <button type="submit"
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
    data-icon="heroicons:truck"
    data-inline="false"></span>                Ajouter un Nouveau Véhicule
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Complétez les 3 étapes pour enregistrer un véhicule
            </p>
        </div>

        
        
        
        <div x-data="vehicleFormValidation()" x-init="init()">

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-0 mb-6">
 
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
                                x-bind:data-icon="'lucide:' + &quot;file-text&quot;"
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
                        Identification
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
                                x-bind:data-icon="'lucide:' + &quot;settings&quot;"
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
                        Caractéristiques
                    </span>

                </li>
                            
                <li class="flex flex-col items-center relative flex-none">

                    
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
                                x-bind:data-icon="'lucide:' + &quot;receipt&quot;"
                                data-inline="false">
                            </span>
                        </div>

                        
                        
                    </div>

                    
                    <span class="mt-4 text-center text-sm font-semibold transition-all duration-200 whitespace-nowrap leading-snug"
                        x-bind:class="{
                            'text-blue-600 font-bold text-sm': currentStep === 3,
                            'text-blue-600 font-semibold': currentStep &gt; 3,
                            'text-gray-500': currentStep &lt; 3
                        }">
                        Acquisition
                    </span>

                </li>
                    </ol>
    </div>
</div>

                
                <form method="POST" action="http://localhost/admin/vehicles" @submit="onSubmit" class="p-6">
                    <input type="hidden" name="_token" value="Pvm3yjCAZeHGNl7uL7BBnE8scngjAaS6utgmQ4R2" autocomplete="off">                    <input type="hidden" name="current_step" x-model="currentStep">

                    
                    <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <span
    class="iconify block w-5 h-5 text-blue-600"
    data-icon="heroicons:identification"
    data-inline="false"></span>                                    Informations d'Identification
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="" @blur="validateField('registration_plate', $event.target.value)">
        <label for="registration_plate" class="block mb-2 text-sm font-medium text-gray-900">
        Immatriculation
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
            name="registration_plate"
            id="registration_plate"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: 16-12345-23"
            value=""
             required                         
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['registration_plate'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['registration_plate']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            @blur="validateField('registration_plate', $event.target.value)" />
    </div>
</div>

<p class="mt-2 text-sm text-gray-600">
    Numéro d&#039;immatriculation officiel du véhicule
</p>


<p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['registration_plate'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['registration_plate']"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-y-1"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    class="mt-2 text-sm text-red-600 flex items-start font-medium"
    style="display: none;">
    <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>    <span>Ce champ est obligatoire et doit être correctement rempli</span>
</p>
</div>
                                    <div class="" maxlength="17" @blur="validateField('vin', $event.target.value)">
        <label for="vin" class="block mb-2 text-sm font-medium text-gray-900">
        Numéro de série (VIN)
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:finger-print"
    data-inline="false"></span>        </div>
        
        <input
            type="text"
            name="vin"
            id="vin"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: 1HGBH41JXMN109186"
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['vin'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['vin']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            maxlength="17" @blur="validateField('vin', $event.target.value)" />
    </div>
</div>

<p class="mt-2 text-sm text-gray-600">
    17 caractères
</p>


<p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['vin'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['vin']"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-y-1"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    class="mt-2 text-sm text-red-600 flex items-start font-medium"
    style="display: none;">
    <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>    <span>Ce champ est obligatoire et doit être correctement rempli</span>
</p>
</div>
                                    <div class="" @blur="validateField('brand', $event.target.value)">
        <label for="brand" class="block mb-2 text-sm font-medium text-gray-900">
        Marque
                <span class="text-red-600">*</span>
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:building-storefront"
    data-inline="false"></span>        </div>
        
        <input
            type="text"
            name="brand"
            id="brand"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: Renault, Peugeot, Toyota..."
            value=""
             required                         
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['brand'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['brand']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            @blur="validateField('brand', $event.target.value)" />
    </div>
</div>



<p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['brand'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['brand']"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-y-1"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    class="mt-2 text-sm text-red-600 flex items-start font-medium"
    style="display: none;">
    <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>    <span>Ce champ est obligatoire et doit être correctement rempli</span>
</p>
</div>
                                    <div class="" @blur="validateField('model', $event.target.value)">
        <label for="model" class="block mb-2 text-sm font-medium text-gray-900">
        Modèle
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:truck"
    data-inline="false"></span>        </div>
        
        <input
            type="text"
            name="model"
            id="model"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: Clio, 208, Corolla..."
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['model'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['model']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            @blur="validateField('model', $event.target.value)" />
    </div>
</div>



<p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['model'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['model']"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-y-1"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    class="mt-2 text-sm text-red-600 flex items-start font-medium"
    style="display: none;">
    <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>    <span>Ce champ est obligatoire et doit être correctement rempli</span>
</p>
</div>
                                    <div class="md:col-span-2">
                                        <div class="">
        <label for="color" class="block mb-2 text-sm font-medium text-gray-900">
        Couleur
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:swatch"
    data-inline="false"></span>        </div>
        
        <input
            type="text"
            name="color"
            id="color"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: Blanc, Noir, Gris métallisé..."
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['color'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['color']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
             />
    </div>
</div>



<p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['color'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['color']"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-y-1"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    class="mt-2 text-sm text-red-600 flex items-start font-medium"
    style="display: none;">
    <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>    <span>Ce champ est obligatoire et doit être correctement rempli</span>
</p>
</div>                                    </div>
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
    data-icon="heroicons:cog-6-tooth"
    data-inline="false"></span>                                    Caractéristiques Techniques
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                    placeholderText: 'Sélectionnez un type...',
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
    class="" @change="validateField('vehicle_type_id', $event.target.value)">

        <label for="slimselect-vehicle_type_id-6976aba26b553" class="block mb-2 text-sm font-medium text-gray-900">
        Type de Véhicule
            </label>
    
    <select
        x-ref="select"
        name="vehicle_type_id"
        id="slimselect-vehicle_type_id-6976aba26b553"
        class="slimselect-field w-full"
                        @change="validateField('vehicle_type_id', $event.target.value)">

        
                        <option value="" data-placeholder="true">Sélectionnez un type...</option>
        
                <option
            value="21"
            >
            Autocar
        </option>
                <option
            value="9"
            >
            Autre
        </option>
                <option
            value="10"
            >
            Berline
        </option>
                <option
            value="13"
            >
            Break
        </option>
                <option
            value="6"
            >
            Bus
        </option>
                <option
            value="15"
            >
            Cabriolet
        </option>
                <option
            value="2"
            >
            Camion
        </option>
                <option
            value="20"
            >
            Camionnette
        </option>
                <option
            value="12"
            >
            Citadine
        </option>
                <option
            value="14"
            >
            Coupé
        </option>
                <option
            value="17"
            >
            Crossover
        </option>
                <option
            value="4"
            >
            Engin
        </option>
                <option
            value="5"
            >
            Fourgonnette
        </option>
                <option
            value="22"
            >
            Minibus
        </option>
                <option
            value="16"
            >
            Monospace
        </option>
                <option
            value="3"
            >
            Moto
        </option>
                <option
            value="18"
            >
            Pick-up
        </option>
                <option
            value="24"
            >
            Quad
        </option>
                <option
            value="25"
            >
            Remorque
        </option>
                <option
            value="11"
            >
            SUV
        </option>
                <option
            value="23"
            >
            Scooter
        </option>
                <option
            value="8"
            >
            Semi-remorque
        </option>
                <option
            value="19"
            >
            Utilitaire léger
        </option>
                <option
            value="7"
            >
            VUL
        </option>
                <option
            value="1"
            >
            Voiture
        </option>
                    </select>

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
                    placeholderText: 'Sélectionnez un carburant...',
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
    class="" @change="validateField('fuel_type_id', $event.target.value)">

        <label for="slimselect-fuel_type_id-6976aba26b806" class="block mb-2 text-sm font-medium text-gray-900">
        Type de Carburant
                <span class="text-red-500">*</span>
            </label>
    
    <select
        x-ref="select"
        name="fuel_type_id"
        id="slimselect-fuel_type_id-6976aba26b806"
        class="slimselect-field w-full"
                        @change="validateField('fuel_type_id', $event.target.value)">

        
                        <option value="" data-placeholder="true">Sélectionnez un carburant...</option>
        
                <option
            value="10"
            >
            Bioéthanol
        </option>
                <option
            value="2"
            >
            Diesel
        </option>
                <option
            value="1"
            >
            Essence
        </option>
                <option
            value="7"
            >
            Ethanol
        </option>
                <option
            value="9"
            >
            GNV
        </option>
                <option
            value="3"
            >
            GPL
        </option>
                <option
            value="5"
            >
            Hybride
        </option>
                <option
            value="6"
            >
            Hybride Rechargeable
        </option>
                <option
            value="8"
            >
            Hydrogène
        </option>
                <option
            value="4"
            >
            Électrique
        </option>
                    </select>

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
                    placeholderText: 'Sélectionnez une transmission...',
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
    class="" @change="validateField('transmission_type_id', $event.target.value)">

        <label for="slimselect-transmission_type_id-6976aba26b8e1" class="block mb-2 text-sm font-medium text-gray-900">
        Type de Transmission
            </label>
    
    <select
        x-ref="select"
        name="transmission_type_id"
        id="slimselect-transmission_type_id-6976aba26b8e1"
        class="slimselect-field w-full"
                        @change="validateField('transmission_type_id', $event.target.value)">

        
                        <option value="" data-placeholder="true">Sélectionnez une transmission...</option>
        
                <option
            value="2"
            >
            Automatique
        </option>
                <option
            value="4"
            >
            CVT
        </option>
                <option
            value="1"
            >
            Manuelle
        </option>
                <option
            value="3"
            >
            Semi-automatique
        </option>
                    </select>

    </div>
                                    <div class="" min="1950" max="2027">
        <label for="manufacturing_year" class="block mb-2 text-sm font-medium text-gray-900">
        Année de Fabrication
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:calendar"
    data-inline="false"></span>        </div>
        
        <input
            type="number"
            name="manufacturing_year"
            id="manufacturing_year"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: 2024"
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['manufacturing_year'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['manufacturing_year']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            min="1950" max="2027" />
    </div>
</div>



<p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['manufacturing_year'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['manufacturing_year']"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-y-1"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    class="mt-2 text-sm text-red-600 flex items-start font-medium"
    style="display: none;">
    <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>    <span>Ce champ est obligatoire et doit être correctement rempli</span>
</p>
</div>
                                    <div class="" min="1" max="99">
        <label for="seats" class="block mb-2 text-sm font-medium text-gray-900">
        Nombre de places
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:user-group"
    data-inline="false"></span>        </div>
        
        <input
            type="number"
            name="seats"
            id="seats"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: 5"
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['seats'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['seats']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            min="1" max="99" />
    </div>
</div>



<p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['seats'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['seats']"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-y-1"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    class="mt-2 text-sm text-red-600 flex items-start font-medium"
    style="display: none;">
    <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>    <span>Ce champ est obligatoire et doit être correctement rempli</span>
</p>
</div>
                                    <div class="" min="0">
        <label for="power_hp" class="block mb-2 text-sm font-medium text-gray-900">
        Puissance (CV)
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:bolt"
    data-inline="false"></span>        </div>
        
        <input
            type="number"
            name="power_hp"
            id="power_hp"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: 90"
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['power_hp'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['power_hp']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            min="0" />
    </div>
</div>



<p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['power_hp'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['power_hp']"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-y-1"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    class="mt-2 text-sm text-red-600 flex items-start font-medium"
    style="display: none;">
    <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>    <span>Ce champ est obligatoire et doit être correctement rempli</span>
</p>
</div>
                                    <div class="lg:col-span-3">
                                        <div class="" min="0">
        <label for="engine_displacement_cc" class="block mb-2 text-sm font-medium text-gray-900">
        Cylindrée (cc)
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:wrench-screwdriver"
    data-inline="false"></span>        </div>
        
        <input
            type="number"
            name="engine_displacement_cc"
            id="engine_displacement_cc"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: 1500"
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['engine_displacement_cc'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['engine_displacement_cc']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            min="0" />
    </div>
</div>

<p class="mt-2 text-sm text-gray-600">
    Capacité du moteur en centimètres cubes
</p>


<p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['engine_displacement_cc'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['engine_displacement_cc']"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-y-1"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    class="mt-2 text-sm text-red-600 flex items-start font-medium"
    style="display: none;">
    <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>    <span>Ce champ est obligatoire et doit être correctement rempli</span>
</p>
</div>                                    </div>
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
    data-icon="heroicons:currency-dollar"
    data-inline="false"></span>                                    Acquisition & Statut
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div
    class="w-full"
    x-data="zenfleetDatepicker"
    data-value=""
    data-display-value=""
         data-max-date="26/01/2026"     wire:ignore>
        <label for="datepicker-6976aba26bc6e" class="block mb-2 text-sm font-medium text-gray-900">
        Date d&#039;acquisition
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
            id="datepicker-6976aba26bc6e"
            class="block w-full !pl-10 p-2.5 bg-gray-50 border-2 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 border-gray-300"
            placeholder="Choisir une date"
            x-model="displayValue"
                                    autocomplete="off"
            readonly>

        
        <input
            type="hidden"
            name="acquisition_date"
            x-model="serverDate"
            value="">
    </div>

        <p class="mt-1 text-xs text-gray-500">Date d&#039;achat du véhicule</p>
    </div>
                                    <div class="" step="0.01" min="0">
        <label for="purchase_price" class="block mb-2 text-sm font-medium text-gray-900">
        Prix d&#039;achat (DA)
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:currency-dollar"
    data-inline="false"></span>        </div>
        
        <input
            type="number"
            name="purchase_price"
            id="purchase_price"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: 2500000"
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['purchase_price'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['purchase_price']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            step="0.01" min="0" />
    </div>
</div>

<p class="mt-2 text-sm text-gray-600">
    Prix d&#039;achat en Dinars Algériens
</p>


<p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['purchase_price'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['purchase_price']"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-y-1"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    class="mt-2 text-sm text-red-600 flex items-start font-medium"
    style="display: none;">
    <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>    <span>Ce champ est obligatoire et doit être correctement rempli</span>
</p>
</div>
                                    <div class="" step="0.01" min="0">
        <label for="current_value" class="block mb-2 text-sm font-medium text-gray-900">
        Valeur actuelle (DA)
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:currency-dollar"
    data-inline="false"></span>        </div>
        
        <input
            type="number"
            name="current_value"
            id="current_value"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: 2000000"
            value=""
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['current_value'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['current_value']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            step="0.01" min="0" />
    </div>
</div>

<p class="mt-2 text-sm text-gray-600">
    Valeur estimée actuelle
</p>


<p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['current_value'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['current_value']"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-y-1"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    class="mt-2 text-sm text-red-600 flex items-start font-medium"
    style="display: none;">
    <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>    <span>Ce champ est obligatoire et doit être correctement rempli</span>
</p>
</div>
                                    <div class="" min="0">
        <label for="initial_mileage" class="block mb-2 text-sm font-medium text-gray-900">
        Kilométrage Initial
            </label>
    
    <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="heroicons:chart-bar"
    data-inline="false"></span>        </div>
        
        <input
            type="number"
            name="initial_mileage"
            id="initial_mileage"
            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 bg-gray-50 pl-10"
            placeholder="Ex: 0"
            value="0"
                                    
            x-bind:class="(typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['initial_mileage'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['initial_mileage']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            min="0" />
    </div>
</div>

<p class="mt-2 text-sm text-gray-600">
    Kilométrage au moment de l&#039;acquisition
</p>


<p x-show="typeof fieldErrors !== 'undefined' && fieldErrors && fieldErrors['initial_mileage'] && typeof touchedFields !== 'undefined' && touchedFields && touchedFields['initial_mileage']"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-y-1"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    class="mt-2 text-sm text-red-600 flex items-start font-medium"
    style="display: none;">
    <span
    class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"
    data-icon="lucide:circle-alert"
    data-inline="false"></span>    <span>Ce champ est obligatoire et doit être correctement rempli</span>
</p>
</div>
                                    <div class="md:col-span-2">
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

        <label for="slimselect-status_id-6976aba26bee1" class="block mb-2 text-sm font-medium text-gray-900">
        Statut Initial
            </label>
    
    <select
        x-ref="select"
        name="status_id"
        id="slimselect-status_id-6976aba26bee1"
        class="slimselect-field w-full"
                        @change="validateField('status_id', $event.target.value)">

        
                        <option value="" data-placeholder="true">Sélectionnez un statut...</option>
        
                <option
            value="2"
            >
            Affecté
        </option>
                <option
            value="4"
            >
            En maintenance
        </option>
                <option
            value="3"
            >
            En panne
        </option>
                <option
            value="1"
            >
            Parking
        </option>
                <option
            value="5"
            >
            Réformé
        </option>
                <option
            value="6"
            >
            Vendu
        </option>
                    </select>

        <p class="mt-2 text-sm text-gray-500">
        État opérationnel du véhicule
    </p>
    </div>                                    </div>

                                    <div class="md:col-span-2">
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
                    placeholderText: 'Rechercher des utilisateurs...',
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

        <label for="slimselect-users-6976aba26bf40" class="block mb-2 text-sm font-medium text-gray-900">
        Utilisateurs Autorisés
            </label>
    
    <select
        x-ref="select"
        name="users"
        id="slimselect-users-6976aba26bf40"
        class="slimselect-field w-full"
                 multiple         >

        
                
                <option
            value="2"
            >
            el hadi chemli (echemli@difex.dz)
        </option>
                    </select>

        <p class="mt-2 text-sm text-gray-500">
        Sélectionnez les utilisateurs autorisés à utiliser ce véhicule
    </p>
    </div>                                    </div>

                                    <div class="md:col-span-2">
                                        <div class="">
  <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">
 Notes
  </label>
 
 <textarea
 name="notes"
 id="notes"
 rows="4"
 class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 !bg-gray-50"
 placeholder="Informations complémentaires sur le véhicule..."
   
 ></textarea>

  <p class="mt-2 text-sm text-gray-600">
 Ajoutez toute information utile (état, équipements, historique...)
 </p>
 </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-between">
                        <div>
                            <!--[if BLOCK]><![endif]--> <button
 type="button"
 class="inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed text-gray-900 bg-white border border-gray-200 hover:bg-gray-100 hover:text-blue-700 active:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:active:bg-gray-600 px-5 py-2.5 text-sm"
  x-show="currentStep > 1" @click="previousStep()"
 >
 <!--[if BLOCK]><![endif]--> <span
    class="iconify block w-5 h-5 mr-2"
    data-icon="heroicons:arrow-left"
    data-inline="false"></span> <!--[if ENDBLOCK]><![endif]-->

 Précédent

 <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
 </button>
<!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="http://localhost/admin/vehicles"
                                class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                                Annuler
                            </a>

                            <!--[if BLOCK]><![endif]--> <button
 type="button"
 class="inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 dark:active:bg-blue-800 px-5 py-2.5 text-sm"
  x-show="currentStep < 3" @click="nextStep()"
 >
 <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

 Suivant

 <!--[if BLOCK]><![endif]--> <span
    class="iconify block w-5 h-5 ml-2"
    data-icon="heroicons:arrow-right"
    data-inline="false"></span> <!--[if ENDBLOCK]><![endif]-->
 </button>
<!--[if ENDBLOCK]><![endif]-->

                            <!--[if BLOCK]><![endif]--> <button
 type="submit"
 class="inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed text-white bg-green-600 hover:bg-green-700 active:bg-green-800 dark:bg-green-500 dark:hover:bg-green-600 dark:active:bg-green-700 px-5 py-2.5 text-sm"
  x-show="currentStep === 3"
 >
 <!--[if BLOCK]><![endif]--> <span
    class="iconify block w-5 h-5 mr-2"
    data-icon="heroicons:check-circle"
    data-inline="false"></span> <!--[if ENDBLOCK]><![endif]-->

 Enregistrer le Véhicule

 <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
 </button>
<!--[if ENDBLOCK]><![endif]-->
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
    /* eslint-disable */
    // @ts-nocheck
    /**
     * ====================================================================
     * 🎯 ALPINE.JS VALIDATION SYSTEM - ENTERPRISE GRADE
     * ====================================================================
     * 
     * Système de validation en temps réel ultra-professionnel
     * 
     * FEATURES:
     * - Validation par phase avec état persistant
     * - Empêchement navigation si étape invalide
     * - Indicateurs visuels de validation
     * - Messages d'erreur contextuels
     * - Validation côté client synchronisée avec serveur
     * 
     * @version 3.0-Enterprise
     * @since 2025-01-19
     * ====================================================================
     */

    // Données d'erreurs serveur (injectées depuis PHP)
    window.zenfleetErrors = {
        hasErrors: {
            {
                $errors - > any() ? 'true' : 'false'
            }
        },
        keys: {
            !!json_encode($errors - > keys()) !!
        }
    };

    function vehicleFormValidation() {
        return {
            currentStep: {
                {
                    old('current_step', 1)
                }
            },

            steps: [{
                    label: 'Identification',
                    icon: 'file-text',
                    validated: false,
                    touched: false,
                    requiredFields: ['registration_plate', 'brand']
                },
                {
                    label: 'Caractéristiques',
                    icon: 'settings',
                    validated: false,
                    touched: false,
                    requiredFields: ['fuel_type_id']
                },
                {
                    label: 'Acquisition',
                    icon: 'receipt',
                    validated: false,
                    touched: false,
                    requiredFields: []
                }
            ],

            fieldErrors: {},

            // ⚠️NOUVEAU: Tracking des champs touchés pour validation temps réel
            touchedFields: {},

            init() {
                // Initialiser avec les erreurs serveur si présentes
                if (window.zenfleetErrors.hasErrors) {
                    this.markStepsWithErrors();
                    // Marquer tous les champs avec erreurs comme touchés
                    window.zenfleetErrors.keys.forEach(function(field) {
                        this.touchedFields[field] = true;
                    }.bind(this));
                }

                // NE PAS valider au chargement (pas de bordures rouges initiales)
                // La validation se fait uniquement après interaction utilisateur
            },

            /**
             * Marquer les étapes ayant des erreurs serveur
             */
            markStepsWithErrors() {
                const fieldToStepMap = {
                    'registration_plate': 0,
                    'vin': 0,
                    'brand': 0,
                    'model': 0,
                    'color': 0,
                    'vehicle_type_id': 1,
                    'fuel_type_id': 1,
                    'transmission_type_id': 1,
                    'manufacturing_year': 1,
                    'seats': 1,
                    'power_hp': 1,
                    'engine_displacement_cc': 1,
                    'acquisition_date': 2,
                    'purchase_price': 2,
                    'current_value': 2,
                    'initial_mileage': 2,
                    'status_id': 2,
                    'notes': 2
                };

                window.zenfleetErrors.keys.forEach(function(field) {
                    var stepIndex = fieldToStepMap[field];
                    if (stepIndex !== undefined) {
                        this.steps[stepIndex].touched = true;
                        this.steps[stepIndex].validated = false;
                    }
                }.bind(this));
            },

            /**
             * Valider un champ individuel
             * ⚠️ VALIDATION TEMPS RÉEL: Marque le champ comme touché + valide
             */
            validateField(fieldName, value) {
                // ✅ ÉTAPE 1: Marquer le champ comme TOUCHÉ (interaction utilisateur)
                this.touchedFields[fieldName] = true;

                // ✅ ÉTAPE 2: Valider selon les règles
                const rules = {
                    'registration_plate': (v) => v && v.length > 0 && v.length <= 50,
                    'brand': (v) => v && v.length > 0 && v.length <= 100,
                    'model': (v) => !v || (v.length > 0 && v.length <= 100),
                    'vin': (v) => !v || v.length === 17,
                    'vehicle_type_id': (v) => true,
                    'fuel_type_id': (v) => v && v.length > 0,
                    'transmission_type_id': (v) => true,
                    'acquisition_date': (v) => true,
                    'status_id': (v) => true,
                };

                const isValid = rules[fieldName] ? rules[fieldName](value) : true;

                // ✅ ÉTAPE 3: Gérer les erreurs
                if (!isValid) {
                    // Marquer le champ comme en erreur
                    this.fieldErrors[fieldName] = true;

                    // Ajouter classe slimselect-error pour SlimSelect
                    const input = document.querySelector(`[name="${fieldName}"]`);
                    if (input) {
                        const tsWrapper = input.closest('.ss-main');
                        if (tsWrapper) {
                            tsWrapper.classList.add('slimselect-error');
                        }
                    }
                } else {
                    // Nettoyer l'erreur si le champ devient valide
                    this.clearFieldError(fieldName);
                }

                return isValid;
            },

            /**
             * Valider l'étape actuelle
             */
            validateCurrentStep() {
                const stepIndex = this.currentStep - 1;
                const step = this.steps[stepIndex];

                // Marquer comme touchée
                step.touched = true;

                // Valider tous les champs requis de l'étape
                let allValid = true;

                step.requiredFields.forEach(fieldName => {
                    const input = document.querySelector(`[name="${fieldName}"]`);
                    if (input) {
                        const value = input.value;
                        const isValid = this.validateField(fieldName, value);
                        if (!isValid) {
                            allValid = false;
                        }
                    }
                });

                step.validated = allValid;
                return allValid;
            },

            /**
             * Passer à l'étape suivante (avec validation)
             */
            nextStep() {
                // Valider l'étape actuelle
                const isValid = this.validateCurrentStep();

                if (!isValid) {
                    // Afficher message d'erreur
                    this.$dispatch('show-toast', {
                        type: 'error',
                        message: 'Veuillez remplir tous les champs obligatoires avant de continuer'
                    });

                    // Faire vibrer les champs invalides
                    this.highlightInvalidFields();
                    return;
                }

                // Passer à l'étape suivante
                if (this.currentStep < 3) {
                    this.currentStep++;
                }
            },

            /**
             * Retourner à l'étape précédente
             */
            previousStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                }
            },

            /**
             * Mettre en évidence les champs invalides
             * ⚠️ VALIDATION TEMPS RÉEL: Marque les champs comme touchés lors du clic "Suivant"
             */
            highlightInvalidFields() {
                const stepIndex = this.currentStep - 1;
                const step = this.steps[stepIndex];

                step.requiredFields.forEach(fieldName => {
                    const input = document.querySelector(`[name="${fieldName}"]`);
                    if (input && !input.value) {
                        // ✅ Marquer le champ comme TOUCHÉ (utilisateur a tenté de passer à l'étape suivante)
                        this.touchedFields[fieldName] = true;

                        // Ajouter animation shake (temporaire)
                        input.classList.add('animate-shake');

                        // Gérer SlimSelect (wrapper avec classe .ss-main)
                        const tsWrapper = input.closest('.ss-main');
                        if (tsWrapper) {
                            tsWrapper.classList.add('slimselect-error');
                        }

                        // Retirer seulement l'animation shake après 500ms
                        // ⚠️ LA BORDURE ROUGE RESTE (gérée par fieldErrors + touchedFields)
                        setTimeout(() => {
                            input.classList.remove('animate-shake');
                        }, 500);
                    }
                });
            },

            /**
             * Retirer l'erreur d'un champ quand il devient valide
             */
            clearFieldError(fieldName) {
                delete this.fieldErrors[fieldName];

                // Retirer la classe slimselect-error si c'est un SlimSelect
                const input = document.querySelector(`[name="${fieldName}"]`);
                if (input) {
                    const tsWrapper = input.closest('.ss-main');
                    if (tsWrapper) {
                        tsWrapper.classList.remove('slimselect-error');
                    }
                }
            },

            /**
             * Validation finale avant soumission
             */
            onSubmit(e) {
                // Valider toutes les étapes
                let allValid = true;

                this.steps.forEach((step, index) => {
                    const tempCurrent = this.currentStep;
                    this.currentStep = index + 1;
                    const isValid = this.validateCurrentStep();
                    this.currentStep = tempCurrent;

                    if (!isValid) {
                        allValid = false;
                    }
                });

                if (!allValid) {
                    e.preventDefault();

                    // Aller à la première étape invalide
                    const firstInvalidStep = this.steps.findIndex(s => s.touched && !s.validated);
                    if (firstInvalidStep !== -1) {
                        this.currentStep = firstInvalidStep + 1;
                    }

                    this.$dispatch('show-toast', {
                        type: 'error',
                        message: 'Veuillez corriger les erreurs avant d\'enregistrer'
                    });

                    return false;
                }

                return true;
            }
        };
    }
</script>

<style>
    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        10%,
        30%,
        50%,
        70%,
        90% {
            transform: translateX(-4px);
        }

        20%,
        40%,
        60%,
        80% {
            transform: translateX(4px);
        }
    }

    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
</style>
    

    
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
    <script data-navigate-once="true">window.livewireScriptConfig = {"csrf":"Pvm3yjCAZeHGNl7uL7BBnE8scngjAaS6utgmQ4R2","uri":"\/livewire\/update","progressBar":"","nonce":""};</script>
</body>

</html>