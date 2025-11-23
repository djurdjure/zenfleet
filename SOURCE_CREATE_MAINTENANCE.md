
<!DOCTYPE html>
<html lang="fr" class="h-full bg-zinc-50">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="csrf-token" content="qIchyO828XloBXfyweeYS5HoCRtoujkb6Lbz7RyC">
  <meta name="user-data" content="{&quot;id&quot;:4,&quot;name&quot;:&quot;admin zenfleet&quot;,&quot;role&quot;:&quot;Admin&quot;}">
 
 <title>Nouvelle Opération de Maintenance - ZenFleet</title>

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

 <link rel="preload" as="style" href="http://localhost/build/assets/app-C7XUWI-X.css" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/app-D2eP0xWF.js" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/vendor-common-B9ygI19o.js" /><link rel="modulepreload" as="script" href="http://localhost/build/assets/ui-public-2hikc2V1.js" /><link rel="stylesheet" href="http://localhost/build/assets/app-C7XUWI-X.css" data-navigate-track="reload" /><script type="module" src="http://localhost/build/assets/app-D2eP0xWF.js" data-navigate-track="reload"></script>  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

 /* Input avec bordure rouge si erreur */
 input.datepicker.border-red-500 + .flatpickr-calendar {
 border-color: rgb(239 68 68);
 }
 </style>
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
 
 
  <li class="flex flex-col" x-data="{ open: true }">
 <button @click="open = !open"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-blue-600 text-white shadow-md">
 <span
 class="iconify block w-5 h-5 mr-3 text-white"
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
 <input type="hidden" name="_token" value="qIchyO828XloBXfyweeYS5HoCRtoujkb6Lbz7RyC" autocomplete="off"> <button type="submit"
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
 <section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-4xl lg:py-6">

        
        <div class="mb-6">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm">
                    <li class="inline-flex items-center">
                        <a href="http://localhost/admin/maintenance/operations" class="text-gray-600 hover:text-blue-600 transition-colors">
                            <span
 class="iconify block w-4 h-4 mr-2 inline"
 data-icon="lucide:wrench"
 data-inline="false"
></span>
                            Maintenance
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span
 class="iconify block w-4 h-4 text-gray-400 mx-1"
 data-icon="lucide:chevron-right"
 data-inline="false"
></span>
                            <span class="text-gray-900 font-medium">Nouvelle Opération</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <span
 class="iconify block w-6 h-6 text-blue-600"
 data-icon="lucide:plus-circle"
 data-inline="false"
></span>
                Nouvelle Opération de Maintenance
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Planifiez une intervention avec recherche intelligente et auto-complétion
            </p>
        </div>

        
        
        
                    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs">
                <strong>📊 Debug Info:</strong>
                Véhicules: 58 |
                Types: 5 |
                Fournisseurs: 3
            </div>
        
        
        <div x-data="maintenanceFormData()" x-init="init()">
            <form action="http://localhost/admin/maintenance/operations" method="POST" @submit="onSubmit" class="space-y-6">
                <input type="hidden" name="_token" value="qIchyO828XloBXfyweeYS5HoCRtoujkb6Lbz7RyC" autocomplete="off">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                        <span
 class="iconify block w-5 h-5 text-blue-600"
 data-icon="lucide:settings"
 data-inline="false"
></span>
                        Informations de l'Opération
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        
                        <div class="md:col-span-2">
                            <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <span
 class="iconify block w-4 h-4 inline mr-1"
 data-icon="lucide:car"
 data-inline="false"
