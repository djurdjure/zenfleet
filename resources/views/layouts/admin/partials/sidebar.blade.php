{{-- 
    🚀 ZENFLEET - SIDEBAR ADMIN PARTIELLE
    Résolution de l'erreur: View [partials.admin.navigation] not found
--}}

@php
    $user = auth()->user();
    $currentRoute = request()->route()?->getName() ?? '';
    
    // Helper pour vérifier les routes actives
    $isActive = function($patterns) use ($currentRoute) {
        $patterns = is_array($patterns) ? $patterns : [$patterns];
        return collect($patterns)->contains(fn($pattern) => 
            str_contains($currentRoute, $pattern) || 
            request()->routeIs($pattern)
        );
    };
@endphp

<aside class="admin-sidebar" id="adminSidebar" data-collapsed="{{ request()->cookie('sidebar-collapsed', 'false') }}">
    
    {{-- Brand Header --}}
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <img src="{{ asset('images/zenfleet-logo.svg') }}" alt="ZenFleet" class="brand-logo">
            <span class="brand-text">ZenFleet</span>
        </a>
        <button type="button" class="sidebar-toggle" onclick="AdminSidebar.toggle()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    {{-- User Info --}}
    @if($user)
    <div class="sidebar-user">
        <div class="user-avatar">
            <img src="{{ $user->avatar_url ?? asset('images/default-avatar.png') }}" alt="{{ $user->name }}">
            <div class="status-indicator online"></div>
        </div>
        <div class="user-details">
            <h4 class="user-name">{{ Str::limit($user->name, 18) }}</h4>
            <span class="user-role">{{ $user->roles->first()?->name ?? 'Utilisateur' }}</span>
        </div>
    </div>
    @endif

    {{-- Navigation Menu --}}
    <nav class="sidebar-nav" role="navigation">
        <ul class="nav-menu">

            {{-- Dashboard --}}
            <li class="nav-item {{ $isActive('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="nav-link" title="Dashboard">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            {{-- Organisations (Super Admin) --}}
            @can('view-organizations')
            <li class="nav-item has-submenu {{ $isActive(['admin.organizations']) ? 'active open' : '' }}">
                <a href="#" class="nav-link submenu-toggle" data-target="organizations">
                    <i class="nav-icon fas fa-building"></i>
                    <span class="nav-text">Organisations</span>
                    <i class="submenu-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-submenu" id="submenu-organizations">
                    <li class="{{ $isActive('admin.organizations.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.organizations.index') }}" class="nav-sublink">
                            <i class="fas fa-list"></i>
                            <span>Liste complète</span>
                        </a>
                    </li>
                    <li class="{{ $isActive('admin.organizations.create') ? 'active' : '' }}">
                        <a href="{{ route('admin.organizations.create') }}" class="nav-sublink">
                            <i class="fas fa-plus"></i>
                            <span>Nouvelle organisation</span>
                        </a>
                    </li>
                    @if(Route::has('admin.organizations.export'))
                    <li>
                        <a href="{{ route('admin.organizations.export') }}" class="nav-sublink">
                            <i class="fas fa-download"></i>
                            <span>Export Excel</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endcan

            {{-- Flotte --}}
            @can('view-vehicles')
            <li class="nav-item has-submenu {{ $isActive(['admin.vehicles', 'admin.drivers', 'admin.assignments']) ? 'active open' : '' }}">
                <a href="#" class="nav-link submenu-toggle" data-target="fleet">
                    <i class="nav-icon fas fa-car"></i>
                    <span class="nav-text">Gestion Flotte</span>
                    <i class="submenu-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-submenu" id="submenu-fleet">
                    <li class="{{ $isActive('admin.vehicles') ? 'active' : '' }}">
                        <a href="{{ route('admin.vehicles.index') }}" class="nav-sublink">
                            <i class="fas fa-car"></i>
                            <span>Véhicules</span>
                        </a>
                    </li>
                    <li class="{{ $isActive('admin.drivers') ? 'active' : '' }}">
                        <a href="{{ route('admin.drivers.index') }}" class="nav-sublink">
                            <i class="fas fa-users"></i>
                            <span>Chauffeurs</span>
                        </a>
                    </li>
                    <li class="{{ $isActive('admin.assignments') ? 'active' : '' }}">
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
            <li class="nav-item has-submenu {{ $isActive(['admin.maintenance']) ? 'active open' : '' }}">
                <a href="#" class="nav-link submenu-toggle" data-target="maintenance">
                    <i class="nav-icon fas fa-tools"></i>
                    <span class="nav-text">Maintenance</span>
                    <i class="submenu-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-submenu" id="submenu-maintenance">
                    <li class="{{ $isActive('admin.maintenance.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.maintenance.dashboard') }}" class="nav-sublink">
                            <i class="fas fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="{{ $isActive('admin.maintenance.plans') ? 'active' : '' }}">
                        <a href="{{ route('admin.maintenance.plans.index') }}" class="nav-sublink">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Plans maintenance</span>
                        </a>
                    </li>
                    <li class="{{ $isActive('admin.maintenance.logs') ? 'active' : '' }}">
                        <a href="{{ route('admin.maintenance.logs.index') }}" class="nav-sublink">
                            <i class="fas fa-clipboard-check"></i>
                            <span>Interventions</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            {{-- Planning --}}
            @can('view-planning')
            <li class="nav-item {{ $isActive('admin.planning') ? 'active' : '' }}">
                <a href="{{ route('admin.planning.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-calendar"></i>
                    <span class="nav-text">Planning</span>
                </a>
            </li>
            @endcan

            {{-- Rapports --}}
            @can('view-reports')
            <li class="nav-item has-submenu {{ $isActive(['admin.reports', 'admin.analytics']) ? 'active open' : '' }}">
                <a href="#" class="nav-link submenu-toggle" data-target="reports">
                    <i class="nav-icon fas fa-chart-bar"></i>
                    <span class="nav-text">Rapports</span>
                    <i class="submenu-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-submenu" id="submenu-reports">
                    <li class="{{ $isActive('admin.reports') ? 'active' : '' }}">
                        <a href="{{ route('admin.reports.index') }}" class="nav-sublink">
                            <i class="fas fa-file-export"></i>
                            <span>Générateur</span>
                        </a>
                    </li>
                    <li class="{{ $isActive('admin.analytics') ? 'active' : '' }}">
                        <a href="{{ route('admin.analytics.index') }}" class="nav-sublink">
                            <i class="fas fa-analytics"></i>
                            <span>Analytics</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            {{-- Administration --}}
            @can('manage-users')
            <li class="nav-divider">
                <span>Administration</span>
            </li>
            <li class="nav-item has-submenu {{ $isActive(['admin.users', 'admin.roles']) ? 'active open' : '' }}">
                <a href="#" class="nav-link submenu-toggle" data-target="admin">
                    <i class="nav-icon fas fa-users-cog"></i>
                    <span class="nav-text">Utilisateurs</span>
                    <i class="submenu-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-submenu" id="submenu-admin">
                    <li class="{{ $isActive('admin.users') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}" class="nav-sublink">
                            <i class="fas fa-user"></i>
                            <span>Gestion utilisateurs</span>
                        </a>
                    </li>
                    <li class="{{ $isActive('admin.roles') ? 'active' : '' }}">
                        <a href="{{ route('admin.roles.index') }}" class="nav-sublink">
                            <i class="fas fa-user-tag"></i>
                            <span>Rôles & permissions</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            {{-- Paramètres --}}
            @can('access-settings')
            <li class="nav-item {{ $isActive('admin.settings') ? 'active' : '' }}">
                <a href="{{ route('admin.settings.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-cog"></i>
                    <span class="nav-text">Paramètres</span>
                </a>
            </li>
            @endcan

        </ul>
    </nav>

    {{-- Sidebar Footer --}}
    <div class="sidebar-footer">
        <div class="footer-content">
            <small class="version">ZenFleet v2.0</small>
            <div class="footer-links">
                <a href="#" class="footer-link" title="Aide">
                    <i class="fas fa-question-circle"></i>
                </a>
                <a href="#" class="footer-link" title="Support">
                    <i class="fas fa-life-ring"></i>
                </a>
            </div>
        </div>
    </div>
