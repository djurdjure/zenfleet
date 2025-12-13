# SlimSelect Style Guide - ZenFleet Enterprise

> **Version**: 1.1-Enterprise
> **Date**: 2025-12-13
> **Status**: Production Ready
> **Référence**: Pages de création/modification de véhicules et chauffeurs

---

## Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture du composant](#architecture-du-composant)
3. [Variables CSS](#variables-css)
4. [Styles entreprise-grade](#styles-entreprise-grade)
5. [Composant Blade](#composant-blade)
6. [Initialisation JavaScript (ZenFleetSelect)](#initialisation-javascript-zenfleetselect)
7. [États et validations](#états-et-validations)
8. [Guide de migration TomSelect → SlimSelect](#guide-de-migration-tomselect--slimselect)
9. [Responsive et Accessibilité](#responsive-et-accessibilité)
10. [Implémentation Multi-select](#implementation-multi-select)

---

## Vue d'ensemble

SlimSelect est la bibliothèque de remplacement de TomSelect dans ZenFleet. Elle offre:

- ✅ Bundle plus léger (~15KB vs ~40KB pour TomSelect)
- ✅ API plus simple et moderne
- ✅ Meilleure intégration avec Alpine.js
- ✅ Support natif des thèmes personnalisés
- ✅ Performances optimisées
- ✅ Wrapper Enterprise `ZenFleetSelect` pour une synchronisation robuste

### Fichiers clés

| Fichier | Chemin | Rôle |
|---------|--------|------|
| **Composant Blade** | `resources/views/components/slim-select.blade.php` | Composant réutilisable |
| **Wrapper JS** | `resources/js/components/zenfleet-select.js` | Logique d'initialisation et synchronisation |
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
    <label for="slimselect-license-categories" class="block mb-2 text-sm font-medium text-gray-900">
        Catégories de permis
        <span class="text-red-500">*</span> <!-- Si required -->
    </label>

    <!-- Select natif (remplacé par SlimSelect) -->
    <!-- Note: x-data et wire:ignore ne sont plus nécessaires grâce à ZenFleetSelect.js -->
    <select
        name="license_categories[]"
        id="slimselect-license-categories-uniqueId"
        class="slimselect-field w-full"
        data-slimselect="true"
        data-placeholder="Sélectionnez..."
        multiple
        required>
        <option value="B">B</option>
        <option value="C" selected>C</option>
    </select>
    <!-- ... Error messages ... -->
</div>
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
    @change="validateField('vehicle_type_id', $event.target.value)"
/>
```

### Sélection multiple (e.g., Catégories de Permis)

```blade
<x-slim-select
    name="license_categories[]"
    label="Catégories de permis"
    :options="$licenseOptions"
    :selected="old('license_categories', [])"
    placeholder="Sélectionnez les catégories..."
    multiple="true"
    required
    :error="$errors->first('license_categories')"
    @change="validateField('license_categories', $event.target.value)"
    helpText="Sélectionnez toutes les catégories détenues"
/>
```

**Note importante :** Pour les sélections multiples, assurez-vous que le nom du champ se termine par `[]` (ex: `name="license_categories[]"`). Le composant gère automatiquement la sanitization de l'ID HTML.

---

## Initialisation JavaScript (ZenFleetSelect)

L'initialisation est gérée centralement par `resources/js/components/zenfleet-select.js`. Ce wrapper "Enterprise" assure :

1.  **Initialisation automatique** sur les éléments avec `data-slimselect="true"`.
2.  **Synchronisation robuste** :
    -   Synchronisation immédiate vers le `<select>` original lors du changement (`afterChange`).
    -   Synchronisation forcée avant la soumission du formulaire (`form submit handler`) pour garantir que les données sont envoyées au serveur.
3.  **Support Livewire** : Synchronisation bidirectionnelle avec les propriétés Livewire.

### Extrait de ZenFleetSelect.js

```javascript
// Sync immédiat
afterChange: (newVal) => {
    this.syncToOriginalSelect();
    // ...
}

// Sync force avant submit
setupFormSync() {
    const form = this.element.closest('form');
    // ... attach 'submit' listener ...
    // appelle syncToOriginalSelect() sur tous les select du formulaire
}
```

---

## Implémentation Multi-select

Pour implémenter un champ multi-select (comme les catégories de permis) :

1.  **Backend (Controller)** : Attendez-vous à recevoir un tableau.
    ```php
    // Validation
    'license_categories' => ['required', 'array'],
    'license_categories.*' => ['string', 'in:A,B,C,D...'],
    ```

2.  **Vue (Blade)** : Utilisez la prop `multiple="true"`.
    ```blade
    <x-slim-select
        name="license_categories[]"
        :options="$options"
        :multiple="true"
        ...
    />
    ```

3.  **Données pré-remplies** : Passez un tableau à la prop `:selected`.
    ```php
    // Dans le contrôleur ou la vue
    $selectedCategories = ['B', 'C'];
    // Component
    :selected="old('license_categories', $selectedCategories)"
    ```

---
