# Plan de Refactorisation Design Complet - ZenFleet

## 1) Objectif
Unifier visuellement toute l'application ZenFleet avec un design system unique, robuste et maintenable:
- mêmes tokens couleur/typographie/espacement,
- mêmes composants de formulaire,
- même mécanisme d'affichage d'erreurs,
- cohérence desktop/mobile et accessibilité.

## 2) Standards visuels cibles

### Couleurs de base
- Sidebar: `#f0f4fb`
- Fond page principale: `#f8fafc`
- Bleu principal actions: `#0c90ee`
- Hover principal: `#0a84da`
- Active principal: `#086fb7`

### Typographie (règles de base)
- Titre page: `text-xl font-bold text-gray-600`
- Indication sous titre / micro-guidance: `text-xs text-gray-600`
- Titre section formulaire: `text-sm font-semibold text-slate-600`
- Sous-titre section formulaire: `text-xs text-slate-400 mt-0.5`

### Formulaires
- Focus: bordure fine bleue + ring doux cohérent.
- Erreurs: bordure rouge + message explicite sous champ + message global unifié.
- Aides sous champ: format uniforme `text-xs text-gray-600`.

### Sections formulaire
- Icône section avec contour premium (double cercle esthétique).
- Rail vertical bleu plus épais, gradient léger, délimitant visuellement les sections.

## 3) Portée de la refonte

### Phase A - Fondations design system (en cours)
- Consolidation des variables CSS globales.
- Uniformisation des boutons primaires.
- Uniformisation des états focus.

### Phase B - Référence visuelle module Véhicules (en cours)
- Liste véhicules.
- Formulaire ajout véhicule.
- Validation de conformité visuelle avant propagation globale.

### Phase C - Propagation formulaires prioritaires
- Chauffeurs (create/edit/import).
- Affectations.
- Maintenance.
- Dépenses.
- Kilométrage.
- Demandes de réparation.

### Phase D - Pages de gestion et matrices
- Rôles & permissions.
- Organisation / utilisateurs.
- Dashboards module par module.

### Phase E - QA finale design + UX
- Vérification responsive.
- Vérification accessibilité (contraste, focus visible, navigation clavier).
- Vérification cohérence des erreurs sur tous les formulaires.

## 4) Gouvernance technique
- Ne pas dupliquer des styles inline quand un composant existe déjà.
- Préférer composants Blade partagés (`x-input`, `x-datepicker`, `x-form-section`, `x-button`).
- Toute nouvelle page doit respecter les tokens globaux.
- Toute exception visuelle doit être documentée.

## 5) Stratégie d'implémentation
1. Corriger d'abord les composants communs.
2. Appliquer module par module avec check-list visuelle.
3. Exécuter build front à chaque lot.
4. Vérifier absence de régression UI fonctionnelle.

## 6) Critères d'acceptation
- Aucune page avec styles contradictoires majeurs.
- Tous les formulaires: comportement erreur homogène.
- Titres/sous-titres aux classes standard.
- Boutons primaires cohérents sur toute l'app.
- Build Vite sans erreur.

## 7) Avancement actuel
- Base de tokens et boutons primaires: en place.
- Module Véhicules (liste + create): en cours d'alignement fin.
- Prochaine étape naturelle: validation visuelle de référence puis propagation à Chauffeurs.

