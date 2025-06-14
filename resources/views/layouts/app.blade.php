<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>




    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-gray-100">
            <div class="flex h-screen bg-gray-100">
                <aside class="hidden w-64 flex-shrink-0 bg-white border-r md:block">
                    @include('layouts.navigation')
                </aside>

                <div x-show="sidebarOpen" class="fixed inset-0 z-40 flex md:hidden" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                    <div @click="sidebarOpen = false" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
                    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
                        <div class="absolute top-0 right-0 -mr-12 pt-2">
                            <button @click="sidebarOpen = false" type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                                <span class="sr-only">Close sidebar</span>
                                <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        @include('layouts.navigation')
                    </div>
                </div>

                <div class="flex-1 flex flex-col overflow-hidden">
                    <header class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
                        <button @click.stop="sidebarOpen = true" type="button" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-violet-500 md:hidden">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                        <div class="flex-1 px-4 flex justify-between">
                            <div class="flex-1 flex">
                                </div>
                            <div class="ml-4 flex items-center md:ml-6">
                                <div class="hidden sm:flex sm:items-center sm:ms-6">
                                    <x-dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                <div>{{ Auth::user()->name }}</div>
                                                <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                                            </button>
                                        </x-slot>
                                        <x-slot name="content">
                                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                                            </form>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            </div>
                        </div>
                    </header>

                    @if (isset($header))
                        <div class="bg-white shadow-sm">
                            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </div>
                    @endif

                    <main class="flex-1 relative overflow-y-auto focus:outline-none">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
        
        @stack('scripts')

    </body>
</html>
