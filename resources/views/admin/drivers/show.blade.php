@extends('layouts.admin.catalyst')
@section('title', 'Fiche Chauffeur - ' . $driver->first_name . ' ' . $driver->last_name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 -m-6 p-6">

    {{-- Header avec actions --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-4 mb-2">
                    <a href="{{ route('admin.drivers.index') }}"
                       class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        <span>Retour à la liste</span>
                    </a>
                </div>

                <h1 class="text-4xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-user-tie text-blue-600 mr-4"></i>
                    {{ $driver->first_name }} {{ $driver->last_name }}
                </h1>

                <p class="text-xl text-gray-600 mt-2">
                    Chauffeur {{ $driver->employee_number ?? 'N/A' }} • {{ $driver->organization->name ?? 'Organisation non définie' }}
                </p>
            </div>

            <div class="flex items-center space-x-3">
                @can('edit drivers')
                    <a href="{{ route('admin.drivers.edit', $driver) }}"
                       class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <i class="fas fa-edit mr-2"></i>
                        Modifier
                    </a>
                @endcan

                {{-- Statut badge --}}
                @if($driver->driverStatus)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium shadow-sm"
                          style="background-color: {{ $driver->driverStatus->color }}15; color: {{ $driver->driverStatus->color }}; border: 1px solid {{ $driver->driverStatus->color }}30;">
                        <i class="fas {{ $driver->driverStatus->icon }} mr-1.5"></i>
                        {{ $driver->driverStatus->name }}
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-700 shadow-sm">
                        <i class="fas fa-question-circle mr-1.5"></i>
                        Statut non défini
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Contenu principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Colonne gauche - Informations principales --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Informations personnelles --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-user mr-3"></i>
                        Informations Personnelles
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Prénom</label>
                                <p class="text-gray-900 font-medium">{{ $driver->first_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nom</label>
                                <p class="text-gray-900 font-medium">{{ $driver->last_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Date de naissance</label>
                                <p class="text-gray-900 font-medium">
                                    @if($driver->birth_date && $driver->birth_date instanceof \Carbon\Carbon)
                                        {{ $driver->birth_date->format('d/m/Y') }}
                                        <span class="text-gray-500 text-sm">({{ $driver->birth_date->age }} ans)</span>
                                    @elseif($driver->birth_date)
                                        {{ $driver->birth_date }}
                                        <span class="text-orange-500 text-xs">(format non reconnu)</span>
                                    @else
                                        <span class="text-gray-500 italic">Non renseignée</span>
                                    @endif
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Groupe sanguin</label>
                                <p class="text-gray-900 font-medium">{{ $driver->blood_type ?? 'Non renseigné' }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Téléphone personnel</label>
                                <p class="text-gray-900 font-medium">{{ $driver->personal_phone ?? 'Non renseigné' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Email personnel</label>
                                <p class="text-gray-900 font-medium">{{ $driver->email ?? 'Non renseigné' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Adresse</label>
                                <p class="text-gray-900 font-medium">{{ $driver->address ?? 'Non renseignée' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Contact d'urgence</label>
                                <p class="text-gray-900 font-medium">
                                    @if($driver->emergency_contact_name)
                                        {{ $driver->emergency_contact_name }}
                                        @if($driver->emergency_contact_phone)
                                            <br><span class="text-gray-600">{{ $driver->emergency_contact_phone }}</span>
                                        @endif
                                    @else
                                        Non renseigné
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informations professionnelles --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-briefcase mr-3"></i>
                        Informations Professionnelles
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Numéro d'employé</label>
                                <p class="text-gray-900 font-medium">{{ $driver->employee_number ?? 'Non attribué' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Date de recrutement</label>
                                <p class="text-gray-900 font-medium">
                                    @if($driver->recruitment_date)
                                        {{ $driver->recruitment_date->format('d/m/Y') }}
                                        <span class="text-gray-500 text-sm">({{ $driver->recruitment_date->diffForHumans() }})</span>
                                    @else
                                        Non renseignée
                                    @endif
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Fin de contrat</label>
                                <p class="text-gray-900 font-medium">
                                    @if($driver->contract_end_date)
                                        {{ $driver->contract_end_date->format('d/m/Y') }}
                                        @if($driver->contract_end_date->isPast())
                                            <span class="text-red-500 text-sm font-semibold">(Expiré)</span>
                                        @elseif($driver->contract_end_date->diffInDays(now()) <= 30)
                                            <span class="text-orange-500 text-sm font-semibold">(Expire bientôt)</span>
                                        @endif
                                    @else
                                        CDI ou non renseignée
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Numéro de permis</label>
                                <p class="text-gray-900 font-medium">{{ $driver->driver_license_number ?? 'Non renseigné' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Expiration du permis</label>
                                <p class="text-gray-900 font-medium">
                                    @if($driver->driver_license_expiry)
                                        {{ $driver->driver_license_expiry->format('d/m/Y') }}
                                        @if($driver->driver_license_expiry->isPast())
                                            <span class="text-red-500 text-sm font-semibold">(Expiré)</span>
                                        @elseif($driver->driver_license_expiry->diffInDays(now()) <= 60)
                                            <span class="text-orange-500 text-sm font-semibold">(Expire bientôt)</span>
                                        @endif
                                    @else
                                        Non renseignée
                                    @endif
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Catégorie de permis</label>
                                <p class="text-gray-900 font-medium">{{ $driver->license_category ?? 'Non renseignée' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activité récente --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-history mr-3"></i>
                        Activité Récente
                    </h2>
                </div>

                <div class="p-6">
                    @if(isset($recentActivity) && $recentActivity->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($recentActivity as $activity)
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-{{ $activity['icon'] ?? 'circle' }} text-blue-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-gray-900 font-medium">{{ $activity['description'] }}</p>
                                        <p class="text-gray-500 text-sm">{{ $activity['date'] ?? 'Date non disponible' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-clock text-gray-300 text-3xl mb-3"></i>
                            <p class="text-gray-500">Aucune activité récente disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Colonne droite - Statistiques et actions --}}
        <div class="space-y-8">

            {{-- Photo et informations rapides --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center shadow-lg">
                        @if($driver->photo)
                            <img src="{{ Storage::url($driver->photo) }}"
                                 alt="Photo de {{ $driver->first_name }}"
                                 class="w-32 h-32 rounded-full object-cover">
                        @else
                            <span class="text-4xl text-white font-bold">
                                {{ strtoupper(substr($driver->first_name, 0, 1) . substr($driver->last_name, 0, 1)) }}
                            </span>
                        @endif
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $driver->first_name }} {{ $driver->last_name }}</h3>
                    <p class="text-gray-600">{{ $driver->organization->name ?? 'Organisation non définie' }}</p>
                </div>
            </div>

            {{-- Statistiques --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-chart-line mr-3"></i>
                        Statistiques
                    </h2>
                </div>

                <div class="p-6 space-y-4">
                    @if(isset($stats))
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_assignments'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Affectations</div>
                            </div>

                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $stats['active_assignments'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600">En cours</div>
                            </div>

                            <div class="text-center p-3 bg-yellow-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">{{ $stats['completed_trips'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Trajets</div>
                            </div>

                            <div class="text-center p-3 bg-purple-50 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_distance'] ?? 0) }}</div>
                                <div class="text-sm text-gray-600">KM Total</div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line text-gray-300 text-2xl mb-2"></i>
                            <p class="text-gray-500">Statistiques en cours de calcul...</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions rapides --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-bolt mr-3"></i>
                        Actions Rapides
                    </h2>
                </div>

                <div class="p-6 space-y-3">
                    @can('create assignments')
                        <a href="{{ route('admin.assignments.create', ['driver_id' => $driver->id]) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Nouvelle Affectation
                        </a>
                    @endcan

                    @can('edit drivers')
                        <a href="{{ route('admin.drivers.edit', $driver) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-edit mr-2"></i>
                            Modifier la Fiche
                        </a>
                    @endcan

                    <button type="button"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors"
                            onclick="window.print()">
                        <i class="fas fa-print mr-2"></i>
                        Imprimer la Fiche
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script pour les interactions --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation d'entrée des cards
    const cards = document.querySelectorAll('.bg-white');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

@endsection