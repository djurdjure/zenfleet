<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center print:hidden">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Fiche de Remise N° {{ $handoverForm->id }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('admin.handovers.vehicles.edit', $handoverForm) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z" /></svg>
                    Modifier
                </a>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Imprimer
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ZONE D'IMPRESSION --}}
            <div id="print-area" class="bg-white shadow-lg p-8 md:p-12">
                
                {{-- En-tête du document --}}
                <div class="text-center mb-10 border-b-2 border-black pb-4">
                    <h1 class="text-3xl font-bold text-black">FICHE DE REMISE DE VÉHICULE</h1>
                    <p class="text-sm text-gray-600">Générée le : {{ now()->format('d/m/Y') }}</p>
                </div>

                {{-- Section Informations Générales --}}
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">1. Informations Générales</h2>
                    <table class="w-full text-sm mt-4">
                        <tbody>
                            <tr>
                                <td class="font-semibold p-2 border bg-gray-50 w-1/4">Date de Remise</td>
                                <td class="p-2 border">{{ $handoverForm->issue_date->format('d/m/Y') }}</td>
                                <td class="font-semibold p-2 border bg-gray-50 w-1/4">Affectation N°</td>
                                <td class="p-2 border">{{ $handoverForm->assignment->id }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold p-2 border bg-gray-50">Chauffeur</td>
                                <td class="p-2 border">{{ $handoverForm->assignment->driver->first_name }} {{ $handoverForm->assignment->driver->last_name }}</td>
                                <td class="font-semibold p-2 border bg-gray-50">Matricule Employé</td>
                                <td class="p-2 border">{{ $handoverForm->assignment->driver->employee_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold p-2 border bg-gray-50">Véhicule</td>
                                <td class="p-2 border">{{ $handoverForm->assignment->vehicle->brand }} {{ $handoverForm->assignment->vehicle->model }}</td>
                                <td class="font-semibold p-2 border bg-gray-50">Immatriculation</td>
                                <td class="p-2 border font-mono">{{ $handoverForm->assignment->vehicle->registration_plate }}</td>
                            </tr>
                             <tr>
                                <td class="font-semibold p-2 border bg-gray-50">Kilométrage</td>
                                <td class="p-2 border font-mono">{{ number_format($handoverForm->current_mileage, 0, ',', ' ') }} km</td>
                                <td class="font-semibold p-2 border bg-gray-50">Motif Affectation</td>
                                <td class="p-2 border">{{ $handoverForm->assignment->reason ?? 'Non spécifié' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Section État Visuel --}}
                <div class="mb-8 print:break-before-page">
                    <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">2. État Visuel et Observations</h2>
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="w-full md:w-1/3 flex-shrink-0 mx-auto">
                             @if($handoverForm->assignment->vehicle->vehicleType->name === 'Moto')
                                 <img src="{{ asset('images/scooter_sketch.png') }}" alt="Croquis Scooter" class="border">
                             @else
                                 <img src="{{ asset('images/car_sketch.png') }}" alt="Croquis Voiture" class="border">
                             @endif
                        </div>
                        <div class="w-full md:w-2/3">
                            <p class="font-semibold">Observations sur l'état extérieur :</p>
                            <p class="text-gray-700 p-2 border bg-gray-50 min-h-[150px] mt-2 whitespace-pre-wrap">{{ $handoverForm->general_observations ?? 'Aucune.' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Section Checklist --}}
                <div class="mb-8">
                     <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">3. Checklist de Contrôle</h2>
                    @foreach($checklist as $category => $items)
                        <h3 class="font-semibold text-gray-700 mt-4 mb-2">{{ $category }}</h3>
                        <table class="w-full text-sm border-collapse">
                            @foreach($items->chunk(2) as $chunk)
                                <tr>
                                    @foreach($chunk as $detail)
                                        <td class="p-2 border w-2/5">{{ $detail->item }}</td>
                                        <td class="p-2 border font-semibold w-[10%] text-center">{{ $detail->status }}</td>
                                    @endforeach
                                    @if($chunk->count() < 2)<td class="p-2 border w-2/5"></td><td class="p-2 border w-[10%]"></td>@endif
                                </tr>
                            @endforeach
                        </table>
                    @endforeach
                </div>

                {{-- Section Signatures --}}
                <div class="pt-24">
                    <div class="grid grid-cols-2 gap-16">
                        <div class="border-t-2 border-black pt-2 text-center">
                            <p class="font-semibold">Signature du Responsable</p>
                        </div>
                        <div class="border-t-2 border-black pt-2 text-center">
                            <p class="font-semibold">Signature du Chauffeur</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section de Téléversement (ne s'imprime pas) --}}
            <div class="max-w-4xl mx-auto mt-6 print:hidden">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Archivage de la Fiche Signée</h3>
                    @if ($handoverForm->signed_form_path)
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-md">
                            <a href="{{ asset('storage/' . $handoverForm->signed_form_path) }}" target="_blank" class="text-green-700 font-semibold hover:underline">Voir la fiche signée actuelle</a>
                            <span class="text-xs text-green-600">Téléversée le {{ $handoverForm->updated_at->format('d/m/Y') }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-4">Pour remplacer, téléversez un nouveau fichier :</p>
                    @else
                        <p class="text-sm text-gray-500 mb-2">Aucune fiche signée n'a encore été téléversée.</p>
                    @endif
                    <form action="{{ route('admin.handovers.vehicles.uploadSigned', $handoverForm) }}" method="POST" enctype="multipart/form-data" class="mt-4 flex items-center space-x-4">
                        @csrf
                        <input type="file" name="signed_form" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100"/>
                        <button type="submit" class="px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">Téléverser</button>
                    </form>
                    @error("signed_form") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror               </div>
            </div>
        </div>

        {{-- Styles spécifiques pour l'impression --}}
        <style>
            @media print {
                body, .x-app-layout-body { background-color: white !important; }
                .print\:hidden { display: none !important; }
                #print-area { box-shadow: none !important; border: none !important; }
                .print\:break-before-page { break-before: page; }
            }
        </style>
    </x-app-layout>