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

$widthPx = match($width) {
 '48' => 192,
 '56' => 224,
 '64' => 256,
 '72' => 288,
 '80' => 320,
 '96' => 384,
 default => 240
};

$dropdownId = 'dropdown-' . uniqid();
@endphp

<div class="relative {{ $dropdownClasses }}" 
 x-data="{
     open: false,
     id: '{{ $dropdownId }}',
     styles: '',
     direction: 'down',
     toggle() {
         this.open = !this.open;
         if (this.open) {
             this.$nextTick(() => requestAnimationFrame(() => this.updatePosition()));
         }
     },
     close() { this.open = false; },
     updatePosition() {
         if (!this.$refs.trigger || !this.$refs.menu) return;
         const rect = this.$refs.trigger.getBoundingClientRect();
         const width = {{ $widthPx }};
         const padding = 12;
         const menuHeight = this.$refs.menu.offsetHeight || 220;
         const spaceBelow = window.innerHeight - rect.bottom - padding;
         const spaceAbove = rect.top - padding;
         const shouldOpenUp = spaceBelow < menuHeight && spaceAbove > spaceBelow;
         this.direction = shouldOpenUp ? 'up' : 'down';

         let top = shouldOpenUp ? (rect.top - menuHeight - 8) : (rect.bottom + 8);
         if (top < padding) top = padding;
         if (top + menuHeight > window.innerHeight - padding) {
             top = window.innerHeight - padding - menuHeight;
         }

         let left = rect.left;
         if ('{{ $align }}' === 'right') {
             left = rect.right - width;
         }
         if ('{{ $align }}' === 'left') {
             left = rect.left;
         }
         const maxLeft = window.innerWidth - width - padding;
         if (left > maxLeft) left = maxLeft;
         if (left < padding) left = padding;

         this.styles = `position: fixed; top: ${top}px; left: ${left}px; width: ${width}px; z-index: 80;`;
     }
 }"
 x-on:close.stop="close()"
 x-on:keydown.escape.window="close()"
 x-init="
    window.addEventListener('scroll', () => { if (open) updatePosition(); }, true);
    window.addEventListener('resize', () => { if (open) updatePosition(); });
 ">
 
 {{-- Trigger --}}
 <div x-ref="trigger"
 x-on:click="toggle" 
 class="cursor-pointer"
 :aria-expanded="open"
 :aria-controls="id"
 role="button"
 tabindex="0"
 x-on:keydown.enter="toggle()"
 x-on:keydown.space.prevent="toggle()">
 {{ $trigger ?? $slot }}
 </div>

 {{-- Dropdown Content --}}
 <template x-teleport="body">
 <div x-show="open"
 :id="id"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 scale-95"
 x-transition:enter-end="opacity-100 scale-100"
 x-transition:leave="transition ease-in duration-75"
 x-transition:leave-start="opacity-100 scale-100"
 x-transition:leave-end="opacity-0 scale-95"
 :style="styles"
 @click.outside="close()"
 x-ref="menu"
 :class="direction === 'up' ? 'origin-bottom-left' : 'origin-top-left'"
 class="fixed rounded-md shadow-lg {{ $alignmentClasses }} z-[80]"
 style="display: none;"
 x-on:click="close()"
 role="menu"
 aria-orientation="vertical">
 
 <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
 {{ $content ?? '' }}
 </div>
 </div>
 </template>
</div>