</aside>

{{-- Sidebar JavaScript --}}
@push('scripts')
<script>
window.AdminSidebar = {
    sidebar: null,
    
    init() {
        this.sidebar = document.getElementById('adminSidebar');
        if (!this.sidebar) return;
        
        this.bindEvents();
        this.restoreState();
    },

    bindEvents() {
        // Submenu toggles
        document.addEventListener('click', (e) => {
            if (e.target.closest('.submenu-toggle')) {
                e.preventDefault();
                this.toggleSubmenu(e.target.closest('.submenu-toggle'));
            }
        });

        // Mobile overlay close
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 992 && 
                !e.target.closest('#adminSidebar') && 
                !e.target.closest('.sidebar-toggle')) {
                this.collapse();
            }
        });

        // Responsive handling
        window.addEventListener('resize', () => this.handleResize());
    },

    toggle() {
        const isCollapsed = this.sidebar.classList.contains('collapsed');
        if (isCollapsed) {
            this.expand();
        } else {
            this.collapse();
        }
    },

    collapse() {
        this.sidebar.classList.add('collapsed');
        document.body.classList.add('sidebar-collapsed');
        this.setCookie('sidebar-collapsed', 'true');
    },

    expand() {
        this.sidebar.classList.remove('collapsed');
        document.body.classList.remove('sidebar-collapsed');
        this.setCookie('sidebar-collapsed', 'false');
    },

    toggleSubmenu(trigger) {
        const parent = trigger.closest('.has-submenu');
        const submenu = parent.querySelector('.nav-submenu');
        const arrow = trigger.querySelector('.submenu-arrow');
        
        if (!submenu) return;

        const isOpen = parent.classList.contains('open');
        
        // Close other submenus
        document.querySelectorAll('.has-submenu.open').forEach(item => {
            if (item !== parent) {
                item.classList.remove('open');
                const otherSubmenu = item.querySelector('.nav-submenu');
                const otherArrow = item.querySelector('.submenu-arrow');
                if (otherSubmenu) otherSubmenu.style.maxHeight = '0px';
                if (otherArrow) {
                    otherArrow.classList.remove('fa-chevron-down');
                    otherArrow.classList.add('fa-chevron-right');
                }
            }
        });

        // Toggle current submenu
        if (isOpen) {
            parent.classList.remove('open');
            submenu.style.maxHeight = '0px';
            arrow.classList.remove('fa-chevron-down');
            arrow.classList.add('fa-chevron-right');
        } else {
            parent.classList.add('open');
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
            arrow.classList.remove('fa-chevron-right');
            arrow.classList.add('fa-chevron-down');
        }
    },

    handleResize() {
        if (window.innerWidth <= 992) {
            document.body.classList.add('mobile-mode');
            this.collapse();
        } else {
            document.body.classList.remove('mobile-mode');
            this.restoreState();
        }
    },

    restoreState() {
        const collapsed = this.getCookie('sidebar-collapsed') === 'true';
        if (collapsed) {
            this.collapse();
        } else {
            this.expand();
        }

        // Restore active submenus
        document.querySelectorAll('.has-submenu.active').forEach(item => {
            const trigger = item.querySelector('.submenu-toggle');
            if (trigger) {
                this.toggleSubmenu(trigger);
            }
        });
    },

    setCookie(name, value) {
        document.cookie = `${name}=${value}; path=/; max-age=31536000`;
    },

    getCookie(name) {
        const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : null;
    }
};

// Auto-init
document.addEventListener('DOMContentLoaded', () => AdminSidebar.init());
</script>
@endpush
