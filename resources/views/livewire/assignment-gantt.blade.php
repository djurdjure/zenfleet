{{-- 📊 Vue Gantt des Affectations Enterprise-Grade --}}
<div class="h-full flex flex-col gantt-container" x-data="assignmentGantt()">
 {{-- Barre d'outils --}}
 <div class="bg-white border-b border-gray-200 px-4 py-3">
 <div class="flex items-center justify-between">
 {{-- Navigation temporelle --}}
 <div class="flex items-center space-x-4">
 <div class="flex items-center space-x-1">
 <button
 wire:click="previousPeriod"
 class="p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md"
 title="Période précédente"
 >
 <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
 </svg>
 </button>

 <button
 wire:click="goToToday"
 class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
 >
 Aujourd'hui
 </button>

 <button
 wire:click="nextPeriod"
 class="p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md"
 title="Période suivante"
 >
 <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
 </svg>
 </button>
 </div>

 <div class="text-lg font-medium text-gray-900">
 {{ $this->period_label }}
 </div>
 </div>

 {{-- Modes de vue --}}
 <div class="flex items-center space-x-4">
 <div class="flex bg-gray-100 rounded-md p-1">
 @foreach($viewModes as $mode => $label)
 <button
 wire:click="$set('viewMode', '{{ $mode }}')"
 class="px-3 py-1 text-sm font-medium rounded transition-colors {{ $viewMode === $mode ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}"
 >
 {{ $label }}
 </button>
 @endforeach
 </div>

 <div class="flex bg-gray-100 rounded-md p-1">
 @foreach($resourceTypes as $type => $label)
 <button
 wire:click="$set('resourceType', '{{ $type }}')"
 class="px-3 py-1 text-sm font-medium rounded transition-colors {{ $resourceType === $type ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}"
 >
 {{ $label }}
 </button>
 @endforeach
 </div>
 </div>

 {{-- Actions --}}
 <div class="flex items-center space-x-2">
 <button
 wire:click="exportPDF"
 class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
 >
 <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
 </svg>
 PDF
 </button>

 <button
 wire:click="exportPNG"
 class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
 >
 <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
 </svg>
 PNG
 </button>
 </div>
 </div>

 {{-- Filtres --}}
 <div class="mt-3 flex items-center space-x-4">
 <div class="flex-1 max-w-md">
 <select
 wire:model.live="resourceFilter"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
 >
 <option value="">Toutes les {{ $resourceType === 'vehicles' ? 'véhicules' : 'chauffeurs' }}</option>
 @if($resourceType === 'vehicles')
 @foreach($resources as $vehicle)
 <option value="{{ $vehicle->id }}">
 {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
 </option>
 @endforeach
 @else
 @foreach($resources as $driver)
 <option value="{{ $driver->id }}">
 {{ $driver->first_name }} {{ $driver->last_name }}
 </option>
 @endforeach
 @endif
 </select>
 </div>

 <div class="flex-1 max-w-md">
 <select
 wire:model.live="statusFilter"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
 >
 <option value="">Tous les statuts</option>
 @foreach($statusOptions as $status => $label)
 <option value="{{ $status }}">{{ $label }}</option>
 @endforeach
 </select>
 </div>

 <div class="text-sm text-gray-500">
 {{ $this->resource_count }} {{ $resourceType === 'vehicles' ? 'véhicules' : 'chauffeurs' }} •
 {{ $this->assignment_count }} affectations
 </div>
 </div>
 </div>

 {{-- Indicateur de chargement --}}
 @if($isLoading)
 <div class="flex-1 flex items-center justify-center bg-gray-50">
 <div class="text-center">
 <svg class="animate-spin h-8 w-8 text-blue-500 mx-auto" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 <p class="mt-2 text-sm text-gray-500">Chargement du planning...</p>
 </div>
 </div>
 @else
 {{-- Vue Gantt principale --}}
 <div class="flex-1 flex overflow-hidden bg-gray-50">
 {{-- Colonne des ressources --}}
 <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
 {{-- En-tête colonne ressources --}}
 <div class="h-20 bg-gray-50 border-b border-gray-200 flex items-center px-4">
 <h3 class="text-sm font-medium text-gray-900">
 {{ $resourceType === 'vehicles' ? 'Véhicules' : 'Chauffeurs' }}
 </h3>
 </div>

 {{-- Liste des ressources --}}
 <div class="flex-1 overflow-y-auto">
 @forelse($resources as $resource)
 <div
 class="h-16 border-b border-gray-100 flex items-center px-4 hover:bg-gray-50 cursor-pointer"
 data-resource-id="{{ $resource->id }}"
 @click="selectResource({{ $resource->id }})"
 >
 <div class="flex-1 min-w-0">
 <div class="text-sm font-medium text-gray-900 truncate">
 @if($resourceType === 'vehicles')
 {{ $resource->registration_plate }}
 @else
 {{ $resource->first_name }} {{ $resource->last_name }}
 @endif
 </div>
 <div class="text-xs text-gray-500 truncate">
 @if($resourceType === 'vehicles')
 {{ $resource->brand }} {{ $resource->model }}
 @else
 {{ $resource->license_number ?? 'Pas de permis renseigné' }}
 @endif
 </div>
 </div>

 {{-- Indicateur d'affectations --}}
 @php
 $resourceAssignments = collect($ganttData[$resource->id]['assignments'] ?? []);
 $activeCount = $resourceAssignments->where('status', 'active')->count();
 $scheduledCount = $resourceAssignments->where('status', 'scheduled')->count();
 @endphp

 @if($activeCount > 0 || $scheduledCount > 0)
 <div class="flex items-center space-x-1">
 @if($activeCount > 0)
 <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
 {{ $activeCount }}
 </span>
 @endif
 @if($scheduledCount > 0)
 <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
 {{ $scheduledCount }}
 </span>
 @endif
 </div>
 @endif
 </div>
 @empty
 <div class="h-16 flex items-center justify-center text-sm text-gray-500">
 Aucune ressource trouvée
 </div>
 @endforelse
 </div>
 </div>

 {{-- Zone temporelle --}}
 <div class="flex-1 flex flex-col overflow-hidden">
 {{-- En-tête temporel --}}
 <div class="h-20 bg-white border-b border-gray-200 overflow-x-auto">
 <div class="flex h-full" style="min-width: {{ count($timeScale) * $ganttConfig['cellWidth'] }}px">
 @foreach($timeScale as $time)
 <div
 class="flex-shrink-0 flex items-center justify-center border-r border-gray-100 text-xs font-medium
 {{ $time['is_today'] ? 'bg-blue-50 text-blue-700' : ($time['is_weekend'] ? 'bg-gray-50 text-gray-500' : 'text-gray-700') }}"
 style="width: {{ $ganttConfig['cellWidth'] }}px"
 >
 {{ $time['label'] }}
 </div>
 @endforeach
 </div>
 </div>

 {{-- Zone des affectations --}}
 <div class="flex-1 overflow-auto">
 <div class="relative" style="min-width: {{ count($timeScale) * $ganttConfig['cellWidth'] }}px">
 @foreach($resources as $index => $resource)
 <div
 class="relative border-b border-gray-100 {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}"
 style="height: {{ $ganttConfig['cellHeight'] }}px"
 data-resource-id="{{ $resource->id }}"
 >
 {{-- Grille temporelle --}}
 <div class="absolute inset-0 flex">
 @foreach($timeScale as $time)
 <div
 class="flex-shrink-0 border-r border-gray-100 {{ $time['is_today'] ? 'bg-blue-50' : ($time['is_weekend'] ? 'bg-gray-100' : '') }}"
 style="width: {{ $ganttConfig['cellWidth'] }}px"
 data-date="{{ $time['date'] }}"
 ></div>
 @endforeach
 </div>

 {{-- Affectations pour cette ressource --}}
 @foreach($ganttData[$resource->id]['assignments'] ?? [] as $assignment)
 @php
 $assignmentData = $assignment;
 $start = \Carbon\Carbon::parse($assignmentData['start']);
 $end = $assignmentData['end'] ? \Carbon\Carbon::parse($assignmentData['end']) : null;
 $periodStart = \Carbon\Carbon::parse($this->startDate);

 // Calculer la position et la largeur
 $startOffset = max(0, $start->diffInDays($periodStart, false));
 if ($viewMode === 'day') {
 $startOffset = max(0, $start->diffInHours($periodStart, false));
 }

 $width = $end ?
 ($viewMode === 'day' ? $start->diffInHours($end) : $start->diffInDays($end)) :
 ($viewMode === 'day' ? 24 : 7); // Largeur par défaut

 $left = $startOffset * $ganttConfig['cellWidth'];
 $widthPx = $width * $ganttConfig['cellWidth'];
 @endphp

 <div
 class="absolute top-1 bottom-1 rounded-md shadow-sm cursor-pointer transition-all hover:shadow-md hover:z-10"
 style="left: {{ $left }}px; width: {{ max(120, $widthPx) }}px; background-color: {{ $assignmentData['color'] }}"
 data-assignment-id="{{ $assignmentData['id'] }}"
 title="{{ $assignmentData['title'] }}"
 @click="viewAssignment({{ $assignmentData['id'] }})"
 x-data="{ dragging: false }"
 draggable="true"
 @dragstart="handleDragStart($event, {{ $assignmentData['id'] }})"
 @dragend="handleDragEnd($event)"
 >
 <div class="h-full flex items-center px-2 text-white text-xs font-medium">
 <div class="flex-1 min-w-0">
 <div class="truncate">
 {{ $assignmentData['title'] }}
 </div>
 @if($assignmentData['meta']['reason'])
 <div class="truncate opacity-75">
 {{ $assignmentData['meta']['reason'] }}
 </div>
 @endif
 </div>

 {{-- Indicateurs de statut --}}
 <div class="flex-shrink-0 ml-1">
 @if($assignmentData['meta']['is_ongoing'])
 <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
 @elseif($assignmentData['status'] === 'scheduled')
 <div class="w-2 h-2 bg-white rounded-full opacity-60"></div>
 @endif
 </div>
 </div>

 {{-- Poignées de redimensionnement --}}
 @if($end)
 <div
 class="absolute top-0 right-0 bottom-0 w-1 cursor-ew-resize hover:bg-white hover:bg-opacity-30"
 @mousedown="startResize($event, {{ $assignmentData['id'] }})"
 ></div>
 @endif
 </div>
 @endforeach

 {{-- Zone de drop pour cette ressource --}}
 <div
 class="absolute inset-0 opacity-0"
 @dragover.prevent
 @drop="handleDrop($event, {{ $resource->id }})"
 ></div>
 </div>
 @endforeach

 {{-- États vides --}}
 @if(count($resources) === 0)
 <div class="h-32 flex items-center justify-center text-gray-500">
 <div class="text-center">
 <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
 </svg>
 <p class="mt-2 text-sm">Aucune ressource disponible</p>
 </div>
 </div>
 @endif
 </div>
 </div>
 </div>
 </div>
 @endif

 {{-- Modal de détail d'affectation --}}
 @if($showAssignmentModal && $selectedAssignment)
 <div class="fixed inset-0 z-50 overflow-y-auto" x-show="true" x-transition>
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$wire.closeModals()"></div>

 <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
 <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
 <div class="flex items-start">
 <div class="flex-shrink-0">
 <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: {{ $selectedAssignment->getStatusColor() }}">
 <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
 </svg>
 </div>
 </div>
 <div class="ml-3 flex-1">
 <h3 class="text-lg leading-6 font-medium text-gray-900">
 Détails de l'affectation
 </h3>
 <div class="mt-4 space-y-3">
 <div class="grid grid-cols-2 gap-4 text-sm">
 <div>
 <dt class="font-medium text-gray-500">Véhicule</dt>
 <dd class="mt-1 text-gray-900">{{ $selectedAssignment->vehicle_display }}</dd>
 </div>
 <div>
 <dt class="font-medium text-gray-500">Chauffeur</dt>
 <dd class="mt-1 text-gray-900">{{ $selectedAssignment->driver_display }}</dd>
 </div>
 <div>
 <dt class="font-medium text-gray-500">Début</dt>
 <dd class="mt-1 text-gray-900">{{ $selectedAssignment->start_datetime->format('d/m/Y H:i') }}</dd>
 </div>
 <div>
 <dt class="font-medium text-gray-500">Fin</dt>
 <dd class="mt-1 text-gray-900">
 {{ $selectedAssignment->end_datetime?->format('d/m/Y H:i') ?? 'Indéterminé' }}
 </dd>
 </div>
 <div>
 <dt class="font-medium text-gray-500">Statut</dt>
 <dd class="mt-1">
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $selectedAssignment->status === 'active' ? 'green' : ($selectedAssignment->status === 'scheduled' ? 'blue' : 'gray') }}-100 text-{{ $selectedAssignment->status === 'active' ? 'green' : ($selectedAssignment->status === 'scheduled' ? 'blue' : 'gray') }}-800">
 {{ $selectedAssignment->status_label }}
 </span>
 </dd>
 </div>
 <div>
 <dt class="font-medium text-gray-500">Durée</dt>
 <dd class="mt-1 text-gray-900">{{ $selectedAssignment->formatted_duration }}</dd>
 </div>
 </div>

 @if($selectedAssignment->reason)
 <div>
 <dt class="font-medium text-gray-500">Motif</dt>
 <dd class="mt-1 text-gray-900">{{ $selectedAssignment->reason }}</dd>
 </div>
 @endif

 @if($selectedAssignment->notes)
 <div>
 <dt class="font-medium text-gray-500">Notes</dt>
 <dd class="mt-1 text-gray-900">{{ $selectedAssignment->notes }}</dd>
 </div>
 @endif

 <div>
 <dt class="font-medium text-gray-500">Créé par</dt>
 <dd class="mt-1 text-gray-900">
 {{ $selectedAssignment->creator?->name ?? 'Inconnu' }}
 <span class="text-gray-500">le {{ $selectedAssignment->created_at->format('d/m/Y à H:i') }}</span>
 </dd>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
 <button
 wire:click="editAssignment({{ $selectedAssignment->id }})"
 class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
 >
 Modifier
 </button>
 <button
 wire:click="duplicateAssignment({{ $selectedAssignment->id }})"
 class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
 >
 Dupliquer
 </button>
 <button
 wire:click="closeModals"
 class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
 >
 Fermer
 </button>
 </div>
 </div>
 </div>
 </div>
 @endif

 {{-- Script Alpine.js intégré --}}
 <script>
 function assignmentGantt() {
 return {
 draggedAssignment: null,
 resizeMode: false,
 originalPosition: null,

 selectResource(resourceId) {
 console.log('Resource selected:', resourceId);
 },

 viewAssignment(assignmentId) {
 this.$wire.viewAssignment(assignmentId);
 },

 handleDragStart(event, assignmentId) {
 this.draggedAssignment = assignmentId;
 event.dataTransfer.effectAllowed = 'move';
 event.dataTransfer.setData('text/html', event.target.outerHTML);
 event.target.style.opacity = '0.5';
 },

 handleDragEnd(event) {
 event.target.style.opacity = '1';
 this.draggedAssignment = null;
 },

 handleDrop(event, resourceId) {
 event.preventDefault();

 if (!this.draggedAssignment) return;

 // Calculer la nouvelle position temporelle
 const rect = event.currentTarget.getBoundingClientRect();
 const x = event.clientX - rect.left;
 const cellWidth = {{ $ganttConfig['cellWidth'] }};
 const dayOffset = Math.floor(x / cellWidth);

 // Calculer la nouvelle date
 const startDate = new Date('{{ $this->startDate }}');
 const newDate = new Date(startDate);

 @if($viewMode === 'day')
 newDate.setHours(newDate.getHours() + dayOffset);
 @else
 newDate.setDate(newDate.getDate() + dayOffset);
 @endif

 const newStart = newDate.toISOString().slice(0, 16);

 // Envoyer au serveur
 this.$wire.moveAssignment(this.draggedAssignment, newStart, resourceId);

 this.draggedAssignment = null;
 },

 startResize(event, assignmentId) {
 event.stopPropagation();
 this.resizeMode = true;

 // Logique de redimensionnement à implémenter
 console.log('Start resize:', assignmentId);
 }
 }
 }

 // Écouter les événements Livewire
 document.addEventListener('livewire:init', () => {
 Livewire.on('assignment-moved', (event) => {
 showNotification(event.message, 'success');
 });

 Livewire.on('move-conflict', (event) => {
 showNotification('Conflit détecté lors du déplacement', 'error');
 });

 Livewire.on('export-gantt-pdf', (event) => {
 // Déclencher l'export PDF
 window.print();
 });

 Livewire.on('export-gantt-png', (event) => {
 // Capturer l'élément Gantt et télécharger en PNG
 html2canvas(document.querySelector('.gantt-container')).then(canvas => {
 const link = document.createElement('a');
 link.download = event.filename;
 link.href = canvas.toDataURL();
 link.click();
 });
 });
 });

 function showNotification(message, type = 'info') {
 // Notification toast simple
 const notification = document.createElement('div');
 notification.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-lg z-50 text-white ${
 type === 'success' ? 'bg-green-500' :
 type === 'error' ? 'bg-red-500' :
 type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
 }`;
 notification.textContent = message;

 document.body.appendChild(notification);

 setTimeout(() => {
 notification.remove();
 }, 5000);
 }
 </script>

 {{-- Styles CSS intégrés --}}
 <style>
 .gantt-container {
 font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
 }

 @media print {
 .gantt-container {
 -webkit-print-color-adjust: exact;
 color-adjust: exact;
 }
 }

 /* Animation pour les éléments en cours de déplacement */
 .dragging {
 transition: all 0.2s ease-in-out;
 transform: scale(1.05);
 z-index: 1000;
 }

 /* Styles pour les poignées de redimensionnement */
 .resize-handle {
 transition: background-color 0.2s ease-in-out;
 }

 .resize-handle:hover {
 background-color: rgba(255, 255, 255, 0.3);
 }
 </style>
</div>