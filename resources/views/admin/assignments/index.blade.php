{{-- resources/views/admin/assignments/index-refactored.blade.php --}}
@php
use Illuminate\Support\Facades\Storage;
@endphp

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
 Réf
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Véhicule
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Chauffeur
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Période
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Créé le
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Statut
 </th>
 <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
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
 #{{ $assignment->id }}
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

 {{-- Chauffeur - FIXED with proper attributes and photo --}}
 <td class="px-6 py-4 whitespace-nowrap">
 @if($assignment->driver)
 <div class="flex items-center">
 {{-- Avatar/Photo Premium --}}
 <div class="flex-shrink-0 h-10 w-10">
 @if($assignment->driver->photo)
 <img src="{{ Storage::url($assignment->driver->photo) }}"
 alt="{{ $assignment->driver->full_name }}"
 class="h-10 w-10 rounded-full object-cover ring-2 ring-blue-100 shadow-sm">
 @else
 <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center ring-2 ring-blue-100 shadow-sm">
 <span class="text-sm font-bold text-white">
 {{ strtoupper(substr($assignment->driver->first_name, 0, 1)) }}{{ strtoupper(substr($assignment->driver->last_name, 0, 1)) }}
 </span>
 </div>
 @endif
 </div>
 {{-- Driver Information --}}
 <div class="ml-3">
 <div class="text-sm font-semibold text-gray-900">
 {{ $assignment->driver->full_name }}
 </div>
 <div class="flex items-center gap-1 text-xs text-gray-500">
 <x-iconify icon="lucide:phone" class="w-3.5 h-3.5" />
 <span>{{ $assignment->driver->personal_phone ?? $assignment->driver->professional_phone ?? 'N/A' }}</span>
 </div>
 </div>
 </div>
 @else
 <div class="flex items-center gap-2 text-sm text-gray-400">
 <x-iconify icon="lucide:user-x" class="w-5 h-5" />
 <span class="italic">Non assigné</span>
 </div>
 @endif
 </td>

 {{-- Période - FIXED with start_datetime and end_datetime --}}
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="text-sm text-gray-900">
 <x-iconify icon="lucide:calendar-check" class="w-4 h-4 inline mr-1 text-green-600" />
 {{ $assignment->start_datetime?->format('d/m/Y H:i') ?? '-' }}
 </div>
 <div class="text-sm text-gray-500">
 <x-iconify icon="lucide:calendar-x" class="w-4 h-4 inline mr-1 text-orange-600" />
 {{ $assignment->end_datetime?->format('d/m/Y H:i') ?? 'Indéterminé' }}
 </div>
 </td>

 {{-- Créé le - NEW COLUMN --}}
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="text-sm text-gray-900">
 {{ $assignment->created_at->format('d/m/Y') }}
 </div>
 <div class="text-xs text-gray-500">
 {{ $assignment->created_at->format('H:i') }}
 </div>
 </td>

 {{-- Statut --}}
 <td class="px-6 py-4 whitespace-nowrap">
 @php
 $statusConfig = [
 'scheduled' => ['badge' => 'bg-purple-100 text-purple-800', 'icon' => 'lucide:clock', 'label' => 'Planifiée'],
 'active' => ['badge' => 'bg-green-100 text-green-800', 'icon' => 'lucide:play-circle', 'label' => 'Active'],
 'completed' => ['badge' => 'bg-blue-100 text-blue-800', 'icon' => 'lucide:check-circle', 'label' => 'Terminée'],
 'cancelled' => ['badge' => 'bg-red-100 text-red-800', 'icon' => 'lucide:x-circle', 'label' => 'Annulée'],
 ];
 $status = $statusConfig[$assignment->status] ?? ['badge' => 'bg-gray-100 text-gray-800', 'icon' => 'lucide:help-circle', 'label' => $assignment->status];
 @endphp
 <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['badge'] }}">
 <x-iconify :icon="$status['icon']" class="w-3.5 h-3.5" />
 {{ $status['label'] }}
 </span>
 </td>

 {{-- Actions - Enterprise-Grade Three-Dot Menu + Terminer Button --}}
 <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
 <div class="flex items-center justify-center gap-1">
 {{-- Terminer Button (only for active/ongoing assignments) --}}
 @if($assignment->canBeEnded())
 <button onclick="endAssignment({{ $assignment->id }}, '{{ addslashes($assignment->vehicle->registration_plate) }}', '{{ addslashes($assignment->driver->full_name) }}')"
 class="inline-flex items-center p-1.5 text-orange-600 hover:text-orange-700 hover:bg-orange-50 rounded-lg transition-all duration-200"
 title="Terminer l'affectation">
 <x-iconify icon="lucide:flag-triangle-right" class="w-4 h-4" />
 </button>
 @endif

 {{-- View Button --}}
 <a href="{{ route('admin.assignments.show', $assignment) }}"
 class="inline-flex items-center p-1.5 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all duration-200"
 title="Voir détails">
 <x-iconify icon="lucide:eye" class="w-4 h-4" />
 </a>

 {{-- Three-Dot Menu --}}
 <div class="relative inline-block text-left" x-data="{ open: false }">
 <button @click="open = !open"
 @click.away="open = false"
 type="button"
 class="inline-flex items-center p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200"
 id="assignment-menu-{{ $assignment->id }}">
 <x-iconify icon="lucide:more-vertical" class="w-4 h-4" />
 </button>

 <div x-show="open"
 x-cloak
 x-transition:enter="transition ease-out duration-100"
 x-transition:enter-start="transform opacity-0 scale-95"
 x-transition:enter-end="transform opacity-100 scale-100"
 x-transition:leave="transition ease-in duration-75"
 x-transition:leave-start="transform opacity-100 scale-100"
 x-transition:leave-end="transform opacity-0 scale-95"
 class="absolute right-0 z-50 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
 <div class="py-1">
 {{-- Edit --}}
 @if($assignment->canBeEdited())
 <a href="{{ route('admin.assignments.edit', $assignment) }}"
 class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
 <x-iconify icon="lucide:edit" class="w-4 h-4 mr-3 text-amber-600" />
 Modifier
 </a>
 @endif

 {{-- Export PDF --}}
 <button onclick="exportAssignmentPDF({{ $assignment->id }})"
 class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
 <x-iconify icon="lucide:file-text" class="w-4 h-4 mr-3 text-emerald-600" />
 Exporter PDF
 </button>

 {{-- History --}}
 <a href="{{ route('admin.assignments.show', $assignment) }}#history"
 class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
 <x-iconify icon="lucide:clock" class="w-4 h-4 mr-3 text-cyan-600" />
 Historique
 </a>

 {{-- Delete --}}
 @if($assignment->canBeDeleted())
 <div class="border-t border-gray-100 mt-1 pt-1">
 <button onclick="deleteAssignment({{ $assignment->id }}, '{{ $assignment->vehicle->registration_plate }}', '{{ $assignment->driver->full_name }}')"
 class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-red-50 transition-colors">
 <x-iconify icon="lucide:trash-2" class="w-4 h-4 mr-3 text-red-600" />
 Supprimer
 </button>
 </div>
 @endif
 </div>
 </div>
 </div>
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

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════════════════════════
// ASSIGNMENT MANAGEMENT - ENTERPRISE ULTRA PRO
// ═══════════════════════════════════════════════════════════════════════════

