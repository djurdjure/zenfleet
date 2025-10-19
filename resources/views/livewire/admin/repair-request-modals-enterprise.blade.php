{{-- 🚀 MODAL CRÉATION DEMANDE RÉPARATION - ULTRA ENTERPRISE GRADE PRO --}}
<div x-data="{
 showModal: @entangle('showCreateModal'),
 currentStep: 1,
 totalSteps: 4,
 descriptionLength: 0,
 canProceed(step) {
 switch(step) {
 case 1: return $wire.vehicle_id && $wire.priority;
 case 2: return $wire.description && $wire.description.length >= 10;
 case 3: return true;
 case 4: return true;
 default: return false;
 }
 },
 nextStep() {
 if (this.currentStep < this.totalSteps && this.canProceed(this.currentStep)) {
 this.currentStep++;
 }
 },
 prevStep() {
 if (this.currentStep > 1) {
 this.currentStep--;
 }
 },
 getProgressPercentage() {
 return (this.currentStep / this.totalSteps) * 100;
 }
}">
 {{-- Modal Overlay with Blur Effect --}}
 <div x-show="showModal"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0"
 x-transition:enter-end="opacity-100"
 x-transition:leave="transition ease-in duration-200"
 x-transition:leave-start="opacity-100"
 x-transition:leave-end="opacity-0"
 class="fixed inset-0 z-50 overflow-y-auto"
 style="display: none;">

 {{-- Backdrop with Glassmorphism --}}
 <div class="fixed inset-0 bg-gradient-to-br from-gray-900/70 via-gray-900/80 to-blue-900/70 backdrop-blur-md"
 @click="$wire.closeCreateModal()"></div>

 {{-- Modal Container --}}
 <div class="flex min-h-full items-center justify-center p-4">
 <div x-show="showModal"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
 x-transition:leave="transition ease-in duration-200"
 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
 @click.stop
 class="relative w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden">

 {{-- Header avec gradient Premium --}}
 <div class="relative bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 px-8 py-6 shadow-lg">
 {{-- Decorative Elements --}}
 <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
 <div class="absolute bottom-0 left-0 w-48 h-48 bg-indigo-500/20 rounded-full -ml-24 -mb-24 blur-2xl"></div>

 <div class="relative flex items-center justify-between">
 <div class="flex items-center space-x-4">
 <div class="relative">
 <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center transform hover:scale-110 transition-all duration-300">
 <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
 </svg>
 </div>
 <div class="absolute -top-1 -right-1 w-5 h-5 bg-green-400 rounded-full border-2 border-white animate-pulse"></div>
 </div>
 <div>
 <h2 class="text-2xl font-black text-white tracking-tight">Nouvelle Demande de Réparation</h2>
 <p class="text-blue-100 text-sm mt-1 font-medium">
 <span class="inline-flex items-center">
 <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
 </svg>
 Workflow de validation à 2 niveaux • Étape <span x-text="currentStep" class="font-bold mx-1"></span> sur <span x-text="totalSteps" class="font-bold"></span>
 </span>
 </p>
 </div>
 </div>

 <button @click="$wire.closeCreateModal()"
 class="relative p-2.5 text-white/80 hover:text-white hover:bg-white/20 rounded-xl transition-all duration-200 transform hover:scale-110 group">
 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
 </svg>
 <span class="sr-only">Fermer</span>
 </button>
 </div>

 {{-- Progress Bar Ultra Premium --}}
 <div class="relative mt-6">
 <div class="flex items-center justify-between mb-2">
 <span class="text-xs font-bold text-blue-100 uppercase tracking-widest">Progression</span>
 <span class="text-xs font-bold text-white" x-text="Math.round(getProgressPercentage()) + '%'"></span>
 </div>
 <div class="h-2 bg-white/20 rounded-full overflow-hidden backdrop-blur-sm">
 <div class="h-full bg-gradient-to-r from-green-400 via-green-500 to-emerald-500 rounded-full transition-all duration-500 ease-out shadow-lg shadow-green-500/50"
 :style="`width: ${getProgressPercentage()}%`"></div>
 </div>

 {{-- Step Indicators --}}
 <div class="flex justify-between mt-4">
 <template x-for="step in totalSteps" :key="step">
 <div class="flex flex-col items-center flex-1">
 <div class="relative flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300 transform"
 :class="{
 'bg-gradient-to-br from-green-400 to-green-600 shadow-lg shadow-green-500/50 scale-110': step < currentStep,
 'bg-gradient-to-br from-blue-400 to-blue-600 shadow-xl shadow-blue-500/50 scale-125 ring-4 ring-white/30': step === currentStep,
 'bg-white/20 backdrop-blur-sm': step > currentStep
 }">
 <svg x-show="step < currentStep" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
 </svg>
 <span x-show="step >= currentStep" class="text-sm font-bold"
 :class="step === currentStep ? 'text-white' : 'text-white/60'"
 x-text="step"></span>
 </div>
 <span class="text-xs mt-2 font-medium text-center"
 :class="step <= currentStep ? 'text-white' : 'text-blue-200/60'"
 x-text="step === 1 ? 'Véhicule' : step === 2 ? 'Description' : step === 3 ? 'Détails' : 'Fichiers'"></span>
 </div>
 </template>
 </div>
 </div>
 </div>

 {{-- Form Content --}}
 <form wire:submit.prevent="createRequest" class="relative">
 <div class="px-8 py-6 max-h-[60vh] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">

 {{-- ÉTAPE 1: Véhicule & Urgence --}}
 <div x-show="currentStep === 1"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 translate-x-4"
 x-transition:enter-end="opacity-100 translate-x-0"
 class="space-y-6">

 <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border-2 border-blue-200 p-6 shadow-inner">
 <div class="flex items-center space-x-3 mb-5">
 <div class="p-2 bg-blue-600 rounded-xl">
 <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
 </svg>
 </div>
 <div>
 <h3 class="text-lg font-bold text-gray-900">Sélection du Véhicule & Urgence</h3>
 <p class="text-sm text-gray-600">Choisissez le véhicule concerné et le niveau d'urgence de l'intervention</p>
 </div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
 {{-- Véhicule --}}
 <div class="space-y-2">
 <label for="vehicle_id" class="block text-sm font-bold text-gray-700 flex items-center">
 <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
 </svg>
 Véhicule concerné <span class="text-red-500 ml-1">*</span>
 </label>
 <div class="relative">
 <select wire:model.live="vehicle_id" id="vehicle_id"
 class="block w-full px-4 py-3 pr-10 rounded-xl border-2 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium bg-white shadow-sm hover:border-blue-400"
 :class="$wire.vehicle_id ? 'border-green-300 bg-green-50/50' : ''">
 <option value="">-- Sélectionner un véhicule --</option>
 @foreach($vehicles as $vehicle)
 <option value="{{ $vehicle->id }}">
 🚗 {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
 </option>
 @endforeach
 </select>
 <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
 <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
 </svg>
 </div>
 </div>
 @error('vehicle_id')
 <p class="text-sm text-red-600 flex items-center mt-1.5 animate-shake">
 <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 {{ $message }}
 </p>
 @enderror
 </div>

 {{-- Priorité --}}
 <div class="space-y-2">
 <label for="priority" class="block text-sm font-bold text-gray-700 flex items-center">
 <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
 </svg>
 Niveau d'urgence <span class="text-red-500 ml-1">*</span>
 </label>
 <div class="relative">
 <select wire:model.live="priority" id="priority"
 class="block w-full px-4 py-3 pr-10 rounded-xl border-2 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all text-sm font-bold bg-white shadow-sm hover:border-blue-400"
 :class="{
 'border-green-300 bg-green-50/50': $wire.priority === 'non_urgente',
 'border-yellow-300 bg-yellow-50/50': $wire.priority === 'a_prevoir',
 'border-red-300 bg-red-50/50': $wire.priority === 'urgente'
 }">
 <option value="non_urgente" class="text-green-700">🟢 Non urgente - Peut attendre</option>
 <option value="a_prevoir" class="text-yellow-700">🟡 À prévoir - Prochains jours</option>
 <option value="urgente" class="text-red-700">🔴 Urgente - Intervention immédiate</option>
 </select>
 <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
 <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
 </svg>
 </div>
 </div>
 @error('priority')
 <p class="text-sm text-red-600 flex items-center mt-1.5 animate-shake">
 <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 {{ $message }}
 </p>
 @enderror
 </div>
 </div>

 {{-- Info Helper --}}
 <div class="mt-5 p-4 bg-blue-100 border-l-4 border-blue-600 rounded-lg">
 <div class="flex">
 <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
 </svg>
 <div class="text-sm text-blue-800">
 <p class="font-semibold mb-1">💡 Conseil</p>
 <p>Les demandes <span class="font-bold text-red-600">URGENTES</span> sont traitées en priorité et notifient immédiatement votre superviseur.</p>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- ÉTAPE 2: Description --}}
 <div x-show="currentStep === 2"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 translate-x-4"
 x-transition:enter-end="opacity-100 translate-x-0"
 class="space-y-6">

 <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl border-2 border-purple-200 p-6 shadow-inner">
 <div class="flex items-center space-x-3 mb-5">
 <div class="p-2 bg-purple-600 rounded-xl">
 <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
 </svg>
 </div>
 <div>
 <h3 class="text-lg font-bold text-gray-900">Description Détaillée du Problème</h3>
 <p class="text-sm text-gray-600">Décrivez avec précision le problème constaté pour faciliter le diagnostic</p>
 </div>
 </div>

 <div class="space-y-4">
 <div>
 <label for="description" class="block text-sm font-bold text-gray-700 flex items-center mb-2">
 <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
 </svg>
 Description du problème <span class="text-red-500 ml-1">*</span>
 </label>
 <div class="relative">
 <textarea wire:model.live="description" id="description" rows="7"
 x-on:input="descriptionLength = $event.target.value.length"
 class="block w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all text-sm leading-relaxed resize-none shadow-sm hover:border-purple-400"
 :class="descriptionLength >= 10 ? 'border-green-300 bg-green-50/30' : 'bg-white'"
 placeholder="Décrivez en détail le problème constaté...

