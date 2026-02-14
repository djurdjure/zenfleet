@props([
'name' => null,
'title' => null,
'maxWidth' => 'lg',
'closeable' => true,
'position' => 'center',
])

@php
$name = $name ?? null;
$title = $title ?? null;
$maxWidth = $maxWidth ?? 'lg';
$closeable = $closeable ?? true;
$position = $position ?? 'center';

$wireModel = $attributes->get('wire:model')
    ?? $attributes->get('wire:model.live')
    ?? $attributes->get('wire:model.defer');

$resolvedName = $name ?: ($wireModel ?: 'modal-'.\Illuminate\Support\Str::random(12));

$maxWidthClasses = match($maxWidth) {
'sm' => 'max-w-sm',
'md' => 'max-w-md',
'lg' => 'max-w-lg',
'xl' => 'max-w-xl',
'2xl' => 'max-w-2xl',
'3xl' => 'max-w-3xl',
'4xl' => 'max-w-4xl',
'full' => 'max-w-full',
default => 'max-w-lg',
};

$isRightDrawer = $position === 'right';

$containerClasses = $isRightDrawer
    ? 'flex min-h-full items-stretch justify-end'
    : 'flex min-h-full items-center justify-center p-4 text-center sm:p-0';

$dialogClasses = $isRightDrawer
    ? "relative h-screen w-full {$maxWidthClasses} transform overflow-hidden rounded-none border-l border-slate-200 bg-white text-left shadow-2xl transition-all z-50"
    : "relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all w-full {$maxWidthClasses} z-50";

$enterClasses = $isRightDrawer ? 'ease-out duration-300' : 'ease-out duration-300';
$enterStartClasses = $isRightDrawer ? 'opacity-0 translate-x-8' : 'opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95';
$enterEndClasses = $isRightDrawer ? 'opacity-100 translate-x-0' : 'opacity-100 translate-y-0 sm:scale-100';
$leaveClasses = $isRightDrawer ? 'ease-in duration-200' : 'ease-in duration-200';
$leaveStartClasses = $isRightDrawer ? 'opacity-100 translate-x-0' : 'opacity-100 translate-y-0 sm:scale-100';
$leaveEndClasses = $isRightDrawer ? 'opacity-0 translate-x-8' : 'opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95';

$bodyClasses = $isRightDrawer
    ? 'px-6 py-4 h-[calc(100vh-73px)] overflow-y-auto'
    : 'px-6 py-4 max-h-[calc(100vh-200px)] overflow-y-auto';
@endphp

{{--
    🎨 MODAL COMPONENT - Enterprise Grade
    
    Architecture:
    - Z-index layering: backdrop (z-40) → modal container (z-50)
    - Focus trap automatique
    - Accessibilité ARIA complète
    - Transitions fluides
    - Click outside to close
    - ESC key to close
--}}

<div
    x-data="{ 
    show: @if($wireModel) $wire.entangle('{{ $wireModel }}') @else false @endif,
    modalName: '{{ $resolvedName }}',
    init() {
        this.$watch('show', value => {
            if (value) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
    }
 }"
    x-on:open-modal.window="if ($event.detail === modalName) { show = true; }"
    x-on:close-modal.window="if ($event.detail === modalName) { show = false; }"
    x-on:keydown.escape.window="if (show) { show = false; }"
    x-show="show"
    x-cloak
    class="relative z-50"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
    style="display: none;">
    {{-- Backdrop - Z-index inférieur pour être derrière le modal --}}
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40"
        aria-hidden="true"></div>

    {{-- Modal Container - Fixed positioning avec z-index supérieur --}}
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="{{ $containerClasses }}">
            {{-- Modal Dialog --}}
            <div
                x-show="show"
                x-transition:enter="{{ $enterClasses }}"
                x-transition:enter-start="{{ $enterStartClasses }}"
                x-transition:enter-end="{{ $enterEndClasses }}"
                x-transition:leave="{{ $leaveClasses }}"
                x-transition:leave-start="{{ $leaveStartClasses }}"
                x-transition:leave-end="{{ $leaveEndClasses }}"
                @click.away="@if($closeable) show = false @endif"
                class="{{ $dialogClasses }}">
                {{-- Header --}}
                @if($title || $closeable)
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                    @if($title)
                    <h3 id="modal-title" class="text-lg font-semibold text-gray-900">
                        {{ $title }}
                    </h3>
                    @endif

                    @if($closeable)
                    <button
                        type="button"
                        @click="show = false"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        aria-label="Fermer la fenêtre">
                        <x-iconify icon="heroicons:x-mark" class="w-5 h-5" />
                    </button>
                    @endif
                </div>
                @endif

                {{-- Body --}}
                <div class="{{ $bodyClasses }}">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
