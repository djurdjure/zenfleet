<div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="kanbanBoard()" x-init="init()">
    
    @foreach($kanbanData as $statusKey => $column)
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            
            {{-- Column Header --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full {{ 
                        $statusKey === 'planned' ? 'bg-blue-500' : 
                        ($statusKey === 'in_progress' ? 'bg-orange-500' : 'bg-green-500') 
                    }}"></div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ $column['label'] }}</h3>
                    <span class="px-2 py-0.5 text-xs font-medium bg-white rounded-full border border-gray-200">
                        {{ $column['count'] }}
                    </span>
                </div>
            </div>

            {{-- Cards Container --}}
            <div 
                class="space-y-3 min-h-[200px]"
                data-status="{{ $statusKey }}"
                x-ref="column_{{ $statusKey }}">
                
                @foreach($column['operations'] as $operation)
                    <div 
                        class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200 cursor-move"
                        data-operation-id="{{ $operation->id }}"
                        draggable="true"
                        @dragstart="dragStart($event)"
                        @dragend="dragEnd($event)">
                        
                        {{-- Vehicle Info --}}
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 bg-blue-100 border border-blue-200 rounded-full flex items-center justify-center flex-shrink-0">
                                <x-iconify icon="lucide:car" class="w-4 h-4 text-blue-600" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-900 truncate">
                                    {{ $operation->vehicle?->registration_plate ?? 'Véhicule indisponible' }}
                                </div>
                                <div class="text-xs text-gray-500 truncate">
                                    @if($operation->vehicle)
                                    {{ $operation->vehicle->brand }} {{ $operation->vehicle->model }}
                                    @else
                                    Relation véhicule indisponible
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Maintenance Type --}}
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-2 h-2 rounded-full" style="background-color: {{ $operation->maintenanceType->getCategoryColor() }}"></div>
                            <div class="text-xs font-medium text-gray-700 truncate">
                                {{ $operation->maintenanceType->name }}
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="flex items-center gap-1 text-xs text-gray-500 mb-2">
                            <x-iconify icon="lucide:calendar" class="w-3 h-3" />
                            <span>{{ $operation->scheduled_date?->format('d/m/Y') ?? 'Non définie' }}</span>
                        </div>

                        {{-- Provider --}}
                        @if($operation->provider)
                            <div class="flex items-center gap-1 text-xs text-gray-500 mb-2">
                                <x-iconify icon="lucide:building" class="w-3 h-3" />
                                <span class="truncate">{{ $operation->provider->company_name ?? $operation->provider->name ?? 'Prestataire' }}</span>
                            </div>
                        @endif

                        {{-- Cost --}}
                        @if($operation->total_cost)
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                <span class="text-xs text-gray-500">Coût</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ number_format($operation->total_cost, 0, ',', ' ') }} DA
                                </span>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-2 mt-3 pt-3 border-t border-gray-100">
                            <a href="{{ route('admin.maintenance.operations.show', $operation) }}" 
                               class="text-blue-600 hover:text-blue-900" 
                               title="Voir détails">
                                <x-iconify icon="lucide:eye" class="w-4 h-4" />
                            </a>
                            <a href="{{ route('admin.maintenance.operations.edit', $operation) }}" 
                               class="text-gray-600 hover:text-gray-900" 
                               title="Modifier">
                                <x-iconify icon="lucide:pencil" class="w-4 h-4" />
                            </a>
                        </div>
                    </div>
                @endforeach

                {{-- Drop Zone Indicator --}}
                <div 
                    class="hidden border-2 border-dashed border-blue-400 bg-blue-50 rounded-lg p-4 text-center text-sm text-blue-600"
                    x-ref="dropzone_{{ $statusKey }}">
                    <x-iconify icon="lucide:move" class="w-5 h-5 mx-auto mb-1" />
                    Déposer ici
                </div>
            </div>
        </div>
    @endforeach

    {{-- JavaScript pour Drag & Drop --}}
    @push('scripts')
    <script>
        function kanbanBoard() {
            return {
                draggedCard: null,
                sourceColumn: null,
                
                init() {
                    // Setup drop zones
                    document.querySelectorAll('[data-status]').forEach(column => {
                        column.addEventListener('dragover', this.dragOver.bind(this));
                        column.addEventListener('drop', this.drop.bind(this));
                        column.addEventListener('dragleave', this.dragLeave.bind(this));
                    });
                },
                
                dragStart(event) {
                    this.draggedCard = event.target;
                    this.sourceColumn = event.target.closest('[data-status]');
                    event.target.classList.add('opacity-50');
                },
                
                dragEnd(event) {
                    event.target.classList.remove('opacity-50');
                    // Hide all dropzones
                    document.querySelectorAll('[x-ref^="dropzone_"]').forEach(zone => {
                        zone.classList.add('hidden');
                    });
                },
                
                dragOver(event) {
                    event.preventDefault();
                    const column = event.currentTarget;
                    const dropzone = column.querySelector('[x-ref^="dropzone_"]');
                    if (dropzone) {
                        dropzone.classList.remove('hidden');
                    }
                },
                
                dragLeave(event) {
                    if (event.target.hasAttribute('data-status')) {
                        const dropzone = event.target.querySelector('[x-ref^="dropzone_"]');
                        if (dropzone) {
                            dropzone.classList.add('hidden');
                        }
                    }
                },
                
                drop(event) {
                    event.preventDefault();
                    
                    const targetColumn = event.currentTarget;
                    const newStatus = targetColumn.dataset.status;
                    const operationId = this.draggedCard.dataset.operationId;
                    
                    // Vérifier si changement de colonne
                    if (this.sourceColumn !== targetColumn) {
                        // Appeler Livewire pour mettre à jour le statut
                        @this.call('moveOperation', operationId, newStatus);
                    }
                    
                    // Hide dropzone
                    const dropzone = targetColumn.querySelector('[x-ref^="dropzone_"]');
                    if (dropzone) {
                        dropzone.classList.add('hidden');
                    }
                }
            }
        }
    </script>
    @endpush
</div>
