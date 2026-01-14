<div>
    <div x-data="{}" @depot-toggled.window="$wire.$refresh()">
        {{-- Messages Flash Enterprise-Grade --}}
        @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="flex items-center">
                <x-iconify icon="lucide:check-circle" class="w-5 h-5 text-green-600 mr-3" />
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                <button @click="show = false" class="ml-auto text-green-600 hover:text-green-800">
                    <x-iconify icon="lucide:x" class="w-4 h-4" />
                </button>
            </div>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="flex items-center">
                <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600 mr-3" />
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                <button @click="show = false" class="ml-auto text-red-600 hover:text-red-800">
                    <x-iconify icon="lucide:x" class="w-4 h-4" />
                </button>
            </div>
        </div>
        @endif

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
                {{-- Total Dépôts --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Dépôts</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_depots'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:building-2" class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                {{-- Dépôts Actifs --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Dépôts Actifs</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active_depots'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:check-circle" class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                </div>

                {{-- Capacité Totale --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Capacité Totale</p>
                            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($stats['total_capacity']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:package" class="w-6 h-6 text-indigo-600" />
                        </div>
                    </div>
                </div>

                {{-- Véhicules Affectés --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Véhicules Affectés</p>
                            <p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($stats['total_occupied']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:truck" class="w-6 h-6 text-orange-600" />
                        </div>
                    </div>
                </div>

                {{-- Taux d'Occupation --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Taux Occupation</p>
                            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['average_occupancy'] }}%</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:pie-chart" class="w-6 h-6 text-purple-600" />
                        </div>
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
                    icon="magnifying-glass" />

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
                    <a href="{{ route('admin.depots.show', $depot->id) }}"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all text-sm font-medium text-gray-700">
                        <x-iconify icon="heroicons:eye" class="w-4 h-4" />
                        Voir
                    </a>
                    <x-button wire:click="openEditModal({{ $depot->id }})" variant="primary" class="flex-1" size="sm">
                        <x-iconify icon="heroicons:pencil" class="w-4 h-4 mr-1" />
                        Modifier
                    </x-button>
                    <x-button
                        wire:click="toggleActive({{ $depot->id }})"
                        wire:confirm="Êtes-vous sûr de vouloir {{ $depot->is_active ? 'désactiver' : 'activer' }} ce dépôt?"
                        variant="{{ $depot->is_active ? 'warning' : 'success' }}"
                        size="sm">
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
            {{-- Messages d'erreur dans le modal - Enterprise UX --}}
            @if (session()->has('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg" x-data="{ show: true }" x-show="show">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600 mr-3 flex-shrink-0" />
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" type="button" class="text-red-600 hover:text-red-800">
                        <x-iconify icon="lucide:x" class="w-4 h-4" />
                    </button>
                </div>
            </div>
            @endif

            <form wire:submit="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Nom --}}
                    <div>
                        <x-input
                            wire:model="name"
                            name="name"
                            label="Nom"
                            placeholder="Dépôt Central"
                            icon="office-building"
                            :required="true"
                            :disabled="$modalMode === 'view'" />
                        @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Code (optionnel) --}}
                    <div>
                        <x-input
                            wire:model="code"
                            name="code"
                            label="Code"
                            placeholder="Auto-généré si vide"
                            icon="hashtag"
                            helpText="Code unique (auto-généré: DP0001, DP0002...)"
                            :disabled="$modalMode === 'view'" />
                        @error('code') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Adresse --}}
                    <div class="md:col-span-2">
                        <x-input
                            wire:model="address"
                            name="address"
                            label="Adresse"
                            placeholder="123 Rue de la République"
                            icon="map-pin"
                            :disabled="$modalMode === 'view'" />
                    </div>

                    {{-- Ville --}}
                    <div>
                        <x-input
                            wire:model="city"
                            name="city"
                            label="Ville"
                            placeholder="Alger"
                            :disabled="$modalMode === 'view'" />
                        @error('city') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Wilaya --}}
                    <div>
                        <x-input
                            wire:model="wilaya"
                            name="wilaya"
                            label="Wilaya"
                            placeholder="Alger"
                            :disabled="$modalMode === 'view'" />
                        @error('wilaya') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Téléphone --}}
                    <div>
                        <x-input
                            wire:model="phone"
                            name="phone"
                            label="Téléphone"
                            placeholder="+213 XXX XX XX XX"
                            icon="phone"
                            :disabled="$modalMode === 'view'" />
                        @error('phone') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input
                            wire:model="email"
                            name="email"
                            type="email"
                            label="Email"
                            placeholder="depot@example.com"
                            icon="envelope"
                            :disabled="$modalMode === 'view'" />
                        @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Manager --}}
                    <div>
                        <x-input
                            wire:model="manager_name"
                            name="manager_name"
                            label="Responsable"
                            placeholder="Nom du responsable"
                            icon="user"
                            :disabled="$modalMode === 'view'" />
                        @error('manager_name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Manager Phone --}}
                    <div>
                        <x-input
                            wire:model="manager_phone"
                            name="manager_phone"
                            label="Tél. Responsable"
                            placeholder="+213 XXX XX XX XX"
                            icon="phone"
                            :disabled="$modalMode === 'view'" />
                        @error('manager_phone') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Capacité --}}
                    <div>
                        <x-input
                            wire:model="capacity"
                            name="capacity"
                            type="number"
                            label="Capacité (véhicules)"
                            placeholder="50"
                            :disabled="$modalMode === 'view'" />
                        @error('capacity') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Latitude --}}
                    <div>
                        <x-input
                            wire:model="latitude"
                            name="latitude"
                            label="Latitude"
                            placeholder="36.7538"
                            :disabled="$modalMode === 'view'" />
                        @error('latitude') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Longitude --}}
                    <div>
                        <x-input
                            wire:model="longitude"
                            name="longitude"
                            label="Longitude"
                            placeholder="3.0588"
                            :disabled="$modalMode === 'view'" />
                        @error('longitude') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Description</label>
                        <textarea
                            wire:model="description"
                            rows="3"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition-colors"
                            placeholder="Description du dépôt..."
                            {{ $modalMode === 'view' ? 'disabled' : '' }}></textarea>
                        @error('description') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Toggle Dépôt actif - INTÉGRÉ DANS LA GRILLE --}}
                    @if($modalMode !== 'view')
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Statut du Dépôt</label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="is_active"
                                class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900">Dépôt actif</span>
                        </label>
                        @error('is_active') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    {{-- Div de remplissage pour la grille 3 colonnes --}}
                    <div class="hidden lg:block"></div>
                    @endif
                </div>

                {{-- Actions - Séparation unique et simple --}}
                @if($modalMode !== 'view')
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <x-button
                        @click="$dispatch('close-modal', 'depot-modal')"
                        type="button"
                        variant="secondary">
                        Annuler
                    </x-button>
                    <x-button
                        type="submit"
                        variant="primary"
                        icon="check"
                        wire:loading.attr="disabled"
                        wire:target="save">
                        <span wire:loading.remove wire:target="save">
                            {{ $modalMode === 'create' ? 'Créer' : 'Mettre à jour' }}
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Enregistrement...
                        </span>
                    </x-button>
                </div>
                @endif
            </form>
        </x-modal>

        @push('scripts')
        <script>
            // Livewire 3 Event Listeners - Enterprise Grade Modal System
            document.addEventListener('livewire:initialized', () => {
                // Écouter l'événement Livewire pour ouvrir le modal
                Livewire.on('depot-modal-open', () => {
                    window.dispatchEvent(new CustomEvent('open-modal', {
                        detail: 'depot-modal'
                    }));
                });

                // Écouter l'événement Livewire pour fermer le modal
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
{{-- Messages Flash Enterprise-Grade --}}
@if (session()->has('success'))
<div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
    <div class="flex items-center">
        <x-iconify icon="lucide:check-circle" class="w-5 h-5 text-green-600 mr-3" />
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        <button @click="show = false" class="ml-auto text-green-600 hover:text-green-800">
            <x-iconify icon="lucide:x" class="w-4 h-4" />
        </button>
    </div>
</div>
@endif

@if (session()->has('error'))
<div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
    <div class="flex items-center">
        <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600 mr-3" />
        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        <button @click="show = false" class="ml-auto text-red-600 hover:text-red-800">
            <x-iconify icon="lucide:x" class="w-4 h-4" />
        </button>
    </div>
</div>
@endif

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
        {{-- Total Dépôts --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Dépôts</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_depots'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:building-2" class="w-6 h-6 text-blue-600" />
                </div>
            </div>
        </div>

        {{-- Dépôts Actifs --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Dépôts Actifs</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active_depots'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:check-circle" class="w-6 h-6 text-green-600" />
                </div>
            </div>
        </div>

        {{-- Capacité Totale --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Capacité Totale</p>
                    <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($stats['total_capacity']) }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:package" class="w-6 h-6 text-indigo-600" />
                </div>
            </div>
        </div>

        {{-- Véhicules Affectés --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Véhicules Affectés</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($stats['total_occupied']) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:truck" class="w-6 h-6 text-orange-600" />
                </div>
            </div>
        </div>

        {{-- Taux d'Occupation --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Taux Occupation</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['average_occupancy'] }}%</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:pie-chart" class="w-6 h-6 text-purple-600" />
                </div>
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
            icon="magnifying-glass" />

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
            <a href="{{ route('admin.depots.show', $depot->id) }}"
                class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all text-sm font-medium text-gray-700">
                <x-iconify icon="heroicons:eye" class="w-4 h-4" />
                Voir
            </a>
            <x-button wire:click="openEditModal({{ $depot->id }})" variant="primary" class="flex-1" size="sm">
                <x-iconify icon="heroicons:pencil" class="w-4 h-4 mr-1" />
                Modifier
            </x-button>
            <x-button
                wire:click="toggleActive({{ $depot->id }})"
                wire:confirm="Êtes-vous sûr de vouloir {{ $depot->is_active ? 'désactiver' : 'activer' }} ce dépôt?"
                variant="{{ $depot->is_active ? 'warning' : 'success' }}"
                size="sm">
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
    {{-- Messages d'erreur dans le modal - Enterprise UX --}}
    @if (session()->has('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg" x-data="{ show: true }" x-show="show">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600 mr-3 flex-shrink-0" />
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
            <button @click="show = false" type="button" class="text-red-600 hover:text-red-800">
                <x-iconify icon="lucide:x" class="w-4 h-4" />
            </button>
        </div>
    </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Nom --}}
            <div>
                <x-input
                    wire:model="name"
                    name="name"
                    label="Nom"
                    placeholder="Dépôt Central"
                    icon="office-building"
                    :required="true"
                    :disabled="$modalMode === 'view'" />
                @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Code (optionnel) --}}
            <div>
                <x-input
                    wire:model="code"
                    name="code"
                    label="Code"
                    placeholder="Auto-généré si vide"
                    icon="hashtag"
                    helpText="Code unique (auto-généré: DP0001, DP0002...)"
                    :disabled="$modalMode === 'view'" />
                @error('code') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Adresse --}}
            <div class="md:col-span-2">
                <x-input
                    wire:model="address"
                    name="address"
                    label="Adresse"
                    placeholder="123 Rue de la République"
                    icon="map-pin"
                    :disabled="$modalMode === 'view'" />
            </div>

            {{-- Ville --}}
            <div>
                <x-input
                    wire:model="city"
                    name="city"
                    label="Ville"
                    placeholder="Alger"
                    :disabled="$modalMode === 'view'" />
                @error('city') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Wilaya --}}
            <div>
                <x-input
                    wire:model="wilaya"
                    name="wilaya"
                    label="Wilaya"
                    placeholder="Alger"
                    :disabled="$modalMode === 'view'" />
                @error('wilaya') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Téléphone --}}
            <div>
                <x-input
                    wire:model="phone"
                    name="phone"
                    label="Téléphone"
                    placeholder="+213 XXX XX XX XX"
                    icon="phone"
                    :disabled="$modalMode === 'view'" />
                @error('phone') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Email --}}
            <div>
                <x-input
                    wire:model="email"
                    name="email"
                    type="email"
                    label="Email"
                    placeholder="depot@example.com"
                    icon="envelope"
                    :disabled="$modalMode === 'view'" />
                @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Manager --}}
            <div>
                <x-input
                    wire:model="manager_name"
                    name="manager_name"
                    label="Responsable"
                    placeholder="Nom du responsable"
                    icon="user"
                    :disabled="$modalMode === 'view'" />
                @error('manager_name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Manager Phone --}}
            <div>
                <x-input
                    wire:model="manager_phone"
                    name="manager_phone"
                    label="Tél. Responsable"
                    placeholder="+213 XXX XX XX XX"
                    icon="phone"
                    :disabled="$modalMode === 'view'" />
                @error('manager_phone') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Capacité --}}
            <div>
                <x-input
                    wire:model="capacity"
                    name="capacity"
                    type="number"
                    label="Capacité (véhicules)"
                    placeholder="50"
                    :disabled="$modalMode === 'view'" />
                @error('capacity') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Latitude --}}
            <div>
                <x-input
                    wire:model="latitude"
                    name="latitude"
                    label="Latitude"
                    placeholder="36.7538"
                    :disabled="$modalMode === 'view'" />
                @error('latitude') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Longitude --}}
            <div>
                <x-input
                    wire:model="longitude"
                    name="longitude"
                    label="Longitude"
                    placeholder="3.0588"
                    :disabled="$modalMode === 'view'" />
                @error('longitude') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Description --}}
            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900">Description</label>
                <textarea
                    wire:model="description"
                    rows="3"
                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition-colors"
                    placeholder="Description du dépôt..."
                    {{ $modalMode === 'view' ? 'disabled' : '' }}></textarea>
                @error('description') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Toggle Dépôt actif - INTÉGRÉ DANS LA GRILLE --}}
            @if($modalMode !== 'view')
            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900">Statut du Dépôt</label>
                <label class="inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model.live="is_active"
                        class="sr-only peer">
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    <span class="ms-3 text-sm font-medium text-gray-900">Dépôt actif</span>
                </label>
                @error('is_active') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            {{-- Div de remplissage pour la grille 3 colonnes --}}
            <div class="hidden lg:block"></div>
            @endif
        </div>

        {{-- Actions - Séparation unique et simple --}}
        @if($modalMode !== 'view')
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <x-button
                @click="$dispatch('close-modal', 'depot-modal')"
                type="button"
                variant="secondary">
                Annuler
            </x-button>
            <x-button
                type="submit"
                variant="primary"
                icon="check"
                wire:loading.attr="disabled"
                wire:target="save">
                <span wire:loading.remove wire:target="save">
                    {{ $modalMode === 'create' ? 'Créer' : 'Mettre à jour' }}
                </span>
                <span wire:loading wire:target="save" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Enregistrement...
                </span>
            </x-button>
        </div>
        @endif
    </form>
</x-modal>

@push('scripts')
<script>
    // Livewire 3 Event Listeners - Enterprise Grade Modal System
    document.addEventListener('livewire:initialized', () => {
        // Écouter l'événement Livewire pour ouvrir le modal
        Livewire.on('depot-modal-open', () => {
            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'depot-modal'
            }));
        });

        // Écouter l'événement Livewire pour fermer le modal
        Livewire.on('depot-modal-close', () => {
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: 'depot-modal'
            }));
        });
    });
</script>
@endpush
</div>