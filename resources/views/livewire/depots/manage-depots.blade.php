<div>
    {{-- Header avec statistiques globales --}}
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestion des Dépôts</h1>
                <p class="text-sm text-gray-600 mt-1">Gérez vos dépôts et leur capacité</p>
            </div>
            <button wire:click="openCreateModal" class="btn btn-primary">
                <x-iconify icon="mdi:plus" class="w-4 h-4 mr-2" />
                Nouveau Dépôt
            </button>
        </div>

        {{-- Statistiques globales --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Dépôts</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_depots'] }}</p>
                    </div>
                    <x-iconify icon="mdi:office-building" class="w-8 h-8 text-blue-500" />
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Dépôts Actifs</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['active_depots'] }}</p>
                    </div>
                    <x-iconify icon="mdi:check-circle" class="w-8 h-8 text-green-500" />
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Capacité Totale</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_capacity']) }}</p>
                    </div>
                    <x-iconify icon="mdi:inbox" class="w-8 h-8 text-indigo-500" />
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Véhicules</p>
                        <p class="text-2xl font-bold text-orange-600">{{ number_format($stats['total_occupied']) }}</p>
                    </div>
                    <x-iconify icon="mdi:car" class="w-8 h-8 text-orange-500" />
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Taux Occupation</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $stats['average_occupancy'] }}%</p>
                    </div>
                    <x-iconify icon="mdi:chart-pie" class="w-8 h-8 text-purple-500" />
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres et recherche --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Nom, code, ville..."
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select wire:model.live="statusFilter" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Tous</option>
                    <option value="active">Actifs</option>
                    <option value="inactive">Inactifs</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Capacité</label>
                <select wire:model.live="capacityFilter" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Tous</option>
                    <option value="available">Avec places disponibles</option>
                    <option value="full">Complets</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trier par</label>
                <select wire:model.live="sortBy" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="name">Nom</option>
                    <option value="code">Code</option>
                    <option value="city">Ville</option>
                    <option value="current_count">Occupation</option>
                    <option value="created_at">Date création</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Liste des dépôts --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($depots as $depot)
            <div class="bg-white rounded-lg border-2 {{ $depot->is_active ? 'border-gray-200' : 'border-red-200' }} hover:shadow-lg transition-shadow">
                {{-- Header --}}
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $depot->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $depot->code }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $depot->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $depot->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>

                    @if($depot->city || $depot->wilaya)
                        <div class="flex items-center text-sm text-gray-600">
                            <x-iconify icon="mdi:map-marker" class="w-4 h-4 mr-1" />
                            {{ $depot->city }}{{ $depot->wilaya ? ', ' . $depot->wilaya : '' }}
                        </div>
                    @endif
                </div>

                {{-- Capacité --}}
                @if($depot->capacity)
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Occupation</span>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ $depot->current_count }} / {{ $depot->capacity }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $percentage = $depot->capacity > 0 ? ($depot->current_count / $depot->capacity) * 100 : 0;
                                $colorClass = $percentage >= 100 ? 'bg-red-500' : ($percentage >= 80 ? 'bg-orange-500' : 'bg-green-500');
                            @endphp
                            <div class="{{ $colorClass }} h-2 rounded-full transition-all" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ round($percentage, 1) }}% occupé</p>
                    </div>
                @endif

                {{-- Infos contact --}}
                @if($depot->manager_name || $depot->phone)
                    <div class="p-4 border-b border-gray-200 space-y-2">
                        @if($depot->manager_name)
                            <div class="flex items-center text-sm text-gray-600">
                                <x-iconify icon="mdi:account" class="w-4 h-4 mr-2 text-gray-400" />
                                {{ $depot->manager_name }}
                            </div>
                        @endif
                        @if($depot->phone)
                            <div class="flex items-center text-sm text-gray-600">
                                <x-iconify icon="mdi:phone" class="w-4 h-4 mr-2 text-gray-400" />
                                {{ $depot->phone }}
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Actions --}}
                <div class="p-4 bg-gray-50 flex gap-2">
                    <button wire:click="openViewModal({{ $depot->id }})" class="flex-1 px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <x-iconify icon="mdi:eye" class="w-4 h-4 inline mr-1" />
                        Voir
                    </button>
                    <button wire:click="openEditModal({{ $depot->id }})" class="flex-1 px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <x-iconify icon="mdi:pencil" class="w-4 h-4 inline mr-1" />
                        Modifier
                    </button>
                    <button
                        wire:click="toggleActive({{ $depot->id }})"
                        wire:confirm="Êtes-vous sûr de vouloir {{ $depot->is_active ? 'désactiver' : 'activer' }} ce dépôt?"
                        class="px-3 py-2 text-sm {{ $depot->is_active ? 'bg-orange-600' : 'bg-green-600' }} text-white rounded-lg hover:opacity-90 transition-opacity"
                        title="{{ $depot->is_active ? 'Désactiver' : 'Activer' }}"
                    >
                        <x-iconify icon="mdi:power" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
                    <x-iconify icon="mdi:office-building" class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun dépôt trouvé</h3>
                    <p class="text-gray-600 mb-4">Commencez par créer votre premier dépôt</p>
                    <button wire:click="openCreateModal" class="btn btn-primary">
                        <x-iconify icon="mdi:plus" class="w-4 h-4 mr-2" />
                        Créer un dépôt
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $depots->links() }}
    </div>

    {{-- Modal CRUD --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Overlay --}}
                <div
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                    wire:click="closeModal"
                ></div>

                {{-- Modal --}}
                <div
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg"
                >
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-900">
                            @if($modalMode === 'create') Nouveau Dépôt
                            @elseif($modalMode === 'edit') Modifier le Dépôt
                            @else Détails du Dépôt
                            @endif
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <x-iconify icon="mdi:close" class="w-6 h-6" />
                        </button>
                    </div>

                    <form wire:submit="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Nom --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                                <input type="text" wire:model="name" class="w-full border-gray-300 rounded-lg" {{ $modalMode === 'view' ? 'disabled' : '' }} />
                                @error('name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            {{-- Code --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Code *</label>
                                <input type="text" wire:model="code" class="w-full border-gray-300 rounded-lg" {{ $modalMode === 'view' ? 'disabled' : '' }} />
                                @error('code') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            {{-- Adresse --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                                <input type="text" wire:model="address" class="w-full border-gray-300 rounded-lg" {{ $modalMode === 'view' ? 'disabled' : '' }} />
                                @error('address') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            {{-- Ville --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                                <input type="text" wire:model="city" class="w-full border-gray-300 rounded-lg" {{ $modalMode === 'view' ? 'disabled' : '' }} />
                            </div>

                            {{-- Wilaya --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Wilaya</label>
                                <input type="text" wire:model="wilaya" class="w-full border-gray-300 rounded-lg" {{ $modalMode === 'view' ? 'disabled' : '' }} />
                            </div>

                            {{-- Téléphone --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                <input type="text" wire:model="phone" class="w-full border-gray-300 rounded-lg" {{ $modalMode === 'view' ? 'disabled' : '' }} />
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" wire:model="email" class="w-full border-gray-300 rounded-lg" {{ $modalMode === 'view' ? 'disabled' : '' }} />
                            </div>

                            {{-- Manager --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                <input type="text" wire:model="manager_name" class="w-full border-gray-300 rounded-lg" {{ $modalMode === 'view' ? 'disabled' : '' }} />
                            </div>

                            {{-- Manager Phone --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tél. Responsable</label>
                                <input type="text" wire:model="manager_phone" class="w-full border-gray-300 rounded-lg" {{ $modalMode === 'view' ? 'disabled' : '' }} />
                            </div>

                            {{-- Capacité --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Capacité</label>
                                <input type="number" wire:model="capacity" class="w-full border-gray-300 rounded-lg" {{ $modalMode === 'view' ? 'disabled' : '' }} />
                            </div>

                            {{-- Latitude --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                                <input type="text" wire:model="latitude" class="w-full border-gray-300 rounded-lg" {{ $modalMode === 'view' ? 'disabled' : '' }} />
                            </div>

                            {{-- Longitude --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                                <input type="text" wire:model="longitude" class="w-full border-gray-300 rounded-lg" {{ $modalMode === 'view' ? 'disabled' : '' }} />
                            </div>

                            {{-- Description --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea wire:model="description" rows="3" class="w-full border-gray-300 rounded-lg" {{ $modalMode === 'view' ? 'disabled' : '' }}></textarea>
                            </div>

                            {{-- Actif --}}
                            @if($modalMode !== 'view')
                                <div class="md:col-span-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                        <span class="ml-2 text-sm text-gray-700">Dépôt actif</span>
                                    </label>
                                </div>
                            @endif
                        </div>

                        @if($modalMode !== 'view')
                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" wire:click="closeModal" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Annuler
                                </button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    {{ $modalMode === 'create' ? 'Créer' : 'Mettre à jour' }}
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
