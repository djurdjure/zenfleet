@props(['active' => false, 'icon' => null, 'badge' => null])

@php
$classes = 'group flex items-center w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium transition duration-150 ease-in-out';

if ($active) {
 $classes .= ' border-indigo-400 text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700';
} else {
 $classes .= ' border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300';
}
@endphp

<a {{ $attributes->merge([
 'class' => $classes,
 'aria-current' => $active ? 'page' : null,
 'role' => 'menuitem'
]) }}>
 <div class="flex items-center justify-between w-full">
 <div class="flex items-center">
 @if($icon)
 <i class="{{ $icon }} mr-3 flex-shrink-0 text-gray-400 group-hover:text-gray-500"></i>
 @endif
 
 <span class="truncate">{{ $slot }}</span>
 </div>
 
 @if($badge)
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200 ml-2">
 {{ $badge }}
 </span>
 @endif
 </div>
</a>

