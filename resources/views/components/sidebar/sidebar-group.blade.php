{{-- 
    🚀 ZENFLEET - Composant Sidebar Group Optimisé
    Correction : Gestion professionnelle de la variable $active
--}}
@props([
    'title' => '',
    'icon' => null,
    'active' => false,
    'collapsed' => false,
    'permission' => null,
    'routes' => []
])

{{-- Vérification des permissions si spécifiées --}}
@can($permission ?? 'view-sidebar')
    @php
        // Détection intelligente du statut actif
        $isActive = $active || collect($routes)->contains(function($route) {
            return request()->routeIs($route);
        });
        
        // Classes CSS dynamiques
        $groupClasses = $isActive ? 'sidebar-group active' : 'sidebar-group';
        $iconClasses = $isActive ? 'sidebar-icon active' : 'sidebar-icon';
    @endphp

    <div class="{{ $groupClasses }}" data-group="{{ Str::slug($title) }}">
        @if($title)
            <div class="sidebar-group-header" 
                 onclick="toggleSidebarGroup('{{ Str::slug($title) }}')"
                 role="button" 
                 tabindex="0"
                 aria-expanded="{{ $collapsed ? 'false' : 'true' }}">
                
                @if($icon)
                    <i class="{{ $iconClasses }} {{ $icon }}"></i>
                @endif
                
                <span class="sidebar-group-title">{{ $title }}</span>
                
                <i class="sidebar-group-arrow fas fa-chevron-{{ $collapsed ? 'right' : 'down' }}"></i>
            </div>
        @endif

        <div class="sidebar-group-content {{ $collapsed ? 'collapsed' : '' }}">
            {{ $slot }}
        </div>
    </div>
@endcan

@push('scripts')
<script>
function toggleSidebarGroup(groupSlug) {
    const group = document.querySelector(`[data-group="${groupSlug}"]`);
    const content = group.querySelector('.sidebar-group-content');
    const arrow = group.querySelector('.sidebar-group-arrow');
    
    content.classList.toggle('collapsed');
    arrow.classList.toggle('fa-chevron-right');
    arrow.classList.toggle('fa-chevron-down');
    
    // Sauvegarde de l'état dans localStorage
    const isCollapsed = content.classList.contains('collapsed');
    localStorage.setItem(`sidebar-group-${groupSlug}`, isCollapsed ? 'collapsed' : 'expanded');
}

// Restauration de l'état au chargement
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-group]').forEach(group => {
        const groupSlug = group.dataset.group;
        const savedState = localStorage.getItem(`sidebar-group-${groupSlug}`);
        
        if (savedState === 'collapsed') {
            const content = group.querySelector('.sidebar-group-content');
            const arrow = group.querySelector('.sidebar-group-arrow');
            
            content.classList.add('collapsed');
            arrow.classList.remove('fa-chevron-down');
            arrow.classList.add('fa-chevron-right');
        }
    });
});
</script>
@endpush
