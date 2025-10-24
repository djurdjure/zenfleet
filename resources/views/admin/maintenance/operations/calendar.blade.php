@extends('layouts.admin.catalyst')

@section('title', 'Maintenance - Calendrier')

@section('content')
<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-full lg:py-6">

        {{-- HEADER --}}
        <div class="mb-6">
            {{-- Breadcrumb --}}
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.maintenance.operations.index') }}" class="text-gray-600 hover:text-blue-600">
                            <x-iconify icon="lucide:wrench" class="w-4 h-4 mr-2 inline" />
                            Maintenance
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <x-iconify icon="lucide:chevron-right" class="w-4 h-4 text-gray-400 mx-1" />
                            <span class="text-gray-900 font-medium">Calendrier</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Title & Actions --}}
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                        <x-iconify icon="lucide:calendar-days" class="w-6 h-6 text-blue-600" />
                        Calendrier des Maintenances
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Visualisez toutes vos opérations planifiées
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Toggle View --}}
                    <div class="inline-flex rounded-lg bg-gray-100 p-1">
                        <a href="{{ route('admin.maintenance.operations.index') }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-white transition-colors duration-200">
                            <x-iconify icon="lucide:list" class="w-4 h-4 inline mr-1" />
                            Liste
                        </a>
                        <a href="{{ route('admin.maintenance.operations.kanban') }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-white transition-colors duration-200">
                            <x-iconify icon="lucide:columns-3" class="w-4 h-4 inline mr-1" />
                            Kanban
                        </a>
                        <span class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md">
                            <x-iconify icon="lucide:calendar" class="w-4 h-4 inline mr-1" />
                            Calendrier
                        </span>
                    </div>

                    @can('create', \App\Models\MaintenanceOperation::class)
                        <a href="{{ route('admin.maintenance.operations.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm">
                            <x-iconify icon="lucide:plus" class="w-4 h-4" />
                            Nouvelle Opération
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- LIVEWIRE CALENDAR COMPONENT --}}
        @livewire('admin.maintenance.maintenance-calendar')

    </div>
</section>
@endsection
