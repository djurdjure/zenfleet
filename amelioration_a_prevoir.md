# Améliorations à prévoir

## Sécurité & Accès Chauffeurs
- Forcer un changement de mot de passe à la première connexion (flag `must_change_password` + écran dédié).
- Remplacer l’affichage d’un mot de passe en clair par un lien d’activation sécurisé (token à usage unique + expiration).
- Envoyer automatiquement les identifiants / lien d’activation par email et SMS (configurable par organisation).

## Permissions & Rôles
- Refactoriser la page des permissions en matrice “CRUD” par rôle (cases à cocher Voir/Créer/Modifier/Supprimer) pour une gestion plus claire et rapide.
- Supprimer définitivement la logique “dual-read” (PermissionAliases + compatibilité legacy) après validation complète, et refactoriser le middleware pour n’accepter que les permissions canoniques.

## UX & Gouvernance des Données
- Ajouter un menu “Corbeille” global pour retrouver tous les éléments archivés (chauffeurs, véhicules, fournisseurs, etc.) avec options de restauration ou suppression définitive, traçabilité et filtres par module.

## Alertes - Pagination/Virtualisation du Centre d'exécution (priorité haute)
- Objectif métier: garder une page Alertes rapide et stable même avec un volume élevé (cible: 500 à 10 000 alertes agrégées sur 30 jours), sans freeze navigateur et sans surcharge Livewire.
- Contexte actuel: la page dispose déjà de filtres, tri, groupement et actions rapides; le rendu est fait côté Livewire avec collections en mémoire. Cette base est correcte pour un volume modéré, mais doit être industrialisée pour la montée en charge.
- Problème à prévenir: coût CPU/mémoire côté navigateur (DOM trop volumineux), payloads Livewire trop lourds, latence élevée des interactions (filtres/tri/groupement), risque de timeout en environnement multi-tenant avec données denses.

### Cibles de performance (SLO)
- Temps de première peinture utile de la zone “Centre d'exécution”: inférieur à 800 ms pour 1000 alertes en base.
- Temps de réponse d'une interaction filtre/tri/groupement: inférieur à 300 ms p95.
- Taille payload Livewire par interaction: inférieur à 150 KB.
- Nombre d'éléments DOM simultanés dans la liste: plafonné à 40-80 lignes visibles.
- Absence de freeze navigateur: aucune montée mémoire continue sur 5 minutes d'utilisation active.

### Stratégie technique retenue
- Étape 1 (immédiate): pagination serveur Livewire sur les “actionItems” (offset/page ou cursor) avec compteur total et navigation compacte.
- Étape 2 (optimisation): virtualisation d'affichage côté client (fenêtre visible + buffer) pour limiter le nombre de noeuds DOM rendus.
- Étape 3 (robustesse): agrégation SQL/Service orientée “query-first” pour éviter de construire des collections complètes en PHP quand non nécessaire.
- Étape 4 (résilience): fallback automatique “mode compact” au-delà d'un seuil volumétrique (ex: > 300 items filtrés).

### Implémentation détaillée prévue
- Service:
- Ajouter une méthode paginée dans `App\Services\AlertCenterService` qui retourne un DTO standardisé: `items`, `total`, `page`, `per_page`, `has_more`, `generated_at`.
- Éviter la matérialisation complète quand possible: filtrage/tri prioritairement en SQL pour maintenance/budget/repair, puis normalisation minimale.
- Cache court conservé (30s) mais segmenté par combinaison de filtres/tri/page pour éviter collisions.
- Livewire:
- Introduire des propriétés dédiées: `page`, `perPage`, `totalItems`, `compactMode`.
- Déplacer la logique de tri/groupement au plus près de la requête paginée.
- Réinitialiser `page=1` à chaque changement de filtre/tri/groupement.
- Conserver les actions rapides (`Filtrer`, `Ouvrir`, `Masquer`) compatibles pagination.
- UI:
- Ajouter un bandeau de statut: `N résultats`, `page X/Y`, `mode compact activé`.
- Pagination uniforme avec le design ZenFleet (même style que les autres listes).
- Si virtualisation activée: conteneur scrollable avec rendu fenêtré (ex: 50 items max simultanés) et skeleton loaders.
- Sécurité et multi-tenant:
- Revalider la contrainte `organization_id` et permissions `alerts.view` sur tous les chemins paginés.
- Aucun item ne doit pouvoir pointer vers une action hors périmètre tenant.

