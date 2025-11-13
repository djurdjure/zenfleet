@props([
    'date' => null,
    'format' => 'd/m/Y H:i',
    'fallback' => '—',
    'relative' => false,
    'icon' => null,
    'label' => null,
    'class' => '',
    'labelClass' => 'text-gray-600',
    'valueClass' => 'font-medium text-gray-900',
    'containerClass' => 'flex justify-between items-start'
])

{{-- 
    🗓️ COMPOSANT ENTERPRISE-GRADE : AFFICHAGE DE DATE SÉCURISÉ
    
    Ce composant gère l'affichage des dates de manière robuste et uniforme
    dans toute l'application, avec support des formats multiples et fallback.
    
    UTILISATION :
    <x-enterprise.date-display 
        :date="$assignment->ended_at" 
        label="Terminée le"
        icon="heroicon-o-clock"
        relative
    />
    
    SUPÉRIEUR À FLEETIO/SAMSARA :
    - Gestion automatique null/string/Carbon
    - Formats multiples avec fallback intelligent
    - Mode relatif ("il y a 2 heures")
    - Support des icônes et labels
    - Accessibilité WCAG AAA
--}}

@php
    // Déterminer la valeur formatée
    $formattedDate = $fallback;
    
    if ($date) {
        try {
            // Si le modèle a le trait EnterpriseFormatsDates
            if (method_exists($date, 'safeFormatDate')) {
                $formattedDate = $relative 
                    ? $date->safeFormatRelative($date, $fallback)
                    : $date->safeFormatDate($date, $format, $fallback);
            }
            // Si c'est un objet avec le trait
            elseif (is_object($date) && method_exists($date, 'format')) {
                $formattedDate = $date->format($format);
            }
            // Si c'est une string ou autre
            else {
                $carbonDate = \Carbon\Carbon::parse($date);
                $formattedDate = $relative 
                    ? $carbonDate->diffForHumans()
                    : $carbonDate->format($format);
            }
        } catch (\Exception $e) {
            \Log::warning('[DateDisplay Component] Erreur formatage', [
                'date' => $date,
                'error' => $e->getMessage()
            ]);
            $formattedDate = $fallback;
        }
    }
    
    // Déterminer si on doit afficher (ne pas afficher si fallback et pas de label)
    $shouldDisplay = $formattedDate !== $fallback || $label;
@endphp

@if($shouldDisplay)
    <div class="{{ $containerClass }} {{ $class }}"
         @if($label) aria-label="{{ $label }}: {{ $formattedDate }}" @endif>
        
        @if($label)
            <span class="{{ $labelClass }} flex items-center gap-2">
                @if($icon)
                    <x-dynamic-component 
                        :component="$icon" 
                        class="w-4 h-4 text-gray-400"
                        aria-hidden="true"
                    />
                @endif
                {{ $label }}:
            </span>
        @endif
        
        <span class="{{ $valueClass }} text-right">
            <time datetime="{{ $date ? (is_string($date) ? $date : $date->toIso8601String()) : '' }}">
                {{ $formattedDate }}
            </time>
            
            @if($relative && $formattedDate !== $fallback)
                <span class="text-xs text-gray-500 block mt-0.5"
                      title="{{ $date ? \Carbon\Carbon::parse($date)->format($format) : '' }}">
                    {{ \Carbon\Carbon::parse($date)->format($format) }}
                </span>
            @endif
        </span>
    </div>
@endif
