{{-- 🔧 Modal de création de demande - ENTERPRISE GRADE --}}
<x-modal wire:model="showCreateModal" max-width="5xl">
 <div class="p-6 bg-gradient-to-br from-white to-gray-50">
 {{-- Header Ultra-Pro --}}
 <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-blue-100">
 <div class="flex items-center space-x-3">
 <div class="p-2 bg-blue-600 rounded-lg">
 <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
 </svg>
 </div>
 <div>
 <h3 class="text-xl font-bold text-gray-900">Nouvelle Demande de Réparation</h3>
 <p class="text-sm text-gray-500 mt-0.5">Créez une demande qui sera validée par votre superviseur</p>
 </div>
 </div>
 <button wire:click="closeCreateModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
 </svg>
 </button>
 </div>

 <form wire:submit.prevent="createRequest" class="space-y-6">
 {{-- 🚗 Informations Véhicule & Urgence --}}
 <div class="bg-white rounded-xl border-2 border-gray-200 p-5 space-y-4">
 <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center">
 <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
 </svg>
 Véhicule & Urgence
 </h4>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 {{-- Véhicule --}}
 <div>
 <label for="vehicle_id" class="block text-sm font-semibold text-gray-700 mb-2">
 🚗 Véhicule concerné <span class="text-red-500">*</span>
 </label>
 <select wire:model="vehicle_id" id="vehicle_id"
 class="block w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-sm">
 <option value="">-- Sélectionner un véhicule --</option>
 @foreach($vehicles as $vehicle)
 <option value="{{ $vehicle->id }}">
 {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
 </option>
 @endforeach
 </select>
 @error('vehicle_id')
 <p class="mt-1.5 text-sm text-red-600 flex items-center">
 <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 {{ $message }}
 </p>
 @enderror
 </div>

 {{-- Priorité --}}
 <div>
 <label for="priority" class="block text-sm font-semibold text-gray-700 mb-2">
 ⚡ Niveau d'urgence <span class="text-red-500">*</span>
 </label>
 <select wire:model="priority" id="priority"
 class="block w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-sm">
 <option value="non_urgente">🟢 Non urgente - Peut attendre</option>
 <option value="a_prevoir">🟡 À prévoir - Dans les prochains jours</option>
 <option value="urgente">🔴 Urgente - Intervention rapide</option>
 </select>
 @error('priority')
 <p class="mt-1.5 text-sm text-red-600 flex items-center">
 <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 {{ $message }}
 </p>
 @enderror
 </div>
 </div>
 </div>

 {{-- 📝 Description Détaillée --}}
 <div class="bg-white rounded-xl border-2 border-gray-200 p-5 space-y-4">
 <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center">
 <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
 </svg>
 Description du Problème
 </h4>
 <div>
 <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
 📋 Décrivez en détail le problème constaté <span class="text-red-500">*</span>
 </label>
 <textarea wire:model="description" id="description" rows="5"
 class="block w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-sm"
 placeholder="Exemples: Bruits anormaux au freinage, fumée noire à l'échappement, voyant moteur allumé...

Soyez le plus précis possible sur:
• Les symptômes observés
• Le moment où cela se produit
• La fréquence du problème"></textarea>
 <div class="mt-1 flex items-center justify-between text-xs">
 <span class="text-gray-500">Minimum 10 caractères requis</span>
 <span class="text-gray-400" x-data="{ count: $wire.entangle('description').length }">
 <span x-text="count || 0"></span>/2000
 </span>
 </div>
 @error('description')
 <p class="mt-1.5 text-sm text-red-600 flex items-center">
 <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 {{ $message }}
 </p>
 @enderror
 </div>
 </div>

 {{-- 📍 Informations Complémentaires --}}
 <div class="bg-white rounded-xl border-2 border-gray-200 p-5 space-y-4">
 <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center">
 <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 Informations Complémentaires
 </h4>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 {{-- Localisation --}}
 <div>
 <label for="location_description" class="block text-sm font-semibold text-gray-700 mb-2">
 📍 Localisation du véhicule
 </label>
 <input wire:model="location_description" type="text" id="location_description"
 class="block w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-sm"
 placeholder="Ex: Parking bureau, Garage Alger-Centre...">
 @error('location_description')
 <p class="mt-1.5 text-sm text-red-600 flex items-center">
 <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 {{ $message }}
 </p>
 @enderror
 </div>

 {{-- Coût estimé --}}
 <div>
 <label for="estimated_cost" class="block text-sm font-semibold text-gray-700 mb-2">
 💰 Coût estimé (DA) <span class="text-gray-400 text-xs font-normal">(optionnel)</span>
 </label>
 <div class="relative">
 <input wire:model="estimated_cost" type="number" step="0.01" min="0" id="estimated_cost"
 class="block w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-sm"
 placeholder="0.00">
 <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
 <span class="text-gray-400 text-sm font-medium">DA</span>
 </div>
 </div>
 @error('estimated_cost')
 <p class="mt-1.5 text-sm text-red-600 flex items-center">
 <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 {{ $message }}
 </p>
 @enderror
 </div>
 </div>
 </div>

 {{-- Photos --}}
 <div>
 <label class="block text-sm font-medium text-gray-700">Photos (max 5)</label>
 <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
 <div class="space-y-1 text-center">
 <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
 <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
 </svg>
 <div class="flex text-sm text-gray-600">
 <label for="photos" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
 <span>Télécharger des photos</span>
 <input wire:model="photos" id="photos" type="file" class="sr-only" multiple accept="image/*">
 </label>
 <p class="pl-1">ou glisser-déposer</p>
 </div>
 <p class="text-xs text-gray-500">PNG, JPG, GIF jusqu'à 5MB chacune</p>
 </div>
 </div>
 @error('photos.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

 @if($photos)
 <div class="mt-3 grid grid-cols-3 gap-2">
 @foreach($photos as $index => $photo)
 <div class="relative">
 <img src="{{ $photo->temporaryUrl() }}" class="w-full h-20 object-cover rounded">
 <button type="button" wire:click="removePhoto({{ $index }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
 </div>
 @endforeach
 </div>
 @endif
 </div>

 {{-- Pièces jointes --}}
 <div>
 <label class="block text-sm font-medium text-gray-700">Pièces jointes (max 3)</label>
 <input wire:model="attachments" type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.txt">
 @error('attachments.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
 </div>

 {{-- 🎯 Actions Finales --}}
 <div class="flex items-center justify-between pt-6 border-t-2 border-gray-200">
 <p class="text-sm text-gray-500 flex items-center">
 <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 Votre demande sera envoyée à votre superviseur
 </p>
 <div class="flex space-x-3">
 <button type="button" wire:click="closeCreateModal"
 class="px-6 py-2.5 text-sm font-semibold text-gray-700 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all">
 ✖ Annuler
 </button>
 <button type="submit"
 class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-lg hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl transition-all transform hover:scale-105"
 wire:loading.attr="disabled"
 wire:loading.class="opacity-75 cursor-not-allowed">
 <span wire:loading.remove wire:target="createRequest">
 ✓ Créer la demande
 </span>
 <span wire:loading wire:target="createRequest" class="flex items-center">
 <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 Création en cours...
 </span>
 </button>
 </div>
 </div>
 </form>
 </div>
</x-modal>

{{-- 🎯 Modal d'approbation/validation ENTERPRISE GRADE --}}
<x-modal wire:model="showApprovalModal" max-width="4xl">
 @if($selectedRequest)
 <div class="p-6 bg-gradient-to-br from-white to-gray-50">
 {{-- Header Ultra-Pro --}}
 <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-blue-100">
 <div class="flex items-center space-x-3">
 <div class="p-3 rounded-lg {{ $selectedRequest->status === 'pending_supervisor' ? 'bg-blue-600' : 'bg-purple-600' }}">
 <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 @if($selectedRequest->status === 'pending_supervisor')
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 @else
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
 @endif
 </svg>
 </div>
 <div>
 <h3 class="text-xl font-bold text-gray-900">
 @if($selectedRequest->status === 'pending_supervisor')
 Approbation Superviseur (Niveau 1)
 @elseif($selectedRequest->status === 'approved_supervisor')
 Validation Gestionnaire Flotte (Niveau 2)
 @else
 Traitement de la Demande
 @endif
 </h3>
 <p class="text-sm text-gray-500 mt-0.5">
 Demande #{{ $selectedRequest->id }} •
 @if($selectedRequest->status === 'pending_supervisor')
 Niveau 1 - Approbation initiale
 @else
 Niveau 2 - Validation finale
 @endif
 </p>
 </div>
 </div>
 <button wire:click="closeApprovalModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
 </svg>
 </button>
 </div>

 {{-- 📋 Détails de la Demande --}}
 <div class="bg-white rounded-xl border-2 border-gray-200 p-5 mb-6 space-y-4">
 <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center">
 <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
 </svg>
 Informations de la Demande
 </h4>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
 <div class="bg-gray-50 rounded-lg p-3">
 <p class="text-xs font-medium text-gray-500 uppercase mb-1">🚗 Véhicule</p>
 <p class="text-sm font-bold text-gray-900">{{ $selectedRequest->vehicle->registration_plate }}</p>
 <p class="text-xs text-gray-600">{{ $selectedRequest->vehicle->brand }} {{ $selectedRequest->vehicle->model }}</p>
 </div>
 <div class="bg-gray-50 rounded-lg p-3">
 <p class="text-xs font-medium text-gray-500 uppercase mb-1">⚡ Urgence</p>
 <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
 @if($selectedRequest->urgency === 'critical') bg-red-50 text-red-700 border border-red-200
 @elseif($selectedRequest->urgency === 'high') bg-orange-50 text-orange-700 border border-orange-200
 @elseif($selectedRequest->urgency === 'normal') bg-yellow-50 text-yellow-700 border border-yellow-200
 @else bg-gray-50 text-gray-700 border border-gray-200 @endif">
 @if($selectedRequest->urgency === 'critical') 🔴 CRITIQUE
 @elseif($selectedRequest->urgency === 'high') 🟠 HAUTE
 @elseif($selectedRequest->urgency === 'normal') 🟡 NORMALE
 @else 🟢 BASSE
 @endif
 </span>
 </div>
 <div class="bg-gray-50 rounded-lg p-3">
 <p class="text-xs font-medium text-gray-500 uppercase mb-1">👤 Demandeur</p>
 <p class="text-sm font-bold text-gray-900">{{ $selectedRequest->driver->name ?? 'N/A' }}</p>
 <p class="text-xs text-gray-600">{{ $selectedRequest->created_at->diffForHumans() }}</p>
 </div>
 </div>

 <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-600">
 <p class="text-xs font-medium text-blue-700 uppercase mb-2">📝 Description du Problème</p>
 <p class="text-sm text-gray-900 leading-relaxed">{{ $selectedRequest->description }}</p>
 </div>

 @if($selectedRequest->location_description || $selectedRequest->estimated_cost)
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 @if($selectedRequest->location_description)
 <div class="bg-gray-50 rounded-lg p-3">
 <p class="text-xs font-medium text-gray-500 uppercase mb-1">📍 Localisation</p>
 <p class="text-sm text-gray-900">{{ $selectedRequest->location_description }}</p>
 </div>
 @endif
 @if($selectedRequest->estimated_cost)
 <div class="bg-gray-50 rounded-lg p-3">
 <p class="text-xs font-medium text-gray-500 uppercase mb-1">💰 Coût Estimé</p>
 <p class="text-lg font-bold text-blue-900">{{ number_format($selectedRequest->estimated_cost, 2) }} DA</p>
 </div>
 @endif
 </div>
 @endif
 </div>

 {{-- 💬 Formulaire de Décision --}}
 <div class="bg-white rounded-xl border-2 border-gray-200 p-5 space-y-5">
 <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center">
 <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
 </svg>
 Votre Décision
 </h4>

 <div>
 <label for="approval_comments" class="block text-sm font-semibold text-gray-700 mb-2">
 💬 Commentaires & Justification
 @if($selectedRequest->status === 'pending_supervisor')
 <span class="text-gray-400 text-xs font-normal">(optionnels pour approbation, requis pour rejet)</span>
 @else
 <span class="text-gray-400 text-xs font-normal">(optionnels pour validation, requis pour rejet)</span>
 @endif
 </label>
 <textarea wire:model="approvalComments" id="approval_comments" rows="4"
 class="block w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-sm"
 placeholder="Ajoutez vos commentaires, remarques ou raisons de votre décision..."></textarea>
 @error('approvalComments')
 <p class="mt-1.5 text-sm text-red-600 flex items-center">
 <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 {{ $message }}
 </p>
 @enderror
 </div>

 {{-- Actions selon le niveau --}}
 <div class="flex items-center justify-between pt-4 border-t-2 border-gray-200">
 <p class="text-sm text-gray-500 flex items-center">
 <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 @if($selectedRequest->status === 'pending_supervisor')
 Cette décision passera au niveau 2 si approuvée
 @else
 Cette décision est FINALE et créera une opération maintenance
 @endif
 </p>
 <div class="flex space-x-3">
 <button type="button" wire:click="closeApprovalModal"
 class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all">
 ✖ Annuler
 </button>

 @if($selectedRequest->status === 'pending_supervisor')
 {{-- Actions Niveau 1 - Superviseur --}}
 <button wire:click="rejectRequest"
 class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-lg hover:from-red-700 hover:to-red-800 shadow-lg hover:shadow-xl transition-all transform hover:scale-105"
 wire:loading.attr="disabled"
 wire:loading.class="opacity-75">
 <span wire:loading.remove wire:target="rejectRequest">❌ Rejeter</span>
 <span wire:loading wire:target="rejectRequest" class="flex items-center">
 <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 Rejet...
 </span>
 </button>
 <button wire:click="approveRequest"
 class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-green-600 to-green-700 border border-transparent rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg hover:shadow-xl transition-all transform hover:scale-105"
 wire:loading.attr="disabled"
 wire:loading.class="opacity-75">
 <span wire:loading.remove wire:target="approveRequest">✓ Approuver</span>
 <span wire:loading wire:target="approveRequest" class="flex items-center">
 <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 Approbation...
 </span>
 </button>
 @elseif($selectedRequest->status === 'approved_supervisor')
 {{-- Actions Niveau 2 - Gestionnaire Flotte --}}
 <button wire:click="rejectByManager"
 class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-lg hover:from-red-700 hover:to-red-800 shadow-lg hover:shadow-xl transition-all transform hover:scale-105"
 wire:loading.attr="disabled"
 wire:loading.class="opacity-75">
 <span wire:loading.remove wire:target="rejectByManager">❌ Rejeter Définitivement</span>
 <span wire:loading wire:target="rejectByManager" class="flex items-center">
 <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 Rejet...
 </span>
 </button>
 <button wire:click="validateRequest"
 class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-lg hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl transition-all transform hover:scale-105"
 wire:loading.attr="disabled"
 wire:loading.class="opacity-75">
 <span wire:loading.remove wire:target="validateRequest">✓ Valider & Créer Opération</span>
 <span wire:loading wire:target="validateRequest" class="flex items-center">
 <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 Validation...
 </span>
 </button>
 @endif
 </div>
 </div>
 </div>
 </div>
 @endif
</x-modal>

{{-- Modal de détails --}}
<x-modal wire:model="showDetailsModal" max-width="4xl">
 @if($selectedRequest)
 <div class="p-6">
 <div class="flex items-center justify-between mb-4">
 <h3 class="text-lg font-medium text-gray-900">Détails de la demande #{{ $selectedRequest->id }}</h3>
 <button wire:click="closeDetailsModal" class="text-gray-400 hover:text-gray-600">
 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
 </svg>
 </button>
 </div>

 <div class="space-y-6">
 {{-- En-tête avec statut --}}
 <div class="bg-gray-50 rounded-lg p-4">
 <div class="flex items-center justify-between">
 <div>
 <h4 class="text-lg font-medium text-gray-900">{{ $selectedRequest->vehicle->registration_plate }}</h4>
 <p class="text-sm text-gray-600">Demandé par {{ $selectedRequest->requester->name }} le {{ $selectedRequest->requested_at->format('d/m/Y à H:i') }}</p>
 </div>
 <div class="text-right">
 <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $selectedRequest->status_color }}-100 text-{{ $selectedRequest->status_color }}-800">
 {{ $selectedRequest->status_label }}
 </span>
 <p class="text-sm text-gray-600 mt-1">
 <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
 @if($selectedRequest->priority === 'urgente') bg-red-50 text-red-700 border border-red-200
 @elseif($selectedRequest->priority === 'a_prevoir') bg-orange-50 text-orange-700 border border-orange-200
 @else bg-gray-50 text-gray-700 border border-gray-200 @endif">
 {{ $selectedRequest->priority_label }}
 </span>
 </p>
 </div>
 </div>
 </div>

 {{-- Description et détails --}}
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
 <div class="space-y-4">
 <div>
 <h5 class="text-sm font-medium text-gray-700 mb-2">Description</h5>
 <p class="text-sm text-gray-900 bg-white p-3 rounded border">{{ $selectedRequest->description }}</p>
 </div>

 @if($selectedRequest->location_description)
 <div>
 <h5 class="text-sm font-medium text-gray-700 mb-2">Localisation</h5>
 <p class="text-sm text-gray-900">{{ $selectedRequest->location_description }}</p>
 </div>
 @endif

 <div class="grid grid-cols-2 gap-4">
 @if($selectedRequest->estimated_cost)
 <div>
 <h5 class="text-sm font-medium text-gray-700">Coût estimé</h5>
 <p class="text-sm text-gray-900">{{ number_format($selectedRequest->estimated_cost, 2) }} DA</p>
 </div>
 @endif

 @if($selectedRequest->actual_cost)
 <div>
 <h5 class="text-sm font-medium text-gray-700">Coût réel</h5>
 <p class="text-sm text-gray-900">{{ number_format($selectedRequest->actual_cost, 2) }} DA</p>
 </div>
 @endif
 </div>
 </div>

 <div class="space-y-4">
 {{-- Timeline du workflow --}}
 <div>
 <h5 class="text-sm font-medium text-gray-700 mb-3">Historique des actions</h5>
 <div class="flow-root">
 <ul class="-mb-8">
 <li class="relative pb-8">
 <div class="relative flex space-x-3">
 <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500">
 <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
 </svg>
 </div>
 <div class="min-w-0 flex-1">
 <div>
 <p class="text-sm text-gray-900">Demande créée</p>
 <p class="text-xs text-gray-500">{{ $selectedRequest->requested_at->format('d/m/Y à H:i') }}</p>
 </div>
 </div>
 </div>
 </li>

 @if($selectedRequest->supervisor_decided_at)
 <li class="relative pb-8">
 <div class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></div>
 <div class="relative flex space-x-3">
 <div class="flex h-8 w-8 items-center justify-center rounded-full bg-{{ $selectedRequest->supervisor_decision === 'accepte' ? 'green' : 'red' }}-500">
 @if($selectedRequest->supervisor_decision === 'accepte')
 <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
 </svg>
 @else
 <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
 </svg>
 @endif
 </div>
 <div class="min-w-0 flex-1">
 <div>
 <p class="text-sm text-gray-900">
 {{ $selectedRequest->supervisor_decision === 'accepte' ? 'Approuvée' : 'Rejetée' }} par {{ $selectedRequest->supervisor->name }}
 </p>
 @if($selectedRequest->supervisor_comments)
 <p class="text-sm text-gray-600 mt-1">{{ $selectedRequest->supervisor_comments }}</p>
 @endif
 <p class="text-xs text-gray-500">{{ $selectedRequest->supervisor_decided_at->format('d/m/Y à H:i') }}</p>
 </div>
 </div>
 </div>
 </li>
 @endif

 @if($selectedRequest->manager_decided_at)
 <li class="relative pb-8">
 <div class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></div>
 <div class="relative flex space-x-3">
 <div class="flex h-8 w-8 items-center justify-center rounded-full bg-{{ $selectedRequest->manager_decision === 'valide' ? 'blue' : 'red' }}-500">
 @if($selectedRequest->manager_decision === 'valide')
 <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
 </svg>
 @else
 <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
 </svg>
 @endif
 </div>
 <div class="min-w-0 flex-1">
 <div>
 <p class="text-sm text-gray-900">
 {{ $selectedRequest->manager_decision === 'valide' ? 'Validée' : 'Rejetée' }} par {{ $selectedRequest->manager->name }}
 </p>
 @if($selectedRequest->manager_comments)
 <p class="text-sm text-gray-600 mt-1">{{ $selectedRequest->manager_comments }}</p>
 @endif
 <p class="text-xs text-gray-500">{{ $selectedRequest->manager_decided_at->format('d/m/Y à H:i') }}</p>
 </div>
 </div>
 </div>
 </li>
 @endif

 @if($selectedRequest->work_started_at)
 <li class="relative pb-8">
 <div class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></div>
 <div class="relative flex space-x-3">
 <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-500">
 <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H15"></path>
 </svg>
 </div>
 <div class="min-w-0 flex-1">
 <div>
 <p class="text-sm text-gray-900">Travaux démarrés</p>
 @if($selectedRequest->assignedSupplier)
 <p class="text-sm text-gray-600">{{ $selectedRequest->assignedSupplier->company_name }}</p>
 @endif
 <p class="text-xs text-gray-500">{{ $selectedRequest->work_started_at->format('d/m/Y à H:i') }}</p>
 </div>
 </div>
 </div>
 </li>
 @endif

 @if($selectedRequest->work_completed_at)
 <li class="relative">
 <div class="relative flex space-x-3">
 <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500">
 <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 </div>
 <div class="min-w-0 flex-1">
 <div>
 <p class="text-sm text-gray-900">Travaux terminés</p>
 @if($selectedRequest->completion_notes)
 <p class="text-sm text-gray-600 mt-1">{{ $selectedRequest->completion_notes }}</p>
 @endif
 @if($selectedRequest->final_rating)
 <div class="flex items-center mt-2">
 <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
 <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
 </svg>
 <span class="text-sm text-gray-600 ml-1">{{ $selectedRequest->final_rating }}/10</span>
 </div>
 @endif
 <p class="text-xs text-gray-500">{{ $selectedRequest->work_completed_at->format('d/m/Y à H:i') }}</p>
 </div>
 </div>
 </div>
 </li>
 @endif
 </ul>
 </div>
 </div>
 </div>
 </div>

 {{-- Photos --}}
 @if($selectedRequest->photos && count($selectedRequest->photos) > 0)
 <div>
 <h5 class="text-sm font-medium text-gray-700 mb-3">Photos de la panne</h5>
 <div class="grid grid-cols-3 gap-2">
 @foreach($selectedRequest->photos as $photo)
 <img src="{{ Storage::url($photo) }}" alt="Photo" class="w-full h-20 object-cover rounded border">
 @endforeach
 </div>
 </div>
 @endif

 {{-- Photos de travaux --}}
 @if($selectedRequest->work_photos && count($selectedRequest->work_photos) > 0)
 <div>
 <h5 class="text-sm font-medium text-gray-700 mb-3">Photos des travaux</h5>
 <div class="grid grid-cols-3 gap-2">
 @foreach($selectedRequest->work_photos as $photo)
 <img src="{{ Storage::url($photo) }}" alt="Photo travaux" class="w-full h-20 object-cover rounded border">
 @endforeach
 </div>
 </div>
 @endif

 {{-- Pièces jointes --}}
 @if($selectedRequest->attachments && count($selectedRequest->attachments) > 0)
 <div>
 <h5 class="text-sm font-medium text-gray-700 mb-3">Pièces jointes</h5>
 <div class="space-y-2">
 @foreach($selectedRequest->attachments as $attachment)
 <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="flex items-center p-2 border rounded hover:bg-gray-50">
 <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
 </svg>
 <span class="text-sm text-gray-900">{{ $attachment['name'] }}</span>
 <span class="text-xs text-gray-500 ml-2">({{ number_format($attachment['size'] / 1024, 0) }} KB)</span>
 </a>
 @endforeach
 </div>
 </div>
 @endif
 </div>

 <div class="flex justify-end mt-6">
 <button wire:click="closeDetailsModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
 Fermer
 </button>
 </div>
 </div>
 @endif
</x-modal>

{{-- Modal d'assignation de fournisseur --}}
<x-modal wire:model="showAssignSupplierModal" max-width="2xl">
 @if($selectedRequest)
 <div class="p-6">
 <div class="flex items-center justify-between mb-4">
 <h3 class="text-lg font-medium text-gray-900">Assigner un fournisseur</h3>
 <button wire:click="closeAssignSupplierModal" class="text-gray-400 hover:text-gray-600">
 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
 </svg>
 </button>
 </div>

 <div class="space-y-4">
 <div class="bg-gray-50 rounded-lg p-4">
 <h4 class="font-medium text-gray-900">{{ $selectedRequest->vehicle->registration_plate }}</h4>
 <p class="text-sm text-gray-600 mt-1">{{ Str::limit($selectedRequest->description, 150) }}</p>
 </div>

 <div>
 <label for="supplier_select" class="block text-sm font-medium text-gray-700">Sélectionner un fournisseur</label>
 <select wire:model="selectedSupplierId" id="supplier_select" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
 <option value="">Choisir un fournisseur...</option>
 @foreach($suppliers as $supplier)
 <option value="{{ $supplier->id }}">
 {{ $supplier->company_name }} - {{ $supplier->supplier_type }}
 @if($supplier->rating) (Note: {{ $supplier->rating }}/10) @endif
 </option>
 @endforeach
 </select>
 @error('selectedSupplierId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
 </div>

 <div class="flex justify-end space-x-3 pt-4">
 <button type="button" wire:click="closeAssignSupplierModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
 Annuler
 </button>
 <button wire:click="assignSupplier" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
 Assigner
 </button>
 </div>
 </div>
 </div>
 @endif
</x-modal>

{{-- Modal de complétion des travaux --}}
<x-modal wire:model="showCompleteWorkModal" max-width="3xl">
 @if($selectedRequest)
 <div class="p-6">
 <div class="flex items-center justify-between mb-4">
 <h3 class="text-lg font-medium text-gray-900">Terminer les travaux</h3>
 <button wire:click="closeCompleteWorkModal" class="text-gray-400 hover:text-gray-600">
 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
 </svg>
 </button>
 </div>

 <form wire:submit.prevent="completeWork" class="space-y-6">
 <div class="bg-gray-50 rounded-lg p-4">
 <h4 class="font-medium text-gray-900">{{ $selectedRequest->vehicle->registration_plate }}</h4>
 <p class="text-sm text-gray-600 mt-1">{{ Str::limit($selectedRequest->description, 150) }}</p>
 @if($selectedRequest->assignedSupplier)
 <p class="text-sm text-blue-600 mt-1">Fournisseur: {{ $selectedRequest->assignedSupplier->company_name }}</p>
 @endif
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <label for="actual_cost" class="block text-sm font-medium text-gray-700">Coût réel (DA) *</label>
 <input wire:model="actualCost" type="number" step="0.01" min="0" id="actual_cost" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
 @error('actualCost') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
 </div>

 <div>
 <label for="final_rating" class="block text-sm font-medium text-gray-700">Note de satisfaction (1-10)</label>
 <input wire:model="finalRating" type="number" min="1" max="10" step="0.1" id="final_rating" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
 @error('finalRating') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
 </div>
 </div>

 <div>
 <label for="completion_notes" class="block text-sm font-medium text-gray-700">Notes de complétion</label>
 <textarea wire:model="completionNotes" id="completion_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Détails des travaux effectués, observations, etc."></textarea>
 @error('completionNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
 </div>

 <div>
 <label class="block text-sm font-medium text-gray-700">Photos des travaux terminés</label>
 <input wire:model="workPhotos" type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" multiple accept="image/*">
 @error('workPhotos.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
 </div>

 <div class="flex justify-end space-x-3 pt-4">
 <button type="button" wire:click="closeCompleteWorkModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
 Annuler
 </button>
 <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700">
 Terminer les travaux
 </button>
 </div>
 </form>
 </div>
 @endif
</x-modal>