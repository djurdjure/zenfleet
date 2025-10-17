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
        <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-5 h-5 text-gray-400" />
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