></span>
                                Véhicule <span class="text-red-500">*</span>
                            </label>
                            <select name="vehicle_id"
                                    id="vehicle_id"
                                    required
                                    x-ref="vehicleSelect"
                                    @change="onVehicleChange($event.target.value)"
                                    class="zenfleet-select block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 transition-colors ">
                                <option value="">-- Sélectionner un véhicule --</option>
                                                                    <option value="26"
                                            data-mileage=""
                                            data-brand="Peugeot"
                                            data-model="308"
                                            >
                                        
                                    </option>
                                                                    <option value="67"
                                            data-mileage=""
                                            data-brand="TOYOTA"
                                            data-model="Yaris"
                                            >
                                        
                                    </option>
                                                                    <option value="22"
                                            data-mileage=""
                                            data-brand="Hyundai"
                                            data-model="Tucson"
                                            >
                                        
                                    </option>
                                                                    <option value="31"
                                            data-mileage=""
                                            data-brand="Volkswagen"
                                            data-model="Crafter"
                                            >
                                        
                                    </option>
                                                                    <option value="45"
                                            data-mileage=""
                                            data-brand="Renault"
                                            data-model="Logan"
                                            >
                                        
                                    </option>
                                                                    <option value="38"
                                            data-mileage=""
                                            data-brand="Hyundai"
                                            data-model="i10"
                                            >
                                        
                                    </option>
                                                                    <option value="41"
                                            data-mileage=""
                                            data-brand="Peugeot"
                                            data-model="Partner"
                                            >
                                        
                                    </option>
                                                                    <option value="6"
                                            data-mileage=""
                                            data-brand="Toyota"
                                            data-model="Hilux"
                                            >
                                        
                                    </option>
                                                                    <option value="34"
                                            data-mileage=""
                                            data-brand="Volkswagen"
                                            data-model="Caddy"
                                            >
                                        
                                    </option>
                                                                    <option value="36"
                                            data-mileage=""
                                            data-brand="Isuzu"
                                            data-model="FTR"
                                            >
                                        
                                    </option>
                                                                    <option value="7"
                                            data-mileage=""
                                            data-brand="Renault"
                                            data-model="Sandero"
                                            >
                                        
                                    </option>
                                                                    <option value="27"
                                            data-mileage=""
                                            data-brand="Peugeot"
                                            data-model="Partner"
                                            >
                                        
                                    </option>
                                                                    <option value="30"
                                            data-mileage=""
                                            data-brand="Ford"
                                            data-model="Escort"
                                            >
                                        
                                    </option>
                                                                    <option value="39"
                                            data-mileage=""
                                            data-brand="Isuzu"
                                            data-model="D-Max"
                                            >
                                        
                                    </option>
                                                                    <option value="13"
                                            data-mileage=""
                                            data-brand="Mercedes"
                                            data-model="A-Class"
                                            >
                                        
                                    </option>
                                                                    <option value="8"
                                            data-mileage=""
                                            data-brand="Renault"
                                            data-model="Logan"
                                            >
                                        
                                    </option>
                                                                    <option value="55"
                                            data-mileage=""
                                            data-brand="Isuzu"
                                            data-model="D-Max"
                                            >
                                        
                                    </option>
                                                                    <option value="42"
                                            data-mileage=""
                                            data-brand="Toyota"
                                            data-model="Hiace"
                                            >
                                        
                                    </option>
                                                                    <option value="16"
                                            data-mileage=""
                                            data-brand="Isuzu"
                                            data-model="NQR"
                                            >
                                        
                                    </option>
                                                                    <option value="11"
                                            data-mileage=""
                                            data-brand="Volkswagen"
                                            data-model="Golf"
                                            >
                                        
                                    </option>
                                                                    <option value="54"
                                            data-mileage=""
                                            data-brand="Peugeot"
                                            data-model="301"
                                            >
                                        
                                    </option>
                                                                    <option value="4"
                                            data-mileage=""
                                            data-brand="Ford"
                                            data-model="Focus"
                                            >
                                        
                                    </option>
                                                                    <option value="12"
                                            data-mileage=""
                                            data-brand="Iveco"
                                            data-model="Stralis"
                                            >
                                        
                                    </option>
                                                                    <option value="5"
                                            data-mileage=""
                                            data-brand="Toyota"
                                            data-model="Hilux"
                                            >
                                        
                                    </option>
                                                                    <option value="32"
                                            data-mileage=""
                                            data-brand="Isuzu"
                                            data-model="FTR"
                                            >
                                        
                                    </option>
                                                                    <option value="53"
                                            data-mileage=""
                                            data-brand="Mercedes"
                                            data-model="Sprinter"
                                            >
                                        
                                    </option>
                                                                    <option value="15"
                                            data-mileage=""
                                            data-brand="Peugeot"
                                            data-model="Boxer"
                                            >
                                        
                                    </option>
                                                                    <option value="3"
                                            data-mileage=""
                                            data-brand="Ford"
                                            data-model="Escort"
                                            >
                                        
                                    </option>
                                                                    <option value="20"
                                            data-mileage=""
                                            data-brand="Peugeot"
                                            data-model="207"
                                            >
                                        
                                    </option>
                                                                    <option value="43"
                                            data-mileage=""
                                            data-brand="Toyota"
                                            data-model="Corolla"
                                            >
                                        
                                    </option>
                                                                    <option value="48"
                                            data-mileage=""
                                            data-brand="Renault"
                                            data-model="Trafic"
                                            >
                                        
                                    </option>
                                                                    <option value="2"
                                            data-mileage=""
                                            data-brand="Toyota"
                                            data-model="Corolla"
                                            >
                                        
                                    </option>
                                                                    <option value="18"
                                            data-mileage=""
                                            data-brand="Isuzu"
                                            data-model="NQR"
                                            >
                                        
                                    </option>
                                                                    <option value="14"
                                            data-mileage=""
                                            data-brand="Peugeot"
                                            data-model="206"
                                            >
                                        
                                    </option>
                                                                    <option value="10"
                                            data-mileage=""
                                            data-brand="Renault"
                                            data-model="Clio"
                                            >
                                        
                                    </option>
                                                                    <option value="24"
                                            data-mileage=""
                                            data-brand="Toyota"
                                            data-model="Corolla"
                                            >
                                        
                                    </option>
                                                                    <option value="46"
                                            data-mileage=""
                                            data-brand="Iveco"
                                            data-model="Stralis"
                                            >
                                        
                                    </option>
                                                                    <option value="44"
                                            data-mileage=""
                                            data-brand="Mercedes"
                                            data-model="Vito"
                                            >
                                        
                                    </option>
                                                                    <option value="17"
                                            data-mileage=""
                                            data-brand="Hyundai"
                                            data-model="i10"
                                            >
                                        
                                    </option>
                                                                    <option value="33"
                                            data-mileage=""
                                            data-brand="Volkswagen"
                                            data-model="Jetta"
                                            >
                                        
                                    </option>
                                                                    <option value="58"
                                            data-mileage=""
                                            data-brand="Renault"
                                            data-model="Sandero"
                                            >
                                        
                                    </option>
                                                                    <option value="47"
                                            data-mileage=""
                                            data-brand="Peugeot"
                                            data-model="301"
                                            >
                                        
                                    </option>
                                                                    <option value="66"
                                            data-mileage=""
                                            data-brand="TOYOTA"
                                            data-model="Yaris"
                                            >
                                        
                                    </option>
                                                                    <option value="21"
                                            data-mileage=""
                                            data-brand="Peugeot"
                                            data-model="208"
                                            >
                                        
                                    </option>
                                                                    <option value="9"
                                            data-mileage=""
                                            data-brand="Hyundai"
                                            data-model="Accent"
                                            >
                                        
                                    </option>
                                                                    <option value="35"
                                            data-mileage=""
                                            data-brand="Isuzu"
                                            data-model="NQR"
                                            >
                                        
                                    </option>
                                                                    <option value="25"
                                            data-mileage=""
                                            data-brand="Toyota"
                                            data-model="Hiace"
                                            >
                                        
                                    </option>
                                                                    <option value="28"
                                            data-mileage=""
                                            data-brand="Isuzu"
                                            data-model="NQR"
                                            >
                                        
                                    </option>
                                                                    <option value="40"
                                            data-mileage=""
                                            data-brand="Ford"
                                            data-model="Fiesta"
                                            >
                                        
                                    </option>
                                                                    <option value="51"
                                            data-mileage=""
                                            data-brand="Mercedes"
                                            data-model="Sprinter"
                                            >
                                        
                                    </option>
                                                                    <option value="50"
                                            data-mileage=""
                                            data-brand="Peugeot"
                                            data-model="308"
                                            >
                                        
                                    </option>
                                                                    <option value="56"
                                            data-mileage=""
                                            data-brand="Isuzu"
                                            data-model="D-Max"
                                            >
                                        
                                    </option>
                                                                    <option value="49"
                                            data-mileage=""
                                            data-brand="Mercedes"
                                            data-model="Vito"
                                            >
                                        
                                    </option>
                                                                    <option value="19"
                                            data-mileage=""
                                            data-brand="Toyota"
                                            data-model="Hiace"
                                            >
                                        
                                    </option>
                                                                    <option value="52"
                                            data-mileage=""
                                            data-brand="Peugeot"
                                            data-model="Partner"
                                            >
                                        
                                    </option>
                                                                    <option value="23"
                                            data-mileage=""
                                            data-brand="Peugeot"
                                            data-model="308"
                                            >
                                        
                                    </option>
                                                                    <option value="37"
                                            data-mileage=""
                                            data-brand="Volkswagen"
                                            data-model="Caddy"
                                            >
                                        
                                    </option>
                                                                    <option value="57"
                                            data-mileage=""
                                            data-brand="Peugeot"
                                            data-model="Partner"
                                            >
                                        
                                    </option>
                                                            </select>
                                                                                </div>

                        
                        <div class="md:col-span-2">
                            <label for="maintenance_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <span
 class="iconify block w-4 h-4 inline mr-1"
 data-icon="lucide:tag"
 data-inline="false"
