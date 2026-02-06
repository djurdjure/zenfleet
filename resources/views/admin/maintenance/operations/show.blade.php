@extends('layouts.admin.catalyst')

@section('title', 'Détails Opération Maintenance')

@section('content')
<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- ===============================================
            HEADER AVEC BREADCRUMB ET ACTIONS
        =============================================== --}}
        <div class="mb-6">
            {{-- Breadcrumb --}}
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.maintenance.operations.index') }}" class="text-gray-600 hover:text-blue-600">
                            <x-iconify icon="lucide:wrench" class="w-4 h-4 mr-2 inline" />
                            Maintenance
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <x-iconify icon="lucide:chevron-right" class="w-4 h-4 text-gray-400 mx-1" />
                            <span class="text-gray-900 font-medium">Opération #{{ $operation->id }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Title & Actions --}}
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                        <x-iconify icon="lucide:file-text" class="w-6 h-6 text-blue-600" />
                        Opération de Maintenance
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $operation->maintenanceType->name }} - {{ $operation->vehicle->registration_plate }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    @can('update', $operation)
                        {{-- Actions selon statut --}}
                        @if($operation->status === 'planned')
                            <form action="{{ route('admin.maintenance.operations.start', $operation) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <x-iconify icon="lucide:play" class="w-4 h-4" />
                                    Démarrer
                                </button>
                            </form>
                        @endif

                        @if(in_array($operation->status, ['planned', 'in_progress']))
                            <a href="{{ route('admin.maintenance.operations.edit', $operation) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <x-iconify icon="lucide:pencil" class="w-4 h-4" />
                                Modifier
                            </a>
                        @endif
                    @endcan

                    <a href="{{ route('admin.maintenance.operations.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <x-iconify icon="lucide:arrow-left" class="w-4 h-4" />
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- COLONNE PRINCIPALE --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Statut Card --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Statut de l'Opération</h2>
                        {!! $operation->getStatusBadge() !!}
                    </div>
                    
                    {{-- Timeline --}}
                    <div class="space-y-4">
                        {{-- Planifiée --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 border border-blue-200 flex items-center justify-center flex-shrink-0">
                                <x-iconify icon="lucide:calendar" class="w-4 h-4 text-blue-600" />
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">Planifiée</div>
                                <div class="text-xs text-gray-500">
                                    {{ $operation->scheduled_date?->format('d/m/Y à H:i') ?? 'Non définie' }}
                                </div>
                            </div>
                        </div>

                        {{-- En cours --}}
                        @if(in_array($operation->status, ['in_progress', 'completed']))
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-100 border border-orange-200 flex items-center justify-center flex-shrink-0">
                                    <x-iconify icon="lucide:play" class="w-4 h-4 text-orange-600" />
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">Démarrée</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $operation->updated_at->format('d/m/Y à H:i') }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Terminée --}}
                        @if($operation->status === 'completed')
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-100 border border-green-200 flex items-center justify-center flex-shrink-0">
                                    <x-iconify icon="lucide:check" class="w-4 h-4 text-green-600" />
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">Terminée</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $operation->completed_date?->format('d/m/Y à H:i') ?? 'Date inconnue' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Détails Opération --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <x-iconify icon="lucide:info" class="w-5 h-5 text-blue-600" />
                        Détails de l'Opération
                    </h2>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Type de Maintenance --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <dt class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-1">Type de Maintenance</dt>
                            <dd class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $operation->maintenanceType->getCategoryColor() }}"></div>
                                <span class="text-sm font-semibold text-gray-900">{{ $operation->maintenanceType->name }}</span>
                            </dd>
                            <dd class="text-xs text-gray-500 mt-1">
                                Catégorie: {{ ucfirst($operation->maintenanceType->category ?? 'N/A') }}
                            </dd>
                        </div>

                        {{-- Kilométrage --}}
                        @if($operation->mileage_at_maintenance)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dt class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-1">Kilométrage</dt>
                                <dd class="text-sm font-semibold text-gray-900">
                                    {{ number_format($operation->mileage_at_maintenance, 0, ',', ' ') }} km
                                </dd>
                            </div>
                        @endif

                        {{-- Durée --}}
                        @if($operation->duration_minutes)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dt class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-1">Durée</dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ $operation->formatted_duration }}</dd>
                            </div>
                        @endif

                        {{-- Coût --}}
                        @if($operation->total_cost)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dt class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-1">Coût Total</dt>
                                <dd class="text-sm font-semibold text-purple-600">
                                    {{ number_format($operation->total_cost, 2, ',', ' ') }} DA
                                </dd>
                            </div>
                        @endif
                    </dl>

                    {{-- Description --}}
                    @if($operation->description)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Description</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $operation->description }}</p>
                        </div>
                    @endif

                    {{-- Notes --}}
                    @if($operation->notes)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Notes</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $operation->notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- Documents (si disponible) --}}
                @if($operation->documents && $operation->documents->count() > 0)
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <x-iconify icon="lucide:paperclip" class="w-5 h-5 text-blue-600" />
                            Documents Attachés ({{ $operation->documents->count() }})
                        </h2>

                        <div class="space-y-3">
                            @foreach($operation->documents as $document)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
                                            <x-iconify icon="lucide:file" class="w-5 h-5 text-blue-600" />
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $document->filename }}</div>
                                            <div class="text-xs text-gray-500">{{ $document->created_at->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                    <a href="{{ route('documents.download', $document) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <x-iconify icon="lucide:download" class="w-5 h-5" />
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- COLONNE SIDEBAR --}}
            <div class="space-y-6">
                
                {{-- Véhicule Card --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <x-iconify icon="lucide:car" class="w-4 h-4 text-blue-600" />
                        Véhicule
                    </h3>
                    
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center flex-shrink-0">
                            <x-iconify icon="lucide:car" class="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900">{{ $operation->vehicle->registration_plate }}</div>
                            <div class="text-xs text-gray-500">{{ $operation->vehicle->brand }} {{ $operation->vehicle->model }}</div>
                        </div>
                    </div>

                    <a href="{{ route('admin.vehicles.show', $operation->vehicle) }}"
                       class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                        Voir le véhicule
                    </a>
                </div>

                {{-- Fournisseur Card --}}
                @if($operation->provider)
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <x-iconify icon="lucide:building" class="w-4 h-4 text-purple-600" />
                            Fournisseur
                        </h3>
                        
                        <div class="space-y-3">
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $operation->provider->name }}</div>
                                @if($operation->provider->contact_phone)
                                    <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                        <x-iconify icon="lucide:phone" class="w-3 h-3" />
                                        {{ $operation->provider->contact_phone }}
                                    </div>
                                @endif
                                @if($operation->provider->email)
                                    <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                        <x-iconify icon="lucide:mail" class="w-3 h-3" />
                                        {{ $operation->provider->email }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Planification Liée --}}
                @if($operation->schedule)
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <x-iconify icon="lucide:repeat" class="w-4 h-4 text-green-600" />
                            Planification Récurrente
                        </h3>
                        
                        <div class="space-y-2">
                            <div class="text-sm text-gray-700">
                                Cette opération fait partie d'une planification de maintenance préventive.
                            </div>
                            <a href="{{ route('admin.maintenance.schedules.show', $operation->schedule) }}"
                               class="block w-full text-center px-4 py-2 bg-green-100 text-green-700 text-sm font-medium rounded-lg hover:bg-green-200 transition-colors duration-200">
                                Voir la planification
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Informations Audit --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <x-iconify icon="lucide:clock" class="w-4 h-4 text-gray-600" />
                        Historique
                    </h3>
                    
                    <div class="space-y-3 text-xs text-gray-600">
                        <div>
                            <span class="font-medium">Créée le:</span>
                            <span class="block text-gray-900 mt-0.5">{{ $operation->created_at->format('d/m/Y à H:i') }}</span>
                            @if($operation->creator)
                                <span class="text-gray-500">par {{ $operation->creator->name }}</span>
                            @endif
                        </div>

                        @if($operation->updated_at != $operation->created_at)
                            <div class="pt-3 border-t border-gray-200">
                                <span class="font-medium">Modifiée le:</span>
                                <span class="block text-gray-900 mt-0.5">{{ $operation->updated_at->format('d/m/Y à H:i') }}</span>
                                @if($operation->updater)
                                    <span class="text-gray-500">par {{ $operation->updater->name }}</span>
                                @endif
                            </div>
                        @endif

                        @if($operation->ended_at && $operation->endedBy)
                            <div class="pt-3 border-t border-gray-200">
                                <span class="font-medium">Terminée par:</span>
                                <span class="block text-gray-900 mt-0.5">{{ $operation->endedBy->name }}</span>
                                <span class="text-gray-500">{{ $operation->ended_at->format('d/m/Y à H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Actions Rapides --}}
                @can('update', $operation)
                    @if($operation->status === 'in_progress')
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg border border-green-200 p-6">
                            <h3 class="text-sm font-semibold text-green-900 mb-3">Terminer l'Opération</h3>
                            <form action="{{ route('admin.maintenance.operations.complete', $operation) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                
                                <div>
                                    <label class="block text-xs font-medium text-green-900 mb-1">Coût Total (DA)</label>
                                    <input type="number" name="total_cost" step="0.01" 
                                           class="w-full text-sm border-green-300 rounded-md focus:ring-green-500 focus:border-green-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-green-900 mb-1">Durée (minutes)</label>
                                    <input type="number" name="duration_minutes" 
                                           class="w-full text-sm border-green-300 rounded-md focus:ring-green-500 focus:border-green-500">
                                </div>

                                <button type="submit"
                                        class="w-full px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center justify-center gap-2">
                                    <x-iconify icon="lucide:check" class="w-4 h-4" />
                                    Terminer
                                </button>
                            </form>
                        </div>
                    @endif

                    @if(in_array($operation->status, ['planned', 'in_progress']))
                        <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-lg border border-red-200 p-6">
                            <h3 class="text-sm font-semibold text-red-900 mb-3">Zone de Danger</h3>
                            <form action="{{ route('admin.maintenance.operations.cancel', $operation) }}" method="POST"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette opération ?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center justify-center gap-2">
                                    <x-iconify icon="lucide:x-circle" class="w-4 h-4" />
                                    Annuler l'Opération
                                </button>
                            </form>
                        </div>
                    @endif
                @endcan
            </div>
        </div>

    </div>
</section>
@endsection
