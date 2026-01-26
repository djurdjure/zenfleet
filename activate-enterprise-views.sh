#!/bin/bash

  # Script d'activation des vues enterprise ZenFleet
  # Auteur: Expert Laravel Enterprise
  # Date: 2025-10-08

  echo "🚀 Activation des vues Enterprise ZenFleet..."
  echo ""

  # Fonction pour copier avec backup
  activate_view() {
      SOURCE=$1
      DEST=$2

      if [ -f "$DEST" ]; then
          echo "📦 Backup: $DEST → $DEST.backup"
          cp "$DEST" "$DEST.backup"
      fi

      echo "✅ Activation: $SOURCE → $DEST"
      cp "$SOURCE" "$DEST"
  }

  # Véhicules
  echo "🚗 Activation des vues Véhicules..."
  activate_view "resources/views/admin/vehicles/enterprise-index.blade.php"
  "resources/views/admin/vehicles/index.blade.php"
  activate_view "resources/views/admin/vehicles/enterprise-create.blade.php"
  "resources/views/admin/vehicles/create.blade.php"
  activate_view "resources/views/admin/vehicles/enterprise-edit.blade.php"
  "resources/views/admin/vehicles/edit.blade.php"
  activate_view "resources/views/admin/vehicles/enterprise-show.blade.php"
  "resources/views/admin/vehicles/show.blade.php"
  echo ""

  # Chauffeurs
  echo "👨‍✈️ Activation des vues Chauffeurs..."
  activate_view "resources/views/admin/drivers/enterprise-index.blade.php"
  "resources/views/admin/drivers/index.blade.php"
  echo ""

  # Affectations
  echo "📋 Activation des vues Affectations..."
  activate_view "resources/views/admin/assignments/index-enterprise.blade.php"
  "resources/views/admin/assignments/index.blade.php"
  echo ""

  # Clear cache
  echo "🧹 Nettoyage du cache..."
  docker compose exec php php artisan view:clear
  echo ""

  echo "✅ Toutes les vues enterprise ont été activées avec succès !"
  echo "🌐 Accédez à votre application: http://localhost"


