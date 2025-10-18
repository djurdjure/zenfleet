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
        <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <x-icon icon="heroicons:clock" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
        </div>
        <input
            type="text"
            name="{{ $name }}"
            id="{{ $inputId }}"
            class="timepicker bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
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
            <x-icon icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" />
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
            // Fonction de masque de saisie pour le format HH:MM
            function applyTimeMask(input) {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, ''); // Garder seulement les chiffres

                    if (value.length >= 2) {
                        // Limiter les heures à 23
                        let hours = parseInt(value.substring(0, 2));
                        if (hours > 23) hours = 23;

                        let formattedValue = String(hours).padStart(2, '0');

                        if (value.length >= 3) {
                            // Limiter les minutes à 59
                            let minutes = parseInt(value.substring(2, 4));
                            if (minutes > 59) minutes = 59;
                            formattedValue += ':' + String(minutes).padStart(2, '0');
                        } else if (value.length === 2) {
                            formattedValue += ':';
                        }

                        e.target.value = formattedValue;
                    }
                });

                // Empêcher la suppression du ':' et gérer le backspace
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace') {
                        const cursorPos = e.target.selectionStart;
                        if (cursorPos === 3 && e.target.value.charAt(2) === ':') {
                            e.preventDefault();
                            e.target.value = e.target.value.substring(0, 2);
                        }
                    }
                });

                // Auto-focus sur les minutes après saisie des heures
                input.addEventListener('input', function(e) {
                    const value = e.target.value;
                    if (value.length === 3 && value.includes(':')) {
                        // Placer le curseur après le ':'
                        e.target.setSelectionRange(3, 3);
                    }
                });
            }

            document.querySelectorAll('.timepicker').forEach(function(el) {
                const enableSeconds = el.getAttribute('data-enable-seconds') === 'true';

                // Appliquer le masque de saisie
                applyTimeMask(el);

                flatpickr(el, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: enableSeconds ? "H:i:S" : "H:i",
                    time_24hr: true,
                    allowInput: true,
                    disableMobile: true,
                    defaultHour: 0,
                    defaultMinute: 0,
                });
            });
        });
    </script>
    @endpush
@endonce
