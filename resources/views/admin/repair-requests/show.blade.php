@extends('layouts.admin.catalyst')

@section('title', 'Demande de Réparation #' . $repairRequest->id)

@section('content')
<div class="space-y-6">
    @if (session('success'))
    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
        {{ session('success') }}
    </div>
    @endif

    @if (session('warning'))
    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">
        {{ session('warning') }}
    </div>
    @endif

    @if (session('error'))
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
        {{ session('error') }}
    </div>
    @endif

    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Demande de Réparation #{{ $repairRequest->id }}</h1>
            <p class="mt-1 text-sm text-gray-600">Suivi workflow à 2 niveaux (superviseur -> gestionnaire flotte)</p>
        </div>
        <a href="{{ route('admin.repair-requests.index') }}"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            <x-iconify icon="lucide:arrow-left" class="h-4 w-4" />
            Retour liste
        </a>
    </div>

    @php
        $urgencyColor = match($repairRequest->urgency) {
            'critical' => 'bg-red-50 text-red-700 border-red-200',
            'high' => 'bg-orange-50 text-orange-700 border-orange-200',
            'normal' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
        $statusLabel = $repairRequest->status_label ?? $repairRequest->status;
        $statusColor = match($repairRequest->status) {
            'pending_supervisor' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            'pending_fleet_manager' => 'bg-orange-50 text-orange-700 border-orange-200',
            'approved_final' => 'bg-green-50 text-green-700 border-green-200',
            'rejected_supervisor', 'rejected_final' => 'bg-red-50 text-red-700 border-red-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    @endphp

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600">Détails</h2>
                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-medium text-gray-500">Véhicule</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $repairRequest->vehicle?->registration_plate ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-600">{{ trim(($repairRequest->vehicle?->brand ?? '') . ' ' . ($repairRequest->vehicle?->model ?? '')) ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Chauffeur</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $repairRequest->driver?->user?->name ?? $repairRequest->driver?->full_name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-600">{{ $repairRequest->driver?->license_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Urgence</p>
                        <span class="mt-1 inline-flex rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $urgencyColor }}">
                            {{ $repairRequest->urgency_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Statut</p>
                        <span class="mt-1 inline-flex rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Créée le</p>
                        <p class="mt-1 text-sm text-gray-900">{{ optional($repairRequest->created_at)->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Coût estimé</p>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($repairRequest->estimated_cost)
                            {{ number_format((float) $repairRequest->estimated_cost, 2, ',', ' ') }} DA
                            @else
                            N/A
                            @endif
                        </p>
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600">Description</h2>
                <p class="mt-3 whitespace-pre-line text-sm text-gray-900">{{ $repairRequest->description }}</p>
                @if($repairRequest->location_description)
                <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                    <span class="font-semibold">Localisation: </span>{{ $repairRequest->location_description }}
                </div>
                @endif
                @if($repairRequest->rejection_reason)
                <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
                    <span class="font-semibold">Motif de rejet: </span>{{ $repairRequest->rejection_reason }}
                </div>
                @endif
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600">Historique</h2>
                <div class="mt-4 space-y-3">
                    @forelse($repairRequest->history as $item)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                        <p class="text-sm font-semibold text-gray-900">{{ $item->action }}</p>
                        <p class="text-xs text-gray-600">{{ optional($item->created_at)->format('d/m/Y H:i') }} - {{ $item->user?->name ?? 'Système' }}</p>
                        @if($item->comment)
                        <p class="mt-1 text-sm text-gray-700">{{ $item->comment }}</p>
                        @endif
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">Aucun historique disponible.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600">Actions</h2>

                @can('approveLevelOne', $repairRequest)
                <form method="POST" action="{{ route('admin.repair-requests.approve-supervisor', $repairRequest) }}" class="mt-4 space-y-3">
                    @csrf
                    <label class="block text-xs font-medium text-gray-600">Commentaire (optionnel)</label>
                    <textarea name="comment" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    <button type="submit" class="w-full rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">
                        Approuver (Niveau 1)
                    </button>
                </form>
                @endcan

                @can('rejectLevelOne', $repairRequest)
                <form method="POST" action="{{ route('admin.repair-requests.reject-supervisor', $repairRequest) }}" class="mt-4 space-y-3">
                    @csrf
                    <label class="block text-xs font-medium text-gray-600">Motif de rejet (obligatoire)</label>
                    <textarea name="reason" rows="3" required class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    <button type="submit" class="w-full rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                        Rejeter (Niveau 1)
                    </button>
                </form>
                @endcan

                @can('approveLevelTwo', $repairRequest)
                <form method="POST" action="{{ route('admin.repair-requests.approve-fleet-manager', $repairRequest) }}" class="mt-4 space-y-3">
                    @csrf
                    <label class="block text-xs font-medium text-gray-600">Commentaire (optionnel)</label>
                    <textarea name="comment" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    <button type="submit" class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Valider (Niveau 2)
                    </button>
                </form>
                @endcan

                @can('rejectLevelTwo', $repairRequest)
                <form method="POST" action="{{ route('admin.repair-requests.reject-fleet-manager', $repairRequest) }}" class="mt-4 space-y-3">
                    @csrf
                    <label class="block text-xs font-medium text-gray-600">Motif de rejet (obligatoire)</label>
                    <textarea name="reason" rows="3" required class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    <button type="submit" class="w-full rounded-lg bg-red-700 px-3 py-2 text-sm font-semibold text-white hover:bg-red-800">
                        Rejeter (Niveau 2)
                    </button>
                </form>
                @endcan

                @can('delete', $repairRequest)
                <form method="POST" action="{{ route('admin.repair-requests.destroy', $repairRequest) }}" class="mt-4 border-t border-gray-200 pt-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-lg border border-red-300 bg-white px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">
                        Supprimer la demande
                    </button>
                </form>
                @endcan
            </section>
        </aside>
    </div>
</div>
@endsection
