@extends('layouts.admin.catalyst')

@section('title', 'Dashboard Administration')

@section('content')
{{-- Header --}}
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Dashboard Administration
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <i class="fas fa-building mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400"></i>
                {{ $organization->name ?? 'Organisation' }}
            </div>
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <i class="fas fa-user mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400"></i>
                {{ $user->name }} - Administrateur
            </div>
        </div>
    </div>
</div>

{{-- Mode dégradé si erreur --}}
@if(isset($error) || isset($fallbackMode))
<div class="rounded-md bg-yellow-50 p-4 mt-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle h-5 w-5 text-yellow-400"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">
                Attention
            </h3>
            <div class="mt-2 text-sm text-yellow-700">
                <p>{{ $error ?? 'Données partiellement indisponibles - Mode dégradé activé' }}</p>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Statistiques principales --}}
<div class="mt-8">
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Utilisateurs --}}
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Utilisateurs</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['organizationUsers'] ?? 0 }}</dd>
            <div class="mt-2 flex items-baseline">
                <div class="flex items-baseline text-sm font-semibold text-green-600">
                    <i class="fas fa-arrow-up mr-1 h-4 w-4 self-center flex-shrink-0"></i>
                    {{ $stats['activeUsers'] ?? 0 }} actifs
                </div>
            </div>
        </div>

        {{-- Véhicules --}}
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Véhicules</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['organizationVehicles'] ?? 0 }}</dd>
            <div class="mt-2 flex items-baseline">
                <div class="flex items-baseline text-sm font-semibold text-green-600">
                    <i class="fas fa-check-circle mr-1 h-4 w-4 self-center flex-shrink-0"></i>
                    {{ $stats['availableVehicles'] ?? 0 }} disponibles
                </div>
            </div>
        </div>

        {{-- Chauffeurs --}}
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Chauffeurs</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['organizationDrivers'] ?? 0 }}</dd>
            <div class="mt-2 flex items-baseline">
                <div class="flex items-baseline text-sm font-semibold text-green-600">
                    <i class="fas fa-user-check mr-1 h-4 w-4 self-center flex-shrink-0"></i>
                    {{ $stats['activeDrivers'] ?? 0 }} actifs
                </div>
            </div>
        </div>

        {{-- Affectations --}}
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Affectations</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['activeAssignments'] ?? 0 }}</dd>
            <div class="mt-2 flex items-baseline">
                <div class="flex items-baseline text-sm font-semibold text-blue-600">
                    <i class="fas fa-clock mr-1 h-4 w-4 self-center flex-shrink-0"></i>
                    En cours
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Contenu principal --}}
<div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
    {{-- Activité récente --}}
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">
                <i class="fas fa-history mr-2 text-blue-600"></i>
                Activité Récente
            </h3>
            <div class="mt-6 flow-root">
                <ul role="list" class="-mb-8">
                    @if(isset($recentActivity) && count($recentActivity) > 0)
                        @foreach($recentActivity as $index => $activity)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-{{ $activity['color'] ?? 'blue' }}-500 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas fa-{{ $activity['icon'] ?? 'circle' }} h-4 w-4 text-white"></i>
                                        </span>
                                    </div>
                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                        <div>
                                            <p class="text-sm text-gray-500">{{ $activity['title'] ?? 'Activité' }}</p>
                                            <p class="text-xs text-gray-400">{{ $activity['description'] ?? '' }}</p>
                                        </div>
                                        <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                            {{ $activity['timestamp']->diffForHumans() ?? 'Il y a quelques minutes' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    @else
                        <li class="text-center py-6 text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p>Aucune activité récente</p>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    {{-- État des véhicules --}}
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">
                <i class="fas fa-chart-pie mr-2 text-green-600"></i>
                État des Véhicules
            </h3>
            <div class="mt-6">
                @if(isset($vehicleDistribution) && count($vehicleDistribution) > 0)
                    <dl class="divide-y divide-gray-200">
                        @foreach($vehicleDistribution as $status => $count)
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-3">
                            <dt class="text-sm font-medium text-gray-500 capitalize">
                                <span class="inline-flex items-center">
                                    <span class="h-2 w-2 rounded-full bg-{{ $status === 'Disponible' ? 'green' : ($status === 'En cours' ? 'blue' : 'yellow') }}-400 mr-2"></span>
                                    {{ $status }}
                                </span>
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                    {{ $count }} véhicule{{ $count > 1 ? 's' : '' }}
                                </span>
                            </dd>
                        </div>
                        @endforeach
                    </dl>
                @else
                    <div class="text-center py-6 text-gray-500">
                        <i class="fas fa-car text-3xl mb-2"></i>
                        <p>Aucune donnée véhicule</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Actions rapides --}}
<div class="mt-8">
    <div class="rounded-lg bg-white shadow">
        <div class="p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Actions Rapides</h3>
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @can('Super Admin|Admin')
                <a href="{{ route('admin.users.index') }}"
                   class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-users mx-auto h-12 w-12 text-gray-400"></i>
                    <span class="mt-2 block text-sm font-semibold text-gray-900">Gérer Utilisateurs</span>
                </a>
                @endcan

                @can('Super Admin|Admin|Gestionnaire Flotte')
                <a href="{{ route('admin.vehicles.index') }}"
                   class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-car mx-auto h-12 w-12 text-gray-400"></i>
                    <span class="mt-2 block text-sm font-semibold text-gray-900">Gérer Véhicules</span>
                </a>

                <a href="{{ route('admin.drivers.index') }}"
                   class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-user-tie mx-auto h-12 w-12 text-gray-400"></i>
                    <span class="mt-2 block text-sm font-semibold text-gray-900">Gérer Chauffeurs</span>
                </a>

                <a href="{{ route('admin.assignments.index') }}"
                   class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-clipboard-list mx-auto h-12 w-12 text-gray-400"></i>
                    <span class="mt-2 block text-sm font-semibold text-gray-900">Affectations</span>
                </a>
                @endcan
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
        const target = parseInt(counter.textContent);
        if (target && target > 0) {
            let current = 0;
            const increment = target / 20;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current);
                }
            }, 100);
        }
    });
});
</script>
@endpush