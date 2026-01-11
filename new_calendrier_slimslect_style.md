# 📅 ZENFLEET - DOCUMENTATION COMPLÈTE: DATEPICKER & SLIMSELECT

> **Version:** 3.0 Ultra-Pro Enterprise  
> **Date de génération:** 11 Janvier 2026  
> **Application:** ZenFleet - Gestion de Flotte SAAS Multi-tenant

---

## TABLE DES MATIÈRES

1. [🎯 Vue d'Ensemble](#1-vue-densemble)
2. [📅 PARTIE 1: DATEPICKER (Calendrier)](#2-partie-1-datepicker-calendrier)
   - [Architecture Globale](#21-architecture-globale)
   - [Composant `<x-datepicker>`](#22-composant-x-datepicker)
   - [CSS & Styling](#23-css--styling)
   - [Exemples d'Utilisation](#24-exemples-dutilisation)
   - [Calendrier Alpine.js Custom (Modal)](#25-calendrier-alpinejs-custom-modal)
3. [📋 PARTIE 2: SLIMSELECT (Listes déroulantes)](#3-partie-2-slimselect-listes-déroulantes)
   - [Architecture Globale](#31-architecture-globale)
   - [Composant `<x-slim-select>`](#32-composant-x-slim-select)
   - [CSS & Styling](#33-css--styling) 
   - [Exemples d'Utilisation](#34-exemples-dutilisation)
   - [Intégration Livewire](#35-intégration-livewire)
4. [🔧 Configuration JavaScript Globale](#4-configuration-javascript-globale)
5. [📌 Checklist d'Implémentation](#5-checklist-dimplémentation)

---

## 1. VUE D'ENSEMBLE

### Technologies Utilisées

| Composant | Librairie | Version | Rôle |
|-----------|-----------|---------|------|
| **Datepicker** | Flowbite Datepicker | Latest | Sélection de dates avec calendrier |
| **Datepicker (time)** | Flatpickr | 4.6.13 | Datetime et Timepicker |
| **SlimSelect** | SlimSelect | 2.8.2 | Listes déroulantes améliorées |
| **Alpine.js** | Alpine.js | 3.4.2 | Réactivité côté client |

### Fichiers Clés

```
resources/
├── views/components/
│   ├── datepicker.blade.php      # Composant Datepicker
│   └── slim-select.blade.php     # Composant SlimSelect
├── css/admin/
│   ├── app.css                   # CSS admin avec styles SlimSelect + Flatpickr
│   └── vendor/datepicker.css     # CSS Flowbite Datepicker
└── js/admin/
    └── app.js                    # JavaScript global avec initialisations
```

---

## 2. PARTIE 1: DATEPICKER (CALENDRIER)

### 2.1 Architecture Globale

Le système utilise **Flowbite Datepicker** (basé sur Tailwind) pour les sélections de date simples, avec une configuration française par défaut.

#### Initialisation JavaScript (`resources/js/admin/app.js`)

```javascript
// ✅ Import Flowbite Datepicker globalement
import Datepicker from 'flowbite-datepicker/Datepicker';
import fr from './locales/fr.js'; // Locale française manuelle

// Enregistrer la locale française
Object.assign(Datepicker.locales, { fr });

// Exposer globalement
window.Datepicker = Datepicker;
console.log('📅 Flowbite Datepicker configured globally:', !!window.Datepicker);
```

#### Fichier Locale Française (`resources/js/admin/locales/fr.js`)

Ce fichier doit contenir la configuration de la locale française pour Flowbite:

```javascript
export default {
    days: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
    daysShort: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
    daysMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
    months: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
    monthsShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc"],
    today: "Aujourd'hui",
    clear: "Effacer",
    weekStart: 1, // Lundi = premier jour
    format: "dd/mm/yyyy"
};
```

---

### 2.2 Composant `<x-datepicker>`

#### Code Complet du Composant

**Fichier:** `resources/views/components/datepicker.blade.php`

```blade
{{--
    🎨 ZENFLEET DATEPICKER - ULTRA-PRO ENTERPRISE GRADE
    Based on Flowbite official documentation
    Version: 3.0 Ultra-Pro
    
    Features:
    - French locale support
    - Dual format handling (Y-m-d / d/m/Y)
    - Proper Flowbite initialization
    - Enterprise-grade styling
--}}

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
'placeholder' => 'Sélectionner une date',
])

@php
$inputId = 'datepicker-' . uniqid();
$rawValue = old($name, $value);

// Convert server value to display format (d/m/Y) for Flowbite
$displayValue = '';
if ($rawValue) {
    // Handle Y-m-d format from database
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawValue)) {
        $date = \Carbon\Carbon::createFromFormat('Y-m-d', $rawValue);
        $displayValue = $date->format('d/m/Y');
    }
    // Handle d/m/Y format from old() flash
    elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $rawValue)) {
        $displayValue = $rawValue;
    }
}

// Input classes following Flowbite pattern
$inputClasses = 'block w-full !pl-10 p-2.5 bg-gray-50 border-2 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-all duration-200';
$inputClasses .= $error ? ' border-red-500' : ' border-gray-300';
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}
    x-data="{
        serverDate: '{{ $rawValue }}',
        displayValue: '{{ $displayValue }}',
        picker: null,

        init() {
            this.$nextTick(() => {
                const el = this.$refs.displayInput;
                
                if (typeof window.Datepicker === 'undefined') {
                    console.error('❌ ZenFleet: Datepicker not loaded');
                    return;
                }
                
                // Initialize Flowbite Datepicker
                this.picker = new window.Datepicker(el, {
                    language: 'fr',
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    todayBtn: true,
                    todayBtnMode: 1, // Select today on click
                    clearBtn: true,
                    weekStart: 1,
                    @if($minDate)
                    minDate: '{{ \Carbon\Carbon::parse($minDate)->format('d/m/Y') }}',
                    @endif
                    @if($maxDate)
                    maxDate: '{{ \Carbon\Carbon::parse($maxDate)->format('d/m/Y') }}',
                    @endif
                    orientation: 'bottom left',
                });
                
                // Set initial date if value exists
                if (this.displayValue) {
                    this.picker.setDate(this.displayValue);
                    el.value = this.displayValue;
                }
                
                // Handle date change
                el.addEventListener('changeDate', (e) => {
                    if (e.detail.date) {
                        const d = e.detail.date;
                        const year = d.getFullYear();
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        this.serverDate = `${year}-${month}-${day}`;
                        this.displayValue = `${day}/${month}/${year}`;
                        this.$dispatch('input', this.serverDate);
                    } else {
                        this.serverDate = '';
                        this.displayValue = '';
                        this.$dispatch('input', '');
                    }
                });
                
                // Handle manual clear
                el.addEventListener('input', (e) => {
                    if (!el.value.trim()) {
                        this.serverDate = '';
                        this.displayValue = '';
                        this.$dispatch('input', '');
                    }
                });
            });
        }
    }"
    wire:ignore>

    @if($label)
    <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        {{ $label }}
        @if($required)
        <span class="text-red-500 ml-0.5">*</span>
        @endif
    </label>
    @endif

    <div class="relative">
        {{-- Calendar Icon --}}
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none z-10">
            <svg class="w-4 h-4 {{ $error ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
            </svg>
        </div>

        {{-- Display Input (User sees this) --}}
        <input
            x-ref="displayInput"
            type="text"
            id="{{ $inputId }}"
            class="{{ $inputClasses }}"
            placeholder="{{ $placeholder }}"
            x-model="displayValue"
            @if($disabled) disabled @endif
            @if($required) required @endif
            autocomplete="off"
            readonly>

        {{-- Hidden Input (Server receives this in Y-m-d format) --}}
        <input type="hidden" name="{{ $name }}" x-model="serverDate">
    </div>

    @if($error)
    <p class="mt-2 text-sm text-red-600 dark:text-red-500 flex items-center gap-1">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        {{ $error }}
    </p>
    @elseif($helpText)
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $helpText }}</p>
    @endif
</div>
```

#### Props du Composant

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `''` | Nom du champ pour la soumission du formulaire |
| `label` | string|null | `null` | Label affiché au-dessus du champ |
| `error` | string|null | `null` | Message d'erreur à afficher |
| `helpText` | string|null | `null` | Texte d'aide sous le champ |
| `required` | bool | `false` | Marque le champ comme requis |
| `disabled` | bool | `false` | Désactive le champ |
| `value` | string|null | `null` | Valeur initiale (format `Y-m-d`) |
| `minDate` | string|null | `null` | Date minimum sélectionnable |
| `maxDate` | string|null | `null` | Date maximum sélectionnable |
| `placeholder` | string | `'Sélectionner une date'` | Placeholder du champ |

---

### 2.3 CSS & Styling

#### Styles Flatpickr Enterprise (`resources/css/admin/app.css`)

```css
/* ====================================
   🎨 FLATPICKR ENTERPRISE-GRADE STYLES
   ==================================== */
.flatpickr-calendar {
    background-color: white !important;
    border: 1px solid rgb(229 231 235);
    border-radius: 0.75rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    font-family: inherit;
}

/* En-tête (mois/année) - Bleu blue-600 premium */
.flatpickr-months {
    background: rgb(37 99 235) !important;
    border-radius: 0.75rem 0.75rem 0 0;
    padding: 0.875rem 0;
}

.flatpickr-months .flatpickr-month,
.flatpickr-current-month .flatpickr-monthDropdown-months {
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
}

/* Jours du mois */
.flatpickr-day {
    color: rgb(17 24 39) !important;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.2s;
    border: 1px solid transparent;
}

.flatpickr-day.today {
    border: 2px solid rgb(37 99 235) !important;
    font-weight: 700;
    color: rgb(37 99 235) !important;
    background-color: rgb(239 246 255) !important;
}

.flatpickr-day.selected,
.flatpickr-day.selected:hover {
    background-color: rgb(37 99 235) !important;
    border-color: rgb(37 99 235) !important;
    color: white !important;
    font-weight: 700;
    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
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
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}

/* État d'erreur */
.datepicker-input.border-red-500 {
    animation: shake 0.5s ease-in-out;
}

.error-state .flatpickr-calendar {
    box-shadow: 0 0 0 2px rgb(239 68 68), 0 20px 25px -5px rgba(239, 68, 68, 0.15) !important;
}
```

---

### 2.4 Exemples d'Utilisation

#### Utilisation Simple

```blade
<x-datepicker
    name="acquisition_date"
    label="Date d'acquisition"
    :value="old('acquisition_date')"
    :error="$errors->first('acquisition_date')"
    placeholder="Choisir une date"
    required
/>
```

#### Avec Contraintes de Date

```blade
<x-datepicker
    name="acquisition_date"
    label="Date d'acquisition"
    :value="old('acquisition_date')"
    :error="$errors->first('acquisition_date')"
    placeholder="Choisir une date"
    required
    :maxDate="date('Y-m-d')"
    helpText="Date d'achat du véhicule"
/>
```

#### Intégration Livewire (via x-on:input)

```blade
{{-- Dans un filtre Livewire --}}
<x-datepicker
    name="date_from"
    :value="$date_from"
    placeholder="JJ/MM/AAAA"
    x-on:input="$wire.set('date_from', $event.detail)" 
/>
```

> **Important:** L'événement `input` dispatch la valeur au format `Y-m-d` (ex: `2025-01-15`) via `$event.detail`.

---

### 2.5 Calendrier Alpine.js Custom (Modal)

Pour les modales où le positionnement du calendrier Flowbite peut poser problème, un calendrier Alpine.js custom est utilisé:

```blade
<div class="col-span-2" x-data="{
    showCalendar: false,
    selectedDate: @entangle('endDate'),
    displayDate: '',
    currentMonth: new Date().getMonth(),
    currentYear: new Date().getFullYear(),
    days: [],
    
    init() {
        this.parseDate();
        this.generateCalendar();
        $watch('selectedDate', value => this.parseDate());
    },
    
    parseDate() {
        if (this.selectedDate) {
            const parts = this.selectedDate.split('-');
            if (parts.length === 3) {
                this.displayDate = `${parts[2]}/${parts[1]}/${parts[0]}`;
                this.currentYear = parseInt(parts[0]);
                this.currentMonth = parseInt(parts[1]) - 1;
            }
        } else {
            this.displayDate = '';
        }
    },
    
    generateCalendar() {
        this.days = [];
        const firstDay = new Date(this.currentYear, this.currentMonth, 1);
        const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
        const startPadding = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
        
        for (let i = 0; i < startPadding; i++) {
            this.days.push({ day: '', disabled: true });
        }
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        for (let d = 1; d <= lastDay.getDate(); d++) {
            const date = new Date(this.currentYear, this.currentMonth, d);
            this.days.push({
                day: d,
                disabled: date > today, // Désactiver dates futures
                isToday: date.getTime() === today.getTime(),
                isSelected: this.selectedDate === `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`
            });
        }
    },
    
    selectDay(day) {
        if (day.disabled || !day.day) return;
        this.selectedDate = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day.day).padStart(2, '0')}`;
        this.displayDate = `${String(day.day).padStart(2, '0')}/${String(this.currentMonth + 1).padStart(2, '0')}/${this.currentYear}`;
        this.generateCalendar();
        this.showCalendar = false;
    },
    
    monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
}">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Date de fin <span class="text-red-600">*</span>
    </label>
    <div class="relative">
        <input
            type="text"
            x-model="displayDate"
            @click="showCalendar = !showCalendar"
            readonly
            placeholder="JJ/MM/AAAA"
            class="block w-full border-gray-300 rounded-lg text-sm cursor-pointer pl-10">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <x-iconify icon="lucide:calendar" class="w-4 h-4 text-gray-400" />
        </div>
        
        {{-- Dropdown Calendar --}}
        <div x-show="showCalendar" x-transition @click.away="showCalendar = false" 
             class="absolute z-50 mt-2 bg-white rounded-xl shadow-2xl border border-gray-200 p-4 w-72 bottom-full mb-2">
            <div class="flex items-center justify-between mb-4">
                <button type="button" @click="prevMonth()" class="p-1 hover:bg-gray-100 rounded-lg">
                    <x-iconify icon="heroicons:chevron-left" class="w-5 h-5 text-gray-600" />
                </button>
                <span class="font-semibold text-gray-900" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                <button type="button" @click="nextMonth()" class="p-1 hover:bg-gray-100 rounded-lg">
                    <x-iconify icon="heroicons:chevron-right" class="w-5 h-5 text-gray-600" />
                </button>
            </div>
            <div class="grid grid-cols-7 gap-1 mb-2">
                <template x-for="day in ['Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di']">
                    <div class="text-center text-xs font-semibold text-gray-500 py-1" x-text="day"></div>
                </template>
            </div>
            <div class="grid grid-cols-7 gap-1">
                <template x-for="(day, index) in days" :key="index">
                    <button type="button" @click="selectDay(day)" :disabled="day.disabled"
                        :class="{
                            'bg-orange-600 text-white': day.isSelected,
                            'bg-orange-100 text-orange-800': day.isToday && !day.isSelected,
                            'hover:bg-gray-100': !day.disabled && !day.isSelected,
                            'text-gray-300 cursor-not-allowed': day.disabled,
                            'text-gray-700': !day.disabled && !day.isSelected
                        }"
                        class="w-8 h-8 flex items-center justify-center text-sm rounded-lg transition-colors" 
                        x-text="day.day">
                    </button>
                </template>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-200">
                <button type="button" @click="clearDate(); showCalendar = false" 
                        class="w-full text-center text-xs text-gray-600 hover:text-gray-900">
                    Effacer
                </button>
            </div>
        </div>
    </div>
</div>
```

---

## 3. PARTIE 2: SLIMSELECT (LISTES DÉROULANTES)

### 3.1 Architecture Globale

**SlimSelect** remplace TomSelect dans l'application ZenFleet. Il est initialisé globalement.

#### Initialisation JavaScript (`resources/js/admin/app.js`)

```javascript
// ✅ ENTERPRISE: Import SlimSelect globally
import SlimSelect from 'slim-select';
import 'slim-select/styles'; // Styles de base

window.SlimSelect = SlimSelect;
```

---

### 3.2 Composant `<x-slim-select>`

#### Code Complet du Composant

**Fichier:** `resources/views/components/slim-select.blade.php`

```blade
@props([
'name' => '',
'label' => null,
'error' => null,
'helpText' => null,
'required' => false,
'disabled' => false,
'options' => [],
'selected' => null,
'placeholder' => 'Sélectionnez...',
'multiple' => false,
'searchable' => true,
])

@php
$selectId = 'slimselect-' . $name . '-' . uniqid();
@endphp

<div wire:ignore
    x-data="{
        instance: null,
        initSelect() {
            if (this.instance) return;
            this.instance = new SlimSelect({
                select: this.$refs.select,
                settings: {
                    showSearch: {{ $searchable ? 'true' : 'false' }},
                    searchPlaceholder: 'Rechercher...',
                    searchText: 'Aucun résultat',
                    searchingText: 'Recherche...',
                    placeholderText: '{{ $placeholder }}',
                    allowDeselect: true,
                    hideSelected: false,
                },
                events: {
                    afterChange: (newVal) => {
                        // Dispatch event for Livewire/Alpine
                        this.$refs.select.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            });
        }
    }"
    x-init="initSelect()"
    {{ $attributes->merge(['class' => '']) }}>

    @if($label)
    <label for="{{ $selectId }}" class="block mb-2 text-sm font-medium text-gray-900">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    <select
        x-ref="select"
        name="{{ $name }}"
        id="{{ $selectId }}"
        class="slimselect-field w-full"
        @if($disabled) disabled @endif
        @if($multiple) multiple @endif
        {{ $attributes->except(['class']) }}>

        {{-- Options --}}
        @if($slot->isNotEmpty())
        {{ $slot }}
        @else
        @if(!$multiple)
        <option value="" data-placeholder="true">{{ $placeholder }}</option>
        @endif

        @foreach($options as $value => $optionLabel)
        <option
            value="{{ $value }}"
            {{ (is_array($selected) ? in_array($value, $selected) : old($name, $selected) == $value) ? 'selected' : '' }}>
            {{ $optionLabel }}
        </option>
        @endforeach
        @endif
    </select>

    @if($error)
    <p class="mt-2 text-sm text-red-600 flex items-start">
        <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
        <span>{{ $error }}</span>
    </p>
    @elseif($helpText)
    <p class="mt-2 text-sm text-gray-500">
        {{ $helpText }}
    </p>
    @endif
</div>
```

#### Props du Composant

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `''` | Nom du champ pour la soumission |
| `label` | string|null | `null` | Label affiché au-dessus |
| `error` | string|null | `null` | Message d'erreur |
| `helpText` | string|null | `null` | Texte d'aide |
| `required` | bool | `false` | Marque comme requis |
| `disabled` | bool | `false` | Désactive le champ |
| `options` | array | `[]` | Options `[value => label]` |
| `selected` | mixed | `null` | Valeur(s) sélectionnée(s) |
| `placeholder` | string | `'Sélectionnez...'` | Placeholder |
| `multiple` | bool | `false` | Sélection multiple |
| `searchable` | bool | `true` | Active la recherche |

---

### 3.3 CSS & Styling

#### Variables CSS SlimSelect (`resources/css/admin/app.css`)

```css
/* ========================================
   🎨 ZENFLEET SLIMSELECT ENTERPRISE THEME
   ======================================== */

/* 🎯 CRITICAL: Override SlimSelect Native CSS Variables */
.ss-main,
.ss-content {
    /* Primary color - ZenFleet blue-500 for selected options */
    --ss-primary-color: #3b82f6 !important;

    /* Background colors - Match x-input gray-50 */
    --ss-bg-color: #f9fafb !important;
    --ss-content-bg-color: #ffffff !important;

    /* Border colors - Match x-input gray-300 */
    --ss-border-color: #d1d5db !important;

    /* Text colors */
    --ss-font-color: #111827 !important;
    --ss-font-placeholder-color: #9ca3af !important;

    /* Border radius - Match x-input rounded-lg */
    --ss-border-radius: 0.5rem !important;

    /* Spacing */
    --ss-spacing-l: 12px !important;
    --ss-spacing-m: 8px !important;
    --ss-spacing-s: 4px !important;
}

/* Additional ZenFleet-specific variables */
:root {
    --ss-main-height: 42px;
    --ss-focus-color: #3b82f6;
    --ss-error-color: #dc2626;
    --ss-hover-border-color: #9ca3af;
}

/* ✅ Main Container - Force gray-50 background */
body .ss-main,
html body .ss-main,
.ss-main {
    background-color: #f9fafb !important;
    border: 1px solid #d1d5db !important;
    color: #111827 !important;
    border-radius: 0.5rem !important;
    padding: 0 !important;
    min-height: 42px !important;
    transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out !important;
    box-shadow: none !important;
}

/* Hover state */
body .ss-main:hover:not(:focus-within) {
    border-color: #9ca3af !important;
}

/* Focus state - Blue ring */
body .ss-main:focus-within {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3) !important;
    background-color: #ffffff !important;
    outline: none !important;
}

/* Selected value styling */
body .ss-main .ss-values .ss-single {
    padding: 10px 12px !important;
    font-size: 0.875rem !important;
    line-height: 1.25rem !important;
    font-weight: 400 !important;
    color: #111827 !important;
    background-color: transparent !important;
}

/* Placeholder styling */
body .ss-main .ss-values .ss-placeholder {
    padding: 10px 12px !important;
    font-size: 0.875rem !important;
    color: #9ca3af !important;
}

/* Dropdown content container */
body .ss-content {
    background-color: #ffffff !important;
    margin-top: 4px !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 0.5rem !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
}

/* Search field in dropdown */
body .ss-content .ss-search {
    background-color: #f9fafb !important;
    border-bottom: 1px solid #e5e7eb !important;
    padding: 8px !important;
}

body .ss-content .ss-search input {
    font-size: 0.875rem !important;
    padding: 10px 12px !important;
    border-radius: 6px !important;
    background-color: #ffffff !important;
    border: 1px solid #d1d5db !important;
}

body .ss-content .ss-search input:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}

/* Options list */
body .ss-content .ss-list .ss-option {
    font-size: 0.875rem !important;
    padding: 10px 12px !important;
    color: #111827 !important;
    transition: background-color 0.15s ease !important;
}

/* Option hover */
body .ss-content .ss-list .ss-option:hover {
    background-color: #eff6ff !important;
}

/* ✅ CRITICAL: Selected option - ZenFleet blue-500 */
body .ss-content .ss-list .ss-option.ss-highlighted,
body .ss-content .ss-list .ss-option:not(.ss-disabled).ss-selected {
    background-color: #3b82f6 !important;
    color: #ffffff !important;
}

/* Selected option checkmark */
body .ss-content .ss-list .ss-option:not(.ss-disabled).ss-selected::after {
    content: '✓' !important;
    margin-left: auto !important;
    font-weight: 600 !important;
    color: #ffffff !important;
}

/* Hide placeholder option in dropdown */
body .ss-content .ss-list .ss-option[data-placeholder="true"] {
    display: none !important;
}

/* Dropdown arrow */
body .ss-main .ss-arrow path {
    stroke-width: 1.5 !important;
    stroke: #6b7280 !important;
}

/* Error state validation */
.slimselect-error .ss-main {
    border-color: #dc2626 !important;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
    background-color: #fef2f2 !important;
}

/* Animation */
body .ss-content.ss-open-below,
body .ss-content.ss-open-above {
    animation: zenfleetSlideIn 0.2s ease-out !important;
}

@keyframes zenfleetSlideIn {
    from { opacity: 0; transform: scaleY(0.95) translateY(-4px); }
    to { opacity: 1; transform: scaleY(1) translateY(0); }
}

/* Responsive Mobile */
@media (max-width: 640px) {
    .ss-content .ss-list .ss-option {
        padding: 12px !important;
        min-height: 44px !important;
    }
}
```

---

### 3.4 Exemples d'Utilisation

#### Utilisation Simple

```blade
<x-slim-select
    name="vehicle_type_id"
    label="Type de Véhicule"
    :options="$vehicleTypes->pluck('name', 'id')->toArray()"
    :selected="old('vehicle_type_id')"
    placeholder="Sélectionnez un type..."
    required
    :error="$errors->first('vehicle_type_id')"
/>
```

#### Sélection Multiple

```blade
<x-slim-select
    name="users"
    label="Utilisateurs Autorisés"
    :options="$users->mapWithKeys(fn($user) => [$user->id => $user->name . ' (' . $user->email . ')'])->toArray()"
    :selected="old('users', [])"
    placeholder="Rechercher des utilisateurs..."
    :multiple="true"
    :error="$errors->first('users')"
    helpText="Sélectionnez les utilisateurs autorisés"
/>
```

#### Avec Options en Slot

```blade
<x-slim-select
    name="status"
    wire:model.live="status"
    placeholder="Tous les statuts">
    <option value="">Tous les statuts</option>
    <option value="scheduled">Planifiée</option>
    <option value="active">Active</option>
    <option value="completed">Complétée</option>
    <option value="cancelled">Annulée</option>
</x-slim-select>
```

---

### 3.5 Intégration Livewire

#### Initialisation dans Modales Livewire

Pour les SlimSelect dans des modales Livewire qui s'ouvrent/ferment dynamiquement:

```javascript
// Dans @push('scripts')
document.addEventListener('livewire:initialized', () => {
    let modalEndTimeSlimSelect = null;

    // Initialize SlimSelect when modal opens
    Livewire.hook('morph.updated', ({ el, component }) => {
        const selectEl = document.getElementById('modal_end_time');
        
        if (selectEl && !modalEndTimeSlimSelect) {
            try {
                modalEndTimeSlimSelect = new SlimSelect({
                    select: selectEl,
                    settings: {
                        showSearch: true,
                        searchPlaceholder: 'Rechercher...',
                        searchText: 'Aucun résultat',
                        placeholderText: 'Sélectionner...',
                    },
                    events: {
                        afterChange: (newVal) => {
                            if (newVal && newVal.length > 0) {
                                @this.set('endTime', newVal[0].value);
                            }
                        }
                    }
                });
                console.log('✅ Modal End Time SlimSelect initialized');
            } catch (error) {
                console.error('❌ Error initializing SlimSelect:', error);
            }
        }
    });
});
```

#### Pattern `wire:ignore`

Toujours utiliser `wire:ignore` sur le conteneur pour empêcher Livewire de détruire l'instance SlimSelect:

```blade
<div wire:ignore>
    <select id="my_select" wire:model="myField">
        <!-- options -->
    </select>
</div>
```

---

## 4. CONFIGURATION JAVASCRIPT GLOBALE

### Imports CSS Requis

```javascript
// resources/js/admin/app.js
import '../../css/admin/app.css';
import 'slim-select/styles';
import 'flatpickr/dist/flatpickr.min.css';
```

### Imports JavaScript

```javascript
import SlimSelect from 'slim-select';
import Datepicker from 'flowbite-datepicker/Datepicker';
import fr from './locales/fr.js';
import flatpickr from 'flatpickr';
import { French } from 'flatpickr/dist/l10n/fr.js';

// Exposer globalement
window.SlimSelect = SlimSelect;
window.Datepicker = Datepicker;
window.flatpickr = flatpickr;

// Configurer locales
Object.assign(Datepicker.locales, { fr });
flatpickr.localize(French);
```

---

## 5. CHECKLIST D'IMPLÉMENTATION

### Pour un nouveau Datepicker

- [ ] Utiliser `<x-datepicker>` avec les props nécessaires
- [ ] Spécifier `name` pour la soumission du formulaire
- [ ] La valeur est transmise au format `Y-m-d` au serveur
- [ ] Pour Livewire, utiliser `x-on:input="$wire.set('field', $event.detail)"`

### Pour un nouveau SlimSelect

- [ ] Utiliser `<x-slim-select>` avec les props nécessaires
- [ ] Passer `options` au format `[value => label]`
- [ ] Pour sélection multiple, ajouter `:multiple="true"`
- [ ] Dans un contexte Livewire, entourer de `wire:ignore`

### Dépendances NPM

```json
{
  "dependencies": {
    "flatpickr": "^4.6.13",
    "slim-select": "^2.8.2",
    "flowbite-datepicker": "^1.3.0"
  }
}
```

### Imports CSS

```css
/* resources/css/admin/app.css */
@import 'slim-select/styles';
@import './vendor/datepicker.css';
```

---

> **Note:** Ce document est la référence complète pour l'implémentation des datepickers et SlimSelects dans l'application ZenFleet. Toute modification doit respecter ces patterns pour assurer la cohérence de l'interface utilisateur.
