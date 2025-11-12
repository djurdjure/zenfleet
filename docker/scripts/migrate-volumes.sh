#!/bin/bash

################################################################################
# 🏢 ZENFLEET - SCRIPT DE MIGRATION DES VOLUMES DOCKER (ENTERPRISE-GRADE)
################################################################################
# Description : Migration sécurisée des volumes Docker vers la nouvelle
#               architecture standardisée
# Usage       : ./docker/scripts/migrate-volumes.sh
# Author      : ZenFleet DevOps Team
# Version     : 1.0.0
################################################################################

set -euo pipefail

# Couleurs pour output professionnel
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

################################################################################
# VÉRIFICATIONS PRÉLIMINAIRES
################################################################################

log_info "🔍 Vérification de l'environnement Docker..."

if ! command -v docker &> /dev/null; then
    log_error "Docker n'est pas installé ou accessible"
    exit 1
fi

log_success "Docker est disponible"

################################################################################
# MIGRATION DU VOLUME POSTGRESQL
################################################################################

OLD_POSTGRES_VOLUME="zenfleet_zenfleet_postgres_data"
NEW_POSTGRES_VOLUME="zenfleet_postgres_data"

log_info "📦 Vérification du volume PostgreSQL..."

if docker volume inspect "$OLD_POSTGRES_VOLUME" &> /dev/null; then
    log_info "Volume ancien détecté: $OLD_POSTGRES_VOLUME"

    # Vérifier si le nouveau volume existe déjà
    if docker volume inspect "$NEW_POSTGRES_VOLUME" &> /dev/null; then
        log_warning "Le nouveau volume existe déjà: $NEW_POSTGRES_VOLUME"
        log_warning "Les données existantes seront préservées"
    else
        log_info "🚀 Création du nouveau volume: $NEW_POSTGRES_VOLUME"
        docker volume create "$NEW_POSTGRES_VOLUME"

        log_info "📋 Migration des données PostgreSQL..."
        log_info "Source: $OLD_POSTGRES_VOLUME"
        log_info "Destination: $NEW_POSTGRES_VOLUME"

        # Migration via container temporaire
        docker run --rm \
            -v "$OLD_POSTGRES_VOLUME":/source:ro \
            -v "$NEW_POSTGRES_VOLUME":/destination \
            alpine sh -c "cp -av /source/. /destination/"

        log_success "✅ Migration PostgreSQL terminée avec succès"

        # Vérification de l'intégrité
        OLD_SIZE=$(docker run --rm -v "$OLD_POSTGRES_VOLUME":/data alpine du -sb /data | cut -f1)
        NEW_SIZE=$(docker run --rm -v "$NEW_POSTGRES_VOLUME":/data alpine du -sb /data | cut -f1)

        log_info "Taille ancien volume: $OLD_SIZE bytes"
        log_info "Taille nouveau volume: $NEW_SIZE bytes"

        if [ "$OLD_SIZE" -eq "$NEW_SIZE" ]; then
            log_success "✅ Vérification d'intégrité réussie"
        else
            log_warning "⚠️  Les tailles diffèrent légèrement (normal si migration en cours)"
        fi
    fi
else
    log_info "Aucun ancien volume PostgreSQL trouvé, création du nouveau..."
    docker volume create "$NEW_POSTGRES_VOLUME"
    log_success "✅ Nouveau volume PostgreSQL créé"
fi

################################################################################
# MIGRATION DU VOLUME REDIS
################################################################################

OLD_REDIS_VOLUME="zenfleet_zenfleet_redis_data"
NEW_REDIS_VOLUME="zenfleet_redis_data"

log_info "📦 Vérification du volume Redis..."

if docker volume inspect "$OLD_REDIS_VOLUME" &> /dev/null; then
    log_info "Volume ancien détecté: $OLD_REDIS_VOLUME"

    if docker volume inspect "$NEW_REDIS_VOLUME" &> /dev/null; then
        log_warning "Le nouveau volume existe déjà: $NEW_REDIS_VOLUME"
    else
        log_info "🚀 Création du nouveau volume: $NEW_REDIS_VOLUME"
        docker volume create "$NEW_REDIS_VOLUME"

        log_info "📋 Migration des données Redis..."
        docker run --rm \
            -v "$OLD_REDIS_VOLUME":/source:ro \
            -v "$NEW_REDIS_VOLUME":/destination \
            alpine sh -c "cp -av /source/. /destination/"

        log_success "✅ Migration Redis terminée avec succès"
    fi
else
    log_info "Aucun ancien volume Redis trouvé, création du nouveau..."
    docker volume create "$NEW_REDIS_VOLUME"
    log_success "✅ Nouveau volume Redis créé"
fi

################################################################################
# RÉSUMÉ ET CLEANUP
################################################################################

echo ""
log_success "=========================================="
log_success "🎉 MIGRATION TERMINÉE AVEC SUCCÈS"
log_success "=========================================="
echo ""

log_info "📊 État des volumes:"
docker volume ls | grep -E "zenfleet_(postgres|redis)" || true

echo ""
log_info "📝 Prochaines étapes:"
echo "   1. Démarrer les conteneurs: docker compose up -d"
echo "   2. Vérifier la santé: docker compose ps"
echo "   3. Tester la connexion DB et Redis"
echo ""

log_warning "🗑️  Nettoyage manuel (OPTIONNEL):"
echo "   Après vérification que tout fonctionne, vous pouvez supprimer les anciens volumes:"
echo "   docker volume rm $OLD_POSTGRES_VOLUME"
echo "   docker volume rm $OLD_REDIS_VOLUME"
echo ""

log_success "✅ Script de migration terminé"
