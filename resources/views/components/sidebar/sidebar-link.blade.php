@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center p-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg'
            : 'flex items-center p-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 group';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($icon))
        <span class="mr-3 text-gray-500 group-hover:text-gray-700 {{ ($active ?? false) ? 'text-indigo-600' : '' }}">
            {{ $icon }}
        </span>
    @endif
    <span class="flex-1 ml-1 whitespace-nowrap">{{ $slot }}</span>
</a>