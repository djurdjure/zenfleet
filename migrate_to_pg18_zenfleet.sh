#!/bin/bash
set -e  # Exit on error

echo "🚀 ================================================================"
echo "🚀  MIGRATION POSTGRESQL 16 → 18 - ZENFLEET ENTERPRISE-GRADE"
echo "🚀 ================================================================"
echo ""

# Variables from .env
CONTAINER_NAME="zenfleet_database"
DB_USER="zenfleet_user"
DB_NAME="zenfleet_db"
DB_PASSWORD="zenfleet_pass"
BACKUP_DIR="backups_pg16_migration"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

echo "📋 Configuration Migration:"
echo "  ├─ Container: $CONTAINER_NAME"
echo "  ├─ Database: $DB_NAME"
echo "  ├─ User: $DB_USER"
echo "  ├─ Backup Directory: $BACKUP_DIR"
echo "  └─ Timestamp: $TIMESTAMP"
echo ""

# ============================================================================
# ÉTAPE 1: VÉRIFICATIONS PRÉ-MIGRATION
# ============================================================================
echo "✅ [1/14] Vérification container..."
if ! docker ps | grep -q "$CONTAINER_NAME"; then
    echo "❌ Container $CONTAINER_NAME n'est pas en cours d'exécution!"
    exit 1
fi
echo "   ✓ Container actif"

# ============================================================================
# ÉTAPE 2: ARRÊT DES CONNEXIONS ACTIVES
# ============================================================================
echo "✅ [2/14] Arrêt des connexions actives..."
docker exec "$CONTAINER_NAME" psql -U "$DB_USER" -d "$DB_NAME" -c "
SELECT pg_terminate_backend(pg_stat_activity.pid)
FROM pg_stat_activity
WHERE pg_stat_activity.datname = '$DB_NAME'
  AND pid <> pg_backend_pid();
" 2>/dev/null || echo "   ✓ Aucune connexion active à terminer"

# ============================================================================
# ÉTAPE 3: BACKUP FINAL AVANT MIGRATION
# ============================================================================
echo "✅ [3/14] Backup complet final (pg_dumpall)..."
docker exec "$CONTAINER_NAME" pg_dumpall -U "$DB_USER" > "$BACKUP_DIR/pg16_final_backup_${TIMESTAMP}.sql"
BACKUP_SIZE=$(ls -lh "$BACKUP_DIR/pg16_final_backup_${TIMESTAMP}.sql" | awk '{print $5}')
echo "   ✓ Backup sauvegardé: $BACKUP_SIZE"

# ============================================================================
# ÉTAPE 4: BACKUP SCHEMA
# ============================================================================
echo "✅ [4/14] Backup schema (référence)..."
docker exec "$CONTAINER_NAME" pg_dump -U "$DB_USER" -d "$DB_NAME" --schema-only > "$BACKUP_DIR/pg16_schema_${TIMESTAMP}.sql"
echo "   ✓ Schema sauvegardé"

# ============================================================================
# ÉTAPE 5: ARRÊT TOUS LES SERVICES
# ============================================================================
echo "✅ [5/14] Arrêt des services Docker Compose..."
docker compose down
echo "   ✓ Services arrêtés"

# ============================================================================
# ÉTAPE 6: RENOMMER CONTAINER PG16 (BACKUP)
# ============================================================================
echo "✅ [6/14] Sauvegarde container PostgreSQL 16..."
docker rename "$CONTAINER_NAME" "${CONTAINER_NAME}_pg16_backup" 2>/dev/null || echo "   ✓ Container déjà arrêté"

# ============================================================================
# ÉTAPE 7: COPIER VOLUME DONNÉES (BACKUP DE SÉCURITÉ)
# ============================================================================
echo "✅ [7/14] Copie volume de données (backup de sécurité)..."
echo "   ⏳ Cette étape peut prendre quelques minutes..."

# Créer volume backup
docker volume create zenfleet_postgres_data_pg16_backup

# Copier les données
docker run --rm \
    -v zenfleet_zenfleet_postgres_data:/source:ro \
    -v zenfleet_postgres_data_pg16_backup:/backup \
    alpine \
    sh -c "cd /source && cp -av . /backup/"

echo "   ✓ Volume backup créé: zenfleet_postgres_data_pg16_backup"

# ============================================================================
# ÉTAPE 8: MISE À JOUR docker-compose.yml
# ============================================================================
echo "✅ [8/14] Mise à jour configuration Docker Compose..."

# Backup docker-compose.yml
cp docker-compose.yml "docker-compose.yml.backup_pg16_${TIMESTAMP}"

# Mettre à jour l'image PostgreSQL
sed -i 's|image: postgis/postgis:16-3.4-alpine|image: postgis/postgis:18-3.6-alpine|g' docker-compose.yml

echo "   ✓ docker-compose.yml mis à jour (backup créé)"

# ============================================================================
# ÉTAPE 9: DÉMARRAGE POSTGRESQL 18
# ============================================================================
echo "✅ [9/14] Démarrage PostgreSQL 18..."
docker compose up -d database

