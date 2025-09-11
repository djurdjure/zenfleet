{{-- 
    🚀 ZENFLEET - COMPOSANT SIDEBAR CORRIGÉ
    Résolution définitive de l'erreur $active
    Emplacement: resources/views/components/admin/sidebar.blade.php
--}}

@php
    // Variables sécurisées pour éviter les erreurs "Undefined variable"
    $currentRoute = request()->route()?->getName() ?? '';
    $user = auth()->user();
    $userRole = $user?->roles?->first()?->name ?? 'Utilisateur';
    $userAvatar = $user?->avatar ?? '/images/default-avatar.png';
    
    // Helper pour déterminer si une route est active
    $isRouteActive = function($routes) use ($currentRoute) {
        if (is_string($routes)) {
            return str_contains($currentRoute, $routes);
        }
        return collect($routes)->contains(fn($route) => str_contains($currentRoute, $route));
    };
@endphp

<aside id="zenfleet-sidebar" 
       class="zenfleet-sidebar {{ $collapsed ?? false ? 'collapsed' : '' }}"
       data-sidebar="zenfleet">
    
    {{-- Header avec logo --}}
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <img src="{{ asset('images/zenfleet-logo.svg') }}" 
                     alt="ZenFleet" 
                     class="brand-image" />
                <span class="brand-text">ZenFleet</span>
            </a>
        </div>
        
        <button type="button" 
                class="sidebar-toggle-btn"
                onclick="ZenFleetSidebar.toggle()"
                aria-label="Basculer la sidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    {{-- Profil utilisateur --}}
    @if($user)
    <div class="sidebar-user">
        <div class="user-avatar">
            <img src="{{ asset($userAvatar) }}" 
                 alt="{{ $user->name }}" />
        </div>
        <div class="user-details">
            <h4 class="user-name">{{ Str::limit($user->name, 18) }}</h4>
            <span class="user-role">{{ $userRole }}</span>
        </div>
    </div>
    @endif

    {{-- Navigation principale --}}
    <nav class="sidebar-nav" role="navigation">
        <ul class="nav-list">

            {{-- Dashboard --}}
            <li class="nav-item {{ $isRouteActive('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <span class="nav-label">Dashboard</span>
                </a>
            </li>

            {{-- Organisations --}}
            @can('view-organizations')
            <li class="nav-item nav-dropdown {{ $isRouteActive(['admin.organizations']) ? 'active' : '' }}">
                <a href="#" class="nav-link dropdown-toggle" data-target="organizations">
                    <i class="nav-icon fas fa-building"></i>
                    <span class="nav-label">Organisations</span>
                    <i class="dropdown-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-dropdown-menu" id="dropdown-organizations">
                    <li class="{{ $isRouteActive('admin.organizations.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.organizations.index') }}" class="nav-sublink">
                            <i class="fas fa-list"></i>
                            <span>Liste</span>
                        </a>
                    </li>
                    <li class="{{ $isRouteActive('admin.organizations.create') ? 'active' : '' }}">
                        <a href="{{ route('admin.organizations.create') }}" class="nav-sublink">
                            <i class="fas fa-plus"></i>
                            <span>Nouvelle</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            {{-- Flotte --}}
            @can('view-vehicles')
            <li class="nav-item nav-dropdown {{ $isRouteActive(['admin.vehicles', 'admin.drivers', 'admin.assignments']) ? 'active' : '' }}">
                <a href="#" class="nav-link dropdown-toggle" data-target="fleet">
                    <i class="nav-icon fas fa-car"></i>
                    <span class="nav-label">Flotte</span>
                    <i class="dropdown-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-dropdown-menu" id="dropdown-fleet">
                    <li class="{{ $isRouteActive('admin.vehicles') ? 'active' : '' }}">
                        <a href="{{ route('admin.vehicles.index') }}" class="nav-sublink">
                            <i class="fas fa-car"></i>
                            <span>Véhicules</span>
                        </a>
                    </li>
                    <li class="{{ $isRouteActive('admin.drivers') ? 'active' : '' }}">
                        <a href="{{ route('admin.drivers.index') }}" class="nav-sublink">
                            <i class="fas fa-users"></i>
                            <span>Chauffeurs</span>
                        </a>
                    </li>
                    <li class="{{ $isRouteActive('admin.assignments') ? 'active' : '' }}">
                        <a href="{{ route('admin.assignments.index') }}" class="nav-sublink">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Affectations</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            {{-- Maintenance --}}
            @can('view-maintenance')
            <li class="nav-item nav-dropdown {{ $isRouteActive(['admin.maintenance']) ? 'active' : '' }}">
                <a href="#" class="nav-link dropdown-toggle" data-target="maintenance">
                    <i class="nav-icon fas fa-tools"></i>
                    <span class="nav-label">Maintenance</span>
                    <i class="dropdown-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-dropdown-menu" id="dropdown-maintenance">
                    <li class="{{ $isRouteActive('admin.maintenance.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.maintenance.dashboard') }}" class="nav-sublink">
                            <i class="fas fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="{{ $isRouteActive('admin.maintenance.plans') ? 'active' : '' }}">
                        <a href="{{ route('admin.maintenance.plans.index') }}" class="nav-sublink">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Plans</span>
                        </a>
                    </li>
                    <li class="{{ $isRouteActive('admin.maintenance.logs') ? 'active' : '' }}">
                        <a href="{{ route('admin.maintenance.logs.index') }}" class="nav-sublink">
                            <i class="fas fa-clipboard-check"></i>
                            <span>Logs</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            {{-- Documents --}}
            @can('view-documents')
            <li class="nav-item nav-dropdown {{ $isRouteActive(['admin.documents', 'admin.handovers']) ? 'active' : '' }}">
                <a href="#" class="nav-link dropdown-toggle" data-target="documents">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <span class="nav-label">Documents</span>
                    <i class="dropdown-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-dropdown-menu" id="dropdown-documents">
                    <li class="{{ $isRouteActive('admin.documents') ? 'active' : '' }}">
                        <a href="{{ route('admin.documents.index') }}" class="nav-sublink">
                            <i class="fas fa-folder"></i>
                            <span>Bibliothèque</span>
                        </a>
                    </li>
                    <li class="{{ $isRouteActive('admin.handovers') ? 'active' : '' }}">
                        <a href="{{ route('admin.handovers.vehicles.index') }}" class="nav-sublink">
                            <i class="fas fa-handshake"></i>
                            <span>Remises</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            {{-- Planning --}}
            @can('view-planning')
            <li class="nav-item {{ $isRouteActive('admin.planning') ? 'active' : '' }}">
                <a href="{{ route('admin.planning.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-calendar"></i>
                    <span class="nav-label">Planning</span>
                </a>
            </li>
            @endcan

            {{-- Rapports --}}
            @can('view-reports')
            <li class="nav-item nav-dropdown {{ $isRouteActive(['admin.reports', 'admin.analytics']) ? 'active' : '' }}">
                <a href="#" class="nav-link dropdown-toggle" data-target="reports">
                    <i class="nav-icon fas fa-chart-bar"></i>
                    <span class="nav-label">Rapports</span>
                    <i class="dropdown-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-dropdown-menu" id="dropdown-reports">
                    <li class="{{ $isRouteActive('admin.reports') ? 'active' : '' }}">
                        <a href="{{ route('admin.reports.index') }}" class="nav-sublink">
                            <i class="fas fa-file-export"></i>
                            <span>Générateur</span>
                        </a>
                    </li>
                    <li class="{{ $isRouteActive('admin.analytics') ? 'active' : '' }}">
                        <a href="{{ route('admin.analytics.index') }}" class="nav-sublink">
                            <i class="fas fa-analytics"></i>
                            <span>Analytics</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            {{-- Utilisateurs --}}
            @can('manage-users')
            <li class="nav-item nav-dropdown {{ $isRouteActive(['admin.users', 'admin.roles']) ? 'active' : '' }}">
                <a href="#" class="nav-link dropdown-toggle" data-target="users">
                    <i class="nav-icon fas fa-users-cog"></i>
                    <span class="nav-label">Utilisateurs</span>
                    <i class="dropdown-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-dropdown-menu" id="dropdown-users">
                    <li class="{{ $isRouteActive('admin.users') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}" class="nav-sublink">
                            <i class="fas fa-user"></i>
                            <span>Gestion</span>
                        </a>
                    </li>
                    <li class="{{ $isRouteActive('admin.roles') ? 'active' : '' }}">
                        <a href="{{ route('admin.roles.index') }}" class="nav-sublink">
                            <i class="fas fa-user-tag"></i>
                            <span>Rôles</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            {{-- Paramètres --}}
            @can('access-settings')
            <li class="nav-item {{ $isRouteActive('admin.settings') ? 'active' : '' }}">
                <a href="{{ route('admin.settings.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-cog"></i>
                    <span class="nav-label">Paramètres</span>
                </a>
            </li>
            @endcan

        </ul>
    </nav>

    {{-- Footer --}}
    <div class="sidebar-footer">
        <small class="text-muted">ZenFleet v2.0</small>
    </div>
