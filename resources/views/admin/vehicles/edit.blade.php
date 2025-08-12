<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier le Véhicule : <span class="text-primary-600">{{ $vehicle->registration_plate }}</span></h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900" x-data="{
                    currentStep: {{ old('current_step', 1) }},
                    initTomSelect() {
                        const tomConfig = (selectedValue) => ({
                            create: false,
                            placeholder: 'Sélectionnez...',
                            items: [selectedValue]
                        });
                        new TomSelect(this.$refs.vehicle_type_id, tomConfig('{{ old('vehicle_type_id', $vehicle->vehicle_type_id) }}'));
                        new TomSelect(this.$refs.fuel_type_id, tomConfig('{{ old('fuel_type_id', $vehicle->fuel_type_id) }}'));
                        new TomSelect(this.$refs.transmission_type_id, tomConfig('{{ old('transmission_type_id', $vehicle->transmission_type_id) }}'));
                        new TomSelect(this.$refs.status_id, tomConfig('{{ old('status_id', $vehicle->status_id) }}'));
                        
                        // Configuration TomSelect pour les utilisateurs avec présélection
                        if (this.$refs.users && window.initUserTomSelect) {
                            const assignedUserIds = @json(old('users', $vehicle->users->pluck('id')->toArray()));
                            window.initUserTomSelect(this.$refs.users, assignedUserIds);
                        }
                    }
                }" x-init="
                    initTomSelect();
                     @if ($errors->any())
                        let errors = {{ json_encode($errors->messages()) }};
                        let firstErrorStep = null;
                        const fieldToStepMap = { 'registration_plate': 1, 'vin': 1, 'brand': 1, 'model': 1, 'color': 1, 'vehicle_type_id': 2, 'fuel_type_id': 2, 'transmission_type_id': 2, 'manufacturing_year': 2, 'seats': 2, 'power_hp': 2, 'engine_displacement_cc': 2, 'acquisition_date': 3, 'purchase_price': 3, 'current_value': 3, 'initial_mileage': 3, 'status_id': 3, 'notes': 3 };
                        for (const field in fieldToStepMap) {
                            if (errors.hasOwnProperty(field)) { firstErrorStep = fieldToStepMap[field]; break; }
                        }
                        if (firstErrorStep) { currentStep = firstErrorStep; }
                    @endif
                ">

                    <ol class="flex items-center w-full mb-8">
                        <li :class="currentStep >= 1 ? 'text-primary-600' : 'text-gray-500'" class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block" :class="currentStep > 1 ? 'after:border-primary-600' : 'after:border-gray-200'">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full shrink-0" :class="currentStep >= 1 ? 'bg-primary-100' : 'bg-gray-100'">
                                <x-lucide-contact class="w-5 h-5"/>
                            </span>
                        </li>
                        <li :class="currentStep >= 2 ? 'text-primary-600' : 'text-gray-500'" class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block" :class="currentStep > 2 ? 'after:border-primary-600' : 'after:border-gray-200'">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full shrink-0" :class="currentStep >= 2 ? 'bg-primary-100' : 'bg-gray-100'">
                                <x-lucide-wrench class="w-5 h-5"/>
                            </span>
                        </li>
                        <li :class="currentStep === 3 ? 'text-primary-600' : 'text-gray-500'" class="flex items-center">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full shrink-0" :class="currentStep === 3 ? 'bg-primary-100' : 'bg-gray-100'">
                                <x-lucide-key-round class="w-5 h-5"/>
                            </span>
                        </li>
                    </ol>

                    <form method="POST" action="{{ route('admin.vehicles.update', $vehicle->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="current_step" x-model="currentStep">

                        <fieldset x-show="currentStep === 1" class="border border-gray-200 p-6 rounded-lg">
                             <legend class="text-lg font-semibold text-gray-800 px-2">Étape 1: Identification</legend>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <x-input-label for="registration_plate" value="Immatriculation" required />
                                    <x-text-input id="registration_plate" name="registration_plate" :value="old('registration_plate', $vehicle->registration_plate)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('registration_plate')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="vin" value="Numéro de série (VIN)" />
                                    <x-text-input id="vin" name="vin" :value="old('vin', $vehicle->vin)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('vin')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="brand" value="Marque" required />
                                    <x-text-input id="brand" name="brand" :value="old('brand', $vehicle->brand)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="model" value="Modèle" required />
                                    <x-text-input id="model" name="model" :value="old('model', $vehicle->model)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('model')" class="mt-2" />
                                </div>
                                 <div class="md:col-span-2">
                                    <x-input-label for="color" value="Couleur" />
                                    <x-text-input id="color" name="color" :value="old('color', $vehicle->color)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('color')" class="mt-2" />
                                </div>
                            </div>
                        </fieldset>

                        <fieldset x-show="currentStep === 2" style="display: none;" class="border border-gray-200 p-6 rounded-lg">
                             <legend class="text-lg font-semibold text-gray-800 px-2">Étape 2: Caractéristiques Techniques</legend>
                             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                                <div>
                                    <x-input-label for="vehicle_type_id" value="Type de Véhicule" required />
                                    <select x-ref="vehicle_type_id" name="vehicle_type_id" id="vehicle_type_id">
                                        @foreach($vehicleTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('vehicle_type_id')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="fuel_type_id" value="Type de Carburant" required />
                                    <select x-ref="fuel_type_id" name="fuel_type_id" id="fuel_type_id">
                                        @foreach($fuelTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('fuel_type_id')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="transmission_type_id" value="Type de Transmission" required />
                                    <select x-ref="transmission_type_id" name="transmission_type_id" id="transmission_type_id">
                                        @foreach($transmissionTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('transmission_type_id')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="manufacturing_year" value="Année de Fabrication" />
                                    <x-text-input id="manufacturing_year" type="number" name="manufacturing_year" :value="old('manufacturing_year', $vehicle->manufacturing_year)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('manufacturing_year')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="seats" value="Nombre de places" />
                                    <x-text-input id="seats" type="number" name="seats" :value="old('seats', $vehicle->seats)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('seats')" class="mt-2" />
                                </div>
                                 <div>
                                    <x-input-label for="power_hp" value="Puissance (CV)" />
                                    <x-text-input id="power_hp" type="number" name="power_hp" :value="old('power_hp', $vehicle->power_hp)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('power_hp')" class="mt-2" />
                                </div>
                                <div class="lg:col-span-3">
                                    <x-input-label for="engine_displacement_cc" value="Cylindrée (cc)" />
                                    <x-text-input id="engine_displacement_cc" type="number" name="engine_displacement_cc" :value="old('engine_displacement_cc', $vehicle->engine_displacement_cc)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('engine_displacement_cc')" class="mt-2" />
                                </div>
                            </div>
                        </fieldset>

                        <fieldset x-show="currentStep === 3" style="display: none;" class="border border-gray-200 p-6 rounded-lg">
                             <legend class="text-lg font-semibold text-gray-800 px-2">Étape 3: Acquisition & Statut</legend>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <x-input-label for="acquisition_date" value="Date d'acquisition" />
                                    <x-text-input id="acquisition_date" type="date" name="acquisition_date" :value="old('acquisition_date', $vehicle->acquisition_date?->format('Y-m-d'))" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('acquisition_date')" class="mt-2" />
                                </div>
                                 <div>
                                    <x-input-label for="purchase_price" value="Prix d'achat (DA)" />
                                    <x-text-input id="purchase_price" type="number" step="0.01" name="purchase_price" :value="old('purchase_price', $vehicle->purchase_price)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('purchase_price')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="current_value" value="Valeur actuelle (DA)" />
                                    <x-text-input id="current_value" type="number" step="0.01" name="current_value" :value="old('current_value', $vehicle->current_value)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('current_value')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="current_mileage" value="Kilométrage Actuel" />
                                    <x-text-input id="current_mileage" type="number" name="current_mileage" :value="old('current_mileage', $vehicle->current_mileage)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('current_mileage')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="status_id" value="Statut" required />
                                    <select x-ref="status_id" name="status_id" id="status_id">
                                        @foreach($vehicleStatuses as $status)<option value="{{ $status->id }}">{{ $status->name }}</option>@endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
                                </div>
                                {{-- --- CHAMP UTILISATEURS AMÉLIORÉ --- --}}
                                <div class="md:col-span-2">
                                    <x-input-label for="users" value="Utilisateurs Autorisés" />
                                    
                                    <select name="users[]" id="users" multiple x-ref="users" class="tomselect-users">
                                        {{-- On prépare en PHP la liste des IDs déjà assignés --}}
                                        @php
                                            $assignedUserIds = old('users', $vehicle->users->pluck('id')->toArray());
                                        @endphp

                                        {{-- On boucle sur tous les utilisateurs disponibles pour créer les options --}}
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                    data-name="{{ $user->name }}" 
                                                    data-email="{{ $user->email }}"
                                                    @selected(in_array($user->id, $assignedUserIds))>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Recherchez et sélectionnez les utilisateurs autorisés à utiliser ce véhicule.
                                    </p>
                                    <x-input-error :messages="$errors->get('users')" class="mt-2" />
                                </div>
                                {{-- --- FIN CHAMP UTILISATEURS --- --}}
                                 <div class="md:col-span-2">
                                    <x-input-label for="notes" value="Notes" />
                                    <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">{{ old('notes', $vehicle->notes) }}</textarea>
                                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                                </div>
                                
                             </div>
                        </fieldset>

                        <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-between">
                            <x-secondary-button type="button" x-show="currentStep > 1" @click="currentStep--">Précédent</x-secondary-button>
                            <div class="flex-grow"></div>
                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.vehicles.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
                                <x-primary-button type="button" x-show="currentStep < 3" @click="currentStep++">Suivant</x-primary-button>
                                <button type="submit" x-show="currentStep === 3" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    Enregistrer les Modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>