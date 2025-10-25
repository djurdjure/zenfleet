{{-- ====================================================================
 📊 RELEVÉS KILOMÉTRIQUES V7.0 - ULTRA-PROFESSIONAL ENTERPRISE-GRADE
 ====================================================================

 Design aligné avec modules Chauffeurs et Véhicules:
 ✨ Fond gris clair premium (bg-gray-50)
 ✨ Header compact moderne (py-4 lg:py-6)
 ✨ 5 Cards métriques riches en information
 ✨ Barre recherche + filtres + actions sur 1 ligne
 ✨ Icônes Lucide via Iconify
 ✨ Table ultra-lisible avec info véhicule
 ✨ Pagination séparée en bas
 ✨ Thème clair 100% (pas de dark mode)

 @version 7.0-World-Class-Light-Theme
 @since 2025-01-20
 ==================================================================== --}}

<div>
<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- ===============================================
            HEADER ULTRA-COMPACT
        =============================================== --}}
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:gauge" class="w-6 h-6 text-blue-600" />
                Relevés Kilométriques
                <span class="ml-2 text-sm font-normal text-gray-500">
                    ({{ isset($readings) ? $readings->total() : 0 }})
                </span>
            </h1>
        </div>

        {{-- ===============================================
            CARDS MÉTRIQUES ULTRA-PRO
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            {{-- Total Relevés --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Total relevés</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">
                            {{ number_format($stats['total_readings'] ?? 0) }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:gauge" class="w-5 h-5 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- Relevés Manuels --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Manuels</p>
                        <p class="text-xl font-bold text-green-600 mt-1">
                            {{ number_format($stats['manual_count'] ?? 0) }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:hand" class="w-5 h-5 text-green-600" />
                    </div>
                </div>
            </div>

            {{-- Relevés Automatiques --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Automatiques</p>
                        <p class="text-xl font-bold text-purple-600 mt-1">
                            {{ number_format($stats['automatic_count'] ?? 0) }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:cpu" class="w-5 h-5 text-purple-600" />
                    </div>
                </div>
            </div>

            {{-- Véhicules Suivis --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Véhicules suivis</p>
                        <p class="text-xl font-bold text-orange-600 mt-1">
                            {{ number_format($stats['vehicles_tracked'] ?? 0) }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:car" class="w-5 h-5 text-orange-600" />
                    </div>
                </div>
            </div>

            {{-- Dernier Relevé --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Dernier relevé</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">
                            {{ isset($stats['last_reading_date']) ? $stats['last_reading_date']->format('d/m/Y') : 'N/A' }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:clock" class="w-5 h-5 text-amber-600" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
            FLASH MESSAGES ULTRA-PRO
        =============================================== --}}
        @if (session()->has('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-iconify icon="lucide:check-circle-2" class="w-5 h-5 text-green-600" />
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- ===============================================
            BARRE RECHERCHE + FILTRES + ACTIONS (1 LIGNE)
        =============================================== --}}
        <div class="mb-6" x-data="{ showFilters: false }">
            {{-- Ligne principale: Recherche + Filtres + Boutons Actions --}}
            <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">
                {{-- Recherche rapide --}}
                <div class="flex-1 w-full lg:w-auto">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                        </div>
                        <input 
                            wire:model.live.debounce.300ms="search"
                            type="text"
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                            placeholder="Rechercher par véhicule, kilométrage, auteur...">
                    </div>
                </div>

                {{-- Bouton filtres avancés --}}
                <button 
                    @click="showFilters = !showFilters"
                    class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200 text-sm font-medium text-gray-700 shadow-sm">
                    <x-iconify icon="lucide:filter" class="w-5 h-5" />
                    <span class="hidden sm:inline">Filtres</span>
                    <x-iconify 
                        icon="lucide:chevron-down" 
                        class="w-4 h-4 transition-transform duration-200"
                        x-bind:class="showFilters ? 'rotate-180' : ''"
                    />
                </button>

                {{-- Bouton Actualiser --}}
                <button 
                    wire:click="$refresh"
                    class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200 text-sm font-medium text-gray-700 shadow-sm">
                    <x-iconify icon="lucide:refresh-cw" class="w-5 h-5" />
                    <span class="hidden sm:inline">Actualiser</span>
                </button>

                {{-- Bouton Ajouter Relevé --}}
                @can('create mileage readings')
                <a href="{{ route('admin.mileage-readings.update') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:plus" class="w-5 h-5" />
                    <span class="hidden sm:inline">Nouveau relevé</span>
                </a>
                @endcan
            </div>

            {{-- Panel Filtres Avancés (Collapsible) --}}
            <div x-show="showFilters"
                 x-collapse
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mt-4 bg-white rounded-lg border border-gray-200 p-6 shadow-sm">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- Filtre Véhicule --}}
                    <div>
                        <label for="vehicle-filter" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:car" class="w-4 h-4 inline-block mr-1" />
                            Véhicule
                        </label>
                        <select 
                            wire:model.live="vehicleFilter"
                            id="vehicle-filter"
                            class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous les véhicules</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->registration_plate }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtre Méthode --}}
                    <div>
                        <label for="method-filter" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:settings" class="w-4 h-4 inline-block mr-1" />
                            Méthode
                        </label>
                        <select 
                            wire:model.live="methodFilter"
                            id="method-filter"
                            class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Toutes les méthodes</option>
                            <option value="manual">Manuel</option>
                            <option value="automatic">Automatique</option>
                        </select>
                    </div>

                    {{-- Date De --}}
                    <div>
                        <label for="date-from" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:calendar" class="w-4 h-4 inline-block mr-1" />
                            Date de
                        </label>
                        <input 
                            wire:model.live="dateFrom"
                            type="date"
                            id="date-from"
                            class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    {{-- Date À --}}
                    <div>
                        <label for="date-to" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:calendar" class="w-4 h-4 inline-block mr-1" />
                            Date à
                        </label>
                        <input 
                            wire:model.live="dateTo"
                            type="date"
                            id="date-to"
                            class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                </div>

                <div class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4">
                    <button 
                        wire:click="resetFilters"
                        class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <x-iconify icon="lucide:x" class="w-4 h-4" />
                        Réinitialiser les filtres
                    </button>

                    <div class="text-sm text-gray-600">
                        <span class="font-semibold">{{ $readings->total() }}</span> résultat(s)
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
            TABLE ULTRA-PRO
        =============================================== --}}
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th 
                                wire:click="sortBy('vehicle')"
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                <div class="flex items-center gap-1.5">
                                    <x-iconify icon="lucide:car" class="w-4 h-4" />
                                    <span>Véhicule</span>
                                    @if ($sortField === 'vehicle')
                                        <x-iconify 
                                            icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" 
                                            class="w-4 h-4"
                                        />
                                    @endif
                                </div>
                            </th>
                            <th 
                                wire:click="sortBy('recorded_at')"
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                <div class="flex items-center gap-1.5">
                                    <x-iconify icon="lucide:calendar-clock" class="w-4 h-4" />
                                    <span>Date & Heure</span>
                                    @if ($sortField === 'recorded_at')
                                        <x-iconify 
                                            icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" 
                                            class="w-4 h-4"
                                        />
                                    @endif
                                </div>
                            </th>
                            <th 
                                wire:click="sortBy('mileage')"
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                <div class="flex items-center gap-1.5">
                                    <x-iconify icon="lucide:gauge" class="w-4 h-4" />
                                    <span>Kilométrage</span>
                                    @if ($sortField === 'mileage')
                                        <x-iconify 
                                            icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" 
                                            class="w-4 h-4"
                                        />
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1.5">
                                    <x-iconify icon="lucide:user" class="w-4 h-4" />
                                    <span>Auteur</span>
                                </div>
                            </th>
                            <th 
                                wire:click="sortBy('recording_method')"
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                <div class="flex items-center gap-1.5">
                                    <x-iconify icon="lucide:settings" class="w-4 h-4" />
                                    <span>Méthode</span>
                                    @if ($sortField === 'recording_method')
                                        <x-iconify 
                                            icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" 
                                            class="w-4 h-4"
                                        />
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($readings as $reading)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Véhicule --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <x-iconify icon="lucide:car" class="w-5 h-5 text-blue-600" />
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $reading->vehicle->registration_plate }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $reading->vehicle->brand }} {{ $reading->vehicle->model }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Date & Heure --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">{{ $reading->recorded_at->format('d/m/Y') }}</span>
                                    <span class="text-xs text-gray-500">{{ $reading->recorded_at->format('H:i') }}</span>
                                </div>
                            </td>

                            {{-- Kilométrage --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-900">
                                    {{ number_format($reading->mileage) }} km
                                </span>
                            </td>

                            {{-- Auteur --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($reading->recordedBy)
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                        <span class="text-blue-600 font-semibold text-xs">
                                            {{ substr($reading->recordedBy->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $reading->recordedBy->name }}</span>
                                </div>
                                @else
                                <div class="flex items-center text-gray-400">
                                    <x-iconify icon="lucide:cpu" class="w-4 h-4 mr-1" />
                                    <span class="text-sm italic">Système</span>
                                </div>
                                @endif
                            </td>

                            {{-- Méthode --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($reading->recording_method === 'manual')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <x-iconify icon="lucide:hand" class="w-3 h-3" />
                                    Manuel
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <x-iconify icon="lucide:cpu" class="w-3 h-3" />
                                    Automatique
                                </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.vehicles.mileage-history', $reading->vehicle) }}"
                                       class="inline-flex items-center p-1.5 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="Voir historique">
                                        <x-iconify icon="lucide:history" class="w-5 h-5" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <x-iconify icon="lucide:gauge" class="w-10 h-10 text-gray-400" />
                                    </div>
                                    <p class="text-lg font-medium text-gray-900">Aucun relevé trouvé</p>
                                    <p class="text-sm text-gray-500 mt-1">Essayez de modifier vos filtres de recherche</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($readings->hasPages())
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $readings->links() }}
            </div>
            @endif
        </div>

    </div>
</section>
</div>

@push('styles')
<style>
/* Animation fade-in pour le contenu */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { 
        opacity: 0; 
        transform: translateY(10px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

/* Hover sur les lignes de table */
tr.hover\:bg-gray-50:hover {
    background-color: rgb(249 250 251);
}
</style>
@endpush
