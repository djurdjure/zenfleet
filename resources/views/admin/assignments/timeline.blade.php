<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Planning des Affectations') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Barre d'outils --}}
            <div class="mb-6 bg-white p-4 shadow-sm sm:rounded-lg border border-gray-200">
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between space-y-4 xl:space-y-0">

                    {{-- Section gauche : Titre et statistiques --}}
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-primary-100 rounded-lg">
                                <x-lucide-calendar class="w-6 h-6 text-primary-600"/>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">Planning des Affectations</h3>
                                <p class="text-sm text-gray-500">Vue timeline des véhicules et conducteurs</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-primary-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">En cours</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                    {{ $activeAssignments }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-success-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">Terminées</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800">
                                    {{ $completedAssignments }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-warning-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">À venir</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-warning-100 text-warning-800">
                                    {{ $upcomingAssignments }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Section droite : Contrôles --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">

                        {{-- Recherche --}}
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Rechercher véhicule, conducteur..."
                                   class="pl-10 pr-4 py-2.5 w-64 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                            <x-lucide-search class="absolute left-3 top-3 h-4 w-4 text-gray-400"/>
                        </div>

                        {{-- Filtres --}}
                        <button id="filtersBtn" class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <x-lucide-filter class="w-4 h-4 mr-2"/>
                            Filtres
                        </button>

                        {{-- Navigation temporelle --}}
                        <div class="flex items-center space-x-2 bg-gray-50 rounded-lg p-1">
                            <button id="prevPeriod" class="p-2 hover:bg-white rounded-md transition-colors">
                                <x-lucide-chevron-left class="w-4 h-4 text-gray-600"/>
                            </button>

                            <button id="todayBtn" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-white rounded-md transition-colors">
                                Aujourd'hui
                            </button>

                            <button id="nextPeriod" class="p-2 hover:bg-white rounded-md transition-colors">
                                <x-lucide-chevron-right class="w-4 h-4 text-gray-600"/>
                            </button>
                        </div>

                        {{-- Sélecteur de vue --}}
                        <select id="viewSelector" class="border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 px-3 py-2.5">
                            <option value="month">Vue Mois</option>
                            <option value="week">Vue Semaine</option>
                            <option value="day">Vue Jour</option>
                        </select>

                        {{-- Nouvelle affectation --}}
                        @can('create assignments')
                            <a href="{{ route('admin.assignments.create') }}" class="inline-flex items-center px-4 py-2.5 bg-primary-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors">
                                <x-lucide-plus class="w-4 h-4 mr-2"/>
                                Nouvelle Affectation
                            </a>
                        @endcan

                        {{-- Vue tableau --}}
                        <a href="{{ route('admin.assignments.index') }}" class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <x-lucide-table class="w-4 h-4 mr-2"/>
                            Vue Tableau
                        </a>
                    </div>
                </div>
            </div>

            {{-- Timeline Container --}}
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 overflow-hidden">

                {{-- En-tête de période --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h4 id="currentPeriod" class="text-lg font-semibold text-gray-900"></h4>
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span id="vehicleCount">{{ count($vehicles) }} véhicules</span>
                            <span id="assignmentCount">{{ $totalAssignments }} affectations</span>
                        </div>
                    </div>
                </div>

                {{-- Timeline Grid --}}
                <div class="relative">
                    {{-- En-tête des colonnes temporelles --}}
                    <div id="timelineHeader" class="sticky top-0 z-20 bg-white border-b border-gray-200">
                        <div class="flex">
                            {{-- Colonne véhicules (fixe) --}}
                            <div class="w-80 flex-shrink-0 px-6 py-4 bg-gray-50 border-r border-gray-200">
                                <div class="flex items-center space-x-2">
                                    <x-lucide-car class="w-4 h-4 text-gray-500"/>
                                    <span class="font-medium text-gray-700">Véhicules</span>
                                </div>
                            </div>

                            {{-- Colonnes temporelles (scrollables) --}}
                            <div id="timeColumns" class="flex-1 flex min-w-0">
                                {{-- Généré par JavaScript --}}
                            </div>
                        </div>
                    </div>

                    {{-- Corps de la timeline --}}
                    <div id="timelineBody" class="max-h-[600px] overflow-y-auto">
                        {{-- Lignes de véhicules --}}
                        <div id="vehicleRows">
                            {{-- Généré par JavaScript --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal pour les détails d'affectation (réutilisation de l'existante) --}}
    <div id="assignmentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-start justify-between">
                        <h3 class="text-lg leading-6 font-semibold text-gray-900" id="modal-title">
                            Détails de l'affectation
                        </h3>
                        <button type="button" id="closeModal" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <x-lucide-x class="h-6 w-6"/>
                        </button>
                    </div>
                    <div class="mt-4" id="modalContent">
                        {{-- Contenu dynamique --}}
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse space-x-reverse space-x-3">
                    @can('update assignments')
                        <a id="editAssignmentBtn" href="#" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <x-lucide-edit class="w-4 h-4 mr-2"/>
                            Modifier
                        </a>
                    @endcan
                    <button type="button" id="closeModalBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tooltip pour les affectations --}}
    <div id="assignmentTooltip" class="absolute z-30 hidden bg-gray-900 text-white text-sm rounded-lg shadow-lg px-3 py-2 max-w-xs">
        <div id="tooltipContent"></div>
        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
    </div>

</x-app-layout>
