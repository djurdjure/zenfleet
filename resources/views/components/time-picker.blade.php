{{-- ====================================================================
    🕐 TIME PICKER ALPINE.JS - ZENFLEET ENTERPRISE
    ====================================================================
    
    Version personnalisée basée sur Alpine.js avec masque intelligent HH:MM
    - Saut automatique après 2 chiffres d'heures
    - Validation en temps réel
    - Compatible Livewire wire:model
    - Pas de dépendance externe (Flatpickr supprimé)
    
    @version 4.0-Alpine
    @since 2025-11-03
    ==================================================================== --}}

@props([
    'name' => '',
    'label' => null,
    'error' => null,
    'helpText' => null,
    'required' => false,
    'disabled' => false,
    'value' => null,
    'placeholder' => 'HH:MM',
    'wireModel' => null,
])

@php
    // Détecter automatiquement wire:model si pas explicitement passé
    $wireModel = $wireModel ?? $attributes->whereStartsWith('wire:model')->first();
    // Générer un ID unique si nécessaire
    $inputId = $attributes->get('id', 'time-picker-' . uniqid());
@endphp

<div 
    x-data="timePickerMask(@js($wireModel), @js($value))"
    {{ $attributes->except(['wire:model', 'wire:model.live', 'wire:model.blur', 'wire:model.change']) }}
    class="{{ $attributes->get('class', '') }}"
