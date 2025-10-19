#!/bin/bash

# ================================================================
# 🔧 SCRIPT DE CORRECTION AUTOMATIQUE - THÈME DARK ZENFLEET
# ================================================================
# Description: Automatise la désactivation du dark mode et le nettoyage
# Author: ZenFleet DevOps Team
# Date: 2025-10-19
# Version: 1.0
# ================================================================

set -e  # Exit on error

# Couleurs pour output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================================${NC}"
echo -e "${BLUE}🚀 ZENFLEET - CORRECTION DU THÈME DARK${NC}"
echo -e "${BLUE}================================================================${NC}"

# 1. Vérification du répertoire
echo -e "\n${YELLOW}📂 Vérification du répertoire...${NC}"
if [ ! -f "tailwind.config.js" ]; then
    echo -e "${RED}❌ Erreur: tailwind.config.js non trouvé. Êtes-vous dans le bon répertoire?${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Répertoire correct${NC}"

# 2. Backup des fichiers importants
echo -e "\n${YELLOW}💾 Création des backups...${NC}"
BACKUP_DIR="backups/theme-dark-fix-$(date +%Y%m%d-%H%M%S)"
mkdir -p $BACKUP_DIR

cp tailwind.config.js $BACKUP_DIR/
cp resources/css/admin/app.css $BACKUP_DIR/
cp -r resources/views/admin/vehicles $BACKUP_DIR/
echo -e "${GREEN}✅ Backups créés dans $BACKUP_DIR${NC}"

# 3. Modification de tailwind.config.js
echo -e "\n${YELLOW}⚙️ Désactivation du dark mode dans Tailwind...${NC}"

# Vérifier si darkMode est déjà présent
if grep -q "darkMode:" tailwind.config.js; then
    echo -e "${BLUE}ℹ️ Configuration darkMode existante détectée, mise à jour...${NC}"
    sed -i "s/darkMode: .*/darkMode: false,/" tailwind.config.js
else
    echo -e "${BLUE}ℹ️ Ajout de la configuration darkMode...${NC}"
    # Ajouter darkMode: false après export default {
    sed -i '/export default {/a\    darkMode: false,' tailwind.config.js
fi
echo -e "${GREEN}✅ Dark mode désactivé dans Tailwind${NC}"

# 4. Nettoyage des classes dark dans les vues
echo -e "\n${YELLOW}🧹 Nettoyage des classes dark: dans les vues...${NC}"

# Compter les occurrences avant
DARK_COUNT_BEFORE=$(grep -r "dark:" resources/views --include="*.blade.php" 2>/dev/null | wc -l || echo 0)
echo -e "${BLUE}ℹ️ Classes dark: trouvées: $DARK_COUNT_BEFORE${NC}"

if [ $DARK_COUNT_BEFORE -gt 0 ]; then
    # Supprimer les classes dark: dans tous les fichiers blade
    find resources/views -name "*.blade.php" -type f -exec sed -i 's/dark:[a-zA-Z0-9-]*//g' {} \;
    
    # Nettoyer les espaces multiples résultants
    find resources/views -name "*.blade.php" -type f -exec sed -i 's/  */ /g' {} \;
    
    DARK_COUNT_AFTER=$(grep -r "dark:" resources/views --include="*.blade.php" 2>/dev/null | wc -l || echo 0)
    echo -e "${GREEN}✅ Classes dark: supprimées: $(($DARK_COUNT_BEFORE - $DARK_COUNT_AFTER))${NC}"
fi

# 5. Nettoyage des styles .dark dans CSS
echo -e "\n${YELLOW}🎨 Nettoyage des styles .dark dans CSS...${NC}"

CSS_FILE="resources/css/admin/app.css"
if [ -f "$CSS_FILE" ]; then
    # Supprimer les blocs .dark
    sed -i '/.dark .*/,/^}/d' $CSS_FILE
    sed -i '/.dark .*/d' $CSS_FILE
    echo -e "${GREEN}✅ Styles .dark supprimés du CSS${NC}"
