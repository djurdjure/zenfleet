
<!DOCTYPE html>
<html lang="fr" class="h-full bg-zinc-50">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="csrf-token" content="iJ2KXRB5KKQdDtzv9hqiS5sJslmnDZwxw0LxLkNo">
  <meta name="user-data" content="{&quot;id&quot;:3,&quot;name&quot;:&quot;Super Administrateur&quot;,&quot;role&quot;:&quot;Super Admin&quot;}">
 
 <title>Dashboard Super Admin - ZenFleet</title>

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

 <link rel="preload" as="style" href="http://localhost/build/assets/app-BEix2DWS.css" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/app-CJDlgUYG.js" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/vendor-common-B9ygI19o.js" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/ui-public-2hikc2V1.js" /><link rel="stylesheet" href="http://localhost/build/assets/app-BEix2DWS.css" data-navigate-track="reload" /><script type="module" src="http://localhost/build/assets/app-CJDlgUYG.js" data-navigate-track="reload"></script> </head>
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
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-blue-600 text-white shadow-md">
 <span
 class="iconify block w-5 h-5 mr-3 text-white"
 data-icon="material-symbols:dashboard-rounded"
 data-inline="false"
></span>
 <span class="flex-1">Dashboard</span>
 </a>
 </li>

 
  <li class="flex">
 <a href="http://localhost/admin/organizations"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
 data-icon="mdi:office-building"
 data-inline="false"
></span>
 <span class="flex-1">Organisations</span>
 </a>
 </li>
 
 
  <li class="flex flex-col" x-data="{ open: false }">
 <button @click="open = !open"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <span
 class="iconify block w-5 h-5 mr-3 text-gray-600"
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
 x-bind:style="`height: 0%; top: 0%;`"></div>
 </div>
 </div>
 <div class="flex-1 min-w-0 space-y-1.5">
  <a href="http://localhost/admin/vehicles"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
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
   <a href="http://localhost/admin/audit"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <span
 class="iconify block w-4 h-4 mr-2.5 text-gray-600"
 data-icon="mdi:security"
 data-inline="false"
></span>
 Audit & Sécurité
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
 class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold bg-zinc-950 text-white">
 <span
 class="iconify block h-5 w-5 shrink-0"
 data-icon="heroicons:home"
 data-inline="false"
></span>
 Dashboard
 </a>
 </li>

 
  <li>
 <a href="http://localhost/admin/organizations"
 class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
 <span
 class="iconify block h-5 w-5 shrink-0"
 data-icon="heroicons:building-office"
 data-inline="false"
></span>
 Organisations
 </a>
 </li>
 
 
  <li x-data="{ open: false }">
 <button @click="open = !open"
 class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
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
 class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
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
   <li class="relative">
 <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
 <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
 <a href="http://localhost/admin/audit"
 class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
 <span
 class="iconify block h-4 w-4 shrink-0"
 data-icon="mdi:security"
 data-inline="false"
