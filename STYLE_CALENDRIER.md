# STYLE ET DESIGN DU CALENDRIER - DOCUMENT DE RÉFÉRENCE

**Projet :** ZenFleet
**Module Analysé :** SANCTIONS chauffeurs (Filtres de date)
**Date de Référence :** 25 Décembre 2025
**Auteur :** Architecte Système

---

## 1. PHILOSOPHIE ET CONTEXTE TECHNIQUE

Le composant de sélection de date (calendrier) utilisé dans les filtres du module **SANCTIONS chauffeurs** est une implémentation **custom** (faite maison) et non un composant de librairie externe (comme `flatpickr` ou autre).

Cette approche garantit une **cohérence parfaite** avec le design système basé sur **Tailwind CSS** et offre un contrôle total sur l'accessibilité et le comportement réactif via **Alpine.js** et **Livewire 3**.

### 1.1. Stack Technologique

| Rôle | Technologie | Détails d'Implémentation |
| :--- | :--- | :--- |
| **Styling** | **Tailwind CSS** | Utilisation exclusive des classes utilitaires ZenFleet (couleurs `zenfleet-primary`, `zenfleet-blue-600`, etc.). |
| **Logique** | **Alpine.js** | Gestion de l'état (`showCalendar`, `selectedDate`), de la navigation (mois précédent/suivant) et du rendu des jours. |
| **Data Binding** | **Livewire 3** | Utilisation de `@entangle('dateFrom')` ou `@entangle('dateTo')` pour la synchronisation bidirectionnelle avec le backend. |

### 1.2. Format de Date

| Contexte | Format | Exemple |
| :--- | :--- | :--- |
| **Affichage Utilisateur** | `JJ/MM/AAAA` | `25/12/2025` |
| **Valeur Livewire (Backend)** | `AAAA-MM-JJ` | `2025-12-25` |

---

## 2. STYLE DU CHAMP DE SAISIE (INPUT FIELD)

Le champ de saisie est la porte d'entrée du calendrier. Il doit être immédiatement reconnaissable comme un sélecteur de date.

| Élément | Classes Tailwind CSS | Description |
| :--- | :--- | :--- |
| **Input Principal** | `w-full px-4 py-2.5 pl-11 bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg shadow-sm transition-all cursor-pointer` | Largeur pleine, padding vertical généreux (`py-2.5`), fond gris clair pour indiquer un champ non éditable directement, bordure standard, texte sombre, coins arrondis. |
| **Focus/Hover** | `focus:border-blue-500 focus:ring-1 focus:ring-blue-500 hover:border-gray-400` | Bordure bleue (`blue-500`) et anneau de focus subtil à l'activation. Bordure légèrement plus foncée au survol. |
| **Icône** | `absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none` | Icône `heroicons:calendar-days` positionnée à gauche, centrée verticalement, en couleur `text-gray-400`. |

---

## 3. STYLE DU CONTENEUR DU CALENDRIER (POPUP)

Le conteneur du calendrier est le panneau qui apparaît au clic. Son style est crucial pour l'expérience utilisateur.

