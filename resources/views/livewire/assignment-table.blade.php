{{-- 📊 Vue Table des Affectations - Enterprise Grade --}}
<div class="space-y-6" x-data="assignmentTable()">
 {{-- Barre de filtres et actions --}}
 <div class="bg-white shadow rounded-lg p-6">
 <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
 <h2 class="text-xl font-semibold text-gray-900">
 Affectations Véhicule ↔ Chauffeur
 <span class="text-sm font-normal text-gray-500 ml-2">
 ({{ $assignments->total() }} {{ Str::plural('résultat', $assignments->total()) }})
 </span>
 </h2>

 <div class="flex items-center space-x-3">
 @can('create', App\Models\Assignment::class)
 <button wire:click="openCreateModal"
 class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
 <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
 </svg>
 Nouvelle Affectation
 </button>
 @endcan

 <button wire:click="exportCsv"
 class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
 <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
 </svg>
 Export CSV
 </button>
 </div>
 </div>

 {{-- Filtres --}}
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-4">
 {{-- Recherche globale --}}
 <div class="lg:col-span-2">
 <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
 <input wire:model.live.debounce.300ms="search"
 type="text"
 id="search"
 placeholder="Véhicule, chauffeur, motif..."
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 </div>

 {{-- Filtre véhicule --}}
 <div>
 <label for="vehicleFilter" class="block text-sm font-medium text-gray-700 mb-1">Véhicule</label>
 <select wire:model.live="vehicleFilter" id="vehicleFilter"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 <option value="">Tous les véhicules</option>
 @foreach($vehicleOptions as $vehicle)
 <option value="{{ $vehicle->id }}">
 {{ $vehicle->registration_plate ?? ($vehicle->brand . ' ' . $vehicle->model) }}
 </option>
 @endforeach
 </select>
 </div>

 {{-- Filtre chauffeur --}}
 <div>
 <label for="driverFilter" class="block text-sm font-medium text-gray-700 mb-1">Chauffeur</label>
 <select wire:model.live="driverFilter" id="driverFilter"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 <option value="">Tous les chauffeurs</option>
 @foreach($driverOptions as $driver)
 <option value="{{ $driver->id }}">{{ $driver->first_name }} {{ $driver->last_name }}</option>
 @endforeach
 </select>
 </div>

 {{-- Filtre statut --}}
 <div>
 <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
 <select wire:model.live="statusFilter" id="statusFilter"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 <option value="">Tous les statuts</option>
 @foreach($statusOptions as $value => $label)
 <option value="{{ $value }}">{{ $label }}</option>
 @endforeach
 </select>
 </div>
 </div>

 {{-- Filtres de date et options --}}
 <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
 <div>
 <label for="dateFromFilter" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
 <input wire:model.live="dateFromFilter"
 type="date"
 id="dateFromFilter"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 </div>

 <div>
 <label for="dateToFilter" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
 <input wire:model.live="dateToFilter"
 type="date"
 id="dateToFilter"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 </div>

 <div class="flex items-end">
 <label class="flex items-center">
 <input wire:model.live="onlyOngoing"
 type="checkbox"
 class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
 <span class="ml-2 text-sm text-gray-700">Seulement en cours</span>
 </label>
 </div>

 <div class="flex items-end space-x-2">
 <button wire:click="resetFilters"
 class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
 Réinitialiser
 </button>

 <select wire:model.live="perPage"
 class="block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 @foreach($perPageOptions as $option)
 <option value="{{ $option }}">{{ $option }}/page</option>
 @endforeach
 </select>
 </div>
 </div>
 </div>

 {{-- Tableau --}}
 <div class="bg-white shadow rounded-lg overflow-hidden">
 @if($assignments->count() > 0)
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200">
 <thead class="bg-gray-50">
 <tr>
 {{-- Véhicule --}}
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
 wire:click="sortBy('vehicle')">
 <div class="flex items-center space-x-1">
 <span>Véhicule</span>
 @if($sortBy === 'vehicle')
 <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
 </svg>
 @endif
 </div>
 </th>

 {{-- Chauffeur --}}
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
 wire:click="sortBy('driver')">
 <div class="flex items-center space-x-1">
 <span>Chauffeur</span>
 @if($sortBy === 'driver')
 <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
 </svg>
 @endif
 </div>
 </th>

 {{-- Date/Heure remise --}}
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
 wire:click="sortBy('start_datetime')">
 <div class="flex items-center space-x-1">
 <span>Date/Heure remise</span>
 @if($sortBy === 'start_datetime')
 <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
 </svg>
 @endif
 </div>
 </th>

 {{-- Date/Heure restitution --}}
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Date/Heure restitution
 </th>

 {{-- Statut --}}
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
 wire:click="sortBy('status')">
 <div class="flex items-center space-x-1">
 <span>Statut</span>
 @if($sortBy === 'status')
 <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
 </svg>
 @endif
 </div>
 </th>

 {{-- Durée --}}
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Durée
 </th>

 {{-- Actions --}}
 <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
 Actions
 </th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @foreach($assignments as $assignment)
 <tr class="hover:bg-gray-50 transition-colors duration-150">
 {{-- Véhicule --}}
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex items-center">
 <div class="flex-shrink-0 h-10 w-10">
 <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
 <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4">
 <div class="text-sm font-medium text-gray-900">
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
 <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
 <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4">
 <div class="text-sm font-medium text-gray-900">
 {{ $assignment->driver->first_name ?? '' }} {{ $assignment->driver->last_name ?? '' }}
 </div>
 <div class="text-sm text-gray-500">
 {{ $assignment->driver->phone_number ?? '' }}
 </div>
 </div>
 </div>
 </td>

 {{-- Date/Heure remise --}}
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
 <div class="font-medium">{{ $assignment->start_datetime->format('d/m/Y') }}</div>
 <div class="text-gray-500">{{ $assignment->start_datetime->format('H:i') }}</div>
 </td>

 {{-- Date/Heure restitution --}}
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
 @if($assignment->end_datetime)
 <div class="font-medium">{{ $assignment->end_datetime->format('d/m/Y') }}</div>
 <div class="text-gray-500">{{ $assignment->end_datetime->format('H:i') }}</div>
 @else
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
 Indéterminé
 </span>
 @endif
 </td>

 {{-- Statut --}}
 <td class="px-6 py-4 whitespace-nowrap">
 @php
 $statusConfig = [
 'scheduled' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
 'active' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
 'completed' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'M5 13l4 4L19 7']
 ];
 $config = $statusConfig[$assignment->status] ?? $statusConfig['active'];
 @endphp
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
 <svg class="mr-1.5 h-2 w-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"></path>
 </svg>
 {{ $assignment->status_label }}
 </span>
 </td>

 {{-- Durée --}}
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
 {{ $assignment->formatted_duration }}
 </td>

 {{-- Actions --}}
 <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
 <div class="flex items-center justify-end space-x-2">
 {{-- Voir/Éditer --}}
 @can('update', $assignment)
 <button wire:click="openEditModal({{ $assignment->id }})"
 class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-100"
 title="Modifier">
 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
 </svg>
 </button>
 @endcan

 {{-- Terminer (si en cours) --}}
 @if($assignment->is_ongoing)
 @can('update', $assignment)
 <button wire:click="openTerminateModal({{ $assignment->id }})"
 class="text-yellow-600 hover:text-yellow-900 p-1 rounded-full hover:bg-yellow-100"
 title="Terminer">
 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
 </svg>
 </button>
 @endcan
 @endif

 {{-- Dupliquer --}}
 @can('create', App\Models\Assignment::class)
 <button wire:click="duplicateAssignment({{ $assignment->id }})"
 class="text-green-600 hover:text-green-900 p-1 rounded-full hover:bg-green-100"
 title="Dupliquer">
 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
 </svg>
 </button>
 @endcan

 {{-- Supprimer --}}
 @can('delete', $assignment)
 <button wire:click="openDeleteModal({{ $assignment->id }})"
 class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100"
 title="Supprimer">
 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
 </svg>
 </button>
 @endcan
 </div>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>

 {{-- Pagination --}}
 <div class="bg-white px-6 py-3 border-t border-gray-200">
 {{ $assignments->links() }}
 </div>
 @else
 {{-- État vide --}}
 <div class="text-center py-12">
 <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.712-3.714M14 40v-4c0-1.313.253-2.566.712-3.714m0 0A10.003 10.003 0 0124 26c4.21 0 7.863 2.602 9.288 6.286"></path>
 </svg>
 <h3 class="mt-4 text-sm font-medium text-gray-900">Aucune affectation trouvée</h3>
 <p class="mt-2 text-sm text-gray-500">
 @if($search || $vehicleFilter || $driverFilter || $statusFilter || $dateFromFilter || $dateToFilter || $onlyOngoing)
 Aucune affectation ne correspond aux critères de filtre actuels.
 <button wire:click="resetFilters" class="text-blue-600 hover:text-blue-800 font-medium">
 Réinitialiser les filtres
 </button>
 @else
 Commencez par créer votre première affectation véhicule ↔ chauffeur.
 @endif
 </p>
 @can('create', App\Models\Assignment::class)
 <div class="mt-6">
 <button wire:click="openCreateModal"
 class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm bg-blue-600 text-sm font-medium text-white hover:bg-blue-700">
 <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
 </svg>
 Créer une affectation
 </button>
 </div>
 @endcan
 </div>
 @endif
 </div>

 {{-- Modal Terminer Affectation --}}
 @if($showTerminateModal && $selectedAssignment)
 <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModals"></div>

 <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
 <div class="sm:flex sm:items-start">
 <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
 <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
 </svg>
 </div>
 <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
 <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
 Terminer l'affectation
 </h3>
 <div class="mt-2">
 <p class="text-sm text-gray-500">
 {{ $selectedAssignment->vehicle_display }} → {{ $selectedAssignment->driver_display }}
 </p>
 <p class="text-xs text-gray-400 mt-1">
 Début: {{ $selectedAssignment->start_datetime->format('d/m/Y H:i') }}
 </p>
 </div>

 {{-- Indication du kilométrage actuel du véhicule --}}
 @if($selectedAssignment->vehicle && $selectedAssignment->vehicle->current_mileage)
 <div class="mt-3 bg-blue-50 rounded-md p-3 border border-blue-200">
 <div class="flex items-start">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
 </svg>
 </div>
 <div class="ml-3 flex-1">
 <p class="text-xs font-medium text-blue-700 uppercase tracking-wider">Kilométrage actuel du véhicule</p>
 <p class="mt-0.5 text-base font-bold text-blue-900 font-mono">
 {{ number_format($selectedAssignment->vehicle->current_mileage) }} km
 </p>
 <p class="mt-0.5 text-xs text-blue-600">
 Enregistré dans le système pour information
 </p>
 </div>
 </div>
 </div>
 @endif

 <div class="mt-4 space-y-4">
 <div>
 <label for="terminateDateTime" class="block text-sm font-medium text-gray-700">
 Date et heure de fin *
 </label>
 <input wire:model="terminateDateTime"
 type="datetime-local"
 id="terminateDateTime"
 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 @error('terminateDateTime')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="terminateNotes" class="block text-sm font-medium text-gray-700">
 Notes de terminaison
 </label>
 <textarea wire:model="terminateNotes"
 id="terminateNotes"
 rows="3"
 placeholder="Commentaires sur la terminaison de l'affectation..."
 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
 @error('terminateNotes')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>
 </div>
 </div>

 <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
 <button wire:click="terminateAssignment"
 class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
 Terminer l'affectation
 </button>
 <button wire:click="closeModals"
 class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
 Annuler
 </button>
 </div>
 </div>
 </div>
 </div>
 @endif

 {{-- Modal Suppression --}}
 @if($showDeleteModal && $selectedAssignment)
 <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModals"></div>

 <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
 <div class="sm:flex sm:items-start">
 <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
 <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
 </svg>
 </div>
 <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
 <h3 class="text-lg leading-6 font-medium text-gray-900">
 Supprimer l'affectation
 </h3>
 <div class="mt-2">
 <p class="text-sm text-gray-500">
 Êtes-vous sûr de vouloir supprimer cette affectation ?<br>
 <strong>{{ $selectedAssignment->vehicle_display }} → {{ $selectedAssignment->driver_display }}</strong><br>
 Cette action ne peut pas être annulée.
 </p>
 </div>
 </div>
 </div>

 <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
 <button wire:click="deleteAssignment"
 class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
 Supprimer
 </button>
 <button wire:click="closeModals"
 class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
 Annuler
 </button>
 </div>
 </div>
 </div>
 </div>
 @endif
</div>

@push('scripts')
<script>
function assignmentTable() {
 return {
 init() {
 // Écouter les événements de téléchargement CSV
 window.addEventListener('download-csv', event => {
 this.downloadCsv(event.detail.filename, event.detail.data);
 });
 },

 downloadCsv(filename, data) {
 const csvContent = data.map(row =>
 row.map(field => `"${String(field).replace(/"/g, '""')}"`).join(',')
 ).join('\n');

 const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
 const link = document.createElement('a');

 if (link.download !== undefined) {
 const url = URL.createObjectURL(blob);
 link.setAttribute('href', url);
 link.setAttribute('download', filename);
 link.style.visibility = 'hidden';
 document.body.appendChild(link);
 link.click();
 document.body.removeChild(link);
 }
 }
 };
}
</script>
@endpush