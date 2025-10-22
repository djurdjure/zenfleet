{{-- ====================================================================
 🎨 EMPTY STATE COMPONENT - ENTERPRISE GRADE
 ====================================================================
 
 Composant d'état vide réutilisable et accessible
 
 PROPS:
 - icon: string - Nom de l'icône Iconify (ex: 'heroicons:inbox')
 - title: string - Titre de l'état vide
 - description: string - Description (optionnel)
 - actionUrl: string - URL du bouton d'action (optionnel)
 - actionText: string - Texte du bouton d'action (optionnel)
 - actionIcon: string - Icône du bouton (optionnel)
 
 USAGE:
 <x-empty-state
 icon="heroicons:user-group"
 title="Aucun chauffeur trouvé"
 description="Commencez par ajouter votre premier chauffeur."
 actionUrl="{{ route('admin.drivers.create') }}"
 actionText="Ajouter un chauffeur"
 actionIcon="plus-circle"
 />
 
 @version 1.0
 @since 2025-01-19
 ==================================================================== --}}

@props([
 'icon' => 'heroicons:inbox',
 'title' => 'Aucun élément trouvé',
 'description' => '',
 'actionUrl' => null,
 'actionText' => null,
 'actionIcon' => 'plus-circle'
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-16 px-4']) }}>
 {{-- Icône --}}
 <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
 <x-iconify :icon="$icon" class="w-10 h-10 text-gray-400" />
 </div>

 {{-- Titre --}}
 <h3 class="text-lg font-medium text-gray-900 mb-2">
 {{ $title }}
 </h3>

 {{-- Description --}}
 @if($description || $slot->isNotEmpty())
 <p class="text-gray-500 text-center mb-6 max-w-md">
 @if($slot->isNotEmpty())
 {{ $slot }}
 @else
 {{ $description }}
 @endif
 </p>
 @endif

 {{-- Bouton d'action --}}
 @if($actionUrl && $actionText)
 <a href="{{ $actionUrl }}"
 class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
 <x-iconify :icon="'heroicons:' . $actionIcon" class="w-5 h-5" />
 {{ $actionText }}
 </a>
 @endif
</div>
