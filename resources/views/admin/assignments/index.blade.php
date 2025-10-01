{{-- resources/views/admin/assignments/index.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Gestion des Affectations - ZenFleet')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
<style>
/* Enterprise-grade animations et styles ultra-modernes */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Animations pour les modales */
@keyframes scale-in {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.animate-scale-in {
    animation: scale-in 0.3s ease-out;
}

.hover-scale {
    transition: transform 0.2s ease-in-out;
}

.hover-scale:hover {
    transform: scale(1.02);
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

.data-table {
    border-collapse: separate;
    border-spacing: 0;
}

.data-table th {
    position: sticky;
    top: 0;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    backdrop-filter: blur(10px);
    z-index: 10;
}

.data-table tbody tr {
    transition: all 0.2s ease-in-out;
}

.data-table tbody tr:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.metric-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border-color: #cbd5e1;
}

.search-input {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.search-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    background: white;
}

.action-button {
    transition: all 0.2s ease;
}

.action-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tab-button {
    position: relative;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    font-size: 0.875rem;
    border: none;
    background: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.tab-button.active {
    color: #6366f1;
    border-bottom: 2px solid #6366f1;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.tab-button:not(.active):hover {
    color: #4b5563;
    background: #f9fafb;
}

/* Styles des couleurs primary pour cohérence */
.bg-primary-600 { background-color: #4f46e5; }
.bg-primary-700 { background-color: #4338ca; }
.hover\:bg-primary-700:hover { background-color: #4338ca; }
.focus\:border-primary-500:focus { border-color: #6366f1; }
.focus\:ring-primary-500:focus { --tw-ring-color: #6366f1; }
</style>
@endpush

@section('content')
<div class="space-y-8 fade-in">
    {{-- Header Section - Architecture Standard ZenFleet --}}
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-exchange-alt text-blue-600 mr-3"></i>
                    Gestion des Affectations Véhicule ↔ Chauffeur
                </h1>
                <div class="mt-3 flex items-center space-x-4 text-sm text-gray-500">
                    <span><i class="fas fa-calendar mr-1"></i>Dernière mise à jour: {{ now()->format('d/m/Y à H:i') }}</span>
                    <span><i class="fas fa-user mr-1"></i>Connecté: {{ auth()->user()->name }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques Enterprise --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" x-data="assignmentStats()" x-init="loadStats()">
        <div class="metric-card hover-scale rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-play-circle text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Affectations actives</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.active">-</p>
                </div>
            </div>
        </div>

        <div class="metric-card hover-scale rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-calendar-alt text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Programmées</p>
                    <p class="text-2xl font-bold text-green-600" x-text="stats.scheduled">-</p>
                </div>
            </div>
        </div>

        <div class="metric-card hover-scale rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-check-circle text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Terminées (ce mois)</p>
                    <p class="text-2xl font-bold text-gray-600" x-text="stats.completed">-</p>
                </div>
            </div>
        </div>

        <div class="metric-card hover-scale rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-tachometer-alt text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Taux d'utilisation</p>
                    <p class="text-2xl font-bold text-orange-600" x-text="stats.utilization + '%'">-</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions et Filtres --}}
    <div x-data="{
        showEndModal: false,
        assignmentToEnd: {},
        endFormUrl: '',
        modalErrors: {},
        isSubmitting: false,
        openEndModal(event) {
            const button = event.currentTarget;
            this.assignmentToEnd = JSON.parse(button.dataset.assignment);
            this.endFormUrl = button.dataset.url;
            this.modalErrors = {};
            this.showEndModal = true;
        },
        async submitEndForm() {
            this.isSubmitting = true;
            this.modalErrors = {};
            const formData = new FormData(this.$refs.endForm);
            try {
                const response = await fetch(this.endFormUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });
                const data = await response.json();
                if (!response.ok) {
                    if (response.status === 422) { this.modalErrors = data.errors; }
                    else { throw new Error(data.message || 'Une erreur serveur est survenue.'); }
                } else {
                    this.showEndModal = false;
                    setTimeout(() => window.location.reload(), 1000);
                }
            } catch (error) {
                this.modalErrors = { general: [error.message] };
            } finally {
                this.isSubmitting = false;
            }
        }
    }">
        {{-- Section des Filtres --}}
        <div class="bg-white p-4 shadow-sm sm:rounded-lg">
            <form action="{{ route('admin.assignments.index') }}" method="GET">
                <div class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-2 md:space-y-0">
                    <div class="flex-grow">
                        <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                        <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                               placeholder="Immat, marque, chauffeur..."
                               class="search-input mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                    </div>
                    <div class="flex-shrink-0">
                        <label for="per_page" class="block text-sm font-medium text-gray-700">Par page</label>
                        <select name="per_page" id="per_page" onchange="this.form.submit()"
                                class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                            @foreach(['15', '30', '50'] as $value)
                                <option value="{{ $value }}" @selected(($filters['per_page'] ?? '15') == $value)>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-shrink-0 flex space-x-2">
                        <button type="submit" class="action-button inline-flex items-center justify-center w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                            <i class="fas fa-search mr-2"></i>Filtrer
                        </button>
                        <a href="{{ route('admin.assignments.index') }}" class="action-button inline-flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table des Affectations --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Liste des Affectations</h3>
                    </div>
                    @can('create assignments')
                        <a href="{{ route('admin.assignments.create') }}"
                           class="action-button inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:from-blue-700 hover:to-indigo-700 shadow-lg">
                            <i class="fas fa-plus mr-2"></i>
                            Nouvelle Affectation
                        </a>
                    @endcan
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="data-table w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Chauffeur</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Véhicule</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Période</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($assignments as $assignment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($assignment->driver?->photo_path)
                                                    <img class="h-10 w-10 rounded-full object-cover"
                                                         src="{{ asset('storage/' . $assignment->driver->photo_path) }}"
                                                         alt="Photo de {{ $assignment->driver->first_name }}">
                                                @else
                                                    <span class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <i class="fas fa-user h-6 w-6 text-gray-400"></i>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $assignment->driver?->first_name }} {{ $assignment->driver?->last_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $assignment->driver?->personal_phone ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $assignment->vehicle?->brand }} {{ $assignment->vehicle?->model }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $assignment->vehicle?->registration_plate }}</div>
                                        <div class="text-xs text-blue-600 font-semibold mt-1 flex items-center">
                                            <i class="fas fa-tachometer-alt mr-1"></i>
                                            {{ number_format($assignment->vehicle?->current_mileage ?? 0, 0, ',', ' ') }} km
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>De: {{ $assignment->start_datetime?->format('d/m/Y H:i') ?? 'N/A' }}</div>
                                        <div>À: @if($assignment->end_datetime) {{ $assignment->end_datetime->format('d/m/Y H:i') }} @else - @endif</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if(is_null($assignment->end_datetime))
                                            <span class="status-indicator inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-play-circle mr-1.5"></i>
                                                En cours
                                            </span>
                                        @else
                                            <span class="status-indicator inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-check-circle mr-1.5"></i>
                                                Terminée
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            @can('edit assignments')
                                                <a href="{{ route('admin.assignments.edit', $assignment) }}"
                                                   title="Modifier"
                                                   class="action-button p-2 rounded-full text-gray-400 hover:bg-blue-100 hover:text-blue-600">
                                                    <i class="fas fa-edit h-5 w-5"></i>
                                                </a>
                                            @endcan
                                            @if(is_null($assignment->end_datetime))
                                                @can('end assignments')
                                                    <button type="button"
                                                            @click="openEndModal($event)"
                                                            data-assignment='@json($assignment->load('vehicle'))'
                                                            data-url="{{ route('admin.assignments.end', $assignment) }}"
                                                            title="Terminer l'affectation"
                                                            class="action-button p-2 rounded-full text-gray-400 hover:bg-yellow-100 hover:text-yellow-600">
                                                        <i class="fas fa-stop-circle h-5 w-5"></i>
                                                    </button>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-clipboard-list h-8 w-8 text-gray-400"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune affectation trouvée</h3>
                                        <p class="text-sm text-gray-500 mb-6">
                                            Commencez par créer votre première affectation pour optimiser la gestion de votre flotte.
                                        </p>
                                        @can('create assignments')
                                            <a href="{{ route('admin.assignments.create') }}"
                                               class="action-button inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg">
                                                <i class="fas fa-plus mr-2"></i>
                                                Créer ma première affectation
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($assignments->hasPages())
                    <div class="mt-6">
                        {{ $assignments->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Modale pour terminer l'affectation --}}
        <div x-show="showEndModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60"
             style="display: none;">
            <div @click.away="showEndModal = false"
                 class="bg-white rounded-lg shadow-xl p-6 sm:p-8 w-full max-w-lg mx-auto transform transition-all animate-scale-in"
                 x-show="showEndModal">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-stop-circle text-yellow-600 text-lg"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Terminer l'affectation</h3>
                </div>

                <div class="mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-2">
                            <strong>Véhicule:</strong> <span x-text="`${assignmentToEnd.vehicle?.brand} ${assignmentToEnd.vehicle?.model} (${assignmentToEnd.vehicle?.registration_plate})`"></span>
                        </p>
                        <p class="text-sm text-gray-600">
                            <strong>Début:</strong> <span x-text="new Date(assignmentToEnd.start_datetime).toLocaleString('fr-FR')"></span>
                            avec <span x-text="assignmentToEnd.start_mileage ? assignmentToEnd.start_mileage.toLocaleString('fr-FR') : 'N/A'"></span> km.
                        </p>
                    </div>
                </div>

                <form x-ref="endForm" @submit.prevent="submitEndForm">
                    <input type="hidden" name="_method" value="PATCH">
                    <div class="space-y-4">
                        <div>
                            <label for="end_datetime" class="block text-sm font-medium text-gray-700">Date et heure de fin</label>
                            <input type="datetime-local"
                                   name="end_datetime"
                                   id="end_datetime"
                                   required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <template x-if="modalErrors.end_datetime">
                                <p class="text-xs text-red-600 mt-1" x-text="modalErrors.end_datetime[0]"></p>
                            </template>
                        </div>
                        <div>
                            <label for="end_mileage" class="block text-sm font-medium text-gray-700">Kilométrage de fin</label>
                            <input type="number"
                                   name="end_mileage"
                                   id="end_mileage"
                                   :min="assignmentToEnd.start_mileage"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <template x-if="modalErrors.end_mileage">
                                <p class="text-xs text-red-600 mt-1" x-text="modalErrors.end_mileage[0]"></p>
                            </template>
                        </div>
                        <template x-if="modalErrors.general">
                            <p class="text-sm text-red-600 mt-2" x-text="modalErrors.general[0]"></p>
                        </template>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button"
                                @click="showEndModal = false"
                                :disabled="isSubmitting"
                                class="action-button px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 disabled:opacity-50">
                            Annuler
                        </button>
                        <button type="submit"
                                :disabled="isSubmitting"
                                class="action-button inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-md shadow-sm hover:bg-yellow-700 disabled:opacity-50">
                            <span x-show="!isSubmitting">Confirmer la fin</span>
                            <span x-show="isSubmitting" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Chargement...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
// Fonction Alpine.js pour les statistiques
function assignmentStats() {
    return {
        stats: {
            active: 0,
            scheduled: 0,
            completed: 0,
            utilization: 0
        },

        async loadStats() {
            try {
                const response = await fetch('/admin/assignments/stats', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.stats = {
                        active: data.active_assignments || 0,
                        scheduled: data.scheduled_assignments || 0,
                        completed: data.completed_assignments || 0,
                        utilization: Math.round(data.average_utilization || 0)
                    };
                    console.log('📊 Statistiques chargées:', this.stats);
                } else {
                    console.warn('⚠️ Erreur lors du chargement des statistiques:', response.status);
                }
            } catch (error) {
                console.warn('⚠️ Erreur lors du chargement des statistiques:', error);
                // Valeurs par défaut en cas d'erreur
                this.stats = { active: 0, scheduled: 0, completed: 0, utilization: 0 };
            }
        }
    }
}

// Animation des cartes au chargement
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎉 Page d\'affectations chargée avec succès !');

    // Animation des cartes statistiques
    const cards = document.querySelectorAll('.metric-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    });
});
</script>
@endpush