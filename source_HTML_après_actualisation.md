
<!DOCTYPE html>
<html lang="fr" class="h-full bg-zinc-50">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="csrf-token" content="RbSUakAcZaawqjidDEzXkXoyiZXdH1kSyrqfkDXc">
  <meta name="user-data" content="{&quot;id&quot;:4,&quot;name&quot;:&quot;admin zenfleet&quot;,&quot;role&quot;:&quot;Admin&quot;}">
 
 <title>ZenFleet Admin - ZenFleet</title>

 <!-- Fonts -->
 <link rel="preconnect" href="https://fonts.bunny.net">
 <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

 <!-- Iconify CDN -->
 <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

 <!-- Font Awesome 6 -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

 
 
 
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">

 
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slim-select@2/dist/slimselect.css">

 
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/light.css">
 
 
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

 <link rel="preload" as="style" href="http://localhost/build/assets/app-6KyhQxfF.css" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/app-Pb0zrdAZ.js" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/vendor-common-B9ygI19o.js" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/ui-public-2hikc2V1.js" /><link rel="stylesheet" href="http://localhost/build/assets/app-6KyhQxfF.css" data-navigate-track="reload" /><script type="module" src="http://localhost/build/assets/app-Pb0zrdAZ.js" data-navigate-track="reload"></script> <!-- Livewire Styles --><style >[wire\:loading][wire\:loading], [wire\:loading\.delay][wire\:loading\.delay], [wire\:loading\.inline-block][wire\:loading\.inline-block], [wire\:loading\.inline][wire\:loading\.inline], [wire\:loading\.block][wire\:loading\.block], [wire\:loading\.flex][wire\:loading\.flex], [wire\:loading\.table][wire\:loading\.table], [wire\:loading\.grid][wire\:loading\.grid], [wire\:loading\.inline-flex][wire\:loading\.inline-flex] {display: none;}[wire\:loading\.delay\.none][wire\:loading\.delay\.none], [wire\:loading\.delay\.shortest][wire\:loading\.delay\.shortest], [wire\:loading\.delay\.shorter][wire\:loading\.delay\.shorter], [wire\:loading\.delay\.short][wire\:loading\.delay\.short], [wire\:loading\.delay\.default][wire\:loading\.delay\.default], [wire\:loading\.delay\.long][wire\:loading\.delay\.long], [wire\:loading\.delay\.longer][wire\:loading\.delay\.longer], [wire\:loading\.delay\.longest][wire\:loading\.delay\.longest] {display: none;}[wire\:offline][wire\:offline] {display: none;}[wire\:dirty]:not(textarea):not(input):not(select) {display: none;}:root {--livewire-progress-bar-color: #2299dd;}[x-cloak] {display: none !important;}[wire\:cloak] {display: none !important;}</style>
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
 data-inline="false"
></span>
 </div>
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
 data-inline="false"
></span>
 <span class="flex-1">Dashboard</span>
 </a>
 </li>

 
 
 
  <li class="flex flex-col" x-data="{ open: true }">
 <button @click="open = !open"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-blue-600 text-white shadow-md">
 <span
 class="iconify block w-5 h-5 mr-3 text-white"
 data-icon="mdi:car-multiple"
 data-inline="false"
></span>
 <span class="flex-1 text-left">Véhicules</span>
 <span
 class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
 data-icon="heroicons:chevron-down"
 data-inline="false"
></span>
 </button>
 <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
 <div class="flex w-full mt-2 pl-3">
 <div class="mr-1">
 <div class="px-1 py-2 h-full relative">
 <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
  <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
 x-bind:style="`height: 50%; top: 0%;`"></div>
 </div>
 </div>
 <div class="flex-1 min-w-0 space-y-1.5">
  <a href="http://localhost/admin/vehicles"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 bg-blue-100 text-blue-700">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-blue-600"
 data-icon="mdi:format-list-bulleted"
 data-inline="false"
></span>
 Gestion Véhicules
 </a>
   <a href="http://localhost/admin/assignments"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="mdi:clipboard-text"
 data-inline="false"
></span>
 Affectations
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
 data-inline="false"
></span>
 <span class="flex-1 text-left">Chauffeurs</span>
 <span
 class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
 data-icon="heroicons:chevron-down"
 data-inline="false"
></span>
 </button>
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
 data-inline="false"
></span>
 Liste
 </a>
   <a href="http://localhost/admin/drivers/sanctions"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="mdi:gavel"
 data-inline="false"
></span>
 Sanctions
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
 data-inline="false"
></span>
 <span class="flex-1">Dépôts</span>
 </a>
 </li>
 
 
 
 
  <li class="flex flex-col" x-data="{ open: false }">
 <button @click="open = !open"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="mdi:speedometer"
 data-inline="false"
></span>
 <span class="flex-1 text-left">Kilométrage</span>
 <span
 class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
 data-icon="heroicons:chevron-down"
 data-inline="false"
></span>
 </button>
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
 data-inline="false"
></span>
 Historique
 </a>
 </li>
 
  <li>
  <a href="http://localhost/admin/mileage-readings/update"
 class="flex items-center h-9 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-5 h-5 mr-2 text-gray-600"
 data-icon="mdi:pencil"
 data-inline="false"
></span>
 Mettre à jour
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
 data-inline="false"
></span>
 <span class="flex-1 text-left">Maintenance</span>
 <span
 class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
 data-icon="lucide:chevron-down"
 data-inline="false"
></span>
 </button>
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
 data-inline="false"
></span>
 Vue d'ensemble
 </a>

 
 <a href="http://localhost/admin/maintenance/operations"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:list"
 data-inline="false"
></span>
 Opérations
 </a>

 
 <a href="http://localhost/admin/maintenance/operations/kanban"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:columns-3"
 data-inline="false"
></span>
 Kanban
 </a>

 
 <a href="http://localhost/admin/maintenance/operations/calendar"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:calendar-days"
 data-inline="false"
></span>
 Calendrier
 </a>

 
 <a href="http://localhost/admin/maintenance/schedules"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:repeat"
 data-inline="false"
></span>
 Planifications
 </a>

 
  <a href="http://localhost/admin/repair-requests"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:hammer"
 data-inline="false"
></span>
 Demandes Réparation
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
 data-inline="false"
