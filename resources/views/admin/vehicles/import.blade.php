<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Importer des Véhicules') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">{{ __('Importer depuis un fichier CSV') }}</h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Importez une liste de véhicules en une seule fois. Le fichier doit respecter un format précis.
                    </p>

                    {{-- Section d'aide et de téléchargement du modèle --}}
                    <div class="mb-6 p-4 bg-violet-50 border border-violet-200 rounded-lg">
                        <h4 class="font-semibold text-gray-800">Instructions :</h4>
                        <ul class="list-disc list-inside text-sm text-gray-600 mt-2 space-y-1">
                            <li>Le fichier doit être au format CSV avec un encodage UTF-8.</li>
                            <li>La première ligne du fichier doit contenir les en-têtes des colonnes.</li>
                            <li>Les colonnes obligatoires sont : `registration_plate`, `brand`, `model`, `vehicle_type_name`, `fuel_type_name`, `transmission_type_name`, `status_name`.</li>
                            <li>Les valeurs pour les types et statuts (ex: "Berline", "En service") doivent correspondre exactement à celles définies dans le système.</li>
                        </ul>
                        <div class="mt-4">
                            <a href="{{ route('admin.vehicles.import.template') }}" class="inline-flex items-center text-sm font-semibold text-violet-600 hover:text-violet-800">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Télécharger le fichier modèle CSV
                            </a>
                        </div>
                    </div>

                    {{-- Affichage des erreurs de validation --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Erreur lors de l'upload :</p>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Formulaire d'upload --}}
                    <form method="POST" action="{{ route('admin.vehicles.import.handle') }}" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label for="csv_file" class="block font-medium text-sm text-gray-700">Fichier CSV à importer</label>
                            <input type="file" name="csv_file" id="csv_file" required class="block w-full text-sm text-gray-500 mt-1 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100"/>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-4">
                            <a href="{{ route('admin.vehicles.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">{{ __('Annuler') }}</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">
                                Lancer l'Importation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
