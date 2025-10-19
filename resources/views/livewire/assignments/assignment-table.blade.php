<div class="space-y-6" x-data="{ autoHideTimer: null }" x-on:auto-hide-message.window="clearTimeout(autoHideTimer); autoHideTimer = setTimeout(() => $wire.clearMessage(), 5000)">

 {{-- Messages de notification --}}
 @if($message)
 <div class="rounded-md p-4 {{ $messageType === 'success' ? 'bg-green-50 border border-green-200' : ($messageType === 'error' ? 'bg-red-50 border border-red-200' : 'bg-blue-50 border border-blue-200') }}">
 <div class="flex">
 <div class="flex-shrink-0">
 @if($messageType === 'success')
 <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.53 10.3a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
 </svg>
 @elseif($messageType === 'error')
 <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
 </svg>
 @else
 <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
 </svg>
 @endif
 </div>
 <div class="ml-3">
 <p class="text-sm {{ $messageType === 'success' ? 'text-green-800' : ($messageType === 'error' ? 'text-red-800' : 'text-blue-800') }}">
 {{ $message }}
 </p>
 </div>
 <div class="ml-auto pl-3">
 <button wire:click="clearMessage" class="inline-flex rounded-md {{ $messageType === 'success' ? 'bg-green-50 text-green-500 hover:bg-green-100' : ($messageType === 'error' ? 'bg-red-50 text-red-500 hover:bg-red-100' : 'bg-blue-50 text-blue-500 hover:bg-blue-100') }} p-1.5">
 <span class="sr-only">Fermer</span>
 <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
 <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
 </svg>
 </button>
 </div>
 </div>
 </div>
 @endif

 {{-- En-tête et actions --}}
 <div class="sm:flex sm:items-center sm:justify-between">
 <div>
 <h1 class="text-2xl font-semibold text-gray-900">Affectations</h1>
 <p class="mt-2 text-sm text-gray-700">Gestion des affectations véhicule ↔ chauffeur</p>
 </div>
 <div class="mt-4 sm:mt-0 sm:flex sm:space-x-3">
 @can('export', App\Models\Assignment::class)
 <button wire:click="exportCsv" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
 <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
 </svg>
 Exporter CSV
 </button>
 @endcan

 @can('create', App\Models\Assignment::class)
 <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
 <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
 </svg>
 Nouvelle affectation
 </button>
 @endcan
 </div>
 </div>

 {{-- Filtres et recherche --}}
 <div class="bg-white shadow rounded-lg">
 <div class="px-6 py-4 border-b border-gray-200">
 <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6">
 {{-- Recherche globale --}}
 <div class="col-span-1 sm:col-span-2">
 <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
 <input wire:model.live.debounce.300ms="search" type="text" id="search" placeholder="Véhicule, chauffeur, motif..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 </div>

 {{-- Filtre statut --}}
 <div>
 <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
 <select wire:model.live="statusFilter" id="status-filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 <option value="">Tous</option>
 @foreach($statusOptions as $value => $label)
 <option value="{{ $value }}">{{ $label }}</option>
 @endforeach
 </select>
 </div>

 {{-- Filtre véhicule --}}
 <div>
 <label for="vehicle-filter" class="block text-sm font-medium text-gray-700 mb-1">Véhicule</label>
 <select wire:model.live="vehicleFilter" id="vehicle-filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 <option value="">Tous</option>
 @foreach($vehicles as $vehicle)
 <option value="{{ $vehicle['id'] }}">{{ $vehicle['label'] }}</option>
 @endforeach
 </select>
 </div>

 {{-- Filtre chauffeur --}}
 <div>
 <label for="driver-filter" class="block text-sm font-medium text-gray-700 mb-1">Chauffeur</label>
 <select wire:model.live="driverFilter" id="driver-filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 <option value="">Tous</option>
 @foreach($drivers as $driver)
 <option value="{{ $driver['id'] }}">{{ $driver['label'] }}</option>
 @endforeach
 </select>
 </div>

 {{-- Période --}}
 <div class="grid grid-cols-2 gap-2">
 <div>
 <label for="date-from" class="block text-sm font-medium text-gray-700 mb-1">Du</label>
 <input wire:model.live="dateFromFilter" type="date" id="date-from" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 </div>
 <div>
 <label for="date-to" class="block text-sm font-medium text-gray-700 mb-1">Au</label>
 <input wire:model.live="dateToFilter" type="date" id="date-to" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 </div>
 </div>
 </div>

 {{-- Options et actions --}}
 <div class="mt-4 flex items-center justify-between">
 <div class="flex items-center space-x-4">
 <label class="flex items-center">
 <input wire:model.live="onlyOngoing" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
 <span class="ml-2 text-sm text-gray-700">Seulement en cours</span>
 </label>

 <select wire:model.live="perPage" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
 <option value="25">25 par page</option>
 <option value="50">50 par page</option>
 <option value="100">100 par page</option>
 </select>
 </div>

 <button wire:click="resetFilters" class="text-sm text-gray-500 hover:text-gray-700">
 Réinitialiser filtres
 </button>
 </div>
 </div>

 {{-- Tableau --}}
 <div class="overflow-hidden">
 @if($assignments->count() > 0)
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200">
 <thead class="bg-gray-50">
 <tr>
 <th wire:click="sortBy('vehicle_id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
 <div class="flex items-center space-x-1">
 <span>Véhicule</span>
 @if($sortField === 'vehicle_id')
 <svg class="h-4 w-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
 </svg>
 @endif
 </div>
 </th>
 <th wire:click="sortBy('driver_id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
 <div class="flex items-center space-x-1">
 <span>Chauffeur</span>
 @if($sortField === 'driver_id')
 <svg class="h-4 w-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
 </svg>
 @endif
 </div>
 </th>
 <th wire:click="sortBy('start_datetime')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
 <div class="flex items-center space-x-1">
 <span>Date/Heure remise</span>
 @if($sortField === 'start_datetime')
 <svg class="h-4 w-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
 </svg>
 @endif
 </div>
 </th>
 <th wire:click="sortBy('end_datetime')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
 <div class="flex items-center space-x-1">
 <span>Date/Heure restitution</span>
 @if($sortField === 'end_datetime')
 <svg class="h-4 w-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
 </svg>
 @endif
 </div>
 </th>
 <th wire:click="sortBy('status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
 <div class="flex items-center space-x-1">
 <span>Statut</span>
 @if($sortField === 'status')
 <svg class="h-4 w-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
 </svg>
 @endif
 </div>
 </th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durée</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
 <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @foreach($assignments as $assignment)
 <tr class="hover:bg-gray-50">
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="h-8 w-8 bg-gray-200 rounded-full flex items-center justify-center">
 <svg class="h-4 w-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8 5a1 1 0 011-1h2a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0V10H7a1 1 0 110-2h3V5z" clip-rule="evenodd" />
 </svg>
 </div>
 </div>
 <div class="ml-3">
 <div class="text-sm font-medium text-gray-900">
 {{ $assignment->vehicle_display }}
 </div>
 @if($assignment->vehicle)
 <div class="text-sm text-gray-500">
 {{ $assignment->vehicle->brand }} {{ $assignment->vehicle->model }}
 </div>
 @endif
 </div>
 </div>
 </td>

 <td class="px-6 py-4 whitespace-nowrap">
 <div class="text-sm font-medium text-gray-900">{{ $assignment->driver_display }}</div>
 @if($assignment->driver && $assignment->driver->phone_number)
 <div class="text-sm text-gray-500">{{ $assignment->driver->phone_number }}</div>
 @endif
 </td>

 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
 {{ $assignment->start_datetime->format('d/m/Y H:i') }}
 </td>

 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
 @if($assignment->end_datetime)
 {{ $assignment->end_datetime->format('d/m/Y H:i') }}
 @else
 <span class="text-orange-600 font-medium">En cours</span>
 @endif
 </td>

 <td class="px-6 py-4 whitespace-nowrap">
 <x-assignment-status-badge :status="$assignment->status" />
 </td>

 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
 {{ $assignment->formatted_duration }}
 </td>

 <td class="px-6 py-4 text-sm text-gray-900">
 <div class="max-w-xs truncate" title="{{ $assignment->reason }}">
 {{ $assignment->reason ?: '-' }}
 </div>
 </td>

 <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
 <div class="flex items-center justify-end space-x-2">
 {{-- Voir/Éditer --}}
 @can('view', $assignment)
 <button wire:click="openEditModal({{ $assignment->id }})" class="text-blue-600 hover:text-blue-900" title="Modifier">
 <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
 </svg>
 </button>
 @endcan

 {{-- Terminer --}}
 @if($assignment->canBeEnded())
 @can('end', $assignment)
 <button wire:click="openEndModal({{ $assignment->id }})" class="text-green-600 hover:text-green-900" title="Terminer">
 <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
 </svg>
 </button>
 @endcan
 @endif

 {{-- Dupliquer --}}
 @can('create', App\Models\Assignment::class)
 <button wire:click="duplicateAssignment({{ $assignment->id }})" class="text-purple-600 hover:text-purple-900" title="Dupliquer">
 <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
 </svg>
 </button>
 @endcan

 {{-- Supprimer --}}
 @if($assignment->canBeDeleted())
 @can('delete', $assignment)
 <button wire:click="openDeleteModal({{ $assignment->id }})" class="text-red-600 hover:text-red-900" title="Supprimer">
 <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
 </svg>
 </button>
 @endcan
 @endif
 </div>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>

 {{-- Pagination --}}
 <div class="px-6 py-4 border-t border-gray-200">
 {{ $assignments->links() }}
 </div>
 @else
 {{-- État vide --}}
 <div class="text-center py-12">
 <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
 </svg>
 <h3 class="mt-4 text-lg font-medium text-gray-900">Aucune affectation trouvée</h3>
 <p class="mt-2 text-gray-500">
 @if($search || $statusFilter || $vehicleFilter || $driverFilter || $dateFromFilter || $dateToFilter || $onlyOngoing)
 Aucune affectation ne correspond à vos critères de recherche.
 @else
 Commencez par créer votre première affectation véhicule ↔ chauffeur.
 @endif
 </p>
 <div class="mt-6">
 @if($search || $statusFilter || $vehicleFilter || $driverFilter || $dateFromFilter || $dateToFilter || $onlyOngoing)
 <button wire:click="resetFilters" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
 Réinitialiser les filtres
 </button>
 @else
 @can('create', App\Models\Assignment::class)
 <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
 <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
 </svg>
 Créer une affectation
 </button>
 @endcan
 @endif
 </div>
 </div>
 @endif
 </div>
 </div>

 {{-- Modales --}}

 {{-- Modal formulaire --}}
 @if($showFormModal)
 <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeFormModal"></div>
 <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
 <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
 <livewire:assignments.assignment-form :assignment="$selectedAssignment" :key="'assignment-form-' . ($selectedAssignment?->id ?? 'new')" />
 </div>
 </div>
 </div>
 @endif

 {{-- Modal confirmation fin --}}
 @if($showEndModal && $selectedAssignment)
 <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeEndModal"></div>
 <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
 <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
 <div class="sm:flex sm:items-start">
 <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
 <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
 </svg>
 </div>
 <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
 <h3 class="text-lg leading-6 font-medium text-gray-900">Terminer l'affectation</h3>
 <div class="mt-2">
 <p class="text-sm text-gray-500">
 Voulez-vous terminer l'affectation du véhicule <strong>{{ $selectedAssignment->vehicle_display }}</strong>
 au chauffeur <strong>{{ $selectedAssignment->driver_display }}</strong> ?
 </p>
 <p class="mt-2 text-sm text-gray-500">
 Date de restitution : <strong>{{ now()->format('d/m/Y à H:i') }}</strong>
 </p>
 </div>
 </div>
 </div>
 <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
 <button wire:click="confirmEnd" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
 Confirmer
 </button>
 <button wire:click="closeEndModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
 Annuler
 </button>
 </div>
 </div>
 </div>
 </div>
 @endif

 {{-- Modal confirmation suppression --}}
 @if($showDeleteModal && $selectedAssignment)
 <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeDeleteModal"></div>
 <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
 <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
 <div class="sm:flex sm:items-start">
 <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
 <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
 </svg>
 </div>
 <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
 <h3 class="text-lg leading-6 font-medium text-gray-900">Supprimer l'affectation</h3>
 <div class="mt-2">
 <p class="text-sm text-gray-500">
 Voulez-vous vraiment supprimer l'affectation du véhicule <strong>{{ $selectedAssignment->vehicle_display }}</strong>
 au chauffeur <strong>{{ $selectedAssignment->driver_display }}</strong> ?
 </p>
 <p class="mt-2 text-sm text-red-600 font-medium">
 Cette action est irréversible.
 </p>
 </div>
 </div>
 </div>
 <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
 <button wire:click="confirmDelete" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
 Supprimer
 </button>
 <button wire:click="closeDeleteModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
 Annuler
 </button>
 </div>
 </div>
 </div>
 </div>
 @endif
</div>