<div class="relative inline-block"
     x-data="{
        open: @entangle('showDropdown'),
        styles: '',
        direction: 'down',
        align: 'left',
        updatePosition() {
            if (!this.$refs.trigger || !this.$refs.menu) return;
            const rect = this.$refs.trigger.getBoundingClientRect();
            const menuHeight = this.$refs.menu.offsetHeight || 240;
            const menuWidth = this.$refs.menu.offsetWidth || 224;
            const padding = 12;
            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top;
            const shouldOpenUp = spaceBelow < menuHeight && spaceAbove > spaceBelow;
            this.direction = shouldOpenUp ? 'up' : 'down';
            let top = shouldOpenUp ? (rect.top - menuHeight - 8) : (rect.bottom + 8);
            if (top + menuHeight > window.innerHeight - padding) {
                top = window.innerHeight - padding - menuHeight;
            }
            if (top < padding) top = padding;
            let left = this.align === 'left' ? rect.left : (rect.right - menuWidth);
            if (left + menuWidth > window.innerWidth - padding) {
                left = window.innerWidth - padding - menuWidth;
            }
            if (left < padding) left = padding;
            this.styles = `position: fixed; top: ${top}px; left: ${left}px; width: ${menuWidth}px; z-index: 9999;`;
        },
        close() { this.open = false; }
     }"
     x-init="$watch('open', value => {
        if (value) {
            $nextTick(() => {
                this.updatePosition();
                requestAnimationFrame(() => this.updatePosition());
            });
        }
     })"
     @keydown.escape.window="close()"
     @scroll.window="open && updatePosition()"
     @resize.window="open && updatePosition()">
    {{-- Badge de statut professionnel - Enterprise Grade --}}
    @if($canUpdate && count($allowedStatuses) > 0)
        <button
            wire:click="toggleDropdown"
            type="button"
            x-ref="trigger"
            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium transition-all hover:shadow-sm hover:scale-105 cursor-pointer {{ $currentEnum ? $currentEnum->badgeClasses() : 'bg-gray-100 text-gray-700' }}"
            title="Cliquer pour changer le statut">
            <span>{{ $currentEnum ? $currentEnum->label() : 'Inconnu' }}</span>
            <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    @else
        {{-- Badge simple (non cliquable) - Style professionnel --}}
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $currentEnum ? $currentEnum->badgeClasses() : 'bg-gray-100 text-gray-700' }}">
            {{ $currentEnum ? $currentEnum->label() : 'Inconnu' }}
        </span>
    @endif

    {{-- Dropdown des statuts autorisés --}}
    @if($canUpdate && count($allowedStatuses) > 0)
        <template x-teleport="body">
            <div
                x-show="open"
                x-ref="menu"
                @click.outside="close()"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                :style="styles"
                class="rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-[9999]"
                x-cloak>

            <div class="py-2">
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-200">
                    Changer vers :
                </div>

                @foreach($allowedStatuses as $status)
                    <button
                        wire:click="changeStatus('{{ $status->value }}')"
                        type="button"
                        class="w-full flex items-center justify-between px-4 py-2.5 text-sm hover:bg-gray-50 transition-all duration-200 group">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $status->badgeClasses() }} group-hover:shadow-sm transition-all">
                            {{ $status->label() }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @endforeach

                @if($currentEnum)
                    <div class="px-3 py-2 text-xs text-gray-500 border-t border-gray-200 mt-2 flex items-center gap-1">
                        <x-iconify icon="lucide:info" class="w-3 h-3" />
                        {{ $currentEnum->description() }}
                    </div>
                @endif
            </div>
        </div>
        </template>
    @endif

    {{-- Message si aucune transition possible --}}
    @if($canUpdate && count($allowedStatuses) === 0 && $currentEnum && !$currentEnum->isTerminal())
        <div class="text-xs text-gray-400 mt-1 flex items-center gap-1">
            <x-iconify icon="lucide:lock" class="w-3 h-3" /> Aucune transition disponible
        </div>
    @endif

    {{-- Message si état terminal --}}
    @if($currentEnum && $currentEnum->isTerminal())
        <div class="text-xs text-gray-400 mt-1 flex items-center gap-1">
            <x-iconify icon="lucide:ban" class="w-3 h-3" /> État terminal
        </div>
    @endif
</div>
