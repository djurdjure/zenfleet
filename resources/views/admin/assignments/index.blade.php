{{-- resources/views/admin/assignments/index-refactored.blade.php --}}
@extends('layouts.admin.catalyst')

@section('title', 'Gestion des Affectations')

@section('content')
{{-- ====================================================================
🚗 PAGE AFFECTATIONS - ULTRA-PROFESSIONAL ENTERPRISE GRADE
====================================================================

DESIGN PRINCIPLES:
✨ Fond gris clair (bg-gray-50) pour la page
✨ Max-width 7xl centré avec padding responsive
✨ Header avec icône + titre + count badge
✨ Metric cards: 4-6 statistiques clés
✨ Search + Filters: Barre recherche + filtre collapsible
✨ Affectations Table: Liste avec statuts colorés
✨ Pagination: Info + navigation
✨ Cohérence totale avec pages Véhicules

@version 1.0-Ultra-Pro-Enterprise-Standard
@since 2025-10-20
==================================================================== --}}

{{-- Message de succès session --}}
@if(session('success'))
 <div x-data="{ show: true }"
 x-show="show"
 x-init="setTimeout(() => show = false, 5000)"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform scale-90"
 x-transition:enter-end="opacity-100 transform scale-100"
 x-transition:leave="transition ease-in duration-300"
 x-transition:leave-start="opacity-100 transform scale-100"
 x-transition:leave-end="opacity-0 transform scale-90"
 class="fixed top-4 right-4 z-50 max-w-md">
 <x-alert type="success" title="Succès" dismissible>
 {{ session('success') }}
 </x-alert>
 </div>
@endif

