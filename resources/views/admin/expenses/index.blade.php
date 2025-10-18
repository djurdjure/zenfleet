{{-- resources/views/admin/expenses/index.blade.php --}}
{{-- 🚀 ZENFLEET EXPENSES DASHBOARD - Ultra Professional Enterprise Grade --}}
@extends('layouts.admin.catalyst')
@section('title', 'Gestion des Dépenses Enterprise - ZenFleet')

@push('styles')
<style>
/* 🎨 Enterprise-Grade Animations et Styles Ultra-Modernes */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card-enterprise {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.card-enterprise::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.6s ease;
}

.card-enterprise:hover::before {
    left: 100%;
}

.card-enterprise:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #cbd5e1;
}

.metric-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.filter-btn {
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.filter-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.filter-btn:hover::before {
    left: 100%;
}

.expenses-table {
    border-collapse: separate;
    border-spacing: 0;
}

.expenses-table tbody tr {
    transition: all 0.2s ease-in-out;
}

.expenses-table tbody tr:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.expenses-table thead th {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    backdrop-filter: blur(10px);
    position: sticky;
    top: 0;
    z-index: 10;
}

.status-indicator {
    position: relative;
    overflow: hidden;
}

.status-indicator::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.status-indicator:hover::before {
    left: 100%;
}
</style>
@endpush

@section('content')
<div class="fade-in">
    {{-- 🎯 En-tête Ultra-Professional Enterprise --}}
    <div class="mb-8">
        <div class="bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 rounded-2xl shadow-2xl p-8 text-white relative overflow-hidden">
            {{-- Effet de brillance --}}
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent animate-pulse"></div>

            <div class="relative z-10">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-4">
                            <div class="p-4 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl shadow-lg">
                                <x-icon icon="heroicons:credit-card" class="h-12 w-12 text-white" stroke-width="2" / />
                            </div>
                            <div>
                                <h1 class="text-4xl font-bold text-white mb-2">Gestion des Dépenses Enterprise</h1>
                                <p class="text-blue-100 text-lg font-medium">Contrôle financier & Analytics avancés</p>
                            </div>
                        </div>
                        <div class="mt-6 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-8">
                            <div class="mt-2 flex items-center text-sm text-blue-100">
                                <x-icon icon="heroicons:arrow-trending-up" class="mr-2 h-5 w-5 text-green-400" stroke-width="2" / />
                                Suivi temps réel des coûts
                            </div>
                            <div class="mt-2 flex items-center text-sm text-blue-100">
                                <x-icon icon="heroicons:shield-check" class="mr-2 h-5 w-5 text-cyan-400" stroke-width="2" / />
                                Validation multi-niveaux
                            </div>
                            <div class="mt-2 flex items-center text-sm text-blue-100">
                                <x-icon icon="heroicons:clock" class="mr-2 h-5 w-5 text-orange-400" stroke-width="2" / />
                                Dernière mise à jour: {{ now()->format('d/m/Y à H:i:s') }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 md:mt-0 md:ml-6">
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('admin.expenses.create') }}" class="inline-flex items-center px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 shadow-lg text-sm font-medium rounded-xl text-white hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-300">
                                <x-icon icon="heroicons:plus" class="mr-2 h-4 w-4" stroke-width="2" / />
                                Nouvelle Dépense
                            </a>
                            <button onclick="exportExpenses()" class="inline-flex items-center px-4 py-3 bg-white/10 backdrop-blur-sm border border-white/20 shadow-sm text-sm font-medium rounded-xl text-white hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all duration-300">
                                <x-icon icon="heroicons:arrow-down-tray" class="mr-2 h-4 w-4" stroke-width="2" / />
                                Exporter
                            </button>
                            <a href="{{ route('admin.expenses.analytics') }}" class="inline-flex items-center px-4 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 shadow-lg text-sm font-medium rounded-xl text-white hover:from-purple-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300">
                                <x-icon icon="heroicons:chart-bar" class="mr-2 h-4 w-4" stroke-width="2" / />
                                Analytics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 📊 Métriques Enterprise Ultra-Professionnelles --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-6 mb-8">
        {{-- Total Dépenses --}}
        <div class="metric-card relative bg-gradient-to-br from-blue-50 to-indigo-100 border-l-4 border-blue-500 p-6 shadow-xl">
            <div class="absolute top-4 right-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-icon icon="lucide:euro" class="w-6 h-6 text-white" stroke-width="2" / />
                </div>
            </div>
            <div class="pb-2">
                <h3 class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">Total Dépenses</h3>
                <div class="flex items-baseline space-x-1">
                    <span class="text-2xl font-bold text-blue-700">{{ number_format($stats['total_amount'] ?? 0, 0, ',', ' ') }}</span>
                    <span class="text-lg font-semibold text-blue-600">€</span>
                </div>
                <div class="mt-2 flex items-center text-xs text-blue-600">
                    <x-icon icon="heroicons:arrow-trending-up" class="w-3 h-3 mr-1" / />
                    {{ $stats['total_count'] ?? 0 }} dépenses
                </div>
            </div>
        </div>

        {{-- En Attente --}}
        <div class="metric-card relative bg-gradient-to-br from-yellow-50 to-orange-100 border-l-4 border-yellow-500 p-6 shadow-xl">
            <div class="absolute top-4 right-4">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-icon icon="heroicons:clock" class="w-6 h-6 text-white" stroke-width="2" / />
                </div>
            </div>
            <div class="pb-2">
                <h3 class="text-xs font-semibold text-yellow-600 uppercase tracking-wider mb-1">En Attente</h3>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-bold text-yellow-700">{{ $stats['pending_count'] ?? 0 }}</span>
                    @if(($stats['pending_count'] ?? 0) > 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 animate-pulse">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>
                            Action requise
                        </span>
                    @else
                        <span class="text-xs text-green-600 font-medium">✓ À jour</span>
                    @endif
                </div>
                <div class="mt-2 flex items-center text-xs text-yellow-600">
                    <x-icon icon="heroicons:user"-check class="w-3 h-3 mr-1" / />
                    Validation en cours
                </div>
            </div>
        </div>

        {{-- Approuvées --}}
        <div class="metric-card relative bg-gradient-to-br from-green-50 to-emerald-100 border-l-4 border-green-500 p-6 shadow-xl">
            <div class="absolute top-4 right-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-icon icon="heroicons:check-circle" class="w-6 h-6 text-white" stroke-width="2" / />
                </div>
            </div>
            <div class="pb-2">
                <h3 class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-1">Approuvées</h3>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-bold text-green-700">{{ $stats['approved_count'] ?? 0 }}</span>
                    <span class="text-xs text-gray-600">validées</span>
                </div>
                <div class="mt-2 flex items-center text-xs text-green-600">
                    <x-icon icon="heroicons:shield-check" class="w-3 h-3 mr-1" / />
                    Conformes
                </div>
            </div>
        </div>

        {{-- Rejetées --}}
        <div class="metric-card relative bg-gradient-to-br from-red-50 to-rose-100 border-l-4 border-red-500 p-6 shadow-xl">
            <div class="absolute top-4 right-4">
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-icon icon="heroicons:x-circle" class="w-6 h-6 text-white" stroke-width="2" / />
                </div>
            </div>
            <div class="pb-2">
                <h3 class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-1">Rejetées</h3>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-bold text-red-700">{{ $stats['rejected_count'] ?? 0 }}</span>
                    <span class="text-xs text-gray-600">refusées</span>
                </div>
                <div class="mt-2 flex items-center text-xs text-red-600">
                    <x-icon icon="heroicons:exclamation-triangle" class="w-3 h-3 mr-1" / />
                    Non conformes
                </div>
            </div>
        </div>

        {{-- Ce Mois --}}
        <div class="metric-card relative bg-gradient-to-br from-purple-50 to-violet-100 border-l-4 border-purple-500 p-6 shadow-xl">
            <div class="absolute top-4 right-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-icon icon="heroicons:calendar" class="w-6 h-6 text-white" stroke-width="2" / />
                </div>
            </div>
            <div class="pb-2">
                <h3 class="text-xs font-semibold text-purple-600 uppercase tracking-wider mb-1">Ce Mois</h3>
                <div class="flex items-baseline space-x-1">
                    <span class="text-2xl font-bold text-purple-700">{{ number_format($stats['this_month_amount'] ?? 0, 0, ',', ' ') }}</span>
                    <span class="text-lg font-semibold text-purple-600">€</span>
                </div>
                <div class="mt-2 flex items-center text-xs text-purple-600">
                    <x-icon icon="heroicons:arrow-trending-up" class="w-3 h-3 mr-1" / />
                    {{ now()->format('F Y') }}
                </div>
            </div>
        </div>

        {{-- Moyenne --}}
        <div class="metric-card relative bg-gradient-to-br from-teal-50 to-cyan-100 border-l-4 border-teal-500 p-6 shadow-xl">
            <div class="absolute top-4 right-4">
                <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-icon icon="heroicons:chart-bar" class="w-6 h-6 text-white" stroke-width="2" / />
                </div>
            </div>
            <div class="pb-2">
                <h3 class="text-xs font-semibold text-teal-600 uppercase tracking-wider mb-1">Moyenne</h3>
                <div class="flex items-baseline space-x-1">
                    <span class="text-2xl font-bold text-teal-700">{{ number_format($stats['average_amount'] ?? 0, 0, ',', ' ') }}</span>
                    <span class="text-lg font-semibold text-teal-600">€</span>
                </div>
                <div class="mt-2 flex items-center text-xs text-teal-600">
                    <x-icon icon="heroicons:calculator" class="w-3 h-3 mr-1" / />
                    Par dépense
                </div>
            </div>
        </div>

        {{-- Budget --}}
        <div class="metric-card relative bg-gradient-to-br from-indigo-50 to-blue-100 border-l-4 border-indigo-500 p-6 shadow-xl">
            <div class="absolute top-4 right-4">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-icon icon="heroicons:chart-pie" class="w-6 h-6 text-white" stroke-width="2" / />
                </div>
            </div>
            <div class="pb-2">
                <h3 class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">Budget</h3>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-bold text-indigo-700">85</span>
                    <span class="text-xs text-gray-600">% utilisé</span>
                </div>
                <div class="mt-2 flex items-center text-xs text-indigo-600">
                    <x-icon icon="lucide:target" class="w-3 h-3 mr-1" / />
                    Dans les limites
                </div>
            </div>
        </div>
    </div>

    {{-- 🔍 Filtres Enterprise Ultra-Avancés --}}
    <div class="card-enterprise p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-3 bg-blue-500 rounded-xl shadow-lg">
                    <x-icon icon="heroicons:funnel" class="h-6 w-6 text-white" stroke-width="2" / />
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Filtres Avancés</h3>
                    <p class="text-sm text-gray-600">Affinage précis des données de dépenses</p>
                </div>
            </div>
            <button onclick="clearFilters()" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                <x-icon icon="heroicons:x-mark" class="w-4 h-4 inline mr-1" / />
                Effacer
            </button>
        </div>

        <form method="GET" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Véhicule --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Véhicule</label>
                    <select name="vehicle_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les véhicules</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ $filters['vehicle_id'] == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Type de dépense --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de dépense</label>
                    <select name="expense_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les types</option>
                        @foreach($expenseTypes ?? [] as $key => $label)
                            <option value="{{ $key }}" {{ $filters['expense_type'] == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Statut --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="all" {{ $filters['status'] == 'all' ? 'selected' : '' }}>Tous</option>
                        <option value="pending" {{ $filters['status'] == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="approved" {{ $filters['status'] == 'approved' ? 'selected' : '' }}>Approuvées</option>
                        <option value="rejected" {{ $filters['status'] == 'rejected' ? 'selected' : '' }}>Rejetées</option>
                    </select>
                </div>

                {{-- Recherche --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ $filters['search'] }}"
                               placeholder="Référence, description..."
                               class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <x-icon icon="heroicons:magnifying-glass" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" / />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Date début --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Date fin --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Montant minimum --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Montant minimum</label>
                    <div class="relative">
                        <input type="number" name="amount_min" value="{{ $filters['amount_min'] }}"
                               step="0.01" min="0" placeholder="0.00"
                               class="w-full pr-8 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">€</span>
                    </div>
                </div>

                {{-- Montant maximum --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Montant maximum</label>
                    <div class="relative">
                        <input type="number" name="amount_max" value="{{ $filters['amount_max'] }}"
                               step="0.01" min="0" placeholder="999999.99"
                               class="w-full pr-8 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">€</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="flex items-center space-x-3">
                    <button type="submit" class="filter-btn inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300">
                        <x-icon icon="heroicons:magnifying-glass" class="w-4 h-4 mr-2" / />
                        Filtrer
                    </button>
                    <a href="{{ route('admin.expenses.index') }}" class="filter-btn inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-300">
                        <x-icon icon="heroicons:arrow-path" class="w-4 h-4 mr-2" / />
                        Réinitialiser
                    </a>
                </div>
                <div class="text-sm text-gray-600">
                    {{ $expenses->total() }} résultat(s) trouvé(s)
                </div>
            </div>
        </form>
    </div>

    {{-- 📋 Tableau des Dépenses Ultra-Professionnel --}}
    <div class="card-enterprise overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-500 rounded-lg shadow-lg">
                        <x-icon icon="heroicons:table-cells" class="h-5 w-5 text-white" stroke-width="2" / />
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Liste des Dépenses</h3>
                        <p class="text-sm text-gray-600">Gestion complète des dépenses de flotte</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">{{ $expenses->count() }} sur {{ $expenses->total() }}</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="expenses-table min-w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Référence</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Véhicule</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <x-icon icon="heroicons:receipt-percent" class="w-5 h-5 text-blue-600" / />
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $expense->reference_number ?? 'EXP-' . $expense->id }}</div>
                                    <div class="text-xs text-gray-500">{{ $expense->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                    <x-icon icon="lucide:car" class="w-4 h-4 text-gray-600" / />
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $expense->vehicle->registration_plate ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $expense->vehicle->brand ?? '' }} {{ $expense->vehicle->model ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $expense->category_label ?? 'Non défini' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $expense->expense_date ? $expense->expense_date->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($expense->total_ttc ?? 0, 2, ',', ' ') }} €</div>
                            @if($expense->amount_ht != $expense->total_ttc)
                                <div class="text-xs text-gray-500">HT: {{ number_format($expense->amount_ht ?? 0, 2, ',', ' ') }} €</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $status = $expense->status_badge ?? ['color' => 'gray', 'label' => 'Inconnu'];
                                $statusClass = match($status['color']) {
                                    'green' => 'bg-green-100 text-green-800',
                                    'yellow' => 'bg-yellow-100 text-yellow-800',
                                    'red' => 'bg-red-100 text-red-800',
                                    'blue' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="status-indicator inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $status['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.expenses.show', $expense) }}" class="text-blue-600 hover:text-blue-900 transition-colors">
                                    <x-icon icon="heroicons:eye" class="w-4 h-4" / />
                                </a>
                                @if($expense->needs_approval && !$expense->approved)
                                    <button onclick="approveExpense({{ $expense->id }})" class="text-green-600 hover:text-green-900 transition-colors">
                                        <x-icon icon="heroicons:check" class="w-4 h-4" / />
                                    </button>
                                @endif
                                <a href="{{ route('admin.expenses.edit', $expense) }}" class="text-amber-600 hover:text-amber-900 transition-colors">
                                    <x-icon icon="heroicons:pencil" class="w-4 h-4" / />
                                </a>
                                <button onclick="deleteExpense({{ $expense->id }})" class="text-red-600 hover:text-red-900 transition-colors">
                                    <x-icon icon="heroicons:trash" class="w-4 h-4" / />
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <x-icon icon="heroicons:inbox" class="h-12 w-12 text-gray-400 mb-4" / />
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune dépense trouvée</h3>
                                <p class="text-gray-600 mb-4">Commencez par ajouter une nouvelle dépense</p>
                                <a href="{{ route('admin.expenses.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <x-icon icon="heroicons:plus" class="w-4 h-4 mr-2" / />
                                    Nouvelle dépense
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($expenses->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Scripts JavaScript Enterprise --}}
@push('scripts')
<script>
// 🔄 Fonction d'export
function exportExpenses() {
    // Implementation d'export
    console.log('Export des dépenses...');
}

// ✅ Fonction d'approbation
function approveExpense(expenseId) {
    if (confirm('Êtes-vous sûr de vouloir approuver cette dépense ?')) {
        // Implementation de l'approbation
        console.log('Approbation de la dépense:', expenseId);
    }
}

// 🗑️ Fonction de suppression
function deleteExpense(expenseId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette dépense ? Cette action est irréversible.')) {
        // Implementation de la suppression
        console.log('Suppression de la dépense:', expenseId);
    }
}

// 🧹 Fonction de nettoyage des filtres
function clearFilters() {
    window.location.href = '{{ route('admin.expenses.index') }}';
}

// 🎨 Animations au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Animation des cartes métriques
    const metricCards = document.querySelectorAll('.metric-card');
    metricCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Animation des lignes du tableau
    const tableRows = document.querySelectorAll('.expenses-table tbody tr');
    tableRows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-10px)';
        setTimeout(() => {
            row.style.transition = 'all 0.4s ease-out';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, 300 + index * 50);
    });
});
</script>
@endpush
@endsection