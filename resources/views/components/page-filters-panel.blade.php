@props(['xShow' => 'showFilters', 'columns' => '4'])

{{--
    ROBUST FILTER PANEL:
    1. Uses static Tailwind classes for JIT
    2. Includes .filters-grid as a backup from app.css
--}}

@php
$tailwindClasses = match($columns) {
'2' => 'grid grid-cols-1 md:grid-cols-2 gap-4',
'3' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4',
'4' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4',
'5' => 'grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4',
default => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4',
};
@endphp

<div x-show="{{ $xShow }}"
    x-collapse
    {{ $attributes->merge(['class' => 'mt-4 bg-white rounded-lg border border-gray-200 p-6 shadow-sm']) }}>

    <div class="filters-grid {{ $tailwindClasses }}">
        {{ $slot }}
    </div>

    {{-- Reset Button --}}
    @isset($reset)
    <div class="mt-4 flex gap-2 justify-end pt-4 border-t border-gray-200">
        {{ $reset }}
    </div>
    @endisset
</div>