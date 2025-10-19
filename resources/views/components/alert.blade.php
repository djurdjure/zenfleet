@props([
 'type' => 'info',
 'title' => null,
 'dismissible' => false,
 'showIcon' => true,
])

@php
 $component = new \App\View\Components\Alert($type, $title, $dismissible, $showIcon);
 $classes = $component->getClasses();
 $icon = $component->getIcon();
 $iconColor = $component->getIconColor();
 $titleColor = $component->getTitleColor();
 $textColor = $component->getTextColor();
@endphp

<div 
 x-data="{ show: true }" 
 x-show="show"
 x-transition:leave="transition ease-in duration-200"
 x-transition:leave-start="opacity-100"
 x-transition:leave-end="opacity-0"
 class="{{ $classes }}" 
 {{ $attributes }}
 role="alert"
>
 <div class="flex items-start">
 @if($showIcon)
 <div class="flex-shrink-0">
 <x-iconify :icon="'heroicons:' . $icon" class="w-5 h-5 {{ $iconColor }} mt-0.5" />
 </div>
 @endif

 <div class="flex-1 {{ $showIcon ? 'ml-3' : '' }}">
 @if($title)
 <h3 class="text-sm font-medium {{ $titleColor }}">
 {{ $title }}
 </h3>
 @endif

 @if($slot->isNotEmpty())
 <div class="text-sm {{ $textColor }} {{ $title ? 'mt-1' : '' }}">
 {{ $slot }}
 </div>
 @endif
 </div>

 @if($dismissible)
 <div class="ml-auto pl-3">
 <button
 @click="show = false"
 class="-mx-1.5 -my-1.5 rounded-lg p-1.5 {{ $iconColor }} hover:bg-black/5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-transparent inline-flex items-center justify-center"
 aria-label="Fermer"
 >
 <x-iconify icon="heroicons:x-mark" class="w-5 h-5" />
 </button>
 </div>
 @endif
 </div>
</div>
