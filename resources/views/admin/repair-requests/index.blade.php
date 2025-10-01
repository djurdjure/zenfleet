@extends('layouts.admin')

@section('title', '🔧 Réparations Enterprise')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50">
    {{-- 🎯 Header Enterprise Ultra-Professionnel --}}
    <div class="relative bg-gradient-to-r from-blue-900 via-indigo-900 to-purple-900 px-6 py-12 shadow-2xl">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="relative mx-auto max-w-7xl">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="p-3 bg-gradient-to-r from-amber-400 to-orange-500 rounded-xl shadow-lg">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-white tracking-tight">
                                Module Réparations
                                <span class="text-amber-400">Enterprise</span>
                            </h1>
                            <p class="mt-2 text-xl text-blue-200">
                                Workflow d'approbation et gestion avancée des réparations
                            </p>
                        </div>
                    </div>

                    {{-- 📊 Quick Stats --}}
                    <div class="flex flex-wrap gap-4 mt-6">
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg px-4 py-2">
                            <div class="text-2xl font-bold text-white">{{ $stats['total'] ?? 0 }}</div>
                            <div class="text-blue-200 text-sm">Total</div>
                        </div>
                        <div class="bg-amber-500/20 backdrop-blur-sm rounded-lg px-4 py-2">
                            <div class="text-2xl font-bold text-amber-300">{{ $stats['pending'] ?? 0 }}</div>
                            <div class="text-amber-200 text-sm">En attente</div>
                        </div>
                        <div class="bg-red-500/20 backdrop-blur-sm rounded-lg px-4 py-2">
                            <div class="text-2xl font-bold text-red-300">{{ $stats['urgent'] ?? 0 }}</div>
                            <div class="text-red-200 text-sm">Urgentes</div>
                        </div>
                        <div class="bg-blue-500/20 backdrop-blur-sm rounded-lg px-4 py-2">
                            <div class="text-2xl font-bold text-blue-300">{{ $stats['in_progress'] ?? 0 }}</div>
                            <div class="text-blue-200 text-sm">En cours</div>
                        </div>
                        <div class="bg-green-500/20 backdrop-blur-sm rounded-lg px-4 py-2">
                            <div class="text-2xl font-bold text-green-300">{{ $stats['completed_this_month'] ?? 0 }}</div>
                            <div class="text-green-200 text-sm">Ce mois</div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 sm:mt-0 sm:ml-16 flex space-x-4">
                    <a href="{{ route('admin.repair-requests.dashboard') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform transition-all hover:scale-105">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.repair-requests.create') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-3 text-base font-semibold text-white shadow-xl hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transform transition-all hover:scale-105">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nouvelle Demande
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-6 py-8 space-y-8">
        {{-- 🔍 Filtres Avancés Enterprise --}}
        <div class="bg-white/70 backdrop-blur-sm shadow-2xl rounded-2xl border border-white/20">
            <div class="px-8 py-6 border-b border-gray-200/50">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg class="h-6 w-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
                    </svg>
                    Filtres de Recherche Avancés
                </h3>
            </div>
            <div class="px-8 py-6">
                <form method="GET" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                    {{-- Recherche Globale --}}
                    <div class="col-span-full lg:col-span-2">
                        <label for="search" class="block text-sm font-bold text-gray-700 mb-2">🔍 Recherche Globale</label>
                        <div class="relative">
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Description, véhicule, fournisseur..."
                                   class="block w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Statut --}}
                    <div>
                        <label for="status" class="block text-sm font-bold text-gray-700 mb-2">📊 Statut</label>
                        <select name="status" id="status" class="block w-full py-3 border-2 border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" {{ request('status') === 'en_attente' ? 'selected' : '' }}>🔄 En attente</option>
                            <option value="accord_initial" {{ request('status') === 'accord_initial' ? 'selected' : '' }}>✅ Accord initial</option>
                            <option value="accordee" {{ request('status') === 'accordee' ? 'selected' : '' }}>✅ Accordée</option>
                            <option value="refusee" {{ request('status') === 'refusee' ? 'selected' : '' }}>❌ Refusée</option>
                            <option value="en_cours" {{ request('status') === 'en_cours' ? 'selected' : '' }}>🔧 En cours</option>
                            <option value="terminee" {{ request('status') === 'terminee' ? 'selected' : '' }}>🎯 Terminée</option>
                        </select>
                    </div>

                    {{-- Priorité --}}
                    <div>
                        <label for="priority" class="block text-sm font-bold text-gray-700 mb-2">⚡ Priorité</label>
                        <select name="priority" id="priority" class="block w-full py-3 border-2 border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all">
                            <option value="">Toutes les priorités</option>
                            <option value="faible" {{ request('priority') === 'faible' ? 'selected' : '' }}>🟢 Faible</option>
                            <option value="normale" {{ request('priority') === 'normale' ? 'selected' : '' }}>🟡 Normale</option>
                            <option value="elevee" {{ request('priority') === 'elevee' ? 'selected' : '' }}>🟠 Élevée</option>
                            <option value="urgente" {{ request('priority') === 'urgente' ? 'selected' : '' }}>🔴 Urgente</option>
                        </select>
                    </div>

                    {{-- Véhicule --}}
                    <div>
                        <label for="vehicle_id" class="block text-sm font-bold text-gray-700 mb-2">🚗 Véhicule</label>
                        <select name="vehicle_id" id="vehicle_id" class="block w-full py-3 border-2 border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all">
                            <option value="">Tous les véhicules</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-end space-x-3">
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl shadow-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition-all hover:scale-105">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Filtrer
                        </button>
                        <a href="{{ route('admin.repair-requests.index') }}" class="inline-flex items-center px-6 py-3 border-2 border-gray-300 text-sm font-bold rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition-all hover:scale-105">
                            Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- 📋 Liste des Demandes Ultra-Professionnelle --}}
        <div class="bg-white/70 backdrop-blur-sm shadow-2xl rounded-2xl border border-white/20 overflow-hidden">
            @if($repairRequests->count() > 0)
                <div class="px-8 py-6 border-b border-gray-200/50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900">
                            📋 {{ $repairRequests->total() }} Demande{{ $repairRequests->total() > 1 ? 's' : '' }} de Réparation
                        </h3>
                        <div class="text-sm text-gray-500">
                            Résultats {{ $repairRequests->firstItem() }} à {{ $repairRequests->lastItem() }} sur {{ $repairRequests->total() }}
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/50">
                        <thead class="bg-gradient-to-r from-gray-50 to-blue-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    🔧 Demande
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    🚗 Véhicule
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    ⚡ Priorité
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    📊 Statut
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    👤 Demandeur
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    📅 Date
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    💰 Coût
                                </th>
                                <th scope="col" class="relative px-6 py-4">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white/50 divide-y divide-gray-200/30">
                            @foreach($repairRequests as $request)
                                <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-orange-400 to-red-500 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">
                                                    #{{ $request->id }}
                                                </div>
                                                <div class="text-sm text-gray-600 mt-1 max-w-xs">
                                                    {{ Str::limit($request->description, 50) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $request->vehicle->registration_plate }}</div>
                                        <div class="text-sm text-gray-600">{{ $request->vehicle->brand }} {{ $request->vehicle->model }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $priorityConfig = [
                                                'faible' => ['bg-green-100', 'text-green-800', '🟢'],
                                                'normale' => ['bg-yellow-100', 'text-yellow-800', '🟡'],
                                                'elevee' => ['bg-orange-100', 'text-orange-800', '🟠'],
                                                'urgente' => ['bg-red-100', 'text-red-800', '🔴']
                                            ];
                                            $config = $priorityConfig[$request->priority] ?? ['bg-gray-100', 'text-gray-800', '⚪'];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config[0] }} {{ $config[1] }}">
                                            {{ $config[2] }} {{ ucfirst($request->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusConfig = [
                                                'en_attente' => ['bg-yellow-100', 'text-yellow-800', '🔄'],
                                                'accord_initial' => ['bg-blue-100', 'text-blue-800', '✅'],
                                                'accordee' => ['bg-green-100', 'text-green-800', '✅'],
                                                'refusee' => ['bg-red-100', 'text-red-800', '❌'],
                                                'en_cours' => ['bg-indigo-100', 'text-indigo-800', '🔧'],
                                                'terminee' => ['bg-emerald-100', 'text-emerald-800', '🎯']
                                            ];
                                            $config = $statusConfig[$request->status] ?? ['bg-gray-100', 'text-gray-800', '⚪'];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config[0] }} {{ $config[1] }}">
                                            {{ $config[2] }} {{ str_replace('_', ' ', ucfirst($request->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $request->requester->name }}</div>
                                        <div class="text-sm text-gray-600">{{ $request->requester->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="font-bold">{{ $request->requested_at->format('d/m/Y') }}</div>
                                        <div class="text-gray-600">{{ $request->requested_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            @if($request->estimated_cost)
                                                <div class="font-bold text-blue-900">{{ number_format($request->estimated_cost, 0, ',', ' ') }} DA</div>
                                                <div class="text-gray-600">Estimé</div>
                                            @endif
                                            @if($request->actual_cost)
                                                <div class="font-bold text-green-900">{{ number_format($request->actual_cost, 0, ',', ' ') }} DA</div>
                                                <div class="text-gray-600">Réel</div>
                                            @endif
                                            @if(!$request->estimated_cost && !$request->actual_cost)
                                                <div class="text-gray-400">Non estimé</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('admin.repair-requests.show', $request) }}"
                                               class="text-indigo-600 hover:text-indigo-900 font-bold">
                                                👁️ Voir
                                            </a>
                                            @if($request->status === 'en_attente')
                                                <a href="{{ route('admin.repair-requests.edit', $request) }}"
                                                   class="text-blue-600 hover:text-blue-900 font-bold">
                                                    ✏️ Modifier
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-8 py-6 border-t border-gray-200/50">
                    {{ $repairRequests->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="mx-auto h-24 w-24 bg-gradient-to-r from-gray-200 to-gray-300 rounded-full flex items-center justify-center mb-4">
                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Aucune demande de réparation</h3>
                    <p class="text-gray-600 mb-6">Aucune demande ne correspond aux critères sélectionnés.</p>
                    <a href="{{ route('admin.repair-requests.create') }}"
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Créer une demande
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const selects = document.querySelectorAll('#status, #priority, #vehicle_id');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Enhanced search with debounce
    const searchInput = document.getElementById('search');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 800);
        });
    }
});
</script>
@endpush
@endsection