<section class="bg-gray-50 min-h-screen">
 <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

 {{-- ====================================================================
 HEADER - ULTRA-PRO DESIGN
 ===================================================================== --}}
 <div class="mb-6">
 <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
 <x-iconify icon="lucide:calendar-clock" class="w-6 h-6 text-blue-600" />
 Affectations
 <span class="ml-2 text-sm font-normal text-gray-500">
 ({{ $assignments->total() }} total)
 </span>
 </h1>
 <p class="text-sm text-gray-600 ml-8.5">
 Gérez les affectations de véhicules à vos chauffeurs
 </p>
 </div>

 {{-- ====================================================================
 METRIC CARDS - KEY STATISTICS
 ===================================================================== --}}
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
 {{-- Total Affectations --}}
 <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Total Affectations</p>
 <p class="text-2xl font-bold text-gray-900 mt-1">{{ $assignments->total() }}</p>
 </div>
 <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:calendar-check" class="w-6 h-6 text-blue-600" />
 </div>
 </div>
 </div>

 {{-- Actives --}}
 <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Affectations Actives</p>
 <p class="text-2xl font-bold text-green-600 mt-1">{{ $activeAssignments ?? 0 }}</p>
 </div>
 <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:check-circle" class="w-6 h-6 text-green-600" />
 </div>
 </div>
 </div>

 {{-- En Cours --}}
 <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">En Cours</p>
 <p class="text-2xl font-bold text-orange-600 mt-1">{{ $inProgressAssignments ?? 0 }}</p>
 </div>
 <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:clock" class="w-6 h-6 text-orange-600" />
 </div>
 </div>
 </div>

 {{-- Planifiées --}}
 <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Planifiées</p>
 <p class="text-2xl font-bold text-purple-600 mt-1">{{ $scheduledAssignments ?? 0 }}</p>
 </div>
 <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:calendar" class="w-6 h-6 text-purple-600" />
 </div>
 </div>
 </div>
 </div>

 {{-- ====================================================================
 SEARCH + FILTERS - PROFESSIONAL DESIGN
 ===================================================================== --}}
 <div class="mb-6" x-data="{ showFilters: false }">
 {{-- Barre d'actions sur une seule ligne --}}
 <div class="flex flex-col md:flex-row gap-3 items-center mb-4">
 {{-- Search Bar - Réduit --}}
 <form action="{{ route('admin.assignments.index') }}" method="GET" class="w-full md:w-96">
 <div class="relative">
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
 </div>
 <input
 type="text"
 name="search"
 value="{{ request('search') }}"
 placeholder="Rechercher..."
 class="pl-10 pr-4 py-2.5 block w-full border-gray-300 rounded-lg
 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm"
 />
 </div>
 </form>

 {{-- Boutons d'actions groupés --}}
 <div class="flex gap-2 w-full md:w-auto md:ml-auto">
 {{-- Filter Button --}}
 <button
 @click="showFilters = !showFilters"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300
 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md flex-1 md:flex-none justify-center">
 <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
 <span class="font-medium text-gray-700">Filtres</span>
 </button>

 {{-- Nouvelle Affectation Button --}}
 <a href="{{ route('admin.assignments.create') }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white
 rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md flex-1 md:flex-none justify-center font-medium">
 <x-iconify icon="lucide:plus-circle" class="w-5 h-5" />
 <span>Nouvelle affectation</span>
 </a>
 </div>
 </div>

 {{-- Collapsible Filter Panel --}}
 <div class="w-full">
 <div x-show="showFilters"
 x-collapse
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 max-h-0"
 x-transition:enter-end="opacity-100 max-h-96"
 class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
 <form action="{{ route('admin.assignments.index') }}" method="GET">
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
 {{-- Status Filter --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
 <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
 <option value="">Tous les statuts</option>
 <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Planifiée</option>
 <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
 <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Complétée</option>
 </select>
 </div>

 {{-- Vehicle Filter --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">Véhicule</label>
 <select name="vehicle" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
 <option value="">Tous les véhicules</option>
 @foreach($vehicles as $vehicle)
 <option value="{{ $vehicle->id }}" {{ request('vehicle') == $vehicle->id ? 'selected' : '' }}>
 {{ $vehicle->registration_plate }} ({{ $vehicle->brand }})
 </option>
 @endforeach
 </select>
 </div>

 {{-- Driver Filter --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">Chauffeur</label>
 <select name="driver" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
 <option value="">Tous les chauffeurs</option>
 @foreach($drivers as $driver)
 <option value="{{ $driver->id }}" {{ request('driver') == $driver->id ? 'selected' : '' }}>
 {{ $driver->name }}
 </option>
 @endforeach
 </select>
 </div>

 {{-- Date Range --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
 <input type="date" name="date_from" value="{{ request('date_from') }}"
 class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
 </div>
 </div>

 <div class="mt-4 flex gap-2">
 <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
 Appliquer
 </button>
 <a href="{{ route('admin.assignments.index') }}"
 class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm font-medium">
 Réinitialiser
 </a>
 </div>
 </form>
 </div>
 </div>
 </div>



 {{-- ====================================================================
 ASSIGNMENTS TABLE - ULTRA-PRO DESIGN
 ===================================================================== --}}
 @if($assignments->count() > 0)
 <x-card padding="p-0" margin="mb-6">
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200">
 {{-- Table Header --}}
 <thead class="bg-gray-50">
 <tr>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Référence
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Véhicule
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Chauffeur
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Dates
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Statut
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Actions
 </th>
 </tr>
 </thead>

 {{-- Table Body --}}
 <tbody class="bg-white divide-y divide-gray-200">
 @foreach($assignments as $assignment)
 <tr class="hover:bg-gray-50 transition-colors duration-150">
 {{-- Référence --}}
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="text-sm font-semibold text-gray-900">
 {{ $assignment->reference ?? '#' . $assignment->id }}
 </div>
 <div class="text-xs text-gray-500 mt-0.5">
 Créée le {{ $assignment->created_at->format('d/m/Y') }}
 </div>
 </td>

 {{-- Véhicule --}}
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex items-center">
 <div class="flex-shrink-0 h-10 w-10">
 <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
 <x-iconify icon="lucide:car" class="h-5 w-5 text-gray-500" />
 </div>
 </div>
 <div class="ml-4">
 <div class="text-sm font-semibold text-gray-900">
 {{ $assignment->vehicle->registration_plate ?? 'N/A' }}
 </div>
 <div class="text-sm text-gray-500">
 {{ $assignment->vehicle->brand ?? '' }} {{ $assignment->vehicle->model ?? '' }}
 </div>
 </div>
 </div>
 </td>

 {{-- Chauffeur --}}
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex items-center">
 <div class="flex-shrink-0 h-10 w-10">
 <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
 <x-iconify icon="lucide:user" class="h-5 w-5 text-blue-600" />
 </div>
 </div>
 <div class="ml-4">
 <div class="text-sm font-semibold text-gray-900">
 {{ $assignment->driver->name ?? 'N/A' }}
 </div>
 <div class="text-sm text-gray-500">
 {{ $assignment->driver->phone ?? '' }}
 </div>
 </div>
 </div>
 </td>

 {{-- Dates --}}
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="text-sm text-gray-900">
 {{ $assignment->start_date?->format('d/m/Y') ?? '-' }}
 </div>
 <div class="text-sm text-gray-500">
 au {{ $assignment->end_date?->format('d/m/Y') ?? '-' }}
 </div>
 </td>

 {{-- Statut --}}
 <td class="px-6 py-4 whitespace-nowrap">
 @php
 $statusConfig = [
 'scheduled' => ['badge' => 'bg-purple-100 text-purple-800', 'label' => 'Planifiée'],
 'active' => ['badge' => 'bg-green-100 text-green-800', 'label' => 'Active'],
 'completed' => ['badge' => 'bg-blue-100 text-blue-800', 'label' => 'Complétée'],
 ];
 $status = $statusConfig[$assignment->status] ?? ['badge' => 'bg-gray-100 text-gray-800', 'label' => $assignment->status];
 @endphp
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['badge'] }}">
 {{ $status['label'] }}
 </span>
 </td>

 {{-- Actions --}}
 <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
 <div class="flex items-center gap-2">
 <a href="{{ route('admin.assignments.show', $assignment) }}"
 class="text-blue-600 hover:text-blue-900 transition-colors"
 title="Voir">
 <x-iconify icon="lucide:eye" class="w-4 h-4" />
 </a>
 <a href="{{ route('admin.assignments.edit', $assignment) }}"
 class="text-blue-600 hover:text-blue-900 transition-colors"
 title="Éditer">
 <x-iconify icon="lucide:pencil" class="w-4 h-4" />
 </a>
 <form action="{{ route('admin.assignments.destroy', $assignment) }}" method="POST" class="inline"
 onsubmit="return confirm('Êtes-vous sûr ?');">
 @csrf
 @method('DELETE')
 <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Supprimer">
 <x-iconify icon="lucide:trash-2" class="w-4 h-4" />
 </button>
 </form>
 </div>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </x-card>

 {{-- Pagination --}}
 <div class="mt-6 bg-white rounded-lg border border-gray-200 px-6 py-4 shadow-sm">
 <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
 <div class="flex items-center gap-4">
 <div class="text-sm text-gray-600">
 Affichage <span class="font-semibold">{{ $assignments->firstItem() }}</span> à
 <span class="font-semibold">{{ $assignments->lastItem() }}</span> de
 <span class="font-semibold">{{ $assignments->total() }}</span> affectations
 </div>
 <div class="flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-200 rounded-md">
 <span class="text-xs font-medium text-blue-700">
 Page {{ $assignments->currentPage() }} / {{ $assignments->lastPage() }}
 </span>
 </div>
 </div>
 <div>
 {{ $assignments->appends(request()->query())->links() }}
 </div>
 </div>
 </div>
 @else
 {{-- Empty State --}}
 <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
 <x-iconify icon="lucide:calendar-clock" class="mx-auto h-12 w-12 text-gray-400" />
 <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune affectation</h3>
 <p class="mt-1 text-sm text-gray-500">Commencez par créer une affectation.</p>
 <div class="mt-6">
 <a href="{{ route('admin.assignments.create') }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg
 hover:bg-blue-700 transition-colors text-sm font-medium shadow-sm">
 <x-iconify icon="lucide:plus" class="w-5 h-5" />
 Nouvelle Affectation
 </a>
 </div>
 </div>
 @endif

 </div>
</section>

@endsection
