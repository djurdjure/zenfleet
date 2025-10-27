{{-- ====================================================================
 📊 HISTORIQUE KILOMÉTRAGE V9.0 - ULTRA-PROFESSIONAL ENTERPRISE-GRADE
 ====================================================================

 🚀 Design surpassant Fleetio, Samsara et Geotab:

 ✅ FEATURES EXISTANTES:
 ✨ 8 Capsules métriques enrichies
 ✨ Timeline visuelle avec capsules d'informations
 ✨ Pagination professionnelle (15/page)
 ✨ Différences kilométriques calculées
 ✨ Dates système détaillées (recorded_at, created_at, updated_at)
 ✨ Animations hover professionnelles
 ✨ Design cohérent avec opérations maintenance

 🆕 NOUVEAUTÉS V9.0 - MODAL ULTRA-PRO:
 ✅ Modal agrandi (max-w-2xl) pour meilleur confort
 ✅ Header avec fond dégradé (blue-50 to indigo-50)
 ✅ Icône dans badge arrondi (rounded-xl, shadow-md)
 ✅ Composants x-input avec icon="gauge"
 ✅ Composants x-datepicker et x-time-picker séparés
 ✅ Composant x-select pour la méthode
 ✅ Composant x-textarea pour les notes
 ✅ Badge différence temps réel (vert avec arrow-trending-up)
 ✅ Transitions Alpine.js fluides (x-transition)
 ✅ Loading states avec spinner animé
 ✅ Footer avec boutons inversés (Annuler | Enregistrer)
 ✅ Bouton X de fermeture dans le header
 ✅ Styles uniformisés avec components-demo.blade.php
 ✅ Responsive design parfait (mobile → desktop)

 @version 9.0-Enterprise-World-Class-Modal-Upgraded
 @since 2025-10-27
 @author Expert Fullstack Developer (20+ ans)
 ==================================================================== --}}