>
    {{-- Label --}}
    @if($label)
    <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium text-gray-900">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    {{-- Input avec icône --}}
    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <x-iconify icon="heroicons:clock" class="w-4 h-4 text-gray-500" />
        </div>
        <input
            type="text"
            id="{{ $inputId }}"
            name="{{ $name }}"
            x-model="timeValue"
            @input="handleTimeInput($event)"
            @keydown="handleTimeKeydown($event)"
            @blur="handleTimeBlur($event)"
            @focus="handleTimeFocus($event)"
            placeholder="{{ $placeholder }}"
            maxlength="5"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 font-medium tracking-wider"
            autocomplete="off"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{-- Préserver les attributs wire:model originaux --}}
            @if($wireModel)
                wire:model{{ str_contains($wireModel, '.live') ? '.live' : (str_contains($wireModel, '.blur') ? '.blur' : '') }}="{{ str_replace(['wire:model.live=', 'wire:model.blur=', 'wire:model='], '', $wireModel) }}"
            @endif
        />
    </div>

    {{-- Messages d'erreur --}}
    @if($error)
    <p class="mt-2 text-sm text-red-600 flex items-start">
        <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" />
        <span>{{ $error }}</span>
    </p>
    @elseif($helpText)
    <p class="mt-2 text-sm text-gray-500">
        {{ $helpText }}
    </p>
    @endif
</div>

{{-- Script Alpine.js pour le masque de saisie --}}
@once
@push('scripts')
<script>
/**
 * ====================================================================
 * 🕐 TIME PICKER ALPINE.JS FUNCTION - ZENFLEET
 * ====================================================================
 * 
 * Fonction globale pour le masque de saisie HH:MM avec Alpine.js
 * - Compatible avec Livewire wire:model
 * - Saut automatique après 2 chiffres
 * - Validation en temps réel
 * 
 * @version 4.0-Alpine
 * @since 2025-11-03
 * ====================================================================
 */
window.timePickerMask = function(wireModel, initialValue) {
    return {
        // Propriété réactive pour la valeur du champ
        timeValue: initialValue || '',
        
        // Flag pour éviter les boucles de mise à jour
        isUpdating: false,

        /**
         * Initialisation du composant Alpine
         */
        init() {
            // S'assurer que la valeur initiale est formatée
            if (this.timeValue && this.timeValue.length === 4 && !this.timeValue.includes(':')) {
                this.timeValue = this.timeValue.slice(0, 2) + ':' + this.timeValue.slice(2, 4);
            }
            
            // Si un modèle Livewire est défini, synchroniser les changements
            if (wireModel) {
                this.$watch('timeValue', (value) => {
                    // Émettre l'événement pour Livewire
                    this.$wire.set(wireModel.replace('wire:model=', '').replace('wire:model.live=', ''), value);
                });
            }
        },

        /**
         * Formater la valeur en supprimant les caractères non numériques
         * et en appliquant le format HH:MM
         */
        formatTimeValue(input) {
            // Garder seulement les chiffres
            let digits = input.replace(/[^0-9]/g, '');
            
            // Limiter à 4 chiffres max (HHMM)
            if (digits.length > 4) {
                digits = digits.slice(0, 4);
            }
            
            let formatted = '';
            
            if (digits.length === 0) {
                return '';
            }
            
            if (digits.length === 1) {
                // Un seul chiffre, l'afficher tel quel
                formatted = digits[0];
            }
            
            if (digits.length === 2) {
                // Deux chiffres = heures complètes
                let hours = parseInt(digits);
                
                // Validation: max 23h
                if (hours > 23) {
                    hours = 23;
                }
                
                formatted = String(hours).padStart(2, '0') + ':';
            }
            
            if (digits.length === 3) {
                // Heures + premier chiffre des minutes
                let hours = parseInt(digits.substring(0, 2));
                if (hours > 23) hours = 23;
                
                // Pas d'auto-complétion agressive
                formatted = String(hours).padStart(2, '0') + ':' + digits[2];
            }
            
            if (digits.length === 4) {
                // Format complet HHMM
                let hours = parseInt(digits.substring(0, 2));
                if (hours > 23) hours = 23;
                
                let minutes = parseInt(digits.substring(2, 4));
                if (minutes > 59) minutes = 59;
                
                formatted = String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
            }
            
            return formatted;
        },

        /**
         * Gestionnaire de l'événement input
         */
        handleTimeInput(event) {
            if (this.isUpdating) return;
            
            const input = event.target;
            const currentValue = input.value;
            const cursorPos = input.selectionStart;
            
            // Formater la valeur
            const formatted = this.formatTimeValue(currentValue);
            
            // Mise à jour seulement si différent
            if (currentValue !== formatted) {
                this.isUpdating = true;
                this.timeValue = formatted;
                
                // Gérer le positionnement du curseur
                this.$nextTick(() => {
                    // Si on vient de taper 2 chiffres et qu'on a ajouté ':'
                    if (formatted.length === 3 && formatted.includes(':') && cursorPos === 2) {
                        // Placer le curseur après le ':'
                        input.setSelectionRange(3, 3);
                    } else if (cursorPos <= formatted.length) {
                        input.setSelectionRange(cursorPos, cursorPos);
                    }
                    this.isUpdating = false;
                });
            }
        },

        /**
         * Gestionnaire de l'événement keydown
         */
        handleTimeKeydown(event) {
            const input = event.target;
            const currentValue = input.value;
            const cursorPos = input.selectionStart;
            const key = event.key;
            
            // Permettre les touches de navigation et de suppression
            if (['ArrowLeft', 'ArrowRight', 'Tab', 'Enter', 'Escape'].includes(key)) {
                // Navigation flèches - Skip le ':'
                if (key === 'ArrowRight' && cursorPos === 2) {
                    event.preventDefault();
                    input.setSelectionRange(3, 3);
                } else if (key === 'ArrowLeft' && cursorPos === 3) {
                    event.preventDefault();
                    input.setSelectionRange(2, 2);
                }
                return;
            }
            
            // Gestion du Backspace
            if (key === 'Backspace') {
                // Si on est juste après ':', effacer le ':' et revenir aux heures
                if (cursorPos === 3 && currentValue.charAt(2) === ':') {
                    event.preventDefault();
                    this.timeValue = currentValue.substring(0, 2);
                    this.$nextTick(() => {
                        input.setSelectionRange(2, 2);
                    });
                }
                return;
            }
            
            // Gestion du Delete
            if (key === 'Delete') {
                return;
            }
            
            // Bloquer les caractères non numériques
            if (!/[0-9]/.test(key)) {
                event.preventDefault();
                return;
            }
            
            // Saut automatique après 2 chiffres
            const onlyDigits = currentValue.replace(/[^0-9]/g, '');
            if (onlyDigits.length === 2 && !currentValue.includes(':') && cursorPos === 2) {
                // On va ajouter ':' automatiquement
                this.$nextTick(() => {
                    input.setSelectionRange(3, 3);
                });
            }
        },

        /**
         * Gestionnaire de l'événement blur
         */
        handleTimeBlur(event) {
            const value = event.target.value;
            
            // Si le champ n'est pas vide
            if (value && value.length > 0) {
                let digits = value.replace(/[^0-9]/g, '');
                
                // Auto-compléter seulement pour les cas évidents
                if (digits.length === 2) {
                    // HH → HH:00
                    this.timeValue = digits + ':00';
                } else if (digits.length === 3) {
                    // HHM → HH:M0
                    let hours = digits.substring(0, 2);
                    let mins = digits.substring(2) + '0';
                    this.timeValue = hours + ':' + mins;
                } else if (digits.length === 4 && !value.includes(':')) {
                    // HHMM → HH:MM
                    this.timeValue = digits.substring(0, 2) + ':' + digits.substring(2, 4);
                }
                
                // Validation finale du format
                if (this.timeValue && this.timeValue.length === 5) {
                    if (!this.timeValue.match(/^([01][0-9]|2[0-3]):([0-5][0-9])$/)) {
                        // Format invalide, réinitialiser
                        this.timeValue = '00:00';
                    }
                }
            }
        },

        /**
         * Gestionnaire de l'événement focus
         */
        handleTimeFocus(event) {
            // Si le champ contient seulement ':' ou est '00:00' par défaut, l'effacer
            if (this.timeValue === ':' || this.timeValue === '00:00') {
                this.timeValue = '';
            }
        }
    };
};
</script>
 @endpush
@endonce
