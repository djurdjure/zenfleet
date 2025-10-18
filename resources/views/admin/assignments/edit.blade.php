<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier l\'Affectation N°') }}{{ $assignment->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                 <div class="p-8 text-gray-900">

                    {{-- Section d'information non modifiable --}}
                    <div class="mb-8 border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Résumé de l'Affectation</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">

                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <x-icon icon="heroicons:user"-circle-2 class="h-10 w-10 text-gray-400"/ />
                                </div>
                                <div>
                                    <p class="text-gray-500">Chauffeur</p>
                                    <p class="font-bold text-gray-900">{{ $assignment->driver?->first_name }} {{ $assignment->driver?->last_name }}</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                 <div class="flex-shrink-0">
                                    <x-icon icon="heroicons:truck" class="h-10 w-10 text-gray-400"/ />
                                </div>
                                <div>
                                     <p class="text-gray-500">Véhicule</p>
                                     <p class="font-bold text-gray-900">{{ $assignment->vehicle?->brand }} {{ $assignment->vehicle?->model }}</p>
                                     <p class="text-gray-600 font-mono">{{ $assignment->vehicle?->registration_plate }}</p>
                                </div>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-md">
                                <p class="text-gray-500">Début de l'affectation</p>
                                <p class="font-semibold text-gray-900">{{ $assignment->start_datetime->format('d/m/Y à H:i') }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-md">
                                <p class="text-gray-500">Fin de l'affectation</p>
                                <p class="font-semibold text-gray-900">{{ $assignment->end_datetime ? $assignment->end_datetime->format('d/m/Y à H:i') : 'En cours' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Formulaire de modification --}}
                    <form method="POST" action="{{ route('admin.assignments.update', $assignment) }}">
                        @csrf
                        @method('PUT')
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="reason" value="Motif de l'affectation" />
                                <x-text-input id="reason" class="block mt-1 w-full" type="text" name="reason" :value="old('reason', $assignment->reason)" />
                                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="notes" value="Notes Complémentaires" />
                                <textarea id="notes" name="notes" rows="4" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">{{ old('notes', $assignment->notes) }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-4 border-t pt-6">
                             <a href="{{ route('admin.assignments.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Retour à la liste</a>
                             <x-primary-button>
                                 <x-icon icon="heroicons:check-circle" class="w-5 h-5 mr-2"/ />
                                 Enregistrer les Modifications
                             </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>