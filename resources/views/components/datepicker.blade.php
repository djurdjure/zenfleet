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
'format' => 'd/m/Y', // Format d'affichage (FR)
'placeholder' => 'Sélectionner une date',
])

@php
$inputId = 'datepicker-' . uniqid();
// Classes style Flowbite
$baseClasses = 'bg-gray-50 border text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 transition-colors duration-200';
$errorClasses = 'bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500';
$normalClasses = 'border-gray-300';

$finalClasses = $error ? $baseClasses . ' ' . $errorClasses : $baseClasses . ' ' . $normalClasses;
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}
    x-data="{
        serverDate: '{{ old($name, $value) }}', // Format Y-m-d (valeur réelle)
        displayDate: '', // Format d/m/Y (affichage)
        picker: null,

        init() {
            // Initialiser la date d'affichage à partir de la valeur serveur (si présente)
            if (this.serverDate) {
                this.displayDate = this.formatServerDateToDisplay(this.serverDate);
            }

            // Initialiser Flowbite Datepicker sur l'input visible
            this.$nextTick(() => {
                const el = this.$refs.displayInput;
                this.picker = new Datepicker(el, {
                    language: 'fr',
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    todayBtn: true,
                    clearBtn: true,
                    weekStart: 1, // Lundi
                    minDate: '{{ $minDate }}',
                    maxDate: '{{ $maxDate }}',
                    // Ajouter des classes pour le calendrier
                    orientation: 'bottom left',
                });

                // Gérer le changement de date via le picker
                el.addEventListener('changeDate', (e) => {
                   if (e.detail.date) {
                       // Convertir Date object -> Y-m-d pour le serveur
                       const d = e.detail.date;
                       const year = d.getFullYear();
                       const month = String(d.getMonth() + 1).padStart(2, '0');
                       const day = String(d.getDate()).padStart(2, '0');
                       this.serverDate = `${year}-${month}-${day}`;
                   } else {
                       this.serverDate = '';
                   }
                });
                
                // Gérer l'effacement manuel ou via bouton clear
                el.addEventListener('change', (e) => {
                    if (!el.value) {
                         this.serverDate = '';
                         this.picker.setDate(null);
                    }
                });
            });
        },

        formatServerDateToDisplay(dateStr) {
            if (!dateStr) return '';
            // Supposer Y-m-d ou Y-m-d H:i:s
            const date = new Date(dateStr);
            if (isNaN(date.getTime())) return dateStr; // Fallback
            
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
     }"
    wire:ignore>

    @if($label)
    <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium {{ $error ? 'text-red-700' : 'text-gray-900' }}">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>
    @endif

    <div class="relative">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4 {{ $error ? 'text-red-500' : 'text-gray-500' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
            </svg>
        </div>

        {{-- Input visible (Flowbite) --}}
        <input
            x-ref="displayInput"
            type="text"
            id="{{ $inputId }}"
            class="{{ $finalClasses }}"
            placeholder="{{ $placeholder }}"
            x-model="displayDate"
            @if($disabled) disabled @endif
            @if($required) required @endif
            autocomplete="off">

        {{-- Input caché (Valeur réelle envoyée au serveur) --}}
        <input
            type="hidden"
            name="{{ $name }}"
            x-model="serverDate">
    </div>

    @if($error)
    <p class="mt-2 text-sm text-red-600 flex items-center">
        <span class="font-medium">Erreur!</span>&nbsp;{{ $error }}
    </p>
    @elseif($helpText)
    <p class="mt-2 text-sm text-gray-500">{{ $helpText }}</p>
    @endif
</div>