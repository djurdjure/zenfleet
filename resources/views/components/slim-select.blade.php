@props([
'name' => '',
'label' => null,
'error' => null,
'helpText' => null,
'required' => false,
'disabled' => false,
'options' => [],
'selected' => null,
'placeholder' => 'Sélectionnez...',
'multiple' => false,
'searchable' => true,
])

@php
$selectId = 'slimselect-' . $name . '-' . uniqid();
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
        class="slimselect-field w-full"
        data-slimselect="true"
        data-placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($multiple) multiple @endif
        {{ $attributes->except(['class']) }}>
        {{-- Options passées via le slot OU via la prop $options --}}
        @if($slot->isNotEmpty())
        {{ $slot }}
        @else
        @if(!$multiple)
        <option value="" data-placeholder="true">{{ $placeholder }}</option>
        @endif

        @foreach($options as $value => $optionLabel)
        <option
            value="{{ $value }}"
            {{ (is_array($selected) ? in_array($value, $selected) : old($name, $selected) == $value) ? 'selected' : '' }}>
            {{ $optionLabel }}
        </option>
        @endforeach
        @endif
    </select>

    @if($error)
    <p class="mt-2 text-sm text-red-600 flex items-start">
        <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
        <span>{{ $error }}</span>
    </p>
    @elseif($helpText)
    <p class="mt-2 text-sm text-gray-500">
        {{ $helpText }}
    </p>
    @endif

    {{-- Erreur dynamique Alpine.js - SEULEMENT si champ touché ET invalide --}}
    <p x-show="fieldErrors && fieldErrors['{{ $name }}'] && touchedFields && touchedFields['{{ $name }}']"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-2 text-sm text-red-600 flex items-start font-medium"
        style="display: none;">
        <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
        <span>Ce champ est obligatoire</span>
    </p>
</div>

@once
@push('styles')
@include('partials.slimselect-styles')
@endpush

@push('scripts')
<script>
    /**
     * ====================================================================
     * 🎯 SLIMSELECT ENTERPRISE INITIALIZATION
     * ====================================================================
     * Initialisation globale de SlimSelect avec style ZenFleet
     */
    function initializeSlimSelects() {
        document.querySelectorAll('[data-slimselect="true"]').forEach(function(el) {
            // Skip si déjà initialisé
            if (el.slimSelectInstance) return;

            const placeholder = el.getAttribute('data-placeholder') || 'Sélectionnez...';
            const isSearchable = el.getAttribute('data-searchable') !== 'false';

            try {
                const instance = new SlimSelect({
                    select: el,
                    settings: {
                        showSearch: isSearchable,
                        searchPlaceholder: 'Rechercher...',
                        searchText: 'Aucun résultat',
                        searchingText: 'Recherche...',
                        allowDeselect: true,
                        placeholderText: placeholder,
                        hideSelected: false,
                        contentLocation: document.body,
                        contentPosition: 'absolute'
                    },
                    events: {
                        afterChange: (newVal) => {
                            // Dispatch change event pour Alpine.js et autres listeners
                            el.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                        },
                        afterOpen: () => {
                            // Focus sur le champ de recherche
                            const searchInput = document.querySelector('.ss-search input');
                            if (searchInput) {
                                setTimeout(() => searchInput.focus(), 50);
                            }
                        }
                    }
                });

                // Stocker l'instance pour référence
                el.slimSelectInstance = instance;
            } catch (e) {
                console.error('SlimSelect init error:', e);
            }
        });
    }

    // Initialiser au chargement du DOM
    document.addEventListener('DOMContentLoaded', initializeSlimSelects);

    // Réinitialiser après navigation Livewire
    document.addEventListener('livewire:navigated', initializeSlimSelects);

    // Support Alpine.js
    document.addEventListener('alpine:init', function() {
        Alpine.magic('slimselect', (el) => {
            return () => {
                const selectEl = el.querySelector('[data-slimselect="true"]');
                return selectEl?.slimSelectInstance;
            };
        });
    });
</script>
@endpush
@endonce