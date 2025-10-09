#!/bin/bash

#############################################
# ZENFLEET ENTERPRISE DESIGN - INSTALLATION DOCKER/YARN
# Version: 2.0.0
# Optimisé pour: Docker Compose + Yarn
#############################################

set -e  # Arrêter en cas d'erreur

# Couleurs pour l'output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
MAGENTA='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color
BOLD='\033[1m'

# Configuration
DOCKER_USER="zenfleet_user"
BACKUP_DIR="backups/enterprise_$(date +%Y%m%d_%H%M%S)"

# Fonction pour afficher les messages
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[✓]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[⚠]${NC} $1"
}

log_error() {
    echo -e "${RED}[✗]${NC} $1"
}

log_step() {
    echo -e "\n${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${CYAN}${BOLD}$1${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"
}

# Fonction pour vérifier les prérequis
check_prerequisites() {
    log_step "ÉTAPE 1: VÉRIFICATION DES PRÉREQUIS"
    
    # Vérifier Docker
    if ! command -v docker &> /dev/null; then
        log_error "Docker n'est pas installé!"
        exit 1
    fi
    log_success "Docker détecté: $(docker --version | cut -d' ' -f3)"
    
    # Vérifier Docker Compose
    if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
        log_error "Docker Compose n'est pas installé!"
        exit 1
    fi
    log_success "Docker Compose détecté"
    
    # Vérifier que les conteneurs sont lancés
    if ! docker compose ps | grep -q "Up"; then
        log_warning "Les conteneurs Docker ne sont pas lancés"
        read -p "Voulez-vous les démarrer maintenant? (y/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            log_info "Démarrage des conteneurs..."
            docker compose up -d
            sleep 10
        else
            log_error "Installation annulée. Veuillez lancer: docker compose up -d"
            exit 1
        fi
    fi
    log_success "Conteneurs Docker actifs"
    
    # Vérifier l'accès aux conteneurs
    if ! docker compose exec php php --version &> /dev/null; then
        log_error "Impossible d'accéder au conteneur PHP"
        exit 1
    fi
    log_success "Conteneur PHP accessible"
    
    if ! docker compose exec node node --version &> /dev/null; then
        log_error "Impossible d'accéder au conteneur Node"
        exit 1
    fi
    log_success "Conteneur Node accessible"
    
    # Vérifier que yarn est installé dans le conteneur
    if ! docker compose exec -u $DOCKER_USER node yarn --version &> /dev/null; then
        log_error "Yarn n'est pas installé dans le conteneur Node"
        exit 1
    fi
    log_success "Yarn détecté: $(docker compose exec -u $DOCKER_USER node yarn --version)"
}

# Fonction pour créer les backups
create_backups() {
    log_step "ÉTAPE 2: CRÉATION DES BACKUPS"
    
    # Créer le répertoire de backup
    mkdir -p "$BACKUP_DIR"
    log_info "Répertoire de backup créé: $BACKUP_DIR"
    
    # Backup Git
    if [ -d ".git" ]; then
        git add . 2>/dev/null || true
        git commit -m "🔒 Backup automatique avant Enterprise Design" 2>/dev/null || log_warning "Pas de changements à commiter"
        git branch -f backup-enterprise-auto 2>/dev/null || true
        log_success "Backup Git créé (branche: backup-enterprise-auto)"
    fi
    
    # Backup des vues
    if [ -d "resources/views" ]; then
        tar -czf "$BACKUP_DIR/views.tar.gz" resources/views/
        log_success "Backup des vues créé"
    fi
    
    # Backup de la base de données
    log_info "Création du backup de base de données..."
    docker compose exec db pg_dump -U zenfleet zenfleet_db > "$BACKUP_DIR/database.sql" 2>/dev/null || log_warning "Backup DB échoué (non critique)"
    
    # Backup des configs
    cp vite.config.js "$BACKUP_DIR/vite.config.js.bak" 2>/dev/null || true
    cp tailwind.config.js "$BACKUP_DIR/tailwind.config.js.bak" 2>/dev/null || true
    cp resources/css/app.css "$BACKUP_DIR/app.css.bak" 2>/dev/null || true
    log_success "Backups de configuration créés"
}

