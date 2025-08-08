<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Importer des Chauffeurs par Fichier CSV') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <div class="mb-6 p-4 bg-violet-50 border border-violet-200 rounded-lg">
                        
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-information-circle class="h-6 w-6 text-violet-500"/>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-lg font-bold text-violet-800">Instructions</h4>
                                <ol class="list-decimal list-inside mt-2 text-sm text-violet-700 space-y-1">
                                    <li>Téléchargez le fichier modèle pour voir le format exact des colonnes.</li>
                                    <li>Remplissez le fichier avec vos données. Les colonnes <strong>nom, prenom, et date_naissance</strong> sont obligatoires.</li>
                                    <li>Le format pour toutes les dates doit être <strong>AAAA-MM-JJ</strong> (ex: 1990-05-25).</li>
                                    <li>Enregistrez votre fichier au format CSV (séparateur virgule, encodage UTF-8).</li>
                                    <li>Téléversez le fichier complété ci-dessous.</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.drivers.import.handle') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label for="csv_file" class="block font-medium text-sm text-gray-700">Votre Fichier CSV <span class="text-red-500">*</span></label>
                            <input type="file" name="csv_file" id="csv_file" class="block w-full text-sm text-gray-500 mt-1 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100" required>
                            <x-input-error :messages="$errors->get('csv_file')" class="mt-2" />
                        </div>

                        <div class="mt-8 pt-6 border-t flex items-center justify-between">
                            <a href="{{ route('admin.drivers.import.template') }}" class="inline-flex items-center text-sm font-semibold text-violet-600 hover:text-violet-800">
                                <x-heroicon-o-arrow-down-tray class="h-5 w-5 mr-2"/>
                                Télécharger le modèle (.csv)
                            </a>
                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.drivers.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">
                                    Lancer l'Importation
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>