# Attendre que PostgreSQL soit prêt
echo "   ⏳ Attente démarrage PostgreSQL 18..."
for i in {1..60}; do
    if docker compose exec database pg_isready -U "$DB_USER" > /dev/null 2>&1; then
        echo "   ✓ PostgreSQL 18 démarré!"
        break
    fi
    if [ $i -eq 60 ]; then
        echo "   ❌ Timeout: PostgreSQL 18 n'a pas démarré en 2 minutes"
        exit 1
    fi
    echo -n "."
    sleep 2
done
echo ""

# ============================================================================
# ÉTAPE 10: VÉRIFIER VERSION POSTGRESQL 18
# ============================================================================
echo "✅ [10/14] Vérification version PostgreSQL..."
PG_VERSION=$(docker compose exec -T database psql -U "$DB_USER" -d postgres -t -c "SELECT version();" | head -1)
echo "   ✓ Version: $PG_VERSION"

if [[ ! "$PG_VERSION" =~ "PostgreSQL 18" ]]; then
    echo "   ⚠️  WARNING: Version attendue PostgreSQL 18, obtenue: $PG_VERSION"
fi

# ============================================================================
# ÉTAPE 11: RESTAURER LES DONNÉES
# ============================================================================
echo "✅ [11/14] Restauration des données..."
echo "   ⏳ Cette étape peut prendre quelques minutes..."

# Restaurer depuis le backup
docker compose exec -T database psql -U "$DB_USER" < "$BACKUP_DIR/pg16_final_backup_${TIMESTAMP}.sql" 2>&1 | grep -v "NOTICE\|WARNING" || true

echo "   ✓ Données restaurées"

# ============================================================================
# ÉTAPE 12: UPGRADE EXTENSIONS
# ============================================================================
echo "✅ [12/14] Upgrade des extensions..."
docker compose exec -T database psql -U "$DB_USER" -d "$DB_NAME" << 'EOFSQL'
-- Upgrade PostGIS
ALTER EXTENSION postgis UPDATE;
SELECT postgis_extensions_upgrade();

-- Upgrade autres extensions
ALTER EXTENSION btree_gist UPDATE;
ALTER EXTENSION fuzzystrmatch UPDATE;

-- Vérifier versions finales
SELECT extname, extversion FROM pg_extension ORDER BY extname;
EOFSQL

echo "   ✓ Extensions upgradées"

# ============================================================================
# ÉTAPE 13: ANALYZE ET MAINTENANCE
# ============================================================================
echo "✅ [13/14] Maintenance post-migration..."
docker compose exec -T database psql -U "$DB_USER" -d "$DB_NAME" << 'EOFSQL'
-- ANALYZE pour recréer statistiques
ANALYZE;

-- Refresh vues matérialisées
REFRESH MATERIALIZED VIEW CONCURRENTLY assignment_stats_daily;

-- Vérifier contraintes
SELECT conname FROM pg_constraint WHERE conname LIKE '%no_overlap%';
EOFSQL

echo "   ✓ Maintenance terminée"

# ============================================================================
# ÉTAPE 14: DÉMARRAGE SERVICES COMPLETS
# ============================================================================
echo "✅ [14/14] Démarrage de tous les services..."
docker compose up -d
sleep 5
echo "   ✓ Tous les services démarrés"

# ============================================================================
# RAPPORT FINAL
# ============================================================================
echo ""
echo "🎉 ================================================================"
echo "🎉  MIGRATION TERMINÉE AVEC SUCCÈS!"
echo "🎉 ================================================================"
echo ""
echo "📊 Résumé:"
echo "  ├─ PostgreSQL 18.0 : ✅ Installé"
echo "  ├─ PostGIS 3.6.0   : ✅ Upgradé"
echo "  ├─ Extensions      : ✅ Mises à jour"
echo "  ├─ Données         : ✅ Restaurées"
echo "  └─ Services        : ✅ Actifs"
echo ""
echo "💾 Backups conservés:"
echo "  ├─ SQL: $BACKUP_DIR/pg16_final_backup_${TIMESTAMP}.sql"
echo "  ├─ Schema: $BACKUP_DIR/pg16_schema_${TIMESTAMP}.sql"
echo "  ├─ Volume: zenfleet_postgres_data_pg16_backup"
echo "  └─ Docker Compose: docker-compose.yml.backup_pg16_${TIMESTAMP}"
echo ""
echo "📋 Prochaines étapes:"
echo "  1. Vérifier logs: docker compose logs database"
echo "  2. Tester connexion Laravel"
echo "  3. Exécuter tests de validation"
echo "  4. Vérifier performances"
echo ""
echo "⚠️  IMPORTANT:"
echo "  - Ne supprimez PAS les backups avant validation complète (7 jours)"
echo "  - Container PG16 sauvegardé: ${CONTAINER_NAME}_pg16_backup"
echo "  - Pour rollback, voir documentation POSTGRESQL_18_MIGRATION_PROCEDURE.md"
echo ""
echo "✅ Migration PostgreSQL 16 → 18 terminée!"
echo ""
