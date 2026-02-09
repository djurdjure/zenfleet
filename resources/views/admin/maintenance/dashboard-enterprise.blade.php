@extends('layouts.app')

@section('title', 'Dashboard Maintenance Enterprise')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
 <div class="container mx-auto px-4 py-8">
 <!-- En-tête Enterprise -->
 <div class="mb-8">
 <div class="flex items-center justify-between">
 <div>
 <h1 class="text-4xl font-bold text-slate-800 mb-2">
 🔧 Dashboard Maintenance Enterprise
 </h1>
 <p class="text-slate-600">Système de gestion de maintenance ultra-professionnel</p>
 </div>
 <div class="text-sm text-slate-500">
 Dernière mise à jour: {{ now()->format('d/m/Y H:i') }}
 </div>
 </div>
 </div>

 <!-- Statistiques Principales -->
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
 @if(isset($stats))
 <div class="bg-blue-50 rounded-xl shadow-lg p-6 border border-blue-200 border-l-4 border-blue-500">
 <div class="flex items-center">
 <div class="p-3 rounded-full bg-blue-100 text-blue-600">
 🚨
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-600">Alertes Totales</p>
 <p class="text-2xl font-bold text-gray-900">{{ $stats['total_alerts'] ?? '0' }}</p>
 </div>
 </div>
 </div>

 <div class="bg-red-50 rounded-xl shadow-lg p-6 border border-red-200 border-l-4 border-red-500">
 <div class="flex items-center">
 <div class="p-3 rounded-full bg-red-100 text-red-600">
 ⚠️
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-600">Alertes Critiques</p>
 <p class="text-2xl font-bold text-gray-900">{{ $stats['critical_alerts'] ?? '0' }}</p>
 </div>
 </div>
 </div>

 <div class="bg-green-50 rounded-xl shadow-lg p-6 border border-green-200 border-l-4 border-green-500">
 <div class="flex items-center">
 <div class="p-3 rounded-full bg-green-100 text-green-600">
 🔧
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-600">Opérations Actives</p>
 <p class="text-2xl font-bold text-gray-900">{{ $stats['active_operations'] ?? '0' }}</p>
 </div>
 </div>
 </div>

 <div class="bg-purple-50 rounded-xl shadow-lg p-6 border border-purple-200 border-l-4 border-purple-500">
 <div class="flex items-center">
 <div class="p-3 rounded-full bg-purple-100 text-purple-600">
 💰
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-600">Coût ce Mois</p>
 <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_cost_this_month'] ?? 0, 0, ',', ' ') }}€</p>
 </div>
 </div>
 </div>
 @endif
 </div>

 <!-- Graphiques et Analytics -->
 @if(isset($chartData))
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
 <div class="bg-white rounded-xl shadow-lg p-6">
 <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Répartition des Alertes</h3>
 <div
 id="alertsChart"
 class="h-64"
 data-zenfleet-chart
 data-chart-id="maintenance-enterprise-alerts"
 data-chart-type="donut"
 data-chart-height="260"
 data-chart-aria-label="Repartition des alertes de maintenance par priorite"
 data-chart-labels='@json(array_keys($chartData["alerts_by_priority"] ?? []))'
 data-chart-series='@json(array_values($chartData["alerts_by_priority"] ?? []))'
 data-chart-options='@json([
    "colors" => ["#ef4444", "#f59e0b", "#10b981", "#3b82f6"],
    "legend" => ["position" => "bottom"]
 ])'
 ></div>
 </div>

 <div class="bg-white rounded-xl shadow-lg p-6">
 <h3 class="text-lg font-semibold text-gray-800 mb-4">💰 Évolution des Coûts</h3>
 <div
 id="costsChart"
 class="h-64"
 data-zenfleet-chart
 data-chart-id="maintenance-enterprise-costs"
 data-chart-type="line"
 data-chart-height="260"
 data-chart-aria-label="Evolution mensuelle des couts de maintenance"
 data-chart-labels='@json(array_column($chartData["cost_evolution"] ?? [], "month"))'
 data-chart-series='@json([[
    "name" => "Coûts (€)",
    "data" => array_column($chartData["cost_evolution"] ?? [], "cost")
 ]])'
 data-chart-options='@json([
    "stroke" => ["curve" => "smooth", "width" => 3],
    "yaxis" => ["min" => 0]
 ])'
 ></div>
 </div>
 </div>
 @endif

 <!-- Alertes Critiques -->
 @if(isset($criticalAlerts) && $criticalAlerts->count() > 0)
 <div class="bg-white rounded-xl shadow-lg mb-8">
 <div class="px-6 py-4 border-b border-gray-200">
 <h3 class="text-lg font-semibold text-gray-800">🚨 Alertes Critiques</h3>
 </div>
 <div class="p-6">
 <div class="space-y-4">
 @foreach($criticalAlerts as $alert)
 <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border-l-4 border-red-500">
 <div>
 <p class="font-medium text-red-800">{{ $alert->title }}</p>
 <p class="text-sm text-red-600">{{ $alert->description }}</p>
 </div>
 <span class="px-3 py-1 text-xs font-medium bg-red-200 text-red-800 rounded-full">
 {{ $alert->priority }}
 </span>
 </div>
 @endforeach
 </div>
 </div>
 </div>
 @endif

 <!-- Maintenance à Venir -->
 @if(isset($upcomingMaintenance) && $upcomingMaintenance->count() > 0)
 <div class="bg-white rounded-xl shadow-lg">
 <div class="px-6 py-4 border-b border-gray-200">
 <h3 class="text-lg font-semibold text-gray-800">📅 Maintenance à Venir</h3>
 </div>
 <div class="p-6">
 <div class="space-y-4">
 @foreach($upcomingMaintenance as $maintenance)
 <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
 <div>
 <p class="font-medium text-blue-800">{{ $maintenance->vehicle->registration_plate ?? 'N/A' }}</p>
 <p class="text-sm text-blue-600">{{ $maintenance->maintenanceType->name ?? 'Maintenance' }}</p>
 </div>
 <span class="text-sm text-blue-700">
 {{ $maintenance->next_due_date ? $maintenance->next_due_date->format('d/m/Y') : 'Date TBD' }}
 </span>
 </div>
 @endforeach
 </div>
 </div>
 </div>
 @endif
 </div>
</div>

@endsection
