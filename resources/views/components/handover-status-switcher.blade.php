{{-- /resources/views/components/handover-status-switcher.blade.php --}}
@props([
 'item', // Le nom de l'item à vérifier (ex: "Carte Grise")
 'category', // La catégorie de l'item (ex: "Papiers du véhicule")
 'statuses' => ['Bon', 'Moyen', 'Mauvais', 'N/A'], // Les options de statut possibles
 'selected' => null // Le statut actuellement sélectionné (pour l'édition)
])

@php
 // CLÉ DE LA CORRECTION :
 // On détermine l'état initial. Priorité à la prop 'selected' (valeur de la BDD),
 // sinon à l'ancien input en cas d'erreur de validation, sinon 'N/A' par défaut.
 $initialStatus = $selected ?? old('checklist.' . $category . '.' . Str::slug($item, '_'), 'N/A');

 // On transforme les noms d'item et de catégorie en slugs pour les attributs HTML
 $itemSlug = Str::slug($item, '_');
@endphp

{{-- 
 La valeur calculée en PHP est injectée dans x-data pour initialiser l'état d'Alpine.js.
 C'est le pont entre le backend (Laravel) et le frontend (JavaScript).
--}}
<div x-data="{ status: '{{ $initialStatus }}' }" class="flex items-center justify-between py-2">
 <label class="text-sm text-gray-800 font-medium" for="status-switcher-{{ $category }}-{{ $itemSlug }}">{{ $item }}</label>

 <div class="flex items-center space-x-1 p-0.5 bg-gray-200 rounded-full" id="status-switcher-{{ $category }}-{{ $itemSlug }}">
 
 {{-- Input caché qui enverra la valeur au serveur. Son nom est dynamique. --}}
 <input type="hidden" name="checklist[{{ $category }}][{{ $itemSlug }}]" x-model="status">

 {{-- Boucle sur les statuts pour générer les boutons --}}
 @foreach($statuses as $buttonStatus)
 <button 
 type="button" 
 @click="status = '{{ $buttonStatus }}'"
 :class="{
 'bg-green-500 text-white shadow-md': status === '{{ $buttonStatus }}' && ('{{ $buttonStatus }}' === 'Bon' || '{{ $buttonStatus }}' === 'Oui'),
 'bg-red-500 text-white shadow-md': status === '{{ $buttonStatus }}' && ('{{ $buttonStatus }}' === 'Mauvais' || '{{ $buttonStatus }}' === 'Non'),
 'bg-yellow-500 text-white shadow-md': status === '{{ $buttonStatus }}' && '{{ $buttonStatus }}' === 'Moyen',
 'bg-gray-400 text-white shadow-md': status === '{{ $buttonStatus }}' && '{{ $buttonStatus }}' === 'N/A',
 'bg-transparent text-gray-500 hover:bg-gray-300': status !== '{{ $buttonStatus }}'
 }"
 class="px-3 py-1 text-xs font-bold rounded-full transition-all duration-200 ease-in-out"
 aria-label="Définir le statut de {{ $item }} à {{ $buttonStatus }}"
 >
 {{ $buttonStatus }}
 </button>
 @endforeach
 </div>
</div>