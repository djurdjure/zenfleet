@props(['active' => false])

@php
// Classes de base pour le lien
$linkClasses = 'flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors group';

// Classes pour l'état actif vs inactif du lien
$activeLinkClasses = 'font-semibold text-primary-600 bg-primary-50';
$inactiveLinkClasses = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900';

// Classes de base pour le conteneur de l'icône (le cercle)
$iconWrapperClasses = 'flex items-center justify-center h-5 w-5 mr-3 rounded-full';

// Classes pour l'état actif vs inactif du cercle
$activeIconWrapperClasses = 'bg-primary-600';
$inactiveIconWrapperClasses = 'bg-gray-400 group-hover:bg-gray-500';
@endphp

<a {{ $attributes->merge(['class' => $linkClasses . ' ' . ($active ? $activeLinkClasses : $inactiveLinkClasses)]) }}>
    {{-- Le conteneur du cercle pour l'icône --}}
    <span class="{{ $iconWrapperClasses }} {{ $active ? $activeIconWrapperClasses : $inactiveIconWrapperClasses }}">
        {{-- L'icône elle-même est passée ici via un "slot" nommé --}}
        <slot name="icon" />
    </span>

    {{-- Le texte du lien --}}
    <span>{{ $slot }}</span>
</a>
