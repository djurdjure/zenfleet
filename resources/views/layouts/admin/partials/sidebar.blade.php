{{--
    🎨 ZENFLEET - SIDEBAR ULTRA-MODERNE ENTERPRISE GRADE
    Design UI/UX Expert - Couleurs harmonieuses et style moderne
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

<aside class="modern-sidebar" id="modernSidebar">

    {{-- Brand Header Ultra-Moderne --}}
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <div class="brand-logo-circle">
                <img src="{{ asset('images/zenfleet-logo.svg') }}" alt="ZenFleet" class="brand-logo">
            </div>
            <span class="brand-text">ZenFleet</span>
        </a>
    </div>

    {{-- User Info Enterprise --}}
    @if($user)
    <div class="sidebar-user">
        <div class="user-avatar">
            <img src="{{ $user->avatar_url ?? asset('images/default-avatar.png') }}" alt="{{ $user->name }}">
            <div class="status-indicator"></div>
        </div>
        <div class="user-details">
            <h4 class="user-name">{{ Str::limit($user->name, 18) }}</h4>
            <span class="user-role">{{ $user->roles->first()?->name ?? 'Utilisateur' }}</span>
        </div>
    </div>
    @endif

    {{-- Navigation Menu Ultra-Moderne --}}
    <nav class="sidebar-nav" role="navigation">
        <ul class="nav-menu">

            {{-- Dashboard --}}
            <li class="nav-item {{ $isActive('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="nav-link" title="Dashboard">
                    <div class="nav-icon-circle">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                    </div>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            {{-- Organisations (Super Admin) --}}
            @can('view-organizations')
            <li class="nav-item has-submenu {{ $isActive(['admin.organizations']) ? 'active open' : '' }}">
                <a href="#" class="nav-link submenu-toggle" data-target="organizations">
                    <div class="nav-icon-circle">
                        <i class="nav-icon fas fa-building"></i>
                    </div>
                    <span class="nav-text">Organisations</span>
                    <i class="submenu-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-submenu" id="submenu-organizations">
                    <li class="{{ $isActive('admin.organizations.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.organizations.index') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-list"></i>
                            </div>
                            <span>Liste complète</span>
                        </a>
                    </li>
                    <li class="{{ $isActive('admin.organizations.create') ? 'active' : '' }}">
                        <a href="{{ route('admin.organizations.create') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-plus"></i>
                            </div>
                            <span>Nouvelle organisation</span>
                        </a>
                    </li>
                    @if(Route::has('admin.organizations.export'))
                    <li>
                        <a href="{{ route('admin.organizations.export') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-download"></i>
                            </div>
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
                    <div class="nav-icon-circle">
                        <i class="nav-icon fas fa-car"></i>
                    </div>
                    <span class="nav-text">Gestion Flotte</span>
                    <i class="submenu-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-submenu" id="submenu-fleet">
                    <li class="{{ $isActive('admin.vehicles') ? 'active' : '' }}">
                        <a href="{{ route('admin.vehicles.index') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-car"></i>
                            </div>
                            <span>Véhicules</span>
                        </a>
                    </li>
                    <li class="{{ $isActive('admin.drivers') ? 'active' : '' }}">
                        <a href="{{ route('admin.drivers.index') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-users"></i>
                            </div>
                            <span>Chauffeurs</span>
                        </a>
                    </li>
                    <li class="{{ $isActive('admin.assignments') ? 'active' : '' }}">
                        <a href="{{ route('admin.assignments.index') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <span>Affectations</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            {{-- Demandes de Réparation --}}
            @canany(['view own repair requests', 'view team repair requests', 'view all repair requests'])
            <li class="nav-item has-submenu {{ $isActive(['admin.repair-requests']) ? 'active open' : '' }}">
                <a href="#" class="nav-link submenu-toggle" data-target="repair-requests">
                    <div class="nav-icon-circle">
                        <i class="nav-icon fas fa-wrench"></i>
                    </div>
                    <span class="nav-text">Réparations</span>
                    @php
                        $pendingCount = \App\Models\RepairRequest::where('organization_id', auth()->user()->organization_id)
                            ->whereIn('status', ['pending_supervisor', 'pending_fleet_manager'])
                            ->when(auth()->user()->hasRole('Chauffeur'), function($q) {
                                $q->whereHas('driver', fn($q2) => $q2->where('user_id', auth()->id()));
                            })
                            ->when(auth()->user()->hasRole('Supervisor'), function($q) {
                                $q->where('status', 'pending_supervisor')
                                  ->whereHas('driver', fn($q2) => $q2->where('supervisor_id', auth()->id()));
                            })
                            ->when(auth()->user()->hasRole('Gestionnaire Flotte'), function($q) {
                                $q->where('status', 'pending_fleet_manager');
                            })
                            ->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="nav-badge">{{ $pendingCount }}</span>
                    @endif
                    <i class="submenu-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-submenu" id="submenu-repair-requests">
                    <li class="{{ $isActive('admin.repair-requests.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.repair-requests.index') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-list"></i>
                            </div>
                            <span>Toutes les demandes</span>
                        </a>
                    </li>
                    @can('create repair requests')
                    <li class="{{ $isActive('admin.repair-requests.create') ? 'active' : '' }}">
                        <a href="{{ route('admin.repair-requests.create') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <span>Nouvelle demande</span>
                        </a>
                    </li>
                    @endcan
                    @can('export repair requests')
                    <li class="{{ $isActive('admin.repair-requests.stats') ? 'active' : '' }}">
                        <a href="{{ route('admin.repair-requests.index', ['filter' => 'stats']) }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <span>Statistiques</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcan

            {{-- Maintenance --}}
            @can('view-maintenance')
            <li class="nav-item has-submenu {{ $isActive(['admin.maintenance']) ? 'active open' : '' }}">
                <a href="#" class="nav-link submenu-toggle" data-target="maintenance">
                    <div class="nav-icon-circle">
                        <i class="nav-icon fas fa-tools"></i>
                    </div>
                    <span class="nav-text">Maintenance</span>
                    <i class="submenu-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-submenu" id="submenu-maintenance">
                    <li class="{{ $isActive('admin.maintenance.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.maintenance.dashboard') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="{{ $isActive('admin.maintenance.plans') ? 'active' : '' }}">
                        <a href="{{ route('admin.maintenance.plans.index') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <span>Plans maintenance</span>
                        </a>
                    </li>
                    <li class="{{ $isActive('admin.maintenance.logs') ? 'active' : '' }}">
                        <a href="{{ route('admin.maintenance.logs.index') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
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
                    <div class="nav-icon-circle">
                        <i class="nav-icon fas fa-calendar"></i>
                    </div>
                    <span class="nav-text">Planning</span>
                </a>
            </li>
            @endcan

            {{-- Rapports --}}
            @can('view-reports')
            <li class="nav-item has-submenu {{ $isActive(['admin.reports', 'admin.analytics']) ? 'active open' : '' }}">
                <a href="#" class="nav-link submenu-toggle" data-target="reports">
                    <div class="nav-icon-circle">
                        <i class="nav-icon fas fa-chart-bar"></i>
                    </div>
                    <span class="nav-text">Rapports</span>
                    <i class="submenu-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-submenu" id="submenu-reports">
                    <li class="{{ $isActive('admin.reports') ? 'active' : '' }}">
                        <a href="{{ route('admin.reports.index') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-file-export"></i>
                            </div>
                            <span>Générateur</span>
                        </a>
                    </li>
                    <li class="{{ $isActive('admin.analytics') ? 'active' : '' }}">
                        <a href="{{ route('admin.analytics.index') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-analytics"></i>
                            </div>
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
                    <div class="nav-icon-circle">
                        <i class="nav-icon fas fa-users-cog"></i>
                    </div>
                    <span class="nav-text">Utilisateurs</span>
                    <i class="submenu-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-submenu" id="submenu-admin">
                    <li class="{{ $isActive('admin.users') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-user"></i>
                            </div>
                            <span>Gestion utilisateurs</span>
                        </a>
                    </li>
                    <li class="{{ $isActive('admin.roles') ? 'active' : '' }}">
                        <a href="{{ route('admin.roles.index') }}" class="nav-sublink">
                            <div class="nav-subicon-circle">
                                <i class="fas fa-user-tag"></i>
                            </div>
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
                    <div class="nav-icon-circle">
                        <i class="nav-icon fas fa-cog"></i>
                    </div>
                    <span class="nav-text">Paramètres</span>
                </a>
            </li>
            @endcan

        </ul>
    </nav>

    {{-- Sidebar Footer Modern --}}
    <div class="sidebar-footer">
        <div class="footer-content">
            <div class="footer-brand">
                <small class="version">ZenFleet v2.0</small>
                <small class="build">Enterprise Edition</small>
            </div>
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

{{-- Sidebar JavaScript Ultra-Performant --}}
@push('scripts')
<script>
window.ModernSidebar = {
    sidebar: null,

    init() {
        this.sidebar = document.getElementById('modernSidebar');
        if (!this.sidebar) return;

        this.bindEvents();
        this.initActiveStates();
    },

    bindEvents() {
        // Submenu toggles avec animations fluides
        document.addEventListener('click', (e) => {
            if (e.target.closest('.submenu-toggle')) {
                e.preventDefault();
                this.toggleSubmenu(e.target.closest('.submenu-toggle'));
            }
        });

        // Gestion responsive intelligente
        window.addEventListener('resize', () => this.handleResize());

        // Animations hover avancées
        this.initHoverEffects();
    },

    toggleSubmenu(trigger) {
        const parent = trigger.closest('.has-submenu');
        const submenu = parent.querySelector('.nav-submenu');
        const arrow = trigger.querySelector('.submenu-arrow');

        if (!submenu) return;

        const isOpen = parent.classList.contains('open');

        // Animation fluide des autres submenus
        document.querySelectorAll('.has-submenu.open').forEach(item => {
            if (item !== parent) {
                item.classList.remove('open');
                const otherSubmenu = item.querySelector('.nav-submenu');
                const otherArrow = item.querySelector('.submenu-arrow');
                if (otherSubmenu) {
                    otherSubmenu.style.maxHeight = '0px';
                    otherSubmenu.style.opacity = '0';
                }
                if (otherArrow) {
                    otherArrow.style.transform = 'rotate(0deg)';
                }
            }
        });

        // Animation du submenu actuel
        if (isOpen) {
            parent.classList.remove('open');
            submenu.style.maxHeight = '0px';
            submenu.style.opacity = '0';
            arrow.style.transform = 'rotate(0deg)';
        } else {
            parent.classList.add('open');
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
            submenu.style.opacity = '1';
            arrow.style.transform = 'rotate(90deg)';
        }
    },

    initActiveStates() {
        // Initialiser les états actifs des submenus
        document.querySelectorAll('.has-submenu.active').forEach(item => {
            const trigger = item.querySelector('.submenu-toggle');
            const submenu = item.querySelector('.nav-submenu');
            const arrow = item.querySelector('.submenu-arrow');

            if (trigger && submenu) {
                item.classList.add('open');
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
                submenu.style.opacity = '1';
                if (arrow) {
                    arrow.style.transform = 'rotate(90deg)';
                }
            }
        });
    },

    initHoverEffects() {
        // Effets hover avancés pour les icônes
        document.querySelectorAll('.nav-icon-circle, .nav-subicon-circle').forEach(circle => {
            circle.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
                this.style.boxShadow = '0 8px 25px rgba(99, 102, 241, 0.3)';
            });

            circle.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
                this.style.boxShadow = this.closest('.nav-item.active') ?
                    '0 4px 15px rgba(99, 102, 241, 0.4)' :
                    '0 2px 8px rgba(255, 255, 255, 0.15)';
            });
        });
    },

    handleResize() {
        // Gestion responsive optimisée
        const isMobile = window.innerWidth <= 768;
        document.body.classList.toggle('mobile-mode', isMobile);

        if (isMobile) {
            document.body.classList.add('sidebar-overlay-mode');
        } else {
            document.body.classList.remove('sidebar-overlay-mode');
        }
    }
};

