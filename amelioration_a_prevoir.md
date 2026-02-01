# Améliorations à prévoir

## Sécurité & Accès Chauffeurs
- Forcer un changement de mot de passe à la première connexion (flag `must_change_password` + écran dédié).
- Remplacer l’affichage d’un mot de passe en clair par un lien d’activation sécurisé (token à usage unique + expiration).
- Envoyer automatiquement les identifiants / lien d’activation par email et SMS (configurable par organisation).

## Permissions & Rôles
- Refactoriser la page des permissions en matrice “CRUD” par rôle (cases à cocher Voir/Créer/Modifier/Supprimer) pour une gestion plus claire et rapide.
