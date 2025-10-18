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
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
            </svg>
        </div>
        <input
            type="text"
            name="{{ $name }}"
            id="{{ $inputId }}"
            class="datepicker bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
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
        <p class="mt-2 text-sm text-red-600 flex items-start">
            <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" />
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/light.css">
    <style>
        .flatpickr-calendar {
            background-color: white;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .dark .flatpickr-calendar {
            background-color: rgb(55 65 81);
            border-color: rgb(75 85 99);
        }
        .flatpickr-months {
            background-color: rgb(59 130 246);
        }
        .flatpickr-months .flatpickr-month,
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            background-color: rgb(59 130 246);
            color: white;
        }
        .flatpickr-weekdays {
            background-color: rgb(239 246 255);
        }
        .dark .flatpickr-weekdays {
            background-color: rgb(30 58 138);
        }
        .flatpickr-day {
            color: rgb(17 24 39);
        }
        .dark .flatpickr-day {
            color: white;
        }
        .flatpickr-day.today {
            border-color: rgb(59 130 246);
        }
        .flatpickr-day.selected {
            background-color: rgb(59 130 246);
            border-color: rgb(59 130 246);
            color: white;
        }
        .flatpickr-day:hover {
            background-color: rgb(219 234 254);
            border-color: rgb(219 234 254);
        }
        .dark .flatpickr-day:hover {
            background-color: rgb(30 58 138);
            border-color: rgb(30 58 138);
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
