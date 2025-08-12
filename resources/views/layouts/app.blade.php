<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="{{ asset('js/tomselect-users-config.js') }}"></script>
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-gray-100">
            <div class="flex h-screen bg-gray-100">
                <aside class="hidden w-72 flex-shrink-0 bg-white border-r md:block">
                    @include('layouts.navigation')
                </aside>

                <div x-show="sidebarOpen" class="fixed inset-0 z-40 flex md:hidden" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                    <div @click="sidebarOpen = false" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
                    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
                        <div class="absolute top-0 right-0 -mr-12 pt-2">
                            <button @click="sidebarOpen = false" type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                                <span class="sr-only">Close sidebar</span>
                                <x-lucide-x class="h-6 w-6 text-white" />
                            </button>
                        </div>
                        @include('layouts.navigation')
                    </div>
                </div>

                <div class="flex-1 flex flex-col overflow-hidden">
                    <header class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
                        <button @click.stop="sidebarOpen = true" type="button" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 md:hidden">
                            <span class="sr-only">Open sidebar</span>
                            <x-lucide-menu class="h-6 w-6" />
                        </button>
                        <div class="flex-1 px-4 flex justify-between">
                            <div class="flex-1 flex items-center">
                                @if (isset($header))
                                    {{ $header }}
                                @endif
                            </div>
                        </div>
                    </header>

                    <main class="flex-1 relative overflow-y-auto focus:outline-none">
                         <div class="py-6">
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                                {{ $slot }}
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
        <x-toast />
        @stack('scripts')
    </body>
</html>