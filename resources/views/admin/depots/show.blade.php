@extends('layouts.admin.catalyst')

@section('title', 'Fiche Dépôt - ' . $depot->name)

@section('content')
{{-- ====================================================================
 📋 FICHE DÉPÔT - FORMAT DOCUMENT PROFESSIONNEL IMPRIMABLE
 ====================================================================

 Design inspiré des documents administratifs professionnels:
 ✨ En-tête corporate avec logo et infos organisation
 ✨ Sections structurées (Informations, Statistiques, Véhicules, Historique)
 ✨ Tableaux propres avec bordures subtiles
 ✨ Badges colorés pour statuts
 ✨ Boutons d'action (Imprimer PDF, Modifier, Supprimer)
 ✨ Optimisé pour impression (media print)

 @version 1.0-Enterprise-Document-Grade
 @since 2025-11-05
 ==================================================================== --}}

<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Messages Flash --}}
        @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg print:hidden">
            <div class="flex items-center">
                <x-iconify icon="lucide:check-circle" class="w-5 h-5 text-green-600 mr-3" />
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg print:hidden">
            <div class="flex items-center">
                <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600 mr-3" />
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        {{-- Barre d'actions (masquée à l'impression) --}}
        <div class="mb-6 flex items-center justify-between print:hidden">
            <a href="{{ route('admin.depots.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all">
                <x-iconify icon="lucide:arrow-left" class="w-4 h-4" />
                <span class="font-medium text-gray-700">Retour à la liste</span>
            </a>

            <div class="flex items-center gap-3">
                {{-- Export PDF --}}
                <a href="{{ route('admin.depots.export.pdf', $depot->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
                    <x-iconify icon="lucide:file-text" class="w-4 h-4" />
                    <span class="font-medium">Exporter PDF</span>
                </a>

                {{-- Modifier --}}
                <button onclick="openEditModal({{ $depot->id }})"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all">
                    <x-iconify icon="lucide:edit" class="w-4 h-4" />
                    <span class="font-medium">Modifier</span>
                </button>

                {{-- Supprimer --}}
                <button onclick="confirmDelete({{ $depot->id }}, '{{ $depot->name }}', {{ $stats['total_vehicles'] }})"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all">
                    <x-iconify icon="lucide:trash-2" class="w-4 h-4" />
                    <span class="font-medium">Supprimer</span>
                </button>
            </div>
        </div>

        {{-- Document Principal --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 print:shadow-none print:border-0">

            {{-- En-tête Document Style Corporate --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-8 py-6 print:bg-white print:border-b-2 print:border-gray-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white print:text-gray-900">
                            FICHE DÉPÔT
                        </h1>
                        <p class="text-blue-100 mt-1 print:text-gray-600">
                            {{ Auth::user()->organization->name ?? 'ZenFleet' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-blue-100 text-sm print:text-gray-600">Référence</p>
                        <p class="text-white font-bold text-xl print:text-gray-900">
                            {{ $depot->code ?? 'DP-' . str_pad($depot->id, 4, '0', STR_PAD_LEFT) }}
                        </p>
                        <p class="text-blue-100 text-xs mt-1 print:text-gray-500">
                            Émis le {{ now()->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-8">

                {{-- Section 1: Informations Générales --}}
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b-2 border-blue-600 flex items-center">
                        <x-iconify icon="lucide:info" class="w-5 h-5 mr-2 text-blue-600" />
                        Informations Générales
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nom du dépôt --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Nom du Dépôt</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $depot->name }}</p>
                        </div>

                        {{-- Code --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Code</label>
                            <p class="text-lg font-mono font-semibold text-gray-900">
                                {{ $depot->code ?? 'Non défini' }}
                            </p>
                        </div>

                        {{-- Adresse --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-600 mb-1">Adresse</label>
                            <p class="text-gray-900">
                                {{ $depot->address ?? 'Non renseignée' }}
                            </p>
                            @if($depot->city || $depot->wilaya || $depot->postal_code)
                            <p class="text-gray-700 mt-1">
                                {{ $depot->city }}
                                @if($depot->wilaya), {{ $depot->wilaya }}@endif
                                @if($depot->postal_code) - {{ $depot->postal_code }}@endif
                            </p>
                            @endif
                        </div>

                        {{-- Contact --}}
                        @if($depot->phone || $depot->email)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Contact</label>
                            @if($depot->phone)
                            <p class="text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:phone" class="w-4 h-4 text-green-600" />
                                {{ $depot->phone }}
                            </p>
                            @endif
                            @if($depot->email)
                            <p class="text-gray-900 flex items-center gap-2 mt-1">
                                <x-iconify icon="lucide:mail" class="w-4 h-4 text-blue-600" />
                                {{ $depot->email }}
                            </p>
                            @endif
                        </div>
                        @endif

                        {{-- Responsable --}}
                        @if($depot->manager_name || $depot->manager_phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Responsable</label>
                            @if($depot->manager_name)
                            <p class="text-gray-900 font-medium">{{ $depot->manager_name }}</p>
                            @endif
                            @if($depot->manager_phone)
                            <p class="text-gray-700 text-sm mt-1">{{ $depot->manager_phone }}</p>
                            @endif
                        </div>
                        @endif

                        {{-- Statut --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Statut</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $depot->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                <x-iconify icon="lucide:{{ $depot->is_active ? 'check-circle' : 'x-circle' }}" class="w-4 h-4 mr-1" />
                                {{ $depot->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>

                        {{-- Capacité --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Capacité</label>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $depot->capacity ?? 'Non définie' }}
                                @if($depot->capacity) véhicule(s)@endif
                            </p>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($depot->description)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Description</label>
                        <p class="text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            {{ $depot->description }}
                        </p>
                    </div>
                    @endif
                </div>

                {{-- Section 2: Statistiques --}}
                <div class="mb-8 print:break-inside-avoid">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b-2 border-blue-600 flex items-center">
                        <x-iconify icon="lucide:bar-chart-2" class="w-5 h-5 mr-2 text-blue-600" />
                        Statistiques
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Total véhicules --}}
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-blue-700">Total véhicules</p>
                                    <p class="text-2xl font-bold text-blue-900 mt-1">
                                        {{ $stats['total_vehicles'] }}
                                    </p>
                                </div>
                                <x-iconify icon="lucide:truck" class="w-8 h-8 text-blue-600" />
                            </div>
                        </div>

                        {{-- Capacité --}}
                        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-purple-700">Capacité totale</p>
                                    <p class="text-2xl font-bold text-purple-900 mt-1">
                                        {{ $stats['capacity'] ?? 'N/A' }}
                                    </p>
                                </div>
                                <x-iconify icon="lucide:package" class="w-8 h-8 text-purple-600" />
                            </div>
                        </div>

                        {{-- Places disponibles --}}
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-700">Places disponibles</p>
                                    <p class="text-2xl font-bold text-green-900 mt-1">
                                        {{ $stats['available_space'] }}
                                    </p>
                                </div>
                                <x-iconify icon="lucide:circle-check" class="w-8 h-8 text-green-600" />
                            </div>
                        </div>

                        {{-- Taux occupation --}}
                        <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-orange-700">Taux occupation</p>
                                    <p class="text-2xl font-bold text-orange-900 mt-1">
                                        {{ number_format($stats['occupancy_percentage'], 1) }}%
                                    </p>
                                </div>
                                <x-iconify icon="lucide:pie-chart" class="w-8 h-8 text-orange-600" />
                            </div>
                        </div>
                    </div>

                    {{-- Barre de progression --}}
                    @if($depot->capacity)
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                            <span>Occupation du dépôt</span>
                            <span class="font-medium">{{ $stats['total_vehicles'] }} / {{ $stats['capacity'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all"
                                style="width: {{ min($stats['occupancy_percentage'], 100) }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Section 3: Véhicules Assignés --}}
                @if($vehicles->count() > 0)
                <div class="mb-8 print:break-inside-avoid">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b-2 border-blue-600 flex items-center">
                        <x-iconify icon="lucide:truck" class="w-5 h-5 mr-2 text-blue-600" />
                        Véhicules Assignés ({{ $vehicles->count() }})
                    </h2>

                    {{-- Groupement par statut --}}
                    @foreach($vehiclesByStatus as $status => $statusVehicles)
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 bg-gray-100 px-3 py-2 rounded">
                            {{ $status }} ({{ $statusVehicles->count() }})
                        </h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Immatriculation</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marque/Modèle</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carburant</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kilométrage</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($statusVehicles as $vehicle)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap font-mono font-semibold text-gray-900">
                                            {{ $vehicle->registration_plate }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $vehicle->brand ?? 'N/A' }} {{ $vehicle->model ?? '' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 text-sm">
                                            {{ $vehicle->vehicleType?->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 text-sm">
                                            {{ $vehicle->fuelType?->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ number_format($vehicle->current_mileage ?? 0, 0, ',', ' ') }} km
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="mb-8 text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <x-iconify icon="lucide:truck" class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                    <p class="text-gray-600 font-medium">Aucun véhicule assigné à ce dépôt</p>
                    <p class="text-gray-500 text-sm mt-1">La capacité est de {{ $depot->capacity ?? 'non définie' }} véhicule(s)</p>
                </div>
                @endif

                {{-- Section 4: Historique Récent --}}
                @if($recentHistory->count() > 0)
                <div class="mb-8 print:break-inside-avoid">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b-2 border-blue-600 flex items-center">
                        <x-iconify icon="lucide:clock" class="w-5 h-5 mr-2 text-blue-600" />
                        Historique Récent (10 dernières affectations)
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Véhicule</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Par</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentHistory as $history)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap font-mono font-semibold text-gray-900">
                                        {{ $history->registration_plate }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                            {{ $history->action === 'assigned' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $history->action === 'transferred' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $history->action === 'unassigned' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($history->action) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $history->assigned_by_name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ Str::limit($history->notes ?? '-', 50) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Pied de page document --}}
                <div class="mt-8 pt-6 border-t-2 border-gray-200 text-center text-sm text-gray-500">
                    <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
                    <p class="mt-1">{{ Auth::user()->organization->name ?? 'ZenFleet' }} - Gestion de Flotte Automobile</p>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal confirmation suppression --}}
<div id="deleteModal" style="display: none;" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 print:hidden">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <x-iconify icon="lucide:alert-triangle" class="w-6 h-6 text-red-600" />
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Supprimer le dépôt</h3>
                    <p class="text-sm text-gray-600" id="deleteDepotName"></p>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-gray-700 mb-2" id="deleteWarningMessage"></p>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-3" id="vehicleWarning" style="display: none;">
                    <p class="text-sm text-yellow-800">
                        <x-iconify icon="lucide:alert-circle" class="w-4 h-4 inline mr-1" />
                        Ce dépôt contient <span id="vehicleCount" class="font-bold"></span> véhicule(s).
                    </p>
                    <label class="flex items-center mt-3">
                        <input type="checkbox" id="forceDelete" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-sm text-yellow-900">Supprimer et désaffecter les véhicules</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                    Annuler
                </button>
                <button onclick="submitDelete()"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let deleteDepotId = null;
    let deleteDepotVehicleCount = 0;

    function confirmDelete(depotId, depotName, vehicleCount) {
        deleteDepotId = depotId;
        deleteDepotVehicleCount = vehicleCount;

        document.getElementById('deleteDepotName').textContent = depotName;

        if (vehicleCount > 0) {
            document.getElementById('deleteWarningMessage').textContent =
                'Attention: Ce dépôt contient des véhicules. Vous devez soit les réaffecter, soit forcer la suppression.';
            document.getElementById('vehicleWarning').style.display = 'block';
            document.getElementById('vehicleCount').textContent = vehicleCount;
        } else {
            document.getElementById('deleteWarningMessage').textContent =
                'Êtes-vous sûr de vouloir supprimer ce dépôt ? Cette action peut être annulée ultérieurement.';
            document.getElementById('vehicleWarning').style.display = 'none';
        }

        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        document.getElementById('forceDelete').checked = false;
    }

    function submitDelete() {
        const force = deleteDepotVehicleCount > 0 && document.getElementById('forceDelete').checked;

        if (deleteDepotVehicleCount > 0 && !force) {
            alert('Veuillez cocher la case pour confirmer la désaffectation des véhicules.');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/depots/${deleteDepotId}`;

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);

        if (force) {
            const forceInput = document.createElement('input');
            forceInput.type = 'hidden';
            forceInput.name = 'force';
            forceInput.value = '1';
            form.appendChild(forceInput);
        }

        document.body.appendChild(form);
        form.submit();
    }

    function openEditModal(depotId) {
        // Dispatch Livewire event pour ouvrir modal édition
        window.dispatchEvent(new CustomEvent('open-edit-depot-modal', {
            detail: depotId
        }));

        // Redirection vers page liste avec modal auto-ouvert
        window.location.href = '/admin/depots?edit=' + depotId;
    }

    // Style impression
    const style = document.createElement('style');
    style.textContent = `
    @media print {
        body * {
            visibility: hidden;
        }
        .max-w-6xl, .max-w-6xl * {
            visibility: visible;
        }
        .max-w-6xl {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .print\\:hidden {
            display: none !important;
        }
        .print\\:shadow-none {
            box-shadow: none !important;
        }
        .print\\:border-0 {
            border: 0 !important;
        }
        .print\\:bg-white {
            background-color: white !important;
        }
        .print\\:text-gray-900 {
            color: #111827 !important;
        }
        .print\\:text-gray-600 {
            color: #4b5563 !important;
        }
        .print\\:text-gray-500 {
            color: #6b7280 !important;
        }
        .print\\:border-b-2 {
            border-bottom-width: 2px !important;
        }
        .print\\:border-gray-300 {
            border-color: #d1d5db !important;
        }
        .print\\:break-inside-avoid {
            break-inside: avoid !important;
        }
    }
`;
    document.head.appendChild(style);
</script>
@endpush