// Auto-init avec performance optimisée
document.addEventListener('DOMContentLoaded', () => ModernSidebar.init());
</script>
@endpush

{{-- CSS Ultra-Moderne Enterprise --}}
@push('styles')
<style>
/* 🎨 ZENFLEET SIDEBAR ULTRA-MODERNE - DESIGN UI/UX EXPERT */

.modern-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    height: 100vh;
    background: #ebf2f9;
    color: #374151;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 4px 0 25px rgba(0, 0, 0, 0.08);
    backdrop-filter: blur(10px);
    border-right: 1px solid rgba(255, 255, 255, 0.2);
}

/* 🎨 Brand Header Ultra-Moderne */
.sidebar-brand {
    padding: 1.75rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.6) 100%);
}

.brand-link {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.brand-link:hover {
    transform: translateY(-2px);
}

.brand-logo-circle {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 3px solid rgba(255, 255, 255, 0.9);
}

.brand-logo-circle:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 35px rgba(99, 102, 241, 0.4);
}

.brand-logo {
    width: 24px;
    height: 24px;
    filter: brightness(0) invert(1);
}

.brand-text {
    font-size: 1.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #1e293b 0%, #374151 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -0.02em;
}

/* 🎨 User Profile Enterprise */
.sidebar-user {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.7) 0%, rgba(255, 255, 255, 0.4) 100%);
}

