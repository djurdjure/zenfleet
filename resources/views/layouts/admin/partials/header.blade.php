{{-- Header Navigation --}}
<header class="admin-header">
    <div class="header-content">
        
        {{-- Left Section --}}
        <div class="header-left">
            <button type="button" class="sidebar-toggle-btn" onclick="AdminSidebar.toggle()">
                <i class="fas fa-bars"></i>
            </button>
            
            {{-- Breadcrumb --}}
            <nav class="breadcrumb-nav">
                @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
                    <ol class="breadcrumb">
                        @foreach($breadcrumbs as $breadcrumb)
                            @if($loop->last)
                                <li class="breadcrumb-item active">{{ $breadcrumb['title'] }}</li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                @endif
            </nav>
        </div>

        {{-- Right Section --}}
        <div class="header-right">
            
            {{-- Notifications --}}
            <div class="header-notifications">
                <button type="button" class="notification-btn" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
                <div class="dropdown-menu notification-dropdown">
                    <h6 class="dropdown-header">Notifications</h6>
                    <div class="notification-list">
                        <a href="#" class="notification-item">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            <div class="notification-content">
                                <p>Maintenance programmée</p>
                                <small>Il y a 2h</small>
                            </div>
                        </a>
                    </div>
                    <div class="dropdown-footer">
                        <a href="#" class="btn btn-sm btn-primary">Voir tout</a>
                    </div>
                </div>
            </div>

            {{-- User Menu --}}
            <div class="header-user">
                <div class="dropdown">
                    <button type="button" class="user-btn" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar_url ?? asset('images/default-avatar.png') }}" 
                             alt="{{ auth()->user()->name }}" 
                             class="user-avatar">
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user"></i> Mon Profil
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                            <i class="fas fa-cog"></i> Paramètres
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
