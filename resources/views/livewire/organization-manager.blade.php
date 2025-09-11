{{-- 🚀 ORGANIZATION MANAGER - Ultra-Modern Enterprise Interface --}}
<div class="modern-organization-manager" x-data="{ isInitialized: false }" x-init="setTimeout(() => isInitialized = true, 100)">
    
    {{-- ✅ CSS ULTRA-MODERNE INTÉGRÉ --}}
    <style>
        .modern-organization-manager {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 32px 64px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            padding: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .stats-grid::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(45deg, transparent, transparent 2px, rgba(255,255,255,0.03) 2px, rgba(255,255,255,0.03) 4px);
            animation: movePattern 20s linear infinite;
        }
        
        @keyframes movePattern {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #fff, #e3f2fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .control-bar {
            padding: 2rem;
            background: linear-gradient(to right, #f8fafc, #e2e8f0);
            border-bottom: 1px solid #e2e8f0;
        }
        
        .search-section {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }
        
        .search-input {
            flex: 1;
            min-width: 300px;
            padding: 1rem 1.5rem;
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 1rem;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            outline: none;
        }
        
        .search-input:focus {
            border-color: #667eea;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }
        
        .modern-btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            outline: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: white;
            color: #4a5568;
            border: 2px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary:hover {
            border-color: #cbd5e0;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }
        
        .filters-panel {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            margin-top: 1rem;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            background: white;
            transition: all 0.2s ease;
        }
        
        .form-select:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .data-table {
            background: white;
            border-radius: 0 0 24px 24px;
            overflow: hidden;
        }
        
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table thead {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        }
        
        .table th {
            padding: 1.5rem;
            text-align: left;
            font-weight: 700;
            font-size: 0.9rem;
            color: #2d3748;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #e2e8f0;
            position: relative;
        }
        
        .sortable-header {
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            background: none;
            color: inherit;
            font: inherit;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .sortable-header:hover {
            color: #667eea;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f7fafc;
        }
        
        .table tbody tr:hover {
            background: linear-gradient(135deg, #f8fafc, #edf2f7);
            transform: scale(1.01);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .table td {
            padding: 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #f7fafc;
        }
        
        .org-avatar {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 1.2rem;
            margin-right: 1rem;
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }
        
        .org-info h4 {
            font-weight: 700;
            font-size: 1.1rem;
            color: #2d3748;
            margin: 0 0 0.25rem 0;
        }
        
        .org-info p {
            color: #718096;
            margin: 0;
            font-size: 0.9rem;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            gap: 0.5rem;
            border: 1px solid;
        }
        
        .status-active {
            background: #f0fff4;
            color: #22543d;
            border-color: #9ae6b4;
        }
        
        .status-pending {
            background: #fffaf0;
            color: #c05621;
            border-color: #fbb6ce;
        }
        
        .status-inactive {
            background: #fff5f5;
            color: #c53030;
            border-color: #feb2b2;
        }
        
        .status-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .stats-mini {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            font-size: 0.9rem;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .stat-item i {
            width: 1rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
        
        .action-btn {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-view {
            background: #ebf8ff;
            color: #3182ce;
        }
        
        .btn-view:hover {
            background: #bee3f8;
        }
        
        .btn-edit {
            background: #f0fff4;
            color: #38a169;
        }
        
        .btn-edit:hover {
            background: #c6f6d5;
        }
        
        .btn-delete {
            background: #fff5f5;
            color: #e53e3e;
        }
        
        .btn-delete:hover {
            background: #fed7d7;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #718096;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        
        .pagination-wrapper {
            padding: 2rem;
            background: #f8fafc;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .pagination-info {
            color: #718096;
            font-size: 0.9rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .modern-organization-manager {
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                padding: 1rem;
            }
            
            .control-bar {
                padding: 1rem;
            }
            
            .search-input {
                min-width: 100%;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Animations d'entrée */
        .fade-in {
            animation: fadeIn 0.6s ease forwards;
            opacity: 0;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-in-1 { animation-delay: 0.1s; }
        .slide-in-2 { animation-delay: 0.2s; }
        .slide-in-3 { animation-delay: 0.3s; }
        .slide-in-4 { animation-delay: 0.4s; }
    </style>

    <div class="main-container">
        {{-- ✅ HEADER AVEC STATISTIQUES ULTRA-MODERNES --}}
        <div class="glass-card fade-in slide-in-1">
            <div class="stats-grid">
                <div class="stat-card fade-in slide-in-1">
                    <div class="stat-number">{{ $stats['total'] ?? 0 }}</div>
                    <div class="stat-label">📊 Total</div>
                </div>
                <div class="stat-card fade-in slide-in-2">
                    <div class="stat-number">{{ $stats['active'] ?? 0 }}</div>
                    <div class="stat-label">✅ Actives</div>
                </div>
                <div class="stat-card fade-in slide-in-3">
                    <div class="stat-number">{{ $stats['pending'] ?? 0 }}</div>
                    <div class="stat-label">⏳ En attente</div>
                </div>
                <div class="stat-card fade-in slide-in-4">
                    <div class="stat-number">{{ $stats['inactive'] ?? 0 }}</div>
                    <div class="stat-label">❌ Inactives</div>
                </div>
            </div>
            
            {{-- ✅ BARRE DE CONTRÔLES MODERNE --}}
            <div class="control-bar">
                <div class="search-section">
                    <input 
                        wire:model.debounce.300ms="search" 
                        type="text" 
                        class="search-input"
                        placeholder="🔍 Rechercher par nom, email, ville..."
                        value="{{ $search ?? '' }}"
                    >
                    
                    <button wire:click="toggleFilters" class="modern-btn btn-secondary">
                        <i class="fas fa-sliders-h"></i>
                        Filtres
                        @if($showFilters ?? false)
                            <i class="fas fa-chevron-up"></i>
                        @else
                            <i class="fas fa-chevron-down"></i>
                        @endif
                    </button>
                    
                    <a href="{{ route('admin.organizations.create') }}" class="modern-btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Nouvelle Organisation
                    </a>
                </div>
                
                {{-- ✅ PANNEAU DE FILTRES MODERNE --}}
                @if($showFilters ?? false)
                    <div class="filters-panel" x-show="true" x-transition>
                        <div class="filters-grid">
                            <div class="form-group">
                                <label>Statut</label>
                                <select wire:model="statusFilter" class="form-select">
                                    <option value="all">Tous les statuts</option>
                                    <option value="active">✅ Active</option>
                                    <option value="pending">⏳ En attente</option>
                                    <option value="inactive">❌ Inactive</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Ville</label>
                                <select wire:model="cityFilter" class="form-select">
                                    <option value="all">Toutes les villes</option>
                                    @if(isset($cities) && is_iterable($cities))
                                        @foreach($cities as $city)
                                            <option value="{{ $city }}">{{ $city }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Par page</label>
                                <select wire:model="perPage" class="form-select">
                                    <option value="10">10 par page</option>
                                    <option value="25">25 par page</option>
                                    <option value="50">50 par page</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <button wire:click="resetFilters" class="modern-btn btn-secondary" style="width: 100%;">
                                    <i class="fas fa-undo"></i>
                                    Réinitialiser
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            {{-- ✅ TABLE ULTRA-MODERNE --}}
            <div class="data-table">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <button wire:click="sortBy('name')" class="sortable-header">
                                        <i class="fas fa-building text-primary"></i>
                                        Organisation
                                        @if(($sortField ?? '') === 'name')
                                            <i class="fas fa-sort-{{ ($sortDirection ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </button>
                                </th>
                                <th>
                                    <i class="fas fa-map-marker-alt text-danger mr-2"></i>
                                    Localisation
                                </th>
                                <th>
                                    <button wire:click="sortBy('status')" class="sortable-header">
                                        <i class="fas fa-toggle-on text-success"></i>
                                        Statut
                                        @if(($sortField ?? '') === 'status')
                                            <i class="fas fa-sort-{{ ($sortDirection ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </button>
                                </th>
                                <th>
                                    <i class="fas fa-chart-bar text-info mr-2"></i>
                                    Statistiques
                                </th>
                                <th>
                                    <button wire:click="sortBy('created_at')" class="sortable-header">
                                        <i class="fas fa-calendar text-warning"></i>
                                        Créée le
                                        @if(($sortField ?? '') === 'created_at')
                                            <i class="fas fa-sort-{{ ($sortDirection ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </button>
                                </th>
                                <th>
                                    <i class="fas fa-cog text-secondary mr-2"></i>
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($organizations) && is_iterable($organizations) && count($organizations) > 0)
                                @foreach($organizations as $index => $organization)
                                    <tr class="fade-in" style="animation-delay: {{ $index * 0.05 }}s" wire:key="org-{{ $organization->id ?? 'unknown' }}">
                                        <td>
                                            <div style="display: flex; align-items: center;">
                                                <div class="org-avatar">
                                                    {{ strtoupper(substr($organization->name ?? 'OR', 0, 2)) }}
                                                </div>
                                                <div class="org-info">
                                                    <h4>{{ $organization->name ?? 'N/A' }}</h4>
                                                    <p>
                                                        <i class="fas fa-envelope mr-1"></i>
                                                        {{ $organization->email ?? 'N/A' }}
                                                    </p>
                                                    @if(isset($organization->phone) && $organization->phone)
                                                        <p>
                                                            <i class="fas fa-phone mr-1"></i>
                                                            {{ $organization->phone }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div>
                                                <strong>{{ $organization->city ?? 'Non spécifiée' }}</strong>
                                                @if(isset($organization->address) && $organization->address)
                                                    <p style="color: #718096; margin: 0.25rem 0 0 0; font-size: 0.9rem;">
                                                        {{ Str::limit($organization->address, 50) }}
                                                    </p>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <td>
                                            @switch($organization->status ?? 'unknown')
                                                @case('active')
                                                    <span class="status-badge status-active">
                                                        <span class="status-dot" style="background: #38a169;"></span>
                                                        Active
                                                    </span>
                                                    @break
                                                @case('pending')
                                                    <span class="status-badge status-pending">
                                                        <span class="status-dot" style="background: #ed8936;"></span>
                                                        En attente
                                                    </span>
                                                    @break
                                                @case('inactive')
                                                    <span class="status-badge status-inactive">
                                                        <span class="status-dot" style="background: #e53e3e;"></span>
                                                        Inactive
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="status-badge">
                                                        <span class="status-dot" style="background: #a0aec0;"></span>
                                                        Inconnu
                                                    </span>
                                            @endswitch
                                        </td>
                                        
                                        <td>
                                            <div class="stats-mini">
                                                <div class="stat-item">
                                                    <i class="fas fa-users" style="color: #3182ce;"></i>
                                                    <span><strong>{{ $organization->users_count ?? 0 }}</strong> utilisateurs</span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="fas fa-car" style="color: #38a169;"></i>
                                                    <span><strong>{{ $organization->vehicles_count ?? 0 }}</strong> véhicules</span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="fas fa-id-card" style="color: #805ad5;"></i>
                                                    <span><strong>{{ $organization->drivers_count ?? 0 }}</strong> chauffeurs</span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            @if(isset($organization->created_at) && $organization->created_at)
                                                <div>
                                                    <strong>{{ $organization->created_at->format('d/m/Y') }}</strong>
                                                </div>
                                                <div style="color: #718096; font-size: 0.85rem;">
                                                    {{ $organization->created_at->diffForHumans() }}
                                                </div>
                                            @else
                                                <span style="color: #cbd5e0;">Non définie</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('admin.organizations.show', $organization->id ?? 0) }}" 
                                                   class="action-btn btn-view" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.organizations.edit', $organization->id ?? 0) }}" 
                                                   class="action-btn btn-edit" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="confirm('Supprimer cette organisation ?')" 
                                                        class="action-btn btn-delete" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <i class="fas fa-search"></i>
                                            <h3>Aucune organisation trouvée</h3>
                                            <p>
                                                @if(!empty($search ?? '') || ($statusFilter ?? 'all') !== 'all' || ($cityFilter ?? 'all') !== 'all')
                                                    Aucun résultat ne correspond à vos critères de recherche.
                                                @else
                                                    Il n'y a pas encore d'organisations enregistrées.
                                                @endif
                                            </p>
                                            <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                                                @if(!empty($search ?? '') || ($statusFilter ?? 'all') !== 'all' || ($cityFilter ?? 'all') !== 'all')
                                                    <button wire:click="resetFilters" class="modern-btn btn-secondary">
                                                        <i class="fas fa-undo mr-2"></i>Réinitialiser les filtres
                                                    </button>
                                                @endif
                                                <a href="{{ route('admin.organizations.create') }}" class="modern-btn btn-primary">
                                                    <i class="fas fa-plus mr-2"></i>Créer une organisation
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                {{-- ✅ PAGINATION MODERNE --}}
                @if(isset($organizations) && is_object($organizations) && method_exists($organizations, 'hasPages') && $organizations->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Affichage de <strong>{{ $organizations->firstItem() ?? 0 }}</strong> à 
                            <strong>{{ $organizations->lastItem() ?? 0 }}</strong> sur 
                            <strong>{{ $organizations->total() ?? 0 }}</strong> résultats
                        </div>
                        <div>
                            {{ $organizations->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ✅ SCRIPTS ULTRA-MODERNES --}}
<script>
document.addEventListener('livewire:load', function() {
    console.log('🚀 Ultra-Modern Organization Manager Loaded');
    
    // Animation des cartes au survol
    document.querySelectorAll('.stat-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Feedback visuel pour les actions Livewire
    Livewire.hook('message.sent', () => {
        document.body.style.cursor = 'progress';
    });
    
    Livewire.hook('message.processed', () => {
        document.body.style.cursor = 'default';
    });
});
</script>
