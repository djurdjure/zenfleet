<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier une Affectation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">

                    {{-- Section d'information (non modifiable) --}}
                    <div class="mb-8 border-b pb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Résumé de l'Affectation</h3>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">

                            {{-- Carte Chauffeur --}}
                            <div class="p-4 border rounded-lg flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    @if($assignment->driver?->photo_path)
                                        <img class="h-16 w-16 rounded-full object-cover" src="{{ asset('storage/' . $assignment->driver->photo_path) }}" alt="Photo">
                                    @else
                                        <span class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center">
                                            <svg class="h-10 w-10 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.997A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-gray-500">Chauffeur</p>
                                    <p class="font-bold text-lg text-gray-900">{{ $assignment->driver?->first_name }} {{ $assignment->driver?->last_name }}</p>
                                    <p class="text-gray-600">{{ $assignment->driver?->personal_phone }}</p>
                                </div>
                            </div>

                            {{-- Carte Véhicule --}}
                            <div class="p-4 border rounded-lg">
                                 <p class="text-gray-500">Véhicule</p>
                                 <p class="font-bold text-lg text-gray-900">{{ $assignment->vehicle?->brand }} {{ $assignment->vehicle?->model }}</p>
                                 <p class="text-gray-600 font-mono">{{ $assignment->vehicle?->registration_plate }}</p>
                            </div>

                            {{-- Dates --}}
                            <div class="p-4 border rounded-lg">
                                <p class="text-gray-500">Début de l'affectation</p>
                                <p class="font-semibold text-gray-900">{{ $assignment->start_datetime->format('d/m/Y à H:i') }}</p>
                            </div>
                            <div class="p-4 border rounded-lg">
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
                                <label for="reason" class="block font-medium text-sm text-gray-700">Motif de l'affectation</label>
                                <x-text-input id="reason" class="block mt-1 w-full" type="text" name="reason" :value="old('reason', $assignment->reason)" />
                                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                            </div>
                            <div>
                                <label for="notes" class="block font-medium text-sm text-gray-700">Notes Complémentaires</label>
                                <textarea id="notes" name="notes" rows="4" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">{{ old('notes', $assignment->notes) }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-4">
                            <a href="{{ route('admin.assignments.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Retour à la liste</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">
                                Enregistrer les Modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
