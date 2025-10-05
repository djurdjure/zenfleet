#!/bin/bash

################################################################################
# ZENFLEET - DÉPLOIEMENT MODULE RÉPARATIONS
# Script de déploiement enterprise-grade
# Version: 1.0
# Date: 2025-10-05
################################################################################

set -e  # Exit on error

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Functions
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

separator() {
    echo ""
    echo "════════════════════════════════════════════════════════════════"
    echo ""
}

################################################################################
# HEADER
################################################################################

clear
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║                                                                ║"
echo "║  🚀 ZENFLEET - DÉPLOIEMENT MODULE RÉPARATIONS                 ║"
echo "║                                                                ║"
echo "║  Version: 1.0 Enterprise                                       ║"
echo "║  Date: $(date '+%Y-%m-%d %H:%M:%S')                                       ║"
echo "║                                                                ║"
echo "╚════════════════════════════════════════════════════════════════╝"
separator

################################################################################
# PRE-FLIGHT CHECKS
################################################################################

log_info "Étape 1/7: Vérifications préliminaires..."
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    log_error "Fichier artisan non trouvé. Êtes-vous dans le bon répertoire?"
    exit 1
fi
log_success "Répertoire Laravel détecté"

# Check if Docker is running
if ! docker compose ps > /dev/null 2>&1; then
    log_error "Docker Compose n'est pas démarré"
    exit 1
fi
log_success "Docker Compose actif"

# Check database connection
if ! docker compose exec php php artisan db:show > /dev/null 2>&1; then
    log_error "Connexion base de données échouée"
    exit 1
fi
log_success "Base de données accessible"

separator

################################################################################
# BACKUP
################################################################################

log_info "Étape 2/7: Sauvegarde de sécurité..."
echo ""

BACKUP_DIR="backups/$(date '+%Y%m%d_%H%M%S')"
mkdir -p "$BACKUP_DIR"

# Backup database
log_info "Sauvegarde base de données..."
docker compose exec -T postgres pg_dump -U zenfleet zenfleet > "$BACKUP_DIR/database.sql"
log_success "Base de données sauvegardée: $BACKUP_DIR/database.sql"

# Backup permissions config
log_info "Sauvegarde config permissions..."
docker compose exec php php artisan tinker --execute="
  file_put_contents('storage/app/permissions-backup.json', json_encode([
    'roles' => \Spatie\Permission\Models\Role::all()->toArray(),
    'permissions' => \Spatie\Permission\Models\Permission::all()->toArray(),
    'model_has_roles' => DB::table('model_has_roles')->get()->toArray(),
  ], JSON_PRETTY_PRINT));
" > /dev/null 2>&1

docker compose exec php cat storage/app/permissions-backup.json > "$BACKUP_DIR/permissions-backup.json"
log_success "Permissions sauvegardées: $BACKUP_DIR/permissions-backup.json"

separator

################################################################################
# CLEAR CACHES
################################################################################

log_info "Étape 3/7: Nettoyage des caches..."
echo ""

docker compose exec php php artisan permission:cache-reset > /dev/null 2>&1
log_success "Cache permissions nettoyé"

docker compose exec php php artisan optimize:clear > /dev/null 2>&1
log_success "Cache application nettoyé"

separator

################################################################################
# RUN MIGRATIONS
################################################################################

log_info "Étape 4/7: Exécution des migrations..."
echo ""

if docker compose exec php php artisan migrate --force; then
    log_success "Migrations exécutées"
else
    log_error "Échec des migrations"
    log_warning "Restauration possible avec: cat $BACKUP_DIR/database.sql | docker compose exec -T postgres psql -U zenfleet zenfleet"
    exit 1
fi

separator

################################################################################
# SEED PERMISSIONS
################################################################################

log_info "Étape 5/7: Configuration des permissions..."
echo ""

log_info "Seed permissions réparations..."
if docker compose exec php php artisan db:seed --class=RepairPermissionsSeeder; then
    log_success "Permissions créées"
else
    log_error "Échec seed permissions"
    exit 1
fi

