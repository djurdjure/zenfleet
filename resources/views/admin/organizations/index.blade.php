{{-- resources/views/admin/organizations/index.blade.php --}}
@extends('layouts.admin.app')
@section('title', 'Organisations')

@push('styles')
<style>
    /* Design System Enterprise ZenFleet */
    .zenfleet-card { @apply bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100/50 hover:shadow-xl transition-all duration-500; }
    .zenfleet-glass { @apply bg-white/80 backdrop-blur-md border border-white/20; }
    .zenfleet-gradient { @apply bg-gradient-to-br from-slate-50 to-gray-100/50; }
    .zenfleet-stat { @apply text-center p-6 rounded-2xl transition-all duration-300 hover:scale-[1.02]; }
    .zenfleet-badge { @apply inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium; }
    .zenfleet-input { @apply px-4 py-3 border border-gray-200/60 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all duration-200; }
    .zenfleet-btn { @apply inline-flex items-center gap-2 px-6 py-3 rounded-xl font-medium transition-all duration-200; }
    .zenfleet-btn-primary { @apply zenfleet-btn bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl; }
    .zenfleet-table-header { @apply sticky top-0 z-10 bg-gray-50/95 backdrop-blur-sm; }
</style>
@endpush

@section('content')
<div class="zenfleet-gradient min-h-screen -m-6 p-6">
    <!-- Header avec breadcrumbs -->
    <div class="flex flex-col gap-6 mb-8">
        <x-breadcrumb :items="[
            ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['title' => 'Organisations', 'active' => true]
        ]" />
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Organisations</h1>
                <p class="text-gray-600">Gérez les organisations de votre plateforme</p>
            </div>
            <div class="flex items-center gap-3">
                <button id="refreshBtn" class="zenfleet-btn bg-white hover:bg-gray-50 text-gray-700 border border-gray-200">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <a href="{{ route('admin.organizations.create') }}" class="zenfleet-btn-primary">
                    <i class="fas fa-plus"></i>
                    Nouvelle organisation
                </a>
            </div>
        </div>
    </div>

    <!-- Stats cards avec animations -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        @foreach([
            'total' => ['label' => 'Total', 'color' => 'blue', 'icon' => 'building'],
            'active' => ['label' => 'Actives', 'color' => 'green', 'icon' => 'check-circle'],
            'pending' => ['label' => 'En attente', 'color' => 'yellow', 'icon' => 'clock'],
            'inactive' => ['label' => 'Inactives', 'color' => 'red', 'icon' => 'times-circle']
        ] as $key => $config)
            <div class="zenfleet-card zenfleet-stat bg-gradient-to-br from-{{ $config['color'] }}-50 to-{{ $config['color'] }}-100/30">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-{{ $config['icon'] }} text-2xl text-{{ $config['color'] }}-500"></i>
                    <span class="text-xs text-{{ $config['color'] }}-600 font-medium uppercase tracking-wider">{{ $config['label'] }}</span>
                </div>
                <div class="text-3xl font-bold text-{{ $config['color'] }}-700 mb-2" x-data="{ count: 0 }" 
                     x-init="$nextTick(() => { 
                         const target = {{ $stats[$key] ?? 0 }}; 
                         const increment = target / 50; 
                         const timer = setInterval(() => { 
                             count = Math.min(count + increment, target); 
                             if (count >= target) clearInterval(timer); 
                         }, 20); 
                     })">
                    <span x-text="Math.floor(count)"></span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Composant Livewire pour la table -->
    @livewire('admin.organization-table', ['initialFilters' => request()->only(['search', 'status', 'country'])])
</div>

@push('scripts')
<script>
document.getElementById('refreshBtn').addEventListener('click', () => {
    Livewire.emit('refreshTable');
});
</script>
@endpush
@endsection