# Fonction pour installer les dépendances
install_dependencies() {
    log_step "ÉTAPE 3: INSTALLATION DES DÉPENDANCES YARN"
    
    log_info "Installation d'Alpine.js..."
    docker compose exec -u $DOCKER_USER node yarn add alpinejs@^3.13
    log_success "Alpine.js installé"
    
    log_info "Installation de Chart.js..."
    docker compose exec -u $DOCKER_USER node yarn add chart.js@^4.4
    log_success "Chart.js installé"
    
    log_info "Installation de la font Inter..."
    docker compose exec -u $DOCKER_USER node yarn add @fontsource/inter
    log_success "Font Inter installée"
    
    log_info "Installation des outils de build..."
    docker compose exec -u $DOCKER_USER node yarn add -D postcss@^8 autoprefixer@^10 cssnano@^6
    log_success "Outils de build installés"
    
    # Vérification
    log_info "Vérification des installations..."
    docker compose exec -u $DOCKER_USER node yarn list --pattern="alpinejs|chart.js|@fontsource/inter" | head -20
}

# Fonction pour configurer Vite
configure_vite() {
    log_step "ÉTAPE 4: CONFIGURATION DE VITE"
    
    # Vérifier si enterprise-design-system.css existe
    if [ ! -f "resources/css/enterprise-design-system.css" ]; then
        log_error "Le fichier enterprise-design-system.css n'existe pas!"
        log_warning "Veuillez créer ce fichier avant de continuer"
        exit 1
    fi
    
    # Mise à jour de vite.config.js
    cat > vite.config.js << 'EOF'
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
EOF
    log_success "vite.config.js configuré"
}

