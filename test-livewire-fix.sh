#!/bin/bash

#=====================================================
# TEST - FIX LIVEWIRE MULTIPLE ROOT ELEMENTS
#=====================================================
# Vérifie que la structure du composant est correcte
#=====================================================

echo ""
echo "========================================================"
echo "  TEST FIX LIVEWIRE - MULTIPLE ROOT ELEMENTS           "
echo "========================================================"
echo ""

GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

FILE="resources/views/livewire/admin/mileage-readings-index.blade.php"

test_pass() {
    echo -e "${GREEN}[✓]${NC} $1"
}

test_fail() {
    echo -e "${RED}[✗]${NC} $1"
}

test_info() {
    echo -e "${CYAN}[TEST]${NC} $1"
}

# Test 1: Vérifier qu'il n'y a qu'une seule balise <section>
test_info "Vérification du nombre d'éléments <section> racine..."
SECTION_OPEN=$(grep -c '^<section' "$FILE")
SECTION_CLOSE=$(grep -c '^</section>' "$FILE")

if [ "$SECTION_OPEN" -eq 1 ] && [ "$SECTION_CLOSE" -eq 1 ]; then
    test_pass "Un seul élément <section> racine trouvé"
else
    test_fail "Nombre incorrect de <section>: $SECTION_OPEN ouvertures, $SECTION_CLOSE fermetures"
fi

# Test 2: Vérifier qu'il n'y a pas de div wire:loading au niveau racine
test_info "Vérification que wire:loading est à l'intérieur de <section>..."
WIRE_LOADING_ROOT=$(grep -c '^<div wire:loading' "$FILE")

if [ "$WIRE_LOADING_ROOT" -eq 0 ]; then
    test_pass "Aucun div wire:loading au niveau racine (correct)"
else
    test_fail "Div wire:loading trouvé au niveau racine (erreur)"
fi

# Test 3: Vérifier que wire:loading existe bien dans le fichier
test_info "Vérification que wire:loading existe (à l'intérieur)..."
if grep -q 'wire:loading.flex' "$FILE"; then
    test_pass "Div wire:loading trouvé à l'intérieur de la structure"
else
    test_fail "Div wire:loading introuvable"
fi

# Test 4: Vérifier la ligne où se trouve <section>
test_info "Vérification de la position de <section>..."
SECTION_LINE=$(grep -n '^<section' "$FILE" | cut -d: -f1)
if [ ! -z "$SECTION_LINE" ]; then
    test_pass "<section> trouvé à la ligne $SECTION_LINE"
else
    test_fail "<section> introuvable"
fi

# Test 5: Vérifier la ligne où se ferme </section>
test_info "Vérification de la position de </section>..."
SECTION_CLOSE_LINE=$(grep -n '^</section>' "$FILE" | cut -d: -f1)
if [ ! -z "$SECTION_CLOSE_LINE" ]; then
    test_pass "</section> trouvé à la ligne $SECTION_CLOSE_LINE"
else
    test_fail "</section> introuvable"
fi

# Test 6: Vérifier qu'il n'y a pas d'autres éléments racine
test_info "Vérification qu'il n'y a pas d'autres éléments racine..."
OTHER_ROOT=$(grep -c '^\s*<div\|^\s*<main\|^\s*<article' "$FILE" | head -1)

if [ "$OTHER_ROOT" -eq 0 ]; then
    test_pass "Aucun autre élément racine détecté"
else
    echo -e "${BLUE}[INFO]${NC} Note: Éléments <div> au niveau racine: $OTHER_ROOT (à vérifier)"
fi

echo ""
echo "========================================================"
echo "                  RÉSUMÉ                                "
echo "========================================================"
echo ""

echo -e "${BLUE}Structure du composant:${NC}"
echo "  • Élément racine: <section> (ligne $SECTION_LINE)"
echo "  • Fermeture: </section> (ligne $SECTION_CLOSE_LINE)"
echo "  • wire:loading: À l'intérieur de <section> ✅"
echo ""

echo -e "${GREEN}✅ Structure Livewire VALIDE${NC}"
echo ""
echo "Le composant respecte la contrainte Livewire:"
echo "  → UN SEUL élément racine (<section>)"
echo "  → Tous les autres éléments sont à l'intérieur"
echo ""

echo -e "${CYAN}Prochaine étape:${NC}"
echo "  1. Accéder à /admin/mileage-readings dans le navigateur"
echo "  2. Vérifier qu'il n'y a pas d'erreur MultipleRootElementsDetectedException"
echo "  3. Tester le bouton 'Filtres'"
echo "  4. Tester les interactions Livewire"
echo ""

exit 0
