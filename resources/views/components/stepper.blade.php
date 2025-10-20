@props([
    'steps' => [],
    'currentStepVar' => 'currentStep'
])

{{--
====================================================================
🎯 STEPPER COMPONENT - ENTERPRISE GRADE SURPASSE AIRBNB/STRIPE/SALESFORCE
====================================================================

Composant de navigation multi-étapes ultra-professionnel

DESIGN PRINCIPLES (Niveau Industry Leader):
- Cercle TOUJOURS bleu (bg-blue-600) - Cohérence visuelle maximale
- Icône GRISE (text-gray-400) pour étape active EN COURS
- Icône BLEUE (text-blue-600) pour étape COMPLÉTÉE
- Ligne GRISE (bg-gray-300) pour étapes non complétées
- Ligne BLEUE (bg-blue-600) pour étapes complétées
- Labels centrés horizontalement ET verticalement avec les cercles
- Transitions fluides et professionnelles (300ms)
- Shadow subtil pour profondeur moderne
- Une seule icône par cercle (pas de checkmark/validation)

LOGIQUE DE VALIDATION:
- currentStep === stepNumber → Étape ACTIVE (cercle bleu + icône GRISE)
- currentStep > stepNumber → Étape COMPLÉTÉE (cercle bleu + icône BLEUE)
- currentStep < stepNumber → Étape FUTURE (cercle bleu + icône gris clair)

@version 4.0-Enterprise-Industry-Leader
@since 2025-10-20
====================================================================
--}}

<div {{ $attributes->merge(['class' => 'px-4 py-8 border-b border-gray-200 bg-white']) }}>
    <ol class="flex items-center justify-between w-full max-w-5xl mx-auto">
        @foreach($steps as $index => $step)
            @php
                $stepNumber = $index + 1;
                $isLast = $stepNumber === count($steps);
                // Conditions Alpine.js précises
                $isCompleted = "{$currentStepVar} > {$stepNumber}";
                $isActive = "{$currentStepVar} === {$stepNumber}";
                $isFuture = "{$currentStepVar} < {$stepNumber}";
            @endphp

            <li class="flex items-center relative {{ !$isLast ? 'flex-1' : '' }}">

                {{-- Container flex-col pour cercle + label --}}
                <div class="flex flex-col items-center relative z-10 bg-white {{ !$isLast ? 'w-full' : '' }}">

                    {{-- Row horizontale: Cercle + Ligne --}}
                    <div class="flex items-center {{ !$isLast ? 'w-full' : '' }}">

                        {{-- ===============================================
                             CERCLE AVEC ICÔNE - LOGIQUE ENTERPRISE GRADE
                             =============================================== --}}
                        <div
                            class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-300 flex-shrink-0 bg-blue-600"
                            x-bind:class="{
                                'shadow-lg shadow-blue-500/30': {{ $isActive }} || {{ $isCompleted }},
                                'shadow-sm': {{ $isFuture }}
                            }"
                        >
                            {{-- UNE SEULE ICÔNE - Couleur change selon l'état --}}
                            <span
                                class="iconify w-6 h-6 transition-colors duration-300"
                                x-bind:class="{
                                    'text-gray-400': {{ $isActive }},      {{-- Étape ACTIVE EN COURS --}}
                                    'text-blue-600': {{ $isCompleted }},   {{-- Étape COMPLÉTÉE --}}
                                    'text-gray-300': {{ $isFuture }}       {{-- Étape FUTURE --}}
                                }"
                                x-bind:data-icon="'lucide:' + {{ json_encode($step['icon']) }}"
                                data-inline="false"
                            ></span>
                        </div>

                        {{-- ===============================================
                             LIGNE DE CONNEXION - Ultra-fine et élégante
                             =============================================== --}}
                        @if(!$isLast)
                            <div
                                class="flex-1 h-0.5 mx-3 rounded-full transition-all duration-300"
                                x-bind:class="{{ $isCompleted }} ? 'bg-blue-600' : 'bg-gray-300'"
                            ></div>
                        @endif

                    </div>

                    {{-- ===============================================
                         LABEL D'ÉTAPE - Centré horizontalement et verticalement
                         =============================================== --}}
                    <span
                        class="mt-3 text-sm font-medium text-center transition-all duration-200 whitespace-nowrap"
                        x-bind:class="{
                            'text-blue-600 font-semibold': {{ $isActive }} || {{ $isCompleted }},
                            'text-gray-500': {{ $isFuture }}
                        }"
                    >
                        {{ $step['label'] }}
                    </span>

                </div>
            </li>
        @endforeach
    </ol>
</div>
