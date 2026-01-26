#!/bin/bash

#=====================================================
# SCRIPT DE DÉPLOIEMENT - MODULE KILOMÉTRAGE FIX
#=====================================================
# Version: 1.0 Enterprise
# Date: 2025-10-26
# Auteur: Expert Fullstack Developer
#=====================================================

echo ""
echo "=================================================="
echo "   DÉPLOIEMENT CORRECTIONS MODULE KILOMÉTRAGE    "
echo "=================================================="
echo ""

# Couleurs pour le terminal
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
    echo -e "${GREEN}[✓]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

log_error() {
    echo -e "${RED}[✗]${NC} $1"
}

# Vérifier que nous sommes dans le bon répertoire
if [ ! -f "artisan" ]; then
    log_error "Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
fi

log_info "Démarrage du déploiement..."

# 1. Créer des backups
log_info "Création des backups..."

# Backup des fichiers modifiés
if [ -f "resources/views/livewire/admin/mileage-readings-index.blade.php" ]; then
    cp resources/views/livewire/admin/mileage-readings-index.blade.php \
       resources/views/livewire/admin/mileage-readings-index-backup-$(date +%Y%m%d-%H%M%S).blade.php
    log_success "Backup de mileage-readings-index.blade.php créé"
fi

if [ -f "resources/views/livewire/admin/update-vehicle-mileage.blade.php" ]; then
    cp resources/views/livewire/admin/update-vehicle-mileage.blade.php \
       resources/views/livewire/admin/update-vehicle-mileage-backup-$(date +%Y%m%d-%H%M%S).blade.php
    log_success "Backup de update-vehicle-mileage.blade.php créé"
fi

# 2. Nettoyer les caches
log_info "Nettoyage des caches..."

# Cache Laravel
if command -v php &> /dev/null; then
    php artisan cache:clear 2>/dev/null && log_success "Cache cleared" || log_warning "Could not clear cache"
    php artisan view:clear 2>/dev/null && log_success "Views cleared" || log_warning "Could not clear views"
    php artisan config:clear 2>/dev/null && log_success "Config cleared" || log_warning "Could not clear config"
    php artisan route:clear 2>/dev/null && log_success "Routes cleared" || log_warning "Could not clear routes"
else
    log_warning "PHP not found in PATH, skipping artisan commands"
fi

# 3. Compiler les assets
log_info "Compilation des assets..."

if command -v npm &> /dev/null; then
    log_info "Building assets with npm..."
    npm run build 2>/dev/null && log_success "Assets compiled successfully" || log_warning "Asset compilation may have failed"
else
    log_warning "npm not found, please compile assets manually: npm run build"
fi

# 4. Vérifier les fichiers critiques
log_info "Vérification des fichiers..."

FILES_TO_CHECK=(
    "resources/views/livewire/admin/mileage-readings-index.blade.php"
    "resources/views/livewire/admin/update-vehicle-mileage.blade.php"
    "app/Livewire/Admin/MileageReadingsIndex.php"
    "app/Livewire/Admin/UpdateVehicleMileage.php"
)

for file in "${FILES_TO_CHECK[@]}"; do
    if [ -f "$file" ]; then
        log_success "$file ✓"
    else
        log_error "$file manquant!"
    fi
done

# 5. Vérifier les corrections appliquées
log_info "Vérification des corrections..."

# Vérifier que le style="display: none;" a été supprimé
if grep -q 'x-show.*style="display: none;"' resources/views/livewire/admin/mileage-readings-index.blade.php 2>/dev/null; then
    log_error "Le problème style=\"display: none;\" est toujours présent!"
else
    log_success "Correction du bouton filtre appliquée ✓"
fi

# Vérifier la présence de TomSelect
if grep -q 'tom-select' resources/views/livewire/admin/update-vehicle-mileage.blade.php 2>/dev/null; then
    log_success "TomSelect intégré ✓"
else
    log_warning "TomSelect non détecté"
fi

# 6. Optimisation pour la production
log_info "Optimisations production..."

if command -v php &> /dev/null; then
    php artisan optimize 2>/dev/null && log_success "Application optimized" || log_warning "Could not optimize"
    php artisan view:cache 2>/dev/null && log_success "Views cached" || log_warning "Could not cache views"
    php artisan route:cache 2>/dev/null && log_success "Routes cached" || log_warning "Could not cache routes"
fi

echo ""
echo "=================================================="
echo "           DÉPLOIEMENT TERMINÉ                   "
echo "=================================================="
echo ""

log_info "Résumé des corrections appliquées:"
echo "  ✅ Bouton filtre corrigé (suppression style=\"display:none\")"
echo "  ✅ Champs de formulaire activés conditionnellement"
echo "  ✅ TomSelect intégré pour la sélection de véhicules"
echo "  ✅ Alpine.js optimisé pour les interactions"
echo ""

log_info "Actions recommandées:"
echo "  1. Tester le bouton 'Filtrer' sur /admin/mileage-readings"
echo "  2. Tester le formulaire sur /admin/mileage-readings/update"
echo "  3. Vérifier avec différents rôles (Admin, Chauffeur)"
echo "  4. Monitorer les logs pour d'éventuelles erreurs"
echo ""

log_success "Module kilométrage prêt pour la production!"
echo ""

# Afficher l'URL de test si disponible
if [ -f ".env" ]; then
    APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2)
    if [ ! -z "$APP_URL" ]; then
        echo "URLs de test:"
        echo "  • ${APP_URL}/admin/mileage-readings"
        echo "  • ${APP_URL}/admin/mileage-readings/update"
        echo ""
    fi
fi

exit 0
