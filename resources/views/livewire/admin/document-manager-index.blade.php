<div>
    {{--
        Single Root Element enforced for Livewire 3
        Design System: Matches admin/drivers/driver-index.blade.php
    --}}
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-10">

        {{-- ===============================================
            HEADER
        =============================================== --}}
        <div class="mb-6 flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-600">
                    Gestion des Documents
                </h1>
                <p class="text-xs text-gray-600">
                    Référentiel documentaire centralisé • {{ $documents->total() }} document(s)
                </p>
            </div>

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
                    <div class="w-12 h-12 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
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
                    <div class="w-12 h-12 bg-green-100 border border-green-300 rounded-full flex items-center justify-center">
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
                    <div class="w-12 h-12 bg-gray-100 border border-gray-300 rounded-full flex items-center justify-center">
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
                    <div class="w-12 h-12 bg-amber-100 border border-amber-300 rounded-full flex items-center justify-center">
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
                        class="pl-10 pr-4 py-2.5 block w-full bg-white border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee] text-sm"
                        placeholder="Rechercher (nom, description)...">
                    <div wire:loading.delay wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <x-iconify icon="lucide:loader-2" class="w-4 h-4 text-blue-500 animate-spin" />
                    </div>
                </div>
            </x-slot:search>

            <x-slot:filters>
                <button @click="showFilters = !showFilters" type="button"
                    class="inline-flex items-center gap-2 h-10 px-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
                    <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" x-bind:class="showFilters ? 'rotate-180' : ''" />
                </button>
            </x-slot:filters>

            <x-slot:actions>
                @can('documents.create')
                    <button wire:click="openUploadModal"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-[#0c90ee] text-white rounded-lg hover:bg-[#0b82d6] transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:upload-cloud" class="w-5 h-5" />
                        <span class="hidden sm:inline font-medium">Uploader</span>
                    </button>
                @endcan
            </x-slot:actions>

            <x-slot:filtersPanel>
                <x-page-filters-panel columns="2">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-600">Catégorie</label>
                        <select wire:model.live="categoryFilter" class="block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-600">Statut</label>
                        <select wire:model.live="statusFilter" class="block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]">
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
        <div class="fixed inset-0 flex items-stretch justify-end">
            <div wire:click="closeUploadModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40"></div>

            <div class="relative z-50 h-screen w-full max-w-2xl border-l border-slate-200 bg-white text-left shadow-2xl overflow-y-auto">
                <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2" id="modal-title">
                        <x-iconify icon="heroicons:document-arrow-up" class="w-5 h-5 text-[#0c90ee]" />
                        Nouveau Document
                    </h3>
                    <button wire:click="closeUploadModal" class="text-gray-400 hover:text-gray-500 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                        <x-iconify icon="heroicons:x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit.prevent="upload" class="space-y-6 px-6 py-4">
                    @if (session()->has('error'))
                        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                            <p class="text-sm text-red-700 flex items-center gap-2">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ session('error') }}
                            </p>
                        </div>
                    @endif

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-600">
                            Fichier
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative border-2 border-dashed rounded-xl p-6 text-center transition-all border-gray-300 hover:border-[#0c90ee] bg-gray-50">
                            <input id="file-upload" wire:model="newFile" type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="pointer-events-none">
                                <x-iconify icon="heroicons:cloud-arrow-up" class="mx-auto h-9 w-9 text-gray-400" />
                                <p class="mt-2 text-sm text-gray-600">
                                    <span class="font-medium text-[#0c90ee]">Cliquez pour téléverser</span> ou glissez-déposez
                                </p>
                                <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX, JPG, PNG (max 10MB)</p>

                                @if($newFile)
                                    <p class="mt-3 inline-flex items-center gap-2 rounded-full bg-green-50 border border-green-200 px-3 py-1 text-sm font-medium text-green-700">
                                        <x-iconify icon="heroicons:check-circle" class="w-4 h-4" />
                                        {{ $newFile->getClientOriginalName() }}
                                    </p>
                                @endif

                                <div wire:loading wire:target="newFile" class="mt-2 text-sm text-[#0c90ee] flex items-center justify-center gap-2">
                                    <x-iconify icon="heroicons:arrow-path" class="w-4 h-4 animate-spin" />
                                    Téléversement en cours...
                                </div>
                            </div>
                        </div>
                        @error('newFile')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1.5">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-600">
                            Catégorie
                            <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="uploadCategoryId" class="block w-full rounded-lg border bg-gray-50 px-3 py-2.5 text-sm text-gray-900 transition-colors duration-200 @error('uploadCategoryId') border-red-500 bg-red-50 focus:ring-red-500 focus:border-red-500 @else border-gray-300 hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee] @enderror">
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('uploadCategoryId')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1.5">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    @if($this->selectedUploadCategory && $this->selectedUploadCategory->meta_schema)
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <h4 class="text-sm font-semibold text-slate-600 mb-3">
                                Détails: {{ $this->selectedUploadCategory->name }}
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($this->selectedUploadCategory->meta_schema as $field)
                                    @php
                                        $key = $field['key'] ?? null;
                                        $label = $field['label'] ?? $key;
                                        $type = $field['type'] ?? 'string';
                                    @endphp
                                    @if($key)
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-600">{{ $label }}</label>
                                            @if($type === 'boolean')
                                                <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                                                    <input type="checkbox" wire:model="uploadMetadata.{{ $key }}" class="h-4 w-4 rounded border-gray-300 text-[#0c90ee] focus:ring-[#0c90ee]/30">
                                                    Oui
                                                </label>
                                            @elseif($type === 'number')
                                                <input type="number" wire:model="uploadMetadata.{{ $key }}" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]">
                                            @elseif($type === 'date')
                                                <input type="date" wire:model="uploadMetadata.{{ $key }}" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]">
                                            @else
                                                <input type="text" wire:model="uploadMetadata.{{ $key }}" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]">
                                            @endif
                                            @error("uploadMetadata.{$key}")
                                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1.5">
                                                    <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-datepicker
                                name="upload_issue_date"
                                label="Date d'émission"
                                :value="$uploadIssueDate"
                                placeholder="JJ/MM/AAAA"
                                :error="$errors->first('uploadIssueDate')"
                                x-on:input="$wire.set('uploadIssueDate', $event.detail)" />
                        </div>
                        <div>
                            <x-datepicker
                                name="upload_expiry_date"
                                label="Date d'expiration"
                                :value="$uploadExpiryDate"
                                :minDate="$uploadIssueDate"
                                placeholder="JJ/MM/AAAA"
                                :error="$errors->first('uploadExpiryDate')"
                                x-on:input="$wire.set('uploadExpiryDate', $event.detail)" />
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-600">Description</label>
                        <textarea wire:model="uploadDescription" rows="3" class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]" placeholder="Contexte du document..."></textarea>
                        @error('uploadDescription')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1.5">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input id="upload-status-validated" type="checkbox" wire:model="uploadStatus" value="validated" class="h-4 w-4 rounded border-gray-300 text-[#0c90ee] focus:ring-[#0c90ee]/30">
                        <label for="upload-status-validated" class="ml-2 block text-sm text-gray-600">
                            Marquer comme validé
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 pb-4 px-6 border-t border-gray-200 bg-gray-50 sticky bottom-0 -mx-6">
                        <button
                            type="button"
                            wire:click="closeUploadModal"
                            class="inline-flex items-center justify-center h-10 px-4 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee] transition-all duration-200">
                            Annuler
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="zf-btn-primary inline-flex items-center justify-center h-10 px-4 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md text-sm disabled:opacity-50">
                            <span wire:loading.remove wire:target="upload">Uploader</span>
                            <span wire:loading wire:target="upload">
                                <x-iconify icon="heroicons:arrow-path" class="w-4 h-4 animate-spin inline" />
                                Traitement...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
