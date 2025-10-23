{{-- resources/views/livewire/admin/document-upload-modal.blade.php --}}
<x-modal name="document-upload-modal" title="Nouveau Document" maxWidth="2xl" wire:model="isOpen">
    <form wire:submit.prevent="upload" class="space-y-6">
        
        {{-- File Upload --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Fichier <span class="text-red-500">*</span>
            </label>
            
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors"
                 x-data="{ 
                     isDragging: false,
                     handleDrop(e) {
                         isDragging = false;
                         @this.file = e.dataTransfer.files[0];
                     }
                 }"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="handleDrop"
                 :class="{ 'border-blue-400 bg-blue-50': isDragging }">
                <div class="space-y-1 text-center">
                    <x-iconify icon="mdi:cloud-upload" class="mx-auto h-12 w-12 text-gray-400" />
                    <div class="flex text-sm text-gray-600">
                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                            <span>Choisir un fichier</span>
                            <input 
                                id="file-upload" 
                                wire:model="file" 
                                type="file" 
                                class="sr-only"
                            >
                        </label>
                        <p class="pl-1">ou glisser-déposer</p>
                    </div>
                    <p class="text-xs text-gray-500">
                        Maximum 10 MB
                    </p>
                    
                    @if($file)
                        <div class="mt-2 text-sm text-green-600 flex items-center justify-center">
                            <x-iconify icon="mdi:check-circle" class="w-4 h-4 mr-1" />
                            {{ $file->getClientOriginalName() }} ({{ number_format($file->getSize() / 1024, 2) }} KB)
                        </div>
                    @endif
                </div>
            </div>
            
            @error('file')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
            
            <div wire:loading wire:target="file" class="mt-2 text-sm text-blue-600">
                Chargement du fichier...
            </div>
        </div>

        {{-- Category Selection --}}
        <div>
            <x-select
                wire:model.live="categoryId"
                name="categoryId"
                label="Catégorie"
                :options="['' => 'Sélectionnez une catégorie'] + $categories->pluck('name', 'id')->toArray()"
                required
                :error="$errors->first('categoryId')"
            />
        </div>

        {{-- Dynamic Metadata Fields (based on category schema) --}}
        @if($selectedCategory && $selectedCategory->meta_schema)
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                    <x-iconify icon="mdi:form-textbox" class="w-5 h-5 mr-2 text-blue-600" />
                    Champs spécifiques ({{ $selectedCategory->name }})
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($selectedCategory->meta_schema as $field)
                        @php
                            $key = $field['key'] ?? null;
                            $label = $field['label'] ?? $key;
                            $type = $field['type'] ?? 'string';
                            $required = $field['required'] ?? false;
                        @endphp
                        
                        @if($key)
                            <div>
                                @if($type === 'date')
                                    <x-datepicker
                                        wire:model="metadata.{{ $key }}"
                                        name="metadata_{{ $key }}"
                                        label="{{ $label }}"
                                        :required="$required"
                                        :error="$errors->first('metadata.' . $key)"
                                    />
                                @elseif($type === 'boolean')
                                    <label class="flex items-center space-x-2">
                                        <input 
                                            type="checkbox" 
                                            wire:model="metadata.{{ $key }}"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        >
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ $label }}
                                            @if($required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </span>
                                    </label>
                                @elseif($type === 'number')
                                    <x-input
                                        wire:model="metadata.{{ $key }}"
                                        type="number"
                                        name="metadata_{{ $key }}"
                                        label="{{ $label }}"
                                        :required="$required"
                                        :error="$errors->first('metadata.' . $key)"
                                    />
                                @else
                                    <x-input
                                        wire:model="metadata.{{ $key }}"
                                        name="metadata_{{ $key }}"
                                        label="{{ $label }}"
                                        :required="$required"
                                        :error="$errors->first('metadata.' . $key)"
                                    />
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Standard Fields --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Issue Date --}}
            <div>
                <x-datepicker
                    wire:model="issueDate"
                    name="issueDate"
                    label="Date d'émission"
                    :error="$errors->first('issueDate')"
                />
            </div>

            {{-- Expiry Date --}}
            <div>
                <x-datepicker
                    wire:model="expiryDate"
                    name="expiryDate"
                    label="Date d'expiration"
                    :error="$errors->first('expiryDate')"
                />
            </div>
        </div>

        {{-- Description --}}
        <div>
            <x-textarea
                wire:model="description"
                name="description"
                label="Description"
                rows="3"
                placeholder="Description du document..."
                helpText="Maximum 500 caractères"
                :error="$errors->first('description')"
            />
        </div>

        {{-- Status --}}
        <div>
            <x-select
                wire:model="status"
                name="status"
                label="Statut"
                :options="[
                    'validated' => 'Validé',
                    'draft' => 'Brouillon',
                ]"
                required
                :error="$errors->first('status')"
            />
        </div>

        {{-- Attachment Info (if provided) --}}
        @if($attachToType && $attachToId)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <x-iconify icon="mdi:link-variant" class="w-5 h-5 text-blue-600 mr-2" />
                    <span class="text-sm text-blue-900">
                        Ce document sera automatiquement attaché à 
                        <strong>{{ ucfirst($attachToType) }} #{{ $attachToId }}</strong>
                    </span>
                </div>
            </div>
        @endif

        {{-- Form Actions --}}
        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
            <x-button 
                @click="$dispatch('close-modal', 'document-upload-modal')" 
                type="button" 
                variant="secondary"
                wire:loading.attr="disabled">
                Annuler
            </x-button>
            
            <x-button 
                type="submit" 
                variant="primary" 
                icon="check"
                iconPosition="left"
                wire:loading.attr="disabled"
                wire:target="file,upload">
                <span wire:loading.remove wire:target="upload">Uploader</span>
                <span wire:loading wire:target="upload">Upload en cours...</span>
            </x-button>
        </div>
    </form>
</x-modal>
