@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
])

@php
    $component = new \App\View\Components\Button($variant, $size, $icon, $iconPosition, $href, $type, $disabled);
    $classes = $component->getClasses();
    $iconSize = $component->getIconSize();
@endphp

@if($href)
    <a href="{{ $href }}" class="{{ $classes }}" {{ $attributes }}>
        @if($icon && $iconPosition === 'left')
            <x-dynamic-component :component="'heroicon-o-' . $icon" :class="$iconSize . ' ' . ($slot->isNotEmpty() ? 'mr-2' : '')" />
        @endif
        
        {{ $slot }}
        
        @if($icon && $iconPosition === 'right')
            <x-dynamic-component :component="'heroicon-o-' . $icon" :class="$iconSize . ' ' . ($slot->isNotEmpty() ? 'ml-2' : '')" />
        @endif
    </a>
@else
    <button 
        type="{{ $type }}" 
        class="{{ $classes }}"
        @if($disabled) disabled @endif
        {{ $attributes }}
    >
        @if($icon && $iconPosition === 'left')
            <x-dynamic-component :component="'heroicon-o-' . $icon" :class="$iconSize . ' ' . ($slot->isNotEmpty() ? 'mr-2' : '')" />
        @endif
        
        {{ $slot }}
        
        @if($icon && $iconPosition === 'right')
            <x-dynamic-component :component="'heroicon-o-' . $icon" :class="$iconSize . ' ' . ($slot->isNotEmpty() ? 'ml-2' : '')" />
        @endif
    </button>
@endif
