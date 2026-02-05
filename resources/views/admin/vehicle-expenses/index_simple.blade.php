@extends('layouts.app')

@section('title', 'Gestion des Dépenses')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- En-tête --}}
    <div class="bg-gradient-to-br from-emerald-900/90 to-green-800/90 backdrop-blur-sm rounded-2xl p-8 mb-8 text-white shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold mb-4 flex items-center gap-3">
                    <x-iconify icon="tabler:moneybag" class="h-10 w-10" />
                    Gestion des Dépenses
                </h1>
                <p class="text-emerald-100/90">Gérez et suivez toutes les dépenses de votre flotte</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.vehicle-expenses.create') }}" 
                   class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition-colors">
                    <x-iconify icon="carbon:add-filled" class="h-5 w-5" />
                    Nouvelle Dépense
                </a>
                <a href="{{ route('admin.vehicle-expenses.dashboard') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition-colors">
                    <x-iconify icon="carbon:analytics" class="h-5 w-5" />
                    Analytics
                </a>
            </div>
        </div>
    </div>

    {{-- Statistiques rapides --}}
    @if(isset($stats))
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total ce mois</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['current_month_total'] ?? 0, 2) }} DZD</p>
                </div>
                <x-iconify icon="carbon:wallet" class="h-8 w-8 text-emerald-500" />
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">En attente</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_count'] ?? 0 }}</p>
                </div>
                <x-iconify icon="carbon:time" class="h-8 w-8 text-yellow-500" />
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Approuvées</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['approved_count'] ?? 0 }}</p>
                </div>
                <x-iconify icon="carbon:checkmark-filled" class="h-8 w-8 text-green-500" />
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Moyenne/véhicule</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['avg_per_vehicle'] ?? 0, 2) }} DZD</p>
                </div>
                <x-iconify icon="carbon:chart-average" class="h-8 w-8 text-blue-500" />
            </div>
        </div>
    </div>
    @endif

    {{-- Composant Livewire (désactivé temporairement) --}}
    @if(false && class_exists(\App\Livewire\Admin\VehicleExpenses\ExpenseManager::class))
        @livewire('admin.vehicle-expenses.expense-manager')
    @else
        {{-- Liste simple des dépenses récentes --}}
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <x-iconify icon="carbon:list" class="h-6 w-6" />
                Dépenses Récentes
            </h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Véhicule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse(\App\Models\VehicleExpense::where('organization_id', auth()->user()->organization_id)->latest()->take(10)->get() as $expense)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $expense->expense_date?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $expense->vehicle?->registration_number ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $expense->expense_type }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ number_format($expense->total_ttc, 2) }} DZD
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($expense->approval_status === 'approved')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">
                                        Approuvée
                                    </span>
                                @elseif(str_contains($expense->approval_status, 'pending'))
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">
                                        En attente
                                    </span>
                                @elseif($expense->approval_status === 'rejected')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-50 text-red-700 border border-red-200">
                                        Rejetée
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-50 text-gray-700 border border-gray-200">
                                        Brouillon
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.vehicle-expenses.show', $expense) }}" 
                                   class="text-blue-600 hover:text-blue-900">Voir</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <x-iconify icon="carbon:data-table" class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                <p>Aucune dépense enregistrée</p>
                                <a href="{{ route('admin.vehicle-expenses.create') }}" 
                                   class="text-blue-600 hover:text-blue-900 mt-2 inline-block">
                                    Créer votre première dépense
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
