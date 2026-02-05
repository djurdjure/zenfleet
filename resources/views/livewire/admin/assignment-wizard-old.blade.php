<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-7xl mx-auto">

        {{-- ====================================================================
            HEADER AVEC ANALYTICS
        ==================================================================== --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-user-tag text-blue-600"></i>
                        Nouvelle Affectation
                    </h1>
                    <p class="text-gray-600 mt-1">Assignez un véhicule disponible à un chauffeur en temps réel</p>
                </div>

                {{-- Quick Stats --}}
                <div class="flex items-center gap-4">
                    <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-car text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Véhicules Parking</p>
                                <p class="text-xl font-bold text-gray-900">{{ $analytics['total_vehicles_parking'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-check text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Chauffeurs Dispo</p>
                                <p class="text-xl font-bold text-gray-900">{{ $analytics['total_drivers_available'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Messages de succès/erreur --}}
            @if($successMessage)
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    <span class="text-green-800 font-medium">{{ $successMessage }}</span>
                </div>
            @endif

            @if($errorMessage)
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                    <span class="text-red-800 font-medium">{{ $errorMessage }}</span>
                </div>
            @endif

            {{-- Conflits détectés --}}
            @if($hasConflicts && count($conflicts) > 0)
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-0.5"></i>
                        <div class="flex-1">
                            <h4 class="text-yellow-800 font-semibold mb-2">⚠️ Conflits détectés ({{ count($conflicts) }})</h4>
                            <ul class="space-y-1">
                                @foreach($conflicts as $conflict)
                                    <li class="text-sm text-yellow-700">
                                        <i class="fas fa-circle text-xs mr-2"></i>
                                        {{ $conflict['message'] ?? 'Conflit détecté' }}
                                    </li>
                                @endforeach
                            </ul>
                            <button wire:click="suggestSlot"
                                    class="mt-3 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm">
                                <i class="fas fa-magic mr-2"></i>Suggérer un créneau libre
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ====================================================================
            GRILLE 2 COLONNES PRINCIPALE
        ==================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            {{-- ========== COLONNE GAUCHE: SÉLECTION VÉHICULE ========== --}}
            <div>
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-car"></i>
                            Sélectionner un véhicule
                        </h2>
                        <p class="text-blue-100 text-sm mt-1">Véhicules au parking uniquement</p>
                    </div>

                    {{-- Barre de recherche + Filtres --}}
                    <div class="p-4 bg-gray-50 border-b border-gray-200">
                        <div class="mb-3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text"
                                       wire:model.live.debounce.300ms="vehicleSearch"
                                       placeholder="Rechercher (immat, marque, modèle...)"
                                       class="pl-10 pr-4 py-2.5 w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        {{-- Filtres rapides --}}
                        <div class="flex gap-2">
                            <button wire:click="$set('vehicleTypeFilter', '')"
                                    class="px-3 py-1.5 text-xs rounded-full {{ $vehicleTypeFilter === '' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300' }}">
                                Tous
                            </button>
                            {{-- Ajouter plus de filtres selon vos types --}}
                        </div>
                    </div>

                    {{-- Liste des véhicules --}}
                    <div class="overflow-y-auto" style="max-height: 600px;">
                        @forelse($availableVehicles as $vehicle)
                            <div wire:key="vehicle-{{ $vehicle->id }}"
                                 wire:click="selectVehicle({{ $vehicle->id }})"
                                 class="p-4 border-b border-gray-100 cursor-pointer transition-all hover:bg-blue-50 {{ $selectedVehicleId === $vehicle->id ? 'bg-blue-100 border-l-4 border-l-blue-600' : '' }}">
                                <div class="flex items-center gap-4">
                                    {{-- Icône véhicule --}}
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white text-2xl flex-shrink-0">
                                        <i class="fas fa-car"></i>
                                    </div>

                                    {{-- Info véhicule --}}
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                                            {{ $vehicle->registration_plate }}
                                            @if($selectedVehicleId === $vehicle->id)
                                                <i class="fas fa-check-circle text-blue-600 text-sm"></i>
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            {{ $vehicle->brand }} {{ $vehicle->model }}
                                        </p>
                                        <div class="flex items-center gap-3 mt-1">
                                            @if($vehicle->vehicleType)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                                    <i class="fas fa-tag mr-1"></i>
                                                    {{ $vehicle->vehicleType->name }}
                                                </span>
                                            @endif
                                            @if($vehicle->depot)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                                    <i class="fas fa-warehouse mr-1"></i>
                                                    {{ $vehicle->depot->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Statut badge --}}
                                    <div>
                                        @if($vehicle->vehicleStatus)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                <i class="fas fa-parking mr-1"></i>
                                                {{ $vehicle->vehicleStatus->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                <p class="font-medium">Aucun véhicule disponible au parking</p>
                                <p class="text-sm mt-1">Essayez d'ajuster vos filtres de recherche</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ========== COLONNE DROITE: SÉLECTION CHAUFFEUR ========== --}}
            <div>
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-user"></i>
                            Sélectionner un chauffeur
                        </h2>
                        <p class="text-green-100 text-sm mt-1">Chauffeurs disponibles uniquement</p>
                    </div>

                    {{-- Barre de recherche --}}
                    <div class="p-4 bg-gray-50 border-b border-gray-200">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text"
                                   wire:model.live.debounce.300ms="driverSearch"
                                   placeholder="Rechercher (nom, prénom, permis...)"
                                   class="pl-10 pr-4 py-2.5 w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>

                    {{-- Liste des chauffeurs --}}
                    <div class="overflow-y-auto" style="max-height: 600px;">
                        @forelse($availableDrivers as $driver)
                            <div wire:key="driver-{{ $driver->id }}"
                                 wire:click="selectDriver({{ $driver->id }})"
                                 class="p-4 border-b border-gray-100 cursor-pointer transition-all hover:bg-green-50 {{ $selectedDriverId === $driver->id ? 'bg-green-100 border-l-4 border-l-green-600' : '' }}">
                                <div class="flex items-center gap-4">
                                    {{-- Avatar --}}
                                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                                        {{ strtoupper(substr($driver->first_name, 0, 1)) }}{{ strtoupper(substr($driver->last_name, 0, 1)) }}
                                    </div>

                                    {{-- Info chauffeur --}}
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                                            {{ $driver->full_name }}
                                            @if($selectedDriverId === $driver->id)
                                                <i class="fas fa-check-circle text-green-600 text-sm"></i>
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-id-card mr-1"></i>
                                            {{ $driver->license_number ?? 'N/A' }}
                                        </p>
                                        @if($driver->employee_number)
                                            <p class="text-xs text-gray-500 mt-1">
                                                Matricule: {{ $driver->employee_number }}
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Statut badge --}}
                                    <div>
                                        @if($driver->driverStatus)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                <i class="fas fa-check mr-1"></i>
                                                {{ $driver->driverStatus->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">
                                <i class="fas fa-user-slash text-4xl mb-3 text-gray-300"></i>
                                <p class="font-medium">Aucun chauffeur disponible</p>
                                <p class="text-sm mt-1">Tous les chauffeurs sont en mission ou indisponibles</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- ====================================================================
            SECTION FORMULAIRE DÉTAILS & VALIDATION
        ==================================================================== --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i>
                    Détails de l'affectation
                </h2>
            </div>

            <div class="p-6">
                {{-- Résumé sélection --}}
                @if($selectedVehicle || $selectedDriver)
                    <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                        <h3 class="font-semibold text-purple-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            Résumé de la sélection
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-purple-700 mb-1">Véhicule sélectionné:</p>
                                @if($selectedVehicle)
                                    <p class="font-bold text-purple-900">
                                        <i class="fas fa-car mr-1"></i>
                                        {{ $selectedVehicle->registration_plate }} - {{ $selectedVehicle->brand }} {{ $selectedVehicle->model }}
                                    </p>
                                @else
                                    <p class="text-gray-400 italic">Aucun véhicule sélectionné</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm text-purple-700 mb-1">Chauffeur sélectionné:</p>
                                @if($selectedDriver)
                                    <p class="font-bold text-purple-900">
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $selectedDriver->full_name }} ({{ $selectedDriver->license_number }})
                                    </p>
                                @else
                                    <p class="text-gray-400 italic">Aucun chauffeur sélectionné</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Formulaire dates et détails --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Date début --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-day mr-1 text-blue-600"></i>
                            Date et heure de début *
                        </label>
                        <input type="datetime-local"
                               wire:model.live="startDatetime"
                               wire:change="validateInRealTime"
                               class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                               required>
                        @error('startDatetime')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date fin --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-check mr-1 text-green-600"></i>
                            Date et heure de fin
                            <label class="inline-flex items-center ml-3">
                                <input type="checkbox" wire:model.live="isIndefinite" wire:change="toggleIndefinite" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-600">Durée indéterminée</span>
                            </label>
                        </label>
                        <input type="datetime-local"
                               wire:model.live="endDatetime"
                               wire:change="validateInRealTime"
                               {{ $isIndefinite ? 'disabled' : '' }}
                               class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 {{ $isIndefinite ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                        @error('endDatetime')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Raison --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment-alt mr-1 text-orange-600"></i>
                        Raison de l'affectation
                    </label>
                    <input type="text"
                           wire:model="reason"
                           placeholder="Ex: Mission régulière, Remplacement, Transport spécial..."
                           class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           maxlength="500">
                </div>

                {{-- Notes --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sticky-note mr-1 text-yellow-600"></i>
                        Notes complémentaires
                    </label>
                    <textarea wire:model="notes"
                              rows="3"
                              placeholder="Instructions spéciales, itinéraire, consignes particulières..."
                              class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                              maxlength="1000"></textarea>
                </div>

                {{-- Boutons d'action --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div>
                        <button type="button"
                                wire:click="suggestSlot"
                                class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors font-medium">
                            <i class="fas fa-magic mr-2"></i>
                            Suggérer un créneau libre
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button"
                                onclick="window.location.reload()"
                                class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                            <i class="fas fa-times mr-2"></i>
                            Annuler
                        </button>

                        <button type="button"
                                wire:click="createAssignment"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                {{ !$selectedVehicleId || !$selectedDriverId || !$startDatetime || $hasConflicts ? 'disabled' : '' }}
                                class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all font-bold shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove>
                                <i class="fas fa-check-circle mr-2"></i>
                                Créer l'affectation
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Création en cours...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Loading overlay --}}
    <div wire:loading.flex class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 flex flex-col items-center">
            <i class="fas fa-spinner fa-spin text-4xl text-purple-600 mb-4"></i>
            <p class="text-gray-700 font-medium">Traitement en cours...</p>
        </div>
    </div>
</div>
