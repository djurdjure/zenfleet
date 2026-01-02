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
'placeholder' => 'JJ/MM/AAAA',
'defaultToday' => true,
])

@php
$inputId = 'datepicker-pro-' . uniqid();
// Classes style Flowbite "Pro"
$baseClasses = 'bg-gray-50 border text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 transition-colors duration-200';
$errorClasses = 'bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 animate-shake';
$normalClasses = 'border-gray-300';

$finalClasses = $error ? $baseClasses . ' ' . $errorClasses : $baseClasses . ' ' . $normalClasses;
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}
    x-data="{
        serverDate: '{{ old($name, $value) }}',
        displayDate: '',
        picker: null,
        hasError: {{ $error ? 'true' : 'false' }},

        init() {
            // Gestion Default Today si vide
            if (!this.serverDate && {{ $defaultToday ? 'true' : 'false' }} && !'{{ old($name) }}') {
                const today = new Date();
                const y = today.getFullYear();
                const m = String(today.getMonth() + 1).padStart(2, '0');
                const d = String(today.getDate()).padStart(2, '0');
                this.serverDate = `${y}-${m}-${d}`;
            }

            if (this.serverDate) {
                this.displayDate = this.formatServerDateToDisplay(this.serverDate);
            }

            this.$nextTick(() => {
                const el = this.$refs.displayInput;
                this.picker = new Datepicker(el, {
                    language: 'fr',
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    todayBtn: true,
                    clearBtn: true,
                    weekStart: 1,
                    minDate: '{{ $minDate }}',
                    maxDate: '{{ $maxDate }}',
                    orientation: 'bottom left',
                });

                el.addEventListener('changeDate', (e) => {
                   if (e.detail.date) {
                       const d = e.detail.date;
                       this.serverDate = this.formatDateToServer(d);
                       this.hasError = false; // Reset error on selection
                   } else {
                       this.serverDate = '';
                   }
                });
                
                // Sync manual input
                el.addEventListener('change', (e) => {
                    if (!el.value) {
                         this.clearDate();
                    }
                });
            });
        },

        formatDateToServer(d) {
             const year = d.getFullYear();
             const month = String(d.getMonth() + 1).padStart(2, '0');
             const day = String(d.getDate()).padStart(2, '0');
             return `${year}-${month}-${day}`;
        },

        formatServerDateToDisplay(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            if (isNaN(date.getTime())) return dateStr;
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        },

        clearDate() {
            this.serverDate = '';
            this.displayDate = '';
            this.picker.setDate(null);
            this.hasError = false;
        },

        validateDate() {
            if (!this.displayDate) return;
            const regex = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
            if (regex.test(this.displayDate)) {
                const parts = this.displayDate.match(regex);
                const day = parseInt(parts[1], 10);
                const month = parseInt(parts[2], 10);
                const year = parseInt(parts[3], 10);
                const date = new Date(year, month - 1, day);
                
                if (date.getDate() !== day || date.getMonth() !== month - 1 || date.getFullYear() !== year) {
                     this.hasError = true;
                } else {
                     this.hasError = false;
                     // Sync server date if valid manually typed
                     this.serverDate = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                     this.picker.setDate(date);
                }
            } else {
                this.hasError = true;
            }
        }
     }"
    wire:ignore>

    @if($label)
    <label for="{{ $inputId }}" class="block mb-2 text-sm font-semibold {{ $error ? 'text-red-700' : 'text-gray-700' }}" :class="{'text-red-700': hasError, 'text-gray-700': !hasError}">
        {{ $label }}
        @if($required) <span class="text-red-500 ml-0.5">*</span> @endif
    </label>
    @endif

    <div class="relative group">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4 transition-colors duration-200" :class="{'text-red-500': hasError, 'text-gray-500': !hasError}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
            </svg>
        </div>

        {{-- Input visible --}}
        <input
            x-ref="displayInput"
            type="text"
            id="{{ $inputId }}"
            class="{{ $baseClasses }}"
            :class="{
                '{{ $errorClasses }}': hasError, 
                '{{ $normalClasses }}': !hasError
            }"
            placeholder="{{ $placeholder }}"
            x-model="displayDate"
            @blur="validateDate"
            @if($disabled) disabled @endif
            @if($required) required @endif
            autocomplete="off"
            maxlength="10">

        {{-- Bouton Clear --}}
        @if(!$disabled)
        <button
            type="button"
            class="absolute inset-y-0 end-2 flex items-center px-2 text-gray-400 hover:text-gray-600 focus:outline-none"
            x-show="displayDate"
            @click="clearDate()"
            style="display: none;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        @endif

        {{-- Input caché (Valeur réelle) --}}
        <input
            type="hidden"
            name="{{ $name }}"
            x-model="serverDate">
    </div>

    {{-- Messages d'erreur --}}
    <div x-show="hasError || '{{ $error }}'" style="display: none;" class="mt-2 flex items-start animate-fadeIn">
        <svg class="w-4 h-4 text-red-600 mr-1.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <p class="text-sm text-red-600 font-medium" x-text="'{{ $error }}' || 'Date invalide'"></p>
            <p class="text-xs text-red-500 mt-0.5" x-show="hasError && !'{{ $error }}'">Format attendu: JJ/MM/AAAA</p>
        </div>
    </div>

    @if($helpText)
    <p class="mt-2 text-sm text-gray-500 flex items-start" x-show="!hasError && !'{{ $error }}'">
        <svg class="w-4 h-4 text-gray-400 mr-1.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>{{ $helpText }}</span>
    </p>
    @endif
</div>