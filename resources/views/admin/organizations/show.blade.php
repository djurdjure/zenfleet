@extends('layouts.admin.app')

@section('title', $organization->name)

@push('styles')
<style>
.organization-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
}

.info-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    height: 100%;
    border: 1px solid #e5e7eb;
}

.metric-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    text-align: center;
    border-left: 4px solid var(--color);
    height: 100%;
    transition: transform 0.2s ease;
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.metric-value {
    font-size: 2rem;
    font-weight: bold;
    color: var(--color);
    margin-bottom: 0.5rem;
}

.metric-label {
    color: #6b7280;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.activity-item {
    padding: 1rem;
    border-left: 3px solid var(--color);
    margin-bottom: 1rem;
    background: #f8fafc;
    border-radius: 0 8px 8px 0;
    transition: background-color 0.2s ease;
}

.activity-item:hover {
    background-color: #f1f5f9;
}

.org-logo-large {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,0.2);
}

.org-logo-placeholder-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, rgba(255,255,255,0.2), rgba(255,255,255,0.1));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    font-weight: bold;
    border: 3px solid rgba(255,255,255,0.2);
}

.status-badge-large {
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-size: 0.75rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 500;
    color: #374151;
}

.info-value {
    color: #6b7280;
}

.progress-bar-custom {
    height: 8px;
    border-radius: 4px;
    background-color: #e5e7eb;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: var(--color);
    border-radius: 4px;
    transition: width 0.3s ease;
}

@media (max-width: 991px) {
    .organization-header {
        padding: 1.5rem;
    }
    
    .organization-header .d-flex {
        flex-direction: column;
        text-align: center;
        gap: 1rem !important;
    }
}
</style>
@endpush

