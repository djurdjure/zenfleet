#!/bin/bash

# Script de reconstruction complète du service PDF Enterprise
# Date: 2025-11-03

set -e

echo "🚀 RECONSTRUCTION COMPLÈTE DU SERVICE PDF ENTERPRISE v3.0"
echo "=========================================================="

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

log_info() { echo -e "${GREEN}✅${NC} $1"; }
log_warning() { echo -e "${YELLOW}⚠️${NC} $1"; }
log_error() { echo -e "${RED}❌${NC} $1"; }

# 1. Arrêter et supprimer l'ancien container
echo ""
echo "📦 Étape 1: Nettoyage..."
docker-compose down pdf-service 2>/dev/null || true
docker stop zenfleet_pdf_service 2>/dev/null || true
docker rm zenfleet_pdf_service 2>/dev/null || true
docker rmi zenfleet-pdf-service:latest 2>/dev/null || true
log_info "Nettoyage terminé"

# 2. Supprimer le cache node_modules si existant
echo ""
echo "🗑️ Étape 2: Suppression du cache..."
if [ -d "pdf-service/node_modules" ]; then
    rm -rf pdf-service/node_modules
    log_info "Cache node_modules supprimé"
fi

# 3. Reconstruire l'image
echo ""
echo "🏗️ Étape 3: Construction de l'image Docker..."
docker-compose build --no-cache pdf-service
if [ $? -eq 0 ]; then
    log_info "Image construite avec succès"
else
    log_error "Échec de la construction"
    exit 1
fi

# 4. Démarrer le service
echo ""
echo "🚀 Étape 4: Démarrage du service..."
docker-compose up -d pdf-service
if [ $? -eq 0 ]; then
    log_info "Service démarré"
else
    log_error "Échec du démarrage"
    exit 1
fi

# 5. Attendre que le service soit prêt
echo ""
echo "⏳ Étape 5: Vérification du service..."
sleep 5

for i in {1..20}; do
    if curl -f -s http://localhost:3000/health > /dev/null 2>&1; then
        log_info "Service PDF opérationnel!"
        break
    fi
    echo -n "."
    sleep 2
done

# 6. Test final
echo ""
echo "🧪 Étape 6: Tests de validation..."

# Test health
HEALTH=$(curl -s http://localhost:3000/health)
if echo "$HEALTH" | grep -q "healthy"; then
    log_info "Health check: OK"
    echo "$HEALTH" | python3 -m json.tool 2>/dev/null || echo "$HEALTH"
else
    log_error "Health check échoué"
fi

# Test depuis le container PHP
echo ""
echo "🔗 Test de connectivité depuis PHP..."
docker exec zenfleet_php curl -s http://pdf-service:3000/health > /dev/null
if [ $? -eq 0 ]; then
    log_info "Connectivité PHP → PDF: OK"
else
    log_error "Connectivité PHP → PDF: ÉCHEC"
fi

# 7. Afficher les logs
echo ""
echo "📋 Logs du service:"
docker logs --tail 20 zenfleet_pdf_service

echo ""
echo "=========================================="
echo -e "${GREEN}✅ RECONSTRUCTION TERMINÉE${NC}"
echo "=========================================="
echo ""
echo "📍 Service disponible sur:"
echo "   • Depuis host: http://localhost:3000"
echo "   • Depuis containers: http://pdf-service:3000"
echo ""
echo "🔧 Commandes utiles:"
echo "   • Logs: docker logs -f zenfleet_pdf_service"
echo "   • Test: curl http://localhost:3000/test"
echo ""
echo "⚙️ Configuration Laravel (.env):"
echo "   PDF_SERVICE_URL=http://pdf-service:3000"
echo "==========================================
