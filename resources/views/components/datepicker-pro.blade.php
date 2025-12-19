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
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/light.css">
<style>
    /* 🎨 FLATPICKR ENTERPRISE-GRADE ULTRA-PRO V2 - ZenFleet 2025 */

    /* Animation pour les erreurs */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-4px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }

    /* Calendrier principal avec effet de verre et profondeur */
    .flatpickr-calendar {
        background-color: #ffffff !important;
        border: 0 !important;
        border-radius: 1rem !important;
        box-shadow:
            0 0 0 1px rgba(0, 0, 0, 0.03),
            0 20px 25px -5px rgba(0, 0, 0, 0.08),
            0 10px 10px -5px rgba(0, 0, 0, 0.02) !important;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif !important;
        margin-top: 8px;
        width: 320px !important;
        padding: 0 !important;
        overflow: hidden;
    }

    .flatpickr-calendar:before,
    .flatpickr-calendar:after {
        display: none !important;
    }

    /* Calendrier pour input avec erreur */
    .error-state .flatpickr-calendar {
        box-shadow:
            0 0 0 2px rgb(239 68 68),
            0 20px 25px -5px rgba(239, 68, 68, 0.15) !important;
    }

    /* En-tête (mois/année) ultra stylisé */
    .flatpickr-months {
        background: #ffffff !important;
        border-bottom: 1px solid #f3f4f6;
        padding: 1.25rem 0.5rem 0.5rem 0.5rem !important;
    }

    .flatpickr-current-month {
        padding-top: 0 !important;
        font-size: 110% !important;
    }

    /* Sélecteurs Mois/Année */
    .flatpickr-current-month .flatpickr-monthDropdown-months,
    .flatpickr-current-month .numInputWrapper {
        margin: 0 0.25rem;
    }

    .flatpickr-current-month .flatpickr-monthDropdown-months {
        background: white !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 0.5rem !important;
        color: #111827 !important;
        font-weight: 600 !important;
        padding: 0.25rem 0.5rem !important;
        appearance: none;
        -webkit-appearance: none;
        cursor: pointer;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .flatpickr-current-month .flatpickr-monthDropdown-months:hover {
        border-color: #d1d5db !important;
        background-color: #f9fafb !important;
    }

    /* FIX CRITIQUE: Couleur du texte dans les options du select */
    .flatpickr-current-month .flatpickr-monthDropdown-months option {
        background-color: white !important;
        color: #111827 !important;
        padding: 8px;
    }

    /* Input Année */
    .flatpickr-current-month input.cur-year {
        background: white !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 0.5rem !important;
        color: #111827 !important;
        font-weight: 600 !important;
        padding: 0.25rem 0.5rem !important;
        height: auto !important;
        transition: all 0.2s;
    }

    .flatpickr-current-month input.cur-year:hover,
    .flatpickr-current-month input.cur-year:focus {
        border-color: #3b82f6 !important;
        background-color: #eff6ff !important;
    }

    /* Boutons navigation plus subtils */
    .flatpickr-months .flatpickr-prev-month,
    .flatpickr-months .flatpickr-next-month {
        top: 1.25rem !important;
        padding: 0.5rem !important;
        height: 2rem !important;
        width: 2rem !important;
        border-radius: 0.5rem;
        fill: #6b7280 !important;
    }

    .flatpickr-months .flatpickr-prev-month:hover,
    .flatpickr-months .flatpickr-next-month:hover {
        background: #f3f4f6 !important;
        fill: #111827 !important;
    }

    /* Jours de la semaine */
    .flatpickr-weekdays {
        background: #f9fafb !important;
        padding: 0.75rem 0.5rem !important;
        margin-bottom: 0.5rem;
    }

    .flatpickr-weekday {
        color: #6b7280 !important;
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Corps du calendrier */
    .flatpickr-days {
        width: 100% !important;
        padding: 0 0.5rem 0.75rem 0.5rem !important;
    }

    .dayContainer {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        justify-content: space-around !important;
    }

    /* Jours du mois */
    .flatpickr-day {
        max-width: 38px !important;
        height: 38px !important;
        line-height: 38px !important;
        margin: 2px !important;
        border-radius: 0.6rem !important;
        color: #374151 !important;
        font-weight: 500;
        border: 1px solid transparent;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Aujourd'hui */
    .flatpickr-day.today {
        background: #eff6ff !important;
        border: 1px solid #bfdbfe !important;
        color: #2563eb !important;
        font-weight: 600;
    }

    .error-state .flatpickr-day.today {
        background: #fef2f2 !important;
        border-color: #fecaca !important;
        color: #dc2626 !important;
    }

    /* Sélectionné */
    .flatpickr-day.selected,
    .flatpickr-day.selected:hover {
        background: #2563eb !important;
        color: white !important;
        border-color: #2563eb !important;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        font-weight: 600;
    }

    .error-state .flatpickr-day.selected,
    .error-state .flatpickr-day.selected:hover {
        background: #dc2626 !important;
        border-color: #dc2626 !important;
        box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.3);
    }

    /* Hover simple */
    .flatpickr-day:hover:not(.selected):not(.flatpickr-disabled):not(.today) {
        background: #f3f4f6 !important;
        transform: scale(1.1);
    }

    /* Désactivé */
    .flatpickr-day.flatpickr-disabled {
        color: #d1d5db !important;
        cursor: not-allowed;
        background: transparent !important;
    }

    /* Input avec erreur - animations */
    .datepicker-input.border-red-500 {
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        10%,
        30%,
        50%,
        70%,
        90% {
            transform: translateX(-2px);
        }

        20%,
        40%,
        60%,
        80% {
            transform: translateX(2px);
        }
    }

    /* Masque de saisie - style */
    .datepicker-input:focus {
        outline: none;
    }

    .datepicker-input::placeholder {
        color: #9ca3af;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>

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