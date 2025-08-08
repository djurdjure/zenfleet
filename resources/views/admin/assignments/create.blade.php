<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer une Nouvelle Affectation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <h3 class="text-xl font-semibold text-gray-700 mb-6 border-b pb-4">Détails de l'Affectation</h3>

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Veuillez corriger les erreurs :</p>
                            <ul class="mt-2 list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.assignments.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Sélection du Véhicule --}}
                            <div class="md:col-span-2">
                                <x-input-label for="select-vehicle" value="Véhicule" required />
                                <select name="vehicle_id" id="select-vehicle" placeholder="Recherchez une immatriculation, marque..." required>
                                    <option value="">Sélectionnez un véhicule disponible</option>
                                    @foreach($availableVehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>
                                            {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->registration_plate }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                            </div>

                            {{-- Sélection du Chauffeur --}}
                            <div class="md:col-span-2">
                                <x-input-label for="select-driver" value="Chauffeur" required />
                                <select name="driver_id" id="select-driver" placeholder="Recherchez un chauffeur..." required>
                                    <option value="">Sélectionnez un chauffeur disponible</option>
                                    @foreach($availableDrivers as $driver)
                                        <option value="{{ $driver->id }}" @selected(old('driver_id') == $driver->id)>{{ $driver->first_name }} {{ $driver->last_name }}</option>
                                    @endforeach
                                </select>
                                 <x-input-error :messages="$errors->get('driver_id')" class="mt-2" />
                            </div>

                            {{-- Date et Heure de Début --}}
                            <div>
                                <x-input-label for="start_datetime" value="Date et Heure de Début" required />
                                <x-text-input id="start_datetime" class="block mt-1 w-full" type="datetime-local" name="start_datetime" :value="old('start_datetime', now()->format('Y-m-d\TH:i'))" required />
                                <x-input-error :messages="$errors->get('start_datetime')" class="mt-2" />
                            </div>

                            {{-- Kilométrage de Début --}}
                            <div>
                                <x-input-label for="start_mileage" value="Kilométrage de Début" required />
                                <x-text-input id="start_mileage" class="block mt-1 w-full" type="number" name="start_mileage" :value="old('start_mileage')" required />
                                <x-input-error :messages="$errors->get('start_mileage')" class="mt-2" />
                            </div>

                            {{-- Motif et Notes --}}
                            <div class="md:col-span-2">
                                <x-input-label for="reason" value="Motif de l'affectation" />
                                <x-text-input id="reason" name="reason" :value="old('reason')" class="block mt-1 w-full" type="text" />
                                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="notes" value="Notes" />
                                <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-4 border-t pt-6">
                            <a href="{{ route('admin.assignments.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
                            <x-primary-button>
                                <x-heroicon-o-check-circle class="w-5 h-5 mr-2"/>
                                Créer l'Affectation
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.TomSelect) {
                const vehicleMileageData = @json($availableVehicles->pluck('current_mileage', 'id'));
                const mileageInput = document.getElementById('start_mileage');

                let tomSelectVehicle = new TomSelect('#select-vehicle',{
                    create: false,
                    sortField: { field: "text", direction: "asc" },
                    onChange: function(value) {
                        if (mileageInput && value && vehicleMileageData[value] !== undefined) {
                            mileageInput.value = vehicleMileageData[value];
                        } else if (mileageInput) {
                            mileageInput.value = '';
                        }
                    }
                });
                
                new TomSelect('#select-driver',{
                    create: false,
                    sortField: { field: "text", direction: "asc" }
                });

                // Si la page est rechargée après une erreur de validation,
                // on s'assure que le kilométrage est bien pré-rempli pour la valeur "old()".
                if (tomSelectVehicle.getValue()) {
                    tomSelectVehicle.trigger('change', tomSelectVehicle.getValue());
                }
            }
        });
    </script>
    @endpush
</x-app-layout>