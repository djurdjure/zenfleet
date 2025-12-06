# SlimSelect Style Guide - ZenFleet Enterprise

> **Version**: 1.0-Enterprise  
> **Date**: 2025-12-06  
> **Status**: Production Ready  
> **Référence**: Pages de création/modification de véhicules

---

## Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture du composant](#architecture-du-composant)
3. [Variables CSS](#variables-css)
4. [Styles entreprise-grade](#styles-entreprise-grade)
5. [Composant Blade](#composant-blade)
6. [Initialisation JavaScript](#initialisation-javascript)
7. [États et validations](#états-et-validations)
8. [Guide de migration TomSelect → SlimSelect](#guide-de-migration-tomselect--slimselect)
9. [Responsive et Accessibilité](#responsive-et-accessibilité)

---

## Vue d'ensemble

SlimSelect est la bibliothèque de remplacement de TomSelect dans ZenFleet. Elle offre:

- ✅ Bundle plus léger (~15KB vs ~40KB pour TomSelect)
- ✅ API plus simple et moderne
- ✅ Meilleure intégration avec Alpine.js
- ✅ Support natif des thèmes personnalisés
- ✅ Performances optimisées

### Fichiers clés

| Fichier | Chemin | Rôle |
|---------|--------|------|
| **Composant Blade** | `resources/views/components/slim-select.blade.php` | Composant réutilisable |
| **Styles CSS** | `resources/views/partials/slimselect-styles.blade.php` | Thème ZenFleet enterprise |
| **Configuration Vite** | `vite.config.js` | Bundle dans `ui-public` |
| **Package** | `package.json` | `slim-select@2.8.2` |

---

## Architecture du composant

### Structure HTML générée

```html
<!-- Wrapper du composant Blade -->
<div class="">
    <!-- Label -->
    <label for="slimselect-name-uniqueId" class="block mb-2 text-sm font-medium text-gray-900">
        Label du champ
        <span class="text-red-500">*</span> <!-- Si required -->
    </label>

    <!-- Select natif (remplacé par SlimSelect) -->
    <select 
        name="field_name" 
        id="slimselect-name-uniqueId"
        class="slimselect-field w-full"
        data-slimselect="true"
        data-placeholder="Sélectionnez..."
        required>
        <option value="" data-placeholder="true">Placeholder</option>
        <option value="1">Option 1</option>
        <option value="2" selected>Option 2</option>
    </select>

    <!-- Message d'erreur serveur -->
    <p class="mt-2 text-sm text-red-600 flex items-start">
        <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
        <span>Message d'erreur</span>
    </p>

    <!-- Ou texte d'aide -->
    <p class="mt-2 text-sm text-gray-500">Texte d'aide contextuel</p>

    <!-- Erreur dynamique Alpine.js -->
    <p x-show="fieldErrors && fieldErrors['field_name'] && touchedFields && touchedFields['field_name']"
       class="mt-2 text-sm text-red-600 flex items-start font-medium">
        <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
        <span>Ce champ est obligatoire</span>
    </p>
</div>
```

### Structure DOM SlimSelect

SlimSelect génère automatiquement cette structure:

```html
<!-- Container principal -->
<div class="ss-main">
    <!-- Zone de valeur sélectionnée -->
    <div class="ss-values">
        <div class="ss-single">Valeur sélectionnée</div>
        <!-- ou -->
        <div class="ss-placeholder">Placeholder</div>
    </div>
    
    <!-- Flèche dropdown -->
    <div class="ss-arrow">
        <svg>...</svg>
    </div>
</div>

<!-- Contenu du dropdown (ajouté au body) -->
<div class="ss-content ss-open-below">
    <!-- Champ de recherche -->
    <div class="ss-search">
        <input type="search" placeholder="Rechercher...">
    </div>
    
    <!-- Liste des options -->
    <div class="ss-list">
        <div class="ss-option" data-value="1">Option 1</div>
        <div class="ss-option ss-selected" data-value="2">Option 2</div>
        <div class="ss-option ss-highlighted" data-value="3">Option 3</div>
    </div>
</div>
```

---

## Variables CSS

### Variables racine (Custom Properties)

```css
:root {
    /* Dimensions */
    --ss-main-height: 42px;
    
    /* Couleurs principales */
    --ss-primary-color: #2563eb;      /* blue-600 - Accent principal */
    --ss-bg-color: #ffffff;           /* Fond blanc */
    --ss-font-color: #111827;         /* gray-900 - Texte principal */
    --ss-font-placeholder-color: #9ca3af; /* gray-400 - Placeholder */
    --ss-border-color: #d1d5db;       /* gray-300 - Bordures */
    
    /* Arrondis */
    --ss-border-radius: 0.5rem;       /* rounded-lg */
    
    /* Espacements */
    --ss-spacing-l: 10px;
    --ss-spacing-m: 8px;
    --ss-spacing-s: 4px;
    
    /* Animations */
    --ss-animation-timing: 0.2s;
    
    /* États */
    --ss-focus-color: #3b82f6;        /* blue-500 - Focus */
    --ss-error-color: #dc2626;        /* red-600 - Erreur */
}

/* Variables responsive mobile */
@media (max-width: 640px) {
    :root {
        --ss-main-height: 44px;       /* Plus grand pour touch */
        --ss-content-height: 240px;
    }
}
```

---

## Styles entreprise-grade

### Container principal (.ss-main)

```css
.ss-main {
    background-color: #f9fafb;        /* gray-50 - Fond léger */
    border-color: #d1d5db;            /* gray-300 */
    color: #111827;                   /* gray-900 */
    border-radius: 0.5rem;            /* rounded-lg */
    padding: 2px 0;
    min-height: 42px;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
}

/* État focus */
.ss-main:focus-within {
    border-color: #3b82f6;            /* blue-500 */
    box-shadow: 0 0 0 1px #3b82f6;    /* ring-1 ring-blue-500 */
    background-color: #ffffff;
}
```

### Valeurs et placeholder

```css
.ss-main .ss-values .ss-single {
    padding: 4px 10px;
    font-size: 0.875rem;              /* text-sm = 14px */
    line-height: 1.25rem;             /* leading-5 */
    font-weight: 400;
}

.ss-main .ss-values .ss-placeholder {
    font-size: 0.875rem;
    font-style: normal;               /* Pas d'italique */
}
```

### Dropdown content

```css
.ss-content {
    margin-top: 4px;
    box-shadow:
        0 10px 15px -3px rgba(0, 0, 0, 0.1),   /* shadow-lg */
        0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border-color: #e5e7eb;            /* gray-200 */
}

/* Animation d'ouverture */
.ss-content.ss-open-below,
.ss-content.ss-open-above {
    animation: zenfleetSlideIn 0.2s ease-out;
}

@keyframes zenfleetSlideIn {
    from {
        opacity: 0;
        transform: scaleY(0.95) translateY(-4px);
    }
    to {
        opacity: 1;
        transform: scaleY(1) translateY(0);
    }
}
```

### Champ de recherche

```css
.ss-content .ss-search {
    background-color: #f9fafb;        /* gray-50 */
    border-bottom: 1px solid #e5e7eb; /* gray-200 */
    padding: 8px;
}

.ss-content .ss-search input {
    font-size: 0.875rem;
    padding: 10px 12px;
    border-radius: 6px;               /* rounded-md */
}

.ss-content .ss-search input:focus {
    border-color: #3b82f6;            /* blue-500 */
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
```

### Options de la liste

```css
.ss-content .ss-list .ss-option {
    font-size: 0.875rem;
    padding: 10px 10px;
    transition: background-color 0.15s ease, color 0.15s ease;
}

/* Hover */
.ss-content .ss-list .ss-option:hover {
    background-color: #eff6ff;        /* blue-50 */
    color: #111827;                   /* Texte lisible */
}

/* Option sélectionnée / highlighted */
.ss-content .ss-list .ss-option.ss-highlighted,
.ss-content .ss-list .ss-option:not(.ss-disabled).ss-selected {
    background-color: #2563eb;        /* blue-600 */
    color: #ffffff;
}

/* Checkmark pour option sélectionnée */
.ss-content .ss-list .ss-option:not(.ss-disabled).ss-selected::after {
    content: '✓';
    margin-left: auto;
    font-weight: 600;
}

/* Cacher le placeholder dans la liste */
.ss-content .ss-list .ss-option[data-placeholder="true"] {
    display: none !important;
}
```

### État d'erreur

```css
.slimselect-error .ss-main {
    border-color: #dc2626 !important;  /* red-600 */
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
}
```

---

## Composant Blade

### Usage basique

```blade
<x-slim-select
    name="vehicle_type_id"
    label="Type de Véhicule"
    :options="$vehicleTypes->pluck('name', 'id')->toArray()"
    :selected="old('vehicle_type_id')"
    placeholder="Sélectionnez un type..."
    required
    :error="$errors->first('vehicle_type_id')"
    helpText="Description du champ"
    @change="validateField('vehicle_type_id', $event.target.value)"
/>
```

### Props disponibles

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `name` | string | `''` | Nom du champ (obligatoire) |
| `label` | string | `null` | Label affiché au-dessus |
| `options` | array | `[]` | Options [value => label] |
| `selected` | mixed | `null` | Valeur(s) présélectionnée(s) |
| `placeholder` | string | `'Sélectionnez...'` | Texte du placeholder |
| `required` | bool | `false` | Champ obligatoire |
| `disabled` | bool | `false` | Champ désactivé |
| `multiple` | bool | `false` | Sélection multiple |
| `searchable` | bool | `true` | Afficher recherche |
| `error` | string | `null` | Message d'erreur serveur |
| `helpText` | string | `null` | Texte d'aide |

### Sélection multiple

```blade
<x-slim-select
    name="users"
    label="Utilisateurs Autorisés"
    :options="$users->mapWithKeys(fn($u) => [$u->id => $u->name])->toArray()"
    :selected="old('users', [])"
    placeholder="Rechercher des utilisateurs..."
    :multiple="true"
    helpText="Sélectionnez les utilisateurs autorisés"
/>
```

---

## Initialisation JavaScript

### Script d'initialisation global

```javascript
function initializeSlimSelects() {
    document.querySelectorAll('[data-slimselect="true"]').forEach(function(el) {
        // Skip si déjà initialisé
        if (el.slimSelectInstance) return;

        const placeholder = el.getAttribute('data-placeholder') || 'Sélectionnez...';
        const isSearchable = el.getAttribute('data-searchable') !== 'false';

        try {
            const instance = new SlimSelect({
                select: el,
                settings: {
                    showSearch: isSearchable,
                    searchPlaceholder: 'Rechercher...',
                    searchText: 'Aucun résultat',
                    searchingText: 'Recherche...',
                    allowDeselect: true,
                    placeholderText: placeholder,
                    hideSelected: false,
                    contentLocation: document.body,  // Important pour z-index
                    contentPosition: 'absolute'
                },
                events: {
                    afterChange: (newVal) => {
                        // Dispatch change event pour Alpine.js
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                    },
                    afterOpen: () => {
                        // Focus automatique sur recherche
                        const searchInput = document.querySelector('.ss-search input');
                        if (searchInput) {
                            setTimeout(() => searchInput.focus(), 50);
                        }
                    }
                }
            });

            // Stocker l'instance pour référence
            el.slimSelectInstance = instance;
        } catch (e) {
            console.error('SlimSelect init error:', e);
        }
    });
}

// Événements de chargement
document.addEventListener('DOMContentLoaded', initializeSlimSelects);
document.addEventListener('livewire:navigated', initializeSlimSelects);
```

### Magic Alpine.js

```javascript
document.addEventListener('alpine:init', function() {
    Alpine.magic('slimselect', (el) => {
        return () => {
            const selectEl = el.querySelector('[data-slimselect="true"]');
            return selectEl?.slimSelectInstance;
        };
    });
});
```

---

## États et validations

### Validation en temps réel avec Alpine.js

```javascript
validateField(fieldName, value) {
    // Marquer comme touché
    this.touchedFields[fieldName] = true;

    // Règle de validation
    const isValid = value && value.length > 0;

    if (!isValid) {
        this.fieldErrors[fieldName] = true;
        
        // Ajouter classe d'erreur visuelle
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            const wrapper = input.closest('.ss-main');
            if (wrapper) {
                wrapper.classList.add('slimselect-error');
            }
        }
    } else {
        this.clearFieldError(fieldName);
    }

    return isValid;
}

clearFieldError(fieldName) {
    delete this.fieldErrors[fieldName];
    
    const input = document.querySelector(`[name="${fieldName}"]`);
    if (input) {
        const wrapper = input.closest('.ss-main');
        if (wrapper) {
            wrapper.classList.remove('slimselect-error');
        }
    }
}
```

### Animation shake pour champs invalides

```css
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
    20%, 40%, 60%, 80% { transform: translateX(4px); }
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}
```

---

## Guide de migration TomSelect → SlimSelect

### Étape 1: Remplacer le composant

**Avant (TomSelect):**
```blade
<x-tom-select
    name="field_name"
    :options="$options"
    :selected="old('field_name')"
    ...
/>
```

**Après (SlimSelect):**
```blade
<x-slim-select
    name="field_name"
    :options="$options"
    :selected="old('field_name')"
    ...
/>
```

### Étape 2: Mettre à jour les sélecteurs CSS

| TomSelect classe | SlimSelect classe |
|------------------|-------------------|
| `.ts-wrapper` | `.ss-main` |
| `.ts-control` | `.ss-values` |
| `.ts-dropdown` | `.ss-content` |
| `.ts-dropdown-content` | `.ss-list` |
| `.option` | `.ss-option` |
| `.active` | `.ss-highlighted` |
| `.tomselect-error` | `.slimselect-error` |

### Étape 3: Mettre à jour JavaScript

**Avant:**
```javascript
const tsWrapper = input.closest('.ts-wrapper');
if (tsWrapper) {
    tsWrapper.classList.add('tomselect-error');
}
```

**Après:**
```javascript
const ssWrapper = input.closest('.ss-main');
if (ssWrapper) {
    ssWrapper.classList.add('slimselect-error');
}
```

### Étape 4: Inclure les styles

```blade
@push('styles')
    @include('partials.slimselect-styles')
@endpush
```

---

## Responsive et Accessibilité

### Styles mobile

```css
@media (max-width: 640px) {
    :root {
        --ss-main-height: 44px;        /* Touch friendly */
        --ss-content-height: 240px;
    }

    .ss-content .ss-list .ss-option {
        padding: 12px 10px;
        min-height: 44px;              /* iOS minimum tap target */
    }

    .ss-content .ss-search input {
        padding: 12px;
        font-size: 16px;               /* Évite le zoom iOS */
    }
}
```

### Préférences de mouvement réduit

```css
@media (prefers-reduced-motion: reduce) {
    .ss-main,
    .ss-content,
    .ss-option {
        transition: none !important;
        animation: none !important;
    }
}
```

---

## Exemples complets

### Formulaire de véhicule

```blade
{{-- Phase 2: Caractéristiques Techniques --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <x-slim-select
        name="vehicle_type_id"
        label="Type de Véhicule"
        :options="$vehicleTypes->pluck('name', 'id')->toArray()"
        :selected="old('vehicle_type_id', $vehicle->vehicle_type_id)"
        placeholder="Sélectionnez un type..."
        required
        :error="$errors->first('vehicle_type_id')"
        @change="validateField('vehicle_type_id', $event.target.value)"
    />

    <x-slim-select
        name="fuel_type_id"
        label="Type de Carburant"
        :options="$fuelTypes->pluck('name', 'id')->toArray()"
        :selected="old('fuel_type_id', $vehicle->fuel_type_id)"
        placeholder="Sélectionnez un carburant..."
        required
        :error="$errors->first('fuel_type_id')"
        @change="validateField('fuel_type_id', $event.target.value)"
    />

    <x-slim-select
        name="transmission_type_id"
        label="Type de Transmission"
        :options="$transmissionTypes->pluck('name', 'id')->toArray()"
        :selected="old('transmission_type_id', $vehicle->transmission_type_id)"
        placeholder="Sélectionnez une transmission..."
        required
        :error="$errors->first('transmission_type_id')"
        @change="validateField('transmission_type_id', $event.target.value)"
    />
</div>
```

---

## Checklist de migration

- [ ] Remplacer `<x-tom-select>` par `<x-slim-select>`
- [ ] Mettre à jour les sélecteurs CSS (`.ts-*` → `.ss-*`)
- [ ] Mettre à jour les références JavaScript
- [ ] Inclure `@include('partials.slimselect-styles')` dans les styles
- [ ] Tester la validation en temps réel
- [ ] Vérifier le comportement sur mobile
- [ ] Tester les sélections multiples
- [ ] Vérifier l'accessibilité clavier

---

> **Note**: Ce guide est basé sur l'implémentation actuelle dans ZenFleet v2.1 Ultra-Pro. Consultez les fichiers de référence pour les dernières mises à jour.