@section('content')
<div class="organization-show">
    
    {{-- Header avec informations principales --}}
    <div class="organization-header">
        <div class="row align-items-center">
            <div class="col-md-2 text-center text-md-start">
                @if($organization->logo_path && Storage::disk('public')->exists($organization->logo_path))
                    <img src="{{ Storage::disk('public')->url($organization->logo_path) }}" 
                         alt="{{ $organization->name }}" 
                         class="org-logo-large">
                @else
                    <div class="org-logo-placeholder-large">
                        {{ strtoupper(substr($organization->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="col-md-7">
                <h1 class="text-white mb-2">{{ $organization->name }}</h1>
                @if($organization->legal_name && $organization->legal_name !== $organization->name)
                    <p class="text-white-50 mb-1">{{ $organization->legal_name }}</p>
                @endif
                @if($organization->description)
                    <p class="text-white-50 mb-2">{{ $organization->description }}</p>
                @endif
                <div class="d-flex align-items-center gap-3 text-white-50 small">
                    <span><i class="fas fa-calendar me-1"></i>Créée le {{ $organization->created_at->format('d/m/Y') }}</span>
                    @if($organization->siret)
                        <span><i class="fas fa-id-card me-1"></i>SIRET: {{ $organization->siret }}</span>
                    @endif
                </div>
            </div>
            <div class="col-md-3 text-center text-md-end">
                @if($organization->status === 'active')
                    <span class="status-badge-large bg-success text-white">
                        <i class="fas fa-check-circle me-1"></i>Active
                    </span>
                @elseif($organization->status === 'inactive')
                    <span class="status-badge-large bg-warning text-white">
                        <i class="fas fa-pause-circle me-1"></i>Inactive
                    </span>
                @else
                    <span class="status-badge-large bg-secondary text-white">
                        <i class="fas fa-clock me-1"></i>{{ ucfirst($organization->status) }}
                    </span>
                @endif

                <div class="mt-3">
                    <a href="{{ route('admin.organizations.edit', $organization) }}" 
                       class="btn btn-light btn-sm me-2">
                        <i class="fas fa-edit me-1"></i>Modifier
                    </a>
                    <a href="{{ route('admin.organizations.index') }}" 
                       class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Métriques principales --}}
        <div class="col-lg-8">
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="metric-card" style="--color: #10b981;">
                        <div class="metric-value">{{ $stats['users']['total'] }}</div>
                        <div class="metric-label">Utilisateurs</div>
                        <div class="small text-muted mt-1">
                            {{ $stats['users']['active'] }} actifs
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card" style="--color: #3b82f6;">
                        <div class="metric-value">{{ $stats['vehicles']['total'] }}</div>
                        <div class="metric-label">Véhicules</div>
                        <div class="small text-muted mt-1">
                            {{ $stats['vehicles']['available'] }} disponibles
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card" style="--color: #f59e0b;">
                        <div class="metric-value">{{ $stats['drivers']['total'] }}</div>
                        <div class="metric-label">Chauffeurs</div>
                        <div class="small text-muted mt-1">
                            {{ $stats['drivers']['active'] }} actifs
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card" style="--color: #8b5cf6;">
                        <div class="metric-value">{{ $stats['assignments']['active'] ?? 0 }}</div>
                        <div class="metric-label">Affectations</div>
                        <div class="small text-muted mt-1">en cours</div>
                    </div>
                </div>
            </div>

            {{-- Activité récente --}}
            <div class="info-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">
                        <i class="fas fa-history text-primary me-2"></i>
                        Activité Récente
                    </h4>
                    <span class="badge bg-light text-dark">{{ $recentActivity->count() }} événements</span>
                </div>
                
                <div class="activity-timeline">
                    @forelse($recentActivity as $activity)
                        <div class="activity-item" style="--color: 
                            @if($activity['color'] === 'success') #10b981
                            @elseif($activity['color'] === 'info') #3b82f6
                            @elseif($activity['color'] === 'warning') #f59e0b
                            @else #ef4444 @endif;">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-{{ $activity['icon'] }} me-3 mt-1" style="color: var(--color);"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-medium">{{ $activity['description'] }}</div>
                                    <small class="text-muted">{{ $activity['date']->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>Aucune activité récente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Informations détaillées --}}
        <div class="col-lg-4">
            
            {{-- Informations générales --}}
            <div class="info-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Informations Générales
                </h5>
                
                <div class="info-row">
                    <span class="info-label">Type</span>
                    <span class="info-value">{{ $organization->organization_type ?? 'Non défini' }}</span>
                </div>
                
                @if($organization->industry)
                <div class="info-row">
                    <span class="info-label">Secteur</span>
                    <span class="info-value">{{ $organization->industry }}</span>
                </div>
                @endif
                
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value">
                        <a href="mailto:{{ $organization->email }}">{{ $organization->email }}</a>
                    </span>
                </div>
                
                @if($organization->phone)
                <div class="info-row">
                    <span class="info-label">Téléphone</span>
                    <span class="info-value">{{ $organization->phone }}</span>
                </div>
                @endif
                
                @if($organization->website)
                <div class="info-row">
                    <span class="info-label">Site web</span>
                    <span class="info-value">
                        <a href="{{ $organization->website }}" target="_blank">
                            {{ parse_url($organization->website, PHP_URL_HOST) }}
                            <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    </span>
                </div>
                @endif
                
                <div class="info-row">
                    <span class="info-label">Localisation</span>
                    <span class="info-value">
                        {{ $organization->city }}, {{ $organization->country }}
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Fuseau horaire</span>
                    <span class="info-value">{{ $organization->timezone }}</span>
                </div>
            </div>

            {{-- Métriques de performance --}}
            <div class="info-card">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    Performance
                </h5>
                
                <div class="performance-metric mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small">Efficacité globale</span>
                        <span class="small fw-bold">{{ $performanceData['efficiency_score'] }}%</span>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" 
                             style="width: {{ $performanceData['efficiency_score'] }}%; --color: #10b981;"></div>
                    </div>
                </div>
                
                <div class="performance-metric mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small">Taux d'utilisation</span>
                        <span class="small fw-bold">{{ $performanceData['utilization_rate'] }}%</span>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" 
                             style="width: {{ $performanceData['utilization_rate'] }}%; --color: #3b82f6;"></div>
                    </div>
                </div>
                
                <div class="performance-metric mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small">Conformité maintenance</span>
                        <span class="small fw-bold">{{ $performanceData['maintenance_compliance'] }}%</span>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" 
                             style="width: {{ $performanceData['maintenance_compliance'] }}%; --color: #f59e0b;"></div>
                    </div>
                </div>
                
                <div class="performance-metric">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small">Satisfaction chauffeurs</span>
                        <span class="small fw-bold">{{ $performanceData['driver_satisfaction'] }}%</span>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" 
                             style="width: {{ $performanceData['driver_satisfaction'] }}%; --color: #8b5cf6;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des métriques au chargement
    const metricCards = document.querySelectorAll('.metric-card');
    metricCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Animation des barres de progression
    const progressBars = document.querySelectorAll('.progress-fill');
    progressBars.forEach((bar, index) => {
        const width = bar.style.width;
        bar.style.width = '0%';
        
        setTimeout(() => {
            bar.style.transition = 'width 1s ease';
            bar.style.width = width;
        }, 500 + (index * 200));
    });

    // Tooltips pour les métriques
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
