<div class="px-4 sm:px-6 lg:px-8 py-8">
 {{-- Header --}}
 <div class="mb-8">
 <div class="flex items-center justify-between">
 <div>
 <h1 class="text-3xl font-bold text-gray-900">
 Nouvelle Demande de Réparation
 </h1>
 <p class="mt-2 text-sm text-gray-600">
 Remplissez le formulaire pour créer une nouvelle demande de réparation
 </p>
 </div>
 <a href="{{ route('admin.repair-requests.index') }}"
 class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
 <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
 </svg>
 Retour à la liste
 </a>
 </div>
 </div>

 {{-- Loader --}}
 <div wire:loading class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
 <div class="bg-white p-4 rounded-lg shadow-xl flex items-center space-x-3">
 <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 <span class="text-gray-700 font-medium">Chargement...</span>
 </div>
 </div>

 {{-- Formulaire --}}
 <form wire:submit.prevent="submit" class="space-y-6">
 {{-- Card principale --}}
 <div class="bg-white rounded-xl shadow-sm overflow-hidden">
 {{-- Section Véhicule et Chauffeur --}}
 <div class="p-6 border-b border-gray-200">
 <h2 class="text-lg font-semibold text-gray-900 mb-4">
 Informations du véhicule et du chauffeur
 </h2>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Véhicule --}}
 <div>
 <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-2">
 Véhicule <span class="text-red-500">*</span>
 </label>
 <select wire:model.live="vehicle_id" id="vehicle_id" required
 class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
 <option value="">Sélectionner un véhicule</option>
 @forelse($vehicles as $vehicle)
 <option value="{{ $vehicle['id'] }}">
 {{ $vehicle['registration_plate'] }} - {{ $vehicle['brand'] ?? '' }} {{ $vehicle['model'] ?? '' }}
 </option>
 @empty
 <option value="" disabled>Aucun véhicule disponible</option>
 @endforelse
 </select>
 @error('vehicle_id')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 @if(empty($vehicles))
 <p class="mt-1 text-xs text-orange-600">⚠️ Aucun véhicule trouvé dans votre organisation</p>
 @else
 <p class="mt-1 text-xs text-gray-500">{{ count($vehicles) }} véhicule(s) disponible(s)</p>
 @endif
 </div>

 {{-- Chauffeur --}}
 <div>
 <label for="driver_id" class="block text-sm font-medium text-gray-700 mb-2">
 Chauffeur <span class="text-red-500">*</span>
 </label>
 <select wire:model.live="driver_id" id="driver_id" required
 class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
 <option value="">Sélectionner un chauffeur</option>
 @forelse($drivers as $driver)
 <option value="{{ $driver['id'] }}">
 {{ $driver['name'] }} - {{ $driver['license_number'] }}
 </option>
 @empty
 <option value="" disabled>Aucun chauffeur disponible</option>
 @endforelse
 </select>
 @error('driver_id')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 @if(empty($drivers))
 <p class="mt-1 text-xs text-orange-600">⚠️ Aucun chauffeur trouvé dans votre organisation</p>
 @else
 <p class="mt-1 text-xs text-gray-500">{{ count($drivers) }} chauffeur(s) disponible(s)</p>
 @endif
 </div>
 </div>
 </div>

 {{-- Section Description --}}
 <div class="p-6 border-b border-gray-200">
 <h2 class="text-lg font-semibold text-gray-900 mb-4">
 Description de la demande
 </h2>

 <div class="space-y-6">
 {{-- Titre --}}
 <div>
 <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
 Titre de la demande <span class="text-red-500">*</span>
 </label>
 <input type="text" wire:model="title" id="title" required
 class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
 placeholder="Ex: Problème de freinage avant droit">
 @error('title')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 {{-- Description --}}
 <div>
 <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
 Description détaillée <span class="text-red-500">*</span>
 </label>
 <textarea wire:model="description" id="description" rows="4" required
 class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
 placeholder="Décrivez en détail le problème rencontré..."></textarea>
 @error('description')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Catégorie --}}
 <div>
 <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
 Catégorie
 </label>
 <select wire:model="category_id" id="category_id"
 class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
 <option value="">Sélectionner une catégorie</option>
 @foreach($categories as $category)
 <option value="{{ $category['id'] }}">
 {{ $category['name'] }}
 </option>
 @endforeach
 </select>
 @error('category_id')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 {{-- Urgence --}}
 <div>
 <label for="urgency" class="block text-sm font-medium text-gray-700 mb-2">
 Niveau d'urgence <span class="text-red-500">*</span>
 </label>
 <select wire:model="urgency" id="urgency" required
 class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
 <option value="low">🟢 Faible - Peut attendre</option>
 <option value="normal">🔵 Normal - À traiter dans les délais habituels</option>
 <option value="high">🟠 Élevé - À traiter rapidement</option>
 <option value="critical">🔴 Critique - Véhicule immobilisé</option>
 </select>
 @error('urgency')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>
 </div>
 </div>

 {{-- Section Informations complémentaires --}}
 <div class="p-6 border-b border-gray-200">
 <h2 class="text-lg font-semibold text-gray-900 mb-4">
 Informations complémentaires
 </h2>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Kilométrage actuel --}}
 <div>
 <label for="current_mileage" class="block text-sm font-medium text-gray-700 mb-2">
 Kilométrage actuel
 </label>
 <input type="number" wire:model="current_mileage" id="current_mileage"
 class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
 placeholder="Ex: 45000">
 @error('current_mileage')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 <p class="mt-1 text-xs text-gray-500">
 Le kilométrage sera chargé automatiquement lors de la sélection du véhicule
 </p>
 </div>

 {{-- Coût estimé --}}
 <div>
 <label for="estimated_cost" class="block text-sm font-medium text-gray-700 mb-2">
 Coût estimé (DZD)
 </label>
 <input type="number" wire:model="estimated_cost" id="estimated_cost"
 step="0.01" min="0"
 class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
 placeholder="Ex: 15000">
 @error('estimated_cost')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>
 </div>

 {{-- Section Pièces jointes --}}
 <div class="p-6">
 <h2 class="text-lg font-semibold text-gray-900 mb-4">
 Pièces jointes
 </h2>

 <div class="space-y-4">
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Photos ou documents
 </label>
 <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
 <div class="space-y-1 text-center">
 <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
 <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
 </svg>
 <div class="flex text-sm text-gray-600">
 <label for="attachments" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
 <span>Télécharger des fichiers</span>
 <input id="attachments" wire:model="attachments" type="file" class="sr-only" multiple accept="image/*,.pdf,.doc,.docx">
 </label>
 <p class="pl-1">ou glisser-déposer</p>
 </div>
 <p class="text-xs text-gray-500">
 PNG, JPG, PDF jusqu'à 10MB
 </p>
 </div>
 </div>
 @error('attachments.*')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>
 </div>
 </div>

 {{-- Actions --}}
 <div class="flex justify-end space-x-4">
 <a href="{{ route('admin.repair-requests.index') }}"
 class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
 Annuler
 </a>
 <button type="submit"
 class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
 <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
 </svg>
 Créer la demande
 </button>
 </div>
 </form>
</div>
