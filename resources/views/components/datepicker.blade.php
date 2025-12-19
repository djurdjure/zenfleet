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
? 'datepicker !bg-red-50 border-2 border-red-500 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full pl-11 p-3 transition-colors duration-200'
: 'datepicker !bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 hover:border-gray-300 block w-full pl-11 p-3 transition-colors duration-200';
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
    @if($label)
    <label for="{{ $inputId }}" class="block mb-2 text-sm font-semibold {{ $error ? 'text-red-700' : 'text-gray-700' }}">
        {{ $label }}
        @if($required)
        <span class="text-red-500 ml-0.5">*</span>
        @endif
    </label>
    @endif

    <div class="relative group">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
            <x-iconify icon="lucide:calendar-days" class="w-5 h-5 {{ $error ? 'text-red-500' : 'text-gray-400 group-hover:text-blue-600 transition-colors duration-200' }}" />
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
            {{ $attributes->except(['class']) }} />
    </div>

    @if($error)
    <p class="mt-2 text-sm text-red-600 flex items-start font-medium">
        <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
        <span>{{ $error }}</span>
    </p>
    @elseif($helpText)
    <p class="mt-2 text-sm text-gray-500 flex items-start">
        <x-iconify icon="lucide:info" class="w-4 h-4 text-gray-400 mr-1.5 mt-0.5 flex-shrink-0" />
        <span>{{ $helpText }}</span>
    </p>
    @endif
</div>

@once
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/light.css">
<style>
    /* 🎨 FLATPICKR ENTERPRISE-GRADE ULTRA-PRO V2 - Standard Variant */

    .flatpickr-calendar {
        background-color: #ffffff !important;
        border: 0 !important;
        border-radius: 1rem !important;
        box-shadow:
            0 0 0 1px rgba(0, 0, 0, 0.03),
            0 20px 25px -5px rgba(0, 0, 0, 0.08),
            0 10px 10px -5px rgba(0, 0, 0, 0.02) !important;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif !important;
        margin-top: 8px;
        width: 320px !important;
        padding: 0 !important;
        overflow: hidden;
    }

    .flatpickr-calendar:before,
    .flatpickr-calendar:after {
        display: none !important;
    }

    .flatpickr-months {
        background: #ffffff !important;
        border-bottom: 1px solid #f3f4f6;
        padding: 1.25rem 0.5rem 0.5rem 0.5rem !important;
    }

    .flatpickr-current-month .flatpickr-monthDropdown-months {
        background: white !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 0.5rem !important;
        color: #111827 !important;
        font-weight: 600 !important;
        padding: 0.25rem 0.5rem !important;
        appearance: none;
        -webkit-appearance: none;
        transition: all 0.2s;
    }

    .flatpickr-current-month .flatpickr-monthDropdown-months:hover {
        border-color: #d1d5db !important;
        background-color: #f9fafb !important;
    }

    .flatpickr-current-month .flatpickr-monthDropdown-months option {
        background-color: white !important;
        color: #111827 !important;
        padding: 8px;
    }

    .flatpickr-current-month input.cur-year {
        background: white !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 0.5rem !important;
        color: #111827 !important;
        font-weight: 600 !important;
        padding: 0.25rem 0.5rem !important;
    }

    .flatpickr-months .flatpickr-prev-month,
    .flatpickr-months .flatpickr-next-month {
        top: 1.25rem !important;
        padding: 0.5rem !important;
        height: 2rem !important;
        width: 2rem !important;
        border-radius: 0.5rem;
        fill: #6b7280 !important;
    }

    .flatpickr-months .flatpickr-prev-month:hover,
    .flatpickr-months .flatpickr-next-month:hover {
        background: #f3f4f6 !important;
        fill: #111827 !important;
    }

    .flatpickr-weekdays {
        background: #f9fafb !important;
        padding: 0.75rem 0.5rem !important;
        margin-bottom: 0.5rem;
    }

    .flatpickr-weekday {
        color: #6b7280 !important;
        font-weight: 700 !important;
        text-transform: uppercase;
    }

    .flatpickr-days {
        padding: 0 0.5rem 0.75rem 0.5rem !important;
    }

    .flatpickr-day {
        max-width: 38px !important;
        height: 38px !important;
        line-height: 38px !important;
        margin: 2px !important;
        border-radius: 0.6rem !important;
        color: #374151 !important;
        font-weight: 500;
        border: 1px solid transparent;
    }

    .flatpickr-day.today {
        background: #eff6ff !important;
        border: 1px solid #bfdbfe !important;
        color: #2563eb !important;
        font-weight: 600;
    }

    .flatpickr-day.selected,
    .flatpickr-day.selected:hover {
        background: #2563eb !important;
        color: white !important;
        border-color: #2563eb !important;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        font-weight: 600;
    }

    .flatpickr-day:hover:not(.selected):not(.flatpickr-disabled):not(.today) {
        background: #f3f4f6 !important;
        transform: scale(1.1);
    }

    input.datepicker.border-red-500+.flatpickr-calendar {
        box-shadow: 0 0 0 2px rgb(239 68 68), 0 20px 25px -5px rgba(239, 68, 68, 0.15) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.datepicker').forEach(function(el) {
            if (el._flatpickr) return;

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
                monthSelectorType: 'dropdown',
                yearSelectorType: 'static',
                nextArrow: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>',
                prevArrow: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>',
                onReady: function(selectedDates, dateStr, instance) {
                    if (el.classList.contains('border-red-500')) {
                        instance.calendarContainer.classList.add('error-calendar');
                    }
                }
            });
        });
    });
</script>
@endpush
@endonce