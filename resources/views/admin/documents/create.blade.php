{{-- resources/views/admin/documents/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Importer un nouveau document') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <x-input-label for="document_file" :value="__('Fichier')" />
                                <input id="document_file" name="document_file" type="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 mt-1" required>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PDF, DOCX, JPG, PNG, XLSX (MAX. 10MB).</p>
                                <x-input-error :messages="$errors->get('document_file')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="document_category_id" :value="__('Catégorie')" />
                                <select id="document_category_id" name="document_category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">-- Choisir une catégorie --</option>
                                    @foreach ($categories as $id => $name)
                                        <option value="{{ $id }}" {{ old('document_category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('document_category_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="issue_date" :value="__('Date d\'émission (optionnel)')" />
                                <x-text-input id="issue_date" class="block mt-1 w-full" type="date" name="issue_date" :value="old('issue_date')" />
                                <x-input-error :messages="$errors->get('issue_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="expiry_date" :value="__('Date d\'expiration (optionnel)')" />
                                <x-text-input id="expiry_date" class="block mt-1 w-full" type="date" name="expiry_date" :value="old('expiry_date')" />
                                <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description (optionnel)')" />
                                <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                            
                            {{-- The section to link entities (vehicles, users) will be added in a future phase --}}
                            
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.documents.index') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white mr-4">
                                {{ __('Annuler') }}
                            </a>
                            <x-primary-button>
                                {{ __('Importer le document') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
