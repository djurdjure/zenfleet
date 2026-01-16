<div>
    <div x-data="{ 
            showSuccess: false, 
            showError: false, 
            init() {
                // Gestionnaire global de notifications
                $watch('$wire.selectedDepotId', value => {
                    // Reset scroll si nécessaire lors de l'ouverture du modal
                });
            }
         }"
        class="space-y-8 animate-fade-in-up">
        {{--
            =================================================================
            🏢 GESTION DES DÉPÔTS - ENTERPRISE GRADE INTERFACE
            =================================================================
            @version 3.2 (Wrapper Fix)
            @author ZenFleet AI
            @desc Interface ultra-premium de gestion des dépôts avec statistiques en temps réel,
                  filtrage avancé et design responsive.
        --}}

        {{--
            =================================================================
            🔔 NOTIFICATIONS & FLASH MESSAGES
            =================================================================
        --}}
        @if (session()->has('success'))
        <div x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <x-iconify icon="lucide:check-circle" class="h-6 w-6 text-green-400" />
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900">Succès</p>
                        <p class="mt-1 text-sm text-gray-500">{{ session('success') }}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span class="sr-only">Fermer</span>
                            <x-iconify icon="lucide:x" class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if (session()->has('error'))
        <div x-data="{ show: true }"
            x-show="show"
            class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-red-500 ring-opacity-50 overflow-hidden border-l-4 border-red-500">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <x-iconify icon="lucide:alert-circle" class="h-6 w-6 text-red-500" />
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-red-900">Erreur</p>
                        <p class="mt-1 text-sm text-red-600">{{ session('error') }}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false" class="text-red-400 hover:text-red-500">
                            <x-iconify icon="lucide:x" class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{--
            =================================================================
            📊 DASHBOARD HEADER & STATS
            =================================================================
        --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
                    <x-iconify icon="lucide:warehouse" class="w-8 h-8 text-blue-600" />
                    Gestion des Dépôts
                </h1>
                <p class="text-sm text-gray-500 mt-1 ml-10">
                    Optimisez la gestion de votre parc immobilier et la répartition de vos véhicules.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <x-button wire:click="openCreateModal" variant="primary" class="shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                    <x-iconify icon="lucide:plus" class="w-5 h-5 mr-2" />
                    Nouveau Dépôt
                </x-button>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
            {{-- Total Depots --}}
            <div class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] p-5 border border-gray-100 hover:border-blue-100 transition-colors group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Dépôts</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1 group-hover:text-blue-600 transition-colors">{{ $stats['total_depots'] }}</h3>
                    </div>
                    <div class="p-2 bg-blue-50 rounded-lg group-hover:bg-blue-100 transition-colors">
                        <x-iconify icon="lucide:building" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- Active Depots --}}
            <div class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] p-5 border border-gray-100 hover:border-green-100 transition-colors group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Actifs</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1 group-hover:text-green-600 transition-colors">{{ $stats['active_depots'] }}</h3>
                    </div>
                    <div class="p-2 bg-green-50 rounded-lg group-hover:bg-green-100 transition-colors">
                        <x-iconify icon="lucide:activity" class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            {{-- Total Capacity --}}
            <div class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] p-5 border border-gray-100 hover:border-indigo-100 transition-colors group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Capacité Totale</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1 group-hover:text-indigo-600 transition-colors">{{ number_format($stats['total_capacity']) }}</h3>
                    </div>
                    <div class="p-2 bg-indigo-50 rounded-lg group-hover:bg-indigo-100 transition-colors">
                        <x-iconify icon="lucide:box" class="w-6 h-6 text-indigo-600" />
                    </div>
                </div>
            </div>

            {{-- Occupied --}}
            <div class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] p-5 border border-gray-100 hover:border-orange-100 transition-colors group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Véhicules</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1 group-hover:text-orange-600 transition-colors">{{ number_format($stats['total_occupied']) }}</h3>
                    </div>
                    <div class="p-2 bg-orange-50 rounded-lg group-hover:bg-orange-100 transition-colors">
                        <x-iconify icon="lucide:truck" class="w-6 h-6 text-orange-600" />
                    </div>
                </div>
            </div>

            {{-- Occupancy Rate --}}
            <div class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] p-5 border border-gray-100 hover:border-purple-100 transition-colors group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Taux Occupation</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1 group-hover:text-purple-600 transition-colors">{{ $stats['average_occupancy'] }}%</h3>
                    </div>
                    <div class="p-2 bg-purple-50 rounded-lg group-hover:bg-purple-100 transition-colors">
                        <x-iconify icon="lucide:pie-chart" class="w-6 h-6 text-purple-600" />
                    </div>
                </div>
            </div>
        </div>

        {{--
            =================================================================
            🔍 FILTERS TOOLBAR
            =================================================================
        --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                {{-- Search --}}
                <div class="md:col-span-1">
                    <x-input
                        wire:model.live.debounce.300ms="search"
                        name="search"
                        placeholder="Rechercher un dépôt..."
                        icon="search"
                        class="w-full" />
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
                    <select wire:model.live="statusFilter" class="form-select w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="all">Tous les statuts</option>
                        <option value="active">Actifs seulement</option>
                        <option value="inactive">Inactifs seulement</option>
                    </select>
                </div>

                {{-- Capacity Filter --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Disponibilité</label>
                    <select wire:model.live="capacityFilter" class="form-select w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="all">Toutes capacités</option>
                        <option value="available">Places disponibles</option>
                        <option value="full">Complet</option>
                    </select>
                </div>

                {{-- Sort --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tri</label>
                    <select wire:model.live="sortBy" class="form-select w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="name">Nom (A-Z)</option>
                        <option value="created_at">Date de création</option>
                        <option value="current_count">Occupation</option>
                        <option value="city">Ville</option>
                    </select>
                </div>
            </div>
        </div>

        {{--
            =================================================================
            🏭 DEPOTS GRID
            =================================================================
        --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($depots as $depot)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-blue-200 transition-all duration-300 group flex flex-col h-full">
                {{-- Card Header --}}
                <div class="p-5 border-b border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-1">
                                {{ $depot->name }}
                            </h3>
                            @if($depot->code)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mt-1">
                                {{ $depot->code }}
                            </span>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $depot->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $depot->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-5 flex-grow space-y-4">
                    {{-- Location --}}
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
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

                    {{-- Manager --}}
                    @if($depot->manager_name)
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
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

                    {{-- Capacity Bar --}}
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

                {{-- Card Footer --}}
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 rounded-b-xl flex justify-between items-center gap-2">
                    <button wire:click="openViewModal({{ $depot->id }})" class="text-gray-600 hover:text-blue-600 text-sm font-medium transition-colors flex items-center gap-1">
                        <x-iconify icon="lucide:eye" class="w-4 h-4" /> Détails
                    </button>

                    <div class="flex items-center gap-2">
                        <button wire:click="openEditModal({{ $depot->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Modifier">
                            <x-iconify icon="lucide:pencil" class="w-4 h-4" />
                        </button>
                        <button wire:click="toggleActive({{ $depot->id }})"
                            wire:confirm="Êtes-vous sûr de vouloir {{ $depot->is_active ? 'désactiver' : 'activer' }} ce dépôt ?"
                            class="p-2 {{ $depot->is_active ? 'text-orange-500 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition-colors"
                            title="{{ $depot->is_active ? 'Désactiver' : 'Activer' }}">
                            <x-iconify icon="{{ $depot->is_active ? 'lucide:power-off' : 'lucide:power' }}" class="w-4 h-4" />
                        </button>
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
                        <x-button wire:click="openCreateModal" variant="primary">
                            <x-iconify icon="lucide:plus" class="w-4 h-4 mr-2" />
                            Créer un dépôt
                        </x-button>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $depots->links() }}
        </div>

        {{--
            =================================================================
            📝 CREATE / EDIT MODAL
            =================================================================
        --}}
        <x-modal name="depot-modal" :title="$modalMode === 'create' ? 'Nouveau Dépôt' : ($modalMode === 'edit' ? 'Modifier le Dépôt' : 'Détails du Dépôt')" maxWidth="4xl">
            <form wire:submit="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Colonne Gauche: Infos Générales --}}
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <h4 class="text-sm font-bold text-gray-900 uppercase mb-4 flex items-center">
                                <x-iconify icon="lucide:info" class="w-4 h-4 mr-2 text-blue-500" /> Informations Générales
                            </h4>

                            <div class="space-y-4">
                                <x-input wire:model="name" name="name" label="Nom du Dépôt" placeholder="ex: Dépôt Central Alger" :disabled="$modalMode === 'view'" required />

                                <div class="grid grid-cols-2 gap-4">
                                    <x-input wire:model="code" name="code" label="Code" placeholder="Auto" helpText="Laisser vide pour auto-générer" :disabled="$modalMode === 'view'" />
                                    <x-input wire:model="capacity" name="capacity" type="number" label="Capacité" placeholder="ex: 100" :disabled="$modalMode === 'view'" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea wire:model="description" rows="3" class="form-textarea w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" {{ $modalMode === 'view' ? 'disabled' : '' }}></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <h4 class="text-sm font-bold text-gray-900 uppercase mb-4 flex items-center">
                                <x-iconify icon="lucide:settings" class="w-4 h-4 mr-2 text-gray-500" /> Configuration
                            </h4>

                            <div class="flex items-center">
                                <input type="checkbox" wire:model="is_active" id="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ $modalMode === 'view' ? 'disabled' : '' }}>
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Dépôt Actif (visible dans les sélecteurs)
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Colonne Droite: Localisation & Contact --}}
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <h4 class="text-sm font-bold text-gray-900 uppercase mb-4 flex items-center">
                                <x-iconify icon="lucide:map-pin" class="w-4 h-4 mr-2 text-green-500" /> Localisation
                            </h4>

                            <div class="space-y-4">
                                <x-input wire:model="address" name="address" label="Adresse" placeholder="ex: Zone Industrielle" :disabled="$modalMode === 'view'" />

                                <div class="grid grid-cols-2 gap-4">
                                    <x-input wire:model="city" name="city" label="Ville" placeholder="ex: Rouiba" :disabled="$modalMode === 'view'" />
                                    <x-input wire:model="wilaya" name="wilaya" label="Wilaya" placeholder="ex: Alger" :disabled="$modalMode === 'view'" />
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <x-input wire:model="latitude" name="latitude" label="Latitude" placeholder="0.0000" :disabled="$modalMode === 'view'" />
                                    <x-input wire:model="longitude" name="longitude" label="Longitude" placeholder="0.0000" :disabled="$modalMode === 'view'" />
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <h4 class="text-sm font-bold text-gray-900 uppercase mb-4 flex items-center">
                                <x-iconify icon="lucide:phone" class="w-4 h-4 mr-2 text-purple-500" /> Contact & Responsable
                            </h4>

                            <div class="space-y-4">
                                <x-input wire:model="manager_name" name="manager_name" label="Responsable du site" placeholder="Nom complet" :disabled="$modalMode === 'view'" />

                                <div class="grid grid-cols-2 gap-4">
                                    <x-input wire:model="phone" name="phone" label="Tél. Dépôt" placeholder="+213..." :disabled="$modalMode === 'view'" />
                                    <x-input wire:model="manager_phone" name="manager_phone" label="Tél. Responsable" placeholder="+213..." :disabled="$modalMode === 'view'" />
                                </div>

                                <x-input wire:model="email" name="email" type="email" label="Email" placeholder="contact@depot.com" :disabled="$modalMode === 'view'" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                @if($modalMode !== 'view')
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end gap-3">
                    <x-button type="button" variant="secondary" @click="$dispatch('close-modal', 'depot-modal')">
                        Annuler
                    </x-button>
                    <x-button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $modalMode === 'create' ? 'Créer le Dépôt' : 'Enregistrer les modifications' }}</span>
                        <span wire:loading class="flex items-center">
                            <x-iconify icon="lucide:loader-2" class="animate-spin w-4 h-4 mr-2" />
                            Traitement...
                        </span>
                    </x-button>
                </div>
                @else
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end">
                    <x-button type="button" variant="secondary" @click="$dispatch('close-modal', 'depot-modal')">
                        Fermer
                    </x-button>
                </div>
                @endif
            </form>
        </x-modal>

        {{-- Scripts Livewire Events --}}
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
                Livewire.on('depot-saved', () => {
                    // Optionnel: Notification toast JS si non géré par session flash
                });
            });
        </script>
        @endpush
    </div>
</div>