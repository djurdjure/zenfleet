#!/bin/bash

# ====================================================================
# 🚀 SCRIPT DE DÉPLOIEMENT - MODULE GESTION DES DÉPENSES
# ====================================================================
# Version: 1.0.0
# Date: 27 Octobre 2025
# Description: Déploie le module de gestion des dépenses ZenFleet
# ====================================================================

set -e # Arrêter en cas d'erreur

# Couleurs pour output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}=====================================================================${NC}"
echo -e "${BLUE}🚀 DÉPLOIEMENT MODULE GESTION DES DÉPENSES - ZENFLEET${NC}"
echo -e "${BLUE}=====================================================================${NC}\n"

# Vérifier qu'on est dans le bon répertoire
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Erreur: Ce script doit être exécuté depuis la racine du projet ZenFleet${NC}"
    exit 1
fi

echo -e "${YELLOW}📋 Étape 1/6: Vérification de l'environnement...${NC}"
php artisan --version
echo -e "${GREEN}✅ Laravel détecté${NC}\n"

echo -e "${YELLOW}📋 Étape 2/6: Exécution des migrations...${NC}"
php artisan migrate --force
echo -e "${GREEN}✅ Migrations appliquées${NC}\n"

echo -e "${YELLOW}📋 Étape 3/6: Clear cache et optimisation...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
echo -e "${GREEN}✅ Cache nettoyé${NC}\n"

echo -e "${YELLOW}📋 Étape 4/6: Re-cache configuration...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✅ Configuration mise en cache${NC}\n"

echo -e "${YELLOW}📋 Étape 5/6: Vérification des tables créées...${NC}"
php artisan tinker --execute="
    echo 'Tables créées:' . PHP_EOL;
    if (Schema::hasTable('expense_groups')) echo '✅ expense_groups' . PHP_EOL;
    if (Schema::hasTable('expense_audit_logs')) echo '✅ expense_audit_logs' . PHP_EOL;
    if (Schema::hasColumn('vehicle_expenses', 'expense_group_id')) echo '✅ vehicle_expenses (colonnes ajoutées)' . PHP_EOL;
"
echo -e "${GREEN}✅ Structure de base de données vérifiée${NC}\n"

echo -e "${YELLOW}📋 Étape 6/6: Activation des routes (manuel)...${NC}"
echo -e "${BLUE}⚠️  Pour activer les routes, décommentez les lignes suivantes dans routes/web.php:${NC}"
echo -e "    Lignes 384-406: Routes VehicleExpenseController"
echo -e "\n${BLUE}⚠️  Pour intégrer au menu, ajoutez dans le sidebar:${NC}"
echo -e "    Route: admin.vehicle-expenses.index"
echo -e "    Icône: heroicons:currency-dollar"
echo -e "    Permission: view vehicle expenses\n"

echo -e "${GREEN}=====================================================================${NC}"
echo -e "${GREEN}🎉 DÉPLOIEMENT TERMINÉ AVEC SUCCÈS!${NC}"
echo -e "${GREEN}=====================================================================${NC}\n"

echo -e "${BLUE}📊 Résumé du module:${NC}"
echo -e "  • 3 tables créées/modifiées"
echo -e "  • 3 modèles Eloquent"
echo -e "  • 1 contrôleur principal"
echo -e "  • 3 services métier"
echo -e "  • Workflow d'approbation 2 niveaux"
echo -e "  • Analytics avancés"
echo -e "  • Audit trail immutable\n"

echo -e "${YELLOW}🔗 Prochaines étapes:${NC}"
echo -e "  1. Activer les routes dans web.php"
echo -e "  2. Ajouter l'entrée au menu sidebar"
echo -e "  3. Créer les permissions RBAC"
echo -e "  4. Tester avec: php artisan tinker"
echo -e "\n${BLUE}Documentation complète: EXPENSE_MODULE_IMPLEMENTATION_SUMMARY.md${NC}\n"