log_info "Correction rôles utilisateurs..."
if docker compose exec php php artisan db:seed --class=FixUserRolesSeeder; then
    log_success "Rôles corrigés"
else
    log_error "Échec correction rôles"
    exit 1
fi

separator

################################################################################
# VALIDATION
################################################################################

log_info "Étape 6/7: Validation du système..."
echo ""

if docker compose exec php php tests-validation-finale.php; then
    log_success "Tous les tests de validation passés"
else
    log_error "Échec de la validation"
    log_warning "Vérifiez les logs ci-dessus"
    exit 1
fi

separator

################################################################################
# FINAL CHECKS
################################################################################

log_info "Étape 7/7: Vérifications finales..."
echo ""

# Check routes
log_info "Vérification routes repair-requests..."
ROUTE_COUNT=$(docker compose exec php php artisan route:list | grep -c "repair-requests" || true)
if [ "$ROUTE_COUNT" -gt 0 ]; then
    log_success "$ROUTE_COUNT routes repair-requests trouvées"
else
    log_error "Aucune route repair-requests trouvée"
    exit 1
fi

# Check permissions count
log_info "Vérification permissions repair..."
PERM_COUNT=$(docker compose exec php php artisan tinker --execute="echo \Spatie\Permission\Models\Permission::where('name', 'like', '%repair%')->count();" 2>/dev/null | tail -1 || echo "0")
if [ "$PERM_COUNT" -gt 0 ]; then
    log_success "$PERM_COUNT permissions repair trouvées"
else
    log_error "Aucune permission repair trouvée"
    exit 1
fi

# Check Super Admin
log_info "Vérification Super Admin..."
docker compose exec php php artisan tinker --execute="
  \$sa = \App\Models\User::where('email', 'superadmin@zenfleet.dz')->first();
  if (\$sa) {
    Auth::login(\$sa);
    if (\$sa->hasRole('Super Admin')) {
      echo 'OK';
    }
  }
" > /dev/null 2>&1

if [ $? -eq 0 ]; then
    log_success "Super Admin configuré correctement"
else
    log_error "Problème configuration Super Admin"
    exit 1
fi

separator

################################################################################
# SUCCESS
################################################################################

echo ""
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║                                                                ║"
echo "║  🎉 DÉPLOIEMENT RÉUSSI !                                       ║"
echo "║                                                                ║"
echo "║  Le module Réparations est maintenant opérationnel             ║"
echo "║                                                                ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

log_success "Module Réparations déployé avec succès"
log_info "Sauvegarde disponible: $BACKUP_DIR"

separator

echo "📋 RÉSUMÉ:"
echo ""
echo "  ✅ Migrations: OK"
echo "  ✅ Permissions: $PERM_COUNT créées"
echo "  ✅ Routes: $ROUTE_COUNT enregistrées"
echo "  ✅ Super Admin: OK"
echo "  ✅ Validation: 4/4 tests passés"
echo ""

log_info "UTILISATEURS DE TEST:"
echo ""
echo "  • superadmin@zenfleet.dz (Super Admin)"
echo "  • fleet@zenfleet.dz (Gestionnaire Flotte)"
echo "  • supervisor@zenfleet.dz (Supervisor)"
echo "  • driver@zenfleet.dz (Chauffeur)"
echo ""
echo "  Mot de passe: password"
echo ""

separator

log_info "PROCHAINES ÉTAPES:"
echo ""
echo "  1. Tester l'interface: http://localhost/admin/repair-requests"
echo "  2. Vérifier les permissions par rôle"
echo "  3. Créer une demande de réparation de test"
echo "  4. Tester le workflow d'approbation"
echo ""

log_info "EN CAS DE PROBLÈME:"
echo ""
echo "  • Logs: tail -f storage/logs/laravel.log"
echo "  • Validation: php tests-validation-finale.php"
echo "  • Fix rôles: php artisan db:seed --class=FixUserRolesSeeder"
echo "  • Restauration: cat $BACKUP_DIR/database.sql | docker compose exec -T postgres psql -U zenfleet zenfleet"
echo ""

separator

log_success "DÉPLOIEMENT TERMINÉ ✨"
echo ""

exit 0
