@props([
 'name',
 'title' => null,
 'maxWidth' => 'lg',
 'closeable' => true,
])

@php
 $maxWidthClasses = match($maxWidth) {
 'sm' => 'max-w-sm',
 'md' => 'max-w-md',
 'lg' => 'max-w-lg',
 'xl' => 'max-w-xl',
 '2xl' => 'max-w-2xl',
 'full' => 'max-w-full',
 default => 'max-w-lg',
 };
@endphp

<div
 x-data="{ show: false }"
 x-on:open-modal.window="$event.detail === '{{ $name }}' ? show = true : null"
 x-on:close-modal.window="$event.detail === '{{ $name }}' ? show = false : null"
 x-on:keydown.escape.window="show = false"
 x-show="show"
 class="fixed inset-0 z-50 overflow-y-auto"
 style="display: none;"
>
 {{-- Backdrop --}}
 <div 
 x-show="show"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0"
 x-transition:enter-end="opacity-100"
 x-transition:leave="transition ease-in duration-200"
 x-transition:leave-start="opacity-100"
 x-transition:leave-end="opacity-0"
 class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
 @click="show = false"
 ></div>

 {{-- Modal Dialog --}}
 <div class="flex min-h-screen items-center justify-center p-4">
 <div
 x-show="show"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 scale-95"
 x-transition:enter-end="opacity-100 scale-100"
 x-transition:leave="transition ease-in duration-200"
 x-transition:leave-start="opacity-100 scale-100"
 x-transition:leave-end="opacity-0 scale-95"
 class="w-full {{ $maxWidthClasses }} bg-white rounded-xl shadow-2xl"
 @click.away="@if($closeable) show = false @endif"
 >
 {{-- Header --}}
 @if($title || $closeable)
 <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
 @if($title)
 <h3 class="text-lg font-semibold text-gray-900">
 {{ $title }}
 </h3>
 @endif

 @if($closeable)
 <button
 type="button"
 @click="show = false"
 class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500/50"
 aria-label="Fermer"
 >
 <x-iconify icon="heroicons:x-mark" class="w-5 h-5" />
 </button>
 @endif
 </div>
 @endif

 {{-- Body --}}
 <div class="px-6 py-4">
 {{ $slot }}
 </div>
 </div>
 </div>
</div>
