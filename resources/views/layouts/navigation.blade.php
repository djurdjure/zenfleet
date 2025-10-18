<nav x-data="{
    open: true,
    activeSection: '{{ request()->segment(2) }}',
    notifications: {
        alerts: {{ auth()->user()->unreadNotifications->where('type', 'like', '%Alert%')->count() }},
        repairs: {{ auth()->user()->unreadNotifications->where('type', 'like', '%Repair%')->count() }},
        expenses: {{ auth()->user()->unreadNotifications->where('type', 'like', '%Expense%')->count() }},
        maintenance: {{ auth()->user()->unreadNotifications->where('type', 'like', '%Maintenance%')->count() }}
    }
}" class="flex flex-col h-full bg-gradient-to-b from-slate-900 to-slate-800 shadow-2xl font-inter text-white">

    {{-- Logo Enterprise --}}
    <div class="flex items-center justify-center h-20 border-b border-slate-700/50 shrink-0 px-6 bg-slate-800/30">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
            <div class="p-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg group-hover:shadow-xl transition-all duration-300">
                <x-iconify icon="heroicons:truck" class="h-8 w-8 text-white" stroke-width="2" / />
            </div>
            <div class="flex flex-col">
                <span class="text-xl font-bold text-white group-hover:text-blue-300 transition-colors">ZenFleet</span>
                <span class="text-xs text-slate-400 font-medium">Enterprise Suite</span>
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
                <x-iconify icon="heroicons:magnifying-glass" class="h-4 w-4 text-slate-400" / />
            </div>
            <input type="text" x-model="searchQuery"
                   placeholder="Rechercher dans le menu..."
                   class="w-full pl-10 pr-4 py-2 bg-slate-800/50 border border-slate-600/30 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
        </div>

        {{-- Dashboard Principal --}}
        <a href="{{ route('dashboard') }}"
           class="nav-item flex items-center px-4 py-3 rounded-xl text-white/90 hover:text-white hover:bg-gradient-to-r hover:from-blue-500/20 hover:to-indigo-500/20 transition-all duration-300 group {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-500/30 to-indigo-500/30 text-white border border-blue-500/30' : '' }}">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-500 to-indigo-600' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} transition-all duration-300">
                <x-iconify icon="heroicons:squares-2x2" class="h-5 w-5" / />
            </div>
            <span class="ml-3 font-medium">Tableau de Bord</span>
            @if(request()->routeIs('dashboard'))
                <div class="ml-auto w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
            @endif
        </a>

        {{-- Section Alertes & Notifications --}}
        <div class="nav-item mt-8 mb-4">
            <h3 class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Alertes & Surveillance</h3>
        </div>

        <a href="{{ route('admin.alerts.index') }}"
           class="nav-item flex items-center px-4 py-3 rounded-xl text-white/90 hover:text-white hover:bg-gradient-to-r hover:from-red-500/20 hover:to-orange-500/20 transition-all duration-300 group {{ request()->routeIs('admin.alerts.*') ? 'bg-gradient-to-r from-red-500/30 to-orange-500/30 text-white border border-red-500/30' : '' }}">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('admin.alerts.*') ? 'bg-gradient-to-r from-red-500 to-orange-600' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} transition-all duration-300 relative">
                <x-iconify icon="heroicons:exclamation-triangle" class="h-5 w-5" / />
                @if(auth()->user()->unreadNotifications->where('type', 'like', '%Alert%')->count() > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                        {{ auth()->user()->unreadNotifications->where('type', 'like', '%Alert%')->count() }}
                    </span>
                @endif
            </div>
            <div class="ml-3 flex-1">
                <span class="font-medium">Alertes Système</span>
                <div class="text-xs text-slate-400">Maintenance • Réparations • Budgets</div>
            </div>
        </a>

        {{-- Section Modules Principaux --}}
        <div class="nav-item mt-8 mb-4">
            <h3 class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Modules Enterprise</h3>
        </div>

        {{-- Gestion de la Flotte --}}
        <div class="nav-item" x-data="{ expanded: {{ request()->routeIs('admin.vehicles.*', 'admin.assignments.*', 'admin.planning.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                    class="w-full flex items-center px-4 py-3 rounded-xl text-white/90 hover:text-white hover:bg-gradient-to-r hover:from-green-500/20 hover:to-emerald-500/20 transition-all duration-300 group">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-slate-700/50 group-hover:bg-slate-600/50 transition-all duration-300">
                    <x-iconify icon="heroicons:truck" class="h-5 w-5" / />
                </div>
                <div class="ml-3 flex-1 text-left">
                    <span class="font-medium">Gestion Flotte</span>
                    <div class="text-xs text-slate-400">Véhicules • Affectations</div>
                </div>
                <x-iconify icon="heroicons:chevron-down" class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" / />
            </button>
            <div x-show="expanded" x-collapse class="mt-2 ml-6 space-y-1">
                <a href="{{ route('admin.vehicles.index') }}" class="flex items-center px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 {{ request()->routeIs('admin.vehicles.*') ? 'text-white bg-slate-700/50' : '' }}">
                    <x-iconify icon="heroicons:truck" class="h-4 w-4 mr-3" / />
                    Véhicules
                </a>
                <a href="{{ route('admin.assignments.index') }}" class="flex items-center px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 {{ request()->routeIs('admin.assignments.*') ? 'text-white bg-slate-700/50' : '' }}">
                    <x-iconify icon="heroicons:clipboard-document-list" class="h-4 w-4 mr-3" / />
                    Affectations
                </a>
                <a href="{{ route('admin.planning.index') }}" class="flex items-center px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 {{ request()->routeIs('admin.planning.*') ? 'text-white bg-slate-700/50' : '' }}">
                    <x-iconify icon="heroicons:calendar"-days class="h-4 w-4 mr-3" / />
                    Planning
                </a>
            </div>
        </div>

        {{-- Chauffeurs --}}
        <div class="nav-item" x-data="{ expanded: {{ request()->routeIs('admin.drivers.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                    class="w-full flex items-center px-4 py-3 rounded-xl text-white/90 hover:text-white hover:bg-gradient-to-r hover:from-purple-500/20 hover:to-violet-500/20 transition-all duration-300 group">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-slate-700/50 group-hover:bg-slate-600/50 transition-all duration-300">
                    <x-iconify icon="heroicons:user"s class="h-5 w-5" / />
                </div>
                <div class="ml-3 flex-1 text-left">
                    <span class="font-medium">Chauffeurs</span>
                    <div class="text-xs text-slate-400">Personnel • Import/Export</div>
                </div>
                <x-iconify icon="heroicons:chevron-down" class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" / />
            </button>
            <div x-show="expanded" x-collapse class="mt-2 ml-6 space-y-1">
                <a href="{{ route('admin.drivers.index') }}" class="flex items-center px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 {{ request()->routeIs('admin.drivers.index') ? 'text-white bg-slate-700/50' : '' }}">
                    <x-iconify icon="heroicons:list-bullet" class="h-4 w-4 mr-3" / />
                    Liste des chauffeurs
                </a>
                <a href="{{ route('admin.drivers.import.show') }}" class="flex items-center px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 {{ request()->routeIs('admin.drivers.import.*') ? 'text-white bg-slate-700/50' : '' }}">
                    <x-iconify icon="heroicons:document"-up class="h-4 w-4 mr-3" / />
                    Importer chauffeurs
                </a>
            </div>
        </div>


        {{-- Fournisseurs Enterprise --}}
        <a href="{{ route('admin.suppliers-enterprise.index') }}"
           class="nav-item flex items-center px-4 py-3 rounded-xl text-white/90 hover:text-white hover:bg-gradient-to-r hover:from-teal-500/20 hover:to-cyan-500/20 transition-all duration-300 group {{ request()->routeIs('admin.suppliers-enterprise.*') ? 'bg-gradient-to-r from-teal-500/30 to-cyan-500/30 text-white border border-teal-500/30' : '' }}">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('admin.suppliers-enterprise.*') ? 'bg-gradient-to-r from-teal-500 to-cyan-600' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} transition-all duration-300">
                <x-iconify icon="heroicons:building-office" class="h-5 w-5" / />
            </div>
            <div class="ml-3 flex-1">
                <span class="font-medium">Fournisseurs</span>
                <div class="text-xs text-slate-400">Conformité DZ • Évaluations</div>
            </div>
            @if(request()->routeIs('admin.suppliers-enterprise.*'))
                <div class="w-2 h-2 bg-teal-400 rounded-full animate-pulse"></div>
            @endif
        </a>

        {{-- Dépenses Enterprise --}}
        <a href="{{ route('admin.vehicle-expenses.index') }}"
           class="nav-item flex items-center px-4 py-3 rounded-xl text-white/90 hover:text-white hover:bg-gradient-to-r hover:from-emerald-500/20 hover:to-green-500/20 transition-all duration-300 group {{ request()->routeIs('admin.vehicle-expenses.*', 'admin.expense-budgets.*') ? 'bg-gradient-to-r from-emerald-500/30 to-green-500/30 text-white border border-emerald-500/30' : '' }}">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('admin.vehicle-expenses.*', 'admin.expense-budgets.*') ? 'bg-gradient-to-r from-emerald-500 to-green-600' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} transition-all duration-300 relative">
                <x-iconify icon="heroicons:credit-card" class="h-5 w-5" / />
                @if(auth()->user()->unreadNotifications->where('type', 'like', '%Expense%')->count() > 0)
                    <span class="absolute -top-2 -right-2 bg-emerald-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        {{ auth()->user()->unreadNotifications->where('type', 'like', '%Expense%')->count() }}
                    </span>
                @endif
            </div>
            <div class="ml-3 flex-1">
                <span class="font-medium">Dépenses</span>
                <div class="text-xs text-slate-400">Budgets • Paiements • Rapports</div>
            </div>
            @if(request()->routeIs('admin.vehicle-expenses.*', 'admin.expense-budgets.*'))
                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
            @endif
        </a>

        {{-- Maintenance Enterprise avec sous-menus --}}
        <div class="nav-item" x-data="{ expanded: {{ request()->routeIs('admin.maintenance.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                    class="w-full flex items-center px-4 py-3 rounded-xl text-white/90 hover:text-white hover:bg-gradient-to-r hover:from-indigo-500/20 hover:to-purple-500/20 transition-all duration-300 group {{ request()->routeIs('admin.maintenance.*') ? 'bg-gradient-to-r from-indigo-500/30 to-purple-500/30 text-white border border-indigo-500/30' : '' }}">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('admin.maintenance.*') ? 'bg-gradient-to-r from-indigo-500 to-purple-600' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} transition-all duration-300 relative">
                    <x-iconify icon="heroicons:cog-6-tooth" class="h-5 w-5" / />
                    @if(auth()->user()->unreadNotifications->where('type', 'like', '%Maintenance%')->count() > 0)
                        <span class="absolute -top-2 -right-2 bg-indigo-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            {{ auth()->user()->unreadNotifications->where('type', 'like', '%Maintenance%')->count() }}
                        </span>
                    @endif
                </div>
                <div class="ml-3 flex-1 text-left">
                    <span class="font-medium">Maintenance</span>
                    <div class="text-xs text-slate-400">Surveillance • Planification</div>
                </div>
                <x-iconify icon="heroicons:chevron-down" class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" / />
            </button>
            <div x-show="expanded" x-collapse class="mt-2 ml-6 space-y-1">
                <a href="{{ route('admin.maintenance.surveillance.index') }}" class="flex items-center px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 {{ request()->routeIs('admin.maintenance.surveillance.*') ? 'text-white bg-slate-700/50' : '' }}">
                    <x-iconify icon="heroicons:computer-desktop" class="h-4 w-4 mr-3" / />
                    Surveillance
                </a>
                <a href="{{ route('admin.maintenance.schedules.index') }}" class="flex items-center px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 {{ request()->routeIs('admin.maintenance.schedules.*') ? 'text-white bg-slate-700/50' : '' }}">
                    <x-iconify icon="heroicons:calendar"-check class="h-4 w-4 mr-3" / />
                    Planifications
                </a>
                <a href="{{ route('admin.repair-requests.index') }}" class="flex items-center px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 {{ request()->routeIs('admin.repair-requests.*') ? 'text-white bg-slate-700/50' : '' }}">
                    <x-iconify icon="heroicons:wrench" class="h-4 w-4 mr-3" / />
                    Demandes de réparation
                </a>
                <a href="{{ route('admin.maintenance.operations.index') }}" class="flex items-center px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 {{ request()->routeIs('admin.maintenance.operations.*') ? 'text-white bg-slate-700/50' : '' }}">
                    <x-iconify icon="lucide:wrench" class="h-4 w-4 mr-3" / />
                    Opérations
                </a>
            </div>
        </div>

        {{-- Section Système --}}
        <div class="nav-item mt-8 mb-4">
            <h3 class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Système & Configuration</h3>
        </div>

        {{-- Documents --}}
        <div class="nav-item" x-data="{ expanded: {{ request()->routeIs('admin.documents.*', 'admin.document-categories.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                    class="w-full flex items-center px-4 py-3 rounded-xl text-white/90 hover:text-white hover:bg-gradient-to-r hover:from-slate-500/20 hover:to-gray-500/20 transition-all duration-300 group">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-slate-700/50 group-hover:bg-slate-600/50 transition-all duration-300">
                    <x-iconify icon="heroicons:folder-open" class="h-5 w-5" / />
                </div>
                <div class="ml-3 flex-1 text-left">
                    <span class="font-medium">Documents</span>
                    <div class="text-xs text-slate-400">Fichiers • Catégories</div>
                </div>
                <x-iconify icon="heroicons:chevron-down" class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" / />
            </button>
            <div x-show="expanded" x-collapse class="mt-2 ml-6 space-y-1">
                <a href="{{ route('admin.documents.index') }}" class="flex items-center px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 {{ request()->routeIs('admin.documents.*') ? 'text-white bg-slate-700/50' : '' }}">
                    <x-iconify icon="heroicons:document"-text class="h-4 w-4 mr-3" / />
                    Liste des Documents
                </a>
                <a href="{{ route('admin.document-categories.index') }}" class="flex items-center px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 {{ request()->routeIs('admin.document-categories.*') ? 'text-white bg-slate-700/50' : '' }}">
                    <x-iconify icon="heroicons:folder" class="h-4 w-4 mr-3" / />
                    Catégories
                </a>
            </div>
        </div>

        {{-- Administration --}}
        @hasanyrole('Super Admin|Admin')
        <div class="nav-item" x-data="{ expanded: {{ request()->routeIs('admin.organizations.*', 'admin.users.*', 'admin.roles.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                    class="w-full flex items-center px-4 py-3 rounded-xl text-white/90 hover:text-white hover:bg-gradient-to-r hover:from-rose-500/20 hover:to-pink-500/20 transition-all duration-300 group">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-slate-700/50 group-hover:bg-slate-600/50 transition-all duration-300">
                    <x-iconify icon="heroicons:shield-check" class="h-5 w-5" / />
                </div>
                <div class="ml-3 flex-1 text-left">
                    <span class="font-medium">Administration</span>
                    <div class="text-xs text-slate-400">Utilisateurs • Permissions</div>
                </div>
                <x-iconify icon="heroicons:chevron-down" class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" / />
            </button>
            <div x-show="expanded" x-collapse class="mt-2 ml-6 space-y-1">
                @role('Super Admin')
                <a href="{{ route('admin.organizations.index') }}" class="flex items-center px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 {{ request()->routeIs('admin.organizations.*') ? 'text-white bg-slate-700/50' : '' }}">
                    <x-iconify icon="heroicons:building-office" class="h-4 w-4 mr-3" / />
                    Organisations
                </a>
                @endrole
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'text-white bg-slate-700/50' : '' }}">
                    <x-iconify icon="heroicons:user"-cog class="h-4 w-4 mr-3" / />
                    Utilisateurs
                </a>
                <a href="{{ route('admin.roles.index') }}" class="flex items-center px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 {{ request()->routeIs('admin.roles.*') ? 'text-white bg-slate-700/50' : '' }}">
                    <x-iconify icon="heroicons:key" class="h-4 w-4 mr-3" / />
                    Rôles & Permissions
                </a>
            </div>
        </div>
        @endhasanyrole
    </div>

    {{-- Section Utilisateur Enterprise --}}
    <div class="mt-auto shrink-0 p-4 border-t border-slate-700/50">
        <div x-data="{ open: false }" @keydown.escape.window="open = false" @click.away="open = false" class="relative">
            <button @click="open = !open" class="w-full flex items-center space-x-3 group p-3 rounded-xl hover:bg-slate-700/30 transition-all duration-300">
                <div class="relative">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-lg">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                    </div>
                    <div class="absolute -bottom-1 -right-1 h-4 w-4 bg-green-500 rounded-full border-2 border-slate-800"></div>
                </div>
                <div class="flex-1 text-left">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                    <div class="flex items-center space-x-2">
                        <p class="text-xs text-slate-300">{{ Auth::user()->roles->first()?->name ?? 'Utilisateur' }}</p>
                        @if(Auth::user()->organization)
                            <span class="text-xs text-slate-400">•</span>
                            <p class="text-xs text-slate-400 truncate">{{ Auth::user()->organization->name }}</p>
                        @endif
                    </div>
                </div>
                <x-iconify icon="heroicons:chevron-up" class="h-4 w-4 text-slate-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" / />
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute bottom-full left-0 right-0 mb-2 w-full origin-bottom z-50"
                 style="display: none;">
                <div class="bg-slate-800 rounded-xl shadow-2xl ring-1 ring-slate-600/50 p-2 backdrop-blur-sm border border-slate-600/20">
                    <div class="space-y-1">
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center w-full px-4 py-3 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-all duration-200 group">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-700/50 group-hover:bg-blue-500/20 transition-all duration-200 mr-3">
                                <x-iconify icon="heroicons:user" class="h-4 w-4" / />
                            </div>
                            <div class="flex-1">
                                <div class="font-medium">Mon Profil</div>
                                <div class="text-xs text-slate-400">Paramètres du compte</div>
                            </div>
                        </a>

                        <a href="{{ route('admin.notifications.index') }}"
                           class="flex items-center w-full px-4 py-3 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-all duration-200 group">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-700/50 group-hover:bg-yellow-500/20 transition-all duration-200 mr-3 relative">
                                <x-iconify icon="heroicons:bell" class="h-4 w-4" / />
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="font-medium">Notifications</div>
                                <div class="text-xs text-slate-400">{{ auth()->user()->unreadNotifications->count() }} non lues</div>
                            </div>
                        </a>

                        <div class="my-2 border-t border-slate-600/30"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex items-center w-full px-4 py-3 text-sm text-slate-300 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-all duration-200 group">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-700/50 group-hover:bg-red-500/20 transition-all duration-200 mr-3">
                                    <x-iconify icon="heroicons:arrow-right-on-rectangle" class="h-4 w-4" / />
                                </div>
                                <div class="flex-1 text-left">
                                    <div class="font-medium">Déconnexion</div>
                                    <div class="text-xs text-slate-400">Terminer la session</div>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistiques rapides utilisateur --}}
        <div class="mt-4 p-3 bg-slate-800/50 rounded-xl border border-slate-600/30">
            <div class="grid grid-cols-2 gap-3 text-center">
                <div>
                    <div class="text-lg font-bold text-blue-400">{{ auth()->user()->vehicles_count ?? 0 }}</div>
                    <div class="text-xs text-slate-400">Véhicules</div>
                </div>
                <div>
                    <div class="text-lg font-bold text-emerald-400">{{ auth()->user()->assignments_count ?? 0 }}</div>
                    <div class="text-xs text-slate-400">Affectations</div>
                </div>
            </div>
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