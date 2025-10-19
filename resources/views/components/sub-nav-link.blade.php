@props(['active' => false])

@php
// ----- CLASSES DE CONFIGURATION -----

// Classes de base pour le lien <a>
$linkClasses = 'flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors group';

// Classes pour l'état ACTIF du lien
$activeLinkClasses = 'font-semibold text-primary-600 bg-primary-50';

// Classes pour l'état INACTIF du lien
$inactiveLinkClasses = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900';


// Classes de base pour le conteneur de l'icône (le cercle)
$iconWrapperClasses = 'flex items-center justify-center h-6 w-6 mr-3 rounded-full';

// Classes pour l'état ACTIF du cercle (fond plein)
$activeIconWrapperClasses = 'bg-primary-600';

// Classes pour l'état INACTIF du cercle (bordure fine)
$inactiveIconWrapperClasses = 'ring-1 ring-inset ring-gray-400 group-hover:ring-gray-500';


// Classes de base pour l'icône <svg>
$iconBaseClasses = 'h-4 w-4';

// Classe pour la couleur de l'icône à l'état ACTIF
$activeIconColor = 'text-white';

// Classe pour la couleur de l'icône à l'état INACTIF
$inactiveIconColor = 'text-gray-500 group-hover:text-gray-700';

// ----- LOGIQUE D'APPLICATION DES CLASSES -----

// On fusionne les classes du lien
$finalLinkClasses = $attributes->merge(['class' => $linkClasses . ' ' . ($active ? $activeLinkClasses : $inactiveLinkClasses)]);

// On fusionne les classes du conteneur de l'icône
$finalIconWrapperClasses = $iconWrapperClasses . ' ' . ($active ? $activeIconWrapperClasses : $inactiveIconWrapperClasses);

// On prépare les classes à appliquer à l'icône qui sera passée dans le slot
$iconAttributes = ['class' => $iconBaseClasses . ' ' . ($active ? $activeIconColor : $inactiveIconColor)];

@endphp

<a {{ $finalLinkClasses }}>
 {{-- Le conteneur du cercle pour l'icône --}}
 <span class="{{ $finalIconWrapperClasses }}">
 {{-- On applique les classes dynamiques à l'icône passée dans le slot --}}
 {{ $icon->withAttributes($iconAttributes) }}
 </span>

 {{-- Le texte du lien --}}
 <span>{{ $slot }}</span>
</a>