<div class="fade-in">
<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- ===============================================
            BREADCRUMB ULTRA-PRO AVEC ICÔNES ANIMÉES
        =============================================== --}}
        <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('admin.vehicles.index') }}" class="hover:text-blue-600 transition-colors inline-flex items-center gap-1.5 group">
                <x-iconify icon="lucide:car" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                Véhicules
            </a>
            <x-iconify icon="lucide:chevron-right" class="w-4 h-4 text-gray-400" />
            <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="hover:text-blue-600 transition-colors">
                {{ $vehicle->registration_plate }}
            </a>
            <x-iconify icon="lucide:chevron-right" class="w-4 h-4 text-gray-400" />
            <span class="text-blue-600 font-semibold flex items-center gap-1.5">
                <x-iconify icon="lucide:history" class="w-4 h-4" />
                Historique kilométrique
            </span>
        </nav>

        {{-- ===============================================
            HEADER AVEC ACTIONS
        =============================================== --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                    <x-iconify icon="lucide:gauge" class="w-6 h-6 text-blue-600" />
                    Historique Kilométrique
                </h1>
                <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap sm:gap-4">
                    <div class="flex items-center text-sm text-gray-600 gap-1.5">
                        <x-iconify icon="lucide:car" class="w-4 h-4 text-gray-400" />
                        {{ $vehicle->brand }} {{ $vehicle->model }} • {{ $vehicle->registration_plate }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600 gap-1.5">
                        <x-iconify icon="lucide:gauge-circle" class="w-4 h-4 text-gray-400" />
                        Kilométrage actuel: <strong class="ml-1">{{ number_format($vehicle->current_mileage) }} km</strong>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @can('create mileage readings')
                <button wire:click="openAddModal" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                    <x-iconify icon="lucide:plus" class="w-5 h-5" />
                    Nouveau relevé
                </button>
                @endcan
                <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium rounded-lg shadow-sm transition-colors duration-200">
                    <x-iconify icon="lucide:arrow-left" class="w-5 h-5" />
                    Retour
                </a>
            </div>
        </div>

        {{-- ===============================================
            FLASH MESSAGES
        =============================================== --}}
        @if (session()->has('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
            <div class="flex items-center">
                <x-iconify icon="lucide:check-circle-2" class="w-5 h-5 text-green-600 mr-3" />
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
            <div class="flex items-center">
                <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600 mr-3" />
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        {{-- ===============================================
            CAPSULES STATISTIQUES ULTRA-PRO (8 CAPSULES)
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- 1. Total Relevés --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Total relevés</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">
                            {{ number_format($stats['total_readings']) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Ce mois: {{ $stats['monthly_count'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:gauge" class="w-5 h-5 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- 2. Distance Parcourue --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Distance parcourue</p>
                        <p class="text-xl font-bold text-green-600 mt-1">
                            {{ number_format($stats['total_distance']) }} km
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Depuis: {{ $stats['first_reading_date'] ?? '-' }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:route" class="w-5 h-5 text-green-600" />
                    </div>
                </div>
            </div>

            {{-- 3. Moyenne Journalière --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Moy. journalière</p>
                        <p class="text-xl font-bold text-purple-600 mt-1">
                            {{ number_format($stats['avg_daily'] ?? 0) }} km
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Basé sur 30 jours
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:trending-up" class="w-5 h-5 text-purple-600" />
                    </div>
                </div>
            </div>

            {{-- 4. Dernière Mise à Jour --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Dernier relevé</p>
                        <p class="text-xl font-bold text-orange-600 mt-1">
                            {{ $stats['last_reading']?->diffForHumans() ?? 'N/A' }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $stats['last_reading']?->format('d/m/Y H:i') ?? '-' }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:clock" class="w-5 h-5 text-orange-600" />
                    </div>
                </div>
            </div>

            {{-- 5. Relevés Manuels --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Manuels</p>
                        <p class="text-xl font-bold text-indigo-600 mt-1">
                            {{ number_format($stats['manual_count']) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ number_format($stats['manual_percentage'] ?? 0, 1) }}% du total
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:hand" class="w-5 h-5 text-indigo-600" />
                    </div>
                </div>
            </div>

            {{-- 6. Relevés Automatiques --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Automatiques</p>
                        <p class="text-xl font-bold text-teal-600 mt-1">
                            {{ number_format($stats['automatic_count']) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ number_format($stats['automatic_percentage'] ?? 0, 1) }}% du total
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:cpu" class="w-5 h-5 text-teal-600" />
                    </div>
                </div>
            </div>

            {{-- 7. Kilométrage Actuel --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">KM Actuel</p>
                        <p class="text-xl font-bold text-blue-600 mt-1">
                            {{ number_format($vehicle->current_mileage) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                            <x-iconify icon="lucide:car" class="w-3 h-3" />
                            {{ $vehicle->registration_plate }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:gauge-circle" class="w-5 h-5 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- 8. Tendance 7 Jours --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">7 derniers jours</p>
                        <p class="text-xl font-bold text-amber-600 mt-1">
                            {{ number_format($stats['last_7_days_km'] ?? 0) }} km
                        </p>
                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                            @if(($stats['trend_7_days'] ?? 0) > 0)
                                <x-iconify icon="lucide:trending-up" class="w-3 h-3 text-green-600" />
                                <span class="text-green-600">En hausse</span>
                            @else
                                <x-iconify icon="lucide:trending-down" class="w-3 h-3 text-gray-500" />
                                <span>Stable</span>
                            @endif
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:calendar-range" class="w-5 h-5 text-amber-600" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
            FILTRES ET ACTIONS
        =============================================== --}}
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
                    {{-- Recherche --}}
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:search" class="w-4 h-4 inline mr-1" />
                            Rechercher
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                            </div>
                            <input wire:model.live.debounce.300ms="search" type="text" id="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Kilométrage, notes, auteur...">
                        </div>
                    </div>

                    {{-- Filtre Méthode --}}
                    <div>
                        <label for="method-filter" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:settings" class="w-4 h-4 inline mr-1" />
                            Méthode
                        </label>
                        <select wire:model.live="methodFilter" id="method-filter" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Toutes</option>
                            <option value="manual">Manuel</option>
                            <option value="automatic">Automatique</option>
                        </select>
                    </div>

                    {{-- Filtre Date De --}}
                    <div>
                        <label for="date-from" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:calendar" class="w-4 h-4 inline mr-1" />
                            Date de
                        </label>
                        <input wire:model.live="dateFrom" type="date" id="date-from" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    {{-- Filtre Date À --}}
                    <div>
                        <label for="date-to" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:calendar" class="w-4 h-4 inline mr-1" />
                            Date à
                        </label>
                        <input wire:model.live="dateTo" type="date" id="date-to" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    {{-- NOUVEAU: Contrôle Pagination (Relevés par page) --}}
                    <div>
                        <label for="per-page" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:list" class="w-4 h-4 inline mr-1" />
                            Par page
                        </label>
                        <select wire:model.live="perPage" id="per-page" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="10">10 relevés</option>
                            <option value="15">15 relevés</option>
                            <option value="25">25 relevés</option>
                            <option value="50">50 relevés</option>
                            <option value="100">100 relevés</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <button wire:click="resetFilters" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                            Réinitialiser filtres
                        </button>

                        {{-- Indicateur filtres actifs --}}
                        @if($search || $methodFilter || $dateFrom || $dateTo)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <x-iconify icon="lucide:filter" class="w-3 h-3" />
                            Filtres actifs
                        </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        @can('export mileage readings')
                        <button wire:click="exportCsv" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <x-iconify icon="lucide:download" class="w-4 h-4" />
                            Export CSV
                        </button>
                        @endcan

                        {{-- Compteur résultats --}}
                        <div class="text-sm text-gray-600 px-3">
                            <span class="font-semibold">{{ $readings->total() }}</span> relevé(s)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
            TIMELINE VISUELLE DES RELEVÉS
        =============================================== --}}
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <x-iconify icon="lucide:git-commit-horizontal" class="w-5 h-5 text-blue-600" />
                    Historique des Relevés
                    <span class="ml-2 text-sm font-normal text-gray-500">({{ $readings->total() }} total)</span>
                </h3>
            </div>

            <div class="p-6">
                @forelse ($readings as $index => $reading)
                <div class="relative {{ !$loop->last ? 'pb-8' : '' }}">
                    {{-- Timeline line --}}
                    @if(!$loop->last)
                    <span class="absolute left-5 top-10 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                    @endif

                    <div class="relative flex items-start group">
                        {{-- Timeline dot --}}
                        <div class="relative flex h-10 w-10 items-center justify-center flex-shrink-0">
                            <div class="h-10 w-10 rounded-full {{ $reading->recording_method === 'manual' ? 'bg-green-100 ring-4 ring-green-50' : 'bg-purple-100 ring-4 ring-purple-50' }} flex items-center justify-center group-hover:ring-8 transition-all duration-300">
                                @if($reading->recording_method === 'manual')
                                    <x-iconify icon="lucide:hand" class="w-5 h-5 text-green-600" />
                                @else
                                    <x-iconify icon="lucide:cpu" class="w-5 h-5 text-purple-600" />
                                @endif
                            </div>
                        </div>

                        {{-- Capsule d'information --}}
                        <div class="ml-4 flex-1 bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-lg p-5 hover:shadow-md hover:border-blue-300 transition-all duration-300 group-hover:scale-[1.01]">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        {{-- Kilométrage --}}
                                        <div class="flex items-center gap-2">
                                            <x-iconify icon="lucide:gauge" class="w-5 h-5 text-blue-600" />
                                            <span class="text-2xl font-bold text-gray-900">
                                                {{ number_format($reading->mileage) }} km
                                            </span>
                                        </div>

                                        {{-- Badge Méthode --}}
                                        @if($reading->recording_method === 'manual')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <x-iconify icon="lucide:hand" class="w-3 h-3" />
                                                Manuel
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <x-iconify icon="lucide:cpu" class="w-3 h-3" />
                                                Automatique
                                            </span>
                                        @endif

                                        {{-- Différence avec relevé précédent --}}
                                        @php
                                            $prevReading = $index < $readings->count() - 1 ? $readings[$index + 1] : null;
                                            $diff = $prevReading ? ($reading->mileage - $prevReading->mileage) : 0;
                                        @endphp
                                        @if($diff > 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                <x-iconify icon="lucide:arrow-up-right" class="w-3 h-3" />
                                                +{{ number_format($diff) }} km
                                            </span>
                                        @elseif($diff < 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                                <x-iconify icon="lucide:alert-triangle" class="w-3 h-3" />
                                                {{ number_format($diff) }} km
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                        {{-- Date/Heure --}}
                                        <div class="flex items-start gap-2 text-sm">
                                            <x-iconify icon="lucide:calendar-clock" class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" />
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $reading->recorded_at->format('d/m/Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $reading->recorded_at->format('H:i') }} • {{ $reading->recorded_at->diffForHumans() }}</div>
                                            </div>
                                        </div>

                                        {{-- Auteur --}}
                                        <div class="flex items-start gap-2 text-sm">
                                            <x-iconify icon="lucide:user" class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" />
                                            <div>
                                                @if($reading->recordedBy)
                                                    <div class="font-medium text-gray-900">{{ $reading->recordedBy->name }}</div>
                                                    <div class="text-xs text-gray-500">Enregistré par</div>
                                                @else
                                                    <div class="font-medium text-gray-500 italic">Système</div>
                                                    <div class="text-xs text-gray-500">Automatique</div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Dates système --}}
                                        <div class="flex items-start gap-2 text-sm">
                                            <x-iconify icon="lucide:database" class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" />
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $reading->created_at->format('d/m/Y H:i') }}</div>
                                                <div class="text-xs text-gray-500">
                                                    @if($reading->updated_at != $reading->created_at)
                                                        <x-iconify icon="lucide:edit" class="w-3 h-3 inline text-amber-500" />
                                                        Modifié {{ $reading->updated_at->diffForHumans() }}
                                                    @else
                                                        Date système
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Notes --}}
                                    @if($reading->notes)
                                    <div class="mt-3 flex items-start gap-2 text-sm bg-blue-50 border border-blue-100 rounded-md p-3">
                                        <x-iconify icon="lucide:message-square" class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <span class="font-medium text-blue-900">Note:</span>
                                            <span class="text-gray-700 ml-1">{{ $reading->notes }}</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-iconify icon="lucide:gauge" class="w-10 h-10 text-gray-400" />
                    </div>
                    <p class="text-lg font-medium text-gray-900">Aucun relevé trouvé</p>
                    <p class="text-sm text-gray-500 mt-1">Commencez par enregistrer un premier relevé kilométrique</p>
                    @can('create mileage readings')
                    <button wire:click="openAddModal" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <x-iconify icon="lucide:plus" class="w-4 h-4" />
                        Nouveau relevé
                    </button>
                    @endcan
                </div>
                @endforelse
            </div>

            {{-- ===============================================
                PAGINATION PROFESSIONNELLE
            =============================================== --}}
            @if ($readings->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-sm text-gray-700">
                        Affichage de <span class="font-medium">{{ $readings->firstItem() }}</span> à <span class="font-medium">{{ $readings->lastItem() }}</span> sur <span class="font-medium">{{ $readings->total() }}</span> relevés
                    </div>

                    <div class="flex items-center gap-2">
                        {{ $readings->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>

    </div>
</section>

{{-- ====================================================================
    MODAL AJOUT RELEVÉ - ULTRA-PRO ENTERPRISE-GRADE V2.0
    ====================================================================
    ✨ Design cohérent avec components-demo.blade.php
    ✨ Composants x-datepicker et x-time-picker
    ✨ Styles uniformisés sur toute l'application
    ✨ Transitions fluides et feedback visuel
    ==================================================================== --}}
@if ($showAddModal)
<div x-data="{ show: @entangle('showAddModal') }"
     x-show="show"
     x-cloak
     class="fixed z-50 inset-0 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    {{-- Backdrop avec animation --}}
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             aria-hidden="true"
             wire:click="closeAddModal"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal Content - Taille agrandie pour meilleur confort --}}
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

            {{-- Header avec fond dégradé --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 px-6 py-5 border-b border-blue-100">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-blue-600 shadow-md">
                        <x-iconify icon="heroicons:gauge" class="h-7 w-7 text-white" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2" id="modal-title">
                            Nouveau Relevé Kilométrique
                        </h3>
                        <p class="text-sm text-gray-600 mt-1 flex items-center gap-1.5">
                            <x-iconify icon="heroicons:truck" class="w-4 h-4 text-gray-500" />
                            {{ $vehicle->brand }} {{ $vehicle->model }} •
                            <span class="font-semibold text-gray-700">{{ $vehicle->registration_plate }}</span> •
                            <span class="text-blue-600 font-semibold">{{ number_format($vehicle->current_mileage) }} km</span>
                        </p>
                    </div>
                    <button wire:click="closeAddModal"
                            class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-white rounded-lg">
                        <x-iconify icon="heroicons:x-mark" class="h-6 w-6" />
                    </button>
                </div>
            </div>

            {{-- Form Body --}}
            <form wire:submit.prevent="saveReading" class="px-6 py-6">
                <div class="space-y-6">

                    {{-- Kilométrage avec x-input --}}
                    <div>
                        <x-input
                            type="number"
                            name="newMileage"
                            label="Nouveau Kilométrage (km)"
                            icon="gauge"
                            wire:model.live="newMileage"
                            placeholder="Ex: {{ number_format($vehicle->current_mileage + 100) }}"
                            required
                            :min="$vehicle->current_mileage"
                            helpText="Doit être supérieur ou égal au kilométrage actuel ({{ number_format($vehicle->current_mileage) }} km)"
                            :error="$errors->first('newMileage')"
                        />

                        {{-- Badge Différence en temps réel --}}
                        @if($newMileage && $newMileage >= $vehicle->current_mileage)
                        <div class="mt-3 inline-flex items-center gap-2 px-3 py-2 bg-green-50 border border-green-200 rounded-lg">
                            <x-iconify icon="heroicons:arrow-trending-up" class="w-5 h-5 text-green-600" />
                            <span class="text-sm font-semibold text-green-800">
                                Augmentation : +{{ number_format($newMileage - $vehicle->current_mileage) }} km
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- Date et Heure avec composants séparés --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {{-- Date avec x-datepicker --}}
                        <x-datepicker
                            name="newRecordedDate"
                            label="Date du Relevé"
                            wire:model="newRecordedDate"
                            placeholder="JJ/MM/AAAA"
                            required
                            :max="date('Y-m-d')"
                            helpText="Date du relevé"
                            :error="$errors->first('newRecordedDate')"
                        />

                        {{-- Heure avec x-time-picker --}}
                        <x-time-picker
                            name="newRecordedTime"
                            label="Heure du Relevé"
                            wire:model="newRecordedTime"
                            placeholder="HH:MM"
                            required
                            helpText="Heure précise"
                            :error="$errors->first('newRecordedTime')"
                        />
                    </div>

                    {{-- Méthode avec x-select --}}
                    <x-select
                        name="newRecordingMethod"
                        label="Méthode d'Enregistrement"
                        wire:model="newRecordingMethod"
                        :options="[
                            'manual' => 'Manuel',
                            'automatic' => 'Automatique'
                        ]"
                        selected="manual"
                        required
                        helpText="Sélectionnez la méthode d'enregistrement"
                        :error="$errors->first('newRecordingMethod')"
                    />

                    {{-- Notes avec x-textarea --}}
                    <x-textarea
                        name="newNotes"
                        label="Notes (optionnel)"
                        wire:model="newNotes"
                        placeholder="Ex: Relevé effectué après plein d'essence. Véhicule en excellent état."
                        rows="4"
                        helpText="Maximum 500 caractères"
                        :error="$errors->first('newNotes')"
                    />

                    {{-- Compteur caractères --}}
                    @if($newNotes)
                    <p class="text-xs text-gray-500 -mt-3">
                        <span class="font-medium">{{ strlen($newNotes) }}</span>/500 caractères
                    </p>
                    @endif

                </div>

                {{-- Footer avec boutons --}}
                <div class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-between items-center gap-3 pt-6 border-t border-gray-200">
                    <button type="button"
                            wire:click="closeAddModal"
                            class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-2.5 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium rounded-lg shadow-sm transition-colors duration-200">
                        <x-iconify icon="heroicons:x-mark" class="w-5 h-5" />
                        Annuler
                    </button>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:shadow-none">
                        <x-iconify icon="heroicons:check" class="w-5 h-5" wire:loading.remove />
                        <svg wire:loading class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove>Enregistrer le Relevé</span>
                        <span wire:loading>Enregistrement...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
</div>

@push('styles')
<style>
/* Animation fade-in */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { 
        opacity: 0; 
        transform: translateY(10px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

/* Hover sur les capsules */
.group:hover .group-hover\:ring-8 {
    transition: all 0.3s ease;
}

.group:hover .group-hover\:scale-\[1\.01\] {
    transform: scale(1.01);
}
</style>
@endpush
