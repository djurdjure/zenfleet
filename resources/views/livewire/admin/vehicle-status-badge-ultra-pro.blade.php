<div class="relative inline-block" 
     x-data="{ 
         open: false, 
         confirmModal: @entangle('showConfirmModal').live,
         styles: '',
         listStyles: '',
         direction: 'down',
         toggle() {
             this.open = !this.open;
             if (this.open) {
                 this.$nextTick(() => requestAnimationFrame(() => this.updatePosition()));
             }
         },
         close() { this.open = false; },
         selectStatus(status) { 
             this.open = false; 
             $wire.prepareStatusChange(status); 
         },
         updatePosition() {
             if (!this.$refs.trigger || !this.$refs.menu) return;
             const rect = this.$refs.trigger.getBoundingClientRect();
             const width = 288; // w-72
             let left = rect.left;
             const padding = 12;
             const maxLeft = window.innerWidth - width - padding;
             if (left > maxLeft) left = maxLeft;
             if (left < padding) left = padding;

             const menuHeight = this.$refs.menu.offsetHeight || 320;
             const spaceBelow = window.innerHeight - rect.bottom - padding;
             const spaceAbove = rect.top - padding;
             const shouldOpenUp = spaceBelow < menuHeight && spaceAbove > spaceBelow;
             this.direction = shouldOpenUp ? 'up' : 'down';

             const maxHeight = Math.max(220, shouldOpenUp ? spaceAbove : spaceBelow);
             const listMax = Math.max(160, maxHeight - 140);
             this.listStyles = `max-height: ${listMax}px; overflow-y: auto;`;

             let top = shouldOpenUp ? (rect.top - Math.min(menuHeight, maxHeight) - 8) : (rect.bottom + 12);
             if (top < padding) top = padding;
             if (top + menuHeight > window.innerHeight - padding) {
                 top = window.innerHeight - padding - menuHeight;
             }

             this.styles = `position: fixed; top: ${top}px; left: ${left}px; width: ${width}px; z-index: 80;`;
         }
     }" 
     @click.stop
     x-init="
        $watch('confirmModal', value => { if (value) open = false; });
        window.addEventListener('scroll', () => { if (open) updatePosition(); }, true);
        window.addEventListener('resize', () => { if (open) updatePosition(); });
     ">
    {{-- 🎯 Badge de Statut Ultra-Professionnel - Enterprise Grade --}}
    @if($canUpdate && count($allowedStatuses) > 0)
        <button
            x-ref="trigger"
            @click.stop="toggle"
            @click.away="close"
            type="button"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold 
                   transition-all duration-200 hover:shadow-md hover:scale-105 cursor-pointer 
                   {{ $currentEnum ? $currentEnum->badgeClasses() : 'bg-gray-100 text-gray-700 ring-1 ring-gray-200' }}
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            title="Cliquer pour modifier le statut">
            {{-- Icône du statut --}}
            @if($currentEnum)
                <x-iconify icon="{{ $currentEnum->icon() }}" class="w-3.5 h-3.5" />
            @else
                <x-iconify icon="lucide:help-circle" class="w-3.5 h-3.5" />
            @endif
            
            {{-- Label du statut --}}
            <span>{{ $currentEnum ? $currentEnum->label() : 'Non défini' }}</span>
            
            {{-- Indicateur de dropdown --}}
            <svg class="w-3 h-3 opacity-60 transition-transform duration-200"
                 :class="{ 'rotate-180': open }"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    @else
        {{-- Badge non-interactif (lecture seule) --}}
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold 
                     {{ $currentEnum ? $currentEnum->badgeClasses() : 'bg-gray-100 text-gray-700 ring-1 ring-gray-200' }}">
            @if($currentEnum)
                <x-iconify icon="{{ $currentEnum->icon() }}" class="w-3.5 h-3.5" />
            @else
                <x-iconify icon="lucide:help-circle" class="w-3.5 h-3.5" />
            @endif
            {{ $currentEnum ? $currentEnum->label() : 'Non défini' }}
            
            {{-- Indicateur d'état terminal --}}
            @if($isTerminal)
                <x-iconify icon="lucide:lock" class="w-3 h-3 opacity-60" title="État terminal" />
            @endif
        </span>
    @endif

    {{-- 🎯 Popover des Statuts Disponibles - Option A (Contextuel & Élégant) --}}
    @if($canUpdate && count($allowedStatuses) > 0)
        <template x-teleport="body">
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                :style="styles"
                @click.outside="close"
                x-ref="menu"
                :class="direction === 'up' ? 'origin-bottom-left' : 'origin-top-left'"
                class="fixed rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] bg-white ring-1 ring-black/5 z-[80] overflow-visible"
                style="display: none;">
            
            {{-- Flèche du Popover --}}
            <div class="absolute -top-2 left-6 w-4 h-4 bg-white transform rotate-45 border-t border-l border-gray-100 shadow-sm"></div>

            <div class="relative bg-white rounded-xl overflow-hidden">
                {{-- Header du popover --}}
                <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                            <x-iconify icon="lucide:git-branch" class="w-3.5 h-3.5 text-blue-500" />
                            Changer le statut
                        </span>
                        <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-full">
                            <x-iconify icon="lucide:x" class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
    
                {{-- Liste des statuts disponibles --}}
                <div class="py-2 custom-scrollbar" :style="listStyles">
                    @forelse($allowedStatuses as $status)
                        <button
                            @click.stop="selectStatus('{{ $status->value }}')"
                            type="button"
                            class="w-full flex items-center justify-between px-4 py-3 hover:bg-blue-50/50 
                                   transition-all duration-200 group focus:outline-none border-l-2 border-transparent hover:border-blue-500">
                            <div class="flex items-center gap-3">
                                {{-- Badge du statut --}}
                                <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold 
                                           {{ $status->badgeClasses() }} shadow-sm group-hover:shadow transition-all">
                                    <x-iconify icon="{{ $status->icon() }}" class="w-3.5 h-3.5" />
                                    {{ $status->label() }}
                                </span>
                            </div>
                            {{-- Flèche d'action --}}
                            <div class="w-6 h-6 rounded-full bg-transparent group-hover:bg-blue-100 flex items-center justify-center transition-all">
                                <x-iconify icon="lucide:arrow-right" 
                                          class="w-3.5 h-3.5 text-gray-300 group-hover:text-blue-600 
                                                 group-hover:translate-x-0.5 transition-all" />
                            </div>
                        </button>
                    @empty
                        <div class="px-4 py-8 text-center">
                            <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-2">
                                <x-iconify icon="lucide:lock" class="w-5 h-5 text-gray-400" />
                            </div>
                            <p class="text-sm text-gray-500 font-medium">Aucune transition possible</p>
                            <p class="text-xs text-gray-400 mt-1">Le statut actuel est terminal</p>
                        </div>
                    @endforelse
                </div>
    
                {{-- Footer avec info contextuelle --}}
                @if($currentEnum)
                    <div class="px-4 py-3 bg-gray-50/80 border-t border-gray-100 backdrop-blur-sm">
                        <div class="flex items-start gap-2.5">
                            <x-iconify icon="lucide:info" class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" />
                            <p class="text-xs text-gray-600 leading-relaxed">
                                <span class="font-medium text-gray-900">Note:</span>
                                {{ $currentEnum->description() }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
            </div>
        </template>
    @endif

    {{-- 🎯 Modal de Confirmation - Design Enterprise Ultra-Pro --}}
    <div x-show="confirmModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        {{-- Overlay avec blur effect --}}
        <div x-show="confirmModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40">
        </div>

        {{-- Modal Container --}}
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="confirmModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg z-50">
                
                {{-- Header avec gradient --}}
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <x-iconify icon="lucide:alert-circle" class="w-5 h-5" />
                            Confirmation de changement de statut
                        </h3>
                        <button @click="confirmModal = false; $wire.cancelStatusChange()"
                                class="text-white/80 hover:text-white transition-colors">
                            <x-iconify icon="lucide:x" class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5">
                    {{-- Message de confirmation --}}
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $confirmMessage }}</p>
                    </div>

                    {{-- Informations du véhicule --}}
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Véhicule:</span>
                                <p class="font-medium text-gray-900">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Immatriculation:</span>
                                <p class="font-medium text-gray-900">{{ $vehicle->registration_plate }}</p>
                            </div>
                            @if($currentEnum)
                            <div>
                                <span class="text-gray-500">Statut actuel:</span>
                                <p class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $currentEnum->badgeClasses() }}">
                                        <x-iconify icon="{{ $currentEnum->icon() }}" class="w-3 h-3" />
                                        {{ $currentEnum->label() }}
                                    </span>
                                </p>
                            </div>
                            @endif
                            @if($pendingStatusEnum)
                            <div>
                                <span class="text-gray-500">Nouveau statut:</span>
                                <p class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $pendingStatusEnum->badgeClasses() }}">
                                        <x-iconify icon="{{ $pendingStatusEnum->icon() }}" class="w-3 h-3" />
                                        {{ $pendingStatusEnum->label() }}
                                    </span>
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Avertissement pour statuts critiques --}}
                    @if($pendingStatusEnum && in_array($pendingStatusEnum->value, ['reforme', 'vendu', 'hors-service']))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                        <div class="flex items-start gap-2">
                            <x-iconify icon="lucide:alert-triangle" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                            <div>
                                <p class="text-sm font-medium text-red-800">Attention - Action critique</p>
                                <p class="text-xs text-red-600 mt-1">
                                    @if($pendingStatusEnum->value === 'reforme')
                                        Cette action est IRRÉVERSIBLE. Le véhicule sera définitivement retiré de la flotte.
                                    @elseif($pendingStatusEnum->value === 'vendu')
                                        Le véhicule sera marqué comme vendu et ne sera plus disponible.
                                    @else
                                        Le véhicule ne sera plus disponible pour les opérations normales.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Footer avec actions --}}
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                    <button wire:click="cancelStatusChange"
                            @click="confirmModal = false"
                            type="button"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 
                                   rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 
                                   transition-all duration-150">
                        <x-iconify icon="lucide:x" class="w-4 h-4" />
                        Annuler
                    </button>
                    
                    <button wire:click="confirmStatusChange"
                            type="button"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="inline-flex items-center gap-2 px-4 py-2 
                                   {{ $pendingStatusEnum && in_array($pendingStatusEnum->value, ['reforme', 'vendu']) ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500' }}
                                   text-white rounded-lg text-sm font-medium 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 
                                   transition-all duration-150 shadow-sm hover:shadow-md">
                        <span wire:loading.remove wire:target="confirmStatusChange">
                            <x-iconify icon="lucide:check" class="w-4 h-4" />
                        </span>
                        <span wire:loading wire:target="confirmStatusChange">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="confirmStatusChange">Confirmer</span>
                        <span wire:loading wire:target="confirmStatusChange">Traitement...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