></span>
 <span class="flex-1">Alertes</span>
 </a>
 </li>
 
 
  <li class="flex">
 <a href="http://localhost/admin/documents"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="mdi:file-document"
 data-inline="false"
></span>
 <span class="flex-1">Documents</span>
 </a>
 </li>
 
 
  <li class="flex">
 <a href="http://localhost/admin/suppliers"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="mdi:store"
 data-inline="false"
></span>
 <span class="flex-1">Fournisseurs</span>
 </a>
 </li>
 
 
  <li class="flex flex-col" x-data="{ open: false }">
 <button @click="open = !open"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="solar:wallet-money-bold"
 data-inline="false"
></span>
 <span class="flex-1 text-left">Dépenses</span>
   <span
 class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
 data-icon="lucide:chevron-down"
 data-inline="false"
></span>
 </button>
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
 data-inline="false"
></span>
 Tableau de bord
 </a>

 
  <a href="http://localhost/admin/vehicle-expenses/create"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:plus-circle"
 data-inline="false"
></span>
 Nouvelle dépense
 </a>
 
 
  <a href="http://localhost/admin/vehicle-expenses/dashboard"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:chart-line"
 data-inline="false"
></span>
 Analytics
 </a>
 
 
  <a href="http://localhost/admin/vehicle-expenses?filter=pending_approval"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:clock"
 data-inline="false"
></span>
 Approbations
  </a>
 
 
 <a href="http://localhost/admin/vehicle-expenses?section=groups"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:wallet"
 data-inline="false"
></span>
 Budgets
 </a>

 
  <a href="http://localhost/admin/vehicle-expenses/export"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:download"
 data-inline="false"
></span>
 Export
 </a>
 
 
  <a href="http://localhost/admin/vehicle-expenses/analytics/cost-trends"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="lucide:trending-up"
 data-inline="false"
></span>
 TCO & Tendances
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
 data-inline="false"
></span>
 <span class="flex-1">Rapports</span>
 </a>
 </li>
 
 
  <li class="flex flex-col" x-data="{ open: false }">
 <button @click="open = !open"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="mdi:cog"
 data-inline="false"
></span>
 <span class="flex-1 text-left">Administration</span>
 <span
 class="iconify block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !open }"
 data-icon="heroicons:chevron-down"
 data-inline="false"
></span>
 </button>
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
 data-inline="false"
></span>
 Utilisateurs
 </a>
   <a href="http://localhost/admin/roles"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="mdi:shield-check"
 data-inline="false"
></span>
 Rôles & Permissions
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
 data-inline="false"
></span>
 <span class="text-zinc-900 text-xl font-bold">ZenFleet</span>
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
 data-inline="false"
></span>
 Dashboard
 </a>
 </li>

 
 
 
  <li x-data="{ open: true }">
 <button @click="open = !open"
 class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold bg-zinc-950 text-white">
 <span
 class="iconify block h-5 w-5 shrink-0"
 data-icon="heroicons:truck"
 data-inline="false"
></span>
 <span class="flex-1 text-left">Véhicules</span>
 <span
 class="iconify block h-4 w-4 transition-transform" :class="{ 'rotate-90': open }"
 data-icon="heroicons:chevron-right"
 data-inline="false"
></span>
 </button>
 <div x-show="open" x-transition class="mt-1">
 <ul class="ml-6 space-y-1">
 <li class="relative">
 <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
 <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
 <a href="http://localhost/admin/vehicles"
 class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium bg-zinc-100 text-zinc-900">
 <span
 class="iconify block h-4 w-4 shrink-0"
 data-icon="heroicons:truck"
 data-inline="false"
></span>
 Gestion Véhicules
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
 data-inline="false"
></span>
 Affectations
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
 data-inline="false"
></span>
 Chauffeurs
 </a>
 </li>
 
 
  <li>
 <a href="http://localhost/admin/depots"
 class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
 <span
 class="iconify block h-5 w-5 shrink-0"
 data-icon="mdi:office-building"
 data-inline="false"
></span>
 Dépôts
 </a>
 </li>
 
 
  <li x-data="{ open: false }">
 <button @click="open = !open"
 class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
 <span
 class="iconify block h-5 w-5 shrink-0"
 data-icon="mdi:cog"
 data-inline="false"
></span>
 <span class="flex-1 text-left">Administration</span>
 <span
 class="iconify block h-4 w-4 transition-transform" :class="{ 'rotate-90': open }"
 data-icon="heroicons:chevron-right"
 data-inline="false"
></span>
 </button>
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
 data-inline="false"
></span>
 Utilisateurs
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
 data-inline="false"
></span>
 Rôles & Permissions
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
 data-inline="false"
></span>
 </button>
 <div class="flex-1 text-sm font-semibold leading-6 text-zinc-900">ZenFleet</div>
 <div class="h-8 w-8 bg-zinc-100 rounded-full flex items-center justify-center">
 <span
 class="iconify block h-4 w-4 text-zinc-500"
 data-icon="heroicons:user"
 data-inline="false"
></span>
 </div>
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
 data-inline="false"
></span>
 </div>
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
 data-inline="false"
></span>
 <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
 </button>
 </div>

 
 <div class="relative">
 <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600 relative">
 <span class="sr-only">Messages</span>
 <span
 class="iconify block h-6 w-6"
 data-icon="heroicons:envelope"
 data-inline="false"
></span>
 <span class="absolute -top-1 -right-1 h-4 w-4 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center">2</span>
 </button>
 </div>

 
 <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600">
 <span class="sr-only">Basculer le mode sombre</span>
 <span
 class="iconify block h-6 w-6"
 data-icon="heroicons:moon"
 data-inline="false"
></span>
 </button>

 
 <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-zinc-200" aria-hidden="true"></div>

 
 <div class="relative" x-data="{ open: false }">
 <button type="button" @click="open = !open" class="-m-1.5 flex items-center p-1.5 hover:bg-zinc-50 rounded-lg transition-colors">
 <span class="sr-only">Ouvrir le menu utilisateur</span>
 <div class="h-8 w-8 bg-gradient-to-br from-zinc-600 to-zinc-800 rounded-full flex items-center justify-center">
 <span
 class="iconify block text-white w-4 h-4"
 data-icon="heroicons:user"
 data-inline="false"
