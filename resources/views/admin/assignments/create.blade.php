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
                    <h3 class="text-xl font-semibold text-gray-700 mb-6">Détails de l'Affectation</h3>

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Veuillez corriger les erreurs :</p>
                            <ul class="mt-2 list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.assignments.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label for="select-vehicle" class="block font-medium text-sm text-gray-700">Véhicule <span class="text-red-500">*</span></label>
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

                            <div>
                                <label for="select-driver" class="block font-medium text-sm text-gray-700">Chauffeur <span class="text-red-500">*</span></label>
                                <select name="driver_id" id="select-driver" placeholder="Recherchez un chauffeur..." required>
                                    <option value="">Sélectionnez un chauffeur disponible</option>
                                    @foreach($availableDrivers as $driver)
                                        <option value="{{ $driver->id }}" @selected(old('driver_id') == $driver->id)>{{ $driver->first_name }} {{ $driver->last_name }}</option>
                                    @endforeach
                                </select>
                                 <x-input-error :messages="$errors->get('driver_id')" class="mt-2" />
                            </div>

                            <div>
                                <label for="start_datetime" class="block font-medium text-sm text-gray-700">Date et Heure de Début <span class="text-red-500">*</span></label>
                                <x-text-input id="start_datetime" class="block mt-1 w-full" type="datetime-local" name="start_datetime" :value="old('start_datetime', now()->format('Y-m-d\TH:i'))" required />
                            </div>

                            <div>
                                <label for="start_mileage" class="block font-medium text-sm text-gray-700">Kilométrage de Début <span class="text-red-500">*</span></label>
                                <x-text-input id="start_mileage" class="block mt-1 w-full" type="number" name="start_mileage" :value="old('start_mileage')" required />
                            </div>

                            <div class="md:col-span-2">
                                <label for="reason" class="block font-medium text-sm text-gray-700">Motif de l'affectation</label>
                                <x-text-input id="reason" name="reason" :value="old('reason')" class="block mt-1 w-full" type="text" />
                            </div>

                            <div class="md:col-span-2">
                                <label for="notes" class="block font-medium text-sm text-gray-700">Notes</label>
                                <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-4">
                            <a href="{{ route('admin.assignments.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">
                                Créer l'Affectation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // On s'assure que TomSelect a été chargé (depuis app.js)
            if (window.TomSelect) {
                // --- CORRECTION DÉFINITIVE DE LA LOGIQUE ---
                // 1. On prépare un objet JS simple: { id_vehicule: kilometrage, ... }
                const vehicleMileageData = @json($availableVehicles->pluck('current_mileage', 'id'));
                const mileageInput = document.getElementById('start_mileage');

                // 2. On initialise TomSelect pour les véhicules
                let tomSelectVehicle = new TomSelect('#select-vehicle',{
                    create: false,
                    sortField: { field: "text", direction: "asc" },
                    onChange: function(value) {
                        // 3. Au changement, on utilise notre objet JS pour trouver le kilométrage
                        if (mileageInput && value && vehicleMileageData[value] !== undefined) {
                            mileageInput.value = vehicleMileageData[value];
                        } else if (mileageInput) {
                            mileageInput.value = '';
                        }
                    }
                });

                // On initialise TomSelect pour les chauffeurs (sans logique additionnelle)
                new TomSelect('#select-driver',{
                    create: false,
                    sortField: { field: "text", direction: "asc" }
                });

                // 4. Si la page est rechargée après une erreur de validation,
                // on s'assure que le kilométrage est bien pré-rempli pour la valeur "old()".
                if (tomSelectVehicle.getValue()) {
                    tomSelectVehicle.trigger('change', tomSelectVehicle.getValue());
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
