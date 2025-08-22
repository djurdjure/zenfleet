<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Planning des Affectations') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="planning Gantt()">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            {{-- The content of the GANTT chart will be built here in subsequent steps --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    @php
                        $period = \Carbon\CarbonPeriod::create($dateRange['start'], $dateRange['end']);
                        $days = iterator_to_array($period->filter(fn($date) => $date->isWeekday() || $viewMode === 'month')); // Show all days in month view
                        $totalDays = count($days);
                        $today = \Carbon\Carbon::today();
                    @endphp

                    {{-- Section des Contrôles --}}
                    <div id="planning-controls" class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center space-x-2">
                                <div class="relative inline-block text-left">
                                    <button @click="changeView('week')" :class="{'bg-primary-600 text-white': viewMode === 'week', 'bg-white text-gray-700 hover:bg-gray-100': viewMode !== 'week'}" class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500">Semaine</button>
                                    <button @click="changeView('month')" :class="{'bg-primary-600 text-white': viewMode === 'month', 'bg-white text-gray-700 hover:bg-gray-100': viewMode !== 'month'}" class="relative -ml-px inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500">Mois</button>
                                </div>
                                <button @click="goToday()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Aujourd'hui</button>
                                <div class="flex items-center rounded-md shadow-sm">
                                    <button @click="previousPeriod()" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"><x-lucide-chevron-left class="h-5 w-5" /></button>
                                    <button @click="nextPeriod()" class="relative -ml-px inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"><x-lucide-chevron-right class="h-5 w-5" /></button>
                                </div>
                            </div>
                            <div class="text-center flex items-center justify-center">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    @if($viewMode === 'week') {{ $dateRange['start']->format('j M') }} - {{ $dateRange['end']->format('j M, Y') }} @else {{ $dateRange['start']->format('F Y') }} @endif
                                </h3>
                            </div>
                            <div class="flex items-center space-x-2 justify-self-end">
                                <form action="{{ route('admin.planning.index') }}" method="GET" class="flex items-center space-x-2">
                                    <input type="hidden" name="view_mode" :value="viewMode">
                                    <input type="hidden" name="date" :value="baseDate">
                                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Véhicule ou chauffeur..." class="block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                                    <select name="per_page" onchange="this.form.submit()" class="block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                                        @foreach(['15', '30', '50'] as $value)
                                        <option value="{{ $value }}" @selected(($filters['per_page'] ?? '15') == $value)>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Filtrer</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Grille GANTT --}}
                    <div class="overflow-x-auto border border-gray-200 rounded-lg bg-white">
                        <div class="grid" style="grid-template-columns: minmax(200px, 1.5fr) repeat({{ $totalDays }}, minmax(60px, 1fr));">

                            {{-- En-tête Vide pour la colonne des véhicules --}}
                            <div class="sticky left-0 z-20 bg-gray-100 border-b border-r border-gray-200 p-2 font-semibold text-gray-600 text-sm">Véhicule</div>

                            {{-- En-têtes des Jours --}}
                            @foreach ($days as $day)
                                <div class="border-b border-r border-gray-200 p-2 text-center {{ $day->isSameDay($today) ? 'bg-primary-50' : 'bg-gray-50' }}">
                                    <div class="font-bold text-xs uppercase text-gray-500">{{ $day->isoFormat('ddd') }}</div>
                                    <div class="text-lg font-semibold {{ $day->isSameDay($today) ? 'text-primary-600' : 'text-gray-800' }}">{{ $day->format('j') }}</div>
                                </div>
                            @endforeach

                            {{-- Lignes des Véhicules --}}
                            @forelse ($vehicles as $vehicle)
                                <div class="sticky left-0 z-10 bg-white border-b border-r border-gray-200 p-2 flex flex-col justify-center">
                                    <p class="font-bold text-sm text-gray-800">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                                    <p class="font-mono text-xs text-gray-500">{{ $vehicle->registration_plate }}</p>
                                </div>

                                <div class="col-span-{{ $totalDays }} grid relative" style="grid-template-columns: repeat({{ $totalDays }}, minmax(60px, 1fr));">
                                    {{-- Ligne de fond de la grille --}}
                                    @foreach ($days as $day)
                                        <div class="border-r border-b border-gray-200 {{ $day->isSameDay($today) ? 'bg-primary-50/50' : '' }}"></div>
                                    @endforeach

                                    {{-- Barres d'affectation --}}
                                    @foreach ($vehicle->assignments as $assignment)
                                        @php
                                            $start = \Carbon\Carbon::parse($assignment->start_datetime);
                                            $end = $assignment->end_datetime ? \Carbon\Carbon::parse($assignment->end_datetime) : $dateRange['end']->copy()->endOfDay();

                                            // Clamp dates to the view range
                                            $clampedStart = $start->max($dateRange['start']);
                                            $clampedEnd = $end->min($dateRange['end']->copy()->endOfDay());

                                            $startColumn = $dateRange['start']->diffInDays($clampedStart) + 1;
                                            $duration = $clampedStart->diffInDays($clampedEnd) + 1;

                                            if ($startColumn < 1) continue; // Skip if it starts after the period
                                            if ($startColumn > $totalDays + 1) continue; // Skip if it starts after the period

                                            $endColumn = $startColumn + $duration;
                                        @endphp

                                        <div x-data="{ tooltipVisible: false }"
                                             @mouseenter="tooltipVisible = true"
                                             @mouseleave="tooltipVisible = false"
                                             class="absolute h-full p-1 z-10"
                                             style="grid-column: {{ $startColumn }} / span {{ $duration }};">

                                            <div class="h-full rounded-md shadow-sm text-white text-xs font-semibold flex items-center justify-center px-2 truncate cursor-pointer
                                                {{ $assignment->end_datetime ? 'bg-gray-400 hover:bg-gray-500' : 'bg-green-500 hover:bg-green-600' }}">
                                                {{ $assignment->driver->first_name ?? 'N/A' }}
                                            </div>

                                            {{-- Infobulle (Tooltip) --}}
                                            <div x-show="tooltipVisible" x-transition class="absolute z-30 bottom-full mb-2 w-64 bg-gray-800 text-white text-sm rounded-lg shadow-lg p-3" style="display: none;">
                                                <p class="font-bold border-b border-gray-600 pb-1 mb-1">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                                                <p><strong class="font-medium text-gray-300">Chauffeur:</strong> {{ $assignment->driver->full_name ?? 'N/A' }}</p>
                                                <p><strong class="font-medium text-gray-300">Début:</strong> {{ $start->format('d/m/Y H:i') }}</p>
                                                <p><strong class="font-medium text-gray-300">Fin:</strong> {{ $assignment->end_datetime ? $end->format('d/m/Y H:i') : 'En cours' }}</p>
                                                <p><strong class="font-medium text-gray-300">Km début:</strong> {{ number_format($assignment->start_mileage, 0, ',', ' ') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @empty
                                <div class="col-span-{{ $totalDays + 1 }} text-center py-12 text-gray-500">
                                    Aucun véhicule trouvé pour les filtres sélectionnés.
                                </div>
                            @endforelse
                        </div>
                    </div>
                     <div class="mt-4">
                        {{ $vehicles->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function planningGantt() {
            return {
                viewMode: @json($viewMode),
                baseDate: @json($baseDate->toDateString()),
                filters: @json($filters),

                init() {
                    console.log('Planning component initialized.');
                    console.log('View Mode:', this.viewMode);
                    console.log('Base Date:', this.baseDate);
                },

                navigateTo(newDate) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('date', newDate);
                    url.searchParams.set('view_mode', this.viewMode);
                    window.location.href = url.toString();
                },

                changeView(mode) {
                    this.viewMode = mode;
                    const url = new URL(window.location.href);
                    url.searchParams.set('view_mode', this.viewMode);
                    // Reset date to today when changing view to avoid confusion
                    url.searchParams.delete('date');
                    window.location.href = url.toString();
                },

                goToday() {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('date');
                    url.searchParams.set('view_mode', this.viewMode);
                    window.location.href = url.toString();
                },

                previousPeriod() {
                    let newDate = new Date(this.baseDate);
                    if (this.viewMode === 'week') {
                        newDate.setDate(newDate.getDate() - 7);
                    } else {
                        newDate.setMonth(newDate.getMonth() - 1);
                    }
                    this.navigateTo(newDate.toISOString().split('T')[0]);
                },

                nextPeriod() {
                    let newDate = new Date(this.baseDate);
                    if (this.viewMode === 'week') {
                        newDate.setDate(newDate.getDate() + 7);
                    } else {
                        newDate.setMonth(newDate.getMonth() + 1);
                    }
                    this.navigateTo(newDate.toISOString().split('T')[0]);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
