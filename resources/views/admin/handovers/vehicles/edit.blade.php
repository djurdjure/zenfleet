<x-app-layout>
 <x-slot name="header">
 <h2 class="font-semibold text-xl text-gray-800 leading-tight">
 {{ __("Modifier la Fiche de Remise N°") }} {{ $handover->id }}
 </h2>
 </x-slot>

 <div class="py-12">
 <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
 <form action="{{ route('admin.handovers.vehicles.update', $handover) }}" method="POST" class="space-y-6">
 @csrf
 @method('PUT')

 {{-- CARTE 1: INFORMATIONS GÉNÉRALES --}}
 <div class="bg-white p-6 shadow-sm sm:rounded-lg">
 <div class="text-center mb-6">
 <h3 class="text-3xl font-bold text-gray-900 mb-2">MODIFICATION FICHE DE REMISE</h3>
 <p class="text-sm text-gray-500">Pour l'Affectation N° {{ $handover->assignment->id }}</p>
 </div>

 <div class="flex flex-col md:flex-row justify-between gap-x-8 text-sm">
 
 <div class="flex-grow">
 <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 border rounded-md p-6">
 <div>
 <p class="text-gray-500 font-semibold">Chauffeur</p>
 <p class="font-bold text-lg text-gray-900">{{ $handover->assignment->driver->first_name }} {{ $handover->assignment->driver->last_name }}</p>
 <p class="text-gray-600">Matricule: {{ $handover->assignment->driver->employee_number ?? 'N/A' }}</p>
 <p class="text-gray-600">Téléphone: {{ $handover->assignment->driver->personal_phone ?? 'N/A' }}</p>
 </div>
 <div>
 <p class="text-gray-500 font-semibold">Véhicule</p>
 <p class="font-bold text-lg text-gray-900">{{ $handover->assignment->vehicle->brand }} {{ $handover->assignment->vehicle->model }}</p>
 <p class="text-gray-600 font-mono">{{ $handover->assignment->vehicle->registration_plate }}</p>
 <p class="text-gray-600">Kilométrage (à la remise): {{ $handover->current_mileage }} km</p>
 </div>
 </div>
 </div>

 <div class="flex-shrink-0 w-full md:w-1/3 mt-6 md:mt-0">
 <div class="border rounded-md p-6 h-full">
 <label for="issue_date" class="block font-medium text-sm text-gray-700">Date de Remise <span class="text-red-500">*</span></label>
 <x-text-input id="issue_date" name="issue_date" type="date" class="mt-1 block w-full" :value="old('issue_date', $handover->issue_date->format('Y-m-d'))" required />
 <x-input-error :messages="$errors->get('issue_date')" class="mt-2" />
 </div>
 </div>

 </div>
 </div>

 {{-- CARTE 2: ÉTAT VISUEL ET OBSERVATIONS --}}
 <div class="bg-white p-6 shadow-sm sm:rounded-lg">
 <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-4 mb-6">État Visuel du Véhicule</h3>
 <div class="flex flex-col md:flex-row gap-6 border rounded-md p-6">
 <div class="flex-shrink-0 mx-auto bg-gray-100 p-4 rounded-md shadow-inner">
 @if($handover->assignment->vehicle->vehicleType->name === 'Moto')
 <img src="{{ asset('images/scooter_sketch.png') }}" alt="Croquis Scooter" class="w-32 rounded">
 @else
 <img src="{{ asset('images/car_sketch.png') }}" alt="Croquis Voiture" class="w-48 rounded">
 @endif
 </div>
 <div class="flex-1">
 <label for="general_observations" class="block font-medium text-sm text-gray-700">Observations sur l'état extérieur (rayures, bosses, etc.)</label>
 <textarea name="general_observations" id="general_observations" rows="5" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">{{ old('general_observations', $handover->general_observations) }}</textarea>
 <x-input-error :messages="$errors->get('general_observations')" class="mt-2" />
 </div>
 </div>
 </div>

 {{-- CARTE 3: CHECKLIST DÉTAILLÉE --}}
 <div class="bg-white p-6 shadow-sm sm:rounded-lg">
 <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-4 mb-6">Checklist des Équipements</h3>
 @php
 $isMoto = $handover->assignment->vehicle->vehicleType->name === 'Moto';
 // Important: Utiliser des clés qui matchent le slug dans le contrôleur (ex: 'Papiers_&_Accessoires')
 $checklistConfig = $isMoto ? [
 'Papiers_&_Accessoires' => ['Carte Grise', 'Assurance', 'Carte Carburant', 'Clé', 'Casque', 'Top-case'],
 'État_Général' => ['Pneu Avant', 'Pneu Arrière', 'Saute-vent', 'Rétroviseur Gauche', 'Rétroviseur Droit', 'Verrouillage', 'Feux avant', 'Feux arrières', 'Carrosserie Générale', 'Propreté']
 ] : [
 'Papiers_du_véhicule' => ['Carte Grise', 'Assurance', 'Vignette', 'Contrôle technique', 'Permis de circuler', 'Carte Carburant'],
 'Accessoires_Intérieur' => ['Triangle', 'Cric', 'Manivelle/Clé', 'Gilet', 'Tapis', 'Extincteur', 'Trousse de secours', 'Rétroviseur intérieur', 'Pare-soleil', 'Autoradio', 'Propreté'],
 'Pneumatiques' => ['Roue AV Gauche', 'Roue AV Droite', 'Roue AR Gauche', 'Roue AR Droite', 'Roue de Secours', 'Enjoliveurs'],
 'État_Extérieur' => ['Vitres', 'Pare-brise', 'Rétroviseur Gauche', 'Rétroviseur Droit', 'Verrouillage', 'Poignées', 'Feux avant', 'Feux arrières', 'Essuie-glaces', 'Carrosserie Générale']
 ];
 $binaryStatuses = ['Oui', 'Non', 'N/A'];
 $conditionStatuses = ['Bon', 'Moyen', 'Mauvais', 'N/A'];
 @endphp

 @if($isMoto)
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 @foreach($checklistConfig as $categoryKey => $items)
 <div class="bg-gray-50 rounded-md p-4 border border-gray-200">
 <h4 class="font-semibold text-gray-700 mb-3">{{ str_replace('_', ' ', $categoryKey) }}</h4>
 <div class="space-y-2">
 @foreach($items as $item)
 @php
 $statusesToUse = $categoryKey === 'Papiers_&_Accessoires' ? $binaryStatuses : $conditionStatuses;
 $itemKey = Str::slug($item, '_');
 $currentStatus = $detailsMap->get($categoryKey . '.' . $itemKey);
 @endphp
 <div class="bg-white rounded-md shadow-sm p-2">
 <x-handover-status-switcher :item="$item" :category="$categoryKey" :statuses="$statusesToUse" :currentStatus="$currentStatus" />
 </div>
 @endforeach
 </div>
 </div>
 @endforeach
 </div>
 @else
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div class="space-y-6">
 @foreach(['Papiers_du_véhicule', 'Accessoires_Intérieur'] as $categoryKey)
 <div class="bg-gray-50 rounded-md p-4 border border-gray-200">
 <h4 class="font-semibold text-gray-700 mb-3">{{ str_replace('_', ' ', $categoryKey) }}</h4>
 <div class="space-y-2">
 @foreach($checklistConfig[$categoryKey] as $item)
 @php
 $itemKey = Str::slug($item, '_');
 $currentStatus = $detailsMap->get($categoryKey . '.' . $itemKey);
 @endphp
 <div class="bg-white rounded-md shadow-sm p-2">
 <x-handover-status-switcher :item="$item" :category="$categoryKey" :statuses="$binaryStatuses" :currentStatus="$currentStatus" />
 </div>
 @endforeach
 </div>
 </div>
 @endforeach
 </div>

 <div class="space-y-6">
 @foreach(['Pneumatiques', 'État_Extérieur'] as $categoryKey)
 <div class="bg-gray-50 rounded-md p-4 border border-gray-200">
 <h4 class="font-semibold text-gray-700 mb-3">{{ str_replace('_', ' ', $categoryKey) }}</h4>
 <div class="space-y-2">
 @foreach($checklistConfig[$categoryKey] as $item)
 @php
 $itemKey = Str::slug($item, '_');
 $currentStatus = $detailsMap->get($categoryKey . '.' . $itemKey);
 @endphp
 <div class="bg-white rounded-md shadow-sm p-2">
 <x-handover-status-switcher :item="$item" :category="$categoryKey" :statuses="$conditionStatuses" :currentStatus="$currentStatus" />
 </div>
 @endforeach
 </div>
 </div>
 @endforeach
 </div>
 </div>
 @endif
 </div>

 <div class="bg-white p-6 shadow-sm sm:rounded-lg">
 <div class="flex items-center justify-end mt-8 gap-4 border-t border-gray-200 pt-6">
 <a href="{{ route('admin.handovers.vehicles.show', $handover) }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
 <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700">
 Mettre à jour la Fiche
 </button>
 </div>
 </div>
 </form>
 </div>
 </div>
</x-app-layout>