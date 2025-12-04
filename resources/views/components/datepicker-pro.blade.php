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
        ? 'datepicker-input !bg-red-50 border-2 border-red-500 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5 transition-all duration-200 placeholder-red-400'
        : 'datepicker-input !bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 block w-full pl-10 p-2.5 transition-all duration-200';
    
    // Classes pour l'icône
    $iconClasses = $error 
        ? 'text-red-500 animate-pulse' 
        : 'text-gray-500';
        
    // Classes pour le conteneur
    $containerClasses = $error
        ? 'relative group error-state'
        : 'relative group';
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
    @if($label)
        <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium {{ $error ? 'text-red-700' : 'text-gray-900' }}">
            {{ $label }}
            @if($required)
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif

    <div class="{{ $containerClasses }}">
        {{-- Icône calendrier --}}
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none z-10">
            <x-iconify icon="lucide:calendar-days" class="w-4 h-4 {{ $iconClasses }}" />
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
            {{ $attributes->except(['class']) }}
        />
        
        {{-- Bouton clear (visible si il y a une valeur et pas d'erreur) --}}
        @if(!$disabled)
            <button 
                type="button" 
                class="clear-date absolute inset-y-0 right-2 flex items-center pr-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                style="display: none;"
                onclick="clearDateInput('{{ $inputId }}')"
            >
                <x-iconify icon="lucide:x-circle" class="w-4 h-4 text-gray-400 hover:text-gray-600" />
            </button>
        @endif
    </div>

    {{-- Messages d'erreur ou d'aide --}}
    @if($error)
        <div class="mt-2 flex items-start animate-fadeIn">
            <x-iconify icon="lucide:alert-triangle" class="w-4 h-4 text-red-600 mr-1.5 mt-0.5 flex-shrink-0" />
            <div>
                <p class="text-sm text-red-600 font-medium">{{ $error }}</p>
                <p class="text-xs text-red-500 mt-0.5">Format attendu: JJ/MM/AAAA (exemple: {{ date('d/m/Y') }})</p>
            </div>
        </div>
    @elseif($helpText)
        <p class="mt-2 text-sm text-gray-600 flex items-start">
            <x-iconify icon="lucide:info-circle" class="w-4 h-4 text-gray-400 mr-1.5 mt-0.5 flex-shrink-0" />
            <span>{{ $helpText }}</span>
        </p>
    @endif
</div>

@once
    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
    <style>
        /* 🎨 FLATPICKR ENTERPRISE-GRADE ULTRA-PRO - ZenFleet 2025 */
        
        /* Animation pour les erreurs */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
        
        /* Calendrier principal */
        .flatpickr-calendar {
            background-color: white !important;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.75rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            font-family: inherit;
            margin-top: 4px;
        }
        
        /* Calendrier pour input avec erreur */
        .error-state .flatpickr-calendar {
            border: 2px solid rgb(239 68 68) !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }

        /* En-tête (mois/année) */
        .flatpickr-months {
            background: linear-gradient(135deg, rgb(37 99 235) 0%, rgb(59 130 246) 100%) !important;
            border-radius: 0.75rem 0.75rem 0 0;
            padding: 0.875rem 0;
        }
        
        .error-state .flatpickr-months {
            background: linear-gradient(135deg, rgb(220 38 38) 0%, rgb(239 68 68) 100%) !important;
        }

        .flatpickr-months .flatpickr-month,
        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-current-month input {
            background-color: transparent !important;
            color: white !important;
            font-weight: 600;
            font-size: 1rem;
        }

        /* Boutons navigation */
        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            fill: white !important;
            transition: all 0.2s;
        }

        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            fill: rgb(219 234 254) !important;
            transform: scale(1.15);
        }

        /* Jours de la semaine */
        .flatpickr-weekdays {
            background-color: rgb(249 250 251) !important;
            padding: 0.625rem 0;
            border-bottom: 1px solid rgb(229 231 235);
        }

        .flatpickr-weekday {
            color: rgb(107 114 128) !important;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Corps du calendrier */
        .flatpickr-days {
            background-color: white !important;
            border-radius: 0 0 0.75rem 0.75rem;
        }

        /* Jours du mois */
        .flatpickr-day {
            color: rgb(17 24 39) !important;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid transparent;
            margin: 2px;
        }

        .flatpickr-day.today {
            border: 2px solid rgb(37 99 235) !important;
            font-weight: 700;
            color: rgb(37 99 235) !important;
            background-color: rgb(239 246 255) !important;
        }
        
        .error-state .flatpickr-day.today {
            border: 2px solid rgb(239 68 68) !important;
            color: rgb(239 68 68) !important;
            background-color: rgb(254 242 242) !important;
        }

        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background-color: rgb(37 99 235) !important;
            border-color: rgb(37 99 235) !important;
            color: white !important;
            font-weight: 700;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
        }
        
        .error-state .flatpickr-day.selected,
        .error-state .flatpickr-day.selected:hover {
            background-color: rgb(239 68 68) !important;
            border-color: rgb(239 68 68) !important;
        }

        .flatpickr-day:hover:not(.selected):not(.flatpickr-disabled):not(.today) {
            background-color: rgb(243 244 246) !important;
            border-color: rgb(229 231 235) !important;
            color: rgb(17 24 39) !important;
            transform: scale(1.05);
        }

        .flatpickr-day.flatpickr-disabled {
            color: rgb(209 213 219) !important;
            opacity: 0.4;
            cursor: not-allowed;
        }

        /* Input avec erreur - animations */
        .datepicker-input.border-red-500 {
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
            20%, 40%, 60%, 80% { transform: translateX(2px); }
        }
        
        /* Masque de saisie - style */
        .datepicker-input:focus {
            outline: none;
        }
        
        .datepicker-input::placeholder {
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }
        
        /* Tooltip pour format */
        .datepicker-input[title]:hover::after {
            content: attr(title);
            position: absolute;
            bottom: -30px;
            left: 0;
            background: rgb(31 41 55);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
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
                    monthSelectorType: 'dropdown',
                    yearSelectorType: 'dropdown',
                    nextArrow: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>',
                    prevArrow: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>',
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
                            el.classList.remove('border-red-500', '!bg-red-50');
                            el.classList.add('border-gray-300', '!bg-white');
                            
                            // Retirer aussi l'animation
                            const errorDiv = el.closest('.group').parentElement.querySelector('.animate-fadeIn');
                            if (errorDiv) {
                                errorDiv.style.display = 'none';
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
                                this.classList.remove('border-gray-300', '!bg-white');
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
