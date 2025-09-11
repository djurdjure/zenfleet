@extends('layouts.admin.app')

@section('title', 'Dashboard Superviseur')

@section('content')
<div class="dashboard-supervisor">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-eye text-blue-600 mr-3"></i>
            Dashboard Superviseur
        </h1>
        <p class="text-gray-600 mt-2">
            Supervision et contrôle des activités
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm uppercase tracking-wider">Véhicules Supervisés</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['supervisedVehicles'] }}</p>
                </div>
                <div class="p-4 bg-blue-400 bg-opacity-30 rounded-full">
                    <i class="fas fa-car text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-700 text-white rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm uppercase tracking-wider">Chauffeurs Supervisés</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['supervisedDrivers'] }}</p>
                </div>
                <div class="p-4 bg-green-400 bg-opacity-30 rounded-full">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-700 text-white rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm uppercase tracking-wider">Affectations Aujourd'hui</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['todayAssignments'] }}</p>
                </div>
                <div class="p-4 bg-yellow-400 bg-opacity-30 rounded-full">
                    <i class="fas fa-calendar-day text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-700 text-white rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm uppercase tracking-wider">Inspections Pendantes</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['pendingInspections'] }}</p>
                </div>
                <div class="p-4 bg-red-400 bg-opacity-30 rounded-full">
                    <i class="fas fa-clipboard-check text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-tasks text-blue-600 mr-2"></i>
                    Tâches du Jour
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Inspection véhicule ABC-123</span>
                        </div>
                        <span class="text-sm text-gray-500">09:00</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-yellow-500 mr-3"></i>
                            <span>Contrôle chauffeur Martin</span>
                        </div>
                        <span class="text-sm text-gray-500">14:30</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                    Alertes
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-fuel-pump mr-2"></i>
                            Véhicule DEF-456 - Niveau carburant faible
                        </p>
                    </div>
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-800">
                            <i class="fas fa-wrench mr-2"></i>
                            Véhicule GHI-789 - Maintenance requise
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

