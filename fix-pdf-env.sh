#!/bin/bash

# Script pour corriger la configuration PDF dans .env

echo "🔧 Correction de la configuration PDF dans .env..."

# Backup du .env actuel
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Corriger l'URL du service PDF (enlever /generate-pdf de l'URL de base)
sed -i 's|PDF_SERVICE_URL=.*|PDF_SERVICE_URL=http://pdf-service:3000|g' .env

echo "✅ Configuration corrigée"
echo ""
echo "📝 Valeurs actuelles:"
grep "PDF_SERVICE_URL" .env | head -1

echo ""
echo "⚠️ Veuillez exécuter ces commandes pour appliquer les changements:"
echo "   docker exec zenfleet_php php artisan config:clear"
echo "   docker exec zenfleet_php php artisan cache:clear"
