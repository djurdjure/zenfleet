<x-app-layout>
   <x-slot name="header">
       <h2 class="font-semibold text-xl text-gray-800 leading-tight">
           {{ __('Fiche de Remise de Véhicule') }}
       </h2>
   </x-slot>

   <div class="py-12">
       <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
           <form action="{{ route('admin.handovers.vehicles.store') }}" method="POST" class="space-y-6">
               @csrf
               <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

               {{-- CARTE 1: INFORMATIONS GÉNÉRALES --}}
               <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                   <div class="text-center mb-6">
                       <h3 class="text-2xl font-bold text-gray-900">FICHE DE REMISE DE VÉHICULE</h3>
                       <p class="text-sm text-gray-500">Pour l'Affectation N° {{ $assignment->id }}</p>
                   </div>
                   <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6 text-sm border-t border-b border-gray-200 py-6">
                       <div>
                           <p class="text-gray-500 font-semibold">Chauffeur</p>
                           <p class="font-bold text-lg text-gray-900">{{ $assignment->driver->first_name }} {{ $assignment->driver->last_name }}</p>
                           <p class="text-gray-600">Matricule: {{ $assignment->driver->employee_number ?? 'N/A' }}</p>
                           <p class="text-gray-600">Tel: {{ $assignment->driver->personal_phone ?? 'N/A' }}</p>
                       </div>
                       <div>
                           <p class="text-gray-500 font-semibold">Véhicule</p>
                           <p class="font-bold text-lg text-gray-900">{{ $assignment->vehicle->brand }} {{ $assignment->vehicle->model }}</p>
                           <p class="text-gray-600 font-mono">{{ $assignment->vehicle->registration_plate }}</p>
                           <p class="text-gray-600">Kilométrage: <span class="font-semibold">{{ number_format($assignment->vehicle->current_mileage, 0, ',', ' ') }} km</span></p>
                       </div>
                       <div>
                           <label for="issue_date" class="block font-medium text-sm text-gray-700">Date de Remise <span class="text-red-500">*</span></label>
                           <x-text-input id="issue_date" name="issue_date" type="date" class="mt-1 block w-full" :value="old('issue_date', now()->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('issue_date')" class="mt-2" />
                       </div>
                       <div class="md:col-span-3">
                            <label for="reason" class="block font-medium text-sm text-gray-700">Motif de l'affectation</label>
                            <p class="mt-1 p-3 bg-gray-50 rounded-md text-gray-800 w-full border border-gray-200">{{ $assignment->reason ?? 'Non spécifié' }}</p>
                       </div>
                   </div>
               </div>

               {{-- CARTE 2: ÉTAT VISUEL ET OBSERVATIONS --}}
               <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-4 mb-6">État Visuel du Véhicule</h3>
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="flex-shrink-0 mx-auto bg-white p-2 rounded-lg">
                            @if($assignment->vehicle->vehicleType->name === 'Moto')
                                <img src="{{ asset('images/scooter_sketch.png') }}" alt="Croquis Scooter" class="w-32 border rounded">
                            @else
                                <img src="{{ asset('images/car_sketch.png') }}" alt="Croquis Voiture" class="w-48 border rounded">
                            @endif
                        </div>
                        <div class="flex-1">
                           <label for="general_observations" class="block font-medium text-sm text-gray-700">Observations sur l'état extérieur (rayures, bosses, etc.)</label>
                           <textarea name="general_observations" id="general_observations" rows="5" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">{{ old('general_observations') }}</textarea>
                           <x-input-error :messages="$errors->get('general_observations')" class="mt-2" />
                        </div>
                    </div>
               </div>

               {{-- CARTE 3: CHECKLIST DÉTAILLÉE --}}
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

                   @foreach($checklist as $category => $items)
                       <div class="mb-6 pt-4 border-t border-gray-100 first:border-t-0 first:pt-0">
                           <h4 class="font-semibold text-gray-700 mb-4">{{ $category }}</h4>
                           <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                               @foreach($items as $item)
                                   @php
                                       $statusesToUse = in_array($category, ['Papiers du véhicule', 'Accessoires Intérieur', 'Papiers & Accessoires']) ? $binaryStatuses : $conditionStatuses;
                                   @endphp
                                   <x-handover-status-switcher :item="$item" :category="$category" :statuses="$statusesToUse" />
                               @endforeach
                           </div>
                       </div>
                   @endforeach
               </div>

               {{-- CARTE 4: SIGNATURES ET ACTIONS --}}
               <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                   <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8 pt-8">
                       <div class="border-t-2 border-gray-300 pt-2">
                           <p class="text-center font-semibold text-gray-700">Signature du Chauffeur</p>
                       </div>
                        <div class="border-t-2 border-gray-300 pt-2">
                           <p class="text-center font-semibold text-gray-700">Signature du Responsable</p>
                       </div>
                   </div>
                    <div class="flex items-center justify-end mt-8 gap-4 border-t border-gray-200 pt-6">
                       <a href="{{ route('admin.assignments.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
                       <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700">
                           Créer et Imprimer la Fiche
                       </button>
                   </div>
               </div>
           </form>
       </div>
   </div>
</x-app-layout>