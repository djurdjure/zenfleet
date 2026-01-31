<div x-data="{ showEventModal: false, selectedEvent: null }">
    
    {{-- Calendar Header --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mb-4">
        <div class="flex items-center justify-between">
            {{-- Navigation --}}
            <div class="flex items-center gap-2">
                <button 
                    wire:click="previousMonth"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <x-iconify icon="lucide:chevron-left" class="w-5 h-5 text-gray-600" />
                </button>
                
                <h2 class="text-lg font-semibold text-gray-900 min-w-[200px] text-center">
                    {{ $startDate->translatedFormat('F Y') }}
                </h2>
                
                <button 
                    wire:click="nextMonth"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <x-iconify icon="lucide:chevron-right" class="w-5 h-5 text-gray-600" />
                </button>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                <button 
                    wire:click="today"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Aujourd'hui
                </button>
                
                <a href="{{ route('admin.maintenance.operations.create') }}"
                   class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                    <x-iconify icon="lucide:plus" class="w-4 h-4" />
                    Nouvelle Maintenance
                </a>
            </div>
        </div>
    </div>

    {{-- Calendar Grid --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        {{-- Days Header --}}
        <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50">
            @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $day)
                <div class="py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        {{-- Calendar Cells --}}
        <div class="grid grid-cols-7 divide-x divide-y divide-gray-200">
            @php
                $startOfMonth = $startDate->copy()->startOfMonth();
                $endOfMonth = $startDate->copy()->endOfMonth();
                $startOfCalendar = $startOfMonth->copy()->startOfWeek();
                $endOfCalendar = $endOfMonth->copy()->endOfWeek();
                $currentDate = $startOfCalendar->copy();
            @endphp

            @while($currentDate <= $endOfCalendar)
                @php
                    $isCurrentMonth = $currentDate->month === $startDate->month;
                    $isToday = $currentDate->isToday();
                    $dayEvents = $events->filter(function($event) use ($currentDate) {
                        return $event['start'] === $currentDate->toDateString();
                    });
                @endphp
                
                <div class="min-h-[120px] p-2 {{ !$isCurrentMonth ? 'bg-gray-50' : 'bg-white' }}">
                    {{-- Day Number --}}
                    <div class="text-right mb-1">
                        <span class="inline-flex items-center justify-center w-7 h-7 text-sm font-medium rounded-full
                            {{ $isToday ? 'bg-blue-600 text-white' : ($isCurrentMonth ? 'text-gray-900' : 'text-gray-400') }}">
                            {{ $currentDate->day }}
                        </span>
                    </div>

                    {{-- Events --}}
                    <div class="space-y-1">
                        @foreach($dayEvents->take(3) as $event)
                            <button 
                                @click="selectedEvent = {{ json_encode($event) }}; showEventModal = true"
                                class="w-full text-left px-2 py-1 rounded text-xs font-medium truncate transition-colors duration-200 hover:opacity-80"
                                style="background-color: {{ $event['backgroundColor'] }}; color: {{ $event['textColor'] }};">
                                <div class="flex items-center gap-1">
                                    <x-iconify icon="lucide:wrench" class="w-3 h-3 flex-shrink-0" />
                                    <span class="truncate">{{ $event['extendedProps']['vehicle'] }}</span>
                                </div>
                            </button>
                        @endforeach

                        @if($dayEvents->count() > 3)
                            <div class="text-xs text-gray-500 px-2">
                                +{{ $dayEvents->count() - 3 }} autres
                            </div>
                        @endif
                    </div>
                </div>

                @php
                    $currentDate->addDay();
                @endphp
            @endwhile
        </div>
    </div>

    {{-- Event Modal --}}
    <div 
        x-show="showEventModal"
        x-cloak
        @click.away="showEventModal = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Backdrop --}}
            <div 
                x-show="showEventModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm z-40">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            {{-- Modal Panel --}}
            <div 
                x-show="showEventModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-50">
                
                <div class="bg-white px-6 pt-5 pb-4">
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Détails de la Maintenance
                        </h3>
                        <button 
                            @click="showEventModal = false"
                            class="text-gray-400 hover:text-gray-500">
                            <x-iconify icon="lucide:x" class="w-5 h-5" />
                        </button>
                    </div>

                    <template x-if="selectedEvent">
                        <div class="space-y-3">
                            {{-- Vehicle --}}
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <x-iconify icon="lucide:car" class="w-5 h-5 text-blue-600" />
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900" x-text="selectedEvent.extendedProps.vehicle"></div>
                                    <div class="text-xs text-gray-500">Véhicule</div>
                                </div>
                            </div>

                            {{-- Type --}}
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                     x-bind:style="'background-color: ' + selectedEvent.backgroundColor + '20'">
                                    <x-iconify icon="lucide:wrench" class="w-5 h-5" 
                                               x-bind:style="'color: ' + selectedEvent.backgroundColor" />
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900" x-text="selectedEvent.extendedProps.type"></div>
                                    <div class="text-xs text-gray-500">Type de maintenance</div>
                                </div>
                            </div>

                            {{-- Date --}}
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <x-iconify icon="lucide:calendar" class="w-5 h-5 text-gray-600" />
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900" x-text="new Date(selectedEvent.start).toLocaleDateString('fr-FR')"></div>
                                    <div class="text-xs text-gray-500">Date planifiée</div>
                                </div>
                            </div>

                            {{-- Provider --}}
                            <div class="flex items-center gap-3" x-show="selectedEvent.extendedProps.provider">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <x-iconify icon="lucide:building" class="w-5 h-5 text-purple-600" />
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900" x-text="selectedEvent.extendedProps.provider"></div>
                                    <div class="text-xs text-gray-500">Fournisseur</div>
                                </div>
                            </div>

                            {{-- Cost --}}
                            <div class="flex items-center gap-3" x-show="selectedEvent.extendedProps.cost">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <x-iconify icon="lucide:banknote" class="w-5 h-5 text-green-600" />
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900" x-text="new Intl.NumberFormat('fr-FR').format(selectedEvent.extendedProps.cost) + ' DA'"></div>
                                    <div class="text-xs text-gray-500">Coût estimé</div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                    <button 
                        @click="showEventModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Fermer
                    </button>
                    <a 
                        :href="'/admin/maintenance/operations/' + selectedEvent?.id"
                        class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Voir Détails
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading class="fixed top-4 right-4 bg-white rounded-lg shadow-lg px-4 py-2 flex items-center gap-2 border border-gray-200">
        <x-iconify icon="lucide:loader" class="w-4 h-4 text-blue-600 animate-spin" />
        <span class="text-sm text-gray-700">Chargement...</span>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
