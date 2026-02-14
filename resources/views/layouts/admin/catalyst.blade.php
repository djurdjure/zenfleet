<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(auth()->check())
    <meta name="user-data" content="{{ json_encode(['id' => auth()->id(), 'name' => auth()->user()->name, 'role' => auth()->user()->getRoleNames()->first()]) }}">
    @endif

    <title>@yield('title', 'ZenFleet Admin') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    {{-- ====================================================================
 📦 ZENFLEET ENTERPRISE-GRADE ASSETS
 ==================================================================== 
 SlimSelect & Flatpickr: bundled locally via Vite (no CDN)
 Icons: Iconify runtime (loads icons from CDN, caches locally)
 FontAwesome: REMOVED - migrating to Iconify progressively
 @version 2.2 Enterprise-Ready
 ==================================================================== --}}

    {{-- Iconify CDN - Original runtime that renders icons from data-icon attributes --}}
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    {{-- Flatpickr Custom Styles ZenFleet --}}


    <!-- 🚀 Performance: Load CSS in Parallel (No JS blocking) -->
    @livewireScriptConfig
    @vite(['resources/css/app.css', 'resources/css/admin/app.css', 'resources/js/admin/app.js'])
    @stack('styles')
    @livewireStyles
</head>

<body class="h-full">
    <div class="min-h-full">
        {{-- Sidebar pour desktop - Style Ultra-Pro World-Class --}}
        <div class="max-lg:hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
            <div class="zf-sidebar-shell flex grow flex-col overflow-hidden shadow-sm">
                {{-- En-tête avec logo Premium --}}
                <div class="w-full flex-none px-4 py-4 h-16 flex items-center border-b border-[color:var(--zf-sidebar-border)]/70">
                    <div class="flex items-center w-full">
                        <div class="relative mr-3">
                            <div class="zf-sidebar-logo-badge w-9 h-9 rounded-xl flex items-center justify-center shadow-md">
                                <x-iconify icon="mdi:truck-fast" class="w-5 h-5 text-white" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <span class="text-slate-800 text-lg font-bold tracking-tight">ZenFleet</span>
                            <div class="text-xs text-slate-500 font-medium">Fleet Management</div>
                        </div>
                    </div>
                </div>

                {{-- Navigation Enterprise --}}
                <div class="flex flex-col flex-1 overflow-hidden">
                    @include('layouts.admin.partials.sidebar-nav')

                    {{-- Footer du menu supprimé --}}
                </div>
            </div>
        </div>

        {{-- Sidebar mobile --}}
        <div class="lg:hidden" x-data="{ open: false }">
            {{-- Backdrop --}}
            <div x-show="open"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="relative z-50 lg:hidden">
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-40" @click="open = false"></div>

                <div class="fixed inset-0 flex z-50">
                    <div x-show="open"
                        x-transition:enter="transition ease-in-out duration-300 transform"
                        x-transition:enter-start="-translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in-out duration-300 transform"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="-translate-x-full"
                        class="relative mr-16 flex w-full max-w-xs flex-1">
                        {{-- Même contenu que la sidebar desktop --}}
                        <div class="zf-sidebar-shell flex grow flex-col gap-y-5 overflow-y-auto px-6 pb-4">
                            {{-- Logo --}}
                            <div class="flex h-16 shrink-0 items-center">
                                <div class="flex items-center">
                                    <x-iconify icon="heroicons:truck" class="w-6 h-6 text-[color:var(--zf-primary)] mr-3" />
                                    <span class="text-slate-800 text-xl font-bold">ZenFleet</span>
                                </div>
                            </div>

                            {{-- Navigation mobile (copie de la navigation desktop) --}}
                            <nav class="flex flex-1 flex-col">
                                @include('layouts.admin.partials.sidebar-nav')
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Header mobile --}}
            <div class="sticky top-0 z-40 flex items-center gap-x-6 bg-white px-4 py-4 shadow-sm sm:px-6 lg:hidden">
                <button type="button" @click="open = true" class="-m-2.5 p-2.5 text-zinc-500 lg:hidden">
                    <span class="sr-only">Ouvrir la sidebar</span>
                    <x-iconify icon="heroicons:bars-3" class="h-6 w-6" />
                </button>
                <div class="flex-1 text-sm font-semibold leading-6 text-zinc-900">ZenFleet</div>
                <div class="h-8 w-8 bg-zinc-100 rounded-full flex items-center justify-center">
                    <x-iconify icon="heroicons:user" class="h-4 w-4 text-zinc-500" />
                </div>
            </div>
        </div>

        {{-- Contenu principal --}}
        <div class="lg:pl-64 zf-page min-h-screen">
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-zinc-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <div class="h-6 w-px bg-zinc-200 lg:hidden" aria-hidden="true"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="relative flex flex-1">
                        {{-- Zone de recherche (optionnelle) --}}
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        {{-- Recherche rapide --}}
                        <div class="relative hidden lg:block">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-iconify icon="heroicons:magnifying-glass" class="h-4 w-4 text-zinc-400" />
                            </div>
                            <input type="search"
                                placeholder="Rechercher..."
                                class="block w-64 rounded-md border-0 bg-white py-1.5 pl-10 pr-3 text-zinc-900 ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-zinc-600 sm:text-sm sm:leading-6">
                        </div>

                        {{-- Notifications avec badge --}}
                        <div class="relative">
                            <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600 relative">
                                <span class="sr-only">Voir les notifications</span>
                                <x-iconify icon="mdi:bell-ring" class="h-6 w-6" />
                                <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                            </button>
                        </div>

                        {{-- Messages --}}
                        <div class="relative">
                            <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600 relative">
                                <span class="sr-only">Messages</span>
                                <x-iconify icon="heroicons:envelope" class="h-6 w-6" />
                                <span class="absolute -top-1 -right-1 h-4 w-4 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center">2</span>
                            </button>
                        </div>

                        {{-- Mode sombre --}}
                        <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600">
                            <span class="sr-only">Basculer le mode sombre</span>
                            <x-iconify icon="heroicons:moon" class="h-6 w-6" />
                        </button>

                        {{-- Séparateur --}}
                        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-zinc-200" aria-hidden="true"></div>

                        {{-- Profile dropdown amélioré --}}
                        <div class="relative"
                             x-data="{
                                open: false,
                                styles: '',
                                direction: 'down',
                                align: 'right',
                                toggle() {
                                    if (this.open) { this.close(); return; }
                                    this.open = true;
                                    this.$nextTick(() => {
                                        this.updatePosition();
                                        requestAnimationFrame(() => this.updatePosition());
                                    });
                                },
                                close() { this.open = false; },
                                updatePosition() {
                                    if (!this.$refs.trigger || !this.$refs.menu) return;
                                    const rect = this.$refs.trigger.getBoundingClientRect();
                                    const menuHeight = this.$refs.menu.offsetHeight || 260;
                                    const menuWidth = this.$refs.menu.offsetWidth || 224;
                                    const padding = 12;
                                    const spaceBelow = window.innerHeight - rect.bottom;
                                    const spaceAbove = rect.top;
                                    const shouldOpenUp = spaceBelow < menuHeight && spaceAbove > spaceBelow;
                                    this.direction = shouldOpenUp ? 'up' : 'down';
                                    let top = shouldOpenUp ? (rect.top - menuHeight - 8) : (rect.bottom + 8);
                                    if (top + menuHeight > window.innerHeight - padding) {
                                        top = window.innerHeight - padding - menuHeight;
                                    }
                                    if (top < padding) top = padding;
                                    let left = this.align === 'right' ? (rect.right - menuWidth) : rect.left;
                                    if (left + menuWidth > window.innerWidth - padding) {
                                        left = window.innerWidth - padding - menuWidth;
                                    }
                                    if (left < padding) left = padding;
                                    this.styles = `position: fixed; top: ${top}px; left: ${left}px; width: ${menuWidth}px; z-index: 9999;`;
                                }
                             }"
                             x-init="$watch('open', value => {
                                if (value) {
                                    $nextTick(() => {
                                        this.updatePosition();
                                        requestAnimationFrame(() => this.updatePosition());
                                    });
                                }
                             })"
                             @keydown.escape.window="close()"
                             @scroll.window="open && updatePosition()"
                             @resize.window="open && updatePosition()">
                            <button type="button" @click="toggle()" x-ref="trigger" class="-m-1.5 flex items-center p-1.5 hover:bg-zinc-50 rounded-lg transition-colors">
                                <span class="sr-only">Ouvrir le menu utilisateur</span>
                                <div class="h-8 w-8 bg-gradient-to-br from-zinc-600 to-zinc-800 rounded-full flex items-center justify-center">
                                    <x-iconify icon="heroicons:user" class="text-white w-4 h-4" />
                                </div>
                                <span class="hidden lg:flex lg:items-center">
                                    <div class="ml-3 text-left">
                                        <div class="text-sm font-semibold leading-5 text-zinc-900">{{ auth()->user()->name }}</div>
                                        <div class="text-xs leading-4 text-zinc-500">{{ auth()->user()->getRoleNames()->first() }}</div>
                                    </div>
                                    <x-iconify icon="heroicons:chevron-down" class="ml-2 h-4 w-4 text-zinc-500 transition-transform" ::class="{ 'rotate-180': open }" />
                                </span>
                            </button>

                            <template x-teleport="body">
                            <div x-show="open"
                                x-ref="menu"
                                @click.outside="close()"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                :style="styles"
                                class="rounded-md bg-white py-2 shadow-lg ring-1 ring-zinc-900/5 z-[9999]"
                                x-cloak>

                                {{-- En-tête du profil --}}
                                <div class="px-4 py-3 border-b border-zinc-100">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-gradient-to-br from-zinc-600 to-zinc-800 rounded-full flex items-center justify-center">
                                            <x-iconify icon="heroicons:user" class="text-white w-5 h-5" />
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-zinc-900">{{ auth()->user()->name }}</div>
                                            <div class="text-xs text-zinc-500">{{ auth()->user()->email }}</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Menu items --}}
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}"
                                        class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                        <x-iconify icon="heroicons:user-circle" class="mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600" />
                                        Mon Profil
                                    </a>
                                    <a href="#"
                                        class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                        <x-iconify icon="mdi:cog" class="mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600" />
                                        Paramètres
                                    </a>
                                    <a href="#"
                                        class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                        <x-iconify icon="heroicons:question-mark-circle" class="mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600" />
                                        Aide & Support
                                    </a>
                                    <div class="border-t border-zinc-100 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="group flex w-full items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                            <x-iconify icon="heroicons:arrow-right-on-rectangle" class="mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600" />
                                            Se déconnecter
                                        </button>
                                    </form>
                                </div>
                            </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    <x-form-error-summary />
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- ====================================================================
 📦 SCRIPTS JAVASCRIPT ENTERPRISE-GRADE
 ==================================================================== 
 Tom Select, Flatpickr - Chargés globalement avant Alpine.js
 @version 1.0 Production-Ready
 ==================================================================== --}}

    {{-- Tom Select JS --}}


    {{-- SlimSelect JS - REMOVED (Bundled locally via Vite) --}}

    {{-- Flatpickr JS --}}


    {{-- Initialisation Globale Tom Select & Flatpickr --}}
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

    @stack('scripts')
    {{--
    ⚠️ ATTENTION: Alpine.js est déjà chargé via Livewire 3 dans resources/js/admin/app.js
    NE PAS AJOUTER de CDN Alpine.js ici - cela cause des conflits de double initialisation
    avec les composants Livewire (@entangle, wire:click, etc.)
 --}}

    {{-- ====================================================================
 🔔 TOAST NOTIFICATION SYSTEM - Enterprise Grade
 ====================================================================
 Système de notifications toast pour feedback utilisateur temps réel
 Compatible avec Livewire events
 ==================================================================== --}}
    <div x-data="toastManager()"
        @toast.window="showToast($event.detail)"
        @notification.window="showToast(Array.isArray($event.detail) ? ($event.detail[0] || {}) : $event.detail)"
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
