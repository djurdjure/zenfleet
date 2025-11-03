@props([
 'name' => '',
 'label' => null,
 'error' => null,
 'helpText' => null,
 'required' => false,
 'disabled' => false,
 'value' => null,
 'placeholder' => 'Sélectionner une heure',
 'enableSeconds' => false,
])

@php
 $component = new \App\View\Components\TimePicker($name, $label, $error, $helpText, $required, $disabled, $value, $placeholder, $enableSeconds);
 $inputId = $component->getId();
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
 @if($label)
 <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium text-gray-900 ">
 {{ $label }}
 @if($required)
 <span class="text-red-500">*</span>
 @endif
 </label>
 @endif

 <div class="relative">
 <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
 <x-iconify icon="heroicons:clock" class="w-4 h-4 text-gray-500 " />
 </div>
 <input
 type="text"
 name="{{ $name }}"
 id="{{ $inputId }}"
 class="timepicker bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 :ring-blue-500 :border-blue-500"
 placeholder="{{ $placeholder }}"
 value="{{ old($name, $value) }}"
 @if($required) required @endif
 @if($disabled) disabled @endif
 @if($enableSeconds) data-enable-seconds="true" @endif
 autocomplete="off"
 {{ $attributes->except(['class']) }}
 />
 </div>

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
</div>

