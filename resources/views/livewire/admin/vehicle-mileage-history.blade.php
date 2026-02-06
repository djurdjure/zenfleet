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
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 hover:shadow-lg transition-shadow duration-300">
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
                        <div class="w-10 h-10 bg-blue-100 border border-blue-200 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:gauge" class="w-5 h-5 text-blue-600" />
                        </div>
                    </div>
                </div>

                {{-- 2. Distance Parcourue --}}
                <div class="bg-green-50 rounded-lg border border-green-200 p-4 hover:shadow-lg transition-shadow duration-300">
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
                        <div class="w-10 h-10 bg-green-100 border border-green-200 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:route" class="w-5 h-5 text-green-600" />
                        </div>
                    </div>
                </div>

                {{-- 3. Moyenne Journalière --}}
                <div class="bg-purple-50 rounded-lg border border-purple-200 p-4 hover:shadow-lg transition-shadow duration-300">
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
                        <div class="w-10 h-10 bg-purple-100 border border-purple-200 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:trending-up" class="w-5 h-5 text-purple-600" />
                        </div>
                    </div>
                </div>

                {{-- 4. Dernière Mise à Jour --}}
                <div class="bg-orange-50 rounded-lg border border-orange-200 p-4 hover:shadow-lg transition-shadow duration-300">
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
                        <div class="w-10 h-10 bg-orange-100 border border-orange-200 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:clock" class="w-5 h-5 text-orange-600" />
                        </div>
                    </div>
                </div>

                {{-- 5. Relevés Manuels --}}
                <div class="bg-indigo-50 rounded-lg border border-indigo-200 p-4 hover:shadow-lg transition-shadow duration-300">
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
                        <div class="w-10 h-10 bg-indigo-100 border border-indigo-200 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:hand" class="w-5 h-5 text-indigo-600" />
                        </div>
                    </div>
                </div>

                {{-- 6. Relevés Automatiques --}}
                <div class="bg-teal-50 rounded-lg border border-teal-200 p-4 hover:shadow-lg transition-shadow duration-300">
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
                        <div class="w-10 h-10 bg-teal-100 border border-teal-200 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:cpu" class="w-5 h-5 text-teal-600" />
                        </div>
                    </div>
                </div>

                {{-- 7. Kilométrage Actuel --}}
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 hover:shadow-lg transition-shadow duration-300">
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
                        <div class="w-10 h-10 bg-blue-100 border border-blue-200 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:gauge-circle" class="w-5 h-5 text-blue-600" />
                        </div>
                    </div>
                </div>

                {{-- 8. Tendance 7 Jours --}}
                <div class="bg-amber-50 rounded-lg border border-amber-200 p-4 hover:shadow-lg transition-shadow duration-300">
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
                        <div class="w-10 h-10 bg-amber-100 border border-amber-200 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:calendar-range" class="w-5 h-5 text-amber-600" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===============================================
            BARRE D'ACTIONS COMPACTE & FILTRES ULTRA-PRO
        =============================================== --}}
            <div class="mb-6" x-data="{ showFilters: false }">
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-3">
                    <div class="flex items-center gap-2">

                        {{-- Recherche Compacte --}}
                        <div class="flex-1 max-w-md">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                                </div>
                                <input
                                    wire:model.live.debounce.500ms="search"
                                    type="text"
                                    placeholder="Rechercher (km, notes, auteur)..."
                                    wire:loading.attr="aria-busy"
                                    wire:target="search"
                                    class="pl-10 pr-4 py-2.5 block w-full bg-white border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <div wire:loading.delay wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <x-iconify icon="lucide:loader-2" class="w-4 h-4 text-blue-500 animate-spin" />
                                </div>
                            </div>
                        </div>

                        {{-- Boutons Actions ICONES UNIQUEMENT --}}
                        <div class="flex items-center gap-1.5 ml-auto">
                            {{-- Bouton Filtrer (Toggle) --}}
                            <button
                                @click="showFilters = !showFilters"
                                type="button"
                                title="Filtres"
                                class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md relative">
                                <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
                                <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" x-bind:class="showFilters ? 'rotate-180' : ''" />
                                @if($methodFilter || $dateFrom || $dateTo || $authorFilter)
                                <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-4 w-4 bg-blue-600 text-white text-[10px] font-bold items-center justify-center">
                                        {{
                                        collect([
                                            $methodFilter ? 1 : 0,
                                            $dateFrom ? 1 : 0,
                                            $dateTo ? 1 : 0,
                                            $authorFilter ? 1 : 0,
                                        ])->sum()
                                    }}
                                    </span>
                                </span>
                                @endif
                            </button>

                            {{-- Bouton Export (Icon-only) --}}
                            @can('mileage-readings.export')
                            <div class="relative" x-data="{ showExportMenu: false }">
                                <button
                                    @click="showExportMenu = !showExportMenu"
                                    @click.outside="showExportMenu = false"
                                    type="button"
                                    title="Export"
                                    class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm hover:shadow-md">
                                    <x-iconify icon="lucide:download" class="w-5 h-5 text-gray-500" />
                                    <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400" />
                                </button>

                                <div x-show="showExportMenu"
                                    x-transition
                                    class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                    <button wire:click="exportCsv"
                                        @click="showExportMenu = false"
                                        class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 rounded-lg">
                                        <x-iconify icon="lucide:file-text" class="w-4 h-4" />
                                        Export CSV
                                    </button>
                                    {{-- Export Excel suppressed as per VehicleMileageHistory implementation --}}
                                </div>
                            </div>
                            @endcan

                            {{-- Bouton Nouveau Relevé (Icon-only) --}}
                            @can('mileage-readings.create')
                            <button wire:click="openAddModal"
                                title="Nouveau relevé"
                                class="inline-flex items-center justify-center w-9 h-9 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm hover:shadow transition-all">
                                <x-iconify icon="lucide:plus" class="w-5 h-5" />
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>

                {{-- ===============================================
                FILTRES COLLAPSIBLES - ULTRA PRO
            =============================================== --}}
                <div x-show="showFilters"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="mt-4 bg-white rounded-lg border border-gray-200 p-4 shadow-sm">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                        {{-- Méthode --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Méthode</label>
                            <x-slim-select
                                wire:model.live="methodFilter"
                                name="methodFilter"
                                placeholder="Toutes"
                                class="text-xs">
                                <option value="" data-placeholder="true">Toutes</option>
                                <option value="manual">Manuel</option>
                                <option value="automatic">Automatique</option>
                            </x-slim-select>
                        </div>

                        {{-- Enregistré par --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Enregistré par</label>
                            <x-slim-select
                                wire:model.live="authorFilter"
                                name="authorFilter"
                                placeholder="Tous"
                                class="text-xs">
                                <option value="" data-placeholder="true">Tous</option>
                                @foreach($authors as $author)
                                <option value="{{ $author->id }}">
                                    {{ $author->name }}
                                </option>
                                @endforeach
                            </x-slim-select>
                        </div>

                        {{-- Date de (Calendrier Popup Style Ultra Pro) --}}
                        <div x-data="{
                        showCalendar: false,
                        selectedDate: @entangle('dateFrom'),
                        displayDate: '',
                        currentMonth: new Date().getMonth(),
                        currentYear: new Date().getFullYear(),
                        days: [],
                        init() {
                            this.parseDate();
                            this.generateCalendar();
                        },
                        parseDate() {
                            if (this.selectedDate) {
                                const parts = this.selectedDate.split('-');
                                if (parts.length === 3) {
                                    this.displayDate = `${parts[2]}/${parts[1]}/${parts[0]}`;
                                    this.currentYear = parseInt(parts[0]);
                                    this.currentMonth = parseInt(parts[1]) - 1;
                                }
                            } else {
                                this.displayDate = '';
                            }
                        },
                        generateCalendar() {
                            this.days = [];
                            const firstDay = new Date(this.currentYear, this.currentMonth, 1);
                            const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
                            const startPadding = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
                            for (let i = 0; i < startPadding; i++) {
                                this.days.push({ day: '', disabled: true });
                            }
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);
                            for (let d = 1; d <= lastDay.getDate(); d++) {
                                const date = new Date(this.currentYear, this.currentMonth, d);
                                this.days.push({
                                    day: d,
                                    disabled: date > today,
                                    isToday: date.getTime() === today.getTime(),
                                    isSelected: this.selectedDate === `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`
                                });
                            }
                        },
                        selectDay(day) {
                            if (day.disabled || !day.day) return;
                            this.selectedDate = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day.day).padStart(2, '0')}`;
                            this.displayDate = `${String(day.day).padStart(2, '0')}/${String(this.currentMonth + 1).padStart(2, '0')}/${this.currentYear}`;
                            this.generateCalendar();
                            this.showCalendar = false;
                        },
                        clearDate() {
                            this.selectedDate = '';
                            this.displayDate = '';
                            this.generateCalendar();
                        },
                        prevMonth() {
                            if (this.currentMonth === 0) {
                                this.currentMonth = 11;
                                this.currentYear--;
                            } else {
                                this.currentMonth--;
                            }
                            this.generateCalendar();
                        },
                        nextMonth() {
                            const today = new Date();
                            const nextMonth = new Date(this.currentYear, this.currentMonth + 1, 1);
                            if (nextMonth <= today) {
                                if (this.currentMonth === 11) {
                                    this.currentMonth = 0;
                                    this.currentYear++;
                                } else {
                                    this.currentMonth++;
                                }
                                this.generateCalendar();
                            }
                        },
                        monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
                    }" x-init="$watch('selectedDate', () => parseDate())">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date de</label>
                            <div class="relative">
                                <input
                                    type="text"
                                    x-model="displayDate"
                                    @click="showCalendar = !showCalendar"
                                    readonly
                                    placeholder="JJ/MM/AAAA"
                                    class="w-full px-3 py-2 pl-9 bg-gray-50 border border-gray-300 text-xs text-gray-900 rounded-lg shadow-sm transition-all cursor-pointer focus:border-blue-500 focus:ring-2 focus:ring-blue-500 hover:border-gray-400">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none">
                                    <x-iconify icon="lucide:calendar" class="w-3.5 h-3.5 text-gray-400" />
                                </div>
                                <div x-show="showCalendar" x-transition @click.away="showCalendar = false" class="absolute z-50 mt-2 bg-white rounded-xl shadow-2xl border border-gray-200 p-4 w-72">
                                    <div class="flex items-center justify-between mb-4">
                                        <button type="button" @click="prevMonth()" class="p-1 hover:bg-gray-100 rounded-lg">
                                            <x-iconify icon="heroicons:chevron-left" class="w-5 h-5 text-gray-600" />
                                        </button>
                                        <span class="font-semibold text-gray-900" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                                        <button type="button" @click="nextMonth()" class="p-1 hover:bg-gray-100 rounded-lg">
                                            <x-iconify icon="heroicons:chevron-right" class="w-5 h-5 text-gray-600" />
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-7 gap-1 mb-2">
                                        <template x-for="day in ['Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di']">
                                            <div class="text-center text-xs font-semibold text-gray-500 py-1" x-text="day"></div>
                                        </template>
                                    </div>
                                    <div class="grid grid-cols-7 gap-1">
                                        <template x-for="(day, index) in days" :key="index">
                                            <button type="button" @click="selectDay(day)" :disabled="day.disabled"
                                                :class="{
                                                'bg-blue-600 text-white': day.isSelected,
                                                'bg-blue-50 text-blue-700 border border-blue-200': day.isToday && !day.isSelected,
                                                'hover:bg-gray-100': !day.disabled && !day.isSelected,
                                                'text-gray-300 cursor-not-allowed': day.disabled,
                                                'text-gray-700': !day.disabled && !day.isSelected
                                            }"
                                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg transition-colors" x-text="day.day">
                                            </button>
                                        </template>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <button type="button" @click="clearDate(); showCalendar = false" class="w-full text-center text-xs text-gray-600 hover:text-gray-900">Effacer</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Date à (Calendrier Popup Style Ultra Pro) --}}
                        <div x-data="{
                        showCalendar: false,
                        selectedDate: @entangle('dateTo'),
                        displayDate: '',
                        currentMonth: new Date().getMonth(),
                        currentYear: new Date().getFullYear(),
                        days: [],
                        init() {
                            this.parseDate();
                            this.generateCalendar();
                        },
                        parseDate() {
                            if (this.selectedDate) {
                                const parts = this.selectedDate.split('-');
                                if (parts.length === 3) {
                                    this.displayDate = `${parts[2]}/${parts[1]}/${parts[0]}`;
                                    this.currentYear = parseInt(parts[0]);
                                    this.currentMonth = parseInt(parts[1]) - 1;
                                }
                            } else {
                                this.displayDate = '';
                            }
                        },
                        generateCalendar() {
                            this.days = [];
                            const firstDay = new Date(this.currentYear, this.currentMonth, 1);
                            const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
                            const startPadding = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
                            for (let i = 0; i < startPadding; i++) {
                                this.days.push({ day: '', disabled: true });
                            }
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);
                            for (let d = 1; d <= lastDay.getDate(); d++) {
                                const date = new Date(this.currentYear, this.currentMonth, d);
                                this.days.push({
                                    day: d,
                                    disabled: date > today,
                                    isToday: date.getTime() === today.getTime(),
                                    isSelected: this.selectedDate === `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`
                                });
                            }
                        },
                        selectDay(day) {
                            if (day.disabled || !day.day) return;
                            this.selectedDate = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day.day).padStart(2, '0')}`;
                            this.displayDate = `${String(day.day).padStart(2, '0')}/${String(this.currentMonth + 1).padStart(2, '0')}/${this.currentYear}`;
                            this.generateCalendar();
                            this.showCalendar = false;
                        },
                        clearDate() {
                            this.selectedDate = '';
                            this.displayDate = '';
                            this.generateCalendar();
                        },
                        prevMonth() {
                            if (this.currentMonth === 0) {
                                this.currentMonth = 11;
                                this.currentYear--;
                            } else {
                                this.currentMonth--;
                            }
                            this.generateCalendar();
                        },
                        nextMonth() {
                            const today = new Date();
                            const nextMonth = new Date(this.currentYear, this.currentMonth + 1, 1);
                            if (nextMonth <= today) {
                                if (this.currentMonth === 11) {
                                    this.currentMonth = 0;
                                    this.currentYear++;
                                } else {
                                    this.currentMonth++;
                                }
                                this.generateCalendar();
                            }
                        },
                        monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
                    }" x-init="$watch('selectedDate', () => parseDate())">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date à</label>
                            <div class="relative">
                                <input
                                    type="text"
                                    x-model="displayDate"
                                    @click="showCalendar = !showCalendar"
                                    readonly
                                    placeholder="JJ/MM/AAAA"
                                    class="w-full px-3 py-2 pl-9 bg-gray-50 border border-gray-300 text-xs text-gray-900 rounded-lg shadow-sm transition-all cursor-pointer focus:border-blue-500 focus:ring-2 focus:ring-blue-500 hover:border-gray-400">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none">
                                    <x-iconify icon="lucide:calendar" class="w-3.5 h-3.5 text-gray-400" />
                                </div>
                                <div x-show="showCalendar" x-transition @click.away="showCalendar = false" class="absolute z-50 mt-2 bg-white rounded-xl shadow-2xl border border-gray-200 p-4 w-72">
                                    <div class="flex items-center justify-between mb-4">
                                        <button type="button" @click="prevMonth()" class="p-1 hover:bg-gray-100 rounded-lg">
                                            <x-iconify icon="heroicons:chevron-left" class="w-5 h-5 text-gray-600" />
                                        </button>
                                        <span class="font-semibold text-gray-900" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                                        <button type="button" @click="nextMonth()" class="p-1 hover:bg-gray-100 rounded-lg">
                                            <x-iconify icon="heroicons:chevron-right" class="w-5 h-5 text-gray-600" />
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-7 gap-1 mb-2">
                                        <template x-for="day in ['Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di']">
                                            <div class="text-center text-xs font-semibold text-gray-500 py-1" x-text="day"></div>
                                        </template>
                                    </div>
                                    <div class="grid grid-cols-7 gap-1">
                                        <template x-for="(day, index) in days" :key="index">
                                            <button type="button" @click="selectDay(day)" :disabled="day.disabled"
                                                :class="{
                                                'bg-blue-600 text-white': day.isSelected,
                                                'bg-blue-50 text-blue-700 border border-blue-200': day.isToday && !day.isSelected,
                                                'hover:bg-gray-100': !day.disabled && !day.isSelected,
                                                'text-gray-300 cursor-not-allowed': day.disabled,
                                                'text-gray-700': !day.disabled && !day.isSelected
                                            }"
                                                class="w-8 h-8 flex items-center justify-center text-sm rounded-lg transition-colors" x-text="day.day">
                                            </button>
                                        </template>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <button type="button" @click="clearDate(); showCalendar = false" class="w-full text-center text-xs text-gray-600 hover:text-gray-900">Effacer</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KM Max --}}
                        <div>
                            <label for="mileage-max" class="block text-xs font-medium text-gray-700 mb-1">
                                KM Max
                            </label>
                            <input
                                wire:model.live.debounce.500ms="mileageMax"
                                type="number"
                                id="mileage-max"
                                placeholder="999999"
                                class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 hover:border-gray-400">
                        </div>

                        {{-- KM Min --}}
                        <div>
                            <label for="mileage-min" class="block text-xs font-medium text-gray-700 mb-1">
                                KM Min
                            </label>
                            <div class="relative">
                                <input
                                    wire:model.live.debounce.500ms="mileageMin"
                                    type="number"
                                    id="mileage-min"
                                    placeholder="0"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 hover:border-gray-400">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4 border-t border-gray-100 pt-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">
                                {{ $readings->total() }} résultats
                            </span>
                        </div>
                        <div>
                            <button
                                wire:click="resetFilters"
                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                <x-iconify icon="lucide:x" class="w-3.5 h-3.5" />
                                Réinitialiser les filtres
                            </button>
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
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                <x-iconify icon="lucide:hand" class="w-3 h-3" />
                                                Manuel
                                            </span>
                                            @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
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
                        @can('mileage-readings.create')
                        <button wire:click="openAddModal" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <x-iconify icon="lucide:plus" class="w-4 h-4" />
                            Nouveau relevé
                        </button>
                        @endcan
                    </div>
                    @endforelse
                </div>

            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                <x-pagination :paginator="$readings" :records-per-page="$perPage" wire:model.live="perPage" />
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
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40"
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
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full relative z-50">

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
                                :error="$errors->first('newMileage')" />

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
                                :error="$errors->first('newRecordedDate')" />

                            {{-- Heure avec x-time-picker --}}
                            <x-time-picker
                                name="newRecordedTime"
                                label="Heure du Relevé"
                                wire:model="newRecordedTime"
                                placeholder="HH:MM"
                                required
                                helpText="Heure précise"
                                :error="$errors->first('newRecordedTime')" />
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
                            :error="$errors->first('newRecordingMethod')" />

                        {{-- Notes avec x-textarea --}}
                        <x-textarea
                            name="newNotes"
                            label="Notes (optionnel)"
                            wire:model="newNotes"
                            placeholder="Ex: Relevé effectué après plein d'essence. Véhicule en excellent état."
                            rows="4"
                            helpText="Maximum 500 caractères"
                            :error="$errors->first('newNotes')" />

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
