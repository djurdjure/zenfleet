<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Résultats de l\'Importation des Chauffeurs') }}
            </h2>
            <a href="{{ route('admin.drivers.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 border border-gray-300 rounded-md font-medium text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                <x-lucide-arrow-left class="h-4 w-4 mr-1"/>
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Informations sur l'importation --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <x-lucide-file-text class="h-8 w-8 text-primary-500 mr-3"/>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Détails de l'importation</h3>
                            <div class="mt-1 text-sm text-gray-600">
                                <p><span class="font-medium">Fichier :</span> {{ $fileName ?? 'Fichier CSV' }}</p>
                                <p><span class="font-medium">Encodage détecté :</span> {{ ucfirst($encoding ?? 'utf8') }}</p>
                                <p><span class="font-medium">Identifiant d'importation :</span> <span class="font-mono text-xs">{{ $importId ?? 'N/A' }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Carte de Résumé Statistique --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gradient-to-br from-green-50 to-green-100 border-l-4 border-green-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 bg-opacity-60">
                                <x-lucide-check-circle-2 class="h-8 w-8 text-green-600"/>
                            </div>
                            <div class="ml-4">
                                <p class="text-3xl font-bold text-green-800">{{ $successCount }}</p>
                                <p class="text-sm font-medium text-green-700">Chauffeur(s) importé(s) avec succès</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gradient-to-br from-red-50 to-red-100 border-l-4 border-red-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 bg-opacity-60">
                                <x-lucide-x-circle class="h-8 w-8 text-red-600"/>
                            </div>
                            <div class="ml-4">
                                <p class="text-3xl font-bold text-red-800">{{ count($errorRows) }}</p>
                                <p class="text-sm font-medium text-red-700">Ligne(s) en erreur</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section des Erreurs (si applicable) --}}
            @if (!empty($errorRows))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 md:p-8 text-gray-900">
                        <div class="flex items-center mb-4">
                            <x-lucide-alert-triangle class="h-6 w-6 text-amber-500 mr-2"/>
                            <h3 class="text-xl font-semibold text-gray-800">Détail des Lignes en Erreur</h3>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-6">
                            Les lignes suivantes n'ont pas été importées en raison d'erreurs. Veuillez corriger ces problèmes dans votre fichier CSV et réessayer l'importation.
                        </p>

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Ligne</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Erreur(s)</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Données</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($errorRows as $error)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-center">
                                                <span class="px-2 py-1 bg-gray-100 rounded-md">{{ $error['line'] }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                                                    @foreach ($error['errors'] as $message)
                                                        <li>{{ $message }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-xs text-gray-600 max-w-md overflow-hidden">
                                                    @foreach ($error['data'] as $key => $value)
                                                        <div class="mb-1">
                                                            <span class="font-medium">{{ $key }}:</span> 
                                                            <span class="font-mono">{{ $value ?? 'NULL' }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('admin.drivers.import.show') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition">
                    <x-lucide-rotate-cw class="h-4 w-4 mr-2"/>
                    Nouvelle Importation
                </a>
                <a href="{{ route('admin.drivers.index') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition">
                    <x-lucide-users class="h-4 w-4 mr-2"/>
                    Voir tous les Chauffeurs
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
