@extends('layouts.admin.catalyst')

@section('title', 'Dashboard Administration')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 -m-6 p-6">
    {{-- 🎨 En-tête avec nom organisation --}}
    <div class="max-w-7xl mx-auto mb-8">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-crown text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900">
                            Dashboard Administrateur
                        </h1>
                        <p class="text-gray-600 text-lg mt-2">
                            {{ $organization->name ?? 'Organisation' }} - Vue d'ensemble administrative
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Connecté en tant que</div>
                    <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                    <div class="text-sm text-blue-600">Administrateur</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 📊 Statistiques principales --}}
    <div class="max-w-7xl mx-auto mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Utilisateurs --}}
            <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['organizationUsers'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Utilisateurs</div>
                        <div class="text-xs text-green-600">{{ $stats['activeUsers'] ?? 0 }} actifs</div>
                    </div>
                </div>
            </div>

            {{-- Véhicules --}}
            <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-car text-white"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['organizationVehicles'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Véhicules</div>
                        <div class="text-xs text-green-600">{{ $stats['availableVehicles'] ?? 0 }} disponibles</div>
                    </div>
                </div>
            </div>

            {{-- Chauffeurs --}}
            <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-tie text-white"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['organizationDrivers'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Chauffeurs</div>
                        <div class="text-xs text-green-600">{{ $stats['activeDrivers'] ?? 0 }} actifs</div>
                    </div>
                </div>
            </div>

            {{-- Affectations --}}
            <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-white"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['activeAssignments'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Affectations</div>
                        <div class="text-xs text-blue-600">En cours</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 🚨 Mode dégradé si erreur --}}
    @if(isset($error) || isset($fallbackMode))
    <div class="max-w-7xl mx-auto mb-8">
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-3xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-amber-800">Erreur Dashboard</h3>
                    <p class="text-amber-700">{{ $error ?? 'Données partiellement indisponibles - Mode dégradé activé' }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 📈 Contenu principal --}}
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Activité récente --}}
            <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
                <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Activité Récente</h2>
                </div>
                <div class="mt-6">
                    @if(isset($recentActivity) && count($recentActivity) > 0)
                        @foreach($recentActivity as $activity)
                        <div class="flex items-center gap-4 py-3 border-b border-gray-50 last:border-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-{{ $activity['icon'] ?? 'info' }} text-blue-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $activity['title'] ?? 'Activité' }}</div>
                                <div class="text-sm text-gray-600">{{ $activity['description'] ?? '' }}</div>
                                <div class="text-xs text-gray-500">{{ isset($activity['timestamp']) ? $activity['timestamp']->diffForHumans() : '' }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-4"></i>
                            <p>Aucune activité récente</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Alertes et notifications --}}
            <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
                <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-pink-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-bell text-white"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Alertes & Notifications</h2>
                </div>
                <div class="mt-6">
                    @if(isset($alerts) && count($alerts) > 0)
                        @foreach($alerts as $alert)
                        <div class="flex items-center gap-4 py-3 border-b border-gray-50 last:border-0">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation text-red-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $alert['title'] ?? 'Alerte' }}</div>
                                <div class="text-sm text-gray-600">{{ $alert['description'] ?? '' }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-check-circle text-3xl mb-4 text-green-500"></i>
                            <p>Aucune alerte</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Maintenance à venir --}}
            <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
                <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-wrench text-white"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Maintenance à Venir</h2>
                </div>
                <div class="mt-6">
                    @if(isset($upcomingMaintenance) && $upcomingMaintenance->count() > 0)
                        @foreach($upcomingMaintenance->take(5) as $maintenance)
                        <div class="flex items-center gap-4 py-3 border-b border-gray-50 last:border-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-wrench text-yellow-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $maintenance->vehicle->registration ?? 'Véhicule' }}</div>
                                <div class="text-sm text-gray-600">{{ $maintenance->description ?? 'Maintenance programmée' }}</div>
                                <div class="text-xs text-gray-500">{{ isset($maintenance->scheduled_date) ? $maintenance->scheduled_date->format('d/m/Y') : '' }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-check-circle text-3xl mb-4 text-green-500"></i>
                            <p>Aucune maintenance prévue</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Distribution des véhicules --}}
            <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
                <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-pie text-white"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">État des Véhicules</h2>
                </div>
                <div class="mt-6">
                    @if(isset($vehicleDistribution) && count($vehicleDistribution) > 0)
                        @foreach($vehicleDistribution as $status => $count)
                        <div class="flex items-center justify-between py-2">
                            <div class="flex items-center gap-3">
                                <div class="w-4 h-4 rounded-full bg-{{ $status === 'available' ? 'green' : ($status === 'in_use' ? 'blue' : 'gray') }}-500"></div>
                                <span class="text-gray-700">{{ ucfirst($status) }}</span>
                            </div>
                            <span class="font-semibold text-gray-900">{{ $count }}</span>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-car text-3xl mb-4"></i>
                            <p>Aucun véhicule</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 🔧 Actions rapides --}}
    <div class="max-w-7xl mx-auto mt-8">
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Actions Rapides</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center gap-3 p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Utilisateurs</span>
                </a>

                <a href="{{ route('admin.vehicles.index') }}" class="flex flex-col items-center gap-3 p-4 bg-green-50 hover:bg-green-100 rounded-xl transition-colors">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-car text-white"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Véhicules</span>
                </a>

                <a href="{{ route('admin.drivers.index') }}" class="flex flex-col items-center gap-3 p-4 bg-orange-50 hover:bg-orange-100 rounded-xl transition-colors">
                    <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-tie text-white"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Chauffeurs</span>
                </a>

                <a href="{{ route('admin.assignments.index') }}" class="flex flex-col items-center gap-3 p-4 bg-purple-50 hover:bg-purple-100 rounded-xl transition-colors">
                    <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-white"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Affectations</span>
                </a>

                <a href="{{ route('admin.maintenance.dashboard') }}" class="flex flex-col items-center gap-3 p-4 bg-yellow-50 hover:bg-yellow-100 rounded-xl transition-colors">
                    <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-wrench text-white"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Maintenance</span>
                </a>

                <a href="{{ route('admin.documents.index') }}" class="flex flex-col items-center gap-3 p-4 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors">
                    <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-white"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Documents</span>
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
    const counters = document.querySelectorAll('.text-2xl.font-bold');
    counters.forEach(counter => {
        const target = parseInt(counter.textContent);
        if (target && target > 0) {
            let current = 0;
            const increment = target / 30;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current);
                }
            }, 50);
        }
    });

    // Auto-refresh des données (optionnel)
    setTimeout(() => {
        window.location.reload();
    }, 300000); // 5 minutes
});
</script>
@endpush