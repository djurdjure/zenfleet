@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center w-full p-2 pl-4 text-sm font-medium text-indigo-600 rounded-lg bg-indigo-50'
            : 'flex items-center w-full p-2 pl-4 text-sm text-gray-600 rounded-lg hover:bg-gray-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <span class="sub-link-icon mr-3">
        <x-lucide-minus class="h-4 w-4"/>
    </span>
    {{ $slot }}
</a>
