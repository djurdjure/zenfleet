@extends('layouts.admin.catalyst')

@section('title', 'Maintenance - Vue Kanban')

@section('content')
<section class="min-h-screen bg-[#f8fafc]">
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
                            <span class="text-gray-900 font-medium">Kanban</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Title & Actions --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div>
                    <h1 class="text-xl font-bold text-gray-600 flex items-center gap-2">
                        <x-iconify icon="lucide:columns-3" class="w-6 h-6 text-blue-600" />
                        Tableau Kanban - Maintenance
                    </h1>
                    <p class="mt-1 text-xs text-gray-600">
                        Glissez-déposez les opérations pour changer leur statut
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Toggle View --}}
                    <div class="inline-flex rounded-lg bg-gray-100 p-1 border border-gray-200">
                        <a href="{{ route('admin.maintenance.operations.index') }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-white transition-colors duration-200 inline-flex items-center gap-1.5">
                            <x-iconify icon="lucide:list" class="w-4 h-4 inline mr-1" />
                            Liste
                        </a>
                        <span class="px-4 py-2 text-sm font-semibold text-white bg-[#0c90ee] rounded-md inline-flex items-center gap-1.5">
                            <x-iconify icon="lucide:columns-3" class="w-4 h-4" />
                            Kanban
                        </span>
                        <a href="{{ route('admin.maintenance.operations.calendar') }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-white transition-colors duration-200 inline-flex items-center gap-1.5">
                            <x-iconify icon="lucide:calendar" class="w-4 h-4" />
                            Calendrier
                        </a>
                    </div>

                    @can('create', \App\Models\MaintenanceOperation::class)
                        <a href="{{ route('admin.maintenance.operations.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 border border-[#0c90ee] bg-[#0c90ee] text-white text-sm font-semibold rounded-lg hover:bg-[#0a7fd1] hover:border-[#0a7fd1] transition-colors duration-200 shadow-sm">
                            <x-iconify icon="lucide:plus" class="w-4 h-4" />
                            Nouvelle Opération
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- LIVEWIRE KANBAN COMPONENT --}}
        @livewire('admin.maintenance.maintenance-kanban')

    </div>
</section>
@endsection
