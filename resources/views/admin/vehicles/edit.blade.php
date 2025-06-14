<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Modifier le Véhicule : <span class="text-violet-700">{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->registration_plate }})</span>
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ currentStep: 1, showMaintenanceModal: false }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900" x-data="{ currentStep: 1 }">

                    {{-- Indicateur d'étapes (Stepper) - Identique à la vue de création --}}
                    <div class="mb-8">
                        <ol class="flex items-center w-full">
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
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Veuillez corriger les erreurs :</p>
                            <ul class="mt-2 list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}">
                        @csrf
                        @method('PUT')
                        <div class="space-y-8">
                            {{-- Étape 1 --}}
                            <div x-show="currentStep === 1">
                                <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-6">Étape 1: Informations d'Identification</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="registration_plate" class="block font-medium text-sm text-gray-700">Immatriculation <span class="text-red-500">*</span></label>
                                        <x-text-input id="registration_plate" class="block mt-1 w-full" type="text" name="registration_plate" :value="old('registration_plate', $vehicle->registration_plate)" required />
                                        <x-input-error :messages="$errors->get('registration_plate')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="vin" class="block font-medium text-sm text-gray-700">Numéro de série (VIN)</label>
                                        <x-text-input id="vin" class="block mt-1 w-full" type="text" name="vin" :value="old('vin', $vehicle->vin)" />
                                        <x-input-error :messages="$errors->get('vin')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="brand" class="block font-medium text-sm text-gray-700">Marque <span class="text-red-500">*</span></label>
                                        <x-text-input id="brand" class="block mt-1 w-full" type="text" name="brand" :value="old('brand', $vehicle->brand)" required />
                                        <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="model" class="block font-medium text-sm text-gray-700">Modèle <span class="text-red-500">*</span></label>
                                        <x-text-input id="model" class="block mt-1 w-full" type="text" name="model" :value="old('model', $vehicle->model)" required />
                                        <x-input-error :messages="$errors->get('model')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="color" class="block font-medium text-sm text-gray-700">Couleur</label>
                                        <x-text-input id="color" class="block mt-1 w-full" type="text" name="color" :value="old('color', $vehicle->color)" />
                                        <x-input-error :messages="$errors->get('color')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            {{-- Étape 2 --}}
                            <div x-show="currentStep === 2" style="display: none;">
                                <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-6">Étape 2: Caractéristiques Techniques</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div>
                                        <label for="vehicle_type_id" class="block font-medium text-sm text-gray-700">Type de Véhicule <span class="text-red-500">*</span></label>
                                        <select name="vehicle_type_id" id="vehicle_type_id" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm" required>
                                            @foreach($vehicleTypes as $type)<option value="{{ $type->id }}" @selected(old('vehicle_type_id', $vehicle->vehicle_type_id) == $type->id)>{{ $type->name }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="fuel_type_id" class="block font-medium text-sm text-gray-700">Type de Carburant <span class="text-red-500">*</span></label>
                                        <select name="fuel_type_id" id="fuel_type_id" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm" required>
                                            @foreach($fuelTypes as $type)<option value="{{ $type->id }}" @selected(old('fuel_type_id', $vehicle->fuel_type_id) == $type->id)>{{ $type->name }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="transmission_type_id" class="block font-medium text-sm text-gray-700">Type de Transmission <span class="text-red-500">*</span></label>
                                        <select name="transmission_type_id" id="transmission_type_id" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm" required>
                                            @foreach($transmissionTypes as $type)<option value="{{ $type->id }}" @selected(old('transmission_type_id', $vehicle->transmission_type_id) == $type->id)>{{ $type->name }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="manufacturing_year" class="block font-medium text-sm text-gray-700">Année de Fabrication</label>
                                        <x-text-input id="manufacturing_year" class="block mt-1 w-full" type="number" name="manufacturing_year" :value="old('manufacturing_year', $vehicle->manufacturing_year)" />
                                    </div>
                                    <div>
                                        <label for="seats" class="block font-medium text-sm text-gray-700">Nombre de places</label>
                                        <x-text-input id="seats" class="block mt-1 w-full" type="number" name="seats" :value="old('seats', $vehicle->seats)" />
                                    </div>
                                    <div>
                                        <label for="power_hp" class="block font-medium text-sm text-gray-700">Puissance (CV)</label>
                                        <x-text-input id="power_hp" class="block mt-1 w-full" type="number" name="power_hp" :value="old('power_hp', $vehicle->power_hp)" />
                                    </div>
                                    <div>
                                        <label for="engine_displacement_cc" class="block font-medium text-sm text-gray-700">Cylindrée (cc)</label>
                                        <x-text-input id="engine_displacement_cc" class="block mt-1 w-full" type="number" name="engine_displacement_cc" :value="old('engine_displacement_cc', $vehicle->engine_displacement_cc)" />
                                    </div>
                                </div>
                            </div>

                            {{-- Étape 3 --}}
                            <div x-show="currentStep === 3" style="display: none;">
                                <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-6">Étape 3: Acquisition, Finances & Statut</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="acquisition_date" class="block font-medium text-sm text-gray-700">Date d'acquisition</label>
                                        <x-text-input id="acquisition_date" class="block mt-1 w-full" type="date" name="acquisition_date" :value="old('acquisition_date', $vehicle->acquisition_date)" />
                                    </div>
                                    <div>
                                        <label for="purchase_price" class="block font-medium text-sm text-gray-700">Prix d'achat (DA)</label>
                                        <x-text-input id="purchase_price" class="block mt-1 w-full" type="number" name="purchase_price" step="0.01" :value="old('purchase_price', $vehicle->purchase_price)" />
                                    </div>
                                     <div>
                                        <label for="current_value" class="block font-medium text-sm text-gray-700">Valeur actuelle (DA)</label>
                                        <x-text-input id="current_value" class="block mt-1 w-full" type="number" name="current_value" step="0.01" :value="old('current_value', $vehicle->current_value)" />
                                    </div>
                                    <div>
                                        <label for="current_mileage" class="block font-medium text-sm text-gray-700">Kilométrage Actuel</label>
                                        <x-text-input id="current_mileage" class="block mt-1 w-full" type="number" name="current_mileage" :value="old('current_mileage', $vehicle->current_mileage)" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="status_id" class="block font-medium text-sm text-gray-700">Statut <span class="text-red-500">*</span></label>
                                        <select name="status_id" id="status_id" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm" required>
                                            @foreach($vehicleStatuses as $status)<option value="{{ $status->id }}" @selected(old('status_id', $vehicle->status_id) == $status->id)>{{ $status->name }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="notes" class="block font-medium text-sm text-gray-700">Notes</label>
                                        <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">{{ old('notes', $vehicle->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>


                        </div>



                        <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-between">
                            <div>
                                <button type="button" x-show="currentStep > 1" @click="currentStep--" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">Précédent</button>
                            </div>






                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.vehicles.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
                                <button type="button" x-show="currentStep < 3" @click="currentStep++" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">Suivant</button>
                                <button type="submit" x-show="currentStep === 3" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    Enregistrer les Modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

                                                            {{-- NOUVELLE SECTION : Plans de Maintenance Préventive --}}
             @can('manage maintenance plans')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-500">Plans de Maintenance</h3>
                    </div>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Type de Maintenance</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Récurrence</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Prochaine Échéance</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($vehicle->maintenancePlans as $plan)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 text-sm text-gray-800">{{ $plan->maintenanceType->name }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-500">{{ $plan->recurrence_value }} {{ $plan->recurrenceUnit->name }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-500">
                                            @if($plan->next_due_date) {{ $plan->next_due_date->format('d/m/Y') }} @endif
                                            @if($plan->next_due_mileage) ({{ number_format($plan->next_due_mileage, 0, ',', ' ') }} km) @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Aucun plan de maintenance défini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endcan

        </div>

    </div>
</x-app-layout>
