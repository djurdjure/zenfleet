<div>
 {{-- 🎭 MODAL BACKDROP & CONTAINER --}}
 <div
 x-data="{ open: @entangle('open') }"
 x-show="open"
 x-cloak
 @keydown.escape.window="open = false"
 class="fixed inset-0 z-50 overflow-y-auto"
 style="display: none;"
 >
 {{-- Backdrop --}}
 <div
 x-show="open"
 x-transition:enter="ease-out duration-300"
 x-transition:enter-start="opacity-0"
 x-transition:enter-end="opacity-100"
 x-transition:leave="ease-in duration-200"
 x-transition:leave-start="opacity-100"
 x-transition:leave-end="opacity-0"
 class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40"
 @click="open = false"
 ></div>

 {{-- Modal Container --}}
 <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
 <div
 x-show="open"
 x-transition:enter="ease-out duration-300"
 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
 x-transition:leave="ease-in duration-200"
 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg z-50"
 >
 <form wire:submit="submit">
 {{-- Modal Header --}}
 <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
 <div class="sm:flex sm:items-start">
 {{-- Icon --}}
 <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 {{ $action === 'approve' ? 'bg-green-100' : 'bg-red-100' }}">
 @if($action === 'approve')
 <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 @else
 <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 @endif
 </div>

 {{-- Content --}}
 <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
 <h3 class="text-lg font-medium leading-6 text-gray-900 ">
 @if($action === 'approve')
 Approuver la demande
 @else
 Rejeter la demande
 @endif
 @if($level === 'supervisor')
 (Superviseur)
 @else
 (Gestionnaire de flotte)
 @endif
 </h3>

 @if($repairRequest)
 <div class="mt-4 space-y-3">
 {{-- Informations demande --}}
 <div class="bg-gray-50 rounded-lg p-4 space-y-2">
 <div class="flex justify-between text-sm">
 <span class="font-medium text-gray-700 ">Véhicule:</span>
 <span class="text-gray-900 ">
 {{ $repairRequest->vehicle->vehicle_name ?? $repairRequest->vehicle->license_plate }}
 </span>
 </div>
 <div class="flex justify-between text-sm">
 <span class="font-medium text-gray-700 ">Chauffeur:</span>
 <span class="text-gray-900 ">
 {{ $repairRequest->driver->user->name ?? 'N/A' }}
 </span>
 </div>
 <div class="flex justify-between text-sm">
 <span class="font-medium text-gray-700 ">Demande:</span>
 <span class="text-gray-900 ">
 {{ Str::limit($repairRequest->title, 40) }}
 </span>
 </div>
 <div class="flex justify-between text-sm">
 <span class="font-medium text-gray-700 ">Urgence:</span>
 <span>
 @php
 $urgencyConfig = [
 'low' => ['bg' => 'bg-green-100 text-green-800', 'label' => 'Faible'],
 'normal' => ['bg' => 'bg-blue-100 text-blue-800', 'label' => 'Normal'],
 'high' => ['bg' => 'bg-orange-100 text-orange-800', 'label' => 'Élevé'],
 'critical' => ['bg' => 'bg-red-100 text-red-800', 'label' => 'Critique'],
 ];
 $config = $urgencyConfig[$repairRequest->urgency] ?? $urgencyConfig['normal'];
 @endphp
 <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $config['bg'] }}">
 {{ $config['label'] }}
 </span>
 </span>
 </div>
 </div>

 {{-- Formulaire conditionnel --}}
 @if($action === 'approve')
 {{-- Commentaire optionnel pour approbation --}}
 <div>
 <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
 Commentaire (optionnel)
 </label>
 <textarea
 id="comment"
 wire:model="comment"
 rows="3"
 class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 "
 placeholder="Ajouter un commentaire sur l'approbation..."
 ></textarea>
 @error('comment')
 <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
 @enderror
 </div>
 @else
 {{-- Raison obligatoire pour rejet --}}
 <div>
 <label for="rejectionReason" class="block text-sm font-medium text-gray-700 mb-2">
 Raison du rejet <span class="text-red-500">*</span>
 </label>
 <textarea
 id="rejectionReason"
 wire:model="rejectionReason"
 rows="4"
 required
 class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 "
 placeholder="Expliquer pourquoi cette demande est rejetée (minimum 10 caractères)..."
 ></textarea>
 @error('rejectionReason')
 <p class="mt-1 text-sm text-red-600 ">{{ $message }}</p>
 @enderror
 <p class="mt-1 text-xs text-gray-500 ">
 Minimum 10 caractères requis
 </p>
 </div>
 @endif
 </div>
 @endif
 </div>
 </div>
 </div>

 {{-- Modal Footer --}}
 <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
 @if($action === 'approve')
 {{-- Bouton Approuver --}}
 <button
 type="submit"
 class="inline-flex w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
 >
 <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 Approuver
 </button>
 @else
 {{-- Bouton Rejeter --}}
 <button
 type="submit"
 class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
 >
 <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 Rejeter
 </button>
 @endif

 {{-- Bouton Annuler --}}
 <button
 type="button"
 wire:click="closeModal"
 class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm :bg-gray-600"
 >
 Annuler
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
</div>