></span>
                                Type de Maintenance <span class="text-red-500">*</span>
                            </label>
                            <select name="maintenance_type_id"
                                    id="maintenance_type_id"
                                    required
                                    @change="onTypeChange($event.target.value)"
                                    class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 transition-colors ">
                                <option value="">-- Sélectionner un type --</option>
                                                                    <option value="3"
                                            data-category="corrective"
                                            data-duration-hours=""
                                            data-duration-minutes=""
                                            data-cost="8000.00"
                                            data-description=""
                                            >
                                        
                                    </option>
                                                                    <option value="5"
                                            data-category="corrective"
                                            data-duration-hours=""
                                            data-duration-minutes=""
                                            data-cost="20000.00"
                                            data-description=""
                                            >
                                        
                                    </option>
                                                                    <option value="4"
                                            data-category="inspection"
                                            data-duration-hours=""
                                            data-duration-minutes=""
                                            data-cost="3000.00"
                                            data-description=""
                                            >
                                        
                                    </option>
                                                                    <option value="2"
                                            data-category="preventive"
                                            data-duration-hours=""
                                            data-duration-minutes=""
                                            data-cost="15000.00"
                                            data-description=""
                                            >
                                        
                                    </option>
                                                                    <option value="1"
                                            data-category="preventive"
                                            data-duration-hours=""
                                            data-duration-minutes=""
                                            data-cost="5000.00"
                                            data-description=""
                                            >
                                        
                                    </option>
                                                            </select>
                                                        <p x-show="selectedType.description"
                               x-text="selectedType.description"
                               class="mt-2 text-xs text-gray-600 italic"></p>
                                                    </div>

                        
                        <div>
                            <div class="">
  <label for="scheduled_date" class="block mb-2 text-sm font-medium text-gray-900">
 Date Planifiée
  <span class="text-red-600">*</span>
  </label>
 
 <div class="relative">
 <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
 <span
 class="iconify block w-4 h-4 text-gray-500"
 data-icon="heroicons:calendar-days"
 data-inline="false"
