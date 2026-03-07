<div wire:poll.30s="silentRefresh">
    @php
        $stats = $dashboard['stats'];
        $criticalAlerts = $dashboard['criticalAlerts'];
        $maintenanceAlerts = $dashboard['maintenanceAlerts'];
        $budgetAlerts = $dashboard['budgetAlerts'];
        $repairAlerts = $dashboard['repairAlerts'];
        $alertCoverage = [
            ['icon' => 'lucide:file-badge', 'title' => 'Conformité & Documents', 'desc' => 'Assurances, cartes grises, contrôles techniques, permis.'],
            ['icon' => 'lucide:wrench', 'title' => 'Maintenance & Inspections', 'desc' => 'Préventif, overdue, campagnes techniques.'],
            ['icon' => 'lucide:git-branch', 'title' => 'Conflits d\'Affectation', 'desc' => 'Ressources double-assignées, indisponibles.'],
            ['icon' => 'lucide:shield-alert', 'title' => 'Sécurité de Conduite', 'desc' => 'Excès de vitesse, freinages brusques, fatigue.'],
            ['icon' => 'lucide:gas-pump', 'title' => 'Carburant & Coûts', 'desc' => 'Surconsommation, anomalies, dépenses hors seuil.'],
            ['icon' => 'lucide:map-pin', 'title' => 'Géolocalisation & Zones', 'desc' => 'Sorties de zone, immobilisation prolongée.'],
        ];
    @endphp

    <section class="zf-page min-h-screen">
        <div class="py-6 px-4 mx-auto max-w-7xl lg:py-10">
            <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div>
                    <h1 class="text-xl font-bold text-gray-600">Centre d'Alertes</h1>
                    <p class="text-xs text-gray-600">
                        Surveillance proactive de la flotte
                        <span class="ml-2">• {{ $stats['total_alerts'] }} alerte(s)</span>
                        <span class="ml-2 text-gray-500">Dernière mise à jour: {{ $lastUpdate }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Système actif
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                        Critiques: {{ $stats['critical_count'] }}
                    </span>
                </div>
            </div>

            <x-page-analytics-grid columns="6">
                <div class="bg-red-50 rounded-lg border border-red-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Critiques</p>
                            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['critical_count'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 border border-red-300 rounded-full flex items-center justify-center">
                            <x-iconify icon="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-50 rounded-lg border border-indigo-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Maintenance</p>
                            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['maintenance_count'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-indigo-100 border border-indigo-300 rounded-full flex items-center justify-center">
                            <x-iconify icon="heroicons:wrench" class="w-6 h-6 text-indigo-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-emerald-50 rounded-lg border border-emerald-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Budget</p>
                            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['budget_overruns'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-100 border border-emerald-300 rounded-full flex items-center justify-center">
                            <x-iconify icon="lucide:wallet" class="w-6 h-6 text-emerald-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-orange-50 rounded-lg border border-orange-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Réparations</p>
                            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['pending_repairs'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 border border-orange-300 rounded-full flex items-center justify-center">
                            <x-iconify icon="lucide:wrench" class="w-6 h-6 text-orange-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 rounded-lg border border-yellow-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">En retard</p>
                            <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $stats['overdue_maintenance'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 border border-yellow-300 rounded-full flex items-center justify-center">
                            <x-iconify icon="heroicons:clock" class="w-6 h-6 text-yellow-700" />
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total alertes</p>
                            <p class="text-2xl font-bold text-blue-700 mt-1">{{ $stats['total_alerts'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
                            <x-iconify icon="heroicons:bell-alert" class="w-6 h-6 text-blue-700" />
                        </div>
                    </div>
                </div>
            </x-page-analytics-grid>

            <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm mb-6">
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 flex-wrap">
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-600">Type:</label>
                            <select wire:model.live="filterType" class="bg-gray-50 border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]">
                                <option value="">Tous</option>
                                <option value="critical">Critiques</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="budget">Budget</option>
                                <option value="repair">Réparations</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-600">Priorité:</label>
                            <select wire:model.live="filterPriority" class="bg-gray-50 border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]">
                                <option value="">Toutes</option>
                                <option value="critical">Critique</option>
                                <option value="urgent">Urgente</option>
                                <option value="high">Haute</option>
                                <option value="medium">Moyenne</option>
                                <option value="low">Basse</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-600">Tri:</label>
                            <select wire:model.live="sortBy" class="bg-gray-50 border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]">
                                <option value="priority">Criticité</option>
                                <option value="date">Date</option>
                                <option value="type">Type</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-600">Ordre:</label>
                            <select wire:model.live="sortDirection" class="bg-gray-50 border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]">
                                <option value="desc">Descendant</option>
                                <option value="asc">Ascendant</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-600">Groupement:</label>
                            <select wire:model.live="groupBy" class="bg-gray-50 border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]">
                                <option value="priority">Par criticité</option>
                                <option value="type">Par type</option>
                                <option value="none">Sans groupement</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 ml-auto">
                        @if(count($dismissedAlertKeys) > 0)
                            <button wire:click="resetDismissedAlerts" class="inline-flex items-center h-10 px-4 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee] transition-colors">
                                <x-iconify icon="lucide:eye" class="h-4 w-4 mr-2" />
                                Réafficher
                            </button>
                        @endif
                        <button wire:click="refreshData" class="inline-flex items-center h-10 px-4 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee] transition-colors">
                            <x-iconify icon="heroicons:arrow-path" class="h-4 w-4 mr-2" wire:loading.class="animate-spin" wire:target="refreshData,silentRefresh" />
                            Actualiser
                        </button>
                        <a href="{{ route('admin.alerts.export') }}" class="inline-flex items-center h-10 px-4 border border-transparent rounded-lg text-sm font-medium text-white bg-[#0c90ee] hover:bg-[#0b82d6] focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20 transition-colors">
                            <x-iconify icon="heroicons:arrow-down-tray" class="h-4 w-4 mr-2" />
                            Exporter
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm mb-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                    <button wire:click="focusAlertType('critical','critical')" class="text-left rounded-lg border border-red-200 bg-red-50 px-4 py-3 hover:bg-red-100/60 transition-colors">
                        <p class="text-xs font-semibold text-red-700 uppercase tracking-wide">Quick Insight</p>
                        <p class="text-sm font-semibold text-red-800 mt-1">Critiques</p>
                        <p class="text-xs text-red-700 mt-1">{{ $stats['critical_count'] }} à traiter immédiatement</p>
                    </button>
                    <button wire:click="focusAlertType('maintenance','urgent')" class="text-left rounded-lg border border-orange-200 bg-orange-50 px-4 py-3 hover:bg-orange-100/60 transition-colors">
                        <p class="text-xs font-semibold text-orange-700 uppercase tracking-wide">Quick Insight</p>
                        <p class="text-sm font-semibold text-orange-800 mt-1">Maintenance à risque</p>
                        <p class="text-xs text-orange-700 mt-1">{{ $stats['overdue_maintenance'] }} échéance(s) retardées</p>
                    </button>
                    <button wire:click="focusAlertType('repair')" class="text-left rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 hover:bg-blue-100/60 transition-colors">
                        <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Quick Insight</p>
                        <p class="text-sm font-semibold text-blue-800 mt-1">Demandes à arbitrer</p>
                        <p class="text-xs text-blue-700 mt-1">{{ $stats['pending_repairs'] }} en attente de décision</p>
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-6">
                <div class="bg-[#e3e7ee] border-b border-gray-200 p-4 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-slate-700">Centre d'exécution des alertes</h2>
                    <span class="text-xs font-semibold text-slate-600">{{ $actionItems->count() }} alerte(s) actives</span>
                </div>
                @if($actionItems->isEmpty())
                    <div class="p-10 text-center text-gray-500">Aucune alerte active avec les filtres en cours</div>
                @else
                    <div class="space-y-4 p-4">
                        @foreach($groupedActionItems as $group)
                            <div class="rounded-lg border border-gray-200 overflow-hidden">
                                <div class="bg-gray-50 border-b border-gray-200 px-4 py-2.5 flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-700">{{ $group['label'] }}</p>
                                    <span class="text-xs font-semibold text-gray-500">{{ $group['items']->count() }}</span>
                                </div>
                                <div class="divide-y divide-gray-100">
                                    @foreach($group['items'] as $item)
                                        <div class="px-4 py-3 flex flex-col lg:flex-row lg:items-center gap-3">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold uppercase tracking-wide {{ $item['priority'] === 'critical' ? 'bg-red-50 text-red-700 border border-red-200' : ($item['priority'] === 'urgent' ? 'bg-orange-50 text-orange-700 border border-orange-200' : ($item['priority'] === 'high' ? 'bg-yellow-50 text-yellow-700 border border-yellow-200' : 'bg-blue-50 text-blue-700 border border-blue-200')) }}">
                                                        {{ $item['priority'] }}
                                                    </span>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold text-gray-600 bg-gray-50 border border-gray-200">{{ $item['type'] }}</span>
                                                </div>
                                                <p class="text-sm font-semibold text-gray-800 mt-1">{{ $item['title'] }}</p>
                                                <p class="text-xs text-gray-600 mt-0.5">{{ $item['message'] }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $item['meta'] }}</p>
                                            </div>
                                            <div class="flex items-center gap-2 lg:justify-end">
                                                <button wire:click="focusAlertType('{{ $item['type'] }}','{{ $item['priority'] }}')" class="inline-flex items-center h-9 px-3 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                                                    Filtrer
                                                </button>
                                                @if(!empty($item['action_url']))
                                                    <a href="{{ $item['action_url'] }}" class="inline-flex items-center h-9 px-3 rounded-lg border border-transparent bg-[#0c90ee] text-xs font-semibold text-white hover:bg-[#0b82d6] transition-colors">
                                                        {{ $item['action_label'] }}
                                                    </a>
                                                @endif
                                                <button wire:click="dismissAlert('{{ $item['key'] }}')" class="inline-flex items-center h-9 px-3 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                                                    Masquer
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <x-iconify icon="lucide:radar" class="w-5 h-5 text-blue-600" />
                        <h2 class="text-lg font-semibold text-gray-900">Catalogue d'alertes (à activer)</h2>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">Préconfiguration</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($alertCoverage as $item)
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-white border border-gray-300 rounded-full flex items-center justify-center">
                                    <x-iconify icon="{{ $item['icon'] }}" class="w-5 h-5 text-gray-700" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $item['title'] }}</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ $item['desc'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($criticalAlerts->count() > 0)
                <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                    <div class="flex items-center mb-4">
                        <div class="p-2 bg-red-100 border border-red-200 rounded-full mr-3">
                            <x-iconify icon="heroicons:exclamation-triangle" class="h-5 w-5 text-red-600" />
                        </div>
                        <h3 class="text-lg font-bold text-red-900">Alertes Critiques - Action Immédiate Requise</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($criticalAlerts as $alert)
                            <div class="bg-white border border-red-200 rounded-lg p-4 shadow-sm">
                                <h4 class="font-semibold text-red-900">{{ $alert->title }}</h4>
                                <p class="text-red-700 text-sm mt-1">{{ $alert->message }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="bg-indigo-50 border-b border-indigo-200 p-4 flex items-center justify-between">
                        <h3 class="text-base font-bold text-indigo-900">Alertes Maintenance</h3>
                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-semibold border border-indigo-200">{{ $maintenanceAlerts->count() }} alertes</span>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @forelse($maintenanceAlerts as $alert)
                            <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $alert->registration_plate }}</p>
                                        <p class="text-xs text-gray-600">{{ $alert->brand }} {{ $alert->model }} • {{ $alert->maintenance_type }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Échéance: {{ \Carbon\Carbon::parse($alert->next_due_date)->format('d/m/Y') }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $alert->alert_priority === 'overdue' ? 'bg-red-50 text-red-700 border border-red-200' : ($alert->alert_priority === 'urgent' ? 'bg-orange-50 text-orange-700 border border-orange-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200') }}">
                                        {{ ucfirst($alert->alert_priority) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">Aucune alerte de maintenance</div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="bg-emerald-50 border-b border-emerald-200 p-4 flex items-center justify-between">
                        <h3 class="text-base font-bold text-emerald-900">Alertes Budget</h3>
                        <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-semibold border border-emerald-200">{{ $budgetAlerts->count() }} alertes</span>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @forelse($budgetAlerts as $alert)
                            <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <h4 class="font-semibold text-gray-900">{{ $alert->scope_description }}</h4>
                                <div class="text-xs text-gray-600 mt-1">
                                    Dépensé: {{ number_format($alert->spent_amount, 0) }} DA / Budget: {{ number_format($alert->budgeted_amount, 0) }} DA
                                </div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $alert->type === 'budget_overrun' ? 'bg-red-50 text-red-700 border border-red-200' : ($alert->type === 'budget_critical' ? 'bg-orange-50 text-orange-700 border border-orange-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200') }}">
                                        {{ number_format($alert->utilization_percentage, 1) }}%
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">Tous les budgets sont sous contrôle</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mt-6">
                <div class="bg-orange-50 border-b border-orange-200 p-4 flex items-center justify-between">
                    <h3 class="text-base font-bold text-orange-900">Demandes de Réparation en Attente</h3>
                    <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-semibold border border-orange-200">{{ $repairAlerts->count() }} demandes</span>
                </div>
                <div class="overflow-x-auto">
                    @if($repairAlerts->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demande</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Véhicule</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorité</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Délai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($repairAlerts as $repair)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">Demande #{{ $repair->id }}</div>
                                            <div class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($repair->message, 60) }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $repair->vehicle }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $repair->priority === 'urgent' ? 'bg-red-50 text-red-700 border border-red-200' : ($repair->priority === 'high' ? 'bg-orange-50 text-orange-700 border border-orange-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200') }}">{{ ucfirst($repair->priority) }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $repair->status }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $repair->days_pending }} jour(s)</td>
                                        <td class="px-6 py-4 text-sm font-medium">
                                            <a href="{{ \Illuminate\Support\Facades\Route::has('admin.repair-requests.show') ? route('admin.repair-requests.show', $repair->id) : route('admin.repair-requests.index') }}" class="text-indigo-600 hover:text-indigo-900">Voir détails</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-10 text-center text-gray-500">Aucune demande de réparation en attente</div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