fi

# 6. Modification des couleurs de validation (vert -> bleu)
echo -e "\n${YELLOW}🎯 Modification des couleurs de validation...${NC}"

# Dans les fichiers de véhicules
for file in resources/views/admin/vehicles/create.blade.php resources/views/admin/vehicles/enterprise-edit.blade.php; do
    if [ -f "$file" ]; then
        # Remplacer green par blue pour les états validés
        sed -i "s/'border-green-500 !bg-green-50'/'border-blue-600 bg-white'/g" $file
        sed -i "s/'text-green-600'/'text-blue-600'/g" $file
        sed -i "s/'bg-green-500'/'bg-blue-500'/g" $file
        sed -i "s/text-green-600/text-blue-600/g" $file
        sed -i "s/lucide:check-circle-2/lucide:check/g" $file
        echo -e "${GREEN}✅ Couleurs modifiées dans $(basename $file)${NC}"
    fi
done

# 7. Suppression des !important dans les composants
echo -e "\n${YELLOW}🔧 Nettoyage des !important...${NC}"

PHP_FILE="app/View/Components/Input.php"
if [ -f "$PHP_FILE" ]; then
    sed -i 's/!bg-/bg-/g' $PHP_FILE
    sed -i 's/!border-/border-/g' $PHP_FILE
    echo -e "${GREEN}✅ !important supprimés des composants${NC}"
fi

# 8. Statistiques finales
echo -e "\n${BLUE}📊 STATISTIQUES DE NETTOYAGE${NC}"
echo -e "${BLUE}================================================================${NC}"

# Compter les occurrences restantes
REMAINING_DARK=$(grep -r "dark:" resources/views --include="*.blade.php" 2>/dev/null | wc -l || echo 0)
REMAINING_CSS_DARK=$(grep -c "\.dark" resources/css/admin/app.css 2>/dev/null || echo 0)
REMAINING_IMPORTANT=$(grep -r "!bg-\|!border-" app/View/Components --include="*.php" 2>/dev/null | wc -l || echo 0)

echo -e "Classes dark: restantes dans les vues: ${REMAINING_DARK}"
echo -e "Styles .dark restants dans CSS: ${REMAINING_CSS_DARK}"
echo -e "!important restants dans les composants: ${REMAINING_IMPORTANT}"

# 9. Instructions de finalisation
echo -e "\n${YELLOW}📝 ACTIONS MANUELLES REQUISES${NC}"
echo -e "${BLUE}================================================================${NC}"
echo -e "1. Recompiler les assets:"
echo -e "   ${GREEN}npm run build${NC}"
echo -e ""
echo -e "2. Vider le cache Laravel:"
echo -e "   ${GREEN}php artisan optimize:clear${NC}"
echo -e ""
echo -e "3. Tester l'application:"
echo -e "   - Activer le mode dark du navigateur"
echo -e "   - Vérifier /admin/vehicles/create"
echo -e "   - Vérifier /admin/vehicles/*/edit"
echo -e ""
echo -e "4. En cas de problème, restaurer depuis:"
echo -e "   ${GREEN}$BACKUP_DIR${NC}"

echo -e "\n${GREEN}✨ CORRECTION AUTOMATIQUE TERMINÉE!${NC}"
echo -e "${BLUE}================================================================${NC}"

# 10. Demander si on doit lancer la compilation
echo -e "\n${YELLOW}Voulez-vous lancer la compilation maintenant? (y/n)${NC}"
read -p "Réponse: " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo -e "\n${YELLOW}🔄 Compilation des assets...${NC}"
    npm run build
    echo -e "${GREEN}✅ Compilation terminée${NC}"
    
    echo -e "\n${YELLOW}🔄 Nettoyage du cache...${NC}"
    php artisan optimize:clear
    echo -e "${GREEN}✅ Cache nettoyé${NC}"
fi

echo -e "\n${GREEN}🎉 Script terminé avec succès!${NC}"
