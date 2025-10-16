# 🎨 ZENFLEET DESIGN SYSTEM

**Version:** 2.0  
**Date:** 16 Octobre 2025  
**Framework:** Tailwind CSS 3.4 + Heroicons  
**Status:** ✅ Production Ready

---

## 📋 TABLE DES MATIÈRES

1. [Introduction](#introduction)
2. [Philosophie de Design](#philosophie)
3. [Palette de Couleurs](#couleurs)
4. [Typographie](#typographie)
5. [Espacements](#espacements)
6. [Composants](#composants)
7. [Icônes](#icones)
8. [Accessibilité](#accessibilite)
9. [Guidelines](#guidelines)

---

## 🎯 INTRODUCTION {#introduction}

Le Design System ZenFleet est un système de design **utility-first** basé sur **Tailwind CSS 3.4**. Il garantit une cohérence visuelle absolue à travers toute l'application de gestion de flotte.

### Principes fondamentaux

✅ **Utility-First** : Classes atomiques Tailwind uniquement, 0 CSS custom  
✅ **Component-Based** : Composants Blade réutilisables avec props  
✅ **Accessible** : WCAG 2.1 niveau AA minimum  
✅ **Responsive** : Mobile-first, breakpoints sm/md/lg/xl/2xl  
✅ **Maintainable** : Documentation complète, exemples clairs

---

## 💡 PHILOSOPHIE DE DESIGN {#philosophie}

### Design Utility-First

```html
<!-- ❌ ANTI-PATTERN: CSS custom classes -->
<button class="btn btn-primary">Save</button>

<!-- ✅ BONNE PRATIQUE: Tailwind utilities -->
<button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
    Save
</button>

<!-- ✅ MEILLEURE PRATIQUE: Composant Blade réutilisable -->
<x-button variant="primary">Save</x-button>
```

### Hiérarchie des Solutions

1. **Tailwind utilities** : Solution par défaut
2. **Composants Blade** : Si pattern répété 3+ fois
3. **Composants Livewire** : Si interactivité serveur requise
4. **CSS custom** : ⛔ JAMAIS (sauf cas exceptionnel validé)

---

## 🎨 PALETTE DE COULEURS {#couleurs}

### Couleurs Principales

#### Primary (Bleu)
```html
<!-- Backgrounds -->
<div class="bg-blue-50">   <!-- #eff6ff - Très clair -->
<div class="bg-blue-100">  <!-- #dbeafe -->
<div class="bg-blue-500">  <!-- #3b82f6 - Principal -->
<div class="bg-blue-600">  <!-- #2563eb - Hover -->
<div class="bg-blue-900">  <!-- #1e3a8a - Foncé -->

<!-- Text -->
<p class="text-blue-600">Texte principal</p>
<p class="text-blue-700">Texte hover</p>

<!-- Borders -->
<div class="border-blue-500">...</div>
```

**Usage:**
- Boutons primaires
- Liens importants
- États actifs dans la navigation
- Call-to-actions

#### Success (Vert)
```html
<div class="bg-green-50 border-l-4 border-green-500 text-green-700">
    Opération réussie
</div>
```

**Usage:**
- Messages de succès
- États positifs (véhicule disponible)
- Badges "Actif"

#### Warning (Ambre)
```html
<div class="bg-amber-50 border-l-4 border-amber-500 text-amber-700">
    Attention requise
</div>
```

**Usage:**
- Avertissements
- États d'alerte (maintenance proche)
- Actions réversibles

#### Danger (Rouge)
```html
<div class="bg-red-50 border-l-4 border-red-500 text-red-700">
    Erreur critique
</div>
```

**Usage:**
- Messages d'erreur
- Actions destructives (supprimer)
- États critiques (panne)

#### Info (Cyan)
```html
<div class="bg-cyan-50 border-l-4 border-cyan-500 text-cyan-700">
    Information
</div>
```

**Usage:**
- Messages informatifs
- Tooltips
- Aide contextuelle

### Couleurs Neutres (Gris)

```html
<!-- Backgrounds -->
<div class="bg-gray-50">   <!-- #f9fafb - Background pages -->
<div class="bg-gray-100">  <!-- #f3f4f6 - Background cards -->
<div class="bg-white">     <!-- #ffffff - Cards, modals -->

<!-- Texte -->
<p class="text-gray-900">  <!-- #111827 - Titres principaux -->
<p class="text-gray-700">  <!-- #374151 - Corps de texte -->
<p class="text-gray-500">  <!-- #6b7280 - Texte secondaire -->
<p class="text-gray-400">  <!-- #9ca3af - Placeholders -->

<!-- Borders -->
<div class="border-gray-200">  <!-- #e5e7eb - Bordures subtiles -->
<div class="border-gray-300">  <!-- #d1d5db - Bordures standards -->
```

### Système de Couleurs Sémantiques

| Contexte | Couleur | Classes Tailwind |
|----------|---------|------------------|
| **Action primaire** | Bleu | `bg-blue-600 hover:bg-blue-700` |
| **Action secondaire** | Gris | `bg-gray-200 hover:bg-gray-300 text-gray-700` |
| **Action destructive** | Rouge | `bg-red-600 hover:bg-red-700 text-white` |
| **Succès** | Vert | `bg-green-50 text-green-700 border-green-500` |
| **Erreur** | Rouge | `bg-red-50 text-red-700 border-red-500` |
| **Avertissement** | Ambre | `bg-amber-50 text-amber-700 border-amber-500` |
| **Information** | Cyan | `bg-cyan-50 text-cyan-700 border-cyan-500` |

---

## 📝 TYPOGRAPHIE {#typographie}

### Hiérarchie des Titres

```html
<!-- H1: Titre de page principal -->
<h1 class="text-3xl font-bold text-gray-900 mb-6">
    Gestion de Flotte
</h1>

<!-- H2: Section principale -->
<h2 class="text-2xl font-semibold text-gray-800 mb-4">
    Véhicules Actifs
</h2>

<!-- H3: Sous-section -->
<h3 class="text-xl font-semibold text-gray-700 mb-3">
    Informations Générales
</h3>

<!-- H4: Titre de carte -->
<h4 class="text-lg font-medium text-gray-700 mb-2">
    Détails Véhicule
</h4>
```

### Corps de Texte

```html
<!-- Texte principal -->
<p class="text-base text-gray-700 leading-relaxed">
    Description standard avec bonne lisibilité.
</p>

<!-- Texte secondaire -->
<p class="text-sm text-gray-500">
    Informations complémentaires ou métadonnées.
</p>

<!-- Petit texte -->
<p class="text-xs text-gray-400">
    Timestamps, légendes, footnotes.
</p>
```

### Emphase et Formatage

```html
<!-- Texte en gras -->
<strong class="font-semibold text-gray-900">Important</strong>

<!-- Texte en italique -->
<em class="italic text-gray-700">Emphase</em>

<!-- Label de formulaire -->
<label class="block text-sm font-medium text-gray-700 mb-2">
    Nom du véhicule
</label>

<!-- Lien -->
<a href="#" class="text-blue-600 hover:text-blue-700 underline">
    En savoir plus
</a>
```

### Échelle de Tailles

| Classe | Taille | Usage |
|--------|--------|-------|
| `text-xs` | 12px / 0.75rem | Timestamps, badges |
| `text-sm` | 14px / 0.875rem | Corps secondaire, labels |
| `text-base` | 16px / 1rem | Corps principal |
| `text-lg` | 18px / 1.125rem | Lead paragraphs |
| `text-xl` | 20px / 1.25rem | H4, titres de carte |
| `text-2xl` | 24px / 1.5rem | H3, sous-titres |
| `text-3xl` | 30px / 1.875rem | H2, titres de section |
| `text-4xl` | 36px / 2.25rem | H1, titres de page |

---

## 📏 ESPACEMENTS {#espacements}

### Système d'Espacement Tailwind

ZenFleet utilise l'échelle d'espacement Tailwind standard (0.25rem = 4px par unité).

```html
<!-- Padding interne -->
<div class="p-4">   <!-- 16px tous côtés -->
<div class="px-6">  <!-- 24px horizontal -->
<div class="py-3">  <!-- 12px vertical -->

<!-- Margin externe -->
<div class="m-4">   <!-- 16px tous côtés -->
<div class="mb-6">  <!-- 24px bas -->
<div class="mt-8">  <!-- 32px haut -->

<!-- Gap (flexbox/grid) -->
<div class="flex gap-4">  <!-- 16px entre éléments -->
<div class="grid gap-6">  <!-- 24px entre éléments -->
```

### Guidelines d'Espacement

| Contexte | Espacement | Classes |
|----------|------------|---------|
| **Entre sections** | 32-48px | `mb-8` ou `mb-12` |
| **Entre cartes** | 16-24px | `gap-4` ou `gap-6` |
| **Padding carte** | 24px | `p-6` |
| **Padding bouton** | 8-12px vertical, 16-24px horizontal | `px-4 py-2` ou `px-6 py-3` |
| **Entre éléments form** | 16-20px | `mb-4` ou `mb-5` |
| **Entre label et input** | 8px | `mb-2` |

### Exemples Pratiques

```html
<!-- Card avec espacement cohérent -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">
        Titre de la carte
    </h3>
    <p class="text-gray-700 mb-6">
        Contenu de la carte avec espacement entre paragraphes.
    </p>
    <div class="flex gap-3">
        <button>Action 1</button>
        <button>Action 2</button>
    </div>
</div>

<!-- Form avec espacement cohérent -->
<form class="space-y-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Nom
        </label>
        <input class="w-full px-4 py-2 border rounded-lg" />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Email
        </label>
        <input class="w-full px-4 py-2 border rounded-lg" />
    </div>
    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg">
        Enregistrer
    </button>
</form>
```

---

## 🧩 COMPOSANTS {#composants}

### Button Component

#### Installation

```bash
php artisan make:component Button
```

#### Props

| Prop | Type | Valeurs | Défaut |
|------|------|---------|--------|
| `variant` | string | `primary`, `secondary`, `danger`, `success`, `ghost` | `primary` |
| `size` | string | `sm`, `md`, `lg` | `md` |
| `icon` | string | Nom Heroicon | `null` |
| `iconPosition` | string | `left`, `right` | `left` |
| `href` | string | URL | `null` |
| `type` | string | `button`, `submit`, `reset` | `button` |

#### Usage

```blade
{{-- Bouton primaire simple --}}
<x-button variant="primary">
    Créer un véhicule
</x-button>

{{-- Bouton avec icône --}}
<x-button variant="primary" icon="plus" iconPosition="left">
    Nouveau
</x-button>

{{-- Bouton danger (supprimer) --}}
<x-button variant="danger" icon="trash" size="sm">
    Supprimer
</x-button>

{{-- Lien stylé comme bouton --}}
<x-button href="/admin/vehicles" variant="secondary">
    Voir tous les véhicules
</x-button>

{{-- Bouton ghost (minimal) --}}
<x-button variant="ghost" icon="x-mark">
    Annuler
</x-button>
```

#### Variantes Visuelles

```html
<!-- Primary: Action principale -->
<button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/50 transition-colors">
    Primary
</button>

<!-- Secondary: Action secondaire -->
<button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:ring-4 focus:ring-gray-500/50 transition-colors">
    Secondary
</button>

<!-- Danger: Action destructive -->
<button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-500/50 transition-colors">
    Danger
</button>

<!-- Success: Confirmation -->
<button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-500/50 transition-colors">
    Success
</button>

<!-- Ghost: Action minimale -->
<button class="px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-500/50 transition-colors">
    Ghost
</button>
```

---

### Input Component

#### Props

| Prop | Type | Valeurs | Défaut |
|------|------|---------|--------|
| `type` | string | `text`, `email`, `password`, `number`, `date` | `text` |
| `name` | string | Nom du champ | required |
| `label` | string | Label du champ | `null` |
| `placeholder` | string | Placeholder | `null` |
| `error` | string | Message d'erreur | `null` |
| `helpText` | string | Texte d'aide | `null` |
| `icon` | string | Nom Heroicon | `null` |
| `required` | boolean | Champ requis | `false` |
| `disabled` | boolean | Champ désactivé | `false` |

#### Usage

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

{{-- Input avec erreur --}}
<x-input 
    name="phone" 
    label="Téléphone" 
    :error="$errors->first('phone')"
    helpText="Format: 06 12 34 56 78"
/>

{{-- Input requis --}}
<x-input 
    name="brand" 
    label="Marque" 
    required
/>

{{-- Input désactivé --}}
<x-input 
    name="status" 
    label="Statut" 
    value="Actif"
    disabled
/>
```

#### États Visuels

```html
<!-- État normal -->
<input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-500/50 focus:border-blue-500" />

<!-- État focus -->
<input class="... focus:ring-4 focus:ring-blue-500/50 focus:border-blue-500" />

<!-- État erreur -->
<input class="... border-red-500 focus:ring-red-500/50 focus:border-red-500" />

<!-- État disabled -->
<input class="... bg-gray-100 text-gray-500 cursor-not-allowed" disabled />

<!-- État success (validation) -->
<input class="... border-green-500 focus:ring-green-500/50" />
```

---

### Modal Component

#### Usage

```blade
{{-- Définir le modal --}}
<x-modal name="create-vehicle" title="Créer un véhicule" maxWidth="lg">
    <form method="POST" action="/vehicles">
        @csrf
        <div class="space-y-6">
            <x-input name="plate" label="Immatriculation" required />
            <x-input name="brand" label="Marque" required />
            <x-input name="model" label="Modèle" />
            
            <div class="flex gap-3 justify-end">
                <x-button type="button" variant="secondary" @click="show = false">
                    Annuler
                </x-button>
                <x-button type="submit" variant="primary">
                    Créer
                </x-button>
            </div>
        </div>
    </form>
</x-modal>

{{-- Ouvrir le modal --}}
<x-button @click="$dispatch('open-modal', 'create-vehicle')">
    Nouveau véhicule
</x-button>
```

#### Props

| Prop | Type | Valeurs | Défaut |
|------|------|---------|--------|
| `name` | string | ID unique du modal | required |
| `title` | string | Titre du modal | `null` |
| `maxWidth` | string | `sm`, `md`, `lg`, `xl`, `2xl`, `full` | `lg` |
| `closeable` | boolean | Modal fermable | `true` |

---

### Alert Component

#### Usage

```blade
{{-- Alert succès --}}
<x-alert type="success" title="Succès" dismissible>
    Le véhicule a été créé avec succès.
</x-alert>

{{-- Alert erreur --}}
<x-alert type="error" title="Erreur">
    Une erreur est survenue lors de l'enregistrement.
</x-alert>

{{-- Alert warning --}}
<x-alert type="warning" title="Attention">
    Ce véhicule nécessite une maintenance dans 7 jours.
</x-alert>

{{-- Alert info --}}
<x-alert type="info">
    Les données de kilométrage sont mises à jour toutes les heures.
</x-alert>
```

#### Variantes Visuelles

```html
<!-- Success -->
<div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
    <div class="flex items-start">
        <svg class="w-5 h-5 text-green-600 mt-0.5">...</svg>
        <div class="ml-3 flex-1">
            <h3 class="text-sm font-medium text-green-800">Succès</h3>
            <p class="text-sm text-green-700 mt-1">Message...</p>
        </div>
    </div>
</div>

<!-- Error -->
<div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">...</div>

<!-- Warning -->
<div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg">...</div>

<!-- Info -->
<div class="bg-cyan-50 border-l-4 border-cyan-500 p-4 rounded-r-lg">...</div>
```

---

### Table Component

#### Usage

```blade
<x-table>
    <x-slot name="header">
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
            Immatriculation
        </th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
            Marque
        </th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
            Statut
        </th>
        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
            Actions
        </th>
    </x-slot>
    
    @foreach($vehicles as $vehicle)
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ $vehicle->plate }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                {{ $vehicle->brand }} {{ $vehicle->model }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <x-badge :type="$vehicle->status_color">
                    {{ $vehicle->status }}
                </x-badge>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                <x-button href="/vehicles/{{ $vehicle->id }}" size="sm" variant="ghost">
                    Voir
                </x-button>
            </td>
        </tr>
    @endforeach
</x-table>
```

---

### Badge Component

#### Usage

```blade
<x-badge type="success">Actif</x-badge>
<x-badge type="warning">En maintenance</x-badge>
<x-badge type="danger">Hors service</x-badge>
<x-badge type="info">Nouveau</x-badge>
<x-badge type="gray">Archivé</x-badge>
```

#### Variantes

```html
<!-- Success (vert) -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
    Actif
</span>

<!-- Warning (ambre) -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
    En maintenance
</span>

<!-- Danger (rouge) -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
    Hors service
</span>
```

---

## 🖼️ ICÔNES {#icones}

### Héroicons (Officiel)

ZenFleet utilise **Heroicons** exclusivement pour toutes les icônes.

#### Installation

```bash
composer require blade-ui-kit/blade-heroicons
```

#### Usage

```blade
{{-- Icon outline (défaut, ligne fine) --}}
<x-heroicon-o-check class="w-5 h-5 text-green-600" />
<x-heroicon-o-x-mark class="w-5 h-5 text-red-600" />
<x-heroicon-o-truck class="w-6 h-6 text-blue-600" />

{{-- Icon solid (rempli) --}}
<x-heroicon-s-check-circle class="w-5 h-5 text-green-600" />
<x-heroicon-s-exclamation-triangle class="w-5 h-5 text-amber-600" />

{{-- Icon mini (20x20, plus compact) --}}
<x-heroicon-m-plus class="w-4 h-4" />
```

#### Mapping FontAwesome → Heroicons

| FontAwesome | Heroicons | Usage |
|-------------|-----------|-------|
| `fa-check` | `heroicon-o-check` | Validation, succès |
| `fa-times` / `fa-x` | `heroicon-o-x-mark` | Fermer, annuler |
| `fa-plus` | `heroicon-o-plus` | Ajouter |
| `fa-trash` | `heroicon-o-trash` | Supprimer |
| `fa-pencil` / `fa-edit` | `heroicon-o-pencil` | Éditer |
| `fa-eye` | `heroicon-o-eye` | Voir |
| `fa-cog` / `fa-settings` | `heroicon-o-cog-6-tooth` | Paramètres |
| `fa-user` | `heroicon-o-user` | Utilisateur |
| `fa-truck` | `heroicon-o-truck` | Véhicule |
| `fa-wrench` | `heroicon-o-wrench` | Maintenance |
| `fa-calendar` | `heroicon-o-calendar` | Date |
| `fa-search` | `heroicon-o-magnifying-glass` | Recherche |
| `fa-bell` | `heroicon-o-bell` | Notifications |
| `fa-chart-bar` | `heroicon-o-chart-bar` | Statistiques |
| `fa-download` | `heroicon-o-arrow-down-tray` | Télécharger |
| `fa-upload` | `heroicon-o-arrow-up-tray` | Uploader |

#### Tailles Standards

```blade
{{-- Extra small (16x16) --}}
<x-heroicon-o-check class="w-4 h-4" />

{{-- Small (20x20) --}}
<x-heroicon-o-check class="w-5 h-5" />

{{-- Medium (24x24) - Défaut --}}
<x-heroicon-o-check class="w-6 h-6" />

{{-- Large (32x32) --}}
<x-heroicon-o-check class="w-8 h-8" />
```

---

## ♿ ACCESSIBILITÉ {#accessibilite}

### Principes WCAG 2.1 Niveau AA

ZenFleet respecte les normes **WCAG 2.1 niveau AA** minimum.

#### Contraste des Couleurs

**Ratio minimum:** 4.5:1 pour le texte normal, 3:1 pour le texte large

```html
<!-- ✅ Bon contraste (4.5:1+) -->
<p class="text-gray-700">Texte normal sur fond blanc</p>
<p class="text-white bg-blue-600">Texte blanc sur bleu</p>

<!-- ❌ Mauvais contraste (<4.5:1) -->
<p class="text-gray-400">Texte trop clair</p>
```

#### Navigation au Clavier

Tous les éléments interactifs doivent être accessibles au clavier.

```html
<!-- Focus visible -->
<button class="... focus:ring-4 focus:ring-blue-500/50 focus:outline-none">
    Bouton
</button>

<!-- Ordre de tabulation logique (utiliser tabindex si nécessaire) -->
<input tabindex="1" />
<input tabindex="2" />
<button tabindex="3">Submit</button>
```

#### Attributs ARIA

```html
<!-- Labels accessibles -->
<button aria-label="Fermer le modal">
    <x-heroicon-o-x-mark class="w-5 h-5" />
</button>

<!-- Icônes décoratives -->
<x-heroicon-o-check class="w-5 h-5" aria-hidden="true" />
<span>Succès</span>

<!-- États dynamiques -->
<button aria-expanded="false" @click="open = !open" :aria-expanded="open">
    Menu
</button>

<!-- Roles -->
<div role="alert" class="...">Message d'alerte</div>
<nav role="navigation">...</nav>
```

#### Textes Alternatifs

```html
<!-- Images -->
<img src="logo.png" alt="Logo ZenFleet" />

<!-- Images décoratives -->
<img src="decoration.png" alt="" role="presentation" />
```

---

## 📐 GUIDELINES {#guidelines}

### Responsive Design

```html
<!-- Mobile first (défaut < 640px) -->
<div class="p-4 md:p-6 lg:p-8">
    <!-- 16px mobile, 24px tablet, 32px desktop -->
</div>

<!-- Breakpoints Tailwind -->
<!-- sm: 640px - Petite tablette -->
<!-- md: 768px - Tablette -->
<!-- lg: 1024px - Desktop -->
<!-- xl: 1280px - Large desktop -->
<!-- 2xl: 1536px - Extra large -->
```

### Conventions de Nommage

```blade
{{-- Composants: PascalCase --}}
<x-Button />
<x-InputField />

{{-- Props: camelCase --}}
<x-button iconPosition="left" />

{{-- Classes CSS: kebab-case (Tailwind) --}}
class="bg-blue-600 hover:bg-blue-700"
```

### Performance

```html
<!-- ✅ Utiliser @once pour assets externes -->
@once
    <script src="..."></script>
@endonce

<!-- ✅ Lazy loading images -->
<img src="..." loading="lazy" />

<!-- ✅ Purge CSS activé (tailwind.config.js) -->
<!-- Seules les classes utilisées sont compilées -->
```

---

## 📚 RESSOURCES

### Documentation Officielle

- [Tailwind CSS](https://tailwindcss.com/docs)
- [Heroicons](https://heroicons.com/)
- [Laravel Blade](https://laravel.com/docs/11.x/blade)
- [Alpine.js](https://alpinejs.dev/)
- [WCAG 2.1](https://www.w3.org/WAI/WCAG21/quickref/)

### Outils

- [Tailwind UI](https://tailwindui.com/) - Composants préfabriqués
- [Headless UI](https://headlessui.com/) - Composants accessible
- [Contrast Checker](https://webaim.org/resources/contrastchecker/)

---

**✅ DESIGN SYSTEM COMPLET**  
**📅 Dernière mise à jour:** 16 Octobre 2025  
**👨‍💻 Mainteneur:** Équipe ZenFleet  
**📧 Contact:** dev@zenfleet.com
