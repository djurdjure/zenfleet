<div>
    {{-- Sélecteur de Période --}}
    <div class="mb-4 flex items-center justify-end">
        <div class="inline-flex bg-white rounded-lg border border-gray-200 p-1">
            <button 
                wire:click="updatePeriod('today')"
                class="px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200 {{ $period === 'today' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                Aujourd'hui
            </button>
            <button 
                wire:click="updatePeriod('week')"
                class="px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200 {{ $period === 'week' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                Cette semaine
            </button>
            <button 
                wire:click="updatePeriod('month')"
                class="px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200 {{ $period === 'month' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                Ce mois
            </button>
            <button 
                wire:click="updatePeriod('quarter')"
                class="px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200 {{ $period === 'quarter' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                Trimestre
            </button>
            <button 
                wire:click="updatePeriod('year')"
                class="px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200 {{ $period === 'year' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                Cette année
            </button>
        </div>
    </div>

    {{-- Cards Métriques --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" wire:loading.class="opacity-50">
        {{-- Total Opérations --}}
        <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Total opérations</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">
                        {{ $analytics['total_operations'] ?? 0 }}
                    </p>
                </div>
                <div class="w-10 h-10 bg-blue-100 border border-blue-200 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:wrench" class="w-5 h-5 text-blue-600" />
                </div>
            </div>
        </div>

        {{-- Planifiées --}}
        <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Planifiées</p>
                    <p class="text-xl font-bold text-blue-600 mt-1">
                        {{ $analytics['planned_operations'] ?? 0 }}
                    </p>
                </div>
                <div class="w-10 h-10 bg-blue-100 border border-blue-200 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:calendar-clock" class="w-5 h-5 text-blue-600" />
                </div>
            </div>
        </div>

        {{-- En Cours --}}
        <div class="bg-orange-50 rounded-lg border border-orange-200 p-4 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">En cours</p>
                    <p class="text-xl font-bold text-orange-600 mt-1">
                        {{ $analytics['in_progress_operations'] ?? 0 }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Véhicules: {{ $analytics['vehicles_in_maintenance'] ?? 0 }}
                    </p>
                </div>
                <div class="w-10 h-10 bg-orange-100 border border-orange-200 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:loader" class="w-5 h-5 text-orange-600" />
                </div>
            </div>
        </div>

        {{-- En Retard --}}
        <div class="bg-red-50 rounded-lg border border-red-200 p-4 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">En retard</p>
                    <p class="text-xl font-bold text-red-600 mt-1">
                        {{ $analytics['overdue_operations'] ?? 0 }}
                    </p>
                    @if(($analytics['overdue_operations'] ?? 0) > 0)
                        <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <x-iconify icon="lucide:alert-triangle" class="w-3 h-3" />
                            Nécessitent attention
                        </p>
                    @endif
                </div>
                <div class="w-10 h-10 bg-red-100 border border-red-200 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600" />
                </div>
            </div>
        </div>

        {{-- Complétées --}}
        <div class="bg-green-50 rounded-lg border border-green-200 p-4 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Complétées</p>
                    <p class="text-xl font-bold text-green-600 mt-1">
                        {{ $analytics['completed_operations'] ?? 0 }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Taux: {{ number_format(($analytics['completed_operations'] ?? 0) / max($analytics['total_operations'] ?? 1, 1) * 100, 1) }}%
                    </p>
                </div>
                <div class="w-10 h-10 bg-green-100 border border-green-200 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:check-circle-2" class="w-5 h-5 text-green-600" />
                </div>
            </div>
        </div>

        {{-- Coût Total --}}
        <div class="bg-purple-50 rounded-lg border border-purple-200 p-4 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Coût total</p>
                    <p class="text-xl font-bold text-purple-600 mt-1">
                        {{ number_format($analytics['total_cost'] ?? 0, 0, ',', ' ') }} DA
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Moyen: {{ number_format($analytics['avg_cost'] ?? 0, 0, ',', ' ') }} DA
                    </p>
                </div>
                <div class="w-10 h-10 bg-purple-100 border border-purple-200 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:banknote" class="w-5 h-5 text-purple-600" />
                </div>
            </div>
        </div>

        {{-- Durée Moyenne --}}
        <div class="bg-indigo-50 rounded-lg border border-indigo-200 p-4 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Durée moyenne</p>
                    <p class="text-xl font-bold text-indigo-600 mt-1">
                        {{ number_format($analytics['avg_duration_minutes'] ?? 0, 0) }} min
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Total: {{ number_format($analytics['total_duration_hours'] ?? 0, 1) }}h
                    </p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 border border-indigo-200 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:clock" class="w-5 h-5 text-indigo-600" />
                </div>
            </div>
        </div>

        {{-- Annulées --}}
        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Annulées</p>
                    <p class="text-xl font-bold text-gray-500 mt-1">
                        {{ $analytics['cancelled_operations'] ?? 0 }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Taux: {{ number_format(($analytics['cancelled_operations'] ?? 0) / max($analytics['total_operations'] ?? 1, 1) * 100, 1) }}%
                    </p>
                </div>
                <div class="w-10 h-10 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center">
                    <x-iconify icon="lucide:x-circle" class="w-5 h-5 text-gray-500" />
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading class="fixed top-4 right-4 bg-white rounded-lg shadow-lg px-4 py-2 flex items-center gap-2 border border-gray-200">
        <x-iconify icon="lucide:loader" class="w-4 h-4 text-blue-600 animate-spin" />
        <span class="text-sm text-gray-700">Actualisation...</span>
    </div>
</div>