### Points d'architecture à trancher avant développement
- Choix pagination:
- Option A: `LengthAwarePaginator` standard (simple, robuste, UX claire).
- Option B: pagination curseur (plus performante à forte volumétrie, UX moins “numéro de page”).
- Choix virtualisation:
- Option A: virtualisation JS légère native (sans nouvelle dépendance).
- Option B: librairie dédiée (plus rapide à intégrer, dette dépendance supplémentaire).
- Recommandation actuelle:
- Démarrer par `Option A/A` pour livraison rapide et risque faible, puis évoluer vers curseur si la volumétrie réelle le justifie.

### Critères d'acceptation fonctionnels
- Le Centre d'exécution affiche les mêmes résultats métier qu'avant à filtres équivalents.
- Les interactions filtres/tri/groupement restent instantanées visuellement et sans rechargement complet de page.
- Les actions rapides continuent de fonctionner correctement sur tous les types d'alertes.
- Le compteur sidebar et la page Alertes restent cohérents (tolérance max 30s due au cache court).

### Critères d'acceptation techniques
- Tests de charge locale sur dataset volumineux (au moins 1000 alertes mixées) validés.
- Aucune erreur JS console ni erreur Laravel log lors de navigation intensive (filtres + pagination + rafraîchissements).
- Pas de régression sur routes existantes (`admin.alerts.*`) ni sur export.

### Plan de test recommandé
- Jeux de données:
- Cas faible (20 alertes), moyen (300), élevé (1000+), extrême (5000+ simulé).
- Scénarios:
- Changement rapide de filtres, bascule tri/date/type, groupement, masquage/réaffichage, refresh manuel.
- Contrôles:
- Mesurer latence p50/p95, taille payload, mémoire onglet navigateur, cohérence des compteurs.
- Non-régression:
- Vérifier que maintenance/budget/repair gardent leurs liens d'actions et permissions.

### Risques et mitigations
- Risque: incohérence temporaire due au cache.
- Mitigation: invalidation ciblée lors `refreshData`, TTL court, horodatage visible “Dernière mise à jour”.
- Risque: complexité excessive de virtualisation.
- Mitigation: livrer d'abord pagination serveur, puis virtualisation en itération contrôlée.
- Risque: divergence entre sidebar count et liste paginée filtrée.
- Mitigation: définir explicitement les règles de comptage (global actionable vs filtré courant).

### Découpage en lot de livraison
- Lot 1: pagination serveur + UX pagination + métriques simples (durée requête/log).
- Lot 2: mode compact auto + virtualisation DOM.
- Lot 3: optimisation SQL avancée + index DB si nécessaires.
- Lot 4: durcissement QA, tests end-to-end et documentation d'exploitation.

### Pré-requis et dépendances
- Vérifier index DB sur colonnes utilisées dans les filtres/tri d'alertes (dates, priorités, statuts, organization_id).
- Vérifier disponibilité des routes cibles actions rapides (`maintenance.operations.index`, `vehicle-expenses.index`, `repair-requests.*`, `vehicles.index`).
- Prévoir une fenêtre de validation QA dédiée multi-rôles (admin/superviseur/chauffeur selon visibilité).

### Journal de décision (mise à jour 2026-02-15)
- Décision validée: implémenter cette passe après stabilisation de la nouvelle vue Alertes Livewire.
- Niveau de priorité: élevée.
- Impact attendu: amélioration nette de la réactivité perçue, réduction du risque de surcharge UI, meilleure scalabilité du module Alertes.