# Fonction pour mettre à jour le CSS
update_css() {
    log_step "ÉTAPE 5: CONFIGURATION DU CSS"
    
    # Ajouter les imports au début de app.css si pas déjà présents
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

/* Smooth scrolling global */
html {
    scroll-behavior: smooth;
}

/* Font smoothing pour meilleur rendu */
body {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

EOF
        cat resources/css/app.css >> resources/css/app.css.tmp
        mv resources/css/app.css.tmp resources/css/app.css
        log_success "Imports CSS ajoutés"
    else
        log_info "Imports CSS déjà présents"
    fi
}

# Fonction pour compiler les assets
build_assets() {
    log_step "ÉTAPE 6: COMPILATION DES ASSETS"
    
    log_info "Compilation des assets en mode production..."
    docker compose exec -u $DOCKER_USER node yarn build
    log_success "Assets compilés avec succès"
    
    # Vérifier la compilation
    if [ -d "public/build/assets" ]; then
        log_success "Répertoire build créé"
        ls -la public/build/assets/ | head -5
    else
        log_warning "Répertoire build non trouvé"
    fi
}

# Fonction pour nettoyer le cache Laravel
clear_cache() {
    log_step "ÉTAPE 7: NETTOYAGE ET OPTIMISATION"
    
    log_info "Nettoyage du cache Laravel..."
    docker compose exec -u $DOCKER_USER php php artisan cache:clear
    docker compose exec -u $DOCKER_USER php php artisan view:clear
    docker compose exec -u $DOCKER_USER php php artisan config:clear
    docker compose exec -u $DOCKER_USER php php artisan route:clear
    log_success "Cache nettoyé"
    
    log_info "Optimisation pour production..."
    docker compose exec -u $DOCKER_USER php php artisan config:cache
    docker compose exec -u $DOCKER_USER php php artisan route:cache
    docker compose exec -u $DOCKER_USER php php artisan view:cache
    docker compose exec -u $DOCKER_USER php php artisan optimize
    log_success "Application optimisée"
}

# Fonction pour activer les nouvelles vues
activate_views() {
    log_step "ÉTAPE 8: ACTIVATION DES VUES ENTERPRISE"
    
    # Page de login
    if [ -f "resources/views/auth/login-enterprise.blade.php" ]; then
        if [ -f "resources/views/auth/login.blade.php" ]; then
            mv resources/views/auth/login.blade.php resources/views/auth/login.blade.php.old
        fi
        mv resources/views/auth/login-enterprise.blade.php resources/views/auth/login.blade.php
        log_success "Page de login Enterprise activée"
    else
        log_warning "Page login-enterprise.blade.php non trouvée"
    fi
    
    # Layout principal
    if [ -f "resources/views/layouts/admin/catalyst-enterprise.blade.php" ]; then
        if [ -f "resources/views/layouts/admin/catalyst.blade.php" ]; then
            mv resources/views/layouts/admin/catalyst.blade.php resources/views/layouts/admin/catalyst.blade.php.old
        fi
        mv resources/views/layouts/admin/catalyst-enterprise.blade.php resources/views/layouts/admin/catalyst.blade.php
        log_success "Layout Enterprise activé"
    else
        log_warning "Layout catalyst-enterprise.blade.php non trouvé"
    fi
    
    # Dashboard
    if [ -f "resources/views/admin/dashboard-enterprise.blade.php" ]; then
        if [ -f "resources/views/admin/dashboard.blade.php" ]; then
            mv resources/views/admin/dashboard.blade.php resources/views/admin/dashboard.blade.php.old
        fi
        mv resources/views/admin/dashboard-enterprise.blade.php resources/views/admin/dashboard.blade.php
        log_success "Dashboard Enterprise activé"
    else
        log_warning "Dashboard enterprise non trouvé"
    fi
}

# Fonction de validation finale
validate_installation() {
    log_step "ÉTAPE 9: VALIDATION DE L'INSTALLATION"
    
    ERRORS=0
    
    # Vérifier les fichiers critiques
    FILES_TO_CHECK=(
        "resources/css/enterprise-design-system.css"
        "resources/views/auth/login.blade.php"
        "resources/views/layouts/admin/catalyst.blade.php"
        "public/build/manifest.json"
    )
    
    for file in "${FILES_TO_CHECK[@]}"; do
        if [ -f "$file" ]; then
            log_success "✓ $file"
        else
            log_error "✗ $file manquant"
            ERRORS=$((ERRORS + 1))
        fi
    done
    
    # Test des routes
    if docker compose exec php php artisan route:list | grep -q "login" 2>/dev/null; then
        log_success "Routes configurées"
    else
        log_warning "Impossible de vérifier les routes"
    fi
    
    if [ $ERRORS -eq 0 ]; then
        log_success "Validation complète réussie!"
    else
        log_warning "$ERRORS erreur(s) détectée(s)"
    fi
}

# Fonction principale
main() {
    clear
    echo -e "${CYAN}${BOLD}"
    echo "╔══════════════════════════════════════════════════════╗"
    echo "║                                                      ║"
    echo "║     ZENFLEET ENTERPRISE DESIGN INSTALLER v2.0       ║"
    echo "║           Optimisé pour Docker + Yarn               ║"
    echo "║                                                      ║"
    echo "╚══════════════════════════════════════════════════════╝"
    echo -e "${NC}\n"
    
    # Confirmation
    echo -e "${YELLOW}Cette installation va:${NC}"
    echo "  • Installer les dépendances via Yarn"
    echo "  • Configurer Vite et Tailwind"
    echo "  • Activer le design Enterprise"
    echo "  • Compiler et optimiser les assets"
    echo ""
    read -p "Continuer l'installation? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        log_warning "Installation annulée"
        exit 0
    fi
    
    # Exécution des étapes
    check_prerequisites
    create_backups
    install_dependencies
    configure_vite
    update_css
    build_assets
    clear_cache
    activate_views
    validate_installation
    
    # Résumé final
    echo -e "\n${GREEN}${BOLD}"
    echo "╔══════════════════════════════════════════════════════╗"
    echo "║                                                      ║"
    echo "║        🎉 INSTALLATION TERMINÉE AVEC SUCCÈS!        ║"
    echo "║                                                      ║"
    echo "╚══════════════════════════════════════════════════════╝"
    echo -e "${NC}\n"
    
    echo -e "${CYAN}📋 PROCHAINES ÉTAPES:${NC}"
    echo ""
    echo "1. Testez la nouvelle page de connexion:"
    echo -e "   ${BLUE}http://localhost:8000/login${NC}"
    echo ""
    echo "2. Connectez-vous avec:"
    echo -e "   Email: ${GREEN}admin@zenfleet.dz${NC}"
    echo -e "   Mot de passe: ${GREEN}admin123${NC}"
    echo ""
    echo "3. Explorez le nouveau dashboard:"
    echo -e "   ${BLUE}http://localhost:8000/admin/dashboard${NC}"
    echo ""
    echo "4. Pour le mode développement avec hot-reload:"
    echo -e "   ${YELLOW}docker compose exec -u $DOCKER_USER node yarn dev${NC}"
    echo ""
    echo "5. En cas de problème, restaurez avec:"
    echo -e "   ${RED}git checkout backup-enterprise-auto${NC}"
    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${GREEN}✨ Profitez de votre nouveau design Enterprise!${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    
    # Créer un fichier de statut
    cat > .enterprise-installed << EOF
ENTERPRISE_DESIGN_INSTALLED=true
INSTALLATION_DATE=$(date)
VERSION=2.0.0
INSTALLER=Docker+Yarn
BACKUP_DIR=$BACKUP_DIR
EOF
    
    # Option pour lancer le mode dev
    echo ""
    read -p "Voulez-vous lancer le serveur en mode développement? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        log_info "Lancement du serveur de développement..."
        echo -e "${YELLOW}Appuyez sur Ctrl+C pour arrêter${NC}"
        docker compose exec -u $DOCKER_USER node yarn dev
    fi
}

# Gestion des erreurs
trap 'log_error "Une erreur est survenue. Installation interrompue."; exit 1' ERR

# Lancer le script principal
main "$@"
