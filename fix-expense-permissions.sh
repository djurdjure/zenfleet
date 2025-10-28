#!/bin/bash

# ====================================================================
# 💰 SCRIPT FIX PERMISSIONS DÉPENSES - ENTERPRISE GRADE
# ====================================================================
# 
# Script pour corriger les permissions du module de dépenses
# Exécuter avec: ./fix-expense-permissions.sh
# 
# @version 1.0.0-Enterprise
# @since 2025-10-28
# ====================================================================

echo "================================================================================";
echo "💰 FIX PERMISSIONS MODULE DÉPENSES - ENTERPRISE GRADE";
echo "================================================================================";
echo "";

# Exécuter la migration des permissions
echo "📋 Exécution de la migration des permissions...";
docker exec -it zenfleet-app php artisan migrate --path=database/migrations/2025_10_28_000001_add_expense_permissions.php

# Vider le cache
echo "";
echo "🧹 Nettoyage du cache...";
docker exec -it zenfleet-app php artisan cache:clear
docker exec -it zenfleet-app php artisan config:clear
docker exec -it zenfleet-app php artisan permission:cache-reset

# Exécuter le script PHP de fix
echo "";
echo "🔧 Exécution du script de correction...";
docker exec -it zenfleet-app php fix_expense_permissions.php

echo "";
echo "================================================================================";
echo "✅ PERMISSIONS CONFIGURÉES!";
echo "================================================================================";
echo "";
echo "📌 Testez maintenant l'accès à: http://localhost/admin/vehicle-expenses";
echo "";
echo "💡 Si l'erreur persiste:";
echo "   1. Déconnectez-vous et reconnectez-vous";
echo "   2. Assurez-vous que votre utilisateur a un des rôles suivants:";
echo "      - Super Admin";
echo "      - Admin"; 
echo "      - Finance";
echo "      - Gestionnaire Flotte";
echo "";