</aside>

{{-- JavaScript intégré --}}
@push('scripts')
<script>
// 🚀 Gestionnaire sidebar ZenFleet optimisé
window.ZenFleetSidebar = {
    sidebar: null,
    
    init() {
        this.sidebar = document.getElementById('zenfleet-sidebar');
        if (!this.sidebar) return;
        
        this.bindEvents();
        this.restoreState();
        this.handleResponsive();
    },

    bindEvents() {
        // Dropdowns
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('.dropdown-toggle');
            if (trigger) {
                e.preventDefault();
                this.toggleDropdown(trigger);
            }
        });

        // Responsive
        window.addEventListener('resize', () => this.handleResponsive());
        
        // Mobile overlay close
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && 
                !e.target.closest('#zenfleet-sidebar') && 
                !e.target.closest('.sidebar-toggle-btn')) {
                this.collapse();
            }
        });
    },

    toggle() {
        if (!this.sidebar) return;
        
        this.sidebar.classList.toggle('collapsed');
        document.body.classList.toggle('sidebar-collapsed');
        this.saveState();
    },

    collapse() {
        if (!this.sidebar) return;
        
        this.sidebar.classList.add('collapsed');
        document.body.classList.add('sidebar-collapsed');
        this.saveState();
    },

    expand() {
        if (!this.sidebar) return;
        
        this.sidebar.classList.remove('collapsed');
        document.body.classList.remove('sidebar-collapsed');
        this.saveState();
    },

    toggleDropdown(trigger) {
        const dropdown = trigger.closest('.nav-dropdown');
        const menu = dropdown.querySelector('.nav-dropdown-menu');
        const arrow = trigger.querySelector('.dropdown-arrow');
        
        if (!menu || !arrow) return;

        const isOpen = menu.style.maxHeight && menu.style.maxHeight !== '0px';
        
        // Fermer autres dropdowns
        document.querySelectorAll('.nav-dropdown-menu').forEach(otherMenu => {
            if (otherMenu !== menu) {
                otherMenu.style.maxHeight = '0px';
                const otherArrow = otherMenu.parentElement.querySelector('.dropdown-arrow');
                if (otherArrow) {
                    otherArrow.classList.remove('fa-chevron-down');
                    otherArrow.classList.add('fa-chevron-right');
                }
            }
        });

        // Toggle current
        if (isOpen) {
            menu.style.maxHeight = '0px';
            arrow.classList.remove('fa-chevron-down');
            arrow.classList.add('fa-chevron-right');
        } else {
            menu.style.maxHeight = menu.scrollHeight + 'px';
            arrow.classList.remove('fa-chevron-right');
            arrow.classList.add('fa-chevron-down');
        }

        // Sauvegarder l'état
        const target = trigger.dataset.target;
        localStorage.setItem(`dropdown-${target}`, isOpen ? 'closed' : 'open');
    },

    handleResponsive() {
        if (window.innerWidth <= 768) {
            document.body.classList.add('mobile-view');
            this.collapse();
        } else {
            document.body.classList.remove('mobile-view');
            this.restoreState();
        }
    },

    saveState() {
        const isCollapsed = this.sidebar?.classList.contains('collapsed');
        localStorage.setItem('zenfleet-sidebar-state', isCollapsed ? 'collapsed' : 'expanded');
    },

    restoreState() {
        // Restaurer sidebar
        const savedState = localStorage.getItem('zenfleet-sidebar-state');
        if (savedState === 'collapsed') {
            this.collapse();
        } else {
            this.expand();
        }

        // Restaurer dropdowns
        document.querySelectorAll('.dropdown-toggle').forEach(trigger => {
            const target = trigger.dataset.target;
            const savedDropdownState = localStorage.getItem(`dropdown-${target}`);
            
            if (savedDropdownState === 'open') {
                this.toggleDropdown(trigger);
            }
        });
    }
};

// Auto-initialisation
document.addEventListener('DOMContentLoaded', () => {
    ZenFleetSidebar.init();
});
</script>
@endpush
