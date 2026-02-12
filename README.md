# ZenFleet

ZenFleet est une application SaaS de gestion de flotte multi-tenant construite avec Laravel + Livewire + PostgreSQL/PostGIS, déployée en environnement Docker.

## Objectif

Ce README décrit l'etat technique reel du projet pour faciliter:
- le demarrage local
- la maintenance
- l'onboarding developpeur
- les decisions d'architecture

## Stack Technique (etat actuel)

### Runtime et orchestration

- `Docker`: `29.1.5`
- `Docker Compose`: `v5.0.2`
- `Host`: Linux WSL2 (`6.6.87.1-microsoft-standard-WSL2`)

### Backend

- `PHP`: `8.3.25` (`php:8.3-fpm-alpine`)
- `Laravel`: `12.28.1`
- `Livewire`: `^3.0`
- `Redis`: `7.4.5`
- `PostgreSQL`: `18.0` avec `postgis/postgis:18-3.6-alpine`

### Frontend

- `Node.js`: `v20.19.4`
- `Yarn`: `1.22.22`
- `Vite`: `^6.0.0`
- `Tailwind`: `^4.0.0` + `@tailwindcss/vite`
- `Alpine.js`: `^3.14.3`
- UI libs installees:
  - `slim-select`
  - `flatpickr`
  - `flowbite-datepicker`
  - `sortablejs`
  - `apexcharts`

## Architecture Docker

Le `docker-compose.yml` declare les services suivants:
- `php`: application Laravel (FPM + Supervisor)
- `nginx`: point d'entree HTTP
- `database`: PostgreSQL 18 + PostGIS avec tuning performance
- `redis`: cache / queue
- `node`: build frontend (`yarn`, `vite`)
- `scheduler`: execution continue de `php artisan schedule:run`
- `pdf-service`: microservice Node dedie a la generation PDF

Volumes externes utilises:
- `zenfleet_postgres_data`
- `zenfleet_redis_data`

## Commandes Standard

### Demarrage / arret

```bash
docker compose up -d --build
docker compose down
```

### Backend (Laravel)

```bash
docker compose exec -u zenfleet_user php php artisan --version
docker compose exec -u zenfleet_user php php artisan migrate
docker compose exec -u zenfleet_user php php artisan optimize:clear
```

### Frontend (Vite)

```bash
docker compose exec -u zenfleet_user node yarn install
docker compose exec -u zenfleet_user node yarn dev
docker compose exec -u zenfleet_user node yarn build
```

### Cache et permissions

```bash
docker compose exec -u zenfleet_user php php artisan view:clear
docker compose exec -u zenfleet_user php php artisan route:clear
docker compose exec -u zenfleet_user php php artisan config:clear
docker compose exec -u zenfleet_user php php artisan cache:clear
docker compose exec -u zenfleet_user php php artisan permission:cache-reset
```

## Validation rapide de l'environnement

```bash
docker compose exec -u zenfleet_user php php -v
docker compose exec -u zenfleet_user node node -v
docker compose exec database psql --version
docker compose exec redis redis-server --version
```

## Etat actuel des graphiques

Constat technique dans le code:
- `ApexCharts` est installe via `npm/yarn` et bundle dans Vite (`manualChunks.charts`)
- certaines vues utilisent encore des scripts CDN (`Chart.js` ou `ApexCharts`) directement en Blade

Conclusion:
- l'application dispose deja d'une base graphique exploitable
- mais la strategie n'est pas encore unifiee (mix bundle npm + CDN)

Un plan de convergence enterprise est documente dans `recommandation_graph.md`.

## Ecarts documentaires identifies

- `Dev_environnement.md` contient des versions historiques qui ne correspondent plus partout a l'etat runtime actuel.
- `.env.example` est encore calibre en MySQL alors que la stack projet execute PostgreSQL.

## Recommandation immediate

Pour limiter les regressions en production:
1. garder `ApexCharts` comme standard court terme
2. supprimer progressivement les scripts CDN des vues Blade
3. unifier la couche graphique via Vite + modules JS dedies

