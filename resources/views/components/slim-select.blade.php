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

<div wire:ignore
    x-data="{
        instance: null,
        initSelect() {
            if (this.instance) return;
            this.instance = new SlimSelect({
                select: this.$refs.select,
                settings: {
                    showSearch: {{ $searchable ? 'true' : 'false' }},
                    searchPlaceholder: 'Rechercher...',
                    searchText: 'Aucun résultat',
                    searchingText: 'Recherche...',
                    placeholderText: '{{ $placeholder }}',
                    allowDeselect: true,
                    hideSelected: false,
                },
                events: {
                    afterChange: (newVal) => {
                        // Dispatch event for Livewire/Alpine
                        this.$refs.select.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            });
        }
    }"
    x-init="initSelect()"
    {{ $attributes->merge(['class' => '']) }}>

    @if($label)
    <label for="{{ $selectId }}" class="block mb-2 text-sm font-medium text-gray-900">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    <select
        x-ref="select"
        name="{{ $name }}"
        id="{{ $selectId }}"
        class="slimselect-field w-full"
        @if($disabled) disabled @endif
        @if($multiple) multiple @endif
        {{ $attributes->except(['class']) }}>

        {{-- Options --}}
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
</div>

@once
@push('styles')
@include('partials.slimselect-styles')
@endpush
@endonce