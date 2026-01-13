<div x-data="vehicleBulkActions()"
    x-init="init()"
    @keydown.window="handleKeyboard($event)"
    class="relative">

    {{-- ====================================================================
         🚀 MENU FLOTTANT D'ACTIONS BULK - ENTERPRISE ULTRA PRO
         ====================================================================
         Design inspiré de Notion/Linear avec animations fluides
         Position sticky intelligente avec détection de viewport
         ==================================================================== --}}

    <div x-show="selectedCount > 0"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 transform translate-y-4"
        :class="menuSticky ? 'fixed' : 'absolute'"
        :style="menuPosition"
        class="z-50 bg-white text-gray-900 rounded-2xl shadow-2xl px-4 py-3 
                backdrop-blur-xl bg-opacity-95 border border-gray-200
                max-w-4xl w-auto min-w-[400px]"
        @click.away="showActionMenu = false">

        <div class="flex items-center justify-between gap-4">
            {{-- Section gauche : Compteur et sélection --}}
            <div class="flex items-center gap-3">
                {{-- Badge de sélection animé --}}
                <div class="relative">
                    <div class="absolute inset-0 bg-blue-500 blur-lg opacity-50 animate-pulse"></div>
                    <div class="relative bg-blue-500 text-white px-3 py-1.5 rounded-lg font-bold text-sm">
                        <span x-text="selectedCount"></span>
                        <span class="ml-1 text-blue-100">sélectionné(s)</span>
                    </div>
                </div>

                {{-- Options de sélection rapide --}}
                <div class="flex items-center gap-1 border-l border-gray-700 pl-3">
                    <button @click="selectAllOnPage = !selectAllOnPage; $wire.selectAllVehicles()"
                        class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                        :class="selectAllOnPage ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' : ''">
                        <span x-show="!selectAllOnPage">Sélectionner page</span>
                        <span x-show="selectAllOnPage">Page sélectionnée</span>
                    </button>

                    <button @click="selectAll = !selectAll; $wire.selectAllVehicles()"
                        class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                        :class="selectAll ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' : ''">
                        <span x-show="!selectAll">Tout sélectionner ({{ $totalVehicles }})</span>
                        <span x-show="selectAll">Tout désélectionné</span>
                    </button>
                </div>
            </div>

            {{-- Section centrale : Actions bulk --}}
            <div class="flex items-center gap-2">
                @foreach($bulkActions as $action => $config)
                @can($action === 'delete' ? 'delete vehicles' : 'edit vehicles')
                <div class="relative" x-data="{ showSubmenu: false }">
                    <button @click="showSubmenu = !showSubmenu"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="group relative flex items-center gap-2 px-3 py-1.5 
                                   bg-{{ $config['color'] }}-500 bg-opacity-20 
                                   hover:bg-opacity-30 rounded-lg transition-all duration-200
                                   hover:scale-105 active:scale-95">

                        {{-- Icône avec animation --}}
                        <x-dynamic-component
                            :component="'iconify'"
                            :icon="$config['icon']"
                            class="w-4 h-4 text-{{ $config['color'] }}-400 
                                   group-hover:text-{{ $config['color'] }}-300 
                                   transition-colors" />

                        {{-- Label --}}
                        <span class="text-xs font-medium text-gray-600 
                                     group-hover:text-gray-900 transition-colors">
                            {{ $config['label'] }}
                        </span>

                        {{-- Badge de notification (optionnel) --}}
                        @if($action === 'export')
                        <span class="absolute -top-1 -right-1 bg-green-500 text-white 
                                     text-[10px] px-1.5 py-0.5 rounded-full font-bold">
                            NEW
                        </span>
                        @endif
                    </button>

                    {{-- Sous-menu contextuel pour certaines actions --}}
                    @if(in_array($action, ['change_status', 'assign_depot', 'export']))
                    <div x-show="showSubmenu"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        @click.away="showSubmenu = false"
                        class="absolute bottom-full mb-2 left-0 bg-white rounded-lg 
                                shadow-xl border border-gray-200 py-2 min-w-[200px]">

                        @if($action === 'change_status')
                        @foreach($statuses as $status)
                        <button wire:click="executeBulkAction('change_status', {{ $status->id }})"
                            class="w-full text-left px-3 py-2 text-sm text-gray-600 
                                           hover:bg-gray-50 hover:text-gray-900 transition-colors
                                           flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-{{ $status->color ?? 'gray' }}-500"></span>
                            {{ $status->name }}
                        </button>
                        @endforeach
                        @endif

                        @if($action === 'assign_depot')
                        @foreach($depots as $depot)
                        <button wire:click="executeBulkAction('assign_depot', {{ $depot->id }})"
                            class="w-full text-left px-3 py-2 text-sm text-gray-600 
                                           hover:bg-gray-50 hover:text-gray-900 transition-colors">
                            <x-iconify icon="lucide:map-pin" class="w-3 h-3 inline mr-2" />
                            {{ $depot->name }}
                        </button>
                        @endforeach
                        @endif

                        @if($action === 'export')
                        <button wire:click="executeBulkAction('export', ['format' => 'excel'])"
                            class="w-full text-left px-3 py-2 text-sm text-gray-600 
                                           hover:bg-gray-50 hover:text-gray-900 transition-colors">
                            <x-iconify icon="lucide:file-spreadsheet" class="w-3 h-3 inline mr-2" />
                            Export Excel
                        </button>
                        <button wire:click="executeBulkAction('export', ['format' => 'csv'])"
                            class="w-full text-left px-3 py-2 text-sm text-gray-600 
                                           hover:bg-gray-50 hover:text-gray-900 transition-colors">
                            <x-iconify icon="lucide:file-text" class="w-3 h-3 inline mr-2" />
                            Export CSV
                        </button>
                        <button wire:click="executeBulkAction('export', ['format' => 'pdf'])"
                            class="w-full text-left px-3 py-2 text-sm text-gray-600 
                                           hover:bg-gray-50 hover:text-gray-900 transition-colors">
                            <x-iconify icon="lucide:file" class="w-3 h-3 inline mr-2" />
                            Export PDF
                        </button>
                        @endif
                    </div>
                    @endif
                </div>
                @endcan
                @endforeach
            </div>

            {{-- Section droite : Actions supplémentaires --}}
            <div class="flex items-center gap-2 border-l border-gray-200 pl-3">
                {{-- Undo/Redo --}}
                <button @click="$wire.undo()"
                    :disabled="historyPointer < 0"
                    class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 
                               disabled:opacity-50 disabled:cursor-not-allowed transition-all
                               hover:scale-110 active:scale-95"
                    title="Annuler (Ctrl+Z)">
                    <x-iconify icon="lucide:undo-2" class="w-4 h-4 text-gray-600" />
                </button>

                <button @click="$wire.redo()"
                    :disabled="historyPointer >= actionHistory.length - 1"
                    class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 
                               disabled:opacity-50 disabled:cursor-not-allowed transition-all
                               hover:scale-110 active:scale-95"
                    title="Refaire (Ctrl+Y)">
                    <x-iconify icon="lucide:redo-2" class="w-4 h-4 text-gray-600" />
                </button>

                {{-- Clear selection --}}
                <button @click="$wire.clearSelection()"
                    class="p-1.5 rounded-lg bg-red-500 bg-opacity-20 hover:bg-opacity-30 
                               transition-all hover:scale-110 active:scale-95"
                    title="Effacer sélection (Esc)">
                    <x-iconify icon="lucide:x" class="w-4 h-4 text-red-500" />
                </button>
            </div>
        </div>

        {{-- Barre de progression pour les actions bulk --}}
        <div x-show="$wire.isProcessing"
            x-transition
            class="mt-3 bg-gray-100 rounded-lg p-2">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-gray-600" x-text="$wire.progressMessage"></span>
                <span class="text-xs text-blue-400 font-mono" x-text="Math.round($wire.progress) + '%'"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-full rounded-full 
                            transition-all duration-300 ease-out"
                    :style="'width: ' + $wire.progress + '%'">
                    <div class="h-full bg-white bg-opacity-30 animate-pulse"></div>
                </div>
            </div>
        </div>

        {{-- Indicateur de collaboration temps réel --}}
        @if($realtimeEnabled && count($collaborators) > 0)
        <div class="mt-2 flex items-center gap-2">
            <span class="text-[10px] text-gray-500">Autres utilisateurs:</span>
            <div class="flex -space-x-2">
                @foreach($collaborators as $collaborator)
                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 
                            border-2 border-gray-900 flex items-center justify-center"
                    title="{{ $collaborator['name'] }}">
                    <span class="text-[10px] font-bold text-white">
                        {{ substr($collaborator['name'], 0, 1) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Notification toast pour les actions --}}
    <div x-data="{ notifications: [] }"
        @notify.window="
            notifications.push($event.detail);
            setTimeout(() => notifications.shift(), $event.detail.duration || 3000)
         "
        class="fixed top-4 right-4 z-50 space-y-2">
        <template x-for="(notification, index) in notifications" :key="index">
            <div x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-full"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0 transform translate-x-full"
                class="bg-white text-gray-900 rounded-lg shadow-xl px-4 py-3 min-w-[300px]
                        border-l-4 border border-gray-100"
                :class="{
                    'border-green-500': notification.type === 'success',
                    'border-red-500': notification.type === 'error',
                    'border-yellow-500': notification.type === 'warning',
                    'border-blue-500': notification.type === 'info'
                 }">
                <div class="flex items-start gap-3">
                    <x-dynamic-component
                        x-show="notification.type === 'success'"
                        component="iconify"
                        icon="lucide:check-circle"
                        class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" />
                    <x-dynamic-component
                        x-show="notification.type === 'error'"
                        component="iconify"
                        icon="lucide:x-circle"
                        class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" />
                    <div class="flex-1">
                        <p class="text-sm font-medium" x-text="notification.message"></p>
                        <p x-show="notification.details"
                            class="text-xs text-gray-500 mt-1"
                            x-text="notification.details"></p>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    function vehicleBulkActions() {
        return {
            selectedCount: @entangle('selectedCount').defer,
            selectAll: @entangle('selectAll').defer,
            selectAllOnPage: @entangle('selectAllOnPage').defer,
            showActionMenu: false,
            menuSticky: true,
            menuPosition: {},
            isProcessing: @entangle('isProcessing'),
            progress: @entangle('progress'),
            historyPointer: @entangle('historyPointer'),
            actionHistory: @entangle('actionHistory'),
            lastSelectedIndex: null,

            init() {
                this.calculateMenuPosition();
                window.addEventListener('scroll', () => this.handleScroll());
                window.addEventListener('resize', () => this.calculateMenuPosition());

                // WebSocket pour collaboration temps réel
                if (window.Echo) {
                    window.Echo.channel(`vehicles.${window.organizationId}`)
                        .listen('VehiclesBulkUpdated', (e) => {
                            this.$wire.refreshVehicles();
                        });
                }
            },

            handleKeyboard(event) {
                // Ctrl+A pour tout sélectionner
                if (event.ctrlKey && event.key === 'a') {
                    event.preventDefault();
                    this.selectAll = true;
                    this.$wire.selectAllVehicles();
                }

                // Escape pour effacer la sélection
                if (event.key === 'Escape') {
                    this.$wire.clearSelection();
                }

                // Ctrl+Z pour undo
                if (event.ctrlKey && event.key === 'z') {
                    event.preventDefault();
                    this.$wire.undo();
                }

                // Ctrl+Y pour redo
                if (event.ctrlKey && event.key === 'y') {
                    event.preventDefault();
                    this.$wire.redo();
                }
            },

            selectVehicle(id, event) {
                // Multi-sélection avec Shift
                if (event.shiftKey && this.lastSelectedIndex !== null) {
                    this.$wire.selectRange(this.lastSelectedIndex, id);
                }
                // Ajout/retrait avec Ctrl
                else if (event.ctrlKey || event.metaKey) {
                    this.$wire.toggleSelection(id, false, true);
                }
                // Sélection simple
                else {
                    this.$wire.toggleSelection(id);
                }

                this.lastSelectedIndex = id;
            },

            calculateMenuPosition() {
                const viewport = {
                    width: window.innerWidth,
                    height: window.innerHeight
                };

                // Position centrée en bas avec offset
                this.menuPosition = {
                    bottom: '20px',
                    left: '50%',
                    transform: 'translateX(-50%)',
                    maxWidth: Math.min(viewport.width - 40, 1200) + 'px'
                };

                // Mode sticky si scroll > 100px
                this.menuSticky = window.scrollY > 100;
            },

            handleScroll() {
                this.menuSticky = window.scrollY > 100;
            }
        };
    }
</script>