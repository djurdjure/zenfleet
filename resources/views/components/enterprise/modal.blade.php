{{-- Composant Modal Enterprise Ultra-Moderne --}}
@props([
'name' => '',
'show' => false,
'maxWidth' => '2xl',
'closeable' => true,
'closeOnEscape' => true,
'closeOnClick' => true,
'title' => null,
'description' => null,
'icon' => null,
'variant' => 'default', // default, success, danger, warning, info
])

@php
$maxWidthClass = [
'sm' => 'sm:max-w-sm',
'md' => 'sm:max-w-md',
'lg' => 'sm:max-w-lg',
'xl' => 'sm:max-w-xl',
'2xl' => 'sm:max-w-2xl',
'3xl' => 'sm:max-w-3xl',
'4xl' => 'sm:max-w-4xl',
'5xl' => 'sm:max-w-5xl',
'6xl' => 'sm:max-w-6xl',
'7xl' => 'sm:max-w-7xl',
'full' => 'sm:max-w-full',
][$maxWidth ?? '2xl'];

$variantColors = [
'default' => 'from-gray-50 to-white border-gray-200',
'success' => 'from-success-50 to-white border-success-200',
'danger' => 'from-danger-50 to-white border-danger-200',
'warning' => 'from-warning-50 to-white border-warning-200',
'info' => 'from-info-50 to-white border-info-200',
];

$variantIcons = [
'success' => 'text-success-600 bg-success-100',
'danger' => 'text-danger-600 bg-danger-100',
'warning' => 'text-warning-600 bg-warning-100',
'info' => 'text-info-600 bg-info-100',
];
@endphp

<div
    x-data="{
 show: @js($show),
 focusables() {
 // All focusable element types...
 let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
 return [...$el.querySelectorAll(selector)]
 .filter(el => !el.hasAttribute('disabled'))
 },
 firstFocusable() { return this.focusables()[0] },
 lastFocusable() { return this.focusables().slice(-1)[0] },
 nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
 prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
 nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
 prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
 }"
    x-init="$watch('show', value => {
 if (value) {
 document.body.classList.add('overflow-y-hidden');
 setTimeout(() => firstFocusable()?.focus(), 100);
 } else {
 document.body.classList.remove('overflow-y-hidden');
 }
 })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    @if($closeOnEscape) x-on:keydown.escape.window="show = false" @endif
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable()?.focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable()?.focus()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: {{ $show ? 'block' : 'none' }};">
    {{-- Backdrop avec blur --}}
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-40"
        @if($closeOnClick) @click="show = false" @endif></div>

    {{-- Modal Container --}}
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative z-50 transform transition-all sm:w-full {{ $maxWidthClass }} sm:mx-auto">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden relative z-50">
            {{-- Header avec gradient subtil --}}
            @if($title || $closeable)
            <div class="bg-gradient-to-r {{ $variantColors[$variant] }} px-6 py-4 border-b">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-3">
                        {{-- Icon du modal --}}
                        @if($icon)
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $variantIcons[$variant] ?? 'bg-gray-100 border border-gray-200 text-gray-600' }}">
                                {!! $icon !!}
                            </div>
                        </div>
                        @endif

                        <div>
                            @if($title)
                            <h3 class="text-lg font-semibold text-gray-900 leading-6">
                                {{ $title }}
                            </h3>
                            @endif

                            @if($description)
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $description }}
                            </p>
                            @endif
                        </div>
                    </div>

                    {{-- Bouton fermer --}}
                    @if($closeable)
                    <button
                        @click="show = false"
                        class="ml-4 flex-shrink-0 inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
            @endif

            {{-- Body avec scroll personnalisé --}}
            <div class="px-6 py-6 max-h-[calc(100vh-200px)] overflow-y-auto custom-scrollbar">
                {{ $slot }}
            </div>

            {{-- Footer (optionnel) --}}
            @if(isset($footer))
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $footer }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Scrollbar personnalisée pour le modal */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #9ca3af;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }
</style>