></span>
 </div>
 <span class="hidden lg:flex lg:items-center">
 <div class="ml-3 text-left">
 <div class="text-sm font-semibold leading-5 text-zinc-900">admin zenfleet</div>
 <div class="text-xs leading-4 text-zinc-500">Admin</div>
 </div>
 <span
 class="iconify block ml-2 h-4 w-4 text-zinc-500 transition-transform" :class="{ 'rotate-180': open }"
 data-icon="heroicons:chevron-down"
 data-inline="false"
></span>
 </span>
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
 data-inline="false"
></span>
 </div>
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
 data-inline="false"
></span>
 Mon Profil
 </a>
 <a href="#"
 class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
 <span
 class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"
 data-icon="mdi:cog"
 data-inline="false"
></span>
 Paramètres
 </a>
 <a href="#"
 class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
 <span
 class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"
 data-icon="heroicons:question-mark-circle"
 data-inline="false"
></span>
 Aide & Support
 </a>
 <div class="border-t border-zinc-100 my-1"></div>
 <form method="POST" action="http://localhost/logout">
 <input type="hidden" name="_token" value="RbSUakAcZaawqjidDEzXkXoyiZXdH1kSyrqfkDXc" autocomplete="off"> <button type="submit"
 class="group flex w-full items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
 <span
 class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"
 data-icon="heroicons:arrow-right-on-rectangle"
 data-inline="false"
></span>
 Se déconnecter
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
         <div wire:snapshot="{&quot;data&quot;:{&quot;search&quot;:&quot;&quot;,&quot;status_id&quot;:&quot;&quot;,&quot;vehicle_type_id&quot;:&quot;&quot;,&quot;fuel_type_id&quot;:&quot;&quot;,&quot;depot_id&quot;:&quot;&quot;,&quot;visibility&quot;:&quot;archived&quot;,&quot;per_page&quot;:20,&quot;sortField&quot;:&quot;created_at&quot;,&quot;sortDirection&quot;:&quot;desc&quot;,&quot;selectedVehicles&quot;:[[],{&quot;s&quot;:&quot;arr&quot;}],&quot;selectAll&quot;:false,&quot;bulkDepotId&quot;:&quot;&quot;,&quot;bulkStatusId&quot;:null,&quot;showBulkDepotModal&quot;:false,&quot;showBulkStatusModal&quot;:false,&quot;showBulkArchiveModal&quot;:false,&quot;restoringVehicleId&quot;:null,&quot;showRestoreModal&quot;:false,&quot;forceDeletingVehicleId&quot;:null,&quot;showForceDeleteModal&quot;:false,&quot;archivingVehicleId&quot;:null,&quot;showArchiveModal&quot;:false,&quot;paginators&quot;:[{&quot;page&quot;:1},{&quot;s&quot;:&quot;arr&quot;}]},&quot;memo&quot;:{&quot;id&quot;:&quot;HwAw6MKtBXuCrYcTcmjQ&quot;,&quot;name&quot;:&quot;admin.vehicles.vehicle-index&quot;,&quot;path&quot;:&quot;admin\/vehicles&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;children&quot;:{&quot;status-52&quot;:[&quot;div&quot;,&quot;CSVWxDPL6hz94cEkbevS&quot;],&quot;status-9&quot;:[&quot;div&quot;,&quot;WrrcrDVVe6atalM4pfXZ&quot;]},&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;errors&quot;:[],&quot;locale&quot;:&quot;fr&quot;},&quot;checksum&quot;:&quot;232c1880206034d10fd789c2059903622b21457860818fe90a9e3de85cf6a645&quot;}" wire:effects="{&quot;url&quot;:{&quot;search&quot;:{&quot;as&quot;:&quot;search&quot;,&quot;use&quot;:&quot;push&quot;,&quot;alwaysShow&quot;:false,&quot;except&quot;:&quot;&quot;},&quot;status_id&quot;:{&quot;as&quot;:&quot;status_id&quot;,&quot;use&quot;:&quot;push&quot;,&quot;alwaysShow&quot;:false,&quot;except&quot;:&quot;&quot;},&quot;vehicle_type_id&quot;:{&quot;as&quot;:&quot;vehicle_type_id&quot;,&quot;use&quot;:&quot;push&quot;,&quot;alwaysShow&quot;:false,&quot;except&quot;:&quot;&quot;},&quot;fuel_type_id&quot;:{&quot;as&quot;:&quot;fuel_type_id&quot;,&quot;use&quot;:&quot;push&quot;,&quot;alwaysShow&quot;:false,&quot;except&quot;:&quot;&quot;},&quot;depot_id&quot;:{&quot;as&quot;:&quot;depot_id&quot;,&quot;use&quot;:&quot;push&quot;,&quot;alwaysShow&quot;:false,&quot;except&quot;:&quot;&quot;},&quot;visibility&quot;:{&quot;as&quot;:&quot;visibility&quot;,&quot;use&quot;:&quot;push&quot;,&quot;alwaysShow&quot;:false,&quot;except&quot;:&quot;active&quot;},&quot;sortField&quot;:{&quot;as&quot;:&quot;sortField&quot;,&quot;use&quot;:&quot;push&quot;,&quot;alwaysShow&quot;:false,&quot;except&quot;:&quot;created_at&quot;},&quot;sortDirection&quot;:{&quot;as&quot;:&quot;sortDirection&quot;,&quot;use&quot;:&quot;push&quot;,&quot;alwaysShow&quot;:false,&quot;except&quot;:&quot;desc&quot;},&quot;paginators.page&quot;:{&quot;as&quot;:&quot;page&quot;,&quot;use&quot;:&quot;push&quot;,&quot;alwaysShow&quot;:false,&quot;except&quot;:null}}}" wire:id="HwAw6MKtBXuCrYcTcmjQ" class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <span
 class="iconify block w-6 h-6 text-blue-600"
 data-icon="lucide:car"
 data-inline="false"
></span>
                Gestion des Véhicules
                <span class="ml-2 text-sm font-normal text-gray-500">
                    (2)
                </span>
            </h1>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Total véhicules</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">56</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span
 class="iconify block w-5 h-5 text-blue-600"
 data-icon="lucide:car"
 data-inline="false"
