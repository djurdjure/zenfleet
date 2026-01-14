
<!DOCTYPE html>
<html lang="fr" class="h-full bg-zinc-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="7X6no3HDKh3OvayVp3JDKdYNzndkkqqGFdLilGSU">
        <meta name="user-data" content="{&quot;id&quot;:4,&quot;name&quot;:&quot;admin zenfleet&quot;,&quot;role&quot;:&quot;Admin&quot;}">
    
    <title>ZenFleet Admin - ZenFleet</title>

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
                                                x-bind:style="`height: 50%; top: 0%;`"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                                                                <a href="http://localhost/admin/drivers"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 bg-blue-100 text-blue-700">
                                            <span
    class="iconify block w-4 h-4 mr-2.5 text-blue-600"
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
                                        <input type="hidden" name="_token" value="7X6no3HDKh3OvayVp3JDKdYNzndkkqqGFdLilGSU" autocomplete="off">                                        <button type="submit"
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
                            <div wire:snapshot="{&quot;data&quot;:{&quot;search&quot;:&quot;&quot;,&quot;status_id&quot;:&quot;&quot;,&quot;license_category&quot;:&quot;&quot;,&quot;visibility&quot;:&quot;active&quot;,&quot;perPage&quot;:25,&quot;sortField&quot;:&quot;created_at&quot;,&quot;sortDirection&quot;:&quot;desc&quot;,&quot;selectedDrivers&quot;:[[],{&quot;s&quot;:&quot;arr&quot;}],&quot;selectAll&quot;:false,&quot;restoringDriverId&quot;:null,&quot;showRestoreModal&quot;:false,&quot;forceDeletingDriverId&quot;:null,&quot;showForceDeleteModal&quot;:false,&quot;archivingDriverId&quot;:null,&quot;showArchiveModal&quot;:false,&quot;paginators&quot;:[{&quot;page&quot;:1},{&quot;s&quot;:&quot;arr&quot;}]},&quot;memo&quot;:{&quot;id&quot;:&quot;RbuvvU8BIzM1oveWEMUN&quot;,&quot;name&quot;:&quot;admin.drivers.driver-index&quot;,&quot;path&quot;:&quot;admin\/drivers&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;children&quot;:{&quot;status-13&quot;:[&quot;div&quot;,&quot;AiltmzMCVUtiy6k9r5C2&quot;],&quot;status-12&quot;:[&quot;div&quot;,&quot;r2Ql6Udu4xOJCTBOE0EM&quot;],&quot;status-11&quot;:[&quot;div&quot;,&quot;SvRhWqbPGsNlX9Pu8b5i&quot;],&quot;status-10&quot;:[&quot;div&quot;,&quot;OKYjOD735UUZ8xXMbidz&quot;],&quot;status-9&quot;:[&quot;div&quot;,&quot;hck72x6Dp2Z7qKvxJOQI&quot;],&quot;status-8&quot;:[&quot;div&quot;,&quot;cgQx8qgbL0AlOnbH0Meo&quot;],&quot;status-6&quot;:[&quot;div&quot;,&quot;xjOlSSaCesAiVMbAVGGe&quot;],&quot;status-4&quot;:[&quot;div&quot;,&quot;qcotP9zTrPYy34s7SFSH&quot;]},&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;errors&quot;:[],&quot;locale&quot;:&quot;fr&quot;},&quot;checksum&quot;:&quot;9c8f645869a63ab3c42ed7cdd1738a0d3e1c0744b1423145565dc33ebf53da12&quot;}" wire:effects="{&quot;url&quot;:{&quot;search&quot;:{&quot;as&quot;:null,&quot;use&quot;:&quot;replace&quot;,&quot;alwaysShow&quot;:false,&quot;except&quot;:&quot;&quot;},&quot;status_id&quot;:{&quot;as&quot;:null,&quot;use&quot;:&quot;replace&quot;,&quot;alwaysShow&quot;:false,&quot;except&quot;:&quot;&quot;},&quot;license_category&quot;:{&quot;as&quot;:null,&quot;use&quot;:&quot;replace&quot;,&quot;alwaysShow&quot;:false,&quot;except&quot;:&quot;&quot;},&quot;visibility&quot;:{&quot;as&quot;:null,&quot;use&quot;:&quot;replace&quot;,&quot;alwaysShow&quot;:false,&quot;except&quot;:&quot;&quot;},&quot;paginators.page&quot;:{&quot;as&quot;:&quot;page&quot;,&quot;use&quot;:&quot;push&quot;,&quot;alwaysShow&quot;:false,&quot;except&quot;:null}}}" wire:id="RbuvvU8BIzM1oveWEMUN">
    

    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <span
    class="iconify block w-6 h-6 text-blue-600"
    data-icon="lucide:users"
    data-inline="false"></span>                Gestion des Chauffeurs
                <span class="ml-2 text-sm font-normal text-gray-500">
                    (8)
                </span>
            </h1>

            
            <div wire:loading class="flex items-center gap-2 text-blue-600">
                <span
    class="iconify block w-5 h-5 animate-spin"
    data-icon="lucide:loader-2"
    data-inline="false"></span>                <span class="text-sm font-medium">Chargement...</span>
            </div>
        </div>

        
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total chauffeurs</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">8</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span
    class="iconify block w-6 h-6 text-blue-600"
    data-icon="lucide:users"
    data-inline="false"></span>                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Disponibles</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">4</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <span
    class="iconify block w-6 h-6 text-green-600"
    data-icon="lucide:user-check"
    data-inline="false"></span>                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En mission</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">2</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <span
    class="iconify block w-6 h-6 text-orange-600"
    data-icon="lucide:briefcase"
    data-inline="false"></span>                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En repos</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">0</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <span
    class="iconify block w-6 h-6 text-amber-600"
    data-icon="lucide:pause-circle"
    data-inline="false"></span>                    </div>
                </div>
            </div>
</div>
        
        <div class="mb-6" x-data="{ showFilters: false }">
            <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">
                
                <div class="flex-1 w-full lg:w-auto relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span
    class="iconify block w-5 h-5 text-gray-400"
    data-icon="lucide:search"
    data-inline="false"></span>                    </div>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Rechercher par nom, prénom, matricule..."
                        class="pl-10 pr-4 py-2.5 block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm">
                </div>

                
                
                <button
                    @click="showFilters = !showFilters"
                    type="button"
                    title="Filtres"
                    class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                    <span
    class="iconify block w-5 h-5 text-gray-500"
    data-icon="lucide:filter"
    data-inline="false"></span>                    <span
    class="iconify block w-4 h-4 text-gray-400 transition-transform duration-200" x-bind:class="showFilters ? 'rotate-180' : ''"
    data-icon="heroicons:chevron-down"
    data-inline="false"></span>                </button>

                
                <div class="flex items-center gap-2">
                    
                    <!--[if BLOCK]><![endif]-->                    <button wire:click="$set('visibility', 'archived')"
                        title="Voir Archives"
                        class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                        <span
    class="iconify block w-5 h-5 text-amber-600"
    data-icon="lucide:archive"
    data-inline="false"></span>                    </button>
                    <!--[if ENDBLOCK]><![endif]-->

                    
                    <div class="relative" x-data="{ exportOpen: false }">
                        <button
                            @click="exportOpen = !exportOpen"
                            @click.away="exportOpen = false"
                            type="button"
                            title="Exporter"
                            class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                            <span
    class="iconify block w-5 h-5 text-gray-500"
    data-icon="lucide:download"
    data-inline="false"></span>                            <span
    class="iconify block w-4 h-4 text-gray-400"
    data-icon="heroicons:chevron-down"
    data-inline="false"></span>                        </button>
                        
                        <div
                            x-show="exportOpen"
                            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                            style="display: none;">
                            <div class="py-1">
                                <a href="http://localhost/admin/drivers/export/pdf?visibility=active" class="group flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100">
                                    <span
    class="iconify block w-4 h-4 text-red-600"
    data-icon="lucide:file-text"
    data-inline="false"></span>                                    <span>Export PDF</span>
                                </a>
                                
                            </div>
                        </div>
                    </div>

                    
                    <a href="http://localhost/admin/drivers/import"
                        title="Importer"
                        class="inline-flex items-center gap-2 p-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <span
    class="iconify block w-5 h-5"
    data-icon="lucide:upload"
    data-inline="false"></span>                    </a>

                    
                    <a href="http://localhost/admin/drivers/create"
                        title="Nouveau Chauffeur"
                        class="inline-flex items-center gap-2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <span
    class="iconify block w-5 h-5"
    data-icon="lucide:plus"
    data-inline="false"></span>                    </a>
                </div>
            </div>

            
            <div
                x-show="showFilters"
                x-transition
                class="mt-4 bg-white rounded-lg border border-gray-200 p-4 shadow-sm"
                style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
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
                    placeholderText: 'Tous les statuts',
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
    class="" wire:model.live="status_id">

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

    <select
        x-ref="select"
        name="status_id"
        id="slimselect-status_id-6966b801e1cd2"
        class="slimselect-field w-full"
                        wire:model.live="status_id">

        
        <!--[if BLOCK]><![endif]-->        <option value="" data-placeholder="true">Tous les statuts</option>
                            <!--[if BLOCK]><![endif]-->                            <option value="10">Autre</option>
                                                        <option value="7">Disponible</option>
                                                        <option value="9">En congé</option>
                                                        <option value="12">En formation</option>
                                                        <option value="8">En mission</option>
                            <!--[if ENDBLOCK]><![endif]-->
        <!--[if ENDBLOCK]><![endif]-->
    </select>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
