<div>
    <div x-data="{
            showSuccess: false,
            showError: false,
            init() {
                $watch('$wire.selectedDepotId', value => {
                    // Reserved for modal sync if needed
                });
            }
         }" class="space-y-8 animate-fade-in-up">
        @if(session()->has('success'))
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

        @if(session()->has('warning'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 5500)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="fixed top-4 right-4 z-50 max-w-md">
            <x-alert type="warning" title="Attention" dismissible>
                {{ session('warning') }}
            </x-alert>
        </div>
        @endif

        @if(session()->has('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 6500)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="fixed top-4 right-4 z-50 max-w-md">
            <x-alert type="error" title="Erreur" dismissible>
                {{ session('error') }}
            </x-alert>
        </div>
        @endif

        <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">
            <div class="mb-4 flex justify-between items-start gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-600">
                        Gestion des Dépôts
                        <span class="ml-2 text-sm font-normal text-gray-500">({{ $stats['total_depots'] ?? 0 }})</span>
                    </h1>
                    <p class="text-xs text-gray-600">
                        Administration centralisée des dépôts, capacité et occupation en temps réel
                    </p>
                </div>

                <div class="flex items-center gap-2 text-blue-600 opacity-0 transition-opacity duration-150"
                     wire:loading.delay.class="opacity-100"
                     wire:loading.delay.class.remove="opacity-0"
                     wire:target="search,statusFilter,capacityFilter,sortBy,perPage">
                    <x-iconify icon="lucide:loader-2" class="w-5 h-5 animate-spin" />
                    <span class="text-sm font-medium">Chargement...</span>
                </div>
            </div>

            <x-page-analytics-grid columns="6">
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Dépôts</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_depots'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
                            <x-iconify icon="lucide:building" class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 rounded-lg border border-green-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Actifs</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active_depots'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 border border-green-300 rounded-full flex items-center justify-center">
                            <x-iconify icon="lucide:activity" class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-50 rounded-lg border border-indigo-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Capacité totale</p>
                            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($stats['total_capacity']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-indigo-100 border border-indigo-300 rounded-full flex items-center justify-center">
                            <x-iconify icon="lucide:box" class="w-6 h-6 text-indigo-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-orange-50 rounded-lg border border-orange-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Occupés</p>
                            <p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($stats['total_occupied']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 border border-orange-300 rounded-full flex items-center justify-center">
                            <x-iconify icon="lucide:truck" class="w-6 h-6 text-orange-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-teal-50 rounded-lg border border-teal-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Disponibles</p>
                            <p class="text-2xl font-bold text-teal-600 mt-1">{{ number_format($stats['total_available']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-teal-100 border border-teal-300 rounded-full flex items-center justify-center">
                            <x-iconify icon="lucide:archive" class="w-6 h-6 text-teal-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 rounded-lg border border-purple-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Taux occupation</p>
                            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['average_occupancy'] }}%</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 border border-purple-300 rounded-full flex items-center justify-center">
                            <x-iconify icon="lucide:pie-chart" class="w-6 h-6 text-purple-600" />
                        </div>
                    </div>
                </div>
            </x-page-analytics-grid>

            @php
                $activeFilters = collect([
                    $search,
                    $statusFilter !== 'all' ? $statusFilter : '',
                    $capacityFilter !== 'all' ? $capacityFilter : '',
                    $sortBy,
                ])->filter(fn($value) => $value !== '' && $value !== null);
                $activeCount = $activeFilters->count();
            @endphp

            <x-page-search-bar x-data="{ showFilters: false }">
                <x-slot:search>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                        </div>
                        <input
                            wire:model.live.debounce.500ms="search"
                            type="text"
                            placeholder="Rechercher un dépôt..."
                            wire:loading.attr="aria-busy"
                            wire:target="search"
                            class="pl-10 pr-4 py-2.5 block w-full bg-white border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <div wire:loading.delay wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <x-iconify icon="lucide:loader-2" class="w-4 h-4 text-blue-500 animate-spin" />
                        </div>
                    </div>
                </x-slot:search>

                <x-slot:filters>
                    <button
                        @click="showFilters = !showFilters"
                        type="button"
                        title="Filtres"
                        class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
                        <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" x-bind:class="showFilters ? 'rotate-180' : ''" />
                        @if($activeCount > 0)
                            <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $activeCount }}
                            </span>
                        @endif
                    </button>
                </x-slot:filters>

                <x-slot:actions>
                    @can('depots.create')
                    <x-button wire:click="openCreateModal" variant="primary" class="shadow-sm hover:shadow-md transition-all">
                        <x-iconify icon="lucide:plus" class="w-5 h-5" />
                    </x-button>
                    @endcan
                </x-slot:actions>

                <x-slot:filtersPanel>
                    <x-page-filters-panel columns="3">
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-600">Statut</label>
                            <select wire:model.live="statusFilter" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="all">Tous les statuts</option>
                                <option value="active">Actifs seulement</option>
                                <option value="inactive">Inactifs seulement</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-600">Disponibilité</label>
                            <select wire:model.live="capacityFilter" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="all">Toutes capacités</option>
                                <option value="available">Places disponibles</option>
                                <option value="full">Complet</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-600">Tri</label>
                            <select wire:model.live="sortBy" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="name">Nom (A-Z)</option>
                                <option value="created_at">Date de création</option>
                                <option value="current_count">Occupation</option>
                                <option value="city">Ville</option>
                            </select>
                        </div>

                        <x-slot:reset>
                            @if($activeCount > 0)
                            <button wire:click="resetFilters" class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1 font-medium bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors">
                                <x-iconify icon="lucide:x" class="w-4 h-4" />
                                Réinitialiser
                            </button>
                            @endif
                        </x-slot:reset>
                    </x-page-filters-panel>
                </x-slot:filtersPanel>
            </x-page-search-bar>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($depots as $depot)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-blue-200 transition-all duration-300 group flex flex-col h-full">
                    <div class="p-5 border-b border-gray-100">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-1">
                                    {{ $depot->name }}
                                </h3>
                                @if($depot->code)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200 mt-1">
                                    {{ $depot->code }}
                                </span>
                                @endif
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $depot->is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                    {{ $depot->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 flex-grow space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-50 border border-blue-200 rounded-full flex items-center justify-center">
                                <x-iconify icon="lucide:map-pin" class="w-4 h-4 text-blue-600" />
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase">Localisation</p>
                                <p class="text-sm text-gray-900 font-medium">
                                    {{ $depot->city ?: 'Ville non définie' }}
                                    @if($depot->wilaya)
                                    <span class="text-gray-400 mx-1">•</span> {{ $depot->wilaya }}
                                    @endif
                                </p>
                                @if($depot->address)
                                <p class="text-xs text-gray-500 mt-0.5 line-clamp-1" title="{{ $depot->address }}">
                                    {{ $depot->address }}
                                </p>
                                @endif
                            </div>
                        </div>

                        @if($depot->manager_name)
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-50 border border-purple-200 rounded-full flex items-center justify-center">
                                <x-iconify icon="lucide:user" class="w-4 h-4 text-purple-600" />
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase">Responsable</p>
                                <p class="text-sm text-gray-900 font-medium">{{ $depot->manager_name }}</p>
                                @if($depot->phone)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $depot->phone }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($depot->capacity > 0)
                        <div class="mt-4 pt-4 border-t border-gray-50">
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-xs font-semibold text-gray-700">Occupation</span>
                                <span class="text-xs font-bold {{ $depot->current_count >= $depot->capacity ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $depot->current_count }} / {{ $depot->capacity }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                @php
                                $percent = ($depot->current_count / $depot->capacity) * 100;
                                $barColor = $percent >= 100 ? 'bg-red-500' : ($percent >= 80 ? 'bg-orange-500' : 'bg-green-500');
                                @endphp
                                <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-500" style="width: {{ min(100, $percent) }}%"></div>
                            </div>
                            <p class="text-xs text-center mt-2 text-gray-500">
                                {{ $depot->capacity - $depot->current_count }} places disponibles
                            </p>
                        </div>
                        @else
                        <div class="mt-4 pt-4 border-t border-gray-50 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                Capacité illimitée/non définie
                            </span>
                        </div>
                        @endif
                    </div>

                    <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 rounded-b-xl flex justify-between items-center gap-2">
                        <button wire:click="openViewModal({{ $depot->id }})" class="text-gray-600 hover:text-blue-600 text-sm font-medium transition-colors flex items-center gap-1">
                            <x-iconify icon="lucide:eye" class="w-4 h-4" /> Détails
                        </button>

                        <div class="flex items-center gap-2">
                            @can('depots.update')
                            <button wire:click="openEditModal({{ $depot->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Modifier">
                                <x-iconify icon="lucide:pencil" class="w-4 h-4" />
                            </button>
                            <button wire:click="toggleActive({{ $depot->id }})"
                                wire:confirm="Êtes-vous sûr de vouloir {{ $depot->is_active ? 'désactiver' : 'activer' }} ce dépôt ?"
                                class="p-2 {{ $depot->is_active ? 'text-orange-500 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition-colors"
                                title="{{ $depot->is_active ? 'Désactiver' : 'Activer' }}">
                                <x-iconify icon="{{ $depot->is_active ? 'lucide:power-off' : 'lucide:power' }}" class="w-4 h-4" />
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-12">
                    <div class="text-center bg-white rounded-2xl border-2 border-dashed border-gray-200 p-12 max-w-2xl mx-auto">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <x-iconify icon="lucide:warehouse" class="w-8 h-8 text-gray-400" />
                        </div>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Aucun dépôt trouvé</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par créer votre premier dépôt ou essayez de modifier vos filtres.</p>
                        <div class="mt-6">
                            @can('depots.create')
                            <x-button wire:click="openCreateModal" variant="primary">
                                <x-iconify icon="lucide:plus" class="w-4 h-4 mr-2" />
                                Créer un dépôt
                            </x-button>
                            @endcan
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <div class="mt-4">
                <x-pagination :paginator="$depots" :records-per-page="$perPage" wire:model.live="perPage" />
            </div>

            <x-modal
                name="depot-modal"
                :title="$modalMode === 'create' ? 'Nouveau Dépôt' : ($modalMode === 'edit' ? 'Modifier le Dépôt' : 'Détails du Dépôt')"
                maxWidth="3xl"
                position="right">
                <form wire:submit="save" class="space-y-6">
                    <x-form-section
                        title="Informations générales"
                        icon="heroicons:building-office"
                        subtitle="Identité, capacité et description du dépôt"
                        :showLine="false">
                        <div class="space-y-4">
                            <x-input wire:model="name" name="name" label="Nom du Dépôt" placeholder="ex: Dépôt Central Alger" :disabled="$modalMode === 'view'" required />

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-input wire:model="code" name="code" label="Code" placeholder="Auto" helpText="Laisser vide pour auto-générer" :disabled="$modalMode === 'view'" />
                                <x-input wire:model="capacity" name="capacity" type="number" label="Capacité" placeholder="ex: 100" :disabled="$modalMode === 'view'" />
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-600">Description</label>
                                <textarea
                                    wire:model="description"
                                    rows="3"
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 transition-colors duration-200 hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]"
                                    {{ $modalMode === 'view' ? 'disabled' : '' }}></textarea>
                            </div>
                        </div>
                    </x-form-section>

                    <x-form-section
                        title="Localisation"
                        icon="heroicons:map-pin"
                        subtitle="Adresse et coordonnées géographiques"
                        :showLine="false">
                        <div class="space-y-4">
                            <x-input wire:model="address" name="address" label="Adresse" placeholder="ex: Zone Industrielle" :disabled="$modalMode === 'view'" />

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-input wire:model="city" name="city" label="Ville" placeholder="ex: Rouiba" :disabled="$modalMode === 'view'" />
                                <x-input wire:model="wilaya" name="wilaya" label="Wilaya" placeholder="ex: Alger" :disabled="$modalMode === 'view'" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-input wire:model="latitude" name="latitude" label="Latitude" placeholder="0.0000" :disabled="$modalMode === 'view'" />
                                <x-input wire:model="longitude" name="longitude" label="Longitude" placeholder="0.0000" :disabled="$modalMode === 'view'" />
                            </div>
                        </div>
                    </x-form-section>

                    <x-form-section
                        title="Contact & Responsable"
                        icon="heroicons:phone"
                        subtitle="Canaux de contact opérationnels"
                        :showLine="false">
                        <div class="space-y-4">
                            <x-input wire:model="manager_name" name="manager_name" label="Responsable du site" placeholder="Nom complet" :disabled="$modalMode === 'view'" />

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-input wire:model="phone" name="phone" label="Tél. Dépôt" placeholder="+213..." :disabled="$modalMode === 'view'" />
                                <x-input wire:model="manager_phone" name="manager_phone" label="Tél. Responsable" placeholder="+213..." :disabled="$modalMode === 'view'" />
                            </div>

                            <x-input wire:model="email" name="email" type="email" label="Email" placeholder="contact@depot.com" :disabled="$modalMode === 'view'" />
                        </div>
                    </x-form-section>

                    <x-form-section
                        title="Configuration"
                        icon="heroicons:cog-6-tooth"
                        subtitle="Activation du dépôt dans les sélecteurs"
                        :showLine="false">
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="is_active" id="is_active" class="h-4 w-4 rounded border-gray-300 text-[#0c90ee] focus:ring-[#0c90ee]/30" {{ $modalMode === 'view' ? 'disabled' : '' }}>
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                Dépôt Actif (visible dans les sélecteurs)
                            </label>
                        </div>
                    </x-form-section>

                    @if($modalMode !== 'view')
                    <div class="relative pl-14">
                        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="px-6 py-4 flex items-center justify-end gap-3">
                                <button
                                    type="button"
                                    @click="$dispatch('close-modal', 'depot-modal')"
                                    class="inline-flex items-center justify-center h-10 px-4 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee] transition-all duration-200">
                                    Annuler
                                </button>
                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center justify-center h-10 gap-2 px-5 rounded-lg border border-[#0c90ee] bg-[#0c90ee] text-sm font-medium text-white transition-all duration-200 hover:bg-[#0a7fd1] hover:border-[#0a7fd1] focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span wire:loading.remove>{{ $modalMode === 'create' ? 'Créer le Dépôt' : 'Enregistrer les modifications' }}</span>
                                    <span wire:loading class="inline-flex items-center">
                                        <x-iconify icon="lucide:loader-2" class="animate-spin w-4 h-4 mr-2" />
                                        Traitement...
                                    </span>
                                </button>
                            </div>
                        </section>
                    </div>
                    @else
                    <div class="relative pl-14">
                        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="px-6 py-4 flex items-center justify-end">
                                <button
                                    type="button"
                                    @click="$dispatch('close-modal', 'depot-modal')"
                                    class="inline-flex items-center justify-center h-10 px-4 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee] transition-all duration-200">
                                    Fermer
                                </button>
                            </div>
                        </section>
                    </div>
                    @endif
                </form>
            </x-modal>

            @push('scripts')
            <script>
                document.addEventListener('livewire:initialized', () => {
                    Livewire.on('depot-modal-open', () => {
                        window.dispatchEvent(new CustomEvent('open-modal', {
                            detail: 'depot-modal'
                        }));
                    });
                    Livewire.on('depot-modal-close', () => {
                        window.dispatchEvent(new CustomEvent('close-modal', {
                            detail: 'depot-modal'
                        }));
                    });
                });
            </script>
            @endpush
        </div>
    </div>
</div>
