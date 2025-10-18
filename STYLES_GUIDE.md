# 🎨 ZenFleet - Guide des Styles Tailwind CSS

**Version:** 1.0.0
**Date:** 18 Octobre 2025
**Framework:** Tailwind CSS 3.x

---

## 📖 Table des Matières

1. [Philosophie de Design](#philosophie-de-design)
2. [Palette de Couleurs](#palette-de-couleurs)
3. [Typographie](#typographie)
4. [Espacements](#espacements)
5. [Layouts et Grilles](#layouts-et-grilles)
6. [Composants UI](#composants-ui)
7. [Animations et Transitions](#animations-et-transitions)
8. [Responsive Design](#responsive-design)
9. [Dark Mode](#dark-mode)
10. [Patterns Réutilisables](#patterns-réutilisables)

---

## Philosophie de Design

### Principes Fondamentaux

**ZenFleet Design System** est basé sur 5 piliers :

1. **🎯 Enterprise-Grade**
   - Apparence professionnelle et sobre
   - Shadows subtiles (`shadow-sm` au lieu de `shadow-lg`)
   - Borders arrondies modérées (`rounded-lg` au lieu de `rounded-xl`)
   - Pas de focus rings flashy sur les boutons

2. **💙 Blue Theme**
   - Couleur primaire : `blue-600` (au lieu des couleurs Flowbite)
   - Cohérence avec l'identité visuelle ZenFleet
   - Utilisation systématique dans tous les CTA

3. **🌓 Dark Mode First**
   - Support complet du mode sombre
   - Toutes les classes ont leur variant `dark:`
   - Contraste optimisé pour accessibilité

4. **📱 Mobile-First**
   - Conception mobile d'abord
   - Progressive enhancement pour desktop
   - Breakpoints : sm (640px), md (768px), lg (1024px), xl (1280px), 2xl (1536px)

5. **♿ Accessibilité (WCAG 2.1 Level AA)**
   - Contraste minimal 4.5:1 pour texte
   - Labels obligatoires pour inputs
   - États focus visibles
   - Navigation clavier complète

---

## Palette de Couleurs

### Couleurs Principales

#### Primary (Blue)

**Usage:** Boutons primaires, liens, éléments interactifs

```css
/* Light Mode */
bg-blue-600       /* Boutons, badges primaires */
hover:bg-blue-700 /* Hover state */
active:bg-blue-800 /* Active state */
text-blue-600     /* Liens, icônes */

/* Dark Mode */
dark:bg-blue-600
dark:hover:bg-blue-700
dark:text-blue-400
```

**Variantes Blue:**

| Nuance | Classe | Usage | Hex |
|--------|--------|-------|-----|
| 50 | `bg-blue-50` | Backgrounds très clairs | #EFF6FF |
| 100 | `bg-blue-100` | Icône backgrounds, badges | #DBEAFE |
| 200 | `bg-blue-200` | Hover backgrounds | #BFDBFE |
| 300 | `bg-blue-300` | Borders | #93C5FD |
| 400 | `text-blue-400` | Dark mode text | #60A5FA |
| 500 | `border-blue-500` | Focus borders | #3B82F6 |
| **600** | **`bg-blue-600`** | **⭐ Primary** | **#2563EB** |
| 700 | `hover:bg-blue-700` | Hover states | #1D4ED8 |
| 800 | `active:bg-blue-800` | Active states | #1E40AF |
| 900 | `bg-blue-900` | Dark backgrounds | #1E3A8A |

---

#### Gray (Neutrals)

**Usage:** Texte, backgrounds, borders

```css
/* Backgrounds */
bg-white          /* Cards, sections (light) */
bg-gray-50        /* Inputs, table headers (light) */
bg-gray-100       /* Hover states, disabled (light) */
bg-gray-800       /* Cards, sections (dark) */
bg-gray-700       /* Inputs (dark) */
bg-gray-900       /* Page background (dark) */

/* Text */
text-gray-900     /* Primary text (light) */
text-gray-700     /* Secondary text (light) */
text-gray-600     /* Tertiary text (light) */
text-gray-500     /* Placeholder, disabled */
text-gray-400     /* Muted text */
text-white        /* Primary text (dark) */
text-gray-300     /* Secondary text (dark) */

/* Borders */
border-gray-200   /* Cards, sections (light) */
border-gray-300   /* Inputs, dropdowns (light) */
border-gray-700   /* Cards, sections (dark) */
border-gray-600   /* Inputs, dropdowns (dark) */
```

**Table Gray:**

| Nuance | Light Mode | Dark Mode | Usage |
|--------|-----------|-----------|-------|
| 50 | `bg-gray-50` | - | Input backgrounds, table headers |
| 100 | `bg-gray-100` | - | Hover states, disabled |
| 200 | `border-gray-200` | - | Card borders |
| 300 | `border-gray-300` | - | Input borders |
| 400 | `text-gray-400` | `text-gray-400` | Muted text |
| 500 | `text-gray-500` | - | Placeholder |
| 600 | `text-gray-600` | `border-gray-600` | Tertiary text, input borders (dark) |
| 700 | `text-gray-700` | `bg-gray-700` | Secondary text, input bg (dark) |
| 800 | - | `bg-gray-800` | Card backgrounds (dark) |
| 900 | `text-gray-900` | `bg-gray-900` | Primary text, page bg (dark) |

---

#### Semantic Colors

**Success (Green):**

```css
/* Badges, Alerts */
bg-green-100 text-green-800  /* Light */
dark:bg-green-900 dark:text-green-200  /* Dark */

/* Buttons */
bg-green-600 hover:bg-green-700
text-white

/* Icons */
text-green-600
```

**Error (Red):**

```css
/* Badges, Alerts, Validation */
bg-red-100 text-red-800  /* Light */
dark:bg-red-900 dark:text-red-200  /* Dark */

/* Buttons */
bg-red-600 hover:bg-red-700
text-white

/* Borders (Input errors) */
border-red-300 focus:border-red-500
```

**Warning (Orange):**

```css
/* Badges, Alerts */
bg-orange-100 text-orange-800  /* Light */
dark:bg-orange-900 dark:text-orange-200  /* Dark */

/* Icons */
text-orange-600
```

**Info (Blue):**

```css
/* Badges, Alerts */
bg-blue-100 text-blue-800  /* Light */
dark:bg-blue-900 dark:text-blue-200  /* Dark */

/* Icons */
text-blue-600
```

---

### Palette Complète

| Couleur | Light BG | Light Text | Dark BG | Dark Text | Usage |
|---------|----------|------------|---------|-----------|-------|
| **Blue** | `bg-blue-100` | `text-blue-800` | `dark:bg-blue-900` | `dark:text-blue-200` | Primary, Info |
| **Green** | `bg-green-100` | `text-green-800` | `dark:bg-green-900` | `dark:text-green-200` | Success, Actif |
| **Red** | `bg-red-100` | `text-red-800` | `dark:bg-red-900` | `dark:text-red-200` | Error, Danger |
| **Orange** | `bg-orange-100` | `text-orange-800` | `dark:bg-orange-900` | `dark:text-orange-200` | Warning, En attente |
| **Purple** | `bg-purple-100` | `text-purple-800` | `dark:bg-purple-900` | `dark:text-purple-200` | Info secondaire |
| **Gray** | `bg-gray-100` | `text-gray-800` | `dark:bg-gray-700` | `dark:text-gray-300` | Neutral, Archivé |

---

## Typographie

### Font Family

**ZenFleet** utilise **Figtree** comme police principale :

```css
font-family: 'Figtree', system-ui, -apple-system, sans-serif;
```

**Import (dans layout):**

```html
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
```

**Poids disponibles:**
- 400 (Regular) : Texte normal
- 500 (Medium) : Labels, petits titres
- 600 (Semibold) : Titres, boutons

---

### Tailles de Texte

#### Headers

```css
/* Page Title (H1) */
text-3xl font-bold text-gray-900 dark:text-white
/* 30px, bold */

/* Section Title (H2) */
text-2xl font-semibold text-gray-900 dark:text-white
/* 24px, semibold */

/* Modal Title / Large Title (H2 variant) */
text-xl font-semibold text-gray-900 dark:text-white
/* 20px, semibold */

/* Subsection Title (H3) */
text-lg font-medium text-gray-900 dark:text-white
/* 18px, medium */

/* Small Section Title (H4) */
text-sm font-medium text-gray-900 dark:text-white
/* 14px, medium */
```

**Exemples:**

```blade
{{-- Page Title --}}
<h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
    🎨 ZenFleet Design System
</h1>

{{-- Section Title --}}
<h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
    Buttons
</h2>

{{-- Subsection Title --}}
<h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
    Inputs
</h3>

{{-- Small Title --}}
<h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
    Variantes
</h4>
```

---

#### Body Text

```css
/* Regular Body */
text-sm text-gray-900 dark:text-white
/* 14px - Inputs, buttons, table cells */

/* Small Text */
text-xs text-gray-600 dark:text-gray-400
/* 12px - Badges, labels, help text */

/* Base Text */
text-base text-gray-900 dark:text-white
/* 16px - Paragraphes, descriptions */
```

**Hiérarchie de Couleurs:**

```css
/* Primary Text */
text-gray-900 dark:text-white

/* Secondary Text */
text-gray-700 dark:text-gray-300

/* Tertiary Text */
text-gray-600 dark:text-gray-400

/* Muted Text */
text-gray-500 dark:text-gray-500

/* Placeholder */
placeholder-gray-400 dark:placeholder-gray-500
```

---

#### Font Weights

```css
font-normal    /* 400 - Texte normal */
font-medium    /* 500 - Labels, small titles */
font-semibold  /* 600 - Titles, buttons */
font-bold      /* 700 - Page titles (rare) */
```

**Usage:**

| Élément | Weight | Classe |
|---------|--------|--------|
| Page Title | Bold | `font-bold` |
| Section Title | Semibold | `font-semibold` |
| Subsection Title | Medium | `font-medium` |
| Button Text | Medium | `font-medium` |
| Label | Medium | `font-medium` |
| Body Text | Normal | `font-normal` (default) |
| Badge | Medium | `font-medium` |

---

### Line Heights

```css
leading-tight   /* 1.25 - Titles */
leading-normal  /* 1.5 - Body text (default) */
leading-relaxed /* 1.625 - Descriptions */
```

---

### Lettres et Capitales

```css
/* Uppercase (Table headers, badges) */
uppercase

/* Tracking (Table headers) */
tracking-wider  /* 0.05em */

/* Capitalize (Proper nouns) */
capitalize
```

**Exemple Table Header:**

```css
text-xs font-medium text-gray-500 uppercase tracking-wider
```

---

## Espacements

### Spacing Scale

ZenFleet utilise l'échelle Tailwind par défaut (basée sur rem, 1 unit = 0.25rem = 4px):

| Classe | Valeur | Pixels | Usage |
|--------|--------|--------|-------|
| `0` | 0 | 0px | Aucun espacement |
| `0.5` | 0.125rem | 2px | Très petit |
| `1` | 0.25rem | 4px | Petit |
| `2` | 0.5rem | 8px | Badge padding |
| `3` | 0.75rem | 12px | Small padding |
| `4` | 1rem | 16px | Standard padding |
| `5` | 1.25rem | 20px | Medium padding |
| `6` | 1.5rem | 24px | Card padding, gaps |
| `8` | 2rem | 32px | Large padding |
| `10` | 2.5rem | 40px | Extra large |
| `12` | 3rem | 48px | Section spacing |
| `16` | 4rem | 64px | Large section spacing |

---

### Padding Standards

#### Cards / Sections

```css
p-6  /* 24px - Standard card padding */
p-4  /* 16px - Small cards */
p-8  /* 32px - Large sections */
```

**Exemple:**

```blade
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
    {{-- Content --}}
</div>
```

---

#### Buttons

```css
/* Small */
px-3 py-2  /* 12px horizontal, 8px vertical */

/* Medium (Default) */
px-5 py-2.5  /* 20px horizontal, 10px vertical */

/* Large */
px-6 py-3  /* 24px horizontal, 12px vertical */
```

---

#### Inputs

```css
p-2.5  /* 10px - All sides */
px-3 py-2  /* Alternative: 12px horizontal, 8px vertical */
```

**Avec icône:**

```css
pl-10  /* 40px - Left padding quand icône à gauche */
pr-10  /* 40px - Right padding quand icône à droite */
```

---

#### Tables

```css
/* Table Cell */
px-6 py-4  /* 24px horizontal, 16px vertical */

/* Table Header */
px-6 py-3  /* 24px horizontal, 12px vertical */
```

---

### Margin Standards

#### Entre Sections

```css
mb-6   /* 24px - Entre cards/sections */
mb-8   /* 32px - Entre grandes sections */
mb-12  /* 48px - Entre blocs majeurs */
```

---

#### Entre Éléments dans Section

```css
mb-2  /* 8px - Entre label et input */
mb-3  /* 12px - Entre small titles et content */
mb-4  /* 16px - Entre titles et content */
```

---

#### Spacing Utilities

```css
/* Space Between (Flexbox children) */
space-y-4  /* 16px vertical - Form fields */
space-y-6  /* 24px vertical - Sections */
space-y-8  /* 32px vertical - Large sections */
space-x-3  /* 12px horizontal - Buttons */

/* Gap (Grid children) */
gap-3  /* 12px - Buttons, badges */
gap-6  /* 24px - Form grids */
```

**Exemples:**

```blade
{{-- Form avec space-y --}}
<div class="space-y-6">
    <x-input name="name" label="Nom" />
    <x-input name="email" label="Email" />
    <x-input name="phone" label="Téléphone" />
</div>

{{-- Buttons avec space-x --}}
<div class="flex space-x-3">
    <x-button variant="primary">Enregistrer</x-button>
    <x-button variant="secondary">Annuler</x-button>
</div>

{{-- Grid avec gap --}}
<div class="grid grid-cols-2 gap-6">
    <x-input name="first_name" />
    <x-input name="last_name" />
</div>
```

---

## Layouts et Grilles

### Container Principal

```css
/* Container avec max-width et centrage */
mx-auto max-w-7xl px-4 py-8 lg:py-16
```

**Breakpoints max-width:**
- `max-w-7xl` : 1280px (Standard)
- `max-w-6xl` : 1152px
- `max-w-5xl` : 1024px
- `max-w-4xl` : 896px

**Exemple:**

```blade
<section class="bg-white dark:bg-gray-900">
    <div class="py-8 px-4 mx-auto max-w-7xl lg:py-16">
        {{-- Content --}}
    </div>
</section>
```

---

### Grid System

#### 2 Colonnes (Responsive)

```css
grid grid-cols-1 md:grid-cols-2 gap-6
```

**Exemple:**

```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <x-input name="first_name" label="Prénom" />
    <x-input name="last_name" label="Nom" />
</div>
```

---

#### 3 Colonnes (Responsive)

```css
grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6
```

---

#### 4 Colonnes (Responsive)

```css
grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6
```

---

#### Auto-fit Grid (Cards)

```css
grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6
```

---

### Flexbox Patterns

#### Flex Row avec Gap

```css
flex flex-wrap gap-3
```

**Usage:** Boutons, badges

---

#### Flex Column

```css
flex flex-col space-y-4
```

**Usage:** Forms, stacked content

---

#### Flex Center

```css
flex items-center justify-center
```

**Usage:** Centering content

---

#### Flex Between

```css
flex items-center justify-between
```

**Usage:** Header avec actions, cards avec badges et buttons

**Exemple:**

```blade
<div class="flex items-center justify-between mb-4">
    <h2 class="text-2xl font-semibold">Titre</h2>
    <x-button icon="plus">Ajouter</x-button>
</div>
```

---

## Composants UI

### Cards / Sections

**Pattern Standard:**

```css
bg-white dark:bg-gray-800
rounded-lg
shadow-sm
p-6
mb-6
border border-gray-200 dark:border-gray-700
```

**Exemple:**

```blade
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6 border border-gray-200 dark:border-gray-700">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
        Section Title
    </h2>
    <div class="space-y-4">
        {{-- Content --}}
    </div>
</div>
```

---

### Buttons

**Pattern Standard:**

```css
inline-flex items-center justify-center
font-medium
rounded-lg
transition-all duration-200
focus:outline-none
disabled:opacity-50 disabled:cursor-not-allowed
```

**Primary:**

```css
text-white
bg-blue-600 hover:bg-blue-700 active:bg-blue-800
dark:bg-blue-600 dark:hover:bg-blue-700
px-5 py-2.5 text-sm
```

**Secondary:**

```css
text-gray-900 dark:text-white
bg-white dark:bg-gray-800
border border-gray-300 dark:border-gray-600
hover:bg-gray-100 dark:hover:bg-gray-700
px-5 py-2.5 text-sm
```

---

### Inputs

**Pattern Standard:**

```css
bg-gray-50 dark:bg-gray-700
border border-gray-300 dark:border-gray-600
text-gray-900 dark:text-white
text-sm
rounded-lg
focus:ring-primary-600 focus:border-primary-600
block w-full
p-2.5
placeholder-gray-400 dark:placeholder-gray-500
```

**Avec Erreur:**

```css
border-red-300 focus:ring-red-500 focus:border-red-500
dark:border-red-600
```

---

### Tables

**Pattern Standard (Style Kilométrage):**

```blade
<div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Colonne
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        Data
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

---

### Badges

**Pattern Standard:**

```css
inline-flex items-center
px-2.5 py-0.5
rounded-full
text-xs font-medium
```

**Success:**

```css
bg-green-100 text-green-800
dark:bg-green-900 dark:text-green-200
```

---

### Modals

**Backdrop:**

```css
fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 z-40
```

**Panel:**

```css
relative bg-white dark:bg-gray-800
rounded-lg shadow-xl
max-w-lg w-full
```

---

## Animations et Transitions

### Transitions Standards

```css
/* Boutons, Links */
transition-all duration-200

/* Hover states */
transition-colors duration-150

/* Modals, Dropdowns */
transition-opacity duration-300
```

---

### Hover States

```css
/* Buttons */
hover:bg-blue-700

/* Links */
hover:text-blue-900

/* Table Rows */
hover:bg-gray-50

/* Cards */
hover:shadow-md
```

---

### Active States

```css
/* Buttons */
active:bg-blue-800

/* Links */
active:text-blue-950
```

---

### Focus States

```css
/* Inputs */
focus:ring-primary-600 focus:border-primary-600

/* Buttons (minimal, enterprise) */
focus:outline-none

/* Links */
focus:outline-none focus:underline
```

---

## Responsive Design

### Breakpoints

| Breakpoint | Min Width | Usage |
|-----------|-----------|-------|
| `sm` | 640px | Petits tablettes, grands mobiles |
| `md` | 768px | Tablettes |
| `lg` | 1024px | Petits laptops |
| `xl` | 1280px | Laptops |
| `2xl` | 1536px | Grands écrans |

---

### Patterns Responsive

#### Hide/Show

```css
/* Mobile only */
block md:hidden

/* Desktop only */
hidden md:block
```

---

#### Grid Responsive

```css
/* Mobile: 1 col, Tablet: 2 cols, Desktop: 3 cols */
grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6
```

---

#### Padding Responsive

```css
/* Mobile: 16px, Desktop: 64px */
py-4 lg:py-16

/* Mobile: 16px, Desktop: 32px */
px-4 lg:px-8
```

---

#### Text Size Responsive

```css
/* Mobile: text-2xl, Desktop: text-4xl */
text-2xl lg:text-4xl
```

---

## Dark Mode

### Classes Dark Mode

**Toujours définir les variants dark:** pour chaque classe de couleur

```css
/* ❌ Incorrect - Pas de dark mode */
bg-white text-gray-900

/* ✅ Correct - Avec dark mode */
bg-white dark:bg-gray-800 text-gray-900 dark:text-white
```

---

### Patterns Dark Mode

#### Backgrounds

```css
bg-white dark:bg-gray-800       /* Cards */
bg-gray-50 dark:bg-gray-700     /* Inputs */
bg-gray-100 dark:bg-gray-600    /* Hover, disabled */
```

---

#### Text

```css
text-gray-900 dark:text-white           /* Primary */
text-gray-700 dark:text-gray-300        /* Secondary */
text-gray-600 dark:text-gray-400        /* Tertiary */
text-gray-500 dark:text-gray-500        /* Muted */
```

---

#### Borders

```css
border-gray-200 dark:border-gray-700    /* Cards */
border-gray-300 dark:border-gray-600    /* Inputs */
```

---

## Patterns Réutilisables

### Page Container

```blade
<section class="bg-white dark:bg-gray-900">
    <div class="py-8 px-4 mx-auto max-w-7xl lg:py-16">
        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Page Title
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Description
            </p>
        </div>

        {{-- Content --}}
    </div>
</section>
```

---

### Section Card

```blade
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6 border border-gray-200 dark:border-gray-700">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
        Section Title
    </h2>

    <div class="space-y-4">
        {{-- Section Content --}}
    </div>
</div>
```

---

### Form Grid

```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <x-input name="field1" label="Field 1" />
    <x-input name="field2" label="Field 2" />
    <x-input name="field3" label="Field 3" />
    <x-input name="field4" label="Field 4" />
</div>
```

---

### Action Buttons Group

```blade
<div class="flex flex-wrap gap-3">
    <x-button variant="primary" icon="plus">
        Nouveau
    </x-button>
    <x-button variant="secondary" icon="download">
        Exporter
    </x-button>
</div>
```

---

### Section Header with Actions

```blade
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
            Véhicules
        </h2>
        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
            Gérez votre flotte de véhicules
        </p>
    </div>
    <x-button variant="primary" icon="plus">
        Ajouter un véhicule
    </x-button>
</div>
```

---

**Maintenu par:** ZenFleet Development Team
**Dernière mise à jour:** 18 Octobre 2025