@once
 @push('styles')
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
 @endpush

 @push('scripts')
 <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
 <script>
 document.addEventListener('DOMContentLoaded', function() {
 
 /**
 * ====================================================================
 * 🚀 TIME PICKER ENTERPRISE-GRADE V3.0
 * ====================================================================
 * 
 * Masque de saisie ultra-professionnel avec:
 * ✨ Format HH:MM avec saut automatique
 * ✨ Validation en temps réel
 * ✨ Navigation intelligente
 * ✨ Protection contre les erreurs
 * 
 * @version 3.0-Enterprise
 * @since 2025-11-03
 * ====================================================================
 */
 function applyEnterpriseTimeMask(input) {
 
 // Variables d'état
 let isUpdating = false;
 let lastValidValue = '';
 
 /**
 * Formater le temps avec validation stricte
 * Version simplifiée sans auto-complétion agressive
 */
 function formatTimeValue(value) {
 // Garder seulement les chiffres
 let digits = value.replace(/\D/g, '');
 
 // Limiter à 4 chiffres max (HHMM)
 if (digits.length > 4) {
 digits = digits.substring(0, 4);
 }
 
 // Construction progressive
 let formatted = '';
 
 if (digits.length === 0) {
 return '';
 }
 
 if (digits.length === 1) {
 // Un seul chiffre, l'afficher tel quel
 formatted = digits[0];
 }
 
 if (digits.length === 2) {
 // Deux chiffres = heures complètes
 let hours = parseInt(digits);
 
 // Validation: max 23h
 if (hours > 23) {
 hours = 23;
 }
 
 formatted = String(hours).padStart(2, '0') + ':';
 
 // Auto-jump après 2 chiffres
 setTimeout(() => {
 input.setSelectionRange(3, 3);
 }, 0);
 }
 
 if (digits.length === 3) {
 // Heures + premier chiffre des minutes
 let hours = parseInt(digits.substring(0, 2));
 if (hours > 23) hours = 23;
 
 // Pas d'auto-complétion, juste afficher le chiffre
 formatted = String(hours).padStart(2, '0') + ':' + digits[2];
 }
 
 if (digits.length === 4) {
 // Format complet HHMM
 let hours = parseInt(digits.substring(0, 2));
 if (hours > 23) hours = 23;
 
 let minutes = parseInt(digits.substring(2, 4));
 if (minutes > 59) minutes = 59;
 
 formatted = String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
 }
 
 return formatted;
 }
 
 /**
 * Gestionnaire d'input avec débounce
 */
 input.addEventListener('input', function(e) {
 if (isUpdating) return;
 
 isUpdating = true;
 const cursorPos = e.target.selectionStart;
 const newValue = formatTimeValue(e.target.value);
 
 // Mise à jour seulement si différent
 if (e.target.value !== newValue) {
 e.target.value = newValue;
 
 // Restaurer position curseur intelligemment
 if (newValue.length === 3 && newValue.includes(':')) {
 // Après le ':'
 e.target.setSelectionRange(3, 3);
 } else if (cursorPos <= newValue.length) {
 e.target.setSelectionRange(cursorPos, cursorPos);
 }
 }
 
 lastValidValue = newValue;
 isUpdating = false;
 });
 
 /**
 * Gestionnaire de touches spéciales
 */
 input.addEventListener('keydown', function(e) {
 const value = e.target.value;
 const cursorPos = e.target.selectionStart;
 
 // Tab ou Enter sur HH: → focus minutes
 if ((e.key === 'Tab' || e.key === 'Enter') && value.length === 3 && cursorPos === 3) {
 e.preventDefault();
 e.target.setSelectionRange(3, 3);
 return;
 }
 
 // Backspace intelligent
 if (e.key === 'Backspace') {
 // Si on est juste après ':', effacer le ':' et revenir aux heures
 if (cursorPos === 3 && value.charAt(2) === ':') {
 e.preventDefault();
 e.target.value = value.substring(0, 2);
 e.target.setSelectionRange(2, 2);
 return;
 }
 }
 
 // Navigation flèches
 if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
 // Skip le ':' automatiquement
 setTimeout(() => {
 const newPos = e.target.selectionStart;
 if (newPos === 2 && e.key === 'ArrowRight') {
 e.target.setSelectionRange(3, 3);
 } else if (newPos === 3 && e.key === 'ArrowLeft') {
 e.target.setSelectionRange(2, 2);
 }
 }, 0);
 }
 });
 
 /**
 * Focus: sélectionner tout si vide
 */
 input.addEventListener('focus', function(e) {
 if (!e.target.value || e.target.value === ':') {
 e.target.value = '';
 }
 });
 
 /**
 * Blur: valider format final (simplifié)
 */
 input.addEventListener('blur', function(e) {
 const value = e.target.value;
 
 // Si le champ n'est pas vide et n'est pas au format valide
 if (value && value.length > 0) {
 let digits = value.replace(/\D/g, '');
 
 // Compléter seulement si l'utilisateur a tapé exactement 2 chiffres (heures)
 if (digits.length === 2) {
 // HH → HH:00 (aider pour les heures pleines)
 e.target.value = digits + ':00';
 } else if (digits.length === 3) {
 // HHM → HH:M0 (compléter la minute manquante)
 let hours = digits.substring(0, 2);
 let mins = digits.substring(2) + '0';
 e.target.value = hours + ':' + mins;
 } else if (digits.length === 4) {
 // HHMM → HH:MM (ajouter juste le ':' si manquant)
 e.target.value = digits.substring(0, 2) + ':' + digits.substring(2, 4);
 }
 
 // Validation finale - seulement si le format est complètement invalide
 if (e.target.value && e.target.value.length >= 3 && !e.target.value.match(/^([01]?\d|2[0-3]):([0-5]?\d)$/)) {
 // Ne pas réinitialiser si c'est juste incomplet
 if (e.target.value.length === 5 && !e.target.value.match(/^([01]\d|2[0-3]):([0-5]\d)$/)) {
 e.target.value = '00:00';
 }
 }
 }
 });
 
 /**
 * Copier/Coller intelligent
 */
 input.addEventListener('paste', function(e) {
 e.preventDefault();
 const pastedText = (e.clipboardData || window.clipboardData).getData('text');
 const formatted = formatTimeValue(pastedText);
 e.target.value = formatted;
 
 // Focus fin si complet
 if (formatted.length === 5) {
 e.target.setSelectionRange(5, 5);
 }
 });
 }

 // Application à tous les timepickers
 document.querySelectorAll('.timepicker').forEach(function(el) {
 const enableSeconds = el.getAttribute('data-enable-seconds') === 'true';

 // Appliquer le masque Enterprise
 applyEnterpriseTimeMask(el);

 // Flatpickr comme fallback/amélioration
 flatpickr(el, {
 enableTime: true,
 noCalendar: true,
 dateFormat: enableSeconds ? "H:i:S" : "H:i",
 time_24hr: true,
 allowInput: true,
 disableMobile: true,
 defaultHour: null,
 defaultMinute: null,
 // Callbacks Enterprise
 onOpen: function(selectedDates, dateStr, instance) {
 // Désactiver temporairement le masque pendant que Flatpickr est ouvert
 instance.input.setAttribute('data-flatpickr-open', 'true');
 },
 onClose: function(selectedDates, dateStr, instance) {
 // Réactiver le masque
 instance.input.removeAttribute('data-flatpickr-open');
 // Valider le format
 if (dateStr && !dateStr.match(/^([01]\d|2[0-3]):([0-5]\d)$/)) {
 instance.input.value = '00:00';
 }
 }
 });
 });
 });
 </script>
 @endpush
@endonce
