{{-- resources/views/livewire/entity/document-list.blade.php --}}
<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-iconify icon="mdi:file-document-multiple" class="w-6 h-6 mr-2 text-blue-600" />
            Documents attachés
            @if($documents->count() > 0)
                <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $documents->count() }}
                </span>
            @endif
        </h3>

        @if($showAddButton)
            <x-button 
                wire:click="openUploadModal" 
                variant="primary" 
                size="sm" 
                icon="plus"
                iconPosition="left">
                Ajouter
            </x-button>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <x-alert type="success" dismissible size="sm">
            {{ session('success') }}
        </x-alert>
    @endif

    @if (session()->has('error'))
        <x-alert type="error" dismissible size="sm">
            {{ session('error') }}
        </x-alert>
    @endif

    {{-- Documents List --}}
    @if($documents->count() > 0)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 divide-y divide-gray-200">
            @foreach($documents as $document)
                <div class="p-4 hover:bg-gray-50 transition-colors duration-150" wire:key="doc-{{ $document->id }}">
                    <div class="flex items-start justify-between">
                        {{-- Document Info --}}
                        <div class="flex items-start space-x-3 flex-1 min-w-0">
                            {{-- Icon --}}
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <x-iconify icon="mdi:file-document" class="h-6 w-6 text-blue-600" />
                                </div>
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-1">
                                    <p class="text-sm font-medium text-gray-900 truncate" title="{{ $document->original_filename }}">
                                        {{ $document->original_filename }}
                                    </p>
                                    <x-badge type="info" size="sm">
                                        {{ $document->category->name }}
                                    </x-badge>
                                </div>

                                <div class="flex items-center space-x-3 text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <x-iconify icon="mdi:file" class="w-3 h-3 mr-1" />
                                        {{ $document->formatted_size }}
                                    </span>
                                    
                                    @if($document->issue_date)
                                        <span class="flex items-center">
                                            <x-iconify icon="mdi:calendar" class="w-3 h-3 mr-1" />
                                            {{ $document->issue_date->format('d/m/Y') }}
                                        </span>
                                    @endif

                                    @if($document->expiry_date)
                                        <span class="flex items-center {{ $document->is_expired ? 'text-red-600 font-medium' : ($document->is_expiring_soon ? 'text-orange-600 font-medium' : '') }}">
                                            <x-iconify icon="mdi:calendar-alert" class="w-3 h-3 mr-1" />
                                            Exp: {{ $document->expiry_date->format('d/m/Y') }}
                                            @if($document->is_expired)
                                                (Expiré)
                                            @elseif($document->is_expiring_soon)
                                                (Expire bientôt)
                                            @endif
                                        </span>
                                    @endif

                                    <span class="flex items-center">
                                        <x-iconify icon="mdi:account" class="w-3 h-3 mr-1" />
                                        {{ $document->uploader->name }}
                                    </span>
                                </div>

                                @if($document->description)
                                    <p class="mt-1 text-xs text-gray-600 line-clamp-2">
                                        {{ $document->description }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        @if($showActions)
                            <div class="flex-shrink-0 ml-4 flex items-center space-x-2">
                                {{-- Download --}}
                                <button 
                                    wire:click="download({{ $document->id }})"
                                    class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50"
                                    title="Télécharger">
                                    <x-iconify icon="mdi:download" class="w-5 h-5" />
                                </button>
                                
                                {{-- Detach --}}
                                <button 
                                    wire:click="detach({{ $document->id }})"
                                    wire:confirm="Êtes-vous sûr de vouloir détacher ce document ?"
                                    class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50"
                                    title="Détacher">
                                    <x-iconify icon="mdi:link-variant-off" class="w-5 h-5" />
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 p-8 text-center">
            <x-iconify icon="mdi:file-document-outline" class="mx-auto h-12 w-12 text-gray-400 mb-3" />
            <h4 class="text-sm font-medium text-gray-900 mb-1">Aucun document attaché</h4>
            <p class="text-sm text-gray-500 mb-4">Commencez par ajouter un document à cette entité</p>
            
            @if($showAddButton)
                <x-button 
                    wire:click="openUploadModal" 
                    variant="primary" 
                    size="sm" 
                    icon="plus"
                    iconPosition="left">
                    Ajouter le premier document
                </x-button>
            @endif
        </div>
    @endif
</div>
