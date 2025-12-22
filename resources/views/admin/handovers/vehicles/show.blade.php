@extends('layouts.admin.catalyst')

@section('title', 'Aperçu Fiche Remise N° ' . $handoverForm->assignment->id)

@push('styles')
<style>
    @media print {
        body {
            background-color: white !important;
            -webkit-print-color-adjust: exact;
            /* For Chrome, Safari */
            print-color-adjust: exact;
            /* For Firefox */
        }

        /* 1. Rendre tout invisible par défaut */
        body * {
            visibility: hidden;
        }

        /* 2. Rendre la zone d'impression et son contenu visibles */
        #print-area,
        #print-area * {
            visibility: visible;
        }

        /* 3. Positionner la zone d'impression pour qu'elle occupe toute la page */
        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: auto;
            padding: 10mm;
            /* Marges d'impression réduites */
            margin: 0;
            box-shadow: none;
            font-size: 10pt;
        }

        /* Classes utilitaires pour l'impression */
        .print\:hidden {
            display: none !important;
        }

        .break-inside-avoid {
            break-inside: avoid;
        }
    }
</style>
@endpush

@section('content')
<div class="py-6">
    <div class="max-w-[210mm] mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Aperçu de la Fiche de Remise N° {{ $handoverForm->assignment->id }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('admin.handovers.vehicles.edit', $handoverForm) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                    <x-iconify icon="lucide:pencil" class="h-4 w-4 mr-2" />
                    Modifier
                </a>
                {{-- Le bouton est maintenant un lien vers notre nouvelle route --}}
                <a href="{{ route('admin.handovers.vehicles.download-pdf', $handoverForm) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    <x-iconify icon="lucide:download" class="h-4 w-4 mr-2" />
                    Télécharger PDF
                </a>
            </div>
        </div>

        {{-- Section Upload Fiche Signée (Non visible à l'impression) --}}
        <div class="mb-8 bg-gray-50 p-6 rounded-lg border border-gray-200 print:hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <x-iconify icon="lucide:file-signature" class="w-5 h-5 text-blue-600" />
                Fiche de Remise Signée
            </h3>

            @if($handoverForm->getSignedFormUrl())
            <div class="flex flex-col gap-4 bg-white p-4 rounded-md border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <x-iconify icon="lucide:file-check" class="w-8 h-8 text-green-500" />
                        <div>
                            <p class="font-bold text-gray-800">Fiche signée disponible</p>
                            <a href="{{ $handoverForm->getSignedFormUrl() }}" target="_blank" class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                                Voir le document <x-iconify icon="lucide:external-link" class="w-3 h-3" />
                            </a>
                        </div>
                    </div>

                    @can('upload signed handovers')
                    <div x-data="{ showUpload: false }">
                        <button @click="showUpload = !showUpload" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            Remplacer le fichier
                        </button>

                        <div x-show="showUpload" class="absolute mt-2 right-0 w-96 bg-white p-4 shadow-xl rounded-lg border border-gray-100 z-10"
                            @click.away="showUpload = false"
                            style="display: none;"
                            x-transition>
                            <form action="{{ route('admin.handovers.vehicles.upload-signed', $handoverForm) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3">
                                @csrf
                                <label class="text-sm font-medium text-gray-700">Nouveau fichier</label>
                                <input type="file" name="signed_form" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 w-full">Envoyer</button>
                            </form>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
            @else
            @can('upload signed handovers')
            <div class="bg-white p-6 rounded-md border border-dashed border-gray-300 text-center">
                <x-iconify icon="lucide:upload-cloud" class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                <p class="text-gray-600 mb-4">Aucune fiche signée n'a été téléversée pour le moment.</p>

                <form action="{{ route('admin.handovers.vehicles.upload-signed', $handoverForm) }}" method="POST" enctype="multipart/form-data" class="max-w-md mx-auto">
                    @csrf
                    <div class="flex gap-2">
                        <input type="file" name="signed_form" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 whitespace-nowrap">
                            Téléverser
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-2 text-left">Formats acceptés: PDF, JPG, PNG. Max 5Mo.</p>
                </form>
            </div>
            @else
            <p class="text-gray-500 italic text-sm">Vous n'avez pas la permission de téléverser des documents signés.</p>
            @endcan
            @endif
        </div>

        {{-- Le conteneur #print-area simule la page A4 et contient tout ce qui doit être imprimé --}}
        <div id="print-area" class="bg-white shadow-lg p-10 print:p-0 print:shadow-none flex flex-col min-h-[297mm]">

            <main class="flex-grow">
                <header class="flex justify-between items-start pb-4 border-b-2 border-black">
                    <div>
                        <h1 class="text-2xl font-bold text-black">FICHE DE REMISE VÉHICULE</h1>
                        <p class="text-sm text-gray-600">Affectation N°: {{ $handoverForm->assignment->id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-lg">{{ $handoverForm->assignment->organization->name }}</p>
                        <p class="text-xs text-gray-500">Document généré le {{ now()->format('d/m/Y') }}</p>
                    </div>
                </header>

                <section class="mt-6 grid grid-cols-2 gap-x-8 text-sm">
                    {{-- Colonne de gauche --}}
                    <div>
                        <p class="font-bold">{{ $handoverForm->assignment->driver->first_name }} {{ $handoverForm->assignment->driver->last_name }}</p>
                        <p class="text-gray-600">{{ $handoverForm->assignment->driver->personal_phone ?? 'N/A' }}</p>
                    </div>
                    {{-- Colonne de droite --}}
                    <div class="text-right">
                        <p class="text-gray-600 mt-2">Remis le: {{ $handoverForm->issue_date->format('d/m/Y') }}</p>
                        <p class="font-bold">{{ $handoverForm->assignment->vehicle->brand }} {{ $handoverForm->assignment->vehicle->model }} ({{ $handoverForm->assignment->vehicle->registration_plate }})</p>
                        <p class="text-gray-600">Kilométrage: {{ number_format($handoverForm->current_mileage, 0, ',', ' ') }} km</p>
                    </div>
                </section>


                <section class="mt-6">
                    <h2 class="text-base font-bold mb-3 text-gray-800 uppercase tracking-wider border-b pb-1">État Visuel et Observations</h2>
                    <div class="flex gap-6 p-2 border rounded-lg">
                        <div class="w-1/3 flex-shrink-0 text-center self-center">
                            @php
                            $sketchAsset = $handoverForm->assignment->vehicle->vehicleType->name === 'Moto' ? 'images/scooter_sketch.png' : 'images/car_sketch.png';
                            @endphp
                            <img src="{{ asset($sketchAsset) }}" alt="Croquis" class="border mx-auto max-w-full">
                            <p class="text-xs text-gray-500 mt-2">Cocher les défauts constatés</p>
                        </div>
                        <div class="w-2/3">
                            <strong>Observations générales:</strong>
                            <p class="text-gray-700 p-2 bg-gray-50 min-h-[120px] mt-1 border rounded whitespace-pre-wrap text-sm">{{ $handoverForm->general_observations ?: 'Aucune observation particulière.' }}</p>
                        </div>
                    </div>
                </section>

                <section class="mt-6">
                    <h2 class="text-base font-bold mb-3 text-gray-800 uppercase tracking-wider border-b pb-1">Checklist de Contrôle</h2>
                    <div class="space-y-4 text-xs">
                        @php
                        $isMoto = $handoverForm->assignment->vehicle->vehicleType->name === 'Moto';
                        $rows = $isMoto ? [
                        ['Papiers & Accessoires', 'État Général']
                        ] : [
                        ['Papiers du véhicule', 'Pneumatiques'],
                        ['Accessoires Intérieur', 'État Extérieur']
                        ];
                        @endphp

                        @foreach($rows as $row)
                        <div class="grid grid-cols-2 gap-x-8">
                            @foreach($row as $categoryName)
                            @if(isset($checklist[$categoryName]))
                            <div class="break-inside-avoid">
                                <h3 class="font-semibold text-gray-700 mb-2 border-b">{{ $categoryName }}</h3>
                                <div class="space-y-1">
                                    @foreach($checklist[$categoryName] as $detail)
                                    <div class="flex justify-between border-b border-dotted">
                                        <span>{{ $detail->item }}</span>
                                        <span class="font-bold">{{ $detail->status }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </section>
            </main>

            <footer class="mt-auto pt-12">
                <div class="grid grid-cols-3 gap-12">
                    <div class="border-t-2 border-black pt-2 text-center text-sm">
                        <p class="font-semibold">{{ $handoverForm->assignment->driver->first_name }} {{ $handoverForm->assignment->driver->last_name }}</p>
                        <p class="text-xs text-gray-600">(Chauffeur)</p>
                    </div>
                    <div class="border-t-2 border-black pt-2 text-center text-sm">
                        <p class="font-semibold">(Nom & Prénom)</p>
                        <p class="text-xs text-gray-600">(Responsable Hiérarchique)</p>
                    </div>
                    <div class="border-t-2 border-black pt-2 text-center text-sm">
                        <p class="font-semibold">(Nom & Prénom)</p>
                        <p class="text-xs text-gray-600">(Responsable Parc)</p>
                    </div>
                </div>
            </footer>

        </div>
    </div>
</div>
@endsection