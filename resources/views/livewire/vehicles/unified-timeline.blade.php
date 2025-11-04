<div>
    {{-- Header avec filtres --}}
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Historique du Véhicule</h3>

            {{-- Compteurs --}}
            @php
                $stats = $this->getFilterStats();
            @endphp
            <div class="flex gap-2 text-sm">
                <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-700">
                    Total: {{ count($timelineEvents) }} événements
                </span>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="flex flex-wrap gap-2 mb-4">
            <button
                wire:click="toggleFilter('showDepotEvents')"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                    {{ $showDepotEvents ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                <x-iconify icon="mdi:office-building" class="w-4 h-4 inline mr-1" />
                Dépôts ({{ $stats['depot'] }})
            </button>

            <button
                wire:click="toggleFilter('showDriverEvents')"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                    {{ $showDriverEvents ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                <x-iconify icon="mdi:account" class="w-4 h-4 inline mr-1" />
                Chauffeurs ({{ $stats['driver'] }})
            </button>

            <button
                wire:click="toggleFilter('showMaintenanceEvents')"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                    {{ $showMaintenanceEvents ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                <x-iconify icon="mdi:wrench" class="w-4 h-4 inline mr-1" />
                Maintenances ({{ $stats['maintenance'] }})
            </button>

            @if($stats['expense'] > 0)
                <button
                    wire:click="toggleFilter('showExpenseEvents')"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                        {{ $showExpenseEvents ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    <x-iconify icon="mdi:credit-card" class="w-4 h-4 inline mr-1" />
                    Dépenses ({{ $stats['expense'] }})
                </button>
            @endif
        </div>
    </div>

    {{-- Timeline --}}
    @if(count($timelineEvents) > 0)
        <div class="relative">
            {{-- Ligne verticale --}}
            <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-200"></div>

            {{-- Événements --}}
            <div class="space-y-6">
                @foreach($timelineEvents as $event)
                    @php
                        $iconColorClasses = match($event['color']) {
                            'blue' => 'bg-blue-100 text-blue-600',
                            'green' => 'bg-green-100 text-green-600',
                            'red' => 'bg-red-100 text-red-600',
                            'orange' => 'bg-orange-100 text-orange-600',
                            'purple' => 'bg-purple-100 text-purple-600',
                            'yellow' => 'bg-yellow-100 text-yellow-600',
                            'gray' => 'bg-gray-100 text-gray-600',
                            default => 'bg-gray-100 text-gray-600',
                        };

                        $borderColorClass = match($event['color']) {
                            'blue' => 'border-blue-200',
                            'green' => 'border-green-200',
                            'red' => 'border-red-200',
                            'orange' => 'border-orange-200',
                            'purple' => 'border-purple-200',
                            'yellow' => 'border-yellow-200',
                            'gray' => 'border-gray-200',
                            default => 'border-gray-200',
                        };
                    @endphp

                    <div class="relative flex items-start group">
                        {{-- Icon --}}
                        <div class="relative flex-shrink-0">
                            <div class="w-16 h-16 {{ $iconColorClasses }} rounded-full flex items-center justify-center ring-4 ring-white">
                                @php
                                    $iconName = $event['icon'];
                                @endphp
                                <x-dynamic-component :component="'lucide-' . $iconName" class="w-6 h-6" />
                            </div>
                        </div>

                        {{-- Content card --}}
                        <div class="ml-6 flex-1">
                            <div class="bg-white border-2 {{ $borderColorClass }} rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                {{-- Header --}}
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900">{{ $event['title'] }}</h4>
                                        <p class="text-sm text-gray-600 mt-0.5">{{ $event['description'] }}</p>
                                    </div>

                                    {{-- Date --}}
                                    <div class="text-right ml-4">
                                        <p class="text-xs text-gray-500">{{ $event['date']->format('d/m/Y') }}</p>
                                        <p class="text-xs text-gray-400">{{ $event['date']->format('H:i') }}</p>
                                    </div>
                                </div>

                                {{-- User --}}
                                @if($event['user'])
                                    <div class="flex items-center text-xs text-gray-500 mt-2">
                                        <x-iconify icon="mdi:account" class="w-3 h-3 mr-1" />
                                        Par {{ $event['user']->name ?? 'Utilisateur inconnu' }}
                                    </div>
                                @endif

                                {{-- Notes --}}
                                @if($event['notes'])
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <p class="text-sm text-gray-700">
                                            <x-iconify icon="mdi:message-text" class="w-4 h-4 inline mr-1 text-gray-400" />
                                            {{ $event['notes'] }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Badge type --}}
                                <div class="mt-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $event['type'] === 'depot' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $event['type'] === 'driver' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $event['type'] === 'maintenance' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $event['type'] === 'expense' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    ">
                                        @if($event['type'] === 'depot')
                                            <x-iconify icon="mdi:office-building" class="w-3 h-3 mr-1" />
                                            Dépôt
                                        @elseif($event['type'] === 'driver')
                                            <x-iconify icon="mdi:account" class="w-3 h-3 mr-1" />
                                            Chauffeur
                                        @elseif($event['type'] === 'maintenance')
                                            <x-iconify icon="mdi:wrench" class="w-3 h-3 mr-1" />
                                            Maintenance
                                        @elseif($event['type'] === 'expense')
                                            <x-iconify icon="mdi:credit-card" class="w-3 h-3 mr-1" />
                                            Dépense
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Bouton Load More --}}
            @if(count($timelineEvents) >= $limit)
                <div class="mt-6 text-center">
                    <button
                        wire:click="loadMore"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                    >
                        <x-iconify icon="mdi:chevron-down" class="w-4 h-4 inline mr-1" />
                        Charger plus
                    </button>
                </div>
            @endif
        </div>
    @else
        {{-- État vide --}}
        <div class="bg-white rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
            <x-iconify icon="mdi:calendar" class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun événement</h3>
            <p class="text-gray-600">
                @if(!$showDepotEvents && !$showDriverEvents && !$showMaintenanceEvents && !$showExpenseEvents)
                    Activez au moins un filtre pour voir l'historique
                @else
                    Aucun événement correspondant aux filtres sélectionnés
                @endif
            </p>
        </div>
    @endif
</div>
