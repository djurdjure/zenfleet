{{-- ====================================================================
 🗂️ COMPOSANT LIVEWIRE - VÉHICULES ARCHIVÉS ULTRA-PRO
 ====================================================================

 Gestion enterprise-grade des véhicules archivés avec Livewire 3:
 ✨ Actions instantanées sans rafraîchissement
 ✨ Notifications toast en temps réel
 ✨ Animations fluides et professionnelles
 ✨ Feedback visuel pendant les actions
 ✨ Modales de confirmation enterprise-grade

 @version 1.0-Enterprise-Ultra
 @since 2025-11-27
 ==================================================================== --}}

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 -m-6 p-6">
    <div class="fade-in space-y-8">
        <!-- 🎨 Enterprise Header Section -->
        <div class="max-w-7xl mx-auto">
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm text-gray-600 mb-6">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    <a href="{{ route('admin.vehicles.index') }}" class="hover:text-blue-600 transition-colors">
                        Gestion des Véhicules
                    </a>
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    <span class="font-semibold text-gray-900">Archives</span>
                </nav>

                <!-- Hero Content -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-amber-600 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-archive text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900">Véhicules Archivés</h1>
                            <p class="text-gray-600 text-lg mt-2">
                                Gestion des véhicules supprimés - Restauration et suppression définitive
                            </p>
                        </div>
                    </div>

                    <!-- Actions principales -->
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.vehicles.index') }}"
                            class="inline-flex items-center gap-3 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all duration-200 hover:scale-105">
                            <i class="fas fa-arrow-left"></i>
                            <span>Retour aux véhicules</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 📊 Statistiques des Archives -->
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6 hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Archivés</p>
                            <p class="text-3xl font-bold text-amber-600">{{ $stats['total_archived'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-archive text-amber-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6 hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Ce Mois</p>
                            <p class="text-3xl font-bold text-orange-600">{{ $stats['archived_this_month'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6 hover:scale-105 transition-transform">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Cette Année</p>
                            <p class="text-3xl font-bold text-red-600">{{ $stats['archived_this_year'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chart-line text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🗂️ Liste des Véhicules Archivés -->
        <div class="max-w-7xl mx-auto">
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-list text-amber-600"></i>
                        Véhicules Archivés ({{ $vehicles->total() }})
                    </h3>
                </div>

                @if($vehicles && $vehicles->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Véhicule</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kilométrage</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivé le</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($vehicles as $vehicle)
                                    <tr class="hover:bg-gray-50 transition-colors" wire:key="vehicle-{{ $vehicle->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full bg-amber-100 flex items-center justify-center">
                                                        <svg class="h-4 w-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ $vehicle->registration_plate }}</div>
                                                    <div class="text-sm text-gray-500">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                {{ $vehicle->vehicleType->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($vehicle->current_mileage) }} km
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex flex-col">
                                                <span>{{ $vehicle->deleted_at->format('d/m/Y') }}</span>
                                                <span class="text-xs text-gray-400">{{ $vehicle->deleted_at->format('H:i') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                            {{-- Bouton Restaurer --}}
                                            <button
                                                wire:click="$dispatch('confirmRestore', { vehicleId: {{ $vehicle->id }}, plate: '{{ $vehicle->registration_plate }}', brand: '{{ $vehicle->brand }} {{ $vehicle->model }}' })"
                                                wire:loading.attr="disabled"
                                                wire:target="restoreVehicle({{ $vehicle->id }})"
                                                class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-800 rounded-lg text-sm font-medium transition-all duration-200 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                                                <i class="fas fa-undo mr-1" wire:loading.remove wire:target="restoreVehicle({{ $vehicle->id }})"></i>
                                                <i class="fas fa-spinner fa-spin mr-1" wire:loading wire:target="restoreVehicle({{ $vehicle->id }})"></i>
                                                <span wire:loading.remove wire:target="restoreVehicle({{ $vehicle->id }})">Restaurer</span>
                                                <span wire:loading wire:target="restoreVehicle({{ $vehicle->id }})">Restauration...</span>
                                            </button>

                                            {{-- Bouton Supprimer Définitivement --}}
                                            <button
                                                wire:click="$dispatch('confirmForceDelete', { vehicleId: {{ $vehicle->id }}, plate: '{{ $vehicle->registration_plate }}', brand: '{{ $vehicle->brand }} {{ $vehicle->model }}' })"
                                                wire:loading.attr="disabled"
                                                wire:target="forceDeleteVehicle({{ $vehicle->id }})"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-800 rounded-lg text-sm font-medium transition-all duration-200 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                                                <i class="fas fa-trash-alt mr-1" wire:loading.remove wire:target="forceDeleteVehicle({{ $vehicle->id }})"></i>
                                                <i class="fas fa-spinner fa-spin mr-1" wire:loading wire:target="forceDeleteVehicle({{ $vehicle->id }})"></i>
                                                <span wire:loading.remove wire:target="forceDeleteVehicle({{ $vehicle->id }})">Supprimer</span>
                                                <span wire:loading wire:target="forceDeleteVehicle({{ $vehicle->id }})">Suppression...</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @else
                    <div class="text-center py-12">
                        <div class="mx-auto h-24 w-24 text-amber-400 mb-4">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 5v6m6 0V5"></path>
                            </svg>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun véhicule archivé</h3>
                        <p class="mt-1 text-sm text-gray-500">Tous vos véhicules sont actifs ou ont été restaurés.</p>
                    </div>
                @endif
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                <x-pagination :paginator="$vehicles" :records-per-page="$perPage" wire:model.live="perPage" />
            </div>
        </div>
    </div>

    {{-- Modales de confirmation Alpine.js + Livewire --}}
    <div x-data="archivedVehiclesModals()"
         @confirm-restore.window="showRestoreModal($event.detail)"
         @confirm-force-delete.window="showForceDeleteModal($event.detail)">

        {{-- Modale Restaurer --}}
        <div x-show="restoreModal.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" @click="restoreModal.show = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="restoreModal.show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-green-100 sm:mx-0 sm:h-12 sm:w-12">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Restaurer le véhicule</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 mb-4">Voulez-vous restaurer ce véhicule ? Il redeviendra actif dans votre flotte.</p>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-green-900" x-text="restoreModal.plate"></p>
                                            <p class="text-sm text-green-700" x-text="restoreModal.brand"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                        <button type="button"
                                @click="confirmRestore()"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-green-600 text-base font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto transition-all duration-200 hover:scale-105">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                            </svg>
                            Restaurer le véhicule
                        </button>
                        <button type="button"
                                @click="restoreModal.show = false"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto transition-all duration-200">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modale Suppression Définitive (similaire) --}}
        <div x-show="deleteModal.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" @click="deleteModal.show = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="deleteModal.show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-red-100 sm:mx-0 sm:h-12 sm:w-12">
                            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">⚠️ Suppression Définitive</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 mb-4">
                                    <strong class="text-red-600">ATTENTION :</strong> Cette action est IRRÉVERSIBLE !
                                </p>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-red-900" x-text="deleteModal.plate"></p>
                                            <p class="text-sm text-red-700" x-text="deleteModal.brand"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                    <div class="flex">
                                        <svg class="h-5 w-5 text-red-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-red-800">Suppression irréversible</p>
                                            <p class="text-xs text-red-700 mt-1">Toutes les données du véhicule seront définitivement perdues. Cette action ne peut pas être annulée.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                        <button type="button"
                                @click="confirmForceDelete()"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-red-600 text-base font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto transition-all duration-200 hover:scale-105">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Supprimer Définitivement
                        </button>
                        <button type="button"
                                @click="deleteModal.show = false"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto transition-all duration-200">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Enterprise-grade animations et styles ultra-modernes */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush

@push('scripts')
<script>
function archivedVehiclesModals() {
    return {
        restoreModal: {
            show: false,
            vehicleId: null,
            plate: '',
            brand: ''
        },
        deleteModal: {
            show: false,
            vehicleId: null,
            plate: '',
            brand: ''
        },

        showRestoreModal(data) {
            this.restoreModal = {
                show: true,
                vehicleId: data.vehicleId,
                plate: data.plate,
                brand: data.brand
            };
        },

        showForceDeleteModal(data) {
            this.deleteModal = {
                show: true,
                vehicleId: data.vehicleId,
                plate: data.plate,
                brand: data.brand
            };
        },

        confirmRestore() {
            @this.call('restoreVehicle', this.restoreModal.vehicleId);
            this.restoreModal.show = false;
        },

        confirmForceDelete() {
            @this.call('forceDeleteVehicle', this.deleteModal.vehicleId);
            this.deleteModal.show = false;
        }
    }
}
</script>
@endpush
