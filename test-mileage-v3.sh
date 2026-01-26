#!/bin/bash

#=====================================================
# SCRIPT DE TEST - MODULE KILOMÉTRAGE V3.0
#=====================================================
# Tests automatiques des corrections appliquées
#=====================================================

echo ""
echo "========================================================"
echo "     TESTS MODULE KILOMÉTRAGE V3.0                      "
echo "========================================================"
echo ""

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
CYAN='\033[0;36m'
NC='\033[0m'

PASSED=0
FAILED=0

test_pass() {
    echo -e "${GREEN}[✓]${NC} $1"
    ((PASSED++))
}

test_fail() {
    echo -e "${RED}[✗]${NC} $1"
    ((FAILED++))
}

test_info() {
    echo -e "${CYAN}[TEST]${NC} $1"
}

echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  TEST 1: STRUCTURE BOUTON FILTRE${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo ""

test_info "Vérification x-data au bon niveau..."
if grep -q '<div class="mb-6" x-data="{ showFilters: false }">' resources/views/livewire/admin/mileage-readings-index.blade.php; then
    test_pass "x-data positionné au niveau de la section filtres"
else
    test_fail "x-data non trouvé au bon niveau"
fi

test_info "Vérification absence de x-cloak sur x-show..."
if ! grep -q 'x-show="showFilters".*x-cloak' resources/views/livewire/admin/mileage-readings-index.blade.php; then
    test_pass "x-cloak absent du div x-show (correct)"
else
    test_fail "x-cloak présent sur x-show (bloque affichage)"
fi

test_info "Vérification icône chevron animée..."
if grep -q 'heroicons:chevron-down' resources/views/livewire/admin/mileage-readings-index.blade.php; then
    test_pass "Icône chevron présente"
else
    test_fail "Icône chevron manquante"
fi

if grep -q "x-bind:class=\"showFilters ? 'rotate-180' : ''\"" resources/views/livewire/admin/mileage-readings-index.blade.php; then
    test_pass "Animation rotation chevron configurée"
else
    test_fail "Animation rotation manquante"
fi

test_info "Vérification badge compteur de filtres..."
if grep -q 'bg-blue-600 text-white' resources/views/livewire/admin/mileage-readings-index.blade.php | grep -q 'collect'; then
    test_pass "Badge compteur de filtres présent"
else
    test_fail "Badge compteur manquant"
fi

echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  TEST 2: COMPOSANTS FORMULAIRE${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo ""

test_info "Vérification utilisation des composants x-input..."
X_INPUT_COUNT=$(grep -c '<x-input' resources/views/livewire/admin/update-vehicle-mileage.blade.php)
if [ "$X_INPUT_COUNT" -ge 3 ]; then
    test_pass "Composants x-input utilisés ($X_INPUT_COUNT trouvés)"
else
    test_fail "Composants x-input insuffisants (seulement $X_INPUT_COUNT)"
fi

test_info "Vérification champ kilométrage avec wire:model.live..."
if grep -q 'wire:model.live="newMileage"' resources/views/livewire/admin/update-vehicle-mileage.blade.php; then
    test_pass "Binding kilométrage wire:model.live présent"
else
    test_fail "Binding kilométrage manquant"
fi

test_info "Vérification icônes dans les labels..."
if grep -q 'icon="gauge"' resources/views/livewire/admin/update-vehicle-mileage.blade.php; then
    test_pass "Icône gauge présente sur champ kilométrage"
else
    test_fail "Icône gauge manquante"
fi

test_info "Vérification layout responsive (grid 3 colonnes)..."
if grep -q 'grid-cols-1 lg:grid-cols-3' resources/views/livewire/admin/update-vehicle-mileage.blade.php; then
    test_pass "Layout responsive 3 colonnes configuré"
else
    test_fail "Layout responsive manquant"
fi

test_info "Vérification calcul différence kilométrique..."
if grep -q 'newMileage - \$selectedVehicle->current_mileage' resources/views/livewire/admin/update-vehicle-mileage.blade.php; then
    test_pass "Calcul différence kilométrique présent"
else
    test_fail "Calcul différence manquant"
fi

echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  TEST 3: LOGIQUE BACKEND${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo ""

test_info "Vérification forçage kilométrage dans updatedVehicleId..."
if grep -A 5 'updatedVehicleId' app/Livewire/Admin/UpdateVehicleMileage.php | grep -q 'Force le rafraîchissement'; then
    test_pass "Commentaire de forçage présent"
else
    test_fail "Commentaire de forçage manquant"
fi

if grep -A 7 'updatedVehicleId' app/Livewire/Admin/UpdateVehicleMileage.php | grep -q 'newMileage.*current_mileage'; then
    test_pass "Code de forçage du kilométrage présent"
else
    test_fail "Code de forçage manquant"
fi

test_info "Vérification méthode loadVehicle..."
if grep -q 'private function loadVehicle' app/Livewire/Admin/UpdateVehicleMileage.php; then
    test_pass "Méthode loadVehicle présente"
else
    test_fail "Méthode loadVehicle manquante"
fi

echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  TEST 4: VÉRIFICATIONS ASSETS ET CACHES${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo ""

test_info "Vérification des fichiers compilés..."
if [ -d "public/build" ] && [ -f "public/build/manifest.json" ]; then
    test_pass "Assets compilés présents (public/build)"
else
    test_fail "Assets compilés manquants"
fi

test_info "Vérification de la date de compilation..."
if [ -f "public/build/manifest.json" ]; then
    BUILD_DATE=$(stat -c %y public/build/manifest.json 2>/dev/null | cut -d' ' -f1)
    if [ ! -z "$BUILD_DATE" ]; then
        test_pass "Dernière compilation: $BUILD_DATE"
    fi
fi

test_info "Vérification backup formulaire v11..."
if [ -f "resources/views/livewire/admin/update-vehicle-mileage-backup-v11.blade.php" ]; then
    test_pass "Backup v11 du formulaire créé"
else
    test_fail "Backup v11 manquant"
fi

echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  TEST 5: STRUCTURE HTML ET CONFORMITÉ${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo ""

test_info "Vérification structure Alpine.js correcte..."
if grep -q 'x-data' resources/views/livewire/admin/mileage-readings-index.blade.php && \
   grep -q 'x-show' resources/views/livewire/admin/mileage-readings-index.blade.php; then
    test_pass "Structure Alpine.js présente"
else
    test_fail "Structure Alpine.js incomplète"
fi

test_info "Vérification transitions..."
if grep -q 'x-transition:enter' resources/views/livewire/admin/mileage-readings-index.blade.php; then
    test_pass "Transitions Alpine.js configurées"
else
    test_fail "Transitions manquantes"
fi

test_info "Vérification bouton submit avec disabled conditionnel..."
if grep -q '@if(!$selectedVehicle || !$newMileage) disabled @endif' resources/views/livewire/admin/update-vehicle-mileage.blade.php; then
    test_pass "Logique disabled du bouton submit présente"
else
    test_fail "Logique disabled manquante"
fi

echo ""
echo "========================================================"
echo "                  RÉSULTATS FINAUX                      "
echo "========================================================"
echo ""

TOTAL=$((PASSED + FAILED))
echo -e "${GREEN}Tests réussis:${NC} $PASSED / $TOTAL"
if [ $FAILED -gt 0 ]; then
    echo -e "${RED}Tests échoués:${NC} $FAILED / $TOTAL"
fi
echo ""

PERCENTAGE=$((PASSED * 100 / TOTAL))
echo -e "Score: ${CYAN}${PERCENTAGE}%${NC}"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}═══════════════════════════════════════════════════════${NC}"
    echo -e "${GREEN}  ✅ TOUS LES TESTS PASSENT - MODULE PRÊT!${NC}"
    echo -e "${GREEN}═══════════════════════════════════════════════════════${NC}"
    echo ""
    echo -e "${CYAN}Prochaines étapes:${NC}"
    echo "  1. Ouvrir le navigateur sur /admin/mileage-readings"
    echo "  2. Tester le bouton 'Filtres' (doit s'ouvrir/fermer)"
    echo "  3. Aller sur /admin/mileage-readings/update"
    echo "  4. Sélectionner un véhicule"
    echo "  5. Vérifier que le kilométrage actuel se charge"
    echo "  6. Modifier le kilométrage et soumettre"
    echo ""
    exit 0
else
    echo -e "${YELLOW}═══════════════════════════════════════════════════════${NC}"
    echo -e "${YELLOW}  ⚠️  CERTAINS TESTS ONT ÉCHOUÉ${NC}"
    echo -e "${YELLOW}═══════════════════════════════════════════════════════${NC}"
    echo ""
    exit 1
fi
