{{-- Fiche Véhicule ZenFleet — Design System v3.1 — Prototype Match — {{ now()->format('d/m/Y H:i') }} --}}
@extends('layouts.admin.catalyst')

@section('title', $vehicle->registration_plate . ' - Fiche Véhicule')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-inter text-gray-800 print:max-w-none print:px-0 print:py-0">

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden print:shadow-none print:border-black print:rounded-none">

        {{-- En-tête document --}}
        <header class="p-5 border-b border-gray-100 print:border-black">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="text-lg font-bold text-gray-900">{{ Auth::user()->organization->name ?? 'ZenFleet' }}</div>
                    @if(Auth::user()->organization->address ?? null)
                    <div class="text-sm text-gray-600">{{ Auth::user()->organization->address }}</div>
                    @endif
                </div>
                <div class="text-right">
                    <div class="text-sm font-medium text-gray-900">Fiche Véhicule</div>
                    <div class="text-xs text-gray-500">{{ now()->format('d/m/Y à H:i') }}</div>
                </div>
            </div>
        </header>

        {{-- Barre d'action – uniquement à l'écran --}}
        <div class="p-5 border-b border-gray-100 print:hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <nav class="flex items-center text-sm text-gray-600">
                    <a href="{{ route('admin.vehicles.index') }}" class="text-violet-600 hover:underline">Véhicules</a>
                    <x-iconify icon="ph:caret-right" class="text-xs mx-1 text-gray-400" />
                    <span class="font-medium text-gray-900">{{ $vehicle->registration_plate }}</span>
                </nav>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.vehicles.export.single.pdf', $vehicle) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <x-iconify icon="ph:file-pdf" class="text-sm" /> Exporter PDF
                    </a>
                    @can('vehicles.update')
                    <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-200 transition-colors">
                        <x-iconify icon="ph:pencil-simple" class="text-sm" /> Modifier
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Identité du véhicule --}}
        <div class="p-5 border-b border-gray-100 print:border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Immatriculation</p>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $vehicle->registration_plate }}</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ $vehicle->brand }} {{ $vehicle->model }} — {{ $vehicle->manufacturing_year }}</p>
                    <p class="text-xs text-gray-500">VIN: {{ $vehicle->vin ?? 'Non renseigné' }}</p>
                </div>
                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $vehicle->getStatusBadgeClass() }}">
                    {{-- Mapping d'icône simple selon le statut pour matcher le style du prototype (Parking/Check/etc) --}}
                    @if($vehicle->status_id === 1)
                    <x-iconify icon="ph:check-circle" class="text-[10px] mr-1" />
                    @elseif($vehicle->status_id === 2)
                    <x-iconify icon="ph:wrench" class="text-[10px] mr-1" />
                    @elseif($vehicle->status_id === 3)
                    <x-iconify icon="ph:prohibit" class="text-[10px] mr-1" />
                    @else
                    <x-iconify icon="ph:info" class="text-[10px] mr-1" />
                    @endif
                    {{ $vehicle->getStatusName() }}
                </div>
            </div>
        </div>

        {{-- Corps de la fiche --}}
        <div class="p-5">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Colonne principale --}}
                <div class="lg:col-span-2 space-y-6">

                    @php
                    // Calculs KPIs
                    $km = $vehicle->current_mileage ?? 0;
                    //$daysInService = $analytics['duration_in_service'] ?? 0;
                    if(isset($analytics['duration_in_service_formatted'])) {
                    $daysInService = $analytics['duration_in_service_formatted']; // Keep formatted string like "5a 11m 14j"
                    } else {
                    $daysInService = 'N/A';
                    }
                    @endphp

                    {{-- KPIs --}}
                    <section class="bg-gray-50 rounded-xl p-4 border border-gray-100 print:bg-white print:border-gray-300">
                        <h2 class="text-xs font-semibold text-gray-900 uppercase tracking-wide mb-3">Indicateurs clés</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <div class="text-center">
                                <p class="text-xs text-gray-600">Kilométrage</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($km, 0, ',', ' ') }} km</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-600">Jours en service</p>
                                <p class="text-lg font-bold text-gray-900 text-sm">{{ $daysInService }}</p> {{-- Reduced font size slighly for long string --}}
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-600">Coût maintenance</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($analytics['maintenance_cost_total'] ?? 0, 0, ',', ' ') }} DA</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-600">Distance parcourue</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($analytics['total_km_driven'] ?? 0, 0, ',', ' ') }} km</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-600">Interventions</p>
                                <p class="text-lg font-bold text-gray-900">{{ $analytics['maintenance_count'] ?? 0 }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-600">Coût / km</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($analytics['cost_per_km'] ?? 0, 2, ',', ' ') }} DA</p>
                            </div>
                        </div>
                    </section>

                    {{-- Informations Générales --}}
                    <section class="bg-gray-50 rounded-xl p-4 border border-gray-100 print:bg-white print:border-gray-300 break-inside-avoid">
                        <h2 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-3">
                            <x-iconify icon="ph:info" class="text-sm text-gray-600" /> Informations Générales
                        </h2>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <div>
                                <dt class="text-gray-600">Marque / Modèle</dt>
                                <dd class="font-medium">{{ $vehicle->brand }} {{ $vehicle->model }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Immatriculation</dt>
                                <dd class="font-medium">{{ $vehicle->registration_plate }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">VIN</dt>
                                <dd class="font-medium">{{ $vehicle->vin ?? 'Non renseigné' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Année</dt>
                                <dd class="font-medium">{{ $vehicle->manufacturing_year }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Couleur</dt>
                                <dd class="font-medium">{{ $vehicle->color ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Type</dt>
                                <dd class="font-medium">{{ optional($vehicle->vehicleType)->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Places</dt>
                                <dd class="font-medium">{{ $vehicle->seats ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Dépôt</dt>
                                <dd class="font-medium">{{ optional($vehicle->depot)->name ?? 'Non assigné' }}</dd>
                            </div>
                        </dl>
                    </section>

                    {{-- Spécifications Techniques --}}
                    <section class="bg-gray-50 rounded-xl p-4 border border-gray-100 print:bg-white print:border-gray-300 break-inside-avoid">
                        <h2 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-3">
                            <x-iconify icon="ph:gear" class="text-sm text-gray-600" /> Spécifications Techniques
                        </h2>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <div>
                                <dt class="text-gray-600">Carburant</dt>
                                <dd class="font-medium">{{ optional($vehicle->fuelType)->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Transmission</dt>
                                <dd class="font-medium">{{ optional($vehicle->transmissionType)->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Cylindrée</dt>
                                <dd class="font-medium">{{ $vehicle->engine_displacement_cc }} cc</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Puissance</dt>
                                <dd class="font-medium">{{ $vehicle->power_hp }} ch</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Kilométrage initial</dt>
                                <dd class="font-medium">{{ number_format($vehicle->initial_mileage, 0, ',', ' ') }} km</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Kilométrage actuel</dt>
                                <dd class="font-medium">{{ number_format($vehicle->current_mileage, 0, ',', ' ') }} km</dd>
                            </div>
                        </dl>
                    </section>

                    {{-- Finances --}}
                    <section class="bg-gray-50 rounded-xl p-4 border border-gray-100 print:bg-white print:border-gray-300 break-inside-avoid">
                        <h2 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-3">
                            <x-iconify icon="ph:bank" class="text-sm text-gray-600" /> Informations Financières
                        </h2>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <div>
                                <dt class="text-gray-600">Date d'acquisition</dt>
                                <dd class="font-medium">{{ $vehicle->acquisition_date ? $vehicle->acquisition_date->format('d/m/Y') : 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Prix d'achat</dt>
                                <dd class="font-medium">{{ number_format($vehicle->purchase_price, 0, ',', ' ') }} DA</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Valeur actuelle</dt>
                                <dd class="font-medium">{{ number_format($vehicle->current_value, 0, ',', ' ') }} DA</dd>
                            </div>
                        </dl>
                    </section>

                    {{-- Historique Affectations --}}
                    <section class="bg-gray-50 rounded-xl p-4 border border-gray-100 print:bg-white print:border-gray-300 break-inside-avoid">
                        <h2 class="text-sm font-semibold text-gray-900 flex items-center justify-between mb-3">
                            <span class="flex items-center gap-2">
                                <x-iconify icon="ph:users-three" class="text-sm text-gray-600" /> Historique des Affectations
                            </span>
                            <span class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">{{ $vehicle->assignments->count() }} total</span>
                        </h2>
                        <table class="w-full text-sm">
                            <thead class="border-b border-gray-200">
                                <tr class="text-left text-gray-600 text-xs">
                                    <th class="pb-2">Chauffeur</th>
                                    <th class="pb-2">Début</th>
                                    <th class="pb-2">Fin</th>
                                    <th class="pb-2 text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($vehicle->assignments->take(5) as $assignment)
                                <tr>
                                    <td class="py-2 font-medium">{{ $assignment->driver->user->name ?? 'Inconnu' }}</td>
                                    <td class="py-2">{{ $assignment->start_datetime->format('d/m/Y') }}</td>
                                    <td class="py-2">{{ $assignment->end_datetime ? $assignment->end_datetime->format('d/m/Y') : '-' }}</td>
                                    <td class="py-2 text-center">
                                        @if(!$assignment->end_datetime)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">En cours</span>
                                        @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">Terminée</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-500 italic">Aucune affectation</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </section>

                    {{-- Notes --}}
                    @if($vehicle->notes)
                    <section class="bg-gray-50 rounded-xl p-4 border border-gray-100 print:bg-white print:border-gray-300 break-inside-avoid">
                        <h2 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-3">
                            <x-iconify icon="ph:note-pencil" class="text-sm text-gray-600" /> Notes
                        </h2>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $vehicle->notes }}</p>
                    </section>
                    @endif

                </div>

                {{-- Colonne latérale --}}
                <div class="space-y-6">

                    {{-- Chauffeur actuel --}}
                    <section class="bg-gray-50 rounded-xl p-4 border border-gray-100 print:bg-white print:border-gray-300 break-inside-avoid">
                        <h2 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-3">
                            <x-iconify icon="ph:user" class="text-sm text-gray-600" /> Chauffeur Actuel
                        </h2>
                        @if($vehicle->currentAssignment)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center font-bold text-gray-600">
                                {{ substr($vehicle->currentAssignment->driver->user->name ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $vehicle->currentAssignment->driver->user->name }}</div>
                                <div class="text-xs text-gray-500">Depuis le {{ $vehicle->currentAssignment->start_datetime->format('d/m/Y') }}</div>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-4 text-sm text-gray-500 italic">
                            Aucun chauffeur affecté
                        </div>
                        @endif
                    </section>

                    {{-- Statistiques --}}
                    <section class="bg-gray-50 rounded-xl p-4 border border-gray-100 print:bg-white print:border-gray-300 break-inside-avoid">
                        <h2 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-3">
                            <x-iconify icon="ph:chart-bar" class="text-sm text-gray-600" /> Statistiques
                        </h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Taux d'utilisation</span>
                                <span class="font-medium">{{ $analytics['utilization_rate'] ?? '0' }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Âge du véhicule</span>
                                <span class="font-medium">{{ $analytics['vehicle_age_formatted'] ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Interventions totales</span>
                                <span class="font-medium">{{ $analytics['maintenance_count'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Affectations totales</span>
                                <span class="font-medium">{{ $vehicle->assignments->count() }}</span>
                            </div>
                        </div>
                    </section>

                    {{-- Activité Récente (Timeline simplifiée) --}}
                    <section class="bg-gray-50 rounded-xl p-4 border border-gray-100 print:bg-white print:border-gray-300 break-inside-avoid">
                        <h2 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-3">
                            <x-iconify icon="ph:clock-counter-clockwise" class="text-sm text-gray-600" /> Activité Récente
                        </h2>
                        <ul class="space-y-3 text-xs">
                            @foreach($timeline ?? [] as $event)
                            <li>
                                <div class="font-medium text-gray-900">{{ $event['title'] ?? 'Activité' }}</div>
                                <div class="text-gray-500">{{ $event['date'] }}</div>
                                <div class="text-gray-400 mt-1">{{ Str::limit($event['description'] ?? '', 50) }}</div>
                            </li>
                            @endforeach
                            @if(empty($timeline))
                            <li class="text-gray-500 italic">Aucune activité récente</li>
                            @endif
                        </ul>
                    </section>

                    {{-- Actions Rapides --}}
                    <section class="bg-gray-50 rounded-xl p-4 border border-gray-100 print:hidden">
                        <h2 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-3">
                            <x-iconify icon="ph:lightning" class="text-sm text-gray-600" /> Actions Rapides
                        </h2>
                        <div class="flex flex-col gap-2">
                            <a href="#" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium bg-white text-gray-800 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                                <x-iconify icon="ph:gauge" class="text-sm" /> Historique kilométrique
                            </a>
                            <a href="#" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium bg-white text-gray-800 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                                <x-iconify icon="ph:clock-counter-clockwise" class="text-sm" /> Historique complet
                            </a>
                        </div>
                    </section>

                    {{-- Système --}}
                    <section class="bg-gray-50 rounded-xl p-4 border border-gray-100 print:bg-white print:border-gray-300 break-inside-avoid">
                        <h2 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-3">
                            <x-iconify icon="ph:database" class="text-sm text-gray-600" /> Système
                        </h2>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-600">ID</span>
                                <span class="font-mono font-medium">#{{ $vehicle->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Créé le</span>
                                <span>{{ $vehicle->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Modifié le</span>
                                <span>{{ $vehicle->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>

        {{-- Pied de page --}}
        <div class="p-4 border-t border-gray-200 text-center text-xs text-gray-500 print:border-black">
            Document généré par ZenFleet • {{ $vehicle->registration_plate }} • {{ $vehicle->brand }} {{ $vehicle->model }} • {{ now()->format('d/m/Y H:i') }}
        </div>

    </div>

</div>
@endsection