></span>
 </div>
 <input
 type="text"
 name="scheduled_date"
 id="scheduled_date"
 class="datepicker !bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 block w-full pl-10 p-2.5 transition-colors duration-200"
 placeholder="Sélectionner une date"
 value="23/11/2025"
  required    data-min-date="23/11/2025"   data-date-format="d/m/Y"
 autocomplete="off"
 
 />
 </div>

  <p class="mt-2 text-sm text-gray-600">
 Date de l&#039;intervention
 </p>
 </div>

 
                         </div>

                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                <span
 class="iconify block w-4 h-4 inline mr-1"
 data-icon="lucide:info"
 data-inline="false"
></span>
                                Statut <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                    class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5">
                                <option value="planned" selected>📅 Planifiée</option>
                                <option value="in_progress">🔧 En cours</option>
                            </select>
                        </div>

                        
                        <div class="md:col-span-2">
                            <div class="flex items-center justify-between mb-2">
                                <label for="provider_id" class="block text-sm font-medium text-gray-700">
                                    <span
 class="iconify block w-4 h-4 inline mr-1"
 data-icon="lucide:building"
 data-inline="false"
></span>
                                    Fournisseur
                                </label>
                                <a href="http://localhost/admin/suppliers/create"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors">
                                    <span
 class="iconify block w-3.5 h-3.5"
 data-icon="lucide:plus-circle"
 data-inline="false"