></span>
                    </div>
                </div>
            </div>
            
            
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Disponibles</p>
                        <p class="text-xl font-bold text-green-600 mt-1">0</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <span
 class="iconify block w-5 h-5 text-green-600"
 data-icon="lucide:check-circle-2"
 data-inline="false"
></span>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Affectés</p>
                        <p class="text-xl font-bold text-orange-600 mt-1">1</p>
                    </div>
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <span
 class="iconify block w-5 h-5 text-orange-600"
 data-icon="lucide:user-check"
 data-inline="false"
></span>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">En maintenance</p>
                        <p class="text-xl font-bold text-red-600 mt-1">0</p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <span
 class="iconify block w-5 h-5 text-red-600"
 data-icon="lucide:wrench"
 data-inline="false"
></span>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Archivés</p>
                        <p class="text-xl font-bold text-gray-500 mt-1">2</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <span
 class="iconify block w-5 h-5 text-gray-500"
 data-icon="lucide:archive"
 data-inline="false"
></span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="mb-6" x-data="{ showFilters: false }">
            <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">
                
                <div class="flex-1 w-full lg:w-auto">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span
 class="iconify block w-5 h-5 text-gray-400"
 data-icon="lucide:search"
 data-inline="false"
></span>
                        </div>
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="text"
                            placeholder="Rechercher par immatriculation, marque, modèle..."
                            class="pl-10 pr-4 py-2.5 block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm">
                    </div>
                </div>

                
                <button
                    @click="showFilters = !showFilters"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm">
                    <span
 class="iconify block w-5 h-5 text-gray-500"
 data-icon="lucide:filter"
 data-inline="false"
></span>
                    <span class="font-medium text-gray-700">Filtres</span>
                    <span
 class="iconify block w-4 h-4 text-gray-400" x-bind:class="showFilters ? 'rotate-180' : ''"
 data-icon="heroicons:chevron-down"
 data-inline="false"
></span>
                </button>

                
                <div class="flex items-center gap-2">
                    <!--[if BLOCK]><![endif]-->                        <button wire:click="$set('visibility', 'active')"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all shadow-sm">
                            <span
 class="iconify block w-5 h-5"
 data-icon="lucide:list"
 data-inline="false"
></span>
                            <span class="hidden lg:inline font-medium">Voir Actifs</span>
                        </button>
                    <!--[if ENDBLOCK]><![endif]-->

                                            <a href="http://localhost/admin/vehicles/create" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <span
 class="iconify block w-5 h-5"
 data-icon="lucide:plus-circle"
 data-inline="false"
></span>
                            <span class="hidden sm:inline">Nouveau véhicule</span>
                        </a>
                                    </div>
            </div>

            
            <div x-show="showFilters" x-collapse class="mt-4 bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dépôt</label>
                        <select wire:model.live="depot_id" class="block w-full border-gray-300 rounded-lg text-sm">
                            <option value="">Tous les dépôts</option>
                            <!--[if BLOCK]><![endif]-->                                <option value="17">ANNABA</option>
                                                            <option value="5">Auto-Généré</option>
                                                            <option value="14">CONSTANTINE</option>
                                                            <option value="16">Dépôt DG</option>
                                                            <option value="6">Dépôt Test Code NULL</option>
                                                            <option value="4">Dépôt Test Personnalisé</option>
                            <!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select wire:model.live="status_id" class="block w-full border-gray-300 rounded-lg text-sm">
                            <option value="">Tous les statuts</option>
                            <!--[if BLOCK]><![endif]-->                                <option value="9">Affecté</option>
                                                            <option value="2">En maintenance</option>
                                                            <option value="10">En panne</option>
                                                            <option value="8">Parking</option>
                                                            <option value="11">Réformé</option>
                            <!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select wire:model.live="vehicle_type_id" class="block w-full border-gray-300 rounded-lg text-sm">
                            <option value="">Tous les types</option>
                            <!--[if BLOCK]><![endif]-->                                <option value="11">Autre</option>
                                                            <option value="1">Berline</option>
                                                            <option value="4">Bus</option>
                                                            <option value="3">Camion</option>
                                                            <option value="7">Engin</option>
                                                            <option value="8">Fourgonnette</option>
                                                            <option value="6">Moto</option>
                                                            <option value="10">Semi-remorque</option>
                                                            <option value="2">Utilitaire</option>
                                                            <option value="9">VUL</option>
                                                            <option value="5">Voiture</option>
                            <!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Carburant</label>
                        <select wire:model.live="fuel_type_id" class="block w-full border-gray-300 rounded-lg text-sm">
                            <option value="">Tous les carburants</option>
                            <!--[if BLOCK]><![endif]-->                                <option value="2">Diesel</option>
                                                            <option value="1">Essence</option>
                                                            <option value="3">GPL</option>
                                                            <option value="4">Électrique</option>
                            <!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Par page</label>
                        <select wire:model.live="per_page" class="block w-full border-gray-300 rounded-lg text-sm">
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-0 mb-6">
 <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

 <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="relative px-3 py-2 w-12">
                                <input type="checkbox" wire:click="toggleAll"  class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </th>
                            <th wire:click="sortBy('registration_plate')" scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700">
                                Véhicule
                                <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th wire:click="sortBy('current_mileage')" scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700">
                                Kilométrage
                                <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dépôt</th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chauffeur</th>
                            <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!--[if BLOCK]><![endif]-->                            <tr wire:key="vehicle-52" class="hover:bg-gray-50 transition-colors duration-150 ">
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <input type="checkbox" wire:click="toggleSelection(52)"  class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-9 w-9">
                                            <div class="h-9 w-9 rounded-full bg-gray-100 flex items-center justify-center ring-1 ring-gray-200 shadow-sm">
                                                <span
 class="iconify block h-4 w-4 text-gray-600"
 data-icon="lucide:car"
 data-inline="false"
></span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-semibold text-gray-900">891024-16</div>
                                            <div class="text-xs text-gray-500">Peugeot Partner</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Berline
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="flex items-center text-sm">
                                        <span
 class="iconify block h-3.5 w-3.5 text-gray-400 mr-1.5"
 data-icon="lucide:gauge"
 data-inline="false"
