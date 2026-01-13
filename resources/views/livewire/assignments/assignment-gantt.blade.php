{{-- 📈 Vue Gantt des Affectations - Enterprise Grade --}}
<div class="space-y-6" x-data="assignmentGantt()" x-init="init()">
    {{-- Messages flash --}}
    @if($message)
    <div class="rounded-md p-4 {{ $messageType === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}"
        x-data="{ show: true }"
        x-show="show"
        x-transition
        @auto-hide-message.window="setTimeout(() => show = false, 3000)">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm font-medium">{{ $message }}</p>
            </div>
            <div class="ml-auto pl-3">
                <button @click="show = false" class="text-current opacity-70 hover:opacity-100">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Barre d'outils --}}
    <div class="bg-white shadow rounded-lg p-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            {{-- Navigation temporelle --}}
            <div class="flex items-center space-x-4">
                <div class="flex rounded-md shadow-sm" role="group">
                    <button wire:click="previousPeriod"
                        class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>

                    <button wire:click="goToToday"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 -ml-px">
                        Aujourd'hui
                    </button>

                    <button wire:click="nextPeriod"
                        class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 -ml-px">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <h2 class="text-lg font-semibold text-gray-900">
                    {{ \Carbon\Carbon::parse($currentDate)->locale('fr_FR')->isoFormat('MMMM YYYY') }}
                </h2>
            </div>

            {{-- Modes et filtres --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                {{-- Mode de vue --}}
                <div class="flex rounded-md shadow-sm" role="group">
                    <button wire:click="$set('viewMode', 'day')"
                        class="relative inline-flex items-center px-3 py-2 rounded-l-md border text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500
 {{ $viewMode === 'day' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
                        Jour
                    </button>
                    <button wire:click="$set('viewMode', 'week')"
                        class="relative inline-flex items-center px-3 py-2 border text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 -ml-px
 {{ $viewMode === 'week' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
                        Semaine
                    </button>
                    <button wire:click="$set('viewMode', 'month')"
                        class="relative inline-flex items-center px-3 py-2 rounded-r-md border text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 -ml-px
 {{ $viewMode === 'month' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
                        Mois
                    </button>
                </div>

                {{-- Regroupement --}}
                <div class="flex rounded-md shadow-sm" role="group">
                    <button wire:click="$set('groupBy', 'vehicle')"
                        class="relative inline-flex items-center px-3 py-2 rounded-l-md border text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500
 {{ $groupBy === 'vehicle' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Véhicules
                    </button>
                    <button wire:click="$set('groupBy', 'driver')"
                        class="relative inline-flex items-center px-3 py-2 rounded-r-md border text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 -ml-px
 {{ $groupBy === 'driver' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Chauffeurs
                    </button>
                </div>
            </div>
        </div>

        {{-- Filtres avancés --}}
        <div class="mt-4 flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label class="sr-only">Filtrer par statut</label>
                <select wire:model.live="statusFilter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Tous les statuts</option>
                    @foreach($statusOptions as $status => $label)
                    <option value="{{ $status }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1">
                <label class="sr-only">Filtrer par ressource</label>
                <select wire:model.live="resourceFilter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">{{ $groupBy === 'vehicle' ? 'Tous les véhicules' : 'Tous les chauffeurs' }}</option>
                    @if($groupBy === 'vehicle')
                    @foreach($vehicleOptions as $vehicle)
                    <option value="{{ $vehicle->id }}">{{ $vehicle->registration_plate ?? ($vehicle->brand . ' ' . $vehicle->model) }}</option>
                    @endforeach
                    @else
                    @foreach($driverOptions as $driver)
                    <option value="{{ $driver->id }}">{{ $driver->first_name }} {{ $driver->last_name }}</option>
                    @endforeach
                    @endif
                </select>
            </div>

            <div class="flex items-center">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="showOnlyActive"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Actives seulement</span>
                </label>
            </div>
        </div>
    </div>

    {{-- Diagramme de Gantt --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="gantt-container" x-ref="ganttContainer">
            {{-- En-tête temporel --}}
            <div class="gantt-header sticky top-0 z-10 bg-gray-50 border-b border-gray-200">
                <div class="flex">
                    {{-- Colonne des ressources --}}
                    <div class="gantt-resource-header w-64 flex-shrink-0 border-r border-gray-200 p-3">
                        <h3 class="text-sm font-medium text-gray-900">
                            {{ $groupBy === 'vehicle' ? 'Véhicules' : 'Chauffeurs' }}
                        </h3>
                    </div>

                    {{-- Échelle temporelle --}}
                    <div class="gantt-timeline-header flex-1 overflow-x-auto">
                        <div class="flex min-w-max">
                            @foreach($timeScale as $timeSlot)
                            <div class="gantt-time-slot flex-shrink-0 border-r border-gray-200 p-2 text-center
 {{ $timeSlot['isToday'] ?? false ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}
 {{ ($timeSlot['isWeekend'] ?? false) && $viewMode !== 'day' ? 'bg-gray-100' : '' }}"
                                style="min-width: {{ $viewMode === 'day' ? '80px' : ($viewMode === 'week' ? '120px' : '40px') }}">
                                <div class="text-xs font-medium">{{ $timeSlot['label'] }}</div>
                                @if($viewMode === 'month' && ($timeSlot['isFirstOfMonth'] ?? false))
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($timeSlot['date'])->format('M') }}
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Corps du Gantt --}}
            <div class="gantt-body">
                @forelse($resources as $resource)
                <div class="gantt-row flex border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                    {{-- Ligne de ressource --}}
                    <div class="gantt-resource-cell w-64 flex-shrink-0 border-r border-gray-200 p-3">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @if($resource['type'] === 'vehicle')
                                <div class="h-8 w-8 rounded bg-blue-100 flex items-center justify-center">
                                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </div>
                                @else
                                <div class="h-8 w-8 rounded bg-green-100 flex items-center justify-center">
                                    <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>
                            <div class="ml-3 min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $resource['label'] }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $resource['sublabel'] }}</p>
                            </div>
                            <div class="ml-2 flex-shrink-0">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium
 {{ $resource['available'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $resource['available'] ? 'Disponible' : 'Occupé' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Timeline des affectations --}}
                    <div class="gantt-timeline-cell flex-1 relative overflow-x-auto"
                        x-data="{ resourceId: {{ $resource['id'] }}, resourceType: '{{ $resource['type'] }}' }">
                        <div class="gantt-timeline relative h-12 min-w-max"
                            @click="handleTimelineClick($event, resourceId, resourceType)">

                            {{-- Grille temporelle --}}
                            <div class="absolute inset-0 flex">
                                @foreach($timeScale as $timeSlot)
                                <div class="gantt-time-slot flex-shrink-0 border-r border-gray-100 h-full
 {{ $timeSlot['isToday'] ?? false ? 'bg-blue-50/30' : '' }}
 {{ ($timeSlot['isWeekend'] ?? false) && $viewMode !== 'day' ? 'bg-gray-100/50' : '' }}"
                                    style="min-width: {{ $viewMode === 'day' ? '80px' : ($viewMode === 'week' ? '120px' : '40px') }}">
                                </div>
                                @endforeach
                            </div>

                            {{-- Barres d'affectation --}}
                            @foreach($ganttData[$resource['id']] ?? [] as $assignment)
                            <div class="gantt-assignment absolute top-1 bottom-1 rounded-md shadow-sm border cursor-pointer transition-all duration-200 hover:shadow-md"
                                style="background-color: {{ $assignment['color'] }};
 border-color: {{ $assignment['borderColor'] }};
 color: {{ $assignment['textColor'] }};
 left: {{ $this->calculateAssignmentPosition($assignment['start']) }}px;
 width: {{ $this->calculateAssignmentWidth($assignment['start'], $assignment['end']) }}px;
 {{ $assignment['isOngoing'] ? 'border-right: 3px dashed rgba(0,0,0,0.3);' : '' }}"
                                x-data="assignmentTooltip({{ json_encode($assignment['tooltip']) }})"
                                x-tooltip="tooltipContent"
                                @click.stop="handleAssignmentClick({{ $assignment['id'] }}, $event)">
                                <div class="px-2 py-1 text-xs font-medium truncate">
                                    {{ $assignment['title'] }}
                                </div>

                                {{-- Indicateur d'affectation en cours --}}
                                @if($assignment['isOngoing'])
                                <div class="absolute -right-1 top-1/2 transform -translate-y-1/2 w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                @endif

                                {{-- Actions rapides --}}
                                @if($assignment['canEdit'])
                                <div class="gantt-assignment-actions absolute top-0 right-0 opacity-0 hover:opacity-100 transition-opacity">
                                    <button class="inline-flex items-center justify-center w-6 h-6 bg-white rounded-full shadow-sm border border-gray-200 text-gray-400 hover:text-gray-600"
                                        @click.stop="editAssignment({{ $assignment['id'] }})"
                                        title="Modifier">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                                @endif
                            </div>
                            @endforeach

                            {{-- Zone de création rapide --}}
                            <div class="gantt-create-zone absolute inset-0 opacity-0 hover:opacity-100 transition-opacity pointer-events-none">
                                <div class="w-full h-full border-2 border-dashed border-blue-300 bg-blue-50/20 rounded flex items-center justify-center">
                                    <span class="text-xs text-blue-600 font-medium">Cliquer pour créer une affectation</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.712-3.714M14 40v-4c0-1.313.253-2.566.712-3.714m0 0A10.003 10.003 0 0124 26c4.21 0 7.863 2.602 9.288 6.286"></path>
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900">Aucune ressource trouvée</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Aucun {{ $groupBy === 'vehicle' ? 'véhicule' : 'chauffeur' }} ne correspond aux critères sélectionnés.
                    </p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Modal de création rapide --}}
    @if($showQuickCreateModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity" @click="closeQuickCreateModal()"></div>

            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Création rapide d'affectation
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Créer une nouvelle affectation pour le créneau sélectionné.
                            </p>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ressource</label>
                                <p class="text-sm text-gray-900">
                                    @php
                                    $selectedResource = collect($resources)->firstWhere('id', $selectedResourceId);
                                    @endphp
                                    {{ $selectedResource['label'] ?? 'Non sélectionnée' }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Créneau</label>
                                <p class="text-sm text-gray-900">
                                    Du {{ $selectedSlotStart ? \Carbon\Carbon::parse($selectedSlotStart)->format('d/m/Y H:i') : '' }}
                                    {{ $selectedSlotEnd ? 'au ' . \Carbon\Carbon::parse($selectedSlotEnd)->format('d/m/Y H:i') : '(durée indéterminée)' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button"
                        wire:click="$dispatch('open-assignment-form', { resourceType: '{{ $selectedResourceType }}', resourceId: {{ $selectedResourceId }}, startDateTime: '{{ $selectedSlotStart }}', endDateTime: '{{ $selectedSlotEnd }}' })"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Ouvrir le formulaire complet
                    </button>
                    <button type="button"
                        wire:click="closeQuickCreateModal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- JavaScript pour interactions --}}
    @push('scripts')
    <script>
        function assignmentGantt() {
            return {
                init() {
                    // Initialisation du composant Gantt
                    this.setupTooltips();
                    this.setupKeyboardNavigation();
                },

                setupTooltips() {
                    // Configuration des tooltips via Alpine.js
                },

                setupKeyboardNavigation() {
                    // Support navigation clavier pour accessibilité
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && @this.showQuickCreateModal) {
                            @this.closeQuickCreateModal();
                        }
                    });
                },

                handleTimelineClick(event, resourceId, resourceType) {
                    // Calcul de la position du clic pour déterminer la date/heure
                    const rect = event.currentTarget.getBoundingClientRect();
                    const clickX = event.clientX - rect.left;

                    // Logique de calcul temporel basée sur la position
                    const slotWidth = @json($viewMode === 'day' ? 80 : ($viewMode === 'week' ? 120 : 40));
                    const slotIndex = Math.floor(clickX / slotWidth);
                    const timeScale = @json($timeScale);

                    if (timeScale[slotIndex]) {
                        const selectedDate = timeScale[slotIndex].date;
                        const endDate = timeScale[slotIndex + 1]?.date || null;

                        @this.openQuickCreate(resourceType, resourceId, selectedDate, endDate);
                    }
                },

                handleAssignmentClick(assignmentId, event) {
                    event.stopPropagation();
                    @this.dispatch('edit-assignment', {
                        id: assignmentId
                    });
                },

                editAssignment(assignmentId) {
                    @this.dispatch('edit-assignment', {
                        id: assignmentId
                    });
                }
            };
        }

        function assignmentTooltip(tooltipData) {
            return {
                tooltipContent: `
  <div class="bg-white text-gray-900 p-3 rounded-lg shadow-xl border border-gray-200 max-w-sm">
 <div class="font-semibold text-sm">${tooltipData.vehicle} → ${tooltipData.driver}</div>
  <div class="text-xs text-gray-500 mt-1">
 <div>Début: ${tooltipData.start}</div>
 <div>Fin: ${tooltipData.end}</div>
 <div>Durée: ${tooltipData.duration}</div>
 </div>
  ${tooltipData.reason ? `<div class="text-xs text-gray-500 mt-2">Motif: ${tooltipData.reason}</div>` : ''}
 <div class="flex justify-between items-center mt-2">
  <span class="text-xs px-2 py-1 rounded bg-gray-100">${tooltipData.status}</span>
 </div>
 </div>
 `
            };
        }
    </script>
    @endpush

    {{-- Styles CSS pour le Gantt --}}
    @push('styles')
    <style>
        .gantt-container {
            min-height: 400px;
        }

        .gantt-timeline {
            background-image:
                linear-gradient(to right, rgba(0, 0, 0, 0.05) 1px, transparent 1px);

            background-size: {
                    {
                    $viewMode ==='day' ? '80px': ($viewMode ==='week' ? '120px' : '40px')
                }
            }

            100%;
        }

        .gantt-assignment {
            min-width: 20px;
            z-index: 10;
        }

        .gantt-assignment:hover {
            z-index: 20;
            transform: translateY(-1px);
        }

        .gantt-assignment-actions {
            z-index: 30;
        }

        .gantt-create-zone {
            z-index: 5;
        }

        .gantt-row:hover .gantt-create-zone {
            pointer-events: auto;
        }

        /* Responsive adaptations */
        @media (max-width: 768px) {

            .gantt-resource-header,
            .gantt-resource-cell {
                width: 200px;
            }

            .gantt-timeline-header,
            .gantt-timeline-cell {
                overflow-x: auto;
            }
        }

        /* Print styles */
        @media print {

            .gantt-assignment-actions,
            .gantt-create-zone {
                display: none !important;
            }
        }
    </style>
    @endpush
</div>