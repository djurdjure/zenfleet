<div class="relative inline-block" x-data="{ open: @entangle('showDropdown') }">
    {{-- Badge de statut professionnel - Enterprise Grade --}}
    @if($canUpdate && count($allowedStatuses) > 0)
        <button
            wire:click="toggleDropdown"
            type="button"
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
        <div
            x-show="open"
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute left-0 mt-2 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50"
            style="display: none;">

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
