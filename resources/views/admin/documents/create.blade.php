<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Importer un Nouveau Document') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        {{-- Section Fichier et Catégorie --}}
                        <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-6">
                                Fichier et Classification
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-input-label for="document_file" :value="__('Fichier à importer')" required />
                                    <x-text-input id="document_file" name="document_file" type="file" class="mt-1 block w-full" required />
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Types autorisés : PDF, DOCX, JPG, PNG, XLSX. Taille maximale : 10MB.</p>
                                    <x-input-error :messages="$errors->get('document_file')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="document_category_id" :value="__('Catégorie')" required />
                                    <select id="document_category_id" name="document_category_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm" required>
                                        <option value="">-- Choisir une catégorie --</option>
                                        @foreach ($categories as $id => $name)
                                            <option value="{{ $id }}" @selected(old('document_category_id') == $id)>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('document_category_id')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Section Dates --}}
                        <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-6">
                                Dates de Validité <span class="text-sm font-normal text-gray-500">(optionnel)</span>
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-date-picker
                                        id="issue_date"
                                        name="issue_date"
                                        label="Date d'émission"
                                        :value="old('issue_date')"
                                    />
                                    <x-input-error :messages="$errors->get('issue_date')" class="mt-2" />
                                </div>

                                <div>
                                    <x-date-picker
                                        id="expiry_date"
                                        name="expiry_date"
                                        label="Date d'expiration"
                                        :value="old('expiry_date')"
                                    />
                                    <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Section Description --}}
                        <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg">
                             <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-6">
                                Informations Complémentaires <span class="text-sm font-normal text-gray-500">(optionnel)</span>
                            </h3>
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.documents.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                                {{ __('Annuler') }}
                            </a>
                            <x-primary-button class="ml-4">
                                <x-lucide-upload class="w-4 h-4 mr-2" />
                                {{ __('Importer le Document') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