/**
 * Terminer une affectation avec modal ultra-professionnelle
 */
function endAssignment(assignmentId, vehiclePlate, driverName) {
    // Générer la date/heure actuelle au format datetime-local
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const currentDatetime = `${year}-${month}-${day}T${hours}:${minutes}`;

    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.setAttribute('aria-labelledby', 'modal-title');
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');

    modal.innerHTML = `
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Terminer l'affectation
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Voulez-vous terminer cette affectation ? Cette action enregistrera la date et l'heure de fin.
                            </p>

                            {{-- Assignment Details --}}
                            <div class="mt-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-blue-900">${vehiclePlate}</p>
                                        <p class="text-sm text-blue-700">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            ${driverName}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Formulaire de fin --}}
                            <div class="mt-4 space-y-3">
                                {{-- Date et heure de fin - OBLIGATOIRE --}}
                                <div>
                                    <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-1">
                                        Date et heure de fin <span class="text-red-600">*</span>
                                    </label>
                                    <input type="datetime-local"
                                           id="end_datetime"
                                           name="end_datetime"
                                           value="${currentDatetime}"
                                           required
                                           class="block w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm">
                                    <p class="mt-1 text-xs text-gray-500">
                                        Champ obligatoire
                                    </p>
                                </div>

                                {{-- Kilométrage de fin - OPTIONNEL --}}
                                <div>
                                    <label for="end_mileage" class="block text-sm font-medium text-gray-700 mb-1">
                                        Kilométrage de fin (optionnel)
                                    </label>
                                    <input type="number"
                                           id="end_mileage"
                                           name="end_mileage"
                                           placeholder="Ex: 125000"
                                           class="block w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm">
                                </div>

                                {{-- Notes - OPTIONNEL --}}
                                <div>
                                    <label for="end_notes" class="block text-sm font-medium text-gray-700 mb-1">
                                        Observations de fin (optionnel)
                                    </label>
                                    <textarea id="end_notes"
                                              name="end_notes"
                                              rows="2"
                                              maxlength="1000"
                                              placeholder="État du véhicule, remarques particulières..."
                                              class="block w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="button"
                            onclick="confirmEndAssignment(${assignmentId})"
                            class="w-full inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-orange-600 hover:bg-orange-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Confirmer la fin
                    </button>
                    <button type="button"
                            onclick="closeModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
}

/**
 * Confirmer la fin d'affectation
 */
function confirmEndAssignment(assignmentId) {
    const endDatetime = document.getElementById('end_datetime')?.value;
    const endMileage = document.getElementById('end_mileage')?.value || null;
    const endNotes = document.getElementById('end_notes')?.value || null;

    // Validation: end_datetime est OBLIGATOIRE
    if (!endDatetime) {
        alert('Veuillez sélectionner la date et l\'heure de fin.');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/assignments/${assignmentId}/end`;

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'PATCH';
    form.appendChild(methodInput);

    // end_datetime OBLIGATOIRE
    const datetimeInput = document.createElement('input');
    datetimeInput.type = 'hidden';
    datetimeInput.name = 'end_datetime';
    datetimeInput.value = endDatetime;
    form.appendChild(datetimeInput);

    if (endMileage) {
        const mileageInput = document.createElement('input');
        mileageInput.type = 'hidden';
        mileageInput.name = 'end_mileage';
        mileageInput.value = endMileage;
        form.appendChild(mileageInput);
    }

    if (endNotes) {
        const notesInput = document.createElement('input');
        notesInput.type = 'hidden';
        notesInput.name = 'notes';
        notesInput.value = endNotes;
        form.appendChild(notesInput);
    }

    document.body.appendChild(form);
    closeModal();
    setTimeout(() => form.submit(), 200);
}