</div>                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie permis</label>
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
                    placeholderText: 'Toutes les catégories',
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
    class="" wire:model.live="license_category">

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

    <select
        x-ref="select"
        name="license_category"
        id="slimselect-license_category-6966b801e1d08"
        class="slimselect-field w-full"
                        wire:model.live="license_category">

        
        <!--[if BLOCK]><![endif]-->        <option value="" data-placeholder="true">Toutes les catégories</option>
                            <!--[if BLOCK]><![endif]-->                            <option value="A1">A1</option>
                                                        <option value="A">A</option>
                                                        <option value="B">B</option>
                                                        <option value="BE">BE</option>
                                                        <option value="C1">C1</option>
                                                        <option value="C1E">C1E</option>
                                                        <option value="C">C</option>
                                                        <option value="CE">CE</option>
                                                        <option value="D">D</option>
                                                        <option value="DE">DE</option>
                                                        <option value="F">F</option>
                            <!--[if ENDBLOCK]><![endif]-->
        <!--[if ENDBLOCK]><![endif]-->
    </select>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
</div>                    </div>
                </div>

                <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-200">
                    <button
                        wire:click="resetFilters"
                        class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                        x Réinitialiser
                    </button>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden relative">

            
            <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">
                                <input type="checkbox" wire:click="toggleAll"  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('last_name')">
                                Chauffeur
                                <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permis</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Véhicule Actuel</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!--[if BLOCK]><![endif]-->                        <tr wire:key="driver-13" class="hover:bg-gray-50 transition-colors duration-150 ">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" wire:click="toggleSelection(13)"  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                        <!--[if BLOCK]><![endif]-->                                        <img src="http://localhost/storage/drivers/photos/UWbCoTRz39mnlzF9j8nybfpFaRrWWK74YE6WgIUK.png" class="h-full w-full object-cover">
                                        <!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            chellouche hocine
                                        </div>
                                        <div class="text-xs text-gray-500">#DIF-4363001</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-900 flex items-center gap-1.5">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:phone"
    data-inline="false"></span> +213645890245
                                </div>
                                <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-1">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:mail"
    data-inline="false"></span> N/A
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-gray-900">DZ-9873986</div>
                                <!--[if BLOCK]><![endif]-->                                <div class="flex flex-wrap gap-1 mt-1">
                                    <!--[if BLOCK]><![endif]-->                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        A1
                                    </span>
                                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        A
                                    </span>
                                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        B
                                    </span>
                                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        C
                                    </span>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div wire:snapshot="{&quot;data&quot;:{&quot;driverId&quot;:13,&quot;driver&quot;:[null,{&quot;class&quot;:&quot;App\\Models\\Driver&quot;,&quot;key&quot;:13,&quot;s&quot;:&quot;mdl&quot;}],&quot;showConfirmModal&quot;:false,&quot;pendingStatus&quot;:null,&quot;pendingStatusEnum&quot;:null,&quot;confirmMessage&quot;:&quot;&quot;},&quot;memo&quot;:{&quot;id&quot;:&quot;AiltmzMCVUtiy6k9r5C2&quot;,&quot;name&quot;:&quot;admin.driver-status-badge-ultra-pro&quot;,&quot;path&quot;:&quot;admin\/drivers&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;children&quot;:[],&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;errors&quot;:[],&quot;locale&quot;:&quot;fr&quot;},&quot;checksum&quot;:&quot;1bd0e0263b7d63545a492fe0cc0b36ae32aa0db84c7e628fd6b12d734a9e4f1d&quot;}" wire:effects="{&quot;listeners&quot;:[&quot;refreshComponent&quot;,&quot;driverStatusUpdated&quot;,&quot;driverStatusChanged&quot;,&quot;echo:drivers,DriverStatusChanged&quot;]}" wire:id="AiltmzMCVUtiy6k9r5C2" class="relative inline-block"
    x-data="{ 
         open: false, 
         confirmModal: window.Livewire.find('AiltmzMCVUtiy6k9r5C2').entangle('showConfirmModal').live,
         toggle() { this.open = !this.open; },
         close() { this.open = false; },
         selectStatus(status) { 
             this.open = false; 
             $wire.prepareStatusChange(status); 
         }
     }"
    @click.stop
    x-init="$watch('confirmModal', value => { if (value) open = false; })">
    
    <!--[if BLOCK]><![endif]-->    <button
        @click.stop="toggle"
        @click.away="close"
        type="button"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold 
                   transition-all duration-200 hover:shadow-md hover:scale-105 cursor-pointer 
                   inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        title="Cliquer pour modifier le statut">
        
        <!--[if BLOCK]><![endif]-->        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:help-circle"
    data-inline="false"></span>        <!--[if ENDBLOCK]><![endif]-->

        
        <span>Autre</span>

        
        <svg class="w-3 h-3 opacity-60 transition-transform duration-200"
            :class="{ 'rotate-180': open }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]-->    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
        class="absolute left-0 mt-3 w-72 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] bg-white ring-1 ring-black/5 z-50 overflow-visible"
        style="display: none;">

        
        <div class="absolute -top-2 left-6 w-4 h-4 bg-white transform rotate-45 border-t border-l border-gray-100 shadow-sm"></div>

        <div class="relative bg-white rounded-xl overflow-hidden">
            
            <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <span
    class="iconify block w-3.5 h-3.5 text-blue-500"
    data-icon="lucide:git-branch"
    data-inline="false"></span>                        Changer le statut
                    </span>
                    <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-full">
                        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:x"
    data-inline="false"></span>                    </button>
                </div>
            </div>

            
            <div class="py-2 max-h-[300px] overflow-y-auto custom-scrollbar">
                <!--[if BLOCK]><![endif]-->                <button
                    @click.stop="selectStatus('disponible')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:user-check"
    data-inline="false"></span>                            Disponible
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                                <button
                    @click.stop="selectStatus('en_conge')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:palmtree"
    data-inline="false"></span>                            En congé
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                <!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <!--[if BLOCK]><![endif]-->            <div class="px-4 py-3 bg-gray-50/80 border-t border-gray-100 backdrop-blur-sm">
                <div class="flex items-start gap-2.5">
                    <span
    class="iconify block w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0"
    data-icon="lucide:info"
    data-inline="false"></span>                    <p class="text-xs text-gray-600 leading-relaxed">
                        <span class="font-medium text-gray-900">Note:</span>
                        Statut spécial : sanctionné, en formation, en maladie, etc.
                    </p>
                </div>
            </div>
            <!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    <!--[if ENDBLOCK]><![endif]-->

    
    <div x-show="confirmModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">

        
        <div x-show="confirmModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity">
        </div>

        
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="confirmModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:alert-circle"
    data-inline="false"></span>                            Confirmation de changement de statut
                        </h3>
                        <button @click="confirmModal = false; $wire.cancelStatusChange()"
                            class="text-white/80 hover:text-white transition-colors">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:x"
    data-inline="false"></span>                        </button>
                    </div>
                </div>

                
                <div class="px-6 py-5">
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 whitespace-pre-line"></p>
                    </div>

                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Chauffeur:</span>
                                <p class="font-medium text-gray-900">chellouche hocine</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Matricule:</span>
                                <p class="font-medium text-gray-900">DIF-4363001</p>
                            </div>
                            <!--[if BLOCK]><![endif]-->                            <div>
                                <span class="text-gray-500">Statut actuel:</span>
                                <p class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <span
    class="iconify block w-3 h-3"
    data-icon="lucide:help-circle"
    data-inline="false"></span>                                        Autre
                                    </span>
                                </p>
                            </div>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>

                
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                    <button wire:click="cancelStatusChange"
                        @click="confirmModal = false"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 
                                   rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 
                                   transition-all duration-150">
                        <span
    class="iconify block w-4 h-4"
    data-icon="lucide:x"
    data-inline="false"></span>                        Annuler
                    </button>

                    <button wire:click="confirmStatusChange"
                        type="button"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                   text-white rounded-lg text-sm font-medium 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                   transition-all duration-150 shadow-sm hover:shadow-md">
                        <span wire:loading.remove wire:target="confirmStatusChange">
                            <span
    class="iconify block w-4 h-4"
    data-icon="lucide:check"
    data-inline="false"></span>                        </span>
                        <span wire:loading wire:target="confirmStatusChange">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="confirmStatusChange">Confirmer</span>
                        <span wire:loading wire:target="confirmStatusChange">Traitement...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                <!--[if BLOCK]><![endif]-->                                <span class="text-gray-400 italic">Aucun véhicule</span>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <!--[if BLOCK]><![endif]-->                                    
                                    <a href="http://localhost/admin/drivers/13"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                        title="Voir">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:eye"
    data-inline="false"></span>                                    </a>
                                    <a href="http://localhost/admin/drivers/13/edit"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group"
                                        title="Modifier">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:edit-3"
    data-inline="false"></span>                                    </a>

                                    
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200 group">
                                            <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:more-vertical"
    data-inline="false"></span>                                        </button>

                                        <div x-show="open"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                            style="display: none;">
                                            <div class="py-1">
                                                <button wire:click="exportPdf(13); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-red-500"
    data-icon="lucide:file-text"
    data-inline="false"></span>                                                    Exporter PDF
                                                </button>
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <button wire:click="confirmArchive(13); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-amber-500"
    data-icon="lucide:archive"
    data-inline="false"></span>                                                    Archiver
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                        </tr>
                                                <tr wire:key="driver-12" class="hover:bg-gray-50 transition-colors duration-150 ">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" wire:click="toggleSelection(12)"  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                        <!--[if BLOCK]><![endif]-->                                        <img src="http://localhost/storage/drivers/photos/6funIROUjmmlVPOAeno0LeqmtBPmqWteqX2dajzO.png" class="h-full w-full object-cover">
                                        <!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            said Bounouh
                                        </div>
                                        <div class="text-xs text-gray-500">#DIF-09707</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-900 flex items-center gap-1.5">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:phone"
    data-inline="false"></span> +213567993689
                                </div>
                                <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-1">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:mail"
    data-inline="false"></span> N/A
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-gray-900">DZ-9879868-001</div>
                                <!--[if BLOCK]><![endif]-->                                <div class="flex flex-wrap gap-1 mt-1">
                                    <!--[if BLOCK]><![endif]-->                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        A1
                                    </span>
                                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        A
                                    </span>
                                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        B
                                    </span>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div wire:snapshot="{&quot;data&quot;:{&quot;driverId&quot;:12,&quot;driver&quot;:[null,{&quot;class&quot;:&quot;App\\Models\\Driver&quot;,&quot;key&quot;:12,&quot;s&quot;:&quot;mdl&quot;}],&quot;showConfirmModal&quot;:false,&quot;pendingStatus&quot;:null,&quot;pendingStatusEnum&quot;:null,&quot;confirmMessage&quot;:&quot;&quot;},&quot;memo&quot;:{&quot;id&quot;:&quot;r2Ql6Udu4xOJCTBOE0EM&quot;,&quot;name&quot;:&quot;admin.driver-status-badge-ultra-pro&quot;,&quot;path&quot;:&quot;admin\/drivers&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;children&quot;:[],&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;errors&quot;:[],&quot;locale&quot;:&quot;fr&quot;},&quot;checksum&quot;:&quot;d4c029862a75c74f680f6cf046c6250956aab09eb3f36874d9cc9d6d394b5901&quot;}" wire:effects="{&quot;listeners&quot;:[&quot;refreshComponent&quot;,&quot;driverStatusUpdated&quot;,&quot;driverStatusChanged&quot;,&quot;echo:drivers,DriverStatusChanged&quot;]}" wire:id="r2Ql6Udu4xOJCTBOE0EM" class="relative inline-block"
    x-data="{ 
         open: false, 
         confirmModal: window.Livewire.find('r2Ql6Udu4xOJCTBOE0EM').entangle('showConfirmModal').live,
         toggle() { this.open = !this.open; },
         close() { this.open = false; },
         selectStatus(status) { 
             this.open = false; 
             $wire.prepareStatusChange(status); 
         }
     }"
    @click.stop
    x-init="$watch('confirmModal', value => { if (value) open = false; })">
    
    <!--[if BLOCK]><![endif]-->    <button
        @click.stop="toggle"
        @click.away="close"
        type="button"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold 
                   transition-all duration-200 hover:shadow-md hover:scale-105 cursor-pointer 
                   inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        title="Cliquer pour modifier le statut">
        
        <!--[if BLOCK]><![endif]-->        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:user-check"
    data-inline="false"></span>        <!--[if ENDBLOCK]><![endif]-->

        
        <span>Disponible</span>

        
        <svg class="w-3 h-3 opacity-60 transition-transform duration-200"
            :class="{ 'rotate-180': open }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]-->    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
        class="absolute left-0 mt-3 w-72 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] bg-white ring-1 ring-black/5 z-50 overflow-visible"
        style="display: none;">

        
        <div class="absolute -top-2 left-6 w-4 h-4 bg-white transform rotate-45 border-t border-l border-gray-100 shadow-sm"></div>

        <div class="relative bg-white rounded-xl overflow-hidden">
            
            <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <span
    class="iconify block w-3.5 h-3.5 text-blue-500"
    data-icon="lucide:git-branch"
    data-inline="false"></span>                        Changer le statut
                    </span>
                    <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-full">
                        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:x"
    data-inline="false"></span>                    </button>
                </div>
            </div>

            
            <div class="py-2 max-h-[300px] overflow-y-auto custom-scrollbar">
                <!--[if BLOCK]><![endif]-->                <button
                    @click.stop="selectStatus('en_mission')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:truck"
    data-inline="false"></span>                            En mission
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                                <button
                    @click.stop="selectStatus('en_conge')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:palmtree"
    data-inline="false"></span>                            En congé
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                                <button
                    @click.stop="selectStatus('en_formation')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:graduation-cap"
    data-inline="false"></span>                            En formation
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                                <button
                    @click.stop="selectStatus('autre')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:help-circle"
    data-inline="false"></span>                            Autre
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                <!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <!--[if BLOCK]><![endif]-->            <div class="px-4 py-3 bg-gray-50/80 border-t border-gray-100 backdrop-blur-sm">
                <div class="flex items-start gap-2.5">
                    <span
    class="iconify block w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0"
    data-icon="lucide:info"
    data-inline="false"></span>                    <p class="text-xs text-gray-600 leading-relaxed">
                        <span class="font-medium text-gray-900">Note:</span>
                        Chauffeur disponible, peut recevoir une affectation de véhicule
                    </p>
                </div>
            </div>
            <!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    <!--[if ENDBLOCK]><![endif]-->

    
    <div x-show="confirmModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">

        
        <div x-show="confirmModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity">
        </div>

        
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="confirmModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:alert-circle"
    data-inline="false"></span>                            Confirmation de changement de statut
                        </h3>
                        <button @click="confirmModal = false; $wire.cancelStatusChange()"
                            class="text-white/80 hover:text-white transition-colors">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:x"
    data-inline="false"></span>                        </button>
                    </div>
                </div>

                
                <div class="px-6 py-5">
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 whitespace-pre-line"></p>
                    </div>

                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Chauffeur:</span>
                                <p class="font-medium text-gray-900">said Bounouh</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Matricule:</span>
                                <p class="font-medium text-gray-900">DIF-09707</p>
                            </div>
                            <!--[if BLOCK]><![endif]-->                            <div>
                                <span class="text-gray-500">Statut actuel:</span>
                                <p class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span
    class="iconify block w-3 h-3"
    data-icon="lucide:user-check"
    data-inline="false"></span>                                        Disponible
                                    </span>
                                </p>
                            </div>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>

                
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                    <button wire:click="cancelStatusChange"
                        @click="confirmModal = false"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 
                                   rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 
                                   transition-all duration-150">
                        <span
    class="iconify block w-4 h-4"
    data-icon="lucide:x"
    data-inline="false"></span>                        Annuler
                    </button>

                    <button wire:click="confirmStatusChange"
                        type="button"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                   text-white rounded-lg text-sm font-medium 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                   transition-all duration-150 shadow-sm hover:shadow-md">
                        <span wire:loading.remove wire:target="confirmStatusChange">
                            <span
    class="iconify block w-4 h-4"
    data-icon="lucide:check"
    data-inline="false"></span>                        </span>
                        <span wire:loading wire:target="confirmStatusChange">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="confirmStatusChange">Confirmer</span>
                        <span wire:loading wire:target="confirmStatusChange">Traitement...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                <!--[if BLOCK]><![endif]-->                                <span class="text-gray-400 italic">Aucun véhicule</span>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <!--[if BLOCK]><![endif]-->                                    
                                    <a href="http://localhost/admin/drivers/12"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                        title="Voir">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:eye"
    data-inline="false"></span>                                    </a>
                                    <a href="http://localhost/admin/drivers/12/edit"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group"
                                        title="Modifier">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:edit-3"
    data-inline="false"></span>                                    </a>

                                    
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200 group">
                                            <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:more-vertical"
    data-inline="false"></span>                                        </button>

                                        <div x-show="open"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                            style="display: none;">
                                            <div class="py-1">
                                                <button wire:click="exportPdf(12); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-red-500"
    data-icon="lucide:file-text"
    data-inline="false"></span>                                                    Exporter PDF
                                                </button>
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <button wire:click="confirmArchive(12); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-amber-500"
    data-icon="lucide:archive"
    data-inline="false"></span>                                                    Archiver
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                        </tr>
                                                <tr wire:key="driver-11" class="hover:bg-gray-50 transition-colors duration-150 ">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" wire:click="toggleSelection(11)"  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                        <!--[if BLOCK]><![endif]-->                                        <div class="h-10 w-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-semibold text-blue-700">
                                                MB
                                            </span>
                                        </div>
                                        <!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            Mounir Bensaid
                                        </div>
                                        <div class="text-xs text-gray-500">#EMP-78537-2000</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-900 flex items-center gap-1.5">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:phone"
    data-inline="false"></span> +213765789254
                                </div>
                                <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-1">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:mail"
    data-inline="false"></span> N/A
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-gray-900">9707-DZ-98</div>
                                <!--[if BLOCK]><![endif]-->                                <div class="flex flex-wrap gap-1 mt-1">
                                    <!--[if BLOCK]><![endif]-->                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        B
                                    </span>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div wire:snapshot="{&quot;data&quot;:{&quot;driverId&quot;:11,&quot;driver&quot;:[null,{&quot;class&quot;:&quot;App\\Models\\Driver&quot;,&quot;key&quot;:11,&quot;s&quot;:&quot;mdl&quot;}],&quot;showConfirmModal&quot;:false,&quot;pendingStatus&quot;:null,&quot;pendingStatusEnum&quot;:null,&quot;confirmMessage&quot;:&quot;&quot;},&quot;memo&quot;:{&quot;id&quot;:&quot;SvRhWqbPGsNlX9Pu8b5i&quot;,&quot;name&quot;:&quot;admin.driver-status-badge-ultra-pro&quot;,&quot;path&quot;:&quot;admin\/drivers&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;children&quot;:[],&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;errors&quot;:[],&quot;locale&quot;:&quot;fr&quot;},&quot;checksum&quot;:&quot;81ca45baf5d3e1133f76fb72e4755fb34c96f1889822d07116e992558284a32c&quot;}" wire:effects="{&quot;listeners&quot;:[&quot;refreshComponent&quot;,&quot;driverStatusUpdated&quot;,&quot;driverStatusChanged&quot;,&quot;echo:drivers,DriverStatusChanged&quot;]}" wire:id="SvRhWqbPGsNlX9Pu8b5i" class="relative inline-block"
    x-data="{ 
         open: false, 
         confirmModal: window.Livewire.find('SvRhWqbPGsNlX9Pu8b5i').entangle('showConfirmModal').live,
         toggle() { this.open = !this.open; },
         close() { this.open = false; },
         selectStatus(status) { 
             this.open = false; 
             $wire.prepareStatusChange(status); 
         }
     }"
    @click.stop
    x-init="$watch('confirmModal', value => { if (value) open = false; })">
    
    <!--[if BLOCK]><![endif]-->    <button
        @click.stop="toggle"
        @click.away="close"
        type="button"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold 
                   transition-all duration-200 hover:shadow-md hover:scale-105 cursor-pointer 
                   inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        title="Cliquer pour modifier le statut">
        
        <!--[if BLOCK]><![endif]-->        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:truck"
    data-inline="false"></span>        <!--[if ENDBLOCK]><![endif]-->

        
        <span>En mission</span>

        
        <svg class="w-3 h-3 opacity-60 transition-transform duration-200"
            :class="{ 'rotate-180': open }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]-->    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
        class="absolute left-0 mt-3 w-72 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] bg-white ring-1 ring-black/5 z-50 overflow-visible"
        style="display: none;">

        
        <div class="absolute -top-2 left-6 w-4 h-4 bg-white transform rotate-45 border-t border-l border-gray-100 shadow-sm"></div>

        <div class="relative bg-white rounded-xl overflow-hidden">
            
            <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <span
    class="iconify block w-3.5 h-3.5 text-blue-500"
    data-icon="lucide:git-branch"
    data-inline="false"></span>                        Changer le statut
                    </span>
                    <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-full">
                        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:x"
    data-inline="false"></span>                    </button>
                </div>
            </div>

            
            <div class="py-2 max-h-[300px] overflow-y-auto custom-scrollbar">
                <!--[if BLOCK]><![endif]-->                <button
                    @click.stop="selectStatus('disponible')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:user-check"
    data-inline="false"></span>                            Disponible
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                <!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <!--[if BLOCK]><![endif]-->            <div class="px-4 py-3 bg-gray-50/80 border-t border-gray-100 backdrop-blur-sm">
                <div class="flex items-start gap-2.5">
                    <span
    class="iconify block w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0"
    data-icon="lucide:info"
    data-inline="false"></span>                    <p class="text-xs text-gray-600 leading-relaxed">
                        <span class="font-medium text-gray-900">Note:</span>
                        Chauffeur actuellement en mission avec un véhicule affecté
                    </p>
                </div>
            </div>
            <!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    <!--[if ENDBLOCK]><![endif]-->

    
    <div x-show="confirmModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">

        
        <div x-show="confirmModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity">
        </div>

        
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="confirmModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:alert-circle"
    data-inline="false"></span>                            Confirmation de changement de statut
                        </h3>
                        <button @click="confirmModal = false; $wire.cancelStatusChange()"
                            class="text-white/80 hover:text-white transition-colors">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:x"
    data-inline="false"></span>                        </button>
                    </div>
                </div>

                
                <div class="px-6 py-5">
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 whitespace-pre-line"></p>
                    </div>

                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Chauffeur:</span>
                                <p class="font-medium text-gray-900">Mounir Bensaid</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Matricule:</span>
                                <p class="font-medium text-gray-900">EMP-78537-2000</p>
                            </div>
                            <!--[if BLOCK]><![endif]-->                            <div>
                                <span class="text-gray-500">Statut actuel:</span>
                                <p class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <span
    class="iconify block w-3 h-3"
    data-icon="lucide:truck"
    data-inline="false"></span>                                        En mission
                                    </span>
                                </p>
                            </div>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>

                
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                    <button wire:click="cancelStatusChange"
                        @click="confirmModal = false"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 
                                   rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 
                                   transition-all duration-150">
                        <span
    class="iconify block w-4 h-4"
    data-icon="lucide:x"
    data-inline="false"></span>                        Annuler
                    </button>

                    <button wire:click="confirmStatusChange"
                        type="button"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                   text-white rounded-lg text-sm font-medium 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                   transition-all duration-150 shadow-sm hover:shadow-md">
                        <span wire:loading.remove wire:target="confirmStatusChange">
                            <span
    class="iconify block w-4 h-4"
    data-icon="lucide:check"
    data-inline="false"></span>                        </span>
                        <span wire:loading wire:target="confirmStatusChange">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="confirmStatusChange">Confirmer</span>
                        <span wire:loading wire:target="confirmStatusChange">Traitement...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                <!--[if BLOCK]><![endif]-->                                <div class="flex items-center gap-1.5">
                                    <span
    class="iconify block w-3.5 h-3.5 text-blue-600"
    data-icon="lucide:car"
    data-inline="false"></span>                                    <span class="font-medium text-gray-900">118910-16</span>
                                </div>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <!--[if BLOCK]><![endif]-->                                    
                                    <a href="http://localhost/admin/drivers/11"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                        title="Voir">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:eye"
    data-inline="false"></span>                                    </a>
                                    <a href="http://localhost/admin/drivers/11/edit"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group"
                                        title="Modifier">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:edit-3"
    data-inline="false"></span>                                    </a>

                                    
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200 group">
                                            <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:more-vertical"
    data-inline="false"></span>                                        </button>

                                        <div x-show="open"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                            style="display: none;">
                                            <div class="py-1">
                                                <button wire:click="exportPdf(11); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-red-500"
    data-icon="lucide:file-text"
    data-inline="false"></span>                                                    Exporter PDF
                                                </button>
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <button wire:click="confirmArchive(11); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-amber-500"
    data-icon="lucide:archive"
    data-inline="false"></span>                                                    Archiver
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                        </tr>
                                                <tr wire:key="driver-10" class="hover:bg-gray-50 transition-colors duration-150 ">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" wire:click="toggleSelection(10)"  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                        <!--[if BLOCK]><![endif]-->                                        <img src="http://localhost/storage/drivers/photos/Hssc9BzkW3Bjpqn2W2p2r8OZHPxXFkV3TQsPoICg.png" class="h-full w-full object-cover">
                                        <!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            Med BOUBENIA
                                        </div>
                                        <div class="text-xs text-gray-500">#DIFEX-00000</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-900 flex items-center gap-1.5">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:phone"
    data-inline="false"></span> +213770457000
                                </div>
                                <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-1">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:mail"
    data-inline="false"></span> m.boubenia@gmail.dz
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-gray-900">Dz-53000-00</div>
                                <!--[if BLOCK]><![endif]-->                                <div class="flex flex-wrap gap-1 mt-1">
                                    <!--[if BLOCK]><![endif]-->                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        B
                                    </span>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div wire:snapshot="{&quot;data&quot;:{&quot;driverId&quot;:10,&quot;driver&quot;:[null,{&quot;class&quot;:&quot;App\\Models\\Driver&quot;,&quot;key&quot;:10,&quot;s&quot;:&quot;mdl&quot;}],&quot;showConfirmModal&quot;:false,&quot;pendingStatus&quot;:null,&quot;pendingStatusEnum&quot;:null,&quot;confirmMessage&quot;:&quot;&quot;},&quot;memo&quot;:{&quot;id&quot;:&quot;OKYjOD735UUZ8xXMbidz&quot;,&quot;name&quot;:&quot;admin.driver-status-badge-ultra-pro&quot;,&quot;path&quot;:&quot;admin\/drivers&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;children&quot;:[],&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;errors&quot;:[],&quot;locale&quot;:&quot;fr&quot;},&quot;checksum&quot;:&quot;53e91e5c18b9b4f6557adbf1e28355c644ef7fd14a516bc649fad6cd8a83c635&quot;}" wire:effects="{&quot;listeners&quot;:[&quot;refreshComponent&quot;,&quot;driverStatusUpdated&quot;,&quot;driverStatusChanged&quot;,&quot;echo:drivers,DriverStatusChanged&quot;]}" wire:id="OKYjOD735UUZ8xXMbidz" class="relative inline-block"
    x-data="{ 
         open: false, 
         confirmModal: window.Livewire.find('OKYjOD735UUZ8xXMbidz').entangle('showConfirmModal').live,
         toggle() { this.open = !this.open; },
         close() { this.open = false; },
         selectStatus(status) { 
             this.open = false; 
             $wire.prepareStatusChange(status); 
         }
     }"
    @click.stop
    x-init="$watch('confirmModal', value => { if (value) open = false; })">
    
    <!--[if BLOCK]><![endif]-->    <button
        @click.stop="toggle"
        @click.away="close"
        type="button"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold 
                   transition-all duration-200 hover:shadow-md hover:scale-105 cursor-pointer 
                   inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        title="Cliquer pour modifier le statut">
        
        <!--[if BLOCK]><![endif]-->        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:graduation-cap"
    data-inline="false"></span>        <!--[if ENDBLOCK]><![endif]-->

        
        <span>En formation</span>

        
        <svg class="w-3 h-3 opacity-60 transition-transform duration-200"
            :class="{ 'rotate-180': open }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]-->    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
        class="absolute left-0 mt-3 w-72 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] bg-white ring-1 ring-black/5 z-50 overflow-visible"
        style="display: none;">

        
        <div class="absolute -top-2 left-6 w-4 h-4 bg-white transform rotate-45 border-t border-l border-gray-100 shadow-sm"></div>

        <div class="relative bg-white rounded-xl overflow-hidden">
            
            <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <span
    class="iconify block w-3.5 h-3.5 text-blue-500"
    data-icon="lucide:git-branch"
    data-inline="false"></span>                        Changer le statut
                    </span>
                    <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-full">
                        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:x"
    data-inline="false"></span>                    </button>
                </div>
            </div>

            
            <div class="py-2 max-h-[300px] overflow-y-auto custom-scrollbar">
                <!--[if BLOCK]><![endif]-->                <button
                    @click.stop="selectStatus('disponible')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:user-check"
    data-inline="false"></span>                            Disponible
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                                <button
                    @click.stop="selectStatus('autre')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:help-circle"
    data-inline="false"></span>                            Autre
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                <!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <!--[if BLOCK]><![endif]-->            <div class="px-4 py-3 bg-gray-50/80 border-t border-gray-100 backdrop-blur-sm">
                <div class="flex items-start gap-2.5">
                    <span
    class="iconify block w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0"
    data-icon="lucide:info"
    data-inline="false"></span>                    <p class="text-xs text-gray-600 leading-relaxed">
                        <span class="font-medium text-gray-900">Note:</span>
                        Chauffeur en période de formation
                    </p>
                </div>
            </div>
            <!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    <!--[if ENDBLOCK]><![endif]-->

    
    <div x-show="confirmModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">

        
        <div x-show="confirmModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity">
        </div>

        
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="confirmModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:alert-circle"
    data-inline="false"></span>                            Confirmation de changement de statut
                        </h3>
                        <button @click="confirmModal = false; $wire.cancelStatusChange()"
                            class="text-white/80 hover:text-white transition-colors">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:x"
    data-inline="false"></span>                        </button>
                    </div>
                </div>

                
                <div class="px-6 py-5">
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 whitespace-pre-line"></p>
                    </div>

                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Chauffeur:</span>
                                <p class="font-medium text-gray-900">Med BOUBENIA</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Matricule:</span>
                                <p class="font-medium text-gray-900">DIFEX-00000</p>
                            </div>
                            <!--[if BLOCK]><![endif]-->                            <div>
                                <span class="text-gray-500">Statut actuel:</span>
                                <p class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <span
    class="iconify block w-3 h-3"
    data-icon="lucide:graduation-cap"
    data-inline="false"></span>                                        En formation
                                    </span>
                                </p>
                            </div>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>

                
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                    <button wire:click="cancelStatusChange"
                        @click="confirmModal = false"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 
                                   rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 
                                   transition-all duration-150">
                        <span
    class="iconify block w-4 h-4"
    data-icon="lucide:x"
    data-inline="false"></span>                        Annuler
                    </button>

                    <button wire:click="confirmStatusChange"
                        type="button"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                   text-white rounded-lg text-sm font-medium 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                   transition-all duration-150 shadow-sm hover:shadow-md">
                        <span wire:loading.remove wire:target="confirmStatusChange">
                            <span
    class="iconify block w-4 h-4"
    data-icon="lucide:check"
    data-inline="false"></span>                        </span>
                        <span wire:loading wire:target="confirmStatusChange">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="confirmStatusChange">Confirmer</span>
                        <span wire:loading wire:target="confirmStatusChange">Traitement...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                <!--[if BLOCK]><![endif]-->                                <span class="text-gray-400 italic">Aucun véhicule</span>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <!--[if BLOCK]><![endif]-->                                    
                                    <a href="http://localhost/admin/drivers/10"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                        title="Voir">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:eye"
    data-inline="false"></span>                                    </a>
                                    <a href="http://localhost/admin/drivers/10/edit"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group"
                                        title="Modifier">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:edit-3"
    data-inline="false"></span>                                    </a>

                                    
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200 group">
                                            <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:more-vertical"
    data-inline="false"></span>                                        </button>

                                        <div x-show="open"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                            style="display: none;">
                                            <div class="py-1">
                                                <button wire:click="exportPdf(10); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-red-500"
    data-icon="lucide:file-text"
    data-inline="false"></span>                                                    Exporter PDF
                                                </button>
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <button wire:click="confirmArchive(10); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-amber-500"
    data-icon="lucide:archive"
    data-inline="false"></span>                                                    Archiver
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                        </tr>
                                                <tr wire:key="driver-9" class="hover:bg-gray-50 transition-colors duration-150 ">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" wire:click="toggleSelection(9)"  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                        <!--[if BLOCK]><![endif]-->                                        <img src="http://localhost/storage/drivers/photos/aNLnjnIDztumn2WmqnBLjGlu5dI04w7UtwvVUCXK.png" class="h-full w-full object-cover">
                                        <!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            Ahmed Lounis
                                        </div>
                                        <div class="text-xs text-gray-500">#876387DZ987</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-900 flex items-center gap-1.5">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:phone"
    data-inline="false"></span> +213567800927
                                </div>
                                <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-1">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:mail"
    data-inline="false"></span> N/A
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-gray-900">56DZ98736</div>
                                <!--[if BLOCK]><![endif]-->                                <div class="flex flex-wrap gap-1 mt-1">
                                    <!--[if BLOCK]><![endif]-->                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        B
                                    </span>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div wire:snapshot="{&quot;data&quot;:{&quot;driverId&quot;:9,&quot;driver&quot;:[null,{&quot;class&quot;:&quot;App\\Models\\Driver&quot;,&quot;key&quot;:9,&quot;s&quot;:&quot;mdl&quot;}],&quot;showConfirmModal&quot;:false,&quot;pendingStatus&quot;:null,&quot;pendingStatusEnum&quot;:null,&quot;confirmMessage&quot;:&quot;&quot;},&quot;memo&quot;:{&quot;id&quot;:&quot;hck72x6Dp2Z7qKvxJOQI&quot;,&quot;name&quot;:&quot;admin.driver-status-badge-ultra-pro&quot;,&quot;path&quot;:&quot;admin\/drivers&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;children&quot;:[],&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;errors&quot;:[],&quot;locale&quot;:&quot;fr&quot;},&quot;checksum&quot;:&quot;1b9a1b1c919b7b555d6c066d840c87560b602fed67499cbfb5ee682fed03b2dc&quot;}" wire:effects="{&quot;listeners&quot;:[&quot;refreshComponent&quot;,&quot;driverStatusUpdated&quot;,&quot;driverStatusChanged&quot;,&quot;echo:drivers,DriverStatusChanged&quot;]}" wire:id="hck72x6Dp2Z7qKvxJOQI" class="relative inline-block"
    x-data="{ 
         open: false, 
         confirmModal: window.Livewire.find('hck72x6Dp2Z7qKvxJOQI').entangle('showConfirmModal').live,
         toggle() { this.open = !this.open; },
         close() { this.open = false; },
         selectStatus(status) { 
             this.open = false; 
             $wire.prepareStatusChange(status); 
         }
     }"
    @click.stop
    x-init="$watch('confirmModal', value => { if (value) open = false; })">
    
    <!--[if BLOCK]><![endif]-->    <button
        @click.stop="toggle"
        @click.away="close"
        type="button"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold 
                   transition-all duration-200 hover:shadow-md hover:scale-105 cursor-pointer 
                   inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        title="Cliquer pour modifier le statut">
        
        <!--[if BLOCK]><![endif]-->        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:truck"
    data-inline="false"></span>        <!--[if ENDBLOCK]><![endif]-->

        
        <span>En mission</span>

        
        <svg class="w-3 h-3 opacity-60 transition-transform duration-200"
            :class="{ 'rotate-180': open }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]-->    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
        class="absolute left-0 mt-3 w-72 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] bg-white ring-1 ring-black/5 z-50 overflow-visible"
        style="display: none;">

        
        <div class="absolute -top-2 left-6 w-4 h-4 bg-white transform rotate-45 border-t border-l border-gray-100 shadow-sm"></div>

        <div class="relative bg-white rounded-xl overflow-hidden">
            
            <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <span
    class="iconify block w-3.5 h-3.5 text-blue-500"
    data-icon="lucide:git-branch"
    data-inline="false"></span>                        Changer le statut
                    </span>
                    <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-full">
                        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:x"
    data-inline="false"></span>                    </button>
                </div>
            </div>

            
            <div class="py-2 max-h-[300px] overflow-y-auto custom-scrollbar">
                <!--[if BLOCK]><![endif]-->                <button
                    @click.stop="selectStatus('disponible')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:user-check"
    data-inline="false"></span>                            Disponible
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                <!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <!--[if BLOCK]><![endif]-->            <div class="px-4 py-3 bg-gray-50/80 border-t border-gray-100 backdrop-blur-sm">
                <div class="flex items-start gap-2.5">
                    <span
    class="iconify block w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0"
    data-icon="lucide:info"
    data-inline="false"></span>                    <p class="text-xs text-gray-600 leading-relaxed">
                        <span class="font-medium text-gray-900">Note:</span>
                        Chauffeur actuellement en mission avec un véhicule affecté
                    </p>
                </div>
            </div>
            <!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    <!--[if ENDBLOCK]><![endif]-->

    
    <div x-show="confirmModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">

        
        <div x-show="confirmModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity">
        </div>

        
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="confirmModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:alert-circle"
    data-inline="false"></span>                            Confirmation de changement de statut
                        </h3>
                        <button @click="confirmModal = false; $wire.cancelStatusChange()"
                            class="text-white/80 hover:text-white transition-colors">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:x"
    data-inline="false"></span>                        </button>
                    </div>
                </div>

                
                <div class="px-6 py-5">
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 whitespace-pre-line"></p>
                    </div>

                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Chauffeur:</span>
                                <p class="font-medium text-gray-900">Ahmed Lounis</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Matricule:</span>
                                <p class="font-medium text-gray-900">876387DZ987</p>
                            </div>
                            <!--[if BLOCK]><![endif]-->                            <div>
                                <span class="text-gray-500">Statut actuel:</span>
                                <p class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <span
    class="iconify block w-3 h-3"
    data-icon="lucide:truck"
    data-inline="false"></span>                                        En mission
                                    </span>
                                </p>
                            </div>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>

                
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                    <button wire:click="cancelStatusChange"
                        @click="confirmModal = false"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 
                                   rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 
                                   transition-all duration-150">
                        <span
    class="iconify block w-4 h-4"
    data-icon="lucide:x"
    data-inline="false"></span>                        Annuler
                    </button>

                    <button wire:click="confirmStatusChange"
                        type="button"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                   text-white rounded-lg text-sm font-medium 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                   transition-all duration-150 shadow-sm hover:shadow-md">
                        <span wire:loading.remove wire:target="confirmStatusChange">
                            <span
    class="iconify block w-4 h-4"
    data-icon="lucide:check"
    data-inline="false"></span>                        </span>
                        <span wire:loading wire:target="confirmStatusChange">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="confirmStatusChange">Confirmer</span>
                        <span wire:loading wire:target="confirmStatusChange">Traitement...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                <!--[if BLOCK]><![endif]-->                                <div class="flex items-center gap-1.5">
                                    <span
    class="iconify block w-3.5 h-3.5 text-blue-600"
    data-icon="lucide:car"
    data-inline="false"></span>                                    <span class="font-medium text-gray-900">631035-16</span>
                                </div>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <!--[if BLOCK]><![endif]-->                                    
                                    <a href="http://localhost/admin/drivers/9"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                        title="Voir">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:eye"
    data-inline="false"></span>                                    </a>
                                    <a href="http://localhost/admin/drivers/9/edit"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group"
                                        title="Modifier">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:edit-3"
    data-inline="false"></span>                                    </a>

                                    
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200 group">
                                            <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:more-vertical"
    data-inline="false"></span>                                        </button>

                                        <div x-show="open"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                            style="display: none;">
                                            <div class="py-1">
                                                <button wire:click="exportPdf(9); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-red-500"
    data-icon="lucide:file-text"
    data-inline="false"></span>                                                    Exporter PDF
                                                </button>
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <button wire:click="confirmArchive(9); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-amber-500"
    data-icon="lucide:archive"
    data-inline="false"></span>                                                    Archiver
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                        </tr>
                                                <tr wire:key="driver-8" class="hover:bg-gray-50 transition-colors duration-150 ">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" wire:click="toggleSelection(8)"  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                        <!--[if BLOCK]><![endif]-->                                        <img src="http://localhost/storage/drivers/photos/eJsJDLnrNN64p3HKluFn7atG1ryDM8Yf9M5l4tix.png" class="h-full w-full object-cover">
                                        <!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            El Hadi CHEMLI
                                        </div>
                                        <div class="text-xs text-gray-500">#DIF-2025-80000</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-900 flex items-center gap-1.5">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:phone"
    data-inline="false"></span> +213789050000
                                </div>
                                <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-1">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:mail"
    data-inline="false"></span> Echemli2025@gmail.com
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-gray-900">DZ-00000</div>
                                <!--[if BLOCK]><![endif]-->                                <div class="flex flex-wrap gap-1 mt-1">
                                    <!--[if BLOCK]><![endif]-->                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        B
                                    </span>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div wire:snapshot="{&quot;data&quot;:{&quot;driverId&quot;:8,&quot;driver&quot;:[null,{&quot;class&quot;:&quot;App\\Models\\Driver&quot;,&quot;key&quot;:8,&quot;s&quot;:&quot;mdl&quot;}],&quot;showConfirmModal&quot;:false,&quot;pendingStatus&quot;:null,&quot;pendingStatusEnum&quot;:null,&quot;confirmMessage&quot;:&quot;&quot;},&quot;memo&quot;:{&quot;id&quot;:&quot;cgQx8qgbL0AlOnbH0Meo&quot;,&quot;name&quot;:&quot;admin.driver-status-badge-ultra-pro&quot;,&quot;path&quot;:&quot;admin\/drivers&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;children&quot;:[],&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;errors&quot;:[],&quot;locale&quot;:&quot;fr&quot;},&quot;checksum&quot;:&quot;7f8111b5ac175dcd10b6778c6a03dc589ca418545ddf4fa4f536ee2d46405e03&quot;}" wire:effects="{&quot;listeners&quot;:[&quot;refreshComponent&quot;,&quot;driverStatusUpdated&quot;,&quot;driverStatusChanged&quot;,&quot;echo:drivers,DriverStatusChanged&quot;]}" wire:id="cgQx8qgbL0AlOnbH0Meo" class="relative inline-block"
    x-data="{ 
         open: false, 
         confirmModal: window.Livewire.find('cgQx8qgbL0AlOnbH0Meo').entangle('showConfirmModal').live,
         toggle() { this.open = !this.open; },
         close() { this.open = false; },
         selectStatus(status) { 
             this.open = false; 
             $wire.prepareStatusChange(status); 
         }
     }"
    @click.stop
    x-init="$watch('confirmModal', value => { if (value) open = false; })">
    
    <!--[if BLOCK]><![endif]-->    <button
        @click.stop="toggle"
        @click.away="close"
        type="button"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold 
                   transition-all duration-200 hover:shadow-md hover:scale-105 cursor-pointer 
                   inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        title="Cliquer pour modifier le statut">
        
        <!--[if BLOCK]><![endif]-->        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:user-check"
    data-inline="false"></span>        <!--[if ENDBLOCK]><![endif]-->

        
        <span>Disponible</span>

        
        <svg class="w-3 h-3 opacity-60 transition-transform duration-200"
            :class="{ 'rotate-180': open }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]-->    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
        class="absolute left-0 mt-3 w-72 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] bg-white ring-1 ring-black/5 z-50 overflow-visible"
        style="display: none;">

        
        <div class="absolute -top-2 left-6 w-4 h-4 bg-white transform rotate-45 border-t border-l border-gray-100 shadow-sm"></div>

        <div class="relative bg-white rounded-xl overflow-hidden">
            
            <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <span
    class="iconify block w-3.5 h-3.5 text-blue-500"
    data-icon="lucide:git-branch"
    data-inline="false"></span>                        Changer le statut
                    </span>
                    <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-full">
                        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:x"
    data-inline="false"></span>                    </button>
                </div>
            </div>

            
            <div class="py-2 max-h-[300px] overflow-y-auto custom-scrollbar">
                <!--[if BLOCK]><![endif]-->                <button
                    @click.stop="selectStatus('en_mission')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:truck"
    data-inline="false"></span>                            En mission
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                                <button
                    @click.stop="selectStatus('en_conge')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:palmtree"
    data-inline="false"></span>                            En congé
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                                <button
                    @click.stop="selectStatus('en_formation')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:graduation-cap"
    data-inline="false"></span>                            En formation
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                                <button
                    @click.stop="selectStatus('autre')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:help-circle"
    data-inline="false"></span>                            Autre
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                <!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <!--[if BLOCK]><![endif]-->            <div class="px-4 py-3 bg-gray-50/80 border-t border-gray-100 backdrop-blur-sm">
                <div class="flex items-start gap-2.5">
                    <span
    class="iconify block w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0"
    data-icon="lucide:info"
    data-inline="false"></span>                    <p class="text-xs text-gray-600 leading-relaxed">
                        <span class="font-medium text-gray-900">Note:</span>
                        Chauffeur disponible, peut recevoir une affectation de véhicule
                    </p>
                </div>
            </div>
            <!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    <!--[if ENDBLOCK]><![endif]-->

    
    <div x-show="confirmModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">

        
        <div x-show="confirmModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity">
        </div>

        
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="confirmModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:alert-circle"
    data-inline="false"></span>                            Confirmation de changement de statut
                        </h3>
                        <button @click="confirmModal = false; $wire.cancelStatusChange()"
                            class="text-white/80 hover:text-white transition-colors">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:x"
    data-inline="false"></span>                        </button>
                    </div>
                </div>

                
                <div class="px-6 py-5">
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 whitespace-pre-line"></p>
                    </div>

                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Chauffeur:</span>
                                <p class="font-medium text-gray-900">El Hadi CHEMLI</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Matricule:</span>
                                <p class="font-medium text-gray-900">DIF-2025-80000</p>
                            </div>
                            <!--[if BLOCK]><![endif]-->                            <div>
                                <span class="text-gray-500">Statut actuel:</span>
                                <p class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span
    class="iconify block w-3 h-3"
    data-icon="lucide:user-check"
    data-inline="false"></span>                                        Disponible
                                    </span>
                                </p>
                            </div>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>

                
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                    <button wire:click="cancelStatusChange"
                        @click="confirmModal = false"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 
                                   rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 
                                   transition-all duration-150">
                        <span
    class="iconify block w-4 h-4"
    data-icon="lucide:x"
    data-inline="false"></span>                        Annuler
                    </button>

                    <button wire:click="confirmStatusChange"
                        type="button"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                   text-white rounded-lg text-sm font-medium 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                   transition-all duration-150 shadow-sm hover:shadow-md">
                        <span wire:loading.remove wire:target="confirmStatusChange">
                            <span
    class="iconify block w-4 h-4"
    data-icon="lucide:check"
    data-inline="false"></span>                        </span>
                        <span wire:loading wire:target="confirmStatusChange">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="confirmStatusChange">Confirmer</span>
                        <span wire:loading wire:target="confirmStatusChange">Traitement...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                <!--[if BLOCK]><![endif]-->                                <span class="text-gray-400 italic">Aucun véhicule</span>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <!--[if BLOCK]><![endif]-->                                    
                                    <a href="http://localhost/admin/drivers/8"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                        title="Voir">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:eye"
    data-inline="false"></span>                                    </a>
                                    <a href="http://localhost/admin/drivers/8/edit"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group"
                                        title="Modifier">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:edit-3"
    data-inline="false"></span>                                    </a>

                                    
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200 group">
                                            <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:more-vertical"
    data-inline="false"></span>                                        </button>

                                        <div x-show="open"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                            style="display: none;">
                                            <div class="py-1">
                                                <button wire:click="exportPdf(8); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-red-500"
    data-icon="lucide:file-text"
    data-inline="false"></span>                                                    Exporter PDF
                                                </button>
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <button wire:click="confirmArchive(8); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-amber-500"
    data-icon="lucide:archive"
    data-inline="false"></span>                                                    Archiver
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                        </tr>
                                                <tr wire:key="driver-6" class="hover:bg-gray-50 transition-colors duration-150 ">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" wire:click="toggleSelection(6)"  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                        <!--[if BLOCK]><![endif]-->                                        <img src="http://localhost/storage/drivers/photos/n2CrsympJ0sAh2wR8EParyAaWdSEgBx4cKa1kfPv.png" class="h-full w-full object-cover">
                                        <!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            zerrouk ALIOUANE
                                        </div>
                                        <div class="text-xs text-gray-500">#DLS-84745</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-900 flex items-center gap-1.5">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:phone"
    data-inline="false"></span> +213684849603
                                </div>
                                <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-1">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:mail"
    data-inline="false"></span> zaliouane@yahoo.fr
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-gray-900">987-DZ-867</div>
                                <!--[if BLOCK]><![endif]-->                                <div class="flex flex-wrap gap-1 mt-1">
                                    <!--[if BLOCK]><![endif]-->                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        B
                                    </span>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div wire:snapshot="{&quot;data&quot;:{&quot;driverId&quot;:6,&quot;driver&quot;:[null,{&quot;class&quot;:&quot;App\\Models\\Driver&quot;,&quot;key&quot;:6,&quot;s&quot;:&quot;mdl&quot;}],&quot;showConfirmModal&quot;:false,&quot;pendingStatus&quot;:null,&quot;pendingStatusEnum&quot;:null,&quot;confirmMessage&quot;:&quot;&quot;},&quot;memo&quot;:{&quot;id&quot;:&quot;xjOlSSaCesAiVMbAVGGe&quot;,&quot;name&quot;:&quot;admin.driver-status-badge-ultra-pro&quot;,&quot;path&quot;:&quot;admin\/drivers&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;children&quot;:[],&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;errors&quot;:[],&quot;locale&quot;:&quot;fr&quot;},&quot;checksum&quot;:&quot;f41cf5cb972fc4ec1534da08a46958648033ba22442a1e4becfecc8870e8cd4b&quot;}" wire:effects="{&quot;listeners&quot;:[&quot;refreshComponent&quot;,&quot;driverStatusUpdated&quot;,&quot;driverStatusChanged&quot;,&quot;echo:drivers,DriverStatusChanged&quot;]}" wire:id="xjOlSSaCesAiVMbAVGGe" class="relative inline-block"
    x-data="{ 
         open: false, 
         confirmModal: window.Livewire.find('xjOlSSaCesAiVMbAVGGe').entangle('showConfirmModal').live,
         toggle() { this.open = !this.open; },
         close() { this.open = false; },
         selectStatus(status) { 
             this.open = false; 
             $wire.prepareStatusChange(status); 
         }
     }"
    @click.stop
    x-init="$watch('confirmModal', value => { if (value) open = false; })">
    
    <!--[if BLOCK]><![endif]-->    <button
        @click.stop="toggle"
        @click.away="close"
        type="button"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold 
                   transition-all duration-200 hover:shadow-md hover:scale-105 cursor-pointer 
                   inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        title="Cliquer pour modifier le statut">
        
        <!--[if BLOCK]><![endif]-->        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:user-check"
    data-inline="false"></span>        <!--[if ENDBLOCK]><![endif]-->

        
        <span>Disponible</span>

        
        <svg class="w-3 h-3 opacity-60 transition-transform duration-200"
            :class="{ 'rotate-180': open }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]-->    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
        class="absolute left-0 mt-3 w-72 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] bg-white ring-1 ring-black/5 z-50 overflow-visible"
        style="display: none;">

        
        <div class="absolute -top-2 left-6 w-4 h-4 bg-white transform rotate-45 border-t border-l border-gray-100 shadow-sm"></div>

        <div class="relative bg-white rounded-xl overflow-hidden">
            
            <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <span
    class="iconify block w-3.5 h-3.5 text-blue-500"
    data-icon="lucide:git-branch"
    data-inline="false"></span>                        Changer le statut
                    </span>
                    <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-full">
                        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:x"
    data-inline="false"></span>                    </button>
                </div>
            </div>

            
            <div class="py-2 max-h-[300px] overflow-y-auto custom-scrollbar">
                <!--[if BLOCK]><![endif]-->                <button
                    @click.stop="selectStatus('en_mission')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:truck"
    data-inline="false"></span>                            En mission
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                                <button
                    @click.stop="selectStatus('en_conge')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:palmtree"
    data-inline="false"></span>                            En congé
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                                <button
                    @click.stop="selectStatus('en_formation')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:graduation-cap"
    data-inline="false"></span>                            En formation
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                                <button
                    @click.stop="selectStatus('autre')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:help-circle"
    data-inline="false"></span>                            Autre
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                <!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <!--[if BLOCK]><![endif]-->            <div class="px-4 py-3 bg-gray-50/80 border-t border-gray-100 backdrop-blur-sm">
                <div class="flex items-start gap-2.5">
                    <span
    class="iconify block w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0"
    data-icon="lucide:info"
    data-inline="false"></span>                    <p class="text-xs text-gray-600 leading-relaxed">
                        <span class="font-medium text-gray-900">Note:</span>
                        Chauffeur disponible, peut recevoir une affectation de véhicule
                    </p>
                </div>
            </div>
            <!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    <!--[if ENDBLOCK]><![endif]-->

    
    <div x-show="confirmModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">

        
        <div x-show="confirmModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity">
        </div>

        
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="confirmModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:alert-circle"
    data-inline="false"></span>                            Confirmation de changement de statut
                        </h3>
                        <button @click="confirmModal = false; $wire.cancelStatusChange()"
                            class="text-white/80 hover:text-white transition-colors">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:x"
    data-inline="false"></span>                        </button>
                    </div>
                </div>

                
                <div class="px-6 py-5">
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 whitespace-pre-line"></p>
                    </div>

                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Chauffeur:</span>
                                <p class="font-medium text-gray-900">zerrouk ALIOUANE</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Matricule:</span>
                                <p class="font-medium text-gray-900">DLS-84745</p>
                            </div>
                            <!--[if BLOCK]><![endif]-->                            <div>
                                <span class="text-gray-500">Statut actuel:</span>
                                <p class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span
    class="iconify block w-3 h-3"
    data-icon="lucide:user-check"
    data-inline="false"></span>                                        Disponible
                                    </span>
                                </p>
                            </div>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>

                
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                    <button wire:click="cancelStatusChange"
                        @click="confirmModal = false"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 
                                   rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 
                                   transition-all duration-150">
                        <span
    class="iconify block w-4 h-4"
    data-icon="lucide:x"
    data-inline="false"></span>                        Annuler
                    </button>

                    <button wire:click="confirmStatusChange"
                        type="button"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                   text-white rounded-lg text-sm font-medium 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                   transition-all duration-150 shadow-sm hover:shadow-md">
                        <span wire:loading.remove wire:target="confirmStatusChange">
                            <span
    class="iconify block w-4 h-4"
    data-icon="lucide:check"
    data-inline="false"></span>                        </span>
                        <span wire:loading wire:target="confirmStatusChange">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="confirmStatusChange">Confirmer</span>
                        <span wire:loading wire:target="confirmStatusChange">Traitement...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                <!--[if BLOCK]><![endif]-->                                <span class="text-gray-400 italic">Aucun véhicule</span>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <!--[if BLOCK]><![endif]-->                                    
                                    <a href="http://localhost/admin/drivers/6"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                        title="Voir">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:eye"
    data-inline="false"></span>                                    </a>
                                    <a href="http://localhost/admin/drivers/6/edit"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group"
                                        title="Modifier">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:edit-3"
    data-inline="false"></span>                                    </a>

                                    
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200 group">
                                            <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:more-vertical"
    data-inline="false"></span>                                        </button>

                                        <div x-show="open"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                            style="display: none;">
                                            <div class="py-1">
                                                <button wire:click="exportPdf(6); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-red-500"
    data-icon="lucide:file-text"
    data-inline="false"></span>                                                    Exporter PDF
                                                </button>
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <button wire:click="confirmArchive(6); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-amber-500"
    data-icon="lucide:archive"
    data-inline="false"></span>                                                    Archiver
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                        </tr>
                                                <tr wire:key="driver-4" class="hover:bg-gray-50 transition-colors duration-150 ">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" wire:click="toggleSelection(4)"  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                        <!--[if BLOCK]><![endif]-->                                        <div class="h-10 w-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-semibold text-blue-700">
                                                TV
                                            </span>
                                        </div>
                                        <!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            TestRole Verification
                                        </div>
                                        <div class="text-xs text-gray-500">#</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-900 flex items-center gap-1.5">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:phone"
    data-inline="false"></span> +213778025640
                                </div>
                                <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-1">
                                    <span
    class="iconify block w-3.5 h-3.5 text-gray-400"
    data-icon="lucide:mail"
    data-inline="false"></span> test001@dontexist.dz
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-gray-900">8763787BIUYE</div>
                                <!--[if BLOCK]><![endif]-->                                <div class="flex flex-wrap gap-1 mt-1">
                                    <!--[if BLOCK]><![endif]-->                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        B
                                    </span>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div wire:snapshot="{&quot;data&quot;:{&quot;driverId&quot;:4,&quot;driver&quot;:[null,{&quot;class&quot;:&quot;App\\Models\\Driver&quot;,&quot;key&quot;:4,&quot;s&quot;:&quot;mdl&quot;}],&quot;showConfirmModal&quot;:false,&quot;pendingStatus&quot;:null,&quot;pendingStatusEnum&quot;:null,&quot;confirmMessage&quot;:&quot;&quot;},&quot;memo&quot;:{&quot;id&quot;:&quot;qcotP9zTrPYy34s7SFSH&quot;,&quot;name&quot;:&quot;admin.driver-status-badge-ultra-pro&quot;,&quot;path&quot;:&quot;admin\/drivers&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;children&quot;:[],&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;errors&quot;:[],&quot;locale&quot;:&quot;fr&quot;},&quot;checksum&quot;:&quot;025d49306514b50f32876f9e3a39c0255800780414221d4ee70258f4990246e5&quot;}" wire:effects="{&quot;listeners&quot;:[&quot;refreshComponent&quot;,&quot;driverStatusUpdated&quot;,&quot;driverStatusChanged&quot;,&quot;echo:drivers,DriverStatusChanged&quot;]}" wire:id="qcotP9zTrPYy34s7SFSH" class="relative inline-block"
    x-data="{ 
         open: false, 
         confirmModal: window.Livewire.find('qcotP9zTrPYy34s7SFSH').entangle('showConfirmModal').live,
         toggle() { this.open = !this.open; },
         close() { this.open = false; },
         selectStatus(status) { 
             this.open = false; 
             $wire.prepareStatusChange(status); 
         }
     }"
    @click.stop
    x-init="$watch('confirmModal', value => { if (value) open = false; })">
    
    <!--[if BLOCK]><![endif]-->    <button
        @click.stop="toggle"
        @click.away="close"
        type="button"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold 
                   transition-all duration-200 hover:shadow-md hover:scale-105 cursor-pointer 
                   inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        title="Cliquer pour modifier le statut">
        
        <!--[if BLOCK]><![endif]-->        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:user-check"
    data-inline="false"></span>        <!--[if ENDBLOCK]><![endif]-->

        
        <span>Disponible</span>

        
        <svg class="w-3 h-3 opacity-60 transition-transform duration-200"
            :class="{ 'rotate-180': open }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]-->    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
        class="absolute left-0 mt-3 w-72 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] bg-white ring-1 ring-black/5 z-50 overflow-visible"
        style="display: none;">

        
        <div class="absolute -top-2 left-6 w-4 h-4 bg-white transform rotate-45 border-t border-l border-gray-100 shadow-sm"></div>

        <div class="relative bg-white rounded-xl overflow-hidden">
            
            <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <span
    class="iconify block w-3.5 h-3.5 text-blue-500"
    data-icon="lucide:git-branch"
    data-inline="false"></span>                        Changer le statut
                    </span>
                    <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-full">
                        <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:x"
    data-inline="false"></span>                    </button>
                </div>
            </div>

            
            <div class="py-2 max-h-[300px] overflow-y-auto custom-scrollbar">
                <!--[if BLOCK]><![endif]-->                <button
                    @click.stop="selectStatus('en_mission')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:truck"
    data-inline="false"></span>                            En mission
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                                <button
                    @click.stop="selectStatus('en_conge')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:palmtree"
    data-inline="false"></span>                            En congé
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                                <button
                    @click.stop="selectStatus('en_formation')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:graduation-cap"
    data-inline="false"></span>                            En formation
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                                <button
                    @click.stop="selectStatus('autre')"
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center gap-3">
                        
                        <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 shadow-sm group-hover:shadow transition-all">
                            <span
    class="iconify block w-3.5 h-3.5"
    data-icon="lucide:help-circle"
    data-inline="false"></span>                            Autre
                        </span>
                    </div>
                    
                    <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                        <span
    class="iconify block w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all"
    data-icon="lucide:arrow-right"
    data-inline="false"></span>                    </div>
                </button>
                <!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <!--[if BLOCK]><![endif]-->            <div class="px-4 py-3 bg-gray-50/80 border-t border-gray-100 backdrop-blur-sm">
                <div class="flex items-start gap-2.5">
                    <span
    class="iconify block w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0"
    data-icon="lucide:info"
    data-inline="false"></span>                    <p class="text-xs text-gray-600 leading-relaxed">
                        <span class="font-medium text-gray-900">Note:</span>
                        Chauffeur disponible, peut recevoir une affectation de véhicule
                    </p>
                </div>
            </div>
            <!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    <!--[if ENDBLOCK]><![endif]-->

    
    <div x-show="confirmModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">

        
        <div x-show="confirmModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity">
        </div>

        
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="confirmModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:alert-circle"
    data-inline="false"></span>                            Confirmation de changement de statut
                        </h3>
                        <button @click="confirmModal = false; $wire.cancelStatusChange()"
                            class="text-white/80 hover:text-white transition-colors">
                            <span
    class="iconify block w-5 h-5"
    data-icon="lucide:x"
    data-inline="false"></span>                        </button>
                    </div>
                </div>

                
                <div class="px-6 py-5">
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 whitespace-pre-line"></p>
                    </div>

                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Chauffeur:</span>
                                <p class="font-medium text-gray-900">TestRole Verification</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Matricule:</span>
                                <p class="font-medium text-gray-900">N/A</p>
                            </div>
                            <!--[if BLOCK]><![endif]-->                            <div>
                                <span class="text-gray-500">Statut actuel:</span>
                                <p class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span
    class="iconify block w-3 h-3"
    data-icon="lucide:user-check"
    data-inline="false"></span>                                        Disponible
                                    </span>
                                </p>
                            </div>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>

                
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                    <button wire:click="cancelStatusChange"
                        @click="confirmModal = false"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 
                                   rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 
                                   transition-all duration-150">
                        <span
    class="iconify block w-4 h-4"
    data-icon="lucide:x"
    data-inline="false"></span>                        Annuler
                    </button>

                    <button wire:click="confirmStatusChange"
                        type="button"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                   text-white rounded-lg text-sm font-medium 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                   transition-all duration-150 shadow-sm hover:shadow-md">
                        <span wire:loading.remove wire:target="confirmStatusChange">
                            <span
    class="iconify block w-4 h-4"
    data-icon="lucide:check"
    data-inline="false"></span>                        </span>
                        <span wire:loading wire:target="confirmStatusChange">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="confirmStatusChange">Confirmer</span>
                        <span wire:loading wire:target="confirmStatusChange">Traitement...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                <!--[if BLOCK]><![endif]-->                                <span class="text-gray-400 italic">Aucun véhicule</span>
                                <!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <!--[if BLOCK]><![endif]-->                                    
                                    <a href="http://localhost/admin/drivers/4"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                        title="Voir">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:eye"
    data-inline="false"></span>                                    </a>
                                    <a href="http://localhost/admin/drivers/4/edit"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group"
                                        title="Modifier">
                                        <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:edit-3"
    data-inline="false"></span>                                    </a>

                                    
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200 group">
                                            <span
    class="iconify block w-4 h-4 group-hover:scale-110 transition-transform"
    data-icon="lucide:more-vertical"
    data-inline="false"></span>                                        </button>

                                        <div x-show="open"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                            style="display: none;">
                                            <div class="py-1">
                                                <button wire:click="exportPdf(4); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-red-500"
    data-icon="lucide:file-text"
    data-inline="false"></span>                                                    Exporter PDF
                                                </button>
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <button wire:click="confirmArchive(4); open = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <span
    class="iconify block mr-3 h-4 w-4 text-gray-400 group-hover:text-amber-500"
    data-icon="lucide:archive"
    data-inline="false"></span>                                                    Archiver
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                        </tr>
                        <!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="mt-4">
            <div class="pagination-footer mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3" style="margin-top: 1rem; display: flex; flex-direction: row; align-items: center; justify-content: space-between; gap: 0.75rem;">
    <!-- Left: Nombre d'éléments par page -->
    <div class="flex items-center text-sm text-gray-700">
        <span class="mr-2">Afficher</span>
        <select
            class="block rounded-md border-0 py-1.5 pl-3 pr-8 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm"
            wire:model.live="perPage">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <span class="ml-2">par page</span>
    </div>

    <!-- Right: Pagination -->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
</div>        </div>
    </div>

    

    
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

</div>
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
    <script data-navigate-once="true">window.livewireScriptConfig = {"csrf":"7X6no3HDKh3OvayVp3JDKdYNzndkkqqGFdLilGSU","uri":"\/livewire\/update","progressBar":"","nonce":""};</script>
</body>

</html>