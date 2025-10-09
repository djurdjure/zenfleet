{{-- Composant Input Enterprise Ultra-Moderne --}}
@props([
    'label' => null,
    'type' => 'text',
    'name' => null,
    'id' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'help' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'addon' => null,
    'addonPosition' => 'left',
    'size' => 'md', // sm, md, lg
    'rounded' => 'lg', // sm, md, lg, xl, full
    'floating' => false,
])

@php
$hasError = $error || ($errors ?? null) && $errors->has($name);
$errorMessage = $error ?? ($errors->first($name) ?? null);

$sizeClasses = [
    'sm' => 'px-3 py-2 text-sm',
    'md' => 'px-4 py-2.5 text-sm',
    'lg' => 'px-5 py-3 text-base',
];

$roundedClasses = [
    'sm' => 'rounded',
    'md' => 'rounded-md',
    'lg' => 'rounded-lg',
    'xl' => 'rounded-xl',
    'full' => 'rounded-full',
];

$inputClasses = 'w-full bg-white border transition-all duration-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 disabled:bg-gray-50 disabled:cursor-not-allowed ';
$inputClasses .= $sizeClasses[$size] . ' ';
$inputClasses .= $roundedClasses[$rounded] . ' ';

if ($hasError) {
    $inputClasses .= 'border-danger-300 text-danger-900 placeholder-danger-400 focus:ring-danger-500 focus:border-danger-500 ';
} else {
    $inputClasses .= 'border-gray-300 text-gray-900 focus:ring-primary-500 focus:border-primary-500 hover:border-gray-400 ';
}

if ($icon && $iconPosition === 'left') {
    $inputClasses .= 'pl-10 ';
}

if ($icon && $iconPosition === 'right') {
    $inputClasses .= 'pr-10 ';
}

$inputId = $id ?? $name ?? Str::random(8);
@endphp

<div 
    x-data="{ 
        focused: false,
        hasValue: {{ $value ? 'true' : 'false' }},
        checkValue(e) {
            this.hasValue = e.target.value.length > 0;
        }
    }"
    class="relative"
>
    {{-- Label standard --}}
    @if($label && !$floating)
    <label 
        for="{{ $inputId }}" 
        class="block text-sm font-medium text-gray-700 mb-1.5"
    >
        {{ $label }}
        @if($required)
        <span class="text-danger-500 ml-1">*</span>
        @endif
    </label>
    @endif
    
    {{-- Conteneur de l'input --}}
    <div class="relative">
        {{-- Addon gauche --}}
        @if($addon && $addonPosition === 'left')
        <div class="absolute inset-y-0 left-0 flex items-center">
            <span class="inline-flex items-center px-3 text-gray-500 bg-gray-50 border border-r-0 border-gray-300 {{ $roundedClasses[$rounded] }} rounded-r-none">
                {{ $addon }}
            </span>
        </div>
        @endif
        
        {{-- Icon gauche --}}
        @if($icon && $iconPosition === 'left')
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <span class="text-gray-400" :class="{ 'text-primary-500': focused, 'text-danger-500': {{ $hasError ? 'true' : 'false' }} }">
                {!! $icon !!}
            </span>
        </div>
        @endif
        
        {{-- Input --}}
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ old($name, $value) }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @focus="focused = true"
            @blur="focused = false"
            @input="checkValue($event)"
            {{ $attributes->merge(['class' => $inputClasses]) }}
            :class="{ 
                'shadow-lg shadow-primary-500/10 ring-2 ring-primary-500 ring-offset-0': focused && !{{ $hasError ? 'true' : 'false' }},
                'shadow-lg shadow-danger-500/10 ring-2 ring-danger-500 ring-offset-0': focused && {{ $hasError ? 'true' : 'false' }}
            }"
        />
        
        {{-- Icon droite --}}
        @if($icon && $iconPosition === 'right')
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <span class="text-gray-400" :class="{ 'text-primary-500': focused, 'text-danger-500': {{ $hasError ? 'true' : 'false' }} }">
                {!! $icon !!}
            </span>
        </div>
        @endif
        
        {{-- Addon droite --}}
        @if($addon && $addonPosition === 'right')
        <div class="absolute inset-y-0 right-0 flex items-center">
            <span class="inline-flex items-center px-3 text-gray-500 bg-gray-50 border border-l-0 border-gray-300 {{ $roundedClasses[$rounded] }} rounded-l-none">
                {{ $addon }}
            </span>
        </div>
        @endif
        
        {{-- Label flottant --}}
        @if($label && $floating)
        <label 
            for="{{ $inputId }}"
            class="absolute left-4 transition-all duration-200 pointer-events-none"
            :class="{
                'top-2.5 text-sm text-gray-500': !focused && !hasValue,
                '-top-2 left-2 text-xs bg-white px-2 text-primary-600 font-medium': focused || hasValue
            }"
        >
            {{ $label }}
            @if($required)
            <span class="text-danger-500 ml-0.5">*</span>
            @endif
        </label>
        @endif
        
        {{-- Indicateur d'état (erreur ou succès) --}}
        @if($hasError)
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-danger-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
        </div>
        @endif
        
        {{-- Barre de progression animée --}}
        <div 
            class="absolute bottom-0 left-0 h-0.5 bg-primary-500 transition-all duration-300"
            :style="focused ? 'width: 100%' : 'width: 0%'"
        ></div>
    </div>
    
    {{-- Message d'aide --}}
    @if($help && !$hasError)
    <p class="mt-1.5 text-xs text-gray-500 flex items-start gap-1">
        <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ $help }}
    </p>
    @endif
    
    {{-- Message d'erreur avec animation --}}
    @if($hasError)
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-1.5"
    >
        <p class="text-xs text-danger-600 flex items-start gap-1">
            <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $errorMessage }}
        </p>
    </div>
    @endif
</div>