.user-avatar {
    position: relative;
    margin-right: 12px;
}

.user-avatar img {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: 3px solid #ffffff;
    object-fit: cover;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.status-indicator {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 12px;
    height: 12px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: 2px solid white;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
}

.user-name {
    font-size: 0.9rem;
    font-weight: 700;
    margin: 0 0 2px 0;
    color: #1e293b;
    line-height: 1.2;
}

.user-role {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* 🎨 Navigation Ultra-Moderne */
.sidebar-nav {
    flex: 1;
    padding: 1rem 0;
    overflow-y: auto;
    overflow-x: hidden;
}

.nav-menu {
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-item {
    margin: 0 1rem 0.5rem;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    color: #4b5563;
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    font-weight: 500;
    font-size: 0.875rem;
}

.nav-link:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
    color: #1e293b;
    transform: translateX(4px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

.nav-item.active > .nav-link {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
    transform: translateX(2px);
}

/* 🎨 Icônes avec Cercles Blancs */
.nav-icon-circle {
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    box-shadow: 0 2px 8px rgba(255, 255, 255, 0.15);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255, 255, 255, 0.4);
}

.nav-item.active .nav-icon-circle {
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.6);
}

.nav-icon {
    font-size: 0.875rem;
    color: #6366f1;
    transition: all 0.3s ease;
}

.nav-item.active .nav-icon {
    color: #4f46e5;
    transform: scale(1.1);
}

.nav-text {
    flex: 1;
    font-weight: 600;
    letter-spacing: -0.01em;
}

.submenu-arrow {
    font-size: 0.75rem;
    color: #9ca3af;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    margin-left: 8px;
}

/* 🎨 Submenus Modernes */
.nav-submenu {
    list-style: none;
    margin: 8px 0 0 0;
    padding: 0;
    max-height: 0;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.6) 0%, rgba(255, 255, 255, 0.3) 100%);
    border-radius: 8px;
    backdrop-filter: blur(5px);
    opacity: 0;
}

