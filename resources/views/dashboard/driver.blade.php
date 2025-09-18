@extends('layouts.admin.catalyst')

@section('title', 'Dashboard Chauffeur')

@section('content')
{{-- Header --}}
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Dashboard Chauffeur
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <i class="fas fa-user mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400"></i>
                Bienvenue {{ $user->name }}
            </div>
            @if(isset($setupRequired))
            <div class="mt-2 flex items-center text-sm text-amber-600">
                <i class="fas fa-exclamation-triangle mr-1.5 h-5 w-5 flex-shrink-0 text-amber-500"></i>
                Configuration requise
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Mode dégradé si erreur --}}
@if(isset($error) || isset($fallbackMode) || isset($setupRequired))
<div class="rounded-md bg-yellow-50 p-4 mt-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle h-5 w-5 text-yellow-400"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">
                {{ isset($setupRequired) ? 'Configuration requise' : 'Attention' }}
            </h3>
            <div class="mt-2 text-sm text-yellow-700">
                <p>{{ $error ?? 'Données partiellement indisponibles - Mode dégradé activé' }}</p>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Statistiques chauffeur --}}
<div class="mt-8">
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Voyages total --}}
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Voyages Total</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['totalTrips'] ?? 0 }}</dd>
            <div class="mt-2 flex items-baseline">
                <div class="flex items-baseline text-sm font-semibold text-blue-600">
                    <i class="fas fa-route mr-1 h-4 w-4 self-center flex-shrink-0"></i>
                    Effectués
                </div>
            </div>
        </div>

        {{-- Kilomètres ce mois --}}
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Kilomètres</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($stats['monthlyKm'] ?? 0) }}</dd>
            <div class="mt-2 flex items-baseline">
                <div class="flex items-baseline text-sm font-semibold text-green-600">
                    <i class="fas fa-road mr-1 h-4 w-4 self-center flex-shrink-0"></i>
                    Ce mois
                </div>
            </div>
        </div>

        {{-- Score de sécurité --}}
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Score Sécurité</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($stats['safetyScore'] ?? 95, 1) }}%</dd>
            <div class="mt-2 flex items-baseline">
                <div class="flex items-baseline text-sm font-semibold text-yellow-600">
                    <i class="fas fa-shield-alt mr-1 h-4 w-4 self-center flex-shrink-0"></i>
                    Performance
                </div>
            </div>
        </div>

        {{-- Statut actuel --}}
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Statut</dt>
            <dd class="mt-1 text-xl font-semibold tracking-tight text-gray-900">Disponible</dd>
            <div class="mt-2 flex items-baseline">
                <div class="flex items-baseline text-sm font-semibold text-green-600">
                    <i class="fas fa-check-circle mr-1 h-4 w-4 self-center flex-shrink-0"></i>
                    Prêt à partir
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Contenu principal --}}
<div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
    {{-- Affectation actuelle --}}
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">
                <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                Affectation Actuelle
            </h3>
            <div class="mt-6">
                @if($stats['currentAssignment'])
                    <div class="rounded-md bg-blue-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-car h-5 w-5 text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Mission en cours</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>{{ $stats['currentAssignment'] }}</p>
                                    <p class="mt-1 text-xs">Démarrée il y a 2h30</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-calendar-check text-3xl mb-4"></i>
                        <p class="font-medium">Aucune affectation en cours</p>
                        <p class="text-sm">Vous êtes disponible pour une nouvelle mission</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Voyages récents --}}
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">
                <i class="fas fa-history mr-2 text-green-600"></i>
                Voyages Récents
            </h3>
            <div class="mt-6">
                @if(isset($recentTrips) && count($recentTrips) > 0)
                    <ul role="list" class="divide-y divide-gray-200">
                        @foreach($recentTrips->take(5) as $trip)
                        <li class="py-4">
                            <div class="flex space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-route text-green-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $trip['destination'] ?? 'Destination' }}</p>
                                    <p class="text-sm text-gray-500">{{ $trip['distance'] ?? '25' }} km • {{ $trip['duration'] ?? '45 min' }}</p>
                                </div>
                                <div class="flex-shrink-0 text-sm text-gray-500">
                                    {{ $trip['date'] ?? 'Hier 14:30' }}
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-6 text-gray-500">
                        <i class="fas fa-route text-3xl mb-2"></i>
                        <p>Aucun voyage récent</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Actions rapides Chauffeur --}}
<div class="mt-8">
    <div class="rounded-lg bg-white shadow">
        <div class="p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Actions Rapides</h3>
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="#"
                   class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-8 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-tasks mx-auto h-8 w-8 text-gray-400"></i>
                    <span class="mt-2 block text-sm font-semibold text-gray-900">Mes Missions</span>
                </a>

                <a href="#"
                   class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-8 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-search mx-auto h-8 w-8 text-gray-400"></i>
                    <span class="mt-2 block text-sm font-semibold text-gray-900">Contrôle Véhicule</span>
                </a>

                <a href="#"
                   class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-8 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-history mx-auto h-8 w-8 text-gray-400"></i>
                    <span class="mt-2 block text-sm font-semibold text-gray-900">Historique</span>
                </a>

                <a href="#"
                   class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-8 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-user mx-auto h-8 w-8 text-gray-400"></i>
                    <span class="mt-2 block text-sm font-semibold text-gray-900">Mon Profil</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des compteurs
    const counters = document.querySelectorAll('.text-3xl.font-semibold');
    counters.forEach(counter => {
        const target = parseFloat(counter.textContent.replace(/[^\d.]/g, ''));
        if (target && target > 0) {
            let current = 0;
            const increment = target / 25;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    if (counter.textContent.includes('%')) {
                        counter.textContent = target.toFixed(1) + '%';
                    } else if (counter.textContent.includes(',')) {
                        counter.textContent = new Intl.NumberFormat('fr-FR').format(Math.round(target));
                    } else {
                        counter.textContent = Math.round(target);
                    }
                    clearInterval(timer);
                } else {
                    if (counter.textContent.includes('%')) {
                        counter.textContent = current.toFixed(1) + '%';
                    } else if (counter.textContent.includes(',')) {
                        counter.textContent = new Intl.NumberFormat('fr-FR').format(Math.floor(current));
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }
            }, 60);
        }
    });
});
</script>
@endpush