# 🎯 PROMPT D'IMPLÉMENTATION ZENFLEET ENTERPRISE DESIGN POUR CLAUDE CODE

## CONTEXTE TECHNIQUE PRÉCIS
Tu es un assistant d'implémentation pour le projet **ZenFleet** - une application Laravel 12 + Livewire 3 + PostgreSQL 16 conteneurisée avec Docker. Le projet utilise **YARN** (et non npm) pour la gestion des dépendances frontend. Toutes les commandes doivent être exécutées via Docker Compose.

## OBJECTIF
Implémenter le nouveau design Enterprise Ultra-Moderne en suivant les standards du projet et en utilisant les commandes Docker appropriées.

---

## 📋 ÉTAPES D'IMPLÉMENTATION AUTOMATIQUE

### PHASE 1: PRÉPARATION ET BACKUP [CRITIQUE]

```bash
# 1.1 - Créer un point de sauvegarde Git
git add .
git commit -m "🔒 Backup: Avant implémentation Enterprise Design v2.0"
git branch backup-enterprise-$(date +%Y%m%d_%H%M%S)

# 1.2 - Backup de la base de données PostgreSQL
docker compose exec db pg_dump -U zenfleet zenfleet_db > backup_db_enterprise_$(date +%Y%m%d_%H%M%S).sql

# 1.3 - Créer une archive des vues actuelles
tar -czf views_backup_$(date +%Y%m%d_%H%M%S).tar.gz resources/views/

# 1.4 - Vérifier l'état du projet
docker compose ps
docker compose exec php php artisan --version
```

### PHASE 2: INSTALLATION DES DÉPENDANCES FRONTEND [YARN UNIQUEMENT]

```bash
# 2.1 - Installer Alpine.js pour les interactions
docker compose exec -u zenfleet_user node yarn add alpinejs@^3.13

# 2.2 - Installer Chart.js pour les graphiques du dashboard
docker compose exec -u zenfleet_user node yarn add chart.js@^4.4

# 2.3 - Installer la font Inter pour le design moderne
docker compose exec -u zenfleet_user node yarn add @fontsource/inter

# 2.4 - Installer les outils de build en dépendances de développement
docker compose exec -u zenfleet_user node yarn add -D postcss@^8 autoprefixer@^10 cssnano@^6

# 2.5 - Vérifier l'installation
docker compose exec -u zenfleet_user node yarn list --pattern="alpinejs|chart.js|@fontsource/inter"
```

### PHASE 3: CONFIGURATION VITE ET TAILWIND

```javascript
// 3.1 - FICHIER: vite.config.js
// REMPLACER LE CONTENU EXISTANT PAR:

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/enterprise-design-system.css',
                'resources/js/app.js',
                'resources/js/admin/app.js'
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            '~': path.resolve(__dirname, './node_modules'),
        },
    },
    optimizeDeps: {
        include: ['alpinejs', 'chart.js']
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'alpine': ['alpinejs'],
                    'charts': ['chart.js'],
                }
            }
        }
    }
});
```

```javascript
// 3.2 - FICHIER: tailwind.config.js
// AJOUTER CES EXTENSIONS AU FICHIER EXISTANT:

module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./resources/views/components/**/*.blade.php",
        "./resources/views/livewire/**/*.blade.php",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont'],
            },
            colors: {
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                    950: '#172554',
                },
                success: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                },
                danger: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                },
                warning: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                },
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'slide-in-left': 'slideInLeft 0.3s ease-out',
                'scale-in': 'scaleIn 0.3s ease-out',
                'pulse-soft': 'pulseSoft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideInLeft: {
                    '0%': { transform: 'translateX(-100%)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.95)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                pulseSoft: {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.5' },
                },
            },
        },
    },
    plugins: [],
}
```

### PHASE 4: INTÉGRATION DU CSS ENTERPRISE

```css
/* 4.1 - FICHIER: resources/css/app.css */
/* AJOUTER AU DÉBUT DU FICHIER (NE PAS SUPPRIMER L'EXISTANT): */

/* Import du système de design Enterprise */
@import './enterprise-design-system.css';

/* Import de la font Inter */
@import '@fontsource/inter/300.css';
@import '@fontsource/inter/400.css';
@import '@fontsource/inter/500.css';
@import '@fontsource/inter/600.css';
@import '@fontsource/inter/700.css';
@import '@fontsource/inter/800.css';
@import '@fontsource/inter/900.css';

/* Smooth scrolling global */
html {
    scroll-behavior: smooth;
}

/* Font smoothing pour meilleur rendu */
body {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Reste du CSS existant... */
@tailwind base;
@tailwind components;
@tailwind utilities;
```

