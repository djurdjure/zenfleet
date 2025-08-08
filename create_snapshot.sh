#!/bin/bash

# Script pour créer un snapshot complet du code source du projet ZenFleet
# Version 1.0 - Architecturé par ZENFLEET V2

set -e

OUTPUT_FILE="zenfleet_project_snapshot_$(date +%Y-%m-%d).txt"

echo "==================================================="
echo "ZENFLEET - Création du Snapshot de Projet"
echo "==================================================="
echo "Fichier de sortie : $OUTPUT_FILE"
echo ""

# Initialisation du fichier de sortie
echo "Snapshot complet du projet ZenFleet - $(date)" > "$OUTPUT_FILE"
echo "===================================================" >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

# Fonction pour ajouter un fichier au snapshot avec un en-tête clair
append_file_content() {
    local file_path=$1
    echo "-> Ajout de : $file_path"
    echo "////////////////////////////////////////////////////////////////" >> "$OUTPUT_FILE"
    echo "CHEMIN : ./$file_path" >> "$OUTPUT_FILE"
    echo "////////////////////////////////////////////////////////////////" >> "$OUTPUT_FILE"
    echo "" >> "$OUTPUT_FILE"
    cat "$file_path" >> "$OUTPUT_FILE"
    echo "" >> "$OUTPUT_FILE"
    echo "--- FIN DU FICHIER : ./$file_path ---" >> "$OUTPUT_FILE"
    echo "" >> "$OUTPUT_FILE"
    echo "" >> "$OUTPUT_FILE"
}

# Liste des fichiers importants à la racine du projet
ROOT_FILES=(
    ".env"
    "docker-compose.yml"
    "package.json"
    "composer.json"
    "vite.config.js"
    "tailwind.config.js"
    "postcss.config.js"
    "README.md"
)

# Liste des répertoires de code source à inclure
SOURCE_DIRECTORIES=(
    "app"
    "bootstrap"
    "config"
    "database"
    "docker"
    "pdf-service"
    "public"
    "resources"
    "routes"
    "tests"
)

# Traitement des fichiers à la racine
echo "--- Traitement des fichiers racine ---"
for file in "${ROOT_FILES[@]}"; do
    if [ -f "$file" ]; then
        append_file_content "$file"
    else
        echo "AVERTISSEMENT : Fichier racine '$file' non trouvé. Ignoré."
    fi
done
echo ""

# Traitement des répertoires de code source
echo "--- Traitement des répertoires de code source ---"
for dir in "${SOURCE_DIRECTORIES[@]}"; do
    if [ -d "$dir" ]; then
        echo "Analyse du répertoire : $dir/"
        find "$dir" -type f -not -path "*/.git/*" -not -path "*/public/build/*" -not -path "*/public/storage/*" | sort | while read -r file; do
            append_file_content "$file"
        done
    else
         echo "AVERTISSEMENT : Répertoire '$dir' non trouvé. Ignoré."
    fi
done

echo ""
echo "==================================================="
echo "✅ Snapshot créé avec succès dans : $OUTPUT_FILE"
echo "==================================================="
