@props([
 'align' => 'right', 
 'width' => '48', 
 'contentClasses' => 'py-1 bg-white',
 'dropdownClasses' => '',
 'trigger' => null
])

@php
// ✅ OPTIMISATION: Gestion dynamique de l'alignement
$alignmentClasses = match($align) {
 'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
 'top' => 'origin-top',
 'none' => '',
 'right', 
 default => 'ltr:origin-top-right rtl:origin-top-left end-0'
};

// ✅ OPTIMISATION: Gestion flexible de la largeur
$widthClasses = match($width) {
 '48' => 'w-48',
 '56' => 'w-56',
 '64' => 'w-64',
 '72' => 'w-72',
 '80' => 'w-80',
 '96' => 'w-96',
 'full' => 'w-full',
 default => $width
};

$dropdownId = 'dropdown-' . uniqid();
@endphp

<div class="relative {{ $dropdownClasses }}" 
 x-data="{ open: false, id: '{{ $dropdownId }}' }" 
 x-on:click.away="open = false" 
 x-on:close.stop="open = false"
 x-on:keydown.escape.window="open = false">
 
 {{-- Trigger --}}
 <div x-on:click="open = ! open" 
 class="cursor-pointer"
 :aria-expanded="open"
 :aria-controls="id"
 role="button"
 tabindex="0"
 x-on:keydown.enter="open = ! open"
 x-on:keydown.space.prevent="open = ! open">
 {{ $trigger ?? $slot }}
 </div>

 {{-- Dropdown Content --}}
 <div x-show="open"
 :id="id"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 scale-95"
 x-transition:enter-end="opacity-100 scale-100"
 x-transition:leave="transition ease-in duration-75"
 x-transition:leave-start="opacity-100 scale-100"
 x-transition:leave-end="opacity-0 scale-95"
 class="absolute z-50 mt-2 {{ $widthClasses }} rounded-md shadow-lg {{ $alignmentClasses }}"
 style="display: none;"
 x-on:click="open = false"
 role="menu"
 aria-orientation="vertical">
 
 <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
 {{ $content ?? '' }}
 </div>
 </div>
</div>

