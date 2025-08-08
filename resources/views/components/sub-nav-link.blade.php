@props(['active' => false])

@php
// Classes de base pour le lien <a>
$linkClasses = 'flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors group';

// Classes pour l'état actif vs inactif du lien
$activeLinkClasses = 'font-semibold text-primary-600 bg-primary-50';
$inactiveLinkClasses = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900';

// Classes de base pour le conteneur de l'icône (le cercle)
$iconWrapperClasses = 'flex items-center justify-center h-6 w-6 mr-3 rounded-full';

// Classes pour l'état ACTIF du cercle (fond plein)
$activeIconWrapperClasses = 'bg-primary-600';

// Classes pour l'état INACTIF du cercle (bordure fine)
$inactiveIconWrapperClasses = 'ring-1 ring-inset ring-gray-400 group-hover:ring-gray-500';

// On fusionne les classes du lien
$finalLinkClasses = $attributes->merge(['class' => $linkClasses . ' ' . ($active ? $activeLinkClasses : $inactiveLinkClasses)]);

// On fusionne les classes du conteneur de l'icône
$finalIconWrapperClasses = $iconWrapperClasses . ' ' . ($active ? $activeIconWrapperClasses : $inactiveIconWrapperClasses);
@endphp

<a {{ $finalLinkClasses }}>
    <span class="{{ $finalIconWrapperClasses }}">
        {{-- L'icône est maintenant passée avec ses propres classes depuis la vue parente --}}
        {{ $icon }}
    </span>
    <span>{{ $slot }}</span>
</a>
