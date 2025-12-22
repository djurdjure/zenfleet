@extends('layouts.admin.catalyst')

@section('title', 'Modifier Fiche Remise N° ' . $handover->assignment->id)

@section('content')
<div class="py-6">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Modifier la Fiche de Remise
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Affectation N° {{ $handover->assignment->id }} — {{ $handover->assignment->vehicle->registration_plate }}
                </p>
            </div>
            <a href="{{ route('admin.handovers.vehicles.show', $handover) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                <x-iconify icon="lucide:arrow-left" class="h-4 w-4 mr-2" />
                Retour
            </a>
        </div>

        <form action="{{ route('admin.handovers.vehicles.update', $handover) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Affichage des erreurs globales --}}
            @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4 mb-6 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-iconify icon="lucide:x-circle" class="h-5 w-5 text-red-400" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Veuillez corriger les erreurs suivantes :
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul role="list" class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                @if(is_array($error))
                                @foreach($error as $subError)
                                <li>{{ $subError }}</li>
                                @endforeach
                                @else
                                <li>{{ $error }}</li>
                                @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- CARTE 1: INFORMATIONS GÉNÉRALES --}}
            <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-4 mb-6 flex items-center gap-2">
                    <x-iconify icon="lucide:info" class="w-5 h-5 text-blue-600" />
                    Informations Générales
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">

                    <div class="p-4 border border-gray-200 rounded-lg flex items-center space-x-4 bg-gray-50">
                        <div class="flex-shrink-0">
                            <x-iconify icon="lucide:user" class="h-10 w-10 text-gray-400" />
                        </div>
                        <div>
                            <p class="text-gray-500 font-semibold uppercase text-xs">Chauffeur</p>
                            <p class="font-bold text-gray-900 text-lg">{{ $handover->assignment->driver->first_name }} {{ $handover->assignment->driver->last_name }}</p>
                            <p class="text-gray-600">{{ $handover->assignment->driver->personal_phone ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="p-4 border border-gray-200 rounded-lg flex items-center space-x-4 bg-gray-50">
                        <div class="flex-shrink-0">
                            <x-iconify icon="lucide:car" class="h-10 w-10 text-gray-400" />
                        </div>
                        <div>
                            <p class="text-gray-500 font-semibold uppercase text-xs">Véhicule</p>
                            <p class="font-bold text-gray-900 text-lg">{{ $handover->assignment->vehicle->brand }} {{ $handover->assignment->vehicle->model }}</p>
                            <p class="text-gray-600 font-mono bg-white px-2 py-0.5 rounded border border-gray-200 inline-block mt-1">{{ $handover->assignment->vehicle->registration_plate }}</p>
                        </div>
                    </div>

                    <div class="p-4 bg-blue-50/50 border border-blue-100 rounded-md">
                        <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-1">Date de Remise <span class="text-red-500">*</span></label>
                        <input id="issue_date" name="issue_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ old('issue_date', $handover->issue_date->format('Y-m-d')) }}" required />
                        @error('issue_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="p-4 bg-gray-50/50 border border-gray-100 rounded-md">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kilométrage (figé)</label>
                        <div class="flex items-center gap-2">
                            <x-iconify icon="lucide:gauge" class="w-5 h-5 text-gray-400" />
                            <p class="text-xl font-bold text-gray-900">{{ number_format($handover->current_mileage, 0, ',', ' ') }} km</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARTE 2: ÉTAT VISUEL ET OBSERVATIONS --}}
            <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-4 mb-6 flex items-center gap-2">
                    <x-iconify icon="lucide:eye" class="w-5 h-5 text-blue-600" />
                    État Visuel du Véhicule
                </h3>
                <div class="flex flex-col md:flex-row gap-8">
                    <div class="flex-shrink-0 mx-auto bg-gray-50 p-4 rounded-xl border border-gray-200 shadow-sm">
                        @if($handover->assignment->vehicle->vehicleType->name === 'Moto')
                        <img src="{{ asset('images/scooter_sketch.png') }}" alt="Croquis Scooter" class="w-48 rounded opacity-80 hover:opacity-100 transition-opacity">
                        @else
                        <img src="{{ asset('images/car_sketch.png') }}" alt="Croquis Voiture" class="w-64 rounded opacity-80 hover:opacity-100 transition-opacity">
                        @endif
                        <p class="text-center text-xs text-gray-400 mt-2 italic">Schéma de référence</p>
                    </div>
                    <div class="flex-1">
                        <label for="general_observations" class="block text-sm font-medium text-gray-700 mb-2">Observations sur l'état extérieur (rayures, bosses, etc.)</label>
                        <textarea name="general_observations" id="general_observations" rows="6" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md placeholder-gray-400">{{ old('general_observations', $handover->general_observations) }}</textarea>
                        @error('general_observations') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- CARTE 3: CHECKLIST DÉTAILLÉE --}}
            <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-4 mb-6 flex items-center gap-2">
                    <x-iconify icon="lucide:clipboard-list" class="w-5 h-5 text-blue-600" />
                    Checklist des Équipements
                </h3>

                <div class="space-y-8">
                    @foreach($checklistStructure as $category => $config)
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide">{{ $category }}</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($config['items'] as $item)
                            @php
                            // Reconstruct the key used in the controller's detailsMap
                            // Controller uses: Str::slug($category, '_') . '.' . Str::slug($item, '_')
                            $lookupKey = Str::slug($category, '_') . '.' . Str::slug($item, '_');
                            $currentStatus = $detailsMap->get($lookupKey);
                            @endphp
                            <div class="bg-white rounded-lg p-3 border border-gray-200 hover:border-blue-300 transition-colors shadow-sm">
                                <x-handover-status-switcher
                                    :item="$item"
                                    :category="$category"
                                    :statuses="$config['statuses']"
                                    :selected="$currentStatus" />
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end mt-8 gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.handovers.vehicles.show', $handover) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <x-iconify icon="lucide:save" class="w-5 h-5 mr-2" />
                    Mettre à jour la Fiche
                </button>
            </div>
        </form>
    </div>
</div>
@endsection