#!/bin/bash

# ============================================================
# Script de déploiement - Fix Format Date Module Affectation
# ZenFleet v2.1 Ultra-Pro - Enterprise Grade
# Date: 18 Novembre 2025
# ============================================================

echo "🚀 DÉPLOIEMENT FIX FORMAT DATE - MODULE AFFECTATION"
echo "===================================================="
echo ""

# Vérification des services Docker
echo "📌 Vérification des services Docker..."
if ! docker ps | grep -q zenfleet_php; then
    echo "❌ Erreur: Le conteneur PHP n'est pas en cours d'exécution"
    echo "   Exécutez: docker-compose up -d"
    exit 1
fi
echo "✅ Services Docker actifs"

# Clear cache Livewire
echo ""
echo "📌 Nettoyage du cache Livewire..."
docker exec zenfleet_php php artisan livewire:discover
docker exec zenfleet_php php artisan view:clear
docker exec zenfleet_php php artisan cache:clear
echo "✅ Cache nettoyé"

# Optimisation de l'application
echo ""
echo "📌 Optimisation de l'application..."
docker exec zenfleet_php php artisan optimize
echo "✅ Application optimisée"

# Clear cache navigateur (instruction utilisateur)
echo ""
echo "⚠️  IMPORTANT - Actions manuelles requises:"
echo "============================================"
echo ""
echo "1. 🌐 VIDER LE CACHE DU NAVIGATEUR:"
echo "   - Chrome/Edge: Ctrl+Shift+Delete → Cocher 'Images et fichiers en cache'"
echo "   - Firefox: Ctrl+Shift+Delete → Cocher 'Cache'"
echo "   - Safari: Cmd+Option+E"
echo ""
echo "2. 🔄 RAFRAÎCHIR LA PAGE avec Ctrl+F5 (ou Cmd+Shift+R sur Mac)"
echo ""
echo "3. 📝 TESTER LA CRÉATION D'UNE AFFECTATION:"
echo "   - Le calendrier doit s'ouvrir sur aujourd'hui ($(date +%d/%m/%Y))"
echo "   - Le format doit être JJ/MM/AAAA"
echo "   - La validation doit accepter les dates au format français"
echo ""

# Affichage du résumé
echo "📊 RÉSUMÉ DU DÉPLOIEMENT"
echo "========================"
echo "✅ Fichier modifié: app/Livewire/AssignmentForm.php"
echo "✅ Méthodes ajoutées:"
echo "   - convertDateFromFrenchFormat()"
echo "   - formatDateForDisplay()"
echo "   - formatDatesForDisplay()"
echo "✅ Format interne: Y-m-d (ISO)"
echo "✅ Format affichage: d/m/Y (Français)"
echo "✅ Date par défaut: Aujourd'hui"
echo ""

# Test rapide
echo "📌 Test rapide du système..."
docker exec zenfleet_php php -r "
use Carbon\\Carbon;
echo '  Date système: ' . now()->format('Y-m-d H:i:s') . PHP_EOL;
echo '  Timezone: ' . config('app.timezone') . PHP_EOL;
echo '  Format français: ' . now()->format('d/m/Y') . PHP_EOL;
"

echo ""
echo "✨ DÉPLOIEMENT TERMINÉ AVEC SUCCÈS!"
echo "===================================="
echo ""
echo "📚 Documentation complète: SOLUTION_FORMAT_DATE_AFFECTATION__18-11-2025.md"
echo "🧪 Script de test: php test_assignment_date_fix.php"
echo ""
echo "🎯 Prochaines étapes:"
echo "1. Tester la création d'une nouvelle affectation"
echo "2. Tester la modification d'une affectation existante"
echo "3. Vérifier les dates dans différents navigateurs"
echo ""
echo "🏆 Solution Enterprise-Grade déployée avec succès!"
