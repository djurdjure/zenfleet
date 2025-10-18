@props([
    'steps' => [],
    'currentStepVar' => 'currentStep'
])

{{--
    ====================================================================
    🎯 STEPPER COMPONENT - ENTERPRISE GRADE
    ====================================================================
    
    Composant de navigation multi-étapes avec support Alpine.js
    
    USAGE:
    ------
    <x-stepper 
        :steps="[
            ['label' => 'Identification', 'icon' => 'heroicons:identification'],
            ['label' => 'Caractéristiques', 'icon' => 'heroicons:cog-6-tooth'],
            ['label' => 'Acquisition', 'icon' => 'heroicons:currency-dollar']
        ]"
        currentStepVar="currentStep"
    />
    
    PROPS:
    ------
    @param array  $steps           - Liste des étapes avec 'label' et 'icon'
    @param string $currentStepVar  - Nom de la variable Alpine.js (default: 'currentStep')
    
    FEATURES:
    ---------
    ✓ Indicateurs visuels de progression
    ✓ Support Dark Mode
    ✓ Animations fluides
    ✓ Responsive design
    ✓ Alpine.js reactivity
    
    @version 2.0-Enterprise
    @author ZenFleet Design System Team
    @since 2025-01-19
    ====================================================================
--}}

<div {{ $attributes->merge(['class' => 'px-6 py-8 border-b border-gray-200 dark:border-gray-700']) }}>
    <ol class="flex items-center w-full">
        @foreach($steps as $index => $step)
            @php
                // Calcul du numéro d'étape (1-based index)
                $stepNumber = $index + 1;
                $isLast = $stepNumber === count($steps);
                
                // Construction des conditions Alpine.js (syntaxe correcte)
                $alpineCompletedCondition = "{$currentStepVar} > {$stepNumber}";
                $alpineActiveCondition = "{$currentStepVar} >= {$stepNumber}";
                
                // Classes statiques de la ligne de connexion entre étapes
                $connectorStaticClasses = !$isLast 
                    ? "after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block after:absolute after:top-5 after:left-1/2" 
                    : '';
            @endphp

            <li 
                class="flex {{ !$isLast ? 'w-full' : '' }} items-center relative {{ $connectorStaticClasses }}"
                @if(!$isLast)
                    x-bind:class="{{ $alpineCompletedCondition }} ? 'after:border-blue-600' : 'after:border-gray-300 dark:after:border-gray-600'"
                @endif
            >
                {{-- Container centré avec background pour éviter chevauchement --}}
                <div class="flex flex-col items-center relative z-10 bg-white dark:bg-gray-800 px-4">
                    
                    {{-- ================================================
                         STEP CIRCLE - Cercle avec icône
                         ================================================ --}}
                    <span 
                        class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-200"
                        x-bind:class="{{ $alpineActiveCondition }} ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-900/30' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400'"
                    >
                        <x-iconify :icon="$step['icon']" class="w-6 h-6" />
                    </span>

                    {{-- ================================================
                         STEP LABEL - Libellé de l'étape
                         ================================================ --}}
                    <span 
                        class="mt-2 text-xs font-medium transition-colors duration-200"
                        x-bind:class="{{ $alpineActiveCondition }} ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'"
                    >
                        {{ $step['label'] }}
                    </span>
                </div>
            </li>
        @endforeach
    </ol>
</div>