></span>
                                        <span class="font-medium text-gray-900">173,448</span>
                                        <span class="text-gray-500 ml-1">km</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div wire:snapshot="{&quot;data&quot;:{&quot;vehicleId&quot;:52,&quot;vehicle&quot;:[null,{&quot;class&quot;:&quot;App\\Models\\Vehicle&quot;,&quot;key&quot;:52,&quot;s&quot;:&quot;mdl&quot;}],&quot;showDropdown&quot;:false,&quot;showConfirmModal&quot;:false,&quot;pendingStatus&quot;:null,&quot;pendingStatusEnum&quot;:null,&quot;confirmMessage&quot;:&quot;&quot;},&quot;memo&quot;:{&quot;id&quot;:&quot;CSVWxDPL6hz94cEkbevS&quot;,&quot;name&quot;:&quot;admin.vehicle-status-badge-ultra-pro&quot;,&quot;path&quot;:&quot;admin\/vehicles&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;children&quot;:[],&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;errors&quot;:[],&quot;locale&quot;:&quot;fr&quot;},&quot;checksum&quot;:&quot;aa95a93ecd0b66f8082840b2d40feaab52701ce8dbc9126a1277cc88cd4c2abc&quot;}" wire:effects="{&quot;listeners&quot;:[&quot;refreshComponent&quot;,&quot;vehicleStatusUpdated&quot;,&quot;vehicleStatusChanged&quot;,&quot;echo:vehicles,VehicleStatusChanged&quot;]}" wire:id="CSVWxDPL6hz94cEkbevS" class="relative inline-block" x-data="statusBadgeComponent()" wire:ignore.self>
    
    <!--[if BLOCK]><![endif]-->        <button
            wire:click="toggleDropdown"
            type="button"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold 
                   transition-all duration-200 hover:shadow-md hover:scale-105 cursor-pointer 
                   bg-blue-50 text-blue-700 ring-1 ring-blue-200
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            title="Cliquer pour modifier le statut">
            
            <!--[if BLOCK]><![endif]-->                <span
 class="iconify block w-3.5 h-3.5"
 data-icon="lucide:square-parking"
 data-inline="false"
></span>
            <!--[if ENDBLOCK]><![endif]-->
            
            
            <span>Parking</span>
            
            
            <svg class="w-3 h-3 opacity-60 transition-transform duration-200"
                 :class="{ 'rotate-180': open }"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    <!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]-->        <div
            x-show="open"
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute left-0 mt-2 w-64 rounded-xl shadow-2xl bg-white ring-1 ring-black ring-opacity-5 z-50 overflow-hidden"
            style="display: none;">

            
            <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                        Changer le statut
                    </span>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <span
 class="iconify block w-4 h-4"
 data-icon="lucide:x"
 data-inline="false"
></span>
                    </button>
                </div>
            </div>

            
            <div class="py-2 max-h-64 overflow-y-auto">
                <!--[if BLOCK]><![endif]-->                    <button
                        wire:click="prepareStatusChange('affecte')"
                        type="button"
                        class="w-full flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 
                               transition-all duration-150 group focus:outline-none focus:bg-gray-50">
                        <div class="flex items-center gap-3">
                            
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium 
                                       bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 group-hover:shadow-sm transition-all">
                                <span
 class="iconify block w-3 h-3"
 data-icon="lucide:user-check"
 data-inline="false"
></span>
                                Affecté
                            </span>
                        </div>
                        
                        <span
 class="iconify block w-4 h-4 text-gray-400 group-hover:text-blue-600 
                                         group-hover:translate-x-1 transition-all"
 data-icon="lucide:chevron-right"
 data-inline="false"
></span>
                    </button>
                                    <button
                        wire:click="prepareStatusChange('en_panne')"
                        type="button"
                        class="w-full flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 
                               transition-all duration-150 group focus:outline-none focus:bg-gray-50">
                        <div class="flex items-center gap-3">
                            
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium 
                                       bg-rose-50 text-rose-700 ring-1 ring-rose-200 group-hover:shadow-sm transition-all">
                                <span
 class="iconify block w-3 h-3"
 data-icon="lucide:alert-triangle"
 data-inline="false"
></span>
                                En panne
                            </span>
                        </div>
                        
                        <span
 class="iconify block w-4 h-4 text-gray-400 group-hover:text-blue-600 
                                         group-hover:translate-x-1 transition-all"
 data-icon="lucide:chevron-right"
 data-inline="false"
></span>
                    </button>
                <!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <!--[if BLOCK]><![endif]-->                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-start gap-2">
                        <span
 class="iconify block w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0"
 data-icon="lucide:info-circle"
 data-inline="false"
></span>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Véhicule disponible au parking, prêt pour affectation
                        </p>
                    </div>
                </div>
            <!--[if ENDBLOCK]><![endif]-->
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
 data-inline="false"
></span>
                            Confirmation de changement de statut
                        </h3>
                        <button @click="confirmModal = false; $wire.cancelStatusChange()"
                                class="text-white/80 hover:text-white transition-colors">
                            <span
 class="iconify block w-5 h-5"
 data-icon="lucide:x"
 data-inline="false"
></span>
                        </button>
                    </div>
                </div>

                
                <div class="px-6 py-5">
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 whitespace-pre-line"></p>
                    </div>

                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Véhicule:</span>
                                <p class="font-medium text-gray-900">Peugeot Partner</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Immatriculation:</span>
                                <p class="font-medium text-gray-900">891024-16</p>
                            </div>
                            <!--[if BLOCK]><![endif]-->                            <div>
                                <span class="text-gray-500">Statut actuel:</span>
                                <p class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-blue-200">
                                        <span
 class="iconify block w-3 h-3"
 data-icon="lucide:square-parking"
 data-inline="false"
></span>
                                        Parking
                                    </span>
                                </p>
                            </div>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    
                    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                </div>

                
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                    <button wire:click="cancelStatusChange"
                            type="button"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 
                                   rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 
                                   transition-all duration-150">
                        <span
 class="iconify block w-4 h-4"
 data-icon="lucide:x"
 data-inline="false"
></span>
                        Annuler
                    </button>
                    
                    <button wire:click="confirmStatusChange"
                            type="button"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="inline-flex items-center gap-2 px-4 py-2 
                                   bg-blue-600 hover:bg-blue-700 focus:ring-blue-500
                                   text-white rounded-lg text-sm font-medium 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 
                                   transition-all duration-150 shadow-sm hover:shadow-md">
                        <span wire:loading.remove wire:target="confirmStatusChange">
                            <span
 class="iconify block w-4 h-4"
 data-icon="lucide:check"
 data-inline="false"
