<div class="space-y-6">
 {{-- 🔍 BARRE DE RECHERCHE ET FILTRES --}}
 <div class="bg-white rounded-lg shadow-sm p-6">
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
 {{-- Recherche globale --}}
 <div class="lg:col-span-2">
 <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
 Recherche
 </label>
 <div class="relative">
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
 </div>
 <input
 type="text"
 id="search"
 wire:model.live.debounce.500ms="search"
 placeholder="Rechercher par titre, description, véhicule, chauffeur..."
 wire:loading.attr="aria-busy"
 wire:target="search"
 class="pl-10 pr-4 py-2.5 block w-full bg-white border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
 >
 <div wire:loading.delay wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
 <x-iconify icon="lucide:loader-2" class="w-4 h-4 text-blue-500 animate-spin" />
 </div>
 </div>
 </div>

 {{-- Filtre statut --}}
 <div>
 <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-2">
 Statut
 </label>
 <select
 id="statusFilter"
 wire:model.live="statusFilter"
 class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 "
 >
 <option value="">Tous les statuts</option>
 @foreach($statuses as $value => $label)
 <option value="{{ $value }}">{{ $label }}</option>
 @endforeach
 </select>
 </div>

 {{-- Filtre urgence --}}
 <div>
 <label for="urgencyFilter" class="block text-sm font-medium text-gray-700 mb-2">
 Urgence
 </label>
 <select
 id="urgencyFilter"
 wire:model.live="urgencyFilter"
 class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 "
 >
 <option value="">Toutes les urgences</option>
 @foreach($urgencyLevels as $value => $label)
 <option value="{{ $value }}">{{ $label }}</option>
 @endforeach
 </select>
 </div>
 </div>

 {{-- Bouton reset filtres --}}
 @if($search || $statusFilter || $urgencyFilter)
 <div class="mt-4">
 <button wire:click="resetFilters" class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1 font-medium bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors">
 <x-iconify icon="lucide:x" class="w-4 h-4" />
 Réinitialiser
 </button>
 </div>
 @endif
 </div>

 {{-- 📊 TABLE DES DEMANDES --}}
 <div class="bg-white rounded-lg shadow-sm overflow-hidden">
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200 ">
 <thead class="bg-gray-50 ">
 <tr>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 :bg-gray-800" wire:click="sortBy('uuid')">
 <div class="flex items-center space-x-1">
 <span>ID</span>
 @if($sortField === 'uuid')
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 @if($sortDirection === 'asc')
 <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path>
 @else
 <path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"></path>
 @endif
 </svg>
 @endif
 </div>
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Véhicule
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Demande
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Chauffeur
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 :bg-gray-800" wire:click="sortBy('urgency')">
 <div class="flex items-center space-x-1">
 <span>Urgence</span>
 @if($sortField === 'urgency')
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 @if($sortDirection === 'asc')
 <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path>
 @else
 <path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"></path>
 @endif
 </svg>
 @endif
 </div>
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 :bg-gray-800" wire:click="sortBy('status')">
 <div class="flex items-center space-x-1">
 <span>Statut</span>
 @if($sortField === 'status')
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 @if($sortDirection === 'asc')
 <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path>
 @else
 <path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"></path>
 @endif
 </svg>
 @endif
 </div>
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 :bg-gray-800" wire:click="sortBy('created_at')">
 <div class="flex items-center space-x-1">
 <span>Date</span>
 @if($sortField === 'created_at')
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 @if($sortDirection === 'asc')
 <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path>
 @else
 <path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"></path>
 @endif
 </svg>
 @endif
 </div>
 </th>
 <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
 Actions
 </th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200 ">
 @forelse($repairRequests as $request)
 <tr class="hover:bg-gray-50 :bg-gray-700 transition-colors">
 {{-- UUID --}}
 <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 ">
 #{{ substr($request->uuid, 0, 8) }}
 </td>

 {{-- Véhicule --}}
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="text-sm font-medium text-gray-900 ">
 {{ $request->vehicle->vehicle_name ?? $request->vehicle->license_plate }}
 </div>
 <div class="text-sm text-gray-500 ">
 {{ $request->vehicle->brand }} {{ $request->vehicle->model }}
 </div>
 </td>

 {{-- Demande --}}
 <td class="px-6 py-4">
 <div class="text-sm font-medium text-gray-900 ">
 {{ Str::limit($request->title, 40) }}
 </div>
 <div class="text-sm text-gray-500 ">
 {{ Str::limit($request->description, 60) }}
 </div>
 </td>

 {{-- Chauffeur --}}
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="text-sm text-gray-900 ">
 {{ $request->driver->user->name ?? 'N/A' }}
 </div>
 </td>

 {{-- Urgence --}}
 <td class="px-6 py-4 whitespace-nowrap">
 @php
 $urgencyConfig = [
 'low' => ['bg' => 'bg-green-50 text-green-700 border border-green-200', 'label' => 'Faible'],
 'normal' => ['bg' => 'bg-blue-50 text-blue-700 border border-blue-200', 'label' => 'Normal'],
 'high' => ['bg' => 'bg-orange-50 text-orange-700 border border-orange-200', 'label' => 'Élevé'],
 'critical' => ['bg' => 'bg-red-50 text-red-700 border border-red-200', 'label' => 'Critique'],
 ];
 $config = $urgencyConfig[$request->urgency] ?? $urgencyConfig['normal'];
 @endphp
 <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $config['bg'] }}">
 {{ $config['label'] }}
 </span>
 </td>

 {{-- Statut --}}
 <td class="px-6 py-4 whitespace-nowrap">
 <x-repair-status-badge :status="$request->status" />
 </td>

 {{-- Date --}}
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">
 {{ $request->created_at->format('d/m/Y') }}
 <div class="text-xs">{{ $request->created_at->format('H:i') }}</div>
 </td>

 {{-- Actions --}}
 <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
 <div class="flex items-center justify-end space-x-2">
 {{-- Voir --}}
 <a
 href="{{ route('admin.repair-requests.show', $request) }}"
 class="text-indigo-600 hover:text-indigo-900 :text-indigo-300"
 title="Voir les détails"
 >
 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
 </svg>
 </a>

 {{-- Approuver (Supervisor) --}}
 @can('approveLevelOne', $request)
 <button
 wire:click="$dispatch('open-approval-modal', { requestId: {{ $request->id }}, action: 'approve', level: 'supervisor' })"
 class="text-green-600 hover:text-green-900 :text-green-300"
 title="Approuver (Superviseur)"
 >
 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 </button>
 @endcan

 {{-- Approuver (Fleet Manager) --}}
 @can('approveLevelTwo', $request)
 <button
 wire:click="$dispatch('open-approval-modal', { requestId: {{ $request->id }}, action: 'approve', level: 'fleet_manager' })"
 class="text-green-600 hover:text-green-900 :text-green-300"
 title="Approuver (Gestionnaire)"
 >
 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 </button>
 @endcan

 {{-- Rejeter --}}
 @can('rejectLevelOne', $request)
 <button
 wire:click="$dispatch('open-approval-modal', { requestId: {{ $request->id }}, action: 'reject', level: 'supervisor' })"
 class="text-red-600 hover:text-red-900 :text-red-300"
 title="Rejeter"
 >
 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 </button>
 @endcan

 @can('rejectLevelTwo', $request)
 <button
 wire:click="$dispatch('open-approval-modal', { requestId: {{ $request->id }}, action: 'reject', level: 'fleet_manager' })"
 class="text-red-600 hover:text-red-900 :text-red-300"
 title="Rejeter"
 >
 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 </button>
 @endcan
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="8" class="px-6 py-12 text-center text-gray-500 ">
 <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
 </svg>
 <p class="mt-2 text-sm">Aucune demande de réparation trouvée.</p>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>

 {{-- Pagination --}}
 <div class="mt-4">
 <x-pagination :paginator="$repairRequests" :records-per-page="$perPage" wire:model.live="perPage" />
 </div>
</div>
</div>
