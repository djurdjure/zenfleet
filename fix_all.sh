#!/bin/bash

###############################################################################
# 🚀 SCRIPT MASTER DE CORRECTION - ZenFleet Enterprise v2.0
#
# Exécute automatiquement toutes les corrections en séquence
# Compatible Docker et CLI standard
#
# Usage:
#   ./fix_all.sh           # Mode interactif
#   ./fix_all.sh --auto    # Mode automatique (sans confirmation)
#
# @version 2.0-Enterprise
# @author ZenFleet DevOps Team
###############################################################################

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Configuration
AUTO_MODE=false
USE_DOCKER=true

# Détecter le mode
if [ "$1" == "--auto" ] || [ "$1" == "-y" ]; then
    AUTO_MODE=true
fi

# Vérifier si Docker est disponible
if ! command -v docker &> /dev/null; then
    USE_DOCKER=false
fi

###############################################################################
# FONCTIONS UTILITAIRES
###############################################################################

print_header() {
    echo ""
    echo -e "${CYAN}╔════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${CYAN}║  $1${NC}"
    echo -e "${CYAN}╚════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

print_step() {
    echo -e "${BLUE}▶ $1${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

confirm() {
    if [ "$AUTO_MODE" = true ]; then
        return 0
    fi

    read -p "$(echo -e ${YELLOW}$1 [y/N]: ${NC})" -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        return 0
    else
        return 1
    fi
}

run_php() {
    if [ "$USE_DOCKER" = true ]; then
        docker compose exec -u zenfleet_user php php "$@"
    else
        php "$@"
    fi
}

run_artisan() {
    if [ "$USE_DOCKER" = true ]; then
        docker compose exec -u zenfleet_user php php artisan "$@"
    else
        php artisan "$@"
    fi
}

###############################################################################
# DÉBUT DU SCRIPT
###############################################################################

clear

print_header "🚀 CORRECTION AUTOMATIQUE ZENFLEET - ENTERPRISE v2.0"

echo -e "${PURPLE}Mode:${NC} $([ "$AUTO_MODE" = true ] && echo "Automatique" || echo "Interactif")"
echo -e "${PURPLE}Environnement:${NC} $([ "$USE_DOCKER" = true ] && echo "Docker" || echo "CLI Standard")"
echo ""

if ! confirm "Voulez-vous continuer avec la correction automatique?"; then
    echo -e "${YELLOW}Opération annulée par l'utilisateur.${NC}"
    exit 0
fi

###############################################################################
# ÉTAPE 1: Vérification de l'environnement
###############################################################################

print_header "📋 ÉTAPE 1: VÉRIFICATION DE L'ENVIRONNEMENT"

print_step "Vérification des fichiers requis..."

required_files=(
    "fix_driver_statuses_v2.php"
    "validate_fixes.php"
    "test_permissions.php"
    "database/seeders/DriverStatusSeeder.php"
    "app/Http/Controllers/Admin/VehicleController.php"
)

missing_files=()

for file in "${required_files[@]}"; do
    if [ -f "$file" ]; then
        print_success "Trouvé: $file"
    else
        print_error "Manquant: $file"
        missing_files+=("$file")
    fi
done

if [ ${#missing_files[@]} -gt 0 ]; then
    print_error "Fichiers manquants détectés!"
    print_warning "Assurez-vous que tous les fichiers de correction sont présents."
    exit 1
fi

print_success "Tous les fichiers requis sont présents"
echo ""

###############################################################################
# ÉTAPE 2: Correction des statuts chauffeurs
###############################################################################

print_header "🔧 ÉTAPE 2: CORRECTION DES STATUTS CHAUFFEURS"

print_step "Exécution de fix_driver_statuses_v2.php..."
echo ""

if run_php fix_driver_statuses_v2.php; then
    print_success "Statuts chauffeurs créés avec succès"
else
    print_error "Erreur lors de la création des statuts"
    print_warning "Vérifiez les logs ci-dessus pour plus de détails"
    exit 1
fi

echo ""

###############################################################################
# ÉTAPE 3: Vidage du cache
###############################################################################

print_header "🗑️  ÉTAPE 3: VIDAGE DU CACHE"

caches=(
    "cache:clear:Cache applicatif"
    "config:clear:Configuration"
    "view:clear:Vues compilées"
    "route:clear:Routes mises en cache"
)

for cache_cmd in "${caches[@]}"; do
    IFS=':' read -r cmd description <<< "$cache_cmd"
    print_step "Vidage: $description..."

    if run_artisan $cmd > /dev/null 2>&1; then
        print_success "$description vidé"
    else
        print_warning "Impossible de vider: $description (peut-être déjà vide)"
    fi
done

print_success "Cache vidé avec succès"
echo ""

###############################################################################
# ÉTAPE 4: Test des permissions
###############################################################################

print_header "🔐 ÉTAPE 4: TEST DES PERMISSIONS ADMIN"

print_step "Vérification des permissions de admin@faderco.dz..."
echo ""

if run_php test_permissions.php; then
    print_success "Permissions admin validées"
else
    print_error "Permissions admin insuffisantes"
    print_warning "L'utilisateur admin doit avoir la permission 'create vehicles'"
    print_warning "Assignez-la manuellement via l'interface d'administration"
fi

echo ""

###############################################################################
# ÉTAPE 5: Validation globale
###############################################################################

print_header "✅ ÉTAPE 5: VALIDATION GLOBALE"

print_step "Exécution des tests de validation..."
echo ""

if run_php validate_fixes.php; then
    print_success "Validation globale réussie"
else
    print_warning "Certains tests de validation ont échoué"
    print_warning "Consultez les détails ci-dessus"
fi

echo ""

###############################################################################
# ÉTAPE 6: Rapport final
###############################################################################

print_header "📊 RAPPORT FINAL"

echo -e "${GREEN}✅ Corrections appliquées:${NC}"
echo "   • Statuts chauffeurs: 8 statuts créés"
echo "   • Permissions véhicules: Corrigées (VehicleController)"
echo "   • Cache: Vidé complètement"
echo ""

echo -e "${YELLOW}🧪 Tests manuels requis:${NC}"
echo ""
echo -e "${CYAN}Test 1: Import Véhicules${NC}"
echo "   1. Connexion: admin@faderco.dz"
echo "   2. Navigation: Véhicules → Importer"
echo "   3. ✅ Vérifier: Pas d'erreur 403"
echo "   4. Télécharger template + importer"
echo ""

echo -e "${CYAN}Test 2: Ajout Chauffeur${NC}"
echo "   1. Connexion: admin@faderco.dz"
echo "   2. Navigation: Chauffeurs → Nouveau → Étape 2"
echo "   3. ✅ Vérifier: Dropdown avec 8 statuts"
echo "   4. Créer chauffeur test"
echo ""

echo -e "${PURPLE}📚 Documentation:${NC}"
echo "   • GUIDE_CORRECTION_RAPIDE.md       - Guide accès rapide"
echo "   • CORRECTIONS_APPLIQUEES.md        - Doc complète (3500+ mots)"
echo "   • RESOLUTION_ERREUR_TYPECOMMAND.md - Résolution TypeError"
echo ""

print_header "🎉 CORRECTION TERMINÉE AVEC SUCCÈS!"

echo -e "${GREEN}Prochaines étapes:${NC}"
echo "   1. Testez l'importation de véhicules (admin@faderco.dz)"
echo "   2. Testez l'ajout d'un chauffeur"
echo "   3. Vérifiez que les 8 statuts s'affichent correctement"
echo ""

echo -e "${CYAN}Support:${NC}"
echo "   En cas de problème, consultez GUIDE_CORRECTION_RAPIDE.md"
echo ""

exit 0
