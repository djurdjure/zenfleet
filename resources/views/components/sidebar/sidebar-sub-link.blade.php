@props(['active'])

@php
$classes = ($active ?? false)
 ? 'flex items-center w-full p-2 pl-4 text-sm font-medium text-indigo-600 rounded-lg bg-indigo-50 active'
 : 'flex items-center w-full p-2 pl-4 text-sm text-gray-600 rounded-lg hover:bg-gray-100';

$iconWrapperClasses = ($active ?? false)
 ? 'flex items-center justify-center h-7 w-7 rounded-full mr-3 bg-indigo-600 text-white'
 : 'flex items-center justify-center h-7 w-7 rounded-full mr-3 bg-gray-100 text-gray-500 border border-gray-300 hover:bg-white hover:border-gray-300';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
 @if(isset($icon))
 <span class="{{ $iconWrapperClasses }}">
 @php
 $newIcon = clone $icon;
 $newIcon->attributes['class'] = 'h-2.5 w-2.5'; // Force 10px
 @endphp
 {{ $newIcon }}
 </span>
 @else
 <span class="mr-3 w-7"></span>
 @endif
 <span class="truncate">{{ $slot }}</span>
</a>