/**
 * Supprimer une affectation avec modal de confirmation
 */
function deleteAssignment(assignmentId, vehiclePlate, driverName) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.setAttribute('aria-labelledby', 'modal-title');
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');

    modal.innerHTML = `
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Supprimer l'affectation
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                <strong class="text-red-600">⚠️ ATTENTION :</strong> Cette action supprimera définitivement cette affectation.
                            </p>
                            <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-6 w-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-red-900">${vehiclePlate}</p>
                                        <p class="text-sm text-red-700">${driverName}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="button"
                            onclick="confirmDeleteAssignment(${assignmentId})"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 hover:bg-red-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Confirmer la suppression
                    </button>
                    <button type="button"
                            onclick="closeModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
}

/**
 * Confirmer la suppression
 */
function confirmDeleteAssignment(assignmentId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/assignments/${assignmentId}`;

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);

    document.body.appendChild(form);
    closeModal();
    setTimeout(() => form.submit(), 200);
}

/**
 * Exporter une affectation en PDF
 */
function exportAssignmentPDF(assignmentId) {
    window.open(`/admin/assignments/${assignmentId}/export/pdf`, '_blank');
}

/**
 * Fermer la modal
 */
function closeModal() {
    const modal = document.querySelector('.fixed.inset-0.z-50');
    if (modal) {
        modal.style.opacity = '0';
        modal.style.transform = 'scale(0.95)';
        setTimeout(() => modal.remove(), 200);
    }
}
</script>
@endpush
