@props([
 'name' => '',
 'label' => null,
 'error' => null,
 'helpText' => null,
 'required' => false,
 'disabled' => false,
 'options' => [],
 'selected' => null,
 'placeholder' => 'Rechercher...',
 'multiple' => false,
 'clearable' => true,
])

@php
 $component = new \App\View\Components\TomSelect($name, $label, $error, $helpText, $required, $disabled, $options, $selected, $placeholder, $multiple, $clearable);
 $selectId = $component->getId();
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
 @if($label)
 <label for="{{ $selectId }}" class="block mb-2 text-sm font-medium text-gray-900">
 {{ $label }}
 @if($required)
 <span class="text-red-500">*</span>
 @endif
 </label>
 @endif

 <select
 name="{{ $name }}{{ $multiple ? '[]' : '' }}"
 id="{{ $selectId }}"
 class="tomselect bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
 @if($required) required @endif
 @if($disabled) disabled @endif
 @if($multiple) multiple @endif
 {{ $attributes->except(['class']) }}
 >
 {{-- ✨ ENTERPRISE FIX: Slot pour options custom OU options depuis prop --}}
 @if($slot->isNotEmpty())
 {{-- Options passées via le slot (méthode recommandée) --}}
 {{ $slot }}
 @else
 {{-- Options passées via la prop $options (méthode alternative) --}}
 @if(!$multiple)
 <option value="">{{ $placeholder ?: '-- Sélectionner --' }}</option>
 @endif

 @foreach($options as $value => $optionLabel)
 <option
 value="{{ $value }}"
 {{ (is_array($selected) ? in_array($value, $selected) : old($name, $selected) == $value) ? 'selected' : '' }}
 >
 {{ $optionLabel }}
 </option>
 @endforeach
 @endif
 </select>

 @if($error)
 <p class="mt-2 text-sm text-red-600 flex items-start">
 <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" />
 <span>{{ $error }}</span>
 </p>
 @elseif($helpText)
 <p class="mt-2 text-sm text-gray-500">
 {{ $helpText }}
 </p>
 @endif

 {{-- ⚠️ Erreur dynamique Alpine.js - SEULEMENT si champ touché ET invalide --}}
 <p x-show="fieldErrors && fieldErrors['{{ $name }}'] && touchedFields && touchedFields['{{ $name }}']"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform -translate-y-1"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-2 text-sm text-red-600 flex items-start"
 style="display: none;">
 <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" />
 <span>Ce champ est obligatoire</span>
 </p>
</div>

@once
 @push('scripts')
 <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
 <script>
 // ✅ OPTIMISATION ENTERPRISE: Fonction d'initialisation Tom Select réutilisable
 function initializeTomSelect(element) {
 if (element.tomSelectInstance) {
 element.tomSelectInstance.destroy();
 }
 
 const tomSelectInstance = new TomSelect(element, {
 plugins: ['clear_button', 'remove_button'],
 maxOptions: 100,
 placeholder: element.getAttribute('data-placeholder') || 'Rechercher...',
 allowEmptyOption: true,
 create: false,
 sortField: {
 field: "text",
 direction: "asc"
 },
 render: {
 no_results: function(data, escape) {
 return '<div class="no-results p-2 text-sm text-gray-500">Aucun résultat trouvé</div>';
 }
 },
 // ✅ INTÉGRATION LIVEWIRE ENTERPRISE-GRADE
 onInitialize: function() {
 const self = this;
 
 // Stocker l'instance pour référence future
 element.tomSelectInstance = self;
 
 // Hook Livewire pour synchronisation après mise à jour DOM
 if (typeof Livewire !== 'undefined') {
 Livewire.hook('element.updated', (el, component) => {
 if (el === element || el.contains(element)) {
 // Synchroniser Tom Select avec les nouvelles options
 self.sync();
 
 // Préserver la valeur sélectionnée
 const wireModel = element.getAttribute('wire:model.live') || 
 element.getAttribute('wire:model');
 if (wireModel && component.get(wireModel)) {
 self.setValue(component.get(wireModel), true);
 }
 }
 });
 
 // Hook pour nettoyer l'instance avant destruction
 Livewire.hook('element.removed', (el, component) => {
 if (el === element || el.contains(element)) {
 self.destroy();
 }
 });
 }
 },
 // ✅ OPTIMISATION: Événements pour synchronisation bidirectionnelle
 onChange: function(value) {
 // Dispatch event pour Alpine.js et Livewire
 element.dispatchEvent(new Event('change', { bubbles: true }));
 
 // Force Livewire update si wire:model est présent
 const wireModel = element.getAttribute('wire:model.live') || 
 element.getAttribute('wire:model');
 if (wireModel && typeof Livewire !== 'undefined') {
 const component = Livewire.find(element.closest('[wire\\:id]').getAttribute('wire:id'));
 if (component) {
 component.set(wireModel, value);
 }
 }
 }
 });
 
 return tomSelectInstance;
 }
 
 // ✅ INITIALISATION AU CHARGEMENT
 document.addEventListener('DOMContentLoaded', function() {
 document.querySelectorAll('.tomselect').forEach(function(el) {
 initializeTomSelect(el);
 });
 });
 
 // ✅ RÉINITIALISATION APRÈS NAVIGATION LIVEWIRE
 document.addEventListener('livewire:navigated', function() {
 document.querySelectorAll('.tomselect').forEach(function(el) {
 if (!el.tomSelectInstance) {
 initializeTomSelect(el);
 }
 });
 });
 
 // ✅ SUPPORT POUR COMPOSANTS DYNAMIQUES ALPINE.JS
 document.addEventListener('alpine:init', function() {
 Alpine.magic('tomselect', (el) => {
 return () => {
 const selectEl = el.querySelector('.tomselect');
 if (selectEl && !selectEl.tomSelectInstance) {
 return initializeTomSelect(selectEl);
 }
 return selectEl?.tomSelectInstance;
 };
 });
 });
 </script>
 @endpush
@endonce
