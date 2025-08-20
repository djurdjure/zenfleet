<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ZenFleet') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Styles CSS améliorés -->
        <link href="{{ asset('css/calendar-improvements.css') }}" rel="stylesheet">
        
        <!-- Meta tags pour PWA (optionnel) -->
        <meta name="theme-color" content="#3b82f6">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        
        <!-- Styles additionnels -->
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Toast Container -->
        <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2">
            <!-- Les toasts seront injectés ici dynamiquement -->
        </div>

        <!-- Loading Indicator Global -->
        <div class="loading-indicator fixed top-4 left-1/2 transform -translate-x-1/2 z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg p-4 flex items-center space-x-3">
                <div class="loading-spinner"></div>
                <span class="text-sm text-gray-600">Chargement...</span>
            </div>
        </div>

        <!-- Scripts JavaScript améliorés -->
        <script src="{{ asset('js/calendar-enhancements.js') }}"></script>
        
        <!-- Scripts additionnels -->
        @stack('scripts')

        <!-- Toast System -->
        <script>
            // Système de toast amélioré
            class ToastSystem {
                constructor() {
                    this.container = document.getElementById('toast-container');
                    this.setupEventListeners();
                }

                setupEventListeners() {
                    window.addEventListener('toast', (e) => {
                        this.show(e.detail);
                    });
                }

                show({ type = 'info', message, description = '', duration = 5000 }) {
                    const toast = this.createToast(type, message, description);
                    this.container.appendChild(toast);

                    // Animation d'entrée
                    requestAnimationFrame(() => {
                        toast.classList.remove('translate-x-full', 'opacity-0');
                        toast.classList.add('translate-x-0', 'opacity-100');
                    });

                    // Auto-suppression
                    setTimeout(() => {
                        this.remove(toast);
                    }, duration);

                    return toast;
                }

                createToast(type, message, description) {
                    const toast = document.createElement('div');
                    toast.className = `transform transition-all duration-300 translate-x-full opacity-0 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden`;

                    const colors = {
                        success: 'text-green-500',
                        error: 'text-red-500',
                        warning: 'text-yellow-500',
                        info: 'text-blue-500'
                    };

                    const icons = {
                        success: '✓',
                        error: '✕',
                        warning: '⚠',
                        info: 'ℹ'
                    };

                    toast.innerHTML = `
                        <div class="p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full ${colors[type]} bg-opacity-10">
                                        ${icons[type]}
                                    </span>
                                </div>
                                <div class="ml-3 w-0 flex-1 pt-0.5">
                                    <p class="text-sm font-medium text-gray-900">${message}</p>
                                    ${description ? `<p class="mt-1 text-sm text-gray-500">${description}</p>` : ''}
                                </div>
                                <div class="ml-4 flex-shrink-0 flex">
                                    <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="this.closest('.transform').remove()">
                                        <span class="sr-only">Fermer</span>
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;

                    return toast;
                }

                remove(toast) {
                    toast.classList.remove('translate-x-0', 'opacity-100');
                    toast.classList.add('translate-x-full', 'opacity-0');
                    
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 300);
                }
            }

            // Initialisation du système de toast
            document.addEventListener('DOMContentLoaded', () => {
                new ToastSystem();
            });

            // Gestion des messages flash Laravel
            @if(session('success'))
                window.addEventListener('DOMContentLoaded', () => {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'success',
                            message: 'Succès',
                            description: '{{ session('success') }}'
                        }
                    }));
                });
            @endif

            @if(session('error'))
                window.addEventListener('DOMContentLoaded', () => {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'error',
                            message: 'Erreur',
                            description: '{{ session('error') }}'
                        }
                    }));
                });
            @endif

            @if(session('warning'))
                window.addEventListener('DOMContentLoaded', () => {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'warning',
                            message: 'Attention',
                            description: '{{ session('warning') }}'
                        }
                    }));
                });
            @endif

            @if(session('info'))
                window.addEventListener('DOMContentLoaded', () => {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'info',
                            message: 'Information',
                            description: '{{ session('info') }}'
                        }
                    }));
                });
            @endif
        </script>

        <!-- Service Worker pour PWA (optionnel) -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then((registration) => {
                            console.log('SW registered: ', registration);
                        })
                        .catch((registrationError) => {
                            console.log('SW registration failed: ', registrationError);
                        });
                });
            }
        </script>
    </body>
</html>

