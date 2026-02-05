@extends('layouts.admin.catalyst')

@section('title', 'Analytics - Statuts Véhicules & Chauffeurs')

@section('content')
{{-- ====================================================================
 📊 DASHBOARD ANALYTICS STATUTS - Enterprise-Grade
 ====================================================================
 Dashboard complet pour analyse des transitions de statuts avec:
 - KPI Cards avec métriques temps réel
 - Graphiques ApexCharts interactifs
 - Filtres avancés (date range, entité, type)
 - Timeline des changements récents
 - Export CSV/PDF (à implémenter)

 @version 1.0-Enterprise
 ==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- ===============================================
            HEADER avec filtres
        =============================================== --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                    <i class="fas fa-chart-line text-blue-600"></i>
                    Analytics - Statuts
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    Analyse des transitions et métriques de performance
                </p>
            </div>

            {{-- Bouton export --}}
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all shadow-sm">
                    <i class="fas fa-download"></i>
                    <span>Exporter PDF</span>
                </button>
            </div>
        </div>

        {{-- ===============================================
            FILTRES
        =============================================== --}}
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('admin.analytics.status-dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Type d'entité --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="entity_type" class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="vehicle" {{ request('entity_type', 'vehicle') === 'vehicle' ? 'selected' : '' }}>Véhicules</option>
                        <option value="driver" {{ request('entity_type') === 'driver' ? 'selected' : '' }}>Chauffeurs</option>
                    </select>
                </div>

                {{-- Date début --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                    <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                           class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Date fin --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                    <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                           class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Bouton filtrer --}}
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>

        {{-- ===============================================
            KPI CARDS
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Total changements --}}
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Total Changements</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($metrics['total_changes']) }}</p>
                        @if($metrics['growth_percentage'] != 0)
                            <p class="text-xs mt-1 {{ $metrics['growth_percentage'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                <i class="fas fa-{{ $metrics['growth_percentage'] > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ abs($metrics['growth_percentage']) }}% vs période précédente
                            </p>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            {{-- Changements manuels --}}
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Manuels</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($metrics['manual_changes']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $metrics['total_changes'] > 0 ? round(($metrics['manual_changes'] / $metrics['total_changes']) * 100, 1) : 0 }}% du total
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-edit text-xl text-green-600"></i>
                    </div>
                </div>
            </div>

            {{-- Changements automatiques --}}
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Automatiques</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($metrics['automatic_changes']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $metrics['total_changes'] > 0 ? round(($metrics['automatic_changes'] / $metrics['total_changes']) * 100, 1) : 0 }}% du total
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-robot text-xl text-purple-600"></i>
                    </div>
                </div>
            </div>

            {{-- Moyenne par entité --}}
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Moyenne / Entité</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $metrics['avg_changes_per_entity'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $metrics['unique_entities'] }} entités actives
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-bar text-xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
            GRAPHIQUES
        =============================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Graphique changements quotidiens --}}
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-area text-blue-600 mr-2"></i>
                    Changements quotidiens
                </h3>
                <div id="dailyChangesChart"></div>
            </div>

            {{-- Distribution actuelle des statuts --}}
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-pie text-green-600 mr-2"></i>
                    Distribution actuelle
                </h3>
                <div id="statusDistributionChart"></div>
            </div>
        </div>

        {{-- ===============================================
            TOP TRANSITIONS & TOP VÉHICULES
        =============================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Top transitions --}}
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-exchange-alt text-purple-600 mr-2"></i>
                    Top 10 Transitions
                </h3>
                <div class="space-y-3">
                    @forelse($transitionStats as $stat)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="text-sm">
                                    <span class="font-medium text-gray-900">{{ $stat['from'] }}</span>
                                    <i class="fas fa-arrow-right text-gray-400 mx-2"></i>
                                    <span class="font-medium text-blue-600">{{ $stat['to'] }}</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $stat['count'] }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">Aucune transition enregistrée</p>
                    @endforelse
                </div>
            </div>

            {{-- Top véhicules avec changements --}}
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-car text-orange-600 mr-2"></i>
                    Véhicules les plus actifs
                </h3>
                <div class="space-y-3">
                    @forelse($topVehiclesChanges as $vehicle)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="text-sm font-medium text-gray-900">{{ $vehicle['vehicle_name'] }}</div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">
                                {{ $vehicle['changes_count'] }} changements
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">Aucun véhicule</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ===============================================
            HISTORIQUE RÉCENT
        =============================================== --}}
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-history text-gray-600 mr-2"></i>
                Changements récents
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transition</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Raison</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Par</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentChanges as $change)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $change['entity_name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $change['from_status'] }} <i class="fas fa-arrow-right text-gray-400 mx-1"></i> <span class="text-blue-600 font-medium">{{ $change['to_status'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ Str::limit($change['reason'] ?? 'N/A', 40) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $change['changed_by'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $change['changed_at'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $change['change_type'] === 'manual' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-green-50 text-green-700 border border-green-200' }}">
                                        {{ ucfirst($change['change_type']) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Aucun changement récent
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Graphique des changements quotidiens
const dailyChangesData = @json($dailyChanges);
const dailyChangesOptions = {
    series: [{
        name: 'Changements',
        data: dailyChangesData.map(d => d.count)
    }],
    chart: {
        type: 'area',
        height: 300,
        toolbar: {
            show: false
        }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'smooth',
        width: 2
    },
    xaxis: {
        categories: dailyChangesData.map(d => d.date),
        labels: {
            rotate: -45
        }
    },
    colors: ['#3b82f6'],
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.3,
        }
    }
};
const dailyChangesChart = new ApexCharts(document.querySelector("#dailyChangesChart"), dailyChangesOptions);
dailyChangesChart.render();

// Graphique distribution des statuts
const statusDistributionData = @json($statusDistribution);
const statusDistributionOptions = {
    series: statusDistributionData.map(d => d.count),
    chart: {
        type: 'donut',
        height: 300
    },
    labels: statusDistributionData.map(d => d.status),
    colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
    legend: {
        position: 'bottom'
    }
};
const statusDistributionChart = new ApexCharts(document.querySelector("#statusDistributionChart"), statusDistributionOptions);
statusDistributionChart.render();
</script>
@endpush
