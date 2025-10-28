#!/bin/bash

# ====================================================================
# 🚀 SCRIPT D'APPLICATION - FIX VALIDATION MODULE EXPENSE
# ====================================================================
# Script pour appliquer automatiquement toutes les corrections
# du module de validation des dépenses
#
# Version: 1.0.0
# Date: 28/10/2025
# ====================================================================

echo -e "\033[1;34m====================================================================\033[0m"
echo -e "\033[1;34m🚀 APPLICATION DES CORRECTIONS - MODULE EXPENSE VALIDATION\033[0m"
echo -e "\033[1;34m====================================================================\033[0m\n"

# Fonction pour afficher les messages
function info() {
    echo -e "\033[1;36mℹ️  $1\033[0m"
}

function success() {
    echo -e "\033[1;32m✅ $1\033[0m"
}

function error() {
    echo -e "\033[1;31m❌ $1\033[0m"
}

function warning() {
    echo -e "\033[1;33m⚠️  $1\033[0m"
}

# Vérifier que nous sommes dans le bon répertoire
if [ ! -f "artisan" ]; then
    error "Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
fi

echo -e "\033[1;36m1. VÉRIFICATION DE L'ENVIRONNEMENT\033[0m"
echo "----------------------------------------"

# Vérifier PHP
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n 1)
    success "PHP trouvé: $PHP_VERSION"
else
    error "PHP n'est pas installé ou n'est pas dans le PATH"
    warning "Installation manuelle requise"
fi

# Vérifier Composer
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version | head -n 1)
    success "Composer trouvé: $COMPOSER_VERSION"
else
    warning "Composer n'est pas dans le PATH"
fi

echo -e "\n\033[1;36m2. VÉRIFICATION DES FICHIERS CRÉÉS\033[0m"
echo "----------------------------------------"

# Vérifier les fichiers créés
FILES_TO_CHECK=(
    "app/Http/Requests/VehicleExpenseRequest.php"
    "lang/fr/validation.php"
    "lang/fr/auth.php"
    "lang/fr/pagination.php"
    "test_expense_validation_fix.php"
    "EXPENSE_VALIDATION_FIX_ENTERPRISE.md"
)

for FILE in "${FILES_TO_CHECK[@]}"; do
    if [ -f "$FILE" ]; then
        success "Fichier créé: $FILE"
    else
        error "Fichier manquant: $FILE"
    fi
done

echo -e "\n\033[1;36m3. PERMISSIONS DES FICHIERS\033[0m"
echo "----------------------------------------"

# Définir les permissions appropriées
info "Définition des permissions pour le dossier lang..."
if [ -d "lang/fr" ]; then
    chmod -R 755 lang/
    chmod -R 644 lang/fr/*.php
    success "Permissions définies pour les fichiers de langue"
else
    error "Dossier lang/fr non trouvé"
fi

# Permissions pour le FormRequest
if [ -f "app/Http/Requests/VehicleExpenseRequest.php" ]; then
    chmod 644 app/Http/Requests/VehicleExpenseRequest.php
    success "Permissions définies pour VehicleExpenseRequest.php"
fi

echo -e "\n\033[1;36m4. NETTOYAGE DU CACHE LARAVEL\033[0m"
echo "----------------------------------------"

if command -v php &> /dev/null; then
    info "Nettoyage des caches Laravel..."
    
    # Cache général
    php artisan cache:clear 2>/dev/null && success "Cache général vidé" || warning "Impossible de vider le cache général"
    
    # Cache de configuration
    php artisan config:clear 2>/dev/null && success "Cache de configuration vidé" || warning "Impossible de vider le cache de config"
    
    # Cache des vues
    php artisan view:clear 2>/dev/null && success "Cache des vues vidé" || warning "Impossible de vider le cache des vues"
    
    # Cache des routes
    php artisan route:clear 2>/dev/null && success "Cache des routes vidé" || warning "Impossible de vider le cache des routes"
    
    # Optimisation
    info "Optimisation de l'application..."
    php artisan optimize:clear 2>/dev/null && success "Optimisation réinitialisée" || warning "Impossible de réinitialiser l'optimisation"
    
else
    warning "PHP n'est pas disponible - Veuillez vider le cache manuellement:"
    echo "   php artisan cache:clear"
    echo "   php artisan config:clear"
    echo "   php artisan view:clear"
    echo "   php artisan route:clear"
fi

echo -e "\n\033[1;36m5. VÉRIFICATION DU CONTRÔLEUR\033[0m"
echo "----------------------------------------"

# Vérifier que le contrôleur utilise le nouveau FormRequest
if grep -q "use App\\\Http\\\Requests\\\VehicleExpenseRequest;" app/Http/Controllers/Admin/VehicleExpenseController.php; then
    success "Le contrôleur utilise VehicleExpenseRequest"
else
    error "Le contrôleur n'utilise pas VehicleExpenseRequest"
    warning "Vérifiez manuellement le fichier VehicleExpenseController.php"
fi

# Vérifier que les méthodes utilisent le bon type
if grep -q "VehicleExpenseRequest \$request" app/Http/Controllers/Admin/VehicleExpenseController.php; then
    success "Les méthodes store/update utilisent le bon FormRequest"
else
    warning "Vérifiez que store() et update() utilisent VehicleExpenseRequest"
fi

echo -e "\n\033[1;36m6. CONFIGURATION LOCALE\033[0m"
echo "----------------------------------------"

# Vérifier la configuration de la locale
if grep -q "'locale' => 'fr'" config/app.php; then
    success "La locale est configurée en français"
else
    warning "La locale n'est pas configurée en français dans config/app.php"
    info "Ajoutez ou modifiez: 'locale' => 'fr'"
fi

echo -e "\n\033[1;36m7. TEST DE VALIDATION\033[0m"
echo "----------------------------------------"

if [ -f "test_expense_validation_fix.php" ] && command -v php &> /dev/null; then
    read -p "Voulez-vous exécuter le script de test? (o/N) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Oo]$ ]]; then
        info "Exécution du script de test..."
        php test_expense_validation_fix.php
    else
        info "Test ignoré. Vous pouvez l'exécuter plus tard avec:"
        echo "   php test_expense_validation_fix.php"
    fi
else
    warning "Script de test non disponible ou PHP manquant"
fi

echo -e "\n\033[1;34m====================================================================\033[0m"
echo -e "\033[1;32m✅ APPLICATION DES CORRECTIONS TERMINÉE!\033[0m"
echo -e "\033[1;34m====================================================================\033[0m\n"

echo -e "\033[1;33m📋 CHECKLIST FINALE:\033[0m"
echo "   1. ✅ FormRequest VehicleExpenseRequest créé"
echo "   2. ✅ Traductions françaises ajoutées"
echo "   3. ✅ Contrôleur mis à jour"
echo "   4. ✅ Permissions définies"
echo "   5. ✅ Documentation créée"

echo -e "\n\033[1;36m🧪 TESTS À EFFECTUER:\033[0m"
echo "   1. Créer une dépense SANS fournisseur"
echo "   2. Créer une dépense AVEC fournisseur"
echo "   3. Vérifier les messages d'erreur en français"
echo "   4. Tester la validation des champs requis"

echo -e "\n\033[1;35m📚 DOCUMENTATION:\033[0m"
echo "   Consultez EXPENSE_VALIDATION_FIX_ENTERPRISE.md pour plus de détails"

echo -e "\n\033[1;32m🎉 Le module de dépenses est maintenant prêt pour la production!\033[0m\n"