### PHASE 5: COMPILATION ET OPTIMISATION DES ASSETS

```bash
# 5.1 - Compiler les assets en mode production
docker compose exec -u zenfleet_user node yarn build

# 5.2 - Vérifier la compilation
docker compose exec -u zenfleet_user node ls -la public/build/assets/

# 5.3 - Nettoyer le cache Laravel
docker compose exec -u zenfleet_user php php artisan cache:clear
docker compose exec -u zenfleet_user php php artisan view:clear
docker compose exec -u zenfleet_user php php artisan config:clear
docker compose exec -u zenfleet_user php php artisan route:clear

# 5.4 - Optimiser pour production
docker compose exec -u zenfleet_user php php artisan config:cache
docker compose exec -u zenfleet_user php php artisan route:cache
docker compose exec -u zenfleet_user php php artisan view:cache
docker compose exec -u zenfleet_user php php artisan optimize
```

### PHASE 6: ACTIVATION DES NOUVELLES VUES

```bash
# 6.1 - Activer la nouvelle page de connexion
mv resources/views/auth/login.blade.php resources/views/auth/login.blade.php.old
mv resources/views/auth/login-enterprise.blade.php resources/views/auth/login.blade.php

# 6.2 - Activer le nouveau layout principal
mv resources/views/layouts/admin/catalyst.blade.php resources/views/layouts/admin/catalyst.blade.php.old
mv resources/views/layouts/admin/catalyst-enterprise.blade.php resources/views/layouts/admin/catalyst.blade.php

# 6.3 - Activer le nouveau dashboard
mv resources/views/admin/dashboard.blade.php resources/views/admin/dashboard.blade.php.old 2>/dev/null || true
mv resources/views/admin/dashboard-enterprise.blade.php resources/views/admin/dashboard.blade.php
```

### PHASE 7: MISE À JOUR DES CONTRÔLEURS

```php
// 7.1 - FICHIER: app/Http/Controllers/Admin/DashboardController.php
// AJOUTER CETTE MÉTHODE OU MODIFIER L'EXISTANTE:

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Préparer les statistiques pour le dashboard Enterprise
        $stats = [
            'active_vehicles' => Vehicle::where('status', 'active')->count(),
            'available_drivers' => Driver::whereHas('driverStatus', function($q) {
                $q->where('name', 'Disponible');
            })->count(),
            'pending_maintenances' => Maintenance::where('status', 'pending')->count(),
            'total_mileage' => Vehicle::sum('current_mileage') ?? 0,
        ];
        
        // Activités récentes (optionnel)
        $recentActivities = collect(); // Ou Activity::latest()->take(5)->get() si vous avez un modèle Activity
        
        // Salutation dynamique
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Bonjour' : ($hour < 18 ? 'Bon après-midi' : 'Bonsoir');
        
        return view('admin.dashboard', compact('stats', 'recentActivities', 'greeting'));
    }
}
```

### PHASE 8: ENREGISTREMENT DES COMPOSANTS BLADE

```php
// 8.1 - FICHIER: app/Providers/AppServiceProvider.php
// AJOUTER DANS LA MÉTHODE boot():

use Illuminate\Support\Facades\Blade;

public function boot(): void
{
    // Enregistrement des composants Enterprise si les fichiers existent
    if (file_exists(resource_path('views/components/enterprise/card.blade.php'))) {
        Blade::component('enterprise.card', 'enterprise.card');
        Blade::component('enterprise.button', 'enterprise.button');
        Blade::component('enterprise.modal', 'enterprise.modal');
        Blade::component('enterprise.toast', 'enterprise.toast');
        Blade::component('enterprise.input', 'enterprise.input');
        Blade::component('enterprise.filter-panel', 'enterprise.filter-panel');
    }
}
```

### PHASE 9: TESTS ET VALIDATION

