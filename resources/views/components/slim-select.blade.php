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
$baseKey = preg_replace('/\[\]$/', '', $name);
$lookupKey = trim(preg_replace('/\[(.*?)\]/', '.$1', $baseKey), '.');
$resolvedError = $error ?: ((isset($errors) && $lookupKey !== '') ? ($errors->first($lookupKey) ?: $errors->first($lookupKey . '.0')) : null);
$htmlName = $multiple && !str_ends_with($name, '[]') ? $name . '[]' : $name;
$selectId = 'slimselect-' . $name . '-' . uniqid();
$selectedValues = old($lookupKey !== '' ? $lookupKey : $name, $selected);
if (!is_array($selectedValues)) {
    $selectedValues = ($selectedValues === null || $selectedValues === '') ? [] : [$selectedValues];
}
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
    {{ $attributes->merge(['class' => $resolvedError ? 'slimselect-error' : '']) }}>

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
        name="{{ $htmlName }}"
        id="{{ $selectId }}"
        class="slimselect-field w-full"
        aria-invalid="{{ $resolvedError ? 'true' : 'false' }}"
        @if($required) required @endif
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
        @php
        $isSelected = $multiple
            ? in_array((string) $value, array_map('strval', $selectedValues), true)
            : ((string) old($lookupKey !== '' ? $lookupKey : $name, $selected) === (string) $value);
        @endphp
        <option
            value="{{ $value }}"
            {{ $isSelected ? 'selected' : '' }}>
            {{ $optionLabel }}
        </option>
        @endforeach
        @endif
    </select>

    @if($resolvedError)
    <p class="mt-2 text-sm text-red-600 flex items-start">
        <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
        <span>{{ $resolvedError }}</span>
    </p>
    @elseif($helpText)
    <p class="mt-2 text-sm text-gray-500">
        {{ $helpText }}
    </p>
    @endif
</div>
