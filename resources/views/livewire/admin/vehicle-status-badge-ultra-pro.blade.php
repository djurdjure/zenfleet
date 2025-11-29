<div class="relative inline-block" x-data="statusBadgeComponent()" wire:ignore.self>
    {{-- 🎯 Badge de Statut Ultra-Professionnel - Enterprise Grade --}}
    @if($canUpdate && count($allowedStatuses) > 0)
        <button
            wire:click="toggleDropdown"
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

    {{-- 🎯 Dropdown des Statuts Disponibles - Design Premium --}}
    @if($canUpdate && count($allowedStatuses) > 0)
        <div
            x-show="open"
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute left-0 mt-2 w-64 rounded-xl shadow-2xl bg-white ring-1 ring-black ring-opacity-5 z-50 overflow-hidden"
            style="display: none;">

            {{-- Header du dropdown --}}
            <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                        Changer le statut
                    </span>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <x-iconify icon="lucide:x" class="w-4 h-4" />
                    </button>
                </div>
            </div>

            {{-- Liste des statuts disponibles --}}
            <div class="py-2 max-h-64 overflow-y-auto">
                @forelse($allowedStatuses as $status)
                    <button
                        wire:click="prepareStatusChange('{{ $status->value }}')"
                        type="button"
                        class="w-full flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 
                               transition-all duration-150 group focus:outline-none focus:bg-gray-50">
                        <div class="flex items-center gap-3">
                            {{-- Badge du statut --}}
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium 
                                       {{ $status->badgeClasses() }} group-hover:shadow-sm transition-all">
                                <x-iconify icon="{{ $status->icon() }}" class="w-3 h-3" />
                                {{ $status->label() }}
                            </span>
                        </div>
                        {{-- Flèche d'action --}}
                        <x-iconify icon="lucide:chevron-right" 
                                  class="w-4 h-4 text-gray-400 group-hover:text-blue-600 
                                         group-hover:translate-x-1 transition-all" />
                    </button>
                @empty
                    <div class="px-4 py-3 text-center text-sm text-gray-500">
                        Aucune transition disponible
                    </div>
                @endforelse
            </div>

            {{-- Footer avec info contextuelle --}}
            @if($currentEnum)
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-start gap-2">
                        <x-iconify icon="lucide:info-circle" class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" />
                        <p class="text-xs text-gray-600 leading-relaxed">
                            {{ $currentEnum->description() }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
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
             class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity">
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
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
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

@push('scripts')
<script>
/**
 * 🎯 COMPOSANT ALPINE.JS POUR VEHICLE STATUS BADGE ULTRA PRO
 * Version: Enterprise-Grade - Compatible Livewire 3
 *
 * CORRECTION: Utilise wire:model et événements Livewire au lieu de entangle()
 * pour éviter les erreurs "Cannot read properties of undefined"
 */
function statusBadgeComponent() {
    return {
        open: @json($showDropdown),
        confirmModal: @json($showConfirmModal),
        componentId: '{{ $this->getId() }}',

        init() {
            const component = this;

            // Écouter les changements Livewire - Alpine vers Livewire
            this.$watch('open', value => {
                component.$wire.set('showDropdown', value, false);
            });

            this.$watch('confirmModal', value => {
                component.$wire.set('showConfirmModal', value, false);
            });

            // Écouter les mises à jour depuis Livewire - Livewire vers Alpine
            Livewire.hook('morph.updated', ({ el, component: livewireComponent }) => {
                if (livewireComponent.id === component.componentId) {
                    component.open = livewireComponent.get('showDropdown');
                    component.confirmModal = livewireComponent.get('showConfirmModal');
                }
            });
        }
    }
}
</script>
@endpush
