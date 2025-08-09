@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center w-full p-2 pl-4 text-sm font-medium text-indigo-600 rounded-lg bg-indigo-50'
            : 'flex items-center w-full p-2 pl-4 text-sm text-gray-600 rounded-lg hover:bg-gray-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <span class="sub-link-icon mr-3">
        <x-lucide-circle class="h-3 w-3 fill-current"/>
    </span>
    {{ $slot }}
</a>
