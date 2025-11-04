#!/bin/bash

# Script de démarrage et vérification du service PDF - Enterprise Grade
# Date: 2025-11-03

set -e

echo "🚀 ZenFleet PDF Service - Script de démarrage Enterprise"
echo "========================================================="

# Couleurs pour output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fonction de log
log_info() {
    echo -e "${GREEN}✅${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}⚠️${NC} $1"
}

log_error() {
    echo -e "${RED}❌${NC} $1"
}

# 1. Arrêter le service s'il existe
echo ""
echo "📦 Étape 1: Nettoyage des containers existants..."
if docker ps -a | grep -q zenfleet_pdf_service; then
    log_warning "Container existant détecté, arrêt en cours..."
    docker stop zenfleet_pdf_service 2>/dev/null || true
    docker rm zenfleet_pdf_service 2>/dev/null || true
    log_info "Container précédent supprimé"
else
    log_info "Aucun container existant"
fi

# 2. Vérifier si le dossier pdf-service existe
echo ""
echo "📁 Étape 2: Vérification de la structure..."
if [ ! -d "pdf-service" ]; then
    log_error "Le dossier pdf-service n'existe pas!"
    echo "Création du dossier pdf-service..."
    mkdir -p pdf-service
fi

# 3. Vérifier les fichiers requis
echo ""
echo "📝 Étape 3: Vérification des fichiers requis..."
FILES_REQUIRED=("pdf-service/package.json" "pdf-service/server.js" "pdf-service/Dockerfile")
MISSING_FILES=0

for file in "${FILES_REQUIRED[@]}"; do
    if [ ! -f "$file" ]; then
        log_error "Fichier manquant: $file"
        MISSING_FILES=$((MISSING_FILES + 1))
    else
        log_info "Fichier trouvé: $file"
    fi
done

if [ $MISSING_FILES -gt 0 ]; then
    log_error "Des fichiers sont manquants. Arrêt du script."
    exit 1
fi

# 4. Construire l'image
echo ""
echo "🏗️ Étape 4: Construction de l'image Docker..."
docker build -t zenfleet-pdf-service:latest ./pdf-service

if [ $? -eq 0 ]; then
    log_info "Image construite avec succès"
else
    log_error "Échec de la construction de l'image"
    exit 1
fi

# 5. Démarrer le container via docker-compose
echo ""
echo "🚀 Étape 5: Démarrage du service via docker-compose..."
docker-compose up -d pdf-service

if [ $? -eq 0 ]; then
    log_info "Service démarré avec docker-compose"
else
    log_error "Échec du démarrage avec docker-compose"
    exit 1
fi

# 6. Attendre que le service soit prêt
echo ""
echo "⏳ Étape 6: Attente du démarrage du service..."
MAX_ATTEMPTS=30
ATTEMPT=0

while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    if curl -f -s http://localhost:3000/health > /dev/null 2>&1; then
        log_info "Service PDF opérationnel!"
        break
    fi
    
    echo -n "."
    sleep 2
    ATTEMPT=$((ATTEMPT + 1))
done

echo ""

if [ $ATTEMPT -eq $MAX_ATTEMPTS ]; then
    log_error "Le service n'a pas démarré dans le temps imparti"
    echo ""
    echo "📋 Logs du container:"
    docker logs zenfleet_pdf_service
    exit 1
fi

# 7. Test de santé final
echo ""
echo "🏥 Étape 7: Test de santé du service..."
HEALTH_RESPONSE=$(curl -s http://localhost:3000/health)

if echo "$HEALTH_RESPONSE" | grep -q "healthy"; then
    log_info "Service PDF en parfaite santé!"
    echo ""
    echo "📊 Réponse du service:"
    echo "$HEALTH_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$HEALTH_RESPONSE"
else
    log_error "Le service ne répond pas correctement"
    exit 1
fi

# 8. Afficher les informations de connexion
echo ""
echo "========================================================="
echo -e "${GREEN}✅ SERVICE PDF ENTERPRISE OPÉRATIONNEL${NC}"
echo "========================================================="
echo ""
echo "📍 URLs disponibles:"
echo "   • Health Check: http://localhost:3000/health"
echo "   • Generate PDF: http://localhost:3000/generate-pdf"
echo "   • Container: zenfleet_pdf_service"
echo ""
echo "🔧 Commandes utiles:"
echo "   • Logs: docker logs -f zenfleet_pdf_service"
echo "   • Restart: docker-compose restart pdf-service"
echo "   • Stop: docker-compose stop pdf-service"
echo ""
echo "🎯 Configuration Laravel (.env):"
echo "   PDF_SERVICE_URL=http://pdf-service:3000"
echo "   PDF_SERVICE_TIMEOUT=60"
echo ""
echo "========================================================="
