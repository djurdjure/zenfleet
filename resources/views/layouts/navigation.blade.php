<nav x-data="{
 open: true,
 activeSection: '{{ request()->segment(2) }}',
 notifications: {
 alerts: {{ auth()->user()->unreadNotifications->where('type', 'like', '%Alert%')->count() }},
 repairs: {{ auth()->user()->unreadNotifications->where('type', 'like', '%Repair%')->count() }},
 expenses: {{ auth()->user()->unreadNotifications->where('type', 'like', '%Expense%')->count() }},
 maintenance: {{ auth()->user()->unreadNotifications->where('type', 'like', '%Maintenance%')->count() }}
 }
}" class="flex flex-col h-full bg-white border-r border-gray-200 font-inter text-gray-900">

    {{-- Logo Enterprise --}}
    <div class="flex items-center justify-center h-20 border-b border-gray-200 shrink-0 px-6 bg-gray-50/50">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
            <div class="p-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg group-hover:shadow-xl transition-all duration-300">
                <x-iconify icon="heroicons:truck" class="h-8 w-8 text-white" stroke-width="2" / />
            </div>
            <div class="flex flex-col">
                <span class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">ZenFleet</span>
                <span class="text-xs text-gray-500 font-medium">Enterprise Suite</span>
            </div>
        </a>
    </div>

    {{-- Système d'alertes global --}}
    @if(auth()->user()->unreadNotifications->count() > 0)
    <div class="mx-4 mt-4 p-3 bg-gradient-to-r from-red-500/20 to-orange-500/20 border border-red-500/30 rounded-lg">
        <div class="flex items-center space-x-2">
            <x-iconify icon="heroicons:bell" class="h-4 w-4 text-red-400 animate-pulse" / />
            <span class="text-sm font-medium text-red-300">{{ auth()->user()->unreadNotifications->count() }} alertes actives</span>
        </div>
    </div>
    @endif

    {{-- Navigation principale --}}
    <div class="flex-1 px-4 py-6 overflow-y-auto space-y-3 text-sm"
        x-data="{ searchQuery: '' }"
        x-init="
 $watch('searchQuery', value => {
 const items = document.querySelectorAll('.nav-item');
 items.forEach(item => {
 const text = item.textContent.toLowerCase();
 item.style.display = text.includes(value.toLowerCase()) ? 'block' : 'none';
 });
 })
 ">

        {{-- Barre de recherche Enterprise --}}
        <div class="relative mb-6">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <x-iconify icon="heroicons:magnifying-glass" class="h-4 w-4 text-gray-400" />
            </div>
            <input type="text" x-model="searchQuery"
                placeholder="Rechercher dans le menu..."
                class="w-full pl-10 pr-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
        </div>

        {{-- Dashboard Principal --}}
        <a href="{{ route('dashboard') }}"
            class="nav-item flex items-center px-4 py-3 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all duration-300 group {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 border border-blue-100' : '' }}">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'bg-gray-100 group-hover:bg-gray-200 text-gray-500' }} transition-all duration-300">
                <x-iconify icon="heroicons:squares-2x2" class="h-5 w-5" / />
            </div>
            <span class="ml-3 font-medium">Tableau de Bord</span>
            @if(request()->routeIs('dashboard'))
            <div class="ml-auto w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
            @endif
        </a>

        {{-- Section Alertes & Notifications --}}
        <div class="nav-item mt-8 mb-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Alertes & Surveillance</h3>
        </div>

        <a href="{{ route('admin.alerts.index') }}"
            class="nav-item flex items-center px-4 py-3 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-red-50 transition-all duration-300 group {{ request()->routeIs('admin.alerts.*') ? 'bg-red-50 text-red-700 border border-red-100' : '' }}">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('admin.alerts.*') ? 'bg-red-600 text-white' : 'bg-gray-100 group-hover:bg-gray-200 text-gray-500' }} transition-all duration-300 relative">
                <x-iconify icon="heroicons:exclamation-triangle" class="h-5 w-5" / />
                @if(auth()->user()->unreadNotifications->where('type', 'like', '%Alert%')->count() > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                    {{ auth()->user()->unreadNotifications->where('type', 'like', '%Alert%')->count() }}
                </span>
                @endif
            </div>
            <div class="ml-3 flex-1">
                <span class="font-medium">Alertes Système</span>
                <div class="text-xs text-gray-500">Maintenance • Réparations • Budgets</div>
            </div>
        </a>

        {{-- Section Modules Principaux --}}
        <div class="nav-item mt-8 mb-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Modules Enterprise</h3>
        </div>

        {{-- Gestion de la Flotte --}}
        <div class="nav-item" x-data="{ expanded: {{ request()->routeIs('admin.vehicles.*', 'admin.assignments.*', 'admin.planning.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                class="w-full flex items-center px-4 py-3 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all duration-300 group">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 group-hover:bg-gray-200 text-gray-500 transition-all duration-300">
                    <x-iconify icon="heroicons:truck" class="h-5 w-5" / />
                </div>
                <div class="ml-3 flex-1 text-left">
                    <span class="font-medium">Gestion Flotte</span>
                    <div class="text-xs text-gray-500">Véhicules • Affectations</div>
                </div>
                <x-iconify icon="heroicons:chevron-down" class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" / />
            </button>
            <div x-show="expanded" x-collapse class="mt-2 ml-6 space-y-1">
                <a href="{{ route('admin.vehicles.index') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.vehicles.*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="heroicons:truck" class="h-4 w-4 mr-3" / />
                    Véhicules
                </a>
                <a href="{{ route('admin.assignments.index') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.assignments.*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="heroicons:clipboard-document-list" class="h-4 w-4 mr-3" / />
                    Affectations
                </a>
                <a href="{{ route('admin.planning.index') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.planning.*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="heroicons:calendar-days" class="h-4 w-4 mr-3" / />
                    Planning
                </a>
            </div>
        </div>

        {{-- Chauffeurs --}}
        <div class="nav-item" x-data="{ expanded: {{ request()->routeIs('admin.drivers.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                class="w-full flex items-center px-4 py-3 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all duration-300 group">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 group-hover:bg-gray-200 text-gray-500 transition-all duration-300">
                    <x-iconify icon="heroicons:users" class="h-5 w-5" / />
                </div>
                <div class="ml-3 flex-1 text-left">
                    <span class="font-medium">Chauffeurs</span>
                    <div class="text-xs text-gray-500">Personnel • Import/Export</div>
                </div>
                <x-iconify icon="heroicons:chevron-down" class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" / />
            </button>
            <div x-show="expanded" x-collapse class="mt-2 ml-6 space-y-1">
                <a href="{{ route('admin.drivers.index') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.drivers.index') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="heroicons:list-bullet" class="h-4 w-4 mr-3" / />
                    Liste des chauffeurs
                </a>
                <a href="{{ route('admin.drivers.import.show') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.drivers.import.*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="heroicons:document-arrow-up" class="h-4 w-4 mr-3" / />
                    Importer chauffeurs
                </a>
            </div>
        </div>


        {{-- Fournisseurs Enterprise --}}
        <a href="{{ route('admin.suppliers-enterprise.index') }}"
            class="nav-item flex items-center px-4 py-3 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-teal-50 transition-all duration-300 group {{ request()->routeIs('admin.suppliers-enterprise.*') ? 'bg-teal-50 text-teal-700 border border-teal-100' : '' }}">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('admin.suppliers-enterprise.*') ? 'bg-teal-600 text-white' : 'bg-gray-100 group-hover:bg-gray-200 text-gray-500' }} transition-all duration-300">
                <x-iconify icon="heroicons:building-office" class="h-5 w-5" / />
            </div>
            <div class="ml-3 flex-1">
                <span class="font-medium">Fournisseurs</span>
                <div class="text-xs text-gray-500">Conformité DZ • Évaluations</div>
            </div>
            @if(request()->routeIs('admin.suppliers-enterprise.*'))
            <div class="w-2 h-2 bg-teal-400 rounded-full animate-pulse"></div>
            @endif
        </a>

        {{-- Gestion des Dépenses avec sous-menus --}}
        <div class="nav-item" x-data="{ expanded: {{ request()->routeIs('admin.vehicle-expenses.*', 'admin.expense-groups.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                class="w-full flex items-center px-4 py-3 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-emerald-50 transition-all duration-300 group {{ request()->routeIs('admin.vehicle-expenses.*', 'admin.expense-groups.*') ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : '' }}">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('admin.vehicle-expenses.*', 'admin.expense-groups.*') ? 'bg-emerald-600 text-white' : 'bg-gray-100 group-hover:bg-gray-200 text-gray-500' }} transition-all duration-300 relative">
                    <x-iconify icon="tabler:moneybag" class="h-5 w-5" />
                    @if(auth()->user()->unreadNotifications->where('type', 'like', '%Expense%')->count() > 0)
                    <span class="absolute -top-2 -right-2 bg-emerald-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                        {{ auth()->user()->unreadNotifications->where('type', 'like', '%Expense%')->count() }}
                    </span>
                    @endif
                </div>
                <div class="ml-3 flex-1 text-left">
                    <span class="font-medium">Gestion des Dépenses</span>
                    <div class="text-xs text-gray-500">Finances • Budgets • Analytics</div>
                </div>
                <x-iconify icon="heroicons:chevron-down" class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" />
            </button>

            <div x-show="expanded" x-collapse class="mt-2 ml-6 space-y-1">
                {{-- Vue d'ensemble --}}
                <a href="{{ route('admin.vehicle-expenses.index') }}"
                    class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.vehicle-expenses.index') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="carbon:dashboard" class="h-4 w-4 mr-3" />
                    Vue d'ensemble
                </a>

                {{-- Nouvelle dépense --}}
                <a href="{{ route('admin.vehicle-expenses.create') }}"
                    class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.vehicle-expenses.create') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="carbon:add-filled" class="h-4 w-4 mr-3" />
                    Nouvelle dépense
                </a>

                {{-- Analytics --}}
                <a href="{{ route('admin.vehicle-expenses.dashboard') }}"
                    class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.vehicle-expenses.dashboard') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="carbon:analytics" class="h-4 w-4 mr-3" />
                    Analytics & Rapports
                </a>

                {{-- Workflow d'approbation --}}
                @can('approve expenses')
                <a href="{{ route('admin.vehicle-expenses.index') }}?filter=pending_approval"
                    class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200">
                    <x-iconify icon="carbon:task-approved" class="h-4 w-4 mr-3" />
                    En attente d'approbation
                    @php
                    $pendingCount = \App\Models\VehicleExpense::where('organization_id', auth()->user()->organization_id)
                    ->whereIn('approval_status', ['pending_level1', 'pending_level2'])
                    ->count();
                    @endphp
                    @if($pendingCount > 0)
                    <span class="ml-auto bg-yellow-500 text-white text-xs rounded-full px-2 py-0.5">{{ $pendingCount }}</span>
                    @endif
                </a>
                @endcan

                {{-- Groupes de dépenses / Budgets --}}
                <a href="{{ route('admin.vehicle-expenses.index') }}?section=groups"
                    class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200">
                    <x-iconify icon="carbon:wallet" class="h-4 w-4 mr-3" />
                    Budgets & Groupes
                </a>

                {{-- Import/Export --}}
                <a href="{{ route('admin.vehicle-expenses.export') }}"
                    class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200">
                    <x-iconify icon="carbon:document-export" class="h-4 w-4 mr-3" />
                    Import / Export
                </a>

                {{-- Rapports TCO --}}
                @can('view expense analytics')
                <a href="{{ route('admin.vehicle-expenses.analytics.cost-trends') }}"
                    class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200">
                    <x-iconify icon="carbon:chart-line" class="h-4 w-4 mr-3" />
                    TCO & Tendances
                </a>
                @endcan
            </div>
        </div>

        {{-- Maintenance Enterprise avec sous-menus --}}
        <div class="nav-item" x-data="{ expanded: {{ request()->routeIs('admin.maintenance.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                class="w-full flex items-center px-4 py-3 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-indigo-50 transition-all duration-300 group {{ request()->routeIs('admin.maintenance.*') ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : '' }}">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('admin.maintenance.*') ? 'bg-indigo-600 text-white' : 'bg-gray-100 group-hover:bg-gray-200 text-gray-500' }} transition-all duration-300 relative">
                    <x-iconify icon="heroicons:cog-6-tooth" class="h-5 w-5" / />
                    @if(auth()->user()->unreadNotifications->where('type', 'like', '%Maintenance%')->count() > 0)
                    <span class="absolute -top-2 -right-2 bg-indigo-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        {{ auth()->user()->unreadNotifications->where('type', 'like', '%Maintenance%')->count() }}
                    </span>
                    @endif
                </div>
                <div class="ml-3 flex-1 text-left">
                    <span class="font-medium">Maintenance</span>
                    <div class="text-xs text-gray-500">Surveillance • Planification</div>
                </div>
                <x-iconify icon="heroicons:chevron-down" class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" / />
            </button>
            <div x-show="expanded" x-collapse class="mt-2 ml-6 space-y-1">
                <a href="{{ route('admin.maintenance.surveillance.index') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.maintenance.surveillance.*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="heroicons:computer-desktop" class="h-4 w-4 mr-3" / />
                    Surveillance
                </a>
                <a href="{{ route('admin.maintenance.schedules.index') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.maintenance.schedules.*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="heroicons:calendar-check" class="h-4 w-4 mr-3" / />
                    Planifications
                </a>
                <a href="{{ route('admin.repair-requests.index') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.repair-requests.*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="heroicons:wrench" class="h-4 w-4 mr-3" / />
                    Demandes de réparation
                </a>
                <a href="{{ route('admin.maintenance.operations.index') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.maintenance.operations.*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="lucide:wrench" class="h-4 w-4 mr-3" / />
                    Opérations
                </a>
            </div>
        </div>

        {{-- Section Système --}}
        <div class="nav-item mt-8 mb-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Système & Configuration</h3>
        </div>

        {{-- Documents --}}
        <div class="nav-item" x-data="{ expanded: {{ request()->routeIs('admin.documents.*', 'admin.document-categories.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                class="w-full flex items-center px-4 py-3 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all duration-300 group">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 group-hover:bg-gray-200 text-gray-500 transition-all duration-300">
                    <x-iconify icon="heroicons:folder-open" class="h-5 w-5" / />
                </div>
                <div class="ml-3 flex-1 text-left">
                    <span class="font-medium">Documents</span>
                    <div class="text-xs text-gray-500">Fichiers • Catégories</div>
                </div>
                <x-iconify icon="heroicons:chevron-down" class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" / />
            </button>
            <div x-show="expanded" x-collapse class="mt-2 ml-6 space-y-1">
                <a href="{{ route('admin.documents.index') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.documents.*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="heroicons:document-text" class="h-4 w-4 mr-3" / />
                    Liste des Documents
                </a>
                <a href="{{ route('admin.document-categories.index') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.document-categories.*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="heroicons:folder" class="h-4 w-4 mr-3" / />
                    Catégories
                </a>
            </div>
        </div>

        {{-- Administration --}}
        @hasanyrole('Super Admin|Admin')
        <div class="nav-item" x-data="{ expanded: {{ request()->routeIs('admin.organizations.*', 'admin.users.*', 'admin.roles.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                class="w-full flex items-center px-4 py-3 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-rose-50 transition-all duration-300 group {{ request()->routeIs('admin.organizations.*', 'admin.users.*', 'admin.roles.*') ? 'bg-rose-50 text-rose-700 border border-rose-100' : '' }}">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('admin.organizations.*', 'admin.users.*', 'admin.roles.*') ? 'bg-rose-600 text-white' : 'bg-gray-100 group-hover:bg-gray-200 text-gray-500' }} transition-all duration-300">
                    <x-iconify icon="heroicons:shield-check" class="h-5 w-5" / />
                </div>
                <div class="ml-3 flex-1 text-left">
                    <span class="font-medium">Administration</span>
                    <div class="text-xs text-gray-500">Utilisateurs • Permissions</div>
                </div>
                <x-iconify icon="heroicons:chevron-down" class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" / />
            </button>
            <div x-show="expanded" x-collapse class="mt-2 ml-6 space-y-1">
                @role('Super Admin')
                <a href="{{ route('admin.organizations.index') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.organizations.*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="heroicons:building-office" class="h-4 w-4 mr-3" / />
                    Organisations
                </a>
                @endrole
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="heroicons:user-cog" class="h-4 w-4 mr-3" / />
                    Utilisateurs
                </a>
                <a href="{{ route('admin.roles.index') }}" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('admin.roles.*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                    <x-iconify icon="heroicons:key" class="h-4 w-4 mr-3" / />
                    Rôles & Permissions
                </a>
            </div>
        </div>
        @endhasanyrole
    </div>

    {{-- Section Utilisateur Enterprise --}}
    <div class="mt-auto shrink-0 p-4 border-t border-gray-200">
        <div class="relative"
             x-data="{
                open: false,
                styles: '',
                direction: 'up',
                align: 'left',
                toggle() {
                    if (this.open) { this.close(); return; }
                    this.open = true;
                    this.$nextTick(() => {
                        this.updatePosition();
                        requestAnimationFrame(() => this.updatePosition());
                    });
                },
                close() { this.open = false; },
                updatePosition() {
                    if (!this.$refs.trigger || !this.$refs.menu) return;
                    const rect = this.$refs.trigger.getBoundingClientRect();
                    const menuHeight = this.$refs.menu.offsetHeight || 320;
                    const menuWidth = rect.width || this.$refs.menu.offsetWidth || 280;
                    const padding = 12;
                    const spaceBelow = window.innerHeight - rect.bottom;
                    const spaceAbove = rect.top;
                    const shouldOpenUp = spaceBelow < menuHeight && spaceAbove > spaceBelow;
                    this.direction = shouldOpenUp ? 'up' : 'down';
                    let top = shouldOpenUp ? (rect.top - menuHeight - 8) : (rect.bottom + 8);
                    if (top + menuHeight > window.innerHeight - padding) {
                        top = window.innerHeight - padding - menuHeight;
                    }
                    if (top < padding) top = padding;
                    let left = this.align === 'left' ? rect.left : (rect.right - menuWidth);
                    if (left + menuWidth > window.innerWidth - padding) {
                        left = window.innerWidth - padding - menuWidth;
                    }
                    if (left < padding) left = padding;
                    this.styles = `position: fixed; top: ${top}px; left: ${left}px; width: ${menuWidth}px; z-index: 9999;`;
                }
             }"
             x-init="$watch('open', value => {
                if (value) {
                    $nextTick(() => {
                        this.updatePosition();
                        requestAnimationFrame(() => this.updatePosition());
                    });
                }
             })"
             @keydown.escape.window="close()"
             @scroll.window="open && updatePosition()"
             @resize.window="open && updatePosition()">
            <button @click="toggle()" x-ref="trigger" class="w-full flex items-center space-x-3 group p-3 rounded-xl hover:bg-gray-100 transition-all duration-300">
                <div class="relative">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-lg">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                    </div>
                    <div class="absolute -bottom-1 -right-1 h-4 w-4 bg-green-500 rounded-full border-2 border-white"></div>
                </div>
                <div class="flex-1 text-left">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    <div class="flex items-center space-x-2">
                        <p class="text-xs text-gray-500">{{ Auth::user()->roles->first()?->name ?? 'Utilisateur' }}</p>
                        @if(Auth::user()->organization)
                        <span class="text-xs text-gray-400">•</span>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->organization->name }}</p>
                        @endif
                    </div>
                </div>
                <x-iconify icon="heroicons:chevron-up" class="h-4 w-4 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" / />
            </button>

            <template x-teleport="body">
            <div x-show="open"
                x-ref="menu"
                @click.outside="close()"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                :style="styles"
                class="origin-bottom z-[9999]"
                x-cloak>
                <div class="bg-white rounded-xl shadow-2xl ring-1 ring-gray-200 p-2 backdrop-blur-sm border border-gray-100">
                    <div class="space-y-1">
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center w-full px-4 py-3 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-200 group">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-blue-50 transition-all duration-200 mr-3">
                                <x-iconify icon="heroicons:user" class="h-4 w-4 text-gray-500 group-hover:text-blue-600" / />
                            </div>
                            <div class="flex-1">
                                <div class="font-medium">Mon Profil</div>
                                <div class="text-xs text-gray-500">Paramètres du compte</div>
                            </div>
                        </a>

                        <a href="{{ route('admin.notifications.index') }}"
                            class="flex items-center w-full px-4 py-3 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-200 group">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-yellow-50 transition-all duration-200 mr-3 relative">
                                <x-iconify icon="heroicons:bell" class="h-4 w-4 text-gray-500 group-hover:text-yellow-600" / />
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="font-medium">Notifications</div>
                                <div class="text-xs text-gray-500">{{ auth()->user()->unreadNotifications->count() }} non lues</div>
                            </div>
                        </a>

                        <div class="my-2 border-t border-gray-100"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full px-4 py-3 text-sm text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 group">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-red-50 transition-all duration-200 mr-3">
                                    <x-iconify icon="heroicons:arrow-right-on-rectangle" class="h-4 w-4 text-gray-500 group-hover:text-red-500" / />
                                </div>
                                <div class="flex-1 text-left">
                                    <div class="font-medium">Déconnexion</div>
                                    <div class="text-xs text-gray-500">Terminer la session</div>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistiques rapides utilisateur --}}
        <div class="mt-4 p-3 bg-gray-50 rounded-xl border border-gray-200">
            <div class="grid grid-cols-2 gap-3 text-center">
                <div>
                    <div class="text-lg font-bold text-blue-600">{{ auth()->user()->vehicles_count ?? 0 }}</div>
                    <div class="text-xs text-gray-500">Véhicules</div>
                </div>
                <div>
                    <div class="text-lg font-bold text-emerald-600">{{ auth()->user()->assignments_count ?? 0 }}</div>
                    <div class="text-xs text-gray-500">Affectations</div>
                </div>
            </div>
            </template>
        </div>
    </div>
</nav>

<style>
    /* Styles pour le menu enterprise */
    .nav-item:hover {
        transform: translateX(2px);
    }

    .nav-item .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
