@extends('layouts.admin.catalyst')

@section('title', $module . ' - En Développement')

@section('content')
{{-- 🚧 Header Development Module --}}
<div class="zenfleet-development-header">
 <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
 <div class="min-w-0 flex-1">
 <h2 class="text-3xl font-black leading-7 text-gray-900 sm:truncate sm:text-4xl sm:tracking-tight">
 <span class="bg-gradient-to-r from-amber-600 via-orange-600 to-red-700 bg-clip-text text-transparent">
 🚧 {{ $module }}
 </span>
 </h2>
 <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
 <div class="mt-2 flex items-center text-sm text-gray-500">
 <i class="fas fa-code mr-1.5 h-5 w-5 flex-shrink-0 text-amber-500"></i>
 Module en cours de développement
 </div>
 <div class="mt-2 flex items-center text-sm text-gray-500">
 <i class="fas fa-rocket mr-1.5 h-5 w-5 flex-shrink-0 text-blue-500"></i>
 ZenFleet Enterprise v3.0
 </div>
 </div>
 </div>
 </div>
</div>

{{-- 🔥 Development Status Card --}}
<div class="mt-8">
 <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 border-2 border-amber-200 shadow-2xl">
 <div class="absolute inset-0 bg-gradient-to-r from-amber-500/10 via-orange-500/10 to-red-500/10"></div>

 <div class="relative p-8 sm:p-12">
 <div class="text-center">
 {{-- Construction Icon --}}
 <div class="mx-auto w-32 h-32 bg-gradient-to-br from-amber-500 via-orange-500 to-red-600 rounded-full flex items-center justify-center mb-8 shadow-2xl animate-pulse">
 <i class="fas fa-hard-hat text-white text-6xl"></i>
 </div>

 {{-- Title --}}
 <h3 class="text-4xl font-black text-gray-900 mb-4">
 Module <span class="text-amber-600">{{ $module }}</span> en Développement
 </h3>

 {{-- Description --}}
 <p class="text-xl text-gray-700 mb-8 max-w-2xl mx-auto leading-relaxed">
 Notre équipe de développement travaille activement sur ce module pour vous offrir une expérience ultra-professionnelle de niveau entreprise.
 </p>

 {{-- Features Coming Soon --}}
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
 <div class="bg-white/80 rounded-xl p-6 shadow-lg border border-amber-200/50">
 <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
 <i class="fas fa-chart-line text-white text-xl"></i>
 </div>
 <h4 class="font-bold text-gray-900 mb-2">Analytics Avancés</h4>
 <p class="text-sm text-gray-600">Tableaux de bord intelligents avec KPIs en temps réel</p>
 </div>

 <div class="bg-white/80 rounded-xl p-6 shadow-lg border border-amber-200/50">
 <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mx-auto mb-4">
 <i class="fas fa-shield-check text-white text-xl"></i>
 </div>
 <h4 class="font-bold text-gray-900 mb-2">Sécurité Enterprise</h4>
 <p class="text-sm text-gray-600">Contrôles d'accès granulaires et audit trail complet</p>
 </div>

 <div class="bg-white/80 rounded-xl p-6 shadow-lg border border-amber-200/50">
 <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-4">
 <i class="fas fa-magic text-white text-xl"></i>
 </div>
 <h4 class="font-bold text-gray-900 mb-2">Interface Moderne</h4>
 <p class="text-sm text-gray-600">Design ultra-moderne avec UX optimisée</p>
 </div>
 </div>

 {{-- Progress Bar --}}
 <div class="mb-8">
 <div class="flex items-center justify-between mb-2">
 <span class="text-sm font-medium text-gray-700">Progression du développement</span>
 <span class="text-sm font-bold text-amber-600">75%</span>
 </div>
 <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner">
 <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 h-3 rounded-full shadow-lg" style="width: 75%"></div>
 </div>
 </div>

 {{-- Actions --}}
 <div class="flex flex-col sm:flex-row gap-4 justify-center">
 <a href="{{ route('admin.dashboard') }}"
 class="zenfleet-btn-enterprise-primary">
 <i class="fas fa-arrow-left mr-2"></i>
 Retour au Dashboard
 </a>

 <button onclick="alert('Fonctionnalité de notification activée ! Vous serez informé dès que le module sera disponible.')"
 class="zenfleet-btn-enterprise-secondary">
 <i class="fas fa-bell mr-2"></i>
 M'alerter lors de la sortie
 </button>
 </div>
 </div>
 </div>
 </div>