></span>
                                    Ajouter un fournisseur
                                </a>
                            </div>
                            <select name="provider_id"
                                    id="provider_id"
                                    x-ref="providerSelect"
                                    class="zenfleet-select block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5">
                                <option value="">Aucun fournisseur (maintenance interne)</option>
                                                                    <option value="3"
                                            data-type=""
                                            data-rating="4.8"
                                            >
                                        
                                    </option>
                                                                    <option value="1"
                                            data-type=""
                                            data-rating="4.5"
                                            >
                                        
                                    </option>
                                                                    <option value="2"
                                            data-type=""
                                            data-rating="4.2"
                                            >
                                        
                                    </option>
                                                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                Sélectionnez un prestataire externe ou laissez vide pour maintenance interne
                            </p>
                                                    </div>

                        
                        <div>
                            <label for="mileage_at_maintenance" class="block text-sm font-medium text-gray-700 mb-2">
                                <span
 class="iconify block w-4 h-4 inline mr-1"
 data-icon="lucide:gauge"
 data-inline="false"
></span>
                                Kilométrage Actuel (km)
                            </label>
                            <div class="relative">
                                <input type="number"
                                       name="mileage_at_maintenance"
                                       id="mileage_at_maintenance"
                                       x-model="currentMileage"
                                       min="0"
                                       step="1"
                                       placeholder="Ex: 50000"
                                       class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 pr-10 ">
                                <div x-show="currentMileage > 0" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span
 class="iconify block w-4 h-4 text-green-500"
 data-icon="lucide:check-circle"
 data-inline="false"
></span>
                                </div>
                            </div>
                                                            <p class="mt-1 text-xs text-blue-600" x-show="currentMileage > 0 && autoFilledMileage">
                                    <span
 class="iconify block w-3 h-3 inline"
 data-icon="lucide:zap"
 data-inline="false"
></span>
                                    Auto-rempli depuis le véhicule
                                </p>
                                                    </div>

                        
                        <div>
                            <label for="total_cost" class="block text-sm font-medium text-gray-700 mb-2">
                                <span
 class="iconify block w-4 h-4 inline mr-1"
 data-icon="lucide:banknote"
 data-inline="false"
></span>
                                Coût Estimé (DA)
                            </label>
                            <input type="number"
                                   name="total_cost"
                                   id="total_cost"
                                   x-model="estimatedCost"
                                   min="0"
                                   step="0.01"
                                   placeholder="Ex: 15000"
                                   class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 ">
                                                            <p class="mt-1 text-xs text-gray-500" x-show="estimatedCost > 0 && autoFilledCost">
                                    <span
 class="iconify block w-3 h-3 inline"
 data-icon="lucide:zap"
 data-inline="false"
></span>
                                    Auto-rempli depuis le type
                                </p>
                                                    </div>

                        
                        <div>
                            <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-2">
                                <span
 class="iconify block w-4 h-4 inline mr-1"
 data-icon="lucide:clock"
 data-inline="false"
