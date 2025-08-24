<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier le Document') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 border-b border-gray-200">
                    <form action="{{ route('admin.documents.update', $document) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        {{-- Section Fichier et Catégorie --}}
                        <div class="p-6 border border-gray-200 rounded-lg">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-6">
                                Fichier et Classification
                            </h3>
                            <div class="mb-4">
                                <p class="block text-sm font-medium text-gray-700">Fichier Actuel</p>
                                <div class="flex items-center gap-2 mt-2 text-sm text-gray-600">
                                    <x-lucide-file class="w-5 h-5 text-gray-400" stroke-width="1.5"/>
                                    <span>{{ $document->original_filename }}</span>
                                    <span class="text-gray-400">({{ $document->formatted_size }})</span>
                                </div>
                            </div>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="document_category_id" :value="__('Catégorie')" required />
                                    <select id="document_category_id" name="document_category_id" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" required>
                                        @foreach ($categories as $id => $name)
                                            <option value="{{ $id }}" @selected(old('document_category_id', $document->document_category_id) == $id)>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('document_category_id')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Section Dates --}}
                        <div class="p-6 border border-gray-200 rounded-lg">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-6">
                                Dates de Validité <span class="text-sm font-normal text-gray-500">(optionnel)</span>
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-date-picker
                                        id="issue_date"
                                        name="issue_date"
                                        label="Date d'émission"
                                        :value="old('issue_date', $document->issue_date ? $document->issue_date->format('Y-m-d') : '')"
                                    />
                                    <x-input-error :messages="$errors->get('issue_date')" class="mt-2" />
                                </div>

                                <div>
                                    <x-date-picker
                                        id="expiry_date"
                                        name="expiry_date"
                                        label="Date d'expiration"
                                        :value="old('expiry_date', $document->expiry_date ? $document->expiry_date->format('Y-m-d') : '')"
                                    />
                                    <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Section Description --}}
                        <div class="p-6 border border-gray-200 rounded-lg">
                             <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-6">
                                Informations Complémentaires <span class="text-sm font-normal text-gray-500">(optionnel)</span>
                            </h3>
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">{{ old('description', $document->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end mt-8 pt-8 border-t border-gray-200">
                            <a href="{{ route('admin.documents.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                                Annuler
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Mettre à jour') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
