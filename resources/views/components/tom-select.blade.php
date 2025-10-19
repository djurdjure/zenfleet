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
