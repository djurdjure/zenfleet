{{-- Composant Filter Panel Enterprise Ultra-Moderne --}}
@props([
    'title' => 'Filtres',
    'icon' => null,
    'showButton' => true,
    'activeCount' => 0,
])

<div x-data="{ 
    showFilters: false,
    activeFilters: {{ $activeCount }},
    resetFilters() {
        this.$dispatch('reset-filters');
        this.activeFilters = 0;
    }
}" class="w-full">
    {{-- Bouton d'activation des filtres --}}
    @if($showButton)
    <button
        @click="showFilters = !showFilters"
        class="inline-flex items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:border-primary-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200"
        :class="{ 'bg-primary-50 border-primary-300 text-primary-700 hover:bg-primary-100': showFilters }"
    >
        {{-- Icon --}}
        @if($icon)
        <span class="mr-2">
            {!! $icon !!}
        </span>
        @else
        <svg class="w-5 h-5 mr-2 transition-transform duration-200" :class="{ 'rotate-180': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
        </svg>
        @endif
        
        {{-- Texte --}}
        <span>{{ $title }}</span>
        
        {{-- Badge compteur --}}
        <span 
            x-show="activeFilters > 0" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-0"
            x-transition:enter-end="opacity-100 transform scale-100"
            class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold rounded-full"
            :class="showFilters ? 'bg-primary-200 text-primary-800' : 'bg-primary-100 text-primary-700'"
        >
            <span x-text="activeFilters"></span>
        </span>
        
        {{-- Chevron animé --}}
        <svg 
            class="ml-2 -mr-0.5 h-4 w-4 transition-transform duration-200" 
            :class="{ 'rotate-180': showFilters }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    @endif
    
    {{-- Panneau de filtres avec animation --}}
    <div 
        x-show="showFilters"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        @if($showButton) class="mt-4" @endif
    >
        <div class="bg-gradient-to-br from-gray-50 via-white to-primary-50/10 rounded-xl border border-gray-200 shadow-lg p-6 space-y-4">
            {{-- Header du panneau --}}
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Filtres avancés</h3>
                    <span 
                        x-show="activeFilters > 0"
                        class="text-xs text-gray-500"
                    >
                        (<span x-text="activeFilters"></span> actif<span x-show="activeFilters > 1">s</span>)
                    </span>
                </div>
                
                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    <button
                        @click="resetFilters()"
                        x-show="activeFilters > 0"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200"
                    >
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Réinitialiser
                    </button>
                    
                    <button
                        @click="showFilters = false"
                        class="inline-flex items-center p-1.5 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 rounded-lg transition-all duration-200"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            {{-- Contenu des filtres --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                {{ $slot }}
            </div>
            
            {{-- Footer avec actions --}}
            @if(isset($footer))
            <div class="pt-4 border-t border-gray-200 flex items-center justify-between">
                {{ $footer }}
            </div>
            @endif
        </div>
    </div>
</div>
