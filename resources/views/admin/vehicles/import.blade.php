<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Importer des Véhicules par Fichier CSV') }}
            </h2>
            <a href="{{ route('admin.vehicles.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 border border-gray-300 rounded-md font-medium text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 transition">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1"/>
                Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start">
                    <div class="flex-shrink-0 mt-0.5">
                        <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-red-500"/>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-red-800">{{ session('error') }}</h4>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <div class="mb-8 p-5 bg-violet-50 border border-violet-200 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-information-circle class="h-6 w-6 text-violet-500"/>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-lg font-bold text-violet-800">Instructions d'importation</h4>
                                <ol class="list-decimal list-inside mt-3 text-sm text-violet-700 space-y-2">
                                    <li>Téléchargez le fichier modèle pour voir le format exact des colonnes.</li>
                                    <li>Remplissez le fichier avec vos données. Les colonnes <strong>Immatriculation*, Marque*, Modèle*, Type de Véhicule*, Type de Carburant*, Type de Transmission*, et Statut Initial*</strong> sont obligatoires.</li>
                                    <li>Le format pour toutes les dates doit être <strong>AAAA-MM-JJ</strong> (ex: 2023-05-25).</li>
                                    <li>Les valeurs pour les types et statuts (ex: "Berline", "En service") doivent correspondre exactement à celles définies dans le système.</li>
                                    <li>Enregistrez votre fichier au format CSV.</li>
                                    <li>Sélectionnez l'encodage approprié si votre fichier contient des caractères accentués.</li>
                                </ol>
                                <div class="mt-4 p-3 bg-violet-100 rounded-md">
                                    <p class="text-sm font-medium text-violet-800">
                                        <x-heroicon-o-light-bulb class="inline-block h-4 w-4 mr-1"/> 
                                        Conseil : Pour les fichiers Excel, utilisez "Enregistrer sous" et choisissez le format "CSV UTF-8" pour une compatibilité optimale.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.vehicles.import.handle') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="importForm">
                        @csrf
                        <div>
                            <label for="csv_file" class="block font-medium text-sm text-gray-700">Votre Fichier CSV <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="file" name="csv_file" id="csv_file" 
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 focus:outline-none" 
                                    required 
                                    accept=".csv,.txt">
                                <p class="mt-1 text-xs text-gray-500">Formats acceptés : .csv, .txt</p>
                            </div>
                            <x-input-error :messages="$errors->get('csv_file')" class="mt-2" />
                        </div>

                        <div>
                            <label for="encoding" class="block font-medium text-sm text-gray-700">Encodage du fichier</label>
                            <select name="encoding" id="encoding" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring focus:ring-violet-200 focus:ring-opacity-50">
                                <option value="auto" selected>Détection automatique (recommandé)</option>
                                <option value="utf8">UTF-8</option>
                                <option value="iso">ISO-8859-1</option>
                                <option value="windows">Windows-1252</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Sélectionnez l'encodage approprié si vous rencontrez des problèmes avec les caractères accentués.</p>
                        </div>

                        <div class="mt-8 pt-6 border-t flex items-center justify-between">
                            <a href="{{ route('admin.vehicles.import.template') }}" class="inline-flex items-center text-sm font-semibold text-violet-600 hover:text-violet-800 transition">
                                <x-heroicon-o-arrow-down-tray class="h-5 w-5 mr-2"/>
                                Télécharger le modèle (.csv)
                            </a>
                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.vehicles.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 transition" id="submitBtn">
                                    <x-heroicon-o-arrow-up-tray class="h-4 w-4 mr-2"/>
                                    Lancer l'Importation
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('importForm');
            const submitBtn = document.getElementById('submitBtn');
            const fileInput = document.getElementById('csv_file');
            
            form.addEventListener('submit', function(e) {
                if (fileInput.files.length > 0) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg> Traitement en cours...`;
                }
            });
            
            fileInput.addEventListener('change', function() {
                const fileName = this.files[0]?.name;
                if (fileName) {
                    // Vérifier l'extension du fichier
                    const extension = fileName.split('.').pop().toLowerCase();
                    if (extension !== 'csv' && extension !== 'txt') {
                        alert("Format de fichier non valide. Veuillez sélectionner un fichier CSV ou TXT.");
                        this.value = '';
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
