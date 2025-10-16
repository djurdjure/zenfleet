@props([
    'type' => 'gray',
    'size' => 'md',
])

@php
    $component = new \App\View\Components\Badge($type, $size);
    $classes = $component->getClasses();
@endphp

<span class="{{ $classes }}" {{ $attributes }}>
    {{ $slot }}
</span>
