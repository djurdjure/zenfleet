<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Modifier le Véhicule') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900" x-data="{ currentStep: {{ old('current_step', 1) }} }" x-init="
                    @if ($errors->any())
                        let errors = {{ json_encode($errors->messages()) }};
                        let firstErrorStep = null;

                        const fieldToStepMap = {
                            'registration_plate': 1, 'vin': 1, 'brand': 1, 'model': 1, 'color': 1,
                            'vehicle_type_id': 2, 'fuel_type_id': 2, 'transmission_type_id': 2,
                            'manufacturing_year': 2, 'seats': 2, 'power_hp': 2, 'engine_displacement_cc': 2,
                            'acquisition_date': 3, 'purchase_price': 3, 'current_value': 3,
                            'initial_mileage': 3, 'status_id': 3, 'notes': 3
                        };

                        for (const field in fieldToStepMap) { // Iterate through the map to ensure order
                            if (errors.hasOwnProperty(field)) {
                                firstErrorStep = fieldToStepMap[field];
                                break; // Found the first error field and its step
                            }
                        }

                        if (firstErrorStep !== null) {
                            currentStep = firstErrorStep;
                        } else if ({{ old('current_step', 0) }} > 0) {
                            // Fallback to the last submitted step if no specific field error was found
                            currentStep = {{ old('current_step') }};
                        } else {
                            // Default to step 1 if no errors or old step is available
                            currentStep = 1;
                        }
                    @endif
                ">

                        {{-- Indicateur d'étapes (Stepper) --}}
                        <ol class="flex items-center w-full mb-6">
                                <li class="flex w-full items-center text-violet-600 after:content-[''] after:w-full after:h-1 after:border-b after:border-violet-600 after:border-3 after:inline-block">
                                    <span class="flex items-center justify-center w-10 h-10 bg-violet-100 rounded-full lg:h-12 lg:w-12 shrink-0">
                                        <svg class="w-4 h-4 text-violet-600 lg:w-6 lg:h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </span>
                                </li>
                                <li class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-3 after:inline-block" :class="{ 'text-violet-600 after:border-violet-600': currentStep >= 2 }">
                                    <span class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0" :class="{ 'bg-violet-100': currentStep >= 2, 'bg-gray-100': currentStep < 2 }">
                                        <span x-show="currentStep < 2">2</span>
                                        <svg x-show="currentStep >= 2" class="w-4 h-4 lg:w-6 lg:h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </span>
                                </li>
                                <li class="flex items-center" :class="{ 'text-violet-600': currentStep === 3 }">
                                    <span class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0" :class="{ 'bg-violet-100': currentStep === 3, 'bg-gray-100': currentStep < 3 }">3</span>
                                </li>
                        </ol>

                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-400 text-red-700 p-4" role="alert">
                            <p class="font-bold">Veuillez corriger les erreurs ci-dessous:</p>
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.vehicles.update', $vehicle->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        {{-- Champ caché pour mémoriser l'étape actuelle --}}
                        <input type="hidden" name="current_step" x-model="currentStep">

                        {{-- Étape 1: Informations d'Identification --}}
                        <section x-show="currentStep === 1">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-6">Étape 1: Identification</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label for="registration_plate" class="block font-medium text-sm text-gray-700">Immatriculation <span class="text-red-500">*</span></label>
                                        <x-text-input id="registration_plate" class="block mt-1 w-full" type="text" name="registration_plate" :value="old('registration_plate', $vehicle->registration_plate)" />
                                        <x-input-error :messages="$errors->get('registration_plate')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="vin" class="block font-medium text-sm text-gray-700">Numéro de série (VIN)</label>
                                        <x-text-input id="vin" class="block mt-1 w-full" type="text" name="vin" :value="old('vin', $vehicle->vin)" />
                                        <x-input-error :messages="$errors->get('vin')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="brand" class="block font-medium text-sm text-gray-700">Marque <span class="text-red-500">*</span></label>
                                        <x-text-input id="brand" class="block mt-1 w-full" type="text" name="brand" :value="old('brand', $vehicle->brand)" />
                                        <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="model" class="block font-medium text-sm text-gray-700">Modèle <span class="text-red-500">*</span></label>
                                        <x-text-input id="model" class="block mt-1 w-full" type="text" name="model" :value="old('model', $vehicle->model)" />
                                        <x-input-error :messages="$errors->get('model')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="color" class="block font-medium text-sm text-gray-700">Couleur</label>
                                        <x-text-input id="color" class="block mt-1 w-full" type="text" name="color" :value="old('color', $vehicle->color)" />
                                        <x-input-error :messages="$errors->get('color')" class="mt-2" />
                                    </div>
                            </div>
                        </section>
                        
                        <section x-show="currentStep === 2">
                            <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-6">Étape 2: Caractéristiques Techniques</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                                    <div>
                                        <label for="vehicle_type_id" class="block font-medium text-sm text-gray-700">Type de Véhicule <span class="text-red-500">*</span></label>
                                        <select name="vehicle_type_id" id="vehicle_type_id" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
                                            <option value="">Sélectionnez...</option>
                                            @foreach($vehicleTypes as $type)<option value="{{ $type->id }}" @selected(old('vehicle_type_id', $vehicle->vehicle_type_id) == $type->id)>{{ $type->name }}</option>@endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('vehicle_type_id')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="fuel_type_id" class="block font-medium text-sm text-gray-700">Type de Carburant <span class="text-red-500">*</span></label>
                                        <select name="fuel_type_id" id="fuel_type_id" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
                                            <option value="">Sélectionnez...</option>
                                            @foreach($fuelTypes as $type)<option value="{{ $type->id }}" @selected(old('fuel_type_id', $vehicle->fuel_type_id) == $type->id)>{{ $type->name }}</option>@endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('fuel_type_id')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="transmission_type_id" class="block font-medium text-sm text-gray-700">Type de Transmission <span class="text-red-500">*</span></label>
                                        <select name="transmission_type_id" id="transmission_type_id" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
                                            <option value="">Sélectionnez...</option>
                                            @foreach($transmissionTypes as $type)<option value="{{ $type->id }}" @selected(old('transmission_type_id', $vehicle->transmission_type_id) == $type->id)>{{ $type->name }}</option>@endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('transmission_type_id')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="manufacturing_year" class="block font-medium text-sm text-gray-700">Année de Fabrication</label>
                                        <x-text-input id="manufacturing_year" class="block mt-1 w-full" type="number" name="manufacturing_year" :value="old('manufacturing_year', $vehicle->manufacturing_year)" />
                                        <x-input-error :messages="$errors->get('manufacturing_year')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="seats" class="block font-medium text-sm text-gray-700">Nombre de places</label>
                                        <x-text-input id="seats" class="block mt-1 w-full" type="number" name="seats" :value="old('seats', $vehicle->seats)" />
                                        <x-input-error :messages="$errors->get('seats')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="power_hp" class="block font-medium text-sm text-gray-700">Puissance (CV)</label>
                                        <x-text-input id="power_hp" class="block mt-1 w-full" type="number" name="power_hp" :value="old('power_hp', $vehicle->power_hp)" />
                                        <x-input-error :messages="$errors->get('power_hp')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="engine_displacement_cc" class="block font-medium text-sm text-gray-700">Cylindrée (cc)</label>
                                        <x-text-input id="engine_displacement_cc" class="block mt-1 w-full" type="number" name="engine_displacement_cc" :value="old('engine_displacement_cc', $vehicle->engine_displacement_cc)" />
                                        <x-input-error :messages="$errors->get('engine_displacement_cc')" class="mt-2" />
                                    </div>
                                </div>
                        </section>


                        <section x-show="currentStep === 3">
                            <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-6">Étape 3: Acquisition, Finances & Statut</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label for="acquisition_date" class="block font-medium text-sm text-gray-700">Date d'acquisition</label>
                                        <x-text-input id="acquisition_date" class="block mt-1 w-full" type="date" name="acquisition_date" :value="old('acquisition_date', $vehicle->acquisition_date)" />
                                        <x-input-error :messages="$errors->get('acquisition_date')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="purchase_price" class="block font-medium text-sm text-gray-700">Prix d'achat (DA)</label>
                                        <x-text-input id="purchase_price" class="block mt-1 w-full" type="number" name="purchase_price" step="0.01" :value="old('purchase_price', $vehicle->purchase_price)" />
                                        <x-input-error :messages="$errors->get('purchase_price')" class="mt-2" />
                                    </div>
                                     <div>
                                        <label for="current_value" class="block font-medium text-sm text-gray-700">Valeur actuelle (DA)</label>
                                        <x-text-input id="current_value" class="block mt-1 w-full" type="number" name="current_value" step="0.01" :value="old('current_value', $vehicle->current_value)" />
                                        <x-input-error :messages="$errors->get('current_value')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="initial_mileage" class="block font-medium text-sm text-gray-700">Kilométrage Initial</label>
                                        <x-text-input id="initial_mileage" class="block mt-1 w-full" type="number" name="initial_mileage" :value="old('initial_mileage', $vehicle->initial_mileage)" />
                                        <x-input-error :messages="$errors->get('initial_mileage')" class="mt-2" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="status_id" class="block font-medium text-sm text-gray-700">Statut Initial <span class="text-red-500">*</span></label>
                                        <select name="status_id" id="status_id" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
                                            <option value="">Sélectionnez...</option>
                                            @foreach($vehicleStatuses as $status)<option value="{{ $status->id }}" @selected(old('status_id', $vehicle->status_id) == $status->id)>{{ $status->name }}</option>@endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="notes" class="block font-medium text-sm text-gray-700">Notes</label>
                                        <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">{{ old('notes', $vehicle->notes) }}</textarea>
                                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                                    </div>
                                </div>
                        </section>

                        {{-- (Les autres étapes contiennent maintenant aussi le composant x-input-error sous chaque champ) --}}

                        {{-- Boutons de Navigation --}}
                        <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-between">
                            <div>
                                <button type="button" x-show="currentStep > 1" @click="currentStep--" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                                    Précédent
                                </button>
                            </div>
                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.vehicles.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
                                <button type="button" x-show="currentStep < 3" @click="currentStep++" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">
                                    Suivant
                                </button>
                                <button type="submit" x-show="currentStep === 3" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    Enregistrer le Véhicule
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


