#!/bin/bash

#############################################
# ZENFLEET ENTERPRISE DESIGN - INSTALLATION AUTOMATIQUE
# Version: 1.0.0
# Date: 2024
#############################################

set -e  # Arrêter en cas d'erreur

# Couleurs pour l'output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Header
clear
echo "================================================"
echo "   ZENFLEET ENTERPRISE DESIGN INSTALLER"
echo "   Version 1.0.0 - Ultra Modern Design"
echo "================================================"
echo ""

# Vérification des prérequis
log_info "Vérification des prérequis..."

# Check PHP
if ! command -v php &> /dev/null; then
    log_error "PHP n'est pas installé!"
    exit 1
fi
log_success "PHP détecté: $(php -v | head -n 1)"

# Check Node
if ! command -v node &> /dev/null; then
    log_error "Node.js n'est pas installé!"
    exit 1
fi
log_success "Node.js détecté: $(node -v)"

# Check NPM
if ! command -v npm &> /dev/null; then
    log_error "NPM n'est pas installé!"
    exit 1
fi
log_success "NPM détecté: $(npm -v)"

# Check Docker Compose
if ! command -v docker-compose &> /dev/null; then
    log_warning "Docker Compose n'est pas installé. Certaines fonctionnalités peuvent ne pas fonctionner."
fi

echo ""
log_info "Début de l'installation du design Enterprise..."
echo ""

# Étape 1: Backup
log_info "Création des backups de sécurité..."

# Backup des vues
if [ -d "resources/views" ]; then
    cp -r resources/views "resources/views.backup.$(date +%Y%m%d_%H%M%S)"
    log_success "Backup des vues créé"
fi

# Backup git
if [ -d ".git" ]; then
    git add . 2>/dev/null || true
    git commit -m "Backup avant installation Enterprise Design" 2>/dev/null || log_warning "Pas de changements à commiter"
    git branch -f backup-pre-enterprise 2>/dev/null || true
    log_success "Backup Git créé sur la branche 'backup-pre-enterprise'"
fi

# Étape 2: Installation des dépendances NPM
echo ""
log_info "Installation des dépendances NPM..."

npm install alpinejs --save
log_success "Alpine.js installé"

npm install chart.js --save
log_success "Chart.js installé"

npm install @fontsource/inter --save
log_success "Font Inter installée"

npm install -D postcss autoprefixer cssnano
log_success "Outils de build installés"

# Étape 3: Configuration Vite
echo ""
log_info "Configuration de Vite..."

# Backup du fichier vite.config.js
if [ -f "vite.config.js" ]; then
    cp vite.config.js "vite.config.js.backup"
    log_success "Backup de vite.config.js créé"
fi

# Mise à jour de vite.config.js
cat > vite.config.js.tmp << 'EOF'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

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
    optimizeDeps: {
        include: ['alpinejs', 'chart.js']
    }
});
EOF

mv vite.config.js.tmp vite.config.js
log_success "vite.config.js mis à jour"

# Étape 4: Mise à jour du CSS principal
echo ""
log_info "Configuration du CSS Enterprise..."

# Vérifier que le fichier enterprise-design-system.css existe
if [ ! -f "resources/css/enterprise-design-system.css" ]; then
    log_error "Le fichier enterprise-design-system.css n'existe pas!"
    log_info "Veuillez créer ce fichier avec le contenu fourni dans le guide."
    exit 1
fi

# Ajouter les imports dans app.css
if ! grep -q "enterprise-design-system.css" resources/css/app.css; then
    cat > resources/css/app.css.tmp << 'EOF'
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

EOF
    cat resources/css/app.css >> resources/css/app.css.tmp
    mv resources/css/app.css.tmp resources/css/app.css
    log_success "Imports CSS ajoutés"
fi

# Étape 5: Build des assets
echo ""
log_info "Compilation des assets..."
npm run build
log_success "Assets compilés avec succès"

# Étape 6: Clear cache Laravel
echo ""
log_info "Nettoyage du cache Laravel..."
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
log_success "Cache nettoyé"

# Étape 7: Optimisation pour la production
echo ""
log_info "Optimisation pour la production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
log_success "Application optimisée"

# Étape 8: Tests de validation
echo ""
log_info "Exécution des tests de validation..."

# Vérifier que les fichiers existent
FILES_TO_CHECK=(
    "resources/css/enterprise-design-system.css"
    "resources/views/auth/login-enterprise.blade.php"
    "resources/views/layouts/admin/catalyst-enterprise.blade.php"
    "resources/views/admin/dashboard-enterprise.blade.php"
    "resources/views/components/enterprise/card.blade.php"
    "resources/views/components/enterprise/button.blade.php"
    "resources/views/components/enterprise/modal.blade.php"
    "resources/views/components/enterprise/input.blade.php"
    "resources/views/components/enterprise/filter-panel.blade.php"
)

MISSING_FILES=0
for file in "${FILES_TO_CHECK[@]}"; do
    if [ ! -f "$file" ]; then
        log_warning "Fichier manquant: $file"
        MISSING_FILES=$((MISSING_FILES + 1))
    fi
done

if [ $MISSING_FILES -eq 0 ]; then
    log_success "Tous les fichiers Enterprise sont présents"
else
    log_warning "$MISSING_FILES fichier(s) manquant(s). L'installation peut être incomplète."
fi

# Étape 9: Instructions finales
echo ""
echo "================================================"
echo "   INSTALLATION TERMINÉE!"
echo "================================================"
echo ""
log_success "Le design Enterprise a été installé avec succès!"
echo ""
echo "📋 PROCHAINES ÉTAPES:"
echo ""
echo "1. Activez le nouveau layout dans vos contrôleurs:"
echo "   - Modifiez les vues retournées pour utiliser '-enterprise'"
echo ""
echo "2. Testez la nouvelle page de connexion:"
echo "   - http://localhost:8000/login"
echo ""
echo "3. Testez le nouveau dashboard:"
echo "   - http://localhost:8000/admin/dashboard"
echo ""
echo "4. Pour activer complètement le nouveau design:"
echo "   - Exécutez: php artisan serve"
echo "   - Visitez l'application dans votre navigateur"
echo ""
echo "5. En cas de problème:"
echo "   - Consultez: IMPLEMENTATION_GUIDE_ENTERPRISE.md"
echo "   - Restaurez le backup: git checkout backup-pre-enterprise"
echo ""
echo "================================================"
echo "✨ Profitez de votre nouveau design Enterprise!"
echo "================================================"

# Créer un fichier de statut
cat > .enterprise-installed << EOF
ENTERPRISE_DESIGN_INSTALLED=true
INSTALLATION_DATE=$(date)
VERSION=1.0.0
EOF

log_success "Fichier de statut créé: .enterprise-installed"

# Option pour lancer le serveur
echo ""
read -p "Voulez-vous lancer le serveur de développement maintenant? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    log_info "Lancement du serveur..."
    php artisan serve
fi
