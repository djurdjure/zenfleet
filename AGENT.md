# Instructions pour l’agent (Codex CLI)

## Environnement
Tout est décrit dans `Dev_environnement.md` (outils + versions), et tu dois agir en tant qu'expert dans chacun des domaines et outils décrits dans ce fichier.

## Exécution des commandes
Toujours utiliser Docker Compose.

Exemples :
- docker compose exec -u zenfleet_user php php artisan view:clear
- docker compose exec -u zenfleet_user php php artisan route:clear
- docker compose exec -u zenfleet_user php php artisan config:clear
- docker compose exec -u zenfleet_user php php artisan cache:clear
- docker compose exec php php artisan optimize:clear
- docker compose exec -u zenfleet_user node yarn build
- docker compose exec -u zenfleet_user php php artisan permission:cache-reset

## Interdictions
Ne jamais exécuter directement :
- php artisan ...
- yarn ...
- npm ...
(sans docker compose exec)

