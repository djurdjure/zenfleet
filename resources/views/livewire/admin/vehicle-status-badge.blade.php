<div class="relative inline-block" x-data="{ open: @entangle('showDropdown') }">
    {{-- Badge actuel (cliquable si permission) --}}
    @if($canUpdate && count($allowedStatuses) > 0)
        <button
            wire:click="toggleDropdown"
            type="button"
            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium transition-all hover:scale-105 hover:shadow-md cursor-pointer {{ $currentEnum ? $currentEnum->badgeClasses() : 'bg-gray-100 text-gray-800' }}"
            title="Cliquer pour changer le statut">
            @if($currentEnum)
                <i class="fas fa-{{ $currentEnum->icon() }} text-xs"></i>
                {{ $currentEnum->label() }}
            @else
                Inconnu
            @endif
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    @else
        {{-- Badge simple (non cliquable) --}}
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $currentEnum ? $currentEnum->badgeClasses() : 'bg-gray-100 text-gray-800' }}">
            @if($currentEnum)
                <i class="fas fa-{{ $currentEnum->icon() }} text-xs"></i>
                {{ $currentEnum->label() }}
            @else
                Inconnu
            @endif
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
                        class="w-full flex items-center gap-2 px-3 py-2.5 text-sm hover:bg-gray-50 transition-colors group">
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium {{ $status->badgeClasses() }} group-hover:scale-105 transition-transform">
                            <i class="fas fa-{{ $status->icon() }} text-xs"></i>
                            {{ $status->label() }}
                        </span>
                        <span class="ml-auto">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </button>
                @endforeach

                @if($currentEnum)
                    <div class="px-3 py-2 text-xs text-gray-500 border-t border-gray-200 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ $currentEnum->description() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Message si aucune transition possible --}}
    @if($canUpdate && count($allowedStatuses) === 0 && $currentEnum && !$currentEnum->isTerminal())
        <div class="text-xs text-gray-400 mt-1">
            <i class="fas fa-lock"></i> Aucune transition disponible
        </div>
    @endif

    {{-- Message si état terminal --}}
    @if($currentEnum && $currentEnum->isTerminal())
        <div class="text-xs text-gray-400 mt-1">
            <i class="fas fa-ban"></i> État terminal
        </div>
    @endif
</div>
