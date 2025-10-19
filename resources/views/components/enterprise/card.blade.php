{{-- Composant Card Enterprise Ultra-Moderne --}}
@props([
 'title' => null,
 'description' => null,
 'actions' => null,
 'footer' => null,
 'variant' => 'default', // default, glass, gradient, elevated
 'padding' => true,
 'hover' => true,
])

@php
$classes = 'rounded-xl transition-all duration-300 ';

switch($variant) {
 case 'glass':
 $classes .= 'bg-white/80 backdrop-blur-xl border border-white/20 shadow-2xl ';
 break;
 case 'gradient':
 $classes .= 'bg-gradient-to-br from-white via-gray-50 to-primary-50/20 border border-gray-200/50 shadow-lg ';
 break;
 case 'elevated':
 $classes .= 'bg-white border border-gray-200/50 shadow-xl ';
 break;
 default:
 $classes .= 'bg-white border border-gray-200/50 shadow-sm ';
}

if ($hover) {
 $classes .= 'hover:shadow-xl hover:-translate-y-1 hover:border-primary-200/50 ';
}

if ($padding) {
 $classes .= 'p-6 ';
}
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
 {{-- Header --}}
 @if($title || $actions)
 <div class="flex items-start justify-between {{ $padding ? '' : 'px-6 pt-6' }}">
 <div class="flex-1">
 @if($title)
 <h3 class="text-lg font-semibold text-gray-900 leading-tight">
 {{ $title }}
 </h3>
 @endif
 
 @if($description)
 <p class="mt-1 text-sm text-gray-500">
 {{ $description }}
 </p>
 @endif
 </div>
 
 @if($actions)
 <div class="flex items-center gap-2 ml-4">
 {{ $actions }}
 </div>
 @endif
 </div>
 
 @if($title || $actions)
 <div class="mt-4 {{ $padding ? '' : 'px-6' }}">
 <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
 </div>
 @endif
 @endif
 
 {{-- Content --}}
 <div class="{{ ($title || $actions) ? 'mt-4' : '' }} {{ $padding ? '' : 'px-6 pb-6' }}">
 {{ $slot }}
 </div>
 
 {{-- Footer --}}
 @if($footer)
 <div class="mt-4 {{ $padding ? '-m-6 mt-6' : '' }}">
 <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
 <div class="px-6 py-4 bg-gray-50/50 rounded-b-xl">
 {{ $footer }}
 </div>
 </div>
 @endif
</div>
