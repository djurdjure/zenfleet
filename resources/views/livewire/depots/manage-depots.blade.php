<div>
    {{-- Header avec statistiques globales --}}
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gestion des Dépôts</h1>
                <p class="text-gray-600 mt-1">Gérez vos dépôts et leur capacité</p>
            </div>
            <x-button wire:click="openCreateModal" variant="primary" icon="plus">
                Nouveau Dépôt
            </x-button>
        </div>

        {{-- Statistiques globales --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Dépôts</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_depots'] }}</p>
                    </div>
                    <x-iconify icon="mdi:office-building" class="w-10 h-10 text-blue-600" />
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Dépôts Actifs</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active_depots'] }}</p>
                    </div>
                    <x-iconify icon="heroicons:check-circle" class="w-10 h-10 text-green-600" />
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Capacité Totale</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_capacity']) }}</p>
                    </div>
                    <x-iconify icon="heroicons:inbox" class="w-10 h-10 text-indigo-600" />
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Véhicules</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($stats['total_occupied']) }}</p>
                    </div>
                    <x-iconify icon="heroicons:truck" class="w-10 h-10 text-orange-600" />
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Taux Occupation</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['average_occupancy'] }}%</p>
                    </div>
                    <x-iconify icon="heroicons:chart-pie" class="w-10 h-10 text-purple-600" />
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres et recherche --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-input
                wire:model.live.debounce.300ms="search"
                name="search"
                label="Recherche"
                placeholder="Nom, code, ville..."
                icon="magnifying-glass"
            />

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Statut</label>
                <select wire:model.live="statusFilter" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="all">Tous</option>
                    <option value="active">Actifs</option>
                    <option value="inactive">Inactifs</option>
                </select>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Capacité</label>
                <select wire:model.live="capacityFilter" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="all">Tous</option>
                    <option value="available">Avec places disponibles</option>
                    <option value="full">Complets</option>
                </select>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Trier par</label>
                <select wire:model.live="sortBy" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
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
            <div class="bg-white rounded-lg shadow-sm border-2 {{ $depot->is_active ? 'border-gray-200' : 'border-red-200' }} hover:shadow-lg transition-all">
                {{-- Header --}}
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $depot->name }}</h3>
                            @if($depot->code)
                                <p class="text-sm text-gray-500 mt-1">{{ $depot->code }}</p>
                            @endif
                        </div>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $depot->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $depot->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>

                    @if($depot->city || $depot->wilaya)
                        <div class="flex items-center text-sm text-gray-600 mt-2">
                            <x-iconify icon="heroicons:map-pin" class="w-4 h-4 mr-1.5" />
                            {{ $depot->city }}{{ $depot->wilaya ? ', ' . $depot->wilaya : '' }}
                        </div>
                    @endif
                </div>

                {{-- Capacité --}}
                @if($depot->capacity)
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Occupation</span>
                            <span class="text-sm font-bold text-gray-900">
                                {{ $depot->current_count }} / {{ $depot->capacity }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            @php
                                $percentage = $depot->capacity > 0 ? ($depot->current_count / $depot->capacity) * 100 : 0;
                                $colorClass = $percentage >= 100 ? 'bg-red-600' : ($percentage >= 80 ? 'bg-orange-500' : 'bg-green-600');
                            @endphp
                            <div class="{{ $colorClass }} h-2.5 rounded-full transition-all" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1.5">{{ round($percentage, 1) }}% occupé</p>
                    </div>
                @endif

                {{-- Infos contact --}}
                @if($depot->manager_name || $depot->phone)
                    <div class="p-4 border-b border-gray-200 space-y-2">
                        @if($depot->manager_name)
                            <div class="flex items-center text-sm text-gray-700">
                                <x-iconify icon="heroicons:user" class="w-4 h-4 mr-2 text-gray-500" />
                                {{ $depot->manager_name }}
                            </div>
                        @endif
                        @if($depot->phone)
                            <div class="flex items-center text-sm text-gray-700">
                                <x-iconify icon="heroicons:phone" class="w-4 h-4 mr-2 text-gray-500" />
                                {{ $depot->phone }}
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Actions --}}
                <div class="p-4 bg-gray-50 flex gap-2">
                    <x-button wire:click="openViewModal({{ $depot->id }})" variant="secondary" class="flex-1" size="sm">
                        <x-iconify icon="heroicons:eye" class="w-4 h-4 mr-1" />
                        Voir
                    </x-button>
                    <x-button wire:click="openEditModal({{ $depot->id }})" variant="primary" class="flex-1" size="sm">
                        <x-iconify icon="heroicons:pencil" class="w-4 h-4 mr-1" />
                        Modifier
                    </x-button>
                    <x-button
                        wire:click="toggleActive({{ $depot->id }})"
                        wire:confirm="Êtes-vous sûr de vouloir {{ $depot->is_active ? 'désactiver' : 'activer' }} ce dépôt?"
                        variant="{{ $depot->is_active ? 'warning' : 'success' }}"
                        size="sm"
                    >
                        <x-iconify icon="heroicons:power" class="w-4 h-4" />
                    </x-button>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-lg shadow-sm border-2 border-dashed border-gray-300 p-12 text-center">
                    <x-iconify icon="mdi:office-building" class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun dépôt trouvé</h3>
                    <p class="text-gray-600 mb-4">Commencez par créer votre premier dépôt</p>
                    <x-button wire:click="openCreateModal" variant="primary">
                        Créer un dépôt
                    </x-button>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $depots->links() }}
    </div>

    {{-- Modal CRUD avec composant x-modal --}}
    <x-modal name="depot-modal" :title="$modalMode === 'create' ? 'Nouveau Dépôt' : ($modalMode === 'edit' ? 'Modifier le Dépôt' : 'Détails du Dépôt')" maxWidth="3xl">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nom --}}
                <x-input
                    wire:model="name"
                    name="name"
                    label="Nom"
                    placeholder="Dépôt Central"
                    icon="office-building"
                    :required="true"
                    :disabled="$modalMode === 'view'"
                />

                {{-- Code (optionnel) --}}
                <x-input
                    wire:model="code"
                    name="code"
                    label="Code"
                    placeholder="DC-001 (optionnel)"
                    icon="hashtag"
                    helpText="Code unique pour identifier rapidement le dépôt"
                    :disabled="$modalMode === 'view'"
                />

                {{-- Adresse --}}
                <div class="md:col-span-2">
                    <x-input
                        wire:model="address"
                        name="address"
                        label="Adresse"
                        placeholder="123 Rue de la République"
                        icon="map-pin"
                        :disabled="$modalMode === 'view'"
                    />
                </div>

                {{-- Ville --}}
                <x-input
                    wire:model="city"
                    name="city"
                    label="Ville"
                    placeholder="Alger"
                    :disabled="$modalMode === 'view'"
                />

                {{-- Wilaya --}}
                <x-input
                    wire:model="wilaya"
                    name="wilaya"
                    label="Wilaya"
                    placeholder="Alger"
                    :disabled="$modalMode === 'view'"
                />

                {{-- Téléphone --}}
                <x-input
                    wire:model="phone"
                    name="phone"
                    label="Téléphone"
                    placeholder="+213 XXX XX XX XX"
                    icon="phone"
                    :disabled="$modalMode === 'view'"
                />

                {{-- Email --}}
                <x-input
                    wire:model="email"
                    name="email"
                    type="email"
                    label="Email"
                    placeholder="depot@example.com"
                    icon="envelope"
                    :disabled="$modalMode === 'view'"
                />

                {{-- Manager --}}
                <x-input
                    wire:model="manager_name"
                    name="manager_name"
                    label="Responsable"
                    placeholder="Nom du responsable"
                    icon="user"
                    :disabled="$modalMode === 'view'"
                />

                {{-- Manager Phone --}}
                <x-input
                    wire:model="manager_phone"
                    name="manager_phone"
                    label="Tél. Responsable"
                    placeholder="+213 XXX XX XX XX"
                    icon="phone"
                    :disabled="$modalMode === 'view'"
                />

                {{-- Capacité --}}
                <x-input
                    wire:model="capacity"
                    name="capacity"
                    type="number"
                    label="Capacité (véhicules)"
                    placeholder="50"
                    :disabled="$modalMode === 'view'"
                />

                {{-- Latitude --}}
                <x-input
                    wire:model="latitude"
                    name="latitude"
                    label="Latitude"
                    placeholder="36.7538"
                    :disabled="$modalMode === 'view'"
                />

                {{-- Longitude --}}
                <x-input
                    wire:model="longitude"
                    name="longitude"
                    label="Longitude"
                    placeholder="3.0588"
                    :disabled="$modalMode === 'view'"
                />

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Description</label>
                    <textarea
                        wire:model="description"
                        rows="3"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        placeholder="Description du dépôt..."
                        {{ $modalMode === 'view' ? 'disabled' : '' }}
                    ></textarea>
                </div>

                {{-- Actif --}}
                @if($modalMode !== 'view')
                    <div class="md:col-span-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900">Dépôt actif</span>
                        </label>
                    </div>
                @endif
            </div>

            @if($modalMode !== 'view')
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <x-button @click="$dispatch('close-modal', 'depot-modal')" type="button" variant="secondary">
                        Annuler
                    </x-button>
                    <x-button type="submit" variant="primary" icon="check">
                        {{ $modalMode === 'create' ? 'Créer' : 'Mettre à jour' }}
                    </x-button>
                </div>
            @endif
        </form>
    </x-modal>

    @push('scripts')
    <script>
        // Ouvrir/fermer modal avec Livewire
        Livewire.on('depot-modal-open', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'depot-modal' }));
        });

        Livewire.on('depot-modal-close', () => {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'depot-modal' }));
        });
    </script>
    @endpush
</div>
