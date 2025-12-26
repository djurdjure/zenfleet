<div>
    {{-- Header avec titre et bouton d'ajout --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                {{-- Titre --}}
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <x-iconify icon="heroicons:exclamation-triangle" class="w-8 h-8 text-red-600" />
                        Sanctions Chauffeurs
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Gestion des sanctions disciplinaires appliquées aux chauffeurs
                    </p>
                </div>

                {{-- Bouton Ajouter --}}
                @can('create', App\Models\DriverSanction::class)
                <button
                    wire:click="create"
                    type="button"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <x-iconify icon="heroicons:plus-circle" class="w-5 h-5 mr-2" />
                    Ajouter une sanction
                </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        {{-- Messages flash --}}
        @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
            <div class="flex">
                <x-iconify icon="heroicons:check-circle" class="w-5 h-5 text-green-400 mt-0.5" />
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
            <div class="flex">
                <x-iconify icon="heroicons:exclamation-circle" class="w-5 h-5 text-red-400 mt-0.5" />
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Barre de recherche et filtres --}}
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            {{-- Header & Actions --}}
            <div class="sm:flex sm:items-center sm:justify-between py-2">
                <div>
                    <div class="relative rounded-md shadow-sm max-w-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <x-iconify icon="heroicons:magnifying-glass" class="h-4 w-4 text-gray-400" />
                        </div>
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="text"
                            class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                            placeholder="Rechercher...">
                    </div>
                </div>

                <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none flex items-center gap-2">
                    <button
                        type="button"
                        wire:click="toggleFilters"
                        class="inline-flex items-center gap-x-1.5 rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                        title="Filtres">
                        <x-iconify icon="heroicons:funnel" class="-ml-0.5 h-5 w-5 text-gray-400" />
                    </button>

                    @can('create', \App\Models\DriverSanction::class)
                    <button
                        type="button"
                        wire:click="openCreateModal"
                        class="block rounded-md bg-blue-600 px-2.5 py-1.5 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                        title="Nouvelle Sanction">
                        <x-iconify icon="heroicons:plus" class="h-5 w-5" />
                    </button>
                    @endcan
                </div>
            </div>

            {{-- Filters --}}
            <div
                x-show="$wire.showFilters"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="bg-gray-50 rounded-lg p-3 mb-4 border border-gray-200 shadow-inner mt-2">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    {{-- Type --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                        <select wire:model.live="filterSanctionType" class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-xs sm:leading-6">
                            <option value="">Tous</option>
                            @foreach($this->sanctionTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Chauffeur --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Chauffeur</label>
                        <select wire:model.live="filterDriverId" class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-xs sm:leading-6">
                            <option value="">Tous</option>
                            @foreach($this->drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Début --}}
                    <div x-data="{
                        showCalendar: false,
                        selectedDate: @entangle('filterDateFrom'),
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
                        <label class="block text-xs font-medium text-gray-700 mb-1">Du</label>
                        <div class="relative">
                            <input
                                type="text"
                                x-model="displayDate"
                                @click="showCalendar = !showCalendar"
                                readonly
                                placeholder="JJ/MM/AAAA"
                                class="w-full px-4 py-2.5 pl-11 bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg shadow-sm transition-all cursor-pointer focus:border-blue-500 focus:ring-1 focus:ring-blue-500 hover:border-gray-400">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                <x-iconify icon="heroicons:calendar-days" class="w-5 h-5 text-gray-400" />
                            </div>

                            {{-- Calendrier Popup --}}
                            <div x-show="showCalendar"
                                x-transition
                                @click.away="showCalendar = false"
                                class="absolute z-50 mt-2 bg-white rounded-xl shadow-2xl border border-gray-200 p-4 w-72">
                                {{-- Header --}}
                                <div class="flex items-center justify-between mb-4">
                                    <button type="button" @click="prevMonth()" class="p-1 hover:bg-gray-100 rounded-lg">
                                        <x-iconify icon="heroicons:chevron-left" class="w-5 h-5 text-gray-600" />
                                    </button>
                                    <span class="font-semibold text-gray-900" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                                    <button type="button" @click="nextMonth()" class="p-1 hover:bg-gray-100 rounded-lg">
                                        <x-iconify icon="heroicons:chevron-right" class="w-5 h-5 text-gray-600" />
                                    </button>
                                </div>
                                {{-- Days of week --}}
                                <div class="grid grid-cols-7 gap-1 mb-2">
                                    <template x-for="day in ['Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di']">
                                        <div class="text-center text-xs font-semibold text-gray-500 py-1" x-text="day"></div>
                                    </template>
                                </div>
                                {{-- Calendar days --}}
                                <div class="grid grid-cols-7 gap-1">
                                    <template x-for="(day, index) in days" :key="index">
                                        <button type="button"
                                            @click="selectDay(day)"
                                            :disabled="day.disabled"
                                            :class="{
                                                'bg-blue-600 text-white': day.isSelected,
                                                'bg-blue-100 text-blue-800': day.isToday && !day.isSelected,
                                                'hover:bg-gray-100': !day.disabled && !day.isSelected,
                                                'text-gray-300 cursor-not-allowed': day.disabled,
                                                'text-gray-700': !day.disabled && !day.isSelected
                                            }"
                                            class="w-8 h-8 flex items-center justify-center text-sm rounded-lg transition-colors"
                                            x-text="day.day">
                                        </button>
                                    </template>
                                </div>
                                {{-- Clear button --}}
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <button type="button" @click="clearDate(); showCalendar = false" class="w-full text-center text-xs text-gray-600 hover:text-gray-900">
                                        Effacer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Date Fin --}}
                    <div x-data="{
                        showCalendar: false,
                        selectedDate: @entangle('filterDateTo'),
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
                        <label class="block text-xs font-medium text-gray-700 mb-1">Au</label>
                        <div class="relative">
                            <input
                                type="text"
                                x-model="displayDate"
                                @click="showCalendar = !showCalendar"
                                readonly
                                placeholder="JJ/MM/AAAA"
                                class="w-full px-4 py-2.5 pl-11 bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg shadow-sm transition-all cursor-pointer focus:border-blue-500 focus:ring-1 focus:ring-blue-500 hover:border-gray-400">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                <x-iconify icon="heroicons:calendar-days" class="w-5 h-5 text-gray-400" />
                            </div>

                            {{-- Calendrier Popup --}}
                            <div x-show="showCalendar"
                                x-transition
                                @click.away="showCalendar = false"
                                class="absolute z-50 mt-2 bg-white rounded-xl shadow-2xl border border-gray-200 p-4 w-72">
                                {{-- Header --}}
                                <div class="flex items-center justify-between mb-4">
                                    <button type="button" @click="prevMonth()" class="p-1 hover:bg-gray-100 rounded-lg">
                                        <x-iconify icon="heroicons:chevron-left" class="w-5 h-5 text-gray-600" />
                                    </button>
                                    <span class="font-semibold text-gray-900" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                                    <button type="button" @click="nextMonth()" class="p-1 hover:bg-gray-100 rounded-lg">
                                        <x-iconify icon="heroicons:chevron-right" class="w-5 h-5 text-gray-600" />
                                    </button>
                                </div>
                                {{-- Days of week --}}
                                <div class="grid grid-cols-7 gap-1 mb-2">
                                    <template x-for="day in ['Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di']">
                                        <div class="text-center text-xs font-semibold text-gray-500 py-1" x-text="day"></div>
                                    </template>
                                </div>
                                {{-- Calendar days --}}
                                <div class="grid grid-cols-7 gap-1">
                                    <template x-for="(day, index) in days" :key="index">
                                        <button type="button"
                                            @click="selectDay(day)"
                                            :disabled="day.disabled"
                                            :class="{
                                                'bg-blue-600 text-white': day.isSelected,
                                                'bg-blue-100 text-blue-800': day.isToday && !day.isSelected,
                                                'hover:bg-gray-100': !day.disabled && !day.isSelected,
                                                'text-gray-300 cursor-not-allowed': day.disabled,
                                                'text-gray-700': !day.disabled && !day.isSelected
                                            }"
                                            class="w-8 h-8 flex items-center justify-center text-sm rounded-lg transition-colors"
                                            x-text="day.day">
                                        </button>
                                    </template>
                                </div>
                                {{-- Clear button --}}
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <button type="button" @click="clearDate(); showCalendar = false" class="w-full text-center text-xs text-gray-600 hover:text-gray-900">
                                        Effacer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Statut --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Statut</label>
                        <select wire:model.live="filterArchived" class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-xs sm:leading-6">
                            <option value="active">Actifs</option>
                            <option value="archived">Archivés</option>
                            <option value="all">Tous</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3 flex justify-end">
                    <button
                        wire:click="resetFilters"
                        type="button"
                        class="rounded bg-white px-2 py-1 text-xs font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Réinitialiser
                    </button>
                </div>
            </div>

            {{-- Content --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('id')">
                                    Réf
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('driver_id')">
                                    Chauffeur
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('sanction_type')">
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Motif
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('sanction_date')">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('supervisor_id')">
                                    Superviseur
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($this->sanctions as $sanction)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $sanction->reference ?? '#' . $sanction->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs">
                                            {{ substr($sanction->driver->first_name, 0, 1) }}{{ substr($sanction->driver->last_name, 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $sanction->driver->full_name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $sanction->driver->employee_number }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sanction->getSanctionTypeColor() }}">
                                        <x-iconify icon="{{ $sanction->getSanctionTypeIcon() }}" class="w-3 h-3 mr-1" />
                                        {{ $sanction->getSanctionTypeLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $sanction->reason }}">
                                        {{ $sanction->reason }}
                                    </div>
                                    @if($sanction->attachment_path)
                                    <a href="{{ $sanction->getAttachmentUrl() }}" target="_blank" class="inline-flex items-center mt-1 text-xs text-blue-600 hover:text-blue-800">
                                        <x-iconify icon="heroicons:paper-clip" class="w-3 h-3 mr-1" />
                                        Pièce jointe
                                    </a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $sanction->sanction_date->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Il y a {{ $sanction->getDaysSinceSanction() }} jours
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $sanction->supervisor->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($sanction->isArchived())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <x-iconify icon="heroicons:archive-box" class="w-3 h-3 mr-1" />
                                        Archivée
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <x-iconify icon="heroicons:check-circle" class="w-3 h-3 mr-1" />
                                        Active
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Voir --}}
                                        @can('view', $sanction)
                                        <a href="{{ route('admin.sanctions.show', $sanction->id) }}"
                                            class="text-gray-600 hover:text-gray-900 transition-colors"
                                            title="Voir détails">
                                            <x-iconify icon="heroicons:eye" class="w-4 h-4" />
                                        </a>
                                        @endcan

                                        {{-- Modifier --}}
                                        @can('update', $sanction)
                                        <button
                                            wire:click="edit({{ $sanction->id }})"
                                            class="text-blue-600 hover:text-blue-900 transition-colors"
                                            title="Modifier">
                                            <x-iconify icon="heroicons:pencil-square" class="w-4 h-4" />
                                        </button>
                                        @endcan

                                        {{-- Archiver/Désarchiver --}}
                                        @if($sanction->isArchived())
                                        @can('unarchive', $sanction)
                                        <button
                                            wire:click="unarchive({{ $sanction->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                            title="Désarchiver">
                                            <x-iconify icon="heroicons:archive-box-x-mark" class="w-4 h-4" />
                                        </button>
                                        @endcan
                                        @else
                                        @can('archive', $sanction)
                                        <button
                                            wire:click="archive({{ $sanction->id }})"
                                            class="text-gray-600 hover:text-gray-900 transition-colors"
                                            title="Archiver">
                                            <x-iconify icon="heroicons:archive-box-arrow-down" class="w-4 h-4" />
                                        </button>
                                        @endcan
                                        @endif

                                        {{-- Supprimer --}}
                                        @can('delete', $sanction)
                                        <button
                                            wire:click="confirmDelete({{ $sanction->id }})"
                                            class="text-red-600 hover:text-red-900 transition-colors"
                                            title="Supprimer">
                                            <x-iconify icon="heroicons:trash" class="w-4 h-4" />
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <x-iconify icon="heroicons:inbox" class="w-12 h-12 text-gray-400 mb-4" />
                                        <p class="text-gray-500 text-lg font-medium">Aucune sanction trouvée</p>
                                        <p class="text-gray-400 text-sm mt-2">
                                            @if($search || $filterSanctionType || $filterDriverId || $filterDateFrom || $filterDateTo)
                                            Essayez de modifier vos filtres de recherche
                                            @else
                                            Commencez par ajouter une nouvelle sanction
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($sanctions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $sanctions->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- Modal de création/édition --}}
        @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-start justify-center min-h-screen pt-10 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                {{-- Centrage du modal --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Contenu du modal --}}
                <div class="inline-block align-top bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full max-h-[90vh] overflow-y-auto">
                    <form wire:submit.prevent="save">
                        {{-- Header --}}
                        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 sticky top-0 z-10">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-semibold text-white flex items-center">
                                    <x-iconify icon="heroicons:scale" class="w-6 h-6 mr-3" />
                                    {{ $editMode ? 'Modifier la sanction' : 'Nouvelle sanction' }}
                                </h3>
                                <button type="button" wire:click="closeModal" class="text-white hover:text-gray-200 transition-colors">
                                    <x-iconify icon="heroicons:x-mark" class="w-6 h-6" />
                                </button>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="px-6 py-4 space-y-4">
                            {{-- Chauffeur - SlimSelect avec recherche --}}
                            <div wire:ignore x-data="{
                            instance: null,
                            value: @entangle('driver_id'),
                            init() {
                                this.$nextTick(() => {
                                    this.initSlimSelect();
                                });
                            },
                            initSlimSelect() {
                                if (this.instance) {
                                    this.instance.destroy();
                                }
                                this.instance = new SlimSelect({
                                    select: this.$refs.driverSelect,
                                    settings: {
                                        showSearch: true,
                                        searchPlaceholder: 'Rechercher un chauffeur...',
                                        searchText: 'Aucun résultat',
                                        placeholderText: 'Sélectionner un chauffeur',
                                        allowDeselect: true,
                                    },
                                    events: {
                                        afterChange: (newVal) => {
                                            if (newVal && newVal[0]) {
                                                this.value = newVal[0].value;
                                            } else {
                                                this.value = '';
                                            }
                                        }
                                    }
                                });
                                // Set initial value if exists
                                if (this.value) {
                                    this.instance.setSelected(this.value);
                                }
                            }
                        }">
                                <label class="block mb-2 text-sm font-medium text-gray-900">
                                    Chauffeur <span class="text-red-500">*</span>
                                </label>
                                <select x-ref="driverSelect" class="slimselect-field w-full">
                                    <option value="" data-placeholder="true">Sélectionner un chauffeur</option>
                                    @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->first_name }} {{ $driver->last_name }}</option>
                                    @endforeach
                                </select>
                                @error('driver_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1.5 flex-shrink-0" />
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Type de sanction --}}
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">
                                    Type de sanction <span class="text-red-500">*</span>
                                </label>
                                <select
                                    wire:model="sanction_type"
                                    class="w-full px-4 py-2.5 bg-gray-50 border text-sm text-gray-900 rounded-lg shadow-sm transition-all @error('sanction_type') border-red-500 bg-red-50 ring-1 ring-red-500 @else border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 hover:border-gray-400 @enderror">
                                    <option value="">Sélectionner un type</option>
                                    @foreach($sanctionTypes as $key => $type)
                                    <option value="{{ $key }}">{{ $type['label'] }} (Sévérité: {{ $type['severity'] }})</option>
                                    @endforeach
                                </select>
                                @error('sanction_type')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1.5 flex-shrink-0" />
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Date de sanction --}}
                            <div x-data="{ 
                            showCalendar: false,
                            selectedDate: @entangle('sanction_date'),
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
                                    const today = new Date();
                                    this.selectedDate = today.toISOString().split('T')[0];
                                    this.displayDate = `${String(today.getDate()).padStart(2, '0')}/${String(today.getMonth() + 1).padStart(2, '0')}/${today.getFullYear()}`;
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
                        }">
                                <label class="block mb-2 text-sm font-medium text-gray-900">
                                    Date de sanction <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        x-model="displayDate"
                                        @click="showCalendar = !showCalendar"
                                        readonly
                                        placeholder="JJ/MM/AAAA"
                                        class="w-full px-4 py-2.5 pl-11 bg-gray-50 border text-sm text-gray-900 rounded-lg shadow-sm transition-all cursor-pointer @error('sanction_date') border-red-500 bg-red-50 ring-1 ring-red-500 @else border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 hover:border-gray-400 @enderror">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <x-iconify icon="heroicons:calendar-days" class="w-5 h-5 text-gray-400" />
                                    </div>

                                    {{-- Calendrier Popup --}}
                                    <div x-show="showCalendar"
                                        x-transition
                                        @click.away="showCalendar = false"
                                        class="absolute z-50 mt-2 bg-white rounded-xl shadow-2xl border border-gray-200 p-4 w-72">
                                        {{-- Header --}}
                                        <div class="flex items-center justify-between mb-4">
                                            <button type="button" @click="prevMonth()" class="p-1 hover:bg-gray-100 rounded-lg">
                                                <x-iconify icon="heroicons:chevron-left" class="w-5 h-5 text-gray-600" />
                                            </button>
                                            <span class="font-semibold text-gray-900" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                                            <button type="button" @click="nextMonth()" class="p-1 hover:bg-gray-100 rounded-lg">
                                                <x-iconify icon="heroicons:chevron-right" class="w-5 h-5 text-gray-600" />
                                            </button>
                                        </div>
                                        {{-- Days of week --}}
                                        <div class="grid grid-cols-7 gap-1 mb-2">
                                            <template x-for="day in ['Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di']">
                                                <div class="text-center text-xs font-semibold text-gray-500 py-1" x-text="day"></div>
                                            </template>
                                        </div>
                                        {{-- Calendar days --}}
                                        <div class="grid grid-cols-7 gap-1">
                                            <template x-for="(day, index) in days" :key="index">
                                                <button type="button"
                                                    @click="selectDay(day)"
                                                    :disabled="day.disabled"
                                                    :class="{
                                                        'bg-blue-600 text-white': day.isSelected,
                                                        'bg-blue-100 text-blue-800': day.isToday && !day.isSelected,
                                                        'hover:bg-gray-100': !day.disabled && !day.isSelected,
                                                        'text-gray-300 cursor-not-allowed': day.disabled,
                                                        'text-gray-700': !day.disabled && !day.isSelected
                                                    }"
                                                    class="w-8 h-8 flex items-center justify-center text-sm rounded-lg transition-colors"
                                                    x-text="day.day">
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                @error('sanction_date')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1" />
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Raison --}}
                            <div>
                                <label for="reason" class="block mb-2 text-sm font-medium text-gray-900">
                                    Raison détaillée <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    wire:model="reason"
                                    id="reason"
                                    rows="4"
                                    placeholder="Décrivez en détail les motifs de la sanction..."
                                    class="w-full px-4 py-2.5 bg-gray-50 border text-sm text-gray-900 rounded-lg shadow-sm transition-all resize-none @error('reason') border-red-500 bg-red-50 ring-1 ring-red-500 @else border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 hover:border-gray-400 @enderror"></textarea>
                                @error('reason')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1.5 flex-shrink-0" />
                                    {{ $message }}
                                </p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">Minimum 10 caractères, maximum 5000 caractères</p>
                            </div>

                            {{-- Pièce jointe existante --}}
                            @if($editMode && $existingAttachmentPath)
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <x-iconify icon="heroicons:paper-clip" class="w-4 h-4 text-blue-600 mr-3" />
                                        <div>
                                            <p class="text-sm font-medium text-blue-900">Pièce jointe existante</p>
                                            <a href="{{ Storage::url($existingAttachmentPath) }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800">
                                                Voir le fichier
                                            </a>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="removeExistingAttachment"
                                        class="text-red-600 hover:text-red-800 transition-colors"
                                        title="Supprimer">
                                        <x-iconify icon="heroicons:trash" class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                            @endif

                            {{-- Upload de pièce jointe --}}
                            <div>
                                <label for="attachment" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <x-iconify icon="heroicons:paper-clip" class="w-4 h-4 text-purple-500 mr-2" />
                                        Pièce jointe (optionnelle)
                                    </span>
                                </label>
                                <input
                                    wire:model="attachment"
                                    type="file"
                                    id="attachment"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full px-4 py-3 bg-white border @error('attachment') border-red-500 @else border-gray-200 @enderror rounded-xl focus:border-red-400 focus:ring-4 focus:ring-red-50 transition-all">
                                @error('attachment')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">Formats acceptés: PDF, JPG, JPEG, PNG (max 5 MB)</p>

                                {{-- Indicateur de chargement --}}
                                <div wire:loading wire:target="attachment" class="mt-2 text-sm text-blue-600 flex items-center">
                                    <x-iconify icon="heroicons:arrow-path" class="w-4 h-4 animate-spin mr-2" />
                                    Chargement du fichier...
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-3">
                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled"
                                wire:target="save">
                                <span wire:loading.remove wire:target="save" class="flex items-center">
                                    <x-iconify icon="heroicons:check" class="w-5 h-5 mr-2" />
                                    {{ $editMode ? 'Mettre à jour' : 'Créer la sanction' }}
                                </span>
                                <span wire:loading wire:target="save" class="flex items-center">
                                    <x-iconify icon="heroicons:arrow-path" class="w-5 h-5 animate-spin mr-2" />
                                    Enregistrement...
                                </span>
                            </button>
                            <button
                                type="button"
                                wire:click="closeModal"
                                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                <x-iconify icon="heroicons:x-mark" class="w-5 h-5 mr-2" />
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal de confirmation de suppression --}}
        @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cancelDelete"></div>

                {{-- Centrage du modal --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Contenu du modal --}}
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <x-iconify icon="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Confirmer la suppression
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Êtes-vous sûr de vouloir supprimer cette sanction ? Cette action est irréversible et supprimera également la pièce jointe associée.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 flex flex-col sm:flex-row-reverse gap-3">
                        <button
                            type="button"
                            wire:click="delete"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <x-iconify icon="heroicons:trash" class="w-4 h-4 mr-2" />
                            Supprimer
                        </button>
                        <button
                            type="button"
                            wire:click="cancelDelete"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>