#!/bin/bash

echo "========================================"
echo "🔍 VÉRIFICATION FINALE CORRECTIONS V14.0"
echo "========================================"
echo ""

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Vérifier que le fichier blade contient vehicleData
echo "1. Vérification du fichier blade Livewire..."
if grep -q "vehicleData" resources/views/livewire/admin/update-vehicle-mileage.blade.php; then
    COUNT=$(grep -o "vehicleData" resources/views/livewire/admin/update-vehicle-mileage.blade.php | wc -l)
    echo -e "   ${GREEN}✅ vehicleData trouvé ($COUNT occurrences)${NC}"
else
    echo -e "   ${RED}❌ vehicleData NON trouvé${NC}"
fi

# Vérifier selectedVehicle mais exclure les commentaires
if grep -v "{{--" resources/views/livewire/admin/update-vehicle-mileage.blade.php | grep -v "--}}" | grep -q "selectedVehicle"; then
    echo -e "   ${RED}❌ selectedVehicle encore présent dans le code (devrait être supprimé)${NC}"
else
    echo -e "   ${GREEN}✅ selectedVehicle supprimé (sauf commentaires)${NC}"
fi

if grep -q "Version 14.0" resources/views/livewire/admin/update-vehicle-mileage.blade.php; then
    echo -e "   ${GREEN}✅ Marqueur Version 14.0 présent${NC}"
else
    echo -e "   ${RED}❌ Marqueur Version 14.0 absent${NC}"
fi

echo ""

# 2. Vérifier que le controller contient vehicleData
echo "2. Vérification du controller Livewire..."
if grep -q "public ?array \$vehicleData" app/Livewire/Admin/UpdateVehicleMileage.php; then
    echo -e "   ${GREEN}✅ Propriété vehicleData array présente${NC}"
else
    echo -e "   ${RED}❌ Propriété vehicleData array absente${NC}"
fi

if grep -q "public ?Vehicle \$selectedVehicle" app/Livewire/Admin/UpdateVehicleMileage.php; then
    echo -e "   ${RED}❌ selectedVehicle encore présent (devrait être supprimé)${NC}"
else
    echo -e "   ${GREEN}✅ selectedVehicle supprimé${NC}"
fi

echo ""

# 3. Vérifier l'import DB
echo "3. Vérification import DB..."
if grep -q "use Illuminate\\\Support\\\Facades\\\DB;" app/Livewire/Admin/MileageReadingsIndex.php; then
    echo -e "   ${GREEN}✅ Import DB présent dans MileageReadingsIndex${NC}"
else
    echo -e "   ${RED}❌ Import DB absent${NC}"
fi

echo ""

# 4. Vérifier le cache
echo "4. Vérification du cache..."
VIEW_CACHE_COUNT=$(ls -1 storage/framework/views/*.php 2>/dev/null | wc -l)
if [ $VIEW_CACHE_COUNT -eq 0 ]; then
    echo -e "   ${GREEN}✅ Cache de vues vide ($VIEW_CACHE_COUNT fichiers)${NC}"
else
    echo -e "   ${YELLOW}⚠️  Cache de vues contient $VIEW_CACHE_COUNT fichiers${NC}"
    echo -e "   ${YELLOW}   Exécutez: docker compose exec php php artisan view:clear${NC}"
fi

echo ""

# 5. Dernière modification
echo "5. Dates de dernière modification..."
BLADE_DATE=$(stat -c %y "resources/views/livewire/admin/update-vehicle-mileage.blade.php" 2>/dev/null)
PHP_DATE=$(stat -c %y "app/Livewire/Admin/UpdateVehicleMileage.php" 2>/dev/null)

echo -e "   Blade:      $BLADE_DATE"
echo -e "   Controller: $PHP_DATE"

echo ""

# 6. Résumé
echo "========================================"
echo "📊 RÉSUMÉ"
echo "========================================"

CHECKS=0
PASSED=0

# Check 1: vehicleData in blade
if grep -q "vehicleData" resources/views/livewire/admin/update-vehicle-mileage.blade.php; then
    ((PASSED++))
fi
((CHECKS++))

# Check 2: no selectedVehicle in blade (exclude comments)
if ! grep -v "{{--" resources/views/livewire/admin/update-vehicle-mileage.blade.php | grep -v "--}}" | grep -q "selectedVehicle"; then
    ((PASSED++))
fi
((CHECKS++))

# Check 3: vehicleData in controller
if grep -q "public ?array \$vehicleData" app/Livewire/Admin/UpdateVehicleMileage.php; then
    ((PASSED++))
fi
((CHECKS++))

# Check 4: no selectedVehicle in controller
if ! grep -q "public ?Vehicle \$selectedVehicle" app/Livewire/Admin/UpdateVehicleMileage.php; then
    ((PASSED++))
fi
((CHECKS++))

# Check 5: DB import
if grep -q "use Illuminate\\\Support\\\Facades\\\DB;" app/Livewire/Admin/MileageReadingsIndex.php; then
    ((PASSED++))
fi
((CHECKS++))

echo ""
echo "   Tests réussis: $PASSED/$CHECKS"
echo ""

if [ $PASSED -eq $CHECKS ]; then
    echo -e "${GREEN}✅ TOUTES LES CORRECTIONS SONT APPLIQUÉES${NC}"
    echo -e "${GREEN}✅ LES FICHIERS SONT CORRECTS${NC}"
    echo ""
    echo -e "${YELLOW}🎯 PROCHAINE ÉTAPE:${NC}"
    echo "   1. Vider le cache du navigateur (Ctrl + Shift + Delete)"
    echo "   2. Accéder à http://localhost/admin/mileage-readings/update"
    echo "   3. Vérifier le badge vert 'Version 14.0 chargée'"
    echo "   4. Tester la sélection d'un véhicule"
else
    echo -e "${RED}❌ CERTAINES CORRECTIONS MANQUENT${NC}"
    echo "   Vérifiez les erreurs ci-dessus"
fi

echo ""
echo "========================================"
