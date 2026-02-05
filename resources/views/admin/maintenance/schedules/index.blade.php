@extends('layouts.admin.catalyst')

@section('title', 'Planifications Maintenance')

@section('content')
<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

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
                            <span class="text-gray-900 font-medium">Planifications</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Title & Actions --}}
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                        <x-iconify icon="lucide:repeat" class="w-6 h-6 text-blue-600" />
                        Planifications de Maintenance
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Gérez les maintenances préventives récurrentes
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.maintenance.schedules.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm">
                        <x-iconify icon="lucide:plus" class="w-4 h-4" />
                        Nouvelle Planification
                    </a>
                </div>
            </div>
        </div>

        {{-- Messages Flash --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <x-iconify icon="lucide:check-circle" class="w-5 h-5 text-green-600" />
                    <span class="text-sm text-green-800">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600" />
                    <span class="text-sm text-red-800">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 uppercase">Total</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $schedules->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:repeat" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 uppercase">Actives</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">
                            {{ $schedules->where('is_active', true)->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:check-circle" class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 uppercase">Inactives</p>
                        <p class="text-2xl font-bold text-gray-600 mt-1">
                            {{ $schedules->where('is_active', false)->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:pause-circle" class="w-6 h-6 text-gray-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 uppercase">Véhicules</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">
                            {{ $schedules->unique('vehicle_id')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:car" class="w-6 h-6 text-purple-600" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Véhicule
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type Maintenance
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Intervalle
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($schedules as $schedule)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <x-iconify icon="lucide:car" class="w-5 h-5 text-blue-600" />
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $schedule->vehicle->registration_plate }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $schedule->vehicle->brand }} {{ $schedule->vehicle->model }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $schedule->maintenanceType->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ ucfirst($schedule->maintenanceType->category) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($schedule->interval_value_km)
                                            {{ number_format($schedule->interval_value_km, 0, ',', ' ') }} km
                                        @endif
                                        @if($schedule->interval_value_km && $schedule->interval_value_days)
                                            ou
                                        @endif
                                        @if($schedule->interval_value_days)
                                            {{ $schedule->interval_value_days }} jours
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($schedule->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.maintenance.schedules.show', $schedule) }}"
                                           class="text-blue-600 hover:text-blue-900">
                                            <x-iconify icon="lucide:eye" class="w-5 h-5" />
                                        </a>
                                        <a href="{{ route('admin.maintenance.schedules.edit', $schedule) }}"
                                           class="text-gray-600 hover:text-gray-900">
                                            <x-iconify icon="lucide:pencil" class="w-5 h-5" />
                                        </a>
                                        <form action="{{ route('admin.maintenance.schedules.toggle', $schedule) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="text-{{ $schedule->is_active ? 'red' : 'green' }}-600 hover:text-{{ $schedule->is_active ? 'red' : 'green' }}-900">
                                                <x-iconify icon="lucide:{{ $schedule->is_active ? 'pause' : 'play' }}-circle" class="w-5 h-5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <x-iconify icon="lucide:repeat" class="w-8 h-8 text-gray-400" />
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            Aucune planification
                                        </h3>
                                        <p class="text-gray-600 mb-4">
                                            Commencez par créer une planification de maintenance préventive.
                                        </p>
                                        <a href="{{ route('admin.maintenance.schedules.create') }}"
                                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                                            <x-iconify icon="lucide:plus" class="w-4 h-4" />
                                            Créer une planification
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($schedules->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    {{ $schedules->links() }}
                </div>
            @endif
        </div>

    </div>
</section>
@endsection