></span>
                        </span>
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
</div>

                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <!--[if BLOCK]><![endif]-->                                        <div class="flex items-center gap-1.5">
                                            <span
 class="iconify block w-3.5 h-3.5 text-purple-600"
 data-icon="lucide:building-2"
 data-inline="false"
></span>
                                            <span class="text-sm text-gray-900">Auto-Généré</span>
                                        </div>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                                                        <!--[if BLOCK]><![endif]-->                                        <span class="text-xs text-gray-400 italic">Non affecté</span>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center gap-1">
                                        
                                                                                    <a href="http://localhost/admin/vehicles/52" 
                                               class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                               title="Voir">
                                                <span
 class="iconify block w-4 h-4"
 data-icon="lucide:eye"
 data-inline="false"
></span>
                                            </a>
                                                                                
                                                                                    <a href="http://localhost/admin/vehicles/52/edit" 
                                               class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-all duration-200"
                                               title="Modifier">
                                                <span
 class="iconify block w-4 h-4"
 data-icon="lucide:edit"
 data-inline="false"
></span>
                                            </a>
                                                                                
                                        
                                        <div class="relative inline-block text-left" x-data="{ open: false }">
                                            <button @click="open = !open"
                                                    @click.away="open = false"
                                                    type="button"
                                                    class="inline-flex items-center p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200"
                                                    title="Plus d'actions">
                                                <span
 class="iconify block w-4 h-4"
 data-icon="lucide:more-vertical"
 data-inline="false"
></span>
                                            </button>

                                            <div x-show="open"
                                                 x-cloak
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="transform opacity-0 scale-95"
                                                 x-transition:enter-end="transform opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-75"
                                                 x-transition:leave-start="transform opacity-100 scale-100"
                                                 x-transition:leave-end="transform opacity-0 scale-95"
                                                 class="absolute right-0 z-50 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                                                <div class="py-1">
                                                    <!--[if BLOCK]><![endif]-->                                                        
                                                        <button wire:click="confirmRestore(52)"
                                                                @click="open = false"
                                                                class="flex w-full items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                            <span
 class="iconify block w-4 h-4 mr-2 text-green-600"
 data-icon="lucide:rotate-ccw"
 data-inline="false"
></span>
                                                            Restaurer
                                                        </button>
                                                        
                                                        
                                                        <button wire:click="confirmForceDelete(52)"
                                                                @click="open = false"
                                                                class="flex w-full items-center px-3 py-2 text-sm text-gray-700 hover:bg-red-50 transition-colors">
                                                            <span
 class="iconify block w-4 h-4 mr-2 text-red-600"
 data-icon="lucide:trash-2"
 data-inline="false"
></span>
                                                            Supprimer
                                                        </button>
                                                    <!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                                                    <tr wire:key="vehicle-9" class="hover:bg-gray-50 transition-colors duration-150 ">
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <input type="checkbox" wire:click="toggleSelection(9)"  class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-9 w-9">
                                            <div class="h-9 w-9 rounded-full bg-gray-100 flex items-center justify-center ring-1 ring-gray-200 shadow-sm">
                                                <span
 class="iconify block h-4 w-4 text-gray-600"
 data-icon="lucide:car"
 data-inline="false"
></span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-semibold text-gray-900">754842-16</div>
                                            <div class="text-xs text-gray-500">Hyundai Accent</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Berline
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="flex items-center text-sm">
                                        <span
 class="iconify block h-3.5 w-3.5 text-gray-400 mr-1.5"
 data-icon="lucide:gauge"
 data-inline="false"
></span>
                                        <span class="font-medium text-gray-900">209,718</span>
                                        <span class="text-gray-500 ml-1">km</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div wire:snapshot="{&quot;data&quot;:{&quot;vehicleId&quot;:9,&quot;vehicle&quot;:[null,{&quot;class&quot;:&quot;App\\Models\\Vehicle&quot;,&quot;key&quot;:9,&quot;s&quot;:&quot;mdl&quot;}],&quot;showDropdown&quot;:false,&quot;showConfirmModal&quot;:false,&quot;pendingStatus&quot;:null,&quot;pendingStatusEnum&quot;:null,&quot;confirmMessage&quot;:&quot;&quot;},&quot;memo&quot;:{&quot;id&quot;:&quot;WrrcrDVVe6atalM4pfXZ&quot;,&quot;name&quot;:&quot;admin.vehicle-status-badge-ultra-pro&quot;,&quot;path&quot;:&quot;admin\/vehicles&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;children&quot;:[],&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;errors&quot;:[],&quot;locale&quot;:&quot;fr&quot;},&quot;checksum&quot;:&quot;6a883e2a1a63f05dffeeccf8085898f4f82def9a57594a33e6d8a2db1ba4058d&quot;}" wire:effects="{&quot;listeners&quot;:[&quot;refreshComponent&quot;,&quot;vehicleStatusUpdated&quot;,&quot;vehicleStatusChanged&quot;,&quot;echo:vehicles,VehicleStatusChanged&quot;]}" wire:id="WrrcrDVVe6atalM4pfXZ" class="relative inline-block" x-data="statusBadgeComponent()" wire:ignore.self>
    
    <!--[if BLOCK]><![endif]-->        <button
            wire:click="toggleDropdown"
            type="button"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold 
                   transition-all duration-200 hover:shadow-md hover:scale-105 cursor-pointer 
                   bg-blue-50 text-blue-700 ring-1 ring-blue-200
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            title="Cliquer pour modifier le statut">
            
            <!--[if BLOCK]><![endif]-->                <span
 class="iconify block w-3.5 h-3.5"
 data-icon="lucide:square-parking"
 data-inline="false"
></span>
            <!--[if ENDBLOCK]><![endif]-->
            
            
            <span>Parking</span>
            
            
            <svg class="w-3 h-3 opacity-60 transition-transform duration-200"
                 :class="{ 'rotate-180': open }"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    <!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]-->        <div
            x-show="open"
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute left-0 mt-2 w-64 rounded-xl shadow-2xl bg-white ring-1 ring-black ring-opacity-5 z-50 overflow-hidden"
            style="display: none;">

            
            <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                        Changer le statut
                    </span>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <span
 class="iconify block w-4 h-4"
 data-icon="lucide:x"
 data-inline="false"
