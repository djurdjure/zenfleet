<!DOCTYPE html>
<!-- saved from url=(0038)http://localhost/admin/vehicles/create -->
<html lang="fr" class="h-full bg-zinc-50" webcrx=""><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
 
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="csrf-token" content="ibkA7bKbNTVfvGQNnAhqpGAYV0nBCkXPfNQTNxxU">
  <meta name="user-data" content="{&quot;id&quot;:4,&quot;name&quot;:&quot;admin zenfleet&quot;,&quot;role&quot;:&quot;Admin&quot;}">
 
 <title>Ajouter un Nouveau Véhicule - ZenFleet</title>

 <!-- Fonts -->
 <link rel="preconnect" href="https://fonts.bunny.net/">
 <link href="./AJOUT_VEHICULE_files/css" rel="stylesheet">

 <!-- Iconify CDN -->
 <script src="./AJOUT_VEHICULE_files/iconify.min.js.download"></script>

 <!-- Font Awesome 6 -->
 <link rel="stylesheet" href="./AJOUT_VEHICULE_files/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">

 
 
 
 <link rel="stylesheet" href="./AJOUT_VEHICULE_files/tom-select.css">

 
 <link rel="stylesheet" href="./AJOUT_VEHICULE_files/slimselect.css">

 
 <link rel="stylesheet" href="./AJOUT_VEHICULE_files/flatpickr.min.css">
 <link rel="stylesheet" href="./AJOUT_VEHICULE_files/light.css">
 
 
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

 <link rel="preload" as="style" href="./AJOUT_VEHICULE_files/app-6KyhQxfF.css"><link rel="modulepreload" as="script" href="./AJOUT_VEHICULE_files/app-Pb0zrdAZ.js.download"><link rel="modulepreload" as="script" href="http://localhost/build/assets/vendor-common-B9ygI19o.js"><link rel="modulepreload" as="script" href="http://localhost/build/assets/ui-public-2hikc2V1.js"><link rel="stylesheet" href="./AJOUT_VEHICULE_files/app-6KyhQxfF.css" data-navigate-track="reload"><script type="module" src="./AJOUT_VEHICULE_files/app-Pb0zrdAZ.js.download" data-navigate-track="reload"></script>  <link rel="stylesheet" href="./AJOUT_VEHICULE_files/flatpickr.min.css">
 <link rel="stylesheet" href="./AJOUT_VEHICULE_files/light.css">
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
 <style>
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
</style>    <!-- Livewire Styles --><style>[wire\:loading][wire\:loading], [wire\:loading\.delay][wire\:loading\.delay], [wire\:loading\.inline-block][wire\:loading\.inline-block], [wire\:loading\.inline][wire\:loading\.inline], [wire\:loading\.block][wire\:loading\.block], [wire\:loading\.flex][wire\:loading\.flex], [wire\:loading\.table][wire\:loading\.table], [wire\:loading\.grid][wire\:loading\.grid], [wire\:loading\.inline-flex][wire\:loading\.inline-flex] {display: none;}[wire\:loading\.delay\.none][wire\:loading\.delay\.none], [wire\:loading\.delay\.shortest][wire\:loading\.delay\.shortest], [wire\:loading\.delay\.shorter][wire\:loading\.delay\.shorter], [wire\:loading\.delay\.short][wire\:loading\.delay\.short], [wire\:loading\.delay\.default][wire\:loading\.delay\.default], [wire\:loading\.delay\.long][wire\:loading\.delay\.long], [wire\:loading\.delay\.longer][wire\:loading\.delay\.longer], [wire\:loading\.delay\.longest][wire\:loading\.delay\.longest] {display: none;}[wire\:offline][wire\:offline] {display: none;}[wire\:dirty]:not(textarea):not(input):not(select) {display: none;}:root {--livewire-progress-bar-color: #2299dd;}[x-cloak] {display: none !important;}[wire\:cloak] {display: none !important;}</style>
<style>/* Make clicks pass-through */

    #nprogress {
      pointer-events: none;
    }

    #nprogress .bar {
      background: var(--livewire-progress-bar-color, #29d);

      position: fixed;
      z-index: 1031;
      top: 0;
      left: 0;

      width: 100%;
      height: 2px;
    }

    /* Fancy blur effect */
    #nprogress .peg {
      display: block;
      position: absolute;
      right: 0px;
      width: 100px;
      height: 100%;
      box-shadow: 0 0 10px var(--livewire-progress-bar-color, #29d), 0 0 5px var(--livewire-progress-bar-color, #29d);
      opacity: 1.0;

      -webkit-transform: rotate(3deg) translate(0px, -4px);
          -ms-transform: rotate(3deg) translate(0px, -4px);
              transform: rotate(3deg) translate(0px, -4px);
    }

    /* Remove these to get rid of the spinner */
    #nprogress .spinner {
      display: block;
      position: fixed;
      z-index: 1031;
      top: 15px;
      right: 15px;
    }

    #nprogress .spinner-icon {
      width: 18px;
      height: 18px;
      box-sizing: border-box;

      border: solid 2px transparent;
      border-top-color: var(--livewire-progress-bar-color, #29d);
      border-left-color: var(--livewire-progress-bar-color, #29d);
      border-radius: 50%;

      -webkit-animation: nprogress-spinner 400ms linear infinite;
              animation: nprogress-spinner 400ms linear infinite;
    }

    .nprogress-custom-parent {
      overflow: hidden;
      position: relative;
    }

    .nprogress-custom-parent #nprogress .spinner,
    .nprogress-custom-parent #nprogress .bar {
      position: absolute;
    }

    @-webkit-keyframes nprogress-spinner {
      0%   { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }
    @keyframes nprogress-spinner {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    </style></head>
<body class="h-full">
 <div class="min-h-full">
 
 <div class="hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
 <div class="flex grow flex-col overflow-hidden bg-[#eef2f7] border-r border-gray-200/60 shadow-sm">
 
 <div class="w-full flex-none px-4 py-4 h-16 flex items-center border-b border-gray-300/50">
 <div class="flex items-center w-full">
 <div class="relative mr-3">
 <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-md">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:truck-fast" data-inline="false" class="iconify block w-5 h-5 text-white iconify--mdi"><path fill="currentColor" d="M3 13.5L2.25 12H7.5l-.6-1.5H2L1.25 9h7.8l-.6-1.5H1.11L.25 6H4a2 2 0 0 1 2-2h12v4h3l3 4v5h-2a3 3 0 0 1-3 3a3 3 0 0 1-3-3h-4a3 3 0 0 1-3 3a3 3 0 0 1-3-3H4v-3.5zm16 5a1.5 1.5 0 0 0 1.5-1.5a1.5 1.5 0 0 0-1.5-1.5a1.5 1.5 0 0 0-1.5 1.5a1.5 1.5 0 0 0 1.5 1.5m1.5-9H18V12h4.46zM9 18.5a1.5 1.5 0 0 0 1.5-1.5A1.5 1.5 0 0 0 9 15.5A1.5 1.5 0 0 0 7.5 17A1.5 1.5 0 0 0 9 18.5"></path></svg>
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
  <a href="http://localhost/admin/dashboard" class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="material-symbols:dashboard-rounded" data-inline="false" class="iconify block w-5 h-5 mr-3 text-gray-600 iconify--material-symbols"><path fill="currentColor" d="M14 9q-.425 0-.712-.288T13 8V4q0-.425.288-.712T14 3h6q.425 0 .713.288T21 4v4q0 .425-.288.713T20 9zM4 13q-.425 0-.712-.288T3 12V4q0-.425.288-.712T4 3h6q.425 0 .713.288T11 4v8q0 .425-.288.713T10 13zm10 8q-.425 0-.712-.288T13 20v-8q0-.425.288-.712T14 11h6q.425 0 .713.288T21 12v8q0 .425-.288.713T20 21zM4 21q-.425 0-.712-.288T3 20v-4q0-.425.288-.712T4 15h6q.425 0 .713.288T11 16v4q0 .425-.288.713T10 21z"></path></svg>
 <span class="flex-1">Dashboard</span>
 </a>
 </li>

 
 
 
  <li class="flex flex-col" x-data="{ open: true }">
 <button @click="open = !open" class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-blue-600 text-white shadow-md">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:car-multiple" data-inline="false" class="iconify block w-5 h-5 mr-3 text-white iconify--mdi"><path fill="currentColor" d="m8 11l1.5-4.5h9L20 11m-1.5 5a1.5 1.5 0 0 1-1.5-1.5a1.5 1.5 0 0 1 1.5-1.5a1.5 1.5 0 0 1 1.5 1.5a1.5 1.5 0 0 1-1.5 1.5m-9 0A1.5 1.5 0 0 1 8 14.5A1.5 1.5 0 0 1 9.5 13a1.5 1.5 0 0 1 1.5 1.5A1.5 1.5 0 0 1 9.5 16M19.92 6c-.21-.6-.78-1-1.42-1h-9c-.64 0-1.21.4-1.42 1L6 12v8a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1h10v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-8zm-5-3c-.21-.6-.78-1-1.42-1h-9c-.64 0-1.21.4-1.42 1L1 9v8a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-4.09A1.49 1.49 0 0 1 3.1 11c.22-.6.77-1 1.4-1h.07l.7-2H3l1.5-4.5h10.59z"></path></svg>
 <span class="flex-1 text-left">Véhicules</span>
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" :class="{ &#39;rotate-180&#39;: !open }" data-icon="heroicons:chevron-down" data-inline="false" class="iconify block w-4 h-4 transition-transform duration-200 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m19.5 8.25l-7.5 7.5l-7.5-7.5"></path></svg>
 </button>
 <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
 <div class="flex w-full mt-2 pl-3">
 <div class="mr-1">
 <div class="px-1 py-2 h-full relative">
 <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
  <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300" x-bind:style="`height: 0%; top: 0%;`" style="height: 0%; top: 0%;"></div>
 </div>
 </div>
 <div class="flex-1 min-w-0 space-y-1.5">
  <a href="http://localhost/admin/vehicles" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:format-list-bulleted" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--mdi"><path fill="currentColor" d="M7 5h14v2H7zm0 8v-2h14v2zM4 4.5A1.5 1.5 0 0 1 5.5 6A1.5 1.5 0 0 1 4 7.5A1.5 1.5 0 0 1 2.5 6A1.5 1.5 0 0 1 4 4.5m0 6A1.5 1.5 0 0 1 5.5 12A1.5 1.5 0 0 1 4 13.5A1.5 1.5 0 0 1 2.5 12A1.5 1.5 0 0 1 4 10.5M7 19v-2h14v2zm-3-2.5A1.5 1.5 0 0 1 5.5 18A1.5 1.5 0 0 1 4 19.5A1.5 1.5 0 0 1 2.5 18A1.5 1.5 0 0 1 4 16.5"></path></svg>
 Gestion Véhicules
 </a>
   <a href="http://localhost/admin/assignments" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:clipboard-text" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--mdi"><path fill="currentColor" d="M17 9H7V7h10m0 6H7v-2h10m-3 6H7v-2h7M12 3a1 1 0 0 1 1 1a1 1 0 0 1-1 1a1 1 0 0 1-1-1a1 1 0 0 1 1-1m7 0h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2"></path></svg>
 Affectations
 </a>
  </div>
 </div>
 </div>
 </li>
 
 
  <li class="flex flex-col" x-data="{ open: false }">
 <button @click="open = !open" class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:account-group" data-inline="false" class="iconify block w-5 h-5 mr-3 text-gray-600 iconify--mdi"><path fill="currentColor" d="M12 5.5A3.5 3.5 0 0 1 15.5 9a3.5 3.5 0 0 1-3.5 3.5A3.5 3.5 0 0 1 8.5 9A3.5 3.5 0 0 1 12 5.5M5 8c.56 0 1.08.15 1.53.42c-.15 1.43.27 2.85 1.13 3.96C7.16 13.34 6.16 14 5 14a3 3 0 0 1-3-3a3 3 0 0 1 3-3m14 0a3 3 0 0 1 3 3a3 3 0 0 1-3 3c-1.16 0-2.16-.66-2.66-1.62a5.54 5.54 0 0 0 1.13-3.96c.45-.27.97-.42 1.53-.42M5.5 18.25c0-2.07 2.91-3.75 6.5-3.75s6.5 1.68 6.5 3.75V20h-13zM0 20v-1.5c0-1.39 1.89-2.56 4.45-2.9c-.59.68-.95 1.62-.95 2.65V20zm24 0h-3.5v-1.75c0-1.03-.36-1.97-.95-2.65c2.56.34 4.45 1.51 4.45 2.9z"></path></svg>
 <span class="flex-1 text-left">Chauffeurs</span>
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" :class="{ &#39;rotate-180&#39;: !open }" data-icon="heroicons:chevron-down" data-inline="false" class="iconify block w-4 h-4 transition-transform duration-200 rotate-180 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m19.5 8.25l-7.5 7.5l-7.5-7.5"></path></svg>
 </button>
 <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden" style="display: none;">
 <div class="flex w-full mt-2 pl-3">
 <div class="mr-1">
 <div class="px-1 py-2 h-full relative">
 <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
  <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300" x-bind:style="`height: 0%; top: 0%;`" style="height: 0%; top: 0%;"></div>
 </div>
 </div>
 <div class="flex-1 min-w-0 space-y-1">
  <a href="http://localhost/admin/drivers" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:view-list" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--mdi"><path fill="currentColor" d="M9 5v4h12V5M9 19h12v-4H9m0-1h12v-4H9M4 9h4V5H4m0 14h4v-4H4m0-1h4v-4H4z"></path></svg>
 Liste
 </a>
   <a href="http://localhost/admin/drivers/sanctions" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:gavel" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--mdi"><path fill="currentColor" d="m2.3 20.28l9.6-9.6l-1.4-1.42l-.72.71a.996.996 0 0 1-1.41 0l-.71-.71a.996.996 0 0 1 0-1.41l5.66-5.66a.996.996 0 0 1 1.41 0l.71.71c.39.39.39 1.02 0 1.41l-.71.69l1.42 1.43a.996.996 0 0 1 1.41 0c.39.39.39 1.03 0 1.42l1.41 1.41l.71-.71c.39-.39 1.03-.39 1.42 0l.7.71c.39.39.39 1.03 0 1.42l-5.65 5.65c-.39.39-1.03.39-1.42 0l-.7-.7a.99.99 0 0 1 0-1.42l.7-.71l-1.41-1.41l-9.61 9.61a.996.996 0 0 1-1.41 0c-.39-.39-.39-1.03 0-1.42M20 19a2 2 0 0 1 2 2v1H12v-1a2 2 0 0 1 2-2z"></path></svg>
 Sanctions
 </a>
  </div>
 </div>
 </div>
 </li>
 
 
  <li class="flex">
 <a href="http://localhost/admin/depots" class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:office-building" data-inline="false" class="iconify block w-5 h-5 mr-3 text-gray-600 iconify--mdi"><path fill="currentColor" d="M5 3v18h6v-3.5h2V21h6V3zm2 2h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2zM7 9h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2zm-8 4h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2zm-8 4h2v2H7zm8 0h2v2h-2z"></path></svg>
 <span class="flex-1">Dépôts</span>
 </a>
 </li>
 
 
 
 
  <li class="flex flex-col" x-data="{ open: false }">
 <button @click="open = !open" class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:speedometer" data-inline="false" class="iconify block w-5 h-5 mr-3 text-gray-600 iconify--mdi"><path fill="currentColor" d="M12 16a3 3 0 0 1-3-3c0-1.12.61-2.1 1.5-2.61l9.71-5.62l-5.53 9.58c-.5.98-1.51 1.65-2.68 1.65m0-13c1.81 0 3.5.5 4.97 1.32l-2.1 1.21C14 5.19 13 5 12 5a8 8 0 0 0-8 8c0 2.21.89 4.21 2.34 5.65h.01c.39.39.39 1.02 0 1.41s-1.03.39-1.42.01A9.97 9.97 0 0 1 2 13A10 10 0 0 1 12 3m10 10c0 2.76-1.12 5.26-2.93 7.07c-.39.38-1.02.38-1.41-.01a.996.996 0 0 1 0-1.41A7.95 7.95 0 0 0 20 13c0-1-.19-2-.54-2.9L20.67 8C21.5 9.5 22 11.18 22 13"></path></svg>
 <span class="flex-1 text-left">Kilométrage</span>
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" :class="{ &#39;rotate-180&#39;: !open }" data-icon="heroicons:chevron-down" data-inline="false" class="iconify block w-4 h-4 transition-transform duration-200 rotate-180 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m19.5 8.25l-7.5 7.5l-7.5-7.5"></path></svg>
 </button>
 <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden" style="display: none;">
 <div class="flex w-full mt-2 pl-3">
 <div class="mr-1">
 <div class="px-1 py-2 h-full relative">
 <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
  <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300 h-1/2" x-bind:style="`top: 0%;`" style="top: 0%;"></div>
 </div>
 </div>
 <ul class="flex-1 space-y-1 pb-2">
 
 <li>
  <a href="http://localhost/admin/mileage-readings" class="flex items-center h-9 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:history" data-inline="false" class="iconify block w-5 h-5 mr-2 text-gray-600 iconify--mdi"><path fill="currentColor" d="M13.5 8H12v5l4.28 2.54l.72-1.21l-3.5-2.08zM13 3a9 9 0 0 0-9 9H1l3.96 4.03L9 12H6a7 7 0 0 1 7-7a7 7 0 0 1 7 7a7 7 0 0 1-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42A8.9 8.9 0 0 0 13 21a9 9 0 0 0 9-9a9 9 0 0 0-9-9"></path></svg>
 Historique
 </a>
 </li>
 
  <li>
  <a href="http://localhost/admin/mileage-readings/update" class="flex items-center h-9 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:pencil" data-inline="false" class="iconify block w-5 h-5 mr-2 text-gray-600 iconify--mdi"><path fill="currentColor" d="M20.71 7.04c.39-.39.39-1.04 0-1.41l-2.34-2.34c-.37-.39-1.02-.39-1.41 0l-1.84 1.83l3.75 3.75M3 17.25V21h3.75L17.81 9.93l-3.75-3.75z"></path></svg>
 Mettre à jour
 </a>
 </li>
  </ul>
 </div>
 </div>
 </li>
 
 
  <li class="flex flex-col" x-data="{ open: false }">
 <button @click="open = !open" class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:wrench" data-inline="false" class="iconify block w-5 h-5 mr-3 text-gray-600 iconify--lucide"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.106-3.105c.32-.322.863-.22.983.218a6 6 0 0 1-8.259 7.057l-7.91 7.91a1 1 0 0 1-2.999-3l7.91-7.91a6 6 0 0 1 7.057-8.259c.438.12.54.662.219.984z"></path></svg>
 <span class="flex-1 text-left">Maintenance</span>
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" :class="{ &#39;rotate-180&#39;: !open }" data-icon="lucide:chevron-down" data-inline="false" class="iconify block w-4 h-4 transition-transform duration-200 rotate-180 iconify--lucide"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9l6 6l6-6"></path></svg>
 </button>
 <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[500px]" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-[500px]" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden" style="display: none;">
 <div class="flex w-full mt-2 pl-3">
 <div class="mr-1">
 <div class="px-1 py-2 h-full relative">
 <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
  <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300" x-bind:style="`height: 0%; top: 0%;`" style="height: 0%; top: 0%;"></div>
 </div>
 </div>
 <div class="flex-1 min-w-0 space-y-1">
 
 <a href="http://localhost/admin/maintenance/dashboard" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:layout-dashboard" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"></rect><rect width="7" height="5" x="14" y="3" rx="1"></rect><rect width="7" height="9" x="14" y="12" rx="1"></rect><rect width="7" height="5" x="3" y="16" rx="1"></rect></g></svg>
 Vue d'ensemble
 </a>

 
 <a href="http://localhost/admin/maintenance/operations" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:list" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--lucide"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h.01M3 12h.01M3 19h.01M8 5h13M8 12h13M8 19h13"></path></svg>
 Opérations
 </a>

 
 <a href="http://localhost/admin/maintenance/operations/kanban" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:columns-3" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"></rect><path d="M9 3v18m6-18v18"></path></g></svg>
 Kanban
 </a>

 
 <a href="http://localhost/admin/maintenance/operations/calendar" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:calendar-days" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M8 2v4m8-4v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"></path></g></svg>
 Calendrier
 </a>

 
 <a href="http://localhost/admin/maintenance/schedules" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:repeat" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="m17 2l4 4l-4 4"></path><path d="M3 11v-1a4 4 0 0 1 4-4h14M7 22l-4-4l4-4"></path><path d="M21 13v1a4 4 0 0 1-4 4H3"></path></g></svg>
 Planifications
 </a>

 
  <a href="http://localhost/admin/repair-requests" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:hammer" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="m15 12l-9.373 9.373a1 1 0 0 1-3.001-3L12 9m6 6l4-4"></path><path d="m21.5 11.5l-1.914-1.914A2 2 0 0 1 19 8.172v-.344a2 2 0 0 0-.586-1.414l-1.657-1.657A6 6 0 0 0 12.516 3H9l1.243 1.243A6 6 0 0 1 12 8.485V10l2 2h1.172a2 2 0 0 1 1.414.586L18.5 14.5"></path></g></svg>
 Demandes Réparation
 </a>
  </div>
 </div>
 </div>
 </li>
 
 
  <li class="flex">
 <a href="http://localhost/admin/alerts" class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:bell-ring" data-inline="false" class="iconify block w-5 h-5 mr-3 text-gray-600 iconify--mdi"><path fill="currentColor" d="M21 19v1H3v-1l2-2v-6c0-3.1 2.03-5.83 5-6.71V4a2 2 0 0 1 2-2a2 2 0 0 1 2 2v.29c2.97.88 5 3.61 5 6.71v6zm-7 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2m9.75-17.81l-1.42 1.42A8.98 8.98 0 0 1 21 11h2c0-2.93-1.16-5.75-3.25-7.81M1 11h2c0-2.4.96-4.7 2.67-6.39L4.25 3.19A10.96 10.96 0 0 0 1 11"></path></svg>
 <span class="flex-1">Alertes</span>
 </a>
 </li>
 
 
  <li class="flex">
 <a href="http://localhost/admin/documents" class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:file-document" data-inline="false" class="iconify block w-5 h-5 mr-3 text-gray-600 iconify--mdi"><path fill="currentColor" d="M13 9h5.5L13 3.5zM6 2h8l6 6v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4c0-1.11.89-2 2-2m9 16v-2H6v2zm3-4v-2H6v2z"></path></svg>
 <span class="flex-1">Documents</span>
 </a>
 </li>
 
 
  <li class="flex">
 <a href="http://localhost/admin/suppliers" class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:store" data-inline="false" class="iconify block w-5 h-5 mr-3 text-gray-600 iconify--mdi"><path fill="currentColor" d="M12 18H6v-4h6m9 0v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6m0-10H4v2h16z"></path></svg>
 <span class="flex-1">Fournisseurs</span>
 </a>
 </li>
 
 
  <li class="flex flex-col" x-data="{ open: false }">
 <button @click="open = !open" class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="solar:wallet-money-bold" data-inline="false" class="iconify block w-5 h-5 mr-3 text-gray-600 iconify--solar"><path fill="currentColor" fill-rule="evenodd" d="M20.41 9.86a3 3 0 0 0-.175-.003H17.8c-1.992 0-3.698 1.581-3.698 3.643s1.706 3.643 3.699 3.643h2.433q.092.001.175-.004a1.7 1.7 0 0 0 1.586-1.581c.004-.059.004-.122.004-.18v-3.756c0-.058 0-.121-.004-.18a1.7 1.7 0 0 0-1.585-1.581m-2.823 4.611c.513 0 .93-.434.93-.971s-.417-.971-.93-.971s-.929.434-.929.971s.416.971.93.971" clip-rule="evenodd"></path><path fill="currentColor" fill-rule="evenodd" d="M20.234 18.6a.214.214 0 0 1 .214.27c-.194.692-.501 1.282-.994 1.778c-.721.727-1.636 1.05-2.766 1.203c-1.098.149-2.5.149-4.272.149h-2.037c-1.771 0-3.174 0-4.272-.149c-1.13-.153-2.045-.476-2.766-1.203C2.62 19.923 2.3 19 2.148 17.862C2 16.754 2 15.34 2 13.555v-.11c0-1.785 0-3.2.148-4.306C2.3 8 2.62 7.08 3.34 6.351c.721-.726 1.636-1.05 2.766-1.202C7.205 5 8.608 5 10.379 5h2.037c1.771 0 3.174 0 4.272.149c1.13.153 2.045.476 2.766 1.202c.493.497.8 1.087.994 1.78a.214.214 0 0 1-.214.269h-2.433c-2.734 0-5.143 2.177-5.143 5.1s2.41 5.1 5.144 5.1zM5.614 8.886a.725.725 0 0 0-.722.728c0 .403.323.729.722.729H9.47c.4 0 .723-.326.723-.729a.726.726 0 0 0-.723-.728z" clip-rule="evenodd"></path><path fill="currentColor" d="m7.777 4.024l1.958-1.443a2.97 2.97 0 0 1 3.53 0l1.969 1.451C14.41 4 13.49 4 12.483 4h-2.17c-.922 0-1.769 0-2.536.024"></path></svg>
 <span class="flex-1 text-left">Dépenses</span>
   <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" :class="{ &#39;rotate-180&#39;: !open }" data-icon="lucide:chevron-down" data-inline="false" class="iconify block w-4 h-4 transition-transform duration-200 rotate-180 iconify--lucide"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9l6 6l6-6"></path></svg>
 </button>
 <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-[400px]" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-[400px]" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden" style="display: none;">
 <div class="flex w-full mt-2 pl-3">
 <div class="mr-1">
 <div class="px-1 py-2 h-full relative">
 <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
  <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300" x-bind:style="`height: 0%; top: 0%;`" style="height: 0%; top: 0%;"></div>
 </div>
 </div>
 <div class="flex-1 min-w-0 space-y-1">
 
 <a href="http://localhost/admin/vehicle-expenses" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:layout-dashboard" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"></rect><rect width="7" height="5" x="14" y="3" rx="1"></rect><rect width="7" height="9" x="14" y="12" rx="1"></rect><rect width="7" height="5" x="3" y="16" rx="1"></rect></g></svg>
 Tableau de bord
 </a>

 
  <a href="http://localhost/admin/vehicle-expenses/create" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:plus-circle" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M8 12h8m-4-4v8"></path></g></svg>
 Nouvelle dépense
 </a>
 
 
  <a href="http://localhost/admin/vehicle-expenses/dashboard" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:chart-line" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M3 3v16a2 2 0 0 0 2 2h16"></path><path d="m19 9l-5 5l-4-4l-3 3"></path></g></svg>
 Analytics
 </a>
 
 
  <a href="http://localhost/admin/vehicle-expenses?filter=pending_approval" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:clock" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M12 6v6l4 2"></path><circle cx="12" cy="12" r="10"></circle></g></svg>
 Approbations
  </a>
 
 
 <a href="http://localhost/admin/vehicle-expenses?section=groups" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:wallet" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"></path><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"></path></g></svg>
 Budgets
 </a>

 
  <a href="http://localhost/admin/vehicle-expenses/export" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:download" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M12 15V3m9 12v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="m7 10l5 5l5-5"></path></g></svg>
 Export
 </a>
 
 
  <a href="http://localhost/admin/vehicle-expenses/analytics/cost-trends" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:trending-up" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M16 7h6v6"></path><path d="m22 7l-8.5 8.5l-5-5L2 17"></path></g></svg>
 TCO &amp; Tendances
 </a>
  </div>
 </div>
 </div>
 </li>
 
 
  <li class="flex">
 <a href="http://localhost/admin/reports" class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:chart-bar" data-inline="false" class="iconify block w-5 h-5 mr-3 text-gray-600 iconify--mdi"><path fill="currentColor" d="M22 21H2V3h2v16h2v-9h4v9h2V6h4v13h2v-5h4z"></path></svg>
 <span class="flex-1">Rapports</span>
 </a>
 </li>
 
 
  <li class="flex flex-col" x-data="{ open: false }">
 <button @click="open = !open" class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:cog" data-inline="false" class="iconify block w-5 h-5 mr-3 text-gray-600 iconify--mdi"><path fill="currentColor" d="M12 15.5A3.5 3.5 0 0 1 8.5 12A3.5 3.5 0 0 1 12 8.5a3.5 3.5 0 0 1 3.5 3.5a3.5 3.5 0 0 1-3.5 3.5m7.43-2.53c.04-.32.07-.64.07-.97s-.03-.66-.07-1l2.11-1.63c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.31-.61-.22l-2.49 1c-.52-.39-1.06-.73-1.69-.98l-.37-2.65A.506.506 0 0 0 14 2h-4c-.25 0-.46.18-.5.42l-.37 2.65c-.63.25-1.17.59-1.69.98l-2.49-1c-.22-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64L4.57 11c-.04.34-.07.67-.07 1s.03.65.07.97l-2.11 1.66c-.19.15-.25.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1.01c.52.4 1.06.74 1.69.99l.37 2.65c.04.24.25.42.5.42h4c.25 0 .46-.18.5-.42l.37-2.65c.63-.26 1.17-.59 1.69-.99l2.49 1.01c.22.08.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64z"></path></svg>
 <span class="flex-1 text-left">Administration</span>
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" :class="{ &#39;rotate-180&#39;: !open }" data-icon="heroicons:chevron-down" data-inline="false" class="iconify block w-4 h-4 transition-transform duration-200 rotate-180 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m19.5 8.25l-7.5 7.5l-7.5-7.5"></path></svg>
 </button>
 <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden" style="display: none;">
 <div class="flex w-full mt-2 pl-3">
 <div class="mr-1">
 <div class="px-1 py-2 h-full relative">
 <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
  <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300" x-bind:style="`height: 0%; top: 0%;`" style="height: 0%; top: 0%;"></div>
 </div>
 </div>
 <div class="flex-1 min-w-0 space-y-1">
  <a href="http://localhost/admin/users" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:account-multiple" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--mdi"><path fill="currentColor" d="M16 17v2H2v-2s0-4 7-4s7 4 7 4m-3.5-9.5A3.5 3.5 0 1 0 9 11a3.5 3.5 0 0 0 3.5-3.5m3.44 5.5A5.32 5.32 0 0 1 18 17v2h4v-2s0-3.63-6.06-4M15 4a3.4 3.4 0 0 0-1.93.59a5 5 0 0 1 0 5.82A3.4 3.4 0 0 0 15 11a3.5 3.5 0 0 0 0-7"></path></svg>
 Utilisateurs
 </a>
   <a href="http://localhost/admin/roles" class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-white/70 hover:text-gray-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:shield-check" data-inline="false" class="iconify block w-4 h-4 mr-2.5 text-gray-600 iconify--mdi"><path fill="currentColor" d="m10 17l-4-4l1.41-1.41L10 14.17l6.59-6.59L18 9m-6-8L3 5v6c0 5.55 3.84 10.74 9 12c5.16-1.26 9-6.45 9-12V5z"></path></svg>
 Rôles &amp; Permissions
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
 
 <div x-show="open" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="relative z-50 lg:hidden" style="display: none;">
 <div class="fixed inset-0 bg-gray-900/80" @click="open = false"></div>

 <div class="fixed inset-0 flex">
 <div x-show="open" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative mr-16 flex w-full max-w-xs flex-1" style="display: none;">
 
 <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-zinc-50 px-6 pb-4">
 
 <div class="flex h-16 shrink-0 items-center">
 <div class="flex items-center">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:truck" data-inline="false" class="iconify block w-6 h-6 text-zinc-900 mr-3 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.9 17.9 0 0 0-3.213-9.193a2.06 2.06 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.6 48.6 0 0 0-10.026 0a1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"></path></svg>
 <span class="text-zinc-900 text-xl font-bold">ZenFleet</span>
 </div>
 </div>

 
 <nav class="flex flex-1 flex-col">
 <ul role="list" class="flex flex-1 flex-col gap-y-2">
 <li>
 <ul role="list" class="-mx-2 space-y-1">
 
 <li>
  <a href="http://localhost/admin/dashboard" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:home" data-inline="false" class="iconify block h-5 w-5 shrink-0 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 12l8.955-8.955a1.124 1.124 0 0 1 1.59 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"></path></svg>
 Dashboard
 </a>
 </li>

 
 
 
  <li x-data="{ open: true }">
 <button @click="open = !open" class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold bg-zinc-950 text-white">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:truck" data-inline="false" class="iconify block h-5 w-5 shrink-0 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.9 17.9 0 0 0-3.213-9.193a2.06 2.06 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.6 48.6 0 0 0-10.026 0a1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"></path></svg>
 <span class="flex-1 text-left">Véhicules</span>
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" :class="{ &#39;rotate-90&#39;: open }" data-icon="heroicons:chevron-right" data-inline="false" class="iconify block h-4 w-4 transition-transform rotate-90 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m8.25 4.5l7.5 7.5l-7.5 7.5"></path></svg>
 </button>
 <div x-show="open" x-transition="" class="mt-1">
 <ul class="ml-6 space-y-1">
 <li class="relative">
 <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
 <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
 <a href="http://localhost/admin/vehicles" class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium bg-zinc-100 text-zinc-900">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:truck" data-inline="false" class="iconify block h-4 w-4 shrink-0 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.9 17.9 0 0 0-3.213-9.193a2.06 2.06 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.6 48.6 0 0 0-10.026 0a1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"></path></svg>
 Gestion Véhicules
 </a>
 </li>
 <li class="relative">
 <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
 <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
 <a href="http://localhost/admin/assignments" class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:clipboard-document-list" data-inline="false" class="iconify block h-4 w-4 shrink-0 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48 48 0 0 0-1.123-.08m-5.801 0q-.099.316-.1.664c0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75a2.3 2.3 0 0 0-.1-.664m-5.8 0A2.25 2.25 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0q-.563.035-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125zM6.75 12h.008v.008H6.75zm0 3h.008v.008H6.75zm0 3h.008v.008H6.75z"></path></svg>
 Affectations
 </a>
 </li>
 </ul>
 </div>
 </li>
 
 
  <li>
 <a href="http://localhost/admin/drivers" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:user" data-inline="false" class="iconify block h-5 w-5 shrink-0 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 1 1-7.5 0a3.75 3.75 0 0 1 7.5 0M4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.9 17.9 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632"></path></svg>
 Chauffeurs
 </a>
 </li>
 
 
  <li>
 <a href="http://localhost/admin/depots" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:office-building" data-inline="false" class="iconify block h-5 w-5 shrink-0 iconify--mdi"><path fill="currentColor" d="M5 3v18h6v-3.5h2V21h6V3zm2 2h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2zM7 9h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2zm-8 4h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2zm-8 4h2v2H7zm8 0h2v2h-2z"></path></svg>
 Dépôts
 </a>
 </li>
 
 
  <li x-data="{ open: false }">
 <button @click="open = !open" class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:cog" data-inline="false" class="iconify block h-5 w-5 shrink-0 iconify--mdi"><path fill="currentColor" d="M12 15.5A3.5 3.5 0 0 1 8.5 12A3.5 3.5 0 0 1 12 8.5a3.5 3.5 0 0 1 3.5 3.5a3.5 3.5 0 0 1-3.5 3.5m7.43-2.53c.04-.32.07-.64.07-.97s-.03-.66-.07-1l2.11-1.63c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.31-.61-.22l-2.49 1c-.52-.39-1.06-.73-1.69-.98l-.37-2.65A.506.506 0 0 0 14 2h-4c-.25 0-.46.18-.5.42l-.37 2.65c-.63.25-1.17.59-1.69.98l-2.49-1c-.22-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64L4.57 11c-.04.34-.07.67-.07 1s.03.65.07.97l-2.11 1.66c-.19.15-.25.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1.01c.52.4 1.06.74 1.69.99l.37 2.65c.04.24.25.42.5.42h4c.25 0 .46-.18.5-.42l.37-2.65c.63-.26 1.17-.59 1.69-.99l2.49 1.01c.22.08.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64z"></path></svg>
 <span class="flex-1 text-left">Administration</span>
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" :class="{ &#39;rotate-90&#39;: open }" data-icon="heroicons:chevron-right" data-inline="false" class="iconify block h-4 w-4 transition-transform iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m8.25 4.5l7.5 7.5l-7.5 7.5"></path></svg>
 </button>
 <div x-show="open" x-transition="" class="mt-1" style="display: none;">
 <ul class="ml-6 space-y-1">
 <li class="relative">
 <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
 <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
 <a href="http://localhost/admin/users" class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:account-multiple" data-inline="false" class="iconify block h-4 w-4 shrink-0 iconify--mdi"><path fill="currentColor" d="M16 17v2H2v-2s0-4 7-4s7 4 7 4m-3.5-9.5A3.5 3.5 0 1 0 9 11a3.5 3.5 0 0 0 3.5-3.5m3.44 5.5A5.32 5.32 0 0 1 18 17v2h4v-2s0-3.63-6.06-4M15 4a3.4 3.4 0 0 0-1.93.59a5 5 0 0 1 0 5.82A3.4 3.4 0 0 0 15 11a3.5 3.5 0 0 0 0-7"></path></svg>
 Utilisateurs
 </a>
 </li>
 <li class="relative">
 <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
 <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
 <a href="http://localhost/admin/roles" class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:shield-check" data-inline="false" class="iconify block h-4 w-4 shrink-0 iconify--mdi"><path fill="currentColor" d="m10 17l-4-4l1.41-1.41L10 14.17l6.59-6.59L18 9m-6-8L3 5v6c0 5.55 3.84 10.74 9 12c5.16-1.26 9-6.45 9-12V5z"></path></svg>
 Rôles &amp; Permissions
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
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:bars-3" data-inline="false" class="iconify block h-6 w-6 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path></svg>
 </button>
 <div class="flex-1 text-sm font-semibold leading-6 text-zinc-900">ZenFleet</div>
 <div class="h-8 w-8 bg-zinc-100 rounded-full flex items-center justify-center">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:user" data-inline="false" class="iconify block h-4 w-4 text-zinc-500 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 1 1-7.5 0a3.75 3.75 0 0 1 7.5 0M4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.9 17.9 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632"></path></svg>
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
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:magnifying-glass" data-inline="false" class="iconify block h-4 w-4 text-zinc-400 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607"></path></svg>
 </div>
 <input type="search" placeholder="Rechercher..." class="block w-64 rounded-md border-0 bg-white py-1.5 pl-10 pr-3 text-zinc-900 ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-zinc-600 sm:text-sm sm:leading-6">
 </div>

 
 <div class="relative">
 <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600 relative">
 <span class="sr-only">Voir les notifications</span>
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:bell-ring" data-inline="false" class="iconify block h-6 w-6 iconify--mdi"><path fill="currentColor" d="M21 19v1H3v-1l2-2v-6c0-3.1 2.03-5.83 5-6.71V4a2 2 0 0 1 2-2a2 2 0 0 1 2 2v.29c2.97.88 5 3.61 5 6.71v6zm-7 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2m9.75-17.81l-1.42 1.42A8.98 8.98 0 0 1 21 11h2c0-2.93-1.16-5.75-3.25-7.81M1 11h2c0-2.4.96-4.7 2.67-6.39L4.25 3.19A10.96 10.96 0 0 0 1 11"></path></svg>
 <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
 </button>
 </div>

 
 <div class="relative">
 <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600 relative">
 <span class="sr-only">Messages</span>
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:envelope" data-inline="false" class="iconify block h-6 w-6 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"></path></svg>
 <span class="absolute -top-1 -right-1 h-4 w-4 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center">2</span>
 </button>
 </div>

 
 <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600">
 <span class="sr-only">Basculer le mode sombre</span>
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:moon" data-inline="false" class="iconify block h-6 w-6 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.752 15.002A9.7 9.7 0 0 1 18 15.75A9.75 9.75 0 0 1 8.25 6c0-1.33.266-2.597.748-3.752A9.75 9.75 0 0 0 3 11.25A9.75 9.75 0 0 0 12.75 21a9.75 9.75 0 0 0 9.002-5.998"></path></svg>
 </button>

 
 <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-zinc-200" aria-hidden="true"></div>

 
 <div class="relative" x-data="{ open: false }">
 <button type="button" @click="open = !open" class="-m-1.5 flex items-center p-1.5 hover:bg-zinc-50 rounded-lg transition-colors">
 <span class="sr-only">Ouvrir le menu utilisateur</span>
 <div class="h-8 w-8 bg-gradient-to-br from-zinc-600 to-zinc-800 rounded-full flex items-center justify-center">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:user" data-inline="false" class="iconify block text-white w-4 h-4 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 1 1-7.5 0a3.75 3.75 0 0 1 7.5 0M4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.9 17.9 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632"></path></svg>
 </div>
 <span class="hidden lg:flex lg:items-center">
 <div class="ml-3 text-left">
 <div class="text-sm font-semibold leading-5 text-zinc-900">admin zenfleet</div>
 <div class="text-xs leading-4 text-zinc-500">Admin</div>
 </div>
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" :class="{ &#39;rotate-180&#39;: open }" data-icon="heroicons:chevron-down" data-inline="false" class="iconify block ml-2 h-4 w-4 text-zinc-500 transition-transform iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m19.5 8.25l-7.5 7.5l-7.5-7.5"></path></svg>
 </span>
 </button>

 <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2.5 w-56 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-zinc-900/5" style="display: none;">

 
 <div class="px-4 py-3 border-b border-zinc-100">
 <div class="flex items-center">
 <div class="h-10 w-10 bg-gradient-to-br from-zinc-600 to-zinc-800 rounded-full flex items-center justify-center">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:user" data-inline="false" class="iconify block text-white w-5 h-5 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 1 1-7.5 0a3.75 3.75 0 0 1 7.5 0M4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.9 17.9 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632"></path></svg>
 </div>
 <div class="ml-3">
 <div class="text-sm font-medium text-zinc-900">admin zenfleet</div>
 <div class="text-xs text-zinc-500">admin@zenfleet.dz</div>
 </div>
 </div>
 </div>

 
 <div class="py-1">
 <a href="http://localhost/profile" class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:user-circle" data-inline="false" class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.982 18.725A7.49 7.49 0 0 0 12 15.75a7.49 7.49 0 0 0-5.982 2.975m11.964 0a9 9 0 1 0-11.963 0m11.962 0A8.97 8.97 0 0 1 12 21a8.97 8.97 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0a3 3 0 0 1 6 0"></path></svg>
 Mon Profil
 </a>
 <a href="http://localhost/admin/vehicles/create#" class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="mdi:cog" data-inline="false" class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600 iconify--mdi"><path fill="currentColor" d="M12 15.5A3.5 3.5 0 0 1 8.5 12A3.5 3.5 0 0 1 12 8.5a3.5 3.5 0 0 1 3.5 3.5a3.5 3.5 0 0 1-3.5 3.5m7.43-2.53c.04-.32.07-.64.07-.97s-.03-.66-.07-1l2.11-1.63c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.31-.61-.22l-2.49 1c-.52-.39-1.06-.73-1.69-.98l-.37-2.65A.506.506 0 0 0 14 2h-4c-.25 0-.46.18-.5.42l-.37 2.65c-.63.25-1.17.59-1.69.98l-2.49-1c-.22-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64L4.57 11c-.04.34-.07.67-.07 1s.03.65.07.97l-2.11 1.66c-.19.15-.25.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1.01c.52.4 1.06.74 1.69.99l.37 2.65c.04.24.25.42.5.42h4c.25 0 .46-.18.5-.42l.37-2.65c.63-.26 1.17-.59 1.69-.99l2.49 1.01c.22.08.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64z"></path></svg>
 Paramètres
 </a>
 <a href="http://localhost/admin/vehicles/create#" class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:question-mark-circle" data-inline="false" class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.879 7.519c1.172-1.025 3.071-1.025 4.243 0c1.171 1.025 1.171 2.687 0 3.712q-.308.268-.67.442c-.746.361-1.452.999-1.452 1.827v.75M21 12a9 9 0 1 1-18 0a9 9 0 0 1 18 0m-9 5.25h.008v.008H12z"></path></svg>
 Aide &amp; Support
 </a>
 <div class="border-t border-zinc-100 my-1"></div>
 <form method="POST" action="http://localhost/logout">
 <input type="hidden" name="_token" value="ibkA7bKbNTVfvGQNnAhqpGAYV0nBCkXPfNQTNxxU" autocomplete="off"> <button type="submit" class="group flex w-full items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:arrow-right-on-rectangle" data-inline="false" class="iconify block mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"></path></svg>
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
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:truck" data-inline="false" class="iconify block w-6 h-6 text-blue-600 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.9 17.9 0 0 0-3.213-9.193a2.06 2.06 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.6 48.6 0 0 0-10.026 0a1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"></path></svg>
                Ajouter un Nouveau Véhicule
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Complétez les 3 étapes pour enregistrer un véhicule
            </p>
        </div>

        
        
        
        <div x-data="vehicleFormValidation()" x-init="init()">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-0 mb-6">
 <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

 <div class="w-full bg-white border-b border-gray-200 py-8">
    <div class="px-4 mx-auto">
        <ol class="flex items-start justify-center gap-0 w-full max-w-4xl mx-auto">
                            
                <li class="flex flex-col items-center relative flex-1">

                    
                    <div class="flex items-center w-full relative z-10">

                        
                        <div class="flex items-center justify-center w-12 h-12 rounded-full flex-shrink-0 bg-white border-2 transition-all duration-300 relative" x-bind:class="{
                                &#39;border-blue-600 shadow-lg shadow-blue-500/40&#39;: currentStep === 1,
                                &#39;border-blue-600 shadow-md shadow-blue-500/20&#39;: currentStep &gt; 1,
                                &#39;border-gray-300 shadow-sm&#39;: currentStep &lt; 1
                            }">

                            
                            <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" x-bind:class="{
                                    &#39;text-gray-400&#39;: currentStep === 1,      
                                    &#39;text-blue-600&#39;: currentStep &gt; 1,   
                                    &#39;text-gray-300&#39;: currentStep &lt; 1       
                                }" x-bind:data-icon="&#39;lucide:&#39; + &quot;file-text&quot;" data-inline="false" data-icon="lucide:file-text" class="iconify w-6 h-6 transition-colors duration-300 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"></path><path d="M14 2v5a1 1 0 0 0 1 1h5M10 9H8m8 4H8m8 4H8"></path></g></svg>
                        </div>

                        
                                                    <div class="flex-1 h-1 mx-2 rounded-full transition-all duration-300" x-bind:class="currentStep &gt; 1 ? &#39;bg-blue-600 shadow-sm&#39; : &#39;bg-gray-300&#39;">
                            </div>
                        
                    </div>

                    
                    <span class="mt-4 text-center text-sm font-semibold transition-all duration-200 whitespace-nowrap leading-snug" x-bind:class="{
                            &#39;text-blue-600 font-bold text-sm&#39;: currentStep === 1,
                            &#39;text-blue-600 font-semibold&#39;: currentStep &gt; 1,
                            &#39;text-gray-500&#39;: currentStep &lt; 1
                        }">
                        Identification
                    </span>

                </li>
                            
                <li class="flex flex-col items-center relative flex-1">

                    
                    <div class="flex items-center w-full relative z-10">

                        
                        <div class="flex items-center justify-center w-12 h-12 rounded-full flex-shrink-0 bg-white border-2 transition-all duration-300 relative" x-bind:class="{
                                &#39;border-blue-600 shadow-lg shadow-blue-500/40&#39;: currentStep === 2,
                                &#39;border-blue-600 shadow-md shadow-blue-500/20&#39;: currentStep &gt; 2,
                                &#39;border-gray-300 shadow-sm&#39;: currentStep &lt; 2
                            }">

                            
                            <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" x-bind:class="{
                                    &#39;text-gray-400&#39;: currentStep === 2,      
                                    &#39;text-blue-600&#39;: currentStep &gt; 2,   
                                    &#39;text-gray-300&#39;: currentStep &lt; 2       
                                }" x-bind:data-icon="&#39;lucide:&#39; + &quot;settings&quot;" data-inline="false" data-icon="lucide:settings" class="iconify w-6 h-6 transition-colors duration-300 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0a2.34 2.34 0 0 0 3.319 1.915a2.34 2.34 0 0 1 2.33 4.033a2.34 2.34 0 0 0 0 3.831a2.34 2.34 0 0 1-2.33 4.033a2.34 2.34 0 0 0-3.319 1.915a2.34 2.34 0 0 1-4.659 0a2.34 2.34 0 0 0-3.32-1.915a2.34 2.34 0 0 1-2.33-4.033a2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915"></path><circle cx="12" cy="12" r="3"></circle></g></svg>
                        </div>

                        
                                                    <div class="flex-1 h-1 mx-2 rounded-full transition-all duration-300" x-bind:class="currentStep &gt; 2 ? &#39;bg-blue-600 shadow-sm&#39; : &#39;bg-gray-300&#39;">
                            </div>
                        
                    </div>

                    
                    <span class="mt-4 text-center text-sm font-semibold transition-all duration-200 whitespace-nowrap leading-snug" x-bind:class="{
                            &#39;text-blue-600 font-bold text-sm&#39;: currentStep === 2,
                            &#39;text-blue-600 font-semibold&#39;: currentStep &gt; 2,
                            &#39;text-gray-500&#39;: currentStep &lt; 2
                        }">
                        Caractéristiques
                    </span>

                </li>
                            
                <li class="flex flex-col items-center relative flex-none">

                    
                    <div class="flex items-center w-full relative z-10">

                        
                        <div class="flex items-center justify-center w-12 h-12 rounded-full flex-shrink-0 bg-white border-2 transition-all duration-300 relative" x-bind:class="{
                                &#39;border-blue-600 shadow-lg shadow-blue-500/40&#39;: currentStep === 3,
                                &#39;border-blue-600 shadow-md shadow-blue-500/20&#39;: currentStep &gt; 3,
                                &#39;border-gray-300 shadow-sm&#39;: currentStep &lt; 3
                            }">

                            
                            <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" x-bind:class="{
                                    &#39;text-gray-400&#39;: currentStep === 3,      
                                    &#39;text-blue-600&#39;: currentStep &gt; 3,   
                                    &#39;text-gray-300&#39;: currentStep &lt; 3       
                                }" x-bind:data-icon="&#39;lucide:&#39; + &quot;receipt&quot;" data-inline="false" data-icon="lucide:receipt" class="iconify w-6 h-6 transition-colors duration-300 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M4 2v20l2-1l2 1l2-1l2 1l2-1l2 1l2-1l2 1V2l-2 1l-2-1l-2 1l-2-1l-2 1l-2-1l-2 1Z"></path><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8m4 1.5v-11"></path></g></svg>
                        </div>

                        
                        
                    </div>

                    
                    <span class="mt-4 text-center text-sm font-semibold transition-all duration-200 whitespace-nowrap leading-snug" x-bind:class="{
                            &#39;text-blue-600 font-bold text-sm&#39;: currentStep === 3,
                            &#39;text-blue-600 font-semibold&#39;: currentStep &gt; 3,
                            &#39;text-gray-500&#39;: currentStep &lt; 3
                        }">
                        Acquisition
                    </span>

                </li>
                    </ol>
    </div>
</div>

                
                <form method="POST" action="http://localhost/admin/vehicles" @submit="onSubmit" class="p-6">
                    <input type="hidden" name="_token" value="ibkA7bKbNTVfvGQNnAhqpGAYV0nBCkXPfNQTNxxU" autocomplete="off">                    <input type="hidden" name="current_step" x-model="currentStep" value="">

                    
                    <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" style="display: none;">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:identification" data-inline="false" class="iconify block w-5 h-5 text-blue-600 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5m6-10.125a1.875 1.875 0 1 1-3.75 0a1.875 1.875 0 0 1 3.75 0m1.294 6.336a6.7 6.7 0 0 1-3.17.789a6.7 6.7 0 0 1-3.168-.789a3.376 3.376 0 0 1 6.338 0"></path></svg>
                                    Informations d'Identification
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="" @blur="validateField(&#39;registration_plate&#39;, $event.target.value)">
  <label for="registration_plate" class="block mb-2 text-sm font-medium text-gray-900">
 Immatriculation
  <span class="text-red-600">*</span>
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:identification" data-inline="false" class="iconify block w-5 h-5 text-gray-400 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5m6-10.125a1.875 1.875 0 1 1-3.75 0a1.875 1.875 0 0 1 3.75 0m1.294 6.336a6.7 6.7 0 0 1-3.17.789a6.7 6.7 0 0 1-3.168-.789a3.376 3.376 0 0 1 6.338 0"></path></svg>
 </div>
 
 <input type="text" name="registration_plate" id="registration_plate" class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pl-10" placeholder="Ex: 16-12345-23" value="" required="" x-bind:class="(fieldErrors &amp;&amp; fieldErrors[&#39;registration_plate&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;registration_plate&#39;]) ? &#39;!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50&#39; : &#39;&#39;" @blur="validateField(&#39;registration_plate&#39;, $event.target.value)">
 </div>

  <p class="mt-2 text-sm text-gray-600">
 Numéro d'immatriculation officiel du véhicule
 </p>
 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;registration_plate&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;registration_plate&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start font-medium" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:circle-alert" data-inline="false" class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4m0 4h.01"></path></g></svg>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="" maxlength="17" @blur="validateField(&#39;vin&#39;, $event.target.value)">
  <label for="vin" class="block mb-2 text-sm font-medium text-gray-900">
 Numéro de série (VIN)
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:finger-print" data-inline="false" class="iconify block w-5 h-5 text-gray-400 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.269M5.742 6.364A7.47 7.47 0 0 0 4.5 10.5a7.46 7.46 0 0 1-1.15 3.993m1.989 3.559A11.2 11.2 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0q0 .79-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.7 18.7 0 0 1-2.485 5.33"></path></svg>
 </div>
 
 <input type="text" name="vin" id="vin" class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pl-10" placeholder="Ex: 1HGBH41JXMN109186" value="" x-bind:class="(fieldErrors &amp;&amp; fieldErrors[&#39;vin&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;vin&#39;]) ? &#39;!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50&#39; : &#39;&#39;" maxlength="17" @blur="validateField(&#39;vin&#39;, $event.target.value)">
 </div>

  <p class="mt-2 text-sm text-gray-600">
 17 caractères
 </p>
 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;vin&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;vin&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start font-medium" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:circle-alert" data-inline="false" class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4m0 4h.01"></path></g></svg>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="" @blur="validateField(&#39;brand&#39;, $event.target.value)">
  <label for="brand" class="block mb-2 text-sm font-medium text-gray-900">
 Marque
  <span class="text-red-600">*</span>
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:building-storefront" data-inline="false" class="iconify block w-5 h-5 text-gray-400 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3 3 0 0 0 3.75-.615A3 3 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a3 3 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015q.062.07.128.136a3 3 0 0 0 3.622.478m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75"></path></svg>
 </div>
 
 <input type="text" name="brand" id="brand" class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pl-10" placeholder="Ex: Renault, Peugeot, Toyota..." value="" required="" x-bind:class="(fieldErrors &amp;&amp; fieldErrors[&#39;brand&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;brand&#39;]) ? &#39;!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50&#39; : &#39;&#39;" @blur="validateField(&#39;brand&#39;, $event.target.value)">
 </div>

 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;brand&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;brand&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start font-medium" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:circle-alert" data-inline="false" class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4m0 4h.01"></path></g></svg>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="" @blur="validateField(&#39;model&#39;, $event.target.value)">
  <label for="model" class="block mb-2 text-sm font-medium text-gray-900">
 Modèle
  <span class="text-red-600">*</span>
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:truck" data-inline="false" class="iconify block w-5 h-5 text-gray-400 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.9 17.9 0 0 0-3.213-9.193a2.06 2.06 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.6 48.6 0 0 0-10.026 0a1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"></path></svg>
 </div>
 
 <input type="text" name="model" id="model" class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pl-10" placeholder="Ex: Clio, 208, Corolla..." value="" required="" x-bind:class="(fieldErrors &amp;&amp; fieldErrors[&#39;model&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;model&#39;]) ? &#39;!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50&#39; : &#39;&#39;" @blur="validateField(&#39;model&#39;, $event.target.value)">
 </div>

 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;model&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;model&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start font-medium" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:circle-alert" data-inline="false" class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4m0 4h.01"></path></g></svg>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="md:col-span-2">
                                        <div class="">
  <label for="color" class="block mb-2 text-sm font-medium text-gray-900">
 Couleur
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:swatch" data-inline="false" class="iconify block w-5 h-5 text-gray-400 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.098 19.902a3.75 3.75 0 0 0 5.304 0l6.401-6.402M6.75 21A3.75 3.75 0 0 1 3 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 0 0 3.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88a1.124 1.124 0 0 1 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75z"></path></svg>
 </div>
 
 <input type="text" name="color" id="color" class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pl-10" placeholder="Ex: Blanc, Noir, Gris métallisé..." value="" x-bind:class="(fieldErrors &amp;&amp; fieldErrors[&#39;color&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;color&#39;]) ? &#39;!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50&#39; : &#39;&#39;">
 </div>

 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;color&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;color&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start font-medium" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:circle-alert" data-inline="false" class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4m0 4h.01"></path></g></svg>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" style="display: none;">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:cog-6-tooth" data-inline="false" class="iconify block w-5 h-5 text-blue-600 iconify--heroicons"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87q.11.06.22.127c.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a8 8 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a7 7 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a7 7 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a7 7 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124q.108-.066.22-.128c.332-.183.582-.495.644-.869z"></path><path d="M15 12a3 3 0 1 1-6 0a3 3 0 0 1 6 0"></path></g></svg>
                                    Caractéristiques Techniques
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="" @change="validateField(&#39;vehicle_type_id&#39;, $event.target.value)">
  <label for="vehicle_type_id-ts-control" class="block mb-2 text-sm font-medium text-gray-900" id="vehicle_type_id-ts-label">
 Type de Véhicule
  <span class="text-red-500">*</span>
  </label>
 
 <select name="vehicle_type_id" id="vehicle_type_id" class="tomselect bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 tomselected ts-hidden-accessible" required="" @change="validateField(&#39;vehicle_type_id&#39;, $event.target.value)" tabindex="-1">
 
  
  <option value="">Sélectionnez un type...</option>
 
  <option value="11">
 Autre
 </option>
  <option value="1">
 Berline
 </option>
  <option value="4">
 Bus
 </option>
  <option value="3">
 Camion
 </option>
  <option value="7">
 Engin
 </option>
  <option value="8">
 Fourgonnette
 </option>
  <option value="6">
 Moto
 </option>
  <option value="10">
 Semi-remorque
 </option>
  <option value="2">
 Utilitaire
 </option>
  <option value="9">
 VUL
 </option>
  <option value="5">
 Voiture
 </option>
   </select><div class="ts-wrapper tomselect bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 single plugin-clear_button plugin-remove_button input-hidden required invalid full has-items"><div class="ts-control"><div data-value="" class="item" data-ts-item="">Sélectionnez un type...<a href="javascript:void(0)" class="remove" tabindex="-1" title="Remove">×</a></div><input type="text" autocomplete="off" size="1" tabindex="0" role="combobox" aria-haspopup="listbox" aria-expanded="false" aria-controls="vehicle_type_id-ts-dropdown" id="vehicle_type_id-ts-control" aria-labelledby="vehicle_type_id-ts-label" placeholder="Rechercher..."><div class="clear-button" title="Clear All">⨯</div></div><div class="ts-dropdown single plugin-clear_button plugin-remove_button" style="display: none;"><div role="listbox" tabindex="-1" class="ts-dropdown-content" id="vehicle_type_id-ts-dropdown" aria-labelledby="vehicle_type_id-ts-label"></div></div></div>

 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;vehicle_type_id&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;vehicle_type_id&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:exclamation-circle" data-inline="false" class="iconify block w-4 h-4 mr-1 mt-0.5 flex-shrink-0 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0a9 9 0 0 1 18 0m-9 3.75h.008v.008H12z"></path></svg>
 <span>Ce champ est obligatoire</span>
 </p>
</div>

 
                                    <div class="" @change="validateField(&#39;fuel_type_id&#39;, $event.target.value)">
  <label for="fuel_type_id-ts-control" class="block mb-2 text-sm font-medium text-gray-900" id="fuel_type_id-ts-label">
 Type de Carburant
  <span class="text-red-500">*</span>
  </label>
 
 <select name="fuel_type_id" id="fuel_type_id" class="tomselect bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 tomselected ts-hidden-accessible" required="" @change="validateField(&#39;fuel_type_id&#39;, $event.target.value)" tabindex="-1">
 
  
  <option value="">Sélectionnez un carburant...</option>
 
  <option value="2">
 Diesel
 </option>
  <option value="1">
 Essence
 </option>
  <option value="3">
 GPL
 </option>
  <option value="4">
 Électrique
 </option>
   </select><div class="ts-wrapper tomselect bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 single plugin-clear_button plugin-remove_button input-hidden required invalid full has-items"><div class="ts-control"><div data-value="" class="item" data-ts-item="">Sélectionnez un carburant...<a href="javascript:void(0)" class="remove" tabindex="-1" title="Remove">×</a></div><input type="text" autocomplete="off" size="1" tabindex="0" role="combobox" aria-haspopup="listbox" aria-expanded="false" aria-controls="fuel_type_id-ts-dropdown" id="fuel_type_id-ts-control" aria-labelledby="fuel_type_id-ts-label" placeholder="Rechercher..."><div class="clear-button" title="Clear All">⨯</div></div><div class="ts-dropdown single plugin-clear_button plugin-remove_button" style="display: none;"><div role="listbox" tabindex="-1" class="ts-dropdown-content" id="fuel_type_id-ts-dropdown" aria-labelledby="fuel_type_id-ts-label"></div></div></div>

 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;fuel_type_id&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;fuel_type_id&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:exclamation-circle" data-inline="false" class="iconify block w-4 h-4 mr-1 mt-0.5 flex-shrink-0 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0a9 9 0 0 1 18 0m-9 3.75h.008v.008H12z"></path></svg>
 <span>Ce champ est obligatoire</span>
 </p>
</div>


                                    <div class="" @change="validateField(&#39;transmission_type_id&#39;, $event.target.value)">
  <label for="transmission_type_id-ts-control" class="block mb-2 text-sm font-medium text-gray-900" id="transmission_type_id-ts-label">
 Type de Transmission
  <span class="text-red-500">*</span>
  </label>
 
 <select name="transmission_type_id" id="transmission_type_id" class="tomselect bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 tomselected ts-hidden-accessible" required="" @change="validateField(&#39;transmission_type_id&#39;, $event.target.value)" tabindex="-1">
 
  
  <option value="">Sélectionnez une transmission...</option>
 
  <option value="2">
 Automatique
 </option>
  <option value="1">
 Manuelle
 </option>
   </select><div class="ts-wrapper tomselect bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 single plugin-clear_button plugin-remove_button input-hidden required invalid full has-items"><div class="ts-control"><div data-value="" class="item" data-ts-item="">Sélectionnez une transmission...<a href="javascript:void(0)" class="remove" tabindex="-1" title="Remove">×</a></div><input type="text" autocomplete="off" size="1" tabindex="0" role="combobox" aria-haspopup="listbox" aria-expanded="false" aria-controls="transmission_type_id-ts-dropdown" id="transmission_type_id-ts-control" aria-labelledby="transmission_type_id-ts-label" placeholder="Rechercher..."><div class="clear-button" title="Clear All">⨯</div></div><div class="ts-dropdown single plugin-clear_button plugin-remove_button" style="display: none;"><div role="listbox" tabindex="-1" class="ts-dropdown-content" id="transmission_type_id-ts-dropdown" aria-labelledby="transmission_type_id-ts-label"></div></div></div>

 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;transmission_type_id&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;transmission_type_id&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:exclamation-circle" data-inline="false" class="iconify block w-4 h-4 mr-1 mt-0.5 flex-shrink-0 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0a9 9 0 0 1 18 0m-9 3.75h.008v.008H12z"></path></svg>
 <span>Ce champ est obligatoire</span>
 </p>
</div>


                                    <div class="" min="1950" max="2026">
  <label for="manufacturing_year" class="block mb-2 text-sm font-medium text-gray-900">
 Année de Fabrication
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:calendar" data-inline="false" class="iconify block w-5 h-5 text-gray-400 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"></path></svg>
 </div>
 
 <input type="number" name="manufacturing_year" id="manufacturing_year" class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pl-10" placeholder="Ex: 2024" value="" x-bind:class="(fieldErrors &amp;&amp; fieldErrors[&#39;manufacturing_year&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;manufacturing_year&#39;]) ? &#39;!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50&#39; : &#39;&#39;" min="1950" max="2026">
 </div>

 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;manufacturing_year&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;manufacturing_year&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start font-medium" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:circle-alert" data-inline="false" class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4m0 4h.01"></path></g></svg>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="" min="1" max="99">
  <label for="seats" class="block mb-2 text-sm font-medium text-gray-900">
 Nombre de places
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:user-group" data-inline="false" class="iconify block w-5 h-5 text-gray-400 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.1 9.1 0 0 0 3.741-.479q.01-.12.01-.241a3 3 0 0 0-4.692-2.478m.94 3.197l.001.031q0 .337-.037.666A11.94 11.94 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6 6 0 0 1 6 18.719m12 0a5.97 5.97 0 0 0-.941-3.197m0 0A6 6 0 0 0 12 12.75a6 6 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72a9 9 0 0 0 3.74.477m.94-3.197a5.97 5.97 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0a3 3 0 0 1 6 0m6 3a2.25 2.25 0 1 1-4.5 0a2.25 2.25 0 0 1 4.5 0m-13.5 0a2.25 2.25 0 1 1-4.5 0a2.25 2.25 0 0 1 4.5 0"></path></svg>
 </div>
 
 <input type="number" name="seats" id="seats" class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pl-10" placeholder="Ex: 5" value="" x-bind:class="(fieldErrors &amp;&amp; fieldErrors[&#39;seats&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;seats&#39;]) ? &#39;!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50&#39; : &#39;&#39;" min="1" max="99">
 </div>

 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;seats&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;seats&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start font-medium" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:circle-alert" data-inline="false" class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4m0 4h.01"></path></g></svg>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="" min="0">
  <label for="power_hp" class="block mb-2 text-sm font-medium text-gray-900">
 Puissance (CV)
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:bolt" data-inline="false" class="iconify block w-5 h-5 text-gray-400 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75L12 13.5z"></path></svg>
 </div>
 
 <input type="number" name="power_hp" id="power_hp" class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pl-10" placeholder="Ex: 90" value="" x-bind:class="(fieldErrors &amp;&amp; fieldErrors[&#39;power_hp&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;power_hp&#39;]) ? &#39;!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50&#39; : &#39;&#39;" min="0">
 </div>

 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;power_hp&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;power_hp&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start font-medium" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:circle-alert" data-inline="false" class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4m0 4h.01"></path></g></svg>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="lg:col-span-3">
                                        <div class="" min="0">
  <label for="engine_displacement_cc" class="block mb-2 text-sm font-medium text-gray-900">
 Cylindrée (cc)
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:wrench-screwdriver" data-inline="false" class="iconify block w-5 h-5 text-gray-400 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17L17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14q.19.017.384.017a4.5 4.5 0 0 0 4.102-6.352l-3.276 3.276a3 3 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008z"></path></svg>
 </div>
 
 <input type="number" name="engine_displacement_cc" id="engine_displacement_cc" class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pl-10" placeholder="Ex: 1500" value="" x-bind:class="(fieldErrors &amp;&amp; fieldErrors[&#39;engine_displacement_cc&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;engine_displacement_cc&#39;]) ? &#39;!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50&#39; : &#39;&#39;" min="0">
 </div>

  <p class="mt-2 text-sm text-gray-600">
 Capacité du moteur en centimètres cubes
 </p>
 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;engine_displacement_cc&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;engine_displacement_cc&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start font-medium" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:circle-alert" data-inline="false" class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4m0 4h.01"></path></g></svg>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" style="display: none;">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:currency-dollar" data-inline="false" class="iconify block w-5 h-5 text-blue-600 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0s1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659c-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0a9 9 0 0 1 18 0"></path></svg>
                                    Acquisition &amp; Statut
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="">
  <label for="acquisition_date" class="block mb-2 text-sm font-medium text-gray-900">
 Date d'acquisition
  <span class="text-red-600">*</span>
  </label>
 
 <div class="relative">
 <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:calendar-days" data-inline="false" class="iconify block w-4 h-4 text-gray-500 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12zM12 15h.008v.008H12zm0 2.25h.008v.008H12zM9.75 15h.008v.008H9.75zm0 2.25h.008v.008H9.75zM7.5 15h.008v.008H7.5zm0 2.25h.008v.008H7.5zm6.75-4.5h.008v.008h-.008zm0 2.25h.008v.008h-.008zm0 2.25h.008v.008h-.008zm2.25-4.5h.008v.008H16.5zm0 2.25h.008v.008H16.5z"></path></svg>
 </div>
 <input type="text" name="acquisition_date" id="acquisition_date" class="datepicker !bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 block w-full pl-10 p-2.5 transition-colors duration-200 flatpickr-input" placeholder="Choisir une date" value="" required="" data-max-date="2025-12-05" data-date-format="d/m/Y" autocomplete="off">
 </div>

  <p class="mt-2 text-sm text-gray-600">
 Date d'achat du véhicule
 </p>
 </div>

 
 
                                    <div class="" step="0.01" min="0">
  <label for="purchase_price" class="block mb-2 text-sm font-medium text-gray-900">
 Prix d'achat (DA)
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:currency-dollar" data-inline="false" class="iconify block w-5 h-5 text-gray-400 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0s1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659c-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0a9 9 0 0 1 18 0"></path></svg>
 </div>
 
 <input type="number" name="purchase_price" id="purchase_price" class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pl-10" placeholder="Ex: 2500000" value="" x-bind:class="(fieldErrors &amp;&amp; fieldErrors[&#39;purchase_price&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;purchase_price&#39;]) ? &#39;!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50&#39; : &#39;&#39;" step="0.01" min="0">
 </div>

  <p class="mt-2 text-sm text-gray-600">
 Prix d'achat en Dinars Algériens
 </p>
 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;purchase_price&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;purchase_price&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start font-medium" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:circle-alert" data-inline="false" class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4m0 4h.01"></path></g></svg>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="" step="0.01" min="0">
  <label for="current_value" class="block mb-2 text-sm font-medium text-gray-900">
 Valeur actuelle (DA)
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:currency-dollar" data-inline="false" class="iconify block w-5 h-5 text-gray-400 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0s1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659c-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0a9 9 0 0 1 18 0"></path></svg>
 </div>
 
 <input type="number" name="current_value" id="current_value" class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pl-10" placeholder="Ex: 2000000" value="" x-bind:class="(fieldErrors &amp;&amp; fieldErrors[&#39;current_value&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;current_value&#39;]) ? &#39;!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50&#39; : &#39;&#39;" step="0.01" min="0">
 </div>

  <p class="mt-2 text-sm text-gray-600">
 Valeur estimée actuelle
 </p>
 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;current_value&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;current_value&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start font-medium" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:circle-alert" data-inline="false" class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4m0 4h.01"></path></g></svg>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="" min="0">
  <label for="initial_mileage" class="block mb-2 text-sm font-medium text-gray-900">
 Kilométrage Initial
  </label>
 
 <div class="relative">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:chart-bar" data-inline="false" class="iconify block w-5 h-5 text-gray-400 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875zm6.75-4.5c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125zm6.75-4.5c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125z"></path></svg>
 </div>
 
 <input type="number" name="initial_mileage" id="initial_mileage" class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pl-10" placeholder="Ex: 0" value="0" x-bind:class="(fieldErrors &amp;&amp; fieldErrors[&#39;initial_mileage&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;initial_mileage&#39;]) ? &#39;!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50&#39; : &#39;&#39;" min="0">
 </div>

  <p class="mt-2 text-sm text-gray-600">
 Kilométrage au moment de l'acquisition
 </p>
 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;initial_mileage&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;initial_mileage&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start font-medium" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="lucide:circle-alert" data-inline="false" class="iconify block w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0 iconify--lucide"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4m0 4h.01"></path></g></svg>
 <span>Ce champ est obligatoire et doit être correctement rempli</span>
 </p>
</div>

                                    <div class="md:col-span-2">
                                        <div class="" @change="validateField(&#39;status_id&#39;, $event.target.value)">
  <label for="status_id-ts-control" class="block mb-2 text-sm font-medium text-gray-900" id="status_id-ts-label">
 Statut Initial
  <span class="text-red-500">*</span>
  </label>
 
 <select name="status_id" id="status_id" class="tomselect bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 tomselected ts-hidden-accessible" required="" @change="validateField(&#39;status_id&#39;, $event.target.value)" tabindex="-1">
 
  
  <option value="">Sélectionnez un statut...</option>
 
  <option value="9">
 Affecté
 </option>
  <option value="12">
 Available
 </option>
  <option value="2">
 En maintenance
 </option>
  <option value="10">
 En panne
 </option>
  <option value="8">
 Parking
 </option>
  <option value="11">
 Réformé
 </option>
   </select><div class="ts-wrapper tomselect bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 single plugin-clear_button plugin-remove_button input-hidden required invalid full has-items"><div class="ts-control"><div data-value="" class="item" data-ts-item="">Sélectionnez un statut...<a href="javascript:void(0)" class="remove" tabindex="-1" title="Remove">×</a></div><input type="text" autocomplete="off" size="1" tabindex="0" role="combobox" aria-haspopup="listbox" aria-expanded="false" aria-controls="status_id-ts-dropdown" id="status_id-ts-control" aria-labelledby="status_id-ts-label" placeholder="Rechercher..."><div class="clear-button" title="Clear All">⨯</div></div><div class="ts-dropdown single plugin-clear_button plugin-remove_button" style="display: none;"><div role="listbox" tabindex="-1" class="ts-dropdown-content" id="status_id-ts-dropdown" aria-labelledby="status_id-ts-label"></div></div></div>

  <p class="mt-2 text-sm text-gray-500">
 État opérationnel du véhicule
 </p>
 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;status_id&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;status_id&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:exclamation-circle" data-inline="false" class="iconify block w-4 h-4 mr-1 mt-0.5 flex-shrink-0 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0a9 9 0 0 1 18 0m-9 3.75h.008v.008H12z"></path></svg>
 <span>Ce champ est obligatoire</span>
 </p>
</div>

                                    </div>

                                    <div class="md:col-span-2">
                                        <div class="">
  <label for="users-ts-control" class="block mb-2 text-sm font-medium text-gray-900" id="users-ts-label">
 Utilisateurs Autorisés
  </label>
 
 <select name="users[]" id="users" class="tomselect bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 tomselected ts-hidden-accessible" multiple="multiple" tabindex="-1">
 
  
 
  <option value="24">
  (ahmedlounis@zenfleet.dz)
 </option>
  <option value="2">
  (amine.belabes@trans-algerlogistics.local)
 </option>
  <option value="14">
  (testroleverification@zenfleet.dz)
 </option>
  <option value="22">
  (saidmerbouhi@zenfleet.dz)
 </option>
  <option value="1">
  (mohamed.meziani@trans-algerlogistics.local)
 </option>
  <option value="23">
 Ali Boumalou (ali@zenfleet.dz)
 </option>
  <option value="5">
 Gestionnaire Flotte (gestionnaire@zenfleet.dz)
 </option>
  <option value="6">
 SUPER VISEUR (superviseur@zenfleet.dz)
 </option>
  <option value="3">
 Super Administrateur (superadmin@zenfleet.dz)
 </option>
  <option value="4">
 admin zenfleet (admin@zenfleet.dz)
 </option>
  <option value="7">
 hamid Baroudi (comptable@zenfleet.dz)
 </option>
  <option value="20">
 zerrouk ALIOUANE (zerroukaliouane@zenfleet.dz)
 </option>
   </select><div class="ts-wrapper tomselect bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 multi plugin-clear_button plugin-remove_button"><div class="ts-control"><input type="text" autocomplete="off" size="1" tabindex="0" role="combobox" aria-haspopup="listbox" aria-expanded="false" aria-controls="users-ts-dropdown" id="users-ts-control" aria-labelledby="users-ts-label" placeholder="Rechercher..."><div class="clear-button" title="Clear All">⨯</div></div><div class="ts-dropdown multi plugin-clear_button plugin-remove_button" style="display: none;"><div role="listbox" tabindex="-1" class="ts-dropdown-content" id="users-ts-dropdown" aria-labelledby="users-ts-label"></div></div></div>

  <p class="mt-2 text-sm text-gray-500">
 Sélectionnez les utilisateurs autorisés à utiliser ce véhicule
 </p>
 
 
 <p x-show="fieldErrors &amp;&amp; fieldErrors[&#39;users&#39;] &amp;&amp; touchedFields &amp;&amp; touchedFields[&#39;users&#39;]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-2 text-sm text-red-600 flex items-start" style="display: none;">
 <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:exclamation-circle" data-inline="false" class="iconify block w-4 h-4 mr-1 mt-0.5 flex-shrink-0 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0a9 9 0 0 1 18 0m-9 3.75h.008v.008H12z"></path></svg>
 <span>Ce champ est obligatoire</span>
 </p>
</div>

                                    </div>

                                    <div class="md:col-span-2">
                                        <div class="">
  <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">
 Notes
  </label>
 
 <textarea name="notes" id="notes" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 !bg-gray-50" placeholder="Informations complémentaires sur le véhicule..."></textarea>

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
                            <button type="button" class="inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed text-gray-900 bg-white border border-gray-200 hover:bg-gray-100 hover:text-blue-700 active:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:active:bg-gray-600 px-5 py-2.5 text-sm" x-show="currentStep &gt; 1" @click="previousStep()" style="display: none;">
  <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:arrow-left" data-inline="false" class="iconify block w-5 h-5 mr-2 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path></svg>
 
 Précédent

  </button>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="http://localhost/admin/vehicles" class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                                Annuler
                            </a>

                            <button type="button" class="inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 dark:active:bg-blue-800 px-5 py-2.5 text-sm" x-show="currentStep &lt; 3" @click="nextStep()" style="display: none;">
 
 Suivant

  <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:arrow-right" data-inline="false" class="iconify block w-5 h-5 ml-2 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path></svg>
  </button>

                            <button type="submit" class="inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed text-white bg-green-600 hover:bg-green-700 active:bg-green-800 dark:bg-green-500 dark:hover:bg-green-600 dark:active:bg-green-700 px-5 py-2.5 text-sm" x-show="currentStep === 3" style="display: none;">
  <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 24 24" data-icon="heroicons:check-circle" data-inline="false" class="iconify block w-5 h-5 mr-2 iconify--heroicons"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15L15 9.75M21 12a9 9 0 1 1-18 0a9 9 0 0 1 18 0"></path></svg>
 
 Enregistrer le Véhicule

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

 
 
 
 <script src="./AJOUT_VEHICULE_files/tom-select.complete.min.js.download"></script>

 
 <script src="./AJOUT_VEHICULE_files/slimselect.min.js.download"></script>

 
 <script src="./AJOUT_VEHICULE_files/flatpickr"></script>
 <script src="./AJOUT_VEHICULE_files/fr.js.download"></script>
 
 
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

  <script src="./AJOUT_VEHICULE_files/tom-select.complete.min.js.download"></script>
 <script>
 // ✅ OPTIMISATION ENTERPRISE: Fonction d'initialisation Tom Select réutilisable
 function initializeTomSelect(element) {
 if (element.tomSelectInstance) {
 element.tomSelectInstance.destroy();
 }
 
 const tomSelectInstance = new TomSelect(element, {
 plugins: ['clear_button', 'remove_button'],
 maxOptions: 100,
 placeholder: element.getAttribute('data-placeholder') || 'Rechercher...',
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
 },
 // ✅ INTÉGRATION LIVEWIRE ENTERPRISE-GRADE
 onInitialize: function() {
 const self = this;
 
 // Stocker l'instance pour référence future
 element.tomSelectInstance = self;
 
 // Hook Livewire pour synchronisation après mise à jour DOM
 if (typeof Livewire !== 'undefined') {
 Livewire.hook('element.updated', (el, component) => {
 if (el === element || el.contains(element)) {
 // Synchroniser Tom Select avec les nouvelles options
 self.sync();
 
 // Préserver la valeur sélectionnée
 const wireModel = element.getAttribute('wire:model.live') || 
 element.getAttribute('wire:model');
 if (wireModel && component.get(wireModel)) {
 self.setValue(component.get(wireModel), true);
 }
 }
 });
 
 // Hook pour nettoyer l'instance avant destruction
 Livewire.hook('element.removed', (el, component) => {
 if (el === element || el.contains(element)) {
 self.destroy();
 }
 });
 }
 },
 // ✅ OPTIMISATION: Événements pour synchronisation bidirectionnelle
 onChange: function(value) {
 // Dispatch event pour Alpine.js et Livewire
 element.dispatchEvent(new Event('change', { bubbles: true }));
 
 // Force Livewire update si wire:model est présent
 const wireModel = element.getAttribute('wire:model.live') || 
 element.getAttribute('wire:model');
 if (wireModel && typeof Livewire !== 'undefined') {
 const component = Livewire.find(element.closest('[wire\\:id]').getAttribute('wire:id'));
 if (component) {
 component.set(wireModel, value);
 }
 }
 }
 });
 
 return tomSelectInstance;
 }
 
 // ✅ INITIALISATION AU CHARGEMENT
 document.addEventListener('DOMContentLoaded', function() {
 document.querySelectorAll('.tomselect').forEach(function(el) {
 initializeTomSelect(el);
 });
 });
 
 // ✅ RÉINITIALISATION APRÈS NAVIGATION LIVEWIRE
 document.addEventListener('livewire:navigated', function() {
 document.querySelectorAll('.tomselect').forEach(function(el) {
 if (!el.tomSelectInstance) {
 initializeTomSelect(el);
 }
 });
 });
 
 // ✅ SUPPORT POUR COMPOSANTS DYNAMIQUES ALPINE.JS
 document.addEventListener('alpine:init', function() {
 Alpine.magic('tomselect', (el) => {
 return () => {
 const selectEl = el.querySelector('.tomselect');
 if (selectEl && !selectEl.tomSelectInstance) {
 return initializeTomSelect(selectEl);
 }
 return selectEl?.tomSelectInstance;
 };
 });
 });
 </script>
  <script src="./AJOUT_VEHICULE_files/flatpickr"></script>
 <script src="./AJOUT_VEHICULE_files/fr.js.download"></script>
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
 <script>
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
                    requiredFields: ['registration_plate', 'brand', 'model']
                },
                {
                    label: 'Caractéristiques',
                    icon: 'settings',
                    validated: false,
                    touched: false,
                    requiredFields: ['vehicle_type_id', 'fuel_type_id', 'transmission_type_id']
                },
                {
                    label: 'Acquisition',
                    icon: 'receipt',
                    validated: false,
                    touched: false,
                    requiredFields: ['acquisition_date', 'status_id']
                }
            ],

            fieldErrors: {},

            {
                {
                    --⚠️NOUVEAU: Tracking des champs touchés pour validation temps réel--
                }
            }
            touchedFields: {},

            init() {
                // Initialiser avec les erreurs serveur si présentes
                
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

                [].forEach(field => {
                    const stepIndex = fieldToStepMap[field];
                    if (stepIndex !== undefined) {
                        this.steps[stepIndex].touched = true;
                        this.steps[stepIndex].validated = false;
                    }
                });
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
                    'model': (v) => v && v.length > 0 && v.length <= 100,
                    'vin': (v) => !v || v.length === 17,
                    'vehicle_type_id': (v) => v && v.length > 0,
                    'fuel_type_id': (v) => v && v.length > 0,
                    'transmission_type_id': (v) => v && v.length > 0,
                    'acquisition_date': (v) => v && v.length > 0,
                    'status_id': (v) => v && v.length > 0,
                };

                const isValid = rules[fieldName] ? rules[fieldName](value) : true;

                // ✅ ÉTAPE 3: Gérer les erreurs
                if (!isValid) {
                    // Marquer le champ comme en erreur
                    this.fieldErrors[fieldName] = true;

                    // Ajouter classe ts-error pour TomSelect
                    const input = document.querySelector(`[name="${fieldName}"]`);
                    if (input) {
                        const tsWrapper = input.closest('.ts-wrapper');
                        if (tsWrapper) {
                            tsWrapper.classList.add('ts-error');
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

                        // Gérer TomSelect (wrapper avec classe .ts-wrapper)
                        const tsWrapper = input.closest('.ts-wrapper');
                        if (tsWrapper) {
                            tsWrapper.classList.add('ts-error');
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

                // Retirer la classe ts-error si c'est un TomSelect
                const input = document.querySelector(`[name="${fieldName}"]`);
                if (input) {
                    const tsWrapper = input.closest('.ts-wrapper');
                    if (tsWrapper) {
                        tsWrapper.classList.remove('ts-error');
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
 

 
 <div x-data="toastManager()" @toast.window="showToast($event.detail)" class="fixed top-4 right-4 z-50 space-y-2" style="pointer-events: none;">
     <template x-for="(toast, index) in toasts" :key="toast.id"></template>
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
    <script data-navigate-once="true">window.livewireScriptConfig = {"csrf":"ibkA7bKbNTVfvGQNnAhqpGAYV0nBCkXPfNQTNxxU","uri":"\/livewire\/update","progressBar":"","nonce":""};</script>

<div class="flatpickr-calendar animate" tabindex="-1"><div class="flatpickr-months"><span class="flatpickr-prev-month"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></span><div class="flatpickr-month"><div class="flatpickr-current-month"><select class="flatpickr-monthDropdown-months" aria-label="Month" tabindex="-1"><option class="flatpickr-monthDropdown-month" value="0" tabindex="-1">janvier</option><option class="flatpickr-monthDropdown-month" value="1" tabindex="-1">février</option><option class="flatpickr-monthDropdown-month" value="2" tabindex="-1">mars</option><option class="flatpickr-monthDropdown-month" value="3" tabindex="-1">avril</option><option class="flatpickr-monthDropdown-month" value="4" tabindex="-1">mai</option><option class="flatpickr-monthDropdown-month" value="5" tabindex="-1">juin</option><option class="flatpickr-monthDropdown-month" value="6" tabindex="-1">juillet</option><option class="flatpickr-monthDropdown-month" value="7" tabindex="-1">août</option><option class="flatpickr-monthDropdown-month" value="8" tabindex="-1">septembre</option><option class="flatpickr-monthDropdown-month" value="9" tabindex="-1">octobre</option><option class="flatpickr-monthDropdown-month" value="10" tabindex="-1">novembre</option><option class="flatpickr-monthDropdown-month" value="11" tabindex="-1">décembre</option></select><div class="numInputWrapper"><input class="numInput cur-year" type="number" tabindex="-1" aria-label="Year" max="2025"><span class="arrowUp"></span><span class="arrowDown"></span></div></div></div><span class="flatpickr-next-month flatpickr-disabled"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></span></div><div class="flatpickr-innerContainer"><div class="flatpickr-rContainer"><div class="flatpickr-weekdays"><div class="flatpickr-weekdaycontainer">
      <span class="flatpickr-weekday">
        lun</span><span class="flatpickr-weekday">mar</span><span class="flatpickr-weekday">mer</span><span class="flatpickr-weekday">jeu</span><span class="flatpickr-weekday">ven</span><span class="flatpickr-weekday">sam</span><span class="flatpickr-weekday">dim
      </span>
      </div></div><div class="flatpickr-days" tabindex="-1"><div class="dayContainer"><span class="flatpickr-day" aria-label="décembre 1, 2025" tabindex="-1">1</span><span class="flatpickr-day" aria-label="décembre 2, 2025" tabindex="-1">2</span><span class="flatpickr-day" aria-label="décembre 3, 2025" tabindex="-1">3</span><span class="flatpickr-day" aria-label="décembre 4, 2025" tabindex="-1">4</span><span class="flatpickr-day today" aria-label="décembre 5, 2025" aria-current="date" tabindex="-1">5</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 6, 2025">6</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 7, 2025">7</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 8, 2025">8</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 9, 2025">9</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 10, 2025">10</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 11, 2025">11</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 12, 2025">12</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 13, 2025">13</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 14, 2025">14</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 15, 2025">15</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 16, 2025">16</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 17, 2025">17</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 18, 2025">18</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 19, 2025">19</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 20, 2025">20</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 21, 2025">21</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 22, 2025">22</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 23, 2025">23</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 24, 2025">24</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 25, 2025">25</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 26, 2025">26</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 27, 2025">27</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 28, 2025">28</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 29, 2025">29</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 30, 2025">30</span><span class="flatpickr-day flatpickr-disabled" aria-label="décembre 31, 2025">31</span><span class="flatpickr-day nextMonthDay flatpickr-disabled" aria-label="janvier 1, 2026">1</span><span class="flatpickr-day nextMonthDay flatpickr-disabled" aria-label="janvier 2, 2026">2</span><span class="flatpickr-day nextMonthDay flatpickr-disabled" aria-label="janvier 3, 2026">3</span><span class="flatpickr-day nextMonthDay flatpickr-disabled" aria-label="janvier 4, 2026">4</span><span class="flatpickr-day nextMonthDay flatpickr-disabled" aria-label="janvier 5, 2026">5</span><span class="flatpickr-day nextMonthDay flatpickr-disabled" aria-label="janvier 6, 2026">6</span><span class="flatpickr-day nextMonthDay flatpickr-disabled" aria-label="janvier 7, 2026">7</span><span class="flatpickr-day nextMonthDay flatpickr-disabled" aria-label="janvier 8, 2026">8</span><span class="flatpickr-day nextMonthDay flatpickr-disabled" aria-label="janvier 9, 2026">9</span><span class="flatpickr-day nextMonthDay flatpickr-disabled" aria-label="janvier 10, 2026">10</span><span class="flatpickr-day nextMonthDay flatpickr-disabled" aria-label="janvier 11, 2026">11</span></div></div></div></div></div></body></html>