.has-submenu.open .nav-submenu {
    max-height: 500px;
    opacity: 1;
    padding: 8px 0;
    box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.05);
}

.nav-sublink {
    display: flex;
    align-items: center;
    padding: 8px 16px;
    color: #6b7280;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 6px;
    margin: 2px 8px;
}

.nav-sublink:hover {
    background: rgba(255, 255, 255, 0.8);
    color: #374151;
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.nav-submenu li.active .nav-sublink {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.nav-subicon-circle {
    width: 24px;
    height: 24px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 8px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.nav-submenu li.active .nav-subicon-circle {
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 2px 8px rgba(255, 255, 255, 0.2);
}

.nav-subicon-circle i {
    font-size: 0.75rem;
    color: #6366f1;
}

.nav-submenu li.active .nav-subicon-circle i {
    color: #4f46e5;
}

/* 🎨 Dividers */
.nav-divider {
    margin: 1.5rem 1rem 1rem;
    padding: 0 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.4);
    padding-top: 1rem;
}

.nav-divider span {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #9ca3af;
}

/* 🎨 Footer Ultra-Moderne */
.sidebar-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.3);
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0.5) 100%);
}

.footer-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.footer-brand {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.version, .build {
    font-size: 0.7rem;
    color: #6b7280;
    font-weight: 600;
}

.build {
    color: #9ca3af;
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.footer-links {
    display: flex;
    gap: 8px;
}

.footer-link {
    width: 28px;
    height: 28px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6366f1;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255, 255, 255, 0.4);
}

.footer-link:hover {
    background: #6366f1;
    color: white;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

/* 🎨 Body Adjustments */
body {
    padding-left: 280px;
    transition: padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body.mobile-mode {
    padding-left: 0;
}

/* 🎨 Scrollbar Ultra-Moderne */
.sidebar-nav::-webkit-scrollbar {
    width: 6px;
}

.sidebar-nav::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar-nav::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.3) 0%, rgba(79, 70, 229, 0.3) 100%);
    border-radius: 3px;
    transition: all 0.3s ease;
}

.sidebar-nav::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.5) 0%, rgba(79, 70, 229, 0.5) 100%);
}

/* 🎨 Responsive Ultra-Optimisé */
@media (max-width: 768px) {
    .modern-sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-overlay-mode .modern-sidebar {
        transform: translateX(0);
    }

    .sidebar-overlay-mode::after {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        backdrop-filter: blur(2px);
    }
}

/* 🎨 Animations Avancées */
@keyframes iconPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.nav-item.active .nav-icon-circle {
    animation: iconPulse 2s ease-in-out infinite;
}

/* 🎨 Performance Optimizations */
.modern-sidebar * {
    will-change: transform, opacity, box-shadow;
}

.nav-link, .nav-sublink {
    contain: layout style paint;
}
</style>
@endpush