></span>
                    </button>
                </div>
            </div>

            
            <div class="py-2 max-h-64 overflow-y-auto">
                <!--[if BLOCK]><![endif]-->                    <button
                        wire:click="prepareStatusChange('affecte')"
                        type="button"
                        class="w-full flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 
                               transition-all duration-150 group focus:outline-none focus:bg-gray-50">
                        <div class="flex items-center gap-3">
                            
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium 
                                       bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 group-hover:shadow-sm transition-all">
                                <span
 class="iconify block w-3 h-3"
 data-icon="lucide:user-check"
 data-inline="false"
></span>
                                Affecté
                            </span>
                        </div>
                        
                        <span
 class="iconify block w-4 h-4 text-gray-400 group-hover:text-blue-600 
                                         group-hover:translate-x-1 transition-all"
 data-icon="lucide:chevron-right"
 data-inline="false"
></span>
                    </button>
                                    <button
                        wire:click="prepareStatusChange('en_panne')"
                        type="button"
                        class="w-full flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 
                               transition-all duration-150 group focus:outline-none focus:bg-gray-50">
                        <div class="flex items-center gap-3">
                            
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium 
                                       bg-rose-50 text-rose-700 ring-1 ring-rose-200 group-hover:shadow-sm transition-all">
                                <span
 class="iconify block w-3 h-3"
 data-icon="lucide:alert-triangle"
 data-inline="false"
></span>
                                En panne
                            </span>
                        </div>
                        
                        <span
 class="iconify block w-4 h-4 text-gray-400 group-hover:text-blue-600 
                                         group-hover:translate-x-1 transition-all"
 data-icon="lucide:chevron-right"
 data-inline="false"
></span>
                    </button>
                <!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <!--[if BLOCK]><![endif]-->                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-start gap-2">
                        <span
 class="iconify block w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0"
 data-icon="lucide:info-circle"
 data-inline="false"
></span>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Véhicule disponible au parking, prêt pour affectation
                        </p>
                    </div>
                </div>
            <!--[if ENDBLOCK]><![endif]-->
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
 data-inline="false"
></span>
                            Confirmation de changement de statut
                        </h3>
                        <button @click="confirmModal = false; $wire.cancelStatusChange()"
                                class="text-white/80 hover:text-white transition-colors">
                            <span
 class="iconify block w-5 h-5"
 data-icon="lucide:x"
 data-inline="false"
></span>
                        </button>
                    </div>
                </div>

                
                <div class="px-6 py-5">
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 whitespace-pre-line"></p>
                    </div>

                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Véhicule:</span>
                                <p class="font-medium text-gray-900">Hyundai Accent</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Immatriculation:</span>
                                <p class="font-medium text-gray-900">754842-16</p>
                            </div>
                            <!--[if BLOCK]><![endif]-->                            <div>
                                <span class="text-gray-500">Statut actuel:</span>
                                <p class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-blue-200">
                                        <span
 class="iconify block w-3 h-3"
 data-icon="lucide:square-parking"
 data-inline="false"
></span>
                                        Parking
                                    </span>
                                </p>
                            </div>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    
                    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                </div>

                
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                    <button wire:click="cancelStatusChange"
                            type="button"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 
                                   rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 
                                   transition-all duration-150">
                        <span
 class="iconify block w-4 h-4"
 data-icon="lucide:x"
 data-inline="false"
></span>
                        Annuler
                    </button>
                    
                    <button wire:click="confirmStatusChange"
                            type="button"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="inline-flex items-center gap-2 px-4 py-2 
                                   bg-blue-600 hover:bg-blue-700 focus:ring-blue-500
                                   text-white rounded-lg text-sm font-medium 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 
                                   transition-all duration-150 shadow-sm hover:shadow-md">
                        <span wire:loading.remove wire:target="confirmStatusChange">
                            <span
 class="iconify block w-4 h-4"
 data-icon="lucide:check"
 data-inline="false"
></span>
                        </span>
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
</div>

                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <!--[if BLOCK]><![endif]-->                                        <div class="flex items-center gap-1.5">
                                            <span
 class="iconify block w-3.5 h-3.5 text-purple-600"
 data-icon="lucide:building-2"
 data-inline="false"
></span>
                                            <span class="text-sm text-gray-900">CONSTANTINE</span>
                                        </div>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                                                        <!--[if BLOCK]><![endif]-->                                        <span class="text-xs text-gray-400 italic">Non affecté</span>
                                    <!--[if ENDBLOCK]><![endif]-->
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center gap-1">
                                        
                                                                                    <a href="http://localhost/admin/vehicles/9" 
                                               class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                               title="Voir">
                                                <span
 class="iconify block w-4 h-4"
 data-icon="lucide:eye"
 data-inline="false"
></span>
                                            </a>
                                                                                
                                                                                    <a href="http://localhost/admin/vehicles/9/edit" 
                                               class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-all duration-200"
                                               title="Modifier">
                                                <span
 class="iconify block w-4 h-4"
 data-icon="lucide:edit"
 data-inline="false"
></span>
                                            </a>
                                                                                
                                        
                                        <div class="relative inline-block text-left" x-data="{ open: false }">
                                            <button @click="open = !open"
                                                    @click.away="open = false"
                                                    type="button"
                                                    class="inline-flex items-center p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200"
                                                    title="Plus d'actions">
                                                <span
 class="iconify block w-4 h-4"
 data-icon="lucide:more-vertical"
 data-inline="false"
></span>
                                            </button>

                                            <div x-show="open"
                                                 x-cloak
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="transform opacity-0 scale-95"
                                                 x-transition:enter-end="transform opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-75"
                                                 x-transition:leave-start="transform opacity-100 scale-100"
                                                 x-transition:leave-end="transform opacity-0 scale-95"
                                                 class="absolute right-0 z-50 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                                                <div class="py-1">
                                                    <!--[if BLOCK]><![endif]-->                                                        
                                                        <button wire:click="confirmRestore(9)"
                                                                @click="open = false"
                                                                class="flex w-full items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                            <span
 class="iconify block w-4 h-4 mr-2 text-green-600"
 data-icon="lucide:rotate-ccw"
 data-inline="false"