></span>
                                Durée Estimée (heures)
                            </label>
                            <div class="relative">
                                <input type="number"
                                       id="duration_hours"
                                       x-model="durationHours"
                                       @input="updateDurationMinutes"
                                       min="0.1"
                                       max="100"
                                       step="0.5"
                                       placeholder="Ex: 2.5"
                                       class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 pr-20">
                                <input type="hidden" name="duration_minutes" :value="durationMinutes">
                                <div x-show="durationHours > 0" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-xs text-gray-500 font-medium" x-text="durationMinutes + ' min'"></span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500" x-show="durationHours > 0 && autoFilledDuration">
                                <span
 class="iconify block w-3 h-3 inline"
 data-icon="lucide:zap"
 data-inline="false"
></span>
                                Auto-rempli depuis le type
                            </p>
                        </div>

                        
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                <span
 class="iconify block w-4 h-4 inline mr-1"
 data-icon="lucide:file-text"
 data-inline="false"
></span>
                                Description
                            </label>
                            <textarea name="description"
                                      id="description"
                                      rows="3"
                                      placeholder="Décrivez les travaux à effectuer..."
                                      class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 "></textarea>
                                                            <p class="mt-1 text-xs text-gray-500">Maximum 1000 caractères</p>
                                                    </div>

                        
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                <span
 class="iconify block w-4 h-4 inline mr-1"
 data-icon="lucide:message-square"
 data-inline="false"
></span>
                                Notes Internes
                            </label>
                            <textarea name="notes"
                                      id="notes"
                                      rows="2"
                                      placeholder="Notes additionnelles..."
                                      class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 "></textarea>
                                                    </div>
                    </div>
                </div>

                
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <a href="http://localhost/admin/maintenance/operations"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            <span
 class="iconify block w-4 h-4"
 data-icon="lucide:arrow-left"
 data-inline="false"
></span>
                            Annuler
                        </a>

                        <div class="flex items-center gap-3">
                            <button type="submit" name="action" value="save_and_continue"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-blue-300 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-50 transition-colors">
                                <span
 class="iconify block w-4 h-4"
 data-icon="lucide:save"
 data-inline="false"
></span>
                                Enregistrer et Créer Autre
                            </button>

                            <button type="submit" name="action" value="save"
                                    class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                <span
 class="iconify block w-4 h-4"
 data-icon="lucide:check"
 data-inline="false"
></span>
                                Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            
            <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <span
 class="iconify block w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"
 data-icon="lucide:sparkles"
 data-inline="false"
