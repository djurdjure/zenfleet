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
        <label for="{{ $selectId }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <select
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        id="{{ $selectId }}"
        class="tomselect bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($multiple) multiple @endif
        {{ $attributes->except(['class']) }}
    >
        @if(!$multiple && !$required)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $value => $optionLabel)
            <option
                value="{{ $value }}"
                {{ (is_array($selected) ? in_array($value, $selected) : old($name, $selected) == $value) ? 'selected' : '' }}
            >
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

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
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-wrapper .ts-control {
            background-color: rgb(249 250 251);
            border: 1px solid rgb(209 213 219);
            border-radius: 0.5rem;
            padding: 0.625rem;
            font-size: 0.875rem;
            color: rgb(17 24 39);
        }
        .dark .ts-wrapper .ts-control {
            background-color: rgb(55 65 81);
            border-color: rgb(75 85 99);
            color: white;
        }
        .ts-wrapper.single .ts-control::after {
            border-color: rgb(107 114 128) transparent transparent transparent;
            border-width: 5px 5px 0 5px;
            right: 15px;
            top: 50%;
            margin-top: -2.5px;
        }
        .ts-wrapper .ts-dropdown {
            background-color: white;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.5rem;
            margin-top: 0.25rem;
        }
        .dark .ts-wrapper .ts-dropdown {
            background-color: rgb(55 65 81);
            border-color: rgb(75 85 99);
        }
        .ts-wrapper .ts-dropdown .option {
            padding: 0.5rem 0.75rem;
            color: rgb(17 24 39);
        }
        .dark .ts-wrapper .ts-dropdown .option {
            color: white;
        }
        .ts-wrapper .ts-dropdown .option.active {
            background-color: rgb(239 246 255);
            color: rgb(37 99 235);
        }
        .dark .ts-wrapper .ts-dropdown .option.active {
            background-color: rgb(30 58 138);
            color: rgb(147 197 253);
        }
        .ts-wrapper .ts-control input {
            color: rgb(17 24 39);
        }
        .dark .ts-wrapper .ts-control input {
            color: white;
        }
        .ts-wrapper.focus .ts-control {
            border-color: rgb(59 130 246);
            box-shadow: 0 0 0 1px rgb(59 130 246);
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.tomselect').forEach(function(el) {
                new TomSelect(el, {
                    plugins: ['clear_button', 'remove_button'],
                    maxOptions: 100,
                    placeholder: el.getAttribute('data-placeholder') || 'Rechercher...',
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
                    }
                });
            });
        });
    </script>
    @endpush
@endonce
