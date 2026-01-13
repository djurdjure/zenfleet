@props([
'name' => '',
'label' => null,
'error' => null,
'helpText' => null,
'required' => false,
'disabled' => false,
'value' => null,
'minDate' => null,
'maxDate' => null,
'format' => 'd/m/Y',
'placeholder' => 'JJ/MM/AAAA',
'defaultToday' => true, // ✨ Par défaut, utilise la date d'aujourd'hui
])

@php
// Générer un ID unique pour le composant
$inputId = 'datepicker-' . uniqid();

// Définir la valeur par défaut
if (!$value && $defaultToday && !old($name)) {
$value = date('d/m/Y'); // Format français par défaut
}

// Classes conditionnelles pour erreur (fond rouge clair et bordure)
$inputClasses = $error
? 'datepicker-input !bg-red-50 border-2 border-red-500 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full pl-11 p-3 transition-all duration-200 placeholder-red-400'
: 'datepicker-input !bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 hover:border-gray-300 block w-full pl-11 p-3 transition-all duration-200';

// Classes pour l'icône
$iconClasses = $error
? 'text-red-500 animate-pulse'
: 'text-gray-400 group-hover:text-blue-600 transition-colors duration-200';

// Classes pour le conteneur
$containerClasses = $error
? 'relative group error-state'
: 'relative group';
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
    @if($label)
    <label for="{{ $inputId }}" class="block mb-2 text-sm font-semibold {{ $error ? 'text-red-700' : 'text-gray-700' }}">
        {{ $label }}
        @if($required)
        <span class="text-red-500 ml-0.5">*</span>
        @endif
    </label>
    @endif

    <div class="{{ $containerClasses }}">
        {{-- Icône calendrier --}}
        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none z-10">
            <x-iconify icon="lucide:calendar-days" class="w-5 h-5 {{ $iconClasses }}" />
        </div>

        {{-- Input avec masque --}}
        <input
            type="text"
            name="{{ $name }}"
            id="{{ $inputId }}"
            class="{{ $inputClasses }}"
            placeholder="{{ $placeholder }}"
            value="{{ old($name, $value) }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($minDate) data-min-date="{{ $minDate }}" @endif
            @if($maxDate) data-max-date="{{ $maxDate }}" @endif
            data-date-format="{{ $format }}"
            data-default-today="{{ $defaultToday ? 'true' : 'false' }}"
            autocomplete="off"
            maxlength="10"
            pattern="\d{1,2}/\d{1,2}/\d{4}"
            title="Format attendu: JJ/MM/AAAA"
            {{ $attributes->except(['class']) }} />

        {{-- Bouton clear (visible si il y a une valeur et pas d'erreur) --}}
        @if(!$disabled)
        <button
            type="button"
            class="clear-date absolute inset-y-0 right-3 flex items-center pr-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
            style="display: none;"
            onclick="clearDateInput('{{ $inputId }}')">
            <div class="p-1 rounded-full hover:bg-gray-100 transition-colors">
                <x-iconify icon="lucide:x" class="w-4 h-4 text-gray-400" />
            </div>
        </button>
        @endif
    </div>

    {{-- Messages d'erreur ou d'aide --}}
    @if($error)
    <div class="mt-2 flex items-start animate-fadeIn">
        <x-iconify icon="lucide:alert-circle" class="w-4 h-4 text-red-600 mr-1.5 mt-0.5 flex-shrink-0" />
        <div>
            <p class="text-sm text-red-600 font-medium">{{ $error }}</p>
            <p class="text-xs text-red-500 mt-0.5">Format attendu: JJ/MM/AAAA</p>
        </div>
    </div>
    @elseif($helpText)
    <p class="mt-2 text-sm text-gray-500 flex items-start">
        <x-iconify icon="lucide:info" class="w-4 h-4 text-gray-400 mr-1.5 mt-0.5 flex-shrink-0" />
        <span>{{ $helpText }}</span>
    </p>
    @endif
</div>

@once


@push('scripts')


<script>
    // Fonction globale pour nettoyer un champ date
    function clearDateInput(inputId) {
        const input = document.getElementById(inputId);
        if (input && input._flatpickr) {
            input._flatpickr.clear();
        }
        input.value = '';
        updateClearButton(input);
        // Déclencher l'événement input pour notifier les frameworks réactifs si nécessaire
        input.dispatchEvent(new Event('input'));
    }

    // Fonction pour gérer l'affichage du bouton clear
    function updateClearButton(input) {
        const clearBtn = input.parentElement.querySelector('.clear-date');
        if (clearBtn) {
            if (input.value && input.value.trim() !== '') {
                clearBtn.style.display = 'flex';
            } else {
                clearBtn.style.display = 'none';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser tous les datepickers
        document.querySelectorAll('.datepicker-input').forEach(function(el) {
            if (el._flatpickr) return; // Éviter la double initialisation

            const minDate = el.getAttribute('data-min-date');
            const maxDate = el.getAttribute('data-max-date');
            const dateFormat = el.getAttribute('data-date-format') || 'd/m/Y';
            const defaultToday = el.getAttribute('data-default-today') === 'true';

            // Initialiser Flatpickr
            const fp = flatpickr(el, {
                locale: 'fr',
                dateFormat: dateFormat,
                minDate: minDate,
                maxDate: maxDate,
                allowInput: true,
                disableMobile: true,
                defaultDate: defaultToday && !el.value ? 'today' : el.value,
                animate: true,
                monthSelectorType: 'dropdown', // IMPORTANT: Permet le selecteur de mois
                yearSelectorType: 'static', // IMPORTANT: Plus propre que dropdown pour l'année

                // Icônes personnalisées SVG (Lucide style)
                nextArrow: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>',
                prevArrow: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>',

                onReady: function(selectedDates, dateStr, instance) {
                    // Ajouter une classe au parent si erreur
                    if (el.classList.contains('border-red-500')) {
                        instance.calendarContainer.classList.add('error-calendar');
                    }

                    // Mettre à jour le bouton clear
                    updateClearButton(el);
                },
                onChange: function(selectedDates, dateStr, instance) {
                    // Retirer les classes d'erreur lors de la sélection
                    if (el.classList.contains('border-red-500')) {
                        el.classList.remove('border-red-500', '!bg-red-50', 'text-red-900', 'placeholder-red-400');
                        el.classList.add('border-gray-200', '!bg-white');

                        // Retirer aussi l'animation
                        const errorDiv = el.closest('.group').parentElement.querySelector('.animate-fadeIn');
                        if (errorDiv) {
                            errorDiv.style.display = 'none';
                        }

                        // Changer l'icône
                        const icon = el.parentElement.querySelector('svg');
                        if (icon) {
                            icon.classList.remove('text-red-500', 'animate-pulse');
                            icon.classList.add('text-gray-400');
                        }
                    }

                    // Mettre à jour le bouton clear
                    updateClearButton(el);
                },
                onClose: function(selectedDates, dateStr, instance) {
                    // Valider le format de la date saisie manuellement
                    const inputValue = el.value;
                    if (inputValue && !dateStr) {
                        // Essayer de parser la date manuellement
                        const parts = inputValue.split('/');
                        if (parts.length === 3) {
                            const day = parseInt(parts[0], 10);
                            const month = parseInt(parts[1], 10);
                            const year = parseInt(parts[2], 10);

                            if (day > 0 && day <= 31 && month > 0 && month <= 12 && year > 1900 && year < 2100) {
                                const date = new Date(year, month - 1, day);
                                instance.setDate(date, true);
                            }
                        }
                    }
                }
            });

            // Gérer la saisie manuelle avec validation
            el.addEventListener('blur', function() {
                const value = this.value;
                if (value && value.includes('_')) {
                    // Date incomplète, nettoyer
                    this.value = '';
                    updateClearButton(this);
                } else if (value) {
                    // Valider le format
                    const regex = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
                    if (regex.test(value)) {
                        const parts = value.match(regex);
                        const day = parseInt(parts[1], 10);
                        const month = parseInt(parts[2], 10);
                        const year = parseInt(parts[3], 10);

                        // Vérifier que la date est valide
                        const date = new Date(year, month - 1, day);
                        if (date.getDate() !== day || date.getMonth() !== month - 1 || date.getFullYear() !== year) {
                            // Date invalide
                            this.classList.add('border-red-500', '!bg-red-50');
                            this.classList.remove('border-gray-200', '!bg-white');
                        }
                    }
                }
            });

            // Gérer l'événement input pour le bouton clear
            el.addEventListener('input', function() {
                updateClearButton(this);
            });
        });
    });
</script>
@endpush
@endonce