| Propriété | Classes Tailwind CSS / Valeur | Description |
| :--- | :--- | :--- |
| **Positionnement** | `absolute z-50 mt-2` | Position absolue, au-dessus de tout (`z-50`), décalé de 2 unités vers le bas. |
| **Arrière-plan** | `bg-white` | Fond blanc pur. |
| **Coins** | `rounded-xl` | Coins très arrondis (plus que l'input) pour un look moderne. |
| **Ombre** | `shadow-2xl` | Ombre forte pour détacher le calendrier du reste de l'interface. |
| **Bordure** | `border border-gray-200` | Bordure très légère pour définir les limites. |
| **Padding** | `p-4` | Padding interne de 4 unités. |
| **Largeur** | `w-72` | Largeur fixe de 72 unités Tailwind (environ 288px) pour assurer un affichage propre de 7 colonnes. |

---

## 4. STYLE DE L'ENTÊTE (HEADER)

L'entête contient le mois/année et les boutons de navigation.

### 4.1. Navigation (Boutons Précédent/Suivant)

| Élément | Classes Tailwind CSS | Description |
| :--- | :--- | :--- |
| **Bouton** | `p-1 hover:bg-gray-100 rounded-lg` | Petit padding, effet de survol très subtil (gris très clair), coins arrondis. |
| **Icône** | `heroicons:chevron-left` / `heroicons:chevron-right` | Icônes de chevron standard, couleur `text-gray-600`. |

### 4.2. Affichage Mois/Année

| Élément | Classes Tailwind CSS | Description |
| :--- | :--- | :--- |
| **Texte** | `font-semibold text-gray-900` | Texte en gras (semi-bold), couleur très sombre pour une lisibilité maximale. |

---

## 5. STYLE DES JOURS DE LA SEMAINE (LU, MA, ME...)

Les en-têtes de colonnes sont statiques et définissent la grille.

| Élément | Classes Tailwind CSS | Description |
| :--- | :--- | :--- |
| **Conteneur** | `grid grid-cols-7 gap-1 mb-2` | Grille 7 colonnes avec un petit espacement, marge basse. |
| **Cellule** | `text-center text-xs font-semibold text-gray-500 py-1` | Texte centré, très petit (`text-xs`), semi-bold, couleur gris moyen (`gray-500`). |

---

## 6. STYLE DES CELLULES DE JOUR (DAY CELLS)

C'est l'élément le plus complexe, car il possède plusieurs états.

| État | Classes Tailwind CSS | Description |
| :--- | :--- | :--- |
| **Base (Non sélectionné)** | `w-8 h-8 flex items-center justify-center text-sm rounded-lg transition-colors` | Taille fixe 8x8 (carré), centrage du contenu, texte petit (`text-sm`), coins arrondis, transition douce des couleurs. |
| **Jour Normal** | `text-gray-700` | Couleur de texte par défaut. |
| **Survol (Hover)** | `hover:bg-gray-100` | Changement de fond très léger au survol. |
| **Jour Désactivé** | `text-gray-300 cursor-not-allowed` | Texte gris très clair, curseur désactivé. **Règle métier :** Les dates futures sont désactivées (`day.disabled`). |
| **Aujourd'hui (Today)** | `bg-blue-100 text-blue-800` | Fond bleu très clair, texte bleu foncé. **Indicateur visuel important.** |
| **Sélectionné (Selected)** | `bg-blue-600 text-white` | Utilisation de la couleur primaire de ZenFleet (`blue-600`), texte blanc. **Indicateur visuel le plus fort.** |

### 6.1. Règle d'État Prioritaire

Les états sont appliqués dans l'ordre de priorité suivant (du plus prioritaire au moins prioritaire) :

1.  **Sélectionné** (`bg-blue-600 text-white`)
2.  **Aujourd'hui** (`bg-blue-100 text-blue-800`) - *Note : L'état "Aujourd'hui" est écrasé par "Sélectionné" si le jour sélectionné est aujourd'hui.*
3.  **Désactivé** (`text-gray-300`)
4.  **Normal** (`text-gray-700`)

---

## 7. RECOMMANDATIONS POUR LA REPRODUCTION

Pour garantir une reproduction fidèle et standardisée du calendrier dans tous les modules de l'application :

1.  **Encapsulation du Composant :** Il est **fortement recommandé** de migrer la logique Alpine.js/HTML/Tailwind actuelle vers un **Blade Component** réutilisable (ex: `<x-date-picker wire:model="dateFilter" />`). Cela réduira la duplication de code et facilitera la maintenance.
2.  **Dépendance Alpine.js :** Assurez-vous que la version d'Alpine.js (v3.4.2+) est bien chargée et que les dépendances Livewire sont correctement configurées.
3.  **Cohérence des Couleurs :** Utiliser les variables de couleur définies dans `tailwind.config.js` (ex: `zenfleet-primary` ou `blue-600`) pour tous les états actifs et de focus.
4.  **Accessibilité (A11Y) :** Maintenir les attributs `aria-` et la gestion du focus au clavier si le composant est étendu, bien que l'implémentation actuelle se concentre sur l'interaction à la souris.
5.  **Localisation :** La liste des noms de mois (`monthNames`) et des jours de la semaine (`['Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di']`) est en **Français**. Toute nouvelle implémentation doit utiliser un mécanisme de traduction Laravel pour ces chaînes si l'application devient multilingue.

Ce document sert de spécification pour le design et le comportement du sélecteur de date. Tout nouveau sélecteur de date dans ZenFleet doit adhérer à ces spécifications de style.
