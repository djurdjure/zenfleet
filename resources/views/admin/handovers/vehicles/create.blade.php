<x-app-layout>
   <x-slot name="header">
       <h2 class="font-semibold text-xl text-gray-800 leading-tight">
           {{ __('Nouvelle Fiche de Remise de Véhicule') }}
       </h2>
   </x-slot>

   <div class="py-12">
       <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
           <form action="{{ route('admin.handovers.vehicles.store') }}" method="POST" class="space-y-6">
               @csrf
               <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

               <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                   <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-4 mb-6">Informations Générales</h3>
                   <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                       <div class="p-4 border rounded-lg flex items-center space-x-4">
                           <div class="flex-shrink-0">
                               <x-icon icon="heroicons:user"-circle-2 class="h-10 w-10 text-gray-400"/ />
                           </div>
                           <div>
                               <p class="text-gray-500 font-semibold">Chauffeur</p>
                               <p class="font-bold text-gray-900">{{ $assignment->driver->first_name }} {{ $assignment->driver->last_name }}</p>
                               <p class="text-gray-600">Matricule: {{ $assignment->driver->employee_number ?? 'N/A' }}</p>
                           </div>
                       </div>
                       <div class="p-4 border rounded-lg flex items-center space-x-4">
                           <div class="flex-shrink-0">
                               <x-icon icon="heroicons:truck" class="h-10 w-10 text-gray-400"/ />
                           </div>
                           <div>
                               <p class="text-gray-500 font-semibold">Véhicule</p>
                               <p class="font-bold text-gray-900">{{ $assignment->vehicle->brand }} {{ $assignment->vehicle->model }}</p>
                               <p class="text-gray-600 font-mono">{{ $assignment->vehicle->registration_plate }}</p>
                           </div>
                       </div>
                       <div class="p-4 bg-gray-50 rounded-md">
                           <x-input-label for="issue_date" value="Date de Remise" required />
                           <x-text-input id="issue_date" name="issue_date" type="date" class="mt-1 block w-full" :value="old('issue_date', now()->format('Y-m-d'))" required />
                           <x-input-error :messages="$errors->get('issue_date')" class="mt-2" />
                       </div>
                       <div class="p-4 bg-gray-50 rounded-md">
                            <x-input-label for="current_mileage" value="Kilométrage Actuel" />
                            <p class="text-lg font-semibold text-gray-800 mt-1">{{ number_format($assignment->vehicle->current_mileage, 0, ',', ' ') }} km</p>
                       </div>
                   </div>
               </div>

               <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-4 mb-6">État Visuel et Observations</h3>
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="flex-shrink-0 mx-auto bg-gray-100 p-2 rounded-lg shadow-inner">
                             @if($assignment->vehicle->vehicleType->name === 'Moto')
                                <img src="{{ asset('images/scooter_sketch.png') }}" alt="Croquis Scooter" class="w-32 rounded">
                            @else
                                <img src="{{ asset('images/car_sketch.png') }}" alt="Croquis Voiture" class="w-48 rounded">
                            @endif
                        </div>
                        <div class="flex-1">
                            <x-input-label for="general_observations" value="Observations sur l'état extérieur (rayures, bosses, etc.)" />
                            <textarea name="general_observations" id="general_observations" rows="5" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">{{ old('general_observations') }}</textarea>
                            <x-input-error :messages="$errors->get('general_observations')" class="mt-2" />
                        </div>
                    </div>
               </div>

               <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                   <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-4 mb-6">Checklist des Équipements</h3>
                   @php
                       $isMoto = $assignment->vehicle->vehicleType->name === 'Moto';
                       $checklist = $isMoto ? [
                           'Papiers & Accessoires' => ['Carte Grise', 'Assurance', 'Carte Carburant', 'Clé', 'Casque', 'Top-case'],
                           'État Général' => ['Pneu Avant', 'Pneu Arrière', 'Saute-vent', 'Rétroviseur Gauche', 'Rétroviseur Droit', 'Verrouillage', 'Feux avant', 'Feux arrières', 'Carrosserie Générale', 'Propreté']
                       ] : [
                           'Papiers du véhicule' => ['Carte Grise', 'Assurance', 'Vignette', 'Contrôle technique', 'Permis de circuler', 'Carte Carburant'],
                           'Accessoires Intérieur' => ['Triangle', 'Cric', 'Manivelle/Clé', 'Gilet', 'Tapis', 'Extincteur', 'Trousse de secours', 'Rétroviseur intérieur', 'Pare-soleil', 'Autoradio', 'Propreté'],
                           'Pneumatiques' => ['Roue AV Gauche', 'Roue AV Droite', 'Roue AR Gauche', 'Roue AR Droite', 'Roue de Secours', 'Enjoliveurs'],
                           'État Extérieur' => ['Vitres', 'Pare-brise', 'Rétroviseur Gauche', 'Rétroviseur Droit', 'Verrouillage', 'Poignées', 'Feux avant', 'Feux arrières', 'Essuie-glaces', 'Carrosserie Générale']
                       ];
                       $binaryStatuses = ['Oui', 'Non', 'N/A'];
                       $conditionStatuses = ['Bon', 'Moyen', 'Mauvais', 'N/A'];
                   @endphp

                   <div class="space-y-8">
                       @if($isMoto)
                           <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                               @foreach($checklist as $category => $items)
                                   <div class="space-y-3">
                                       <h4 class="font-semibold text-gray-700">{{ $category }}</h4>
                                       @foreach($items as $item)
                                           @php $statusesToUse = in_array($category, ['Papiers & Accessoires']) ? $binaryStatuses : $conditionStatuses; @endphp
                                           <div class="bg-gray-50 rounded-md p-2 border border-gray-200">
                                               <x-handover-status-switcher :item="$item" :category="$category" :statuses="$statusesToUse" />
                                           </div>
                                       @endforeach
                                   </div>
                               @endforeach
                           </div>
                       @else
                           @php
                               $rows = [
                                   ['Papiers du véhicule', 'Pneumatiques'],
                                   ['Accessoires Intérieur', 'État Extérieur']
                               ];
                           @endphp
                           @foreach($rows as $row)
                           <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">
                               @foreach($row as $categoryName)
                                   <div class="space-y-3">
                                       <h4 class="font-semibold text-gray-700">{{ $categoryName }}</h4>
                                       @foreach($checklist[$categoryName] as $item)
                                           @php $statusesToUse = in_array($categoryName, ['Papiers du véhicule', 'Accessoires Intérieur']) ? $binaryStatuses : $conditionStatuses; @endphp
                                           <div class="bg-gray-50 rounded-md p-2 border border-gray-200">
                                                <x-handover-status-switcher :item="$item" :category="$categoryName" :statuses="$statusesToUse" />
                                           </div>
                                       @endforeach
                                   </div>
                               @endforeach
                           </div>
                           @endforeach
                       @endif
                   </div>
               </div>

               <div class="flex items-center justify-end mt-8 gap-4">
                   <a href="{{ route('admin.assignments.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
                   <x-primary-button>
                        <x-icon icon="heroicons:check-circle" class="w-5 h-5 mr-2"/ />
                        Créer la Fiche
                   </x-primary-button>
               </div>
           </form>
       </div>
   </div>
</x-app-layout>