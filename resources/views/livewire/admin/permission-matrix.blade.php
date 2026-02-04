<div class="min-h-screen bg-gray-50 ">
 <div class="px-4 sm:px-6 lg:px-8 py-8">

 {{-- 🎯 EN-TÊTE AVEC CONTRÔLES --}}
 <div class="mb-8">
 <div class="flex items-center justify-between">
 <div>
 <h1 class="text-4xl font-bold text-gray-900 flex items-center">
 <svg class="w-10 h-10 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
 </svg>
 Matrice des Permissions
 </h1>
 <p class="mt-2 text-sm text-gray-600 ">
 Configuration avancée des rôles et permissions - Architecture enterprise-grade
 </p>
 </div>

 <div class="flex items-center space-x-3">
 {{-- Bouton Historique --}}
 <button wire:click="$toggle('showHistory')"
 class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 :bg-gray-700">
 <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
 </svg>
 Historique
 </button>

 {{-- Bouton Preview --}}
 <button wire:click="$toggle('showPreview')"
 class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 :bg-gray-700">
 <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
 </svg>
 Aperçu
 </button>
 </div>
 </div>
 </div>

 {{-- 🎛️ PANNEAU DE CONTRÔLE --}}
 <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
 <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

 {{-- Sélecteur de Rôle --}}
 <div class="lg:col-span-1">
 <label class="block text-sm font-medium text-gray-700 mb-2">
 🎭 Rôle à configurer
 </label>
 <select wire:model.live="selectedRoleId"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 ">
 <option value="">-- Sélectionner un rôle --</option>
 @foreach($availableRoles as $role)
 <option value="{{ $role->id }}">
 {{ $role->name }} • {{ $role->organization_id ? 'Org #' . $role->organization_id : 'Global' }} ({{ $role->permissions_count }} permissions)
 </option>
 @endforeach
 </select>

 @if($selectedRole)
 <div class="mt-4 p-3 bg-blue-50 /20 rounded-md">
 <div class="flex items-center">
 <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
 </svg>
 <div>
 <p class="text-sm font-semibold text-blue-900 ">
 {{ count($rolePermissions) }} / {{ count($permissionsMatrix) }}
 </p>
 <p class="text-xs text-blue-700 ">
 permissions assignées
 </p>
 </div>
 </div>
 </div>
 @endif
 </div>

 @if(auth()->user()->hasRole('Super Admin'))
 {{-- Contexte Organisation --}}
 <div class="lg:col-span-1">
 <label class="block text-sm font-medium text-gray-700 mb-2">
 🧭 Contexte des rôles
 </label>
 <select wire:model.live="organizationContext"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 ">
 <option value="organization">Organisation</option>
 <option value="global">Rôles globaux</option>
 <option value="all">Toutes les organisations</option>
 </select>
 <p class="mt-2 text-xs text-gray-500">
 Utilisez “Organisation” pour éviter les doublons par nom.
 </p>
 </div>

 @if($organizationContext === 'organization')
 <div class="lg:col-span-1">
 <label class="block text-sm font-medium text-gray-700 mb-2">
 🏢 Organisation
 </label>
 <select wire:model.live="selectedOrganizationId"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 ">
 @foreach($availableOrganizations as $org)
 <option value="{{ $org->id }}">
 {{ $org->name }}{{ $org->legal_name ? ' · ' . $org->legal_name : '' }}
 </option>
 @endforeach
 </select>
 </div>
 @endif
 @endif

 {{-- Barre de Recherche --}}
 <div class="lg:col-span-1">
 <label class="block text-sm font-medium text-gray-700 mb-2">
 🔍 Recherche
 </label>
 <div class="relative">
 <input type="text"
 wire:model.live.debounce.500ms="search"
 placeholder="Rechercher une permission..."
 wire:loading.attr="aria-busy"
 wire:target="search"
 class="pl-10 pr-4 py-2.5 block w-full bg-white border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
 <div wire:loading.delay wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
 <x-iconify icon="lucide:loader-2" class="w-4 h-4 text-blue-500 animate-spin" />
 </div>
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
 </svg>
 </div>
 </div>
 </div>

 {{-- Filtre par Ressource --}}
 <div class="lg:col-span-1">
 <label class="block text-sm font-medium text-gray-700 mb-2">
 📦 Filtrer par ressource
 </label>
 <select wire:model.live="filterByResource"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 ">
 <option value="">Toutes les ressources</option>
 @foreach($resources as $resource)
 <option value="{{ $resource }}">{{ $this->formatResourceName($resource) }}</option>
 @endforeach
 </select>
 </div>

 {{-- Filtre par Action --}}
 <div class="lg:col-span-1">
 <label class="block text-sm font-medium text-gray-700 mb-2">
 ⚡ Filtrer par action
 </label>
 <select wire:model.live="filterByAction"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 ">
 <option value="">Toutes les actions</option>
 @foreach($actions as $action)
 <option value="{{ $action }}">{{ $this->formatActionName($action) }}</option>
 @endforeach
 </select>
 </div>

 </div>

 {{-- Options supplémentaires --}}
 @if($organizationContext === 'all')
 <div class="mt-4 mb-2 bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-800">
 Mode “Toutes les organisations” : des rôles portant le même nom peuvent apparaître plusieurs fois.
 </div>
 @endif
 <div class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4">
 <div class="flex items-center space-x-4">
 <label class="inline-flex items-center cursor-pointer">
 <input type="checkbox" wire:model.live="showOnlyAssigned" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 ">
 <span class="ml-2 text-sm text-gray-700 ">
 Afficher uniquement les permissions assignées
 </span>
 </label>
 </div>

 @if($hasPendingChanges)
 <div class="flex items-center text-sm">
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 /30 ">
 <svg class="w-4 h-4 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 {{ count($pendingChanges) }} modification(s) en attente
 </span>
 </div>
 @endif
 </div>
 </div>

 {{-- 📊 MATRICE DES PERMISSIONS --}}
 @if(!$selectedRole)
 <div class="bg-white rounded-lg shadow-lg p-12 text-center">
 <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
 </svg>
 <h3 class="text-xl font-medium text-gray-900 mb-2">
 Veuillez sélectionner un rôle
 </h3>
 <p class="text-gray-600 ">
 Sélectionnez un rôle dans le panneau de contrôle ci-dessus pour configurer ses permissions
 </p>
 </div>
 @else
 {{-- Matrice par ressource --}}
 <div class="space-y-6">
 @forelse($groupedPermissions as $resource => $permissions)
 <div class="bg-white rounded-lg shadow-lg overflow-hidden"
 x-data="{ expanded: true }">

 {{-- En-tête de la ressource --}}
 <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4">
 <div class="flex items-center justify-between">
 <div class="flex items-center space-x-3">
 <button @click="expanded = !expanded"
 class="text-white hover:text-blue-100 transition-transform"
 :class="{ 'rotate-180': !expanded }">
 <svg class="w-6 h-6 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
 </svg>
 </button>

 <h3 class="text-xl font-bold text-white flex items-center">
 {{ $this->formatResourceName($resource) }}
 <span class="ml-3 px-3 py-1 bg-white/20 rounded-full text-sm">
 {{ $permissions->whereIn('id', $rolePermissions)->count() }} / {{ $permissions->count() }}
 </span>
 </h3>
 </div>

 <div class="flex items-center space-x-2">
 <button wire:click="selectAllForResource('{{ $resource }}')"
 class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-md text-sm font-medium transition-colors">
 ✅ Tout sélectionner
 </button>
 <button wire:click="deselectAllForResource('{{ $resource }}')"
 class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-md text-sm font-medium transition-colors">
 ❌ Tout désélectionner
 </button>
 </div>
 </div>
 </div>

 {{-- Grille des permissions --}}
 <div x-show="expanded"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform scale-95"
 x-transition:enter-end="opacity-100 transform scale-100"
 class="p-6">

 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
 @foreach($permissions as $permission)
 @php
 $isAssigned = in_array($permission['id'], $rolePermissions);
 @endphp

 <div wire:key="permission-{{ $permission['id'] }}"
 class="relative group">
 <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all
 {{ $isAssigned
 ? 'border-green-500 bg-green-50 /20'
 : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-blue-50 :bg-blue-900/10' }}">

 <input type="checkbox"
 wire:click="togglePermission({{ $permission['id'] }})"
 {{ $isAssigned ? 'checked' : '' }}
 class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">

 <div class="ml-3 flex-1">
 <p class="text-sm font-semibold {{ $isAssigned ? 'text-green-900 ' : 'text-gray-900 ' }}">
 {{ $permission['display_action'] }}
 </p>
 <p class="text-xs text-gray-500 mt-0.5">
 {{ $permission['name'] }}
 </p>
 </div>

 @if($isAssigned)
 <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
 </svg>
 @endif
 </label>
 </div>
 @endforeach
 </div>
 </div>
 </div>
 @empty
 <div class="bg-white rounded-lg shadow-lg p-12 text-center">
 <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
 </svg>
 <h3 class="text-xl font-medium text-gray-900 mb-2">
 Aucune permission trouvée
 </h3>
 <p class="text-gray-600 ">
 Essayez d'ajuster vos filtres de recherche
 </p>
 </div>
 @endforelse
 </div>

 {{-- 💾 BARRE D'ACTIONS FLOTTANTE --}}
 @if($hasPendingChanges)
 <div class="fixed bottom-8 left-1/2 transform -translate-x-1/2 z-50"
 x-data="{ show: true }"
 x-show="show"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform translate-y-4"
 x-transition:enter-end="opacity-100 transform translate-y-0">

 <div class="bg-gradient-to-r from-blue-600 to-blue-500 rounded-lg shadow-2xl p-6 flex items-center space-x-6">
 <div class="flex items-center text-white">
 <svg class="w-8 h-8 mr-3 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
 </svg>
 <div>
 <p class="font-bold text-lg">{{ count($pendingChanges) }} modification(s) en attente</p>
 <p class="text-sm text-blue-100">Cliquez sur Enregistrer pour appliquer les changements</p>
 </div>
 </div>

 <div class="flex items-center space-x-3">
 <button wire:click="cancel"
 class="px-6 py-3 bg-white/20 hover:bg-white/30 text-white rounded-lg font-semibold transition-colors">
 ❌ Annuler
 </button>
 <button wire:click="save"
 class="px-8 py-3 bg-white text-blue-600 hover:bg-blue-50 rounded-lg font-bold shadow-lg transition-all transform hover:scale-105">
 ✅ Enregistrer
 </button>
 </div>
 </div>
 </div>
 @endif

 @if(auth()->user()->hasRole('Super Admin') && $selectedRole)
 <div class="fixed bottom-8 right-8 z-40">
 <button wire:click="confirmApplyToAllOrganizations"
 class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold shadow-lg transition-all">
 📌 Appliquer à toutes les organisations
 </button>
 </div>
 @endif
 @endif

 {{-- 🔐 MODAL CONFIRMATION APPLICATION GLOBALE --}}
 @if($showApplyAllModal && $selectedRole)
 <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center z-50"
 x-data="{ show: true }"
 x-show="show"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0"
 x-transition:enter-end="opacity-100"
 @click.self="$wire.set('showApplyAllModal', false)">

 <div class="bg-white rounded-xl shadow-2xl max-w-xl w-full mx-4 overflow-hidden"
 @click.stop>
 <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 py-4">
 <h3 class="text-xl font-bold text-white">Appliquer les permissions à toutes les organisations</h3>
 </div>

 <div class="p-6 space-y-4">
 <p class="text-sm text-gray-700">
 Vous êtes sur le point de propager les permissions du rôle
 <span class="font-semibold text-gray-900">“{{ $selectedRole->name }}”</span>
 à {{ $applyAllTargetCount }} organisation(s).
 </p>
 <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
 Cette action synchronise les permissions du rôle pour chaque organisation.
 Les permissions manquantes seront créées automatiquement si nécessaire.
 </div>
 </div>

 <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
 <button wire:click="$set('showApplyAllModal', false)"
 class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100">
 Annuler
 </button>
 <button wire:click="applyPermissionsToAllOrganizations"
 class="px-6 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700">
 Confirmer l’application
 </button>
 </div>
 </div>
 </div>
 @endif

 {{-- 📊 MODAL APERÇU --}}
 @if($showPreview && $selectedRole)
 <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center z-50"
 x-data="{ show: true }"
 x-show="show"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0"
 x-transition:enter-end="opacity-100"
 @click.self="$wire.set('showPreview', false)">

 <div class="bg-white rounded-lg shadow-2xl max-w-4xl w-full mx-4 max-h-[80vh] overflow-hidden"
 @click.stop>
 <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4">
 <div class="flex items-center justify-between">
 <h3 class="text-2xl font-bold text-white">
 Aperçu des Permissions - {{ $selectedRole->name }}
 </h3>
 <button wire:click="$set('showPreview', false)"
 class="text-white hover:text-blue-100">
 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
 </svg>
 </button>
 </div>
 </div>

 <div class="p-6 overflow-y-auto max-h-[calc(80vh-80px)]">
 <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
 @foreach($permissionsMatrix as $perm)
 @if(in_array($perm['id'], $rolePermissions))
 <div class="flex items-center p-3 bg-green-50 /20 rounded-lg border border-green-200 ">
 <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
 </svg>
 <div class="flex-1">
 <p class="text-xs font-semibold text-green-900 ">
 {{ $perm['display_resource'] }}
 </p>
 <p class="text-xs text-green-700 ">
 {{ $perm['display_action'] }}
 </p>
 </div>
 </div>
 @endif
 @endforeach
 </div>

 <div class="mt-6 p-4 bg-blue-50 /20 rounded-lg">
 <p class="text-sm text-blue-900 ">
 <strong>Total:</strong> {{ count($rolePermissions) }} permission(s) assignée(s)
 </p>
 </div>
 </div>
 </div>
 </div>
 @endif

 </div>

 {{-- 🔔 Notifications Toast --}}
 <div x-data="{ notifications: [] }"
 @notification.window="
 const notification = $event.detail;
 notifications.push({ ...notification, id: Date.now() });
 setTimeout(() => notifications.shift(), 5000);
 "
 class="fixed top-4 right-4 z-50 space-y-2">
 <template x-for="notification in notifications" :key="notification.id">
 <div x-show="true"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform translate-x-8"
 x-transition:enter-end="opacity-100 transform translate-x-0"
 x-transition:leave="transition ease-in duration-200"
 x-transition:leave-start="opacity-100"
 x-transition:leave-end="opacity-0"
 class="bg-white rounded-lg shadow-lg p-4 max-w-sm"
 :class="{
 'border-l-4 border-green-500': notification.type === 'success',
 'border-l-4 border-blue-500': notification.type === 'info',
 'border-l-4 border-yellow-500': notification.type === 'warning',
 'border-l-4 border-red-500': notification.type === 'error'
 }">
 <p class="text-sm font-medium text-gray-900 " x-text="notification.message"></p>
 </div>
 </template>
 </div>
</div>