></span>
 Audit & Sécurité
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
 <div class="text-sm font-semibold leading-5 text-zinc-900">Super Administrateur</div>
 <div class="text-xs leading-4 text-zinc-500">Super Admin</div>
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
 <div class="text-sm font-medium text-zinc-900">Super Administrateur</div>
 <div class="text-xs text-zinc-500">superadmin@zenfleet.dz</div>
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
 <input type="hidden" name="_token" value="iJ2KXRB5KKQdDtzv9hqiS5sJslmnDZwxw0LxLkNo" autocomplete="off"> <button type="submit"
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

 <main class="py-10">
 <div class="dashboard-super-admin">
 
 
 <div class="mb-8">
 <div class="flex items-center justify-between">
 <div>
 <h1 class="text-4xl font-bold text-gray-900">
 <i class="fas fa-tachometer-alt text-blue-600 mr-4"></i>
 Dashboard Super Admin
 </h1>
 <p class="text-xl text-gray-600 mt-2">
 Vue d'ensemble globale du système ZenFleet
 </p>

 
  </div>
 <div class="text-right">
 <div class="text-sm text-gray-500">Dernière mise à jour</div>
 <div class="text-lg font-semibold">26/11/2025 00:19</div>
 </div>
 </div>
 </div>

 
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
 
 <div class="admin-card bg-gradient-to-br from-blue-500 to-blue-700 text-white">
 <div class="admin-card-body">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">
 Organisations
 </p>
 <p class="text-4xl font-bold mt-2">
 12
 </p>
 <p class="text-blue-100 text-sm mt-1">
 10 actives
 </p>
 </div>
 <div class="p-4 bg-blue-400 bg-opacity-30 rounded-full">
 <i class="fas fa-building text-3xl"></i>
 </div>
 </div>
 </div>
 </div>

 
 <div class="admin-card bg-gradient-to-br from-green-500 to-green-700 text-white">
 <div class="admin-card-body">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-green-100 text-sm font-medium uppercase tracking-wider">
 Utilisateurs
 </p>
 <p class="text-4xl font-bold mt-2">
 11
 </p>
 <p class="text-green-100 text-sm mt-1">
 11 actifs
 </p>
 </div>
 <div class="p-4 bg-green-400 bg-opacity-30 rounded-full">
 <i class="fas fa-users text-3xl"></i>
 </div>
 </div>
 </div>
 </div>

 
 <div class="admin-card bg-gradient-to-br from-yellow-500 to-yellow-700 text-white">
 <div class="admin-card-body">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-yellow-100 text-sm font-medium uppercase tracking-wider">
 Véhicules
 </p>
 <p class="text-4xl font-bold mt-2">
 58
 </p>
 <p class="text-yellow-100 text-sm mt-1">
 Toutes organisations
 </p>
 </div>
 <div class="p-4 bg-yellow-400 bg-opacity-30 rounded-full">
 <i class="fas fa-car text-3xl"></i>
 </div>
 </div>
 </div>
 </div>

 
 <div class="admin-card bg-gradient-to-br from-purple-500 to-purple-700 text-white">
 <div class="admin-card-body">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-purple-100 text-sm font-medium uppercase tracking-wider">
 Santé Système
 </p>
 <p class="text-4xl font-bold mt-2">
 99.9%
 </p>
 <p class="text-purple-100 text-sm mt-1">
 Disponibilité
 </p>
 </div>
 <div class="p-4 bg-purple-400 bg-opacity-30 rounded-full">
 <i class="fas fa-heartbeat text-3xl"></i>
 </div>
 </div>
 </div>
 </div>
 </div>

 
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
 
 
 <div class="lg:col-span-2">
 <div class="admin-card">
 <div class="admin-card-header">
 <h3 class="text-xl font-semibold text-gray-900">
 <i class="fas fa-activity text-blue-600 mr-2"></i>
 Activité Système Récente
 </h3>
 </div>
 <div class="admin-card-body">
 <div class="space-y-4 max-h-96 overflow-y-auto">
  <div class="text-center text-gray-500 py-8">
 <i class="fas fa-inbox text-4xl mb-4"></i>
 <p>Aucune activité récente</p>
 </div>
  </div>
 </div>
 </div>
 </div>

 
 <div class="space-y-6">
 
 
 <div class="admin-card">
 <div class="admin-card-header">
 <h3 class="text-lg font-semibold text-gray-900">
 <i class="fas fa-server text-green-600 mr-2"></i>
 État des Services
 </h3>
 </div>
 <div class="admin-card-body">
 <div class="space-y-3">
  <div class="flex items-center justify-between">
 <span class="text-sm font-medium text-gray-700 capitalize">
 database
 </span>
 <span class="px-2 py-1 text-xs font-semibold rounded-full
 bg-green-100 text-green-800">
 Opérationnel
 </span>
 </div>
  <div class="flex items-center justify-between">
 <span class="text-sm font-medium text-gray-700 capitalize">
 redis
 </span>
 <span class="px-2 py-1 text-xs font-semibold rounded-full
 bg-green-100 text-green-800">
 Opérationnel
 </span>
 </div>
  <div class="flex items-center justify-between">
 <span class="text-sm font-medium text-gray-700 capitalize">
 storage
 </span>
 <span class="px-2 py-1 text-xs font-semibold rounded-full
 bg-green-100 text-green-800">
 Opérationnel
 </span>
 </div>
  <div class="flex items-center justify-between">
 <span class="text-sm font-medium text-gray-700 capitalize">
 queue
 </span>
 <span class="px-2 py-1 text-xs font-semibold rounded-full
 bg-green-100 text-green-800">
 Opérationnel
 </span>
 </div>
  <div class="flex items-center justify-between">
 <span class="text-sm font-medium text-gray-700 capitalize">
 overall
 </span>
 <span class="px-2 py-1 text-xs font-semibold rounded-full
 bg-green-100 text-green-800">
 Opérationnel
 </span>
 </div>
  </div>
 </div>
 </div>

 
 <div class="admin-card">
 <div class="admin-card-header">
 <h3 class="text-lg font-semibold text-gray-900">
 <i class="fas fa-trophy text-yellow-600 mr-2"></i>
 Top Organisations
 </h3>
 </div>
 <div class="admin-card-body">
 <div class="space-y-3">
  <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-900">Trans-Alger Logistics</p>
 <p class="text-xs text-gray-500">Alger-Centre</p>
 </div>
 <div class="text-right">
 <p class="text-sm font-semibold text-blue-600">
 11 users
 </p>
 <p class="text-xs text-gray-500">
 58 véhicules
 </p>
 </div>
 </div>
  <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-900">Setif Fleet Services</p>
 <p class="text-xs text-gray-500">Sétif</p>
 </div>
 <div class="text-right">
 <p class="text-sm font-semibold text-blue-600">
 0 users
 </p>
 <p class="text-xs text-gray-500">
 0 véhicules
 </p>
 </div>
 </div>
  <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-900">Oran Maritime Transport</p>
 <p class="text-xs text-gray-500">Oran</p>
 </div>
 <div class="text-right">
 <p class="text-sm font-semibold text-blue-600">
 0 users
 </p>
 <p class="text-xs text-gray-500">
 0 véhicules
 </p>
 </div>
 </div>
  <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-900">Stoltenberg LLC</p>
 <p class="text-xs text-gray-500">Souk Ahras</p>
 </div>
 <div class="text-right">
 <p class="text-sm font-semibold text-blue-600">
 0 users
 </p>
 <p class="text-xs text-gray-500">
 0 véhicules
 </p>
 </div>
 </div>
  <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-900">Dickens, Rath and Murazik</p>
 <p class="text-xs text-gray-500">Tlemcen</p>
 </div>
 <div class="text-right">
 <p class="text-sm font-semibold text-blue-600">
 0 users
 </p>
 <p class="text-xs text-gray-500">
 0 véhicules
 </p>
 </div>
 </div>
  </div>
 </div>
 </div>

 
 <div class="admin-card">
 <div class="admin-card-header">
 <h3 class="text-lg font-semibold text-gray-900">
 <i class="fas fa-bolt text-blue-600 mr-2"></i>
 Actions Rapides
 </h3>
 </div>
 <div class="admin-card-body">
 <div class="space-y-2">
 <a href="http://localhost/admin/organizations/create" 
 class="admin-btn admin-btn-primary w-full text-center">
 <i class="fas fa-plus mr-2"></i>
 Nouvelle Organisation
 </a>
 
 <a href="http://localhost/admin/system/health" 
 class="admin-btn admin-btn-secondary w-full text-center">
 <i class="fas fa-heartbeat mr-2"></i>
 Monitoring Système
 </a>
 
 <a href="http://localhost/admin/audit" 
 class="admin-btn admin-btn-secondary w-full text-center">
 <i class="fas fa-shield-alt mr-2"></i>
 Logs d'Audit
 </a>
 </div>
 </div>
 </div>
 </div>
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
document.addEventListener('DOMContentLoaded', function() {
 console.log('🎉 Dashboard Super Admin chargé avec succès !');
 
 // Animation des cartes statistiques
 const cards = document.querySelectorAll('.admin-card');
 cards.forEach((card, index) => {
 card.style.opacity = '0';
 card.style.transform = 'translateY(20px)';
 
 setTimeout(() => {
 card.style.transition = 'all 0.5s ease';
 card.style.opacity = '1';
 card.style.transform = 'translateY(0)';
 }, index * 100);
 });

 // Actualisation automatique des données toutes les 5 minutes
 setInterval(() => {
 window.location.reload();
 }, 300000);
});
</script>
 <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

 
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