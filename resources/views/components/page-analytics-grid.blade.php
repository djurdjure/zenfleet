@props(['columns' => 'auto', 'gap' => '4'])

{{--
    ROBUST GRID SYSTEM:
    1. Uses static Tailwind classes for JIT (grid-cols-X)
    2. Includes .analytics-cards-grid as a backup from app.css
--}}

@php
// Generate complete static class string based on columns
$tailwindClasses = match($columns) {
'2' => 'grid grid-cols-1 md:grid-cols-2 gap-4 mb-6',
'3' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6',
'4' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6',
'5' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6',
'6' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6',
default => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6',
};
@endphp

<div {{ $attributes->merge(['class' => "analytics-cards-grid {$tailwindClasses}"]) }}>
    {{ $slot }}
</div>