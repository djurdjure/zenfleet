@props([
    'steps' => [],
    'currentStepVar' => 'currentStep'
])

{{--
    Stepper Component - Flowbite-inspired
    Usage:
    <x-stepper :steps="[
        ['label' => 'Identification', 'icon' => 'heroicons:identification'],
        ['label' => 'Caractéristiques', 'icon' => 'heroicons:cog-6-tooth'],
        ['label' => 'Acquisition', 'icon' => 'heroicons:currency-dollar']
    ]" />
--}}

<div {{ $attributes->merge(['class' => 'px-6 py-8 border-b border-gray-200 dark:border-gray-700']) }}>
    <ol class="flex items-center w-full">
        @foreach($steps as $index => $step)
            @php
                $stepNumber = $index + 1;
                $isLast = $stepNumber === count($steps);
                $alpineCondition = $currentStepVar . ' >= ' . $stepNumber;
                $alpineCompleted = $currentStepVar . ' > ' . $stepNumber;
            @endphp

            <li class="flex {{ !$isLast ? 'w-full' : '' }} items-center relative"
                x-bind:class="{{ !$isLast ? "'{$$currentStepVar} > {$stepNumber} ? \"after:border-blue-600\" : \"after:border-gray-300 dark:after:border-gray-600\"'" : '{}' }}
                {{ !$isLast ? " class=\"after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block after:absolute after:top-5 after:left-1/2\"" : '' }}>

                <div class="flex flex-col items-center relative z-10 bg-white dark:bg-gray-800 px-4">
                    {{-- Step Circle with Icon --}}
                    <span class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-200"
                          x-bind:class="{{ $currentStepVar }} >= {{ $stepNumber }} ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-900/30' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400'">
                        <x-iconify :icon="$step['icon']" class="w-6 h-6" />
                    </span>

                    {{-- Step Label --}}
                    <span class="mt-2 text-xs font-medium"
                          x-bind:class="{{ $currentStepVar }} >= {{ $stepNumber }} ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">
                        {{ $step['label'] }}
                    </span>
                </div>
            </li>
        @endforeach
    </ol>
</div>