></span>
                    <div>
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">✨ Fonctionnalités Intelligentes</h3>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• <strong>Recherche rapide</strong> : Tapez pour filtrer les véhicules et fournisseurs</li>
                            <li>• <strong>Auto-complétion</strong> : Le kilométrage, coût et durée se remplissent automatiquement</li>
                            <li>• <strong>Durée en heures</strong> : Convertie automatiquement en minutes pour le système</li>
                            <li>• <strong>Ajout rapide</strong> : Créez un fournisseur sans quitter cette page</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>



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

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
 <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
 <script>
 document.addEventListener('DOMContentLoaded', function() {
 document.querySelectorAll('.datepicker').forEach(function(el) {
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
 });
 </script>
 <script type="module">
/**
 * 🎯 FORMULAIRE MAINTENANCE - ALPINE.JS + SLIMSELECT
 * Version finale avec SlimSelect intégré
 */

// Import SlimSelect dynamique
let SlimSelect = null;
try {
    const module = await import('slim-select');
    SlimSelect = module.default;
    console.log('✅ SlimSelect chargé avec succès');
} catch (error) {
    console.warn('⚠️ SlimSelect non disponible, utilisation des selects natifs', error);
}

window.maintenanceFormData = function() {
    return {
        // État
        currentMileage: 0,
        estimatedCost: 0,
        durationHours: 0,
        durationMinutes: 0,

        selectedVehicle: {},
        selectedType: {},

        autoFilledMileage: false,
        autoFilledCost: false,
        autoFilledDuration: false,

        // Instances SlimSelect
        vehicleSlimSelect: null,
        providerSlimSelect: null,

        init() {
            console.log('✅ Formulaire maintenance initialisé');

            // Initialiser SlimSelect si disponible
            this.$nextTick(() => {
                if (SlimSelect) {
                    this.initSlimSelects();
                } else {
                    console.log('📋 Utilisation des selects natifs HTML5');
                }
            });
        },

        initSlimSelects() {
            try {
                // SlimSelect pour véhicules
                if (this.$refs.vehicleSelect) {
                    this.vehicleSlimSelect = new SlimSelect({
                        select: this.$refs.vehicleSelect,
                        settings: {
                            searchPlaceholder: 'Rechercher un véhicule...',
                            searchText: 'Aucun véhicule trouvé',
                            searchHighlight: true,
                            closeOnSelect: true
                        }
                    });
                    console.log('✅ SlimSelect véhicules initialisé');
                }

                // SlimSelect pour fournisseurs
                if (this.$refs.providerSelect) {
                    this.providerSlimSelect = new SlimSelect({
                        select: this.$refs.providerSelect,
                        settings: {
                            searchPlaceholder: 'Rechercher un fournisseur...',
                            searchText: 'Aucun fournisseur trouvé',
                            searchHighlight: true,
                            closeOnSelect: true,
                            allowDeselect: true
                        }
                    });
                    console.log('✅ SlimSelect fournisseurs initialisé');
                }
            } catch (error) {
                console.error('❌ Erreur lors de l\'initialisation de SlimSelect:', error);
            }
        },

        onVehicleChange(vehicleId) {
            if (!vehicleId) {
                this.currentMileage = 0;
                this.selectedVehicle = {};
                this.autoFilledMileage = false;
                return;
            }

            const select = document.getElementById('vehicle_id');
            const option = select.options[select.selectedIndex];

            if (option) {
                const mileage = parseInt(option.dataset.mileage) || 0;

                this.selectedVehicle = {
                    id: vehicleId,
                    mileage: mileage,
                    brand: option.dataset.brand,
                    model: option.dataset.model
                };

                if (mileage > 0) {
                    this.currentMileage = mileage;
                    this.autoFilledMileage = true;
                    console.log('📊 Kilométrage auto-rempli:', mileage);
                }
            }
        },

        onTypeChange(typeId) {
            if (!typeId) {
                this.selectedType = {};
                this.autoFilledCost = false;
                this.autoFilledDuration = false;
                return;
            }

            const select = document.getElementById('maintenance_type_id');
            const option = select.options[select.selectedIndex];

            if (option) {
                this.selectedType = {
                    id: typeId,
                    category: option.dataset.category,
                    duration_hours: parseFloat(option.dataset.durationHours) || 0,
                    duration_minutes: parseInt(option.dataset.durationMinutes) || 0,
                    estimated_cost: parseFloat(option.dataset.cost) || 0,
                    description: option.dataset.description || ''
                };

                if (this.selectedType.estimated_cost > 0) {
                    this.estimatedCost = this.selectedType.estimated_cost;
                    this.autoFilledCost = true;
                    console.log('💰 Coût auto-rempli:', this.estimatedCost);
                }

                if (this.selectedType.duration_hours > 0) {
                    this.durationHours = this.selectedType.duration_hours;
                    this.durationMinutes = this.selectedType.duration_minutes;
                    this.autoFilledDuration = true;
                    console.log('⏱️ Durée auto-remplie:', this.durationHours, 'h');
                }
            }
        },

        updateDurationMinutes() {
            this.durationMinutes = Math.round(this.durationHours * 60);
        },

        onSubmit(event) {
            const vehicleId = document.getElementById('vehicle_id').value;
            const typeId = document.getElementById('maintenance_type_id').value;

            if (!vehicleId) {
                event.preventDefault();
                alert('❌ Veuillez sélectionner un véhicule');
                return false;
            }

            if (!typeId) {
                event.preventDefault();
                alert('❌ Veuillez sélectionner un type de maintenance');
                return false;
            }

            console.log('✅ Formulaire validé et soumis');
            return true;
        }
    };
};
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