</div>

{{-- 📊 Development Timeline --}}
<div class="mt-8 bg-white shadow-lg rounded-2xl border border-gray-200/50 overflow-hidden">
 <div class="px-6 py-4 border-b border-gray-200/50 bg-gradient-to-r from-gray-50 to-blue-50">
 <h3 class="text-lg font-bold text-gray-900 flex items-center">
 <i class="fas fa-road mr-2 text-indigo-600"></i>
 Roadmap de Développement
 </h3>
 </div>

 <div class="p-6">
 <div class="space-y-6">
 {{-- Phase 1 --}}
 <div class="flex items-start">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
 <i class="fas fa-check text-white text-sm"></i>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <h4 class="text-sm font-bold text-gray-900">Phase 1: Architecture & Sécurité</h4>
 <p class="text-sm text-gray-600 mt-1">Mise en place de l'architecture enterprise et du système de permissions granulaires</p>
 <div class="mt-2">
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
 ✅ Terminé
 </span>
 </div>
 </div>
 </div>

 {{-- Phase 2 --}}
 <div class="flex items-start">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-full flex items-center justify-center">
 <i class="fas fa-cog fa-spin text-white text-sm"></i>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <h4 class="text-sm font-bold text-gray-900">Phase 2: Modules Core</h4>
 <p class="text-sm text-gray-600 mt-1">Développement des modules {{ $module }}, avec interface ultra-moderne</p>
 <div class="mt-2">
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
 🚧 En cours (75%)
 </span>
 </div>
 </div>
 </div>

 {{-- Phase 3 --}}
 <div class="flex items-start">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
 <i class="fas fa-rocket text-white text-sm"></i>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <h4 class="text-sm font-bold text-gray-900">Phase 3: Analytics & Intelligence</h4>
 <p class="text-sm text-gray-600 mt-1">Intégration d'analytics avancés et de recommandations IA</p>
 <div class="mt-2">
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
 📋 Planifié
 </span>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>

{{-- 👥 Development Team Info --}}
<div class="mt-8 bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 rounded-2xl border border-indigo-200/50 p-6">
 <div class="text-center">
 <h3 class="text-lg font-bold text-gray-900 mb-4">
 <i class="fas fa-users text-indigo-600 mr-2"></i>
 Équipe de Développement ZenFleet
 </h3>
 <p class="text-sm text-gray-700 mb-4">
 Notre équipe d'experts travaille 24/7 pour vous livrer une solution enterprise de classe mondiale.
 </p>
 <div class="flex items-center justify-center space-x-4 text-sm text-gray-600">
 <div class="flex items-center">
 <i class="fas fa-code text-blue-600 mr-1"></i>
 <span>Architecture Enterprise</span>
 </div>
 <div class="flex items-center">
 <i class="fas fa-shield-alt text-green-600 mr-1"></i>
 <span>Sécurité Avancée</span>
 </div>
 <div class="flex items-center">
 <i class="fas fa-chart-line text-purple-600 mr-1"></i>
 <span>Performance Optimisée</span>
 </div>
 </div>
 </div>
</div>

@endsection

@push('styles')
<style>
.zenfleet-development-header {
 @apply bg-gradient-to-r from-white via-amber-50 to-orange-50 rounded-2xl border border-amber-200/50 p-6 mb-6;
}

.animate-pulse {
 animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
 0%, 100% {
 opacity: 1;
 transform: scale(1);
 }
 50% {
 opacity: .8;
 transform: scale(1.05);
 }
}
</style>
@endpush