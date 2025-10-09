{{-- Composant Button Enterprise Ultra-Moderne --}}
@props([
    'variant' => 'primary', // primary, secondary, success, danger, warning, ghost, link
    'size' => 'md', // xs, sm, md, lg, xl
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'loading' => false,
    'disabled' => false,
    'fullWidth' => false,
    'type' => 'button',
    'href' => null,
    'rounded' => 'lg', // sm, md, lg, xl, 2xl, full
    'gradient' => false,
    'glow' => false,
    'ripple' => true,
])

@php
// Tailles
$sizeClasses = [
    'xs' => 'px-2.5 py-1.5 text-xs font-medium',
    'sm' => 'px-3 py-2 text-sm font-medium',
    'md' => 'px-4 py-2.5 text-sm font-medium',
    'lg' => 'px-6 py-3 text-base font-medium',
    'xl' => 'px-8 py-4 text-lg font-semibold',
];

// Variantes de couleur
$variantClasses = [
    'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 shadow-lg hover:shadow-xl',
    'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-gray-500 shadow-sm hover:shadow-md',
    'success' => 'bg-success-600 text-white hover:bg-success-700 focus:ring-success-500 shadow-lg hover:shadow-xl',
    'danger' => 'bg-danger-600 text-white hover:bg-danger-700 focus:ring-danger-500 shadow-lg hover:shadow-xl',
    'warning' => 'bg-warning-500 text-white hover:bg-warning-600 focus:ring-warning-500 shadow-lg hover:shadow-xl',
    'ghost' => 'text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:ring-gray-500',
    'link' => 'text-primary-600 hover:text-primary-700 hover:underline focus:ring-0',
];

// Classes gradient
$gradientClasses = [
    'primary' => 'bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800',
    'secondary' => 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 hover:from-gray-200 hover:to-gray-300',
    'success' => 'bg-gradient-to-r from-success-600 to-success-700 hover:from-success-700 hover:to-success-800',
    'danger' => 'bg-gradient-to-r from-danger-600 to-danger-700 hover:from-danger-700 hover:to-danger-800',
    'warning' => 'bg-gradient-to-r from-warning-500 to-warning-600 hover:from-warning-600 hover:to-warning-700',
];

// Classes glow
$glowClasses = [
    'primary' => 'shadow-primary-500/25',
    'success' => 'shadow-success-500/25',
    'danger' => 'shadow-danger-500/25',
    'warning' => 'shadow-warning-500/25',
];

// Rayons de bordure
$roundedClasses = [
    'sm' => 'rounded',
    'md' => 'rounded-md',
    'lg' => 'rounded-lg',
    'xl' => 'rounded-xl',
    '2xl' => 'rounded-2xl',
    'full' => 'rounded-full',
];

// Construction des classes
$baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-200 transform active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none';

$classes = $baseClasses . ' ';
$classes .= $sizeClasses[$size] . ' ';
$classes .= $roundedClasses[$rounded] . ' ';

if ($gradient && isset($gradientClasses[$variant])) {
    $classes .= $gradientClasses[$variant] . ' text-white ';
} else {
    $classes .= $variantClasses[$variant] . ' ';
}

if ($fullWidth) {
    $classes .= 'w-full ';
}

if ($glow && isset($glowClasses[$variant])) {
    $classes .= 'shadow-lg hover:shadow-2xl ' . $glowClasses[$variant] . ' ';
}

$tag = $href ? 'a' : 'button';
@endphp

{{-- Alpine.js pour effet ripple --}}
<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    @if(!$href) type="{{ $type }}" @endif
    @if($disabled || $loading) disabled @endif
    @if($ripple)
    x-data="{ ripple: false }"
    @click="
        ripple = true;
        setTimeout(() => ripple = false, 600);
    "
    @endif
    {{ $attributes->merge(['class' => $classes]) }}
    style="position: relative; overflow: hidden;"
>
    {{-- Effet ripple --}}
    @if($ripple)
    <span
        x-show="ripple"
        x-transition:enter="transition ease-out duration-600"
        x-transition:enter-start="opacity-0 transform scale-0"
        x-transition:enter-end="opacity-20 transform scale-100"
        class="absolute inset-0 rounded-{{ $rounded }} bg-white"
        style="animation: ripple 0.6s linear;"
    ></span>
    @endif
    
    {{-- Loader --}}
    @if($loading)
    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    @endif
    
    {{-- Icon gauche --}}
    @if($icon && $iconPosition === 'left' && !$loading)
    <span class="mr-2 -ml-0.5">
        {!! $icon !!}
    </span>
    @endif
    
    {{-- Contenu --}}
    <span class="relative z-10">
        {{ $slot }}
    </span>
    
    {{-- Icon droite --}}
    @if($icon && $iconPosition === 'right' && !$loading)
    <span class="ml-2 -mr-0.5">
        {!! $icon !!}
    </span>
    @endif
</{{ $tag }}>

<style>
@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
</style>