📋 Exemples concrets:
• Bruits anormaux au freinage (grincement métallique)
• Fumée noire à l'échappement lors des accélérations
• Voyant moteur allumé depuis 2 jours
• Fuite d'huile sous le capot côté droit
• Climatisation ne fonctionne plus

🎯 Informations utiles:
• Quand le problème apparaît-il?
• À quelle fréquence?
• Y a-t-il des symptômes associés?"></textarea>

 {{-- Character Counter Premium --}}
 <div class="absolute bottom-3 right-3 flex items-center space-x-2">
 <div class="px-3 py-1.5 rounded-full text-xs font-bold shadow-lg backdrop-blur-sm"
 :class="{
 'bg-red-100 text-red-700': descriptionLength < 10,
 'bg-yellow-100 text-yellow-700': descriptionLength >= 10 && descriptionLength < 100,
 'bg-green-100 text-green-700': descriptionLength >= 100
 }">
 <span x-text="descriptionLength"></span> / 2000
 </div>
 </div>
 </div>

 {{-- Real-time Validation --}}
 <div class="mt-2 flex items-center space-x-2">
 <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
 <div class="h-full transition-all duration-300"
 :class="{
 'bg-red-500 w-0': descriptionLength < 10,
 'bg-yellow-500': descriptionLength >= 10 && descriptionLength < 100,
 'bg-green-500': descriptionLength >= 100
 }"
 :style="`width: ${Math.min((descriptionLength / 200) * 100, 100)}%`"></div>
 </div>
 <div class="text-xs font-semibold"
 :class="{
 'text-red-600': descriptionLength < 10,
 'text-yellow-600': descriptionLength >= 10 && descriptionLength < 100,
 'text-green-600': descriptionLength >= 100
 }">
 <span x-show="descriptionLength < 10">Minimum requis</span>
 <span x-show="descriptionLength >= 10 && descriptionLength < 100">Bien</span>
 <span x-show="descriptionLength >= 100">Excellent!</span>
 </div>
 </div>

 @error('description')
 <p class="text-sm text-red-600 flex items-center mt-2 animate-shake">
 <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 {{ $message }}
 </p>
 @enderror
 </div>
 </div>

 {{-- Success Indicator --}}
 <div x-show="descriptionLength >= 100"
 x-transition
 class="mt-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-lg">
 <div class="flex items-center">
 <svg class="w-6 h-6 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
 </svg>
 <div>
 <p class="text-sm font-bold text-green-800">Description excellente! ✨</p>
 <p class="text-xs text-green-700 mt-0.5">Votre description est suffisamment détaillée pour un diagnostic précis.</p>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- ÉTAPE 3: Détails Complémentaires --}}
 <div x-show="currentStep === 3"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 translate-x-4"
 x-transition:enter-end="opacity-100 translate-x-0"
 class="space-y-6">

 <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl border-2 border-orange-200 p-6 shadow-inner">
 <div class="flex items-center space-x-3 mb-5">
 <div class="p-2 bg-orange-600 rounded-xl">
 <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
 </svg>
 </div>
 <div>
 <h3 class="text-lg font-bold text-gray-900">Informations Complémentaires</h3>
 <p class="text-sm text-gray-600">Ajoutez des détails supplémentaires pour accélérer le traitement</p>
 </div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
 {{-- Localisation --}}
 <div class="space-y-2">
 <label for="location_description" class="block text-sm font-bold text-gray-700 flex items-center">
 <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
 </svg>
 Localisation du véhicule
 <span class="ml-2 text-xs font-normal text-gray-500">(optionnel)</span>
 </label>
 <div class="relative">
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
 </svg>
 </div>
 <input wire:model="location_description" type="text" id="location_description"
 class="block w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-300 focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all text-sm bg-white shadow-sm hover:border-orange-400"
 placeholder="Ex: Parking bureau Alger, Garage Hydra, Dépôt Oran...">
 </div>
 @error('location_description')
 <p class="text-sm text-red-600 flex items-center mt-1.5">
 <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 {{ $message }}
 </p>
 @enderror
 </div>

 {{-- Coût Estimé --}}
 <div class="space-y-2">
 <label for="estimated_cost" class="block text-sm font-bold text-gray-700 flex items-center">
 <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
 </svg>
 Coût estimé
 <span class="ml-2 text-xs font-normal text-gray-500">(optionnel)</span>
 </label>
 <div class="relative">
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <span class="text-gray-500 font-bold">DA</span>
 </div>
 <input wire:model.blur="estimated_cost" type="number" step="100" min="0" id="estimated_cost"
 class="block w-full pl-14 pr-12 py-3 rounded-xl border-2 border-gray-300 focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all text-sm font-semibold bg-white shadow-sm hover:border-green-400"
 placeholder="0.00">
 <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
 <span class="text-gray-400 text-xs font-medium">Dinars</span>
 </div>
 </div>
 @error('estimated_cost')
 <p class="text-sm text-red-600 flex items-center mt-1.5">
 <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 {{ $message }}
 </p>
 @enderror
 <p class="text-xs text-gray-500 italic mt-1.5">
 💡 Si vous connaissez une estimation du coût, cela aidera à la planification budgétaire.
 </p>
 </div>
 </div>

 {{-- Info Box --}}
 <div class="mt-5 p-4 bg-gradient-to-r from-amber-100 to-orange-100 border-l-4 border-amber-500 rounded-lg">
 <div class="flex">
 <svg class="w-5 h-5 text-amber-700 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
 </svg>
 <div class="text-sm text-amber-900">
 <p class="font-semibold mb-1">📍 Informations pratiques</p>
 <ul class="space-y-1 text-xs">
 <li>• La <span class="font-bold">localisation</span> permet à l'équipe maintenance de planifier l'intervention</li>
 <li>• Le <span class="font-bold">coût estimé</span> aide à prioriser et budgétiser les réparations</li>
 </ul>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- ÉTAPE 4: Photos & Documents --}}
 <div x-show="currentStep === 4"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 translate-x-4"
 x-transition:enter-end="opacity-100 translate-x-0"
 class="space-y-6">

 <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-2xl border-2 border-teal-200 p-6 shadow-inner">
 <div class="flex items-center space-x-3 mb-5">
 <div class="p-2 bg-teal-600 rounded-xl">
 <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
 </svg>
 </div>
 <div>
 <h3 class="text-lg font-bold text-gray-900">Photos & Documents</h3>
 <p class="text-sm text-gray-600">Ajoutez des photos et documents pour illustrer le problème (optionnel)</p>
 </div>
 </div>

 <div class="space-y-6">
 {{-- Photos Upload Zone --}}
 <div>
 <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
 <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
 </svg>
 Photos du problème <span class="text-gray-500 font-normal text-xs ml-2">(max 5 photos, 5MB chacune)</span>
 </label>
 <div class="relative">
 <label for="photos" class="flex flex-col items-center justify-center w-full h-40 px-6 border-3 border-dashed border-teal-300 rounded-2xl cursor-pointer bg-teal-50/50 hover:bg-teal-100/70 transition-all duration-300 hover:border-teal-400 group">
 <div class="flex flex-col items-center justify-center space-y-3">
 <svg class="w-12 h-12 text-teal-400 group-hover:text-teal-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 48 48">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"/>
 </svg>
 <div class="text-center">
 <p class="text-sm font-semibold text-teal-700 group-hover:text-teal-900">
 <span class="underline decoration-wavy decoration-teal-400">Cliquez pour télécharger</span> ou glissez-déposez
 </p>
 <p class="text-xs text-teal-600 mt-1">PNG, JPG, GIF jusqu'à 5MB</p>
 </div>
 </div>
 <input wire:model="photos" id="photos" type="file" class="hidden" multiple accept="image/*">
 </label>
 </div>
 @error('photos.*')
 <p class="mt-2 text-sm text-red-600 flex items-center">
 <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 {{ $message }}
 </p>
 @enderror

 {{-- Photo Previews --}}
 @if($photos)
 <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
 @foreach($photos as $index => $photo)
 <div class="relative group">
 <img src="{{ $photo->temporaryUrl() }}" class="w-full h-28 object-cover rounded-xl border-2 border-teal-200 shadow-md group-hover:shadow-xl transition-all">
 <button type="button" wire:click="removePhoto({{ $index }})"
 class="absolute -top-2 -right-2 w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-full shadow-lg hover:scale-110 transition-transform flex items-center justify-center font-bold">
 ✕
 </button>
 </div>
 @endforeach
 </div>
 @endif
 </div>

 {{-- Attachments Upload --}}
 <div>
 <label for="attachments" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
 <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
 </svg>
 Pièces jointes <span class="text-gray-500 font-normal text-xs ml-2">(max 3 fichiers, 10MB chacun)</span>
 </label>
 <input wire:model="attachments" id="attachments" type="file" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.txt"
 class="block w-full px-4 py-3 text-sm border-2 border-teal-300 rounded-xl cursor-pointer bg-teal-50/50 hover:bg-teal-100/70 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-teal-600 file:text-white hover:file:bg-teal-700 file:cursor-pointer file:transition-all">
 @error('attachments.*')
 <p class="mt-2 text-sm text-red-600 flex items-center">
 <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 {{ $message }}
 </p>
 @enderror
 <p class="mt-2 text-xs text-gray-500 italic">
 📎 Formats acceptés: PDF, Word, Excel, TXT
 </p>
 </div>
 </div>

 {{-- Info Box --}}
 <div class="mt-5 p-4 bg-gradient-to-r from-cyan-100 to-teal-100 border-l-4 border-cyan-500 rounded-lg">
 <div class="flex">
 <svg class="w-5 h-5 text-cyan-700 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
 </svg>
 <div class="text-sm text-cyan-900">
 <p class="font-semibold mb-1">📸 Conseils pour de meilleures photos</p>
 <ul class="space-y-1 text-xs">
 <li>• Prenez des photos <span class="font-bold">nettes et bien éclairées</span></li>
 <li>• Montrez le problème sous <span class="font-bold">différents angles</span></li>
 <li>• Incluez le <span class="font-bold">contexte environnant</span> si pertinent</li>
 </ul>
 </div>
 </div>
 </div>
 </div>
 </div>

 </div>

 {{-- Footer avec Actions Premium --}}
 <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-gray-100 border-t-2 border-gray-200">
 <div class="flex items-center justify-between">
 {{-- Info Progress --}}
 <div class="flex items-center space-x-3">
 <div class="p-2 bg-blue-100 rounded-lg">
 <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
 </svg>
 </div>
 <div class="text-sm">
 <p class="font-semibold text-gray-700">
 <span x-show="currentStep === 4">Prêt à soumettre!</span>
 <span x-show="currentStep < 4">Étape <span x-text="currentStep"></span> sur 4</span>
 </p>
 <p class="text-xs text-gray-500">Votre demande sera envoyée à votre superviseur</p>
 </div>
 </div>

 {{-- Navigation Buttons --}}
 <div class="flex items-center space-x-3">
 {{-- Bouton Retour --}}
 <button type="button"
 x-show="currentStep > 1"
 @click="prevStep()"
 class="inline-flex items-center px-5 py-3 text-sm font-bold text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm hover:shadow-md transform hover:scale-105">
 <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
 </svg>
 Précédent
 </button>

 {{-- Bouton Annuler --}}
 <button type="button"
 @click="$wire.closeCreateModal()"
 class="inline-flex items-center px-5 py-3 text-sm font-bold text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm hover:shadow-md">
 <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
 </svg>
 Annuler
 </button>

 {{-- Bouton Suivant --}}
 <button type="button"
 x-show="currentStep < totalSteps"
 @click="nextStep()"
 :disabled="!canProceed(currentStep)"
 :class="canProceed(currentStep)
 ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-lg hover:shadow-xl transform hover:scale-105'
 : 'bg-gray-300 cursor-not-allowed opacity-60'"
 class="inline-flex items-center px-6 py-3 text-sm font-bold text-white rounded-xl transition-all">
 Suivant
 <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
 </svg>
 </button>

 {{-- Bouton Soumettre --}}
 <button type="submit"
 x-show="currentStep === totalSteps"
 wire:loading.attr="disabled"
 wire:loading.class="opacity-75 cursor-not-allowed"
 class="inline-flex items-center px-8 py-3 text-sm font-bold text-white bg-gradient-to-r from-green-600 via-green-700 to-emerald-700 rounded-xl hover:from-green-700 hover:to-emerald-800 shadow-xl hover:shadow-2xl transition-all transform hover:scale-105 relative overflow-hidden group">
 <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
 <span wire:loading.remove wire:target="createRequest" class="flex items-center">
 <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
 </svg>
 Créer la demande
 </span>
 <span wire:loading wire:target="createRequest" class="flex items-center">
 <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 Création en cours...
 </span>
 </button>
 </div>
 </div>
 </div>
 </form>
 </div>
 </div>
 </div>
</div>

{{-- Inclure les autres modals (approbation, détails, etc.) depuis le fichier original --}}
@include('livewire.admin.repair-request-modals')
