@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center w-full p-2 pl-4 text-sm font-medium text-indigo-600 rounded-lg bg-indigo-50 active'
            : 'flex items-center w-full p-2 pl-4 text-sm text-gray-600 rounded-lg hover:bg-gray-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($icon))
        <span class="sub-link-icon-wrapper mr-3">
            {{ $icon }}
        </span>
    @else
        <span class="mr-3 w-6"></span>
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>
