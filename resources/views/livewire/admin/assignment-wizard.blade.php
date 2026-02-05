{{-- 
    ASSIGNMENT WIZARD ENTERPRISE-GRADE ULTRA-PRO
    Système d'affectation surpassant Fleetio, Samsara et Verizon Connect
    Version 3.0 - Design System Unifié avec Iconify
--}}

<div class="space-y-6">

    {{-- ====================================================================
        HEADER AVEC ANALYTICS EN TEMPS RÉEL
    ==================================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        {{-- Card Véhicules disponibles --}}
        <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Véhicules au Parking</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $analytics['total_vehicles_parking'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 border border-blue-200 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:car" class="w-6 h-6 text-blue-600" />
                </div>
            </div>
        </div>

        {{-- Card Chauffeurs disponibles --}}
        <div class="bg-green-50 rounded-lg border border-green-200 p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Chauffeurs Disponibles</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $analytics['total_drivers_available'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 border border-green-200 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:user-check" class="w-6 h-6 text-green-600" />
                </div>
            </div>
        </div>

        {{-- Card Affectations actives --}}
        <div class="bg-orange-50 rounded-lg border border-orange-200 p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Affectations Actives</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $analytics['active_assignments'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 border border-orange-200 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:git-branch" class="w-6 h-6 text-orange-600" />
                </div>
            </div>
        </div>
    </div>

    {{-- ====================================================================
        MESSAGES & ALERTES
    ==================================================================== --}}
    
    {{-- Message de succès --}}
    @if($successMessage)
        <div x-data="{ show: true }" 
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center gap-3">
                <x-iconify icon="lucide:check-circle-2" class="w-6 h-6 text-green-600 flex-shrink-0" />
                <p class="text-sm font-medium text-green-800 flex-1">{{ $successMessage }}</p>
                <button @click="show = false" class="text-green-600 hover:text-green-800 transition-colors">
                    <x-iconify icon="lucide:x" class="w-5 h-5" />
                </button>
            </div>
        </div>
    @endif

    {{-- Message d'erreur --}}
    @if($errorMessage)
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center gap-3">
                <x-iconify icon="lucide:alert-circle" class="w-6 h-6 text-red-600 flex-shrink-0" />
                <p class="text-sm font-medium text-red-800 flex-1">{{ $errorMessage }}</p>
                <button @click="show = false" class="text-red-600 hover:text-red-800 transition-colors">
                    <x-iconify icon="lucide:x" class="w-5 h-5" />
                </button>
            </div>
        </div>
    @endif

    {{-- Détection de conflits --}}
    @if($hasConflicts && count($conflicts) > 0)
        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start gap-3">
                <x-iconify icon="lucide:alert-triangle" class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" />
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-yellow-800 mb-2">
                        Conflits détectés ({{ count($conflicts) }})
                    </h4>
                    <ul class="space-y-1">
                        @foreach($conflicts as $conflict)
                            <li class="text-sm text-yellow-700 flex items-start gap-2">
                                <x-iconify icon="lucide:chevron-right" class="w-4 h-4 mt-0.5 flex-shrink-0" />
                                <span>{{ $conflict['message'] ?? 'Conflit détecté' }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <button wire:click="suggestSlot"
                            wire:loading.attr="disabled"
                            class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 text-white rounded-lg 
                                   hover:bg-yellow-700 transition-colors text-sm font-medium disabled:opacity-50">
                        <x-iconify icon="lucide:sparkles" class="w-4 h-4" wire:loading.class="animate-spin" />
                        <span>Suggérer un créneau libre</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ====================================================================
        GRILLE PRINCIPALE: VÉHICULES & CHAUFFEURS
    ==================================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ========== COLONNE GAUCHE: SÉLECTION VÉHICULE ========== --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            {{-- Header de section --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <x-iconify icon="lucide:car" class="w-5 h-5" />
                    Sélectionner un véhicule
                </h2>
                <p class="text-blue-100 text-sm mt-1">Véhicules disponibles au parking</p>
            </div>

            {{-- Barre de recherche et filtres --}}
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <div class="space-y-3">
                    {{-- Recherche --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                        </div>
                        <input type="text"
                               wire:model.live.debounce.300ms="vehicleSearch"
                               placeholder="Rechercher (immatriculation, marque, modèle...)"
                               class="pl-10 pr-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    {{-- Filtres rapides --}}
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="$set('vehicleTypeFilter', '')"
                                class="px-3 py-1.5 text-xs font-medium rounded-full transition-all
                                       {{ $vehicleTypeFilter === '' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                            Tous les types
                        </button>
                        @foreach(['sedan' => 'Berline', 'suv' => 'SUV', 'van' => 'Utilitaire'] as $key => $label)
                            <button wire:click="$set('vehicleTypeFilter', '{{ $key }}')"
                                    class="px-3 py-1.5 text-xs font-medium rounded-full transition-all
                                           {{ $vehicleTypeFilter === $key ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Liste des véhicules --}}
            <div class="overflow-y-auto" style="max-height: 500px;">
                @forelse($availableVehicles as $vehicle)
                    <div wire:key="vehicle-{{ $vehicle->id }}"
                         wire:click="selectVehicle({{ $vehicle->id }})"
                         class="p-4 border-b border-gray-100 cursor-pointer transition-all duration-200
                                hover:bg-blue-50 {{ $selectedVehicleId === $vehicle->id ? 'bg-blue-50 border-l-4 border-l-blue-600' : '' }}">
                        
                        <div class="flex items-center gap-4">
                            {{-- Icône/Photo véhicule --}}
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md flex-shrink-0">
                                <x-iconify icon="lucide:car" class="w-7 h-7" />
                            </div>

                            {{-- Informations véhicule --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                                    {{ $vehicle->registration_plate }}
                                    @if($selectedVehicleId === $vehicle->id)
                                        <x-iconify icon="lucide:check-circle-2" class="w-5 h-5 text-blue-600" />
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-600 truncate">
                                    {{ $vehicle->brand }} {{ $vehicle->model }}
                                    @if($vehicle->year)
                                        <span class="text-gray-400">({{ $vehicle->year }})</span>
                                    @endif
                                </p>
                                
                                {{-- Badges --}}
                                <div class="flex items-center gap-2 mt-2">
                                    @if($vehicle->vehicleType)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                            <x-iconify icon="lucide:tag" class="w-3 h-3" />
                                            {{ $vehicle->vehicleType->name }}
                                        </span>
                                    @endif
                                    @if($vehicle->depot)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700">
                                            <x-iconify icon="lucide:map-pin" class="w-3 h-3" />
                                            {{ $vehicle->depot->name }}
                                        </span>
                                    @endif
                                    @if($vehicle->fuel_type)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-700">
                                            <x-iconify icon="lucide:fuel" class="w-3 h-3" />
                                            {{ ucfirst($vehicle->fuel_type) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Statut --}}
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                    <x-iconify icon="lucide:check" class="w-3 h-3" />
                                    Disponible
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <x-iconify icon="lucide:car-off" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                        <p class="text-gray-500 font-medium">Aucun véhicule disponible</p>
                        <p class="text-sm text-gray-400 mt-1">Tous les véhicules sont actuellement affectés</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ========== COLONNE DROITE: SÉLECTION CHAUFFEUR ========== --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            {{-- Header de section --}}
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <x-iconify icon="lucide:user" class="w-5 h-5" />
                    Sélectionner un chauffeur
                </h2>
                <p class="text-green-100 text-sm mt-1">Chauffeurs disponibles pour affectation</p>
            </div>

            {{-- Barre de recherche --}}
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                    </div>
                    <input type="text"
                           wire:model.live.debounce.300ms="driverSearch"
                           placeholder="Rechercher un chauffeur..."
                           class="pl-10 pr-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                </div>
            </div>

            {{-- Liste des chauffeurs --}}
            <div class="overflow-y-auto" style="max-height: 500px;">
                @forelse($availableDrivers as $driver)
                    <div wire:key="driver-{{ $driver->id }}"
                         wire:click="selectDriver({{ $driver->id }})"
                         class="p-4 border-b border-gray-100 cursor-pointer transition-all duration-200
                                hover:bg-green-50 {{ $selectedDriverId === $driver->id ? 'bg-green-50 border-l-4 border-l-green-600' : '' }}">
                        
                        <div class="flex items-center gap-4">
                            {{-- Avatar --}}
                            <div class="flex-shrink-0">
                                @if($driver->photo)
                                    <img src="{{ Storage::url($driver->photo) }}" 
                                         alt="{{ $driver->full_name }}"
                                         class="w-14 h-14 rounded-full object-cover ring-2 ring-green-100">
                                @else
                                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                                        {{ strtoupper(substr($driver->first_name, 0, 1)) }}{{ strtoupper(substr($driver->last_name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            {{-- Informations chauffeur --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                                    {{ $driver->full_name }}
                                    @if($selectedDriverId === $driver->id)
                                        <x-iconify icon="lucide:check-circle-2" class="w-5 h-5 text-green-600" />
                                    @endif
                                </h3>
                                
                                {{-- Contact --}}
                                <div class="flex items-center gap-3 text-sm text-gray-500 mt-1">
                                    @if($driver->personal_phone)
                                        <span class="flex items-center gap-1">
                                            <x-iconify icon="lucide:phone" class="w-3.5 h-3.5" />
                                            {{ $driver->personal_phone }}
                                        </span>
                                    @endif
                                    @if($driver->license_number)
                                        <span class="flex items-center gap-1">
                                            <x-iconify icon="lucide:credit-card" class="w-3.5 h-3.5" />
                                            {{ $driver->license_number }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Badges --}}
                                <div class="flex items-center gap-2 mt-2">
                                    @if($driver->driverStatus)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium 
                                                     {{ $driver->driverStatus->slug === 'disponible' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                            <x-iconify icon="lucide:user-check" class="w-3 h-3" />
                                            {{ $driver->driverStatus->name }}
                                        </span>
                                    @endif
                                    @if($driver->license_expiry_date)
                                        @php
                                            $daysUntilExpiry = now()->diffInDays($driver->license_expiry_date, false);
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium 
                                                     {{ $daysUntilExpiry < 30 ? 'bg-red-100 text-red-700' : ($daysUntilExpiry < 90 ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">
                                            <x-iconify icon="lucide:calendar-check" class="w-3 h-3" />
                                            Permis: {{ $driver->license_expiry_date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Statut --}}
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                    <x-iconify icon="lucide:check" class="w-3 h-3" />
                                    Disponible
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <x-iconify icon="lucide:users-x" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                        <p class="text-gray-500 font-medium">Aucun chauffeur disponible</p>
                        <p class="text-sm text-gray-400 mt-1">Tous les chauffeurs sont actuellement en mission</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ====================================================================
        FORMULAIRE DE DÉTAILS D'AFFECTATION
    ==================================================================== --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <x-iconify icon="lucide:calendar-clock" class="w-5 h-5 text-indigo-600" />
            Détails de l'affectation
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Date et heure de début --}}
            <div>
                <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-1">
                    Date et heure de début <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-iconify icon="lucide:calendar" class="w-5 h-5 text-gray-400" />
                    </div>
                    <input type="datetime-local"
                           id="start_datetime"
                           wire:model="startDatetime"
                           wire:change="validateDates"
                           required
                           class="pl-10 pr-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                @error('startDatetime')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Date et heure de fin --}}
            <div>
                <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-1">
                    Date et heure de fin 
                    <span class="text-gray-400 text-xs">(optionnel)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-iconify icon="lucide:calendar-x" class="w-5 h-5 text-gray-400" />
                    </div>
                    <input type="datetime-local"
                           id="end_datetime"
                           wire:model="endDatetime"
                           wire:change="validateDates"
                           {{ $isIndefinite ? 'disabled' : '' }}
                           class="pl-10 pr-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                </div>
                
                {{-- Checkbox durée indéterminée --}}
                <div class="mt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox"
                               wire:model="isIndefinite"
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Durée indéterminée</span>
                    </label>
                </div>
                @error('endDatetime')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Raison de l'affectation --}}
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">
                    Raison de l'affectation <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-iconify icon="lucide:file-text" class="w-5 h-5 text-gray-400" />
                    </div>
                    <select id="reason"
                            wire:model="reason"
                            required
                            class="pl-10 pr-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Sélectionner une raison</option>
                        <option value="mission">Mission régulière</option>
                        <option value="remplacement">Remplacement</option>
                        <option value="formation">Formation</option>
                        <option value="essai">Essai routier</option>
                        <option value="livraison">Livraison</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                @error('reason')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Notes additionnelles --}}
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    Notes additionnelles
                </label>
                <div class="relative">
                    <div class="absolute top-3 left-3 pointer-events-none">
                        <x-iconify icon="lucide:message-square" class="w-5 h-5 text-gray-400" />
                    </div>
                    <textarea id="notes"
                              wire:model="notes"
                              rows="3"
                              placeholder="Instructions spéciales, observations..."
                              class="pl-10 pr-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm resize-none"></textarea>
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ strlen($notes) }}/500 caractères</p>
            </div>
        </div>
    </div>

    {{-- ====================================================================
        BOUTONS D'ACTION
    ==================================================================== --}}
    <div class="flex items-center justify-between bg-white rounded-lg border border-gray-200 p-6">
        {{-- Résumé de sélection --}}
        <div class="flex items-center gap-6 text-sm">
            @if($selectedVehicle)
                <div class="flex items-center gap-2">
                    <x-iconify icon="lucide:car" class="w-5 h-5 text-blue-600" />
                    <span class="font-medium text-gray-900">{{ $selectedVehicle->registration_plate }}</span>
                </div>
            @endif
            
            @if($selectedDriver)
                <div class="flex items-center gap-2">
                    <x-iconify icon="lucide:user" class="w-5 h-5 text-green-600" />
                    <span class="font-medium text-gray-900">{{ $selectedDriver->full_name }}</span>
                </div>
            @endif
        </div>

        {{-- Boutons --}}
        <div class="flex items-center gap-3">
            {{-- Bouton Valider --}}
            <button type="button"
                    wire:click="validateAssignment"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 
                           transition-all duration-200 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <x-iconify icon="lucide:shield-check" class="w-5 h-5" wire:loading.class="animate-pulse" />
                <span>Valider l'affectation</span>
            </button>

            {{-- Bouton Créer --}}
            <button type="button"
                    wire:click="createAssignment"
                    wire:loading.attr="disabled"
                    {{ !$selectedVehicleId || !$selectedDriverId || !$reason ? 'disabled' : '' }}
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 
                           transition-all duration-200 font-medium text-sm shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                <x-iconify icon="lucide:check" class="w-5 h-5" wire:loading.class="animate-spin" />
                <span wire:loading.remove wire:target="createAssignment">Créer l'affectation</span>
                <span wire:loading wire:target="createAssignment">Création...</span>
            </button>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Auto-dismiss des messages après 5 secondes
    document.addEventListener('livewire:init', () => {
        Livewire.on('assignment-created', () => {
            setTimeout(() => {
                window.location.href = '{{ route("admin.assignments.index") }}';
            }, 2000);
        });
    });
</script>
@endpush
