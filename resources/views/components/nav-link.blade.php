@props(['active' => false, 'type' => 'primary'])

@php
// ✅ OPTIMISATION: Support de différents types de navigation
$baseClasses = 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out';

$typeClasses = match($type) {
    'primary' => $active 
        ? 'border-indigo-400 text-gray-900 focus:border-indigo-700' 
        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300',
    'admin' => $active 
        ? 'border-blue-400 text-blue-900 focus:border-blue-700' 
        : 'border-transparent text-gray-300 hover:text-white hover:border-gray-300 focus:text-white focus:border-gray-300',
    'danger' => $active 
        ? 'border-red-400 text-red-900 focus:border-red-700' 
        : 'border-transparent text-gray-500 hover:text-red-700 hover:border-red-300 focus:text-red-700 focus:border-red-300',
    default => $active 
        ? 'border-indigo-400 text-gray-900 focus:border-indigo-700' 
        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300'
};

$classes = $baseClasses . ' ' . $typeClasses;
@endphp

<a {{ $attributes->merge([
    'class' => $classes,
    'aria-current' => $active ? 'page' : null,
    'role' => 'menuitem'
]) }}>
    {{ $slot }}
</a>

