@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-tachometer-alt text-blue-600 mr-3"></i>
                        Dashboard Chauffeur
                    </h1>
                    <p class="text-gray-600 mt-2">Bienvenue {{ $user->name }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-blue-500 text-white p-6 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100">Voyages Total</p>
                                <p class="text-2xl font-bold">{{ $stats['totalTrips'] ?? 0 }}</p>
                            </div>
                            <i class="fas fa-route text-2xl"></i>
                        </div>
                    </div>

                    <div class="bg-green-500 text-white p-6 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100">KM ce mois</p>
                                <p class="text-2xl font-bold">{{ $stats['monthlyKm'] ?? 0 }}</p>
                            </div>
                            <i class="fas fa-road text-2xl"></i>
                        </div>
                    </div>

                    <div class="bg-yellow-500 text-white p-6 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-100">Score Sécurité</p>
                                <p class="text-2xl font-bold">{{ $stats['safetyScore'] ?? 0 }}%</p>
                            </div>
                            <i class="fas fa-shield-alt text-2xl"></i>
                        </div>
                    </div>

                    <div class="bg-purple-500 text-white p-6 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100">Statut</p>
                                <p class="text-lg font-bold">Disponible</p>
                            </div>
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="bg-white border rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Affectation Actuelle</h3>
                        @if($stats['currentAssignment'])
                            <p>{{ $stats['currentAssignment'] }}</p>
                        @else
                            <p class="text-gray-500">Aucune affectation en cours</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

