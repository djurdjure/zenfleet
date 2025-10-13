{{-- 💼 STEP 2: Informations Professionnelles - VERSION ENTERPRISE ULTRA-ROBUSTE --}}
<div class="mb-6">
    <h3 class="text-2xl font-bold text-gray-900 mb-2">Informations Professionnelles</h3>
    <p class="text-gray-600">Définissez le statut et les informations d'emploi</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="employee_number" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-id-badge text-gray-400 mr-2"></i>Matricule Employé
        </label>
        <input type="text" id="employee_number" name="employee_number" value="{{ old('employee_number', $driver->employee_number ?? '') }}"
               class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
        @error('employee_number')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- 🚀 SOLUTION ENTERPRISE ULTRA-ROBUSTE POUR LES STATUTS --}}
    @php
        // Récupération sécurisée des statuts avec fallback garanti
        $statusesData = isset($driverStatuses) && $driverStatuses ? $driverStatuses : collect([]);
        
        // Si aucun statut, utiliser des valeurs par défaut
        if ($statusesData->isEmpty()) {
            $statusesData = collect([
                ['id' => 1, 'name' => 'Disponible', 'description' => 'Chauffeur disponible', 'color' => '#10B981', 'icon' => 'fa-check-circle', 'can_drive' => true, 'can_assign' => true],
                ['id' => 2, 'name' => 'En mission', 'description' => 'En cours de mission', 'color' => '#3B82F6', 'icon' => 'fa-truck', 'can_drive' => true, 'can_assign' => false],
                ['id' => 3, 'name' => 'En congé', 'description' => 'En congé', 'color' => '#F59E0B', 'icon' => 'fa-calendar-times', 'can_drive' => false, 'can_assign' => false],
                ['id' => 4, 'name' => 'Inactif', 'description' => 'Inactif', 'color' => '#EF4444', 'icon' => 'fa-ban', 'can_drive' => false, 'can_assign' => false],
            ]);
        }
        
        // Conversion en array pour JavaScript
        $statusesJson = $statusesData->map(function($status) {
            if (is_array($status)) {
                return $status;
            }
            return [
                'id' => $status->id ?? $status['id'],
                'name' => $status->name ?? $status['name'],
                'description' => $status->description ?? $status['description'] ?? '',
                'color' => $status->color ?? $status['color'] ?? '#6B7280',
                'icon' => $status->icon ?? $status['icon'] ?? 'fa-circle',
                'can_drive' => isset($status->can_drive) ? $status->can_drive : ($status['can_drive'] ?? true),
                'can_assign' => isset($status->can_assign) ? $status->can_assign : ($status['can_assign'] ?? true),
            ];
        })->values()->toArray();
    @endphp

    {{-- 🎯 Composant Alpine.js pour le sélecteur de statut --}}
    <div x-data="{
        open: false,
        selectedStatus: null,
        selectedId: @json(old('status_id', $driver->status_id ?? '')),
        statuses: @json($statusesJson),
        hasLocalError: false,
        searchQuery: '',

        init() {
            console.log('🔍 Initialisation du sélecteur de statuts');
            console.log('📊 Nombre de statuts chargés:', this.statuses.length);
            console.log('📋 Statuts disponibles:', this.statuses);
            
            // Pré-sélection si une valeur existe
            if (this.selectedId && this.statuses.length > 0) {
                const found = this.statuses.find(s => s.id == this.selectedId);
                if (found) {
                    this.selectedStatus = found;
                    console.log('✅ Statut pré-sélectionné:', found.name);
                }
            }
            
            // Vérification de sécurité
            if (this.statuses.length === 0) {
                console.error('❌ ERREUR: Aucun statut disponible!');
                // Fallback d'urgence côté client
                this.statuses = [
                    {id: 1, name: 'Disponible', description: 'Disponible', color: '#10B981', icon: 'fa-check-circle', can_drive: true, can_assign: true},
                    {id: 2, name: 'En mission', description: 'En mission', color: '#3B82F6', icon: 'fa-truck', can_drive: true, can_assign: false}
                ];
                console.warn('⚠️ Fallback activé avec', this.statuses.length, 'statuts');
            }
        },

        selectStatus(status) {
            console.log('✓ Sélection du statut:', status.name);
            this.selectedStatus = status;
            this.selectedId = status.id;
            this.open = false;
            this.hasLocalError = false;
            this.searchQuery = '';

            // Appel de la validation parent si disponible
            if (typeof this.$root !== 'undefined' && typeof this.$root.validateField === 'function') {
                this.$root.validateField('status_id', status.id);
            }
        },
        
        get filteredStatuses() {
            if (!this.searchQuery) return this.statuses;
            const query = this.searchQuery.toLowerCase();
            return this.statuses.filter(s => 
                s.name.toLowerCase().includes(query) || 
                (s.description && s.description.toLowerCase().includes(query))
            );
        }
    }" x-init="init()" class="relative">

        <label for="status_id" class="block text-sm font-semibold text-gray-700 mb-3">
            <i class="fas fa-user-check text-blue-500 mr-2"></i>
            Statut du Chauffeur <span class="text-red-500">*</span>
        </label>

        {{-- Input caché pour la soumission du formulaire --}}
        <input type="hidden" name="status_id" :value="selectedId" required>

        {{-- Bouton de sélection personnalisé --}}
        <button type="button" 
                @click="open = !open" 
                @click.away="open = false; searchQuery = ''"
                :class="{
                    'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-100': hasLocalError,
                    'border-gray-200 bg-white focus:border-blue-400 focus:ring-blue-50 hover:border-blue-300': !hasLocalError
                }"
                class="w-full px-4 py-3 border-2 rounded-xl focus:ring-4 transition-all duration-200 flex items-center justify-between group">

            {{-- Affichage du statut sélectionné --}}
            <div class="flex items-center flex-1" x-show="selectedStatus">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium shadow-sm"
                         :style="`background-color: ${selectedStatus?.color || '#6B7280'}`">
                        <i :class="selectedStatus?.icon || 'fas fa-circle'" class="text-xs"></i>
                    </div>
                    <div class="text-left">
                        <div class="font-semibold text-gray-900" x-text="selectedStatus?.name"></div>
                        <div class="text-xs text-gray-500" x-text="selectedStatus?.description" x-show="selectedStatus?.description"></div>
                    </div>
                </div>
            </div>

            {{-- Placeholder si aucun statut sélectionné --}}
            <div x-show="!selectedStatus" class="text-gray-500">
                <i class="fas fa-user-plus mr-2"></i> Sélectionnez un statut
            </div>

            {{-- Icône chevron --}}
            <i class="fas fa-chevron-down transition-transform duration-200 text-gray-400 group-hover:text-blue-500 ml-2"
               :class="{ 'rotate-180': open }"></i>
        </button>

        {{-- Menu déroulant Alpine.js (sans dépendance PHP) --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @keydown.escape="open = false; searchQuery = ''"
             class="absolute z-50 w-full mt-2 bg-white border-2 border-gray-100 rounded-xl shadow-xl overflow-hidden">

            {{-- Barre de recherche --}}
            <div class="p-3 border-b border-gray-100" x-show="statuses.length > 3">
                <div class="relative">
                    <input type="text" 
                           x-model="searchQuery" 
                           @click.stop
                           placeholder="Rechercher un statut..."
                           class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-50">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>
            </div>

            {{-- Liste des statuts (entièrement gérée par Alpine.js) --}}
            <div class="max-h-80 overflow-y-auto">
                <template x-if="filteredStatuses.length > 0">
                    <div>
                        <template x-for="status in filteredStatuses" :key="status.id">
                            <button type="button" 
                                    @click="selectStatus(status)"
                                    class="w-full px-4 py-4 text-left hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200 border-b border-gray-50 last:border-0 group flex items-center"
                                    :class="{ 'bg-blue-50 border-l-4 border-l-blue-500': selectedId == status.id }">

                                <div class="flex items-center gap-4 flex-1">
                                    {{-- Icône du statut --}}
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-medium shadow-md group-hover:shadow-lg transition-shadow"
                                         :style="`background-color: ${status.color || '#6B7280'}`">
                                        <i :class="status.icon || 'fa-circle'" class="text-sm"></i>
                                    </div>
                                    
                                    {{-- Informations du statut --}}
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900" x-text="status.name"></div>
                                        <div class="text-sm text-gray-600 mt-1" x-text="status.description" x-show="status.description"></div>
                                        
                                        {{-- Badges de capacités --}}
                                        <div class="flex gap-2 mt-2">
                                            <span x-show="status.can_drive" 
                                                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-car mr-1"></i> Conduite
                                            </span>
                                            <span x-show="status.can_assign" 
                                                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-tasks mr-1"></i> Missions
                                            </span>
                                            <span x-show="!status.can_drive && !status.can_assign" 
                                                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Limité
                                            </span>
                                        </div>
                                    </div>
                                    
                                    {{-- Indicateur de sélection --}}
                                    <i class="fas fa-check text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"
                                       :class="{ 'opacity-100': selectedId == status.id }"></i>
                                </div>
                            </button>
                        </template>
                    </div>
                </template>

                {{-- Message si aucun résultat de recherche --}}
                <template x-if="filteredStatuses.length === 0 && searchQuery">
                    <div class="px-4 py-8 text-center">
                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-search text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Aucun statut trouvé</p>
                        <p class="text-gray-400 text-sm mt-1">Essayez avec d'autres termes</p>
                    </div>
                </template>

                {{-- Message d'erreur si vraiment aucun statut --}}
                <template x-if="statuses.length === 0">
                    <div class="px-4 py-8 text-center">
                        <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-exclamation-circle text-red-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Erreur de chargement</p>
                        <p class="text-gray-400 text-sm mt-1">Les statuts n'ont pas pu être chargés</p>
                        <button @click="location.reload()" 
                                class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                            <i class="fas fa-sync-alt mr-2"></i>Recharger la page
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Messages d'erreur --}}
        @error('status_id')
            <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ $message }}
                </p>
            </div>
        @enderror

        {{-- Message d'aide --}}
        <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-xs text-blue-700 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Le statut détermine les permissions et capacités du chauffeur dans le système
            </p>
        </div>
    </div>

    {{-- Date de recrutement --}}
    <div>
        <label for="recruitment_date" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-calendar-plus text-gray-400 mr-2"></i>Date de Recrutement
        </label>
        <input type="date" id="recruitment_date" name="recruitment_date" 
               value="{{ old('recruitment_date', ($driver->recruitment_date ?? null)?->format('Y-m-d')) }}"
               class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
        @error('recruitment_date')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Date de fin de contrat --}}
    <div>
        <label for="contract_end_date" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-calendar-times text-gray-400 mr-2"></i>Date de Fin de Contrat
        </label>
        <input type="date" id="contract_end_date" name="contract_end_date" 
               value="{{ old('contract_end_date', ($driver->contract_end_date ?? null)?->format('Y-m-d')) }}"
               class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
        @error('contract_end_date')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
