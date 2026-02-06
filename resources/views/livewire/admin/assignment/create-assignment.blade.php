{{--
 🚀 INTERFACE LIVEWIRE ENTERPRISE - Création d'Affectation Véhicule-Chauffeur

 FONCTIONNALITÉS UI:
 ✅ Validation temps réel Livewire
 ✅ Alertes conflits intelligentes
 ✅ Design responsive mobile-first
 ✅ Accessibilité WCAG 2.1 AA
 ✅ Animations fluides
 ✅ États de chargement
 ✅ Messages d'erreur contextuels
--}}

<div class="space-y-6 fade-in" x-data="{ showAdvanced: false }">
 {{-- 🎨 HEADER ENTERPRISE AVEC STATISTIQUES --}}
 <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 rounded-2xl p-8 border border-blue-200 shadow-lg">
 <div class="flex items-center justify-between">
 <div class="flex-1">
 <div class="flex items-center space-x-4 mb-4">
 <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl flex items-center justify-center shadow-lg">
 <i class="fas fa-exchange-alt text-white text-2xl"></i>
 </div>
 <div>
 <h1 class="text-2xl font-bold text-gray-900">
 Nouvelle Affectation Véhicule-Chauffeur
 </h1>
 <p class="text-gray-600 mt-1">Système de détection de conflits en temps réel</p>
 </div>
 </div>

 {{-- Statistiques temps réel --}}
 <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
 <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-white/20">
 <div class="flex items-center space-x-3">
 <div class="w-10 h-10 bg-green-100 border border-green-300 rounded-full flex items-center justify-center">
 <i class="fas fa-car text-green-600"></i>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-600">Véhicules disponibles</p>
 <p class="text-xl font-bold text-green-600">{{ $this->availableVehicles->count() }}</p>
 </div>
 </div>
 </div>

 <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-white/20">
 <div class="flex items-center space-x-3">
 <div class="w-10 h-10 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
 <i class="fas fa-user-tie text-blue-600"></i>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-600">Chauffeurs libres</p>
 <p class="text-xl font-bold text-blue-600">{{ $this->availableDrivers->count() }}</p>
 </div>
 </div>
 </div>

 <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-white/20">
 <div class="flex items-center space-x-3">
 <div class="w-10 h-10 bg-purple-100 border border-purple-300 rounded-full flex items-center justify-center">
 <i class="fas fa-shield-alt text-purple-600"></i>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-600">Statut validation</p>
 @if($validation_complete && !$has_conflicts)
 <p class="text-xl font-bold text-green-600">✓ Validé</p>
 @elseif($has_conflicts)
 <p class="text-xl font-bold text-red-600">⚠ Conflits</p>
 @else
 <p class="text-xl font-bold text-gray-400">En attente</p>
 @endif
 </div>
 </div>
 </div>
 </div>
 </div>

 <div class="flex space-x-3">
 <a href="{{ route('admin.assignments.index') }}" class="btn-secondary">
 <i class="fas fa-arrow-left mr-2"></i>
 Retour à la liste
 </a>
 </div>
 </div>
 </div>

 {{-- 🚨 ALERTES DE CONFLITS TEMPS RÉEL --}}
 @if($has_conflicts)
 <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-xl shadow-lg animate-pulse" role="alert" aria-live="assertive">
 <div class="flex items-start">
 <div class="flex-shrink-0">
 <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
 </div>
 <div class="ml-4 flex-1">
 <h3 class="text-lg font-bold text-red-900 mb-3">
 ⚠️ {{ count($conflicts) }} Conflit(s) Détecté(s)
 </h3>
 <div class="space-y-3">
 @foreach($conflicts as $index => $conflict)
 <div class="bg-white rounded-lg p-4 border-l-4 border-red-400">
 <div class="flex items-start justify-between">
 <div class="flex-1">
 <p class="font-semibold text-red-800 mb-1">
 <i class="fas {{ $conflict['type'] === 'vehicle' ? 'fa-car' : 'fa-user-tie' }} mr-2"></i>
 {{ $conflict['resource'] }}: {{ $conflict['resource_name'] }}
 </p>
 <p class="text-sm text-red-700">{{ $conflict['message'] }}</p>
 <p class="text-xs text-gray-500 mt-2">
 <i class="fas fa-clock mr-1"></i>
 Période en conflit: {{ $conflict['period'] }}
 </p>
 <p class="text-xs text-gray-500">
 <i class="fas fa-link mr-1"></i>
 Affectation #{{ $conflict['assignment_id'] }}
 </p>
 </div>
 @if($conflict['can_override'])
 <button type="button"
 wire:click="$set('force_create', true)"
 class="ml-4 px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors shadow"
 @if($force_create) disabled @endif>
 @if($force_create)
 <i class="fas fa-check mr-1"></i> Mode forcé activé
 @else
 <i class="fas fa-unlock-alt mr-1"></i> Forcer (Admin)
 @endif
 </button>
 @endif
 </div>
 </div>
 @endforeach
 </div>

 @if($force_create)
 <div class="mt-4 p-3 bg-yellow-50 border border-yellow-300 rounded-lg">
 <p class="text-sm text-yellow-800">
 <i class="fas fa-info-circle mr-2"></i>
 <strong>Mode forcé activé:</strong> L'affectation sera créée malgré les conflits détectés. Cette action sera enregistrée dans l'audit trail.
 </p>
 </div>
 @endif
 </div>
 </div>
 </div>
 @endif

 {{-- ✅ VALIDATION RÉUSSIE --}}
 @if($validation_complete && !$has_conflicts && $vehicle_id && $driver_id)
 <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg" role="status" aria-live="polite">
 <div class="flex items-center">
 <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
 <p class="text-green-800 font-semibold">
 ✓ Aucun conflit détecté. L'affectation peut être créée.
 </p>
 </div>
 </div>
 @endif

 {{-- 📋 FORMULAIRE PRINCIPAL --}}
 <form wire:submit="create" class="space-y-6">
 {{-- SECTION 1: SÉLECTION VÉHICULE --}}
 <div class="form-section">
 <h3>
 <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center">
 <i class="fas fa-car text-white text-sm"></i>
 </div>
 Sélection du Véhicule
 </h3>

 <div class="form-group">
 <label for="vehicle_id" class="form-label required">
 Véhicule disponible ({{ $this->availableVehicles->count() }} disponible{{ $this->availableVehicles->count() > 1 ? 's' : '' }})
 </label>

 <select wire:model.live="vehicle_id"
 id="vehicle_id"
 class="form-select @error('vehicle_id') border-red-500 @enderror"
 aria-required="true"
 aria-describedby="vehicle_id_error">
 <option value="">Sélectionnez un véhicule</option>
 @foreach($this->availableVehicles as $vehicle)
 <option value="{{ $vehicle->id }}">
 {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
 @if($vehicle->current_mileage)
 ({{ number_format($vehicle->current_mileage, 0, ',', ' ') }} km)
 @endif
 - {{ $vehicle->vehicleType->name ?? 'N/A' }}
 </option>
 @endforeach
 </select>

 @error('vehicle_id')
 <p class="error-message" id="vehicle_id_error">{{ $message }}</p>
 @enderror

 {{-- Affichage détails véhicule sélectionné --}}
 @if($this->selectedVehicle)
 <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
 <div class="flex items-center space-x-4">
 <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center">
 <i class="fas fa-car text-white"></i>
 </div>
 <div class="flex-1">
 <p class="font-bold text-blue-900">{{ $this->selectedVehicle->registration_plate }}</p>
 <p class="text-sm text-blue-700">{{ $this->selectedVehicle->brand }} {{ $this->selectedVehicle->model }}</p>
 <div class="flex items-center space-x-3 text-xs text-blue-600 mt-1">
 @if($this->selectedVehicle->current_mileage)
 <span><i class="fas fa-tachometer-alt mr-1"></i>{{ number_format($this->selectedVehicle->current_mileage, 0, ',', ' ') }} km</span>
 @endif
 @if($this->selectedVehicle->vehicleType)
 <span><i class="fas fa-tag mr-1"></i>{{ $this->selectedVehicle->vehicleType->name }}</span>
 @endif
 @if($this->selectedVehicle->vehicleStatus)
 <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full">
 <i class="fas fa-check-circle mr-1"></i>{{ $this->selectedVehicle->vehicleStatus->name }}
 </span>
 @endif
 </div>
 </div>
 </div>
 </div>
 @endif

 <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
 <div class="flex items-center space-x-2 text-sm text-blue-700">
 <i class="fas fa-info-circle"></i>
 <span><strong>Filtrage intelligent:</strong> Véhicules disponibles sans affectation en cours</span>
 </div>
 </div>
 </div>
 </div>

 {{-- SECTION 2: SÉLECTION CHAUFFEUR --}}
 <div class="form-section">
 <h3>
 <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-700 rounded-xl flex items-center justify-center">
 <i class="fas fa-user-tie text-white text-sm"></i>
 </div>
 Sélection du Chauffeur
 </h3>

 <div class="form-group">
 <label for="driver_id" class="form-label required">
 Chauffeur disponible ({{ $this->availableDrivers->count() }} libre{{ $this->availableDrivers->count() > 1 ? 's' : '' }})
 </label>

 <select wire:model.live="driver_id"
 id="driver_id"
 class="form-select @error('driver_id') border-red-500 @enderror"
 aria-required="true"
 aria-describedby="driver_id_error">
 <option value="">Sélectionnez un chauffeur</option>
 @foreach($this->availableDrivers as $driver)
 <option value="{{ $driver->id }}">
 {{ $driver->first_name }} {{ $driver->last_name }}
 @if($driver->personal_phone)
 - Tél: {{ $driver->personal_phone }}
 @endif
 @if($driver->driver_license_number)
 - Permis: {{ $driver->driver_license_number }}
 @endif
 </option>
 @endforeach
 </select>

 @error('driver_id')
 <p class="error-message" id="driver_id_error">{{ $message }}</p>
 @enderror

 {{-- Affichage détails chauffeur sélectionné --}}
 @if($this->selectedDriver)
 <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
 <div class="flex items-center space-x-4">
 <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-user-tie text-white"></i>
 </div>
 <div class="flex-1">
 <p class="font-bold text-green-900">{{ $this->selectedDriver->first_name }} {{ $this->selectedDriver->last_name }}</p>
 <div class="flex items-center space-x-3 text-xs text-green-600 mt-1">
 @if($this->selectedDriver->personal_phone)
 <span><i class="fas fa-phone mr-1"></i>{{ $this->selectedDriver->personal_phone }}</span>
 @endif
 @if($this->selectedDriver->driver_license_number)
 <span><i class="fas fa-id-card mr-1"></i>Permis: {{ $this->selectedDriver->driver_license_number }}</span>
 @endif
 <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full">
 <i class="fas fa-check-circle mr-1"></i>Disponible
 </span>
 </div>
 </div>
 </div>
 </div>
 @endif

 <div class="mt-3 bg-green-50 border border-green-200 rounded-lg p-3">
 <div class="flex items-center space-x-2 text-sm text-green-700">
 <i class="fas fa-shield-alt"></i>
 <span><strong>Vérification automatique:</strong> Chauffeurs actifs sans affectation en cours</span>
 </div>
 </div>
 </div>
 </div>

 {{-- SECTION 3: PROGRAMMATION --}}
 <div class="form-section">
 <h3>
 <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-indigo-700 rounded-xl flex items-center justify-center">
 <i class="fas fa-calendar-alt text-white text-sm"></i>
 </div>
 Programmation de l'Affectation
 </h3>

 {{-- Début de l'affectation --}}
 <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-6 border border-blue-200">
 <div class="flex items-center space-x-3 mb-4">
 <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center">
 <i class="fas fa-play text-white"></i>
 </div>
 <h4 class="text-lg font-bold text-blue-900">Début de l'Affectation (Obligatoire)</h4>
 </div>

 <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
 <div class="form-group">
 <label for="start_date" class="form-label required">
 <i class="fas fa-calendar text-blue-600 mr-2"></i>
 Date de début
 </label>
 <input type="date"
 wire:model.live="start_date"
 id="start_date"
 class="form-input @error('start_date') border-red-500 @enderror"
 aria-required="true">
 @error('start_date')
 <p class="error-message">{{ $message }}</p>
 @enderror
 @if($allow_retroactive)
 <p class="text-xs text-blue-600 mt-1">
 <i class="fas fa-info-circle mr-1"></i>
 Dates passées autorisées (planification rétroactive)
 </p>
 @endif
 </div>

 <div class="form-group">
 <label for="start_time" class="form-label required">
 <i class="fas fa-clock text-blue-600 mr-2"></i>
 Heure de début
 </label>
 <input type="time"
 wire:model.live="start_time"
 id="start_time"
 class="form-input @error('start_time') border-red-500 @enderror"
 aria-required="true">
 @error('start_time')
 <p class="error-message">{{ $message }}</p>
 @enderror
 </div>

 <div class="form-group">
 <label for="start_mileage" class="form-label required">
 <i class="fas fa-tachometer-alt text-blue-600 mr-2"></i>
 Kilométrage initial
 </label>
 <input type="number"
 wire:model="start_mileage"
 id="start_mileage"
 class="form-input @error('start_mileage') border-red-500 @enderror"
 min="0"
 step="1"
 placeholder="0"
 aria-required="true">
 @error('start_mileage')
 <p class="error-message">{{ $message }}</p>
 @enderror
 @if($this->selectedVehicle && $this->selectedVehicle->current_mileage)
 <p class="text-xs text-blue-600 mt-1">
 <i class="fas fa-info-circle mr-1"></i>
 Kilométrage actuel: {{ number_format($this->selectedVehicle->current_mileage, 0, ',', ' ') }} km
 </p>
 @endif
 </div>
 </div>
 </div>

 {{-- Type d'affectation --}}
 <div class="mb-6">
 <div class="flex items-center space-x-3 mb-4">
 <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-700 rounded-xl flex items-center justify-center">
 <i class="fas fa-route text-white"></i>
 </div>
 <h4 class="text-lg font-bold text-gray-900">Type d'Affectation</h4>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div class="assignment-type-card @if($assignment_type === 'open') selected-open @endif"
 wire:click="$set('assignment_type', 'open')">
 <div class="flex items-center space-x-4">
 <div class="flex-shrink-0">
 <input type="radio"
 wire:model.live="assignment_type"
 value="open"
 id="assignment_type_open"
 class="w-4 h-4 text-green-600">
 </div>
 <div class="flex-grow">
 <div class="flex items-center space-x-3">
 <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-infinity text-white"></i>
 </div>
 <div>
 <h5 class="font-bold text-green-800">Affectation Ouverte</h5>
 <p class="text-sm text-green-600">Durée indéterminée - À terminer manuellement</p>
 </div>
 </div>
 </div>
 </div>
 </div>

 <div class="assignment-type-card @if($assignment_type === 'scheduled') selected-scheduled @endif"
 wire:click="$set('assignment_type', 'scheduled')">
 <div class="flex items-center space-x-4">
 <div class="flex-shrink-0">
 <input type="radio"
 wire:model.live="assignment_type"
 value="scheduled"
 id="assignment_type_scheduled"
 class="w-4 h-4 text-purple-600">
 </div>
 <div class="flex-grow">
 <div class="flex items-center space-x-3">
 <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-calendar-check text-white"></i>
 </div>
 <div>
 <h5 class="font-bold text-purple-800">Affectation Programmée</h5>
 <p class="text-sm text-purple-600">Avec date et heure de fin précises</p>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- Fin de l'affectation (conditionnel) --}}
 @if($assignment_type === 'scheduled')
 <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-6 border border-purple-200"
 x-data
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform scale-95"
 x-transition:enter-end="opacity-100 transform scale-100">
 <div class="flex items-center space-x-3 mb-4">
 <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-700 rounded-xl flex items-center justify-center">
 <i class="fas fa-stop text-white"></i>
 </div>
 <h4 class="text-lg font-bold text-purple-900">Fin de l'Affectation Programmée</h4>
 </div>

 <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
 <div class="form-group">
 <label for="end_date" class="form-label required">
 <i class="fas fa-calendar text-purple-600 mr-2"></i>
 Date de fin
 </label>
 <input type="date"
 wire:model.live="end_date"
 id="end_date"
 class="form-input @error('end_date') border-red-500 @enderror"
 aria-required="true">
 @error('end_date')
 <p class="error-message">{{ $message }}</p>
 @enderror
 </div>

 <div class="form-group">
 <label for="end_time" class="form-label required">
 <i class="fas fa-clock text-purple-600 mr-2"></i>
 Heure de fin
 </label>
 <input type="time"
 wire:model.live="end_time"
 id="end_time"
 class="form-input @error('end_time') border-red-500 @enderror"
 aria-required="true">
 @error('end_time')
 <p class="error-message">{{ $message }}</p>
 @enderror
 </div>

 <div class="form-group">
 <label for="end_mileage" class="form-label">
 <i class="fas fa-route text-purple-600 mr-2"></i>
 Kilométrage final estimé
 </label>
 <input type="number"
 wire:model="end_mileage"
 id="end_mileage"
 class="form-input @error('end_mileage') border-red-500 @enderror"
 min="0"
 step="1"
 placeholder="Optionnel">
 @error('end_mileage')
 <p class="error-message">{{ $message }}</p>
 @enderror
 </div>
 </div>
 </div>
 @endif
 </div>

 {{-- SECTION 4: INFORMATIONS COMPLÉMENTAIRES --}}
 <div class="form-section" x-show="showAdvanced" x-cloak x-transition>
 <h3>
 <div class="w-8 h-8 bg-gradient-to-br from-amber-500 to-orange-700 rounded-xl flex items-center justify-center">
 <i class="fas fa-sticky-note text-white text-sm"></i>
 </div>
 Informations Complémentaires
 </h3>

 <div class="form-group">
 <label for="reason" class="form-label">
 <i class="fas fa-tag mr-2"></i>
 Motif de l'affectation
 </label>
 <select wire:model="reason"
 id="reason"
 class="form-select">
 <option value="">Sélectionnez un motif (optionnel)</option>
 <option value="mission">Mission professionnelle</option>
 <option value="formation">Formation</option>
 <option value="maintenance">Maintenance/Contrôle</option>
 <option value="deplacement">Déplacement administratif</option>
 <option value="urgence">Urgence</option>
 <option value="autre">Autre</option>
 </select>
 </div>

 <div class="form-group">
 <label for="notes" class="form-label">
 <i class="fas fa-comment-alt mr-2"></i>
 Notes et observations
 </label>
 <textarea wire:model="notes"
 id="notes"
 class="form-textarea"
 rows="4"
 maxlength="2000"
 placeholder="Informations complémentaires, itinéraire prévu, contacts, restrictions particulières, etc."></textarea>
 @if($notes)
 <p class="text-xs text-gray-500 mt-1">{{ strlen($notes) }}/2000 caractères</p>
 @endif
 </div>
 </div>

 {{-- Bouton toggle infos complémentaires --}}
 <div class="text-center">
 <button type="button"
 @click="showAdvanced = !showAdvanced"
 class="text-blue-600 hover:text-blue-700 font-medium text-sm">
 <i class="fas" :class="showAdvanced ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
 <span x-text="showAdvanced ? 'Masquer' : 'Afficher'"></span> les informations complémentaires
 </button>
 </div>

 {{-- ACTIONS FOOTER --}}
 <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-6 mt-8">
 <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
 <div class="flex items-center space-x-3">
 <div class="w-2 h-2 rounded-full animate-pulse"
 :class="@js($validation_complete && !$has_conflicts) ? 'bg-green-500' : 'bg-gray-400'"></div>
 <span class="text-sm font-medium text-gray-600">
 @if($validation_complete && !$has_conflicts && $vehicle_id && $driver_id)
 Formulaire prêt à soumettre
 @else
 En attente de validation
 @endif
 </span>
 </div>

 <div class="flex space-x-4">
 <button type="button"
 wire:click="resetForm"
 class="btn-secondary group">
 <i class="fas fa-undo mr-2 group-hover:rotate-180 transition-transform duration-500"></i>
 Réinitialiser
 </button>

 <button type="submit"
 class="btn-primary group relative"
 wire:loading.attr="disabled"
 wire:target="create">
 <span wire:loading.remove wire:target="create">
 <i class="fas fa-rocket mr-2 group-hover:translate-x-1 transition-transform duration-300"></i>
 Créer l'Affectation
 </span>
 <span wire:loading wire:target="create">
 <i class="fas fa-spinner fa-spin mr-2"></i>
 Création en cours...
 </span>
 </button>
 </div>
 </div>

 {{-- Message d'erreur global --}}
 @error('creation')
 <div class="mt-4 p-3 bg-red-50 border border-red-300 rounded-lg">
 <p class="text-sm text-red-800">
 <i class="fas fa-exclamation-triangle mr-2"></i>
 {{ $message }}
 </p>
 </div>
 @enderror

 @error('conflicts')
 <div class="mt-4 p-3 bg-yellow-50 border border-yellow-300 rounded-lg">
 <p class="text-sm text-yellow-800">
 <i class="fas fa-info-circle mr-2"></i>
 {{ $message }}
 </p>
 </div>
 @enderror
 </div>
 </form>

 {{-- 🔄 INDICATEURS DE CHARGEMENT FLOTTANTS --}}
 <div wire:loading
 wire:target="checkConflicts,updatedVehicleId,updatedDriverId,updatedStartDate,updatedStartTime,updatedEndDate,updatedEndTime"
 class="fixed bottom-4 right-4 bg-blue-500 text-white px-4 py-3 rounded-lg shadow-2xl z-50 animate-bounce"
 role="status"
 aria-live="polite">
 <div class="flex items-center space-x-2">
 <i class="fas fa-spinner fa-spin"></i>
 <span class="font-medium">Vérification des conflits...</span>
 </div>
 </div>

 <div wire:loading
 wire:target="create"
 class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center"
 role="status"
 aria-live="assertive">
 <div class="bg-white rounded-xl p-8 shadow-2xl">
 <div class="text-center">
 <i class="fas fa-spinner fa-spin text-blue-600 text-4xl mb-4"></i>
 <p class="text-lg font-bold text-gray-900">Création de l'affectation...</p>
 <p class="text-sm text-gray-600 mt-2">Veuillez patienter</p>
 </div>
 </div>
 </div>
</div>

@push('styles')
<style>
/* Styles spécifiques au composant Livewire */
.assignment-type-card {
 cursor: pointer;
 transition: all 0.3s ease;
}

.assignment-type-card.selected-open {
 background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
 border-color: #22c55e;
 box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
}

.assignment-type-card.selected-scheduled {
 background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
 border-color: #a855f7;
 box-shadow: 0 4px 12px rgba(168, 85, 247, 0.2);
}

[x-cloak] {
 display: none !important;
}
</style>
@endpush
