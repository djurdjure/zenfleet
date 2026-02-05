<div>
    {{--
        Single Root Element enforced for Livewire 3
        Design System: Matches admin/drivers/driver-index.blade.php
    --}}
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- ===============================================
            HEADER
        =============================================== --}}
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:file-text" class="w-6 h-6 text-blue-600" />
                Gestion des Documents
                <span class="ml-2 text-sm font-normal text-gray-500">
                    ({{ $documents->total() }})
                </span>
            </h1>

            <div wire:loading class="flex items-center gap-2 text-blue-600">
                <x-iconify icon="lucide:loader-2" class="w-5 h-5 animate-spin" />
                <span class="text-sm font-medium">Chargement...</span>
            </div>
        </div>

        {{-- ===============================================
            STATS GRID
        =============================================== --}}
        <x-page-analytics-grid columns="4">
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Documents</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 border border-blue-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:files" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <div class="bg-green-50 rounded-lg border border-green-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Validés</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['validated'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 border border-green-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:check-circle" class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Brouillons</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['draft'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:file-edit" class="w-6 h-6 text-gray-600" />
                    </div>
                </div>
            </div>

            <div class="bg-amber-50 rounded-lg border border-amber-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Archivés</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['archived'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 border border-amber-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:archive" class="w-6 h-6 text-amber-600" />
                    </div>
                </div>
            </div>
        </x-page-analytics-grid>

        {{-- ===============================================
            SEARCH & ACTIONS
        =============================================== --}}
        <x-page-search-bar x-data="{ showFilters: false }">
            <x-slot:search>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                    </div>
                    <input wire:model.live.debounce.500ms="search" type="text"
                        wire:loading.attr="aria-busy"
                        wire:target="search"
                        class="pl-10 pr-4 py-2.5 block w-full bg-white border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        placeholder="Rechercher (nom, description)...">
                    <div wire:loading.delay wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <x-iconify icon="lucide:loader-2" class="w-4 h-4 text-blue-500 animate-spin" />
                    </div>
                </div>
            </x-slot:search>

            <x-slot:filters>
                <button @click="showFilters = !showFilters" type="button"
                    class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
                    <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" x-bind:class="showFilters ? 'rotate-180' : ''" />
                </button>
            </x-slot:filters>

            <x-slot:actions>
                @can('documents.create')
                    <button wire:click="openUploadModal"
                        class="inline-flex items-center gap-2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:upload-cloud" class="w-5 h-5" />
                        <span class="hidden sm:inline">Uploader</span>
                    </button>
                @endcan
            </x-slot:actions>

            <x-slot:filtersPanel>
                <x-page-filters-panel columns="2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie</label>
                        <select wire:model.live="categoryFilter" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                        <select wire:model.live="statusFilter" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Tous les statuts</option>
                            <option value="draft">Brouillon</option>
                            <option value="validated">Validé</option>
                            <option value="archived">Archivé</option>
                        </select>
                    </div>

                    <x-slot:reset>
                        @if($search || $categoryFilter || $statusFilter)
                        <button wire:click="resetFilters" class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1 font-medium bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors">
                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                            Réinitialiser
                        </button>
                        @endif
                    </x-slot:reset>
                </x-page-filters-panel>
            </x-slot:filtersPanel>
        </x-page-search-bar>

        {{-- ===============================================
            TABLE
        =============================================== --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mt-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('original_filename')">
                                Document
                                @if($sortField === 'original_filename') <x-iconify icon="{{ $sortDirection === 'asc' ? 'lucide:arrow-up' : 'lucide:arrow-down' }}" class="w-3 h-3 inline ml-1" /> @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('issue_date')">
                                Date Émission
                                @if($sortField === 'issue_date') <x-iconify icon="{{ $sortDirection === 'asc' ? 'lucide:arrow-up' : 'lucide:arrow-down' }}" class="w-3 h-3 inline ml-1" /> @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('expiry_date')">
                                Expiration
                                @if($sortField === 'expiry_date') <x-iconify icon="{{ $sortDirection === 'asc' ? 'lucide:arrow-up' : 'lucide:arrow-down' }}" class="w-3 h-3 inline ml-1" /> @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($documents as $document)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                                        <x-iconify icon="lucide:file-text" class="w-5 h-5" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 truncate max-w-xs">{{ $document->original_filename }}</div>
                                        <div class="text-xs text-gray-500">{{ $document->formatted_size }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                    {{ $document->category->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $document->issue_date?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($document->expiry_date)
                                <span class="{{ $document->is_expired ? 'text-red-600 font-medium' : ($document->is_expiring_soon ? 'text-amber-600' : 'text-gray-500') }}">
                                    {{ $document->expiry_date->format('d/m/Y') }}
                                </span>
                                @else
                                <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                $colors = [
                                'draft' => 'bg-gray-50 text-gray-700 border border-gray-200',
                                'validated' => 'bg-green-50 text-green-700 border border-green-200',
                                'archived' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                'expired' => 'bg-red-50 text-red-700 border border-red-200',
                                ];
                                $statusLabels = [
                                'draft' => 'Brouillon',
                                'validated' => 'Validé',
                                'archived' => 'Archivé',
                                'expired' => 'Expiré',
                                ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[$document->status] ?? 'bg-gray-50 text-gray-700 border border-gray-200' }}">
                                    {{ $statusLabels[$document->status] ?? ucfirst($document->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @can('documents.download')
                                        <button wire:click="download({{ $document->id }})" class="p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Télécharger">
                                            <x-iconify icon="lucide:download" class="w-4 h-4" />
                                        </button>
                                    @endcan

                                    @can('documents.update')
                                        @if($document->status !== 'archived')
                                            <button wire:click="archive({{ $document->id }})" class="p-2 rounded-full text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-colors" title="Archiver">
                                                <x-iconify icon="lucide:archive" class="w-4 h-4" />
                                            </button>
                                        @endif
                                    @endcan

                                    @can('documents.delete')
                                    <button wire:click="delete({{ $document->id }})" wire:confirm="Êtes-vous sûr ?" class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Supprimer">
                                        <x-iconify icon="lucide:trash-2" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Aucun document trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            <x-pagination :paginator="$documents" :records-per-page="$perPage" wire:model.live="perPage" />
        </div>
    </div>

    {{-- ===============================================
        INLINE UPLOAD MODAL
    =============================================== --}}
    @if($showUploadModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="closeUploadModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full relative z-50">

                {{-- Validated Header Logic --}}
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Nouveau Document
                        </h3>
                        <button wire:click="closeUploadModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <x-iconify icon="lucide:x" class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="px-4 pt-5 pb-4 sm:p-6">
                    @if (session()->has('error'))
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0"><x-iconify icon="lucide:alert-circle" class="h-5 w-5 text-red-500" /></div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <form wire:submit.prevent="upload" class="space-y-4">

                        {{-- File Input --}}
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 flex justify-center items-center hover:border-blue-400 transition-colors bg-gray-50">
                            <div class="text-center">
                                <x-iconify icon="lucide:cloud-upload" class="mx-auto h-12 w-12 text-gray-400" />
                                <div class="mt-2 text-sm text-gray-600">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-2 py-1">
                                        <span>Choisir un fichier</span>
                                        <input id="file-upload" wire:model="newFile" type="file" class="sr-only">
                                    </label>
                                    <p class="pl-1 inline">ou glisser-déposer</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">PDF, IMG jusqu'à 10MB</p>

                                @if($newFile)
                                <div class="mt-2 text-sm text-green-600 font-medium flex items-center justify-center gap-1">
                                    <x-iconify icon="lucide:check" class="w-4 h-4" />
                                    {{ $newFile->getClientOriginalName() }}
                                </div>
                                @endif

                                <div wire:loading wire:target="newFile" class="mt-2 text-sm text-blue-600 flex items-center justify-center gap-1">
                                    <x-iconify icon="lucide:loader-2" class="w-4 h-4 animate-spin" />
                                    Upload en cours...
                                </div>
                            </div>
                        </div>
                        @error('newFile') <span class="text-xs text-red-500">{{ $message }}</span> @enderror

                        {{-- Category --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catégorie</label>
                            <select wire:model.live="uploadCategoryId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Choisir...</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('uploadCategoryId') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        {{-- Dynamic Fields --}}
                        @if($this->selectedUploadCategory && $this->selectedUploadCategory->meta_schema)
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                Détails : {{ $this->selectedUploadCategory->name }}
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($this->selectedUploadCategory->meta_schema as $field)
                                @php
                                $key = $field['key'] ?? null;
                                $label = $field['label'] ?? $key;
                                $type = $field['type'] ?? 'string';
                                @endphp
                                @if($key)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                    @if($type === 'boolean')
                                    <input type="checkbox" wire:model="uploadMetadata.{{ $key }}" class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    @elseif($type === 'number')
                                    <input type="number" wire:model="uploadMetadata.{{ $key }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    @elseif($type === 'date')
                                    <input type="date" wire:model="uploadMetadata.{{ $key }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    @else
                                    <input type="text" wire:model="uploadMetadata.{{ $key }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    @endif
                                    @error("uploadMetadata.{$key}") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Standard Dates --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date d'émission</label>
                                <input type="date" wire:model="uploadIssueDate" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('uploadIssueDate') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                                <input type="date" wire:model="uploadExpiryDate" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('uploadExpiryDate') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea wire:model="uploadDescription" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                            @error('uploadDescription') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        {{-- Status & Submit --}}
                        <div class="flex items-center justify-between pt-2">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="uploadStatus" value="validated" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label class="ml-2 block text-sm text-gray-900">
                                    Marquer comme validé
                                </label>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" wire:click="closeUploadModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none">
                                    Annuler
                                </button>
                                <button type="submit" wire:loading.attr="disabled" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none">
                                    <span wire:loading.remove wire:target="upload">Uploader</span>
                                    <span wire:loading wire:target="upload">Traitement...</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
