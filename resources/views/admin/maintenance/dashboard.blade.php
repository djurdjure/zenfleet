<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de Bord de la Maintenance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- SECTION DES STATISTIQUES CLÉS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                {{-- Carte État de la Flotte --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-lg col-span-1 md:col-span-2 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">État Actuel de la Flotte</h3>
                    <div id="fleet-status-chart"></div>
                </div>
                {{-- Cartes KPI --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex flex-col justify-center">
                    <h4 class="text-sm font-medium text-gray-500">Maintenance Urgente</h4>
                    <p class="mt-1 text-3xl font-semibold text-orange-600">{{ $urgentPlans->count() }}</p>
                    <p class="text-xs text-gray-500">Plans nécessitant une attention</p>
                </div>
                 <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex flex-col justify-center">
                    <h4 class="text-sm font-medium text-gray-500">Total Véhicules</h4>
                    <p class="mt-1 text-3xl font-semibold text-primary-600">{{ $vehicleStats->sum() }}</p>
                    <p class="text-xs text-gray-500">actifs dans la flotte</p>
                </div>
            </div>

            {{-- SECTION DES JAUGES D'URGENCE --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4">Échéances de Maintenance Prioritaires</h3>
                    @if($urgentPlans->isEmpty())
                        <div class="text-center py-8 text-gray-500">Aucune maintenance urgente à signaler.</div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($urgentPlans as $plan)
                                <div class="border rounded-lg p-4 flex flex-col">
                                    <div class="text-center">
                                        {{-- Placeholder pour la jauge ApexCharts --}}
                                        <div id="gauge-{{ $plan['id'] }}"></div>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <p class="font-semibold text-gray-800">{{ $plan['vehicle_name'] }}</p>
                                        <p class="text-sm text-gray-600">{{ $plan['maintenance_type'] }}</p>
                                        <p class="text-xs font-mono text-gray-500">{{ $plan['plate'] }}</p>
                                        <p class="mt-2 text-sm font-bold {{ $plan['urgency_percent'] >= 90 ? 'text-red-600' : 'text-gray-700' }}">
                                            Échéance : {{ $plan['next_due'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.ApexCharts) {
            // --- Configuration du Graphique Circulaire (Donut) ---
            const fleetStatusData = @json($vehicleStats);
            const donutOptions = {
                series: Object.values(fleetStatusData),
                labels: Object.keys(fleetStatusData),
                chart: { type: 'donut', height: 250 },
                colors: ['#10B981', '#F59E0B', '#3B82F6', '#EF4444', '#6B7280'], // Vert, Orange, Bleu, Rouge, Gris
                legend: { position: 'bottom' },
                responsive: [{ breakpoint: 480, options: { chart: { width: 200 }, legend: { position: 'bottom' } } }]
            };
            new ApexCharts(document.querySelector("#fleet-status-chart"), donutOptions).render();

            // --- Configuration des Jauges Radiales ---
            const urgentPlansData = @json($urgentPlans);
            urgentPlansData.forEach(plan => {
                const gaugeOptions = {
                    series: [plan.urgency_percent],
                    chart: { type: 'radialBar', height: 200, sparkline: { enabled: true } },
                    plotOptions: {
                        radialBar: {
                            startAngle: -90,
                            endAngle: 90,
                            hollow: { margin: 5, size: '60%' },
                            track: { background: '#e7e7e7', strokeWidth: '97%' },
                            dataLabels: {
                                name: { show: false },
                                value: { offsetY: -2, fontSize: '22px' }
                            }
                        }
                    },
                    grid: { padding: { top: -10 } },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'horizontal',
                            shadeIntensity: 0.5,
                            gradientToColors: [plan.urgency_percent > 85 ? '#EF4444' : '#F59E0B'], // Rouge si > 85%, sinon Orange
                            inverseColors: false,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 100]
                        },
                    },
                    labels: ['Progression'],
                };
                new ApexCharts(document.querySelector(`#gauge-${plan.id}`), gaugeOptions).render();
            });
        }
    });
    </script>
    @endpush
</x-app-layout>
