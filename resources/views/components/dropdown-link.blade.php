@props(['active' => false, 'icon' => null, 'disabled' => false])

@php
$classes = 'group flex items-center w-full px-4 py-2 text-start text-sm leading-5 transition duration-150 ease-in-out';

if ($disabled) {
 $classes .= ' text-gray-400 cursor-not-allowed opacity-50';
} elseif ($active) {
 $classes .= ' text-gray-900 bg-gray-100 focus:outline-none focus:bg-gray-200';
} else {
 $classes .= ' text-gray-700 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-900';
}
@endphp

<a {{ $attributes->merge([
 'class' => $classes,
 'role' => 'menuitem',
 'tabindex' => $disabled ? '-1' : '0',
 'aria-disabled' => $disabled ? 'true' : null
]) }}>
 @if($icon)
 <i class="{{ $icon }} mr-3 text-gray-400 group-hover:text-gray-500 flex-shrink-0"></i>
 @endif
 
 <span class="truncate">{{ $slot }}</span>
</a>

