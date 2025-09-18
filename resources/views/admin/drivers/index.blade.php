@extends('layouts.admin.catalyst')
@section('title', 'Gestion des Chauffeurs - ZenFleet Enterprise')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 -m-6 p-6">
    <div x-data="{
            showConfirmModal: false,
            modalAction: '',
            modalTitle: '',
            modalDescription: '',
            modalButtonText: '',
            modalButtonClass: '',
            modalIconClass: '',
            driverToProcess: {},
            formUrl: '',
            viewMode: 'grid',

            openModal(event, action) {
                const button = event.currentTarget;
                this.driverToProcess = JSON.parse(button.dataset.driver);
                this.formUrl = button.dataset.url;
                this.modalAction = action;

                if (action === 'archive') {
                    this.modalTitle = 'Archiver le Chauffeur';
                    this.modalDescription = `Êtes-vous sûr de vouloir archiver le chauffeur <strong>${this.driverToProcess.first_name} ${this.driverToProcess.last_name}</strong> ? Il pourra être restauré plus tard.`;
                    this.modalButtonText = 'Confirmer l\'Archivage';
                    this.modalButtonClass = 'bg-amber-600 hover:bg-amber-700';
                    this.modalIconClass = 'text-amber-600 bg-amber-100';
                } else if (action === 'delete') {
                    this.modalTitle = 'Suppression Définitive';
                    this.modalDescription = `Cette action est irréversible et supprimera définitivement le chauffeur <strong>${this.driverToProcess.first_name} ${this.driverToProcess.last_name}</strong>. Confirmez-vous cette action ?`;
                    this.modalButtonText = 'Supprimer Définitivement';
                    this.modalButtonClass = 'bg-red-600 hover:bg-red-700';
                    this.modalIconClass = 'text-red-600 bg-red-100';
                }
                this.showConfirmModal = true;
            }
        }" class="space-y-8">

        <!-- 🎨 Enterprise Header Section -->
        <div class="max-w-7xl mx-auto">
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm text-gray-600 mb-6">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    <span class="font-semibold text-gray-900">Gestion des Chauffeurs</span>
                </nav>

                <!-- Hero Content -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900">Gestion des Chauffeurs</h1>
                            <p class="text-gray-600 text-lg mt-2">
                                @if(auth()->user()->hasRole('Super Admin'))
                                    Vue globale de tous les chauffeurs de la plateforme
                                @else
                                    Chauffeurs de {{ auth()->user()->organization->name ?? 'votre organisation' }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="flex gap-4">
                        <div class="bg-green-50 rounded-xl p-4 text-center min-w-[100px]">
                            <div class="text-2xl font-bold text-green-700">{{ $drivers->where('driverStatus.name', 'Disponible')->count() }}</div>
                            <div class="text-sm text-green-600">Disponibles</div>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4 text-center min-w-[100px]">
                            <div class="text-2xl font-bold text-blue-700">{{ $drivers->where('driverStatus.name', 'En mission')->count() }}</div>
                            <div class="text-sm text-blue-600">En mission</div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center min-w-[100px]">
                            <div class="text-2xl font-bold text-gray-700">{{ $drivers->total() }}</div>
                            <div class="text-sm text-gray-600">Total</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🔍 Advanced Filters Section -->
        <div class="max-w-7xl mx-auto">
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6">
                <form action="{{ route('admin.drivers.index') }}" method="GET" class="space-y-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-3">
                            <i class="fas fa-filter text-blue-600"></i>
                            Filtres Avancés
                        </h3>
                        <div class="flex items-center gap-3">
                            <!-- View Mode Toggle -->
                            <div class="bg-gray-100 rounded-lg p-1 flex">
                                <button type="button" @click="viewMode = 'grid'"
                                        :class="viewMode === 'grid' ? 'bg-white shadow-sm' : ''"
                                        class="px-3 py-2 rounded-md text-sm font-medium transition-all">
                                    <i class="fas fa-th-large"></i> Cartes
                                </button>
                                <button type="button" @click="viewMode = 'table'"
                                        :class="viewMode === 'table' ? 'bg-white shadow-sm' : ''"
                                        class="px-3 py-2 rounded-md text-sm font-medium transition-all">
                                    <i class="fas fa-table"></i> Tableau
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                        <!-- Search -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-search text-gray-400 mr-2"></i>Recherche globale
                            </label>
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                                   placeholder="Nom, matricule, organisation..."
                                   class="w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user-check text-gray-400 mr-2"></i>Statut
                            </label>
                            <select name="status_id" class="w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all appearance-none">
                                <option value="">Tous les statuts</option>
                                @foreach($driverStatuses as $status)
                                    <option value="{{ $status->id }}" {{ ($filters['status_id'] ?? '') == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Per Page -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-list-ol text-gray-400 mr-2"></i>Affichage
                            </label>
                            <select name="per_page" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all appearance-none">
                                @foreach(['15', '30', '50', '100'] as $value)
                                    <option value="{{ $value }}" {{ ($filters['per_page'] ?? '15') == $value ? 'selected' : '' }}>{{ $value }} par page</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- View Deleted -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-eye text-gray-400 mr-2"></i>Vue
                            </label>
                            <select name="view_deleted" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all appearance-none">
                                <option value="">Chauffeurs actifs</option>
                                <option value="true" {{ request('view_deleted') ? 'selected' : '' }}>Chauffeurs archivés</option>
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-search mr-2"></i>Filtrer
                            </button>
                            <a href="{{ route('admin.drivers.index') }}" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- 🎬 Action Bar -->
        <div class="max-w-7xl mx-auto">
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="text-sm text-gray-600">
                            <strong>{{ $drivers->total() }}</strong> chauffeur(s) trouvé(s)
                        </div>
                    </div>

                    @can('create drivers')
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.drivers.import.show') }}"
                           class="inline-flex items-center gap-3 px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-sm">
                            <i class="fas fa-file-import text-emerald-600"></i>
                            <span>Importer</span>
                        </a>
                        <a href="{{ route('admin.drivers.create') }}"
                           class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-plus-circle"></i>
                            <span>Nouveau Chauffeur</span>
                        </a>
                    </div>
                    @endcan
                </div>
            </div>
        </div>

        <!-- 📊 Data Display Section -->
        <div class="max-w-7xl mx-auto">
            <!-- Grid View -->
            <div x-show="viewMode === 'grid'" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse ($drivers as $driver)
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300 group">
                        <!-- Driver Photo & Status -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    @if($driver->photo_path)
                                        <img class="w-16 h-16 rounded-full object-cover ring-4 ring-white shadow-lg"
                                             src="{{ asset('storage/' . $driver->photo_path) }}"
                                             alt="Photo de {{ $driver->first_name }}">
                                    @else
                                        <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center shadow-lg">
                                            <i class="fas fa-user text-gray-400 text-xl"></i>
                                        </div>
                                    @endif

                                    <!-- Status Indicator -->
                                    @php
                                        $statusColor = match($driver->driverStatus?->name ?? 'Indéfini') {
                                            'Disponible' => 'bg-green-500',
                                            'En mission' => 'bg-blue-500',
                                            'En congé' => 'bg-indigo-500',
                                            'Suspendu' => 'bg-amber-500',
                                            'Inactif', 'Ex-employé' => 'bg-red-500',
                                            default => 'bg-gray-500'
                                        };
                                    @endphp
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 {{ $statusColor }} rounded-full border-2 border-white"></div>
                                </div>
                            </div>

                            @if($driver->trashed())
                                <div class="px-2 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-medium">
                                    <i class="fas fa-archive mr-1"></i>Archivé
                                </div>
                            @endif
                        </div>

                        <!-- Driver Info -->
                        <div class="space-y-3">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $driver->last_name }} {{ $driver->first_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $driver->employee_number ?? 'Pas de matricule' }}</p>
                            </div>

                            <!-- Status Badge -->
                            @php
                                $statusName = $driver->driverStatus?->name ?? 'Indéfini';
                                $statusClass = match($statusName) {
                                    'Disponible' => 'bg-green-50 text-green-700 border-green-200',
                                    'En mission' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'En congé' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'Suspendu' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'Inactif', 'Ex-employé' => 'bg-red-50 text-red-700 border-red-200',
                                    default => 'bg-gray-50 text-gray-700 border-gray-200'
                                };
                            @endphp
                            <div class="flex items-center justify-between">
                                <span class="px-3 py-1 {{ $statusClass }} border rounded-full text-xs font-medium">
                                    {{ $statusName }}
                                </span>
                            </div>

                            <!-- Organization Info (for Super Admin) -->
                            @if(auth()->user()->hasRole('Super Admin') && $driver->organization)
                                <div class="pt-2 border-t border-gray-100">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <i class="fas fa-building text-gray-400"></i>
                                        <span>{{ $driver->organization->name }}</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Contact Info -->
                            <div class="space-y-2 text-sm text-gray-600">
                                @if($driver->personal_phone)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-phone text-gray-400 w-4"></i>
                                        <span>{{ $driver->personal_phone }}</span>
                                    </div>
                                @endif
                                @if($driver->user?->email)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-user-circle text-gray-400 w-4"></i>
                                        <span class="truncate">{{ $driver->user->email }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-center gap-2 mt-6 pt-4 border-t border-gray-100">
                            @if ($driver->trashed())
                                @can('restore drivers')
                                    <form method="POST" action="{{ route('admin.drivers.restore', $driver->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Restaurer"
                                                class="p-2 rounded-xl text-gray-400 hover:bg-green-50 hover:text-green-600 transition-all">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                @endcan
                                @can('force delete drivers')
                                    <button type="button" @click="openModal($event, 'delete')"
                                            data-driver='@json($driver->only(['id', 'first_name', 'last_name']))'
                                            data-url="{{ route('admin.drivers.force-delete', $driver->id) }}"
                                            title="Supprimer Définitivement"
                                            class="p-2 rounded-xl text-gray-400 hover:bg-red-50 hover:text-red-600 transition-all">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endcan
                            @else
                                @can('edit drivers')
                                    <a href="{{ route('admin.drivers.edit', $driver) }}" title="Modifier"
                                       class="p-2 rounded-xl text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-all">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan
                                @can('delete drivers')
                                    <button type="button" @click="openModal($event, 'archive')"
                                            data-driver='@json($driver->only(['id', 'first_name', 'last_name']))'
                                            data-url="{{ route('admin.drivers.destroy', $driver->id) }}"
                                            title="Archiver"
                                            class="p-2 rounded-xl text-gray-400 hover:bg-amber-50 hover:text-amber-600 transition-all">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                @endcan
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-users text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun chauffeur trouvé</h3>
                            <p class="text-gray-600 mb-6">Aucun chauffeur ne correspond aux critères de recherche actuels.</p>
                            @can('create drivers')
                                <a href="{{ route('admin.drivers.create') }}"
                                   class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Ajouter le premier chauffeur</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Table View -->
            <div x-show="viewMode === 'table'" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Chauffeur</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Matricule</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Statut</th>
                                @if(auth()->user()->hasRole('Super Admin'))
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Organisation</th>
                                @endif
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Utilisateur Lié</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($drivers as $driver)
                                <tr class="hover:bg-blue-50/50 transition-all duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-4">
                                            <div class="relative">
                                                @if($driver->photo_path)
                                                    <img class="w-12 h-12 rounded-full object-cover ring-2 ring-white shadow-sm"
                                                         src="{{ asset('storage/' . $driver->photo_path) }}"
                                                         alt="Photo de {{ $driver->first_name }}">
                                                @else
                                                    <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center shadow-sm">
                                                        <i class="fas fa-user text-gray-400"></i>
                                                    </div>
                                                @endif

                                                @php
                                                    $statusColor = match($driver->driverStatus?->name ?? 'Indéfini') {
                                                        'Disponible' => 'bg-green-500',
                                                        'En mission' => 'bg-blue-500',
                                                        'En congé' => 'bg-indigo-500',
                                                        'Suspendu' => 'bg-amber-500',
                                                        'Inactif', 'Ex-employé' => 'bg-red-500',
                                                        default => 'bg-gray-500'
                                                    };
                                                @endphp
                                                <div class="absolute -bottom-1 -right-1 w-4 h-4 {{ $statusColor }} rounded-full border-2 border-white"></div>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $driver->last_name }} {{ $driver->first_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $driver->personal_phone ?? 'Pas de téléphone' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $driver->employee_number ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $statusName = $driver->driverStatus?->name ?? 'Indéfini';
                                            if ($driver->trashed()) {
                                                $statusName = 'Archivé';
                                                $statusClass = 'bg-gray-100 text-gray-600 border-gray-200';
                                            } else {
                                                $statusClass = match($statusName) {
                                                    'Disponible' => 'bg-green-50 text-green-700 border-green-200',
                                                    'En mission' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    'En congé' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                    'Suspendu' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                    'Inactif', 'Ex-employé' => 'bg-red-50 text-red-700 border-red-200',
                                                    default => 'bg-gray-50 text-gray-700 border-gray-200'
                                                };
                                            }
                                        @endphp
                                        <span class="px-3 py-1 {{ $statusClass }} border rounded-full text-xs font-medium">{{ $statusName }}</span>
                                    </td>
                                    @if(auth()->user()->hasRole('Super Admin'))
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            @if($driver->organization)
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-building text-gray-400"></i>
                                                    <span>{{ $driver->organization->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic">Non assigné</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $driver->user?->email ?? 'Non lié' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($driver->trashed())
                                                @can('restore drivers')
                                                    <form method="POST" action="{{ route('admin.drivers.restore', $driver->id) }}">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" title="Restaurer"
                                                                class="p-2 rounded-xl text-gray-400 hover:bg-green-50 hover:text-green-600 transition-all">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @can('force delete drivers')
                                                    <button type="button" @click="openModal($event, 'delete')"
                                                            data-driver='@json($driver->only(['id', 'first_name', 'last_name']))'
                                                            data-url="{{ route('admin.drivers.force-delete', $driver->id) }}"
                                                            title="Supprimer Définitivement"
                                                            class="p-2 rounded-xl text-gray-400 hover:bg-red-50 hover:text-red-600 transition-all">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @endcan
                                            @else
                                                @can('edit drivers')
                                                    <a href="{{ route('admin.drivers.edit', $driver) }}" title="Modifier"
                                                       class="p-2 rounded-xl text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-all">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete drivers')
                                                    <button type="button" @click="openModal($event, 'archive')"
                                                            data-driver='@json($driver->only(['id', 'first_name', 'last_name']))'
                                                            data-url="{{ route('admin.drivers.destroy', $driver->id) }}"
                                                            title="Archiver"
                                                            class="p-2 rounded-xl text-gray-400 hover:bg-amber-50 hover:text-amber-600 transition-all">
                                                        <i class="fas fa-archive"></i>
                                                    </button>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->hasRole('Super Admin') ? '6' : '5' }}" class="px-6 py-12 text-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-users text-gray-400 text-xl"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun chauffeur trouvé</h3>
                                        <p class="text-gray-600">Aucun chauffeur ne correspond aux critères de recherche actuels.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 📄 Pagination -->
        @if($drivers->hasPages())
            <div class="max-w-7xl mx-auto">
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6">
                    {{ $drivers->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        @endif

        <!-- 🎭 Confirmation Modal -->
        <div x-show="showConfirmModal" x-transition
             class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
             style="display: none;">
            <div @click.away="showConfirmModal = false"
                 class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md mx-4 transform transition-all">
                <div class="flex items-start gap-6">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center" :class="modalIconClass">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3" x-text="modalTitle"></h3>
                        <p class="text-gray-600 mb-6" x-html="modalDescription"></p>

                        <div class="flex items-center gap-3">
                            <form :action="formUrl" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full px-6 py-3 rounded-xl font-semibold text-white transition-all duration-200 shadow-sm hover:shadow-md"
                                        :class="modalButtonClass"
                                        x-text="modalButtonText">
                                </button>
                            </form>
                            <button type="button" @click="showConfirmModal = false"
                                    class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection