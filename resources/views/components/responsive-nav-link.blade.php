@props(['active'])

@php
$classes = ($active ?? false)
            ? 'group flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-violet-50 text-violet-700'
            : 'group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
