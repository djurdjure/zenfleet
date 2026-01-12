# Plan d'Implémentation : Upgrade Tailwind CSS 4

## Objectif
Migrer l'application de Tailwind CSS 3 vers Tailwind CSS 4.1, en optimisant les performances de build et en supprimant chirurgicalement toute trace de configuration Dark Mode.

## Vérifications Préliminaires
- [x] Analyse de `package.json` (Tailwind ~3.1)
- [x] Analyse de `tailwind.config.js` (Forte personnalisation : couleurs `zenfleet`, espacements, plugins)
- [x] Analyse de `vite.config.js` (Configuration Laravel standard)

## Modifications Proposées

### 1. Mise à jour des Dépendances
#### [MODIFY] [package.json](file:///wsl.localhost/Ubuntu-22.04/home/lynx/projects/zenfleet/package.json)
- Suppression de `autoprefixer` et `postcss` (gérés nativement par `@tailwindcss/vite`).
- Mise à jour de `tailwindcss` vers `^4.0`.
- Ajout de `@tailwindcss/vite`.
- Suppression de `@tailwindcss/forms` (si inclus ou géré nativement/autrement, à vérifier compatibilité v4). Note: Le plugin forms est compatible v4 mais s'installe via `@import`.

### 2. Configuration Vite
#### [MODIFY] [vite.config.js](file:///wsl.localhost/Ubuntu-22.04/home/lynx/projects/zenfleet/vite.config.js)
- Import du plugin `@tailwindcss/vite`.
- Ajout du plugin `tailwindcss()` à la liste des plugins.
- Suppression de la configuration CSS/PostCSS si elle devient redondante.

### 3. Nettoyage de la Configuration Obsolète
#### [DELETE] [postcss.config.js](file:///wsl.localhost/Ubuntu-22.04/home/lynx/projects/zenfleet/postcss.config.js)
- Fichier obsolète avec Tailwind 4 et le plugin Vite.

#### [DELETE] [tailwind.config.js](file:///wsl.localhost/Ubuntu-22.04/home/lynx/projects/zenfleet/tailwind.config.js)
- La configuration sera migrée vers CSS. Ce fichier sera supprimé une fois la migration complète.

### 4. Migration du CSS et du Thème
#### [MODIFY] [resources/css/app.css](file:///wsl.localhost/Ubuntu-22.04/home/lynx/projects/zenfleet/resources/css/app.css) et [resources/css/admin/app.css](file:///wsl.localhost/Ubuntu-22.04/home/lynx/projects/zenfleet/resources/css/admin/app.css)
- Remplacement de `@tailwind base; ...` par `@import "tailwindcss";`.
- **Migration du Thème ZenFleet** :
    - Définition des variables CSS pour les couleurs (`--color-zenfleet-primary`, etc.) dans `@theme`.
    - Définition des espacements personnalisés (`--spacing-sidebar`).
    - Définition des animations (`--animate-fade-in`).
- **Migration des Plugins** :
    - Réécriture des composants `.zenfleet-card`, `.zenfleet-btn` en CSS natif avec `@layer components`.

#### [MODIFY] [resources/views/layouts/app.blade.php](file:///wsl.localhost/Ubuntu-22.04/home/lynx/projects/zenfleet/resources/views/layouts/app.blade.php) (et autres layouts)
- S'assurer que la classe `dark` n'est jamais ajoutée au `html` ou `body`.
- Ajouter `<meta name="color-scheme" content="light">` si absent.

## Plan de Vérification

### 1. Vérification Technique (Build)
- Lancer `npm run build` et s'assurer qu'il n'y a pas d'erreurs.
- Vérifier la taille du bundle CSS généré.

### 2. Vérification Visuelle (Browser)
- Lancer `npm run dev`.
- Vérifier les éléments clés du design "ZenFleet" :
    - **Couleurs** : Les boutons primaires sont-ils bien `#0ea5e9` ?
    - **Sidebar** : La largeur est-elle bien de `280px` ?
    - **Cartes** : L'ombre `.zenfleet-card` est-elle appliquée ?
- **Dark Mode Check** : Modifier les préférences système en mode sombre et vérifier que l'interface RESTE en mode clair (fond blanc, texte sombre).

### 3. Commandes de Test
```bash
# Installation propre
npm install

# Build de production
npm run build

# Lancement dev
npm run dev
```
