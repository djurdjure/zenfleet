@props([
    'type' => 'text',
    'name' => '',
    'label' => null,
    'placeholder' => null,
    'error' => null,
    'helpText' => null,
    'icon' => null,
    'required' => false,
    'disabled' => false,
    'value' => null,
])

@php
    $component = new \App\View\Components\Input($type, $name, $label, $placeholder, $error, $helpText, $icon, $required, $disabled, $value);
    $classes = $component->getClasses();
    $inputId = $component->getId();
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
    @if($label)
        <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium text-gray-900">
            {{ $label }}
            @if($required)
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <x-iconify :icon="'heroicons:' . $icon" class="w-5 h-5 text-gray-400" />
            </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            class="{{ $classes }} {{ $icon ? 'pl-10' : '' }}"
            placeholder="{{ $placeholder }}"
            value="{{ old($name, $value) }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{-- ⚠️ VALIDATION TEMPS RÉEL: Bordure rouge vif SEULEMENT si champ touché ET invalide --}}
            x-bind:class="(fieldErrors && fieldErrors['{{ $name }}'] && touchedFields && touchedFields['{{ $name }}']) ? '!border-red-500 !focus:ring-2 !focus:ring-red-500 !focus:border-red-500 !bg-red-50' : ''"
            {{ $attributes->except(['class']) }}
        />
    </div>

    @if($error)
        <p class="mt-2 text-sm text-red-600 flex items-start font-medium">
            <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
            <span>{{ $error }}</span>
        </p>
    @elseif($helpText)
        <p class="mt-2 text-sm text-gray-600">
            {{ $helpText }}
        </p>
    @endif

    {{-- ⚠️ Erreur dynamique Alpine.js - SEULEMENT si champ touché ET invalide --}}
    <p x-show="fieldErrors && fieldErrors['{{ $name }}'] && touchedFields && touchedFields['{{ $name }}']"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 transform -translate-y-1"
       x-transition:enter-end="opacity-100 transform translate-y-0"
       class="mt-2 text-sm text-red-600 flex items-start font-medium"
       style="display: none;">
        <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
        <span>Ce champ est obligatoire et doit être correctement rempli</span>
    </p>
</div>