```bash
# 9.1 - Vérifier que les fichiers Enterprise sont présents
ls -la resources/css/enterprise-design-system.css
ls -la resources/views/auth/login.blade.php
ls -la resources/views/layouts/admin/catalyst.blade.php
ls -la resources/views/admin/dashboard.blade.php
ls -la resources/views/components/enterprise/

# 9.2 - Tester la compilation en mode développement
docker compose exec -u zenfleet_user node yarn dev

# 9.3 - Vérifier les logs d'erreur
docker compose exec php tail -n 50 storage/logs/laravel.log

# 9.4 - Tester l'accès à l'application
curl -I http://localhost:8000/login
```

### PHASE 10: SCRIPT DE VALIDATION AUTOMATIQUE

```bash
# 10.1 - Créer et exécuter le script de validation
cat > validate_enterprise.sh << 'EOF'
#!/bin/bash
set -e

echo "🔍 Validation ZenFleet Enterprise Design..."

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

# Fonction de validation
check() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✓${NC} $2"
    else
        echo -e "${RED}✗${NC} $2"
        exit 1
    fi
}

# Tests
[ -f "resources/css/enterprise-design-system.css" ]
check $? "CSS Enterprise présent"

[ -f "resources/views/auth/login.blade.php" ]
check $? "Page login Enterprise active"

[ -f "resources/views/layouts/admin/catalyst.blade.php" ]
check $? "Layout Enterprise actif"

[ -d "public/build/assets" ]
check $? "Assets compilés"

docker compose exec php php artisan route:list | grep -q "login"
check $? "Routes configurées"

echo -e "\n${GREEN}✨ Validation réussie!${NC}"
echo "📌 URLs à tester:"
echo "   - Login: http://localhost:8000/login"
echo "   - Dashboard: http://localhost:8000/admin/dashboard"
EOF

chmod +x validate_enterprise.sh
./validate_enterprise.sh
```

---

## 🚨 COMMANDES DE ROLLBACK SI NÉCESSAIRE

```bash
# Restaurer les backups
git checkout backup-enterprise-*
mv resources/views/auth/login.blade.php.old resources/views/auth/login.blade.php
mv resources/views/layouts/admin/catalyst.blade.php.old resources/views/layouts/admin/catalyst.blade.php
mv resources/views/admin/dashboard.blade.php.old resources/views/admin/dashboard.blade.php

# Nettoyer et recompiler
docker compose exec -u zenfleet_user php php artisan optimize:clear
docker compose exec -u zenfleet_user node yarn build
```

---

## 📊 CHECKLIST FINALE DE VALIDATION

- [ ] ✅ Backup Git créé sur branche dédiée
- [ ] ✅ Dépendances installées via yarn (alpinejs, chart.js, @fontsource/inter)
- [ ] ✅ Vite configuré avec les nouveaux assets
- [ ] ✅ Tailwind étendu avec couleurs Enterprise
- [ ] ✅ CSS Enterprise importé dans app.css
- [ ] ✅ Assets compilés avec `yarn build`
- [ ] ✅ Cache Laravel nettoyé et optimisé
- [ ] ✅ Nouvelles vues activées (login, layout, dashboard)
- [ ] ✅ Contrôleur Dashboard mis à jour
- [ ] ✅ Composants Blade enregistrés
- [ ] ✅ Tests de validation passés
- [ ] ✅ Application accessible sans erreurs

---

## 🎯 RÉSULTAT ATTENDU

Après exécution complète de ce prompt:
1. **Page de connexion** : Design glassmorphism avec animations
2. **Dashboard** : Widgets animés avec graphiques Chart.js
3. **Navigation** : Sidebar moderne avec gradients
4. **Composants** : Système unifié de cards, buttons, modals
5. **Notifications** : Toast système intégré
6. **Performance** : Assets optimisés et cachés

---

## 💡 NOTES IMPORTANTES POUR CLAUDE CODE

1. **TOUJOURS** utiliser `yarn` et non `npm`
2. **TOUJOURS** exécuter via `docker compose exec`
3. **TOUJOURS** utiliser l'utilisateur `zenfleet_user` pour node
4. **VÉRIFIER** que Docker est lancé avec `docker compose ps`
5. **COMPILER** avec `yarn build` après chaque modification CSS/JS
6. **NETTOYER** le cache après modifications des vues

---

**FIN DU PROMPT D'IMPLÉMENTATION**

Ce prompt est conçu pour être copié-collé directement dans Claude Code pour une exécution automatique et séquentielle de toutes les commandes.
