@props([
    'steps' => [],
    'currentStepVar' => 'currentStep'
])

{{--
    ====================================================================
    🎯 STEPPER COMPONENT - ULTRA ENTERPRISE GRADE
    ====================================================================

    Composant de navigation multi-étapes professionnel avec design épuré

    DESIGN PRINCIPLES:
    - Lignes fines et élégantes (2px au lieu de 4px)
    - Espacement optimisé pour visibilité complète
    - Cercles compacts mais lisibles
    - Responsive sur tous les écrans

    @version 3.0-Ultra-Professional
    @since 2025-01-19
    ====================================================================
--}}

<div {{ $attributes->merge(['class' => 'px-4 py-6 border-b border-gray-200 dark:border-gray-700']) }}>
    <ol class="flex items-center justify-between w-full max-w-4xl mx-auto">
        @foreach($steps as $index => $step)
            @php
                $stepNumber = $index + 1;
                $isLast = $stepNumber === count($steps);
                $alpineCompletedCondition = "{$currentStepVar} > {$stepNumber}";
                $alpineActiveCondition = "{$currentStepVar} >= {$stepNumber}";
            @endphp

            <li
                class="flex items-center relative"
                x-bind:class="'{{ !$isLast ? 'flex-1' : '' }}'"
            >
                {{-- Container pour l'étape --}}
                <div class="flex flex-col items-center relative z-10 bg-white dark:bg-gray-800 {{ !$isLast ? 'w-full' : '' }}">

                    {{-- Cercle avec icône --}}
                    <div class="flex items-center {{ !$isLast ? 'w-full' : '' }}">
                        <span
                            class="flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300 flex-shrink-0"
                            x-bind:class="{{ $alpineActiveCondition }} ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/50' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400'"
                        >
                            <x-iconify :icon="$step['icon']" class="w-5 h-5" />
                        </span>

                        {{-- Ligne de connexion fine et professionnelle --}}
                        @if(!$isLast)
                            <div class="flex-1 h-0.5 mx-2 transition-colors duration-300"
                                 x-bind:class="{{ $alpineCompletedCondition }} ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'">
                            </div>
                        @endif
                    </div>

                    {{-- Label de l'étape --}}
                    <span
                        class="mt-2 text-xs font-medium text-center transition-colors duration-200 whitespace-nowrap"
                        x-bind:class="{{ $alpineActiveCondition }} ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-500 dark:text-gray-400'"
                    >
                        {{ $step['label'] }}
                    </span>
                </div>
            </li>
        @endforeach
    </ol>
</div>
