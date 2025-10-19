@extends('layouts.admin.catalyst')
@section('title', 'Planning des Affectations')

@section('content')

 <div class="py-12" x-data="planningGantt()">
 <div class="max-w-full mx-auto sm:px-6 lg:px-8">
 <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
 <div class="p-4 sm:p-6 border-b border-gray-200">
 <div class="flex flex-col md:flex-row items-center justify-between gap-4">
 <!-- Left Controls -->
 <div class="flex items-center gap-2">
 <form action="{{ route('admin.planning.index') }}" method="GET" class="relative">
 <input type="hidden" name="view_mode" :value="viewMode">
 <input type="hidden" name="date" :value="baseDate">
 <x-iconify icon="heroicons:magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" / />
 <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Rechercher un actif ou un chauffeur..." class="pl-10 pr-4 py-2 w-64 border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
 </form>
 <button @click="showFilterPanel = true" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
 <x-iconify icon="heroicons:adjustments-horizontal" class="h-5 w-5" / />
 <span>Filtres</span>
 </button>
 </div>

 <!-- Center Controls -->
 <div class="flex items-center gap-2 text-lg font-semibold text-gray-800">
 <button @click="previousPeriod()" class="p-2 rounded-md hover:bg-gray-100"><x-iconify icon="heroicons:chevron-left" class="h-6 w-6" / /></button>
 <h3 class="w-48 text-center">
 @if($viewMode === 'month') {{ $baseDate->isoFormat('MMMM YYYY') }}
 @elseif($viewMode === 'week') {{ $dateRange['start']->isoFormat('D MMM') }} - {{ $dateRange['end']->isoFormat('D MMM, YYYY') }}
 @elseif($viewMode === 'day') {{ $baseDate->isoFormat('D MMMM YYYY') }}
 @else {{ $baseDate->isoFormat('YYYY') }}
 @endif
 </h3>
 <button @click="nextPeriod()" class="p-2 rounded-md hover:bg-gray-100"><x-iconify icon="heroicons:chevron-right" class="h-6 w-6" / /></button>
 </div>

 <!-- Right Controls -->
 <div class="flex items-center gap-2">
 <button @click="goToday()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Aujourd'hui</button>
 <div class="relative" x-data="{ open: false }">
 <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
 <span class="capitalize">{{ $viewMode }}</span>
 <x-iconify icon="heroicons:chevron-down" class="h-5 w-5" / />
 </button>
 <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-32 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20">
 <div class="py-1">
 <a href="#" @click.prevent="changeView('day')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Jour</a>
 <a href="#" @click.prevent="changeView('week')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Semaine</a>
 <a href="#" @click.prevent="changeView('month')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mois</a>
 <a href="#" @click.prevent="changeView('year')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Année</a>
 </div>
 </div>
 </div>
 <div class="p-1 bg-gray-200 rounded-lg flex items-center">
 <button @click="activeView = 'gantt'" :class="activeView === 'gantt' ? 'bg-white shadow' : ''" class="p-1.5 rounded-md text-gray-600 hover:bg-white"><x-iconify icon="heroicons:squares-2x2" class="h-5 w-5" / /></button>
 <button @click="activeView = 'table'" :class="activeView === 'table' ? 'bg-white shadow' : ''" class="p-1.5 rounded-md text-gray-600 hover:bg-white"><x-iconify icon="heroicons:list-bullet" class="h-5 w-5" / /></button>
 </div>
 <button @click="openNewModal()" class="inline-flex items-center gap-2 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
 <x-iconify icon="heroicons:plus" class="h-5 w-5" / />
 <span>Ajouter une Affectation</span>
 </button>
 </div>
 </div>
 </div>

 <div class="p-6 text-gray-900 space-y-6" x-show="activeView === 'gantt'">
 @php
 if ($viewMode === 'day') {
 $period = \Carbon\CarbonPeriod::create($dateRange['start'], '1 hour', $dateRange['end']);
 $totalUnits = 24;
 } else {
 $period = \Carbon\CarbonPeriod::create($dateRange['start'], $dateRange['end']);
 $totalUnits = $dateRange['start']->diffInDays($dateRange['end']) + 1;
 }
 $today = \Carbon\Carbon::today();
 @endphp

 {{-- Grille GANTT --}}
 <div class="overflow-x-auto border border-gray-200 rounded-lg bg-white">
 <div class="grid" style="grid-template-columns: minmax(250px, 1.5fr) repeat({{ $totalUnits }}, minmax(60px, 1fr));">

 {{-- En-tête de Colonne Asset --}}
 <div class="sticky left-0 z-20 bg-gray-100 border-b border-r border-gray-200 p-2 flex items-center justify-between">
 <span class="font-semibold text-gray-600 text-sm">Asset</span>
 <button @click="toggleSort('alpha_asc')" class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 hover:text-gray-800" :class="{'text-primary-600': filters.sort === 'alpha_asc'}">
 A-Z
 <x-iconify icon="heroicons:bars-arrow-down" class="h-4 w-4" / />
 </button>
 </div>

 {{-- En-têtes des Jours/Heures --}}
 @foreach ($period as $date)
 <div class="border-b border-r border-gray-200 p-2 text-center {{ $date->isSameDay($today) ? 'bg-primary-50' : 'bg-gray-50' }}">
 @if($viewMode === 'day')
 <div class="font-bold text-xs uppercase text-gray-500">{{ $date->format('H:i') }}</div>
 @else
 <div class="font-bold text-xs uppercase text-gray-500">{{ $date->isoFormat('ddd') }}</div>
 <div class="text-lg font-semibold {{ $date->isSameDay($today) ? 'text-primary-600' : 'text-gray-800' }}">{{ $date->format('j') }}</div>
 @endif
 </div>
 @endforeach

 {{-- Lignes des Véhicules --}}
 @forelse ($vehicles as $vehicle)
 <div class="sticky left-0 z-10 bg-white border-b border-r border-gray-200 p-2 flex items-center gap-3">
 <img src="{{ $vehicle->photo_path ? asset('storage/' . $vehicle->photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($vehicle->brand) . '&color=7F9CF5&background=EBF4FF' }}" alt="Photo of {{ $vehicle->brand }}" class="h-10 w-10 rounded-md object-cover">
 <div class="flex-grow">
 <p class="font-bold text-sm text-gray-800 flex items-center">
 <span class="mr-2 w-2 h-2 rounded-full {{ match($vehicle->vehicleStatus->name ?? 'default') { 'Parking' => 'bg-blue-500', 'En mission' => 'bg-green-500', 'En maintenance' => 'bg-orange-500', 'Hors service' => 'bg-red-500', default => 'bg-gray-400' } }}"></span>
 {{ $vehicle->brand }} {{ $vehicle->model }}
 </p>
 <p class="text-xs text-gray-500">
 {{ $vehicle->vehicleStatus->name ?? 'N/A' }} &bull; {{ $vehicle->vehicleType->name ?? 'N/A' }}
 </p>
 </div>
 </div>

 <div x-ref="gridRow_{{ $vehicle->id }}" data-vehicle-id="{{ $vehicle->id }}" class="col-span-{{ $totalUnits }} grid relative" style="grid-template-columns: repeat({{ $totalUnits }}, minmax(60px, 1fr));">
 {{-- Ligne de fond de la grille --}}
 @foreach ($period as $index => $date)
 <div class="border-r border-b border-gray-200 {{ $date->isSameDay($today) ? 'bg-primary-50/50' : '' }}" data-date-index="{{ $index }}"></div>
 @endforeach

 {{-- Barres d'affectation --}}
 @foreach ($vehicle->assignments as $assignment)
 @php
 $start = \Carbon\Carbon::parse($assignment->start_datetime);
 $isOngoing = is_null($assignment->end_datetime);
 // Si pas de date de fin, on la fait courir jusqu'à la fin de la période visible pour le calcul
 $end = $isOngoing ? $dateRange['end']->copy()->endOfDay() : \Carbon\Carbon::parse($assignment->end_datetime);

 // On ne processe que les affectations visibles dans la période
 if ($end->isBefore($dateRange['start']) || $start->isAfter($dateRange['end'])) {
 continue;
 }

 $viewStart = $dateRange['start']->copy()->startOfDay();
 $viewEnd = $dateRange['end']->copy()->endOfDay();
 $totalMinutesInView = $viewStart->diffInMinutes($viewEnd);

 // On s'assure que les barres ne dépassent pas de la vue
 $barStart = $start->max($viewStart);
 $barEnd = $end->min($viewEnd);

 $startOffsetMinutes = $viewStart->diffInMinutes($barStart);
 $durationMinutes = $barStart->diffInMinutes($barEnd);

 // Sécurité pour éviter les divisions par zéro ou les durées négatives
 if ($totalMinutesInView <= 0 || $durationMinutes <= 0) {
 continue;
 }

 $leftPercentage = ($startOffsetMinutes / $totalMinutesInView) * 100;
 $widthPercentage = ($durationMinutes / $totalMinutesInView) * 100;
 @endphp

 <div id="assignment_{{ $assignment->id }}"
 data-assignment-id="{{ $assignment->id }}"
 data-duration-minutes="{{ $start->diffInMinutes($end) }}"
 @click="openEditModal({{ $assignment->id }})"
 class="absolute h-full p-1 z-10"
 style="left: {{ $leftPercentage }}%; width: {{ $widthPercentage }}%;">
 <div @class([
 'relative h-full w-full border-l-4 border-blue-600 rounded-md shadow-sm text-blue-900 text-xs flex flex-col justify-center px-2 truncate cursor-pointer hover:ring-2 hover:ring-blue-500',
 'bg-blue-200' => !$isOngoing,
 'ongoing-assignment-bar' => $isOngoing,
 ])>
 <p class="font-bold truncate">{{ $assignment->driver->full_name ?? 'N/A' }}</p>
 <p class="font-normal">
 {{ $start->format('H:i') }}
 @if (!$isOngoing)
 - {{ $end->format('H:i') }}
 @else
 - No end date
 @endif
 </p>
 </div>
 </div>
 @endforeach
 </div>
 @empty
 <div class="col-span-{{ $totalUnits + 1 }} text-center py-12 text-gray-500">
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

 {{-- Placeholders for Modals and Side Panels --}}
 <div x-show="showFilterPanel" @click.away="showFilterPanel = false" class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50" x-transition>
 <div class="absolute top-0 right-0 h-full bg-white w-96 p-6 shadow-lg">
 <h3 class="text-lg font-semibold mb-4">Filtres Avancés</h3>
 <p class="text-gray-600">Les options de filtre seront ici.</p>
 </div>
 </div>

 <div x-show="showAssignmentModal" class="fixed inset-0 z-40 flex items-center justify-center bg-gray-900 bg-opacity-50" x-transition>
 <div @click.away="showAssignmentModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl">
 <h3 class="text-xl font-semibold text-gray-800 mb-4" x-text="modalTitle"></h3>
 <p class="text-gray-600">Le formulaire de création/édition d'affectation sera ici.</p>
 <div class="mt-6 flex justify-end">
 <button @click="showAssignmentModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">Annuler</button>
 </div>
 </div>
 </div>
 </div>

 @push('scripts')
 <script>
 function planningGantt() {
 return {
 viewMode: @json($viewMode),
 activeView: 'gantt', // or 'table'
 baseDate: @json($baseDate->toDateString()),
 filters: @json($filters),
 showFilterPanel: false,
 showAssignmentModal: false,
 modalTitle: '',

 init() {
 console.log('Planning component initialized.');
 this.initSortable();
 },

 initSortable() {
 this.$nextTick(() => {
 const ganttRows = this.$el.querySelectorAll('[x-ref^="gridRow_"]');
 ganttRows.forEach(row => {
 new Sortable(row, {
 group: 'gantt-assignments',
 animation: 150,
 onEnd: (evt) => {
 const item = evt.item;
 const toRow = evt.to;
 const assignmentId = item.dataset.assignmentId;
 const newVehicleId = toRow.dataset.vehicleId;

 const ganttStartDate = new Date(this.baseDate);
 const totalWidth = toRow.offsetWidth;
 const leftPosition = item.offsetLeft;

 const totalMinutesInView = (new Date('{{ $dateRange['end']->toDateTimeString() }}') - new Date('{{ $dateRange['start']->toDateTimeString() }}')) / 60000;
 const minutesOffset = (leftPosition / totalWidth) * totalMinutesInView;

 let newStartDate = new Date('{{ $dateRange['start']->toDateTimeString() }}');
 newStartDate.setMinutes(newStartDate.getMinutes() + minutesOffset);

 let durationMinutes = parseInt(item.dataset.durationMinutes, 10);
 let newEndDate = new Date(newStartDate.getTime() + durationMinutes * 60000);

 this.updateAssignment(assignmentId, newVehicleId, newStartDate, newEndDate);
 }
 });
 });
 });
 },

 updateAssignment(id, vehicleId, startDate, endDate) {
 const data = {
 vehicle_id: vehicleId,
 start_datetime: startDate.toISOString().slice(0, 19).replace('T', ' '),
 end_datetime: endDate.toISOString().slice(0, 19).replace('T', ' '),
 _token: '{{ csrf_token() }}'
 };

 fetch(`/api/admin/assignments/${id}/move`, {
 method: 'PATCH',
 headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
 body: JSON.stringify(data)
 })
 .then(async response => {
 if (!response.ok) {
 const errorData = await response.json();
 throw new Error(errorData.message || `Erreur ${response.status}`);
 }
 return response.json();
 })
 .then(data => {
 console.log('Assignment updated:', data);
 window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Affectation mise à jour !' } }));
 })
 .catch(error => {
 console.error('Error updating assignment:', error);
 window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: error.message } }));
 // We reload to revert the visual change on failure
 setTimeout(() => window.location.reload(), 1500);
 });
 },

 openNewModal() {
 this.modalTitle = 'Ajouter une Affectation';
 // Here you would typically clear any form data
 this.showAssignmentModal = true;
 },

 async openEditModal(assignmentId) {
 this.modalTitle = 'Modifier l\'Affectation';
 // Here you would fetch assignment data from the API
 // For now, we just open the modal
 console.log('Fetching data for assignment', assignmentId);
 this.showAssignmentModal = true;
 },

 toggleSort(type) {
 let newSort = this.filters.sort === type ? '' : type;
 const url = new URL(window.location.href);
 if (newSort) {
 url.searchParams.set('sort', newSort);
 } else {
 url.searchParams.delete('sort');
 }
 window.location.href = url.toString();
 },

 navigateTo(newDate) {
 const url = new URL(window.location.href);
 url.searchParams.set('date', newDate);
 url.searchParams.set('view_mode', this.viewMode);
 // Persist search filters
 for (const key in this.filters) {
 if (this.filters[key]) {
 url.searchParams.set(key, this.filters[key]);
 }
 }
 window.location.href = url.toString();
 },

 changeView(mode) {
 this.viewMode = mode;
 const url = new URL(window.location.href);
 url.searchParams.set('view_mode', this.viewMode);
 url.searchParams.delete('date'); // Reset date to today
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
 if (this.viewMode === 'week') newDate.setDate(newDate.getDate() - 7);
 else if (this.viewMode === 'month') newDate.setMonth(newDate.getMonth() - 1);
 else if (this.viewMode === 'year') newDate.setFullYear(newDate.getFullYear() - 1);
 else if (this.viewMode === 'day') newDate.setDate(newDate.getDate() - 1);
 this.navigateTo(newDate.toISOString().split('T')[0]);
 },

 nextPeriod() {
 let newDate = new Date(this.baseDate);
 if (this.viewMode === 'week') newDate.setDate(newDate.getDate() + 7);
 else if (this.viewMode === 'month') newDate.setMonth(newDate.getMonth() + 1);
 else if (this.viewMode === 'year') newDate.setFullYear(newDate.getFullYear() + 1);
 else if (this.viewMode === 'day') newDate.setDate(newDate.getDate() + 1);
 this.navigateTo(newDate.toISOString().split('T')[0]);
 }
 }
 }
 </script>
 @endpush
@endsection