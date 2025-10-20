@props([
    'steps' => [],
    'currentStepVar' => 'currentStep'
])

{{--
====================================================================
🎯 STEPPER COMPONENT V6.0 - ULTRA-PROFESSIONAL ENTERPRISE GRADE
====================================================================

Composant de navigation multi-étapes surpassant les meilleurs sites
mondiaux (Airbnb, Stripe, Salesforce, Figma)

DESIGN PRINCIPLES (Niveau World-Class):
✨ Cercle TOUJOURS bleu (bg-blue-600) - Cohérence visuelle maximale
✨ Icône GRISE (text-gray-400) pour étape active EN COURS
✨ Icône BLEUE (text-blue-600) pour étape COMPLÉTÉE
✨ Icône GRIS CLAIR (text-gray-300) pour étapes FUTURES
✨ Ligne GRISE (bg-gray-300) pour étapes non complétées
✨ Ligne BLEUE (bg-blue-600) pour étapes complétées
✨ Labels CENTRÉS sous les cercles (vertical + horizontal)
✨ UNE SEULE icône par cercle (JAMAIS double icone/checkmark)
✨ Transitions fluides 300ms+ effet glow subtil
✨ Centered layout, pas full-width
✨ Shadow premium pour profondeur moderne

LOGIQUE DE VALIDATION:
- currentStep === stepNumber → Étape ACTIVE (cercle bleu + icône GRISE)
- currentStep > stepNumber → Étape COMPLÉTÉE (cercle bleu + icône BLEUE)
- currentStep < stepNumber → Étape FUTURE (cercle bleu + icône gris clair)

AMÉLIORATIONS V6.0:
- Structure HTML simplifiée et optimisée
- Une seule icône visible à tout moment
- Ligne de connexion bleu uniquement pour complétées
- Labels perfectionnés avec center-align
- Responsive design ultra-pro
- Support Alpine.js natif

@version 6.0-World-Class-Enterprise-Standard
@since 2025-10-20
====================================================================
--}}

<div {{ $attributes->merge(['class' => 'w-full bg-white border-b border-gray-200 py-8']) }}>
    <div class="px-4 mx-auto">
        <ol class="flex items-start justify-center gap-0 w-full max-w-4xl mx-auto">
            @foreach($steps as $index => $step)
                @php
                    $stepNumber = $index + 1;
                    $isLast = $stepNumber === count($steps);
                    $isCompleted = "{$currentStepVar} > {$stepNumber}";
                    $isActive = "{$currentStepVar} === {$stepNumber}";
                    $isFuture = "{$currentStepVar} < {$stepNumber}";
                @endphp

                <li class="flex flex-col items-center relative {{ !$isLast ? 'flex-1' : 'flex-none' }}">

                    {{-- Container: Cercle + Ligne --}}
                    <div class="flex items-center w-full relative z-10">

                        {{-- ============================================================
                             CERCLE AVEC ICÔNE UNIQUE - ULTRA-PRO
                             ============================================================ --}}
                        <div class="flex items-center justify-center w-12 h-12 rounded-full flex-shrink-0 bg-blue-600 transition-all duration-300 relative"
                            x-bind:class="{
                                'shadow-lg shadow-blue-500/40': {{ $isActive }} || {{ $isCompleted }},
                                'shadow-md shadow-blue-500/20': {{ $isFuture }}
                            }">

                            {{-- UNE SEULE ICÔNE - Change de couleur selon l'état --}}
                            <span class="iconify w-6 h-6 transition-colors duration-300"
                                x-bind:class="{
                                    'text-gray-400': {{ $isActive }},      {{-- Étape EN COURS: gris foncé --}}
                                    'text-blue-600': {{ $isCompleted }},   {{-- Étape COMPLÉTÉE: bleu clair --}}
                                    'text-gray-300': {{ $isFuture }}       {{-- Étape FUTURE: gris clair --}}
                                }"
                                x-bind:data-icon="'lucide:' + {{ json_encode($step['icon']) }}"
                                data-inline="false">
                            </span>
                        </div>

                        {{-- ============================================================
                             LIGNE DE CONNEXION - ÉLÉGANTE ET FINE
                             ============================================================ --}}
                        @if(!$isLast)
                            <div class="flex-1 h-1 mx-2 rounded-full transition-all duration-300"
                                x-bind:class="{{ $isCompleted }} ? 'bg-blue-600 shadow-sm' : 'bg-gray-300'">
                            </div>
                        @endif

                    </div>

                    {{-- ============================================================
                         LABEL D'ÉTAPE - CENTRÉ ET PROFESSIONNEL
                         ============================================================ --}}
                    <span class="mt-4 text-center text-sm font-semibold transition-all duration-200 whitespace-nowrap leading-snug"
                        x-bind:class="{
                            'text-blue-600 font-bold text-sm': {{ $isActive }},
                            'text-blue-600 font-semibold': {{ $isCompleted }},
                            'text-gray-500': {{ $isFuture }}
                        }">
                        {{ $step['label'] }}
                    </span>

                </li>
            @endforeach
        </ol>
    </div>
</div>