></span>
                                                            Restaurer
                                                        </button>
                                                        
                                                        
                                                        <button wire:click="confirmForceDelete(9)"
                                                                @click="open = false"
                                                                class="flex w-full items-center px-3 py-2 text-sm text-gray-700 hover:bg-red-50 transition-colors">
                                                            <span
 class="iconify block w-4 h-4 mr-2 text-red-600"
 data-icon="lucide:trash-2"
 data-inline="false"
></span>
                                                            Supprimer
                                                        </button>
                                                    <!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-200">
                <div>
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
</div>

            </div>
</div>
    </div>

    
    <div x-data="{ show: window.Livewire.find('HwAw6MKtBXuCrYcTcmjQ').entangle('selectedVehicles').live }" 
         x-show="show.length > 0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-8 left-1/2 transform -translate-x-1/2 z-50"
         style="display: none;">
        
        <div class="bg-white rounded-xl shadow-2xl border border-gray-200 px-6 py-4 flex items-center gap-6">
            
            <div class="flex items-center gap-2 border-r border-gray-300 pr-6">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <span
 class="iconify block w-4 h-4 text-blue-600"
 data-icon="lucide:check-circle-2"
 data-inline="false"
></span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Sélectionnés</p>
                    <p class="text-lg font-bold text-gray-900" x-text="show.length"></p>
                </div>
            </div>

            
            <div class="flex items-center gap-3">
                <!--[if BLOCK]><![endif]-->                    
                    <button 
                        wire:click="bulkRestore" 
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                        <span
 class="iconify block w-4 h-4" wire:loading.remove="" wire:target="bulkRestore"
 data-icon="lucide:rotate-ccw"
 data-inline="false"
></span>
                        <span
 class="iconify block w-4 h-4 animate-spin" wire:loading="" wire:target="bulkRestore"
 data-icon="lucide:loader-2"
 data-inline="false"
></span>
                        <span class="hidden sm:inline">Restaurer</span>
                    </button>

                    
                    <button 
                        wire:click="bulkForceDelete" 
                        wire:confirm="Êtes-vous sûr de vouloir supprimer définitivement ces véhicules ? Cette action est irréversible."
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                        <span
 class="iconify block w-4 h-4" wire:loading.remove="" wire:target="bulkForceDelete"
 data-icon="lucide:trash-2"
 data-inline="false"
></span>
                        <span
 class="iconify block w-4 h-4 animate-spin" wire:loading="" wire:target="bulkForceDelete"
 data-icon="lucide:loader-2"
 data-inline="false"
></span>
                        <span class="hidden sm:inline">Supprimer Définitivement</span>
                    </button>
                <!--[if ENDBLOCK]><![endif]-->

                
                <button 
                    wire:click="$set('selectedVehicles', [])" 
                    class="inline-flex items-center gap-2 px-3 py-2 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors border border-gray-300 shadow-sm">
                    <span
 class="iconify block w-4 h-4"
 data-icon="lucide:x"
 data-inline="false"
></span>
                    <span class="hidden sm:inline">Annuler</span>
                </button>
            </div>
        </div>
    </div>

    
    
    
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
</div>

     </div>
 </main>
 </div>
 </div>

 
 
 
 <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

 
 <script src="https://cdn.jsdelivr.net/npm/slim-select@2/dist/slimselect.min.js"></script>

 
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
 document.addEventListener('livewire:navigated', function () {
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
/**
 * 🎯 COMPOSANT ALPINE.JS POUR VEHICLE STATUS BADGE ULTRA PRO
 * Version: Enterprise-Grade - Compatible Livewire 3
 *
 * CORRECTION: Utilise wire:model et événements Livewire au lieu de entangle()
 * pour éviter les erreurs "Cannot read properties of undefined"
 */
function statusBadgeComponent() {
    return {
        open: false,
        confirmModal: false,
        componentId: 'CSVWxDPL6hz94cEkbevS',

        init() {
            const component = this;

            // Écouter les changements Livewire - Alpine vers Livewire
            this.$watch('open', value => {
                component.$wire.set('showDropdown', value, false);
            });

            this.$watch('confirmModal', value => {
                component.$wire.set('showConfirmModal', value, false);
            });

            // Écouter les mises à jour depuis Livewire - Livewire vers Alpine
            Livewire.hook('morph.updated', ({ el, component: livewireComponent }) => {
                if (livewireComponent.id === component.componentId) {
                    component.open = livewireComponent.get('showDropdown');
                    component.confirmModal = livewireComponent.get('showConfirmModal');
                }
            });
        }
    }
}
</script>
<script>
/**
 * 🎯 COMPOSANT ALPINE.JS POUR VEHICLE STATUS BADGE ULTRA PRO
 * Version: Enterprise-Grade - Compatible Livewire 3
 *
 * CORRECTION: Utilise wire:model et événements Livewire au lieu de entangle()
 * pour éviter les erreurs "Cannot read properties of undefined"
 */
function statusBadgeComponent() {
    return {
        open: false,
        confirmModal: false,
        componentId: 'WrrcrDVVe6atalM4pfXZ',

        init() {
            const component = this;

            // Écouter les changements Livewire - Alpine vers Livewire
            this.$watch('open', value => {
                component.$wire.set('showDropdown', value, false);
            });

            this.$watch('confirmModal', value => {
                component.$wire.set('showConfirmModal', value, false);
            });

            // Écouter les mises à jour depuis Livewire - Livewire vers Alpine
            Livewire.hook('morph.updated', ({ el, component: livewireComponent }) => {
                if (livewireComponent.id === component.componentId) {
                    component.open = livewireComponent.get('showDropdown');
                    component.confirmModal = livewireComponent.get('showConfirmModal');
                }
            });
        }
    }
}
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
                                 <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 0 010-1.414z" clip-rule="evenodd" />
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
<!-- Livewire Scripts -->
<script src="http://localhost/vendor/livewire/livewire.js?id=df3a17f2"   data-csrf="RbSUakAcZaawqjidDEzXkXoyiZXdH1kSyrqfkDXc" data-update-uri="/livewire/update" data-navigate-once="true"></script>
</body>
</html>