@props([
 'name' => '',
 'label' => null,
 'error' => null,
 'helpText' => null,
 'required' => false,
 'disabled' => false,
 'value' => null,
 'minDate' => null,
 'maxDate' => null,
 'format' => 'd/m/Y',
 'placeholder' => 'Sélectionner une date',
])

@php
 $component = new \App\View\Components\Datepicker($name, $label, $error, $helpText, $required, $disabled, $value, $minDate, $maxDate, $format, $placeholder);
 $inputId = $component->getId();

 // Classes conditionnelles pour erreur (fond gris clair bg-gray-50)
 $inputClasses = $error
 ? 'datepicker !bg-red-50 border-red-500 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5 transition-colors duration-200'
 : 'datepicker !bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 block w-full pl-10 p-2.5 transition-colors duration-200';
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
 @if($label)
 <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium text-gray-900">
 {{ $label }}
 @if($required)
 <span class="text-red-600">*</span>
 @endif
 </label>
 @endif

 <div class="relative">
 <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
 <x-iconify icon="heroicons:calendar-days" class="w-4 h-4 {{ $error ? 'text-red-500' : 'text-gray-500' }}" />
 </div>
 <input
 type="text"
 name="{{ $name }}"
 id="{{ $inputId }}"
 class="{{ $inputClasses }}"
 placeholder="{{ $placeholder }}"
 value="{{ old($name, $value) }}"
 @if($required) required @endif
 @if($disabled) disabled @endif
 @if($minDate) data-min-date="{{ $minDate }}" @endif
 @if($maxDate) data-max-date="{{ $maxDate }}" @endif
 data-date-format="{{ $format }}"
 autocomplete="off"
 {{ $attributes->except(['class']) }}
 />
 </div>

 @if($error)
 <p class="mt-2 text-sm text-red-600 flex items-start font-medium">
 <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
 <span>{{ $error }}</span>
 </p>
 @elseif($helpText)
 <p class="mt-2 text-sm text-gray-600">
 {{ $helpText }}
 </p>
 @endif
</div>

@once
 @push('styles')
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/light.css">
 <style>
 /* 🎨 FLATPICKR ENTERPRISE-GRADE LIGHT MODE - ZenFleet Ultra-Pro */
 .flatpickr-calendar {
 background-color: white !important;
 border: 1px solid rgb(229 231 235);
 border-radius: 0.75rem;
 box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
 font-family: inherit;
 }

 /* En-tête (mois/année) - Bleu blue-600 premium */
 .flatpickr-months {
 background: rgb(37 99 235) !important;
 border-radius: 0.75rem 0.75rem 0 0;
 padding: 0.875rem 0;
 }

 .flatpickr-months .flatpickr-month,
 .flatpickr-current-month .flatpickr-monthDropdown-months {
 background-color: transparent !important;
 color: white !important;
 font-weight: 600;
 font-size: 1rem;
 }

 /* Boutons navigation */
 .flatpickr-months .flatpickr-prev-month,
 .flatpickr-months .flatpickr-next-month {
 fill: white !important;
 transition: all 0.2s;
 }

 .flatpickr-months .flatpickr-prev-month:hover,
 .flatpickr-months .flatpickr-next-month:hover {
 fill: rgb(219 234 254) !important;
 transform: scale(1.15);
 }

 /* Jours de la semaine */
 .flatpickr-weekdays {
 background-color: rgb(249 250 251) !important;
 padding: 0.625rem 0;
 border-bottom: 1px solid rgb(229 231 235);
 }

 .flatpickr-weekday {
 color: rgb(107 114 128) !important;
 font-weight: 600;
 font-size: 0.75rem;
 text-transform: uppercase;
 letter-spacing: 0.05em;
 }

 /* Corps du calendrier */
 .flatpickr-days {
 background-color: white !important;
 }

 /* Jours du mois */
 .flatpickr-day {
 color: rgb(17 24 39) !important;
 border-radius: 0.5rem;
 font-weight: 500;
 transition: all 0.2s;
 border: 1px solid transparent;
 }

 .flatpickr-day.today {
 border: 2px solid rgb(37 99 235) !important;
 font-weight: 700;
 color: rgb(37 99 235) !important;
 background-color: rgb(239 246 255) !important;
 }

 .flatpickr-day.selected,
 .flatpickr-day.selected:hover {
 background-color: rgb(37 99 235) !important;
 border-color: rgb(37 99 235) !important;
 color: white !important;
 font-weight: 700;
 box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
 }

 .flatpickr-day:hover:not(.selected):not(.flatpickr-disabled):not(.today) {
 background-color: rgb(243 244 246) !important;
 border-color: rgb(229 231 235) !important;
 color: rgb(17 24 39) !important;
 transform: scale(1.05);
 }

 .flatpickr-day.flatpickr-disabled {
 color: rgb(209 213 219) !important;
 opacity: 0.4;
 }

 /* Input avec bordure rouge si erreur */
 input.datepicker.border-red-500 + .flatpickr-calendar {
 border-color: rgb(239 68 68);
 }
 </style>
 @endpush

 @push('scripts')
 <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
 <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
 <script>
 document.addEventListener('DOMContentLoaded', function() {
 document.querySelectorAll('.datepicker').forEach(function(el) {
 const minDate = el.getAttribute('data-min-date');
 const maxDate = el.getAttribute('data-max-date');
 const dateFormat = el.getAttribute('data-date-format') || 'd/m/Y';

 flatpickr(el, {
 locale: 'fr',
 dateFormat: dateFormat,
 minDate: minDate,
 maxDate: maxDate,
 allowInput: true,
 disableMobile: true,
 nextArrow: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>',
 prevArrow: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>',
 });
 });
 });
 </script>
 @endpush
@endonce
