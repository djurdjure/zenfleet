{{-- resources/views/livewire/admin/document-manager-index.blade.php --}}
{{-- 
    ⚠️ IMPORTANT LIVEWIRE 3 : Ce composant DOIT avoir UN SEUL élément racine
    Tous les enfants (contenu + modal) sont wrappés dans un seul <div>
--}}
<div>
    {{-- Header --}}
    <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate flex items-center">
                    <x-iconify icon="mdi:file-document-multiple" class="w-8 h-8 mr-3 text-blue-600" />
                    Gestion des Documents
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Gérez tous les documents de votre organisation avec recherche Full-Text PostgreSQL
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <x-button 
                    @click="$dispatch('open-modal', 'document-upload-modal')" 
                    variant="primary" 
                    icon="plus"
                    iconPosition="left">
                    Nouveau Document
                </x-button>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <x-alert type="success" dismissible class="mb-6">
            {{ session('success') }}
        </x-alert>
    @endif

    @if (session()->has('error'))
        <x-alert type="error" dismissible class="mb-6">
            {{ session('error') }}
        </x-alert>
    @endif

    {{-- Filters --}}
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Search --}}
            <div class="md:col-span-2">
                <x-input
                    wire:model.live.debounce.300ms="search"
                    name="search"
                    placeholder="Rechercher (nom, description, métadonnées)..."
                    icon="magnifying-glass"
                />
            </div>

            {{-- Category Filter --}}
            <div>
                <x-select
                    wire:model.live="categoryFilter"
                    name="categoryFilter"
                    :options="['' => 'Toutes les catégories'] + $categories->pluck('name', 'id')->toArray()"
                />
            </div>

            {{-- Status Filter --}}
            <div>
                <x-select
                    wire:model.live="statusFilter"
                    name="statusFilter"
                    :options="[
                        '' => 'Tous les statuts',
                        'draft' => 'Brouillon',
                        'validated' => 'Validé',
                        'archived' => 'Archivé',
                        'expired' => 'Expiré',
                    ]"
                />
            </div>
        </div>

        {{-- Reset Filters --}}
        @if($search || $categoryFilter || $statusFilter)
            <div class="mt-4 flex justify-end">
                <x-button wire:click="resetFilters" variant="ghost" size="sm">
                    Réinitialiser les filtres
                </x-button>
            </div>
        @endif
    </div>

    {{-- Documents Table --}}
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('original_filename')">
                            <div class="flex items-center space-x-1">
                                <span>Document</span>
                                @if($sortField === 'original_filename')
                                    <x-iconify icon="heroicons:chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Catégorie
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('issue_date')">
                            <div class="flex items-center space-x-1">
                                <span>Émission</span>
                                @if($sortField === 'issue_date')
                                    <x-iconify icon="heroicons:chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('expiry_date')">
                            <div class="flex items-center space-x-1">
                                <span>Expiration</span>
                                @if($sortField === 'expiry_date')
                                    <x-iconify icon="heroicons:chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('created_at')">
                            <div class="flex items-center space-x-1">
                                <span>Créé le</span>
                                @if($sortField === 'created_at')
                                    <x-iconify icon="heroicons:chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($documents as $document)
                        <tr class="hover:bg-gray-50 transition-colors duration-150" wire:key="document-{{ $document->id }}">
                            {{-- Document Info --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                            <x-iconify icon="mdi:file-document" class="h-6 w-6 text-blue-600" />
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 truncate max-w-xs" title="{{ $document->original_filename }}">
                                            {{ $document->original_filename }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $document->formatted_size }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Category --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-badge type="info" size="sm">
                                    {{ $document->category->name }}
                                </x-badge>
                            </td>

                            {{-- Issue Date --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $document->issue_date?->format('d/m/Y') ?? '-' }}
                            </td>

                            {{-- Expiry Date --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($document->expiry_date)
                                    <div class="flex flex-col">
                                        <span class="font-medium {{ $document->is_expired ? 'text-red-600' : ($document->is_expiring_soon ? 'text-orange-600' : 'text-gray-900') }}">
                                            {{ $document->expiry_date->format('d/m/Y') }}
                                        </span>
                                        @if($document->is_expired)
                                            <span class="text-xs text-red-600">Expiré</span>
                                        @elseif($document->is_expiring_soon)
                                            <span class="text-xs text-orange-600">Expire bientôt</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'draft' => ['type' => 'gray', 'label' => 'Brouillon'],
                                        'validated' => ['type' => 'success', 'label' => 'Validé'],
                                        'archived' => ['type' => 'warning', 'label' => 'Archivé'],
                                        'expired' => ['type' => 'error', 'label' => 'Expiré'],
                                    ];
                                    $config = $statusConfig[$document->status] ?? ['type' => 'gray', 'label' => $document->status];
                                @endphp
                                <x-badge :type="$config['type']" size="sm">
                                    {{ $config['label'] }}
                                </x-badge>
                            </td>

                            {{-- Created At --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $document->created_at->format('d/m/Y H:i') }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button 
                                    wire:click="download({{ $document->id }})"
                                    class="text-blue-600 hover:text-blue-900 inline-flex items-center"
                                    title="Télécharger">
                                    <x-iconify icon="mdi:download" class="w-5 h-5" />
                                </button>
                                
                                @if($document->status !== 'archived')
                                    <button 
                                        wire:click="archive({{ $document->id }})"
                                        wire:confirm="Êtes-vous sûr de vouloir archiver ce document ?"
                                        class="text-orange-600 hover:text-orange-900 inline-flex items-center"
                                        title="Archiver">
                                        <x-iconify icon="mdi:archive" class="w-5 h-5" />
                                    </button>
                                @endif
                                
                                @can('delete documents')
                                    <button 
                                        wire:click="delete({{ $document->id }})"
                                        wire:confirm="ATTENTION : Cette action est irréversible. Êtes-vous sûr de vouloir supprimer définitivement ce document ?"
                                        class="text-red-600 hover:text-red-900 inline-flex items-center"
                                        title="Supprimer">
                                        <x-iconify icon="mdi:delete" class="w-5 h-5" />
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <x-iconify icon="mdi:file-document-outline" class="w-16 h-16 text-gray-400 mb-4" />
                                    <p class="text-gray-500 text-lg font-medium">Aucun document trouvé</p>
                                    <p class="text-gray-400 text-sm mt-1">Essayez de modifier vos filtres ou d'ajouter un nouveau document</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($documents->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200">
                {{ $documents->links() }}
            </div>
        @endif
    </div>

    {{-- Stats Footer --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <x-iconify icon="mdi:file-document-multiple" class="h-6 w-6 text-blue-600" />
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Total Documents</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $documents->total() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include Upload Modal Component - Intégré dans le wrapper racine --}}
    @livewire('admin.document-upload-modal')
</div>
