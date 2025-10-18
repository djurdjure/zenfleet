# 📚 ZenFleet - Documentation de Référence des Composants

**Version:** 1.0.0
**Date:** 18 Octobre 2025
**Référence:** `resources/views/admin/components-demo.blade.php`

---

## 📖 Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Composants de Formulaire](#composants-de-formulaire)
3. [Composants d'Interface](#composants-dinterface)
4. [Composants de Tableau](#composants-de-tableau)
5. [Styles et Patterns](#styles-et-patterns)
6. [Dark Mode](#dark-mode)
7. [Accessibilité](#accessibilité)

---

## Vue d'ensemble

### Philosophie de Design

Le ZenFleet Design System est basé sur les principes suivants:
- **Flowbite-inspired**: Styles inspirés de Flowbite avec Tailwind CSS
- **Enterprise-grade**: Apparence professionnelle (shadow-sm, rounded-lg)
- **Blue theme**: Couleur primaire blue-600 au lieu des couleurs Flowbite
- **Dark mode**: Support complet du mode sombre
- **Responsive**: Mobile-first avec breakpoints md, lg, xl, 2xl
- **Accessible**: Labels, erreurs, états focus, ARIA

### Technologies

- **Laravel 12.28.1** - Framework PHP
- **Tailwind CSS 3.x** - Framework CSS utility-first
- **Alpine.js 3.x** - JavaScript réactif léger
- **Heroicons 2.6.0** - Bibliothèque d'icônes (à migrer vers Iconify)
- **Tom Select 2.3.1** - Dropdowns avec recherche
- **Flatpickr** - Date et time pickers

---

## Composants de Formulaire

### 1. Button (`<x-button>`)

**Fichiers:**
- `app/View/Components/Button.php`
- `resources/views/components/button.blade.php`

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `variant` | string | `'primary'` | Type de bouton: `primary`, `secondary`, `danger`, `success`, `ghost` |
| `size` | string | `'md'` | Taille: `sm`, `md`, `lg` |
| `icon` | string | `null` | Nom de l'icône Heroicons |
| `iconPosition` | string | `'left'` | Position de l'icône: `left`, `right` |
| `disabled` | boolean | `false` | État désactivé |
| `href` | string | `null` | Si défini, rend un `<a>` au lieu d'un `<button>` |
| `type` | string | `'button'` | Type HTML: `button`, `submit`, `reset` |

**Variantes de Couleur:**

```php
'primary' => 'text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800
              dark:bg-blue-600 dark:hover:bg-blue-700 dark:active:bg-blue-800'

'secondary' => 'text-gray-900 bg-white border border-gray-300 hover:bg-gray-100
                active:bg-gray-200 dark:bg-gray-800 dark:text-white
                dark:border-gray-600 dark:hover:bg-gray-700'

'danger' => 'text-white bg-red-600 hover:bg-red-700 active:bg-red-800
             dark:bg-red-600 dark:hover:bg-red-700'

'success' => 'text-white bg-green-600 hover:bg-green-700 active:bg-green-800
              dark:bg-green-600 dark:hover:bg-green-700'

'ghost' => 'text-gray-700 hover:bg-gray-100 active:bg-gray-200
            dark:text-gray-300 dark:hover:bg-gray-800'
```

**Tailles:**

```php
'sm' => 'px-3 py-2 text-xs'
'md' => 'px-5 py-2.5 text-sm'
'lg' => 'px-6 py-3 text-base'
```

**Exemples:**

```blade
{{-- Bouton primaire avec icône --}}
<x-button variant="primary" icon="plus" iconPosition="left">
    Nouveau véhicule
</x-button>

{{-- Bouton danger small --}}
<x-button variant="danger" icon="trash" size="sm">
    Supprimer
</x-button>

{{-- Lien stylé comme bouton --}}
<x-button href="/admin/vehicles" variant="primary">
    Voir véhicules
</x-button>

{{-- Bouton désactivé --}}
<x-button variant="primary" disabled>
    Enregistrer
</x-button>
```

**États:**
- ✅ Hover: `hover:bg-{color}-700`
- ✅ Active: `active:bg-{color}-800`
- ✅ Disabled: `opacity-50 cursor-not-allowed`
- ✅ Focus: `focus:outline-none` (pas de ring pour aspect enterprise)
- ✅ Dark mode: `dark:bg-{color}-600 dark:hover:bg-{color}-700`

---

### 2. Input (`<x-input>`)

**Fichiers:**
- `app/View/Components/Input.php`
- `resources/views/components/input.blade.php`

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | *required* | Attribut name du champ |
| `type` | string | `'text'` | Type HTML: `text`, `email`, `number`, `password`, etc. |
| `label` | string | `null` | Label du champ |
| `placeholder` | string | `null` | Placeholder |
| `value` | string | `null` | Valeur par défaut |
| `icon` | string | `null` | Icône Heroicons à gauche |
| `error` | string | `null` | Message d'erreur |
| `helpText` | string | `null` | Texte d'aide |
| `required` | boolean | `false` | Champ requis |
| `disabled` | boolean | `false` | Champ désactivé |

**Classes de Base (Flowbite-inspired):**

```css
bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5
dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white
```

**États:**

- **Normal**: `border-gray-300 focus:ring-primary-600 focus:border-primary-600`
- **Erreur**: `border-red-300 focus:ring-red-500 focus:border-red-500`
- **Désactivé**: `bg-gray-100 text-gray-500 cursor-not-allowed opacity-60`

**Exemples:**

```blade
{{-- Input simple --}}
<x-input
    name="plate"
    label="Immatriculation"
    placeholder="XX-123-YY"
/>

{{-- Input avec icône --}}
<x-input
    name="email"
    type="email"
    label="Email"
    icon="envelope"
    placeholder="nom@exemple.com"
/>

{{-- Input requis --}}
<x-input
    name="brand"
    label="Marque"
    placeholder="Toyota"
    required
/>

{{-- Input avec erreur --}}
<x-input
    name="phone"
    label="Téléphone"
    error="Le numéro de téléphone est invalide"
    value="123"
/>

{{-- Input avec aide --}}
<x-input
    name="mileage"
    type="number"
    label="Kilométrage"
    helpText="En kilomètres"
    placeholder="50000"
/>

{{-- Input désactivé --}}
<x-input
    name="status"
    label="Statut"
    value="Actif"
    disabled
/>
```

**Structure HTML:**

```html
<div>
    <!-- Label avec astérisque si required -->
    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        Label
        <span class="text-red-500">*</span>
    </label>

    <!-- Input avec icône optionnelle -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <!-- Icône Heroicons -->
        </div>
        <input class="..." />
    </div>

    <!-- Message d'erreur ou aide -->
    <p class="mt-2 text-sm text-red-600 flex items-start">
        <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1 mt-0.5" />
        <span>Message d'erreur</span>
    </p>
</div>
```

---

### 3. Select (`<x-select>`)

**Fichiers:**
- `app/View/Components/Select.php`
- `resources/views/components/select.blade.php`

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | *required* | Attribut name du champ |
| `label` | string | `null` | Label du champ |
| `options` | array | `[]` | Options au format `['value' => 'Label']` |
| `selected` | string | `null` | Valeur sélectionnée par défaut |
| `error` | string | `null` | Message d'erreur |
| `helpText` | string | `null` | Texte d'aide |
| `required` | boolean | `false` | Champ requis |
| `disabled` | boolean | `false` | Champ désactivé |

**Classes (identiques à Input):**

```css
bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400
dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500
```

**Exemples:**

```blade
{{-- Select simple --}}
<x-select
    name="vehicle_type"
    label="Type de véhicule"
    :options="[
        '' => 'Sélectionner un type',
        'sedan' => 'Berline',
        'suv' => 'SUV',
        'truck' => 'Camion',
        'van' => 'Fourgon'
    ]"
/>

{{-- Select requis avec valeur sélectionnée --}}
<x-select
    name="fuel_type"
    label="Type de carburant"
    :options="[
        'diesel' => 'Diesel',
        'gasoline' => 'Essence',
        'electric' => 'Électrique',
        'hybrid' => 'Hybride'
    ]"
    selected="diesel"
    required
/>

{{-- Select avec erreur --}}
<x-select
    name="status_select"
    label="Statut du véhicule"
    :options="[
        'active' => 'Actif',
        'maintenance' => 'En maintenance',
        'inactive' => 'Inactif'
    ]"
    error="Veuillez sélectionner un statut"
/>

{{-- Select avec aide --}}
<x-select
    name="driver"
    label="Chauffeur assigné"
    :options="[
        '' => 'Sélectionner un chauffeur',
        '1' => 'Jean Dupont',
        '2' => 'Marie Martin',
        '3' => 'Pierre Dubois'
    ]"
    helpText="Sélectionnez le chauffeur principal"
/>
```

---

### 4. Textarea (`<x-textarea>`)

**Fichiers:**
- `app/View/Components/Textarea.php`
- `resources/views/components/textarea.blade.php`

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | *required* | Attribut name du champ |
| `label` | string | `null` | Label du champ |
| `placeholder` | string | `null` | Placeholder |
| `value` | string | `null` | Valeur par défaut |
| `rows` | int | `3` | Nombre de lignes |
| `error` | string | `null` | Message d'erreur |
| `helpText` | string | `null` | Texte d'aide |
| `required` | boolean | `false` | Champ requis |
| `disabled` | boolean | `false` | Champ désactivé |

**Classes (identiques à Input/Select):**

```css
bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5
dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400
dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500
```

**Exemples:**

```blade
{{-- Textarea simple --}}
<x-textarea
    name="description"
    label="Description"
    placeholder="Décrivez le véhicule..."
    rows="4"
/>

{{-- Textarea requis --}}
<x-textarea
    name="notes"
    label="Notes de maintenance"
    placeholder="Entrez les notes..."
    rows="4"
    required
/>

{{-- Textarea avec erreur --}}
<x-textarea
    name="comments"
    label="Commentaires"
    value="Trop court"
    error="Le commentaire doit contenir au moins 20 caractères"
    rows="4"
/>

{{-- Textarea avec aide --}}
<x-textarea
    name="observations"
    label="Observations"
    placeholder="Observations générales..."
    helpText="Maximum 500 caractères"
    rows="4"
/>
```

---

### 5. TomSelect (`<x-tom-select>`)

**Fichiers:**
- `app/View/Components/TomSelect.php`
- `resources/views/components/tom-select.blade.php`

**Dépendances:**
- Tom Select 2.3.1 (CDN)
- CSS: `https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css`
- JS: `https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js`

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | *required* | Attribut name du champ |
| `label` | string | `null` | Label du champ |
| `options` | array | `[]` | Options au format `['value' => 'Label']` |
| `selected` | string\|array | `null` | Valeur(s) sélectionnée(s) |
| `placeholder` | string | `'Rechercher...'` | Placeholder |
| `multiple` | boolean | `false` | Sélection multiple |
| `clearable` | boolean | `true` | Bouton de suppression |
| `error` | string | `null` | Message d'erreur |
| `helpText` | string | `null` | Texte d'aide |
| `required` | boolean | `false` | Champ requis |
| `disabled` | boolean | `false` | Champ désactivé |

**Configuration JavaScript:**

```javascript
new TomSelect(el, {
    plugins: ['clear_button', 'remove_button'],
    maxOptions: 100,
    placeholder: 'Rechercher...',
    allowEmptyOption: true,
    create: false,
    sortField: {
        field: "text",
        direction: "asc"
    },
    render: {
        no_results: function(data, escape) {
            return '<div class="no-results p-2 text-sm text-gray-500">Aucun résultat trouvé</div>';
        }
    }
});
```

**Styles Personnalisés (Enterprise-grade):**

```css
/* Retirer le wrapper et laisser seulement la bordure du control */
.ts-wrapper {
    padding: 0 !important;
}

.ts-wrapper .ts-control {
    background-color: rgb(249 250 251);
    border: 1px solid rgb(209 213 219);
    border-radius: 0.5rem;
    padding: 0.625rem;
    font-size: 0.875rem;
    color: rgb(17 24 39);
    box-shadow: none !important; /* Pas d'ombre pour aspect professionnel */
}

/* Focus state - seulement sur le control */
.ts-wrapper.focus .ts-control {
    border-color: rgb(59 130 246);
    box-shadow: 0 0 0 1px rgb(59 130 246) !important;
    outline: none;
}
```

**Exemples:**

```blade
{{-- TomSelect simple --}}
<x-tom-select
    name="vehicle_tomselect"
    label="Véhicule (avec recherche)"
    :options="[
        '1' => 'AA-123-BB - Toyota Corolla',
        '2' => 'CC-456-DD - Renault Master',
        '3' => 'EE-789-FF - Peugeot Partner'
    ]"
    placeholder="Rechercher un véhicule..."
/>

{{-- TomSelect requis --}}
<x-tom-select
    name="driver_tomselect"
    label="Chauffeur assigné"
    :options="[
        '1' => 'Jean Dupont',
        '2' => 'Marie Martin',
        '3' => 'Pierre Dubois'
    ]"
    placeholder="Sélectionner un chauffeur..."
    required
/>

{{-- TomSelect avec erreur --}}
<x-tom-select
    name="fuel_type_tomselect"
    label="Type de carburant"
    :options="[
        'diesel' => 'Diesel',
        'gasoline' => 'Essence'
    ]"
    error="Ce champ est requis"
/>
```

**UX Highlights:**
- ✅ Recherche instantanée
- ✅ Clear button pour effacer la sélection
- ✅ Pas de cadre externe (seamless avec autres inputs)
- ✅ Focus subtil (1px blue ring)
- ✅ Support dark mode
- ✅ Tri alphabétique automatique

---

### 6. Datepicker (`<x-datepicker>`)

**Fichiers:**
- `app/View/Components/Datepicker.php`
- `resources/views/components/datepicker.blade.php`

**Dépendances:**
- Flatpickr (CDN)
- CSS: `https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css`
- JS: `https://cdn.jsdelivr.net/npm/flatpickr`
- JS Locale: `https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js`

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | *required* | Attribut name du champ |
| `label` | string | `null` | Label du champ |
| `value` | string | `null` | Valeur par défaut (format Y-m-d) |
| `placeholder` | string | `'Sélectionner une date'` | Placeholder |
| `minDate` | string | `null` | Date minimale (format Y-m-d) |
| `maxDate` | string | `null` | Date maximale (format Y-m-d) |
| `format` | string | `'d/m/Y'` | Format d'affichage |
| `error` | string | `null` | Message d'erreur |
| `helpText` | string | `null` | Texte d'aide |
| `required` | boolean | `false` | Champ requis |
| `disabled` | boolean | `false` | Champ désactivé |

**Configuration JavaScript:**

```javascript
flatpickr(el, {
    dateFormat: "d/m/Y",
    locale: "fr",
    allowInput: true,
    disableMobile: true,
    minDate: minDate || null,
    maxDate: maxDate || null
});
```

**Exemples:**

```blade
{{-- Datepicker simple --}}
<x-datepicker
    name="maintenance_date"
    label="Date de maintenance"
    placeholder="Choisir une date"
/>

{{-- Datepicker requis --}}
<x-datepicker
    name="assignment_date"
    label="Date d'affectation"
    placeholder="JJ/MM/AAAA"
    required
/>

{{-- Datepicker avec contraintes --}}
<x-datepicker
    name="start_date"
    label="Date de début"
    :minDate="date('Y-m-d')"
    placeholder="Sélectionner..."
    helpText="La date ne peut pas être dans le passé"
/>

{{-- Datepicker avec erreur --}}
<x-datepicker
    name="end_date"
    label="Date de fin"
    error="La date de fin doit être après la date de début"
/>
```

**Features:**
- ✅ Calendrier visuel interactif
- ✅ Locale française (jours, mois)
- ✅ Contraintes min/max date
- ✅ Saisie manuelle autorisée
- ✅ Format JJ/MM/AAAA
- ✅ Icône calendrier à gauche
- ✅ Support dark mode

---

### 7. TimePicker (`<x-time-picker>`)

**Fichiers:**
- `app/View/Components/TimePicker.php`
- `resources/views/components/time-picker.blade.php`

**Dépendances:**
- Flatpickr (CDN)
- CSS: `https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css`
- JS: `https://cdn.jsdelivr.net/npm/flatpickr`

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | *required* | Attribut name du champ |
| `label` | string | `null` | Label du champ |
| `value` | string | `null` | Valeur par défaut (format H:i) |
| `placeholder` | string | `'Sélectionner une heure'` | Placeholder |
| `enableSeconds` | boolean | `false` | Afficher les secondes |
| `error` | string | `null` | Message d'erreur |
| `helpText` | string | `null` | Texte d'aide |
| `required` | boolean | `false` | Champ requis |
| `disabled` | boolean | `false` | Champ désactivé |

**Configuration JavaScript:**

```javascript
flatpickr(el, {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i", // ou "H:i:S" si enableSeconds
    time_24hr: true,
    allowInput: true,
    disableMobile: true,
    defaultHour: 0,
    defaultMinute: 0
});
```

**Masque de Saisie Intelligent (UX Enhancement):**

```javascript
function applyTimeMask(input) {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Garder seulement les chiffres

        if (value.length >= 2) {
            // Limiter les heures à 23
            let hours = parseInt(value.substring(0, 2));
            if (hours > 23) hours = 23;

            let formattedValue = String(hours).padStart(2, '0');

            if (value.length >= 3) {
                // Limiter les minutes à 59
                let minutes = parseInt(value.substring(2, 4));
                if (minutes > 59) minutes = 59;
                formattedValue += ':' + String(minutes).padStart(2, '0');
            } else if (value.length === 2) {
                formattedValue += ':';
            }

            e.target.value = formattedValue;
        }
    });
}
```

**Exemples:**

```blade
{{-- TimePicker simple --}}
<x-time-picker
    name="departure_time"
    label="Heure de départ"
    placeholder="HH:MM"
/>

{{-- TimePicker requis --}}
<x-time-picker
    name="arrival_time"
    label="Heure d'arrivée"
    placeholder="00:00"
    required
/>

{{-- TimePicker avec valeur --}}
<x-time-picker
    name="scheduled_time"
    label="Heure planifiée"
    value="09:00"
/>

{{-- TimePicker avec aide --}}
<x-time-picker
    name="maintenance_time"
    label="Heure de maintenance"
    helpText="Format 24 heures (HH:MM)"
/>
```

**UX Highlights:**
- ✅ Format 24 heures (HH:MM)
- ✅ Masque de saisie automatique:
  - Tape "09" → auto-ajoute ":" → "09:"
  - Tape "0930" → auto-formate → "09:30"
  - Validation heures max 23
  - Validation minutes max 59
  - Auto-focus sur minutes après heures
- ✅ Picker visuel (horloge)
- ✅ Icône horloge à gauche
- ✅ Support dark mode

---

## Composants d'Interface

### 8. Alert (`<x-alert>`)

**Fichiers:**
- `app/View/Components/Alert.php`
- `resources/views/components/alert.blade.php`

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | string | `'info'` | Type d'alerte: `success`, `error`, `warning`, `info` |
| `title` | string | `null` | Titre de l'alerte |
| `dismissible` | boolean | `false` | Bouton de fermeture |

**Variantes de Couleur:**

```php
'success' => 'bg-green-50 text-green-800 border-green-300 dark:bg-green-900 dark:text-green-200 dark:border-green-800'
'error' => 'bg-red-50 text-red-800 border-red-300 dark:bg-red-900 dark:text-red-200 dark:border-red-800'
'warning' => 'bg-orange-50 text-orange-800 border-orange-300 dark:bg-orange-900 dark:text-orange-200 dark:border-orange-800'
'info' => 'bg-blue-50 text-blue-800 border-blue-300 dark:bg-blue-900 dark:text-blue-200 dark:border-blue-800'
```

**Structure:**

```html
<div class="flex items-start p-4 rounded-lg border">
    <!-- Icône -->
    <div class="flex-shrink-0">
        <svg class="w-5 h-5">...</svg>
    </div>

    <!-- Contenu -->
    <div class="ml-3 flex-1">
        <h3 class="text-sm font-medium">Titre</h3>
        <div class="mt-1 text-sm">Message</div>
    </div>

    <!-- Bouton fermer (si dismissible) -->
    <button @click="$el.closest('.alert').remove()" class="...">×</button>
</div>
```

**Exemples:**

```blade
<x-alert type="success" title="Succès">
    Le véhicule a été créé avec succès.
</x-alert>

<x-alert type="error" title="Erreur">
    Une erreur est survenue lors de l'enregistrement.
</x-alert>

<x-alert type="warning" title="Attention">
    Ce véhicule nécessite une maintenance dans 7 jours.
</x-alert>

<x-alert type="info">
    Les données de kilométrage sont mises à jour toutes les heures.
</x-alert>

<x-alert type="success" title="Avec bouton fermer" dismissible>
    Cette alerte peut être fermée par l'utilisateur.
</x-alert>
```

---

### 9. Badge (`<x-badge>`)

**Fichiers:**
- `app/View/Components/Badge.php`
- `resources/views/components/badge.blade.php`

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | string | `'primary'` | Type: `success`, `error`, `warning`, `info`, `primary`, `gray` |
| `size` | string | `'md'` | Taille: `sm`, `md`, `lg` |

**Variantes de Couleur:**

```php
'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
'error' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
'warning' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200'
'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
'primary' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
```

**Tailles:**

```php
'sm' => 'px-2 py-0.5 text-xs'
'md' => 'px-2.5 py-0.5 text-xs'
'lg' => 'px-3 py-1 text-sm'
```

**Classes de Base:**

```css
inline-flex items-center font-medium rounded-full
```

**Exemples:**

```blade
{{-- Variantes --}}
<x-badge type="success">Actif</x-badge>
<x-badge type="error">Hors service</x-badge>
<x-badge type="warning">En maintenance</x-badge>
<x-badge type="info">Nouveau</x-badge>
<x-badge type="primary">Important</x-badge>
<x-badge type="gray">Archivé</x-badge>

{{-- Tailles --}}
<x-badge type="success" size="sm">Small</x-badge>
<x-badge type="success" size="md">Medium</x-badge>
<x-badge type="success" size="lg">Large</x-badge>
```

**Usage Typique dans Tableaux:**

```blade
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
    Actif
</span>
```

---

### 10. Modal (`<x-modal>`)

**Fichiers:**
- `app/View/Components/Modal.php`
- `resources/views/components/modal.blade.php`

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | *required* | Nom unique du modal |
| `title` | string | `null` | Titre du modal |
| `maxWidth` | string | `'lg'` | Largeur max: `sm`, `md`, `lg`, `xl`, `2xl`, `4xl`, `6xl` |

**Largeurs Maximales:**

```php
'sm' => 'max-w-sm'      // 384px
'md' => 'max-w-md'      // 448px
'lg' => 'max-w-lg'      // 512px
'xl' => 'max-w-xl'      // 576px
'2xl' => 'max-w-2xl'    // 672px
'4xl' => 'max-w-4xl'    // 896px
'6xl' => 'max-w-6xl'    // 1152px
```

**Ouverture/Fermeture (Alpine.js):**

```blade
{{-- Bouton d'ouverture --}}
<x-button @click="$dispatch('open-modal', 'modal-name')">
    Ouvrir
</x-button>

{{-- Bouton de fermeture --}}
<x-button @click="$dispatch('close-modal', 'modal-name')">
    Fermer
</x-button>
```

**Structure:**

```html
<div x-data="{ show: false }"
     @open-modal.window="if ($event.detail === 'modal-name') show = true"
     @close-modal.window="if ($event.detail === 'modal-name') show = false"
     x-show="show"
     x-cloak>

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div @click.away="show = false"
                 class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full">

                <!-- Header -->
                <div class="flex items-center justify-between p-5 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold">Titre</h3>
                    <button @click="show = false">×</button>
                </div>

                <!-- Body -->
                <div class="p-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
```

**Exemples:**

```blade
{{-- Modal simple --}}
<x-modal name="demo-modal-sm" title="Modal Small" maxWidth="sm">
    <p class="text-gray-700">Contenu du modal</p>
    <div class="mt-4 flex justify-end">
        <x-button @click="$dispatch('close-modal', 'demo-modal-sm')" variant="secondary">
            Fermer
        </x-button>
    </div>
</x-modal>

{{-- Modal large --}}
<x-modal name="demo-modal-lg" title="Modal Large" maxWidth="2xl">
    <p class="text-gray-700 mb-4">Grand modal</p>
    <div class="mt-6 flex justify-end gap-3">
        <x-button @click="$dispatch('close-modal', 'demo-modal-lg')" variant="secondary">
            Annuler
        </x-button>
        <x-button @click="$dispatch('close-modal', 'demo-modal-lg')" variant="primary">
            Confirmer
        </x-button>
    </div>
</x-modal>

{{-- Modal avec formulaire --}}
<x-modal name="demo-modal-form" title="Créer un véhicule" maxWidth="lg">
    <form class="space-y-4">
        <x-input name="plate" label="Immatriculation" icon="truck" required />
        <x-input name="brand" label="Marque" required />

        <div class="flex justify-end gap-3 pt-4">
            <x-button @click="$dispatch('close-modal', 'demo-modal-form')"
                      type="button" variant="secondary">
                Annuler
            </x-button>
            <x-button type="submit" variant="primary" icon="check">
                Créer
            </x-button>
        </div>
    </form>
</x-modal>
```

**Features:**
- ✅ Backdrop avec transparence
- ✅ Click outside pour fermer
- ✅ Échap pour fermer (Alpine.js)
- ✅ Focus trap (Alpine.js)
- ✅ Responsive (padding adaptatif)
- ✅ Dark mode complet
- ✅ Animations (x-transition)
- ✅ Accessible (ARIA roles)

---

## Composants de Tableau

### 11. Table (Pattern HTML/Tailwind)

**Pas de composant Blade dédié** - Pattern HTML réutilisable

**Structure de Base (Style Kilométrage):**

```blade
<div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        <div class="flex items-center space-x-1">
                            <span>Véhicule</span>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kilométrage
                    </th>
                    <!-- Plus de colonnes... -->
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <!-- Icône véhicule -->
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                                    </svg>
                                </div>
                            </div>
                            <!-- Infos véhicule -->
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">AA-123-BB</div>
                                <div class="text-sm text-gray-500">Toyota Corolla</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        85,432 km
                    </td>
                    <!-- Plus de colonnes... -->
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

**Classes Clés:**

| Élément | Classes Tailwind |
|---------|-----------------|
| Container | `bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden` |
| Scroll wrapper | `overflow-x-auto` |
| Table | `min-w-full divide-y divide-gray-200` |
| Thead | `bg-gray-50` |
| Th | `px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider` |
| Th sortable | `cursor-pointer hover:bg-gray-100` |
| Tbody | `bg-white divide-y divide-gray-200` |
| Tr hover | `hover:bg-gray-50` |
| Td | `px-6 py-4 whitespace-nowrap` |

**Patterns Récurrents:**

1. **Cellule avec Icône Véhicule:**

```blade
<td class="px-6 py-4 whitespace-nowrap">
    <div class="flex items-center">
        <div class="flex-shrink-0 h-10 w-10">
            <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <!-- SVG paths... -->
                </svg>
            </div>
        </div>
        <div class="ml-4">
            <div class="text-sm font-medium text-gray-900">AA-123-BB</div>
            <div class="text-sm text-gray-500">Toyota Corolla</div>
        </div>
    </div>
</td>
```

2. **Cellule Date/Heure:**

```blade
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
    <div class="flex flex-col">
        <span class="font-medium">18/10/2025</span>
        <span class="text-gray-500">14:30</span>
    </div>
</td>
```

3. **Cellule avec Avatar Utilisateur:**

```blade
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
    <div class="flex items-center">
        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
            <span class="text-blue-600 font-semibold text-xs">JD</span>
        </div>
        <span class="font-medium">Jean Dupont</span>
    </div>
</td>
```

4. **Cellule avec Badge:**

```blade
<td class="px-6 py-4 whitespace-nowrap">
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
        Actif
    </span>
</td>
```

5. **Cellule Actions:**

```blade
<td class="px-6 py-4 whitespace-nowrap text-sm">
    <button class="text-blue-600 hover:text-blue-900 font-medium">Détails</button>
</td>
```

**Responsive:**
- Container avec `overflow-x-auto` pour scroll horizontal sur mobile
- `min-w-full` sur table
- `whitespace-nowrap` sur td pour éviter retours à la ligne

---

## Styles et Patterns

### Classes Récurrentes

#### Sections/Cards

```css
/* Container principal */
bg-white dark:bg-gray-800
rounded-lg
shadow-sm
p-6
mb-6
border border-gray-200 dark:border-gray-700
```

**Notes:**
- `shadow-sm` au lieu de `shadow-lg` pour aspect enterprise
- `rounded-lg` au lieu de `rounded-xl` pour aspect professionnel

#### Titres de Sections

```css
/* H2 */
text-2xl font-semibold text-gray-900 dark:text-white mb-4

/* H3 */
text-lg font-medium text-gray-900 dark:text-white mb-4

/* Petits titres */
text-sm font-medium text-gray-900 dark:text-white mb-3
```

#### Grid Layouts (Forms)

```css
/* Grid 2 colonnes (responsive) */
grid grid-cols-1 md:grid-cols-2 gap-6
```

#### Spacing

```css
/* Entre sections */
space-y-8

/* Entre éléments d'une section */
space-y-4

/* Entre boutons/badges */
gap-3

/* Entre form fields */
gap-6
```

---

### Palette de Couleurs

#### Couleurs Principales

| Usage | Light Mode | Dark Mode |
|-------|-----------|-----------|
| Primary (Buttons) | `blue-600` | `blue-600` |
| Text | `gray-900` | `white` |
| Background | `white` | `gray-800` |
| Border | `gray-200` | `gray-700` |
| Input BG | `gray-50` | `gray-700` |
| Input Border | `gray-300` | `gray-600` |

#### Couleurs de Statut

| Statut | Background | Text | Border |
|--------|-----------|------|--------|
| Success | `green-100` | `green-800` | `green-300` |
| Error | `red-100` | `red-800` | `red-300` |
| Warning | `orange-100` | `orange-800` | `orange-300` |
| Info | `blue-100` | `blue-800` | `blue-300` |

#### Icônes

| Contexte | Couleur |
|----------|---------|
| Véhicule (icon background) | `bg-blue-100` |
| Véhicule (icon) | `text-blue-600` |
| Error icon | `text-red-600` |
| Success icon | `text-green-600` |

---

### Typography

```css
/* Headers */
text-3xl font-bold      /* Page title */
text-2xl font-semibold  /* Section title */
text-xl font-semibold   /* Modal title */
text-lg font-medium     /* Subsection title */
text-sm font-medium     /* Small title */

/* Body */
text-sm                 /* Input, button, table cell */
text-xs                 /* Badge, small text */

/* Colors */
text-gray-900 dark:text-white       /* Primary text */
text-gray-700 dark:text-gray-300    /* Secondary text */
text-gray-600 dark:text-gray-400    /* Tertiary text */
text-gray-500                       /* Placeholder, disabled */
```

---

### Shadows

```css
/* Enterprise-grade shadows (subtiles) */
shadow-sm    /* Cards, sections, tables */
shadow-xl    /* Modals */

/* PAS de shadow-lg pour aspect professionnel */
```

---

### Border Radius

```css
/* Enterprise-grade radius */
rounded-lg       /* Cards, sections, inputs, tables */
rounded-full     /* Badges, avatars */

/* PAS de rounded-xl pour aspect professionnel */
```

---

## Dark Mode

### Activation

Le dark mode est géré par Tailwind CSS avec la classe `dark:` variant.

**Configuration Tailwind (tailwind.config.js):**

```javascript
module.exports = {
  darkMode: 'class', // ou 'media'
  // ...
}
```

**Toggle (Alpine.js):**

```blade
<button @click="darkMode = !darkMode"
        x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
        @click="localStorage.setItem('darkMode', darkMode);
                document.documentElement.classList.toggle('dark', darkMode)">
    Toggle Dark Mode
</button>
```

### Classes Dark Mode par Composant

#### Backgrounds

```css
bg-white dark:bg-gray-800       /* Cards, sections */
bg-gray-50 dark:bg-gray-700     /* Inputs, table thead */
```

#### Text

```css
text-gray-900 dark:text-white           /* Primary */
text-gray-700 dark:text-gray-300        /* Secondary */
text-gray-600 dark:text-gray-400        /* Tertiary */
text-gray-500 dark:text-gray-400        /* Placeholder */
```

#### Borders

```css
border-gray-200 dark:border-gray-700    /* Cards */
border-gray-300 dark:border-gray-600    /* Inputs */
```

#### Buttons

```css
/* Primary */
bg-blue-600 hover:bg-blue-700
dark:bg-blue-600 dark:hover:bg-blue-700

/* Secondary */
bg-white dark:bg-gray-800
border-gray-300 dark:border-gray-600
text-gray-900 dark:text-white
```

#### Alerts/Badges

```css
/* Success */
bg-green-50 text-green-800
dark:bg-green-900 dark:text-green-200
```

---

## Accessibilité

### Checklist WCAG 2.1 Level AA

#### Labels et Descriptions

```blade
{{-- ✅ Labels explicites pour tous les inputs --}}
<label for="{{ $inputId }}" class="...">
    {{ $label }}
    @if($required)
        <span class="text-red-500">*</span>
    @endif
</label>

{{-- ✅ HelpText pour contexte additionnel --}}
<p class="mt-2 text-sm text-gray-500">
    {{ $helpText }}
</p>
```

#### États d'Erreur

```blade
{{-- ✅ Message d'erreur avec icône visuelle --}}
<p class="mt-2 text-sm text-red-600 flex items-start">
    <x-heroicon-o-exclamation-circle class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" />
    <span>{{ $error }}</span>
</p>

{{-- ✅ Bordure rouge pour erreur --}}
border-red-300 focus:ring-red-500 focus:border-red-500
```

#### Focus States

```blade
{{-- ✅ Focus visible (sans ring pour enterprise) --}}
focus:outline-none focus:border-blue-500

{{-- ✅ Focus visible sur modals (focus trap avec Alpine.js) --}}
x-trap.noscroll="show"
```

#### Keyboard Navigation

```blade
{{-- ✅ Tous les boutons sont focusables --}}
<button class="..." tabindex="0">

{{-- ✅ Modals fermables avec Échap --}}
@keydown.escape.window="if (show) show = false"
```

#### Contraste des Couleurs

| Élément | Ratio | Status |
|---------|-------|--------|
| Text gray-900 sur white | 21:1 | ✅ AAA |
| Text gray-700 sur white | 10.7:1 | ✅ AAA |
| Text gray-600 sur white | 7.2:1 | ✅ AA |
| Primary button blue-600 | 4.5:1 | ✅ AA |
| Success badge green-800 sur green-100 | 7.9:1 | ✅ AAA |
| Error badge red-800 sur red-100 | 8.3:1 | ✅ AAA |

#### ARIA Attributes

```blade
{{-- ✅ Roles sémantiques --}}
<table role="table">
<thead role="rowgroup">
<tr role="row">
<th scope="col" role="columnheader">

{{-- ✅ Modal avec ARIA --}}
<div role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <h3 id="modal-title">{{ $title }}</h3>
</div>

{{-- ✅ Alerts avec role --}}
<div role="alert" class="...">
```

#### Responsive Text

```blade
{{-- ✅ Tailles de texte accessibles (min 14px) --}}
text-sm   /* 14px */
text-base /* 16px */
text-lg   /* 18px */
```

---

## Responsive Design

### Breakpoints Tailwind

| Breakpoint | Min Width | Usage |
|-----------|-----------|-------|
| `sm` | 640px | Petits tablettes |
| `md` | 768px | Tablettes |
| `lg` | 1024px | Petits laptops |
| `xl` | 1280px | Laptops |
| `2xl` | 1536px | Grands écrans |

### Patterns Responsive

#### Grids

```blade
{{-- Mobile: 1 col, Desktop: 2 cols --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

{{-- Mobile: 1 col, Tablet: 2 cols, Desktop: 3 cols --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
```

#### Padding/Spacing

```blade
{{-- Container principal --}}
<div class="py-8 px-4 mx-auto max-w-7xl lg:py-16">

{{-- Sections --}}
<div class="p-6 mb-6">
```

#### Tables

```blade
{{-- Scroll horizontal sur mobile --}}
<div class="overflow-x-auto">
    <table class="min-w-full">
```

#### Modals

```blade
{{-- Padding adaptatif --}}
<div class="flex min-h-full items-center justify-center p-4">
```

#### Buttons

```blade
{{-- Stack sur mobile, inline sur desktop --}}
<div class="flex flex-wrap gap-3">
    <x-button>...</x-button>
</div>
```

---

## Migration Iconify (À Venir)

### Heroicons Actuelles

**Utilisation actuelle:**
```blade
<x-heroicon-o-truck class="w-6 h-6" />
<x-heroicon-o-exclamation-circle class="w-4 h-4" />
<x-heroicon-o-envelope class="w-5 h-5" />
```

**Icônes utilisées dans components-demo.blade.php:**
- `plus` - Bouton "Nouveau"
- `trash` - Bouton "Supprimer"
- `pencil` - Bouton "Éditer"
- `check` - Bouton "Créer"
- `envelope` - Input email
- `truck` - Input véhicule
- `exclamation-circle` - Erreurs
- Icône véhicule (SVG inline) - Tables
- Icône tri (SVG inline) - Tables
- Icône calendrier (SVG inline) - Datepicker
- Icône horloge (SVG inline) - TimePicker

### Plan de Migration Iconify

**Phase 2.2: Migration Heroicons → Iconify**

1. **Intégration Iconify:**
```html
<!-- Dans layouts/admin/catalyst.blade.php -->
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
```

2. **Composant <x-icon>:**
```blade
{{-- resources/views/components/icon.blade.php --}}
<span {{ $attributes->merge(['class' => '']) }}>
    <span class="iconify" data-icon="{{ $icon }}" data-inline="false"></span>
</span>
```

3. **Mapping Heroicons → Iconify:**

| Heroicons | Iconify | Collection |
|-----------|---------|------------|
| `plus` | `heroicons:plus` | Heroicons |
| `trash` | `heroicons:trash` | Heroicons |
| `pencil` | `heroicons:pencil` | Heroicons |
| `truck` | `heroicons:truck` | Heroicons |
| `envelope` | `heroicons:envelope` | Heroicons |

4. **Utilisation:**
```blade
<x-icon icon="heroicons:plus" class="w-6 h-6" />
```

**Voir:** `HEROICONS_MAPPING.md` (à créer)

---

## Bonnes Pratiques

### Composants

1. **Toujours utiliser les composants Blade** au lieu de HTML brut
2. **Props requis:** Toujours définir `name` pour les inputs
3. **Labels:** Toujours fournir un `label` pour accessibilité
4. **Erreurs:** Utiliser prop `error` pour validation
5. **HelpText:** Utiliser `helpText` pour contexte additionnel
6. **Required:** Marquer les champs obligatoires avec `required`

### Grids et Layouts

1. **Mobile-first:** Utiliser `grid-cols-1 md:grid-cols-2`
2. **Espacement:** `gap-6` pour grids, `space-y-4` pour flex
3. **Max-width:** Utiliser `max-w-7xl` pour containers principaux

### Couleurs

1. **Primary:** Toujours `blue-600` (pas primary-700)
2. **Shadows:** Toujours `shadow-sm` pour sections (pas shadow-lg)
3. **Rounded:** Toujours `rounded-lg` (pas rounded-xl)
4. **Dark mode:** Toujours définir `dark:` variants

### Accessibilité

1. **Labels:** Jamais d'input sans label
2. **Contraste:** Respecter ratio 4.5:1 minimum
3. **Focus:** États focus visibles
4. **Keyboard:** Navigation clavier complète
5. **ARIA:** Utiliser roles sémantiques

---

## Fichiers à Consulter

### Composants Blade

```
app/View/Components/
├── Button.php
├── Input.php
├── Select.php
├── Textarea.php
├── TomSelect.php
├── Datepicker.php
├── TimePicker.php
├── Alert.php
├── Badge.php
└── Modal.php

resources/views/components/
├── button.blade.php
├── input.blade.php
├── select.blade.php
├── textarea.blade.php
├── tom-select.blade.php
├── datepicker.blade.php
├── time-picker.blade.php
├── alert.blade.php
├── badge.blade.php
└── modal.blade.php
```

### Référence Design

```
resources/views/admin/
└── components-demo.blade.php  ⭐ RÉFÉRENCE ULTIME
```

### Documentation

```
/
├── PLAN_REFONTE_DESIGN_ZENFLEET.md
├── COMPOSANTS_REFERENCE.md (ce fichier)
├── HEROICONS_MAPPING.md (à créer)
├── INVENTAIRE_VUES.txt (à créer)
├── STYLES_GUIDE.md (à créer)
└── ALPINE_PATTERNS.md (à créer)
```

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 18/10/2025 | Documentation initiale complète |

---

**Maintenu par:** ZenFleet Development Team
**Dernière mise à jour:** 18 Octobre 2025
