@extends('layouts.admin.catalyst')

@section('content')
<section class="zf-page min-h-screen">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- En-tête avec navigation et actions --}}
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            {{-- Fil d'ariane --}}
            <nav class="flex mb-3" aria-label="Breadcrumb">
                <ol role="list" class="flex items-center space-x-2">
                    <li>
                        <div class="flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500">
                                <x-iconify icon="heroicons:home" class="h-5 w-5 flex-shrink-0" />
                                <span class="sr-only">Dashboard</span>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <x-iconify icon="heroicons:chevron-right" class="h-4 w-4 text-gray-300 flex-shrink-0" />
                            <a href="{{ route('admin.drivers.index') }}" class="ml-2 text-sm font-medium text-gray-500 hover:text-gray-700">Chauffeurs</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <x-iconify icon="heroicons:chevron-right" class="h-4 w-4 text-gray-300 flex-shrink-0" />
                            <a href="{{ route('admin.drivers.sanctions.index') }}" class="ml-2 text-sm font-medium text-gray-500 hover:text-gray-700">Sanctions</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <x-iconify icon="heroicons:chevron-right" class="h-4 w-4 text-gray-300 flex-shrink-0" />
                            <span class="ml-2 text-sm font-medium text-gray-900" aria-current="page">{{ $sanction->reference }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <h2 class="text-xl font-bold text-gray-600 sm:truncate">
                Détail de la Sanction
            </h2>
            <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <x-iconify icon="heroicons:calendar" class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
                    Créée le {{ $sanction->created_at->format('d/m/Y') }}
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <x-iconify icon="heroicons:user-circle" class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
                    Par {{ $sanction->supervisor->name ?? 'Système' }}
                </div>
            </div>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ route('admin.drivers.sanctions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <x-iconify icon="heroicons:arrow-left" class="-ml-1 mr-2 h-5 w-5 text-gray-500" />
                Retour
            </a>
            {{-- Possible future actions: Print, Export PDF, Notify --}}
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Colonne Principale --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Carte Info Sanction --}}
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-iconify icon="heroicons:exclamation-triangle" class="w-5 h-5 text-orange-500 mr-2" />
                        Informations de la Sanction
                    </h3>
                    <div class="flex space-x-2">
                        @php
                        $severityColor = match($sanction->severity) {
                        'low' => 'bg-green-50 text-green-700 border border-green-200',
                        'medium' => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                        'high' => 'bg-orange-50 text-orange-700 border border-orange-200',
                        'critical' => 'bg-red-50 text-red-700 border border-red-200',
                        default => 'bg-gray-50 text-gray-700 border border-gray-200'
                        };
                        $severityLabel = match($sanction->severity) {
                        'low' => 'Faible',
                        'medium' => 'Moyenne',
                        'high' => 'Élevée',
                        'critical' => 'Critique',
                        default => 'Inconnue'
                        };
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $severityColor }}">
                            Sévérité: {{ $severityLabel }}
                        </span>

                        @if($sanction->isArchived())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                            <x-iconify icon="heroicons:archive-box" class="w-3 h-3 mr-1" />
                            Archivée
                        </span>
                        @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                            <x-iconify icon="heroicons:check-circle" class="w-3 h-3 mr-1" />
                            Active
                        </span>
                        @endif
                    </div>
                </div>
                <div class="px-6 py-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Type de Sanction</dt>
                            <dd class="mt-1 flex items-center text-sm text-gray-900 font-semibold">
                                <div class="p-1.5 rounded-lg {{ $sanction->getSanctionTypeColor() }} bg-opacity-10 mr-2">
                                    <x-iconify icon="{{ $sanction->getSanctionTypeIcon() }}" class="w-5 h-5 {{ str_replace('bg-', 'text-', $sanction->getSanctionTypeColor()) }}" />
                                </div>
                                {{ $sanction->getSanctionTypeLabel() }}
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Date d'application</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold flex items-center">
                                <x-iconify icon="heroicons:calendar-days" class="w-5 h-5 text-gray-400 mr-2" />
                                {{ $sanction->sanction_date->format('d/m/Y') }}
                                <span class="ml-2 text-xs text-gray-500">
                                    ({{ $sanction->getDaysSinceSanction() }} jours)
                                </span>
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Motif / Raison détaillée</dt>
                            <dd class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-xl p-4 border border-gray-100 leading-relaxed whitespace-pre-line">
                                {{ $sanction->reason }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Carte Pièce Jointe --}}
            @if($sanction->attachment_path)
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex items-center">
                    <x-iconify icon="heroicons:paper-clip" class="w-5 h-5 text-blue-500 mr-2" />
                    <h3 class="text-lg font-semibold text-gray-900">Pièce Jointe Officielle</h3>
                </div>
                <div class="p-6">
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 flex flex-col items-center justify-center text-center bg-gray-50 hover:bg-gray-100 transition-colors group cursor-pointer" onclick="window.open('{{ $sanction->getAttachmentUrl() }}', '_blank')">
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-full mb-3 group-hover:scale-110 transition-transform">
                            <x-iconify icon="heroicons:document-text" class="w-8 h-8" />
                        </div>
                        <h4 class="text-sm font-medium text-gray-900">Fichiers Joint</h4>
                        <p class="text-xs text-gray-500 mt-1 mb-4">Cliquez pour visualiser ou télécharger le document original</p>

                        <a href="{{ $sanction->getAttachmentUrl() }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <x-iconify icon="heroicons:eye" class="w-4 h-4 mr-2" />
                            Visualiser le document
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Historique des Modifications --}}
            @if($sanction->history->count() > 0)
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center">
                        <x-iconify icon="heroicons:clock" class="w-5 h-5 text-indigo-500 mr-2" />
                        <h3 class="text-lg font-semibold text-gray-900">Historique des modifications</h3>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                        {{ $sanction->history->count() }} {{ $sanction->history->count() > 1 ? 'événements' : 'événement' }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($sanction->history as $index => $historyItem)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            @php
                                            $actionConfig = match($historyItem->action) {
                                            'created' => ['icon' => 'heroicons:plus-circle', 'color' => 'bg-green-500'],
                                            'updated' => ['icon' => 'heroicons:pencil-square', 'color' => 'bg-blue-500'],
                                            'archived' => ['icon' => 'heroicons:archive-box', 'color' => 'bg-amber-500'],
                                            'unarchived' => ['icon' => 'heroicons:arrow-path', 'color' => 'bg-green-500'],
                                            'restored' => ['icon' => 'heroicons:arrow-path', 'color' => 'bg-teal-500'],
                                            'deleted' => ['icon' => 'heroicons:trash', 'color' => 'bg-red-500'],
                                            'force_deleted' => ['icon' => 'heroicons:x-circle', 'color' => 'bg-red-700'],
                                            default => ['icon' => 'heroicons:information-circle', 'color' => 'bg-gray-500']
                                            };
                                            $actionLabel = match($historyItem->action) {
                                            'created' => 'Création',
                                            'updated' => 'Modification',
                                            'archived' => 'Archivage',
                                            'unarchived' => 'Désarchivage',
                                            'restored' => 'Restauration',
                                            'deleted' => 'Suppression',
                                            'force_deleted' => 'Suppression définitive',
                                            default => ucfirst($historyItem->action)
                                            };
                                            @endphp
                                            <span class="h-8 w-8 rounded-full {{ $actionConfig['color'] }} flex items-center justify-center ring-8 ring-white">
                                                <x-iconify icon="{{ $actionConfig['icon'] }}" class="h-5 w-5 text-white" />
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">{{ $actionLabel }}</p>
                                                    <p class="mt-0.5 text-xs text-gray-500">
                                                        Par <span class="font-medium text-gray-700">{{ $historyItem->user->name ?? 'Système' }}</span>
                                                    </p>
                                                </div>
                                                <div class="text-right whitespace-nowrap text-xs text-gray-500">
                                                    <div>{{ $historyItem->created_at->format('d/m/Y') }}</div>
                                                    <div class="text-gray-400">{{ $historyItem->created_at->format('H:i') }}</div>
                                                </div>
                                            </div>
                                            @if($historyItem->details && is_array($historyItem->details) && count($historyItem->details) > 0)
                                            <div class="mt-2 text-sm text-gray-600 bg-gray-50 rounded-lg p-3 border border-gray-100">
                                                @foreach($historyItem->details as $key => $value)
                                                <div class="flex items-start gap-2 mb-1 last:mb-0">
                                                    <x-iconify icon="heroicons:chevron-right" class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" />
                                                    <span class="text-xs">
                                                        <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                        <span class="text-gray-600">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                    </span>
                                                </div>
                                                @endforeach
                                            </div>
                                            @endif
                                            @if($historyItem->ip_address)
                                            <p class="mt-1 text-xs text-gray-400 flex items-center gap-1">
                                                <x-iconify icon="heroicons:globe-alt" class="w-3 h-3" />
                                                {{ $historyItem->ip_address }}
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- Colonne Latérale --}}
        <div class="space-y-8">
            {{-- Carte Chauffeur --}}
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex items-center">
                    <x-iconify icon="heroicons:user" class="w-5 h-5 text-blue-500 mr-2" />
                    <h3 class="text-lg font-semibold text-gray-900">Chauffeur concerné</h3>
                </div>
                <div class="px-6 py-6 text-center">
                    <div class="relative inline-block">
                        @if($sanction->driver->photo)
                        <img class="h-24 w-24 rounded-full mx-auto object-cover border-4 border-white shadow-lg" src="{{ asset('storage/' . $sanction->driver->photo) }}" alt="">
                        @else
                        <div class="h-24 w-24 rounded-full mx-auto bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg border-4 border-white">
                            {{ strtoupper(substr($sanction->driver->first_name, 0, 1) . substr($sanction->driver->last_name, 0, 1)) }}
                        </div>
                        @endif
                        @if($sanction->driver->driverStatus)
                        @php
                        $statusColor = match($sanction->driver->driverStatus->name) {
                        'Disponible' => 'bg-green-400',
                        'En mission' => 'bg-amber-400',
                        'En repos' => 'bg-red-400',
                        default => 'bg-gray-400'
                        };
                        @endphp
                        <span class="absolute bottom-1 right-1 block h-4 w-4 rounded-full ring-2 ring-white {{ $statusColor }}" title="{{ $sanction->driver->driverStatus->name }}"></span>
                        @endif
                    </div>

                    <h3 class="mt-4 text-xl font-bold text-gray-900">{{ $sanction->driver->first_name }} {{ $sanction->driver->last_name }}</h3>
                    <p class="text-sm text-gray-500 mb-6">Matricule: {{ $sanction->driver->employee_number ?? 'N/A' }}</p>

                    <div class="border-t border-gray-100 pt-6 text-left space-y-4">
                        {{-- Email Personnel --}}
                        <div>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide block mb-1">Email personnel</span>
                            <div class="flex items-center gap-2">
                                <x-iconify icon="heroicons:envelope" class="w-4 h-4 text-gray-400 flex-shrink-0" />
                                <span class="text-sm font-medium text-gray-900 truncate">{{ $sanction->driver->personal_email ?? 'Non renseigné' }}</span>
                            </div>
                        </div>

                        {{-- Téléphone Personnel --}}
                        <div>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide block mb-1">Téléphone personnel</span>
                            <div class="flex items-center gap-2">
                                <x-iconify icon="heroicons:phone" class="w-4 h-4 text-gray-400 flex-shrink-0" />
                                <span class="text-sm font-medium text-gray-900">{{ $sanction->driver->personal_phone ?? 'Non renseigné' }}</span>
                            </div>
                        </div>

                        {{-- Permis --}}
                        <div>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide block mb-1">Permis</span>
                            <div class="flex items-center gap-2">
                                <x-iconify icon="heroicons:identification" class="w-4 h-4 text-gray-400 flex-shrink-0" />
                                <span class="text-sm font-medium text-gray-900">{{ $sanction->driver->license_number ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('admin.drivers.show', $sanction->driver->id) }}" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none ring-1 ring-gray-200 transition-colors">
                            Voir le profil complet
                        </a>
                    </div>
                </div>
            </div>

            {{-- Carte Statistiques Chauffeur (Mini) --}}
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Stats Juridiques</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-red-50 rounded-xl p-3 text-center">
                            <span class="block text-2xl font-bold text-red-600">{{ $sanction->driver->sanctions()->count() }}</span>
                            <span class="text-xs text-red-600 font-medium">Sanctions Totales</span>
                        </div>
                        <div class="bg-orange-50 rounded-xl p-3 text-center">
                            <span class="block text-2xl font-bold text-orange-600">{{ $sanction->driver->sanctions()->active()->count() }}</span>
                            <span class="text-xs text-orange-600 font-medium">Actives</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
@endsection
